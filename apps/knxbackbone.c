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
 * knxbackbone.c
 *
 * EIB/KNX backbone process
 *
 * knxbackbone as such does not do more but create a shared memory segment
 * which serves as the central buffer for all EIB messages.
 * Accessing this central buffer happens through the usage of functions in
 * the "eib" library, which provides all necessary functions for opening
 * and closing connections to the EIB messagign world as well as sending
 * and receiving messages.
 *
 * When a programm wants to gain access to EIB messages, be it for sending,
 * writing or both, it opens a connection to the knx-backbone through a call to
 * "eibOpen".
 *
 *
 * Revision history
 *
 * Date			Rev.	Who	what
 * -----------------------------------------------------------------------------
 * 2016-03-02	PA1		khw	inception;
 * 2016-04-26	PA2		khw	added explanation of the mechanism;
 * 2016-10-06	PA3		khw	modifications towards daemon process;
 * 2018-07-29	PA4		khw	modified logging towards system logger;
 *
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<stdarg.h>
#include	<string.h>
#include	<strings.h>
#include	<unistd.h>
#include	<time.h>
#include	<math.h>
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/sem.h>
#include	<sys/signal.h>
#include	<sys/stat.h>

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
int	cfgDaemon	=	1 ;				// default:	run as daemon process

/**
 *
 */
void	iniCallback( char *_block, char *_para, char *_value) {
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		} else if ( strcmp( _para, "daemon") == 0) {
			cfgDaemon	=	atoi( _value) ;
		}
	}
}

/**
 *
 */
int	main( int argc, char *argv[]) {
	eibHdl	*myEIB ;
	int	opt ;
	int	i ;
	pid_t	pid, sid ;
	char	iniFilename[]	=	"knx.ini" ;

	/**
	 * setup the shared memory for EIB Receiving Buffer
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
	while (( opt = getopt( argc, argv, "FQ:?")) != -1) {
		switch ( opt) {
		case	'F'	:
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
	 * change file mode mask
	 */
	umask( 0) ;
	ownPID	=	getpid() ;			// re-read due to forking ...
	if ( createPIDFile( progName, "", ownPID)) {
		logit( "opening eib ...") ;
		myEIB	=	eibOpen( 0x0000, IPC_CREAT, cfgQueueKey, progName, 0) ;
		while ( runLevel >= 0) {
			sleep( 5) ;
			for ( i=1 ; i<EIB_MAX_APN; i++) {
				if ( myEIB->shmKnxBus->apns[i] == i) {
					myEIB->shmKnxBus->apnDesc[i].wdCount	+=	myEIB->shmKnxBus->wdIncr ;
					myEIB->shmKnxBus->wdIncr	=	0 ;
					if ( myEIB->shmKnxBus->apnDesc[i].wdCount > 60) {
						eibForceCloseAPN( myEIB, i) ;
					}
				}
			}
		}
		logit( "closing eib ...") ;
		eibClose( myEIB) ;
		deletePIDFile( progName, "", ownPID) ;
	} else {
		logit( "process already running ...", ownPID) ;
	}
	logit( "shutting down ...", ownPID) ;

	/**
	 *
	 */
	exit( EXIT_SUCCESS) ;
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
	printf( "%s: %s [-D <debugLevel>] [-Q=<queueIdf>] [-M] [-S] \n\n", progName, progName) ;
	printf( "Start a TPUART<->SimEIB/KNX bridge with id queueId.\n") ;
}
