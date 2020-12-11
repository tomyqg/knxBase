<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "Pay.php") ;
error_log( "From: '" . $_SERVER['REMOTE_ADDR'] . "' '" . $_SERVER['HTTP_USER_AGENT'] . "' ") ;
error_log( "URI: " . $_SERVER['REQUEST_URI']) ;
EISSCoreObject::dumpGet() ;
EISSCoreObject::dumpPost() ;
$myGiroPay	=	new Pay_GiroPay() ;
$myConfig	=	$myGiroPay->getConfig() ;
?>
