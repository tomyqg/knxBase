/**
 * Copyright (c) 2015, 2016 wimtecc, Karl-Heinz Welter
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
 * knxipbridge.c
 *
 * IP bridge process
 *
 * ipbridge bridges a "simulated" knx-bus to the IP network
 *
 * Revision history
 *
 * Date		Rev.	Who	what
 * -----------------------------------------------------------------------------
 * 2016-01-20	PA1	khw	inception;
 * 2016-12-15	PA2	khw	complete rework towards broadcasting (multicast);
 *
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<string.h>
#include	<strings.h>
#include	<unistd.h>
#include	<time.h>
#include	<math.h>
#include	<fcntl.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/sem.h>
#include	<sys/socket.h>
#include	<sys/signal.h>
#include	<arpa/inet.h>
#include	<netinet/in.h>
#include	<netdb.h>
#include	<errno.h>

#include	"debug.h"
#include	"knxlog.h"
#include	"knxipbridge.h"
#include	"eib.h"
#include	"eib.h"
#include	"inilib.h"
#include	"mylib.h"
/**
 *
 */
#define	MAX_SLEEP	1
#define FALSE   0
#define	TRUE	1
/**
 *
 */
typedef	enum	{
		eibServer
	,	eibClient
	}	ipbridgeMode ;
/**
 *
 */
extern	void	hdlSocket( int, int) ;
extern	void	help() ;

char	progName[64] ;
int	debugLevel	=	0 ;
knxLogHdl	*myKnxLogger	=	NULL ;
ipbridgeMode	myMode	=	eibClient ;
struct	in_addr	localInterface ;
struct	sockaddr_in	groupSocket, localSocket ;
struct	ip_mreq	group ;
int	sd ;
char	databuf[1024]	=	"This should be an EIB message from the bus ..." ;
int	datalen	=	sizeof( databuf) ;
int	runAsDaemon	=	TRUE ;
/**
 *
 */
void	sigHandler( int _sig) {
	knxLog( myKnxLogger, progName, "%d: mode %d ", getpid(), myMode) ;
	debugLevel	=	-1 ;
}
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
	} else if ( strcmp( _block, "[knxipbridge]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		}
	}
}
/**
 *
 */
int	main( int argc, char *argv[]) {
	pid_t	ownPID ;
	pid_t	childProcessPid, waitProcessPid ;
	int	opt ;
	int	sleepTimer	=	0 ;
	char	serverName[64]	=	"" ;
	unsigned	char	buf, bufp ;
	unsigned	char	*rcvData ;
	unsigned	char	*sndData ;
	/**
	 *
	 */
	int		pid ;
	int		rootSockfd ;
	int		workSockfd ;
	int		portno;
	socklen_t	clilen;
	char		buffer[256];
	struct		sockaddr_in serv_addr, cli_addr;
	int		n;
	struct		hostent	*server ;
			char		iniFilename[]	=	"knx.ini" ;
	/**
	 * setup the shared memory for EIB Receiving Buffer
	 */
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
	strcpy( serverName, "") ;
	while (( opt = getopt( argc, argv, "dD:H:Q:?")) != -1) {
		switch ( opt) {
		case	'd'	:
			runAsDaemon	=	FALSE ;
			break ;
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
	_debug( 0, progName, "starting up ...") ;
	myKnxLogger	=	knxLogOpen( 0) ;
	knxLog( myKnxLogger, progName, "%d: starting up ...", ownPID) ;
	/**
	 *
	 */
	childProcessPid	=	fork() ;
	/**
	 * CHILD process, will run knx -> network (client)
	 */
	if ( childProcessPid == 0) {
		ownPID	=	getpid() ;
		knxLog( myKnxLogger, progName, "%d: child process (sender) started ...", ownPID) ;
		if ( createPIDFile( progName, "snd", ownPID)) {
			sd	=	socket( AF_INET, SOCK_DGRAM, 0) ;
			if ( sd < 0) {
				knxLog( myKnxLogger, progName, "%d: can't open SOCK_DGRAM ...", ownPID) ;
				exit( 1) ;
			} else {
				knxLog( myKnxLogger, progName, "%d: opened SOCK_DGRAM ...", ownPID) ;
			}
			memset( ( char *) &groupSocket, 0, sizeof( groupSocket)) ;
			groupSocket.sin_family	=	AF_INET ;
			groupSocket.sin_addr.s_addr	=	inet_addr( "226.1.1.1") ;
			groupSocket.sin_port	=	htons( 4711) ;
			/**
			 * disable loop-back so we don't receive our own packets ...
			 */
			char	loopch	=	0 ;
			if ( setsockopt( sd, IPPROTO_IP, IP_MULTICAST_LOOP, (char *) &loopch, sizeof( loopch)) < 0) {
				knxLog( myKnxLogger, progName, "%d: setting IP_MULTICAST_LOOP error ...", ownPID) ;
				close( sd) ;
				exit( 1) ;
			} else {
				knxLog( myKnxLogger, progName, "%d: setting IP_MULTICAST_LOOP ok ...", ownPID) ;
			}
			localInterface.s_addr	=	inet_addr( "192.168.6.199") ;
			if ( setsockopt( sd, IPPROTO_IP, IP_MULTICAST_IF, (char *) &localInterface, sizeof( localInterface)) < 0) {
				knxLog( myKnxLogger, progName, "%d: setting local interface error ...", ownPID) ;
				close( sd) ;
				exit( 1) ;
			} else {
				knxLog( myKnxLogger, progName, "%d: setting local interface ok ...", ownPID) ;
			}
			while ( 1) {
				if ( sendto( sd, databuf, datalen, 0, (struct sockaddr *) &groupSocket, sizeof( groupSocket)) < 0) {
					knxLog( myKnxLogger, progName, "%d: sending datagram message error ...", ownPID) ;
				} else {
					knxLog( myKnxLogger, progName, "%d: sending datagram message ok ...", ownPID) ;
				}
				sleep( 5) ;
			}
		} else {
			knxLog( myKnxLogger, progName, "%d: can't create PID file ...", ownPID) ;
		}
	/**
	 * PARENT process, will run network -> knx (server)
	 */
	} else {
		ownPID	=	getpid() ;
		knxLog( myKnxLogger, progName, "%d: parent process (receiver) started ...", ownPID) ;
		if ( createPIDFile( progName, "rcv", ownPID)) {
			sd	=	socket( AF_INET, SOCK_DGRAM, 0) ;
			if ( sd < 0) {
				knxLog( myKnxLogger, progName, "%d: can't open SOCK_DGRAM ...", ownPID) ;
				exit( 1) ;
			} else {
				knxLog( myKnxLogger, progName, "%d: opened SOCK_DGRAM ...", ownPID) ;
			}
			/**
			 * disable loop-back so we don't receive our own packets ...
			 */
			int	reuse	=	1 ;
			if ( setsockopt( sd, SOL_SOCKET, SO_REUSEADDR, (char *) &reuse, sizeof( reuse)) < 0) {
				knxLog( myKnxLogger, progName, "%d: setting SO_REUSEADDR error ...", ownPID) ;
				close( sd) ;
				exit( 1) ;
			} else {
				knxLog( myKnxLogger, progName, "%d: setting SO_REUSEADDR ok ...", ownPID) ;
			}
			memset( ( char *) &localSocket, 0, sizeof( localSocket)) ;
			localSocket.sin_family	=	AF_INET ;
			localSocket.sin_port	=	htons( 4711) ;
			localSocket.sin_addr.s_addr	=	INADDR_ANY ;
			if ( bind( sd, ( struct sockaddr *) &localSocket, sizeof( localSocket))) {
				knxLog( myKnxLogger, progName, "%d: binding datagram error ...", ownPID) ;
				close( sd) ;
				exit( 1) ;
			} else {
				knxLog( myKnxLogger, progName, "%d: binding datagram error ...", ownPID) ;
			}
			group.imr_multiaddr.s_addr	=	inet_addr( "226.1.1.1") ;
			group.imr_interface.s_addr	=	inet_addr( "192.168.6.199") ;
			if ( setsockopt( sd, IPPROTO_IP, IP_ADD_MEMBERSHIP, (char *) &group, sizeof( group)) < 0) {
				knxLog( myKnxLogger, progName, "%d: adding multicast group error ...", ownPID) ;
				close( sd) ;
				exit( 1) ;
			} else {
				knxLog( myKnxLogger, progName, "%d: adding multicast group ok ...", ownPID) ;
			}
			knxLog( myKnxLogger, progName, "%d: entering receive loop ...", ownPID) ;
			while ( 1) {
				if ( read( sd, databuf, datalen) < 0) {
					knxLog( myKnxLogger, progName, "%d: reading datagram message error ...", ownPID) ;
					close( sd) ;
					exit( 1) ;
				} else {
					knxLog( myKnxLogger, progName, "%d: reading datagram message ok ...", ownPID) ;
				}
			}
		} else {
			knxLog( myKnxLogger, progName, "%d: can't create PID file ...", ownPID) ;
		}
	}
	/**
	 *
	 */
	knxLog( myKnxLogger, progName, "%d: shutting down ...", ownPID) ;
	knxLogClose( myKnxLogger) ;
	exit( 0) ;
}

void	help() {
}
