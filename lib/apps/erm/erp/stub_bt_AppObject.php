<?php

$pathC	=	"../phpconfig" ;
$pathI	=	"../phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

require_once( "config.inc.php") ;

error_log( "stub_bt_AppObject.php: starting up ...") ;

error_log( "stub_bt_AppObject.php: step 1 ...") ;
$myTask	=	new Task() ;

$myTask->dump() ;

$myTask->newKey( 6) ;

$myTask->dump() ;

echo "stub_bt_AppObject.php: finishing up ... \n" ;

?>
