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
 * hdlheatpump.c
 *
 * Handling for the heatpump, which can heat up the buffer and the water tank.
 *
 * Revision history
 *
 * date		rev.		who		what
 * ----------------------------------------------------------------------------
 * 2015-12-14	PA1		khw		copied form hdlpellet.c and modified;
 * 2016-01-12	PA2		khw		adapted to new architecture of eib.c;
 * 2016-01-25	PA3		khw		added option for waiting time after startup;
 * 2017-01-18	PA4		khw		added function to increase buffer temperature
 *								when outside temperature falls below 0degC;
 * 2018-08-12	PA5		khw		adapted to systemd;
 * 2018-10-20	PA6		khw		fixed some major fault;
 * 2019-05-11	PA7		khw		adapted to new structure of group addresses;
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<stdarg.h>
#include	<string.h>
#include	<strings.h>
#include	<stdbool.h>
#include	<unistd.h>
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

typedef	enum	modeHP	{
			MODE_STOPPED	=	0
		,	MODE_WATER	=	1
		,	MODE_BUFFER	=	2
	}	modeHP ;

typedef	struct	processDataHP	{
		int		freeToRun ;				// heatpump is allowed to operate
		modeHP	currentMode ;			// actual mode of operation
		int		minTimeOnPerDay ;		// minutes to operate per day to stay operational (
		int		minTimeOff ;			// minimum minutes to stay off before switching on again
		int		minTimeOn ;				// minimum minutes to stay on before switching off again
		int		handleWater ;			// handle water tank
		int		handleBuffer ;			// handle heating buffer
		float	adjustTempAmbient ;		// minimun ambient temperature for operation
		float	adjustTempFactor ;
		float	tempToResign ;			// temperature below which the heatpump will stop working due to efficiency
		float	tempToRestart ;			// temperature above which the heatpump will start working (again) due to efficiency
		float	waterTempOn ;			// temperature of water when heat pump shall start
		float	waterTempOff ;			// temperature of water when heat pump can stop
		float	bufferTempOn ;			// temperature of buffer when heat pump shall start
		float	bufferTempOff ;			// temperature of buffer when heat pump can stop
		float	actBufferTempOn ;			// temperature of buffer when heat pump shall start
		float	actBufferTempOff ;			// temperature of buffer when heat pump can stop
	}	processDataHP ;

#define	GAP_OFFON	600		// may not be switched on before this amount of seconds has elapsed
#define	GAP_ONOFF	600		// may not be switched off before this amount of seconds has elapsed

#define	TEMP_WW_ON	40
#define	TEMP_WW_OFF	47
#define	TEMP_HB_ON	30		// summer 30, winter 33
#define	TEMP_HB_OFF	35		// summer 35, winter 38

extern	void	setModeStopped( eibHdl *, node *, modeHP *_mode) ;
extern	void	setModeWater( eibHdl *, node *, modeHP *_mode) ;
extern	void	setModeBuffer( eibHdl *, node *, modeHP *_mode) ;

extern	char	*modeToText( int) ;
extern	void	logit( char *_fmt, ...) ;
extern	void	help() ;

char	progName[64] ;
pid_t	ownPID ;
int		runLevel ;

int	heatPump ;
int	valvePelletStove ;

/**
 *
 */
char	*modeText[3]	=	{
		"not working"
	,	"heating water tank"
	,	"heating buffer"
	} ;

/**
 *
 */
int				cfgQueueKey	=	10031 ;
int				cfgSenderAddr	=	1 ;
int				cfgDaemon		=	1 ;		// default:	run as daemon process
int				cfgConsiderTime	=	1 ;

processDataHP	this	=	{
		true					// heatpump is allowed to operate
	,	MODE_STOPPED			// actual mode of operation
	,	10						// minutes to operate per day to stay operational (
	,	10						// minimum minutes to stay off before switching on again
	,	10						// minimum minutes to stay on before switching off again
	,	false					// handle water tank
	,	true					// handle heating buffer
	,	9.0						// minimun ambient temperature for operation of temperature adjustment
	,	3.0						// adjust temperature factor
	,	-10.0					// temperature below which the heatpump will stop working due to efficiency
	,	-7.0					// temperature above which the heatpump will start working (again) due to efficiency
	,	40.0					// temperature of drinking water when heat pump shall start
	,	47.0					// temperature of drinking water when heat pump can stop
	,	32.0					// temperature of buffer when heat pump shall start, base value
	,	35.0					// temperature of buffer when heat pump can stop, base value
	,	0.0						// temperature of buffer when heat pump shall start, actual value
	,	0.0						// temperature of buffer when heat pump can stop, actual value
} ;

/**
 *
 */
void	sigHandler( int _sig) {
	char	buffer[128] ;
	runLevel	-=	1 ;
	sprintf( buffer, "received signal INT; new runLevel = %d\n", runLevel) ;
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
	sprintf( buffer, "hdlheatpump water := %d, buffer := %d, tempOn := %5.1f, tempOff := %5.1f"
					,	this.handleWater
					,	this.handleBuffer
					,	this.actBufferTempOn
					,	this.actBufferTempOff
					) ;
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
	} else if ( strcmp( _block, "[hdlheatpump]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		} else if ( strcmp( _para, "handleBuffer") == 0) {
			this.handleBuffer	=	atoi( _value) ;
		} else if ( strcmp( _para, "handleWater") == 0) {
			this.handleWater	=	atoi( _value) ;
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
	/**
	 *
	 */
		float	tempWW ;
		float	tempHB ;
		float	tempAmbient ;
		modeHP	mode		=	MODE_STOPPED ;
		modeHP	nextMode	=	MODE_STOPPED ;
		int		changeMode ;
		int		startupDelay	=	5 ;	// default 0 seconds startup delay
		int		tempWWu ;				// point to node["TempWWu"],
		int		tempHBu ;				// point to node["TempHBu"],
		int		tempHBmu ;				// point to node["TempHBmu"],
		int		tempHBmo ;				// point to node["TempHBmo"],
		int		tempHBo ;				// point to node["TempHBo"],
										// HeatingBuffer
		int		ndxTempAmbient ;
		time_t	lastOffTime	=	0L ;
		time_t	lastOnTime	=	0L ;
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
	 * define shared memory segment #2: KNX Table with buffer
	 */
			key_t	shmCRFKey	=	SHM_CRF_KEY ;
			int	shmCRFFlg	=	IPC_CREAT | 0600 ;
			int	shmCRFId ;
			int	shmCRFSize	=	65536 * sizeof( int) ;
			int	*crf ;
			time_t		actTime ;
	struct	tm			myTime ;
			int		timerModeHeatpump	=	0 ;
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
		case	'D'	:
			debugLevel	=	atoi( optarg) ;
			break ;
		case    'F'     :
			cfgDaemon	=	0 ;
			break ;
		case	'Q'	:
			cfgQueueKey	=	atoi( optarg) ;
			break ;
		case	'd'	:
			startupDelay	=	atoi( optarg) ;
			break ;
		case	'w'	:
			this.handleWater	=	1 ;
			break ;
		case	'b'	:
			this.handleBuffer	=	1 ;
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
		debugLevel	=	0 ;
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
	heatPump			=	getEntry( data, lastDataIndexC, "HEAT_HEATPUMP_SETONOFF") ;
	valvePelletStove	=	getEntry( data, lastDataIndexC, "HEAT_VALVE_PELLET_SETOPENCLOSE") ;

	tempWWu				=	getEntry( data, lastDataIndexC, "HEAT_TEMP_WASSER_ACTVALUE_ZONE_1") ;
	tempHBu				=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_1") ;
	tempHBmu			=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_2") ;
	tempHBmo			=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_3") ;
	tempHBo				=	getEntry( data, lastDataIndexC, "HEAT_TEMP_PUFFER_ACTVALUE_ZONE_4") ;

	ndxTempAmbient		=	getEntry( data, lastDataIndexC, "HEAT_TEMP_UMGEBUNG_ACTVALUE_ZONE_1") ;

	_debug( 1, progName, "Heatpump at index ..............: %d", heatPump) ;
	_debug( 1, progName, "valvePelletStove at index ......: %d", valvePelletStove) ;

	/**
	 * try to determine the current mode of the heatpump-module
	 */
	if ( data[heatPump].val.i == 1) {
		if ( data[valvePelletStove].val.i == VALVE_PS_WW) {
			mode	=	MODE_BUFFER ;
		} else {
			mode	=	MODE_WATER ;
		}
	} else {
		mode	=	MODE_STOPPED ;
	}

	/**
	 * mode holds the currens operation mode
	 * nextMode contains the next operation mode to be set
	 */
	myEIB	=	eibOpen( cfgSenderAddr, 0, cfgQueueKey, progName, APN_WR | APN_INTRN) ;
	while ( runLevel >= 0) {

		/**
		 *
		 */
		actTime	=	time( NULL) ;
		myTime	=	*localtime( &actTime) ;
		timerModeHeatpump	=	0 ;
		switch ( myTime.tm_wday) {
		case	6	:		// saturday
			if ( myTime.tm_hour >= 5 && myTime.tm_hour <= 12) {
				timerModeHeatpump	=	1 ;
			}
			break ;
		case	0	:		// sunday
			if ( myTime.tm_hour >= 7 && myTime.tm_hour <= 12) {
				timerModeHeatpump	=	1 ;
			}
			break ;
		default	:			// monday - friday
			if ( myTime.tm_hour >= 4 && myTime.tm_hour <= 8) {
				timerModeHeatpump	=	1 ;
			} else if ( myTime.tm_hour >= 19 && myTime.tm_hour <= 21) {
				timerModeHeatpump	=	1 ;
			}
			break ;
		}
		_debug( 1, progName, "timer mode .................... : %d", timerModeHeatpump) ;

		/**
		 * dump all input data for this "MES"
		 */
		if ( debugLevel > 1) {
			dumpData( data, lastDataIndexC, MASK_PELLET, (void *) floats) ;
		}

		/**
		 *
		 */
		tempWW	=	data[tempWWu].val.f ;
		tempHB	=	( data[tempHBu].val.f + data[tempHBmu].val.f + data[tempHBmo].val.f + data[tempHBo].val.f) / 4.0 ;
		tempHB	=	( data[tempHBu].val.f + data[tempHBmo].val.f + data[tempHBo].val.f) / 3.0 ;

		tempAmbient	=	data[ndxTempAmbient].val.f ;

		/**
		 * adjust temperature of buffer in case the ambient tempertaure drops below a certain limit
		 */
		this.actBufferTempOn	=	this.bufferTempOn ;
		this.actBufferTempOff	=	this.bufferTempOff ;
		if ( tempAmbient <= this.adjustTempAmbient) {
			this.actBufferTempOn	+=	( -1.0 * ( ( tempAmbient - this.adjustTempAmbient) / this.adjustTempFactor)) ;
			this.actBufferTempOff	+=	( -1.0 * ( ( tempAmbient - this.adjustTempAmbient) / this.adjustTempFactor)) ;
		}

		if ( cfgDaemon == 0) {
			_debug( 1, progName, "week day (0= sun, ... 6=sat)... : %d", myTime.tm_wday) ;
			_debug( 1, progName, "hour .......................... : %d", myTime.tm_hour) ;
			_debug( 1, progName, "timer mode .................... : %d", timerModeHeatpump) ;
			_debug( 1, progName, "current mode .................. : %d:'%s'", mode, modeText[ mode]) ;
			_debug( 1, progName, "temp. warm water, actual ...... : %5.1f ( %5.1f ... %5.1f)", tempWW, this.waterTempOn, this.waterTempOff) ;
			_debug( 1, progName, "temp. buffer, actual .......... : %5.1f (%5.1f/%5.1f/%5.1f/%5.1f)", tempHB, data[tempHBu].val.f, data[tempHBmu].val.f, data[tempHBmo].val.f, data[tempHBo].val.f) ;
			_debug( 1, progName, "temp. buffer, on - off, base .. : %5.1f - %5.1f", this.bufferTempOn, this.bufferTempOff) ;
			_debug( 1, progName, "temp. buffer, on - off, actual  : %5.1f - %5.1f", this.actBufferTempOn, this.actBufferTempOff) ;
			_debug( 1, progName, "temp. ambient ................. : %5.1f", tempAmbient) ;
		}

		changeMode	=	1 ;
		while ( changeMode) {
			if ( cfgDaemon == 0) {
				_debug( 1, progName, "mode := %d \n", mode) ;
			}
			changeMode	=	0 ;
			switch( mode) {
			case	MODE_STOPPED	:
				if ( this.handleWater && tempWW <= this.waterTempOn) {
					nextMode	=	MODE_WATER ;
				} else if ( tempHB <= this.actBufferTempOn && this.handleBuffer) {
					nextMode	=	MODE_BUFFER ;
				} else {
					nextMode	=	MODE_STOPPED ;
				}
				break ;
			case	MODE_WATER	:
				if ( tempWW >= this.waterTempOff) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				}
				break ;
			case	MODE_BUFFER	:
				if ( tempWW <= this.waterTempOn && this.handleWater) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				} else if ( tempHB >= this.actBufferTempOff && this.handleBuffer) {
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
		switch ( nextMode) {
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
		sleep( 1) ;
		eibWriteHalfFloatIA( myEIB, 6244, this.actBufferTempOn, 0) ;
		sleep( 1) ;
		eibWriteHalfFloatIA( myEIB, 6245, this.actBufferTempOff, 0) ;
		sleep( 28) ;
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

/**
 * 	IF the current mode is different than the mode we need to set
 *		set the marker that it's not a reset function
 */
void	setModeStopped( eibHdl *_myEIB, node *data, modeHP *_mode) {
	int	reset	=	0 ;

	/**
	 *	IF the current mode is already MODE_STOPPED
	 *		=> we are re-setting this mode
	 */
	if ( *_mode == MODE_STOPPED) {
		reset	=	1 ;
	}
	*_mode	=	MODE_STOPPED ;

	/**
	 *	IF heatpump is on
	 *		set it to off
	 *		IF we are re-setting this mode
	 *			previous setting of heatpump if wrong, thus ALERT
	 *		ENDIF
	 *	ENDIF
	 */
	if ( data[heatPump].val.i != 0) {
		eibWriteBitIA( _myEIB, data[heatPump].knxGroupAddr, 0, 1) ;
		if ( reset) {
			logit( "ALERT ... Heatpump Setting (on/off) is WRONG ..., should be 0") ;
		}
	}
	sleep( 1) ;
	if ( data[valvePelletStove].val.i != VALVE_PS_WW) {
		eibWriteBitIA( _myEIB, data[valvePelletStove].knxGroupAddr, VALVE_PS_WW, 0) ;
		if ( reset) {
			logit( "ALERT ... Heatpump Setting (valve) is WRONG ...") ;
		}
	}
}

void	setModeWater( eibHdl *_myEIB, node *data, modeHP *_mode) {
	int	reset	=	0 ;

	/**
	 *	IF the current mode is already MODE_STOPPED
	 *		=> we are re-setting this mode
	 */
	if ( *_mode == MODE_WATER) {
		reset	=	1 ;
	}
	*_mode	=	MODE_WATER ;

	/**
	 *	IF heatpump is off
	 *		set it to on
	 *		IF we are re-setting this mode
	 *			previous setting of heatpump if wrong, thus ALERT
	 *		ENDIF
	 *	ENDIF
	 */
	if ( data[heatPump].val.i != 1) {
		eibWriteBitIA( _myEIB, data[heatPump].knxGroupAddr, 1, 1) ;
		if ( reset) {
			logit( "ALERT ... Heatpump Setting (on/off) is WRONG ..., should be 1") ;
		}
	}
	sleep( 1) ;
	if ( data[valvePelletStove].val.i != VALVE_PS_HB) {
		eibWriteBitIA( _myEIB, data[valvePelletStove].knxGroupAddr, VALVE_PS_HB, 0) ;
		if ( reset) {
			logit( "ALERT ... Heatpump Setting (valve) is WRONG ..., should be VALVE_PS_HB") ;
		}
	}
}

void	setModeBuffer( eibHdl *_myEIB, node *data, modeHP *_mode) {
	int	reset	=	0 ;

	/**
	 *	IF the current mode is already MODE_STOPPED
	 *		=> we are re-setting this mode
	 */
	if ( *_mode != MODE_BUFFER) {
		reset	=	1 ;
	}
	*_mode	=	MODE_BUFFER ;

	/**
	 *	IF heatpump is off
	 *		set it to on
	 *		IF we are re-setting this mode
	 *			previous setting of heatpump if wrong, thus ALERT
	 *		ENDIF
	 *	ENDIF
	 */
	if ( data[heatPump].val.i != 1) {
		eibWriteBitIA( _myEIB, data[heatPump].knxGroupAddr, 1, 1) ;
		if ( reset) {
			logit( "ALERT ... Heatpump Setting (on/off) is WRONG ..., should be 1") ;
		}
	}
	sleep( 1) ;
	if ( data[valvePelletStove].val.i != VALVE_PS_WW) {
		eibWriteBitIA( _myEIB, data[valvePelletStove].knxGroupAddr, VALVE_PS_WW, 0) ;
		if ( reset) {
			logit( "ALERT ... Heatpump Setting (valve) is WRONG ..., should be VALVE_PS_WW") ;
		}
	}
}

char	*modeToText( int _mode) {
	return( modeText[ _mode]) ;
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
