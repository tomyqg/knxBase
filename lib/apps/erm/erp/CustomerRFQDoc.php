<?php
/**
 * CustomerRFQDoc.php Application Level Class for printed version of CustomerRFQ
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * CustomerRFQDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage CustomerRFQ
 */
class	CustomerRFQDoc	extends BDocRegLetter {
	private	$myCustomerRFQ ;
	private	$totalProTaxClass	=	array() ;
	private	$percentage	=	array( "A" => 19.0, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	private	$cellCharFmt ;
	private	$cellParaFmtLeft ;
	private	$cellParaFmtCenter ;
	private	$cellParaFmtRight ;
	public	$_valid	=	false ;
	/**
	 *
	 */
	function	__construct() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_formMode)") ;
		parent::__construct( false) ;
		$this->formMode	=	$_formMode ;
		$this->pdfName	=	$_key . ".pdf" ;
		$this->fullPDFName	=	$this->path->Archive . "CustomerRFQ/" . $_key . ".pdf" ;
		$this->setKey( $_key) ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setKey( $_customerRFQNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_customerRFQNo')") ;
		$this->myCustomerRFQ	=	new CustomerRFQ( $_customerRFQNo) ;
		$this->_valid	=	$this->myCustomerRFQ->_valid ;
		$this->lang	=	$this->myCustomerRFQ->getCustomer()->Language ;
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
	 *
	 */
	function	createPDF( $_key, $_val, $_pdfName='') {
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
		$this->psRow->addCell( 5, new BCell( "Discount", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 6, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 7, new BCell( "Schl.", $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRow) ;
		/**
		 * Aufsetzten der Zeile f�r "�bertrag von Seite ...:"
		 */
		$this->cfRow	=	new BRow( BRow::RTHeaderCF) ;
		$this->cfRow->addCell( 3, new BCell( "�bertrag von Seite: %pp%", $this->cellParaFmtLeft)) ;
		$cellCarryFrom	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->cfRow->addCell( 6, $cellCarryFrom) ;
		$myTable->addRow( $this->cfRow) ;
		/**
		 * Aufsetzten der Zeile f�r "Weitere Details:"
		 */
		$this->myKHRow	=	new BRow( BRow::RTDataIT) ;
		$cellArtKompHeader	=	new BCell( "Weitere Details zu diesem Article (Optionen/Komponenten):\n", $this->cellParaFmtLeft) ;
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
		 * Aufsetzten der Zeile f�r "�bertrag auf Seite ...:"
		 */
		$this->ctRow	=	new BRow( BRow::RTFooterCT) ;
		$this->ctRow->addCell( 3, new BCell( "�bertrag auf Seite: %np%", $this->cellParaFmtLeft)) ;
		$cellCarryTo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 6, $cellCarryTo) ;
		$myTable->addRow( $this->ctRow) ;
		/**
		 *
		 */
		BDocRegLetter::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252', $this->myCustomerRFQ->getCustomer()->CustomerName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252', $this->myCustomerRFQ->getCustomer()->CustomerName2)) ;
//		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252', $this->myCustomerRFQ->getCustomerContact()->getAttentionLine())) ;
//		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252', $this->myCustomerRFQ->getCustomerContact()->Address)) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252', $this->myCustomerRFQ->getCustomer()->getAddrStreet())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252', $this->myCustomerRFQ->getCustomer()->getAddrCity())) ;
		$this->setRcvr( 7, iconv( 'UTF-8', 'windows-1252', $this->myCustomerRFQ->getCustomer()->getAddrCountry())) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252', FTr::tr( "RFQ", null, $this->lang)), "") ;
		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252', FTr::tr( "RFQ no.", null, $this->lang).":"), $this->myCustomerRFQ->CustomerRFQNo) ;
		$this->setInfo( 3, FTr::tr( "Date", null, $this->lang).":", $this->myCustomerRFQ->Date) ;
		$this->setInfo( 4, FTr::tr( "Customer no.", null, $this->lang).":", $this->myCustomerRFQ->CustomerNo . "/" . $this->myCustomerRFQ->CustomerContactNo) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, FTr::tr( "Customer", null, $this->lang).":", "") ;
		$this->setInfo( 8, FTr::tr( "Ref. no.", null, $this->lang).":", $this->myCustomerRFQ->CustomerReferenceNo) ;
		$this->setInfo( 9, FTr::tr( "Ref. date", null, $this->lang).":", $this->myCustomerRFQ->CustomerReferenceDate) ;

		$this->setRef( "Enquiry / RFQ") ;

		$this->begin() ;

		/**
		 *
		 */
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $this->myCustomerRFQ->Prefix)) ;
		$this->skipMyLine() ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;

		$myCustomerRFQItem	=	new CustomerRFQItem() ;
		$myCustomerRFQItem->CustomerRFQNo	=	 $this->myCustomerRFQ->CustomerRFQNo ;
		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		$myArticle	=	new Article() ;
		/**
		 *
		 */
		$myItem	=	new FDbObject( "CustomerRFQItem", "Id", "def") ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "CustomerRFQNo = '".$this->myCustomerRFQ->CustomerRFQNo."' ") ;
		$myItem->setIterOrder( [ "ItemNo", "SubItemNo"]) ;
		foreach ( $myItem as $myCustomerRFQItem) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Pos=%d, ArticleNo='".$myCustomerRFQItem->ItemNo."', '" . $myCustomerRFQItem->ArticleNo. "'") ;
			if ( $myCustomerRFQItem->Quantity > 0) {
				$myArticle->setArticleNo( $myCustomerOrderItem->ArticleNo) ;
				/**
				 * Article Nummer
				 */
				$this->cellArticleNo->setData( $myCustomerRFQItem->ArticleNo) ;
				/**
				 * Article Descriptioneichnung zusammensetzen
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
				if ( strlen( $myCustomerRFQItem->SubItemNo) == 0) {
					$itemCnt++ ;
					$printLine	=	TRUE ;
				} else if ( $myCustomerRFQItem->myArticle->ArtType == 1) {
					if ( $myCustomerRFQItem->Price > 0.0) {
						$printLine	=	TRUE ;
					}
				}

				/**
				 * IF this is the main item, output all the data
				 */
				if ( $printLine) {

					$this->cellItemNo->setData( $itemCnt) ;
					$this->cellSubItemNo->setData( $myCustomerRFQItem->SubItemNo) ;

					/**
					 * do the required calculations
					 */
					$lineNet	=	$myCustomerRFQItem->TotalPrice ;
					$totalNet	+=	$lineNet ;
					if ( ! isset( $totalProTaxClass[ $myArticle->TaxClass]))
						$totalProTaxClass[ $myArticle->TaxClass]	=	0.0 ;
					$totalProTaxClass[ $myArticle->TaxClass]	+=	$lineNet ;

					/**
					 *
					 */
					$buf	=	sprintf( "%d\n%6.2f%%",
											$myCustomerRFQItem->Quantity,
											( $myCustomerRFQItem->Price - $myCustomerRFQItem->ReferencePrice ) / $myCustomerRFQItem->ReferencePrice * 100.0) ;
					$this->cellQuantity->setData( $buf) ;

					$buf	=	sprintf( "%9.2f\n%6.2f",
											$myCustomerRFQItem->ReferencePrice,
											( $myCustomerRFQItem->Price - $myCustomerRFQItem->ReferencePrice )) ;
					$cellPrice->setData( $buf) ;
					$buf	=	sprintf( "%9.2f",
											$myCustomerRFQItem->Quantity * $myCustomerRFQItem->Price) ;
					$cellCarryTo->setData( $totalNet) ;
					$cellCarryFrom->setData( $totalNet) ;
					$cellTotalPrice->setData( $buf) ;
					$cellTaxKey->setData( $myArticle->TaxClass) ;
					$subPosHeader	=	FALSE ;
				} else {
					if ( ! $subPosHeader) {
						$this->myKHRow->enable() ;
					}
					$this->cellSubItemNo->setData( $myCustomerRFQItem->SubItemNo) ;
					$this->cellItemNo->setData( "") ;
					$this->cellSubItemNo->setData( "") ;
					$buf	=	sprintf( "%d", $myCustomerRFQItem->Quantity) ;
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
					FDbg::dump( "CustomerRFQDoc::getPDF(...), Articlekomponenten werden ausgegeben") ;
					$this->showArticleKomp( $myCustomerRFQItem->ArticleNo) ;
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
		 * Discountzeile nur zeigen f�r Wiederverkaeufer Bestellungen (DMDEALER) oder Bestellungen gemaess Discount Modell V2.
		 * In jedem Fall muss der Discount > 0 sein, sonst macht es keinen Sinn den Discount aus zu geben.
		 */
		if ($this->myCustomerRFQ->Discount > 0) {

			/**
			 * now we can complete the setup the teble-end row
			 */
			$discount	=	( $totalProTaxClass['A'] * $this->myCustomerRFQ->Discount) / 100.0 ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( sprintf( "Nachlass, %.1f%% auf %.2f (ausschl. MwSt. Satz A)", $this->myCustomerRFQ->Discount, $totalProTaxClass['A']), $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $discount) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;

			/**
			 * now we can complete the setup the teble-end row
			 */
			$totalProTaxClass['A']	-=	$discount ;
			$totalNet	-=	$discount ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( "Warenwert nach Nachlass", $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $totalNet) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
		}

		/**
		 * Steuerzeile(n) nur ausgeben f�r Customern die wirklich Steuer bezahlen m�ssen.
		 */
//		if ( $this->myCustomerRFQ->getCustomer()->Tax == Opt::JA) {
			$taxes	=	0.0 ;
			foreach( $totalProTaxClass as $percentage => $mwstTotal) {
				$buf	=	sprintf( "Mehrwertsteuer (%s), %4.1f%% auf %.2f", $percentage, $this->percentage[ $percentage], $mwstTotal) ;
				$taxAmount	=	$mwstTotal * $this->percentage[ $percentage] / 100.0 ;
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
			$myRow->addCell( 3, new BCell( "Gesamtwert brutto", $this->cellParaFmtLeft)) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
//		}

		/**
		 *
		 */
		$this->endTable() ;

		/**
		 *
		 */
		if ( strlen( $this->myCustomerRFQ->CustRem) > 0) {
			$this->addMyText( "Customernbemerkung:") ;
			$this->addMyXML( iconv('UTF-8', 'windows-1252', $this->myCustomerRFQ->CustRem)) ;
		}
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $this->myCustomerRFQ->Postfix)) ;

		if ( $_pdfName == '') {
			$this->end( $this->path->Archive . "CustomerRFQ/" . $this->myCustomerRFQ->CustomerRFQNo . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
	}
	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( "Anfrage", $this->defParaFmt) ;
		$_frm->addLine( sprintf( "Anfrage Nr. %s, %s", $this->myCustomerRFQ->CustomerRFQNo, $this->myCustomerRFQ->Datum), $this->defParaFmt) ;

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
			if ( $this->myCustomerRFQ->getCustomer()->Tax == Opt::NEIN) {
				$this->addMyText( "Die Rechnungsstellung erfolgt ohne Mehrwertsteuer aufgrund Ihrer " .
									"Umsatzsteuerident Nr. " . $this->myCustomerRFQ->getCustomer()->UStId . " ") ;
			}
			break ;
		case	"shippingadr"	:
			if ( $this->myCustomerRFQ->getLiefCustomer()) {
				$this->addMyText( "Die Lieferung erfolgt an:") ;
				$this->addMyText( $this->myCustomerRFQ->getLiefCustomer()->CustomerName1) ;
				$this->addMyText( $this->myCustomerRFQ->getLiefCustomer()->CustomerName2) ;
				$this->addMyText( $this->myCustomerRFQ->getLiefCustomerContact()->getAttn()) ;
				$this->addMyText( $this->myCustomerRFQ->getLiefCustomer()->Street . " " . $this->myCustomerRFQ->getLiefCustomer()->Number) ;
				$this->addMyText( $this->myCustomerRFQ->getLiefCustomer()->ZIP . " " . $this->myCustomerRFQ->getLiefCustomer()->City) ;
			}
			break ;
		case	"invoicingadr"	:
			if ( $this->myCustomerRFQ->getRechCustomer()) {
				$this->addMyText( "Die Rechnungsstellung erfolgt an:\n") ;
				$this->addMyText( $this->myCustomerRFQ->getRechCustomer()->CustomerName1) ;
				$this->addMyText( $this->myCustomerRFQ->getRechCustomer()->CustomerName2) ;
				$this->addMyText( $this->myCustomerRFQ->getRechCustomerContact()->getAttn()) ;
				$this->addMyText( $this->myCustomerRFQ->getRechCustomer()->Street . " " . $this->myCustomerRFQ->getRechCustomer()->Number) ;
				$this->addMyText( $this->myCustomerRFQ->getRechCustomer()->ZIP . " " . $this->myCustomerRFQ->getRechCustomer()->City) ;
			}
			break ;
		case	"zahlbed"	:
			switch ( "de") {
			case	"de"	:
//				$this->addMyText( "Zahlungsbedingungen: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myCustomerRFQ->ModusSkonto)) ;
				break ;
			case	"en"	:
//				$this->addMyText( "Terms of payment: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myCustomerRFQ->ModusSkonto)) ;
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
	function	showArticleKomp( $_artikelNr) {
		FDbg::dump( "CustomerRFQDoc::showArticleKomp( '%s')", $_artikelNr) ;
		$this->myKHRow->enable() ;
		$actArtKomp	=	new ArtKomp() ;
		$actArtKomp->ArticleNo	=	$_artikelNr ;
		for ( $actArtKomp->firstFromDb() ; $actArtKomp->_valid == 1 ; $actArtKomp->nextFromDb()) {
			FDbg::dumpL( 99, "Pos=%d, ArticleNo=%s", $actArtKomp->ItemNo, $actArtKomp->CompArticleNo) ;
			if ( $actArtKomp->CompQuantity > 0) {

				/**
				 * set the table cell data
				 */
				$this->cellArticleNo->setData( $actArtKomp->ArticleNo) ;
				$myArticleText	=	$actArtKomp->getArticle()->getFullTextLF( $actArtKomp->CompQuantityPerPU) ;
				$this->cellArticleDescription1->setData( $myArticleText) ;
				$this->cellQuantity->setData( $actArtKomp->CompQuantity) ;

				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
			}
		}
	}

}

?>
