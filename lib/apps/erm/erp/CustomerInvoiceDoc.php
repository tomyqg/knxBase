<?php
/**
 * CustomerInvoiceDoc.php - create a PDF version of a customer invoice
 *
 * Revision history
 *
 * Date			User		Change
 * ----------------------------------------------------------------------------
 * 2013-04-30	miskhwe		added revision header, fixed fault in printPDF w/
 * 							rgds to wrong config variable names (CustomerInvoicePrn)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcAppERPObjects
 * @filesource
 */
/**
 *
 * @package wtcAppERPObjects
 * @subpackage Customer Invoicing
 */
class	CustomerInvoiceDoc	extends BDocRegLetter	{
	/**
	 *
	 * @var unknown_type
	 */
	private	$myCustomerInvoice ;
	private	$totalPerTaxClass	=	array() ;
	private	$mwstSatz	=	array( "A" => 19.0, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	private	$cellCharFmt ;
	private	$cellParaFmtLeft ;
	private	$cellParaFmtCenter ;
	private	$cellParaFmtRight ;
	private	$listeCustomerDelivery	=	array() ;
	public	$_valid	=	false ;
	/**
	 *
	 */
	function	__construct( $_key, $_formMode=true) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_formMode)") ;
		parent::__construct( false) ;
		$this->formMode	=	$_formMode ;
		$this->pdfName	=	$_key . ".pdf" ;
		$this->fullPDFName	=	$this->path->Archive . "CustomerInvoice/" . $_key . ".pdf" ;
		$this->setKey( $_key) ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param $_cuInvcNo
	 */
	function	setKey( $_customerInvoiceNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_customerInvoiceNo')") ;
		$this->myCustomerInvoice	=	new CustomerInvoice( $_customerInvoiceNo) ;
		$this->_valid	=	$this->myCustomerInvoice->_valid ;
		$this->lang	=	$this->myCustomerInvoice->getCustomer()->Language ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	archive() {
		/**
		 * create the invoice-original (Rechnungsoriginal)
		 */
		$pdfTargetName	=	$this->path->Archive . "CustomerInvoice/" . $this->myCustomerInvoice->CustomerInvoiceNo . ".pdf" ;
		$this->getPDF( $pdfTargetName) ;

		$pdfs[]	=	$pdfTargetName ;

		/**
		 * create the invoice-copy (Rechnungskopie)
		 */
		$pdfTargetNameKopie	=	$this->path->Archive . "Rechnungen/" . $this->myCustomerInvoice->CustomerInvoiceNo . "-Kopie.pdf" ;
		overlayPDF( $pdfTargetNameKopie, $pdfTargetName, $this->path->Archive . "overlay_kopie_a4.pdf") ;

		/**
		 *
		 */
		try {
			$query	=	"select CustomerDeliveryNo from CustomerDelivery where CuOrdrNo = '" . $this->myCustomerInvoice->CuOrdrNo . "' " ;
			$sqlResult =       FDb::query( $query) ;
			if ( $sqlResult) {
				$numrows        =       FDb::rowCount() ;
				while ($row = mysql_fetch_assoc( $sqlResult)) {
					$pdfs[]	=	$this->path->Archive . "Lieferungen/" . $row['CustomerDeliveryNo'] . "-Kopie.pdf " ;
				}
			} else {
				$mainResult	=	-1 ;
				FDbg::dumpF( "FEHLER BEI DATENBANKZUGRIFF") ;
				FDbg::dumpF( $query) ;
			}
//			combinePDFs( $this->path->Archive . "CustomerInvoice/SINGLE/" . $this->myCustomerInvoice->CustomerInvoiceNo . ".pdf", $pdfs) ;

//			$pdfs[]	=	$this->path->Archive . "CustomerInvoice/" . $this->myCustomerInvoice->CustomerInvoiceNo . "-Kopie.pdf" ;
//			combinePDFs( $this->path->Archive . "CustomerInvoice/DOUBLE/" . $this->myCustomerInvoice->CustomerInvoiceNo . ".pdf", $pdfs) ;

//			$pdfs[]	=	$this->path->Archive . "CustomerInvoice/" . $this->myCustomerInvoice->CustomerInvoiceNo . "-Kopie.pdf" ;
//			combinePDFs( $this->path->Archive . "CustomerInvoice/TRIPPLE/" . $this->myCustomerInvoice->CustomerInvoiceNo . ".pdf", $pdfs) ;
		} catch ( Exception $e) {
			throw $e ;
		}

	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 * returns the full pathname of the
	 */
	function	getPDF( $_key="", $_id=-1, $_pdfName="") {
		return $this->pdfName ;
	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 */
	function	createPDF( $_key, $_val, $_pdfName='', $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_pdfName', <Reply>)") ;
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
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Item", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Article no.", null, $this->lang), $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Description", null, $this->lang), $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Qty.", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Unit Price", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Total", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Tax", null, $this->lang), $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRowMain) ;
		/**
		 * setup the second table header line
		 */
		$this->psRow	=	new BRow( BRow::RTHeaderPS) ;
		$this->psRow->addCell( 4, new BCell( "+/- %", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 5, new BCell( FTr::tr( "Discount", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 6, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 7, new BCell( FTr::tr( "key", null, $this->lang), $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRow) ;
		/**
		 * setup line: carry over from
		 */
		$this->cfRow	=	new BRow( BRow::RTHeaderCF) ;
		$this->cfRow->addCell( 3, new BCell( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Carry over from page: %pp%", null, $this->lang)), $this->cellParaFmtLeft)) ;
		$cellCarryFrom	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->cfRow->addCell( 6, $cellCarryFrom) ;
		$myTable->addRow( $this->cfRow) ;
		/**
		 * setup line: further details
		 */
		$this->myKHRow	=	new BRow( BRow::RTDataIT) ;
		$cellArticleComponentHeader	=	new BCell( "Weitere Details zu diesem Article (Optionen/Komponenten):\n", $this->cellParaFmtLeft) ;
		$this->myKHRow->addCell( 3, $cellArticleComponentHeader) ;
		$this->myKHRow->disable() ;
		$myTable->addRow( $this->myKHRow) ;
		/**
		 * setup the first table data row
		 */
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
		$cellPrice	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellPrice) ;
		$cellGesamtPrice	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellGesamtPrice) ;
		$cellTaxKey	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $cellTaxKey) ;
		$myTable->addRow( $myRow) ;
		/**
		 * setup the second table data row
		 */
		$myRow	=	new BRow( BRow::RTDataIT) ;
		$cellArticleDescription2	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 2, $cellArticleDescription2) ;
		$cellRabProz	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellRabProz) ;
		$cellRabCurr	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellRabCurr) ;
		$myCell	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $myCell) ;
		$myTable->addRow( $myRow) ;
		/**
		 * Set up the carry-over to line
		 */
		$myRow	=	new BRow( BRow::RTFooterCT) ;
		$myRow->addCell( 3, new BCell( iconv('UTF-8', 'windows-1252', FTr::tr( "Carry over to page: %np%", null, $this->lang)), $this->cellParaFmtLeft)) ;
		$cellCarryTo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 6, $cellCarryTo) ;
		$myTable->addRow( $myRow) ;
		/**
		 *
		 */
		BDocRegLetter::__construct( $_copy) ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;
		$this->defParaFmt->setLineSpacing( 1.5) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252', $this->myCustomerInvoice->getCustomer()->CustomerName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252', $this->myCustomerInvoice->getCustomer()->CustomerName2)) ;
//		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252', $this->myCustomerInvoice->getCustomerContact()->getAttentionLine())) ;
//		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252', $this->myCustomerInvoice->getCustomer()->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252', $this->myCustomerInvoice->getCustomer()->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252', $this->myCustomerInvoice->getCustomer()->getAddrCountry())) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Invoice", null, $this->lang)), "") ;
		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Invoice no.", null, $this->lang).":"), $this->myCustomerInvoice->CustomerInvoiceNo) ;
		$this->setInfo( 3, FTr::tr( "Date", null, $this->lang).":", $this->myCustomerInvoice->Date) ;
		$this->setInfo( 4, FTr::tr( "Customer no.", null, $this->lang).":", $this->myCustomerInvoice->CustomerNo . "/" . $this->myCustomerInvoice->CustomerContactNo) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, FTr::tr( "Customer", null, $this->lang).":", "") ;
		$this->setInfo( 8, FTr::tr( "Ref. no.", null, $this->lang).":", $this->myCustomerInvoice->CustomerReferenceNo) ;
		$this->setInfo( 9, FTr::tr( "Ref. date", null, $this->lang).":", $this->myCustomerInvoice->CustomerReferenceDate) ;

		$this->setRef( FTr::tr( "Invoice", null, $this->lang)) ;

		$this->begin() ;

		/**
		 *
		 */
		$this->addMyXML( $this->myCustomerInvoice->Prefix) ;
		$this->skipMyLine() ;

		/**
		 *
		 */
		$lastCustomerDeliveryNo	=	"" ;
		$this->addTable( $myTable) ;

		$lastItemNo	=	0 ;

		$myCustomerInvoiceItem	=	new CustomerInvoiceItem() ;
		$myCustomerInvoiceItem->CustomerInvoiceNo	=	 $this->myCustomerInvoice->CustomerInvoiceNo ;
		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		$myArticle	=	new Article() ;
		/**
		 * Zuerst alle normale Articlepositione (i.e. ohne Sonderposition wie Versand und Versicherung)
		 * ausgeben
		 */
		$myItem	=	new FDbObject( "CustomerInvoiceItem", "Id", "def") ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "CustomerInvoiceNo = '".$this->myCustomerInvoice->CustomerInvoiceNo."' ") ;
		$myItem->setIterOrder( [ "ItemNo", "SubItemNo"]) ;
		foreach ( $myItem as $myCustomerInvoiceItem) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Pos=%d, ArticleNo='".$myCustomerOrderItem->ItemNo."', '" . $myCustomerOrderItem->ArticleNo. "'") ;
			if ( strcmp( $lastCustomerDeliveryNo, $myCustomerInvoiceItem->CustomerDeliveryNo) != 0 && ( $myCustomerInvoiceItem->PosType == CustomerOrderItem::IT_NORMAL || $myCustomerInvoiceItem->PosType == CustomerOrderItem::IT_INVONLY)) {
				if ( strlen( $myCustomerInvoiceItem->CustomerDeliveryNo) >= 6) {
					$this->listeCustomerDelivery[$myCustomerInvoiceItem->CustomerDeliveryNo]	=	$this->path->Archive . "CustomerDelivery/" . $myCustomerInvoiceItem->CustomerDeliveryNo . "-Kopie.pdf" ;
				}
				$buf	=	FTr::tr( "per delivery #1", array( "%s:".$myCustomerInvoiceItem->CustomerDeliveryNo), $this->lang) ;
				if ( $myCustomerInvoiceItem->PosType < CustomerOrderItem::IT_HDLG) {
					$this->addMyText( $buf) ;
					$this->skipMyLine() ;
				}
				$lastCustomerDeliveryNo	=	$myCustomerInvoiceItem->CustomerDeliveryNo ;
			}

			if ( $myCustomerInvoiceItem->Quantity > 0 && ( $myCustomerInvoiceItem->PosType == CustomerOrderItem::IT_NORMAL || $myCustomerInvoiceItem->PosType == CustomerOrderItem::IT_INVONLY)) {
				$myArticle->setArticleNo( $myCustomerInvoiceItem->ArticleNo) ;
				/**
				 * pull together the Article description
				 */
				$myArticleText	=	"" ;
				if ( strlen( $myCustomerOrderItem->AddText) > 0) {
					$myArticleText	.=	$myCustomerOrderItem->AddText . "\n" ;
				}
				$myArticleText	.=	iconv('UTF-8', 'windows-1252', $myArticle->ArticleDescription1) ;
				if ( strlen( $myArticle->ArticleDescription2) > 0) {
					$myArticleText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArticle->ArticleDescription2) ;
				}
				if ( strlen( $myArticle->QuantityText) > 0) {
					$myArticleText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArticle->QuantityText) ;
				} else if ( $myCustomerOrderItem->QuantityPerPU > 1) {
					$myArticleText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArticle->textFromQuantity( $myCustomerOrderItem->QuantityPerPU)) ;
				}
				$this->cellArticleDescription1->setData( $myArticleText) ;
				/**
				 * Festlegen wie wir diese Zeile drucken m�ssen...
				 * WENN das eine Hauptzeile ist (keine Unterpositionsnummer, SubItemNo = "") dann ja, oder...
				 * WENN es eine Option ist die bezahlt werden muss (Unterposition mit Price > 0) dann ebenfalls
				 */
				$printLine	=	FALSE ;
				if ( strlen( $myCustomerInvoiceItem->SubItemNo) == 0) {
					$itemCnt++ ;
					$printLine	=	TRUE ;
//				} else if ( $myCustomerInvoiceItem->ArtType == 1) {
//					if ( $myCustomerInvoiceItem->Price > 0.0) {
//						$printLine	=	TRUE ;
//					}
				}

				/**
				 *
				 */
				if ( $lastItemNo === $myCustomerInvoiceItem->ItemNo && ! $subPosHeader) {
					$myKHRow->enable() ;
					$subPosHeader	=	TRUE ;
				} else if ( $lastItemNo !== $myCustomerInvoiceItem->ItemNo) {
					$subPosHeader	=	FALSE ;
				}
				/**
				 * WENN als vollst�ndige Rechnungspositionszeile...
				 */
				if ( $printLine) {

					$this->cellItemNo->setData( $itemCnt) ;
					$this->cellSubItemNo->setData( $myCustomerInvoiceItem->SubItemNo) ;

					/**
					 * do the required calculations
					 */
					$lineNet	=	round( $myCustomerInvoiceItem->QuantityInvoiced * $myCustomerInvoiceItem->Price, 2) ;
					$totalNet	+=	round( $lineNet, 2) ;
					if ( ! isset( $totalPerTaxClass[ $myArticle->TaxClass]))
						$totalPerTaxClass[ $myArticle->TaxClass]	=	0.0 ;
					$totalPerTaxClass[ $myArticle->TaxClass]	+=	round( $lineNet, 2) ;

					/**
					 *
					 */
					$buf	=	sprintf( "%d\n%6.2f",
											$myCustomerInvoiceItem->QuantityInvoiced,
											( $myCustomerInvoiceItem->Price - $myCustomerInvoiceItem->ReferencePrice ) / $myCustomerInvoiceItem->ReferencePrice * 100.0) ;
					$this->cellQuantity->setData( $buf) ;

					$buf	=	sprintf( "%9.2f\n%9.2f",
											$myCustomerInvoiceItem->ReferencePrice,
											( $myCustomerInvoiceItem->Price - $myCustomerInvoiceItem->ReferencePrice )) ;
					$cellPrice->setData( $buf) ;

					$buf	=	sprintf( "%9.2f", $myCustomerInvoiceItem->QuantityInvoiced * $myCustomerInvoiceItem->Price) ;
					$cellGesamtPrice->setData( $buf) ;

					$cellTaxKey->setData( $myArticle->TaxClass) ;

					$cellCarryTo->setData( $totalNet) ;
					$cellCarryFrom->setData( $totalNet) ;

					$subPosHeader	=	FALSE ;
				} else {
					if ( ! $subPosHeader) {
						$this->myKHRow->enable() ;
					}
//					$this->cellSubItemNo->setData( $myCustomerInvoiceItem->SubItemNo) ;
					$this->cellItemNo->setData( "") ;
					$this->cellSubItemNo->setData( "") ;
					$buf	=	sprintf( "%d", $myCustomerInvoiceItem->QuantityInvoiced) ;
					$this->cellQuantity->setData( $buf) ;

					$cellPrice->setData( "") ;
					$cellGesamtPrice->setData( "") ;
					$cellTaxKey->setData( "") ;
					$subPosHeader	=	TRUE ;
				}

				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
				if ( $myArticle->CompositionType == 1 && $myArticle->ModeCustomerOrder == 2) {
					$this->cellItemNo->setData( "") ;
					$this->cellSubItemNo->setData( "") ;
					$cellPrice->setData( "") ;
					$cellGesamtPrice->setData( "") ;
					$cellTaxKey->setData( "") ;

					$this->myKHRow->enable() ;
					FDbg::dump( "CustomerInvoiceDoc::getPDF(...), Articlekomponenten werden ausgegeben") ;
					$this->showArticleKomp( $myCustomerInvoiceItem->getArticle()->ArticleNo) ;
				}
			}
		}

		/**
		 * Jetzt alle Sonderposten wie Versand, Versicherung etc. ausgeben
		 */
//		for ( $myCustomerInvoiceItem->firstFromDb( "CustomerInvoiceNo", "Article", array(
//																		"ArticleDescription1" => "var",
//																		"ArticleDescription2" => "var",
//																		"QuantityText" => "var",
//																		"QuantitySpecification" => "var",
//																		"ArtType" => "int"
//																), "ArticleNo") ;
//				$myCustomerInvoiceItem->_valid == 1 ;
//				$myCustomerInvoiceItem->nextFromDb()) {
//					FDbg::dumpL( 2, "Pos=%d, ArticleNo=%s", $myCustomerInvoiceItem->ItemNo, $myCustomerInvoiceItem->ArticleNo) ;
//
//			if ( $myCustomerInvoiceItem->Quantity > 0) {
//
//				$itemCnt++ ;
//
//				/**
//				 * set the table cell data
//				 */
//				$this->cellArticleNo->setData( $myCustomerInvoiceItem->ArticleNo) ;
//				$myArticleText	=	"" ;
//				if ( strlen( $myCustomerInvoiceItem->AddText) > 0) {
//					$myArticleText	.=	$myCustomerInvoiceItem->AddText . "\n" ;
//				}
//				$myArticleText	.=	$myCustomerInvoiceItem->myArticle->getFullTextLF( $myCustomerInvoiceItem->QuantityPerPU) ;
//				$this->cellArticleDescription1->setData( $myArticleText) ;
//				$this->cellQuantity->setData( $myCustomerInvoiceItem->QuantityInvoiced) ;
//
//				$this->cellItemNo->setData( $itemCnt) ;
//				$this->cellSubItemNo->setData( $myCustomerInvoiceItem->SubItemNo) ;
//
//				/**
//				 * do the required calculations
//				 */
//				$lineNet	=	round( $myCustomerInvoiceItem->QuantityInvoiced * $myCustomerInvoiceItem->Price, 2) ;
//				$totalNet	+=	round( $lineNet, 2) ;
//				if ( ! isset( $totalPerTaxClass[ $myCustomerInvoiceItem->TaxClass]))
//					$totalPerTaxClass[ $myCustomerInvoiceItem->TaxClass]	=	0.0 ;
//				$totalPerTaxClass[ $myCustomerInvoiceItem->TaxClass]	+=	round( $lineNet, 2) ;
//
//				/**
//				 *
//				 */
//				$buf	=	sprintf( "%9.2f",
//										$myCustomerInvoiceItem->ReferencePrice) ;
//				$cellPrice->setData( $buf) ;
//
//				$buf	=	sprintf( "%9.2f", $myCustomerInvoiceItem->QuantityInvoiced * $myCustomerInvoiceItem->Price) ;
//				$cellGesamtPrice->setData( $buf) ;
//
//				$cellCarryTo->setData( $totalNet) ;
//				$cellCarryFrom->setData( $totalNet) ;
//
//				$cellTaxKey->setData( $myCustomerInvoiceItem->TaxClass) ;
//				$subPosHeader	=	FALSE ;
//
//				$this->punchTable() ;
//			}
//		}

		/**
		 * now we can complete the setup the table-end row
		 */
		$myRow	=	new BRow( BRow::RTFooterTE) ;
		$myRow->addCell( 3, new BCell( FTr::tr( "Total net value", null, $this->lang), $this->cellParaFmtLeft)) ;
		$buf2	=	sprintf( "%.2f", $totalNet) ;
		$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
		$myTable->addRow( $myRow) ;

		/**
		 * show discount lines only if there is some. otherwise customers might complain why this is 0.0
		 */
		if ( $this->myCustomerInvoice->DiscountMode == 20 && $this->myCustomerInvoice->Discount > 0) {

			/**
			 * now we can complete the setup the teble-end row
			 */
			$rabatt	=	round( ( $totalPerTaxClass['A'] * $this->myCustomerInvoice->Discount) / 100.0, 2) ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( FTr::tr( "Discount, #1 of #2 (ausschl. MwSt. Satz A)", array( "%.1f:".$this->myCustomerInvoice->Discount, "%.2f:".$totalPerTaxClass['A']), $this->lang), $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $rabatt) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;

			/**
			 * now we can complete the setup the teble-end row
			 */
			$totalPerTaxClass['A']	-=	$rabatt ;
			$totalNet	-=	$rabatt ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( FTr::tr( "Total net value after discount", null, $this->lang), $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $totalNet) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
		}

		/**
		 * show tax lines only if this customer requires taxes to be collected (within Germany or customer w/o Tax Id.Nr.
		 */
		if ( $this->myCustomerInvoice->getCustomer()->Tax == 1) {
			$taxes	=	0.0 ;
			foreach( $totalPerTaxClass as $mwstSatz => $mwstTotal) {
				error_log( "Tax catagory: '$mwstSatz'") ;
				$buf	=	FTr::tr( "Tax (#1), #2% of #3", array( "%s:".$mwstSatz, "%4.1f:".$this->mwstSatz[ $mwstSatz], "%.2f:".$mwstTotal)) ;
				$taxAmount	=	round( $mwstTotal * $this->mwstSatz[ $mwstSatz] / 100.0, 2) ;
				$buf2	=	sprintf( "%.2f", $taxAmount) ;
				$myRow	=	new BRow( BRow::RTFooterTE) ;
				$myRow->addCell( 3, new BCell( $buf, $this->cellParaFmtLeft)) ;
				$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
				$myTable->addRow( $myRow) ;
				$taxes	+=	round( $taxAmount, 2) ;
			}

			/**
			 * the tax-total line
			 */
			$buf2	=	sprintf( "%.2f", $taxes) ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( FTr::tr( "Total tax(es)", null, $this->lang), $this->cellParaFmtLeft)) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;

			/**
			 * the gross-total line
			 */
			$buf2	=	sprintf( "%.2f", $totalNet + $taxes) ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( FTr::tr( "Total gross value", null, $this->lang), $this->cellParaFmtLeft)) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
		}

		/**
		 *
		 */
		$this->endTable() ;
		$this->addMyText( "\n") ;

		/*
		 *
		 */
		$myReplTableIn	=	array( "#Anrede", "#Date", "#Zahlbed") ;
		$myReplTableOut	=	array( $this->myCustomerInvoice->getCustomerContact()->getSalutationLine(),
									$this->myCustomerInvoice->Date, "") ;
//									FTr::tr( Opt::optionReturn( Opt::getRModusSkonto(), $this->myCustomerInvoice->ModusSkonto)), null,
//												$this->lang) ;
		$myPostfix	=	str_replace( $myReplTableIn, $myReplTableOut, $this->myCustomerInvoice->Postfix) ;
		$this->addMyXML( iconv( 'UTF-8', 'windows-1252', $myPostfix)) ;

		if ( $_pdfName == '') {
			$_pdfName	=	$this->path->Archive . "CustomerInvoice/" . $this->myCustomerInvoice->CustomerInvoiceNo ;
			if ( $_copy)
				$_pdfName	.=	"-Kopie" ;
			$_pdfName	.=	".pdf" ;
		}
		$this->end( $_pdfName) ;
		return $_pdfName ;
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
			$_frm->addLine( FTr::tr( "Invoice", null, $this->lang), $this->defParaFmt) ;
			$_frm->addLine( FTr::tr( "Nr. #1. #2", array( "%s:".$this->myCustomerInvoice->CustomerInvoiceNo, "%s:".$this->myCustomerInvoice->Date), $this->lang), $this->defParaFmt) ;
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
		global	$Carrier ;
		global	$modusSkonto ;
		$myLang	=	$this->lang ;
		switch ( $_token) {
		case	"steuer"	:
			if ( $this->myCustomerInvoice->getCustomer()->Tax == Opt::NEIN) {
				$this->addMyText( FTR::tr( "Invoicing tax-free based on your Tax Id " .
									"#1", array( "%s:".$this->myCustomerInvoice->getCustomer()->UStId), $this->lang)) ;
			}
			break ;
		case	"pkgcount"	:
			break ;
		case	"opttrckcodes"	:
			break ;
		case	"carrier"	:
			break ;
		case	"zahlbed"	:
				$this->addMyText( FTr::tr( "Terms of payment: ", null, $this->lang).FTr::tr( Opt::optionReturn( Opt::getRModusSkonto(), $this->myCustomerInvoice->ModusSkonto)), null, $this->lang) ;
			break ;
		case	"shippingadr"	:
			if ( $this->myCustomerInvoice->getCuOrdr()->getDeliveryCustomer() !== NULL) {
				$this->addMyText( FTr::tr( "The goods will be send to:", null, $this->lang)) ;
				$this->addMyText( $this->myCustomerInvoice->getCuOrdr()->getDeliveryCustomer()->CustomerName1) ;
				$this->addMyText( $this->myCustomerInvoice->getCuOrdr()->getDeliveryCustomer()->CustomerName2) ;
				$this->addMyText( $this->myCustomerInvoice->getCuOrdr()->getDeliveryCustomer()->Strasse . " " . $this->myCustomerInvoice->getCuOrdr()->getDeliveryCustomer()->Hausnummer) ;
				$this->addMyText( $this->myCustomerInvoice->getCuOrdr()->getDeliveryCustomer()->PLZ . " " . $this->myCustomerInvoice->getCuOrdr()->getDeliveryCustomer()->Ort) ;
			} else if ( strcmp( $this->myCustomerInvoice->CustomerNo, $this->myCustomerInvoice->getCuOrdr()->CustomerNo) !== 0) {
				$this->addMyText( FTr::tr( "The invoice will be send to:"), null, $this->lang) ;
				$this->addMyText( $this->myCustomerInvoice->getCuOrdr()->getCustomer()->CustomerName1) ;
				$this->addMyText( $this->myCustomerInvoice->getCuOrdr()->getCustomer()->CustomerName2) ;
				$this->addMyText( $this->myCustomerInvoice->getCuOrdr()->getCustomer()->Strasse . " " . $this->myCustomerInvoice->getCuOrdr()->getCustomer()->Hausnummer) ;
				$this->addMyText( $this->myCustomerInvoice->getCuOrdr()->getCustomer()->PLZ . " " . $this->myCustomerInvoice->getCuOrdr()->getCustomer()->Ort) ;
			}
			break ;
		}
	}
	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
	}
}
?>
