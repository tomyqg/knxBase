#include	<stdio.h>
#include	<stdlib.h>
#include	<string.h>
#include	<sys/ipc.h>
#include	<sys/msg.h>

typedef struct msgbuf {
		long    mtype;
		char    mtext[ 1024];
	} msgBuf;


key_t key ;	/* key to be passed to msgget() */ 
int msgflg ;	/* msgflg to be passed to msgget() */ 
int msqid ;	/* return value from msgget() */ 

int	main( int argc, char *argv[]) {
	msgBuf	myMessage ;
	key	=	12345 ;
	if (( msqid = msgget( key, IPC_CREAT | 0666)) < 0) {
		printf( "could not connect to message queue\n") ;
	} else {
		printf( "message queue connected\n") ;
		myMessage.mtype	=	atoi( *++argv) ;
		strcpy( myMessage.mtext, "Hello, PHP script, I am the C-Program\n") ;
		msgsnd( msqid, &myMessage, strlen( myMessage.mtext), IPC_NOWAIT) ;
	}
}
