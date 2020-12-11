<?php
/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 *
 * Revision history
 *  
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-06-25	PA1		khw		added;
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * requires the WTF Debugger and the Base Class
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
/**
 * Journal - Implements a booking in the Bookkeeping Journal
 *
 * @package Application
 * @subpackage Attribute
 */

class	FaJournal	extends	AppObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_journalNo="") {
		parent::__construct( "FaJournal", "JournalNo") ;
		if ( $_journalNo != "") {
			$this->JournalNo	=	$_journalNo ;
		}
	}
	/**
	 * 
	 */
	function	addDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Journal.php", "Journal", "addDep( '$_key', $_id, '$_val')") ;
		$myJournalLineItem	=	new JournalLineItem() ;
		$myJournalLineItem->JournalNo	=	$this->JournalNo ;
		$myJournalLineItem->Description	=	$_POST['_IDescription'] ;
		$myJournalLineItem->_getNextLineNo() ;
		foreach ( $_POST['_IAccountDebit'] as $idx => $account) {
			$myJournalLineItem->_clear() ;
			if ( $account != "") {
				FDbg::trace( 2, "Journal.php", "Journal", "addDep( '$_key', $_id, '$_val')",
						"Account := '$account', Amount := ".$_POST['_FAmountDebit'][$idx]) ;
				$myJournalLineItem->AccountDebit	=	$account ;
				$myJournalLineItem->AmountDebit	=	$_POST['_FAmountDebit'][$idx] ;
				$myJournalLineItem->_getNextItemNo() ;
				$myJournalLineItem->storeInDb() ;
				$myJournalLineItem->_clear() ;
			}
		}
		foreach ( $_POST['_IAccountCredit'] as $idx => $account) {
			if ( $account != "") {
				FDbg::trace( 2, "Journal.php", "Journal", "addDep( '$_key', $_id, '$_val')",
						"Account := '$account', Amount := ".$_POST['_FAmountCredit'][$idx]) ;
				$myJournalLineItem->AccountCredit	=	$account ;
				$myJournalLineItem->AmountCredit	=	$_POST['_FAmountCredit'][$idx] ;
				$myJournalLineItem->_getNextItemNo() ;
				$myJournalLineItem->storeInDb() ;
				$myJournalLineItem->_clear() ;
			}
		}
		FDbg::end( 1, "Journal.php", "Journal", "addDep( '$_key', $_id, '$_val')") ;
		return $this->getXMLComplete( $_key, $_id, $_val) ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Journal.php", "Journal", "updDep( '$_key', $_id, '$_val')") ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Journal.php", "Journal", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		case	"JournalLineItem"	:
			$myJournalLineItem	=	new JournalLineItem() ;
			$myJournalLineItem->setId( $_id) ;
			$myJournalLineItem->_upd() ;
			break ;
		default	:
			return parent::updDep( $_key, $_id, $_val) ;
			break ;
		}
		FDbg::end( 1, "Journal.php", "Journal", "updDep( '$_key', $_id, '$_val')") ;
		return "" ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString( $_key, $_id, $_val) ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "FaJournalLineItem") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getTableDepAsXML()
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		require_once( "FaJournalLine.php" );
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		$objName	=	$_val ;
		$_POST['_step']	=	$_id ;
		if ( $objName == "FaJournalLine") {
			$tmpObj	=	new FaJournalLineItem() ;
			$tmpObj->addCol( "CompleteDescription", "var") ;
			$tmpObj->setId( $_id) ;
			$filter	.=	"C.JournalNo = '" . $this->JournalNo . "' " ;
			if ( $_searchCrit != "")
				$filter	.=	"AND ( C.Name like '%$_searchCrit%' OR C.Vorname like '%$_searchCrit%' ) " ;
			return $tmpObj->tableFromDb( ", CONCAT( JL.Description, \",\" , C.Description) AS CompleteDescription ",
										"LEFT JOIN FaJournalLine AS JL ON JL.LineNo = C.LineNo ",
										$filter,								
										"ORDER BY C.LineNo, C.ItemNo ASC ",
										"FaJournalLine",
										"FaJournalLineItem",
										"C.Id, C.LineNo, C.ItemNo, C.AccountDebit, C.AccountCredit, C.AmountDebit, C.AmountCredit ") ;
		} else if ( $objName == "FaJournalLineItem") {
			$tmpObj	=	new FaJournalLineItem() ;
			$tmpObj->addCol( "CompleteDescription", "var") ;
			$tmpObj->setId( $_id) ;
			$filter	.=	"C.JournalNo = '" . $this->JournalNo . "' " ;
			if ( $_searchCrit != "")
				$filter	.=	"AND ( C.Name like '%$_searchCrit%' OR C.Vorname like '%$_searchCrit%' ) " ;
			return $tmpObj->tableFromDb( ", CONCAT( JL.Description, \",\" , C.Description) AS CompleteDescription ",
										"LEFT JOIN FaJournalLine AS JL ON JL.LineNo = C.LineNo ",
										$filter,								
										"ORDER BY C.LineNo, C.ItemNo ASC ",
										"FaJournalLineItem",
										"FaJournalLineItem",
										"C.Id, C.LineNo, C.ItemNo, C.AccountDebit, C.AccountCredit, C.AmountDebit, C.AmountCredit ") ;
		}
	}
		/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getListAsXML( $_key="", $_id=-1, $_val="") {
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		$_journalNoCrit	=	"" ;
		$_descriptionCrit	=	"" ;
		if ( isset( $_POST['_SJournalNo']))
			$_accountNoCrit	=	$_POST['_SJournalNo'] ;
		if ( isset( $_POST['_SDescription']))
			$_descriptionCrit	=	$_POST['_SDescription'] ;
		$_POST['_step']	=	$_id ;
		$filter	.=	"( C.JournalNo like '%" . $_journalNoCrit . "%' ) " ;
		$filter	.=	"  AND ( C.Description like '%" . $_descriptionCrit . "%') " ;
		if ( $_searchCrit != "")
			$filter	.=	"  AND ( C.JournalNo like '%$_searchCrit%' OR C.Description like '%$_searchCrit%' ) " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "JournalNo", "var") ;
		$myObj->addCol( "Description", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.JournalNo ASC ",
								"FaJournal",
								"FaJournal",
								"C.Id, C.JournalNo, C.Description ") ;
//		error_log( $ret) ;
		return $ret ;
	}
}
?>
