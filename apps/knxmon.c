/**
 *
 * knxmon.c
 *
 * KNX bus monitor with buffer
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
 * Date			Rev.	Who	what
 * -----------------------------------------------------------------------------
 * 2015-11-20	PA1		khw	inception;
 * 2015-11-24	PA2		khw	added semaphores for shared memory
 *						and message queue;
 * 2015-11-26	PA3		khw	added mylib function for half-float
 *						conversions;
 * 2015-12-17	PA4		khw	added MySQL for logging data;
 * 2016-02-01	PA5		khw	added XML capabilities for the object table;
 * 2018-07-29	PA6		khw	modified logging towards system logger;
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
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/signal.h>
#include	<mysql.h>

#include	"nodeinfo.h"
#include	"knxprot.h"
#include	"knxtpbridge.h"
#include	"mylib.h"
#include	"myxml.h"
#include	"inilib.h"
#include	"eib.h"		// rs232.c will differentiate:
						// ifdef  __MAC__
						// 	simulation
						// else
						// 	real life
#include	"mylib.h"
#include	"myxml.h"		// short xml-reader wrapper

/**
 *
 */
#define	MAX_SLEEP	2
#define	SLEEP_TIME	1

typedef struct msgbuf {
                long	mtype;
                long	group;
                value	val;
                char	mtext[32] ;
        } msgBuf;

extern	void	help() ;
extern	int		restoreFromDb( node *, int *, int *, float *) ;
extern	void	logit( char *_fmt, ...) ;

/**
 *
 */
char	progName[64] ;
pid_t	ownPID ;
int		runLevel ;

/**
 *
 */
void	sigHandler( int _sig) {
	logit( "received signal") ;
	runLevel	-=	1 ;
}

/**
 *
 */
void	sigHandlerTERM( int _sig) {
	logit( "received signal SIGTERM") ;
	runLevel	=	-1 ;
}

/**
 *
 */
int		cfgQueueKey		=	10031 ;
int		cfgSenderAddr	=	1 ;
int		cfgDaemon		=	true ;				// default:	run as daemon process
int		cfgRestore		=	true ;				// default:	restore data from database
char	dbHost[64]		=	"*" ;
char	dbName[64]		=	"*" ;
char	dbUser[64]		=	"*" ;
char	dbPassword[64]	=	"*" ;

/**
 *
 */
void	strlwr( char *_argv) {
	while ( *_argv) {
		if ( *_argv >= 'A' && *_argv <= 'Z')
			*_argv	=	*_argv + 32 ;
		_argv++ ;
	}
}

/**
 *
 */
int	argToBool( char *_argv) {
	int		retval	=	false ;
	strlwr( _argv) ;
	if ( strcmp( _argv, "true") == 0 || strcmp( _argv, "yes") == 0 || strcmp( _argv, "y") == 0 || atoi( _argv) == 1)
		retval	=	true ;
	return retval ;
}

/**
 *
 */
void	iniCallback( char *_block, char *_para, char *_value) {
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		}
	} else if ( strcmp( _block, "[knxmon]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		} else if ( strcmp( _para , "dbHost") == 0) {
			strcpy( dbHost, _value) ;
		} else if ( strcmp( _para, "dbName") == 0) {
			strcpy( dbName, _value) ;
		} else if ( strcmp( _para, "dbUser") == 0) {
			strcpy( dbUser, _value) ;
		} else if ( strcmp( _para, "dbPassword") == 0) {
			strcpy( dbPassword, _value) ;
		}
	}
}

/**
 *
 */
int	main( int argc, char *argv[]) {
			eibHdl	*myEIB ;
			pid_t	pid, sid ;
			int		opt ;
			int		status		=	0 ;
			int		sleepTimer	=	0 ;
			int		i ;
			time_t	actTime ;
	struct	tm		*myTime ;
			char	timeBuffer[64] ;
	int	cycleCounter ;
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

	/**
	 * variables needed for the reception of EIB message
	 */
			FILE	*file ;
	unsigned	char	buf, myBuf[64] ;
			node	*actData ;
			node	*opcData ;
			int	opcDataSize ;
			int	rcvdBytes ;
			int	objectCount ;
			int	checksumError ;
			int	adrBytes ;
			int	n; 				// holds number of received characters
	unsigned        int     control ;
	unsigned        int     addressType ;
	unsigned        int     routingCount ;
		int     expectedLength ;
			float	value ;
	unsigned        int     checkSum ;
	unsigned        char    checkS1 ;
			char    *ptr ;
		msgBuf  buffer ;
			knxMsg	myMsgBuf ;
			knxMsg	*myMsg ;
			char	xmlObjFile[128]	=	"/etc/knx.d/baos.xml" ;
			time_t		t ;
			struct	tm	tm ;
			int	lastSec	=	0 ;
			int	lastMin	=	0 ;
			char		iniFilename[]	=	"knx.ini" ;

	/**
	 *	END OF TEST SECTION
	 */
	ownPID	=	getpid() ;
	signal( SIGTERM, sigHandlerTERM) ;
	signal( SIGINT, sigHandler) ;
	signal( SIGHUP, sigHandler) ;
	setbuf( stdout, NULL) ;				// disable output buffering on stdout
	strcpy( progName, *argv) ;

	/**
	 *
	 */
	runLevel	=	1 ;
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 * get command line options
	 */
	while (( opt = getopt( argc, argv, "FQ:r:x:?")) != -1) {
		switch ( opt) {
		case	'F'	:
			cfgDaemon	=	false ;
			break ;
		case	'Q'	:
			cfgQueueKey	=	atoi( optarg) ;
			break ;
		case	'r'	:
			cfgRestore	=	argToBool( optarg) ;
			break ;
		case	'x'	:
			strcpy( xmlObjFile, optarg) ;
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
	logit(  "starting up ...") ;

	/**
	 *	get the array of group adresses to be monitored
	 */
	opcData	=	getNodeTable( xmlObjFile, &objectCount) ;
	opcDataSize	=	objectCount * sizeof( node) ;

	/**
	 * setup the shared memory for COMtable
	 * COMTable
	 */
	if (( shmCOMId = shmget( shmCOMKey, shmCOMSize, IPC_CREAT | 0600)) < 0) {
		logit( "shmget failed for COMtable");
		exit( -1) ;
	}
	if (( sizeTable = (int *) shmat( shmCOMId, NULL, 0)) == (int *) -1) {
		logit( "shmat failed for COMtable");
		exit( -1) ;
	}
	sizeTable[ SIZE_COM_TABLE]	=	256 ;
	sizeTable[ SIZE_KNX_TABLE]	=	shmKNXSize ;
	sizeTable[ SIZE_CRF_TABLE]	=	shmCRFSize ;

	/**
	 * setup the shared memory for OPCtable
	 */
	shmOPCSize	=	opcDataSize ;
	sizeTable[ SIZE_OPC_TABLE]	=	shmOPCSize ;
	if (( shmOPCId = shmget (shmOPCKey, shmOPCSize, shmOPCFlg)) < 0) {
		logit( "shmget failed for OPCtable");
		exit(1);
	}
	if (( data = (node *) shmat(shmOPCId, NULL, 0)) == (node *) -1) {
		logit( "shmat failed for OPCtable");
		exit(1);
	}
	memcpy( data, opcData, opcDataSize) ;

	/**
	 * setup the shared memory for KNXtable
	 */
	if (( shmKNXId = shmget( shmKNXKey, shmKNXSize, IPC_CREAT | 0666)) < 0) {
		logit( "shmget failed for KNXtable");
		exit( -1) ;
	}
	if (( floats = (float *) shmat( shmKNXId, NULL, 0)) == (float *) -1) {
		logit( "shmat failed for KNXtable");
		exit( -1) ;
	}
	ints	=	(int *) floats ;

	/**
	 * setup the shared memory for CRFtable
	 */
	if (( shmCRFId = shmget( shmCRFKey, shmCRFSize, IPC_CREAT | 0600)) < 0) {
		logit( "shmget failed for CRFtable");
		exit( -1) ;
	}
	if (( crf = (int *) shmat( shmCRFId, NULL, 0)) == (int *) -1) {
		logit( "shmat failed for CRFtable");
		exit( -1) ;
	}

	/**
	 * build the cross-reference table for the KNX group numbers
	 */
	createCRF( data, objectCount, crf, (void *) floats) ;

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
//		close( STDIN_FILENO) ;
//		close( STDOUT_FILENO) ;
//		close( STDERR_FILENO) ;
	}

	/**
	 * restore from database if requested
	 */
	if ( cfgRestore) {
		logit(  "restoring from db ...") ;

		/**
		 * wait for mysql to become available
		 */
		int		reconnectCount	=	0 ;
		MYSQL	*mySql, *testSql ;
		mySql	=	mysql_init( NULL) ;
		do {
			if ( ( testSql = mysql_real_connect( mySql, dbHost, dbUser, dbPassword, dbName, 0, NULL, 0)) == NULL) {
				logit(  "mySql retrying ...") ;
				reconnectCount++ ;
				sleep( 3) ;
			}
		} while ( reconnectCount < 10 && mySql == NULL) ;
		if ( reconnectCount >= 10) {
			logit( "mySql reconnect count exceeded; terminating ...") ;
			logit( "mySql error := '%s'", mysql_error( mySql)) ;
			logit( "Exiting with -2");
		}
		logit( "starting restore") ;
		restoreFromDb( data, crf, ints, floats) ;
	} else {
		logit(  "skipping restore from db ...") ;
	}

	/**
	 *
	 */
	myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_RD) ;
	sleepTimer	=	0 ;
	cycleCounter	=	0 ;
	while ( runLevel >= 0) {
		myMsg	=	eibReceiveMsg( myEIB, &myMsgBuf) ;
		if ( myMsg != NULL) {
			sleepTimer	=	0 ;

			/**
			 * only process if apn != 0, ie. skip messages from "local" messengers
			 * and we know about the receiving group address
			 */
			if ( myMsg->apn != 0 && crf[ myMsg->rcvAddr] != 0) {
				actData	=	&data[ crf[ myMsg->rcvAddr]] ;
				switch ( myMsg->tlc) {
				case	0x00	:		// UDP
				case	0x01	:		// NDP
					switch ( myMsg->apci) {
					case	0x02	:	// groupValueWrite
						actData->knxHWAddr	=	myMsg->sndAddr ;
						switch ( actData->type) {
						case	dtBit	:
							value	=	myMsg->mtext[1] & 0x01 ;
							actData->val.i	=	value ;
							ints[ myMsg->rcvAddr]	=	value ;
							break ;
						case	dtUInt1	:
							value	=	myMsg->mtext[2] & 0xff ;
							actData->val.i	=	value ;
							ints[ myMsg->rcvAddr]	=	(unsigned int) value ;
							break ;
						case	dtFloat2	:
							value	=	hfbtf( &myMsg->mtext[2]) ;
							actData->val.f	=	value ;
							floats[ myMsg->rcvAddr]	=	value ;
							break ;
						default	:
							break ;
						}
						break ;
					}
					break ;
				case	0x02	:		// UCD
					break ;
				case	0x03	:		// NCD
					break ;
				}
			} else if ( myMsg->apn == 0) {
				/**
				 * "message received from INTERNAL sender; not considered;"
				 */
			} else {
				/**
				 * "received an un-known group address [myMsg->rcvAddr|"
				 */
			}
		} else {
			sleepTimer++ ;
			if ( sleepTimer > MAX_SLEEP)
				sleepTimer	=	MAX_SLEEP ;
			sleep( sleepTimer) ;
		}
	}

	/**
	 * destroy shared memory: cross-reference table
	 */
	logit( "detaching cross-reference-table") ;
	shmdt( crf) ;						// detach shared memory of knxBus
	shmctl( shmCRFId, IPC_RMID, NULL) ;	// remove it

	/**
	 * destroy shared memory:
	 */
	logit( "detaching ") ;
	shmdt( floats) ;					// detach shared memory of knxBus
	shmctl( shmKNXId, IPC_RMID, NULL) ;	// remove it

	/**
	 * destroy shared memory:
	 */
	logit( "detaching ") ;
	shmdt( data) ;						// detach shared memory of knxBus
	shmctl( shmOPCId, IPC_RMID, NULL) ;	// remove it

	/**
	 * destroy shared memory: shared-memory-size table
	 */
	logit( "detaching ") ;
	shmdt( sizeTable) ;					// detach shared memory
	shmctl( shmCOMId, IPC_RMID, NULL) ;	// remove it

	/**
	 * close EIB port
	 */
	logit(  "shutting down up ...") ;
	eibClose( myEIB) ;
	exit( status) ;
}

/**
 *
 */
int		restoreFromDb( node *data, int *crf, int *ints, float *floats) {
	int		status	=	0 ;
	int		queryResult ;
	int		numFields ;
	int		groupAddress ;
	node	*actData ;
	MYSQL	*mySql ;
	MYSQL_RES	*mySqlResult ;
	MYSQL_ROW	mySqlRow ;
	char		buf[16] ;
	char		msgBuffer[128] ;
	char		sysCmd[256] ;

	/**
	 *
	 */
	mySql	=	mysql_init( NULL) ;
	if ( mySql == NULL) {
		status	=	-1 ;
		logit( "failure on mysql_init()") ;
	}

	/**
	 *
	 */
	if ( status == 0) {
		mySql	=	mysql_real_connect( mySql, dbHost, dbUser, dbPassword, dbName, 0, NULL, 0) ;
		if ( mySql == NULL) {
			status	=	-1 ;
			logit( "failure on mysql_real_connect()") ;
		}
	}

	/**
	 *
	 */
	if ( status == 0) {
		queryResult	=	mysql_query( mySql, "SELECT GroupObject, Value FROM objectValue WHERE GroupObject <> ''") ;
		system( sysCmd) ;
		if ( queryResult != 0) {
			status	=	-1 ;
			logit( "failure on mysql_query()") ;
		}
	}

	/**
	 *
	 */
	if ( status == 0) {
		mySqlResult	=	mysql_store_result( mySql) ;
		if ( mySqlResult == NULL) {
			status	=	-1 ;
			logit( "failure on mysql_store_result()") ;
		}
	}

	/**
	 *
	 */
	if ( status == 0) {
		numFields	=	mysql_num_fields( mySqlResult) ;
		while ( ( mySqlRow = mysql_fetch_row( mySqlResult))) {
			groupAddress	=	eibGroupToInt( mySqlRow[ 0]) ;
			actData	=	&data[ crf[ groupAddress]] ;
			switch ( actData->type) {
			case	dtBit	:
				actData->val.i	=	atoi( mySqlRow[ 1]) ;
				ints[ groupAddress]	=	actData->val.i ;
				break ;
			case	dtUInt1	:
				actData->val.i	=	atoi( mySqlRow[ 1]) ;
				ints[ groupAddress]	=	(unsigned int) actData->val.i ;
				break ;
			case	dtFloat2	:
				actData->val.f	=	atof( mySqlRow[ 1]) ;
				floats[ groupAddress]	=	actData->val.f ;
				break ;
			default	:
				break ;
			}
		}
	}

	/**
	 *
	 */
	return status ;
}

void	logit( char *_fmt, ...) {
	va_list	arglist ;
	va_start( arglist, _fmt );
	openlog( NULL, LOG_PID|LOG_CONS, LOG_USER);
	syslog( LOG_INFO, _fmt, arglist) ;
	va_end( arglist ) ;
	closelog();
//	vprintf( _fmt, arglist) ;
}

void	help() {
}
