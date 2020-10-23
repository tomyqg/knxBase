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
 * hdllogic.c
 *
 * EIB/KNX Logic Handler
 *
 * this module receives messages and sends messages as reply to certain logic
 * rules.
 *
 * Revision history
 *
 * Date		Rev.		Who		what
 * -----------------------------------------------------------------------------
 * 2016-01-13	PA1		khw		inception;
 * 2016-04-07	PA2		khw		included logging to MySQL database;
 * 2018-06-22	PA5		khw		added unistd.h include;
 * 2018-08-12	PA6		k9w		adapted to systemd;
 * 2019-05-11	PA7		khw		adapted to new structure of group addresses;
 *
 */
#include	<stdio.h>
#include	<stdarg.h>
#include	<string.h>
#include	<strings.h>
#include	<unistd.h>
#include	<stdlib.h>
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
#include	"knxprot.h"
#include	"inilib.h"

#include	"mylib.h"

#define	MAX_SLEEP	2

extern	void	help() ;
extern	void	logit( char *_fmt, ...) ;

		char	progName[64] ;
		pid_t	ownPID ;
		int		runLevel ;
		int		debugLevel ;

		void		hdlMsg( eibHdl *, knxMsg *) ;
		void		hdlFunc( eibHdl *, knxMsg *) ;
		void		_prn( knxMsg *) ;

		char		progName[64] ;

extern	void		cbOpenP2P( eibHdl *, knxMsg *) ;
extern	void		cbCloseP2P( eibHdl *, knxMsg *) ;
extern	void		cbConfP2P( eibHdl *, knxMsg *) ;
extern	void		cbRejectP2P( eibHdl *, knxMsg *) ;
extern	void		cbGroupValueRead( eibHdl *, knxMsg *) ;
extern	void		cbGroupValueResponse( eibHdl *, knxMsg *) ;
extern	void		cbGroupValueWrite( eibHdl *, knxMsg *) ;
extern	void		cbIndividualAddrWrite( eibHdl *, knxMsg *) ;
extern	void		cbIndividualAddrRequest( eibHdl *, knxMsg *) ;
extern	void		cbIndividualAddrResponse( eibHdl *, knxMsg *) ;
extern	void		cbAdcRead( eibHdl *, knxMsg *) ;
extern	void		cbAdcResponse( eibHdl *, knxMsg *) ;
extern	void		cbMemoryRead( eibHdl *, knxMsg *) ;
extern	void		cbMemoryResponse( eibHdl *, knxMsg *) ;
extern	void		cbMemoryWrite( eibHdl *, knxMsg *) ;
extern	void		cbUserMessage( eibHdl *, knxMsg *) ;
extern	void		cbMaskVersionRead( eibHdl *, knxMsg *) ;
extern	void		cbMaskVersionResponse( eibHdl *, knxMsg *) ;
extern	void		cbRestart( eibHdl *, knxMsg *) ;

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
	}	modeTrace ;

int	ownAddr		=	0xfff0 ;
int	progMode	=	1 ;
int	connectedTo	=	0 ;
int	myTraceMode	=	KNX_TRC_DISAS ;
char	myTraceFileName[128] ;

/**
 *
 */
int	cfgQueueKey	=	10031 ;
int	cfgSenderAddr	=	1 ;
int	cfgDaemon		=	1 ;		// default:	run as daemon process

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
void	iniCallback( char *_block, char *_para, char *_value) {
	_debug( 1, progName, "receive ini value block/paramater/value ... : %s/%s/%s\n", _block, _para, _value) ;
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		}
	} else if ( strcmp( _block, "[knxLogicSim]") == 0) {
//		if ( strcmp( _para , "para1") == 0) {
//		} else if ( strcmp( _para,"para2") == 0) {
//			strcpy( dbHost, _value) ;
//		}
	}
}

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
	unsigned	int     control ;
	unsigned	int     addressType ;
	unsigned	int     routingCount ;
		int		startupDelay	=	0 ;	// default 0 seconds startup delay
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
	 *
	 */
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
					hdlMsg( myEIB, myMsg) ;
					break ;
				case	eibAckFrame	:
//					hdlMsg( myEIB, myMsg) ;
					break ;
				case	eibNackFrame	:
//					hdlMsg( myEIB, myMsg) ;
					break ;
				case	eibBusyFrame	:
//					hdlMsg( myEIB, myMsg) ;
					break ;
				case	eibPollDataFrame	:
//					hdlMsg( myEIB, myMsg) ;
					break ;
				case	eibOtherFrame	:
//					hdlMsg( myEIB, myMsg) ;
					break ;
				case	eibDataConfirmFrame	:
//					hdlMsg( myEIB, myMsg) ;
					break ;
				case	eibTPUARTStateIndication	:
//					hdlMsg( myEIB, myMsg) ;
					break ;
				case	bridgeCtrlFrame	:
//					hdlMsg( myEIB, myMsg) ;
					break ;
				default	:
					hdlMsg( myEIB, myMsg) ;
					break ;
				}
			} else {
				_debug( 1, progName, "message received from INTERNAL sender; not considered;") ;
			}
		} else {
			_debug( 1, progName, "msg ptr is NULL") ;
		}
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
 *
 */
void	_prn( knxMsg *myMsg) {
	int	il0 ;
	printf( "Message ....... : \n") ;
	printf( "  From ........ : %5d\n", myMsg->sndAddr) ;
	printf( "  To .......... : %5d\n", myMsg->rcvAddr) ;
	printf( "  Priority .... : %2d\n", myMsg->prio) ;
	switch ( myMsg->tlc) {
	case	0x00	:		// UDP
	case	0x01	:		// NDP
		switch ( myMsg->tlc) {
		case	0x00	:		// UDP
			printf( "  N_PDU Type...: UDP\n") ;
			break ;
		case	0x01	:		// NDP
			printf( "  N_PDU Type...: NDP\n") ;
			printf( "  Sequence no..: %d\n", myMsg->seqNo) ;
			break ;
		}
		switch ( myMsg->apci) {
		case	0x00	:	// GroupValueRead
			printf( "GroupValueRead\n") ;
			break ;
		case	0x01	:	// GroupValueResponse
			printf( "GroupValueResponse\n") ;
			break ;
		case	0x02	:	// GroupValueWrite
			printf( "GroupValueWrite\n") ;
			for ( il0=0 ; il0<16 ; il0++) {
				printf( "%02x ", myMsg->mtext[ il0]) ;
			}
			break ;
		case	0x03	:	// IndividualAddressWrite
			printf( "IndividualAddrWrite\n") ;
			break ;
		case	0x04	:	// IndividualAddressRequest
			printf( "IndividualAddrRequest\n") ;
			break ;
		case	0x05	:	// IndividualAddressResponse
			printf( "IndividualAddrResponse\n") ;
			break ;
		case	0x06	:	// AdcRead
			printf( "AdcRead\n") ;
			break ;
		case	0x07	:	// AdcResponse
			printf( "AdcResponse\n") ;
			break ;
		case	0x08	:	// MemoryRead
			printf( "MemoryRead\n") ;
			break ;
		case	0x09	:	// MemoryResponse
			printf( "MemoryResponse\n") ;
			break ;
		case	0x0a	:	// MemoryWrite
			printf( "MemoryWrite\n") ;
			break ;
		case	0x0b	:	// UserMessage
			printf( "UserMessage\n") ;
			break ;
		case	0x0c	:	// MaskVersionRead
			printf( "MaskVersionread\n") ;
			break ;
		case	0x0d	:	// MaskVersionResponse
			printf( "MaskVersionResponse\n") ;
			break ;
		case	0x0e	:	// Restart
			printf( "Restart\n") ;
			break ;
		case	0x0f	:	// Escape
			printf( "Escape\n") ;
			break ;
		}
		break ;
	case	0x02	:		// UCD
	case	0x03	:		// NCD
		switch ( myMsg->ppCmd) {
		case	0x00	:
			printf( "  Command ..... : open P2P connection\n") ;
			break ;
		case	0x01	:
			printf( "  Command ..... : terminate P2P connection\n") ;
			break ;
		case	0x02	:
			printf( "  Confirm ..... : positiv\n") ;
			break ;
		case	0x03	:
			printf( "  Reject ...... : negativ\n") ;
			break ;
		}
	}
}
void	hdlMsg( eibHdl *_myEIB, knxMsg *myMsg) {
	_debug( 1, progName, "hdlMsg;") ;
	hdlFunc( _myEIB, myMsg) ;
}

/**
 *
 */
void	hdlFunc( eibHdl *_myEIB, knxMsg *myMsg) {
	knxMsg	*myReply, myReplyBuf ;
	_debug( 1, progName, "hdlFunc;") ;
	myReply	=	&myReplyBuf ;
	switch ( myMsg->tlc) {
	case	0x00	:		// UDP
	case	0x01	:		// NDP
		switch ( myMsg->apci) {
		case	0x00	:	// GroupValueRead
			break ;
		case	0x01	:	// GroupValueResponse
			break ;
		case	0x02	:	// GroupValueWrite
			_debug( 1, progName, "hdlFunc; 0x02;") ;
			if ( myCB.cbGroupValueWrite != NULL) {
				myCB.cbGroupValueWrite( _myEIB, myMsg) ;
			}
			break ;
		case	0x03	:	// IndividualAddressWrite
			if ( myCB.cbIndividualAddrWrite != NULL) {
				myCB.cbIndividualAddrWrite( _myEIB, myMsg) ;
			}
			break ;
		case	0x04	:	// IndividualAddressRequest
			if ( myCB.cbIndividualAddrRequest != NULL) {
				myCB.cbIndividualAddrRequest( _myEIB, myMsg) ;
			}
			break ;
		case	0x05	:	// IndividualAddressResponse
			if ( myCB.cbIndividualAddrResponse != NULL) {
				myCB.cbIndividualAddrResponse( _myEIB, myMsg) ;
			}
			break ;
		case	0x06	:	// AdcRead
			break ;
		case	0x07	:	// AdcResponse
			break ;
		case	0x08	:	// MemoryRead
			break ;
		case	0x09	:	// MemoryResponse
			break ;
		case	0x0a	:	// MemoryWrite
			break ;
		case	0x0b	:	// UserMessage
			break ;
		case	0x0c	:	// MaskVersionRead
			break ;
		case	0x0d	:	// MaskVersionResponse
			break ;
		case	0x0e	:	// Restart
			break ;
		case	0x0f	:	// Escape
			break ;
		}
		break ;
	case	0x02	:		// UCD
	case	0x03	:		// NCD
		switch ( myMsg->ppCmd) {
		case	0x00	:
			if ( myCB.cbOpenP2P != NULL) {
				myCB.cbOpenP2P( _myEIB, myMsg) ;
			}
			break ;
		case	0x01	:
			if ( myCB.cbCloseP2P != NULL) {
				myCB.cbCloseP2P( _myEIB, myMsg) ;
			}
			break ;
		case	0x02	:
			if ( myCB.cbConfP2P != NULL) {
				myCB.cbConfP2P( _myEIB, myMsg) ;
			}
			break ;
		case	0x03	:
			if ( myCB.cbRejectP2P != NULL) {
				myCB.cbRejectP2P( _myEIB, myMsg) ;
			}
			break ;
		}
		break ;
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
	int     il0 ;
	char	buffer[16] ;
	_debug( 11, progName, "hdlFunc; rcvAddr := %s", eibIntToGroup( myMsg->rcvAddr, buffer)) ;
	if ( myMsg->rcvAddr == eibGroupToInt( "0/2/10")) {		// ALL_SZENE_G00
		if ( debugLevel > 1) {
			_prn( myMsg) ;
			printf( "\n") ;
		}
		_debug( 1, progName, "hdlFunc; rcvAddr := %5d, length := %2d, value := %3d",
 					myMsg->rcvAddr, myMsg->length, myMsg->mtext[ 2]) ;
		switch ( myMsg->mtext[ 2]) {
		case    0x00    :
			logit( "de-blocking motion sensors ... ") ;
			_debug( 1, progName, "de-blocking motion sensors ...") ;
			eibWriteBit( _myEIB, "2/7/221", 0, 1) ;		// un-block motion sensor EG
			sleep( 1) ;
			eibWriteBit( _myEIB, "3/6/221", 0, 1) ;		// un-block motion sensor OG
			break ;
		default   :
			logit( "blocking motion sensors ... ") ;
			_debug( 1, progName, "blocking motion sensors ...") ;
			eibWriteBit( _myEIB, "2/7/221", 1, 1) ;		// block motion sensor EG
			sleep( 1) ;
			eibWriteBit( _myEIB, "3/6/221", 1, 1) ;		// block motion sensor EG
			break ;
		}
//	} else if ( myMsg->rcvAddr == 12800) {
//		eibWriteByte( _myEIB, 5192, 140, 1) ;
//		sleep( 1) ;
//		eibWriteBitIA( _myEIB, 2560, 0, 1) ;
//		sleep( 1) ;
//		eibWriteBitIA( _myEIB, 2600, 0, 1) ;
//	} else if ( myMsg->rcvAddr == 3182) {
//		printf( "message for stair lights\n") ;
	}
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
	printf( "%s: %s [-D <debugLevel>] [-F] [-Q=<queueId>]\n\n", progName, progName) ;
	printf( "Start the Logic Handler on the internal EIB/KNX busn") ;
	printf( "-F force process to run in foreground, do not daemonize\n") ;
}
