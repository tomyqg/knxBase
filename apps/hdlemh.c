/**
 * Copyright (c) 2016, 2017 Karl-Heinz Welter
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
 * hdlemh.c
 *
 * handler for EMH eHZ-H power meter
 *
 * Revision history
 *
 * Date		Rev.	Who	what
 * -----------------------------------------------------------------------------
 * 2016-06-23	PA1	khw	inception;
 *
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<strings.h>
#include	<unistd.h>
#include	<time.h>
#include	<math.h>
#include	<unistd.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/sem.h>
#include	<sys/signal.h>
#include        <mysql.h>

#include	"debug.h"
#include	"knxlog.h"
#include	"tty.h"
#include	"eib.h"
#include	"nodeinfo.h"
#include	"mylib.h"
#include	"hdlemh.h"
#include	"inilib.h"
/**
 *
 */
#define	MAX_SLEEP	1
#define	SLEEP_TIME	1
/**
 *
 */
extern	void	help() ;
/**
 *
 */
char	progName[64]  ;
pid_t	ownPID ;
knxLogHdl	*myKnxLogger ;
int	recLevel	=	0 ;;
/**
 *
 */
void	sigHandler( int _sig) {
	debugLevel	=	-1 ;
}
/**
 *
 */
int	cfgQueueKey	=	10031 ;
int	cfgSenderAddr	=	1 ;
char	cfgSerial[64] ;				// serial port, e.g. /dev/tty00
int	cfgStartupDelay	=	15 ;		// default startup delay 15 seconds
char	dbHost[64]	=	"*" ;
char	dbName[64]	=	"*" ;
char	dbUser[64]	=	"*" ;
char	dbPassword[64]	=	"*" ;
/**
  *
 */
int	timeLast ;
int	timeAct ;
int	timeDiff ;
/**
 *
 */
void	iniCallback( char *_block, char *_para, char *_value) {
	_debug( 1, progName, "receive ini value block/paramater/value ... : %s/%s/%s\n", _block, _para, _value) ;
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		}
	} else if ( strcmp( _block, "[knxtrace]") == 0) {			// will use same db connection as for the tracer logging
		if ( strcmp( _para , "dbHost") == 0) {
			strcpy( dbHost, _value) ;
		} else if ( strcmp( _para, "dbName") == 0) {
			strcpy( dbName, _value) ;
		} else if ( strcmp( _para, "dbUser") == 0) {
			strcpy( dbUser, _value) ;
		} else if ( strcmp( _para, "dbPassword") == 0) {
			strcpy( dbPassword, _value) ;
		}
	} else if ( strcmp( _block, "[hdlemh]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		} else if ( strcmp( _para, "dev") == 0) {
			strcpy( cfgSerial, _value) ;
		} else if ( strcmp( _para, "startupDelay") == 0) {
			cfgStartupDelay	=	atoi( _value) ;
		}
	}
}

/**
 *
 */
MYSQL	*mySql ;
extern	void	mySqlQuery( char *) ;

/**
 *
 */
int	main( int argc, char *argv[]) {
	eibHdl	*myEIB ;
	ttyHdl	*myTTY ;
	int	myAPN	=	0 ;
	knxMsg	*msgToSnd, msgBuf ;
	int		pid ;
	int		il0 ;
	int	sendingByte ;
	int	msgCount ;
	int	opt ;
	int	sleepTimer	=	0 ;
	int	incompleteMsg ;
	int	rcvdLength ;
	int	cntEscBegin, cntEscEnd, cntVersion, cntChecksum ;
	int	versionNeeded ;
	int	addEscPend ;
	int	expLength ;
	emhModeRcv	rcvMode ;
	char	mode[]={'8','n','1',0};
	char	numPrefix[64] ;
	int	bdrate		=	9600 ;	// 9600 baud */
	int	rcvdBytes ;
	unsigned	char	buf ;
	unsigned	char	message[512] ;
	unsigned	char	lastMessage[512], *lmp ;
			int	lastMessageLen ;
	unsigned	char	*bufp ;
	unsigned	char	*rcvData ;
	unsigned	char	*sndData ;
	int	cycleCounter ;
	char	iniFilename[]	=	"knx.ini" ;
	/**
	 * setup the shared memory for EIB Receiving Buffer
	 */
	ownPID	=	getpid() ;
	signal( SIGINT, sigHandler) ;
	setbuf( stdout, NULL) ;
	strcpy( progName, *argv) ;
	_debug( 0, progName, "starting up ...") ;

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
	if (( mySql = mysql_init( NULL)) == NULL) {
		_debug( 0, progName, "could not initialize MySQL access") ;
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
	timeLast	=	0 ;
	timeAct	=	0 ;
	timeDiff	=	0 ;
	myKnxLogger	=	knxLogOpen( IPC_CREAT) ;
	knxLog( myKnxLogger, progName, "%d: starting up ...", ownPID) ;
	knxLog( myKnxLogger, progName, "%d: start up delay %d", ownPID, cfgStartupDelay) ;
	sleep( cfgStartupDelay) ;

	/**
	 *
	 */
	if ( createPIDFile( progName, "", ownPID)) {

		/**
		 * open communication port
		 */
		ttyPrep( cfgSerial, bdrate, mode) ;
		if (( myTTY = ttyOpen( cfgSerial, bdrate, mode)) == NULL) {
			_debug( 1, progName, "Can not open tty");
			return( 0);
		}
		myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_RDWR) ;
		myAPN	=	myEIB->apn ;
		printf( "myAPN ..... %d \n", myEIB->apn) ;
		knxLog( myKnxLogger, progName, "%d: myAPN := %d", ownPID, myEIB->apn) ;

		/**
		 *
		 */
		cycleCounter	=	0 ;
		knxLog( myKnxLogger, progName, "%d: running on MacOS", ownPID) ;
		rcvMode	=	emhWaitBegin ;
		cntEscBegin	=	0 ;		// counter for 0x1b in the begin message
		lmp	=	NULL ;
		_debug( 1, progName, "starting .... : emhWaitBegin");
		while ( debugLevel >= 0) {
//			_debug( 1, progName, "cycleCounter := %d", cycleCounter++) ;
			rcvdBytes	=	ttyPoll( myTTY, (unsigned char *) &buf, 1) ;
			if ( rcvdBytes > 0) {
				_debug( 11, progName, "character ... : %02x", buf);
				switch ( rcvMode) {
				case	emhWaitBegin	:	// waiting for: 0x1b 0x1b 0x1b 0x1b
					if ( buf == 0x1b) {
						cntEscBegin++ ;
						if ( cntEscBegin >= 4) {
							rcvMode	=	emhWaitVersion ;
							cntVersion	=	0 ;
							_debug( 1, progName, "entering .... : emhWaitVersion");
						}
					} else {
					}
					break ;
				case	emhWaitVersion	:	// waiting for: 0x01 0x01 0x01 0x01 (or: 4 x 0x02)
					if ( cntVersion == 0) {
						if ( buf >= 0x01 && buf <= 0x02) {
							versionNeeded	=	buf ;
							cntVersion++ ;
						} else {
							rcvMode	=	emhWaitBegin ;
							cntEscBegin	=	0 ;
							_debug( 1, progName, "entering .... : emhWaitBegin");
						}
					} else if ( buf == versionNeeded) {
						cntVersion++ ;
						if ( cntVersion >= 4) {
							rcvMode	=	emhWaitEnd ;
							cntEscEnd	=	0 ;
							bufp	=	message ;
							_debug( 1, progName, "entering .... : emhWaitEnd");
						}
					} else {
						rcvMode	=	emhWaitBegin ;
						cntEscBegin	=	0 ;
							_debug( 1, progName, "entering .... : emhWaitBegin");
					}
					break ;
				case	emhWaitEnd	:
					if ( buf == 0x1b) {
						cntEscEnd++ ;
						if ( cntEscEnd >= 4) {
							rcvMode	=	emhWaitChecksum ;
							cntChecksum	=	0 ;
							lastMessageLen	=	(int) ( bufp - message) ;
							memcpy( lastMessage, message, lastMessageLen) ;
							lmp	=	lastMessage ;
							_debug( 1, progName, "received .... : %3d octets", lastMessageLen);
							_debug( 1, progName, "entering .... : emhWaitChecksum");
for ( il0=0 ; il0<lastMessageLen ; il0++) {
	printf( "%02x ", *lmp++) ;
}
printf( "\n") ;
							lmp	=	lastMessage ;
							msgCount	=	0 ;
						}
					} else {
						for ( addEscPend=0 ; addEscPend < cntEscEnd ; addEscPend++)
							*bufp++	=	0x1b ;
						*bufp++	=	buf ;
						cntEscEnd	=	0 ;
					}
					break ;
				case	emhWaitChecksum	:
					switch ( cntChecksum) {
					case	0	:		// expecting 0x1a = Ende einer Nachricht
						if ( buf == 0x1a) {
						} else {
							rcvMode	=	emhWaitBegin ;
							cntEscBegin	=	0 ;
							_debug( 1, progName, "entering .... : emhWaitBegin (pre-maturely)");
						}
						break ;
					case	1	:		// receive number of filler bytes
						break ;
					case	2	:		// checksum
						break ;
					case	3	:		// checksum
						rcvMode	=	emhWaitBegin ;
						cntEscBegin	=	0 ;
						_debug( 1, progName, "entering .... : emhWaitBegin");
						break ;
					}
					cntChecksum++ ;
					break ;
				case	emhWaitTerm	:	// waiting for: 0x1b 0x1b 0x1b 0x1b
					break ;
				}
			} else {
				if ( lmp != NULL && lmp < ( lastMessage + lastMessageLen)) {
					sprintf( numPrefix, "%d", ++msgCount) ;
					lmp	=	analyze( lmp, ( lastMessage + lastMessageLen), numPrefix, "") ;
				} else if ( lmp != NULL) {
//					printf( "\n") ;
					lmp	=	NULL ;
					_debug( 1, progName, "finished evaluation of last message") ;
				}
			}
		}
		ttyClose( myTTY) ;
		eibClose( myEIB) ;
		deletePIDFile( progName, "", ownPID) ;
	} else {
		knxLogRelease( myKnxLogger) ;
		knxLog( myKnxLogger, progName, "%d: process already running ...", ownPID) ;
		_debug( 0, progName, "process already running ...") ;
	}
	knxLog( myKnxLogger, progName, "%d: shutting down ...", ownPID) ;

	/**
	 * close virtual EIB bus
	 * close KNX Level logger
	 */
	knxLogClose( myKnxLogger) ;

	/**
	 *
	 */
	exit( 0) ;
}

unsigned	char	*analyze( unsigned char *_mp, unsigned char *_end, const char *_numPrefix, const char *_prefix) {
	struct		tm	tm ;
	unsigned	char	*lmp, *lbp ;
			int	len ;
			int	lenCnt ;
			int	done ;
			int	lenOffs ;
			int	addLenOctets ;
			int	locCnt ;
			int	value ;
			char	prefix[64] ;
			char	numPrefix[64] ;
			char	sql[256] ;
			int	updatedRows ;
	MYSQL_RES		*result ;
	MYSQL_ROW		row ;
	/**
	 *
	 */
	sprintf( prefix, "   %s", _prefix) ;
	recLevel++ ;
	lmp	=	_mp ;			// get the message pointer in a more "handy" form
	done	=	0 ;
	lenOffs	=	0 ;
	addLenOctets	=	0 ;
	while ( lmp < _end && ! done) {
		printf( "%-8s: %s%02x (lenOffs := %d)", _numPrefix, _prefix, *lmp, lenOffs) ;
		len	=	((int) ( *lmp & 0x0f)) + lenOffs ;
		lenOffs	=	0 ;
		/**
		 * which datatype do we have ...
		 */
		switch ( *lmp++ & 0xf0) {
		case	0x00	:			// datatype:	octet string
			printf( "         dataType ... : octet string, length ... : %d\n%s", len, prefix) ;
			lbp	=	lmp + len - 1 - addLenOctets ;
			locCnt	=	0 ;
			while ( lmp < lbp) {
				printf( "%02x ", *lmp++) ;
				locCnt++ ;
				if ( ( locCnt % 8) == 0)
					printf( "\n%s", prefix) ;
			}
			printf( "\n") ;
			done	=	1 ;
			break ;
		case	0x10	:			// datatype:	future usage
			break ;
		case	0x20	:			// datatype:	future usage
			break ;
		case	0x30	:			// datatype:	future usage
			break ;
		case	0x40	:			// datatype:	boolean
			printf( "         dataType ... : boolean, length ... : %d\n%s", len, prefix) ;
			lbp	=	lmp + len - 1 ;
			while ( lmp < lbp) {
				printf( "%02x ", *lmp++) ;
			}
			printf( "\n") ;
			done	=	1 ;
			break ;
		case	0x50	:			// datatype:	integer
			switch ( len) {
			case	2	:
				printf( "         dataType ... : integer8, length ... : %d\n%s", len, prefix) ;
				lbp	=	lmp + len - 1 ;
				value	=	0 ;
				while ( lmp < lbp) {
					value	=	(char) *lmp ;
					printf( "%02x ", *lmp++) ;
				}
				printf( " => %dd\n", value) ;
				done	=	1 ;
				break ;
			default	:
				printf( "         dataType ... : integer, length ... : %d\n%s", len, prefix) ;
				lbp	=	lmp + len - 1 ;
				value	=	0 ;
				while ( lmp < lbp) {
					value	=	((value << 8) + *lmp) ;
					printf( "%02x ", *lmp++) ;
				}
				printf( " => %dd\n", value) ;
				done	=	1 ;
				break ;
			}
			if ( strcmp( _numPrefix, "2.4.2.5.3.6") == 0) {
printf(" ****************************** : %d\n", timeDiff) ;
				if ( timeDiff >= 10) {				// log every 10 seconds
					timeLast	=	timeAct ;
					/**
					 * add the logging record
					 */
					sprintf( sql, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%d');",
							10496,
							29,
							value
						) ;
					mySqlQuery( sql) ;
				}
			}
			break ;
		case	0x60	:			// datatype:	unsigned
			printf( "         dataType ... : unsigned, length ... : %d\n%s", len, prefix) ;
			lbp	=	lmp + len - 1 ;
			value	=	0 ;
			while ( lmp < lbp) {
				value	=	(value << 8) + *lmp ;
				printf( "%02x ", *lmp++) ;
			}
			printf( " => %dd\n", value) ;
			done	=	1 ;
			if ( strcmp( _numPrefix, "2.4.2.4.2") == 0) {
				timeAct	=	value ;
				timeDiff	=	timeAct - timeLast ;
printf(" ############################## %d : %d\n", timeAct, timeDiff) ;
			}
			break ;
		case	0x70	:			// datatype:	listOf
			printf( "         dataType ... : listOf, length ... : %d\n", len) ;
			for ( lenCnt = 0 ; lenCnt < len ; lenCnt++) {
				sprintf( numPrefix, "%s.%d", _numPrefix, lenCnt+1) ;
				lmp	=	analyze( lmp, _end, numPrefix, prefix) ;
			}
			done	=	1 ;
			break ;
		case	0x80	:			// datatype:	extended TL-field
//			_debug( 1, progName, "dataType ... : extended TL-field, here: len := %d", len * 16) ;
			addLenOctets++ ;
			lenOffs	=	len * 16 ;
			break ;
		case	0x90	:			// datatype:	--- INVALID ---
			break ;
		case	0xa0	:			// datatype:	--- INVALID ---
			break ;
		case	0xb0	:			// datatype:	--- INVALID ---
			break ;
		case	0xc0	:			// datatype:	--- RESERVED ---
			break ;
		case	0xd0	:			// datatype:	--- INVALID ---
			break ;
		case	0xe0	:			// datatype:	--- INVALID ---
			break ;
		case	0xf0	:			// datatype:	--- INVALID ---
			break ;
		}
	}
//	printf( "%02d===================================%08lx %08lx \n", recLevel, lmp, _end) ;
	recLevel-- ;
	return( lmp) ;			// return the more handy message pointer to the un-handy form
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
			sleep( retryCount * 10) ;
		}
		if ( ( sqlResult = mysql_query( mySql, _sql))) {
			knxLog( myKnxLogger, progName, "mySQL: error during query ...") ;
			do {
				if ( ( connectResult = mysql_real_connect( mySql, dbHost, dbUser, dbPassword, dbName, 0, NULL, 0)) == NULL) {
					knxLog( myKnxLogger, progName, "mySQL: reconnecting ...") ;
					reconnectCount++ ;
					sleep( reconnectCount * 2) ;
				}
			} while ( reconnectCount < 10 && connectResult == NULL) ;
			if ( reconnectCount >= 10) {
				knxLog( myKnxLogger, progName, "mySQL: reconnect count exceeded;") ;
			}
		} else {
			result  =       mysql_store_result( mySql) ;
			mysql_free_result( result) ;
		}
	} while ( retryCount < 10 && sqlResult) ;
	if ( retryCount >= 10) {
		knxLog( myKnxLogger, progName, "mySQL: retry count exceeded; terminating ...") ;
		exit( -3) ;
	}
}

void	help() {
	printf( "%s: %s [-D <debugLevel>] [-Q=<queueIdf>] [-M] [-S] \n\n", progName, progName) ;
	printf( "Start a TPUART<->SimEIB/KNX bridge with id queueId.\n") ;
}
