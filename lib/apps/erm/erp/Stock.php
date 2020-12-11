<?php

/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package WTA
 */

/**
 * requires the WTF Debugger and the Base Class
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/AppObject.php") ;
require_once( "base/AppDepObject.php") ;
require_once( "option.php") ;
/**
 * Stock - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BStock which should
 * not be modified.
 *
 * @package WTA
 * @subpackage Article
 */
class	Stock	extends	AppObject	{

	const	STD		=	0 ;
	const	INV_OUT	=	11 ;
	const	INV_IN	=	12 ;
	private	static	$rType	=	array (
						-1				=> "ALL",
						Stock::STD		=> "standard",
						Stock::INV_OUT	=> "inventory out",
						Stock::INV_IN	=> "inventory in"
					) ;
	var	$startRow	=	0 ;
	var	$rowCount	=	10 ;
	
	/**
	 *
	 */
	function	getRType() {
		return self::$rType ;
	}
					
	/*
	 * The constructor can be passed an ArticleNr (StockId), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_stockNr="") {
		FDbg::dumpL( 0x01000000, "Stock::__construct(...): ") ;
		parent::__construct( "Stock", "StockId") ;
		if ( strlen( $_stockNr) >= 0) {
			$this->setStockId	=	$_stockNr ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setStockId( $_stockNr='') {
		FDbg::dumpL( 0x01000000, "Stock::setId(...): ") ;
		if ( strlen( $_stockNr) > 0) {
			$this->StockId	=	$_stockNr ;
			$this->reload() ;
		} else {
		}
	}

	/**
	 * Create a new temporary order with the next available temp-order-nr and store
	 * the order in the database.
	 *
	 * @return void
	 */
	function	add( $_key, $_id, $_val) {
		try {
			$this->getFromPostL() ;
			$this->StockId	=	$_key ;
			$this->storeInDb() ;
		} catch( Exception $e) {
			FDbg::dumpF( "Stock.php::Stock::add(...): exception='%s'", $e->getMessage()) ;
		}
	}

	function	addStockLocation( $_stockNr, $_id=-1, $_val="") {
		error_log( "Stock::addStockLocation( \"$_stockNr\", $_id, $_val)") ;
		$myStockLocation	=	new StockLocation() ;
		$myStockLocation->getFromPostL() ;
		$myStockLocation->StockId	=	$this->StockId ;
		$myStockLocation->storeInDb() ;
		return $this->getTableLocationAsXML() ;
	}

	function	updStockLocation( $_key, $_id, $_val) {
		error_log( "Stock::updStockLocation( \"$_key\", $_id, $_val)") ;
		$myStockLocation	=	new StockLocation() ;
		$myStockLocation->setId( $_id) ;
		$myStockLocation->getFromPostL() ;
		$myStockLocation->updateInDb() ;
		return $this->getTableLocationAsXML() ;
	}

	function	delStockLocation( $_key, $_id=-1, $_val="") {
		throw new Exception( "Stock::delStockLocation( $_key, $_id, $_val): not yet implemented") ;
//		return $this->getTableLocationAsXML() ;
	}

	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "StockLocation") ;
		return $ret ;
	}

	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}

	function	getStockLocationAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myStockLocation	=	new StockLocation() ;
		$myStockLocation->setId( $_id) ;
		$ret	.=	$myStockLocation->getXMLF() ;
		return $ret ;
	}

	function	getTableAsXML( $_key="", $_id="", $_val="") {
		FDbg::dumpL( 0x00000001, "Stock.php::Stock::getTableAsXML(...): begin") ;
		$this->StockId	=	$_key ;
		$ret	=	"" ;
		if ( isset( $_POST['_SStartRow'])) {
			$this->startRow	=	intval( $_POST['_SStartRow']) ;
		}
		if ( isset( $_POST['_SLang'])) {
			$this->sLang	=	$_POST['_SLang'] ;
		}
		if ( isset( $_POST['_SName'])) {
			$this->sName	=	$_POST['_SName'] ;
		}
		switch ( $_val) {
		case	"f50"	:
			$this->startRow	=	0 ;
			break ;
		case	"p50"	:
			if ( $this->startRow > 10) {
				$this->startRow	-=	10 ;
			} else {
				$this->startRow	=	0 ;
			}
			break ;
		case	"t50"	:
			break ;
		case	"n50"	:
			$this->startRow	+=	10 ;
			break ;
		case	"l50"	:
			break ;
		}
		$ret	.=	"<RowInfo>" ;
		$ret	.=	"<StartRow type=\"int(11)\" title=\"StartRow\"><![CDATA[" . $tmpObj->getStartRow() . "]]></StartRow>" ;
		$ret	.=	"<RowCount type=\"int(11)\" title=\"StartRow\"><![CDATA[" . $this->rowCount . "]]></RowCount>" ;
		$ret	.=	"</RowInfo>" ;
				$order	=	"ORDER BY StockId, ShelfId LIMIT " . $this->startRow . ", " . $this->rowCount . " " ;
		$this->setStartRow( 0) ;
		$this->setRowCount( 10) ;
		$ret	.=	$this->tableFromDb( "", "", "WarehouseId = '$this->WarehouseId' AND StockId = '$this->StockId' ", $order) ;
		FDbg::dumpL( 0x00000001, "Stock.php::Stock::getTableAsXML(...): end") ;
		return $ret ;
	}
	/**
	 * report
	 * 
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	report( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Stock.php::Stock::report( '$_key', $_id, '$_val'): begin") ;
		/**
		 * 
		 * @var unknown_type
		 */
//		$filtArticleNo	=	$_POST['_IFiltArticleNo'] ;
//		if ( $filtArticleNo == "") {
//			$filtArticleNo	=	"%" ;
//		}
//		$filtDescription	=	$_POST['_IFiltDescription'] ;
//		if ( $filtDescription == "") {
//			$filtDescription	=	"%" ;
//		}
//		$filtDesc1	=	$_POST['_IFiltDesc1'] ;
//		$filtDesc2	=	$_POST['_IFiltDesc2'] ;
		if ( isset( $_POST['_IFiltStockId']) && $_POST['_IFiltStockId'] != "") {
			$cond	.=	"AND SL.StockId LIKE '".$_POST['_IFiltStockNo']."%' " ;
		} else if ( $_val != "") {
			$cond	=	"AND SL.StockId LIKE '$_val%' " ;
		} else {
			$cond	=	"AND SL.StockId LIKE '%' " ;
		}
		if ( isset( $_POST['_IFiltShelfId'])) {
			$cond	.=	"AND SL.ShelfId LIKE '".$_POST['_IFiltShelfId']."%' " ;
		}
		if ( isset( $_POST['_IFiltArticleNo'])) {
			$cond	.=	"AND A.ArtikelNr LIKE '%".$_POST['_IFiltArticleNo']."%' " ;
		}
		if ( isset( $_POST['_IFiltDescription'])) {
			$cond	.=	"AND A.ArtikelBez1 LIKE '%".$_POST['_IFiltDescription']."%' " ;
		}
		$query	=	"SELECT SL.StockId, SL.ShelfId, AB.ArtikelNr, A.ERPNo, A.ArtikelBez1, A.ArtikelBez2, AB.Lagerbestand " ;
		$query	.=	"FROM StockLocation as SL " ;
		$query	.=	"LEFT JOIN ArtikelBestand AS AB ON AB.Location = concat( SL.StockId, SL.ShelfId) " ;
		$query	.=	"LEFT JOIN Artikel AS A ON A.ArtikelNr = AB.ArtikelNr " ;
		$query	.=	"WHERE A.ArtikelNr IS NOT NULL " ;
		$query	.=	$cond ;	
		$query	.=	"ORDER BY SL.StockId, SL.ShelfId, A.ArtikelNr " ;
		FDbg::dumpL( 0x00000008, "Stock.php::Stock::report(...): query := '$query'") ;
		/**
		 * 
		 * @var unknown_type
		 */
		$res	=	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" ;
		$res	.=	"<doc toc=\"true\" toclevel=\"3\" cover=\"false\" xmlns:fo=\"http://www.w3.org/1999/XSL/Format\" lang=\"en\">\n" ;
		$res	.=	"<Copyright>2007-2011 Copyright MODIS GmbH, D-51674 Wiehl - Bomig, Robert-Bosch-Str. 1</Copyright>\n" ;
		/**
		 * 
		 */
		$res	.=	"<Image>".$this->path->Logos."logo_main.jpg"."</Image>\n" ;
		$res	.=	"<Scope>Lagerliste</Scope>\n" ;
		$res	.=	"<Date>".$this->today()."</Date>\n" ;
		$res	.=	FDb::queryForXMLTable( $query, "Stock") ;
		$res	.=	"</doc>" ;
		$myFile	=	fopen( $this->path->Catalog . "stock.xml", "w") ;
		fwrite( $myFile, $res) ;
		fclose( $myFile) ;
		$sysCmd	=	"fop -xml ".$this->path->Catalog."stock.xml "
					. "-xsl ".$this->path->Styles."stocklist.xsl "
					. "-pdf ".$this->path->Catalog."stocklist.pdf > /tmp/errlog 2>&1 " ;
		system( $sysCmd, $res) ;
		FDbg::dumpL( 0x00000002, "Stock.php::Stock::report(...): sysCmd := '$sysCmd', result: $res") ;
		FDbg::dumpL( 0x00000001, "Stock.php::Stock::report( '$_key' $_id, '$_val'): end") ;
	}
	/**
	 * report
	 * 
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	labels( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Stock.php::Stock::labels( '$_key', $_id, '$_val'): begin") ;
		/**
		 * 
		 * @var unknown_type
		 */
		$cond	=	"" ;
		if ( isset( $_POST['_IFiltStockId']) && $_POST['_IFiltStockId'] != "") {
			$cond	.=	"AND SL.StockId LIKE '".$_POST['_IFiltStockId']."%' " ;
		} else if ( $_val != "") {
			$cond	.=	"AND SL.StockId LIKE '$_val%' " ;
		} else {
			$cond	.=	"AND SL.StockId LIKE '%' " ;
		}
		if ( isset( $_POST['_IFiltShelfId'])) {
			$cond	.=	"AND SL.ShelfId LIKE '".$_POST['_IFiltShelfId']."%' " ;
		}
		if ( isset( $_POST['_IFiltArticleNo'])) {
			$cond	.=	"AND A.ArtikelNr LIKE '%".$_POST['_IFiltArticleNo']."%' " ;
		}
		if ( isset( $_POST['_IFiltDescription'])) {
			$cond	.=	"AND A.ArtikelBez1 LIKE '%".$_POST['_IFiltDescription']."%' " ;
		}
		$query	=	"SELECT SL.StockId, SL.StockId, AB.ArtikelNr AS ArtikelNr, A.ERPNo, A.ArtikelBez1, A.ArtikelBez2, AB.Lagerbestand " ;
		$query	.=	"FROM StockLocation as SL " ;
		$query	.=	"LEFT JOIN ArtikelBestand AS AB ON AB.Location = concat( SL.StockId, SL.ShelfId) " ;
		$query	.=	"LEFT JOIN Artikel AS A ON A.ArtikelNr = AB.ArtikelNr " ;
		$query	.=	"WHERE A.ArtikelNr IS NOT NULL AND AB.Lagerbestand <> 0 " ;
		$query	.=	$cond ;	
		$query	.=	"ORDER BY SL.StockId, SL.StockId" ;
		FDbg::dumpL( 0x00000008, "Stock.php::Stock::report(...): query := '$query'") ;
		$sqlResult	=	FDb::query( $query) ;
		$myLbl	=	new ArticleLblDoc() ;
		while ( $myRow = FDb::getRow( $sqlResult)) {
			$myLbl->setKey( $myRow['ArtikelNr']) ;
			$myLbl->createPDF55x25() ;
			error_log( "Artikel No.: " . $myRow['ArtikelNr']) ;
		}
		FDbg::dumpL( 0x00000001, "Stock.php::Stock::labels( '$_key' $_id, '$_val'): end") ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTableDocumentsAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	"<DocList>\n" ;
		$ret	.=	"<URLPath>".$this->path->Documents."</URLPath>\n" ;
		$myDocument	=	new Document() ;
		$ret	.=	$myDocument->tableFromDb( "", "", "C.RefType='" . Document::RT_STOCK . "' ", "ORDER BY DocRev ", "Doc") ;
		$ret	.=	"</DocList>\n" ;
		return $ret ;
	}
	function	updStocks( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Stock.php::Stock::updStocks( '$_key' $_id, '$_val'): begin") ;
		$myDoc	=	new Document() ;
		$myDoc->setId( $_id) ;
		if ( $myDoc->isValid()) {
			$myFile	=	fopen( $this->path->Documents.$myDoc->Filename, "r") ;
			if ( $myFile) {
				FDbg::dumpL( 0x00000001, "Stock.php::Stock::updStocks(...): document file is open!") ;
				$myArticle	=	new Artikel() ;
				$myArtikelBestand	=	new ArtikelBestand() ;
				while ( ! feof( $myFile)) {
					$myLine	=	fgets( $myFile, 8192);
					FDbg::dumpL( 0x00000001, "Stock.php::Stock::updStocks(...): line := '".$myLine."'") ;
					$myData	=	explode( ",", $myLine) ;
					if ( $myData[3]) {
						$myScanData	=	trim( $myData[3]) ;
						try {
							if ( $myArticle->setArtikelNr( $myScanData)) {
								$myArticleNo	=	$myArticle->ArtikelNr ;
								$myArtikelBestand->getDefault( $myArticle->ArtikelNr) ;
								$myArtikelBestand->Location	=	$myStockId ;
								$myArtikelBestand->updateColInDb( "Location") ;
							}
						} catch ( Exception $e){
							$myStockId	=	$myScanData ;
						}						
					}
					FDbg::dumpL( 0x00000001, "Stock.php::Stock::updStocks(...): scanned data ([3]) := ".$myData[3]) ;
				}
				fclose( $myFile) ;
			}
		} else {
			FDbg::dumpL( 0x00000001, "Stock.php::Stock::updStocks(...): document id := $_id not valid!") ;
		}
		FDbg::dumpL( 0x00000001, "Stock.php::Stock::updStocks( '$_key' $_id, '$_val'): end") ;
	}
}

/**
 * Stock - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BStock which should
 * not be modified.
 *
 * @package WTA
 * @subpackage Article
 */
class	StockLocation	extends	AppDepObject	{

	/*
	 * The constructor can be passed an ArticleNr (StockId), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_myStockId='') {
		AppDepObject::__construct( "StockLocation", "Id") ;
		if ( ! isset( $_SESSION['Sess_StockLocation_startRow'])) {
			error_log( "Stock::StockLocation: initializing session variables") ;
			$this->setStartRow( 0) ;
			$this->setRowCount( 10) ;
			$_SESSION['Sess_StockLocation_startRow']	=	$this->getStartRow() ;	
			$_SESSION['Sess_StockLocation_rowCount']	=	$this->getRowCount() ;	
		} else {
//			error_log( "Stock::StockLocation: re-using session variables") ;
			$this->setStartRow( $_SESSION['Sess_StockLocation_startRow']) ;	
			$this->setRowCount( $_SESSION['Sess_StockLocation_rowCount']) ;	
		}
		$this->StockId	=	$_myStockId ;
		$this->myArtikel	=	new Artikel() ;
	}

	function	addStockLocation( $_key, $_id, $_val) {
	}

	function	updStockLocation( $_key, $_id, $_val) {
	}

	/**
	 *
	 */
	function    reload() {
		FDbg::dumpL( 0x01000000, "StockLocation::reload()") ;
		$this->fetchFromDbById() ;
		$this->myStock->setStockId( $this->StockId) ;
		FDbg::dumpL( 0x01000000, "StockLocation::reload(), done") ;
	}

}

?>
