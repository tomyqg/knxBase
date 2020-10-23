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
/*
 *
 * knxtrace.c
 *
 * EIB/KNX bus monitor with buffer
 *
 * thius module monitors the messages on the knx bus and stores all group address write
 * value in a table stored in shared memory.
 *
 * Shared Memory Segment #0:	COM Table with sizes of the following three
 *				shared memory segments
 *				-> COMtable, -> int	*sizeTable
 * Shared Memory Segment #1:	OPC Table with value buffer
 *				-> OPCtable, -> node	*data (copy from _data)
 * Shared Memory Segment #2:	KNX Group Address value buffer
 *				-> KNXtable, -> int	*ints AND float	*floats
 * Shared Memory Segment #3:	Fixes size buffer of 256 bytes to communicate
 *				buffer sizes for Shared Memory Segment #1 and #2.
 *				->CRFtable, int		*crf
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * -----------------------------------------------------------------------------
 * 2016-01-13	PA1		khw		inception;
 * 2016-04-07	PA2		khw		included logging to MySQL database;
 * 2016-11-11	PA3		khw		extensions to include data;
 * 2017-02-14	PA4		khw		fault tolerance for the MySQL connection;
 * 2018-06-22	PA5		khw		added unistd.h include;
 * 								added mqtt functionality (again);
 * 2018-07-29	PA6		khw		modified logging towards system logger;
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
#include	<math.h>
#include	<getopt.h>
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/signal.h>
#include	<mysql.h>

#include	"debug.h"
#include	"nodeinfo.h"
#include	"knxprot.h"
#include	"inilib.h"

#include	"eib.h"		// rs232.c will differentiate:
						// ifdef  __MAC__
						// 	simulation
						// else
						// 	real life
#include	"mylib.h"

#define	MAX_SLEEP	2

extern	void	help() ;
extern	void	logit( char *_fmt, ...) ;

		void	hdlMsg( eibHdl *, knxMsg *) ;
extern	void	logDisas( eibHdl *, knxMsg *) ;
extern	void	logBin( eibHdl *, knxMsg *) ;
extern	void	logHex( eibHdl *, knxMsg *) ;
extern	void	logCtrl( eibHdl *, knxMsg *, char *txt) ;
extern	void	logDb( eibHdl *, knxMsg *, int *, node *) ;
extern	void	logMqtt( eibHdl *, knxMsg *, int *, node *) ;

char	progName[64] ;
pid_t	ownPID ;
int		runLevel ;

extern	void	cbOpenP2P( eibHdl *, knxMsg *) ;
extern	void	cbCloseP2P( eibHdl *, knxMsg *) ;
extern	void	cbConfP2P( eibHdl *, knxMsg *) ;
extern	void	cbRejectP2P( eibHdl *, knxMsg *) ;
extern	void	cbGroupValueRead( eibHdl *, knxMsg *) ;
extern	void	cbGroupValueResponse( eibHdl *, knxMsg *) ;
extern	void	cbGroupValueWrite( eibHdl *, knxMsg *) ;
extern	void	cbIndividualAddrWrite( eibHdl *, knxMsg *) ;
extern	void	cbIndividualAddrRequest( eibHdl *, knxMsg *) ;
extern	void	cbIndividualAddrResponse( eibHdl *, knxMsg *) ;
extern	void	cbAdcRead( eibHdl *, knxMsg *) ;
extern	void	cbAdcResponse( eibHdl *, knxMsg *) ;
extern	void	cbMemoryRead( eibHdl *, knxMsg *) ;
extern	void	cbMemoryResponse( eibHdl *, knxMsg *) ;
extern	void	cbMemoryWrite( eibHdl *, knxMsg *) ;
extern	void	cbUserMessage( eibHdl *, knxMsg *) ;
extern	void	cbMaskVersionRead( eibHdl *, knxMsg *) ;
extern	void	cbMaskVersionResponse( eibHdl *, knxMsg *) ;
extern	void	cbRestart( eibHdl *, knxMsg *) ;

eibCallbacks	myCB	=	{
			cbOpenP2P
		,	cbCloseP2P
		,	cbConfP2P
		,	cbRejectP2P
		,	cbGroupValueRead
		,	cbGroupValueResponse
		,	cbGroupValueWrite
		,	cbIndividualAddrWrite
		,	cbIndividualAddrRequest
		,	cbIndividualAddrResponse
		,	cbAdcRead
		,	cbAdcResponse
		,	cbMemoryRead
		,	cbMemoryResponse
		,	cbMemoryWrite
		,	cbUserMessage
		,	cbMaskVersionRead
		,	cbMaskVersionResponse
		,	cbRestart
		} ;

typedef	enum	modeTrace	{
		KNX_TRC_DISAS	=	1
	,	KNX_TRC_LOG_BIN	=	2
	,	KNX_TRC_LOG_HEX	=	3
	,	KNX_TRC_HANDLE	=	4
	,	KNX_TRC_LOG_DB	=	5
	,	KNX_TRC_MQTT	=	6
	}	modeTrace ;

int	ownAddr		=	0xfff0 ;
int	progMode	=	1 ;
int	connectedTo	=	0 ;
int	myTraceMode	=	KNX_TRC_DISAS ;
char	myTraceFileName[128] ;
char	dbHost[64]	=	"*" ;
char	dbName[64]	=	"*" ;
char	dbUser[64]	=	"*" ;
char	dbPassword[64]	=	"*" ;
char	cfgMqttHost[32] ;
char	cfgMqttUser[32] ;
char	cfgMqttPasswd[32] ;
char	cfgMqttCACert[64] ;
char	cfgMqttKey[64] ;
char	cfgMqttCert[64] ;
char	cfgMqttPrefix[64] ;
char	cfgMqttFilter[64] ;
bool	cfgMqttForwardAll	=	false ;
bool	cfgMqttForwardBaos	=	false ;

/**
 *
 */
int		cfgQueueKey	=	10031 ;
int		cfgSenderAddr	=	1 ;
int		cfgDaemon		=	1 ;

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
	sprintf( buffer, "knxtrace"
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
	} else if ( strcmp( _block, "[knxtrace]") == 0) {
		if ( strcmp( _para , "dbHost") == 0) {
			strcpy( dbHost, _value) ;
		} else if ( strcmp( _para, "dbName") == 0) {
			strcpy( dbName, _value) ;
		} else if ( strcmp( _para, "dbUser") == 0) {
			strcpy( dbUser, _value) ;
		} else if ( strcmp( _para, "dbPassword") == 0) {
			strcpy( dbPassword, _value) ;
//		} else if ( strcmp( _para,"dbHost") == 0) {
//			strcpy( dbHost, _value) ;
		} else if ( strcmp( _para, "mqttHost") == 0) {
			strcpy( cfgMqttHost, _value) ;
		} else if ( strcmp( _para, "mqttUser") == 0) {
			strcpy( cfgMqttUser, _value) ;
		} else if ( strcmp( _para, "mqttPasswd") == 0) {
			strcpy( cfgMqttPasswd, _value) ;
		} else if ( strcmp( _para, "mqttCACert") == 0) {
			strcpy( cfgMqttCACert, _value) ;
		} else if ( strcmp( _para, "mqttKey") == 0) {
			strcpy( cfgMqttKey, _value) ;
		} else if ( strcmp( _para, "mqttCert") == 0) {
			strcpy( cfgMqttCert, _value) ;
		} else if ( strcmp( _para, "mqttPrefix") == 0) {
			strcpy( cfgMqttPrefix, _value) ;
		} else if ( strcmp( _para, "mqttFilter") == 0) {
			strcpy( cfgMqttFilter, _value) ;
			if ( strcmp( cfgMqttFilter, "none") == 0) {
				cfgMqttForwardAll	=	true ;
			} else if ( strcmp( cfgMqttFilter, "baos") == 0) {
				cfgMqttForwardBaos	=	true ;
			}
		}
	}
}
/**
 * variables related to MySQL database connection
 */
MYSQL	*mySql ;
extern	void	mySqlQuery( char *) ;

int	main( int argc, char *argv[]) {
		eibHdl	*myEIB ;
		pid_t	pid, sid ;
		int	opt ;
		int	status		=	0 ;
		int	sleepTimer	=	0 ;
		int	i ;
		time_t	actTime ;
	struct	tm	*myTime ;
		char	timeBuffer[64] ;
	int	cycleCounter ;
	/**
	 * variables needed for the reception of EIB message
	 */
			FILE	*file ;
	unsigned	char	buf, myBuf[64] ;
			node	*actData ;
			int	rcvdBytes ;
			int	monitor	=	0 ; 		// default: no message monitoring
	unsigned        int     control ;
	unsigned        int     addressType ;
	unsigned        int     routingCount ;
			char    *ptr ;
			knxMsg	myMsgBuf ;
			knxMsg	*myMsg ;
	unsigned	char	*cp ;
	/**
	 * define shared memory segment #0: COM Table
	 *	this segment holds information about the sizes of the other tables
 	 */
		key_t	shmCOMKey	=	SHM_COM_KEY ;
		int	shmCOMFlg	=	IPC_CREAT | 0666 ;
		int	shmCOMId ;
		int	shmCOMSize	=	256 ;
		int	*sizeTable ;
	/**
	 * define shared memory segment #1: OPC Table with buffer
	 *	this segment holds the structure defined in nodedata.h
 	 */
		key_t	shmOPCKey	=	SHM_OPC_KEY ;
		int	shmOPCFlg	=	IPC_CREAT | 0666 ;
		int	shmOPCId ;
		int	shmOPCSize ;
		node	*data ;
	/**
	 * define shared memory segment #2: KNX Table with buffer
	 *	this segment holds the KNX table defined in nodedata.h
 	 */
		key_t	shmKNXKey	=	SHM_KNX_KEY ;
		int	shmKNXFlg	=	IPC_CREAT | 0666 ;
		int	shmKNXId ;
		int	shmKNXSize	=	65536 * sizeof( float) ;
		float	*floats ;
		int	*ints ;

	/**
	 * define shared memory segment #3: CRF Table with buffer
	 *	this segment holds the cross-reference-table
 	 */
		key_t	shmCRFKey	=	SHM_CRF_KEY ;
		int	shmCRFFlg	=	IPC_CREAT | 0666 ;
		int	shmCRFId ;
		int	shmCRFSize	=	65536 * sizeof( int) ;
		int	*crf ;
	char	iniFilename[]	=	"knx.ini" ;

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
	while (( opt = getopt( argc, argv, "D:FQ:f:m:?")) != -1) {
		switch ( opt) {
		case	'D'	:
			debugLevel	=	atoi( optarg) ;
			cfgDaemon	=	0 ;
			break ;
		case    'F'     :
			cfgDaemon	=	0 ;
			break ;
		case	'Q'	:
			cfgQueueKey	=	atoi( optarg) ;
			break ;
		case	'f'	:
			strcpy( myTraceFileName, optarg) ;
			break ;
		case	'm'	:
			myTraceMode	=	atoi( optarg) ;
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

	/**
	 *
	 */
	switch ( myTraceMode) {
	case	KNX_TRC_DISAS	:
		break ;
	case	KNX_TRC_LOG_BIN	:
		break ;
	case	KNX_TRC_LOG_HEX	:
		break ;
	case	KNX_TRC_HANDLE	:
		break ;
	case	KNX_TRC_LOG_DB	:
		if (( mySql = mysql_init( NULL)) == NULL) {
			_debug( 0, progName, "could not connect to MySQL Server") ;
			_debug( 0, progName, "Exiting with -1");
			exit( -1) ;
		}
		if ( mysql_real_connect( mySql, dbHost, dbUser, dbPassword, dbName, 0, NULL, 0) == NULL) {
			_debug( 0, progName, "mysql error := '%s'", mysql_error( mySql)) ;
			_debug( 0, progName, "Exiting with -2");
			exit( -2) ;
		}
		break ;
	case	KNX_TRC_MQTT	:
		break ;
	default	:
		break ;
	}

	/**
	 * get and attach the shared memory for COMtable
	 */
	if (( shmCOMId = shmget( shmCOMKey, shmCOMSize, 0600)) < 0) {
		exit( -1) ;
	}
	if (( sizeTable = (int *) shmat( shmCOMId, NULL, 0)) == (int *) -1) {
		exit( -1) ;
	}
	shmCOMSize	=	sizeTable[ SIZE_COM_TABLE] ;
	shmOPCSize	=	sizeTable[ SIZE_OPC_TABLE] ;
	shmKNXSize	=	sizeTable[ SIZE_KNX_TABLE] ;
	shmCRFSize	=	sizeTable[ SIZE_CRF_TABLE] ;

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
	ints	=	(int *) floats ;

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
	 *
	 */
	printf( "opening queue key %d\n", cfgQueueKey) ;
	myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_RD) ;
	sleepTimer	=	0 ;
	cycleCounter	=	0 ;

	/**
	 *
	 */
	while ( runLevel >= 0) {
		_debug( 1, progName, "cycleCounter := %d", cycleCounter++) ;
		myMsg	=	eibReceiveMsg( myEIB, &myMsgBuf) ;
		_debug( 1, progName, "message received") ;
		if ( myMsg != NULL) {
			if ( myMsg->apn != 0) {
				_debug( 1, progName, "frameType := %d", myMsg->frameType) ;
				switch ( myMsg->frameType) {
				case	eibDataFrame	:
					sleepTimer	=	0 ;
					eibDisect( myMsg) ;
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						logDisas( myEIB, myMsg) ;
						break ;
					case	KNX_TRC_LOG_BIN	:
						logBin( myEIB, myMsg) ;
						break ;
					case	KNX_TRC_LOG_HEX	:
						logHex( myEIB, myMsg) ;
						break ;
					case	KNX_TRC_HANDLE	:
						hdlMsg( myEIB, myMsg) ;
						break ;
					case	KNX_TRC_LOG_DB	:
						logDb( myEIB, myMsg, crf, data) ;
						break ;
					case	KNX_TRC_MQTT	:
						logMqtt( myEIB, myMsg, crf, data) ;
						break ;
					default	:
						break ;
					}
					break ;
				case	eibAckFrame	:
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						break ;
					case	KNX_TRC_LOG_BIN	:
						break ;
					case	KNX_TRC_LOG_HEX	:
						logCtrl( myEIB, myMsg, "ACK") ;
						break ;
					case	KNX_TRC_HANDLE	:
//						hdlMsg( myEIB, myMsg) ;
						break ;
					default	:
						break ;
					}
					break ;
				case	eibNackFrame	:
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						break ;
					case	KNX_TRC_LOG_BIN	:
						break ;
					case	KNX_TRC_LOG_HEX	:
						logCtrl( myEIB, myMsg, "NACK") ;
						break ;
					case	KNX_TRC_HANDLE	:
//						hdlMsg( myEIB, myMsg) ;
						break ;
					default	:
						break ;
					}
					break ;
				case	eibBusyFrame	:
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						break ;
					case	KNX_TRC_LOG_BIN	:
						break ;
					case	KNX_TRC_LOG_HEX	:
						logCtrl( myEIB, myMsg, "BUSY") ;
						break ;
					case	KNX_TRC_HANDLE	:
//						hdlMsg( myEIB, myMsg) ;
						break ;
					default	:
						break ;
					}
					break ;
				case	eibPollDataFrame	:
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						break ;
					case	KNX_TRC_LOG_BIN	:
						break ;
					case	KNX_TRC_LOG_HEX	:
						logCtrl( myEIB, myMsg, "::eibPollDataFrame") ;
						break ;
					case	KNX_TRC_HANDLE	:
//						hdlMsg( myEIB, myMsg) ;
						break ;
					default	:
						break ;
					}
					break ;
				case	eibOtherFrame	:
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						break ;
					case	KNX_TRC_LOG_BIN	:
						break ;
					case	KNX_TRC_LOG_HEX	:
						logCtrl( myEIB, myMsg, "::eibOtherFrame") ;
						break ;
					case	KNX_TRC_HANDLE	:
//						hdlMsg( myEIB, myMsg) ;
						break ;
					default	:
						break ;
					}
					break ;
				case	eibDataConfirmFrame	:
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						break ;
					case	KNX_TRC_LOG_BIN	:
						break ;
					case	KNX_TRC_LOG_HEX	:
						logCtrl( myEIB, myMsg, "::eibDataConfirmFrame") ;
						break ;
					case	KNX_TRC_HANDLE	:
//						hdlMsg( myEIB, myMsg) ;
						break ;
					default	:
						break ;
					}
					break ;
				case	eibTPUARTStateIndication	:
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						break ;
					case	KNX_TRC_LOG_BIN	:
						break ;
					case	KNX_TRC_LOG_HEX	:
						logCtrl( myEIB, myMsg, "::eibTPUARTStateIndication") ;
						break ;
					case	KNX_TRC_HANDLE	:
//						hdlMsg( myEIB, myMsg) ;
						break ;
					default	:
						break ;
					}
					break ;
				case	bridgeCtrlFrame	:
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						break ;
					case	KNX_TRC_LOG_BIN	:
						break ;
					case	KNX_TRC_LOG_HEX	:
						logCtrl( myEIB, myMsg, "::bridgeCtrlFrame") ;
						break ;
					case	KNX_TRC_HANDLE	:
//						hdlMsg( myEIB, myMsg) ;
						break ;
					default	:
						break ;
					}
					break ;
				default	:
					switch ( myTraceMode) {
					case	KNX_TRC_DISAS	:
						break ;
					case	KNX_TRC_LOG_BIN	:
						break ;
					case	KNX_TRC_LOG_HEX	:
						logCtrl( myEIB, myMsg, "::??????????") ;
						break ;
					case	KNX_TRC_HANDLE	:
//						hdlMsg( myEIB, myMsg) ;
						break ;
					default	:
						break ;
					}
					break ;
				}
			} else {
				_debug( 1, progName, "message received from INTERNAL sender; not considered;") ;
			}
		} else {
/**
				sleepTimer++ ;
				if ( sleepTimer > MAX_SLEEP)
					sleepTimer	=	MAX_SLEEP ;
				_debug( 1, progName, "will go to sleep ... for %d seconds", sleepTimer) ;
				sleep( sleepTimer) ;
*/
		}
	}

	/**
	 * detach shared memory: cross-reference table
	 */
	logit( "detaching cross-reference-table") ;
	shmdt( crf) ;						// detach shared memory of knxBus

	/**
	 * destroy shared memory: variable area
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

#include	"_knxfsm.c"
#include	"_disasMsg.c"

void	logDisas( eibHdl *_myEIB, knxMsg *_msg) {
	time_t		t ;
	struct	tm	tm ;
	char		cTime[64] ;
	FILE	*traceFile ;
	int	i, len ;
	/**
	 *
	 */
	/**
	 *
	 */
	if ( strlen( myTraceFileName) == 0) {
		traceFile	=	stdout ;
	} else {
		traceFile	=	fopen( myTraceFileName, "a+") ;
	}
	if ( traceFile != NULL) {
		disasMsg( traceFile, _myEIB, _msg) ;
		if ( strlen( myTraceFileName) == 0) {
		} else {
			fclose( traceFile) ;
		}
	}
}

void	logDb( eibHdl *_myEIB, knxMsg *_msg, int *_crf, node *_data) {
	node	*actData ;
	time_t		t ;
	struct	tm	tm ;
	char		cTime[64] ;
	char	value[33] ;
	char	sql[256] ;
	FILE	*traceFile ;
	int	i, len ;
	int	updatedRows ;
	int	mySqlstatus ;
			MYSQL_RES	*result ;
			MYSQL_ROW	row ;
	char	group[64] ;
	/**
	 *
	 */
	if ( _msg->apn != 0 && _crf[ _msg->rcvAddr] != 0 && ( ( _msg->control & 0x20) == 0x20)) {
		actData	=	&_data[ _crf[ _msg->rcvAddr]] ;
		switch ( actData->type) {
		case	dtBit	:
			_debug( 1, progName, "Assigning BIT") ;
			sprintf( value, "%d", _msg->mtext[1] & 0x01) ;
			break ;
		case	dtFloat2	:
			_debug( 1, progName, "Assigning HALF-FLOAT") ;
			sprintf( value, "%5.2f", hfbtf( &_msg->mtext[2])) ;
			break ;
		case	dtDateTime	:
			_debug( 1, progName, "Assigning HALF-FLOAT") ;
			sprintf( value, "%02d:%02d:%02d",
						_msg->mtext[2] & 0x1f,
						_msg->mtext[3] & 0x3f,
						_msg->mtext[4] & 0x3f
						) ;
			break ;
		default	:
			sprintf( value, "unkonw datatype") ;
			break ;
		}
		/**
		 * add the logging record
		 */
		sprintf( sql, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%s');",
				_msg->rcvAddr,
				actData->type,
				value
			) ;
		mySqlQuery( sql) ;
		/**
		 * update teh status record
		 * due to a problem with the Jessie MySQL we need to delete any existing reference to this group object
		 * and insert a new record (this will be quite heavy on the SDRAM if the Db is run on the RasPi server!!!)
		 */
		sprintf( sql, "DELETE FROM objectValue WHERE GroupObjectId = %d ;",
				_msg->rcvAddr
			) ;
		mySqlQuery( sql) ;
		sprintf( sql, "INSERT INTO objectValue( GroupObjectId, GroupObject, Descriptor, DataType, Value) VALUES( %d, '%s', '%s', %d, '%s');",
				_msg->rcvAddr,
				eibIntToGroup( _msg->rcvAddr, group),
				actData->name,
				actData->type,
				value
			) ;
		mySqlQuery( sql) ;
	} else {
//		printf( "APN ...... : %d \n", _msg->apn) ;
//		printf( "rcvrAdr .. : %d \n", _msg->rcvAddr) ;
	}
}

void	logMqtt( eibHdl *_myEIB, knxMsg *_msg, int *_crf, node *_data) {
	node	*actData ;
	time_t		t ;
	struct	tm	tm ;
	char		cTime[64] ;
	char	value[33] ;
	char	cmd[256] ;
	FILE	*traceFile ;
	int	i, len ;
	/**
	 *
	 */
	if ( _msg->apn != 0 && (( _msg->control & 0x20) == 0x20) && (( cfgMqttForwardBaos && _crf[ _msg->rcvAddr] != 0) || cfgMqttForwardAll)) {
		actData	=	&_data[ _crf[ _msg->rcvAddr]] ;
		switch ( actData->type) {
		case	dtBit	:
			_debug( 1, progName, "Assigning BIT") ;
			sprintf( value, "%d", _msg->mtext[1] & 0x01) ;
			break ;
		case	dtUInt1	:
			_debug( 1, progName, "Assigning UInt1") ;
			sprintf( value, "%d", _msg->mtext[2]) ;
			break ;
		case	dtFloat2	:
			_debug( 1, progName, "Assigning HALF-FLOAT") ;
			sprintf( value, "%5.2f", hfbtf( &_msg->mtext[2])) ;
			break ;
		case	dtDateTime	:
			_debug( 1, progName, "Assigning HALF-FLOAT") ;
			sprintf( value, "%02d:%02d:%02d",
						_msg->mtext[2] & 0x1f,
						_msg->mtext[3] & 0x3f,
						_msg->mtext[4] & 0x3f
						) ;
			break ;
		default	:
			sprintf( value, "unkonw datatype") ;
			break ;
		}
		/**
		 * add the logging record
		 */
		sprintf( cmd, "mosquitto_pub -h %s -u %s -P %s -t '%s%d/%d/%d' -m '%s'",
				cfgMqttHost,
				cfgMqttUser,
				cfgMqttPasswd,
				cfgMqttPrefix,
				(( _msg->rcvAddr & 0xf800) >> 11) & 0x0001f,
				(( _msg->rcvAddr & 0x0700) >>  8) & 0x00007,
				(( _msg->rcvAddr & 0x00ff) >>  0) & 0x000ff,
				value
			) ;
		if ( debugLevel > 0) {
			printf( "%s \n", cmd) ;
		}
		system( cmd) ;
	} else {
//		printf( "APN ...... : %d \n", _msg->apn) ;
//		printf( "rcvrAdr .. : %d \n", _msg->rcvAddr) ;
	}
}

void	mySqlQuery( char *_sql) {
	MYSQL_RES	*result ;
	int	sqlResult	=	0 ;
	int	retryCount	=	0 ;
	int	reconnectCount	=	0 ;
	MYSQL	*connectResult ;
	do {
		if ( retryCount > 0) {
			logit(  "mySQL: retrying ...") ;
		}
		if ( ( sqlResult = mysql_query( mySql, _sql))) {
			logit(  "mySQL: error during query ...") ;
			do {
				if ( ( connectResult = mysql_real_connect( mySql, dbHost, dbUser, dbPassword, dbName, 0, NULL, 0)) == NULL) {
					logit(  "mySQL: reconnecting ...") ;
					reconnectCount++ ;
					sleep( reconnectCount) ;
				}
			} while ( reconnectCount < 10 && connectResult == NULL) ;
			if ( reconnectCount >= 10) {
				logit(  "mySQL: reconnect count exceeded; terminating ...") ;
				_debug( 0, progName, "mysql error := '%s'", mysql_error( mySql)) ;
				_debug( 0, progName, "Exiting with -2");
			}
		} else {
			result  =       mysql_store_result( mySql) ;
			mysql_free_result( result) ;
		}
	} while ( retryCount < 10 && sqlResult) ;
	if ( retryCount >= 10) {
		logit(  "mySQL: retry count exceeded; terminating ...") ;
		_debug( 0, progName, "mysql error := '%s'", mysql_error( mySql)) ;
		_debug( 0, progName, "Exiting with -3");
		exit( -3) ;
	}
}

void	logBin( eibHdl *_myEIB, knxMsg *_msg) {
	time_t		t ;
	struct	tm	tm ;
	char		cTime[64] ;
	FILE	*traceFile ;
	int	i, len ;
	/**
	 *
	 */
	/**
	 *
	 */
	if ( strlen( myTraceFileName) == 0) {
		traceFile	=	stdout ;
	} else {
		traceFile	=	fopen( myTraceFileName, "a+") ;
	}
	if ( traceFile != NULL) {
		if ( strlen( myTraceFileName) == 0) {
		} else {
			fclose( traceFile) ;
		}
	} else {
		_debug( 0, progName, "can't open log file [%s] for appending ... ", myTraceFileName) ;
	}
}

void	logHex( eibHdl *_myEIB, knxMsg *_msg) {
	time_t		t ;
	struct	tm	tm ;
	char		cTime[64] ;
	FILE	*traceFile ;
	int	i, len ;
	/**
	 *
	 */
	/**
	 *
	 */
	if ( strlen( myTraceFileName) == 0) {
		traceFile	=	stdout ;
	} else {
		traceFile	=	fopen( myTraceFileName, "a+") ;
	}
	if ( traceFile != NULL) {
		/**
		 * start with a time stamp
		 */
		t	=	time( NULL) ;
		tm	=	*localtime( &t) ;
		fprintf( traceFile, "%4d-%02d-%02d %02d:%02d:%02d ", tm.tm_year + 1900, tm.tm_mon + 1, tm.tm_mday, tm.tm_hour, tm.tm_min, tm.tm_sec);
		/**
		 * add the message
		 */
		fprintf( traceFile, "APN:%02d (Len: %2d)%02x %02x %02x %02x %02x %02x "
			,	_msg->apn
			,	_msg->length
			,	_msg->control
			,	(_msg->sndAddr >> 8) & 0x00ff
			,	_msg->sndAddr & 0x00ff
			,	(_msg->rcvAddr >> 8) & 0x00ff
			,	_msg->rcvAddr & 0x00ff
			,	_msg->info
			) ;
		len	=	_msg->length ;
		for ( i=0 ; i<len ; i++) {
			fprintf( traceFile, "%02x ", _msg->mtext[i]) ;
		}
		fprintf( traceFile, "%02x\n", _msg->checksum) ;
		if ( strlen( myTraceFileName) == 0) {
		} else {
			fclose( traceFile) ;
		}
	} else {
		_debug( 0, progName, "can't open log file [%s] for appending ... ", myTraceFileName) ;
	}
}

void	logCtrl( eibHdl *_myEIB, knxMsg *_msg, char *txt) {
	time_t		t ;
	struct	tm	tm ;
	char		cTime[64] ;
	FILE	*traceFile ;
	int	i, len ;
	/**
	 *
	 */
	/**
	 *
	 */
	if ( strlen( myTraceFileName) == 0) {
		traceFile	=	stdout ;
	} else {
		traceFile	=	fopen( myTraceFileName, "a+") ;
	}
	if ( traceFile != NULL) {
		/**
		 * start with a time stamp
		 */
		t	=	time( NULL) ;
		tm	=	*localtime( &t) ;
		fprintf( traceFile, "%4d-%02d-%02d %02d:%02d:%02d ", tm.tm_year + 1900, tm.tm_mon + 1, tm.tm_mday, tm.tm_hour, tm.tm_min, tm.tm_sec);
		/**
		 * add the message
		 */
		fprintf( traceFile, "APN:%02d %s (%02x)\n", _msg->apn, txt, _msg->control) ;
		if ( strlen( myTraceFileName) == 0) {
		} else {
			fclose( traceFile) ;
		}
	} else {
		_debug( 0, progName, "can't open log file [%s] for appending ... ", myTraceFileName) ;
	}
}

void	cbOpenP2P( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbCloseP2P( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbConfP2P( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbRejectP2P( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbGroupValueRead( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbGroupValueResponse( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbGroupValueWrite( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbIndividualAddrWrite( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbIndividualAddrRequest( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbIndividualAddrResponse( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbAdcRead( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbAdcResponse( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbMemoryRead( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbMemoryResponse( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbMemoryWrite( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbUserMessage( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbMaskVersionRead( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbMaskVersionResponse( eibHdl *_myEIB, knxMsg *myMsg) {
}

void	cbRestart( eibHdl *_myEIB, knxMsg *myMsg) {
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
	printf( "%s: %s [-D <debugLevel>] [-Q=<queueIdf>] [-f filename] [-b] [-m 1|2|3|4]\n\n", progName, progName) ;
	printf( "Start a tracer on the internal EIB/KNX busn") ;
	printf( "-f filename\tredirect oiutput to given file.\n") ;
	printf( "-m mode\t1 = disassembly\n") ;
	printf( "       \t2 = log binary\n") ;
	printf( "       \t3 = log hexadecimal\n") ;
	printf( "       \t4 = handle\n") ;
	printf( "       \t5 = log to Database (currently only MySQL; config in /etc/knx.d/knx.ini)\n") ;
}
