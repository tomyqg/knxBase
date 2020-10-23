/**
 *
 * knxmondump.c
 *
 * dump content of KNX bus monitor buffer
 *
 * Revision history
 *
 * Date		Rev.	Who	what
 * -----------------------------------------------------------------------------
 * 2016-11-09	PA1	khw	inception;
 *
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<string.h>
#include	<strings.h>
#include	<unistd.h>
#include	<time.h>
#include	<math.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/signal.h>

#include	"debug.h"
#include	"knxlog.h"
#include	"nodeinfo.h"
#include	"knxprot.h"
#include	"knxtpbridge.h"
#include	"mylib.h"
#include	"myxml.h"
#include	"inilib.h"
#include	"eib.h"		// rs232.c will differentiate:
				// ifdef  __MAC__
				// 	simulation
				// else
				// 	real life
#include	"mylib.h"
#include	"myxml.h"		// short xml-reader wrapper

#define	MAX_SLEEP	2

extern	void	help() ;
/**
 *
 */
char	progName[64] ;
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
/**
 *
 */
void	iniCallback( char *_block, char *_para, char *_value) {
	_debug( 1, progName, "receive ini value block/paramater/value ... : %s/%s/%s\n", _block, _para, _value) ;
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		}
	} else if ( strcmp( _block, "[knxmon]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
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
		time_t	actTime ;
	struct	tm	*myTime ;
		char	timeBuffer[64] ;
	int	cycleCounter ;
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
	/**
	 * variables needed for the reception of EIB message
	 */
			FILE	*file ;
	unsigned	char	buf, myBuf[64] ;
			node	*actData ;
			node	*opcData ;
			int	opcDataSize ;
			int	rcvdBytes ;
			int	objectCount ;
			int	checksumError ;
			int	adrBytes ;
			int	n; 				// holds number of received characters
			int	monitor	=	0 ; 		// default: no message monitoring
	unsigned        int     control ;
	unsigned        int     addressType ;
	unsigned        int     routingCount ;
		int     expectedLength ;
			float	value ;
	unsigned        int     checkSum ;
	unsigned        char    checkS1 ;
			char    *ptr ;
			knxMsg	myMsgBuf ;
			knxMsg	*myMsg ;
			char	xmlObjFile[128]	=	"/etc/knx.d/baos.xml" ;
			time_t		t ;
			struct	tm	tm ;
			int	lastSec	=	0 ;
			int	lastMin	=	0 ;
			char		iniFilename[]	=	"knx.ini" ;
			char		filter[64]	=	"" ;
	/**
	 *	END OF TEST SECTION
	 */
	signal( SIGINT, sigHandler) ;			// setup the signal handler for ctrl-C
	setbuf( stdout, NULL) ;				// disable output buffering on stdout
	strcpy( progName, *argv) ;
	_debug( 1, progName, "starting up ...") ;

	/**
	 *
	 */
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 * get command line options
	 */
	while (( opt = getopt( argc, argv, "D:Q:f:mx:?")) != -1) {
		switch ( opt) {
		case	'Q'	:
			cfgQueueKey	=	atoi( optarg) ;
			break ;
		case	'D'	:
			debugLevel	=	atoi( optarg) ;
			break ;
		case	'f'	:
			strcpy( filter, optarg) ;
			break ;
		case	'm'	:
			monitor	=	1 ;
			break ;
		case	'x'	:
			strcpy( xmlObjFile, optarg) ;
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
#include	"_shmblock.c"
	/**
	 *
	 */
	objectCount	=	shmOPCSize / sizeof( node) ;
	dumpDataAll( data, objectCount, (void *) floats, filter) ;

	/**
	 * close EIB port
	 */
	exit( status) ;
}

void	help() {
}
