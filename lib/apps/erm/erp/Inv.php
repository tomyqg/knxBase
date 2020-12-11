<?php

/**
 * Inv.php - Package: Article Stock Correction
 * 
 * Main Class:
 * <ul>
 * <li>Inv</li>
 * </ul>
 * 
 * Dependent Classes:
 * <ul>
 * <li>InvItem</li>
 * </ul>
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Class Inv - Stock corrections main data
 *
 * This class acts as an interface towards the automatically generated BInv which should
 * not be modified.
 *
 * @package Application
 * @subpackage Inv
 */
class	Inv	extends	AppObject	{
	/**
	 * definition of constants
	 * @var $rType	array of types of a stock correction
	 */
	const	STD		=	0 ;
	private	static	$rType	=	array (
						-1				=> "ALL",
						Inv::STD		=> "standard"
					) ;
	function	getRType() {	return self::$rType ;	}
	const	ORDR_STOCK	=	0 ;
	const	ORDR_ARTNO	=	1 ;
	private	static	$rOrder	=	array (
						Inv::ORDR_STOCK	=> "Stock position",
						Inv::ORDR_ARTNO	=> "Article no."
					) ;
	function	getROrder() {	return self::$rOrder ;	}
	/**
	 * 
	 * @var unknown_type
	 */
	var	$startRow	=	0 ;
	var	$rowCount	=	25 ;					
	/*
	 * The constructor can be passed an ArticleNr (InvNo), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_invNo="") {
		FDbg::dumpL( 0x000000001, "Inv::__construct(...): ") ;
		AppObject::__construct( "Inv", "InvNo") ;
		if ( ! isset( $_SESSION['Sess_Inv_startRow'])) {
			$this->setStartRow( 0) ;
			$this->setRowCount( 25) ;
			$_SESSION['Sess_Inv_startRow']	=	$this->getStartRow() ;	
			$_SESSION['Sess_Inv_rowCount']	=	$this->getRowCount() ;	
		} else {
			$this->setStartRow( $_SESSION['Sess_Inv_startRow']) ;	
			$this->setRowCount( $_SESSION['Sess_Inv_rowCount']) ;	
		}
		if ( $_invNo != "") {
			$this->setInvNo( $_invNo) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setInvNo( $_invNo='') {
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::setId( '$_invNo'): begin") ;
		if ( strlen( $_invNo) > 0) {
			$this->InvNo	=	$_invNo ;
			$this->reload() ;
		} else {
		}
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::setId( '$_invNo'): end") ;
	}
	/**
	 * 
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::add( '$_key', $_id, '$_val'): begin") ;
//		$myQuery	=	"SELECT IFNULL(( SELECT InvNo + 1 FROM Inv " .
//						"ORDER BY InvNo DESC LIMIT 1 ), 100001) AS newKey" ;
//		$myRow	=	FDb::queryRow( $myQuery) ;
//		$this->InvNo	=	sprintf( "%06s", $myRow['newKey']) ;
		$this->InvNo	=	str_replace( "-", "", $_POST['_IDate']) ;
		if ( $_val == "") {
			$this->Date	=	$this->today() ;
		} else {
			$this->Date	=	$_val ;
		}
		$this->storeInDb() ;
		$this->reload() ;
		return $this->getXMLString() ;
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::add( '$_key', $_id, '$_val'): end") ;
	}
	
	/**
	 * 
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::upd( '$_key', $_id, '$_val'): begin") ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			$this->getFromPostL() ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( 'Inv.php::Inv::upd(...): rhe object [" . $this->$keyCol . "] is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::upd( '$_key', $_id, '$_val'): end") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * 
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		error_log( "Inv.php::Inv::del( '$_key', $_id, '$_val'): begin") ;
		$myResult	=	FDb::query( "DELETE FROM Inv WHERE InvNo = '".$_key."' ") ;
		$myResult	=	FDb::query( "DELETE FROM InvItem WHERE InvNo = '".$_key."' ") ;
		error_log( "Inv.php::Inv::del( '$_key', $_id, '$_val'): end") ;
	}
	/**
	 * 
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "InvItem") ; ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getXMLString()
	 */
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		if ( $objName == "InvItem") {
			$andCond	=	"" ;
			$order		=	"" ;
			if ( isset( $_POST['_ISearch'])) {
				if ( strlen( $_POST['_ISearch']) > 0)
					$andCond	=	"AND ( A.ArtikelBez1 like '".$_POST['_ISearch']."' OR A.ArtikelNr like '%".$_POST['_ISearch']."%') " ;
					$order		=	"ORDER BY ArtikelNr " ;
			}
 			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "ERPNo", "varchar") ;
			$tmpObj->addCol( "ArtikelBez1", "varchar") ;
			return $tmpObj->tableFromDb( ", A.ERPNo ,A.ArtikelBez1",
											"LEFT JOIN Artikel AS A ON A.ArtikelNr = C.ArticleNo ",
											"C.InvNo = '$this->InvNo' " . $andCond,
											$order) ;
		}
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	getList( $_key="", $_id=-1, $_val="") {
		$_suchKrit	=	$_POST['_SInvNo'] ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( " ;
		$filter	.=	"C.InvNo like '%" . $_suchKrit . "%' " ;
		$filter	.=	") " ;
		$ret	=	"" ;
		$ret	=	$this->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.InvNo DESC ",
								"Inv",
								"Inv",
								"C.Id, C.InvNo, C.Date, C.KeyDate") ;
		return $ret ;
	}
	/**
	 * 
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	create( $_key="", $_id=0, $_val="") {
		error_log( "Inv.php::Inv::create( '$_key', $_id, '$_val'): begin") ;
		/**
		 * 
		 */
		$this->add() ;							// create new inventory
		$this->Description	=	"Inv" ;
		$this->Type	=	Inv::STD ;
		/**
		 * determine some important variables
		 */
		$keyDate	=	$_POST['_IDate'] ;
		if ( $keyDate == "")
			$this->KeyDate	=	$this->today() ;
		else
			$this->KeyDate	=	$keyDate ;
		/**
		 *	fetch the last inventory before the keyDate
		 * 	IF found
		 * 		apply this latest inventory
		 * 	ELSE
		 * 		zero the stock
		 * 	FOR EACH SuDlvr between last inventory and the keydate
		 * 		book SuDlvr
		 * 	FOR EACH CuDlvr between last inventory and the keydate
		 * 		book CuDlvr
		 * @var unknown_type
		 */
		$lastInv	=	new Inv() ;
		$lastInv->fetchFromDbWhere( "WHERE Date < '$this->KeyDate' ORDER BY DATE DESC, Id DESC LIMIT 0, 1 ") ;
		if ( $lastInv->isValid()) {
			FDbg::dumpL( 0x00000008, "Inv.php::Inv::create(...): latest inventory, Id := $lastInv->Id, is dated '$lastInv->Date'") ;
			$lastInv->apply() ;
		} else {
			FDbg::dumpL( 0x00000008, "Inv.php::Inv::create(...): no latest inventoryfound! Will set to 0!") ;
			ArtikelBestand::zero() ;
			$lastInv->KeyDate	=	"2000-01-01" ;
		}
		$this->BaseDate	=	$lastInv->KeyDate ;
		$this->updateInDb() ;
		/**
		 * apply all changes to the stock
		 */
		SuDlvr::_clearAll( "", -1, "", $this->BaseDate, $this->KeyDate) ;
		SuDlvr::_bucheAll( "", -1, "", $this->BaseDate, $this->KeyDate) ;
		CuDlvr::_clearAll( "", -1, "", $this->BaseDate, $this->KeyDate) ;
		CuDlvr::_bucheAll( "", -1, "", $this->BaseDate, $this->KeyDate) ;
		/**
		 * 
		 */
		$stock	=	new ArtikelBestand() ;
		$myInvItem	=	new InvItem() ;
		$myInvItem->InvNo	=	 $this->InvNo ;
		$myItemNo	=	10 ;
		for ( $valid = $stock->_firstFromDb( "Lagerbestand <> 0 ORDER BY WarehouseId, StockId, ShelfId") ;
				$valid == true ;
				$valid = $stock->_nextFromDb()) {
			FDbg::dumpL( 0x00000008, "Inv.php::Inv::create(...): working on article no. '$stock->ArtikelNr'") ;
			$myInvItem->getNextItemNo() ;
			$myInvItem->ArticleNo	=	$stock->ArtikelNr ;
			$myInvItem->WarehouseId	=	$stock->WarehouseId ;
			$myInvItem->StockId	=	$stock->StockId ;
			$myInvItem->ShelfId	=	$stock->ShelfId ;
			$myInvItem->QtyOut	=	$stock->Lagerbestand ;
			$myInvItem->QtyIn	=	$stock->Lagerbestand ;
			$myInvItem->storeInDb() ;
		}
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::create( '$_key', $_id, '$_val'): end") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	reCreate( $_key="", $_id=0, $_val="") {
		/**
		 *	fetch the last inventory before the keyDate
		 * 	IF found
		 * 		apply this latest inventory
		 * 	ELSE
		 * 		zero the stock
		 * 	FOR EACH SuDlvr between last inventory and the keydate
		 * 		book SuDlvr
		 * 	FOR EACH CuDlvr between last inventory and the keydate
		 * 		book CuDlvr
		 * @var unknown_type
		 */
		$lastInv	=	new Inv() ;
		$lastInv->fetchFromDbWhere( "WHERE Date < '$this->KeyDate' ORDER BY DATE DESC, Id DESC LIMIT 0, 1 ") ;
		if ( $lastInv->isValid()) {
			FDbg::dumpL( 0x00000008, "Inv.php::Inv::create(...): latest inventory, Id := $lastInv->Id, is dated '$lastInv->Date'") ;
			$lastInv->apply() ;
		} else {
			FDbg::dumpL( 0x00000008, "Inv.php::Inv::create(...): no latest inventoryfound! Will set to 0!") ;
			ArtikelBestand::zero() ;
			$lastInv->KeyDate	=	"2000-01-01" ;
		}
		$this->BaseDate	=	$lastInv->KeyDate ;
		$this->updateColInDb( "BaseDate") ;
		/**
		 * apply all changes to the stock
		 */
		SuDlvr::_clearAll( "", -1, "", $this->BaseDate, $this->KeyDate) ;
		SuDlvr::_bucheAll( "", -1, "", $this->BaseDate, $this->KeyDate) ;
		CuDlvr::_clearAll( "", -1, "", $this->BaseDate, $this->KeyDate) ;
		CuDlvr::_bucheAll( "", -1, "", $this->BaseDate, $this->KeyDate) ;
		/**
		 * 
		 */
		FDb::query( "Update InvItem SET QtyOut = 0 WHERE InvNo = '$this->InvNo' ") ;
		$stock	=	new ArtikelBestand() ;
		$myInvItem	=	new InvItem() ;
		$myInvItem->InvNo	=	 $this->InvNo ;
		$myItemNo	=	10 ;
		for ( $valid = $stock->_firstFromDb( "Lagerbestand <> 0 ORDER BY WarehouseId, StockId, ShelfId") ;
				$valid == true ;
				$valid = $stock->_nextFromDb()) {
//			FDbg::dumpL( 0x00000008, "Inv.php::Inv::create(...): working on article no. '$stock->ArtikelNr'") ;
			$myInvItem->getNextItemNo() ;
			$myInvItem->ArticleNo	=	$stock->ArtikelNr ;
			$myInvItem->WarehouseId	=	$stock->WarehouseId ;
			$myInvItem->StockId	=	$stock->StockId ;
			$myInvItem->ShelfId	=	$stock->ShelfId ;
			if ( $myInvItem->getItem()) {
				$myInvItem->QtyOut	=	$stock->Lagerbestand ;
				$myInvItem->updateInDb() ;
			} else {
				$myInvItem->QtyOut	=	$stock->Lagerbestand ;
				$myInvItem->QtyIn	=	$stock->Lagerbestand ;
				$myInvItem->storeInDb() ;
			}
		}
		return $this->getXMLComplete() ;
	}
	/**
	 * appl
	 * resets all stock values to 0 and sets all stock qty. to the data from this inventory
	 *  
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	apply( $_key="", $_id=0, $_val="") {
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::apply( '$_key', $_id, '$_val'): begin") ;
		ArtikelBestand::zero() ;
		$myArtikelBestand	=	new ArtikelBestand() ;
		$myInvItem	=	new InvItem( $this->InvNo) ;
		for ( $valid = $myInvItem->_firstFromDb( "InvNo = '$this->InvNo' ") ;
				$valid == true ;
				$valid = $myInvItem->_nextFromDb()) {
			if ( $myArtikelBestand->fetchFromDbWhere( "WHERE ArtikelNr = '$myInvItem->ArticleNo' ")) {
				$myArtikelBestand->Lagerbestand	=	$myInvItem->QtyIn ;
				$myArtikelBestand->updateColInDb( "Lagerbestand") ;
			} else {
				
			}
		}
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::apply( '$_key', $_id, '$_val'): end") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * books all
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	book( $_key="", $_id=-1, $_val="") {
		SuDlvr::_bucheAll( "", -1, "", $this->KeyDate, "2099-12-31") ;
		CuDlvr::_clearAll( "", -1, "", $this->KeyDate, "2099-12-31") ;
		CuDlvr::_bucheAll( "", -1, "", $this->KeyDate, "2099-12-31") ;
	}
	/**
	 * books all
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	static	function	zero( $_key="", $_id=-1, $_val="") {
		ArtikelBestand::zero() ;
		ArtikelBestand::zeroMark() ;
	}
	/**
	 * books all
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	static	function	mark( $_key="", $_id=-1, $_val="") {
		ArtikelBestand::zeroMark() ;
		SuOrdr::_clearAll( "", -1, "", "2000-01-01", "2099-12-31") ;
		SuOrdr::_markAll( "", -1, "", "2000-01-01", "2099-12-31") ;
		CuOrdr::_clearAll( "", -1, "", "2000-01-01", "2099-12-31") ;
		CuOrdr::_markAll( "", -1, "", "2000-01-01", "2099-12-31") ;
		CuComm::_clearAll( "", -1, "", "2000-01-01", "2099-12-31") ;
		CuComm::_markAll( "", -1, "", "2000-01-01", "2099-12-31") ;
	}
	function	report( $_key="", $_id=-1, $_val="") {
		$myReport	=	new InvReport() ;
		$myReport->setKey( $this->InvNo) ;
		$myReport->_createPDF() ;
	}
	function	reportCSV( $_key="", $_id=-1, $_val="") {
		$myReport	=	new InvReportCSV() ;
		$myReport->setKey( $this->InvNo) ;
		$myReport->_create() ;
	}
	/**
	 * report
	 * 
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	report2( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::report( '$_key', $_id, '$_val'): begin") ;
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
		$cond	=	"" ;
		if ( isset( $_POST['_SArticleNo'])) {
			$cond	.=	"AND Ii.ArticleNo LIKE '".$_POST['_SArticleNo']."' " ;
		}
		if ( isset( $_POST['_SArticleDescr'])) {
			$cond	.=	"AND A.ArtikelBez1 LIKE '".$_POST['_SArticleDescr']."' " ;
		}
		$_val	=	"E" ;
		switch ( $_val) {
			case	"I"	:
				$query	=	"SELECT Ii.InvNo, Ii.StockId, Ii.ShelfId, Ii.ArticleNo, A.ERPNo, A.ArtikelBez1, A.ArtikelBez2, Ii.QtyOut " ;
				$query	.=	"FROM InvItem as Ii " ;
				$query	.=	"LEFT JOIN Artikel AS A ON A.ArtikelNr = Ii.ArticleNo " ;
				$query	.=	"WHERE Ii.InvNo = '$this->InvNo' AND A.ArtikelNr IS NOT NULL " ;
				$xsl	=	$this->path->Styles . "inventorylist.xsl " ;
				$pdf	=	$this->path->Catalog . "inventorylist.pdf" ;
				break ;
			case	"E"	:
				$query	=	"SELECT Ii.InvNo, Ii.StockId, Ii.ShelfId, Ii.ArticleNo, A.ERPNo, A.ArtikelBez1, A.ArtikelBez2, Ii.QtyOut, Ii.QtyIn, EKPr.LiefNr, AEKP.Preis, AEKP.MengeFuerPreis, AEKP.Waehrung, C.VonKurs, C.NachKurs " ;
				$query	.=	"FROM InvItem as Ii " ;
				$query	.=	"LEFT JOIN Artikel AS A ON A.ArtikelNr = Ii.ArticleNo " ;
				$query	.=	"LEFT JOIN EKPreisR AS EKPr ON EKPr.ArtikelNr = Ii.ArticleNo AND EKPr.KalkBasis > 0 " ;
				$query	.=	"LEFT JOIN ArtikelEKPreis AS AEKP ON AEKP.LiefNr = EKPr.LiefNr AND AEKP.LiefArtNr = EKPr.LiefArtNr AND AEKP.Menge = EKPr.KalkBasis AND AEKP.GueltigVon < '$this->KeyDate' AND AEKP.GueltigBis >= '$this->KeyDate' " ;
				$query	.=	"LEFT JOIN Currency AS C ON C.VonWaehrung = AEKP.Waehrung AND C.NachWaehrung = 'EUR' AND C.CGueltigVon < '$this->KeyDate' AND C.CGueltigBis >= '$this->KeyDate' " ;
				$query	.=	"WHERE Ii.InvNo = '$this->InvNo' AND A.ArtikelNr IS NOT NULL " ;
				$xsl	=	$this->path->Styles . "stockeval.xsl " ;
				$pdf	=	$this->path->Catalog . "stockeval.pdf" ;
				break ;
		}
		$query	.=	$cond ;
		switch ( $_POST['_SOrder']) {
			case	Inv::ORDR_STOCK	:
				$query	.=	"ORDER BY Ii.StockId, Ii.ShelfId, Ii.ArticleNo " ;
				break ;
			case	Inv::ORDR_ARTNO	:
				$query	.=	"ORDER BY Ii.ArticleNo " ;
				break ;
		}
		FDbg::dumpL( 0x00000008, "Inv.php::Inv::report(...): query := '$query'") ;
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
		$valueTotal	=	0.0 ;
		$res	.=	"<Image>".$this->path->Logos."logo_main.jpg"."</Image>\n" ;
		$res	.=	"<Scope>Inventurliste</Scope>\n" ;
		$res	.=	"<Date>".$this->$this->today()."</Date>\n" ;
		$res	.=	FDb::queryForXMLTable( $query, "InvItem", myCbEoL, myCbEoT) ;
		$res	.=	"</doc>" ;
		$myFile	=	fopen( $this->path->Catalog . "inventory.xml", "w") ;
		fwrite( $myFile, $res) ;
		fclose( $myFile) ;
		$sysCmd	=	"fop -xml ".$this->path->Catalog."inventory.xml "
					. "-xsl $xsl "
					. "-pdf $pdf > /tmp/errlog 2>&1 " ;
		system( $sysCmd, $res) ;
		FDbg::dumpL( 0x00000002, "Inv.php::Inv::report(...): sysCmd := '$sysCmd', result: $res") ;
		FDbg::dumpL( 0x00000001, "Inv.php::Inv::report( '$_key' $_id, '$_val'): end") ;
	}
}
$valueTotal ;
function	myCbEoL( $_row) {
	global	$valueTotal ;
	$mengeFuerPreis	=	$_row['MengeFuerPreis'] ;
	$menge	=	$_row['QtyOut'] ;
	$preis	=	$_row['Preis'] ;
	$value	=	$menge * $preis / $mengeFuerPreis ;
	$valueTotal	+=	$value ;
	return "<Value>$value</Value>\n" ;
}
function	myCbEoT() {
	global	$valueTotal ;
	return "<InvItem><Value>$valueTotal</Value></InvItem>\n" ;
}
/**
 * Class InvItem - Stock corrections item data
 *
 * This class implements the individual booking records of a stock correction.
 * The referenced 'Article' is part of the 
 * 
 * Depends on:
 * <ul>
 * <li>Inv</li
 * </ul>
 *
 * @package Application
 * @subpackage Inv
 */
class	InvItem	extends	FDbObject	{
	/**
	 * 
	 * @var unknown_type
	 */
	var	$myArtikel	=	null ;
	/*
	 * The constructor can be passed an ArticleNr (InvNo), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		parent::__construct( "InvItem", "Id") ;
		$this->Id	=	$_id ;
	}
	/**
	 * reload 
	 */
	function    reload() {
		$this->fetchFromDbById() ;
	}
	/**
	 * getNextItemNo()
	 * 
	 * assigns the next available item no. 
	 */
	function	getNextItemNo() {
		$query	=	"SELECT ItemNo FROM InvItem WHERE InvNo='$this->InvNo' ORDER BY ItemNo DESC LIMIT 0, 1 " ;
		$sqlResult	=	FDb::query( $query) ;
		if ( !$sqlResult) { 
			$e	=	new Exception ( "Inv.php::Inv::getNextItemNo(): void sqlResult!") ;
			error_log( $e) ;
			throw $e ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->ItemNo	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
	/**
	 * getNextItemNo()
	 * 
	 * assigns the next available item no. 
	 */
	function	getItem() {
		$where	=	"WHERE InvNo='$this->InvNo' " ;
		$where	.=	"AND ArticleNo = '$this->ArticleNo' " ;
		$where	.=	"AND WarehouseId = '$this->WarehouseId' " ;
		$where	.=	"AND StockId = '$this->StockId' " ;
		$where	.=	"AND ShelfId = '$this->ShelfId' " ;
		return $this->fetchFromDbWhere( $where) ;
	}
}
?>
