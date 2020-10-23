/**
 * Copyright (c) 2015-2020 wimtecc, Karl-Heinz Welter
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
 * 2019-05-11	PA7		khw		adapted to new structure of group addresses;
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
#include	<mosquitto.h>

#include	"debug.h"
#include	"nodeinfo.h"
#include	"mylib.h"
#include	"eib.h"
#include	"inilib.h"

#define	CYCLE_TIME	60		// run in 60-seconds cycles

typedef	enum	modeShade	{
			MODE_INVALID	=	'-'
		,	MODE_DOWN	=	'D'
		,	MODE_UP	=	'U'
	}	modeShade ;

typedef	enum	modeShadeControl	{
			CTRL_TIMER	=	't'
		,	CTRL_RISESET	=	'r'
		,	CTRL_BRIGHT	=	'b'
	}	modeShadeControl ;

typedef	struct	{
		char	mode[96] ;		// 0= off, 1= on; 15 minutes grid "0000... " starting at 00:00, 00:15, 00:30 ...
	}	timeProfile ;

typedef	struct	{
		int		id ;
		int		active ;
		char	name[32] ;
		char		gaBase[16] ;
		timeProfile	*timer ;
	}	shadePosition ;

/**
 * structure with
 */
typedef	struct	{
		int			id ;
		int			active ;
		char		name[32] ;
		int			gaBase ;		// actual position
		time_t		timeDown ;		// time for moving down
		time_t		timeUp ;
		int			timeDelayDown ;		// time delay in minutes for moving down
		int			timeDelayUp ;
		int			brightnessDown ;	// brightness for moving down
		int			brightnessUp ;		// brightness for moving up
	}	shadeSetup ;

extern	void	setDown( eibHdl *, char *) ;
extern	void	setUp( eibHdl *, char *) ;

extern	void		dumpData( node *, int, int, void *) ;
extern	void		dumpShadeTable( node *, int *, float *) ;
extern	shadeSetup	*getShadeSetup( shadePosition *) ;

extern	void	logit( char *_fmt, ...) ;
extern	void	help() ;

char	progName[64] ;
pid_t	ownPID ;
int		runLevel ;

//					  		  00:00   02:00   04:00   06:00   08:00   10:00   12:00   14:00   16:00   18:00   20:00   22:00
// Up: 07:00 - 22:00
timeProfile	wdTime1		=	{"----------------------------U-----------------------------------------------------------D-------"} ;

// Up: 08:00 - 22:00
timeProfile	wdTime2		=	{"--------------------------------U-------------------------------------------------------D-------"} ;

// Up: 08:00 - 19:00
timeProfile	wdTime3		=	{"--------------------------------U------------------------------------------------------D--------"} ;
timeProfile	wdTime3x	=	{"----------------------------------------U-----------------------------------------------D-------"} ;

// Up: 08:00 - 23:00
timeProfile	wdTime4		=	{"--------------------------------U-----------------------------------------------------------D---"} ;

// Up: 07:00 - 23:30
timeProfile	wdTime5		=	{"----------------------------U-----------------------------------------------------------------D-"} ;

shadePosition	shades[]	=	{
/**
 *	basement shades
 */
		{	0, 	0,"UG_OFCL"			, "1/2/100"	,&wdTime1		}
	,	{	0, 	0,"UG_OFCR"			, "1/3/100"	,&wdTime1		}
	,	{	0, 	0,"UG_STO1"			, "1/4/100"	,&wdTime1		}
/**
 *	ground-floor shades
 */
	,	{	0, 	1,"ShadeEGHWRVAufAb", "2/1/100"	,&wdTime1		}
	,	{	0, 	0,"ShadeEGHWRSAufAb", "2/1/110"	,&wdTime1		}
	,	{	0, 	1,"ShadeEGGSAufAb"	, "2/2/100"	,&wdTime1		}
	,	{	0, 	1,"ShadeEGGHAufAb"	, "2/2/110"	,&wdTime1		}
	,	{	0, 	1,"ShadeEGWZAufAb"	, "2/3/100"	,&wdTime4		}
	,	{	1, 	1,"ShadeEGEZHAufAb"	, "2/4/100"	,&wdTime4		}
	,	{	1, 	1,"ShadeEGEZSAufAb"	, "2/4/110"	,&wdTime4		}
	,	{	0, 	1,"ShadeEGKSAufAb"	, "2/5/100"	,&wdTime5		}
	,	{	0, 	1,"ShadeEGKVVAufAb"	, "2/5/110"	,&wdTime2		}
	,	{	0, 	1,"ShadeEGBAufAb"	, "2/6/100"	,&wdTime2		}
/**
 *	1st-floor shades
 */
	,	{	0, 	1,"OG_MBATH1"		, "3/1/100"	,&wdTime3		}
	,	{	0, 	1,"OG_MBATH2"		, "3/1/110"	,&wdTime3		}
	,	{	0, 	1,"OG_MBR1"			, "3/2/100"	,&wdTime3		}
	,	{	0, 	0,"OG_MBR2"			, "3/2/110"	,&wdTime3		}
	,	{	0, 	0,"OG_KBAD"			, "3/3/100"	,&wdTime1		}
	,	{	0, 	0,"OG_BRR1"			, "3/4/100"	,&wdTime3		}
	,	{	0, 	0,"OG_BRR2"			, "3/4/110"	,&wdTime3x		}
	,	{	0, 	1,"OG_BRF1"			, "3/5/100"	,&wdTime3x		}
	,	{	0, 	1,"OG_BRF2"			, "3/5/110"	,&wdTime3x		}
	,	{	-1,	0,""				, "0"		,&wdTime1		}
	} ;

/**
 *
 */
char	*modeText[2]	=	{
		"down"
	,	"up"
	} ;

/**
 *
 */
int	cfgQueueKey	=	10031 ;
int	cfgSenderAddr	=	1 ;
int	cfgDaemon		=	1 ;		// default:	run as daemon process
struct mosquitto	*mosq ;

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
	} else if ( strcmp( _block, "[hdlshades]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		}
	}
}

/**
 *
 */
void	connectCB( struct mosquitto *mosq, void *obj, int result)
{
	printf("connect callback, rc=%d\n", result);
}

/**
 *
 */
void	messageCB( struct mosquitto *mosq, void *obj, const struct mosquitto_message *message)
{
	bool	match = 0;
	printf("got message '%.*s' for topic '%s'\n", message->payloadlen, (char*) message->payload, message->topic);

	mosquitto_topic_matches_sub("/devices/wb-adc/controls/+", message->topic, &match);
	if ( match) {
		printf("got message for ADC topic\n");
	}
}

/**
 *
 */

/**
 *
 */

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
		int	lastMode	=	MODE_INVALID ;
		time_t	actTime ;
	struct	tm	myTime ;
		int	timeMin ;				// daytime in minutes
		int	timeIndex ;				// index: daytime in minutes / 15
		char	timeBuffer[64] ;
		int	mainPump ;
		char	mqttClientId[24] ;
	timeProfile	*myProfile ;
	/**
	 *
	 */
		time_t	lastDownTime	=	0L ;
		time_t	lastUpTime	=	0L ;
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
		int	actShadePosition ;
		int	actShadePosRel ;
		int	newShadePosition ;
		int	modeAuto ;
		int	modeAutoOn ;
		shadeSetup	*shadeData ;
		int			entry ;
		int		startupDelay	=	5 ;		// default 0 seconds startup delay

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
	 * setup the shared memory for COMtable
	 */

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
	 * open the knx bus
	 */
	myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_WR | APN_INTRN) ;

	/**
	 * prepare in memory table with shade control information
	 */
	shadeData	=	getShadeSetup( shades) ;
	for ( i=0 ; shades[i].id != -1 ; i++) {
		modeAutoOn	=	shades[i].active ;
		eibWriteBitIA( myEIB, eibGroupToInt( shades[ i].gaBase) + 5, modeAutoOn, 1) ;
	}

	mosquitto_lib_init();

	memset( mqttClientId, 0, 24);
	snprintf( mqttClientId, 23, "hdlshades_%d", getpid());
	mosq	=	mosquitto_new( mqttClientId, true, 0);

	/**
	 * and start working ...
	 */
	while ( runLevel >= 0) {
		/**
		 * dump all input data for this "MES"
		 */
		if ( debugLevel >= 1) {
			dumpShadeTable( data, ints, floats) ;
		}
		/**
		 * determine current time
		 */
		actTime	=	time( NULL) ;
		myTime	=	*localtime( &actTime) ;
		timeMin	=	myTime.tm_hour * 60 + myTime.tm_min ;
		_debug( 1, progName, "timeMin ........... : %5d", timeMin) ;
		timeIndex	=	( timeMin / 15) ;
		_debug( 1, progName, "timeIndex ......... : %5d", timeIndex) ;
		/**
		 *
		 */
		for ( i=0 ; shades[i].id != -1 ; i++) {
			entry		=	getEntry( data, lastDataIndexC, shades[ i].name) ;
			newShadePosition	=	-1 ;		// don't set
//			actShadePosition	=	ints[ eibGroupToInt( shades[ i].gaBase) + 2] ;
			actShadePosition	=	ints[ eibGroupToInt( shades[ i].gaBase)] ;
			modeAuto	=	ints[ eibGroupToInt( shades[ i].gaBase) + 4] ;
			modeAutoOn	=	ints[ eibGroupToInt( shades[ i].gaBase) + 5] ;
			if ( entry >= 0)
				actShadePosRel	=	data[ entry+3].val.i * 100 / 255 ;
			if ( modeAutoOn == 1) {
				myProfile	=	shades[i].timer ;
				_debug( 1, progName, "control ........... : %02x", myProfile->mode[ timeIndex]) ;
				if ( myProfile != NULL) {
					_debug( 1, progName, "schedule for %-32s, actPos := %d%", shades[ i].name, actShadePosRel) ;
					switch ( myProfile->mode[ timeIndex]) {
	 				case	'D'	:			// move it definitely Down
						if ( actShadePosition != 1) { 		//  && actShadePosRel <= 5) 
							_debug( 1, progName, "forcing %s DOWN due to schedule", shades[ i].name) ;
							newShadePosition	=	1 ;
						} else {
							_debug( 1, progName, "%s is already DOWN, this is ok ... ", shades[ i].name) ;
						}
						break ;
					case	'U'	:			// move it definitely Up
						if ( actShadePosition != 0) {		//  && actShadePosRel >= 95) {
							_debug( 1, progName, "forcing %s UP due to schedule", shades[ i].name) ;
							newShadePosition	=	0 ;
						} else {
							_debug( 1, progName, "%s is already UP, this is ok ... ", shades[ i].name) ;
						}
						break ;
					case	'-'	:			// leave as is
						_debug( 1, progName, "no impact on %s due to schedule", shades[ i].name) ;
						break ;
					}
				}
			} else {
				_debug( 1, progName, "no impact on %s due to auto-mode\n", shades[ i].name) ;
			}
			switch ( newShadePosition) {
			case	0	:		// position DOWN
				setDown( myEIB, shades[ i].gaBase) ;
				sleep( 5) ;
				break ;
			case	1	:		// position UP
				setUp( myEIB, shades[ i].gaBase) ;
				sleep( 5) ;
				break ;
			default	:
				break ;
			}
		}
		/**
		 *
		 */
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

void	dumpShadeTable( node *data, int *ints, float *floats) {
	int	i ;
	for ( i=0 ; shades[i].id != -1 ; i++) {
		if ( shades[i].active == 1) {
			printf( "%s: %-20s => Actual := %s\n",
					progName,
					shades[i].name,
					(ints[ atoi( shades[ i].gaBase)] == 1 ? "down" : "up")
				) ;
		}
	}
}

void	setDown( eibHdl *_myEIB, char *_groupAddr) {
	_debug( 1, progName,  "... will position shade down ...") ;
	eibWriteBit( _myEIB, _groupAddr, 0, 1) ;
}

void	setUp( eibHdl *_myEIB, char *_groupAddr) {
	_debug( 1, progName,  "... will position shade up ...") ;
	eibWriteBit( _myEIB, _groupAddr, 1, 1) ;
}

/**
 *
 */
shadeSetup	*getShadeSetup( shadePosition *_baseData) {
	shadeSetup	*thisShadeSetup	=	NULL ;
	int			i ;
	thisShadeSetup	=	malloc( 50 * sizeof( shadeSetup)) ;
	for ( i=0 ; _baseData[i].id != -1 ; i++) {

	}
	return thisShadeSetup ;
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
	printf( "%s: Shade Manager\n", progName) ;
	printf( "%s: %s [-D=<debugLevel>] [-F] [-Q=<queueNumber>] \n\n", progName, progName) ;
	printf( "-F force process to run in foreground, do not daemonize\n") ;
}
