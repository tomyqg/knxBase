<?php
/**
 * ArticleReport.php Application Level Class for printed version of Article
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * 
 * Required following POST variables to be set:
 * 
 * _FMarketId		required to filter the "Market" for this price list
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocRegReport.php") ;
/**
 * ArticleReport - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage Article
 */
class	ArticleReport	extends BDocRegReport {
	private	$myArticle ;
	/**
	 *
	 */
	function	__construct() {
		FDbg::dumpL( 0x00000002, "ArticleReport.php::ArticleReport::__construct():") ;
	}
	/**
	 * 
	 * @param $_invNo
	 */
	function	setKey( $_invNo="") {
		FDbg::dumpL( 0x00000002, "ArticleReport.php::ArticleReport::setKey( _invNo):") ;
	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 */
	function	createPDF( $_key="", $_id=-1, $_pdfName="") {
		$this->_createPDF( $_key, $_id, $_pdfName) ;
	}
	function	getPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "Article Report/" . $this->myArticle->ArticleNo . ".pdf" ;
		}
		return $_pdfName ;
	}
	function	printPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "Article Report/" . $this->myArticle->ArticleNo . ".pdf" ;
		}
		if ( $this->cuComm->autoprint) {
			$cmd	=	"lpr -P " . $this->printer->cuComm . " " . $_pdfName ;
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
		$this->psRowMain->addCell( 0, new BCell( "Menge / VPE", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Netto", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Inkl. Mwst.", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "St. Schl.", $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRowMain) ;

		/**
		 * setup the second table header line
		 */
		$this->psRow	=	new BRow( BRow::RTHeaderPS) ;
		$this->psRow->addCell( 4, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 5, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 6, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 7, new BCell( "", $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRow) ;
		
		/**
		 * Aufsetzten der Zeile für "Übertrag von Seite ...:"
		 */
		$this->cfRow	=	new BRow( BRow::RTHeaderCF) ;
		$this->cfRow->addCell( 3, new BCell( "Uebertrag von Seite: %pp%", $this->cellParaFmtLeft)) ;
		$cellCarryFrom	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->cfRow->addCell( 6, $cellCarryFrom) ;
		$myTable->addRow( $this->cfRow) ;
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
		 * Aufsetzten der Zeile für "Übertrag auf Seite ...:"
		 */
		$this->ctRow	=	new BRow( BRow::RTFooterCT) ;
		$this->ctRow->addCell( 3, new BCell( "Uebertrag auf Seite: %np%", $this->cellParaFmtLeft)) ;
		$cellCarryTo	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->ctRow->addCell( 6, $cellCarryTo) ;
		$myTable->addRow( $this->ctRow) ;
				
		/**
		 *
		 */
		BDocRegReport::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegReport) ;

		$this->begin() ;

		/**
		 *
		 */
//		$this->addMyXML( iconv( 'UTF-8', 'windows-1252', $this->myArticle->Prefix)) ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;
		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		$myArticle	=	new Artikel() ;				// no specific object we need here
		$myArticle->addCol( "Preis", "float") ;
		$myArticle->addCol( "MengeProVPE", "int") ;
		$cond	=	"C.ArtikelNr like '".$_POST['_IFiltArticleNo']."' " ;
		if ( $_POST['_IFiltDescription'] != "")
			$cond	.=	"AND ( ArtikelBez1 like '%".$_POST['_IFiltDescription']."%' OR ArtikelBez2 like '%".$_POST['_IFiltDescription']."%') " ;
		if ( $_POST['_IFiltDesc1'] != "")
			$cond	.=	"AND ( ArtikelBez1 like '%".$_POST['_IFiltDesc1']."%') " ;
		if ( $_POST['_IFiltDesc2'] != "")
			$cond	.=	"AND ( ArtikelBez2 like '%".$_POST['_IFiltDesc1']."%') " ;
		$myArticle->setIterCond( $cond) ;
		$myArticle->setIterJoin( "LEFT JOIN VKPreisCache AS VKPC ON VKPC.ArtikelNr = C.ArtikelNr AND MarketId = '".$_POST['_IFiltMarketId']."' ",
									"VKPC.Preis AS Preis, VKPC.MengeProVPE AS MengeProVPE ") ;
		$myArticle->setIterOrder( "ORDER BY C.ArtikelNr, VKPC.MarketId, VKPC.MengeProVPE ") ;
// 		if ( isset( $_POST['_SArticleNo'])) {
// 			$query	.=	"AND Ii.ArticleNo LIKE '".$_POST['_SArticleNo']."' " ;
// 		}
// 		if ( isset( $_POST['_SArticleDescr'])) {
// 			$query	.=	"AND A.ArtikelBez1 LIKE '".$_POST['_SArticleDescr']."' " ;
// 		}
// 		if ( isset( $_POST['_SSuppNo'])) {
// 			$query	.=	"AND EKPr.LiefNr = '".$_POST['_SSuppNo']."' " ;
// 		}
// 		switch ( $_POST['_SOrder']) {
// 			case	Article::ORDR_STOCK	:
// 				$query	.=	"ORDER BY Ii.StockId, Ii.ShelfId, Ii.ArticleNo " ;
// 				break ;
// 			case	Article::ORDR_ARTNO	:
// 				$query	.=	"ORDER BY Ii.ArticleNo " ;
// 				break ;
// 		}
		foreach ( $myArticle as $key => $obj) {
			/**
			 * set the table cell data
			 */
			$this->cellArtikelNr->setData( $myArticle->ArtikelNr . "\n(" . $myArticle->ERPNo . ")") ;
			$myArtikelText	=	"" ;
			$myArtikelText	.=	iconv('UTF-8', 'windows-1252', $myArticle->ArtikelBez1) ;
			if ( strlen( $myArticle->ArtikelBez2) > 0) {
				$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArticle->ArtikelBez2) ;	
			}
			if ( strlen( $myArticle->MengenText) > 0) {
				$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArticle->MengenText) ;	
//			} else if ( $myArticle->MengeProVPE > 1) {
//				$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArtikel->textFromMenge( $myArticle->MengeProVPE)) ;
			}
			$this->cellArtikelBez1->setData( $myArtikelText) ;

			/**
			 * determine if we need to print this line and how we have to print it
			 * IF this is a main line, ->print it
			 * IF this is an option to an article, ->print it
			 */
			$printLine	=	TRUE ;
			/**
			 * IF this is the main item, output all the data
			 */

			$this->cellPosNr->setData( 0) ;
			$this->cellSubPosNr->setData( "") ;

			/**
			 * do the required calculations
			 */
			$buf	=	sprintf( "%3d", $myArticle->MengeProVPE) ;
			$this->cellMenge->setData( $buf) ;
			/**
			 *
			 */
			$buf	=	sprintf( "%9.2f / 1", $myArticle->Preis) ;
			$cellPreis->setData( $buf) ;
			$buf	=	sprintf( "%9.2f / 1", $myArticle->Preis * 1.19) ;
			$cellGesamtPreis->setData( $buf) ;
			$cellTaxKey->setData( $myArticle->MwstSatz) ;
			$cellCarryTo->setData( $totalNet) ;
			$cellCarryFrom->setData( $totalNet) ;
			$subPosHeader	=	FALSE ;
			$this->punchTable() ;
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
		$this->endTable() ;

		//
		$myPostfix	=	"" ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;
		
		if ( $_pdfName == '') {
			$_pdfName	=	$this->path->Catalog . "ArticleReport" ;
			if ( isset( $_POST['_SSuppNo'])) {
				$_pdfName	.=	"_" . $_POST['_SSuppNo'] ;
			}
			$_pdfName	.=	".pdf" ;
		} else {
		}
		$this->end( $_pdfName) ;
	}
	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Article Report")), $this->defParaFmt) ;
		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT',
									FTr::tr( "Article Report, Key date #1",
											array( "%s:".$this->today()))),
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
	}
	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
	}
}
?>
