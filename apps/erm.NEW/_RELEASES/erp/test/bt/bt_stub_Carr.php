<?php

$_SERVER["DOCUMENT_ROOT"]	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/r2/httpdocs/" ;
require_once( "../../../Config/config.inc.php") ;

require_once( "FDbg.php") ;
require_once( "FDb.php") ;

require_once( "global.inc.php") ;
require_once( "option.inc.php") ;

require_once( "Carr.php") ;

/**
 *
 */
$newCarr	=	new Carr() ;
print_r( $newCarr) ;
print_r( $newCarr->getRVersOpt()) ;

printf( "Ausgabe von optionRet(...) \n") ;
printf( optionRet( $newCarr->getRVersOpt(), "", "_-I-_")) ;

/**
 *
 */

$newCarr->setCarrier( 80) ;
print_r( $newCarr) ;

$kosten	=	$newCarr->getVsnd( 3.45, 12, 23, 34) ;
printf( "Versandkosten: %7.2f\n", $kosten) ;

$kosten	=	$newCarr->getVsnd( 4.99, 12, 23, 34) ;
printf( "Versandkosten: %7.2f\n", $kosten) ;

$kosten	=	$newCarr->getVsnd( 5.00, 12, 23, 34) ;
printf( "Versandkosten: %7.2f\n", $kosten) ;

$kosten	=	$newCarr->getVsnd( 5.00, 250, 10, 10) ;			// hoeher Ueberlaengekosten > 200
printf( "Versandkosten: %7.2f\n", $kosten) ;

$kosten	=	$newCarr->getVsnd( 5.00, 10, 80, 80) ;			// hoeher Gurtmasskosten > 300
printf( "Versandkosten: %7.2f\n", $kosten) ;

$kosten	=	$newCarr->getVrschng( 123.00) ;			// hoeher Gurtmasskosten > 300
printf( "Versicherungskosten: %7.2f\n", $kosten) ;

$kosten	=	$newCarr->getVrschng( 1234.00) ;			// hoeher Gurtmasskosten > 300
printf( "Versicherungskosten: %7.2f\n", $kosten) ;

/**
 *
 */
