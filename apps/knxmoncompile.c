/**
 *
 * knxmoncompile.c
 *
 * KNX bus monitor with buffer
 *
 * thius module monitors the messages on the knx bus and stores all group address write
 * value in a table stored in shared memory.
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
 * 2015-11-20	PA1		khw	inception;
 * 2015-11-24	PA2		khw	added semaphores for shared memory
 *						and message queue;
 * 2015-11-26	PA3		khw	added mylib function for half-float
 *						conversions;
 * 2015-12-17	PA4		khw	added MySQL for logging data;
 * 2016-02-01	PA5		khw	added XML capabilities for the object table;
 * 2018-07-29	PA6		khw	modified logging towards system logger;
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
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/signal.h>
#include	<mysql.h>

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

/**
 *
 */
extern	void	help() ;
extern	void	logit( char *_fmt, ...) ;

/**
 *
 */
char	progName[64] ;
pid_t	ownPID ;
int		runLevel ;

/**
 *
 */
void	strlwr( char *_argv) {
	while ( *_argv) {
		if ( *_argv >= 'A' && *_argv <= 'Z')
			*_argv	=	*_argv + 32 ;
		_argv++ ;
	}
}

/**
 *
 */
int	argToBool( char *_argv) {
	int		retval	=	false ;
	strlwr( _argv) ;
	if ( strcmp( _argv, "true") == 0 || strcmp( _argv, "yes") == 0 || strcmp( _argv, "y") == 0 || atoi( _argv) == 1)
		retval	=	true ;
	return retval ;
}

/**
 *
 */
void	iniCallback( char *_block, char *_para, char *_value) {
	if ( strcmp( _block, "[knxglobals]") == 0) {
	} else if ( strcmp( _block, "[knxmoncompile]") == 0) {
	}
}

/**
 *
 */
int	main( int argc, char *argv[]) {
			int		status		=	0 ;

	/**
	 * variables needed for the reception of EIB message
	 */
			int		opt ;
			node	*opcData ;
			char	xmlObjFile[128]	=	"/etc/knx.d/baos.xml" ;
			char	iniFilename[]	=	"knx.ini" ;
			int		objectCount ;

	/**
	 *	END OF TEST SECTION
	 */
	strcpy( progName, *argv) ;

	/**
	 *
	 */
	runLevel	=	1 ;
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 * get command line options
	 */
	while (( opt = getopt( argc, argv, "?")) != -1) {
		switch ( opt) {
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
	logit(  "starting up ...") ;

	/**
	 *	get the array of group adresses to be monitored
	 */
	opcData	=	getNodeTable( xmlObjFile, &objectCount) ;

	exit( status) ;
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
}
