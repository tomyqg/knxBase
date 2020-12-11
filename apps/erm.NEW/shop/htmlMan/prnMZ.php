<?php
include( "../ini.php") ;
require_once( $_SERVER["DOCUMENT_ROOT"]."/../../../Config/config.inc.php") ;
$mySysConfig	=	EISSCoreObject::__getSysConfig() ;
$myAppConfig	=	EISSCoreObject::__getAppConfig() ;
$myAppConfig->addFromAppDb( $mySysConfig->classId) ;
/**
 *
 */
$myShopSession	=	new ShopSession() ;
$myCustomerCart	=	new CustomerCart() ;
if ( isset( $_COOKIE[$myAppConfig->shop->cookieName])) {
	FDbg::trace( 2, FDbg::mdTrcInfo1, "prnMZ.php", "*", "main", "taking path 1.1") ;
	FDbg::trace( 2, FDbg::mdTrcInfo1, "prnMZ.php", "*", "main", "ShopSession Cookie ist definiert") ;
	$myShopSession->ShopSessionId	=	$_COOKIE[$myAppConfig->shop->cookieName] ;
	$myShopSession->fetchFromDb() ;
	if ( $myShopSession->_valid && strcmp( $myShopSession->CustomerCartNo, "") != 0) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, "mzinfo.php", "*", "main", "taking path 1.1.1") ;
		setcookie( $myAppConfig->shop->cookieName, $myShopSession->ShopSessionId, $myAppConfig->shop->cookieTime, "/", $myAppConfig->shop->cookieDomain) ;
		$myCustomerCart->CustomerCartUniqueId	=	$myShopSession->CustomerCartUniqueId ;
		$myCustomerCart->fetchFromDbByUniqueId() ;
	}
}
if ( $myCustomerCart->Status == 5) {
	printf( "FEHLER: Dieser CustomerCart ist bereits verschickt ! <br/>\n") ;
	die() ;
}

$finalMode	=	true ;

$myCustomerCartDoc	=	new CustomerCartDoc($myCustomerCart->CustomerCartNo) ;
$myCustomerCartDoc->createPDF( '', '', '') ;
$pdfName	=	$myAppConfig->path->Archive . "CustomerCart/" . $myCustomerCart->CustomerCartNo . ".pdf" ;

error_log( "prnMZ.php::main: pdf-name '" . $pdfName . "' ") ;
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
