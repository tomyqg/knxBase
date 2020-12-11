#!/usr/bin/php
<?php

$pathC	=	"../Config" ;
$pathI	=	"../Classes" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

require_once( "config.inc.php") ;

// evaluate command line parameters

$debugLevel	=	0 ;

$i	=	1 ;
while ( isset( $argv[$i])) {
	if ( strcmp( $argv[ $i], "-d") == 0) {
		$i++ ;
		if ( isset( $argv[ $i])) {
			$debugLevel	=	intval( $argv[ $i]) ;
		} else {
			printf( "PANIC.: -d muss gefolgt sein durch den Debug Level <br />\n") ;
		}
	} else if ( strcmp( $argv[ $i], "-m") == 0) {
		$i++ ;
		if ( isset( $argv[ $i])) {
			$month	=	intval( $argv[ $i]) ;
		} else {
			printf( "PANIC.: -m muss gefolgt sein durch den Monat <br />\n") ;
		}
	} else if ( strcmp( $argv[ $i], "-y") == 0) {
		$i++ ;
		if ( isset( $argv[ $i])) {
			$year	=	intval( $argv[ $i]) ;
		} else {
			printf( "PANIC.: -y muss gefolgt sein durch das Jahr <br />\n") ;
		}
	}
	$i++ ;
}


//
//

$secPerDay	=	24 * 60 * 60 ;
$zahlTageInUnix	=	30 * $secPerDay ;
$sktoTageInUnix	=	10 * $secPerDay ;

$myCuInvc	=	new CuInvc() ;

//

$myDate	=	getdate( mktime() - ( 10 * $secPerDay)) ;

if ( ! isset( $month)) {
	$month	=	$myDate["mon"] ;
}
if ( ! isset( $year)) {
	$year	=	$myDate["year"] ;
}
$resultMonat	=	sprintf( "%02d (%4d)", $month, $year) ;
$resultPDF	=	sprintf( $myConfig->path->Archive . "CuInvc/%4d-%02d.pdf", $year, $month) ;

// wir schauen uns alle offenen Rechnungen an ...

$query  =      sprintf( "Datum like '%4d-%02d-%%' ORDER BY CuInvcNo DESC ", $year, $month) ;

// get the names of the result field including the `AS` denominator

for ( $myCuInvc->_firstFromDb( $query) ; $myCuInvc->isValid() ; $myCuInvc->_nextFromDb()) {
        $pdfName        =       $myConfig->path->Archive . "CuInvc/" . $myCuInvc->CuInvcNo . ".pdf " ;
        $systemCmd      =       "lpr -P " . $myCuInvc->CuInvcPrnBatch->PrnName . " " . $pdfName . " " . $myCuInvc->CuInvcPrnBatch->PrnOpt . " " ;
        printf( "System Cmd: [%s] \n", $systemCmd) ;
        system( $systemCmd) ;
}

printf( "Datei: %s \n", $resultPDF) ;

?>
