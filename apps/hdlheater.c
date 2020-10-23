/**
 * Copyright (c) 2015-2020 Karl-Heinz Welter
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
 * hdlheater.c
 *
 * handling for our pellet stove and heatpump (air2water)
 *
 * Revision history
 *
 * date		rev.	who	what
 * ----------------------------------------------------------------------------
 * 2015-11-20	PA1	khw	inception;
 * 2016-01-12	PA2	khw	adapted to new architecture of eib.c;
 * 2016-01-25	PA3	khw	added option for waiting time after startup;
 * 2016-03-30	PA4	khw	added timing per day;
 * 2016-05-13	PA5	khw	modified timer to break down to the minute;
 * 2016-11-05	PA6	khw	added single-shot force mode to override
 *						timer mode in case pellet stove is wanted;
 * 2017-01-05	PA7	khw	added function to temporarily take over
 *						buffer heating when outside temp. falls below
 *						a given limit
 * 2020-09-23	PA8	khw	
 */
#include	<stdio.h>
#include	<string.h>
#include	<strings.h>
#include	<unistd.h>
#include	<stdlib.h>
#include	<stdbool.h>
#include	<time.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>

#include	"eib.h"
#include	"debug.h"
#include	"nodeinfo.h"
#include	"mylib.h"
#include	"knxlog.h"
#include	"inilib.h"

typedef	enum	modeHeating	{
			MODE_INVALID	=	-1
		,	MODE_STOPPED	=	0
		,	MODE_WATER	=	1
		,	MODE_BUFFER	=	2
	}	modeHeating ;

#define	GAP_OFFON	600			// may not be switched on before this amount of seconds has elapsed
#define	GAP_ONOFF	600			// may not be switched off before this amount of seconds has elapsed

#define	TEMP_PS_WW_ON	53		// pellet stove starts making hot water below this hotwater temp
#define	TEMP_PS_WW_OFF	58		// pellet stove stops making hot water above this hotwater temp
#define	TEMP_PS_HB_ON	33		// summer: 30, winter: 33
#define	TEMP_PS_HB_OFF	40		// summer: 35, winter: 38

#define	TEMP_HP_WW_ON	42		// heatpump starts making hot water below this hotwater temp
#define	TEMP_HP_WW_OFF	56		// heatpump stops making hot water above this hotwater temp
#define	TEMP_HP_HB_ON	33		// summer: 30, winter: 33
#define	TEMP_HP_HB_OFF	36		// summer: 35, winter: 38

#define	TEMP_HP_OFF		-3.0		// temperature when heatpump is taken out of service by pellet stove
#define	TEMP_HP_ON		-1.0		// temperature when heatpump takes over again from pellet stove

extern	void	setModePelletStop( eibHdl *, node *) ;
extern	void	setModeHeatpumpStop( eibHdl *, node *) ;
extern	void	setModePelletWater( eibHdl *, node *) ;
extern	void	setModePelletBuffer( eibHdl *, node *) ;
extern	void	myEibWriteBit( eibHdl *, unsigned int, unsigned char, unsigned char) ;

extern	void	help() ;

char	progName[64] ;
knxLogHdl	*myKnxLogger ;
modeHeating	currentPelletMode ;
modeHeating	currentHeatpumpMode ;

int	ndxPelletStove ;
int	ndxHeatPump ;
int	ndxValvePelletStove ;

char	*modeText[3]	=	{
		"not working"
	,	"heating water tank"
	,	"heating buffer"
	} ;

/**
 *
 */
int	cfgQueueKey	=	10031 ;
int	cfgSenderAddr	=	1 ;
int	cfgConsiderTime	=	1 ;
int	cfgDaemon		=	true ;
int	cfgHandleBuffer	=	0 ;		// default: do NOT care about the buffer
int	cfgHandleWater	=	1 ;		// default: do care about the warm-water

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
		int	status		=	0 ;
		int	opt ;
		char	timeBuffer[64] ;
		int	timeMin ;				// daytime in minutes

		int	pumpRunTime ;				// time pump is running in seconds
		int	pumpDownTime ;				// time pump is idle in seconds
		int	pumpOnTime ;				// time when solar pump was switched on last
		int	pumpOffTime ;				// time when solar pump was switched off
		int	hdlBuffer ;				// time when solar pump was switched off
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
		float	tempWWu ;
		float	tempWWm ;
		float	tempWWo ;
		float	tempHB ;
		float	tempHBu ;
		float	tempHBmu ;
		float	tempHBmo ;
		float	tempHBo ;
		float	tempAmbient ;

		int	ndxTempWWu ;				// point to node["TempWWu"],
		int	ndxTempWWm ;				// point to node["TempWWu"],
		int	ndxTempWWo ;				// point to node["TempWWu"],
		int	ndxTempHBu ;
		int	ndxTempHBmu ;
		int	ndxTempHBmo ;
		int	ndxTempHBo ;
		int	ndxTempAmbient ;
		int	ndxUsePelletForBuffer ;

		int	ndxPelletHandlerActive ;
		int	ndxPelletTimerMode ;
		int	ndxForceOnce ;

		int	ndxHeatpumpHandlerActive ;
		int	ndxHeatpumpTimerMode ;
								// WarmWater
		int	lastMode	=	MODE_INVALID ;
		int	mode	=	MODE_STOPPED ;
		int	changeMode ;
		int	startupDelay	=	5 ;		// default 0 seconds startup delay
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
	 * define shared memory segment #3: Cross-Reference table
 	 */
			key_t		shmCRFKey	=	SHM_CRF_KEY ;
			int		shmCRFFlg	=	IPC_CREAT | 0600 ;
			int		shmCRFId ;
			int		shmCRFSize	=	65536 * sizeof( int) ;
			int		*crf ;
			time_t		actTime ;
	struct	tm			myTime ;
			int		timerModePellet	=	0 ;
			int		timerModeHeatpump	=	0 ;
			char		iniFilename[]	=	"knx.ini" ;
	/**
	 *
	 */
	strcpy( progName, *argv) ;
	_debug( 0, progName, "starting up ...") ;

	/**
	 *
	 */
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 * get command line options
	 */
	while (( opt = getopt( argc, argv, "D:Q:d:wb?")) != -1) {
		switch ( opt) {

		/**
		 * general options
		 */
		case	'D'	:
			debugLevel	=	atoi( optarg) ;
			break ;
		case	'F'	:
			cfgDaemon	=	atoi( optarg) ;
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
	myKnxLogger	=	knxLogOpen( 0) ;
	knxLog( myKnxLogger, progName, "starting up ...") ;
	sleep( startupDelay) ;
	/**
	 * get and attach the shared memory for COMtable
	 */
	_debug( 1, progName, "trying to obtain shared memory COMtable ...") ;
	if (( shmCOMId = shmget( shmCOMKey, shmCOMSize, 0600)) < 0) {
		_debug( 0, progName, "shmget failed for COMtable");
		_debug( 0, progName, "Exiting with -1");
		exit( -1) ;
	}
	_debug( 1, progName, "trying to attach shared memory for COMtable") ;
	if (( sizeTable = (int *) shmat( shmCOMId, NULL, 0)) == (int *) -1) {
		_debug( 0, progName, "shmat failed for COMtable");
		_debug( 0, progName, "Exiting with -1");
		exit( -1) ;
	}
	shmCOMSize	=	sizeTable[ SIZE_COM_TABLE] ;
	shmOPCSize	=	sizeTable[ SIZE_OPC_TABLE] ;
	shmKNXSize	=	sizeTable[ SIZE_KNX_TABLE] ;
	shmCRFSize	=	sizeTable[ SIZE_CRF_TABLE] ;

	/**
	 *
	 */
#include	"_shmblock.c"

	/**
	 * get some indices from the
	 */
	_debug( 1, progName, "test .......................... : ") ;

	ndxPelletStove		=	getEntry( data, lastDataIndexC, "PelletStove") ;
	ndxHeatPump		=	getEntry( data, lastDataIndexC, "HeatPump") ;
	ndxValvePelletStove	=	getEntry( data, lastDataIndexC, "ValvePelletStove") ;

	ndxTempWWu		=	getEntry( data, lastDataIndexC, "TempWWu") ;
	ndxTempWWm		=	getEntry( data, lastDataIndexC, "TempWWm") ;
	ndxTempWWo		=	getEntry( data, lastDataIndexC, "TempWWo") ;
	ndxTempHBu		=	getEntry( data, lastDataIndexC, "TempPSu") ;
	ndxTempHBmu		=	getEntry( data, lastDataIndexC, "TempPSu") ;
	ndxTempHBmo		=	getEntry( data, lastDataIndexC, "TempPSo") ;
	ndxTempHBo		=	getEntry( data, lastDataIndexC, "TempPSo") ;
	ndxTempAmbient		=	getEntry( data, lastDataIndexC, "TempAmb") ;

	ndxPelletHandlerActive	=	getEntry( data, lastDataIndexC, "PelletHandlerOnOff") ;
	ndxPelletTimerMode	=	getEntry( data, lastDataIndexC, "PelletTimerOnOff") ;
	ndxForceOnce		=	getEntry( data, lastDataIndexC, "PelletForceOnce") ;

	ndxHeatpumpHandlerActive	=	getEntry( data, lastDataIndexC, "HeatpumpHandlerOnOff") ;
	ndxHeatpumpTimerMode	=	getEntry( data, lastDataIndexC, "HeatpumpTimerOnOff") ;

	ndxUsePelletForBuffer	=	getEntry( data, lastDataIndexC, "UsePellet4Buffer") ;

	_debug( 1, progName, "ndxPelletStove ................ : %d (knx: %05d)", ndxPelletStove, data[ndxPelletStove].knxGroupAddr) ;
	_debug( 1, progName, "ndxValvePelletStove ........... : %d (knx: %05d)", ndxValvePelletStove, data[ndxValvePelletStove].knxGroupAddr) ;

	data[ndxPelletHandlerActive].val.i	=	1 ;
	data[ndxPelletTimerMode].val.i		=	1 ;
	data[ndxForceOnce].val.i		=	0 ;

	data[ndxHeatpumpHandlerActive].val.i	=	1 ;
	data[ndxHeatpumpTimerMode].val.i	=	1 ;

	/**
	 * try to determine the current mode of the pellet-module
	 */
	_debug( 1, progName, "trying to determine current pellet status") ;
	if ( data[ndxPelletStove].val.i == 1) {
		if ( data[ndxValvePelletStove].val.i == VALVE_PS_WW) {
			currentPelletMode	=	MODE_WATER ;
		} else {
			currentPelletMode	=	MODE_BUFFER ;
		}
	} else {
		currentPelletMode	=	MODE_STOPPED ;
	}
	_debug( 1, progName, "current pellet status ...... %0d:'%s'", currentPelletMode, modeText[currentPelletMode]) ;

	/**
	 * try to determine the current mode of the pellet-module
	 */
	_debug( 1, progName, "trying to determine current heatpump status") ;
	if ( data[ndxHeatPump].val.i == 1) {
		if ( data[ndxValvePelletStove].val.i == VALVE_PS_WW) {
			currentHeatpumpMode	=	MODE_BUFFER ;
		} else {
			currentHeatpumpMode	=	MODE_WATER ;
		}
	} else {
		currentHeatpumpMode	=	MODE_STOPPED ;
	}
	_debug( 1, progName, "current heatpump status .... %0d:'%s'", currentHeatpumpMode, modeText[currentHeatpumpMode]) ;

	/**
	 *
	 */
	myEIB	=	eibOpen( cfgSenderAddr, 0, cfgQueueKey, progName, APN_WR | APN_INTRN) ;
	hdlBuffer	=	cfgHandleBuffer ;
	while ( debugLevel >= 0) {

		/**
		 *----------------------------------------------------------------------
		 *
		 *	prepare handling, get time, temperatures
		 *
		 *----------------------------------------------------------------------
		 */

		actTime	=	time( NULL) ;
		myTime	=	*localtime( &actTime) ;

		tempWWu		=	data[ndxTempWWu].val.f ;
		tempWWm		=	data[ndxTempWWm].val.f ;
		tempWWo		=	data[ndxTempWWo].val.f ;
		tempWW		=	( tempWWu + tempWWu + tempWWu) / 3.0 ;

		tempHBu		=	data[ndxTempHBu].val.f ;
		tempHBmu	=	data[ndxTempHBmu].val.f ;
		tempHBmo	=	data[ndxTempHBmo].val.f ;
		tempHBo		=	data[ndxTempHBo].val.f ;
		tempHB		=	( tempHBu + tempHBmu + tempHBmu + tempHBu) / 4.0 ;
		tempAmbient	=	data[ndxTempAmbient].val.f ;

		tempHBOn	=	TEMP_HB_ON ;
		tempHBOff	=	TEMP_HB_OFF ;

		_debug( 1, progName, "config: handle water .......... : %2d", cfgHandleWater) ;
		_debug( 1, progName, "config: handle buffer ......... : %2d", cfgHandleBuffer) ;
		_debug( 1, progName, "week day (0= sun, ... 6=sat)... : %d", myTime.tm_wday) ;
		_debug( 1, progName, "hour .......................... : %02d:%02d", myTime.tm_hour, myTime.tm_min) ;
		_debug( 1, progName, "temp. water, actual ........... : %5.1f ( %5.1f ... %5.1f)", tempWW, tempWWOn, tempWWOff) ;
		_debug( 1, progName, "temp. buffer, actual .......... : %5.1f ( %5.1f ... %5.1f)", tempHB, tempHBOn, tempHBOff) ;

		_debug( 1, progName, "pellet handler active ......... : %2d", data[ndxPelletHandlerActive].val.i) ;
		_debug( 1, progName, "pellet timer mode ............. : %2d", data[ndxPelletTimerMode].val.i) ;
		_debug( 1, progName, "force mode .................... : %2d", data[ndxForceOnce].val.i) ;

		_debug( 1, progName, "heatpump handler active ....... : %2d", data[ndxHeatpumpHandlerActive].val.i) ;
		_debug( 1, progName, "heatpump timer mode ........... : %2d", data[ndxHeatpumpTimerMode].val.i) ;

		/**
		 *----------------------------------------------------------------------
		 *
		 *	PELLET STOVE HANDLER
		 *
		 *----------------------------------------------------------------------
		 */
		if ( data[ndxPelletTimerMode].val.i != 0) {
			timeMin	=	myTime.tm_hour * 60 + myTime.tm_min ;
			timerModePellet	=	0 ;
			switch ( myTime.tm_wday) {
			case	6	:		// saturday
				if ( timeMin >= ( 5 * 60) && timeMin <= ( 10 * 60)) {
					timerModePellet	=	1 ;
				} else if ( timeMin >= ( 19 * 60) && timeMin <= ( 21 * 60)) {
					timerModePellet	=	1 ;
				}
				break ;
			case	0	:		// sunday
				if ( timeMin >= ( 7 * 60) && timeMin <= ( 12 * 60)) {
					timerModePellet	=	1 ;
				} else if ( timeMin >= ( 19 * 60) && timeMin <= ( 21 * 60)) {
					timerModePellet	=	1 ;
				}
				break ;
			default	:			// monday - friday
				if ( timeMin >= ( 5 * 60) && timeMin <= ( 8 * 60)) {
					timerModePellet	=	1 ;
				} else if ( timeMin >= ( 21 * 60) && timeMin <= ( 23 * 60)) {
					timerModePellet	=	1 ;
				}
				break ;
			}
			_debug( 1, progName, "pellet timer mode ............. : %d", timerModePellet) ;
		} else {
			_debug( 1, progName, "pellet timer mode ............. : not considered") ;
			timerModePellet	=	1 ;
		}
		_debug( 1, progName, "pellet current mode ........... : %d:'%s'", currentPelletMode, modeText[currentPelletMode]) ;

		/**
		 *
		 */
		tempAmbient	=	data[ndxTempAmbient].val.f ;
		if ( tempAmbient < TEMP_HP_OFF) {
			hdlBuffer	=	1 ;
			if ( data[ndxUsePelletForBuffer].val.i != 1) {
				_debug( 1, progName, "setting EIB value to ForcePellet4Buffer") ;
				myEibWriteBit( myEIB, 9734, 1, 0) ;
			}
		} else if ( tempAmbient > TEMP_HP_ON) {
			hdlBuffer	=	cfgHandleBuffer ;
			if ( data[ndxUsePelletForBuffer].val.i != 0) {
				_debug( 1, progName, "setting EIB value to normal mode") ;
				myEibWriteBit( myEIB, 9734, 0, 0) ;
			}
		}
		_debug( 1, progName, "outside temperature ........... : %5.1f ( %5.1f ... %5.1f)", tempAmbient, TEMP_HP_ON, TEMP_HP_OFF) ;
		_debug( 1, progName, "buffer mode actual ............ : %2d", hdlBuffer) ;
		_debug( 1, progName, "temp. ambient ................. : %5.1f", tempAmbient) ;

		/**
		 *
		 */
		changeMode	=	1 ;
		lastMode	=	mode ;

		while ( changeMode) {
			changeMode	=	0 ;
			switch( mode) {
			case	MODE_STOPPED	:
				if ( tempWWu <= tempWWOn && cfgHandleWater && ( timerModePellet == 1 || data[ndxForceOnce].val.i == 1)) {
					_debug( 1, progName, "starting Water ") ;
					data[ndxForceOnce].val.i	=	0 ;
					mode	=	MODE_WATER ;
				} else if ( tempHB <= tempHBOn && hdlBuffer) {
					_debug( 1, progName, "starting Buffer ") ;
					mode	=	MODE_BUFFER ;
				} else {
					mode	=	MODE_STOPPED ;
				}
				break ;
			case	MODE_WATER	:
				if (( tempWWu >= tempWWOff)) {	//  || timerModePellet == 0)) {
					changeMode	=	1 ;
					mode	=	MODE_STOPPED ;
				}
				break ;
			case	MODE_BUFFER	:
				if (( tempWWu <= tempWWOn && cfgHandleWater) && pumpRunTime > 300) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				} else if (( tempHB >= tempHBOff && hdlBuffer)) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				} else if ( ! hdlBuffer) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				}
				break ;
			}
		}
		lastMode	=	mode ;
		switch ( mode) {
		case	MODE_STOPPED	:
			setModePelletStop( myEIB, data) ;
			break ;
		case	MODE_WATER	:
			setModePelletWater( myEIB, data) ;
			break ;
		case	MODE_BUFFER	:
			setModePelletBuffer( myEIB, data) ;
			break ;
		}

		/**
		 *----------------------------------------------------------------------
		 *
		 *	HEATPUMP HANDLER
		 *
		 *----------------------------------------------------------------------
		 */
		if ( data[ndxHeatpumpTimerMode].val.i != 0) {
			timeMin	=	myTime.tm_hour * 60 + myTime.tm_min ;
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
			_debug( 1, progName, "heatpump timer mode ........... : %d", timerModeHeatpump) ;
		} else {
			_debug( 1, progName, "heatpump timer mode ........... : not considered") ;
			timerModeHeatpump	=	1 ;
		}
		_debug( 1, progName, "heatpump current mode ......... : %d:'%s'", currentHeatpumpMode, modeText[currentHeatpumpMode]) ;

		/**
		 *
		 */
		changeMode	=	1 ;
		lastMode	=	mode ;
		if ( tempAmbient < 0) {
			tempHBOn	+=	( -1.0 * ( tempAmbient / 3)) ;
			tempHBOff	+=	( -1.0 * ( tempAmbient / 3)) ;
		}
		while ( changeMode) {
			changeMode	=	0 ;
			switch( mode) {
			case	MODE_STOPPED	:
				if ( tempWWu <= tempWWOn && cfgHandleWater) {
					mode	=	MODE_WATER ;
				} else if ( tempHB <= tempHBOn && cfgHandleBuffer) {
					mode	=	MODE_BUFFER ;
				} else {
					mode	=	MODE_STOPPED ;
				}
				break ;
			case	MODE_WATER	:
				if ( tempWWu >= tempWWOff) {
					changeMode	=	1 ;
					mode	=	MODE_STOPPED ;
				}
				break ;
			case	MODE_BUFFER	:
				if ( tempWWu <= tempWWOn && cfgHandleWater) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				} else if ( tempHB >= tempHBOff && cfgHandleBuffer) {
					mode	=	MODE_STOPPED ;
					changeMode	=	1 ;
				}
				break ;
			}
		}
		lastMode	=	mode ;
		switch ( mode) {
		case	MODE_STOPPED	:
			setModeHeatpumpStop( myEIB, data) ;
			break ;
		case	MODE_WATER	:
//			setModeHeatpumpWater( myEIB, data) ;
			break ;
		case	MODE_BUFFER	:
//			setModeBuffer( myEIB, data) ;
			break ;
		}

		/**
		 *
		 */
		_debug( 1, progName, "going to sleep ... \n") ;
		sleep( 15) ;
	}

	/**
	 *
	 */
	knxLog( myKnxLogger, progName, "terminating ...") ;
	knxLogClose( myKnxLogger) ;
	exit( status) ;
}

void	setModePelletStop( eibHdl *_myEIB, node *data) {
	int	reset	=	1 ;
	if ( currentPelletMode != MODE_STOPPED) {
		knxLog( myKnxLogger, progName, "Setting mode OFF") ;
		currentPelletMode	=	MODE_STOPPED ;
		reset	=	0 ;
	}
	if ( data[ndxPelletStove].val.i != 0) {
		if ( reset) {
			knxLog( myKnxLogger, progName, "ALERT ... Pellet Stove Setting (on/off) is WRONG ...") ;
		}
		myEibWriteBit( _myEIB, data[ndxPelletStove].knxGroupAddr, 0, 0) ;
	}
	sleep( 1) ;
	if ( data[ndxValvePelletStove].val.i != VALVE_PS_WW) {
		if ( reset) {
			knxLog( myKnxLogger, progName, "ALERT ... Pellet Stove Setting (valve) is WRONG ...") ;
		}
		myEibWriteBit( _myEIB, data[ndxValvePelletStove].knxGroupAddr, VALVE_PS_WW, 0) ;
	}
}

void	setModeHeatpumpStop( eibHdl *_myEIB, node *data) {
	int	reset	=	1 ;
	if ( currentPelletMode != MODE_STOPPED) {
		knxLog( myKnxLogger, progName, "Setting mode OFF") ;
		currentPelletMode	=	MODE_STOPPED ;
		reset	=	0 ;
	}
	if ( data[ndxHeatPump].val.i != 0) {
		if ( reset) {
			knxLog( myKnxLogger, progName, "ALERT ... Heatpump Setting (on/off) is WRONG ...") ;
		}
		myEibWriteBit( _myEIB, data[ndxHeatPump].knxGroupAddr, 0, 1) ;
	}
	sleep( 1) ;
	if ( data[ndxValvePelletStove].val.i != VALVE_PS_WW) {
		if ( reset) {
			knxLog( myKnxLogger, progName, "ALERT ... Heatpump Setting (valve) is WRONG ...") ;
		}
		myEibWriteBit( _myEIB, data[ndxValvePelletStove].knxGroupAddr, VALVE_PS_WW, 0) ;
	}
}

void	setModePelletWater( eibHdl *_myEIB, node *data) {
	int	reset	=	1 ;
	if ( currentPelletMode != MODE_WATER) {
		knxLog( myKnxLogger, progName, "Setting mode OFF") ;
		currentPelletMode	=	MODE_WATER ;
		reset	=	0 ;
	}
	if ( data[ndxPelletStove].val.i != 1) {
		if ( reset) {
			knxLog( myKnxLogger, progName, "ALERT ... Pellet Stove Setting (on/off) is WRONG ...") ;
		}
		myEibWriteBit( _myEIB, data[ndxPelletStove].knxGroupAddr, 1, 0) ;
	}
	sleep( 1) ;
	if ( data[ndxValvePelletStove].val.i != VALVE_PS_WW) {
		if ( reset) {
			knxLog( myKnxLogger, progName, "ALERT ... Pellet Stove Setting (valve) is WRONG ...") ;
		}
		myEibWriteBit( _myEIB, data[ndxValvePelletStove].knxGroupAddr, VALVE_PS_WW, 0) ;
	}
}

void	setModePelletBuffer( eibHdl *_myEIB, node *data) {
	int	reset	=	1 ;
	if ( currentPelletMode != MODE_BUFFER) {
		knxLog( myKnxLogger, progName, "Setting mode ON") ;
		currentPelletMode	=	MODE_BUFFER ;
		reset	=	0 ;
	}
	if ( data[ndxPelletStove].val.i != 1) {
		if ( reset) {
			knxLog( myKnxLogger, progName, "ALERT ... Pellet Stove Setting (on/off) is WRONG ...") ;
			_debug( 1, progName, "ALERT ... Pellet Stove Setting (on/off) is WRONG ...") ;
		}
		myEibWriteBit( _myEIB, data[ndxPelletStove].knxGroupAddr, 1, 0) ;
	}
	sleep( 1) ;
	if ( data[ndxValvePelletStove].val.i != VALVE_PS_HB) {
		if ( reset) {
			knxLog( myKnxLogger, progName, "ALERT ... Pellet Stove Setting (valve) is WRONG ...") ;
			_debug( 1, progName, "ALERT ... Pellet Stove Setting (valve) is WRONG ...") ;
		}
		myEibWriteBit( _myEIB, data[ndxValvePelletStove].knxGroupAddr, VALVE_PS_HB, 0) ;
	}
}

void	myEibWriteBit( eibHdl *this, unsigned int receiver, unsigned char value, unsigned char repeat) {
//	eibWriteBit( this, receiver, valoue, repeat) ;
}

void	help() {
	printf( "%s: %s -d=<debugLevel> -w -b \n\n", progName, progName) ;
	printf( "-w handle water tank\n") ;
	printf( "-b handle heating buffer\n") ;
}
