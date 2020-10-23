/*
 * sender.c -- multicasts "hello, world!" to a multicast group once a second
 *
 * Antony Courtney,	25/11/94
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

int	main(int argc, char *argv[])
{
	struct sockaddr_in addr;
	socklen_t	addrlen	=	sizeof( addr) ;

	int fd, cnt;
	struct ip_mreq mreq;
	char *message="Hello, World!";

	/* create what looks like an ordinary UDP socket */
	if ((fd=socket(AF_INET,SOCK_DGRAM,0)) < 0) {
	perror("socket");
	exit(1);
	}

	/* set up destination address */
	memset( &addr, 0, addrlen);
	addr.sin_family=AF_INET;
	addr.sin_addr.s_addr=inet_addr(HELLO_GROUP);
	addr.sin_port=htons(HELLO_PORT);

	/* now just sendto() our destination! */
	while (1) {
		if ( sendto( fd,message, strlen(message), 0, (struct sockaddr *) &addr,
			sizeof(addr)) < 0) {
			perror("sendto");
			exit(1);
		}
		sleep(1);
	}
}
