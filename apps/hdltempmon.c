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
 * hdltempmon.c
 *
 * handle temperature monitor, based on 1-wire temperature sensors
 *
 * Revision history
 *
 * date		rev.	who	what
 * ----------------------------------------------------------------------------
 * 2017-03-19	PA1	khw	inception;
 *
 */
#include	<stdio.h>
#include	<string.h>
#include	<strings.h>
#include	<unistd.h>
#include	<stdlib.h>
#include	<dirent.h>
#include	<time.h>
#include	<dirent.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<mysql.h>

#include	"eib.h"
#include	"debug.h"
#include	"nodeinfo.h"
#include	"mylib.h"
#include	"knxlog.h"
#include	"inilib.h"

extern	void	help() ;

char	progName[64] ;
int	debugLevel	=	0 ;
knxLogHdl	*myKnxLogger ;

/**
 *
 */
int	cfgQueueKey	=	10031 ;
int	cfgSenderAddr	=	1 ;
char	cfgChanTemp[16][128] ;
int	cfgStartupDelay	=	5 ;		// 120 

#define	MAX_SLEEP	2

extern	void	help() ;

	char	progName[64] ;
	knxLogHdl	*myKnxLogger ;

int	ownAddr		=	0xfff0 ;
int	progMode	=	1 ;
int	connectedTo	=	0 ;
char	dbHost[64]	=	"*" ;
char	dbName[64]	=	"*" ;
char	dbUser[64]	=	"*" ;
char	dbPassword[64]	=	"*" ;

/**
 *
 */
void	sigHandler( int _sig) {
	debugLevel	=	-1 ;
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
		}
	} else if ( strcmp( _block, "[hdltempmon]") == 0) {
		if ( strcmp( _para, "startupDelay") == 0) {
			cfgStartupDelay	=	atoi( _value) ;
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
		int	startupDelay	=	120 ;
		int	groupId ;
		char	filename[64] ;
	char	iniFilename[]	=	"knx.ini" ;
	char	tempBuffer[64] ;
	char	*tBP ;
	char	sql[256] ;
	DIR *d;
	struct dirent *dir;
 

	/**
	 *
	 */
//	setbuf( stdout, NULL) ;				// disable output buffering on stdout
	strcpy( progName, *argv) ;
	myKnxLogger	=	knxLogOpen( 0) ;
	knxLog( myKnxLogger, progName, "starting up ...") ;
	sleep( cfgStartupDelay) ;
	/**
	 *
	 */
	knxLog( myKnxLogger, progName, "loading initialization from file ...") ;
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 * get command line options
	 */
	knxLog( myKnxLogger, progName, "evaluating options ...") ;
	while (( opt = getopt( argc, argv, "D:Q:f:m:?")) != -1) {
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
#include	"_shmblock.c"
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

	/**
	 *
	 */
	knxLog( myKnxLogger, progName, "starting loop ...") ;
	while ( debugLevel >= 0) {
		_debug( 1, progName, "cycleCounter := %d", cycleCounter++) ;

		/**
		 *
		 */
		d = opendir("/mnt/1wire");
		if (d) {
			groupId	=	60001 ;
			while ((dir = readdir( d)) != NULL) {
				if ( strncmp( dir->d_name, "28.FF", 5) == 0) {
					strcpy( filename, "/mnt/1wire/") ;
					strcat( filename, dir->d_name) ;
					strcat( filename, "/") ;
					strcat( filename, "temperature") ;
					if ( ( file = fopen( filename, "r")) != NULL) {
						tBP	=	fgets( tempBuffer, 7, file) ;
						tBP[7]	=	'\0' ;
						fclose( file) ;
						printf( "Temperature ... : %s\n", tBP) ;

						/**
						 * add the logging record
						 */
						sprintf( sql, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%s');",
								groupId,
								21,
								tBP
							) ;
						mySqlQuery( sql) ;
					}
					groupId++ ;
				}
			}
			closedir(d);
		}

//		sleep( 60) ;
		sleep( 5) ;
	}
	knxLog( myKnxLogger, progName, "shutting down ...") ;
	eibClose( myEIB) ;
	knxLogClose( myKnxLogger) ;
	exit( status) ;
}

void	mySqlQuery( char *_sql) {
	MYSQL_RES	*result ;
	int	sqlResult	=	0 ;
	int	retryCount	=	0 ;
	int	reconnectCount	=	0 ;
	MYSQL	*connectResult ;
	do {
		if ( retryCount > 0) {
			knxLog( myKnxLogger, progName, "mySQL: retrying ...") ;
		}
		if ( ( sqlResult = mysql_query( mySql, _sql))) {
			knxLog( myKnxLogger, progName, "mySQL: error during query ...") ;
			do {
				if ( ( connectResult = mysql_real_connect( mySql, dbHost, dbUser, dbPassword, dbName, 0, NULL, 0)) == NULL) {
					knxLog( myKnxLogger, progName, "mySQL: reconnecting ...") ;
					reconnectCount++ ;
					sleep( reconnectCount) ;
				}
			} while ( reconnectCount < 10 && connectResult == NULL) ;
			if ( reconnectCount >= 10) {
				knxLog( myKnxLogger, progName, "mySQL: reconnect count exceeded; terminating ...") ;
				_debug( 0, progName, "mysql error := '%s'", mysql_error( mySql)) ;
				_debug( 0, progName, "Exiting with -2");
			}
		} else {
			result  =       mysql_store_result( mySql) ;
			mysql_free_result( result) ;
		}
	} while ( retryCount < 10 && sqlResult) ;
	if ( retryCount >= 10) {
		knxLog( myKnxLogger, progName, "mySQL: retry count exceeded; terminating ...") ;
		_debug( 0, progName, "mysql error := '%s'", mysql_error( mySql)) ;
		_debug( 0, progName, "Exiting with -3");
		exit( -3) ;
	}
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
