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
/*
 *
 * knxmqtt.c
 *
 * EIB/KNX bus to MQTT Broker bridge
 *
 * this module serves as bridge between the EIB/KNXbus and an MQTT broker
 * knxmon needs to be running in order to obatin information about the data types for the group messages
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
 * 2018-08-10	PA1		khw	inception; splitted off from the knxtrace.c, which
 *							hosted the functionality so far as '-m 6';
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
#include	<sys/signal.h>
#include	<sys/msg.h>
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
/**
 *
 */
char	progName[64]  ;
pid_t	ownPID ;
int		runLevel ;

/**
 *
 */

		void	hdlMsg( eibHdl *, knxMsg *) ;
extern	void	logDisas( eibHdl *, knxMsg *) ;
extern	void	logBin( eibHdl *, knxMsg *) ;
extern	void	logHex( eibHdl *, knxMsg *) ;
extern	void	logCtrl( eibHdl *, knxMsg *, char *txt) ;
extern	void	logDb( eibHdl *, knxMsg *, int *, node *) ;
extern	void	logMqtt( eibHdl *, knxMsg *, int *, node *) ;

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

int	ownAddr		=	0xfff0 ;
int	progMode	=	1 ;
int	connectedTo	=	0 ;

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
int	cfgQueueKey	=	10031 ;
int	cfgSenderAddr	=	1 ;
int	cfgDaemon	=	1 ;				// default:	run as daemon process

/**
 *
 */
void	iniCallback( char *_block, char *_para, char *_value) {
//_debug( 1, progName, "receive ini value block/paramater/value ... : %s/%s/%s\n", _block, _para, _value) ;
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		}
	} else if ( strcmp( _block, "[knxmqttbridge]") == 0) {
	_debug( 1, progName, "receive ini value block/paramater/value ... : %s/%s/%s\n", _block, _para, _value) ;
		if ( strcmp( _para, "host") == 0) {
			strcpy( cfgMqttHost, _value) ;
		} else if ( strcmp( _para, "user") == 0) {
			strcpy( cfgMqttUser, _value) ;
		} else if ( strcmp( _para, "passwd") == 0) {
			strcpy( cfgMqttPasswd, _value) ;
		} else if ( strcmp( _para, "caCert") == 0) {
			strcpy( cfgMqttCACert, _value) ;
		} else if ( strcmp( _para, "key") == 0) {
			strcpy( cfgMqttKey, _value) ;
		} else if ( strcmp( _para, "cert") == 0) {
			strcpy( cfgMqttCert, _value) ;
		} else if ( strcmp( _para, "prefix") == 0) {
			strcpy( cfgMqttPrefix, _value) ;
		} else if ( strcmp( _para, "filter") == 0) {
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
 *
 */
int	main( int argc, char *argv[]) {
		eibHdl	*myEIB ;
		int	opt ;
		int	status		=	0 ;
		int	sleepTimer	=	0 ;
		int	i ;
	pid_t	pid, sid ;
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
	signal( SIGHUP, sigHandler) ;
	setbuf( stdout, NULL) ;
	strcpy( progName, *argv) ;
	logit(  "starting up ...") ;

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
		case	'F'	:
			cfgDaemon	=	false ;
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
	 * daemonize this process, if so required
	 */
	if ( cfgDaemon) {
		logit( "daemon mode ...", progName, ownPID) ;
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

	/**
	 *
	 */
	myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_RD) ;
	sleepTimer	=	0 ;
	cycleCounter	=	0 ;
	while ( runLevel >= 0) {
		myMsg	=	eibReceiveMsg( myEIB, &myMsgBuf) ;
		if ( myMsg != NULL) {
			if ( myMsg->apn != 0) {
				switch ( myMsg->frameType) {
				case	eibDataFrame	:
					sleepTimer	=	0 ;
					eibDisect( myMsg) ;
					logMqtt( myEIB, myMsg, crf, data) ;
					break ;
				case	eibAckFrame	:
					break ;
				case	eibNackFrame	:
					break ;
				case	eibBusyFrame	:
					break ;
				case	eibPollDataFrame	:
					break ;
				case	eibOtherFrame	:
					break ;
				case	eibDataConfirmFrame	:
					break ;
				case	eibTPUARTStateIndication	:
					break ;
				case	bridgeCtrlFrame	:
					break ;
				default	:
					break ;
				}
			} else {
				/**
				 * message received from INTERNAL sender; not considered
				 */
			}
		} else {
		}
	}
	logit(  "shutting down ...") ;
	eibClose( myEIB) ;
	exit( status) ;
}

#include	"_knxfsm.c"
#include	"_disasMsg.c"

/**
 *
 */
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
	printf( "%s: %s [-D <debugLevel>] [-F] [-Q=<queueIdf>] \n\n", progName, progName) ;
	printf( "MQTT Bridge for EIB/KNX bus") ;
}
