#include	"knxCoreClass.hpp"

int	main() {
	int		exitStatus ;
	knxCore		*myCore ;

	exitStatus	=	0 ;

	myCore	=	new knxCore( "knx.ini") ;
	myCore->dump() ;

	printf( "PHOTOVOLT_GRIDVOLTAGE ............. : %5d \n", myCore->getEntry( "PHOTOVOLT_GRIDVOLTAGE")) ;
	printf( "    Current value ................. : %5.1f \n", myCore->getFloat( myCore->getEntry( "PHOTOVOLT_GRIDVOLTAGE"))) ;
	printf( "PHOTOVOLT_BATTVOLT ................ : %5d \n", myCore->getEntry( "PHOTOVOLT_BATTVOLT")) ;
	printf( "    Current value ................. : %5.1f \n", myCore->getFloat( myCore->getEntry( "PHOTOVOLT_BATTVOLT"))) ;

	exit( exitStatus) ;
}
