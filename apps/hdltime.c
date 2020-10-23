/**
 * Copyright (c) 2015-2018 wimtecc, Karl-Heinz Welter
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
 * date			rev.	who	what
 * ----------------------------------------------------------------------------
 * 2016-04-04	PA1		khw	inception;
 * 2016-10-07	PA2		khw	enabled EG_BATH;
 * 2018-07-29	PA6		khw	modified logging towards system logger;
 *
 */
#include	<stdio.h>
#include	<stdarg.h>
#include	<unistd.h>
#include	<stdlib.h>
#include	<string.h>
#include	<strings.h>
#include	<time.h>
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>

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
		int		gaPosition ;
		timeProfile	*timer ;
	}	shadePosition ;

/**
 * structure with
 */
typedef	struct	{
		int			id ;
		int			active ;
		char		name[32] ;
		int			gaPosition ;		// actual position
		time_t		timeDown ;		// time for moving down
		time_t		timeUp ;
		int			timeDelayDown ;		// time delay in minutes for moving down
		int			timeDelayUp ;
		int			brightnessDown ;	// brightness for moving down
		int			brightnessUp ;		// brightness for moving up
	}	shadeSetup ;

extern	void	setDown( eibHdl *, int) ;
extern	void	setUp( eibHdl *, int) ;

extern	void		dumpData( node *, int, int, void *) ;
extern	void		dumpShadeTable( node *, int *, float *) ;
extern	shadeSetup	*getShadeSetup( shadePosition *) ;

char	progName[64] ;
pid_t	ownPID ;

//					  00:00   02:00   04:00   06:00   08:00   10:00   12:00   14:00   16:00   18:00   20:00   22:00
// Tag/Nacht: 05:00 - 23:30
timeProfile	wdDayNight	=	{"--------------------1-------------------------------------------------------------------0-------"} ;

// Tag/Nacht: 05:00 - 23:30
timeProfile	wdMainComfort	=	{"--------------------1-------------------------------------------------------------------------0-"} ;
timeProfile	wdMainNight	=	{"--------------------0-------------------------------------------------------------------------1-"} ;

// Tag/Nacht: 05:00 - 23:30
timeProfile	wdSleepComfort	=	{"--------------------1---------------------------------------------------------------0---------0-"} ;
timeProfile	wdSleepNight	=	{"--------------------0---------------------------------------------------------------1---------1-"} ;

// Tag/Nacht: 05:00 - 23:30
timeProfile	wdMiscComfort	=	{"--------------------1-------------------------------------------------------------------------0-"} ;
timeProfile	wdMiscNight	=	{"--------------------0-------------------------------------------------------------------------1-"} ;

// Tag/Nacht: 04:00 - 23:30
timeProfile	wdBathComfort	=	{"----------------1-----------------------------------------------------------------------------0-"} ;
timeProfile	wdBathNight	=	{"----------------0-----------------------------------------------------------------------------1-"} ;

shadePosition	shades[]	=	{
/**
 *	other timer controlled features
 */
		{	0, 	1,"DayNight"	,   10	,&wdDayNight		}
	,	{	0, 	1,"MainComfort"	,   11	,&wdMainComfort		}
	,	{	0, 	1,"MainNight"	,   12	,&wdMainNight		}
	,	{	0, 	1,"SleepComfort",   13	,&wdSleepComfort	}
	,	{	0, 	1,"SleepNight"	,   14	,&wdSleepNight		}
	,	{	0, 	1,"MiscComfort"	,   15	,&wdMiscComfort		}
	,	{	0, 	1,"MiscNight"	,   16	,&wdMiscNight		}
	,	{	0, 	1,"BathComfort"	,   17	,&wdBathComfort		}
	,	{	0, 	1,"BathNight"	,   18	,&wdBathNight		}
	,	{	-1, 	0,""		,    0	,NULL		}
	} ;

extern	void	help() ;
extern	void	logit( char *_fmt, ...) ;

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
int	cfgQueueKey	=	10031 ;
int	cfgSenderAddr	=	1 ;

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
int	main( int argc, char *argv[]) {
		eibHdl	*myEIB ;
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
		int	newShadePosition ;
		shadeSetup	*shadeData ;

	/**
	 *
	 */
	ownPID	=	getpid() ;
	strcpy( progName, *argv) ;

	/**
	 *
	 */
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 * get command line options
	 */
	while (( opt = getopt( argc, argv, "D:Q:?")) != -1) {
		switch ( opt) {
		case	'D'	:
			debugLevel	=	atoi( optarg) ;
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

	/**
	 *
	 */
	sprintf( logMsg, "%s: starting up ...", progName) ;
	logit( logMsg) ;

	/**
	 * setup the shared memory for COMtable
	 */

	/**
	 *
	 */
#include	"_shmblock.c"

	/**
	 * prepare in memory table with shade control information
	 */
	shadeData	=	getShadeSetup( shades) ;

	/**
	 * open the knx bus
	 */
	myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_WR | APN_INTRN) ;
	while ( debugLevel >= 0) {
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
			newShadePosition	=	-1 ;		// don't set
			actShadePosition	=	ints[ shades[ i].gaPosition] ;
			if ( shades[i].active == 1) {
				myProfile	=	shades[i].timer ;
				_debug( 1, progName, "control ........... : %02x", myProfile->mode[ timeIndex]) ;
				if ( myProfile != NULL) {
					_debug( 1, progName, "schedule for %-32s", shades[ i].name) ;
					switch ( myProfile->mode[ timeIndex]) {
	 				case	'D'	:			// switch if definitively OFF
						if ( actShadePosition != 1) {
							_debug( 1, progName, "forcing %s DOWN due to schedule", shades[ i].name) ;
							newShadePosition	=	1 ;
						} else {
							_debug( 1, progName, "%s is already DOWN, this is ok ... ", shades[ i].name) ;
						}
						break ;
					case	'U'	:			// switch it definitely ON
						if ( actShadePosition != 0) {
							_debug( 1, progName, "forcing %s UP due to schedule", shades[ i].name) ;
							newShadePosition	=	0 ;
						} else {
							_debug( 1, progName, "%s is already UP, this is ok ... ", shades[ i].name) ;
						}
						break ;
	 				case	'0'	:			// switch if definitively OFF
						if ( actShadePosition != 0) {
							_debug( 1, progName, "forcing %s OFF due to schedule", shades[ i].name) ;
							newShadePosition	=	0 ;
						} else {
							_debug( 1, progName, "%s is already OFF, this is ok ... ", shades[ i].name) ;
						}
						break ;
					case	'1'	:			// switch it definitely ON
						if ( actShadePosition != 1) {
							_debug( 1, progName, "forcing %s ON due to schedule", shades[ i].name) ;
							newShadePosition	=	1 ;
						} else {
							_debug( 1, progName, "%s is already ON, this is ok ... ", shades[ i].name) ;
						}
						break ;
					case	'-'	:			// leave as is
						_debug( 1, progName, "no impact on %s due to schedule", shades[ i].name) ;
						break ;
					}
				}
			}
			switch ( newShadePosition) {
			case	0	:		// position DOWN
				setDown( myEIB, shades[ i].gaPosition) ;
				sleep( 1) ;
				break ;
			case	1	:		// position UP
				setUp( myEIB, shades[ i].gaPosition) ;
				sleep( 1) ;
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
					(ints[ shades[ i].gaPosition] == 1 ? "on" : "off")
				) ;
		}
	}
}

void	setDown( eibHdl *_myEIB, int groupAddr) {
	_debug( 1, progName,  "... will position shade down ...") ;
	eibWriteBitIA( _myEIB, groupAddr, 0, 1) ;
}

void	setUp( eibHdl *_myEIB, int groupAddr) {
	_debug( 1, progName,  "... will position shade up ...") ;
	eibWriteBitIA( _myEIB, groupAddr, 1, 1) ;
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
	printf( "%s: %s [-D=<debugLevel>] [-Q=<queueNumber>] \n\n", progName, progName) ;
}
