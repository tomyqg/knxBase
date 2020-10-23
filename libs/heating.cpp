#include	"heating.h"

heating::heating( char *_confFile) {
	bufferWater->config( _confFile) ;
	bufferHeating->config( _confFile) ;
} ;

heating::~heating() {
}

heating::control() {

	int		waterNeedsHeating ;
	int		heatingNeedsHeating ;

	switch ( currentMode) {
	case	0x00	:				// idle

	}

	if ( modeValid( nextMode)) {
	}
}
