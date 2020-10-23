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
 * knxtpbridge.c
 *
 * KNX TP (Twisted Pair) bridge process
 *
 * knxbridge bridges a "simulated" knx-bus to a "real-world" knx-bus
 * through a TPUART or, in
 * a purely simulated mode, e.g. on Mac OS, acts as a virtual TPUART which copies
 * everything supposed to be transmitted to the real-orld to the incoming side.
 * the communication towards other modules happens through two separate message queue.
 * the message queues contain a complete message (see: eib.h)
 *
 * Revision history
 *
 * Date			Rev.	Who	what
 * -----------------------------------------------------------------------------
 * 2015-11-20	PA1		khw	inception;
 * 2015-11-24	PA2		khw	added semaphores for shared memory
 *							and message queue;
 * 2015-11-26	PA3		khw	added mylib function for half-float
 *							conversions;
 * 2015-12-03	PA4		khw	derived from knx R1;
 * 2015-12-17	PA5		khw	added semaphores for send- and receive-queue;
 * 2016-01-26	PA6		khw	renamed to knxtpbridge;
 *							added apn as routing decision;
 * 2016-05-13	PA7		khw	added forking towards receiving fork and
 *							sending fork;
 * 2018-06-22	PA8		khw	fault fixes, minor enhancements, removed debugging
 *							and general printing;
 * 2018-07-29	PA9		khw	modified logging towards system logger;
 *
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<stdarg.h>
#include	<strings.h>
#include	<unistd.h>
#include	<time.h>
#include	<math.h>
#include	<unistd.h>
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/sem.h>
#include	<sys/signal.h>

#include	"tty.h"
#include	"eib.h"
#include	"nodeinfo.h"
#include	"mylib.h"
#include	"knxtpbridge.h"
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
char	cfgSerial[64] ;				// serial port, e.g. /dev/tty00

/**
 *
 */
void	iniCallback( char *_block, char *_para, char *_value) {
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		}
	} else if ( strcmp( _block, "[knxtpbridge]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		} else if ( strcmp( _para, "serial") == 0) {
			strcpy( cfgSerial, _value) ;
		}
	}
}

/**
 *
 */
int	main( int argc, char *argv[]) {
	pid_t	ownPID ;
	pid_t	childProcessPid, parentProcessPid ;
	eibHdl	*myEIB ;
	ttyHdl	*myTTY ;
	int	myAPN	=	0 ;
	knxMsg	*msgToSnd, msgBuf ;
	knxMsg	msgRcv ;
	knxMsg	lastSentMsg ;
	int		pid ;
	int	sendingByte ;
	int	opt ;
	int	sleepTimer	=	0 ;
	int	incompleteMsg ;
	int	rcvdLength ;
	int	sentLength ;
	int	expLength ;
	bridgeModeRcv	rcvMode ;
	bridgeModeSnd	sndMode ;
	char	mode[]={'8','e','1',0};
	char	sndCompareBuffer[64] ;
	int	sndBufferCount ;
	char	rcvCompareBuffer[64] ;
	int	rcvBufferCount ;
	int	bdrate		=	19200 ;	// 9600 baud */
	int	rcvdBytes ;
	unsigned	char	buf, bufp ;
	unsigned	char	*rcvData ;
	unsigned	char	*sndData ;
	int	cycleCounter ;
	int	sndConfirmed	=	0 ;
	int	sending	=	0 ;
	int	sndTimeout	=	0 ;
	char	iniFilename[]	=	"knx.ini" ;

	/**
	 * setup the shared memory for EIB Receiving Buffer
	 */
	ownPID	=	getpid() ;
	strcpy( progName, *argv) ;

	/**
	 *
	 */
	runLevel	=	1 ;
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 *
	 */
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 * get command line options
	 */
	while (( opt = getopt( argc, argv, "MQ:S?")) != -1) {
		switch ( opt) {
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
	logit(  "starting up ...") ;
	if ( createPIDFile( progName, "", ownPID)) {

		/**
		 * open communication port
		 */
		ttyPrep( cfgSerial, bdrate, mode) ;
		if (( myTTY = ttyOpen(  cfgSerial, bdrate, mode)) == NULL) {
			return(0);
		}
		ttySendByte( myTTY, 0x01);
		rcvdBytes	=	ttyPoll( myTTY, (unsigned char *) &buf, 1) ;

		/**
		 *
		 */
		myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_RDWR) ;
		myAPN	=	myEIB->apn ;
#ifdef	__MACH__
		printf( "myAPN ..... %d \n", myEIB->apn) ;
		logit(  "myAPN := %d", myEIB->apn) ;

		/**
		 * running in plain simulation mode
		 * the only thing we do here is to copy every message from the transmit buffer
		 * to the receive buffer. this is actually what happens if we are in TPUART mode,
		 * when the transmitted bytes are immediately received back
		 */
		cycleCounter	=	0 ;
		logit(  "running in SIMULATION mode ... (on MacOS?)") ;
		while ( runLevel >= 0) {

			/**
			 * read a message to be send from the send queue
			 * msgBuf collects messages
			 */
			if (( msgToSnd = eibReceiveMsg( myEIB, &msgBuf)) != NULL && runLevel >= 0) {

				/**
				 * and put it into the received queue
				 */
				if ( msgToSnd->apn == 0) {
					msgToSnd->apn	=	myAPN ;
					msgToSnd->frameType	=	eibDataFrame ;
					eibQueueMsg( myEIB, msgToSnd) ;
				} else {
				}
			}
//			sleepTimer	=	SLEEP_TIME ;
//			sleep( sleepTimer) ;
		}
#else

		/**
		 * close the stdin, -out and -err files;
		 * fork the process;
		 * the parent process (parentProcessPid != 0) will terminate
		 * the child process will fork again, thereby creating the >new< receiver process
		 * and the >existing< sending process;
		 *
		 */
		parentProcessPid	=	fork() ;

		/**
		 * CHILD process, will continue running
		 */
		if ( parentProcessPid == 0) {

			/**
			 * fork process to separate receiver and sender process;
			 */
			childProcessPid	=	fork() ;

			/**
			 * CHILD process, will run receiving device
			 */
			if ( childProcessPid == 0) {

				/**
				 * setup signal handler
				 */
				close( STDIN_FILENO) ;
				close( STDOUT_FILENO) ;
				close( STDERR_FILENO) ;
				signal( SIGTERM, sigHandlerTERM) ;
				signal( SIGINT, sigHandler) ;
				signal( SIGHUP, sigHandler) ;
				setbuf( stdout, NULL) ;

				strcat( progName, "-EIB") ;
				logit(  "myAPN := %d", myEIB->apn) ;
				logit(  "running in real bridging mode ... (on Raspberry?) RECEIVER") ;
				rcvMode	=	waiting_for_control ;
				incompleteMsg	=	0 ;
				while ( runLevel >= 0) {

					/**
					 * as long as there's something coming from the serial port or an incomplete message
					 *	put it into the receive buffer
					 */
					rcvMode	=	waiting_for_control ;
					rcvdBytes	=	ttyPoll( myTTY, (unsigned char *) &buf, 1) ;
					while ( rcvdBytes > 0 || incompleteMsg) {
						if ( rcvdBytes > 0) {
							switch ( rcvMode) {
							case	waiting_for_control	:
								rcvBufferCount	=	0 ;
								rcvCompareBuffer[rcvBufferCount++]	=	buf ;

								/**
								 *  check for start of L_DATA frame
								 */
								if (( buf & 0xd3) == 0x90) {
									msgRcv.control	=	buf ;
									rcvMode	=	waiting_for_hw_adr ;
									rcvdLength	=	0 ;
									incompleteMsg	=	1 ;
									msgRcv.sndAddr	=	0x0000 ;
									msgRcv.ownChecksum	=	0x00 ;

								/**
								 *  check for start of L_LONG_DATA frame
								 */
								} else if (( buf & 0xd3) == 0x10) {
									msgRcv.control	=	buf ;
									rcvMode	=	waiting_for_hw_adr ;
									rcvdLength	=	0 ;
									incompleteMsg	=	1 ;
									msgRcv.sndAddr	=	0x0000 ;
									msgRcv.ownChecksum	=	0x00 ;

								/**
								 *  check for ACK frame
								 */
								} else if ( buf == 0xcc) {
									msgRcv.apn	=	myAPN ;
									msgRcv.control	=	buf ;
									msgRcv.frameType	=	eibAckFrame ;
									eibQueueMsg( myEIB, &msgRcv) ;

								/**
								 *  check for NACK frame
								 */
								} else if ( buf == 0x0c) {
									msgRcv.apn	=	myAPN ;
									msgRcv.control	=	buf ;
									msgRcv.frameType	=	eibNackFrame ;
									eibQueueMsg( myEIB, &msgRcv) ;

								/**
								 *  check for BUSY frame
								 */
								} else if ( buf == 0xc0) {
									msgRcv.apn	=	myAPN ;
									msgRcv.control	=	buf ;
									msgRcv.frameType	=	eibBusyFrame ;
									eibQueueMsg( myEIB, &msgRcv) ;

								/**
								 *  check for L_POLL_DATA frame
								 */
								} else if ( buf == 0xf0) {
									msgRcv.apn	=	myAPN ;
									msgRcv.control	=	buf ;
									msgRcv.frameType	=	eibPollDataFrame ;

								/**
								 *  check for L_DATA.confirm frame
								 */
								} else if ( buf == 0x8b) {
									msgRcv.apn	=	myAPN ;
									msgRcv.control	=	buf ;
									msgRcv.frameType	=	eibDataConfirmFrame ;
									eibQueueMsg( myEIB, &msgRcv) ;
									sndConfirmed	=	1 ;

								/**
								 *  check for L_DATA.confirm frame
								 */
								} else if ( buf == 0x0b) {
									msgRcv.apn	=	myAPN ;
									msgRcv.control	=	buf ;
									msgRcv.frameType	=	eibDataConfirmFrame ;
									eibQueueMsg( myEIB, &msgRcv) ;
									sndConfirmed	=	1 ;

								/**
								 *  check for L_DATA.confirm frame
								 */
								} else if ( ( buf & 0x07) == 0x07) {
									msgRcv.apn	=	myAPN ;
									msgRcv.control	=	buf ;
									msgRcv.frameType	=	eibTPUARTStateIndication ;
									eibQueueMsg( myEIB, &msgRcv) ;

								/**
								 *  no valid frame start, consider data to be spurious
								 */
								} else {
								}
								break ;
							case	waiting_for_hw_adr	:
								rcvdLength++ ;
								if ( rcvdLength >= 2) {
									rcvMode	=	waiting_for_group_adr ;
									rcvdLength	=	0 ;
									msgRcv.rcvAddr	=	0x0000 ;
								}
								msgRcv.sndAddr	<<=	8 ;
								msgRcv.sndAddr	|=	buf ; ;
								break ;
							case	waiting_for_group_adr	:
								rcvdLength++ ;
								if ( rcvdLength >= 2) {
									rcvMode	=	waiting_for_info ;
								}
								msgRcv.rcvAddr	<<=	8 ;
								msgRcv.rcvAddr	|=	buf ; ;
								break ;
							case	waiting_for_info	:
								msgRcv.info	=	buf ;
								expLength	=	(int) (buf & 0x07) + 1 ;
								rcvdLength	=	0 ;
								rcvMode	=	waiting_for_data ;
								rcvData	=	msgRcv.mtext ;
								break ;
							case	waiting_for_data	:
								*rcvData++	=	buf ;
								rcvdLength++ ;
								if ( rcvdLength >= expLength) {
									rcvMode	=	waiting_for_checksum ;
								}
								break ;
							case	waiting_for_checksum	:
								incompleteMsg	=	0 ;
								rcvMode	=	waiting_for_control ;
								msgRcv.checksum	=	buf ;
								msgRcv.apn	=	myAPN ;
								msgRcv.frameType	=	eibDataFrame ;
								if (( msgRcv.checksum + msgRcv.ownChecksum) == 0xff) {
									eibQueueMsg( myEIB, &msgRcv) ;
								} else {
								}
								break ;
							}
							msgRcv.ownChecksum	^=	buf ;
						}
						rcvdBytes	=	ttyPoll( myTTY, (unsigned char *) &buf, 1) ;
					}
				}
			} else {

				/**
				 * setup signal handler
				 */
				close( STDIN_FILENO) ;
				close( STDOUT_FILENO) ;
				close( STDERR_FILENO) ;
				signal( SIGHUP, sigHandler) ;
				signal( SIGINT, sigHandler) ;
				setbuf( stdout, NULL) ;

				strcat( progName, "-BRIDGE") ;
				logit(  "myAPN := %d", myEIB->apn) ;
				logit(  "running in real bridging mode ... (on Raspberry?) SENDER") ;
				rcvMode	=	waiting_for_control ;
				incompleteMsg	=	0 ;
				while ( runLevel >= 0) {

					/**
					 * IF there's a message to send
					 */
					if (( msgToSnd = eibReceiveMsg( myEIB, &msgBuf)) != NULL) {

						/**
						 * IF this message is not coming from my own port
						 */
						if ( msgToSnd->apn != myAPN /* && sndConfirmed == 1 */) {
							sndMode	=	sending_control ;
						 	msgToSnd->checksum	=	0x00 ;
							sendingByte	=	0 ;
							while ( sndMode != sending_idle) {
								switch ( sndMode) {
								case	sending_control	:
									sndConfirmed	=	0 ;
									bufp	=	0x80 + sendingByte ;
									buf	=	msgToSnd->control ;
							 		msgToSnd->checksum	^=	buf ;
									sndMode	=	sending_hw_adr ;
									sentLength	=	0 ;
									break ;
								case	sending_hw_adr	:
									bufp	=	0x80 + sendingByte ;
									if ( sentLength == 0)
										buf	=	msgToSnd->sndAddr >> 8 ;
									else if ( sentLength == 1)
										buf	=	msgToSnd->sndAddr & 0xff ;
						 			msgToSnd->checksum	^=	buf ;
									sentLength++ ;
									if ( sentLength >= 2) {
										sndMode	=	sending_group_adr ;
										sentLength	=	0 ;
									}
									break ;
								case	sending_group_adr	:
									bufp	=	0x80 + sendingByte ;
									if ( sentLength == 0)
										buf	=	msgToSnd->rcvAddr >> 8 ;
									else if ( sentLength == 1)
										buf	=	msgToSnd->rcvAddr & 0xff ;
						 			msgToSnd->checksum	^=	buf ;
									sentLength++ ;
									if ( sentLength >= 2) {
										sndMode	=	sending_info ;
									}
									break ;
								case	sending_info	:
									bufp	=	0x80 + sendingByte ;
									buf	=	msgToSnd->info ;
						 			msgToSnd->checksum	^=	buf ;
									expLength	=	(int) ( msgToSnd->info & 0x07)  + 1 ;
									sentLength	=	0 ;
									sndMode	=	sending_data ;
									break ;
								case	sending_data	:
									bufp	=	0x80 + sendingByte ;
									buf	=	msgToSnd->mtext[sentLength] ;
						 			msgToSnd->checksum	^=	buf ;
									sentLength++ ;
									if ( sentLength >= expLength) {
										sndMode	=	sending_checksum ;
									}
									break ;
								case	sending_checksum	:
									bufp	=	0x40 + sendingByte ;
						 			msgToSnd->checksum	^=	0xff ;
									buf	=	msgToSnd->checksum ;
									sndMode	=	sending_idle ;
									break ;
								}
								ttySendBytes(  myTTY, bufp, buf) ;
								sendingByte++ ;
							}
							memcpy( &lastSentMsg, msgToSnd, sizeof( knxMsg)) ;
							sndTimeout	=	0 ;
						} else if ( msgToSnd->apn != 0 && ( msgToSnd->control & 0x20) == 0x20) {
						} else if ( msgToSnd->apn != 0) {
						} else {
						}
					}
				usleep( 100000) ;			// sleep for 100 milliseconds
				}
				deletePIDFile( progName, "", ownPID) ;
				eibClose( myEIB) ;
				ttyClose( myTTY) ;
			}
		}
#endif
	} else {
		logit(  "process already running ...") ;
	}
	logit(  "shutting down ...") ;

	/**
	 *
	 */
	exit( 0) ;
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
	printf( "%s: %s [-Q=<queueIdf>] [-M] [-S] \n\n", progName, progName) ;
	printf( "Start a TPUART<->SimEIB/KNX bridge with id queueId.\n") ;
}
