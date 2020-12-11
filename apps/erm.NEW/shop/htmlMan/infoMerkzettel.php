<?php 

//
// exec_mzzeigen
//
// performs:
//	some basic connectivity tests
//
require_once( "option.php") ;
require_once( "mzlibrary.inc.php") ;
error_log( "Hello, world") ;
//
// determine my own script name
//

$baseName	=	$_GET['ziel'] ;

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

if ( isset( $_POST['_action'])) {
	if ( strcmp( $_POST['_action'],"updatemzposten") == 0) {
		$myMerkzettelPosten	=	new MerkzettelPosten() ;
		$myMerkzettelPosten->Id	=	$_POST['_HId'] ;
		$newQty	=	intval( $_POST['_IQty']) ;
		$myMerkzettelPosten->fetchFromDbById() ;
		$myMerkzettel->GesamtPreis	-=	$myMerkzettelPosten->GesamtPreis ;
		if ( $newQty == 0) {
			if ( $myMerkzettelPosten->removeFromDb() == 0) {
				if ( $myMerkzettel->Positionen >= 1) {
					$myMerkzettel->Positionen	-=	1 ;
					$myMerkzettel->updateInDb() ;
				}
			}
		} else {
			$myMerkzettelPosten->fetchFromDbById() ;
			if ( $myMerkzettelPosten->_valid) {
				$myArtikel	=	new Artikel( $myMerkzettelPosten->ArtikelNr) ;
				$myArtikel->fetchFromDb() ;
				$myVKPreisCache	=	new VKPreisCache() ;
				$myVKPreisCache->ArtikelNr	=	$myArtikel->ArtikelNr ;
				$myVKPreisCache->MengeProVPE	=	$myMerkzettelPosten->MengeProVPE ;
				$myVKPreisCache->fetchFromDbWhere( "WHERE ArtikelNr = '$myVKPreisCache->ArtikelNr' AND MengeProVPE=$myVKPreisCache->MengeProVPE ") ;
				$myMerkzettelPosten->Menge	=	$newQty ;
				$myMerkzettelPosten->Preis	=	round( $myMerkzettelPosten->RefPreis * ( 1 - ( $myVKPreisCache->Rabatt + 0.000001357) + $myVKPreisCache->Rabatt / sqrt( $myMerkzettelPosten->Menge)), 2) ;
				$myMerkzettelPosten->GesamtPreis	=	$newQty * $myMerkzettelPosten->Preis ;
				$myMerkzettelPosten->updateInDb() ;
			} else
				echo "Anzahl Resultate stimmt nicht ... <br/>\n" ;
		}
		$myMerkzettel->_updateHdlg() ;
	} else if ( strcmp( $_POST['_action'], "activateMZ") == 0) {
		$myMerkzettel->MerkzettelUniqueId	=	$_POST['_IMerkzettelUniqueId'] ;
		$myMerkzettel->fetchFromDbByUniqueId() ;
		if ( $myMerkzettel->_valid) {
//			$myMerkzettel->Status	=	0 ;			// Entwertung der Merkzettels ungueltig machen
			$myMerkzettel->updateInDb() ;
			$mySession->MerkzettelUniqueId	=	$myMerkzettel->MerkzettelUniqueId ;
			$mySession->MerkzettelNr	=	$myMerkzettel->MerkzettelNr ;
			$mySession->updateInDb() ;
			if ( $mySession->_valid) {
				$actionResult	=	410 ;			// Kundekontaktdaten erfolgreich upgedatet
			} else {
				$actionResult	=	412 ;			// Kundekontaktdaten erfolgreich upgedatet
			}
		} else {
			$actionResult	=	411 ;			// Kundekontaktdaten NICHT erfolgreich upgedatet
		}

	} else if ( $_POST['_action'] == "deleteMZ") {
		$myMerkzettel->KundeNr	=	"ENTWERTET" ;
		$myMerkzettel->Status	=	9 ;
		$myMerkzettel->updateInDb() ;
		$mySession->MerkzettelUniqueId	=	"" ;
		$mySession->MerkzettelNr	=	"" ;
		$mySession->updateInDb() ;
	}
}

zeigeMerkzettelSchritt( 0) ;

echo "<h1>".FTr::tr( "Wishlist")."</h1>" ;

if ( $myKunde->_valid) {
	zeigeKunde( 1, $myKunde, $myKundeKontakt) ;
} else {
	echo FTr::tr( "At this very moment you are not logged in as customer.") . "<br/>" ;
	echo FTr::tr( "If you would like to login or register as new customer you may do this ") ;
	writeURL( FTr::tr( "&nbsp;here"), "/AllgemeineDaten.php", "") ;
	echo "<br/>" ;
}

if ( $myMerkzettel->_valid && ( $myMerkzettel->Status == 0 || $myMerkzettel->Status == 1 || $myMerkzettel->Status == 7)) {
	zeigeMerkzettel( 0, $myMerkzettel) ;
	zeigeMZFunktionen( 0, $myKunde) ;
} else {
	echo FTr::tr( "At this very moment there is no valid wishlist.") ;
	echo "<br/>" ;
}

?>
