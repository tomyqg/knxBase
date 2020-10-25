#include	"knxCoreClass.hpp"

knxCore::knxCore( const char *_iniFile) {

	/**
	 *
	 */
	iniFromFile( _iniFile) ;

	/**
	 * get and attach the shared memory for COMtable
	 */
	if (( shmCOMId = shmget( shmCOMKey, shmCOMSize, 0600)) < 0) {
		exit( -1) ;
	}
	if (( sizeTable = (int *) shmat( shmCOMId, NULL, 0)) == (void *) -1) {
		exit( -1) ;
	}
	shmCOMSize      =       sizeTable[ SIZE_COM_TABLE] ;
	shmOPCSize      =       sizeTable[ SIZE_OPC_TABLE] ;
	shmKNXSize      =       sizeTable[ SIZE_KNX_TABLE] ;
	shmCRFSize      =       sizeTable[ SIZE_CRF_TABLE] ;

	/**
	 * setup the shared memory for OPCtable
	 */
	if (( shmOPCId = shmget (shmOPCKey, shmOPCSize, shmOPCFlg)) < 0) {
		exit( -101);
	}
	if (( data = (node *) shmat(shmOPCId, NULL, 0)) == (node *) -1) {
		exit( -102);
	}

	/**
	 * setup the shared memory for KNXtable
	 */
	if (( shmKNXId = shmget( shmKNXKey, shmKNXSize, IPC_CREAT | 0600)) < 0) {
		exit( -111) ;
	}
	if (( floats = (float *) shmat( shmKNXId, NULL, 0)) == (void *) -1) {
		exit( -112) ;
	}
	ints    =       (int *) floats ;

	/**
	 * setup the shared memory for CRFtable
	 */
	if (( shmCRFId = shmget( shmCRFKey, shmCRFSize, IPC_CREAT | 0600)) < 0) {
		exit( -121) ;
	}
	if (( crf = (int *) shmat( shmCRFId, NULL, 0)) == (void *) -1) {
		exit( -122) ;
	}

}

knxCore::~knxCore() {
}

int	knxCore::getEntry( const char *_name) {
	int	i, j ;
	j	=	-1 ;
	for ( i=0 ; i<1000 && j == -1 ; i++) {
		if ( strcmp( data[i].name, _name) == 0 || strcmp( data[i].alias, _name) == 0) {
			j	=	i ;
		}
	}
	return j ;
}

float	knxCore::getFloat( int _ndx) {
	return( floats[ _ndx]) ;
}

void	knxCore::iniCallback( char *_block, char *_para, char *_value) {
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		}
	}
}

int	knxCore::iniFromFile( const char *_file) {
		int	status	=	true ;
		int	i ;
		int	lc ;
		int	tc ;
		char	line[128] ;
		char	block[32], para[32], value[64], value2[64] ;
		char	*lp, *p ;
		int	earlEx ;
		FILE	*iniFile ;
	const	char	*homedir;
		int	done	=	0 ;
		int	pathId ;
		char	fileName[128] ;
	/**
	 *
	 */
	pathId	=	0 ;
	while ( ! done) {
		switch ( pathId) {
		case	0	:
			strcpy( fileName, "/etc/knx.d/") ;
			strcat( fileName, _file) ;
			iniFile	=	fopen( fileName, "r") ;
			if ( ! iniFile) {
//				printf( "inilib.c: could not open(find?) ini file [%s]\n", fileName) ;
			}
			break ;
		case	1	:
			if (( homedir = getenv("HOME")) == NULL) {
				homedir	=	getpwuid( getuid())->pw_dir;
			}
			strcpy( fileName, homedir) ;
			strcat( fileName, "/.") ;
			strcat( fileName, _file) ;
			iniFile	=	fopen( fileName, "r") ;
			if ( ! iniFile) {
//				printf( "inilib.c: could not open(find?) ini file [%s]\n", fileName) ;
			}
			break ;
		default	:
			done	=	1 ;
			break ;
		}
		pathId++ ;
		if ( iniFile) {
			lc	=	0 ;
			strcpy( block, "*") ;
			strcpy( para, "*") ;
			strcpy( value, "*") ;
			while ( fgets( line, 128, iniFile)) {
				strcpy( value2, "*") ;
				lc++ ;
				lp	=	line ;
				tc	=	0 ;
				earlEx	=	0 ;
				for ( p=strtok( lp, " \t\n") ; p != NULL && earlEx == 0 ; p=strtok( NULL, " \t\n")) {
					switch ( tc) {
					case	0	:
						if ( p[0] == '[')
							strcpy( block, p) ;
						else if ( p[0] == '#')			// comment => early exit
							earlEx	=	1 ;
						else
							strcpy( para, p) ;
						break ;
					case	1	:
						break ;
					case	2	:
						strcpy( value, p) ;
						break ;
					case	3	:
						strcpy( value2, p) ;
						break ;
					default	:
						break ;
					}
					tc++ ;
				}
				iniCallback( block, para, value) ;
				strcpy( para, "*") ;
				strcpy( value, "*") ;
			}
			fclose( iniFile) ;
			iniFile	=	NULL ;
		}
	}
	return status ;
}

/**
 *
 */
void	knxCore::dump() {
	
	printf( "shmCOMData ...... : %08lx %04lx %5d %8d -> %08lx \n", 
					shmCOMKey,
					shmCOMFlg,
					shmCOMId ,
					shmCOMSize,
					sizeTable) ;
	printf( "shmOPCData ...... : %08lx %04lx %5d %8d -> %08lx \n", 
					shmOPCKey,
					shmOPCFlg,
					shmOPCId ,
					shmOPCSize,
					data) ;
	printf( "shmKNXData ...... : %08lx %04lx %5d %8d -> %08lx \n", 
					shmKNXKey,
					shmKNXFlg,
					shmKNXId ,
					shmKNXSize,
					floats) ;
	printf( "shmCRFData ...... : %08lx %04lx %5d %8d -> %08lx \n", 
					shmCRFKey,
					shmCRFFlg,
					shmCRFId ,
					shmCRFSize,
					crf) ;
} ;
