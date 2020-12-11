<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "validateEMail.inc.php") ;

if ( $_POST['_ITypeCust'] == "0") {
	$_POST['_IFirmaName1']	=	$_POST['_IB2CFirmaName1'] ;
	$_POST['_IFirmaName2']	=	$_POST['_IB2CFirmaName2'] ;
	$_POST['_IStrasse']	=	$_POST['_IB2CStrasse'] ;
	$_POST['_IHausnummer']	=	$_POST['_IB2CHausnummer'] ;
	$_POST['_IPLZ']	=	$_POST['_IB2CPLZ'] ;
	$_POST['_IOrt']	=	$_POST['_IB2COrt'] ;
	$_POST['_ITelefon']	=	$_POST['_IB2CTelefon'] ;
	$_POST['_IFAX']	=	$_POST['_IB2CFAX'] ;
	$_POST['_IMobil']	=	$_POST['_IB2CMobil'] ;
	$_POST['_IIAnrede']	=	$_POST['_IB2CAnrede'] ;
	$_POST['_IIName']	=	$_POST['_IB2CFirmaName1'] ;
	$_POST['_IIVorname']	=	$_POST['_IB2CFirmaName2'] ;
	$_POST['_IITelefon']	=	$_POST['_IB2CTelefon'] ;
	$_POST['_IIFAX']	=	$_POST['_IB2CFAX'] ;
	$_POST['_IIMobil']	=	$_POST['_IB2CMobil'] ;
	$_POST['_IIeMail']	=	$_POST['_IB2CeMail'] ;
	$_POST['_IIIeMail']	=	$_POST['_IB2CeMailV'] ;
} else {
}
$_POST['_IFirmaName3']	=	"" ;
	$myKunde	=	new Kunde() ;
	$myKunde->eMail	=	$_POST['_IIeMail'] ;
	error_log( "reg_kunde.php: E-Mail := '" . $myKunde->eMail."' ") ;
	$myKunde->_valid	=	true ;			// kleiner "miss-use"
	if ( strlen( $_POST['_IFirmaName1']) < 3) {
		$myKunde->_valid	=	false ;
		$neuKundeFehler	=	11 ;			// kleiner "miss-use"
	}
	if ( strlen( $_POST['_IStrasse']) < 3) {
		$myKunde->_valid	=	false ;
		$neuKundeFehler	=	12 ;			// kleiner "miss-use"
	}
	if ( strlen( $_POST['_IPLZ']) < 3) {
		$myKunde->_valid	=	false ;
		$neuKundeFehler	=	13 ;			// kleiner "miss-use"
	}
	if ( strlen( $_POST['_IOrt']) < 3) {
		$myKunde->_valid	=	false ;
		$neuKundeFehler	=	14 ;			// kleiner "miss-use"
	}
	if ( strlen( $_POST['_IIName']) < 3) {
		$myKunde->_valid	=	false ;
		$neuKundeFehler	=	21 ;			// kleiner "miss-use"
	}
	if ( strcmp( $_POST['_IIeMail'], $_POST['_IIIeMail']) != 0) {
		$myKunde->_valid	=	false ;
		$neuKundeFehler	=	4 ;			// kleiner "miss-use"
	} else if ( ! validEmail( $myKunde->eMail)) {
		$myKunde->_valid	=	false ;
		$neuKundeFehler	=	1 ;			// kleiner "miss-use"
	} else if ( $_POST['_ITypeCust'] == 1 AND strlen( $_POST['_IUStId']) != 11) {
//		$myKunde->_valid	=	false ;
//		$neuKundeFehler	=	31 ;			// kleiner "miss-use"
	} else {
		$dummyKunde	=	new Kunde() ;
		$dummyKunde->eMail	=	$myKunde->eMail ;
		$dummyKunde->fetchFromDbByEmail() ;
		if ( $dummyKunde->_valid) {
			$myKunde->_valid	=	false ;
			$neuKundeFehler	=	5 ;			// kleiner "miss-use"
		}
		$dummyKundeKontakt	=	new KundeKontakt() ;
		$dummyKundeKontakt->eMail	=	$myKunde->eMail ;
		$dummyKundeKontakt->fetchFromDbByEmail() ;
		if ( $dummyKundeKontakt->_valid) {
			$myKunde->_valid	=	false ;
			$neuKundeFehler	=	5 ;			// kleiner "miss-use"
		}
		if ( $neuKundeFehler == 5) {
			if ( strpos( $myKunde->eMail, "wimtecc") !== false || strpos( $myKundeKontakt->eMail, "wimtecc") !== false) {
				$neuKundeFehler	=	0 ;
				$myKunde->_valid	=	true ;
			}
		}
	}
	if ( $myKunde->_valid) {
		$myKunde->newKunde( "950000", "999999") ;
		$myKunde->fetchForNew() ;
		$myKunde->eMail	=	$_POST['_IIeMail'] ;
		$myKunde->updateInDb() ;
		if ( $myKunde->_valid) {
//			echo "===================> Kontakt wird registriert..: " . $myKunde->KundeNr . "<br /> \n" ;
			$myKundeKontakt	=	new KundeKontakt() ;
			$myKundeKontakt->KundeNr	=	$myKunde->KundeNr ;
			$myKundeKontakt->Anrede	=	$_POST['_IIAnrede'] ;
			$myKundeKontakt->Titel	=	$_POST['_IITitel'] ;
			$myKundeKontakt->Vorname	=	$_POST['_IIVorname'] ;
			$myKundeKontakt->Name	=	$_POST['_IIName'] ;
			$myKundeKontakt->Telefon	=	$_POST['_IITelefon'] ;
			$myKundeKontakt->FAX	=	$_POST['_IIFAX'] ;
			$myKundeKontakt->Mobil	=	$_POST['_IIMobil'] ;
			$myKundeKontakt->eMail	=	$_POST['_IIeMail'] ;
//			$myKundeKontakt->getKundeKontaktNr() ;
			$myKundeKontakt->KundeKontaktNr	=	"000" ;		// Default Kundenkontakt !!!
			$myKundeKontakt->storeInDb() ;
//			echo "===================> KontaktKontakt wird registriert..: " . $myKundeKontakt->KundeKontaktNr . "<br /> \n" ;
			if ( $myKundeKontakt->_valid) {
				echo "<h3>".FTr::tr( "REGISTRATION OK HEADER")."</h3>" ;
				echo FTr::tr( "REGISTRATION OK BODY") ;
				if ( 0 == 1) {
					$myKunde->sendKundeNr() ;
					$myKundeKontakt->sendPassword( "") ;
				} else {
					$myKunde->activateNoKey() ;
					$myKundeKontakt->sendPassword( "wCustNo") ;
				}

				$neuKunde	=	true ;
				$oFile	=	fopen( $myConfig->path->Archive."XML/down/Cust".$myKunde->KundeNr.".xml", "w+") ;
				fwrite( $oFile, "<Kundenregistrierung>\n") ;
				$buffer	=	$myKunde->getXML() ;
				fwrite( $oFile, $buffer) ;
				$buffer	=	$myKundeKontakt->getXML() ;
				fwrite( $oFile, $buffer) ;
				fwrite( $oFile, "</Kundenregistrierung>\n") ;
				fclose( $oFile) ;
			} else {
				echo "<h3>".FTr::tr( "REGISTRATION NOT OK HEADER")."</h3>" ;
				echo FTr::tr( "REGISTRATION NOT OK BODY") ;
				$myKunde->_valid	=	false ;
				$neuKundeFehler	=	3 ;
			}
			//
			// IF there's a valid 'Session'
			// 	we need to update this session
			//
			if ( $mySession->_valid) {
				$mySession->KundeNr	=	$myKunde->KundeNr ;
				$mySession->KundeKontaktNr	=	$myKundeKontakt->KundeKontaktNr ;
				$mySession->KundeNr	=	"" ;
				$mySession->KundeKontaktNr	=	"" ;
				$mySession->updateInDb() ;
			}
			//
			// IF there's a valid 'Merkzettel'
			// 	we need to update this Merkzettel
			//
			if ( $myMerkzettel->_valid) {
				$myMerkzettel->KundeNr	=	$myKunde->KundeNr ;
				$myMerkzettel->KundeKontaktNr	=	$myKundeKontakt->KundeKontaktNr ;
//				$myMerkzettel->KundeNr	=	"" ;
//				$myMerkzettel->KundeKontaktNr	=	"" ;
				$myMerkzettel->updateInDb() ;
			}
		} else {
			echo "<h3>".FTr::tr( "REGISTRATION NOT OK HEADER")."</h3>" ;
			echo FTr::tr( "REGISTRATION NOT OK BODY") ;
			$myKunde->_valid	=	false ;
			$neuKundeFehler	=	2 ;
		}
	}
?>
