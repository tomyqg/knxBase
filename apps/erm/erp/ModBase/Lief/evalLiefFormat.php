<?php
/**
 * 
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;

//session_start() ;

$myConfig	=	EISSCoreObject::__getConfig() ;

FDbg::dumpL( 0x00000001, "action_LiefFormat:") ;

try {

	$_lief	=	new Lief( $_POST['_ILiefNr']) ;
	$_lief->PricesValidFrom	=	$_POST['_IGueltigVon'] ;
	$_lief->PricesValidTo	=	$_POST['_IGueltigBis'] ;
	$_lief->updateColInDb( "PricesValidFrom") ;	
	$_lief->updateColInDb( "PricesValidTo") ;	
	/**
	 * alle erforderlichen Variablen, entweder aus _POST oder aus _SESSION holen
	 */
	$fullFilename	=	$myConfig->path->Archive . "Lief/" . actionManageVar( "_IFilename") ;
	$maxCount		=	intval( actionManageVar( "_IMaxCount")) ;
	$startLine		=	intval( actionManageVar( "_IStartLine")) ;
	$endLine		=	intval( actionManageVar( "_IEndLine")) ;
	$myArtNrCol		=	intval( actionManageVar( "_IArtNrCol")) ;
	$defCurr		=	actionManageVar( "_IDefCurr") ;
	$defMenge		=	intval( actionManageVar( "_IDefMenge")) ;
	$defHKRabKlasse	=	actionManageVar( "_IDefHKRabKlasse") ;
	$defMengeProVPE	=	actionManageVar( "_IDefMengeProVPE") ;
	$defMengeFuerPreis	=	actionManageVar( "_IDefMengeFuerPreis") ;
	$nrFormat		=	actionManageVar( "_INrFormat") ;
	$gueltigVon		=	actionManageVar( "_IGueltigVon") ;
	$gueltigBis		=	actionManageVar( "_IGueltigBis") ;
	$myLiefNr		=	actionManageVar( "_ILiefNr") ;
	$genEKPreisR	=	intval( actionManageVar( "_IGenEKPreisR")) ;

	/**
	 * Datei oeffnen
	 */
	$myCSVFile	=	fopen( $fullFilename, "r") ;

	/**
	 * erforderliche Objekte instanziieren
	 */
	$testArtikelEKPreis	=	new ArtikelEKPreis() ;

	$newArtikelEKPreis1	=	new ArtikelEKPreis() ;
	$newArtikelEKPreis1->LiefNr	=	$_lief->LiefNr ;
	$newArtikelEKPreis1->GueltigVon	=	$_SESSION['_IGueltigVon'] ;
	$newArtikelEKPreis1->GueltigBis	=	$_SESSION['_IGueltigBis'] ;
	$newArtikelEKPreis1->Waehrung	=	$defCurr ;

	$newArtikelEKPreis2	=	new ArtikelEKPreis() ;
	$newArtikelEKPreis2->LiefNr	=	$_lief->LiefNr ;
	$newArtikelEKPreis2->GueltigVon	=	$_SESSION['_IGueltigVon'] ;
	$newArtikelEKPreis2->GueltigBis	=	$_SESSION['_IGueltigBis'] ;
	$newArtikelEKPreis2->Waehrung	=	$defCurr ;

	$newArtikelEKPreis3	=	new ArtikelEKPreis() ;
	$newArtikelEKPreis3->LiefNr	=	$_lief->LiefNr ;
	$newArtikelEKPreis3->GueltigVon	=	$_SESSION['_IGueltigVon'] ;
	$newArtikelEKPreis3->GueltigBis	=	$_SESSION['_IGueltigBis'] ;
	$newArtikelEKPreis3->Waehrung	=	$defCurr ;

	$newArtikelEKPreis4	=	new ArtikelEKPreis() ;
	$newArtikelEKPreis4->LiefNr	=	$_lief->LiefNr ;
	$newArtikelEKPreis4->GueltigVon	=	$_SESSION['_IGueltigVon'] ;
	$newArtikelEKPreis4->GueltigBis	=	$_SESSION['_IGueltigBis'] ;
	$newArtikelEKPreis4->Waehrung	=	$defCurr ;

	$myLiefRabatt	=	new LiefRabatt() ;

	$myLiefListe	=	new LiefListe() ;

	$myEKPreisR	=	new EKPreisR() ;

	/**
	 * flags setzen
	 */
	$neueListe	=	true ;
	$myLiefListe->fetchFromDbWhere( "WHERE LiefNr = '" . $_lief->LiefNr . "' " .
										"AND GueltigVon = '" . $gueltigVon . "' " .
										"AND GueltigBis = '" . $gueltigBis . "' "
									) ;
	if ( $myLiefListe->_valid == 1) {
		FDbg::dumpL( 0x00000001, "action_LiefFormat::evaluate: Liste fuer diesen Zeitraum existiert bereits ") ;
		$neueListe	=	false ;
		$myLiefListe->LastUpdate	=	$myConfig->today() ;
		$myLiefListe->updateInDb() ;
	} else {
		$myCO	=	new EISSCoreObject() ;
		FDbg::dumpL( 0x00000001, "action_LiefFormat::evaluate: Dies wird eine neue Liste fuer diesen Zeitraum ") ;
		$myLiefListe->LiefNr	=	$_lief->LiefNr ;
		$myLiefListe->Eingelesen	=	$myConfig->today() ;
		$myLiefListe->GueltigVon	=	$gueltigVon ;
		$myLiefListe->GueltigBis	=	$gueltigBis ;
		$myLiefListe->storeInDb() ;
	}

	/**
	 * Zeilenzaehler zuruecksetzen
	 */
	$lineCnt	=	1 ;

	/**
	 * die CSV Datei zeilenweise lesen ...
	 */
	while ( ( $myFields = fgetcsv( $myCSVFile)) !== FALSE && ( $lineCnt <= $endLine || $endLine <= 0)) {
		if ( $lineCnt >= intval( $startLine)) {
			/**
			 * Die folgenden Attribute muessen geloescht werde
			 */
			$myRabStaffel	=	$defHKRabKlasse ;
			$newArtikelEKPreis1->Menge	=	0 ;
			$newArtikelEKPreis2->Menge	=	0 ;
			$newArtikelEKPreis3->Menge	=	0 ;
			$newArtikelEKPreis4->Menge	=	0 ;
			$newArtikelEKPreis1->MengeProVPE	=	$defMengeProVPE ;
			$newArtikelEKPreis2->MengeProVPE	=	$defMengeProVPE ;
			$newArtikelEKPreis3->MengeProVPE	=	$defMengeProVPE ;
			$newArtikelEKPreis4->MengeProVPE	=	$defMengeProVPE ;
			$newArtikelEKPreis1->MengeFuerPreis	=	$defMengeFuerPreis ;
			$newArtikelEKPreis2->MengeFuerPreis	=	$defMengeFuerPreis ;
			$newArtikelEKPreis3->MengeFuerPreis	=	$defMengeFuerPreis ;
			$newArtikelEKPreis4->MengeFuerPreis	=	$defMengeFuerPreis ;
			$newArtikelEKPreis1->Preis	=	0 ;
			$newArtikelEKPreis2->Preis	=	0 ;
			$newArtikelEKPreis3->Preis	=	0 ;
			$newArtikelEKPreis4->Preis	=	0 ;
			$newArtikelEKPreis1->LiefVKPreis	=	0 ;
			$newArtikelEKPreis2->LiefVKPreis	=	0 ;
			$newArtikelEKPreis3->LiefVKPreis	=	0 ;
			$newArtikelEKPreis4->LiefVKPreis	=	0 ;
			$newArtikelEKPreis1->MKF	=	1 ;
			$newArtikelEKPreis2->MKF	=	1 ;
			$newArtikelEKPreis3->MKF	=	1 ;
			$newArtikelEKPreis4->MKF	=	1 ;
			$newArtikelEKPreis1->Marge	=	1 ;
			$newArtikelEKPreis2->Marge	=	1 ;
			$newArtikelEKPreis3->Marge	=	1 ;
			$newArtikelEKPreis4->Marge	=	1 ;
			
			/**
			 *
			 */
			$colCnt	=	0 ;
			$mySpecArtNr	=	$myFields[$myArtNrCol] ;
			if ( strlen( $mySpecArtNr) >= 2) {
				foreach ( $myFields AS $value) {
					$colName	=	sprintf( "_IColName_%s_%03d", $_lief->LiefNr, $colCnt) ;
					$colSplit	=	sprintf( "_IColSplit_%s_%03d", $_lief->LiefNr, $colCnt) ;
					$colSplitChars	=	sprintf( "_IColSplitChars_%s_%03d", $_lief->LiefNr, $colCnt) ;
					$colFieldAction	=	sprintf( "_IColFieldAction_%s_%03d", $_lief->LiefNr, $colCnt) ;
					if ( intval( $_SESSION[ $colSplit]) == Opt::Split && $lineCnt >= intval( $startLine)) {
						$parts	=	explode( $_SESSION[ $colSplitChars], $value) ;
						if ( ! isset( $parts[1]))
							$parts[1]	=	"1" ;
						switch ( intval( $_SESSION[ $colFieldAction])) {
						case	Opt::ActionMul:
							$value	=	sprintf( "%d", getInt( $parts[0]) * getInt( $parts[1])) ;
							break ;
						case	Opt::ActionLeft:
							$value	=	$parts[0] ;
							break ;
						case	Opt::ActionRight:
							$value	=	$parts[1] ;
							break ;
						default	:
							break ;
						}
					}
					switch ( intval( $_SESSION[ $colName])) {
					case	Opt::LiefArtNr	:
						$myLiefArtNr	=	$value ;
						$newArtikelEKPreis1->LiefArtNr	=	$value ;
						$newArtikelEKPreis2->LiefArtNr	=	$value ;
						$newArtikelEKPreis3->LiefArtNr	=	$value ;
						$newArtikelEKPreis4->LiefArtNr	=	$value ;
						break ;
					case	Opt::LiefArtNrAlt	:
						$newArtikelEKPreis1->LiefArtNrAlt	=	$value ;
						$newArtikelEKPreis2->LiefArtNrAlt	=	$value ;
						$newArtikelEKPreis3->LiefArtNrAlt	=	$value ;
						$newArtikelEKPreis4->LiefArtNrAlt	=	$value ;
						break ;
					case	Opt::ArtBez1	:
						$newArtikelEKPreis1->LiefArtText	=	$value ;
						$newArtikelEKPreis2->LiefArtText	=	$value ;
						$newArtikelEKPreis3->LiefArtText	=	$value ;
						$newArtikelEKPreis4->LiefArtText	=	$value ;
						break ;
					case	Opt::MengeFuerPreis	:
						$newArtikelEKPreis1->MengeFuerPreis	=	getInt( $value) ;
						$newArtikelEKPreis2->MengeFuerPreis	=	getInt( $value) ;
						$newArtikelEKPreis3->MengeFuerPreis	=	getInt( $value) ;
						$newArtikelEKPreis4->MengeFuerPreis	=	getInt( $value) ;
						break ;
					case	Opt::MengeProVPE	:
						$newArtikelEKPreis1->MengeProVPE	=	getInt( $value) ;
						$newArtikelEKPreis2->MengeProVPE	=	getInt( $value) ;
						$newArtikelEKPreis3->MengeProVPE	=	getInt( $value) ;
						$newArtikelEKPreis4->MengeProVPE	=	getInt( $value) ;
						break ;
					case	Opt::EKCurr	:
						$newArtikelEKPreis1->Waehrung	=	$value ;
						$newArtikelEKPreis2->Waehrung	=	$value ;
						$newArtikelEKPreis3->Waehrung	=	$value ;
						$newArtikelEKPreis4->Waehrung	=	$value ;
						break ;
					case	Opt::OwnVK	:
						$newArtikelEKPreis1->OwnVKPreis	=	getFloat( $value, ",") ;
						$newArtikelEKPreis2->OwnVKPreis	=	getFloat( $value, ",") ;
						$newArtikelEKPreis3->OwnVKPreis	=	getFloat( $value, ",") ;
						$newArtikelEKPreis4->OwnVKPreis	=	getFloat( $value, ",") ;
						break ;
					case	Opt::Menge1	:
						$newArtikelEKPreis1->Menge	=	getInt( $value) ;
						break ;
					case	Opt::Menge2	:
						$newArtikelEKPreis2->Menge	=	getInt( $value) ;
						break ;
					case	Opt::Menge3	:
						$newArtikelEKPreis3->Menge	=	getInt( $value) ;
						break ;
					case	Opt::Menge4	:
						$newArtikelEKPreis4->Menge	=	getInt( $value) ;
						break ;
					case	Opt::Preis1	:
						$newArtikelEKPreis1->Preis	=	getFloat( $value, ",") ;
						break ;
					case	Opt::Preis2	:
						$newArtikelEKPreis2->Preis	=	getFloat( $value, ",") ;
						break ;
					case	Opt::Preis3	:
						$newArtikelEKPreis3->Preis	=	getFloat( $value, ",") ;
						break ;
					case	Opt::Preis4	:
						$newArtikelEKPreis4->Preis	=	getFloat( $value, ",") ;
						break ;
					case	Opt::LiefVKPreis1	:
						$newArtikelEKPreis1->LiefVKPreis	=	getFloat( $value, ",") ;
						break ;
					case	Opt::LiefVKPreis2	:
						$newArtikelEKPreis2->LiefVKPreis	=	getFloat( $value, ",") ;
						break ;
					case	Opt::LiefVKPreis3	:
						$newArtikelEKPreis3->LiefVKPreis	=	getFloat( $value, ",") ;
						break ;
					case	Opt::LiefVKPreis4	:
						$newArtikelEKPreis4->LiefVKPreis	=	getFloat( $value, ",") ;
						break ;
					case	Opt::RabStaffel	:
						if ( $value != "") {
							$myRabStaffel	=	$value ;
						}
						break ;
					default	:
						break ;
					}
					$colCnt++ ;
				}
				reset( $myFields) ;
		
				/**
				 *
				 */
				$myLiefRabatt->fetchFromDbWhere( "WHERE LiefNr = '" . $newArtikelEKPreis1->LiefNr . "' AND HKRabKlasse = '" . $myRabStaffel . "' ") ;
				$myRabFakt	=	( 100.0 - $myLiefRabatt->Rabatt ) / 100.0 ;
		
				/**
				 *
				 */
				if ( $newArtikelEKPreis1->Menge > 0 || $defMenge > 0) {
					$newArtikelEKPreis1->Waehrung	=	$defCurr ;
					$newArtikelEKPreis1->HKRabKlasse	=	$myRabStaffel ;
					if ( $newArtikelEKPreis1->Menge == 0) {
						$newArtikelEKPreis1->Menge	=	$defMenge ;
						$newArtikelEKPreis1->MengeProVPE	=	$defMenge ;
					}
					if ( $newArtikelEKPreis1->MengeProVPE > 0 and $newArtikelEKPreis1->MengeFuerPreis == 0) {
						$newArtikelEKPreis1->MengeFuerPreis	=	$newArtikelEKPreis1->MengeProVPE ;
					} else if ( $newArtikelEKPreis1->MengeProVPE == 0 and $newArtikelEKPreis1->MengeFuerPreis > 0) {
						$newArtikelEKPreis1->MengeProVPE	=	$newArtikelEKPreis1->MengeFuerPreis ;
					}
					if ( $newArtikelEKPreis1->LiefVKPreis == 0) {
						$newArtikelEKPreis1->LiefVKPreis	=	$newArtikelEKPreis1->Preis / $myRabFakt ;
					} else if ( $newArtikelEKPreis1->Preis == 0) {
						$newArtikelEKPreis1->Preis	=	$newArtikelEKPreis1->LiefVKPreis * $myRabFakt ;
					}
					$crit	=	sprintf( "WHERE LiefNr = '%s' AND LiefArtNr = '%s' AND Menge = %d AND GueltigVon = '%s' AND GueltigBis = '%s' ",
											$newArtikelEKPreis1->LiefNr,
											$newArtikelEKPreis1->LiefArtNr,
											$newArtikelEKPreis1->Menge,
											$newArtikelEKPreis1->GueltigVon,
											$newArtikelEKPreis1->GueltigBis) ;
					$testArtikelEKPreis->fetchFromDbWhere ( $crit) ;
					if ( $testArtikelEKPreis->_valid == 1) {
						FDbg::dumpL( 0x00000010, "action_LiefFormat::evaluate::updating ") ;
						$newArtikelEKPreis1->Id	=	$testArtikelEKPreis->Id ;
						$newArtikelEKPreis1->updateInDb() ;
		
					} else {
						FDbg::dumpL( 0x00000010, "action_LiefFormat::evaluate::storing ") ;
						$newArtikelEKPreis1->storeInDb() ;
		
					}
				}
				if ( $newArtikelEKPreis2->Menge > 0) {
					$newArtikelEKPreis2->Waehrung	=	$defCurr ;
					$newArtikelEKPreis2->HKRabKlasse	=	$myRabStaffel ;
					if ( $newArtikelEKPreis1->Menge == 0) {
						$newArtikelEKPreis2->Menge	=	$defMenge ;
						$newArtikelEKPreis2->MengeProVPE	=	$defMenge ;
					}
					if ( $newArtikelEKPreis2->MengeProVPE > 0 and $newArtikelEKPreis2->MengeFuerPreis == 0) {
						$newArtikelEKPreis2->MengeFuerPreis	=	$newArtikelEKPreis2->MengeProVPE ;
					} else if ( $newArtikelEKPreis2->MengeProVPE == 0 and $newArtikelEKPreis2->MengeFuerPreis > 0) {
						$newArtikelEKPreis2->MengeProVPE	=	$newArtikelEKPreis2->MengeFuerPreis ;
					}
					if ( $newArtikelEKPreis2->LiefVKPreis == 0) {
						$newArtikelEKPreis2->LiefVKPreis	=	$newArtikelEKPreis2->Preis ;
					} else if ( $newArtikelEKPreis2->Preis == 0) {
						$newArtikelEKPreis2->Preis	=	$newArtikelEKPreis2->LiefVKPreis * $myRabFakt ;
					}
					$crit	=	sprintf( "WHERE LiefNr = '%s' AND LiefArtNr = '%s' AND Menge = %d AND GueltigVon = '%s' AND GueltigBis = '%s' ",
											$newArtikelEKPreis2->LiefNr,
											$newArtikelEKPreis2->LiefArtNr,
											$newArtikelEKPreis2->Menge,
											$newArtikelEKPreis2->GueltigVon,
											$newArtikelEKPreis2->GueltigBis) ;
					$testArtikelEKPreis->fetchFromDbWhere ( $crit) ;
					if ( $testArtikelEKPreis->_valid == 1) {
						$newArtikelEKPreis2->Id	=	$testArtikelEKPreis->Id ;
						$newArtikelEKPreis2->updateInDb() ;
					} else {
						$newArtikelEKPreis2->storeInDb() ;
					}
				}
				if ( $newArtikelEKPreis3->Menge > 0) {
					$newArtikelEKPreis3->Waehrung	=	$defCurr ;
					$newArtikelEKPreis3->HKRabKlasse	=	$myRabStaffel ;
					if ( $newArtikelEKPreis3->MengeProVPE > 0 and $newArtikelEKPreis3->MengeFuerPreis == 0) {
						$newArtikelEKPreis3->MengeFuerPreis	=	$newArtikelEKPreis3->MengeProVPE ;
					} else if ( $newArtikelEKPreis3->MengeProVPE == 0 and $newArtikelEKPreis3->MengeFuerPreis > 0) {
						$newArtikelEKPreis3->MengeProVPE	=	$newArtikelEKPreis3->MengeFuerPreis ;
					}
					if ( $newArtikelEKPreis3->LiefVKPreis == 0) {
						$newArtikelEKPreis3->LiefVKPreis	=	$newArtikelEKPreis3->Preis ;
					} else if ( $newArtikelEKPreis3->Preis == 0) {
						$newArtikelEKPreis3->Preis	=	$newArtikelEKPreis3->LiefVKPreis * $myRabFakt ;
					}
					$crit	=	sprintf( "WHERE LiefNr = '%s' AND LiefArtNr = '%s' AND Menge = %d AND GueltigVon = '%s' AND GueltigBis = '%s' ",
											$newArtikelEKPreis3->LiefNr,
											$newArtikelEKPreis3->LiefArtNr,
											$newArtikelEKPreis3->Menge,
											$newArtikelEKPreis3->GueltigVon,
											$newArtikelEKPreis3->GueltigBis) ;
					$testArtikelEKPreis->fetchFromDbWhere ( $crit) ;
					if ( $testArtikelEKPreis->_valid == 1) {
						$newArtikelEKPreis3->Id	=	$testArtikelEKPreis->Id ;
						$newArtikelEKPreis3->updateInDb() ;
					} else {
						$newArtikelEKPreis3->storeInDb() ;
					}
				}
				if ( $newArtikelEKPreis4->Menge > 0) {
					$newArtikelEKPreis4->Waehrung	=	$defCurr ;
					$newArtikelEKPreis4->HKRabKlasse	=	$myRabStaffel ;
					if ( $newArtikelEKPreis4->MengeProVPE > 0 and $newArtikelEKPreis4->MengeFuerPreis == 0) {
						$newArtikelEKPreis4->MengeFuerPreis	=	$newArtikelEKPreis4->MengeProVPE ;
					} else if ( $newArtikelEKPreis4->MengeProVPE == 0 and $newArtikelEKPreis4->MengeFuerPreis > 0) {
						$newArtikelEKPreis4->MengeProVPE	=	$newArtikelEKPreis4->MengeFuerPreis ;
					}
					if ( $newArtikelEKPreis4->LiefVKPreis == 0) {
						$newArtikelEKPreis4->LiefVKPreis	=	$newArtikelEKPreis4->Preis ;
					} else if ( $newArtikelEKPreis4->Preis == 0) {
						$newArtikelEKPreis4->Preis	=	$newArtikelEKPreis4->LiefVKPreis * $myRabFakt ;
					}
					$crit	=	sprintf( "WHERE LiefNr = '%s' AND LiefArtNr = '%s' AND Menge = %d AND GueltigVon = '%s' AND GueltigBis = '%s' ",
											$newArtikelEKPreis4->LiefNr,
											$newArtikelEKPreis4->LiefArtNr,
											$newArtikelEKPreis4->Menge,
											$newArtikelEKPreis4->GueltigVon,
											$newArtikelEKPreis4->GueltigBis) ;
					$testArtikelEKPreis->fetchFromDbWhere ( $crit) ;
					if ( $testArtikelEKPreis->_valid == 1) {
						$newArtikelEKPreis4->Id	=	$testArtikelEKPreis->Id ;
						$newArtikelEKPreis4->updateInDb() ;
					} else {
						$newArtikelEKPreis4->storeInDb() ;
					}
				}
				/**
				 *
				 */
				if ( $_lief->LiefPrefix != "" && $genEKPreisR == 1) {
					$myArtikelNr	=	$_lief->LiefPrefix . "." . str_replace( " ", "", $myLiefArtNr) ;
					$myEKPreisR->fetchFromDbWhere( "WHERE ArtikelNr = '" . $myArtikelNr . "' AND LiefNr = '" . $_lief->LiefNr . "' ") ;
					if ( ! $myEKPreisR->_valid) {
						echo "noch keine EKPreisR fuer " . $myArtikelNr . "<br/>" ;
						$myEKPreisR->ArtikelNr	=	$myArtikelNr ;
						$myEKPreisR->LiefNr	=	$_lief->LiefNr ;
						$myEKPreisR->LiefArtNr	=	$myLiefArtNr ;
						$myEKPreisR->KalkBasis	=	$newArtikelEKPreis1->Menge ;
						$myEKPreisR->LiefArtText	=	$newArtikelEKPreis1->LiefArtText ;
						$myEKPreisR->MKF	=	1 ;
						$myEKPreisR->Marge	=	1.0 ;
						$myEKPreisR->OrdMode	=	0 ;
						$myEKPreisR->MengeProVPE	=	$newArtikelEKPreis1->MengeProVPE ;
						$myEKPreisR->storeInDb() ;
					} else {
						echo "EKPreisR already exists<br/>" ;
					}
				} else {
					echo "no valid prefix or parameter not set (= 0)<br/>" ;
				}
			}
		}
		$lineCnt++ ;
	}
	fclose( $myCSVFile) ;

//	$_lief->recalcPrices() ;

} catch ( Exception $e) {
	echo "Exception: " . $e->getMessage() . "<br/>" ;
}

/**
 *
 */
function	actionManageVar( $_varname) {
	if ( isset( $_POST[ $_varname])) {
		$_SESSION[ $_varname]	=	$_POST[ $_varname] ;
	} else if ( isset( $_SESSION[ $_varname])) {
	} else {
		$_SESSION[ $_varname]	=	"VOID" ;
	}
	return $_SESSION[ $_varname] ;
}

?>
