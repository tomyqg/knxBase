<?php
/**
 * CustomerOrderDoc.php - create a PDF version of a customer order
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
 * @package Application
 * @filesource
 */
/**
 * CustomerOrderDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage CustomerOrder
 */
class	CustomerOrderDoc	extends BDocRegLetter {

	private	$myCustomerOrder ;
	private	$totalProTaxClass	=	array() ;
	private	$mwstSatz	=	array( "A" => 19, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	private	$cellCharFmt ;
	private	$cellParaFmtLeft ;
	private	$cellParaFmtCenter ;
	private	$cellParaFmtRight ;
	public	$_valid	=	false ;
	/**
	 *
	 */
	function	__construct( $_key, $_formMode=true) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_formMode)") ;
		parent::__construct( false) ;
		$this->formMode	=	$_formMode ;
		$this->pdfName	=	$_key . ".pdf" ;
		$this->fullPDFName	=	$this->path->Archive . "CustomerOrder/" . $_key . ".pdf" ;
		$this->setKey( $_key) ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param $_customerOrderNo
	 */
	function	setKey( $_customerOrderNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_customerOrderNo')") ;
		$this->myCustomerOrder	=	new CustomerOrder( $_customerOrderNo) ;
		$this->_valid	=	$this->myCustomerOrder->_valid ;
		$this->lang	=	$this->myCustomerOrder->getCustomer()->Language ;
		FDbg::end() ;
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
	 * returns the complete path/filename where the PDF file has been stored
	 * @param $_key
	 * @param $_id
	 * @param $_pdfName
	 */
	function	createPDF( $_key, $_id, $_pdfName='', $reply=null) {
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
		$this->cellSubItemNo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellSubItemNo) ;
		$this->cellArticleNo	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArticleNo) ;
		$this->cellArticleDescription1	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArticleDescription1) ;
		$this->cellQuantity	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellQuantity) ;
		$cellPrice	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellPrice) ;
		$cellTotalPrice	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellTotalPrice) ;
		$cellTaxKey	=	new BCell( "", $this->cellParaFmtCenter) ;
		$myRow->addCell( 0, $cellTaxKey) ;
		$myTable->addRow( $myRow) ;
		/**
		 * setup line: carry over to
		 */
		$this->ctRow	=	new BRow( BRow::RTFooterCT) ;
		$this->ctRow->addCell( 3, new BCell( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Carry over to page: %np%")), $this->cellParaFmtLeft)) ;
		$cellCarryTo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 6, $cellCarryTo) ;
		$myTable->addRow( $this->ctRow) ;
		/**
		 *
		 */
		BDocRegLetter::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252', $this->myCustomerOrder->getCustomer()->CustomerName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252', $this->myCustomerOrder->getCustomer()->CustomerName2)) ;
//		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252', $this->myCustomerOrder->getCustomerContact()->getAttentionLine())) ;
//		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252', $this->myCustomerOrder->getCustomerContact()->Address)) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252', $this->myCustomerOrder->getCustomer()->getAddrStreet())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252', $this->myCustomerOrder->getCustomer()->getAddrCity())) ;
		$this->setRcvr( 7, iconv( 'UTF-8', 'windows-1252', $this->myCustomerOrder->getCustomer()->getAddrCountry())) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Order Confirmation", null, $this->lang)), "") ;
		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Order conf. no.", null, $this->lang).":"), $this->myCustomerOrder->CustomerOrderNo) ;
		$this->setInfo( 3, FTr::tr( "Date", null, $this->lang).":", $this->myCustomerOrder->Date) ;
		$this->setInfo( 4, FTr::tr( "Customer no.", null, $this->lang).":", $this->myCustomerOrder->CustomerNo . "/" . $this->myCustomerOrder->CustomerContactNo) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, FTr::tr( "Customer", null, $this->lang).":", "") ;
		$this->setInfo( 8, FTr::tr( "Ref. no.", null, $this->lang).":", $this->myCustomerOrder->CustomerReferenceNo) ;
		$this->setInfo( 9, FTr::tr( "Ref. date", null, $this->lang).":", $this->myCustomerOrder->CustomerReferenceDate) ;

		$this->setRef( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Order Confirmation", null, $this->lang))) ;

		$this->begin() ;

		/**
		 *
		 */
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $this->myCustomerOrder->Prefix)) ;
		$this->skipMyLine() ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;

		$myCustomerOrderItem	=	new CustomerOrderItem() ;
		$myCustomerOrderItem->CustomerOrderNo	=	 $this->myCustomerOrder->CustomerOrderNo ;
		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		$myArticle	=	new Article() ;
		/**
		 *
		 */
		$myItem	=	new FDbObject( "CustomerOrderItem", "Id", "def") ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "CustomerOrderNo = '".$this->myCustomerOrder->CustomerOrderNo."' ") ;
		$myItem->setIterOrder( [ "ItemNo", "SubItemNo"]) ;
		foreach ( $myItem as $myCustomerOrderItem) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Pos=%d, ArticleNo='".$myCustomerOrderItem->ItemNo."', '" . $myCustomerOrderItem->ArticleNo. "'") ;
			if ( $myCustomerOrderItem->Quantity != 0) {
				$myArticle->setArticleNo( $myCustomerOrderItem->ArticleNo) ;
				/**
				 * Article Nummer
				 */
				$this->cellArticleNo->setData( $myCustomerOrderItem->ArticleNo) ;
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
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$printLine	=	FALSE ;
				if ( strlen( $myCustomerOrderItem->SubItemNo) == 0) {
					$itemCnt++ ;
					$printLine	=	TRUE ;
				} else if ( $myCustomerOrderItem->ArtType == 1) {
					if ( $myCustomerOrderItem->Price > 0.0) {
						$printLine	=	TRUE ;
					}
				}

				/**
				 * IF this is the main item, output all the data
				 */
				error_log( "Here i am ") ;
				if ( $printLine) {

					$this->cellItemNo->setData( $itemCnt) ;
					$this->cellSubItemNo->setData( $myCustomerOrderItem->SubItemNo) ;

					/**
					 * do the required calculations
					 */
					$lineNet	=	$myCustomerOrderItem->Quantity * $myCustomerOrderItem->Price ;
					$totalNet	+=	$lineNet ;
					if ( ! isset( $totalProTaxClass[ $myArticle->TaxClass]))
						$totalProTaxClass[ $myArticle->TaxClass]	=	0.0 ;
					$totalProTaxClass[ $myArticle->TaxClass]	+=	$lineNet ;

					/**
					 *
					 */
					if ( $myCustomerOrderItem->Price == 0) {
						$myCustomerOrderItem->ReferencePrice	=	$myCustomerOrderItem->Price ;
						$buf	=	sprintf( "%d",
												$myCustomerOrderItem->Quantity) ;
					} else {
						if ( $myCustomerOrderItem->ReferencePrice == 0) {
							$myCustomerOrderItem->ReferencePrice	=	$myCustomerOrderItem->Price ;
						}
						$buf	=	sprintf( "%d\n%6.2f%%",
												$myCustomerOrderItem->Quantity,
												( $myCustomerOrderItem->Price - $myCustomerOrderItem->ReferencePrice ) / $myCustomerOrderItem->ReferencePrice * 100.0) ;
					}
					$this->cellQuantity->setData( $buf) ;

					$buf	=	sprintf( "%9.2f\n%6.2f",
											$myCustomerOrderItem->ReferencePrice,
											( $myCustomerOrderItem->Price - $myCustomerOrderItem->ReferencePrice )) ;
					$cellPrice->setData( $buf) ;
					$buf	=	sprintf( "%9.2f",
											$myCustomerOrderItem->Quantity * $myCustomerOrderItem->Price) ;
					$cellCarryTo->setData( $totalNet) ;
					$cellCarryFrom->setData( $totalNet) ;
					$cellTotalPrice->setData( $buf) ;
					$cellTaxKey->setData( $myArticle->TaxClass) ;
					$subPosHeader	=	FALSE ;
				} else {
					if ( ! $subPosHeader) {
						$this->myKHRow->enable() ;
					}
//					$this->cellSubItemNo->setData( $myCustomerOrderItem->SubItemNo) ;
					$this->cellItemNo->setData( "") ;
					$this->cellSubItemNo->setData( "") ;
					$buf	=	sprintf( "%d", $myCustomerOrderItem->Quantity) ;
					$this->cellQuantity->setData( $buf) ;
					$cellPrice->setData( "") ;
					$cellTotalPrice->setData( "") ;
					$cellTaxKey->setData( "") ;
					$subPosHeader	=	TRUE ;
				}

				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
				if ( $myArticle->CompositionType == 1 && $myArticle->ModeCustomerOrder == 2) {
					$this->cellItemNo->setData( "") ;
					$this->cellSubItemNo->setData( "") ;
					$cellPrice->setData( "") ;
					$cellTotalPrice->setData( "") ;
					$cellTaxKey->setData( "") ;

					$this->myKHRow->enable() ;
					FDbg::dump( "CustomerOrderDoc::getPDF(...), Articlekomponenten werden ausgegeben") ;
					$this->showArticleKomp( $myCustomerOrderItem->getArticle()->ArticleNo) ;
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
		$myRow->addCell( 3, new BCell( FTr::tr( "Total net value", null, $this->lang), $this->cellParaFmtLeft)) ;
		$buf2	=	sprintf( "%.2f", $totalNet) ;
		$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
		$myTable->addRow( $myRow) ;

		/**
		 * Rabattzeile nur zeigen f�r Wiederverkaeufer Bestellungen (DMDEALER) oder Bestellungen gemaess Rabatt Modell V2.
		 * In jedem Fall muss der Rabatt > 0 sein, sonst macht es keinen Sinn den Rabatt aus zu geben.
		 */
		if ( $this->myCustomerOrder->DiscountMode == 20 && $this->myCustomerOrder->Rabatt > 0) {

			/**
			 * now we can complete the setup the teble-end row
			 */
			$rabatt	=	( $totalProTaxClass['A'] * $this->myCustomerOrder->Rabatt) / 100.0 ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( FTr::tr( "Discount, #1%% of #2 (excl. Tax key A)",
											array( "%.1f:".$this->myCustomerOrder->Rabatt, "%.2f:".$totalProTaxClass['A']),
											$this->lang),
											$this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $rabatt) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;

			/**
			 * now we can complete the setup the teble-end row
			 */
			$totalProTaxClass['A']	-=	$rabatt ;
			$totalNet	-=	$rabatt ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( FTr::tr( "Total net value after discount", null, $this->lang), $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $totalNet) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
		}

		/**
		 * Steuerzeile(n) nur ausgeben f�r Customern die wirklich Steuer bezahlen m�ssen.
		 */
		if ( $this->myCustomerOrder->getCustomer()->Tax == 1) {
			$taxes	=	0.0 ;
			foreach( $totalProTaxClass as $mwstSatz => $mwstTotal) {
				$buf	=	FTr::tr( "Tax (#1), #2%% on #3",
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
		if ( strlen( $this->myCustomerOrder->CustRem) > 0) {
			$this->addMyText( FTr::tr( "Customer remark:")) ;
			$this->addMyXML( iconv('UTF-8', 'windows-1252', $this->myCustomerOrder->CustRem)) ;
		}
		$myReplTableIn	=	array( "#Anrede", "#Date", "#Zahlbed") ;
		$myReplTableOut	=	array( $this->myCustomerOrder->getCustomerContact()->getSalutationLine(),
									$this->myCustomerOrder->Date, "") ;
//									FTr::tr( Opt::optionReturn( Opt::getRModusSkonto(), $this->myCustomerOrder->ModusSkonto), null,
//												$this->lang)) ;
		$myPostfix	=	str_replace( $myReplTableIn, $myReplTableOut, $this->myCustomerOrder->Postfix) ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;
		if ( $_pdfName == '') {
			$_pdfName	=	$this->path->Archive . "CustomerOrder/" . $this->myCustomerOrder->CustomerOrderNo . ".pdf" ;
		}
		$this->end( $_pdfName) ;
		FDbg::end() ;
		return $_pdfName ;
	}
	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <BFrame>)") ;
		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Order Confirmation", null, $this->lang)), $this->defParaFmt) ;
		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT',
									FTr::tr( "Order no. #1, dated #2",
											array( "%s:".$this->myCustomerOrder->CustomerOrderNo, "%s:".$this->myCustomerOrder->Date),
											$this->lang)),
									$this->defParaFmt) ;

		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
					$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	cascTokenStart( $_token) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_token')") ;
		switch ( $_token) {
		case	"steuer"	:
			if ( $this->myCustomerOrder->getCustomer()->Tax == Opt::NEIN) {
				$this->addMyText( "Die Rechnungsstellung erfolgt ohne Mehrwertsteuer aufgrund Ihrer " .
									"Umsatzsteuerident Nr. " . $this->myCustomerOrder->getCustomer()->UStId . " ") ;
			}
			break ;
		case	"shippingadr"	:
			if ( $this->myCustomerOrder->getDeliveryCustomer()) {
				$this->addMyText( "Die Lieferung erfolgt an:") ;
				$this->addMyText( $this->myCustomerOrder->getDeliveryCustomer()->CustomerName1) ;
				$this->addMyText( $this->myCustomerOrder->getDeliveryCustomer()->CustomerName2) ;
				$this->addMyText( $this->myCustomerOrder->getDeliveryCustomerContact()->getAttn()) ;
				$this->addMyText( $this->myCustomerOrder->getDeliveryCustomer()->Strasse . " " . $this->myCustomerOrder->getDeliveryCustomer()->Hausnummer) ;
				$this->addMyText( $this->myCustomerOrder->getDeliveryCustomer()->PLZ . " " . $this->myCustomerOrder->getDeliveryCustomer()->Ort) ;
			}
			break ;
		case	"invoicingadr"	:
			if ( $this->myCustomerOrder->getInvoiceCustomer()) {
				$this->addMyText( "Die Rechnungsstellung erfolgt an:\n") ;
				$this->addMyText( $this->myCustomerOrder->getInvoiceCustomer()->CustomerName1) ;
				$this->addMyText( $this->myCustomerOrder->getInvoiceCustomer()->CustomerName2) ;
				$this->addMyText( $this->myCustomerOrder->getInvoiceCustomerContact()->getAttn()) ;
				$this->addMyText( $this->myCustomerOrder->getInvoiceCustomer()->Strasse . " " . $this->myCustomerOrder->getInvoiceCustomer()->Hausnummer) ;
				$this->addMyText( $this->myCustomerOrder->getInvoiceCustomer()->PLZ . " " . $this->myCustomerOrder->getInvoiceCustomer()->Ort) ;
			}
			break ;
		case	"zahlbed"	:
			switch ( "de") {
			case	"de"	:
//				$this->addMyText( "Zahlungsbedingungen: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myCustomerOrder->ModusSkonto)) ;
				break ;
			case	"en"	:
//				$this->addMyText( "Terms of payment: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myCustomerOrder->ModusSkonto)) ;
				break ;
			}
				$this->addMyText( "Zahlungsbedingungen: " . $this->myCustomerOrder->ModusSkonto) ;
			break ;
		}
		FDbg::end() ;
	}

	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_token')") ;
		FDbg::end() ;
	}

	/**
	 *
	 */
	function	showArticleKomp( $_articleNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_articleNo')") ;
		$this->myKHRow->enable() ;
		$actArticleComponent	=	new ArticleComponent() ;
		$actArticleComponent->ArticleNo	=	$_articleNo ;
		for ( $actArticleComponent->firstFromDb() ; $actArticleComponent->_valid == 1 ; $actArticleComponent->nextFromDb()) {
			FDbg::dumpL( 99, "Pos=%d, ArticleNo=%s", $actArticleComponent->ItemNo, $actArticleComponent->CompArticleNo) ;
			if ( $actArticleComponent->CompQuantity > 0) {

				/**
				 * set the table cell data
				 */
				$this->cellArticleNo->setData( $actArticleComponent->getArticle()->ArticleNo) ;
				$myArticleText	=	$actArticleComponent->getArticle()->getFullTextLF( $actArticleComponent->CompQuantityPerPU) ;
				$this->cellArticleDescription1->setData( $myArticleText) ;
				$this->cellQuantity->setData( $actArticleComponent->CompQuantity) ;

				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
			}
		}
		FDbg::end() ;
	}
}
?>
