<?php

/**
 * CuOffrDoc.php Application Level Class for printed version of CuOffr
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
 * CuOffrDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage CuOffr
 */
class	CuOffrDoc	extends BDocRegLetter {

	private	$myCuOffr ;
	private	$totalProMwstSatz	=	array() ;
	private	$mwstSatz	=	array( "A" => 19.0, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	/**
	 *
	 */
	function	__construct() {
		FDbg::dumpL( 0x00000002, "CuOffrDoc.php::CuOffrDoc::__construct():") ;
	}
	/**
	 * 
	 * @param $_cuOffrNo
	 */
	function	setKey( $_cuOffrNo) {
		FDbg::dumpL( 0x00000002, "CuOffrDoc.php::CuOffrDoc::setKey( _cuOffrNo):") ;
		$this->myCuOffr	=	new CuOffr( $_cuOffrNo) ;
		$this->_valid	=	$this->myCuOffr->_valid ;
		$this->lang	=	$this->myCuOffr->getKunde()->Sprache ;
	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 */
	function	createPDF( $_key="", $_id=-1, $_pdfName="") {
		$this->pdfName	=	"CuOffr_" . $this->myCuOffr->CuOffrNo . "_" . $this->myCuOffr->RevNo . ".pdf" ;
		$pdfName	=	$this->path->Archive . "CuOffr/" . $this->pdfName ;
		$this->_createPDF( $_key, $_id, $pdfName) ;
//		$this->myCuOffr->newRev() ;
		return $this->myCuOffr->getXMLComplete() ;
	}
	function	getPDF( $_key="", $_id=-1, $_pdfName="") {
		$this->pdfName	=	"CuOffr_" . $this->myCuOffr->CuOffrNo . "_" . $this->myCuOffr->RevNo . ".pdf" ;
		$pdfName	=	$this->path->Archive . "CuOffr/" . $this->pdfName ;
		return $pdfName ;
	}
	function	printPDF( $_key="", $_id=-1, $_pdfName="") {
		$this->pdfName	=	"CuOffr_" . $this->myCuOffr->CuOffrNo . "_" . $this->myCuOffr->RevNo . ".pdf" ;
		$pdfName	=	$this->path->Archive . "CuOffr/" . $this->pdfName ;
		if ( $this->cuComm->autoprint) {
			$cmd	=	"lpr -P " . $this->printer->cuComm . " " . $_pdfName ;
			system( $cmd) ;
		}
		return $pdfName ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_pdfName
	 */
	function	_createPDF( $_key="", $_id=-1, $_pdfName="") {
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
		$this->psRowMain->addCell( 0, new BCell( "", $this->cellParaFmtLeft)) ;
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
		 * Aufsetzten der Zeile für "Übertrag von Seite ...:"
		 */
		$this->cfRow	=	new BRow( BRow::RTHeaderCF) ;
		$this->cfRow->addCell( 3, new BCell( "Übertrag von Seite: %pp%", $this->cellParaFmtLeft)) ;
		$cellCarryFrom	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->cfRow->addCell( 6, $cellCarryFrom) ;
		$myTable->addRow( $this->cfRow) ;
		
		/**
		 * Aufsetzten der Zeile für "Weitere Details:"
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
		$this->cellItemNo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellItemNo) ;
		$this->cellSubItemNo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellSubItemNo) ;
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
		 * Aufsetzten der Zeile für "Übertrag auf Seite ...:"
		 */
		$this->ctRow	=	new BRow( BRow::RTFooterCT) ;
		$this->ctRow->addCell( 3, new BCell( "Übertrag auf Seite: %np%", $this->cellParaFmtLeft)) ;
		$cellCarryTo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 6, $cellCarryTo) ;
		$myTable->addRow( $this->ctRow) ;
				
		/**
		 *
		 */
		BDocRegLetter::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252', $this->myCuOffr->getKunde()->CustomerName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252', $this->myCuOffr->getKunde()->CustomerName2)) ;
		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252', $this->myCuOffr->getKundeKontakt()->getAddrLine())) ;
		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252', $this->myCuOffr->getKunde()->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252', $this->myCuOffr->getKunde()->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252', $this->myCuOffr->getKunde()->getAddrCountry())) ;

		$this->setInfo( 1, FTr::tr( "Angebot:"), "") ;
		$this->setInfo( 2, FTr::tr( "Angebot Nr.:"), $this->myCuOffr->CuOffrNo . "/ Rev. " . $this->myCuOffr->RevNo) ;
		$this->setInfo( 3, FTr::tr( "Datum:"), $this->myCuOffr->Datum) ;
		$this->setInfo( 4, FTr::tr( "Kunde Nr.:"), $this->myCuOffr->KundeNr . "/" . $this->myCuOffr->KundeKontaktNr) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, FTr::tr( "Kundenseitig:"), "") ;
		$this->setInfo( 8, FTr::tr( "Ref. Nr.:"), iconv( 'UTF-8', 'windows-1252', $this->myCuOffr->KdRefNr)) ;
		$this->setInfo( 9, FTr::tr( "Ref. Datum:"), $this->myCuOffr->KdRefDatum) ;

		$this->setRef( "Angebot") ;

		$this->begin() ;

		/**
		 *
		 */
		$this->addMyXML( iconv( 'UTF-8', 'windows-1252', $this->myCuOffr->Prefix)) ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;
		$myCuOffrItem	=	new CuOffrItem( $this->myCuOffr->CuOffrNo) ;
		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		$myArtikel	=	new Artikel() ;
		for ( $myCuOffrItem->firstFromDb( "CuOffrNo", "Artikel", array(
																		"ArtikelBez1" => "var",
																		"ArtikelBez2" => "var",
																		"MengenText" => "var",
																		"MwstSatz" => "var",
																		"Comp" => "var"
																), "ArtikelNr") ;
				$myCuOffrItem->_valid == 1 ;
				$myCuOffrItem->nextFromDb()) {
				FDbg::dumpL( 0x00000002, "Pos=%d, ArtikelNr=%s", $myCuOffrItem->ItemNo, $myCuOffrItem->ArtikelNr) ;
			if ( $myCuOffrItem->Menge > 0) {
				$myArtikel->setArtikelNr( $myCuOffrItem->ArtikelNr) ;

				/**
				 * set the table cell data
				 */
				$this->cellArtikelNr->setData( $myCuOffrItem->ArtikelNr) ;
				$myArtikelText	=	"" ;
				if ( strlen( $myCuOffrItem->AddText) > 0) {
					$myArtikelText	.=	iconv('UTF-8', 'windows-1252', $myCuOffrItem->AddText . "\n") ;
				}
				$myArtikelText	.=	iconv('UTF-8', 'windows-1252', $myCuOffrItem->ArtikelBez1) ;
				if ( strlen( $myCuOffrItem->ArtikelBez2) > 0) {
					$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myCuOffrItem->ArtikelBez2) ;	
				}
				if ( strlen( $myCuOffrItem->MengenText) > 0) {
					$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myCuOffrItem->MengenText) ;	
				} else if ( $myCuOffrItem->MengeProVPE > 1) {
					$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArtikel->textFromMenge( $myCuOffrItem->MengeProVPE)) ;
				}
				$this->cellArtikelBez1->setData( $myArtikelText) ;
				$this->cellMenge->setData( $myCuOffrItem->Menge) ;

				/**
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$printLine	=	FALSE ;
				if ( strlen( $myCuOffrItem->SubItemNo) == 0) {
					$itemCnt++ ;
					$printLine	=	TRUE ;
				} else if ( $myCuOffrItem->myArtikel->ArtType == 1) {
					if ( $myCuOffrItem->Preis > 0.0) {
						$printLine	=	TRUE ;
					}
				}

				/**
				 * IF this is the main item, output all the data
				 */
				if ( $printLine) {

					$this->cellItemNo->setData( $myCuOffrItem->ItemNo) ;
					$this->cellSubItemNo->setData( $myCuOffrItem->SubItemNo) ;

					/**
					 * do the required calculations
					 */
					$lineNet	=	$myCuOffrItem->Menge * $myCuOffrItem->Preis ;
					$totalNet	+=	$lineNet ;
					if ( ! isset( $totalProMwstSatz[ $myCuOffrItem->MwstSatz])) 
						$totalProMwstSatz[ $myCuOffrItem->MwstSatz]	=	0.0 ;
					$totalProMwstSatz[ $myCuOffrItem->MwstSatz]	+=	$lineNet ;

					$buf	=	sprintf( "%d\n%6.2f%%",
											$myCuOffrItem->Menge,
											( $myCuOffrItem->Preis - $myCuOffrItem->RefPreis ) / $myCuOffrItem->RefPreis * 100.0) ;
					$this->cellMenge->setData( $buf) ;
					/**
					 *
					 */
					$buf	=	sprintf( "%9.2f\n%6.2f",
											$myCuOffrItem->RefPreis,
											( $myCuOffrItem->Preis - $myCuOffrItem->RefPreis )) ;
					$cellPreis->setData( $buf) ;
					$buf	=	sprintf( "%9.2f", $myCuOffrItem->Menge * $myCuOffrItem->Preis) ;
					$cellGesamtPreis->setData( $buf) ;
					$cellTaxKey->setData( $myCuOffrItem->MwstSatz) ;
					$cellCarryTo->setData( $totalNet) ;
					$cellCarryFrom->setData( $totalNet) ;
					$subPosHeader	=	FALSE ;
				} else {
					if ( ! $subPosHeader) {
						$this->myKHRow->enable() ;
					}
//					$cellSubItemNo->setData( $myCuOffrItem->SubItemNo) ;
					$this->cellItemNo->setData( "") ;
					$this->cellSubItemNo->setData( "") ;
					$cellPreis->setData( "") ;
					$cellGesamtPreis->setData( "") ;
					$cellTaxKey->setData( "") ;
					$subPosHeader	=	TRUE ;
				}

				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
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
		 * Rabattzeile nur zeigen für Wiederverkaeufer Bestellungen (DMDEALER) oder Bestellungen gemaess Rabatt Modell V2.
		 * In jedem Fall muss der Rabatt > 0 sein, sonst macht es keinen Sinn den Rabatt aus zu geben.
		 */
		if ( $this->myCuOffr->Rabatt > 0) {

			/**
			 * now we can complete the setup the teble-end row
			 */
			$rabatt	=	( $totalProMwstSatz['A'] * $this->myCuOffr->Rabatt) / 100.0 ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( sprintf( "Nachlass, %.1f%% auf %.2f (ausschl. MwSt. Satz A)", $this->myCuOffr->Rabatt, $totalProMwstSatz['A']), $this->cellParaFmtLeft)) ;
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

		//
		// show tax lines only if this customer requires taxes to be collected (within Germany or customer w/o Tax Id.Nr.
		//

		{
			$taxes	=	0.0 ;
			FDbg::dumpL( 0x00000002, "KdAndDoc.php::CuOffrDoc::getPDF(...), schreibe Mwst Saetze") ;
			foreach( $totalProMwstSatz as $mwstSatz => $mwstTotal) {
				FDbg::dumpL( 0x00000002, "CuOffrDoc.php::CuOffrDoc::getPDF(...), mwstSatz='%s'", $mwstSatz) ;
				$buf	=	sprintf( "Mehrwertsteuer (%s), %4.1f%% auf %.2f", $mwstSatz, $this->mwstSatz[ $mwstSatz], $mwstTotal) ;
				$taxAmount	=	$mwstTotal * $this->mwstSatz[ $mwstSatz] / 100.0 ;
				$buf2	=	sprintf( "%.2f", $taxAmount) ;
				$myRow	=	new BRow( BRow::RTFooterTE) ;
				$myRow->addCell( 3, new BCell( $buf, $this->cellParaFmtLeft)) ;
				$myRow->addCell( 5, new BCell( $buf2, $this->cellParaFmtRight)) ;
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
			$myRow->addCell( 3, new BCell( "Gesamtwert brutto", $this->cellParaFmtLeft)) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
		}

		//
		$this->endTable() ;

		//
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Zahlbed") ;
		$myReplTableOut	=	array( $this->myCuOffr->getKundeKontakt()->getAnrede(),
									$this->myCuOffr->Datum,
									FTr::tr( Opt::optionReturn( Opt::getRModusSkonto(), $this->myCuOffr->ModusSkonto), null,
												$this->lang)) ;
		$myPostfix	=	str_replace( $myReplTableIn, $myReplTableOut, $this->myCuOffr->Postfix) ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;
		
		if ( $_pdfName == '') {
			$this->end( $this->path->Archive . "CuOffr/" . $this->myCuOffr->CuOffrNo . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
		return $this->myCuOffr->getAsXML() ;
	}
	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Offer", null, $this->lang)), $this->defParaFmt) ;
		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT',
									FTr::tr( "Offer no. #1, dated #2",
											array( "%s:".$this->myCuOffr->CuOffrNo, "%s:".$this->myCuOffr->Datum),
											$this->lang)),
									$this->defParaFmt) ;
		
		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
					$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;
	}
	/**
	 *
	 */
	function	cascTokenStart( $_token) {
		switch ( $_token) {
		case	"steuer"	:
			if ( $this->myCuOffr->getKunde()->Tax == Opt::NEIN) {
				$this->addMyText( "Die Rechnungsstellung erfolgt ohne Mehrwertsteuer aufgrund Ihrer " .
									"Umsatzsteuerident Nr. " . $this->myCuOffr->getKunde()->UStId . " ") ;
			}
			break ;
		case	"shippingadr"	:
			if ( $this->myCuOffr->getLiefKunde()) {
				$this->addMyText( "Die Lieferung erfolgt an:") ;
				$this->addMyText( $this->myCuOffr->getLiefKunde()->CustomerName1) ;
				$this->addMyText( $this->myCuOffr->getLiefKunde()->CustomerName2) ;
				$this->addMyText( $this->myCuOffr->getLiefKundeKontakt()->getAttn()) ;
				$this->addMyText( $this->myCuOffr->getLiefKunde()->Strasse . " " . $this->myCuOffr->getLiefKunde()->Hausnummer) ;
				$this->addMyText( $this->myCuOffr->getLiefKunde()->PLZ . " " . $this->myCuOffr->getLiefKunde()->Ort) ;
			}
			break ;
		case	"invoicingadr"	:
			if ( $this->myCuOffr->getRechKunde()) {
				$this->addMyText( "Die Rechnungsstellung erfolgt an:\n") ;
				$this->addMyText( $this->myCuOffr->getRechKunde()->CustomerName1) ;
				$this->addMyText( $this->myCuOffr->getRechKunde()->CustomerName2) ;
				$this->addMyText( $this->myCuOffr->getRechKundeKontakt()->getAttn()) ;
				$this->addMyText( $this->myCuOffr->getRechKunde()->Strasse . " " . $this->myCuOffr->getRechKunde()->Hausnummer) ;
				$this->addMyText( $this->myCuOffr->getRechKunde()->PLZ . " " . $this->myCuOffr->getRechKunde()->Ort) ;
			}
			break ;
		case	"zahlbed"	:
			switch ( "de") {
			case	"de"	:
				$this->addMyText( "Zahlungsbedingungen: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myCuOffr->ModusSkonto)) ;
				break ;
			case	"en"	:
				$this->addMyText( "Terms of payment: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myCuOffr->ModusSkonto)) ;
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
		FDbg::dump( "CuOffrDoc::showArtikelKomp( '%s')", $_artikelNr) ;
		$this->myKHRow->enable() ;
		$actArtKomp	=	new ArtKomp() ;
		$actArtKomp->ArtikelNr	=	$_artikelNr ;
		for ( $actArtKomp->firstFromDb() ; $actArtKomp->_valid == 1 ; $actArtKomp->nextFromDb()) {
			FDbg::dumpL( 0x00000001, "CuOffrDoc.php::CuOffrDoc::showArtikelKomp(...): Pos=%d, ArtikelNr=%s", $actArtKomp->ItemNo, $actArtKomp->CompArtikelNr) ;
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
