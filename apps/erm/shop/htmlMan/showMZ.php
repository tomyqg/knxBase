<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "MZDoc.php") ;

$mySession	=	new Session() ;
$mySession->SessionId	=	$_COOKIE[ $myConfig->shop->cookieName] ;
$mySession->fetchFromDb() ;
if ( ! $mySession->_valid) {
	$errorCode	=	1009 ;
	include( "fehler.php") ;
	die() ;
}

$myMerkzettel	=	new Merkzettel() ;
//$myMerkzettel->MerkzettelNr	=	$_POST['_IMerkzettelNr'] ;
$myMerkzettel->MerkzettelUniqueId	=	$mySession->MerkzettelUniqueId ;
if ( isset( $_POST['_IMerkzettelUniqueId'])) {
	$myMerkzettel->MerkzettelUniqueId	=	$_POST['_IMerkzettelUniqueId'] ;
}
$myMerkzettel->fetchFromDbByUniqueId() ;

if ( $myMerkzettel->Status == 5) {
	printf( "FEHLER: Dieser Merkzettel ist bereits verschickt ! <br/>\n") ;
	printf( "Gedruckte Version bitte dem Archiv entnehmen ! <br/>\n") ;
	die() ;
}

$finalMode	=	true ;

$myMZDoc	=	new MZDoc() ;
$myMZDoc->setKey( $myMerkzettel->MerkzettelNr) ;
$myMZDoc->createPDF( '', '', '') ;
$pdfName	=	$myConfig->path->Archive . "Merkzettel/" . $myMerkzettel->MerkzettelNr . ".pdf" ;

$len	=	filesize( $pdfName) ;

$localMode	=	0 ;
if ( $localMode == 0) {
	header( "Content-type: application/pdf") ;
	header( "Content-length: $len") ;
	header( "Content-Disposition: inline; filename=" . $pdfName) ;
	readfile( $pdfName) ;
} else {
	echo $pdfName . "<br/>\n" ;
	system( $systemCmd) ;
}

//system( "rm " . $pdfName) ;

?>
