<?php

require_once( "global.inc.php") ;
require_once( "parameter.inc.php") ;

require_once( "genKdDoc.php") ;

if ( ! isset ( $_COOKIE[ $myConfig->shop->cookieName])) {
	$errorCode	=	1008 ;
	include( "fehler.php") ;
	die() ;
}

$mySession	=	new Session() ;
$mySession->SessionId	=	$_COOKIE[ $myConfig->shop->cookieName] ;
$mySession->fetchFromDb() ;
if ( $mySession->_valid == 0) {
	$errorCode	=	1009 ;
	include( "fehler.php") ;
	die() ;
}

$pdfName	=	genDocument( "Merkzettel", $mySession->MerkzettelUniqueId, "mein-mikroskop.de Team, via www.mein.mikroskop.de", true) ;

//echo $pdfName . "<br /> \n" ;

$len	=	filesize( $pdfName) ;

//echo $len . "<br/>" ;

$localMode	=	1 ;
if ( $localMode == 0) {
	header( "Content-type: application/pdf") ;
	header( "Content-length: $len") ;
	header( "Content-Disposition: inline; filename=" . $pdfName) ;
	readfile( $pdfName) ;
	$systemCmd	=	"mv " . $pdfName . " Archiv/Merkzettel/" . $mySession->MerkzettelNr . ".pdf" ;
	system( $systemCmd) ;
} else {
	echo $pdfName . "<br/>\n" ;
	$systemCmd	=	"mv " . $pdfName . " Archiv/Merkzettel/" . $mySession->MerkzettelNr . ".pdf" ;
	echo $systemCmd . "<br/>\n" ;
	echo system( $systemCmd) ;
}
//system( "rm " . $pdfName) ;
?>
