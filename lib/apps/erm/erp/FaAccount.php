<?php
/**
 * Revision history
 *  
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-06-12	PA1		khw		renamed Konto to Account; adaptations
 *
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
/**
 * Account - Base Class
 *
 * @package Application
 * @subpackage Account
 */
class	FaAccount	extends	AppObject	{

	var		$actAccountUnterkonto ;
	var		$actLiefAccount ;
	var		$actRechAccount ;
	/**
	 *
	 */
	function	__construct( $_myAccountNo="") {
		FDbg::begin( 1, "Account.php", "Account", "__construct( '$_myAccountNo')") ;
		parent::__construct( "FaAccount", "AccountNo") ;
		if ( strlen( $_myAccountNo) > 0) {
			try {
				$this->setAccountNo( $_myAccountNo) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
		FDbg::end( 1, "Account.php", "Account", "__construct( '$_myAccountNo')") ;
	}
	/**
	 *
	 */
	function	setAccountNo( $_myAccountNo) {
		FDbg::begin( 1, "Account.php", "Account", "setAccountNo( '$_myAccountNo')") ;
		$this->AccountNo	=	$_myAccountNo ;
		$this->reload() ;
		if ( $this->_valid == 1) {
			FDbg::dumpL( 0x01000000, "Account::setAccountNo(...): Account is valid !") ;
		} else {
			FDbg::dumpL( 0x01000000, "Account::setAccountNo(...): Account not valid !") ;
			throw new Exception( "WTA:Account:0001:inv") ;
		}
		FDbg::end( 1, "Account.php", "Account", "setAccountNo( '$_myAccountNo')") ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::add()
	 */
	function	add( $_key, $_id, $_val) {
		FDbg::dumpL( 0x01000000, "Account::new(...)") ;
		try {
			$this->getFromPostL() ;
			$this->AccountNo	=	$_POST['_IAccountNo'] ;
			$this->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	/**
	 * 
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 */
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Account.php", "Account", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::end( 1, "Account.php", "Account", "_upd()") ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::upd()
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Account.php", "Account", "upd()") ;
		$this->_upd() ;
		FDbg::end( 1, "Account.php", "Account", "upd()") ;
		return $this->getXMLString() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::del()
	 */
	function	del( $_key, $_id, $_val) {
//		if ( $this->LockState == 0) {
			$query	=	sprintf( "Account_remove( @status, '%s') ; ", $_key) ;
			$sqlRows	=	FDb::callProc( $query) ;
//		} else {
//			throw new Exception( 'AppObject::del:Das Objekt ist schreibgeschuetzt !') ;
//		}
		return $this->getXMLString() ;
	}

	/**
	 * Kommentar zu der Colli hinzufuegen
	 *
	 *	Dies Funktion fuegt einen Kommentar, $_rem, an die Colli an.
	 *	Datum/Uhrzeit sowie die Id des angemeldeten Benutzers, oder - im Falle eines unauthtntizierten Zugriffs - der Zusatz: "Hintergrunf Prozess"
	 *	werden automatisch in dem Kommentar vermerkt.
	 *
	 *	@param	char	$_rem
	 *	@return	void
	 */
	function	addRem( $_rem) {
		FDbg::dumpL( 0x01000000, "Account.php::Account::addRem(...)") ;
		try {
			$this->_addRem( $_POST['_IRem']) ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}

	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
//		$ret	.=	$this->getTableAccountUnterkontoAsXML() ;
		return $ret ;
	}

	function	getXMLString() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	function	getJSON( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	"{\n" ;
		$ret	.=	parent::getJSON() ;
		$ret	.=	"}\n" ;
		$res	=	"{ \"Account\" : " . $ret . "}" ;
		return $res ;
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
		if ( $objName == "FaJournalLineItem") {
			$tmpObj	=	new FaJournalLineItem() ;
			$tmpObj->addCol( "CompleteDescription", "var") ;
			$tmpObj->setId( $_id) ;
			$filter	.=	"C.AccountDebit = '" . $this->AccountNo . "' OR C.AccountCredit = '" . $this->AccountNo . "' " ;
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
		$_accountNoCrit	=	"" ;
		$_descriptionCrit	=	"" ;
		if ( isset( $_POST['_SAccountNo']))
			$_accountNoCrit	=	$_POST['_SAccountNo'] ;
		if ( isset( $_POST['_SDescription']))
			$_descriptionCrit	=	$_POST['_SDescription'] ;
		$_POST['_step']	=	$_id ;
		$filter	.=	"( C.AccountNo like '%" . $_accountNoCrit . "%' ) " ;
		$filter	.=	"  AND ( C.Description1 like '%" . $_descriptionCrit . "%' OR C.Description2 LIKE '%" . $_descriptionCrit . "%') " ;
		if ( $_searchCrit != "")
			$filter	.=	"  AND ( C.AccountNo like '%$_searchCrit%' OR C.Description1 like '%$_searchCrit%' ) " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "AccountNo", "var") ;
		$myObj->addCol( "SubAccountNo", "var") ;
		$myObj->addCol( "Description1", "var") ;
		$myObj->addCol( "Description2", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.AccountNo ASC, C.SubAccountNo ",
								"FaAccount",
								"FaAccount",
								"C.Id, C.AccountNo, C.SubAccountNo, C.Description1, C.Description2 ") ;
//		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getJSONList( $_key="", $_id=-1, $_val="") {
		$tmpObj	=	new FDbObject( "Account", "") ;				// no specific object we need here
		$tmpObj->addCol( "Id", "int") ;
		$tmpObj->addCol( "AccountNo", "var") ;
		$tmpObj->addCol( "SubAccountNo", "var") ;
		$tmpObj->addCol( "Description1", "var") ;
		$tmpObj->addCol( "Description2", "var") ;
		$ret	=	"" ;
		$ret	.=	"{\n"
				.	$tmpObj->tableFromDbAsJSON( "", "", "1 = 1 ") ;
				;
		$ret	.=	"}\n" ;
		$res	=	"{ \"List\" : " . $ret . "}" ;
		return $res ;
	}
}
?>
