#!/usr/bin/php5

<?php

/**
 *
 * job_checkKB.php
 *
 * Dieser Job prueft alle offenen Bestellungen (KdBest) auf kommisionierbarkeit.
 * Eine Bestellung ist kommissionierbar wenn alle Artikel in ausreichender Menge auf
 * Lager sind.
 * Dieser Job prueft ausschliesslich das Default Lager !
 *
 * Grober Abaluf ist wie folgt (Pseudo Code):
 *
 *	Fuer alle Bestellungen die nicht im Status "abgeschlossen/on-hold/storniert" sind
 *		Fuer alle Positionen dieser Bestellung
 *			Wenn Artikel kein DirektVersand-Artikel
 *				Wenn Lagerbestand[Defaultlager] > noch zu liefernde Menge
 *					"Lieferbare Positionen" ++
 *				Sonst
 *					"Nicht lieferbare Positionen" ++
 *			Sonst
 *				...
 *		Wenn "Nicht lieferbare Positionen" == 0 ( Auftrag ist vollstÃ¤ndig kommissionierbar )
 *			...
 *		Sonst
 *			
 */
require_once( "../phpinc/FDbg.php") ;
require_once( "../phpinc/FDb.php") ;

require_once( "../phpconfig/global.inc.php") ;
require_once( "../phpinc/option.inc.php") ;
require_once( "../phpinc/ArtikelBestand.php") ;
require_once( "../phpinc/Artikel.php") ;
require_once( "../phpinc/EKPreisR.php") ;
require_once( "../phpinc/ArtikelEKPreis.php") ;
require_once( "../phpinc/PLfBest.php") ;


/**
 *
 */
FDbg::setLevel( 0xffffffff) ;
FDbg::disable() ;
FDbg::enable() ;
FDbg::setHTMLMode() ;
FDbg::dumpL( 0, "job_createPLfBest: starting up") ;

/**
 *
 */

$actArtikelBestand	=	new ArtikelBestand() ;
$actArtikel	=	new Artikel() ;

/**
 *
 */
PLfBest::clean() ;

/**
 *	1. Schritt:
 *
 *	Alle Artikel die bestellt werden muessen in die Tabelle PLfBestPosten eintragen, und zwar mir der Menge in der
 *	diese Artikel benoetigt werden.
 */
$crit	=	"((Reserviert + (Mindestbestand/2)) - Lagerbestand - Bestellt) > 0 " ;

$posNr	=	10 ;
FDbg::dumpL( 0x00000001, "job_createPLfBest: ") ;
for ( $actArtikelBestand->firstFromDb( $crit) ;
		$actArtikelBestand->_valid == 1 ;
		$actArtikelBestand->nextFromDb()) {
	$actArtikel->setArtikelNr( $actArtikelBestand->ArtikelNr) ;
	FDbg::dumpL( 0x00000001, "job_createPLfBest: [Id=%7d] ArtikelNr: %s, '%s'",
								$actArtikelBestand->Id,
								$actArtikel->ArtikelNr,
								$actArtikel->ArtikelBez1) ;

	$bestellen	=	false ;
	if ( $actArtikel->ModeLfBest == 1) {
		$bestellen	=	true ;
	}
	/**
	 *
	 */
	if ( $bestellen) {
		$critCount	=	"FROM EKPreisR WHERE ArtikelNr = '" . $actArtikel->ArtikelNr . "' " ;
		$cntEKPreisR	=	FDb::getCount( $critCount) ;
		FDbg::dumpL( 0x00000100, "job_createPLfBest: Anzahl EKPreisR: %d",
									$cntEKPreisR) ;
		switch ( $cntEKPreisR) {
		case	0	:
			FDbg::dumpL( 0x00000200, "job_createPLfBest: Fuer Artikel Nr. %s ist kein EKPreisR verfuegbar", $actArtikel->ArtikelNr) ;
			break ;
		case	1	:
			$myEKPreisR	=	new EKPreisR() ;
			$myEKPreisR->firstFromDb( "ArtikelNr = '" . $actArtikel->ArtikelNr . "' ") ;
			if ( $myEKPreisR->_valid == 1) {
				$myArtikelEKPreis	=	new ArtikelEKPreis() ;
				$myArtikelEKPreis->LiefNr	=	$myEKPreisR->LiefNr ;
				$myArtikelEKPreis->LiefArtNr	=	$myEKPreisR->LiefArtNr ;
				$myArtikelEKPreis->firstFromDb() ;								// holt automatisch den aktuellsten EK-Preis fuer diesen Lieferanten !
				FDbg::dumpL( 0x00000200, "job_createPLfBest: Speichere Artikel in PLfBestPosten ") ;
				$newPLfBestPos	=	new PLfBestPosten() ;
				$newPLfBestPos->PosNr	=	$posNr ;
				$newPLfBestPos->PLfBestNr	=	$myEKPreisR->LiefNr ;
				$newPLfBestPos->ArtikelNr	=	$actArtikel->ArtikelNr ;
				$newPLfBestPos->LiefArtNr	=	$myEKPreisR->LiefArtNr ;
				$newPLfBestPos->Menge	=	(( $actArtikelBestand->Reserviert + ( $actArtikelBestand->Mindestbestand/2)) -
												$actArtikelBestand->Lagerbestand -
												$actArtikelBestand->Bestellt) ;
				$newPLfBestPos->Preis	=	$myArtikelEKPreis->Preis ;
				$newPLfBestPos->MengeProVPE	=	$myArtikelEKPreis->MengeProVPE ;
				$newPLfBestPos->GesamtPreis	=	$newPLfBestPos->Menge * $newPLfBestPos->Preis ;
				$newPLfBestPos->storeInDb() ;
			} else {
				FDbg::dumpL( 0x00000200, "job_createPLfBest: EKPreisR ist nicht gueltig ") ;
			}
			break ;
		default	:
			$myEKPreisR	=	new EKPreisR() ;
			$myEKPreisR->firstFromDb( "ArtikelNr = '" . $actArtikel->ArtikelNr . "' AND KalkBasis > 0 ") ;
			if ( $myEKPreisR->_valid == 1) {
				FDbg::dumpL( 0x00000200, "job_createPLfBest: Speichere Artikel in PLfBestPosten ") ;
				$newPLfBestPos	=	new PLfBestPosten() ;
				$newPLfBestPos->PosNr	=	$posNr ;
				$newPLfBestPos->PLfBestNr	=	$myEKPreisR->LiefNr ;
				$newPLfBestPos->ArtikelNr	=	$actArtikel->ArtikelNr ;
				$newPLfBestPos->LiefArtNr	=	$myEKPreisR->LiefArtNr ;
				$newPLfBestPos->Menge	=	(( $actArtikelBestand->Reserviert + ( $actArtikelBestand->Mindestbestand/2)) -
												$actArtikelBestand->Lagerbestand -
												$actArtikelBestand->Bestellt) ;
				$newPLfBestPos->storeInDb() ;
			} else {
				FDbg::dumpL( 0x00000200, "job_createPLfBest: EKPreisR ist nicht gueltig ") ;
			}
			break ;
		}
	}
	$posNr	+=	10 ;
}

/**
 *	2. Schritt:
 *
 *	Für alle Lieferanten die in der Tabelle PLfBestPosten angesprochen sind einen Eintrag in der Tabelle
 *	PLfBest erzeugen
 */
$query	=	"SELECT DISTINCT PLfBestNr FROM PLfBestPosten ORDER BY PLfBestNr " ;

try {
	$sqlResult	=	FDb::query( $query) ;
	while ($coreRow = mysql_fetch_assoc( $sqlResult)) {
		$newPLfBest	=	new PLfBest() ;
		$newPLfBest->PLfBestNr	=	$coreRow[ 'PLfBestNr'] ;
		$newPLfBest->LiefNr	=	$coreRow[ 'PLfBestNr'] ;
		$newPLfBest->Datum	=	today() ;
		$newPLfBest->storeInDb() ;
	}
} catch ( Exception $e) {
}
	
/**
 *
 */

/**
 *
 */
function	infoMail( $_rcvr, $_subject, $_text, $_htmlText, $_pdfFile) {

	$newMail	=	new mimeMail( "MODIS-GmbH <karl@modis-gmbh.eu>",
						$_rcvr,
						"MODIS GmbH <karl@modis-gmbh.eu>",
						$_subject,
						"") ;

	$myText	=	new mimeData( "multipart/alternative") ;
	
	if ( strlen( $_text) > 0) {
		$myText->addData( "text/plain", $_text) ;
	} else {
		$myText->addData( "text/plain", "Kein plain text, siehe HTML fuer weitere Daten ... \n") ;
	}
	if ( strlen( $_htmlText) > 0) {
		$myText->addData( "text/html", $_htmlText) ;
	} else {
		$myText->addData( "text/html", "<html><body>".$_text."</body></html>") ;
	}

	$myBody	=	new mimeData( "multipart/mixed") ;
	$myBody->addData( "multipart/mixed", $myText->getAll(), "", false) ;
	$myBody->addData( "application/pdf", $_pdfFile, "Mahnung.pdf", true) ;

	$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
	$newMail->send() ;

}

?>
