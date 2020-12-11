<script>
function enable( element) {
        if ( document.f2.AGBFrage[1].checked) {
                document.f2.anfrage.disabled    =       false ;
        } else {
                document.f2.anfrage.disabled    =       true ;
        }
}
</script>
<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "KdAnfDoc.php") ;
require_once( "mzlibrary.inc.php") ;

//
// determine 'how' we are called, can be any of:
//	'anfrage1.php'	if in fact the 'Kunde' is valid already we silently switch to 'bestellen2.php'-behaviour
//	'anfrage2.php'
//

$baseName	=	$_GET['ziel'] ;

//
// see if a customer is logging in
//

//
// High Level Function
//
//	IF called from

if ( isset( $_POST['_neuKunde'])) {
	include( "regKunde.php") ;
}

if ( $myKunde->_valid) {
	$step	=	$_POST["step"] ;
	if ( $step == 0) {
	} else if ( $step == 1) {
	} else if ( $step == 2) {
		if ( $myKunde->_valid) {
			zeigeKdAnfSchritt( 2) ;
			echo "<h2>".FTr::tr( "Submit enquiry")."</h2>" ;
			echo "<h3>".FTr::tr( "Customer Data")."</h3>" ;
			zeigeKunde( 2, $myKunde, $myKundeKontakt) ;
			echo "<h3>".FTr::tr( "Current enquiry")."</h3>" ;
			if ( $myMerkzettel->_valid) {
				zeigeMerkzettel( 2, $myMerkzettel) ;
				zeigeKdAnfFunktionen( 2, $myKunde) ;
			}
		} else {
			$nextFunction	=	"Anfrage.php" ;
			include( "frag_kunde.php") ;
		}
	} else if ( $step == 3) {
		if ( $myKunde->_valid) {
			zeigeKdAnfSchritt( 3) ;
			//
			if ( $myMerkzettel->_valid && ( $myMerkzettel->Status == 0 || $myMerkzettel->Status == 7)) {
			//
				require_once( "ArtikelBestand.php") ;
				require_once( "KdAnf.php") ;
				require_once( "genPosTables.inc.php") ;

				/**
				 * create new RfQ
				 */

				$myKdAnf	=	new KdAnf() ;
				$myKdAnf->newFromMZ( '', '', $myMerkzettel->MerkzettelNr) ;
				$myKdAnf->fetchFromDb() ;
				$myKdAnf->KdRefNr	=	$_POST['_IKdRefNr'] ;
				$myKdAnf->CustRem	=	"<div>" . $_POST['_ICustText'] . "</div>" ;
				$myKdAnf->updateInDb() ;
				$myKdAnf->updateHdlg() ;
				$myKdAnf->_restate( $myKdAnf->KdAnfNr) ;
				//
				echo "<h3>".FTr::tr( "RFQ SUBMISSION OK HEADER")."</h3>" ;
				echo FTr::tr( "RFQ SUBMISSION OK BODY") ;
				/**
				 *
				 */
				echo "<h3>".FTr::tr( "Customer Data")."</h3>" ;
				if ( $myKunde->_valid) {
					zeigeKunde( 2, $myKunde, $myKundeKontakt) ;
				}
				/**
				 *
				 */
				echo "<h3>".FTr::tr( "Current enquiry")."</h3>" ;
				if ( $myConfig->cuWishlist->autoCancel) {
					$myMerkzettel->Status	=	1 ;		// merkzettel "entwerten"
					$myMerkzettel->updateInDb() ;
					$mySession->MerkzettelNr	=	"" ;
					$mySession->MerkzettelUniqueId	=	"" ;
					$mySession->updateInDb() ;
				}
				/**
				 *
				 */
				zeigeKdAnf( 3, $myKdAnf) ;
				/**
				 *
				 */
				$myKdAnf->infoMailSales( "", $myKundeKontakt->eMail) ;
				/**
				 * PDF Auftragsbestaeigung generieren
				 */
				$myKdAnfDoc	=	new KdAnfDoc() ;
				$myKdAnfDoc->setKey( $myKdAnf->KdAnfNr) ;
				$myKdAnfDoc->genPDF( '', '', '') ;
				$pdfName	=	$myConfig->path->Archive . "KdAnf/" . $myKdAnf->KdAnfNr . ".pdf" ;

				$myKdAnf->infoMailCust( $pdfName, $myKundeKontakt->eMail) ;
				/**
				 *
				 */
				$oFile	=	fopen( $myConfig->path->Archive."XML/down/KdAnf".$myKdAnf->KdAnfNr.".xml", "w+") ;
				fwrite( $oFile, "<KdAnfPaket>\n") ;
				$buffer	=	$myKdAnf->getXML() ;
				fwrite( $oFile, $buffer) ;
				$myKdAnfPosten	=	new KdAnfPosten() ;
				$myKdAnfPosten->KdAnfNr	=	$myKdAnf->KdAnfNr ;
				for ( $myKdAnfPosten->_firstFromDb( "KdAnfNr='$myKdAnf->KdAnfNr' ORDER BY PosNr ") ;
							$myKdAnfPosten->_valid ;
							$myKdAnfPosten->_nextFromDb()) {
					$buffer	=	$myKdAnfPosten->getXML() ;
					fwrite( $oFile, $buffer) ;
				}
				fwrite( $oFile, "</KdAnfPaket>\n") ;
				fclose( $oFile) ;
				//
			} else {
				echo "<h3>".FTr::tr( "RFQ SUBMISSION NOT OK HEADER")."</h3>" ;
				echo FTr::tr( "RFQ SUBMISSION NOT OK BODY") ;
			}
		} else {
			$nextFunction	=	"anfrage2.php" ;
			include( "frag_kunde.php") ;
		}
	}
} else {
	$nextFunction	=	"anfrage2.php" ;
	$nextFunction	=	"/AllgemeineDaten.php" ;
	include( "frag_kunde.php") ;
}

?>
