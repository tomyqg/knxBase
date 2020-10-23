#include	<string.h>

#include	"buffer.h"

buffer::buffer() {
} ;

buffer::~buffer() {
}

int		buffer::config( char *_confFile) {
} ;

int		buffer::needsHeating() {
	int		result	=	false ;
	return result ;
}

float	buffer::getTemp() {
	float	average	=	0.0 ;
	int		zones	=	0 ;
	for ( int i = 0 ; i < 8 ; i++) {
		if ( strlen( tagTemp[i]) > 0) {
			average	=	average + temp[ i] ;
			zones++ ;
		}
	}
	if ( zones > 0) {
		average	=	average / zones ;
	}
	return average ;
}

void	buffer::_debug() {
}
