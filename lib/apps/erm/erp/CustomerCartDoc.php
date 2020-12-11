<?php
/**
 * CustomerCartDoc.php Application Level Class for printed version of CustomerCart
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * CustomerCartDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage CustomerCart
 */
class	CustomerCartDoc	extends BDocRegLetter {

	private	$myCustomerCart ;
	private	$totalProTaxClass	=	array() ;
	private	$taxClass	=	array( "A" => 19.0, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	private	$cellCharFmt ;
	private	$cellParaFmtLeft ;
	private	$cellParaFmtCenter ;
	private	$cellParaFmtRight ;
	public	$_valid	=	false ;

	/**
	 *
	 */
	function	__construct( $_key, $_formMode=true) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( false) ;
		$this->formMode	=	$_formMode ;
		$this->pdfName	=	$_key . ".pdf" ;
		$this->fullPDFName	=	$this->path->Archive . "CustomerCart/" . $_key . ".pdf" ;
		$this->setKey( $_key) ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setKey( $_customerCartNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_customerCartNo')") ;
		$this->myCustomerCart	=	new CustomerCart( $_customerCartNo) ;
		$this->_valid	=	$this->myCustomerCart->_valid ;
		$this->lang	=	$this->myCustomerCart->getCustomer()->Language ;
		FDbg::end( 1, "CustomerCartDoc.php", "CustomerCartDoc", "setKey( ...)") ;
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
		$this->psRowMain->addCell( 0, new BCell( "Item", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Article Nr.", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Description", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Qty.", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Unit Price", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Total", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Tax", $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRowMain) ;

		/**
		 * setup the second table header line
		 */
		$this->psRow	=	new BRow( BRow::RTHeaderPS) ;
		$this->psRow->addCell( 4, new BCell( "+/- %", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 5, new BCell( "Discount", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 6, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 7, new BCell( "key", $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRow) ;

		/**
		 * Aufsetzten der Zeile f�r "�bertrag von Seite ...:"
		 */
		$this->cfRow	=	new BRow( BRow::RTHeaderCF) ;
		$this->cfRow->addCell( 3, new BCell( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Carry over from page: %pp%", null, $this->lang)), $this->cellParaFmtLeft)) ;
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

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252', $this->myCustomerCart->getCustomer()->CustomerName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252', $this->myCustomerCart->getCustomer()->CustomerName2)) ;
//		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252', $this->myCustomerCart->getCustomerContact()->getAttentionLine())) ;
//		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252', $this->myCustomerCart->getCustomer()->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252', $this->myCustomerCart->getCustomer()->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252', $this->myCustomerCart->getCustomer()->getAddrCountry())) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Shopping Cart", null, $this->lang)), "") ;
		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Cart no.", null, $this->lang).":"), $this->myCustomerCart->CustomerCartNo) ;
		$this->setInfo( 3, FTr::tr( "Date", null, $this->lang).":", $this->myCustomerCart->Date) ;
		$this->setInfo( 4, FTr::tr( "Customer no.", null, $this->lang).":", $this->myCustomerCart->CustomerNo . "/" . $this->myCustomerCart->CustomerContactNo) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, FTr::tr( "Customer", null, $this->lang).":", "") ;
		$this->setInfo( 8, FTr::tr( "Ref. no.", null, $this->lang).":", $this->myCustomerCart->CustomerReferenceNo) ;
		$this->setInfo( 9, FTr::tr( "Ref. date", null, $this->lang).":", $this->myCustomerCart->CustomerReferenceDate) ;

		$this->setRef( FTr::tr( "DOC-REF-CUCART")) ;

		$this->begin() ;

		/**
		 *
		 */
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $this->myCustomerCart->Prefix)) ;
		$this->skipMyLine() ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;

		$myCustomerCartItem	=	new CustomerCartItem( $this->myCustomerCart->CustomerCartNo) ;

		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		$myArticle	=	new Article() ;
		/**
		 *
		 */
		$myItem	=	new FDbObject( "CustomerCartItem", "Id", "def") ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "CustomerCartNo = '".$this->myCustomerCart->CustomerCartNo."' ") ;
		$myItem->setIterOrder( [ "ItemNo", "SubItemNo"]) ;
		foreach ( $myItem as $myCustomerCartItem) {
			if ( $myCustomerCartItem->Quantity > 0) {
				$myArticle->setArticleNo( $myCustomerOrderItem->ArticleNo) ;
				/**
				 * Article Nummer
				 */
				$this->cellArticleNo->setData( $myCustomerCartItem->ArticleNo) ;

				/**
				 * Article Descriptioneichnung zusammensetzen
				 */
				$myArticleText	=	"" ;
				if ( strlen( $myCustomerCartItem->AddText) > 0) {
					$myArticleText	.=	$myCustomerCartItem->AddText . "\n" ;
				}
				$myArticleText	.=	iconv('UTF-8', 'windows-1252', $myArticle->ArticleDescription1) ;
				if ( strlen( $myArticle->ArticleDescription2) > 0) {
					$myArticleText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArticle->ArticleDescription2) ;
				} else if ( $myCustomerCartItem->QuantityPerPU > 1) {
					$myArticleText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArticle->textFromQuantity( $myCustomerOrderItem->QuantityPerPU)) ;
				}
				$this->cellArticleDescription1->setData( $myArticleText) ;

				/**
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$printLine	=	FALSE ;
				if ( strlen( $myCustomerCartItem->SubItemNo) == 0) {
					$itemCnt++ ;
					$printLine	=	TRUE ;
				} else if ( $myCustomerCartItem->myArticle->ArtType == 1) {
					if ( $myCustomerCartItem->Price > 0.0) {
						$printLine	=	TRUE ;
					}
				}

				/**
				 * IF this is the main item, output all the data
				 */
				if ( $printLine) {

					$this->cellItemNo->setData( $itemCnt) ;
					$this->cellSubItemNo->setData( $myCustomerCartItem->SubItemNo) ;

					/**
					 * do the required calculations
					 */
					$lineNet	=	$myCustomerCartItem->TotalPrice ;
					$totalNet	+=	$lineNet ;
					if ( ! isset( $totalProTaxClass[ $myArticle->TaxClass]))
						$totalProTaxClass[ $myArticle->TaxClass]	=	0.0 ;
					$totalProTaxClass[ $myArticle->TaxClass]	+=	$lineNet ;

					/**
					 *
					 */
					$buf	=	sprintf( "%d\n%6.2f%%",
											$myCustomerCartItem->Quantity,
											( $myCustomerCartItem->Price - $myCustomerCartItem->ReferencePrice ) / $myCustomerCartItem->ReferencePrice * 100.0) ;
					$this->cellQuantity->setData( $buf) ;

					$buf	=	sprintf( "%9.2f\n%6.2f",
											$myCustomerCartItem->ReferencePrice,
											( $myCustomerCartItem->Price - $myCustomerCartItem->ReferencePrice )) ;
					$cellPrice->setData( $buf) ;
					$buf	=	sprintf( "%9.2f",
											$myCustomerCartItem->Quantity * $myCustomerCartItem->Price) ;
					$cellCarryTo->setData( $totalNet) ;
					$cellCarryFrom->setData( $totalNet) ;
					$cellTotalPrice->setData( $buf) ;
					$cellTaxKey->setData( $myArticle->TaxClass) ;
					$subPosHeader	=	FALSE ;
				} else {
					if ( ! $subPosHeader) {
						$this->myKHRow->enable() ;
					}
//					$this->cellSubItemNo->setData( $myCustomerCartItem->SubItemNo) ;
					$this->cellItemNo->setData( "") ;
					$this->cellSubItemNo->setData( "") ;
					$buf	=	sprintf( "%d", $myCustomerCartItem->Quantity) ;
					$this->cellQuantity->setData( $buf) ;
					$cellPrice->setData( "") ;
					$cellTotalPrice->setData( "") ;
					$cellTaxKey->setData( "") ;
					$subPosHeader	=	TRUE ;
				}

				$this->punchTable( false) ;
				$this->myKHRow->disable() ;		// disable in every case
				if ( $myArticle->CompositionType == 1 && $myArticle->ModeCustomerOrder == 2) {
					$this->cellItemNo->setData( "") ;
					$this->cellSubItemNo->setData( "") ;
					$cellPrice->setData( "") ;
					$cellTotalPrice->setData( "") ;
					$cellTaxKey->setData( "") ;

					$this->myKHRow->enable() ;
					FDbg::dump( "CustomerCartDoc::getPDF(...), Articlekomponenten werden ausgegeben") ;
					$this->showArticleKomp( $myCustomerCartItem->getArticle()->ArticleNo) ;
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
		 * Rabattzeile nur zeigen f�r Wiederverkaeufer Bestellungen (DMDEALER) oder Bestellungen gemaess Rabatt Modell V2.
		 * In jedem Fall muss der Rabatt > 0 sein, sonst macht es keinen Sinn den Rabatt aus zu geben.
		 */
		if ( $this->myCustomerCart->Rabatt > 0) {

			/**
			 * now we can complete the setup the teble-end row
			 */
			$rabatt	=	( $totalProTaxClass['A'] * $this->myCustomerCart->Rabatt) / 100.0 ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( sprintf( "Nachlass, %.1f%% auf %.2f (ausschl. MwSt. Satz A)", $this->myCustomerCart->Rabatt, $totalProTaxClass['A']), $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $rabatt) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;

			/**
			 * now we can complete the setup the teble-end row
			 */
			$totalProTaxClass['A']	-=	$rabatt ;
			$totalNet	-=	$rabatt ;
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( "Warenwert nach Nachlass", $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $totalNet) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;
		}

		/**
		 * Steuerzeile(n) nur ausgeben f�r Customern die wirklich Steuer bezahlen m�ssen.
		 */
//		if ( $this->myCustomerCart->getCustomer()->Tax == Opt::JA) {
			$taxes	=	0.0 ;
			foreach( $totalProTaxClass as $taxClass => $totalTax) {
				$buf	=	sprintf( "Mehrwertsteuer (%s), %4.1f%% auf %.2f", $taxClass, $this->taxClass[ $taxClass], $totalTax) ;
				$taxAmount	=	$totalTax * $this->taxClass[ $taxClass] / 100.0 ;
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
			$myRow->addCell( 3, new BCell( "Gesamtswert brutto", $this->cellParaFmtLeft)) ;
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
		if ( strlen( $this->myCustomerCart->CustRem) > 0) {
			$this->addMyText( "Customernbemerkung:") ;
			$this->addMyXML( iconv('UTF-8', 'windows-1252', $this->myCustomerCart->CustRem)) ;
		}
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $this->myCustomerCart->Postfix)) ;

		if ( $_pdfName == '') {
			$this->end( $this->path->Archive . "CustomerCart/" . $this->myCustomerCart->CustomerCartNo . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
		return $this->myCustomerCart->getAsXML() ;
	}

	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( "CustomerCart", $this->defParaFmt) ;
		$_frm->addLine( sprintf( "CustomerCart Nr. %s, %s", $this->myCustomerCart->CustomerCartNo, $this->myCustomerCart->Datum), $this->defParaFmt) ;

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
			if ( $this->myCustomerCart->getCustomer()->Tax == Opt::NEIN) {
				$this->addMyText( "Die Rechnungsstellung erfolgt ohne Mehrwertsteuer aufgrund Ihrer " .
									"Umsatzsteuerident Nr. " . $this->myCustomerCart->getCustomer()->UStId . " ") ;
			}
			break ;
		case	"shippingadr"	:
			if ( $this->myCustomerCart->getLiefCustomer()) {
				$this->addMyText( "Die Lieferung erfolgt an:") ;
				$this->addMyText( $this->myCustomerCart->getLiefCustomer()->CustomerName1) ;
				$this->addMyText( $this->myCustomerCart->getLiefCustomer()->CustomerName2) ;
				$this->addMyText( $this->myCustomerCart->getLiefCustomerContact()->getAttn()) ;
				$this->addMyText( $this->myCustomerCart->getLiefCustomer()->Street . " " . $this->myCustomerCart->getLiefCustomer()->Number) ;
				$this->addMyText( $this->myCustomerCart->getLiefCustomer()->ZIP . " " . $this->myCustomerCart->getLiefCustomer()->City) ;
			}
			break ;
		case	"invoicingadr"	:
			if ( $this->myCustomerCart->getRechCustomer()) {
				$this->addMyText( "Die Rechnungsstellung erfolgt an:\n") ;
				$this->addMyText( $this->myCustomerCart->getRechCustomer()->CustomerName1) ;
				$this->addMyText( $this->myCustomerCart->getRechCustomer()->CustomerName2) ;
				$this->addMyText( $this->myCustomerCart->getRechCustomerContact()->getAttn()) ;
				$this->addMyText( $this->myCustomerCart->getRechCustomer()->Street . " " . $this->myCustomerCart->getRechCustomer()->Number) ;
				$this->addMyText( $this->myCustomerCart->getRechCustomer()->ZIP . " " . $this->myCustomerCart->getRechCustomer()->City) ;
			}
			break ;
		case	"zahlbed"	:
			switch ( "de") {
			case	"de"	:
				$this->addMyText( "Zahlungsbedingungen: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myCustomerCart->ModusSkonto)) ;
				break ;
			case	"en"	:
				$this->addMyText( "Terms of payment: " . Opt::optionReturn( Opt::getRModusSkonto(), $this->myCustomerCart->ModusSkonto)) ;
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
		FDbg::dump( "CustomerCartDoc::showArticleKomp( '%s')", $_artikelNr) ;
		$this->myKHRow->enable() ;
		$actArtKomp	=	new ArtKomp() ;
		$actArtKomp->ArticleNo	=	$_artikelNr ;
		for ( $actArtKomp->firstFromDb() ; $actArtKomp->_valid == 1 ; $actArtKomp->nextFromDb()) {
			FDbg::dumpL( 99, "Pos=%d, ArticleNo=%s", $actArtKomp->ItemNo, $actArtKomp->CompArticleNo) ;
			if ( $actArtKomp->CompQuantity > 0) {

				/**
				 * set the table cell data
				 */
				$this->cellArticleNo->setData( $actArtKomp->getArticle()->ArticleNo) ;
				$myArticleText	=	$actArtKomp->getArticle()->getFullTextLF( $actArtKomp->CompQuantityProVPE) ;
				$this->cellArticleDescription1->setData( $myArticleText) ;
				$this->cellQuantity->setData( $actArtKomp->CompQuantity) ;

				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
			}
		}
	}

}

?>
