/**
 * Copyright (c) 2015-2019 wimtecc, Karl-Heinz Welter
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 *
 * hdlsolar.c
 *
 * handle "our" solar collector
 *
 * Revision history
 *
 * date			rev.	who		what
 * ----------------------------------------------------------------------------
 * 2015-11-20	PA1		khw		inception;
 * 2016-03-04	PA2		khw		adapted to latest changes;
 * 2016-03-17	PA3		khw		added minimum collector temperature;
 * 2018-07-29	PA4		khw		modified logging towards system logger;
 * 2018-08-12	PA5		khw		adapted to systemd;
 * 2019-05-11	PA7		khw		adapted to new structure of group addresses;
 *
 */
#include	<stdio.h>
#include	<stdarg.h>
#include	<string.h>
#include	<strings.h>
#include	<unistd.h>
#include	<stdlib.h>
#include	<time.h>
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/signal.h>

#include	"eib.h"
#include	"debug.h"
#include	"nodeinfo.h"
#include	"mylib.h"
#include	"inilib.h"

typedef	enum	modeSolar	{
			MODE_INVALID	=	-1
		,	MODE_STOPPED	=	0
		,	MODE_WATER	=	1
		,	MODE_BUFFER	=	2
	}	modeSolar ;

#define	TEMP_COL_MIN_ON	35
#define	TEMP_COL_MIN_OFF	32
#define	TEMP_WW_OFF	65
#define	TEMP_HB_OFF	52

#define	MISCHER_SOLAR_PUFFER	0
#define	MISCHER_SOLAR_WASSER	1
#define	PUMPE_SOLAR_AUS		0
#define	PUMPE_SOLAR_EIN		1

extern	void	setModeStopped( eibHdl *, node *) ;
extern	void	setModeWater( eibHdl *, node *) ;
extern	void	setModeBuffer( eibHdl *, node *) ;

extern	void	help() ;
extern	void	logit( char *_fmt, ...) ;

char	progName[64] ;
pid_t	ownPID ;
int		runLevel ;
modeSolar	currentMode ;

int		pumpSolar ;
int		valveSolar ;

char    *modeText[3]    =       {
		"working"
	,	"heating water tank"
	,	"heating buffer"
	} ;

/**
 *
 */
int	cfgQueueKey	=	10031 ;
int	cfgSenderAddr	=	1 ;
int	cfgDaemon	=	1 ;				// default:	run as daemon process
int	cfgHdlWater	=	1 ;
int	cfgHdlBuffer	=	1 ;

/**
 *
 */
void	sigHandler( int _sig) {
	runLevel	-=	1 ;
}

/**
 *
 */
void	sigHandlerTERM( int _sig) {
	runLevel	=	-1 ;
}

/**
 *
 */
void	sigHandlerHUP( int _sig) {
	char	buffer[128] ;
	sprintf( buffer, "hdlsolar water := %d, buffer := %d", cfgHdlWater, cfgHdlBuffer) ;
	logit( buffer) ;
}

/**
 *
 */
void	iniCallback( char *_block, char *_para, char *_value) {
	_debug( 1, progName, "receive ini value block/paramater/value ... : %s/%s/%s\n", _block, _para, _value) ;
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		}
	} else if ( strcmp( _block, "[hdlsolar]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		} else if ( strcmp( _para, "daemon") == 0) {
			cfgDaemon	=	atoi( _value) ;
		} else if ( strcmp( _para, "handleWater") == 0) {
			cfgHdlWater	=	atoi( _value) ;
		} else if ( strcmp( _para, "handleBuffer") == 0) {
			cfgHdlBuffer	=	atoi( _value) ;
		}
	}
}

/**
 *
 */
int	main( int argc, char *argv[]) {
		eibHdl	 *myEIB ;
			pid_t	pid, sid ;
		int	status		=	0 ;
		int	opt ;
		time_t	actTime ;
	struct	tm	myTime ;
		char	timeBuffer[64] ;
		int	timeMin ;				// daytime in minutes
		int	pumpUpTime ;				// time pump is running in seconds
		int	pumpDownTime ;				// time pump is idle in seconds
		int	pumpOnTime ;				// time when solar pump was switched on last
		int	pumpOffTime ;				// time when solar pump was switched off
	/**
	 * define application specific variables
	 */
		float	tempWWOff	=	TEMP_WW_OFF ;	// high temp. when water heating can stop
		float	tempHBOff	=	TEMP_HB_OFF ;	// high temp. when buffer heating can stop
		float	diffTempCollHB ;
		float	diffTempCollWW ;
		float	tempWW ;
		float	tempHB ;
		float	tempCol ;
		int	lastMode	=	MODE_INVALID ;
		int	mode	=	MODE_INVALID ;
		int	changeMode ;
		int	startupDelay	=	1 ;
		int	tempWWcf ;				// index of node["TempWWo"], WarmWater
		int	tempWWu ;				// index of node["TempWWu"], WarmWater
		int	tempWWm ;				// index of node["TempWWm"], WarmWater
		int	tempWWo ;				// index of node["TempWWo"], WarmWater
		int	tempHBu ;				// index of node["TempHBu"], HeatingBuffer
		int	tempHBmu ;				// index of node["TempHBmu"], HeatingBuffer
		int	tempHBmo ;				// index of node["TempHBmo"], HeatingBuffer
		int	tempHBo ;				// index of node["TempHBo"], HeatingBuffer
		int	tempCol1 ;				// index of node["TempCol1"], SolarCollector
		int	tempColReturn ;				// index of node["TempCol1"], SolarCollector
		time_t	lastOffTime	=	0L ;
		time_t	lastOnTime	=	0L ;
	/**
	 * define shared memory segment #0: COM Table
 	 */
		key_t	shmCOMKey	=	SHM_COM_KEY ;
		int	shmCOMFlg	=	IPC_CREAT | 0600 ;
		int	shmCOMId ;
		int	shmCOMSize	=	256 ;
		int	 *sizeTable ;
	/**
	 * define shared memory segment #1: OPC Table with buffer
 	 */
		key_t	shmOPCKey	=	SHM_OPC_KEY ;
		int	shmOPCFlg	=	IPC_CREAT | 0600 ;
		int	shmOPCId ;
		int	shmOPCSize ;
		node	 *data ;
	/**
	 * define shared memory segment #2: KNX Table with buffer
 	 */
		key_t	shmKNXKey	=	SHM_KNX_KEY ;
		int	shmKNXFlg	=	IPC_CREAT | 0600 ;
		int	shmKNXId ;
		int	shmKNXSize	=	65536 * sizeof( float) ;
		float	 *floats ;
		int	 *ints ;
	/**
	 * define shared memory segment #3: cross-referecne table
 	 */
		key_t	shmCRFKey	=	SHM_CRF_KEY ;
		int	shmCRFFlg	=	IPC_CREAT | 0600 ;
		int	shmCRFId ;
		int	shmCRFSize	=	65536 * sizeof( int) ;
		int	 *crf ;
		char	logMsg[256] ;
		char		iniFilename[]	=	"knx.ini" ;

	/**
	 * setup the shared memory for EIB Receiving Buffer
	 */
	ownPID	=	getpid() ;
	signal( SIGTERM, sigHandlerTERM) ;
	signal( SIGINT, sigHandler) ;
	signal( SIGHUP, sigHandlerHUP) ;
	setbuf( stdout, NULL) ;
	strcpy( progName, *argv) ;

	/**
	 *
	 */
	runLevel	=	1 ;
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 * get command line options
	 */
	while (( opt = getopt( argc, argv, "D:FQ:d:m:wb?")) != -1) {
		switch ( opt) {
		/**
		 * general options
		 */
		case    'D'     :
			debugLevel	=	atoi( optarg) ;
			break ;
		case    'F'     :
			cfgDaemon	=	0 ;
			break ;
		case    'Q'     :
			cfgQueueKey	=	atoi( optarg) ;
			break ;
		/**
		 * application specific options
		 */
		case    'b'     :
			cfgHdlBuffer       =       1 ;
			break ;
		case    'd'     :
			startupDelay    =       atoi( optarg) ;
			break ;
		case    'm'     :
			mode    =       atoi( optarg) ;
			break ;
		case    'w'     :
			cfgHdlWater        =       1 ;
			break ;
		case    '?'     :
			help() ;
			exit(0) ;
			break ;
		default :
			help() ;
			exit( -1) ;
			break ;
		}
	}
	sigHandlerHUP( 0) ;

	/**
	 * daemonize this process, if so required
	 */
	if ( cfgDaemon) {
		logit( "daemon mode ...") ;
		pid	=	fork() ;
		if ( pid < 0) {
			logit( "pid < 0, exiting ...") ;
			exit( EXIT_FAILURE) ;
		}
		if ( pid > 0) {
			logit( "pid > 0, exiting ...") ;
			exit( EXIT_SUCCESS) ;
		}
		sid	=	setsid() ;
		if ( sid < 0) {
			logit( "sid < 0, exiting ...") ;
			exit( EXIT_FAILURE) ;
		}
		logit( "closing std i/o ...") ;
		close( STDIN_FILENO) ;
		close( STDOUT_FILENO) ;
		close( STDERR_FILENO) ;
	}
	logit(  "starting up ...") ;
	ownPID	=	getpid() ;
	sleep( startupDelay) ;

	/**
	 * get and attach the shared memory for COMtable
	 */
	if (( shmCOMId = shmget( shmCOMKey, shmCOMSize, 0600)) < 0) {
		logit(  "-1") ;
		exit( -1) ;
	}
	if (( sizeTable = (int *) shmat( shmCOMId, NULL, 0)) == (int *) -1) {
		logit(  "-2") ;
		exit( -1) ;
	}
	shmCOMSize      =       sizeTable[ SIZE_COM_TABLE] ;
	shmOPCSize      =       sizeTable[ SIZE_OPC_TABLE] ;
	shmKNXSize      =       sizeTable[ SIZE_KNX_TABLE] ;
	shmCRFSize      =       sizeTable[ SIZE_CRF_TABLE] ;

	/**
	 * setup the shared memory for OPCtable
	 */
	if (( shmOPCId = shmget (shmOPCKey, shmOPCSize, shmOPCFlg)) < 0) {
		exit( -101);
	}
	if (( data = (node *) shmat(shmOPCId, NULL, 0)) == (node *) -1) {
		exit( -102);
	}

	/**
	 * setup the shared memory for KNXtable
	 */
	if (( shmKNXId = shmget( shmKNXKey, shmKNXSize, IPC_CREAT | 0600)) < 0) {
		exit( -111) ;
	}
	if (( floats = (float *) shmat( shmKNXId, NULL, 0)) == (float *) -1) {
		exit( -112) ;
	}
	ints    =       (int *) floats ;

	/**
	 * setup the shared memory for CRFtable
	 */
	if (( shmCRFId = shmget( shmCRFKey, shmCRFSize, IPC_CREAT | 0600)) < 0) {
		exit( -121) ;
	}
	if (( crf = (int *) shmat( shmCRFId, NULL, 0)) == (int *) -1) {
		exit( -122) ;
	}

	/**
	 * get some indices from the
	 */
	pumpSolar	=	getEntry( data, lastDataIndexC, "HEAT_PUMP_SOLAR_SETONOFF") ;
	valveSolar	=	getEntry( data, lastDataIndexC, "HEAT_VALVE_SOLAR_SETOPENCLOSE") ;
	tempWWcf	=	getEntry( data, lastDataIndexC, "HEAT_TEMP_SOLAR_ACTVALUE_ZONE_1") ;
	tempWWu		=	getEntry( data, lastDataIndexC, "HEAT_TEMP_WASSER_ACTVALUE_ZONE_1") ;
	tempWWm		=	getEntry( data, lastDataIndexC, "HEAT_TEMP_WASSER_ACTVALUE_ZONE_2") ;
	tempWWo		=	getEntry( data, lastDataIndexC, "HEAT_TEMP_WASSER_ACTVALUE_ZONE_3") ;
	tempHBu		=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_1") ;
	tempHBmu	=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_2") ;
	tempHBmo	=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_3") ;
	tempHBo		=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_4") ;
	tempCol1	=	getEntry( data, lastDataIndexC, "HEAT_TEMP_SOLAR_ACTVALUE_ZONE_1") ;
	tempColReturn	=	getEntry( data, lastDataIndexC, "HEAT_TEMP_SOLAR_ACTVALUE_ZONE_2") ;

	if ( cfgDaemon == 0) {
		_debug( 1, progName, "pumpSolar at index ...... : %d", pumpSolar) ;
		_debug( 1, progName, "valveSolar at index ..... : %d", valveSolar) ;
		_debug( 1, progName, "tempWWcf at index ....... : %d", tempWWcf) ;
		_debug( 1, progName, "tempWW* at index ........ : %d/%d/%d", tempWWu, tempWWm, tempWWo) ;
		_debug( 1, progName, "tempHB* at index ........ : %d/%d/%d/%d", tempHBu, tempHBmu, tempHBmo, tempHBo) ;
		_debug( 1, progName, "tempCol1 at index ....... : %d", tempCol1) ;
		_debug( 1, progName, "tempColReturn at index .. : %d", tempColReturn) ;
	}

	/**
	 * try to determine the current mode of the solar-module
	 */
	logit( "trying to setermine current status") ;
	actTime	=	time( NULL) ;
	myTime	=	 *localtime( &actTime) ;
	pumpOffTime	=	0 ;
	pumpOnTime	=	0 ;
	if ( mode == MODE_INVALID) {
		if ( data[pumpSolar].val.i == 1) {
			if ( data[valveSolar].val.i == VALVE_SOLAR_WW) {
				mode	=	MODE_WATER ;
				pumpOnTime	=	actTime ;
			} else {
				mode	=	MODE_BUFFER ;
				pumpOnTime	=	actTime ;
			}
		} else {
			mode	=	MODE_STOPPED ;
			pumpOffTime	=	actTime ;
		}
	}
	currentMode	=	mode ;
	logit( "daemon mode ...") ;

	/**
	 *
	 */
	myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_WR | APN_INTRN) ;
	while ( runLevel >= 0) {

		/**
		 *
		 */
		actTime	=	time( NULL) ;
		myTime	=	 *localtime( &actTime) ;
		if ( pumpOnTime > 0) {
			pumpUpTime	=	actTime - pumpOnTime ;
		} else {
			pumpUpTime	=	0 ;
		}
		if ( pumpOffTime > 0) {
			pumpDownTime	=	actTime - pumpOffTime ;
		} else {
			pumpDownTime	=	0 ;
		}

		/**
		 *
		 */
		changeMode	=	1 ;
		lastMode	=	mode ;
		tempWW	=	data[tempWWcf].val.f ;
		tempWW	=	data[tempWWu].val.f ;
		tempWW	=	( data[tempWWu].val.f + data[tempWWm].val.f + data[tempHBo].val.f) / 3.0 ;
		tempWW	=	( data[tempWWu].val.f) / 1.0 ;
		tempHB	=	( data[tempHBu].val.f + data[tempHBmu].val.f + data[tempHBmo].val.f + data[tempHBo].val.f) / 4.0 ;
		tempHB	=	( data[tempHBu].val.f + data[tempHBmo].val.f + data[tempHBo].val.f) / 3.0 ;

		/**
		 * as our temperature sensor is located closer to the return (returning to collector water)
		 * it doesn't really reflect the collectors temperature. therefor the collector temperatur
		 * is corrected by 1.25, making it hotter than the temperature sensors tells
		 */
		tempCol	=	data[tempCol1].val.f * 1.25 ;
		diffTempCollWW	=	tempCol - tempWW ;
		diffTempCollHB	=	tempCol - tempHB ;

		if ( cfgDaemon == 0) {
			_debug( 1, progName, "current mode .................. : %d:'%s'", currentMode, modeText[currentMode]) ;
			_debug( 1, progName, "temp. water, actual ........... : %5.1f (%5.1f/%5.1f/%5.1f)", tempWW, data[tempWWu].val.f, data[tempWWm].val.f, data[tempWWo].val.f) ;
			_debug( 1, progName, "temp. buffer, actual .......... : %5.1f (%5.1f/%5.1f/%5.1f/%5.1f)", tempHB, data[tempHBu].val.f, data[tempHBmu].val.f, data[tempHBmo].val.f, data[tempHBo].val.f) ;
			_debug( 1, progName, "temp. collector return ........ : %5.1f", tempColReturn) ;
			_debug( 1, progName, "temp. solCol1, actual ......... : %5.1f", tempCol) ;
			_debug( 1, progName, "temp. diff. tank, actual ...... : %5.1f (max: %5.1f)", diffTempCollWW, tempWWOff) ;
			_debug( 1, progName, "temp. diff. buffer, actual .... : %5.1f (max: %5.1f)", diffTempCollHB, tempHBOff) ;
			_debug( 1, progName, "pump down time ................ : %d", pumpDownTime) ;
			_debug( 1, progName, "pump running time ............. : %d", pumpUpTime) ;
		}

		/**
		 *
		 */
		while ( changeMode) {
			changeMode	=	0 ;
			switch( mode) {
			case	MODE_STOPPED	:
				/**
				 * IF collector temperature at least 5 degress above current water temperature
				 *	AND water temperature below max. water temperature
				 *	AND collectore temperature above minimum collector temperature
				 *		start heating water
				 * ELSEIF collector temperature at least 5 degress above buffer temperature
				 *	AND buffer temperatue below max. buffer temperature
				 *	AND collector temperature above minimum collector temperature
				 *		start heating buffer
				 * ELSE
				 *	stop heating
				 */
				if ( diffTempCollWW >= 5.0 && tempWW < tempWWOff && tempCol >= TEMP_COL_MIN_ON && cfgHdlWater == 1) {
					if ( cfgDaemon == 0) {
						_debug( 1, progName, "shall be heating water ...") ;
					}
					mode	=	MODE_WATER ;
					pumpOffTime	=	0 ;
					pumpOnTime	=	actTime ;
				} else if ( diffTempCollHB >= 5.0 && tempHB < tempHBOff && tempCol >=TEMP_COL_MIN_ON && cfgHdlBuffer == 1) {
					if ( cfgDaemon == 0) {
						_debug( 1, progName, "shall be heating buffer ...") ;
					}
					mode	=	MODE_BUFFER ;
					pumpOffTime	=	0 ;
					pumpOnTime	=	actTime ;
				} else {
					if ( cfgDaemon == 0) {
						_debug( 1, progName, "shall stop ...") ;
					}
					mode	=	MODE_STOPPED ;
					pumpOffTime	=	actTime ;
					pumpOnTime	=	0 ;
				}
				break ;
			case	MODE_WATER	:
				if ((( diffTempCollWW <= 5.0 || tempCol < TEMP_COL_MIN_OFF) && pumpUpTime > 300) || tempWW >= tempWWOff) {
					if ( cfgDaemon == 0) {
						_debug( 1, progName, "shall stop ...") ;
					}
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				} else if ( cfgHdlWater == 0) {
					mode	=	MODE_STOPPED ;
				}
				break ;
			case	MODE_BUFFER	:
				if ((( diffTempCollHB <= 5.0 || tempCol < TEMP_COL_MIN_OFF) && pumpUpTime > 300) || tempHB >= tempHBOff ) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				} else if (( diffTempCollWW >= 5.0 && tempWW < tempWWOff && tempCol >= TEMP_COL_MIN_ON) && pumpUpTime > 300) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				} else if ( cfgHdlBuffer == 0) {
					mode	=	MODE_STOPPED ;
				}
				break ;
			}
		}
		lastMode	=	mode ;
		switch ( mode) {
		case	MODE_STOPPED	:
			setModeStopped( myEIB, data) ;
			break ;
		case	MODE_WATER	:
			setModeWater( myEIB, data) ;
			break ;
		case	MODE_BUFFER	:
			setModeBuffer( myEIB, data) ;
			break ;
		}
		_debug( 1, progName, "going to sleep ... ") ;
		sleep( 5) ;
	}

	/**
	 * destroy shared memory: cross-reference table
	 */
	logit( "detaching cross-reference-table") ;
	shmdt( crf) ;						// detach shared memory of knxBus

	/**
	 * destroy shared memory:
	 */
	logit( "detaching ") ;
	shmdt( floats) ;					// detach shared memory of knxBus

	/**
	 * destroy shared memory:
	 */
	logit( "detaching ") ;
	shmdt( data) ;						// detach shared memory of knxBus

	/**
	 * destroy shared memory: shared-memory-size table
	 */
	logit( "detaching ") ;
	shmdt( sizeTable) ;					// detach shared memory

	/**
	 * close EIB port
	 */
	logit(  "shutting down up ...") ;
	eibClose( myEIB) ;
	exit( status) ;
}

void	setModeStopped( eibHdl *_myEIB, node *data) {
	int     reset   =       0 ;
	if ( currentMode != MODE_STOPPED) {
		reset   =       1 ;
	} else if ( data[pumpSolar].val.i != PUMPE_SOLAR_AUS) {
		logit(  "ALERT ... Solar Heating Setting (on/off) is WRONG ...") ;
		reset   =       1 ;
	}
	if ( reset) {
		if ( data[pumpSolar].val.i != PUMPE_SOLAR_AUS) {
			eibWriteBitIA( _myEIB, data[pumpSolar].knxGroupAddr, PUMPE_SOLAR_AUS, 0) ;
		}
		currentMode     =       MODE_STOPPED ;
	}
}

void	setModeWater( eibHdl *_myEIB, node *data) {
	int     reset   =       0 ;
	if ( currentMode != MODE_WATER) {
		reset   =       1 ;
	} else if ( data[pumpSolar].val.i != PUMPE_SOLAR_EIN) {
		logit(  "ALERT ... Solar Heating Setting (on/off) is WRONG ...") ;
		reset   =       1 ;
	} else if ( data[valveSolar].val.i != MISCHER_SOLAR_WASSER) {
		logit(  "ALERT ... Solar Heating Setting (valve) is WRONG ...") ;
		reset   =       1 ;
	}
	if ( reset) {
		if ( data[pumpSolar].val.i != PUMPE_SOLAR_EIN) {
			eibWriteBitIA( _myEIB, data[pumpSolar].knxGroupAddr, PUMPE_SOLAR_EIN, 0) ;
		}
		if ( data[valveSolar].val.i != MISCHER_SOLAR_WASSER) {
			sleep( 1) ;
			eibWriteBitIA( _myEIB, data[valveSolar].knxGroupAddr, MISCHER_SOLAR_WASSER, 0) ;
		}
		currentMode     =       MODE_WATER ;
	}
}

void	setModeBuffer( eibHdl *_myEIB, node *data) {
	int     reset   =       0 ;
	if ( currentMode != MODE_BUFFER) {
		reset   =       1 ;
	} else if ( data[pumpSolar].val.i != PUMPE_SOLAR_EIN) {
		logit(  "ALERT ... Solar Heating Setting (on/off) is WRONG ...") ;
		reset   =       1 ;
	} else if ( data[valveSolar].val.i != MISCHER_SOLAR_PUFFER) {
		logit(  "ALERT ... Solar Heating Setting (valve) is WRONG ...") ;
		reset   =       1 ;
	}
	if ( reset) {
		if ( data[pumpSolar].val.i != PUMPE_SOLAR_EIN) {
				eibWriteBitIA( _myEIB, data[pumpSolar].knxGroupAddr, PUMPE_SOLAR_EIN, 0) ;
		}
		if ( data[valveSolar].val.i != MISCHER_SOLAR_PUFFER) {
			sleep( 1) ;
			eibWriteBitIA( _myEIB, data[valveSolar].knxGroupAddr, MISCHER_SOLAR_PUFFER, 0) ;
		}
		currentMode     =       MODE_BUFFER ;
	}
}

void	logit( char *_fmt, ...) {
	va_list	arglist ;
	va_start( arglist, _fmt );
	openlog( NULL, LOG_PID|LOG_CONS, LOG_USER);
	syslog( LOG_INFO, _fmt, arglist) ;
	va_end( arglist ) ;
	closelog();
}

void	help() {
}
