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
 * knxipbridged.c
 *
 * knx<->ip bridge daemon
 *
 * this servcer does not listen to the multicast address.
 * knxipbridgehd acts as the hello-daemon to answer such requests.
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2015-11-26	PA1		khw		inception;
 * 2017-11-21	PA2		khw		serious continuation; daemon is able to react
 *								on SEARCH_REQUEST and answer with SEARCH_REPLY;
 */

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <time.h>
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/ioctl.h>
#include <netinet/in.h>
#include <net/if.h>
#include <arpa/inet.h>

#include	"knxip.h"
#include	"mylib.h"

#define KNX_HELLO_GROUP	"192.168.6.204"			// multicast address
#define KNX_HELLO_PORT	3671
#define MSGBUFSIZE		256

int	main(int argc, char *argv[]) {
	struct sockaddr_in	rcvAddr, sndAddr ;
	struct ifreq 		ifr;
	int					rcvFd, sndFd ;
	int					nbytes ;
	int					i ;
	socklen_t			addrlen	=	sizeof( rcvAddr) ;
	struct				ip_mreq mreq;
	char				msgbuf[MSGBUFSIZE];
	char				sndBuffer[MSGBUFSIZE] ;
	int					sndBufferLen ;
	char				*mbp ;
	IPHeader			knxBuf ;
	knxHPAI				knxDisc ;
	int					sendToResult ;
	int					errorCode ;
	char				targetIPAddr[32] ;
	char				ownIPAddr[32] ;

	IPHeader		myReplyIPHeader ;

	IPMessage		myIPMessage ;
	knxHPAI			myKNXHPAI ;
	knxDIB01		myKNXDIB01 ;
	knxDIB02		myKNXDIB02 ;
	knxDIBFE		myKNXDIBFE ;

	u_int yes	=	1;		/*** MODIFICATION TO ORIGINAL */

	printf( "Length of 'knxHPAI' ............ : %3d \n", ( int) sizeof( knxHPAI)) ;
	printf( "Length of 'knxDIB01' ........... : %3d \n", ( int) sizeof( knxDIB01)) ;
	printf( "Length of 'knxDIB02' ........... : %3d \n", ( int) sizeof( knxDIB02)) ;
	printf( "Length of 'knxDIBFE' ........... : %3d \n", ( int) sizeof( knxDIBFE)) ;

	/**---------------------------------------------------------------------------
	 *	create the multicast socket
	 */
	/**
	 * create an ordinary UDP socket
	 */
	if (( rcvFd = socket( AF_INET, SOCK_DGRAM, 0)) < 0) {
		perror( "socket");
		exit( 1);
	}

	/**** MODIFICATION TO ORIGINAL */
	/**
	 * allow multiple sockets to use the same PORT number
	 */
	if ( setsockopt( rcvFd, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof(yes)) < 0) {
		perror( "Reusing ADDR failed");
		exit(1);
	}
	/*** END OF MODIFICATION TO ORIGINAL */

	/**
	 * set up destination address
 	 */
	memset( &rcvAddr, 0, addrlen);
	rcvAddr.sin_family			=	AF_INET;
	rcvAddr.sin_addr.s_addr		=	htonl( INADDR_ANY); /* N.B.: differs from sender */
	rcvAddr.sin_port			=	htons( KNX_HELLO_PORT);

	/**
	 * bind to receive address
	 */
	if ( bind( rcvFd, (struct sockaddr *) &rcvAddr, sizeof(rcvAddr)) < 0) {
		perror("bind");
		exit(1);
	}

	/**---------------------------------------------------------------------------
	 *	create the unicast socket for sending the reply to the SEARCH_REQUEST
	 */
	/**
	 * create what looks like an ordinary UDP socket for the originator of SEARCH_REQUEST
	 */
	if (( sndFd = socket( AF_INET, SOCK_DGRAM, IPPROTO_UDP)) < 0) {
		perror( "socket");
		exit( 1);
	}

	/**
	 * I want to get an IPv4 IP address
	 */
	 ifr.ifr_addr.sa_family	=	AF_INET;

	/**
 	 * I want IP address attached to "en0"
	 */
	strncpy( ifr.ifr_name, "en0", IFNAMSIZ-1);

	ioctl(sndFd, SIOCGIFADDR, &ifr);

 	/**
	 * display result
	 */
	sprintf( ownIPAddr, "%s", inet_ntoa(((struct sockaddr_in *)&ifr.ifr_addr)->sin_addr));

//	if( bind( sndFd ,(struct sockaddr*) &sndAddr, sizeof(sndAddr)) < 0) {
//		perror( "bind");
//		exit( 1);
//    }

	/**
	 * now just enter a read-print loop
	 */
	while ( 1) {
		addrlen	=	sizeof( rcvAddr) ;
		if (( nbytes = recvfrom( rcvFd, msgbuf, MSGBUFSIZE, 0, (struct sockaddr *) &rcvAddr, &addrlen)) < 0) {
			 perror( "recvfrom") ;
			 exit( 1) ;
		}
		memcpy( &knxBuf, msgbuf, sizeof( knxBuf)) ;
		printf( "KNX Length .................. : %02x \n", knxBuf.len) ;
		printf( "KNX Protokoll Version ....... : %02x \n", knxBuf.protVer) ;
		printf( "KNX Request ................. : %04x \n", knxBuf.req) ;
		printf( "KNX Total Length ............ : %04x \n", KNX_LEN( knxBuf.totLen)) ;
		switch ( KNX_LEN( knxBuf.req)) {
 		case	SEARCH_REQUEST	:
			printf( "  SEARCH_REQUEST\n") ;
			memcpy( &knxDisc, &msgbuf[knxBuf.len], sizeof( knxHPAI)) ;
			sprintf( targetIPAddr, "%d.%d.%d.%d", knxDisc.addr[0], knxDisc.addr[1], knxDisc.addr[2], knxDisc.addr[3]) ;
			printf( "    KNX Length .................. : %02x \n", knxDisc.len) ;
			printf( "    KNX Host Protocol ........... : %02x \n", knxDisc.hostProt) ;
			printf( "    KNX my IP addr .............. : %s\n", inet_ntoa( ((struct sockaddr_in *)&ifr.ifr_addr)->sin_addr)) ;
			printf( "    KNX SEARCH_REQUEST from IP .. : %s\n", targetIPAddr) ;
			printf( "    KNX Port .................... : %d\n", KNX_LEN( knxDisc.port)) ;

			/**
			 *	now lets answer the SEARCH_REQUEST
			 */
			createKNXHPAI( &myKNXHPAI, ((struct sockaddr_in *)&ifr.ifr_addr)->sin_addr, KNX_HELLO_PORT) ;
			createKNXDIB01( &myKNXDIB01) ;
			createKNXDIB02( &myKNXDIB02) ;
			createKNXDIBFE( &myKNXDIBFE) ;

			/**
			 *
			 */
			/**
			 * set up destination address
		 	 */
			memset( &sndAddr, 0, addrlen);
			sndAddr.sin_family			=	AF_INET;
			sndAddr.sin_addr.s_addr		=	inet_addr( targetIPAddr) ;
			sndAddr.sin_port			=	htons( KNX_HELLO_PORT);

			sndBufferLen	=	0 ;
			memcpy( &sndBuffer[ sndBufferLen], &myKNXHPAI, sizeof( knxHPAI)) ;					sndBufferLen	+=	sizeof( knxHPAI) ;
			memcpy( &sndBuffer[ sndBufferLen], &myKNXDIB01, sizeof( myKNXDIB01)) ;				sndBufferLen	+=	sizeof( myKNXDIB01) ;
			memcpy( &sndBuffer[ sndBufferLen], &myKNXDIB02, sizeof( myKNXDIB02)) ;				sndBufferLen	+=	sizeof( myKNXDIB02) ;
			memcpy( &sndBuffer[ sndBufferLen], &myKNXDIBFE, sizeof( myKNXDIBFE)) ;				sndBufferLen	+=	sizeof( myKNXDIBFE) ;
			createIPMessage( &myIPMessage, (u_char *) &sndBuffer, sndBufferLen) ;
			DumpHex( &myIPMessage, KNX_LEN( myIPMessage.totLen)) ;
			sendToResult	=	sendto( sndFd, &myIPMessage, KNX_LEN( myIPMessage.totLen), 0, ( struct sockaddr *) &sndAddr, sizeof( sndAddr)) ;
			errorCode	=	errno ;
			printf( "Sent Total Length .... : %3d, Status ... : %d, errno ... : %d \n", KNX_LEN( myIPMessage.totLen), sendToResult, errorCode) ;
			break ;
		case	SEARCH_RESPONSE	:
			printf( "  SEARCH_RESPONSE\n") ;
			break ;
		case	DESCRIPTION_REQUEST	:
			printf( "  DESCRIPTION_REQUEST\n") ;
			break ;
		case	DESCRIPTION_RESPONSE	:
			printf( "  DESCRIPTION_RESPONSE\n") ;
			break ;
		case	CONNECT_REQUEST	:
			printf( "  CONNECT_REQUEST\n") ;
			break ;
		case	CONNECT_RESPONSE	:
			printf( "  CONNECT_RESPONSE\n") ;
			break ;
		case	CONNECTIONSTATE_REQUEST	:
			printf( "  CONNECTIONSTATE_REQUEST\n") ;
			break ;
		case	CONNECTIONSTATE_RESPONSE	:
			printf( "  CONNECTIONSTATE_RESPONSE\n") ;
			break ;
		case	DISCONNECT_REQUEST	:
			printf( "  DISCONNECT_REQUEST\n") ;
			break ;
		case	DISCONNECT_RESPONSE	:
			printf( "  DISCONNECT_RESPONSE\n") ;
			break ;
		}
//		printf( "received %3d bytes; ..... : %s\n", nbytes, msgbuf) ;
	}
}
