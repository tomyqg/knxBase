<?php
/**
 * InvReport.php Application Level Class for printed version of Inv
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocRegReport.php") ;
/**
 * InvReport - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage Inv
 */
class	InvReport	extends BDocRegReport {
	private	$myInv ;
	/**
	 *
	 */
	function	__construct() {
		FDbg::dumpL( 0x00000002, "InvReport.php::InvReport::__construct():") ;
	}
	/**
	 * 
	 * @param $_invNo
	 */
	function	setKey( $_invNo="") {
		FDbg::dumpL( 0x00000002, "InvReport.php::InvReport::setKey( _invNo):") ;
		$this->myInv	=	new Inv( $_invNo) ;
		$this->_valid	=	$this->myInv->_valid ;
		$this->lang	=	"de" ;
	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 */
	function	createPDF( $_key="", $_id=-1, $_pdfName="") {
		$this->_createPDF( $_key, $_id, $_pdfName) ;
		return $this->myInv->getXMLComplete() ;
	}
	function	getPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "Inventory/" . $this->myInv->InvNo . ".pdf" ;
		}
		return $_pdfName ;
	}
	function	printPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "Inventory/" . $this->myInv->InvNo . ".pdf" ;
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
		$this->psRowMain->addCell( 0, new BCell( "Menge", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Einzel", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Gesamt", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "St.", $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRowMain) ;

		/**
		 * setup the second table header line
		 */
		$this->psRow	=	new BRow( BRow::RTHeaderPS) ;
		$this->psRow->addCell( 4, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 5, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 6, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRow->addCell( 7, new BCell( "Schl.", $this->cellParaFmtRight)) ;
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
		$this->addMyXML( iconv( 'UTF-8', 'windows-1252', $this->myInv->Prefix)) ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;
		$myItem	=	new InvItem() ;
		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "InvNo", "var") ;
		$myObj->addCol( "ArticleNo", "var") ;
		$myItem->addCol( "ERPNo", "var") ;
		$myItem->addCol( "ArtikelBez1", "var") ;
		$myItem->addCol( "ArtikelBez2", "var") ;
		$myItem->addCol( "MengenText", "var") ;
		$myItem->addCol( "LiefNr", "var") ;
		$myItem->addCol( "Preis", "var") ;
		$myItem->addCol( "Waehrung", "var") ;
		$myItem->addCol( "VonKurs", "var") ;
		$myItem->addCol( "NachKurs", "var") ;
		$query	=	"SELECT Ii.*, A.ERPNo, A.ArtikelBez1, A.ArtikelBez2, EKPr.LiefNr, AEKP.Preis, AEKP.MengeFuerPreis, AEKP.Waehrung, C.VonKurs, C.NachKurs " ;
		$query	.=	"FROM InvItem as Ii " ;
		$query	.=	"LEFT JOIN Artikel AS A ON A.ArtikelNr = Ii.ArticleNo " ;
		$query	.=	"LEFT JOIN EKPreisR AS EKPr ON EKPr.ArtikelNr = Ii.ArticleNo AND EKPr.KalkBasis > 0 " ;
		$query	.=	"LEFT JOIN ArtikelEKPreis AS AEKP ON AEKP.LiefNr = EKPr.LiefNr AND AEKP.LiefArtNr = EKPr.LiefArtNr AND AEKP.Menge = EKPr.KalkBasis AND AEKP.GueltigVon < '".$this->myInv->KeyDate."' AND AEKP.GueltigBis >= '".$this->myInv->KeyDate."' " ;
		$query	.=	"LEFT JOIN Currency AS C ON C.VonWaehrung = AEKP.Waehrung AND C.NachWaehrung = 'EUR' AND C.CGueltigVon < '".$this->myInv->KeyDate."' AND C.CGueltigBis >= '".$this->myInv->KeyDate."' " ;
		$query	.=	"WHERE Ii.InvNo = '".$this->myInv->InvNo."' " ;		//AND A.ArtikelNr IS NOT NULL " ;
		if ( isset( $_POST['_SArticleNo'])) {
			$query	.=	"AND Ii.ArticleNo LIKE '".$_POST['_SArticleNo']."' " ;
		}
		if ( isset( $_POST['_SArticleDescr'])) {
			$query	.=	"AND A.ArtikelBez1 LIKE '".$_POST['_SArticleDescr']."' " ;
		}
		if ( isset( $_POST['_SSuppNo'])) {
			$query	.=	"AND EKPr.LiefNr = '".$_POST['_SSuppNo']."' " ;
		}
		switch ( $_POST['_SOrder']) {
			case	Inv::ORDR_STOCK	:
				$query	.=	"ORDER BY Ii.StockId, Ii.ShelfId, Ii.ArticleNo " ;
				break ;
			case	Inv::ORDR_ARTNO	:
				$query	.=	"ORDER BY Ii.ArticleNo " ;
				break ;
		}
		$myRes	=	FDb::query( $query) ;
		while ( $row = FDb::getRow( $myRes)) {
			$myItem->assignFromRow( $row) ;
			/**
			 * set the table cell data
			 */
			$this->cellArtikelNr->setData( $myItem->ArticleNo . "\n(" . $myItem->ERPNo . ")") ;
			$myArtikelText	=	"" ;
			$myArtikelText	.=	iconv('UTF-8', 'windows-1252', $myItem->ArtikelBez1) ;
			if ( strlen( $myItem->ArtikelBez2) > 0) {
				$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myItem->ArtikelBez2) ;	
			}
			if ( strlen( $myItem->MengenText) > 0) {
				$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myItem->MengenText) ;	
//			} else if ( $myItem->MengeProVPE > 1) {
//				$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myArtikel->textFromMenge( $myItem->MengeProVPE)) ;
			}
			$this->cellArtikelBez1->setData( $myArtikelText) ;
			$this->cellMenge->setData( $myItem->QtyIn) ;

			/**
			 * determine if we need to print this line and how we have to print it
			 * IF this is a main line, ->print it
			 * IF this is an option to an article, ->print it
			 */
			$printLine	=	TRUE ;
			/**
			 * IF this is the main item, output all the data
			 */

			$this->cellPosNr->setData( $myItem->ItemNo) ;
			$this->cellSubPosNr->setData( "") ;

			/**
			 * do the required calculations
			 */
			if ( $myItem->MengeFuerPreis > 0) {
				$lineNet	=	$myItem->QtyIn * $myItem->Preis / $myItem->MengeFuerPreis ;
			} else {
				$lineNet	=	0.0 ;
			}
			$totalNet	+=	$lineNet ;
			$buf	=	sprintf( "%d(%d)", $myItem->QtyIn, $myItem->QtyOut) ;
			$this->cellMenge->setData( $buf) ;
			/**
			 *
			 */
			$buf	=	sprintf( "%9.2f", $myItem->Preis) ;
			$cellPreis->setData( $buf) ;
			$buf	=	sprintf( "%9.2f", $lineNet) ;
			$cellGesamtPreis->setData( $buf) ;
			$cellTaxKey->setData( "") ;
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
		$myRow	=	new BRow( BRow::RTFooterTE) ;
		$myRow->addCell( 3, new BCell( "Warenwert netto", $this->cellParaFmtLeft)) ;
		$buf2	=	sprintf( "%.2f", $totalNet) ;
		$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
		$myTable->addRow( $myRow) ;
		//
		$this->endTable() ;

		//
		$myPostfix	=	"" ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;
		
		if ( $_pdfName == '') {
			$_pdfName	=	$this->path->Archive . "Inventory/" . $this->myInv->InvNo ;
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

		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Inventory", null, $this->lang)), $this->defParaFmt) ;
		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT',
									FTr::tr( "Inventory no. #1, Key date #2",
											array( "%s:".$this->myInv->InvNo, "%s:".$this->myInv->KeyDate),
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
	}
	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
	}
}
?>
