<?php

$pathC	=	"../phpconfig" ;
$pathI	=	"../phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

require_once( "config.inc.php") ;

error_log( "stub_bt_EISSCoreObject.php: starting up ...") ;

error_log( "stub_bt_EISSCoreObject.php: step 1 ...") ;
$myCuOrdrItem	=	new CuOrdrItem() ;
$myCuCommItem	=	new CuCommItem() ;

error_log( "stub_bt_EISSCoreObject.php: step 2 ...") ;
$myCuOrdrItem->dump() ;
$myCuCommItem->dump() ;

error_log( "stub_bt_EISSCoreObject.php: step 3 ...") ;
$myCuOrdrItem->Id	=	10 ;
$myCuOrdrItem->Menge	=	998877;
$myCuOrdrItem->KalkPreis	=	123.45 ;
$myCuOrdrItem->GelieferteMenge	=	12345 ;
$myCuOrdrItem->MengeProVPE	=	100 ;

error_log( "stub_bt_EISSCoreObject.php: step 4 ...") ;
$myCuCommItem->Id	=	1234 ;

error_log( "stub_bt_EISSCoreObject.php: step 5 ...") ;
$myCuOrdrItem->copyTo( $myCuCommItem) ;

error_log( "stub_bt_EISSCoreObject.php: step 6 ...") ;
$myCuOrdrItem->dump() ;
$myCuCommItem->dump() ;

echo "stub_bt_EISSCoreObject.php: finishing up ... \n" ;

?>
