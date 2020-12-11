<?php

/**
 * KdGutsDoc.php Application Level Class for printed version of KdGuts
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocRegLetter.php") ;
/**
 * KdGutsDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage KdGuts
 */
class	KdGutsDoc	extends BDocRegLetter {

	private	$myKdGuts ;
	private	$totalProMwstSatz	=	array() ;
	private	$mwstSatz	=	array( "A" => 19, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	private	$cellCharFmt ;
	private	$cellParaFmtLeft ;
	private	$cellParaFmtCenter ;
	private	$cellParaFmtRight ;
	public	$_valid	=	false ;
	
	/**
	 *
	 */
	function	__construct( $_formMode=true) {
		$this->formMode	=	$_formMode ;
	}
	
	function	setKey( $_kdGutsNr) {
		FDbg::get()->dump( "KdGutsDoc::__construct( _kdGutsNr)") ;
		$this->myKdGuts	=	new KdGuts( $_kdGutsNr) ;
		$this->_valid	=	$this->myKdGuts->_valid ;
		$this->lang	=	$this->myKdGuts->getKunde()->Sprache ;
	}

	/**
	 *
	 */
	function	createPDF( $_key, $_val, $_pdfName='') {
		$this->_createPDF( $_key, $_val, $_pdfName) ;
		$this->formMode	=	false ;
		$myPDFName	=	$this->path->Archive . "KdGuts/" . $this->myKdGuts->KdGutsNr . "_noForm.pdf" ;
		$this->_createPDF( $_key, $_val, $myPDFName, false) ;
		$myPDFName	=	$this->path->Archive . "KdGuts/" . $this->myKdGuts->KdGutsNr . "_noFormCopy.pdf" ;
		$this->_createPDF( $_key, $_val, $myPDFName, true) ;
		return $this->myKdGuts->getAsXML() ;
	}
	function	_createPDF( $_key, $_val, $_pdfName='') {
		$myColWidths	=	array( /*10,*/ 10, 23, 70, 10, 17, 17, 8) ;

		/**
		 * 
		 */
		$this->cellCharFmt	=	new BCharFmt() ;
		$this->cellCharFmt->setCharSize( 9) ;

		$this->cellParaFmtLeft	=	new BParaFmt() ;
		$this->cellParaFmtLeft->setCharFmt( $this->cellCharFmt) ;
		$this->cellParaFmtLeft->setAlignment( BParaFmt::alignLeft) ;
		
		$this->cellParaFmtCenter	=	new BParaFmt() ;
		$this->cellParaFmtCenter->setCharFmt( $this->cellCharFmt) ;
		$this->cellParaFmtCenter->setAlignment( BParaFmt::alignCenter) ;

		$this->cellParaFmtRight	=	new BParaFmt() ;
		$this->cellParaFmtRight->setCharFmt( $this->cellCharFmt) ;
		$this->cellParaFmtRight->setAlignment( BParaFmt::alignRight) ;
		
		/**
		 * 
		 */
		$myTable	=	new BTable() ;
		$myTable->addCol( new BCol( 10), true) ;
		$myTable->addCols( $myColWidths) ;
		
		/**
		 * setup the first table header line
		 */
		$this->psRowMain	=	new BRow( BRow::RTHeaderPS) ;
		$this->psRowMain->addCell( 0, new BCell( "Pos.", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Artikel Nr.", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Bezeichung", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Menge", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Einzel", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Gesamt", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "St.", $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRowMain) ;

		/**
		 * setup the second table header line
		 */
		$this->psRow	=	new BRow( BRow::RTHeaderPS) ;
		$this->psRow->addCell( 4, new BCell( "+/- %", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 5, new BCell( "Rabatt", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 6, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 7, new BCell( "Schl.", $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRow) ;
		
		/**
		 * Aufsetzten der Zeile fï¿½r "ï¿½bertrag von Seite ...:"
		 */
		$this->cfRow	=	new BRow( BRow::RTHeaderCF) ;
		$this->cfRow->addCell( 3, new BCell( "Ãœbertrag von Seite: %pp%", $this->cellParaFmtLeft)) ;
		$cellCarryFrom	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->cfRow->addCell( 6, $cellCarryFrom) ;
		$myTable->addRow( $this->cfRow) ;
		
		/**
		 * Aufsetzten der Zeile fï¿½r "Weitere Details:"
		 */
		$this->myKHRow	=	new BRow( BRow::RTDataIT) ;
		$cellArtKompHeader	=	new BCell( "Weitere Details zu diesem Artikel (Optionen/Komponenten):\n", $this->cellParaFmtLeft) ;
		$this->myKHRow->addCell( 3, $cellArtKompHeader) ;
		$this->myKHRow->disable() ;
		$myTable->addRow( $this->myKHRow) ;
		
		/**
		 * setup the first table data row
		 */
		$myRow	=	new BRow( BRow::RTDataIT) ;
		$this->cellPosNr	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellPosNr) ;
		$this->cellSubPosNr	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellSubPosNr) ;
		$this->cellArtikelNr	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArtikelNr) ;
		$this->cellArtikelBez1	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArtikelBez1) ;
		$this->cellMenge	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellMenge) ;
		$cellPreis	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellPreis) ;
		$cellGesamtPreis	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellGesamtPreis) ;
		$cellTaxKey	=	new BCell( "", $this->cellParaFmtCenter) ;
		$myRow->addCell( 0, $cellTaxKey) ;
		$myTable->addRow( $myRow) ;
		
		/**
		 * Aufsetzten der Zeile fï¿½r "ï¿½bertrag auf Seite ...:"
		 */
		$this->ctRow	=	new BRow( BRow::RTFooterCT) ;
		$this->ctRow->addCell( 3, new BCell( "†bertrag auf Seite: %np%", $this->cellParaFmtLeft)) ;
		$cellCarryTo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 6, $cellCarryTo) ;
		$myTable->addRow( $this->ctRow) ;
		
		/**
		 *
		 */
		BDocRegLetter::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252', $this->myKdGuts->getKunde()->FirmaName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252', $this->myKdGuts->getKunde()->FirmaName2)) ;
		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252', $this->myKdGuts->getKundeKontakt()->getAddrLine())) ;
		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252', $this->myKdGuts->getKunde()->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252', $this->myKdGuts->getKunde()->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252', $this->myKdGuts->getKunde()->getAddrCountry())) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Credit Note", null, $this->lang)), "") ;
		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Credit Note. no.", null, $this->lang).":"), $this->myKdGuts->KdGutsNr) ;
		$this->setInfo( 3, FTr::tr( "Date", null, $this->lang).":", $this->myKdGuts->Datum) ;
		$this->setInfo( 4, FTr::tr( "Customer no.", null, $this->lang).":", $this->myKdGuts->KundeNr . "/" . $this->myKdGuts->KundeKontaktNr) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, FTr::tr( "Customer", null, $this->lang).":", "") ;
		$this->setInfo( 8, FTr::tr( "Ref. no.", null, $this->lang).":", iconv( 'UTF-8', 'windows-1252', $this->myKdGuts->KdRefNr)) ;
		$this->setInfo( 9, FTr::tr( "Ref. date", null, $this->lang).":", $this->myKdGuts->KdRefDatum) ;

		$this->setRef( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Credit Note", null, $this->lang))) ;

		$this->begin() ;

		/**
		 *
		 */
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $this->myKdGuts->Prefix)) ;
		$this->skipMyLine() ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;

		$myKdGutsPosten	=	new KdGutsPosten( $this->myKdGuts->KdGutsNr) ;

		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		for ( $myKdGutsPosten->firstFromDb( "KdGutsNr", "Artikel", array(
																		"ArtikelBez1" => "var",
																		"ArtikelBez2" => "var",
																		"MengenText" => "var",
																		"MwstSatz" => "var",
																		"Comp" => "var"
																), "ArtikelNr") ;
				$myKdGutsPosten->_valid == 1 ;
				$myKdGutsPosten->nextFromDb()) {
			FDbg::get()->dumpL( 2, "Pos=%d, ArtikelNr=%s", $myKdGutsPosten->PosNr, $myKdGutsPosten->ArtikelNr) ;
			if ( $myKdGutsPosten->Menge != 0) {

				/**
				 * Artikel Nummer
				 */
				$this->cellArtikelNr->setData( $myKdGutsPosten->ArtikelNr) ;

				/**
				 * Artikel Bezeichnung zusammensetzen
				 */
				$myArtikelText	=	"" ;
				if ( strlen( $myKdGutsPosten->AddText) > 0) {
					$myArtikelText	.=	$myKdGutsPosten->AddText . "\n" ;
				}
				$myArtikelText	.=	iconv('UTF-8', 'windows-1252', $myKdGutsPosten->ArtikelBez1) ;
				if ( strlen( $myKdGutsPosten->ArtikelBez2) > 0) {
					$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myKdGutsPosten->ArtikelBez2) ;	
				}
				if ( strlen( $myKdGutsPosten->MengenText) > 0) {
					$myArtikelText	.=	"\n" . $myKdGutsPosten->MengenText ;	
				}
				$this->cellArtikelBez1->setData( $myArtikelText) ;

				/**
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$printLine	=	FALSE ;
				if ( strlen( $myKdGutsPosten->SubPosNr) == 0) {
					$itemCnt++ ;
					$printLine	=	TRUE ;
				} else if ( $myKdGutsPosten->myArtikel->ArtType == 1) {
					if ( $myKdGutsPosten->Preis > 0.0) {
						$printLine	=	TRUE ;
					}
				}

				/**
				 * IF this is the main item, output all the data
				 */
				error_log( "Here i am ") ;
				if ( $printLine) {

					$this->cellPosNr->setData( $itemCnt) ;
					$this->cellSubPosNr->setData( $myKdGutsPosten->SubPosNr) ;

					/**
					 * do the required calculations
					 */
					$lineNet	=	$myKdGutsPosten->Menge * $myKdGutsPosten->Preis ;
					$totalNet	+=	$lineNet ;
					if ( ! isset( $totalProMwstSatz[ $myKdGutsPosten->MwstSatz])) 
						$totalProMwstSatz[ $myKdGutsPosten->MwstSatz]	=	0.0 ;
					$totalProMwstSatz[ $myKdGutsPosten->MwstSatz]	+=	$lineNet ;

					/**
					 *
					 */
					$buf	=	sprintf( "%d\n%6.2f%%",
											$myKdGutsPosten->Menge,
											( $myKdGutsPosten->Preis - $myKdGutsPosten->RefPreis ) / $myKdGutsPosten->RefPreis * 100.0) ;
					$this->cellMenge->setData( $buf) ;

					$buf	=	sprintf( "%9.2f\n%6.2f",
											$myKdGutsPosten->RefPreis,
											( $myKdGutsPosten->Preis - $myKdGutsPosten->RefPreis )) ;
					$cellPreis->setData( $buf) ;
					$buf	=	sprintf( "%9.2f",
											$myKdGutsPosten->Menge * $myKdGutsPosten->Preis) ;
					$cellCarryTo->setData( $totalNet) ;
					$cellCarryFrom->setData( $totalNet) ;
					$cellGesamtPreis->setData( $buf) ;
					$cellTaxKey->setData( $myKdGutsPosten->MwstSatz) ;
					$subPosHeader	=	FALSE ;
				} else {
					if ( ! $subPosHeader) {
						$this->myKHRow->enable() ;
					}
//					$this->cellSubPosNr->setData( $myKdGutsPosten->SubPosNr) ;
					$this->cellPosNr->setData( "") ;
					$this->cellSubPosNr->setData( "") ;
					$buf	=	sprintf( "%d", $myKdGutsPosten->Menge) ;
					$this->cellMenge->setData( $buf) ;
					$cellPreis->setData( "") ;
					$cellGesamtPreis->setData( "") ;
					$cellTaxKey->setData( "") ;
					$subPosHeader	=	TRUE ;
				}

				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
				if ( $myKdGutsPosten->getArtikel()->Comp == 1 && $myKdGutsPosten->getArtikel()->ModeKdGuts == 2) {
					$this->cellPosNr->setData( "") ;
					$this->cellSubPosNr->setData( "") ;
					$cellPreis->setData( "") ;
					$cellGesamtPreis->setData( "") ;
					$cellTaxKey->setData( "") ;

					$this->myKHRow->enable() ;
					FDbg::get()->dump( "KdGutsDoc::getPDF(...), Artikelkomponenten werden ausgegeben") ;
					$this->showArtikelKomp( $myKdGutsPosten->getArtikel()->ArtikelNr) ;
				}
			}
		}

		/**
		 * now we can complete the setup the teble-end row
		 */
		$this->psRowMain->disable() ;
		$this->psRow->disable() ;
		$this->ctRow->disable() ;
		$this->cfRow->disable() ;

		/**
		 * now we can complete the setup the teble-end row
		 */
		$myRow	=	new BRow( BRow::RTFooterTE) ;
		$myRow->addCell( 3, new BCell( "Warenwert netto", $this->cellParaFmtLeft)) ;
		$buf2	=	sprintf( "%.2f", $totalNet) ;
		$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
		$myTable->addRow( $myRow) ;

		/**
		 * Rabattzeile nur zeigen fï¿½r Wiederverkaeufer Bestellungen (DMDEALER) oder Bestellungen gemaess Rabatt Modell V2.
		 * In jedem Fall muss der Rabatt > 0 sein, sonst macht es keinen Sinn den Rabatt aus zu geben.
		 */
		if ( $this->myKdGuts->DiscountMode == Opt::DMV2 && $this->myKdGuts->Rabatt > 0) {

			/**
			 * now we can complete the setup the teble-end row
			 */
			$rabatt	=	( $totalProMwstSatz['A'] * $this->myKdGuts->Rabatt) / 100.0 ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( sprintf( "Nachlass, %.1f%% auf %.2f (ausschl. MwSt. Satz A)", $this->myKdGuts->Rabatt, $totalProMwstSatz['A']), $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $rabatt) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;

			/**
			 * now we can complete the setup the teble-end row
			 */
			$totalProMwstSatz['A']	-=	$rabatt ;
			$totalNet	-=	$rabatt ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( "Warenwert nach Nachlass", $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $totalNet) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
		}

		/**
		 * Steuerzeile(n) nur ausgeben fï¿½r Kunden die wirklich Steuer bezahlen mï¿½ssen.
		 */
		if ( $this->myKdGuts->getKunde()->Tax == Opt::JA) {
			$taxes	=	0.0 ;
			foreach( $totalProMwstSatz as $mwstSatz => $mwstTotal) {
				$buf	=	sprintf( "Mehrwertsteuer (%s), %4.1f%% auf %.2f", $mwstSatz, $this->mwstSatz[ $mwstSatz], $mwstTotal) ;
				$taxAmount	=	$mwstTotal * $this->mwstSatz[ $mwstSatz] / 100.0 ;
				$buf2	=	sprintf( "%.2f", $taxAmount) ;
				$myRow	=	new BRow( BRow::RTFooterTE) ;
				$myRow->addCell( 3, new BCell( $buf, $this->cellParaFmtLeft)) ;
				$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
				$myTable->addRow( $myRow) ;
				$taxes	+=	$taxAmount ;
			}

			/**
			 * the tax-total line
			 */
			$buf2	=	sprintf( "%.2f", $taxes) ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( "Mehrwertsteuer gesamt          ", $this->cellParaFmtLeft)) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
	
			/**
			 * the gross-total line
			 */
			$buf2	=	sprintf( "%.2f", $totalNet + $taxes) ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( "Gutschriftbetrag brutto", $this->cellParaFmtLeft)) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
		}

		/**
		 *
		 */
		$this->endTable() ;

		/**
		 *
		 */
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Zahlbed") ;
		$myReplTableOut	=	array( $this->myKdGuts->getKundeKontakt()->getAnrede(),
									$this->myKdGuts->Datum,
									FTr::tr( Opt::optionReturn( Opt::getRModusSkonto(), $this->myKdGuts->ModusSkonto), null,
												$this->lang)) ;
		$myPostfix	=	str_replace( $myReplTableIn, $myReplTableOut, $this->myKdGuts->Postfix) ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;

		if ( $_pdfName == '') {
			$this->end( $this->path->Archive . "KdGuts/" . $this->myKdGuts->KdGutsNr . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
		return $this->myKdGuts->getAsXML() ;
	}
	function	genPDF( $_key, $_val, $_pdfName='') {
		$this->_createPDF( $_key, $_val, $_pdfName) ;
	}
	/**
	 * 
	 * @param unknown_type $_frm
	 */
	function	setupHeaderFirst( $_frm) {
		if ( $this->formMode) {
			BDocRegLetter::setupHeaderFirst( $_frm) ;
		}
		return ;
	}
	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {
		if ( $this->formMode) {
			$_frm->addLine( "Gutschrift", $this->defParaFmt) ;
			$_frm->addLine( sprintf( "Gutschrift Nr. %s, %s", $this->myKdGuts->KdGutsNr, $this->myKdGuts->Datum), $this->defParaFmt) ;
	
			/**
			 * draw the separating line between the header and the document content
			 */
			$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
						$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;
		}
	}
	/**
	 * 
	 * @param unknown_type $_frm
	 */
	function	setupFooter( $_frm) {
		if ( $this->formMode) {
			BDocRegLetter::setupFooter( $_frm) ;
		}
	}
	/**
	 *
	 */
	function	cascTokenStart( $_token) {
		switch ( $_token) {
		case	"steuer"	:
			if ( $this->myKdGuts->getKunde()->Tax == Opt::NEIN) {
				$this->addMyText( "Die Rechnungsstellung erfolgt ohne Mehrwertsteuer aufgrund Ihrer " .
									"Umsatzsteuerident Nr. " . $this->myKdGuts->getKunde()->UStId . " ") ;
			}
			break ;
		case	"shippingadr"	:
			if ( $this->myKdGuts->getLiefKunde()) {
				$this->addMyText( "Die Lieferung erfolgt an:") ;
				$this->addMyText( $this->myKdGuts->getLiefKunde()->FirmaName1) ;
				$this->addMyText( $this->myKdGuts->getLiefKunde()->FirmaName2) ;
				$this->addMyText( $this->myKdGuts->getLiefKundeKontakt()->getAttn()) ;
				$this->addMyText( $this->myKdGuts->getLiefKunde()->Strasse . " " . $this->myKdGuts->getLiefKunde()->Hausnummer) ;
				$this->addMyText( $this->myKdGuts->getLiefKunde()->PLZ . " " . $this->myKdGuts->getLiefKunde()->Ort) ;
			}
			break ;
		case	"invoicingadr"	:
			if ( $this->myKdGuts->getRechKunde()) {
				$this->addMyText( "Die Rechnungsstellung erfolgt an:\n") ;
				$this->addMyText( $this->myKdGuts->getRechKunde()->FirmaName1) ;
				$this->addMyText( $this->myKdGuts->getRechKunde()->FirmaName2) ;
				$this->addMyText( $this->myKdGuts->getRechKundeKontakt()->getAttn()) ;
				$this->addMyText( $this->myKdGuts->getRechKunde()->Strasse . " " . $this->myKdGuts->getRechKunde()->Hausnummer) ;
				$this->addMyText( $this->myKdGuts->getRechKunde()->PLZ . " " . $this->myKdGuts->getRechKunde()->Ort) ;
			}
			break ;
		case	"zahlbed"	:
			switch ( "de") {
			case	"de"	:
				$this->addMyText( "Zahlungsbedingungen: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myKdGuts->ModusSkonto)) ;
				break ;
			case	"en"	:
				$this->addMyText( "Terms of payment: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myKdGuts->ModusSkonto)) ;
				break ;
			}
			break ;
		}
	}

	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
	}

	/**
	 *
	 */
	function	showArtikelKomp( $_artikelNr) {
		FDbg::get()->dump( "KdGutsDoc::showArtikelKomp( '%s')", $_artikelNr) ;
		$this->myKHRow->enable() ;
		$actArtKomp	=	new ArtKomp() ;
		$actArtKomp->ArtikelNr	=	$_artikelNr ;
		for ( $actArtKomp->firstFromDb() ; $actArtKomp->_valid == 1 ; $actArtKomp->nextFromDb()) {
			FDbg::get()->dumpL( 99, "Pos=%d, ArtikelNr=%s", $actArtKomp->PosNr, $actArtKomp->CompArtikelNr) ;
			if ( $actArtKomp->CompMenge > 0) {

				/**
				 * set the table cell data
				 */
				$this->cellArtikelNr->setData( $actArtKomp->getArtikel()->ArtikelNr) ;
				$myArtikelText	=	$actArtKomp->getArtikel()->getFullTextLF( $actArtKomp->CompMengeProVPE) ;
				$this->cellArtikelBez1->setData( $myArtikelText) ;
				$this->cellMenge->setData( $actArtKomp->CompMenge) ;

				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
			}
		}
	}

}

?>
