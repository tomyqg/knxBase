<?php

require_once( "FDbg.php") ;
require_once( "FDb.php") ;

require_once( "global.inc.php") ;
require_once( "option.inc.php") ;

require_once( "VeColi.php") ;

FDbg::setLevel( 0xffffffff) ;			// alles tracen
FDbg::enable() ;

/**
 *
 */
$newVeColi	=	new VeColi() ;
print_r( $newVeColi) ;
print_r( $newVeColi->getRStatus()) ;
print_r( $newVeColi->getRVeColiTyp()) ;

printf( "Ausgabe von optionRet(...) \n") ;
printf( optionRet( $newVeColi->getStatusArray(), "", "_-I-_")) ;

printf( "Ausgabe von optionRet(...) \n") ;
printf( optionRet( $newVeColi->getStatusArray(), "90", "_-I-_")) ;

/**
 *
 */

/**
 *
 */
