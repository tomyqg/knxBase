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
 * hdlpellet.c
 *
 * handling for "our" pellet stove
 *
 * Revision history
 *
 * date			rev.	who	what
 * ----------------------------------------------------------------------------
 * 2015-11-20	PA1		khw		inception;
 * 2016-01-12	PA2		khw		adapted to new architecture of eib.c;
 * 2016-01-25	PA3		khw		added option for waiting time after startup;
 * 2016-03-30	PA4		khw		added timing per day;
 * 2016-05-13	PA5		khw		modified timer to break down to the minute;
 * 2016-11-05	PA6		khw		added single-shot force mode to override
 *								timer mode in case pellet stove is wanted;
 * 2017-01-05	PA7		khw		added function to temporarily take over
 *								buffer heating when outside temp. falls below
 *								a given limit
 * 2018-08-12	PA8		khw		adapted to systemd;
 * 2019-05-11	PA9		khw		adapted to new structure of group addresses;
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
#include	"knxlog.h"
#include	"inilib.h"

typedef	enum	modePS	{
			MODE_STOPPED	=	0
		,	MODE_WATER	=	1
		,	MODE_BUFFER	=	2
	}	modePS ;

#define	GAP_OFFON	600		// may not be switched on before this amount of seconds has elapsed
#define	GAP_ONOFF	600		// may not be switched off before this amount of seconds has elapsed

#define	TEMP_WW_ON	48
#define	TEMP_WW_OFF	53
#define	TEMP_HB_ON	33		// summer: 30, winter: 33
#define	TEMP_HB_OFF	36		// summer: 35, winter: 38
#define	TEMP_HP_OFF	-3.0	// temperature when heatpump is taken out of service by pellet stove
#define	TEMP_HP_ON	-1.0	// temperature when heatpump takes over again from pellet stove

extern	void	setModeStopped( eibHdl *, node *, modePS *_mode) ;
extern	void	setModeWater( eibHdl *, node *, modePS *_mode) ;
extern	void	setModeBuffer( eibHdl *, node *, modePS *_mode) ;

extern	void	help() ;
extern	void	logit( char *_fmt, ...) ;

char	progName[64] ;
pid_t	ownPID ;
int		runLevel ;

int	pelletStove ;
int	valvePelletStove ;

char	*modeText[3]	=	{
		"not working"
	,	"heating water tank"
	,	"heating buffer"
	} ;

/**
 *
 */
int	cfgQueueKey		=	10031 ;
int	cfgSenderAddr	=	1 ;
int	cfgDaemon		=	1 ;				// default:	run as daemon process
int	cfgConsiderTime	=	1 ;
int	cfgHandleBuffer	=	0 ;		// default: do NOT care about the buffer
int	cfgHandleWater	=	1 ;		// default: do care about the warm-water

/**
 *
 */
void	sigHandler( int _sig) {
	char	buffer[128] ;
	runLevel	-=	1 ;
	sprintf( buffer, "received signal TERM; new runLevel = %d\n", runLevel) ;
	logit( buffer) ;
}

/**
 *
 */
void	sigHandlerTERM( int _sig) {
	char	buffer[128] ;
	runLevel	=	-1 ;
	sprintf( buffer, "received signal TERM; new runLevel = %d\n", runLevel) ;
	logit( buffer) ;
}

/**
 *
 */
void	sigHandlerHUP( int _sig) {
	char	buffer[128] ;
	sprintf( buffer, "hdlpellet water := %d, buffer := %d", cfgHandleWater, cfgHandleBuffer) ;
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
	} else if ( strcmp( _block, "[hdlpellet]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		} else if ( strcmp( _para, "considerTime") == 0) {
			cfgConsiderTime	=	atoi( _value) ;
		} else if ( strcmp( _para, "handleBuffer") == 0) {
			cfgHandleBuffer	=	atoi( _value) ;
		} else if ( strcmp( _para, "handleWater") == 0) {
			cfgHandleWater	=	atoi( _value) ;
		}
	}
}

/**
 *
 */
int	main( int argc, char *argv[]) {
		eibHdl	*myEIB ;
		pid_t	pid, sid ;
		int		status		=	0 ;
		int		opt ;
		char	timeBuffer[64] ;
		int		timeMin ;				// daytime in minutes
		int		pumpRunTime ;			// time pump is running in seconds
		int		pumpDownTime ;			// time pump is idle in seconds
		int		pumpOnTime ;			// time when solar pump was switched on last
		int		pumpOffTime ;			// time when solar pump was switched off
		int		cfgHandleBuffer ;				// time when solar pump was switched off
		modePS	mode		=	MODE_STOPPED ;
	/**
	 *
	 */
		float	tempWWOn	=	TEMP_WW_ON ;	// low temp. when water heating
												// needs to start
		float	tempWWOff	=	TEMP_WW_OFF ;	// high temp. when water heating
												// can stop
		float	tempHBOn	=	TEMP_HB_ON ;	// low temp. when buffer heating
												// needs to start
		float	tempHBOff	=	TEMP_HB_OFF ;	// high temp. when buffer heating
												// can stop
		float	tempWW ;
		float	tempHB ;
		int		ndxTempAmbient ;
		int		ndxUsePelletForBuffer ;
		float	tempOutside ;
		int		changeMode ;
		int		startupDelay	=	5 ;		// default 0 seconds startup delay
		int		tempWWu ;				// point to node["TempWWu"],
										// WarmWater
		int		tempHBu ;				// point to node["TempHBu"],
		int		tempHBmu ;				// point to node["TempHBmu"],
		int		tempHBmo ;				// point to node["TempHBmo"],
		int		tempHBo ;				// point to node["TempHBo"],
		time_t	lastOffTime	=	0L ;
		time_t	lastOnTime	=	0L ;
		int	handlerActive ;
		int	forceOnce ;
		int	timerOn ;
	/**
	 * define shared memory segment #0: COM Table
 	 */
		key_t	shmCOMKey	=	SHM_COM_KEY ;
		int	shmCOMFlg	=	IPC_CREAT | 0600 ;
		int	shmCOMId ;
		int	shmCOMSize	=	256 ;
		int	*sizeTable ;
	/**
	 * define shared memory segment #1: OPC Table with buffer
 	 */
		key_t	shmOPCKey	=	SHM_OPC_KEY ;
		int	shmOPCFlg	=	IPC_CREAT | 0600 ;
		int	shmOPCId ;
		int	shmOPCSize ;
		node	*data ;
	/**
	 * define shared memory segment #2: KNX Table with buffer
 	 */
		key_t	shmKNXKey	=	SHM_KNX_KEY ;
		int	shmKNXFlg	=	IPC_CREAT | 0600 ;
		int	shmKNXId ;
		int	shmKNXSize	=	65536 * sizeof( float) ;
		float	*floats ;
		int	*ints ;
	/**
	 * define shared memory segment #3: Cross-Reference table
 	 */
			key_t		shmCRFKey	=	SHM_CRF_KEY ;
			int		shmCRFFlg	=	IPC_CREAT | 0600 ;
			int		shmCRFId ;
			int		shmCRFSize	=	65536 * sizeof( int) ;
			int		*crf ;
			time_t		actTime ;
	struct	tm			myTime ;
			int		timerMode	=	0 ;
			char		iniFilename[]	=	"knx.ini" ;

	/**
	 *
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
	while (( opt = getopt( argc, argv, "D:FQ:d:wb?")) != -1) {
		switch ( opt) {
		/**
		 * general options
		 */
		case	'D'	:
			debugLevel	=	atoi( optarg) ;
			break ;
		case    'F'     :
			cfgDaemon	=	0 ;
			break ;
		case	'Q'	:
			cfgQueueKey	=	atoi( optarg) ;
			break ;
		/**
		 * application specific options
		 */
		case	'b'	:
			cfgHandleBuffer	=	1 ;
			break ;
		case	'd'	:
			startupDelay	=	atoi( optarg) ;
			break ;
		case	'w'	:
			cfgHandleWater	=	1 ;
			break ;
		case	'?'	:
			help() ;
			exit(0) ;
			break ;
		default	:
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
	pelletStove			=	getEntry( data, lastDataIndexC, "HEAT_PELLET_SETONOFF") ;
	valvePelletStove	=	getEntry( data, lastDataIndexC, "HEAT_VALVE_PELLET_SETOPENCLOSE") ;

	tempWWu		=	getEntry( data, lastDataIndexC, "HEAT_TEMP_WASSER_ACTVALUE_ZONE_1") ;
	tempHBu		=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_1") ;
	tempHBmu	=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_2") ;
	tempHBmo	=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_3") ;
	tempHBo		=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_4") ;

	ndxTempAmbient	=	getEntry( data, lastDataIndexC, "HEAT_TEMP_UMGEBUNG_ACTVALUE_ZONE_1") ;

	_debug( 1, progName, "pelletStove at index .......... : %d (knx: %05d)", pelletStove, data[pelletStove].knxGroupAddr) ;
	_debug( 1, progName, "valvePelletStove at index ..... : %d (knx: %05d)", valvePelletStove, data[valvePelletStove].knxGroupAddr) ;

	handlerActive	=	getEntry( data, lastDataIndexC, "HEAT_PELLET_HANDLER_ACTIVE") ;
	forceOnce		=	getEntry( data, lastDataIndexC, "HEAT_PELLET_FORCEON") ;
	timerOn			=	getEntry( data, lastDataIndexC, "HEAT_PELLET_TIMER_ACTIVE") ;
	ndxUsePelletForBuffer	=	getEntry( data, lastDataIndexC, "HEAT_PELLET_ALLOW_HEATING") ;

	data[ forceOnce].val.i	=	0 ;
	data[ handlerActive].val.i	=	1 ;
	data[ timerOn].val.i	=	1 ;

	/**
	 * try to determine the current mode of the pellet-module
	 */
	_debug( 1, progName, "trying to determine current status") ;
	if ( data[pelletStove].val.i == 1) {
		if ( data[valvePelletStove].val.i == VALVE_PS_WW) {
			mode	=	MODE_WATER ;
		} else {
			mode	=	MODE_BUFFER ;
		}
	} else {
		mode	=	MODE_STOPPED ;
	}

	/**
	 *
	 */
	myEIB	=	eibOpen( cfgSenderAddr, 0, cfgQueueKey, progName, APN_WR | APN_INTRN) ;
	while ( runLevel >= 0) {

		/**
		 *
		 */
		actTime	=	time( NULL) ;
		myTime	=	*localtime( &actTime) ;
		if ( cfgConsiderTime != 0) {
			timeMin	=	myTime.tm_hour * 60 + myTime.tm_min ;
			timerMode	=	0 ;
			switch ( myTime.tm_wday) {
			case	6	:		// saturday
				if ( timeMin >= ( 5 * 60) && timeMin <= ( 10 * 60)) {
					timerMode	=	1 ;
				} else if ( timeMin >= ( 19 * 60) && timeMin <= ( 21 * 60)) {
					timerMode	=	1 ;
				}
				break ;
			case	0	:		// sunday
				if ( timeMin >= ( 7 * 60) && timeMin <= ( 12 * 60)) {
					timerMode	=	1 ;
				} else if ( timeMin >= ( 19 * 60) && timeMin <= ( 21 * 60)) {
					timerMode	=	1 ;
				}
				break ;
			default	:			// monday - friday
				if ( timeMin >= ( 5 * 60) && timeMin <= ( 9 * 60)) {
					timerMode	=	1 ;
				} else if ( timeMin >= ( 22 * 60) && timeMin <= ( 23 * 60)) {
					timerMode	=	1 ;
				}
				break ;
			}
			_debug( 1, progName, "timer mode .................... : %d", timerMode) ;
		} else {
			_debug( 1, progName, "timer mode .................... : not considered", timerMode) ;
			timerMode	=	1 ;
		}

		tempOutside	=	data[ndxTempAmbient].val.f ;
		/**
		 *
		if ( tempOutside < TEMP_HP_OFF) {
			cfgHandleBuffer	=	1 ;
			if ( data[ndxUsePelletForBuffer].val.i != 1) {
				_debug( 1, progName, "setting EIB value to ForcePellet4Buffer") ;
				eibWriteBitIA( myEIB, 9734, 1, 0) ;
			}
		} else if ( tempOutside > TEMP_HP_ON) {
			cfgHandleBuffer	=	cfgHandleBuffer ;
			if ( data[ndxUsePelletForBuffer].val.i != 0) {
				_debug( 1, progName, "setting EIB value to normal mode") ;
				eibWriteBitIA( myEIB, 9734, 0, 0) ;
			}
		}
		 */

		/**
		 *
		 */
		changeMode	=	1 ;

		tempWW	=	data[tempWWu].val.f ;
		tempHB	=	( data[tempHBu].val.f + data[tempHBmu].val.f + data[tempHBmo].val.f + data[tempHBo].val.f) / 4.0 ;
		tempHB	=	( data[tempHBu].val.f + data[tempHBmo].val.f + data[tempHBo].val.f) / 3.0 ;

		if ( cfgDaemon == 0) {
			_debug( 1, progName, "week day (0= sun, ... 6=sat)... : %d", myTime.tm_wday) ;
			_debug( 1, progName, "hour .......................... : %02d:%02d", myTime.tm_hour, myTime.tm_min) ;
			_debug( 1, progName, "timer mode .................... : %d", timerMode) ;
			_debug( 1, progName, "current mode .................. : %d:'%s'", mode, modeText[mode]) ;
			_debug( 1, progName, "temp. warm water, actual ...... : %5.1f ( %5.1f ... %5.1f)", tempWW, tempWWOn, tempWWOff) ;
			_debug( 1, progName, "temp. buffer, actual .......... : %5.1f (%5.1f/%5.1f/%5.1f/%5.1f)", tempHB, data[tempHBu].val.f, data[tempHBmu].val.f, data[tempHBmo].val.f, data[tempHBo].val.f) ;
			_debug( 1, progName, "handler active ................ : %2d", data[handlerActive].val.i) ;
			_debug( 1, progName, "force mode .................... : %2d", data[forceOnce].val.i) ;
			_debug( 1, progName, "timer mode .................... : %2d", data[timerMode].val.i) ;
			_debug( 1, progName, "outside temperature ........... : %5.1f ( %5.1f ... %5.1f)", tempOutside, TEMP_HP_ON, TEMP_HP_OFF) ;
			_debug( 1, progName, "warmwater mode ................ : %2d", cfgHandleWater) ;
			_debug( 1, progName, "buffer mode default ........... : %2d", cfgHandleBuffer) ;
			_debug( 1, progName, "buffer mode actual ............ : %2d", cfgHandleBuffer) ;
		}
		while ( changeMode) {
			changeMode	=	0 ;
			switch( mode) {
			case	MODE_STOPPED	:
				if ( tempWW <= tempWWOn && cfgHandleWater && ( timerMode == 1 || data[forceOnce].val.i == 1)) {
					_debug( 1, progName, "starting Water ") ;
					eibWriteBitIA( myEIB, data[forceOnce].knxGroupAddr, 0, 0) ;
					mode	=	MODE_WATER ;
				} else if ( tempHB <= tempHBOn && cfgHandleBuffer) {
					_debug( 1, progName, "starting Buffer ") ;
					mode	=	MODE_BUFFER ;
				} else {
					mode	=	MODE_STOPPED ;
				}
				break ;
			case	MODE_WATER	:
				if (( tempWW >= tempWWOff)) {	//  || timerMode == 0)) {
					changeMode	=	1 ;
					mode	=	MODE_STOPPED ;
				}
				break ;
			case	MODE_BUFFER	:
				if (( tempWW <= tempWWOn && cfgHandleWater) && pumpRunTime > 300) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				} else if (( tempHB >= tempHBOff && cfgHandleBuffer)) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				} else if ( ! cfgHandleBuffer) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				}
				break ;
			default	:
				changeMode	=	1 ;
				mode	=	MODE_STOPPED ;
				break ;
			}
		}
		switch ( mode) {
		case	MODE_STOPPED	:
			setModeStopped( myEIB, data, &mode) ;
			break ;
		case	MODE_WATER	:
			setModeWater( myEIB, data, &mode) ;
			break ;
		case	MODE_BUFFER	:
			setModeBuffer( myEIB, data, &mode) ;
			break ;
		}
		sleep( 30) ;
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

void	setModeStopped( eibHdl *_myEIB, node *data, modePS *_mode) {
	int	reset	=	1 ;
	if ( *_mode != MODE_STOPPED) {
		*_mode	=	MODE_STOPPED ;
		reset	=	0 ;
	}
	if ( data[pelletStove].val.i != 0) {
		if ( reset) {
			logit( "ALERT ... Pellet Stove Setting (on/off) is WRONG ...") ;
		}
		eibWriteBitIA( _myEIB, data[pelletStove].knxGroupAddr, 0, 0) ;
	}
	sleep( 1) ;
	if ( data[valvePelletStove].val.i != VALVE_PS_WW) {
		if ( reset) {
			logit( "ALERT ... Pellet Stove Setting (valve) is WRONG ...") ;
		}
		eibWriteBitIA( _myEIB, data[valvePelletStove].knxGroupAddr, VALVE_PS_WW, 0) ;
	}
}

void	setModeWater( eibHdl *_myEIB, node *data, modePS *_mode) {
	int	reset	=	1 ;
	if ( *_mode != MODE_WATER) {
		*_mode	=	MODE_WATER ;
		reset	=	0 ;
	}
	if ( data[pelletStove].val.i != 1) {
		if ( reset) {
			logit( "ALERT ... Pellet Stove Setting (on/off) is WRONG ...") ;
		}
		eibWriteBitIA( _myEIB, data[pelletStove].knxGroupAddr, 1, 0) ;
	}
	sleep( 1) ;
	if ( data[valvePelletStove].val.i != VALVE_PS_WW) {
		if ( reset) {
			logit( "ALERT ... Pellet Stove Setting (valve) is WRONG ...") ;
		}
		eibWriteBitIA( _myEIB, data[valvePelletStove].knxGroupAddr, VALVE_PS_WW, 0) ;
	}
}

void	setModeBuffer( eibHdl *_myEIB, node *data, modePS *_mode) {
	int	reset	=	1 ;
	if ( *_mode != MODE_BUFFER) {
		*_mode	=	MODE_BUFFER ;
		reset	=	0 ;
	}
	if ( data[pelletStove].val.i != 1) {
		if ( reset) {
			logit("ALERT ... Pellet Stove Setting (on/off) is WRONG ...") ;
		}
		eibWriteBitIA( _myEIB, data[pelletStove].knxGroupAddr, 1, 0) ;
	}
	sleep( 1) ;
	if ( data[valvePelletStove].val.i != VALVE_PS_HB) {
		if ( reset) {
			logit( "ALERT ... Pellet Stove Setting (valve) is WRONG ...") ;
		}
		eibWriteBitIA( _myEIB, data[valvePelletStove].knxGroupAddr, VALVE_PS_HB, 0) ;
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
	printf( "%s: %s [-D=<debugLevel>] [-F] [-w] [-b] \n\n", progName, progName) ;
	printf( "-F force process to run in foreground, do not daemonize\n") ;
	printf( "-w handle water tank\n") ;
	printf( "-b handle heating buffer\n") ;
}
