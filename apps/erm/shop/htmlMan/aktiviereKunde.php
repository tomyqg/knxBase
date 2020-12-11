<?php 

//
// exec_mzzeigen
//
// performs:
//	some basic connectivity tests
//

//
// determine my own script name
//
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$baseName	=	$_GET['ziel'] ;
$myConfig	=	EISSCoreObject::__getConfig() ;

//
// see if a customer is logging in
// IF "KundeNr" and "Password" are present, somebody tries to log in
//

//
// process pending action, indicated by $_POST['action'] being defined
// this can be either of the following:
//	removemzposten	==>	remove an item from the 'merkzettel'
//	updatemzposten	==>	update an item on the 'merkzettel', typically this would be quantity
//

if ( isset( $_GET['key'])) {
	$myKunde->activateKunde( $_GET['key']) ;
	if ( $myKunde->_valid) {
		echo "<h3>" . FTr::tr( "ACTIVATION OK HEADER") . "</h3>" ;
		echo FTr::tr( "ACTIVATION OK BODY", array( "%s:".$myConfig->base->fullSiteName)) ;
	} else {
		echo "<h3>" . FTr::tr( "ACTIVATION NO OK HEADER") . "</h3>" ;
		echo FTr::tr( "ACTIVATION NOT OK BODY", array( "%s:".$myConfig->base->fullSiteName)) ;
	}
}
?>
