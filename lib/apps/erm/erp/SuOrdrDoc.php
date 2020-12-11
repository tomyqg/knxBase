<?php
/**
 * SuOrdrDoc.php Application Level Class for printed version of SuOrdr
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocRegLetter.php") ;
/**
 * SuOrdrDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage SuOrdr
 */
class	SuOrdrDoc	extends BDocRegLetter {
	private	$mySuOrdr ;
	private	$totalProMwstSatz	=	array() ;
	private	$mwstSatz	=	array( "A" => 19, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
		/**
	 * 
	 */
	function	__construct() {
	}
	/**
	 * 
	 * @param $_suOrdrNo
	 */
	function	setKey( $_suOrdrNo) {
		$this->mySuOrdr	=	new SuOrdr( $_suOrdrNo) ;
		$this->_valid	=	$this->mySuOrdr->_valid ;
		$this->lang	=	$this->mySuOrdr->getLief()->Sprache ;
	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 */
	function	createPDF( $_key="", $_id=-1, $_pdfName="") {
		$this->_createPDF( $_key, $_id, $_pdfName) ;
		return $this->mySuOrdr->getXMLComplete() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_pdfName
	 * @return	string	full pathname of the generated pdf-file
	 */
	function	getPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "SuOrdr/" . $this->mySuOrdr->SuOrdrNo . ".pdf" ;
		}
		return $_pdfName ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_pdfName
	 * @return	string	full pathname of the generated pdf-file
	 */
	function	printPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "SuOrdr/" . $this->mySuOrdr->SuOrdrNo . ".pdf" ;
		}
		if ( $this->suOrdr->autoprint) {
			$cmd	=	"lpr -P " . $this->printer->suOrdr . " " . $_pdfName ;
			system( $cmd) ;
		}
		return $_pdfName ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_pdfName
	 * @return	string	full pathname of the generated pdf-file
	 */
	function	_createPDF( $_key, $_id, $_pdfName='') {
		FDbg::begin( 1, "SuOrdrDoc.php", "SuOrdrDoc", "_createPDF( '$_key', $_id, '$_pdfName')") ;
		/**
		 * 
		 * @var unknown_type
		 */
		$myArtikel	=	new Artikel() ;
		$myColWidths	=	array( /*10,*/0, 23, 70, 10, 17, 17, 8) ;
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
		$myRow	=	new BRow( BRow::RTHeaderPS) ;
		$myRow->addCell( 0, new BCell( FTr::tr( "Item", null, $this->lang), $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( "", $this->cellParaFmtLeft)) ;
		$myRow->addCell( 0, new BCell( FTr::tr( "Article no.", null, $this->lang), $this->cellParaFmtLeft)) ;
		$myRow->addCell( 0, new BCell( FTr::tr( "Description", null, $this->lang), $this->cellParaFmtLeft)) ;
		$myRow->addCell( 0, new BCell( FTr::tr( "Qty.", null, $this->lang), $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( FTr::tr( "Unit price", null, $this->lang), $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( FTr::tr( "Total", null, $this->lang), $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( FTr::tr( "Tax", null, $this->lang), $this->cellParaFmtRight)) ;
		$myTable->addRow( $myRow) ;

		// setup the second table header line
		$myRow	=	new BRow( BRow::RTHeaderCF) ;
		$myRow->addCell( 3, new BCell( FTr::tr( "Carry over from page: %pp%", null, $this->lang), $this->cellParaFmtLeft)) ;
		$cellCarryFrom	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 6, $cellCarryFrom) ;
		$myTable->addRow( $myRow) ;
		
		// setup the second table data row
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
		$myRow->addCell( 0, new BCell( "", $this->cellParaFmtRight)) ;
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
		 *setup the table 'carry-forward' line
		 */
		$myRow	=	new BRow( BRow::RTFooterCT) ;
		$myRow->addCell( 3, new BCell( FTr::tr( "Carry over to page: %np%", null, $this->lang), $this->cellParaFmtLeft)) ;
		$cellCarryTo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 6, $cellCarryTo) ;
		$myTable->addRow( $myRow) ;
		
//		$myDocRegLetter	=	new BDocRegLetter() ;
		BDocRegLetter::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252', $this->mySuOrdr->getLief()->FirmaName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252', $this->mySuOrdr->getLief()->FirmaName2)) ;
		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252', $this->mySuOrdr->getLiefKontakt()->getAddrLine())) ;
		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252', $this->mySuOrdr->getLief()->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252', $this->mySuOrdr->getLief()->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252', $this->mySuOrdr->getLief()->getAddrCountry())) ;

		$this->setInfo( 1, FTr::tr( "Supplier Order", null, $this->lang), "") ;
		$this->setInfo( 2, FTr::tr( "Order No.", null, $this->lang).":", $this->mySuOrdr->SuOrdrNo) ;
		$this->setInfo( 3, FTr::tr( "Date", null, $this->lang).":", $this->mySuOrdr->Datum) ;
		$this->setInfo( 4, FTr::tr( "Supplier No.", null, $this->lang).":", $this->mySuOrdr->LiefNr . "/" . $this->mySuOrdr->LiefKontaktNr) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, FTr::tr( "Reference", null, $this->lang), "") ;
		$this->setInfo( 8, FTr::tr( "Ref. No.", null, $this->lang).":", $this->mySuOrdr->RefNr) ;
		$this->setInfo( 9, FTr::tr( "Ref. Date", null, $this->lang).":", $this->mySuOrdr->RefDatum) ;

		$this->setRef( FTr::tr( "Supplier Order", null, $this->lang)) ;

		$this->begin() ;

		/**
		 * 
		 */
		$this->addMyXML( iconv( 'UTF-8', 'windows-1252', $this->mySuOrdr->Prefix)) ;
		$this->skipMyLine() ;
		
		/**
		 * 
		 */
		$lastSuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;

		$mySuOrdrItem	=	new SuOrdrItem( $this->mySuOrdr->SuOrdrNo) ;

		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		for ( $mySuOrdrItem->firstFromDb( "SuOrdrNo", "Artikel",
												array(	"ArtikelBez1" => "var",
														"ArtikelBez2" => "var",
														"MengenText" => "var",
														"MwstSatz" => "var",
														"Comp" => "var"
												), "ArtikelNr") ;
				$mySuOrdrItem->_valid == 1 ;
				$mySuOrdrItem->nextFromDb()) {
			error_log( "SuOrdrDoc.php::SuOrdrDoc::_createPDF(...): Pos=%d, ArtikelNr=%s", $mySuOrdrItem->ItemNo, $mySuOrdrItem->ArtikelNr) ;
			if ( $mySuOrdrItem->Menge > 0) {
				$myArtikel->setArtikelNr( $mySuOrdrItem->ArtikelNr) ;
				/**
				 * set the table cell data
				 */
				$this->cellArtikelNr->setData( $mySuOrdrItem->LiefArtNr) ;
				$myArtikelText	=	$mySuOrdrItem->ArtikelBez1 ;
				if ( strlen( $mySuOrdrItem->ArtikelBez2) > 0) {
					$myArtikelText	.=	"\n" . $mySuOrdrItem->ArtikelBez2 ;	
				}
				if ( strlen( $mySuOrdrItem->MengenText) > 0) {
					$myArtikelText	.=	"\n" . $mySuOrdrItem->MengenText ;	
				}
				$this->cellArtikelBez1->setData( iconv( "UTF-8", "ISO-8859-15//IGNORE", $myArtikelText)) ;
				$this->cellMenge->setData( $mySuOrdrItem->Menge) ;

				/**
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$itemCnt++ ;
				$printLine	=	TRUE ;

				/**
				 * IF this is the main item, output all the data
				 */
				if ( $printLine) {

					$this->cellPosNr->setData( $itemCnt) ;

					/**
					 * do the required calculations
					 */
					$lineNet	=	$mySuOrdrItem->Menge * $mySuOrdrItem->Preis / $mySuOrdrItem->MengeFuerPreis ;
					$totalNet	+=	$lineNet ;
					if ( ! isset( $this->totalProMwstSatz[ $mySuOrdrItem->MwstSatz])) 
						$this->totalProMwstSatz[ $mySuOrdrItem->MwstSatz]	=	0.0 ;
					$this->totalProMwstSatz[ $mySuOrdrItem->MwstSatz]	+=	$lineNet ;

					/**
					 *
					 */
					if ( $mySuOrdrItem->MengeFuerPreis > 1) {
						$buf	=	sprintf( "%9.2f/%d", $mySuOrdrItem->Preis, $mySuOrdrItem->MengeFuerPreis) ;
					} else {
						$buf	=	sprintf( "%9.2f", $mySuOrdrItem->Preis) ;
					}
					$cellPreis->setData( $buf) ;
					$buf	=	sprintf( "%9.2f", $lineNet) ;
					$cellCarryTo->setData( $totalNet) ;
					$cellCarryFrom->setData( $totalNet) ;
					$cellGesamtPreis->setData( $buf) ;
					$cellTaxKey->setData( $mySuOrdrItem->MwstSatz) ;
					$subPosHeader	=	FALSE ;
				} else {
				}

				$this->punchTable() ;
				if ( $myArtikel->Comp == 1 && $myArtikel->ModeSuOrdr == 2) {
					$this->cellPosNr->setData( "") ;
					$cellPreis->setData( "") ;
					$cellGesamtPreis->setData( "") ;
					$cellTaxKey->setData( "") ;

					$this->myKHRow->enable() ;
					FDbg::get()->dump( "SuOrdrDoc::getPDF(...), Artikelkomponenten werden ausgegeben") ;
					$this->showArtikelKomp( $mySuOrdrItem->ArtikelNr) ;
				}
			}
		}

		/**
		 * now we can complete the setup the table-end row
		 */
		$myRow	=	new BRow( BRow::RTFooterTE) ;
		$myRow->addCell( 3, new BCell( FTr::tr( "Total net value", null, $this->lang), $this->cellParaFmtLeft)) ;
		$buf2	=	sprintf( "%.2f", $totalNet) ;
		$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
		$myTable->addRow( $myRow) ;

		/**
		 * show tax lines only if this customer requires taxes to be collected (within Germany or customer w/o Tax Id.Nr.
		 */
		if ( $this->mySuOrdr->getLief()->Tax == 1) {
			$taxes	=	0.0 ;
			foreach( $this->totalProMwstSatz as $mwstSatz => $mwstTotal) {
				$buf	=	FTr::tr( "Tax (#1), #2% on #3",
											array( "%s:".$mwstSatz, "%4.1f:".$this->mwstSatz[ $mwstSatz], "%.2f:".$mwstTotal),
											$this->lang) ;
				$taxAmount	=	$mwstTotal * $this->mwstSatz[ $mwstSatz] / 100.0 ;
				$buf2	=	sprintf( "[%.2f]", $taxAmount) ;
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
			$myRow->addCell( 3, new BCell( FTr::tr( "Total taxes", null, $this->lang), $this->cellParaFmtLeft)) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
	
			/**
			 * the gross-total line
			 */
			$buf2	=	sprintf( "%.2f", $totalNet + $taxes) ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( FTr::tr( "Total gross order value", null, $this->lang), $this->cellParaFmtLeft)) ;
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
		if ( strlen( $this->mySuOrdr->CuDlvrNo) > 0) {
			$myCuDlvr	=	new CuDlvr( $this->mySuOrdr->CuDlvrNo) ;
			$myKunde	=	$myCuDlvr->getKunde() ;
			$myKundeKontakt	=	$myCuDlvr->getKundeKontakt() ;
		} else {
			$myKunde	=	new Kunde() ;
			$myKundeKontakt	=	new KundeKontakt() ;
		}
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#FirmaName1", "#FirmaName2", "#Kontakt", "#Strasse", "#Hausnummer", "#PLZ", "#Ort") ;
		$myReplTableOut	=	array( $this->mySuOrdr->getLiefKontakt()->getAnrede(),
									$this->mySuOrdr->Datum,
									$myKunde->FirmaName1,
									$myKunde->FirmaName2,
									$myKundeKontakt->getAddrLine(),
									$myKunde->Strasse,
									$myKunde->Hausnummer,
									$myKunde->PLZ,
									$myKunde->Ort
								) ;
		$myPostfix	=	str_replace( $myReplTableIn, $myReplTableOut, $this->mySuOrdr->Postfix) ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;
		if ( $_pdfName == '') {
			$_pdfName	=	$this->path->Archive . "SuOrdr/" . $this->mySuOrdr->SuOrdrNo . ".pdf" ;
		}
		$this->end( $_pdfName) ;
		FDbg::end( 1, "SuOrdrDoc.php", "SuOrdrDoc", "_createPDF( '$_key', $_id, '$_pdfName')") ;
		return $_pdfName ;
	}

	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( FTr::tr( "Supplier Order", null, $this->lang), $this->defParaFmt) ;
		$_frm->addLine( FTr::tr( "Order Nr. #1, #2", array( "%s:".$this->mySuOrdr->SuOrdrNo, "%s:".$this->mySuOrdr->Datum), $this->lang), $this->defParaFmt) ;

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
		$buffer	=	"" ;
		switch ( $_token) {
		case	"FirmaName1"	:
		case	"FirmaName2"	:
		case	"Strasse"	:
		case	"Hausnummer"	:
		case	"PLZ"	:
		case	"Ort"	:
			try {
				if ( $this->mySuOrdr->myCuOrdr->_valid == 1) {
					$myLiefKunde	=	$this->mySuOrdr->myCuOrdr->getLiefKunde() ;
					if ( $myLiefKunde == NULL) {
						$myLiefKunde	=	$this->mySuOrdr->myCuOrdr->getKunde() ;
					}
					$buffer	.=	sprintf( "%s", $myLiefKunde->$_token) ;
				} else {
					$buffer	=	"CuOrdr [" . $this->RefNr . "] ungueltig" ;
				}
			} catch ( Exception $e) {
				FDbg::dump( "SuOrdrDoc::cascTokenStart: invalid token '%s' received (non-critical)", $_token) ;
			}
			break ;	
		default	:
			FDbg::dump( "SuOrdrDoc::cascTokenStart: invalid token '%s' received (non-critical)", $_token) ;
			break ;
		}
		return( $buffer) ;
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
		FDbg::get()->dump( "SuOrdrDoc::showArtikelKomp( '%s')", $_artikelNr) ;
		$this->myKHRow->enable() ;
		$actArtKomp	=	new ArtKomp() ;
		$actArtKomp->ArtikelNr	=	$_artikelNr ;
		for ( $actArtKomp->_firstFromDb() ; $actArtKomp->_valid == 1 ; $actArtKomp->_nextFromDb()) {
			FDbg::get()->dumpL( 99, "Pos=%d, ArtikelNr=%s", $actArtKomp->ItemNo, $actArtKomp->CompArtikelNr) ;
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
