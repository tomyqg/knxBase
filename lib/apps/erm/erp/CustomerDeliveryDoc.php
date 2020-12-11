<?php

/**
 * CustomerDeliveryDoc.php Application Level Class for printed version of CustomerDelivery
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * CustomerDeliveryDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage CustomerDelivery
 */
class	CustomerDeliveryDoc	extends BDocRegLetter {
	/**
	 *
	 */
	private	$myCustomerDelivery ;
	private	$totalProMwstSatz	=	array() ;
	private	$mwstSatz	=	array( "A" => 19.0, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
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
		$this->fullPDFName	=	$this->path->Archive . "CustomerDelivery/" . $_key . ".pdf" ;
		$this->setKey( $_key) ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setKey( $_customerDeliveryNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_customerDeliveryNo')") ;
		$this->myCustomerDelivery	=	new CustomerDelivery( $_customerDeliveryNo) ;
		$this->_valid	=	$this->myCustomerDelivery->_valid ;
		$this->lang	=	$this->myCustomerDelivery->getCustomer()->Language ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	archive() {
		/**
		 * create the bill-of-delivery-original (Lieferschein-Original)
		 */
		$pdfTargetName	=	$this->path->Archive . "CustomerDelivery/" . $this->myCustomerDelivery->CustomerDeliveryNo . ".pdf" ;
		$this->getPDF( $pdfTargetName) ;

		/**
		 * create the bill-of-delivery-copy (Lieferschein-Kopie)
		 */
		$pdfTargetNameKopie	=	$this->path->Archive . "CustomerDelivery/" . $this->myCustomerDelivery->CustomerDeliveryNo . "-Kopie.pdf" ;
		overlayPDF( $pdfTargetNameKopie, $pdfTargetName, $this->path->Archive . "overlay_kopie_a4.pdf") ;

	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 */
	function	createPDF( $_key="", $_id=-1, $_pdfName="", $reply=null) {
		$myColWidths	=	array( /*10,*/ 10, 23, 70, 17, 17, 17) ;
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
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Delivered\nearlier", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Delivered\nnow", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Delivered\nlater", null, $this->lang), $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRowMain) ;
		/**
		 *
		 */
		$myKHRow	=	new BRow( BRow::RTDataIT) ;
		$cellArtKompHeader	=	new BCell( "Further details (Componenets):\n", $this->cellParaFmtLeft) ;
		$myKHRow->addCell( 3, $cellArtKompHeader) ;
		$myKHRow->disable() ;
		$myTable->addRow( $myKHRow) ;
		/**
		 *
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
		$this->cellQuantityDeliveredAlready	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellQuantityDeliveredAlready) ;
		$this->cellQuantityDelivered	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellQuantityDelivered) ;
		$this->cellQuantityYetToDeliver	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellQuantityYetToDeliver) ;
		$myTable->addRow( $myRow) ;

		/**
		 *
		 */
		BDocRegLetter::__construct( $_copy) ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252', $this->myCustomerDelivery->getCustomer()->CustomerName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252', $this->myCustomerDelivery->getCustomer()->CustomerName2)) ;
//		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252', $this->myCustomerDelivery->getCustomerContact()->getAttentionLine())) ;
//		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252', $this->myCustomerDelivery->getCustomer()->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252', $this->myCustomerDelivery->getCustomer()->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252', $this->myCustomerDelivery->getCustomer()->getAddrCountry())) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Bill of delivery", null, $this->lang)), "") ;
		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Delivery no.", null, $this->lang).":"), $this->myCustomerDelivery->CustomerDeliveryNo) ;
		$this->setInfo( 3, FTr::tr( "Date", null, $this->lang).":", $this->myCustomerDelivery->Date) ;
		$this->setInfo( 4, FTr::tr( "Customer no.", null, $this->lang).":", $this->myCustomerDelivery->CustomerNo . "/" . $this->myCustomerDelivery->CustomerContactNo) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, FTr::tr( "Customer", null, $this->lang).":", "") ;
		$this->setInfo( 8, FTr::tr( "Ref. no.", null, $this->lang).":", $this->myCustomerDelivery->CustomerReferenceNo) ;
		$this->setInfo( 9, FTr::tr( "Ref. date", null, $this->lang).":", $this->myCustomerDelivery->CustomerReferenceDate) ;

		$this->setRef( FTr::tr( "Bill of delivery", null, $this->lang)) ;

		$this->begin() ;

		/**
		 *
		 */
		$this->addMyXML( iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myCustomerDelivery->Prefix)) ;

		$this->addMyText() ;
		$this->addMyText() ;

		/**
		 *
		 */
		$lastItemNo	=	0 ;
		$this->addTable( $myTable) ;
		$itemCnt	=	0 ;
		$myArticle	=	new Article() ;
		/**
		 *
		 */
		$myItem	=	new FDbObject( "CustomerDeliveryItem", "Id", "def") ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "CustomerDeliveryNo = '".$this->myCustomerDelivery->CustomerDeliveryNo."' ") ;
		$myItem->setIterOrder( [ "ItemNo", "SubItemNo"]) ;
		foreach ( $myItem as $myCustomerDeliveryItem) {
			if ( $myCustomerDeliveryItem->Quantity != 0 || $myCustomerDeliveryItem->QuantityDelivered != 0) {
				$myArticle->setArticleNo( $myCustomerDeliveryItem->ArticleNo) ;
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
				$this->cellQuantityDeliveredAlready->setData( $myCustomerDeliveryItem->QuantityDeliveredAlready) ;
				$this->cellQuantityDelivered->setData( $myCustomerDeliveryItem->QuantityDelivered) ;
				$this->cellQuantityYetToDeliver->setData( $myCustomerDeliveryItem->Quantity - $myCustomerDeliveryItem->QuantityDeliveredAlready - $myCustomerDeliveryItem->QuantityDelivered) ;

				/**
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$printLine	=	FALSE ;
				if ( strlen( $myCustomerDeliveryItem->SubItemNo) == 0) {
					$printLine	=	TRUE ;
					$itemCnt++ ;
				} else if ( $myCustomerDeliveryItem->Quantity > 0 || $myCustomerDeliveryItem->QuantityDelivered > 0) {
					$printLine	=	TRUE ;
				}

				/**
				 *
				 */
				if ( $lastItemNo === $myCustomerDeliveryItem->ItemNo && ! $subPosHeader) {
					$myKHRow->enable() ;
					$subPosHeader	=	TRUE ;
				}
				if ( $lastItemNo !== $myCustomerDeliveryItem->ItemNo) {
					$subPosHeader	=	FALSE ;
				}

				/**
				 * IF this is the main item, output all the data
				 */
				if ( $printLine) {
					/**
					 *
					 */
					$this->cellItemNo->setData( $itemCnt) ;
					$this->cellSubItemNo->setData( $myCustomerDeliveryItem->SubItemNo) ;
					$this->punchTable() ;
					$myKHRow->disable() ;		// disable in every case
				}

				$lastItemNo	=	$myCustomerDeliveryItem->ItemNo ;
			}
			$this->emptyTableRow( 5) ;
		}

		//
		$this->endTable() ;

		/*
		 *
		 */
		$myCarr	=	new Carr( $this->myCustomerDelivery->Carrier) ;
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Carrier", "#PkgCount", "#TrckCodes") ;
		$myReplTableOut	=	array( $this->myCustomerDelivery->getCustomer()->CustomerName1, $this->myCustomerDelivery->Date, $myCarr->FullName, $this->myCustomerDelivery->AnzahlPakete,"") ;
		$myPostfix	=	str_replace( $myReplTableIn, $myReplTableOut, $this->myCustomerDelivery->Postfix) ;
		$this->addMyXML( iconv( 'UTF-8', 'windows-1252//TRANSLIT', $myPostfix)) ;

		if ( $_pdfName == '') {
			$_pdfName	=	$this->path->Archive . "CustomerDelivery/" . $this->myCustomerDelivery->CustomerDeliveryNo ;
			if ( $_copy)
				$_pdfName	.=	"-Kopie" ;
			$_pdfName	.=	".pdf" ;
		}
		$this->end( $_pdfName) ;

		return $_pdfName ;

	}

	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( "Lieferschein", $this->defParaFmt) ;
		$_frm->addLine( sprintf( "Lieferung Nr. %s, %s", $this->myCustomerDelivery->CustomerDeliveryNo, $this->myCustomerDelivery->Datum), $this->defParaFmt) ;

		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
								$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;
			}

	//
	function	cascTokenStart( $_token) {
		switch ( $_token) {
		default	:
			FDbg::dump( "CustomerDeliveryDoc::cascTokenStart: invalid token received (non-critical)") ;
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
	function	printIt( $_prn) {

		global	$archivPath ;

		if ( strcmp( $_prn, "-") != 0) {
			$systemCmd	=	"lpr -P " . $_prn . " " . $this->path->Archive . "CustomerDelivery/" . $this->myCustomerDelivery->CustomerDeliveryNo . ".pdf " ;
			FDbg::dumpL( 0x01000000, "CustomerDeliveryDoc::printIt: systemCmd='%s'", $systemCmd) ;
			system( $systemCmd) ;
		}

	}

}

?>
