#include	<stdio.h>
#include	<stdbool.h>
#include	<stdint.h>
#include	<stdlib.h>
#include	<stdarg.h>
#include	<string.h>
#include	<strings.h>
#include	<time.h>
#include	<math.h>
#include	<pwd.h>
#include	<unistd.h>
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>

#include	"eib.h"

#define SHM_COM_KEY	10000
#define SHM_OPC_KEY	10001
#define SHM_KNX_KEY	10002
#define SHM_CRF_KEY	10003
#define SHM_MSG_KEY	10004

#define	SIZE_COM_TABLE	0
#define	SIZE_OPC_TABLE	1
#define	SIZE_KNX_TABLE	2
#define	SIZE_CRF_TABLE	3

#ifndef	KNX_CORE_CLASS
#define	KNX_CORE_CLASS

typedef	enum	{
		dtBit		=	1
	,	dt2	=	2
	,	dt3	=	3
	,	dt4		=	4
	,	dt5		=	5
	,	dtInt1		=	11
	,	dtUInt1		=	12
	,	dtInt2		=	13
	,	dtUInt2		=	14
	,	dtInt4		=	15
	,	dtUInt4		=	16
	,	dtFloat2	=	21
	,	dtFloat4	=	22
	,	dtString	=	31
	,	dtTime		=	41
	,	dtDate		=	42
	,	dtDateTime	=	43
	}	dataType ;

typedef	enum	{
		srcKNX		=	1
	,	srcODS		=	2
	,	srcOTHER	=	3
	}	dataOrigin ;

typedef	union	{
		int	i ;
		float	f ;
		double	d ;
		char	s[64] ;
	}	value ;

typedef	struct	{
		int		id ;				//   4
		char		name[64] ;			//  64
		char		alias[32] ;			//  32
		char		defaultVal[64] ;		//  64
		dataType	type ;				//   4
		int		knxHWAddr ;			//   4
		int		knxGroupAddr ;			//   4
		value		val ;				//  64
		int		monitor ;			//   4
		dataOrigin	origin ;			//   4
		int		log ;				//   4
	}	node ;

class	knxCore	{

public:
		knxCore( const char *) ;
		~knxCore() ;

	void	dump() ;
	int	getEntry( const char *) ;
	int	getInt( int _indx) ;
	float	getFloat( int _indx) ;

private:
	void	iniCallback( char *, char *, char *) ;
	int	iniFromFile( const char *_file) ;

	/**
	 *
	 */
	int	cfgQueueKey	=	10031 ;

	/**
	 * define shared memory segment #0: COM Table
 	 */
	key_t	shmCOMKey	=	SHM_COM_KEY ;
	int	shmCOMFlg	=	IPC_CREAT | 0600 ;
	int	shmCOMId ;
	int	shmCOMSize	=	256 ;
	int	 *sizeTable ;

	/**
	 * define shared memory segment #1: OPC Table with buffer
 	 */
	key_t	shmOPCKey	=	SHM_OPC_KEY ;
	int	shmOPCFlg	=	IPC_CREAT | 0600 ;
	int	shmOPCId ;
	int	shmOPCSize ;
	node	 *data ;

	/**
	 * define shared memory segment #2: KNX Table with buffer
 	 */
	key_t	shmKNXKey	=	SHM_KNX_KEY ;
	int	shmKNXFlg	=	IPC_CREAT | 0600 ;
	int	shmKNXId ;
	int	shmKNXSize	=	65536 * sizeof( float) ;
	float	 *floats ;
	int	 *ints ;

	/**
	 * define shared memory segment #3: cross-reference table
 	 */
	key_t	shmCRFKey	=	SHM_CRF_KEY ;
	int	shmCRFFlg	=	IPC_CREAT | 0600 ;
	int	shmCRFId ;
	int	shmCRFSize	=	65536 * sizeof( int) ;
	int	 *crf ;
} ;
#endif
