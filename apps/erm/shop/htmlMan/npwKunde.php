<?php 

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;

//
// get all "kunde" related data
//

$myKunde	=	new Kunde() ;
$myKundeKontakt	=	new KundeKontakt() ;
if ( isset( $_POST['_IKundeNr'])) {
	$myKundeKontakt->setKundeKontaktNr( $_POST['_IKundeNr'], "000") ;
	$myKundeKontakt->sendPassword( "New") ;
	if ( $myKundeKontakt->_status == 0) {
		echo "<h3>".FTr::tr( "New password has been sent by E-Mail.")."</h3><br/>" ;
	} else {
		echo FTr::tr( "Invalid customer number. Please check your customer no. and try again.")."<br/>" ;
	}
	$myKunde->clear() ;
}

?>
