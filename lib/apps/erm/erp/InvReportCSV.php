<?php
/**
 * InvReportCSV.php Application Level Class for printed version of Inv
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
/**
 * InvReportCSV - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage Inv
 */
class	InvReportCSV	extends	EISSCoreObject	{
	private	$myInv ;
	/**
	 *
	 */
	function	__construct() {
		FDbg::dumpL( 0x00000002, "InvReportCSVCSV.php::InvReportCSVCSV::__construct():") ;
	}
	/**
	 * 
	 * @param $_invNo
	 */
	function	setKey( $_invNo="") {
		FDbg::dumpL( 0x00000002, "InvReportCSV.php::InvReportCSV::setKey( _invNo):") ;
		$this->myInv	=	new Inv( $_invNo) ;
		$this->_valid	=	$this->myInv->_valid ;
		$this->lang	=	"de" ;
	}
	/**
	 * create
	 * create the  document and returns the complete filename (path+file)
	 * hooked to hdlObject()
	 */
	function	create( $_key="", $_id=-1, $_csvName="") {
		$this->_create( $_key, $_id, $_csvName) ;
		return $this->myInv->getXMLComplete() ;
	}
	function	get( $_key="", $_id=-1, $_csvName="") {
		if ( $_csvName == "") {
			$_csvName	=	$this->path->Archive . "Inventory/" . $this->myInv->InvNo . ".csv" ;
		}
		return $_csvName ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_csvName
	 */
	function	_create( $_key="", $_id=-1, $_csvName="") {
		/**
		 *
		 */
		$myCSV	=	"" ;
		$csvSep	=	";" ;
		$csvQuote	=	"\"" ;
		$lastCuDlvrNo	=	"" ;
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
			$myCSV	.=	$myItem->ItemNo .$csvSep ;
			$myCSV	.=	$csvQuote . $myItem->ArticleNo . $csvQuote . $csvSep . $csvQuote . $myItem->ERPNo . $csvQuote . $csvSep ;
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
			$myCSV	.=	"\"" . $myArtikelText . "\"" . $csvSep ;


			/**
			 * do the required calculations
			 */
			if ( $myItem->MengeFuerPreis > 0) {
				$lineNet	=	$myItem->QtyIn * $myItem->Preis / $myItem->MengeFuerPreis ;
			} else {
				$lineNet	=	0.0 ;
			}
			$totalNet	+=	$lineNet ;
			$buf	=	sprintf( "%d", $myItem->QtyIn) ;
			$myCSV	.=	$buf .$csvSep ;
			/**
			 *
			 */
			$buf	=	str_replace( ".", ",", sprintf( "%.2f", $myItem->Preis)) ;
			$myCSV	.=	$buf . $csvSep ;
			$buf	=	str_replace( ".", ",", sprintf( "%.2f", $lineNet)) ;
			$myCSV	.=	$buf . $csvSep ;
			$myCSV	.=	str_replace( ".", ",", sprintf( "%.2f", $totalNet)) ;
			$myCSV	.=	"\n" ;
		}
		
		if ( $_csvName == '') {
			$_csvName	=	$this->path->Archive . "Inventory/" . $this->myInv->InvNo ;
			if ( isset( $_POST['_SSuppNo'])) {
				if ( $_POST['_SSuppNo'] != "%")
					$_csvName	.=	"_" . $_POST['_SSuppNo'] ;
			}
			$_csvName	.=	".csv" ;
		} else {
		}
		$myFile	=	fopen( $_csvName, "w+") ;
		fwrite( $myFile, $myCSV) ;
		error_log( $myCSV) ;
		fclose( $myFile) ;
	}
}
?>
