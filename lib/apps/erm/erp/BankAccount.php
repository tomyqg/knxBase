<?php
/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Artikel - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package Application
 * @subpackage BankAccount
 */
class	BankAccount	extends	FDbObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_erpNo="") {
		FDbg::dumpL( 0x00000001, "BankAccount.php::BankAccount::__construct( '$_erpNo'): begin") ;
		parent::__construct( "BankAccount", "ERPNo") ;
		if ( $_erpNo != "") {
			$this->setKey( $_erpNo) ;
		} else {
		}
		FDbg::dumpL( 0x00000001, "BankAccount.php::BankAccount::__construct( '$_erpNo'): end") ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "BankAccount.php::BankAccount::add( '$_key', $_id, '$_val'): begin") ;
		$this->newKey( 8, "90000000", "90009999") ;
		$this->updateInDb() ;
		$this->reload() ;
		FDbg::dumpL( 0x00000001, "BankAccount.php::BankAccount::add( '$_key', $_id, '$_val'): end") ;
		return $this->getXMLString() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "BankAccount.php::BankAccount::upd( '$_key', $_id, '$_val'): begin") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::dumpL( 0x00000001, "BankAccount.php::BankAccount::upd( '$_key', $_id, '$_val'): end") ;
		return $this->getXMLString() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "BankAccount.php::BankAccount::del( '$_key', $_id, '$_val'): begin") ;
		FDbg::dumpL( 0x00000001, "BankAccount.php::BankAccount::del( '$_key', $_id, '$_val'): end") ;
	}
	/**
	 * 
	 */
	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		return $ret ;
	}
	/**
	 * 
	 */
	function	getXMLString() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getListAsXML( $_key="", $_id=-1, $_val="") {
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		$_POST['_step']	=	$_id ;
		$filter	=	"1 = 1 " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "ERPNo", "var") ;
		$myObj->addCol( "BankName1", "var") ;
		$myObj->addCol( "BankName2", "var") ;
		$myObj->addCol( "BankCode", "var") ;
		$myObj->addCol( "AccountNo", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.ERPNo ASC ",
								"BankAccount",
								"BankAccount",
								"C.Id, C.ERPNo, C.BankName1, C.BankName2, C.BankCode, C.AccountNo ") ;
		return $ret ;
	}
	/**
	 * 
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		$objName	=	$_val ;
		$_POST['_step']	=	$_id ;
		if ( $objName == "BankAccountTransaction") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			if ( $_searchCrit != "")
				$filter	.=	"AND ( C.Date like '%$_searchCrit%' OR C.Description like '%$_searchCrit%' ) " ;
			return $tmpObj->tableFromDb( "", "", "ERPNo = '$this->ERPNo' ".$filter) ;
		}
	}
	/**
	 * 
	 * @param unknown $_key
	 * @param unknown $_id
	 * @param unknown $_val
	 * @return string
	 */
	function	bookingData( $_key, $_id, $_val) {
		FDbg::begin( 0x00000001, "BankAccount.php", "BankAccount", "bookingData( '$_key', $_id, '$_val')") ;
		$tmpFilename	=	$_FILES['_IFilename']['tmp_name'] ;
		$filename	=	$_FILES['_IFilename']['name'] ;
		$fullFilename	=	$this->path->xml . $filename ;
		if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
			if ( $this->Misc1 != "") {
				$transactionClass	=	"BankAccountTransaction_" . $this->Misc1 ;
				$myTC	=	new $transactionClass() ;
				$myTC->fromFile( $this->ERPNo, $this->AccountNo, $fullFilename) ;
			}
		} else {
			if ( $_FILES['_IFilename']['error'] != UPLOAD_ERR_OK) {
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "BankAccount.php", "BankAccount", "bookingData( ...)", "error := '".$_FILES['_IFilename']['error']."'") ;
				FDbg::trace( 0x00000001, FDbg::mdTrcInfo1, "BankAccount.php", "BankAccount", "bookingData( ...)", "error := '".$_FILES['_IFilename']['size']."'") ;
			}
		}
		FDbg::end( 0x00000001, "BankAccount.php", "BankAccount", "bookingData( '$_key', $_id, '$_val')") ;
	}
}
?>
