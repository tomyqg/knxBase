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
 * hdlheating.c
 *
 * handler for "our" heating system.
 * this file is not yet
 *
 * Revision history
 *
 * date			rev.	who		what
 * ----------------------------------------------------------------------------
 * 2016-04-04	PA1		khw		inception;
 * 2016-10-07	PA2		khw		enabled EG_BATH;
 * 2017-01-18	PA3		khw		fixed force mode;
 * 2019-05-11	PA4		khw		adapted to new structure of group addresses;
 *
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

#include	"debug.h"
#include	"nodeinfo.h"
#include	"mylib.h"
#include	"eib.h"
#include	"inilib.h"

typedef	enum	modeFBH	{
			MODE_INVALID	=	-1
		,	MODE_OFF	=	0
		,	MODE_ON	=	1
	}	modeFBH ;

#define	CYCLE_TIME	10		// run in 10-seconds cycles

extern	void	setModeOff( eibHdl *, node *) ;
extern	void	setModeOn( eibHdl *, node *) ;
extern	void	setOff( eibHdl *, char *) ;
extern	void	setOn( eibHdl *, char *) ;

extern	void	dumpData( node *, int, int, void *) ;
extern	void	dumpValveTable( node *, int *, float *) ;
extern	void	logit( char *_fmt, ...) ;
extern	void	help() ;

char	progName[64] ;
pid_t	ownPID ;
int		runLevel ;

typedef	struct	{
		char		mode[96] ;		// 0= off, 1= on; 15 minutes grid "0000... " starting at 00:00, 00:15, 00:30 ...
	}	timeProfile ;

typedef	struct	{
		int		id ;
		char	name[32] ;
		char	gaActive[16] ;
		int		initControlActive ;
		char	gaTempTarget[16] ;
		char	gaTempAct[16] ;
		char	gaValve[16] ;
		float		temp[4] ;
		timeProfile	*timer ;
	}	floorValve ;

//								  00:00   02:00   04:00   06:00   08:00   10:00   12:00   14:00   16:00   18:00   20:00   22:00
timeProfile	wdTimeBath		=	{"--------------------1-1-1-1---------------------------------------------------------------------"} ;
timeProfile	wdTimeLiving	=	{"----------------------------------------------------------------1-------1-----------------------"} ;
timeProfile	wdTimeKitchen	=	{"--------------------1---1---1---------------------------------------1---1-----------------------"} ;
timeProfile	wdTimeMBR		=	{"------------------------1-------1-------1-------1-------1-------1-------1-------1---------------"} ;

floorValve	valves[]	=	{
		{	0,"UG_TECH"		,"1/1/14"	,0	,"1/1/10"	,"1/1/11"	,"1/1/12"	, 7.0, 19.0, 21.0, 19.0, NULL			}
	,	{	0,"UG_OFCL"		,"1/2/14"	,0	,"1/2/10"	,"1/2/11"	,"1/2/12"	, 7.0, 19.0, 21.0, 19.0, NULL			}
	,	{	0,"UG_OFCR"		,"1/3/14"	,0	,"1/3/10"	,"1/3/11"	,"1/3/12"	, 7.0, 19.0, 21.0, 19.0, NULL			}
	,	{	0,"UG_STO1"		,"1/4/14"	,0	,"1/4/10"	,"1/4/11"	,"1/4/12"	, 7.0, 19.0, 21.0, 19.0, NULL			}
	,	{	0,"UG_STO2"		,"1/5/14"	,0	,"1/5/10"	,"1/5/11"	,"1/5/12"	, 7.0, 19.0, 21.0, 19.0, NULL			}
	,	{	0,"UG_HALL"		,"1/6/14"	,0	,"1/6/10"	,"1/6/11"	,"1/6/12"	, 7.0, 19.0, 21.0, 19.0, NULL			}
	,	{	0,"EG_HWR"		,"2/1/14"	,0	,"2/1/10"	,"2/1/11"	,"2/1/12"	, 7.0, 19.0, 21.0, 19.0, NULL			}
	,	{	0,"EG_GUEST"	,"2/2/14"	,1	,"2/2/10"	,"2/2/11"	,"2/2/12"	, 7.0, 19.0, 21.0, 19.0, NULL			}
	,	{	0,"EG_LIV"		,"2/3/14"	,1	,"2/3/10"	,"2/3/11"	,"2/3/12"	, 7.0, 19.0, 21.0, 19.0, &wdTimeLiving	}
	,	{	0,"EG_DIN"		,"2/4/14"	,0	,"2/4/10"	,"2/4/11"	,"2/4/12"	, 7.0, 19.0, 21.0, 19.0, &wdTimeLiving	}
	,	{	0,"EG_KITCH"	,"2/5/14"	,1	,"2/5/10"	,"2/5/11"	,"2/5/12"	, 7.0, 19.0, 21.0, 19.0, &wdTimeKitchen	}
	,	{	0,"EG_BATH"		,"2/6/14"	,1	,"2/6/10"	,"2/6/11"	,"2/6/12"	, 7.0, 20.0, 21.0, 19.0, NULL			}
	,	{	0,"EG_HALL"		,"2/7/14"	,0	,"2/7/10"	,"2/7/11"	,"2/7/12"	, 7.0, 19.0, 21.0, 19.0, NULL			}
	,	{	0,"OG_MBATH"	,"3/1/14"	,0	,"3/1/10"	,"3/1/11"	,"3/1/12"	, 7.0, 21.0, 22.0, 19.0, &wdTimeMBR		}
	,	{	1,"OG_MBR"		,"3/2/14"	,0	,"3/2/10"	,"3/2/11"	,"3/2/12"	, 7.0, 18.0, 18.0, 18.0, &wdTimeMBR		}
	,	{	0,"OG_BATH"		,"3/3/14"	,1	,"3/3/10"	,"3/3/11"	,"3/3/12"	, 7.0, 21.0, 22.0, 19.0, &wdTimeBath	}
	,	{	0,"OG_BRR"		,"3/4/14"	,1	,"3/4/10"	,"3/4/11"	,"3/4/12"	, 7.0, 18.0, 19.0, 19.0, NULL			}
	,	{	0,"OG_BRF"		,"3/5/14"	,1	,"3/5/10"	,"3/5/11"	,"3/5/12"	, 7.0, 18.0, 19.0, 19.0, NULL			}
	,	{	0,"OG_HALL"		,"3/6/14"	,1	,"3/6/10"	,"3/6/11"	,"3/6/12"	, 7.0, 18.0, 20.0, 19.0, NULL			}
	,	{  -1,""			,""			,0	,""			,""			,""			, 7.0, 19.0, 21.0, 19.0, NULL			}
	} ;


modeFBH	currentMode ;
int	pumpFBH ;

/**
 *
 */
char	*modeText[2]	=	{
		"off"
	,	"on"
	} ;

/**
 *
 */
int				cfgQueueKey	=	10031 ;
int				cfgSenderAddr	=	1 ;
int				cfgDaemon		=	1 ;		// default:	run as daemon process

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
	} else if ( strcmp( _block, "[hdlheating]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		}
	}
}

/**
 *
 */
int	main( int argc, char *argv[]) {
		eibHdl	*myEIB ;
		pid_t	pid, sid ;
		int	status		=	0 ;
		int		opt ;
		int	i ;
		int	comfort ;
		int	night ;
		int	modeCurrent ;
		int	lastMode	=	MODE_INVALID ;
		int	mode	=	MODE_OFF ;
		time_t	actTime ;
	struct	tm	myTime ;
		int	timeMin ;				// daytime in minutes
		int	timeIndex ;				// index: daytime in minutes / 15
		char	timeBuffer[64] ;
		int	mainPump ;
	timeProfile	*myProfile ;
		int		startupDelay	=	5 ;		// default 0 seconds startup delay
	/**
	 *
	 */
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
			char	logMsg[256] ;
			char	iniFilename[]	=	"knx.ini" ;
			float	actRoomTemp ;
			float	targetRoomTemp ;
			float	cfgTempHyst	=	0.5 ;
			int	actValveSetting ;
			int	newValveSetting ;
			int	groupAdr ;
			int	controlActive ;
			int	firstLoop ;

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
	while (( opt = getopt( argc, argv, "D:FQ:?")) != -1) {
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
	 * try to determine the current mode of the FBH
	 */
	_debug( 1, progName, "trying to determine current status") ;
	pumpFBH		=	getEntry( data, lastDataIndexC, "HEAT_PUMP_FBH_SETONOFF") ;
	if ( data[pumpFBH].val.i == 1) {
		mode	=	MODE_ON ;
	} else {
		mode	=	MODE_OFF ;
	}
	currentMode	=	mode ;
	_debug( 1, progName, "current status ... %5d:%0d:'%s'", data[pumpFBH].knxGroupAddr, currentMode, modeText[currentMode]) ;

	/**
	 * open the knx bus
	 */
	comfort		=	getEntry( data, lastDataIndexC, "SwitchCO") ;
	night		=	getEntry( data, lastDataIndexC, "SwitchNI") ;
	myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_WR | APN_INTRN) ;

	firstLoop	=	1 ;
	while ( runLevel >= 0) {
		modeCurrent	=	data[comfort].val.i << 1 | data[night].val.i ;
		_debug( 1, progName, "Day/Night node %02x", modeCurrent) ;

		/**
		 * dump all input data for this "MES"
		 */
		if ( debugLevel >= 1) {
			dumpValveTable( data, ints, floats) ;
		}

		/**
		 *
		 */
		mode	=	MODE_OFF ;
		for ( i=0 ; valves[i].id != -1 ; i++) {
			if ( firstLoop) {
				groupAdr	=	eibGroupToInt( valves[ i].gaActive) ;
				ints[groupAdr]	=	valves[ i].initControlActive ;
			}
			newValveSetting	=	-1 ;		// don't set
			groupAdr	=	eibGroupToInt( valves[ i].gaValve) ;
			actValveSetting	=	ints[ groupAdr] ;
			groupAdr	=	eibGroupToInt( valves[ i].gaActive) ;
			controlActive	=	ints[ groupAdr] ;
			if ( controlActive == 1) {
				_debug( 1, progName, "-------------------------------------------------------------") ;
				_debug( 1, progName, "%s: temperature control is active", valves[ i].name) ;
				_debug( 1, progName, "actual valve setting: %5d", actValveSetting) ;
				/**
				 * IF we have reasonable value for target and actual temperature
				 */
				groupAdr	=	eibGroupToInt( valves[ i].gaTempAct) ;
				actRoomTemp	=	floats[ groupAdr] ;
				groupAdr	=	eibGroupToInt( valves[ i].gaTempTarget) ;
				targetRoomTemp	=	floats[ groupAdr] ;
				if ( actRoomTemp > 1.0) {
					_debug( 1, progName, "actual temperature seems reasonable") ;
					if (( targetRoomTemp <= 1.0) || ( targetRoomTemp == 0)) {
						_debug( 1, progName, "target room temperature from sensor <= 1.0\n") ;
						targetRoomTemp	=	valves[ i].temp[modeCurrent] ;
						groupAdr	=	eibGroupToInt( valves[ i].gaTempAct) ;
						floats[ groupAdr]	=	targetRoomTemp ;
					}
					_debug( 1, progName, "target temperature %5.2f", targetRoomTemp) ;
					if ( actRoomTemp >= targetRoomTemp && actValveSetting != 0) {
						_debug( 1, progName, "switching off valve %-32s", valves[ i].name) ;
						newValveSetting	=	0 ;
					} else if ( actRoomTemp < ( targetRoomTemp - cfgTempHyst)) {
						_debug( 1, progName, "actual temperature < target temperature for %-32s", valves[ i].name) ;
						if ( actValveSetting != 1) {
							_debug( 1, progName, "switching on valve %-32s", valves[ i].name) ;
							newValveSetting	=	1 ;
						}
						mode	=	MODE_ON ;
					} else {
						_debug( 1, progName, "actual temperature ok for %-32s, %5.2f", valves[ i].name, actRoomTemp) ;
					}
				} else {
					_debug( 1, progName, "no valid actual temperature for %-32s", valves[ i].name) ;
				}
				myProfile	=	valves[i].timer ;
				if ( myProfile != NULL) {
					_debug( 1, progName, "schedule for %-32s", valves[ i].name) ;
					/**
					 * determine current time
					 */
					actTime	=	time( NULL) ;
					myTime	=	*localtime( &actTime) ;
					timeMin	=	myTime.tm_hour * 60 + myTime.tm_min ;
					_debug( 1, progName, "timeMin ........... : %5d", timeMin) ;
					timeIndex	=	( timeMin / 15) ;
					_debug( 1, progName, "timeIndex ......... : %5d", timeIndex) ;
					_debug( 1, progName, "control ........... : %02x", myProfile->mode[ timeIndex]) ;
					switch ( myProfile->mode[ timeIndex]) {
	 				case	'0'	:			// switch if definitively OFF
						_debug( 1, progName, "forcing %s OFF due to schedule", valves[ i].name) ;
						if ( actValveSetting != 0) {
						_debug( 1, progName, "forcing %s OFF due to schedule", valves[ i].name) ;
							newValveSetting	=	0 ;
						} else {
							newValveSetting	=	-1 ;
						}
						break ;
					case	'1'	:			// switch it definitely ON
						_debug( 1, progName, "forcing %s ON due to schedule", valves[ i].name) ;
						if ( actValveSetting != 1) {
						_debug( 1, progName, "forcing %s ON due to schedule", valves[ i].name) ;
							newValveSetting	=	1 ;
						} else {
							newValveSetting	=	-1 ;
						}
						break ;
					case	'-'	:			// leave as determined by temperature
						_debug( 1, progName, "no impact on %-32s due to schedule", valves[ i].name) ;
						break ;
					}
				}
				switch ( newValveSetting) {
				case	0	:		// switch valve off
					_debug( 1, progName, "    ..... closing valve %s", valves[ i].gaValve) ;
					setOff( myEIB, valves[ i].gaValve) ;
					break ;
				case	1	:		// switch valve off
					_debug( 1, progName, "    ..... opening valve %s", valves[ i].gaValve) ;
					setOn( myEIB, valves[ i].gaValve) ;
					break ;
				default	:
					break ;
				}
			} else {
				_debug( 1, progName, "-------------------------------------------------------------") ;
				_debug( 1, progName, "%s: temperature control is not active", valves[ i].name) ;
				_debug( 1, progName, "actual valve setting: %5d", actValveSetting) ;
				if ( actValveSetting != 0) {
					_debug( 1, progName, "    ..... closing valve %s", valves[ i].gaValve) ;
					setOff( myEIB, valves[ i].gaValve) ;
				}
			}
		}
		sleep( 1) ;
		lastMode	=	mode ;
		switch ( mode) {
		case	MODE_OFF	:
			_debug( 1, progName, "switching pump off ... ") ;
			setModeOff( myEIB, data) ;
			break ;
		case	MODE_ON	:
			_debug( 1, progName, "switching pump on ... ") ;
			setModeOn( myEIB, data) ;
			break ;
		}
		/**
		 *
		 */
		firstLoop	=	0 ;
		sleep( CYCLE_TIME) ;
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

void	dumpValveTable( node *data, int *ints, float *floats) {
	int	i ;
	int		gaTempTarget ;
	int		gaTempAct ;
	int		gaActive ;
	int		gaValve ;
	for ( i=0 ; valves[i].id != -1 ; i++) {
			gaActive		=	eibGroupToInt( valves[i].gaActive) ;
			gaTempTarget	=	eibGroupToInt( valves[i].gaTempTarget) ;
			gaTempAct		=	eibGroupToInt( valves[i].gaTempAct) ;
			gaValve			=	eibGroupToInt( valves[i].gaValve) ;
			printf( "%s: %-20s => Control is %-3s, actual := %5.2f, target := %5.2f, valve is %-6s\n",
					progName,
					valves[ i].name,
					(ints[ gaActive] == 1 ? "on" : "off"),
					floats[ gaTempAct],
					floats[ gaTempTarget],
					(ints[ gaValve] == 1 ? "open" : "closed")
				) ;
	}
}

void	setOff( eibHdl *_myEIB, char *groupAddr) {
	eibWriteBit( _myEIB, groupAddr, 0, 1) ;
}

void	setOn( eibHdl *_myEIB, char *groupAddr) {
	eibWriteBit( _myEIB, groupAddr, 1, 1) ;
}

void	setModeOff( eibHdl *_myEIB, node *data) {
	int	reset	=	0 ;
	if ( currentMode != MODE_OFF) {
		reset	=	1 ;
	} else if ( data[pumpFBH].val.i != 0) {
		logit( "ALERT ... FBH Pump Setting (on/off) is WRONG ...") ;
		reset	=	1 ;
	}
	if ( reset) {
		eibWriteBitIA( _myEIB, data[pumpFBH].knxGroupAddr, 0, 0) ;
		currentMode	=	MODE_OFF;
	}
}

void	setModeOn( eibHdl *_myEIB, node *data) {
	int	reset	=	0 ;
	if ( currentMode != MODE_ON) {
		reset	=	1 ;
	} else if ( data[pumpFBH].val.i != 1) {
		logit( "ALERT ... FBH Pump Settings are WRONG ...") ;
		reset	=	1 ;
	}
	if ( reset) {
		eibWriteBitIA( _myEIB, data[pumpFBH].knxGroupAddr, 1, 0) ;
		currentMode	=	MODE_ON ;
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
	printf( "%s: %s [-D=<debugLevel>] [-F] \n\n", progName, progName) ;
	printf( "-F force process to run in foreground, do not daemonize\n") ;
}
