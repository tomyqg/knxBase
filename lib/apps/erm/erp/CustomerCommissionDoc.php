<?php
/**
 * CustomerCommissionDoc.php Application Level Class for printed version of CustomerCommission
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
 * CustomerCommissionDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage CustomerCommission
 */
class	CustomerCommissionDoc	extends BDocRegLetter {

	public	$_valid	=	false ;
	private	$myCustomerCommission ;
	private	$totalProMwstSatz	=	array() ;
	private	$mwstSatz	=	array( "A" => 19.0, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	private	$cellCharFmt ;
	private	$cellParaFmtLeft ;
	private	$cellParaFmtCenter ;
	private	$cellParaFmtRight ;
	/**
	 * 
	 */
	function	__construct( $_cuCommNo="") {
		$this->_valid	=	false ;
		if ( $_cuCommNo != "") {
			$this->myCustomerCommission	=	new CustomerCommission( $_cuCommNo) ;
			$this->_valid	=	$this->myCustomerCommission->_valid ;
		}
	}
	/**
	 * 
	 * @param $_cuCommNo
	 */
	function	setKey( $_cuCommNo) {
		$this->myCustomerCommission	=	new CustomerCommission( $_cuCommNo) ;
		$this->_valid	=	$this->myCustomerCommission->_valid ;
	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 */
	function	createPDF( $_key="", $_id=-1, $_pdfName="") {
		$this->_createPDF( $_key, $_id, $_pdfName) ;
		$this->printPDF() ;
		return $this->myCustomerCommission->getXMLComplete() ;
	}
	function	getPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "CustomerCommission/" . $this->myCustomerCommission->CustomerCommissionNo . ".pdf" ;
		}
		return $_pdfName ;
	}
	function	printPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "CustomerCommission/" . $this->myCustomerCommission->CustomerCommissionNo . ".pdf" ;
		}
		if ( $this->CustomerCommissionPrn->AutoPrint) {
			$cmd	=	"lpr -P " . $this->CustomerCommissionPrn->PrnName . " " . $_pdfName . " " . $this->CustomerCommissionPrn->PrnOpt . " " ;
			error_log( "'$cmd'") ;
			system( $cmd) ;
		}
		return $_pdfName ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_pdfName
	 */
	function	_createPDF( $_key="", $_id=-1, $_pdfName="") {
		FDbg::dumpL( 0x00000001, "CustomerCommissionDoc.php::CustomerCommissionDoc::_createPDF( '$_key', $_id, '$_pdfName'): begin") ;

		$myColWidths	=	array( /*10,*/ 10, 23, 70, 10, 17, 17) ;

		$this->bcCharFmt	=	new BCharFmt() ;
		$this->bcCharFmt->setBCName( "Code39") ;
		$this->bcCharFmt->setCharStretch( 100.0) ;
		$this->bcCharFmt->setCharSize( mmToPt( 10.0)) ;

		$this->bcParaFmt	=	new BParaFmt() ;
		$this->bcParaFmt->setLineSpacing( 1.0) ;
		$this->bcParaFmt->setAlignment( BParaFmt::alignCenter) ;
		$this->bcParaFmt->setCharFmt( $this->bcCharFmt) ;
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
		$myRow->addCell( 0, new BCell( "Pos.", $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( "", $this->cellParaFmtLeft)) ;
		$myRow->addCell( 0, new BCell( "Article Nr.", $this->cellParaFmtLeft)) ;
		$myRow->addCell( 0, new BCell( "Descriptioneichung", $this->cellParaFmtLeft)) ;
		$myRow->addCell( 0, new BCell( "Quantity", $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( "Delivered", $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( "Fehlend", $this->cellParaFmtRight)) ;
		$myTable->addRow( $myRow) ;

		// setup the second table data row
		$this->myKHRow	=	new BRow( BRow::RTDataIT) ;
		$cellArtKompHeader	=	new BCell( "Weitere Details zu diesem Article (Optionen/Komponenten):\n", $this->cellParaFmtLeft) ;
		$this->myKHRow->addCell( 3, $cellArtKompHeader) ;
		$this->myKHRow->disable() ;
		$myTable->addRow( $this->myKHRow) ;
		
		// setup the second table data row
		$this->myILRow	=	new BRow( BRow::RTDataIT) ;
		$cellArtKompHeader	=	new BCell( FTr::tr( "HEADER-ITEMLIST-PDF-DOC\n"), $this->cellParaFmtLeft) ;
		$this->myILRow->addCell( 3, $cellArtKompHeader) ;
		$this->myILRow->disable() ;
		$myTable->addRow( $this->myILRow) ;
		
		// setup the first table data row
		$myRow	=	new BRow( BRow::RTDataIT) ;
		$this->cellItemNo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellItemNo) ;
		$this->cellSubItemNo	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellSubItemNo) ;
		$this->cellArticleNo	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArticleNo) ;
		$this->cellArticleDescription1	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArticleDescription1) ;
		$this->cellQuantity	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellQuantity) ;
		$cellDelivered	=	new BCell( "", $this->cellParaFmtRight) ;
		$cellDelivered->setBorder( BCell::BTFull) ;
		$myRow->addCell( 0, $cellDelivered) ;
		$cellFehlt	=	new BCell( "", $this->cellParaFmtRight) ;
		$cellFehlt->setBorder( BCell::BTFull) ;
		$myRow->addCell( 0, $cellFehlt) ;
		$myTable->addRow( $myRow) ;
		
		// setup the first table header line
		$stockRow	=	new BRow( BRow::RTDataIT) ;
		$lagerOrtCell	=	new BCell( "", $this->cellParaFmtLeft) ;
		$stockRow->addCell( 3, $lagerOrtCell) ;
		$lagerQuantityCell	=	new BCell( "", $this->cellParaFmtRight) ;
		$stockRow->addCell( 4, $lagerQuantityCell) ;
		$myTable->addRow( $stockRow) ;

		// setup the first table header line
		$infoRow	=	new BRow( BRow::RTDataIT) ;
		$realArticleNoCell	=	new BCell( "", $this->cellParaFmtLeft) ;
		$infoRow->addCell( 3, $realArticleNoCell) ;
		$myTable->addRow( $infoRow) ;

		/**
		 *
		 */
		BDocRegLetter::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myCustomerCommission->getKunde()->FirmaName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myCustomerCommission->getKunde()->FirmaName2)) ;
		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myCustomerCommission->getKundeKontakt()->getAddrLine())) ;
		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myCustomerCommission->getKundeKontakt()->AdrZusatz)) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myCustomerCommission->getKunde()->getAddrStreet())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myCustomerCommission->getKunde()->getAddrCity())) ;
		$this->setRcvr( 7, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myCustomerCommission->getKunde()->getAddrCountry())) ;
		
		$this->setInfo( 1, "Kommissionierschein", "") ;
		$this->setInfo( 2, "Kommission Nr.:", $this->myCustomerCommission->CustomerCommissionNo) ;
		$this->setInfo( 3, "Datum:", $this->myCustomerCommission->Datum) ;
		$this->setInfo( 4, "Kunde Nr.:", $this->myCustomerCommission->CustomerNo . "/" . $this->myCustomerCommission->KundeKontaktNr) ;
		$this->setInfo( 5, "Auftrag Nr.:", $this->myCustomerCommission->CuOrdrNo) ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, "Kundenseitig:", "") ;
		$this->setInfo( 8, "Ref. Nr.:", $this->myCustomerCommission->KdRefNr) ;
		$this->setInfo( 9, "Ref. Datum:", $this->myCustomerCommission->KdRefDatum) ;

		$this->setRef( "Kommissionierauftrag") ;

		$this->begin() ;

		$this->frmContent->addBC( $this->myCustomerCommission->CustomerCommissionNo, $this->bcParaFmt) ;
		
		$this->addMyXML( $this->myCustomerCommission->Prefix) ;

		$this->skipMyLine() ;

		//
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;
		$myCustomerCommissionItem	=	new CustomerCommissionItem( $this->myCustomerCommission->CustomerCommissionNo) ;
		$myArticle	=	new Article() ;
		$myArticleBestand	=	new ArticleBestand() ;
		$itemCnt	=	0 ;
		for ( $myCustomerCommissionItem->firstFromDb( "CustomerCommissionNo", "Article", array(
																		"ERPNo" => "var",
																		"ArticleDescription1" => "var",
																		"ArticleDescription2" => "var",
																		"QuantityText" => "var",
																		"QuantitynEinheit" => "var",
																		"ArticleNoLager" => "var",
																		"ArtType" => "var",
																		"Comp" => "int"
																), "ArticleNo",
																"ORDER BY ItemNo, SubItemNo ") ;
				$myCustomerCommissionItem->_valid == 1 ;
				$myCustomerCommissionItem->nextFromDb()) {
			if ( $myCustomerCommissionItem->Quantity > 0) {

				/**
				 * Lagerbestand einlesen
				 */
				$myArticleBestand->_valid	=	0 ;
				$myArticleBestand->ArticleNo	=	$myCustomerCommissionItem->ArticleNo ;
				$myArticleBestand->getDefault() ;
				if ( $myArticleBestand->_valid == 1) {
					$lagerOrtCell->setData( "Lager: " . $myArticleBestand->Location) ;
					$lagerQuantityCell->setData( sprintf( "%d", $myArticleBestand->Lagerbestand)) ;
				} else {
					$lagerOrtCell->setData( "kein gueltiger Lagerort vorhanden") ;
					$lagerQuantityCell->setData( "INV") ;
				}
				
				$realArticleNoCell->setData( "stocked as: " . $myCustomerCommissionItem->ArticleNoLager) ;

				/**
				 * set the table cell data
				 */
				$myArticle->setArticleNo( $myCustomerCommissionItem->ArticleNo) ;
				$this->cellItemNo->setData( $myCustomerCommissionItem->ItemNo) ;
				$this->cellArticleNo->setData( $myCustomerCommissionItem->ArticleNo . "\n" . $myCustomerCommissionItem->ERPNo) ;
				$myArticleText	=	"" ;
				if ( strlen( $myCustomerCommissionItem->AddText) > 0) {
					$myArticleText	.=	iconv('UTF-8', 'windows-1252', $myCustomerCommissionItem->AddText . "\n") ;
				}
				$myArticleText	.=	$myCustomerCommissionItem->ArticleDescription1 ;
				if ( strlen( $myCustomerCommissionItem->ArticleDescription2) > 0) {
					$myArticleText	.=	"\n" . $myCustomerCommissionItem->ArticleDescription2 ;	
				}
				if ( strlen( $myCustomerCommissionItem->QuantityText) > 0) {
					$myArticleText	.=	"\n" . $myCustomerCommissionItem->QuantityText ;	
				}
				$this->cellArticleDescription1->setData( iconv('UTF-8', 'windows-1252', $myArticleText)) ;
				$this->cellQuantity->setData( $myCustomerCommissionItem->Quantity - $myCustomerCommissionItem->QuantityBereitsDelivered) ;

				/**
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$printLine	=	FALSE ;
				if ( strlen( $myCustomerCommissionItem->SubItemNo) == 0) {
					$itemCnt++ ;
					$printLine	=	TRUE ;
				} else if ( $myCustomerCommissionItem->ArtType == 1) {
					if ( $myCustomerCommissionItem->Quantity > 0) {
						$printLine	=	TRUE ;
					}
				}

				/**
				 * IF this is the main item, output all the data
				 */
				if ( $printLine) {
					/**
					 *
					 */
					$this->cellSubItemNo->setData( $myCustomerCommissionItem->SubItemNo) ;
					$cellDelivered->setData( "") ;
					$cellFehlt->setData( "") ;
					$subPosHeader	=	FALSE ;
				} else {
					if ( ! $subPosHeader) {
						$this->myKHRow->enable() ;
					}
					$this->cellSubItemNo->setData( $myCustomerCommissionItem->SubItemNo) ;
					$cellDelivered->setData( "") ;
					$cellFehlt->setData( "") ;
					$subPosHeader	=	TRUE ;
				}

				$this->cellItemNo->setData( $itemCnt) ;
				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
				if ( $myCustomerCommissionItem->Comp == Article::COMP20) {
					$this->cellItemNo->setData( "") ;
					$this->cellSubItemNo->setData( "") ;
					$this->myILRow->enable() ;
					FDbg::dumpL( 0x00000008, "CustomerCommissionDoc.php::CustomerCommissionDoc::getPDF(...): print omponents") ;
					$this->showArticleKomp( $myCustomerCommissionItem->ArticleNo, $myCustomerCommissionItem->Quantity) ;
				}
			}
			$this->emptyTableRow( 5) ;
		}
		$this->endTable() ;
		$this->addMyXML( $this->myCustomerCommission->Postfix) ;
		if ( $_pdfName == '') {
			$_pdfName	=	$this->path->Archive . "CustomerCommission/" . $this->myCustomerCommission->CustomerCommissionNo . ".pdf" ;
		}
		$this->end( $_pdfName) ;
		FDbg::dumpL( 0x00000001, "CustomerCommissionDoc.php::CustomerCommissionDoc::_createPDF( '$_key', $_id, '$_pdfName'): begin") ;
		return $_pdfName ;
	}
	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( "Kommissionierung", $this->defParaFmt) ;
		$_frm->addLine( sprintf( "Kommission Nr. %s, %s", $this->myCustomerCommission->CustomerCommissionNo, $this->myCustomerCommission->Datum), $this->defParaFmt) ;

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
		case	"shippingadr"	:
			break ;
		case	"invoicingadr"	:
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
	function	showArticleKomp( $_artikelNr, $_qty) {
		FDbg::dumpL( 0x00000001, "CustomerCommissionDoc.php::CustomerCommissionDoc::showArticleKomp( '$_artikelNr'): begin") ;
		$actArticle	=	new Article() ;
		$actArtKomp	=	new ArtKomp() ;
		$actArtKomp->ArticleNo	=	$_artikelNr ;
		for ( $actArtKomp->_firstFromDb( "ArticleNo = '$_artikelNr' ORDER BY ItemNo ") ; $actArtKomp->_valid == 1 ; $actArtKomp->_nextFromDb()) {
			FDbg::dumpL( 0x00000002, "CustomerCommissionDoc.php::CustomerCommissionDoc::showArticleKomp(...): pos=%d, articleno='%s'",
							$actArtKomp->ItemNo, $actArtKomp->CompArticleNo) ;
			if ( $actArtKomp->CompQuantity > 0) {
				/**
				 * set the table cell data
				 */
				$actArticle->setKey( $actArtKomp->CompArticleNo) ;
				$this->cellArticleNo->setData( $actArtKomp->CompArticleNo) ;
				$myArticleText	=	"" ;
//				if ( strlen( $myCustomerCommissionItem->AddText) > 0) {
//					$myArticleText	.=	iconv('UTF-8', 'windows-1252', $myCustomerCommissionItem->AddText . "\n") ;
//				}
				$myArticleText	.=	$actArticle->getFullTextLF( $actArtKomp->CompQuantityPerPU) ;
				if ( strlen( $actArticle->ArticleDescription2) > 0) {
					$myArticleText	.=	"\n" . $actArticle->ArticleDescription2 ;	
				}
				if ( strlen( $actArticle->QuantityText) > 0) {
					$myArticleText	.=	"\n" . $actArticle->QuantityText ;	
				}
				$this->cellArticleDescription1->setData( iconv('UTF-8', 'windows-1252', $myArticleText)) ;
				$this->cellQuantity->setData( $actArtKomp->CompQuantity * $_qty) ;
				$this->punchTable() ;
				$this->myILRow->disable() ;		// disable in every case
			}
		}
		FDbg::dumpL( 0x00000001, "CustomerCommissionDoc.php::CustomerCommissionDoc::showArticleKomp( '$_artikelNr'): end") ;
	}
}
?>
