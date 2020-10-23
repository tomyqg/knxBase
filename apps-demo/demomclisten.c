/*
 * listener.c -- joins a multicast group and echoes all data it receives from
 *		the group to its stdout...
 *
 * Antony Courtney,	25/11/94
 * Modified by: Frédéric Bastien (25/03/04)
 * to compile without warning and work correctly
 */

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <time.h>
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#define HELLO_GROUP "224.0.23.12"
#define HELLO_PORT 3671
#define MSGBUFSIZE 256

#define	KNX_SEARCH_REQUEST	0x0201
#define	KNX_SEARCH_REPLY	0x0202

#define KNX_LEN(vhl)          ((vhl) & 0xff) << 8 | ((((vhl) & 0xff00) >> 8) & 0x00ff)

typedef	struct	knxNetIPHeader	{
	u_char	len ;
	u_char	protVer ;
	u_short	req ;
	u_short	totLen ;
}	knxNetIPHeader ;

typedef	struct	knxNetIPBody	{

}	knxNetIPBody ;

typedef	struct	knxHPAI	{
	u_char	len ;
	u_char	hostProt ;
	u_char	addr[4] ;
	u_short	port ;
}	knxHPAI ;

typedef	struct	knxDIB01	{
	u_char	len ;
	u_char	dibType ;
	u_char	medium ;
	u_char	status ;
	u_short	knxAdr ;
	u_short	instId ;
	u_char	serno[6] ;
	u_char	mcAddr[4] ;
	u_char	macAddr[6] ;
	char	name[30] ;
}	knxDIB01 ;

typedef	struct	knxDIB02	{
	u_char	len ;
	u_char	dibType ;
	u_char	sid1 ;
	u_char	sidData1 ;
	u_char	sid2 ;
	u_char	sidData2 ;
	u_char	sid3 ;
	u_char	sidData3 ;
}	knxDIB02 ;

typedef	struct	knxDIBFE	{
	u_char	len ;
	u_char	dibType ;
	u_char	data[6] ;
}	knxDIBFE ;

extern	void	presetKNXDIB01( knxDIB01 *) ;
extern	void	presetKNXDIB02( knxDIB02 *) ;
extern	void	presetKNXDIBFE( knxDIBFE *) ;

int	main(int argc, char *argv[]) {
	struct sockaddr_in addr;
	int	fd, nbytes ;
	int	i ;
	socklen_t	addrlen	=	sizeof( addr) ;
	struct	ip_mreq mreq;
	char	msgbuf[MSGBUFSIZE];
	char	*mbp ;
	knxNetIPHeader	knxBuf ;
	knxHPAI	knxDisc ;
	knxHPAI	knxResp ;
	knxDIB01	myKNXDIB01 ;
	knxDIB02	myKNXDIB02 ;
	knxDIBFE	myKNXDIBFE ;

	u_int yes=1;            /*** MODIFICATION TO ORIGINAL */

	/* create what looks like an ordinary UDP socket */
	if ((fd=socket(AF_INET,SOCK_DGRAM,0)) < 0) {
		perror("socket");
		exit(1);
	}


	/**** MODIFICATION TO ORIGINAL */
	/* allow multiple sockets to use the same PORT number */
	if ( setsockopt( fd, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof(yes)) < 0) {
		perror( "Reusing ADDR failed");
		exit(1);
	}
	/*** END OF MODIFICATION TO ORIGINAL */

	/* set up destination address */
	memset(&addr,0, addrlen);
	addr.sin_family=AF_INET;
	addr.sin_addr.s_addr=htonl(INADDR_ANY); /* N.B.: differs from sender */
	addr.sin_port=htons(HELLO_PORT);

	/* bind to receive address */
	if (bind(fd,(struct sockaddr *) &addr,sizeof(addr)) < 0) {
		perror("bind");
		exit(1);
	}

	/* use setsockopt() to request that the kernel join a multicast group */
	mreq.imr_multiaddr.s_addr=inet_addr(HELLO_GROUP);
	mreq.imr_interface.s_addr=htonl(INADDR_ANY);
	if (setsockopt(fd,IPPROTO_IP,IP_ADD_MEMBERSHIP,&mreq,sizeof(mreq)) < 0) {
		perror("setsockopt");
		exit(1);
	}

	/* now just enter a read-print loop */
	while (1) {
		addrlen=sizeof(addr);
		if (( nbytes = recvfrom( fd, msgbuf, MSGBUFSIZE, 0, (struct sockaddr *) &addr, &addrlen)) < 0) {
			 perror("recvfrom");
			 exit(1);
		}
		memcpy( &knxBuf, msgbuf, sizeof( knxBuf)) ;
		printf( "KNX Length .................. : %02x \n", knxBuf.len) ;
		printf( "KNX Protokoll Version ....... : %02x \n", knxBuf.protVer) ;
		printf( "KNX Request ................. : %04x \n", knxBuf.req) ;
		printf( "KNX Total Length ............ : %04x \n", KNX_LEN( knxBuf.totLen)) ;
		switch ( KNX_LEN( knxBuf.req)) {
 		case	KNX_SEARCH_REQUEST	:
			memcpy( &knxDisc, &msgbuf[knxBuf.len], sizeof( knxHPAI)) ;
			printf( "    KNX Length .................. : %02x \n", knxDisc.len) ;
			printf( "    KNX Host Protocol ........... : %02x \n", knxDisc.hostProt) ;
			printf( "    KNX From IP ................. : %d.%d.%d.%d \n", knxDisc.addr[0], knxDisc.addr[1], knxDisc.addr[2], knxDisc.addr[3]) ;
			printf( "    KNX Port .................... : %d \n", KNX_LEN( knxDisc.port)) ;
			/**
			 *	now lets answer the SEARCH_REQUEST
			 */
			presetKNXDIB01( &myKNXDIB01) ;
			presetKNXDIB02( &myKNXDIB02) ;
			presetKNXDIBFE( &myKNXDIBFE) ;
			break ;
		default	:
			break ;
		}
		printf( "\n") ;
//		printf( "received %3d bytes; ..... : %s\n", nbytes, msgbuf) ;
	}
}

void	presetKNXHPAI( knxHPAI *_myHPAI) {
	myHPAI->len		= (u_char) sizeof( knxHPAI) ;
	myHPAI->hostProt	=	(u_char) 0x01 ;
	myHPAI->addr[0]		=	(u_char) 192 ;
	myHPAI->addr[1]		=	(u_char) 168 ;
	myHPAI->addr[2]		=	(u_char) 6 ;
	myHPAI->addr[3]		=	(u_char) 153 ;
	myHPAI->port		=	KNX_LEN( 3671) ;
}

void	presetKNXDIB01( knxDIB01 *_myDIB) {
	_myDIB->len	=	(u_char) sizeof( knxDIB01) ;
	_myDIB->dibType	=	(u_char) 0x01 ;		// DEVICE_INFO
	_myDIB->medium	=	(u_char) 0x02 ;		// TPuart
	_myDIB->status	=	0x00 ;
	_myDIB->knxAdr	=	KNX_LEN( 0x1002) ;
	_myDIB->instId	=	KNX_LEN( 0x0000) ;
	strcpy( ( char *) _myDIB->serno, "9999992") ;
	_myDIB->macAddr[0]	=	0x00 ;
	_myDIB->macAddr[1]	=	0x01 ;
	_myDIB->macAddr[2]	=	0x02 ;
	_myDIB->macAddr[3]	=	0x03 ;
	_myDIB->macAddr[4]	=	0x04 ;
	_myDIB->macAddr[5]	=	0x05 ;
	strcpy( _myDIB->name, "wimtecc KNX IP Bridge") ;
}

void	presetKNXDIB02( knxDIB02 *_myDIB) {
	_myDIB->len	=	 (u_char) sizeof( knxDIB01) ;
	_myDIB->dibType	=	(u_char) 0x02 ;			// SUPP_SVC_FAMILIES
	_myDIB->sid1	=	0x02 ;
	_myDIB->sidData1	=	0x01 ;
	_myDIB->sid2	=	0x03 ;
	_myDIB->sidData2	=	0x01 ;
	_myDIB->sid3	=	0x04 ;
	_myDIB->sidData3	=	0x01 ;
}

void	presetKNXDIBFE( knxDIBFE *_myDIB) {
	_myDIB->len	=	 (u_char) sizeof( knxDIBFE) ;
	_myDIB->dibType	=	(u_char) 0xfe ;			// SUPP_SVC_FAMILIES
	_myDIB->data[0]	=	0x00 ;
	_myDIB->data[1]	=	0xc5 ;
	_myDIB->data[2]	=	0x01 ;
	_myDIB->data[3]	=	0x04 ;
	_myDIB->data[4]	=	0xf0 ;
	_myDIB->data[5]	=	0x20 ;
}
