<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "MimeMail.php") ;
require_once( "XmlTools.php") ;
/**
 * Account - Base Class
 *
 * @package Application
 * @subpackage Account
 */
class	Account	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myAccountNo="") {
		parent::__construct( "Account", "AccountNo") ;
		if ( strlen( $_myAccountNo) > 0) {
			try {
				$this->setAccountNo( $_myAccountNo) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	
	/**
	 *
	 */
	function	setAccountNo( $_myAccountNo) {
		$this->AccountNo	=	$_myAccountNo ;
		$this->reload() ;
		return $this->_valid ;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return null|Reply|void
	 * @throws FException
	 */
	function	add( $_key="", $_id=-1, $_val="", $_reply = NULL) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$myAccount	=	new Account() ;
		if ( $_val == "AccountBooking"){
			$this->addDep( $_key, $_id, $_val, $_reply) ;
		} else if ( $myAccount->first( "LENGTH(AccountNo) = 8", "AccountNo DESC")) {
			$this->getFromPostL() ;
			$this->AccountNo	=	sprintf( "%08d", intval( $myAccount->AccountNo) + 1) ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			$this->getFromPostL() ;
			$this->AccountNo	=	sprintf( "%08d", intval( $myAccount->AccountNo) + 1) ;
			if ( ! $this->storeInDb()) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"object[".$this->cacheName."], Account invalid after creation!'") ;
			}
		}
		return $this->isValid() ;
	}

	function	upd( $_key="", $_id=-1, $_val="", $_reply = NULL) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$this->getFromPostL( "AccountNo") ;
		$this->updateInDb() ;
		return $this->isValid() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $_reply = NULL) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		parent::del( $_key, $_id, $_val, $_reply) ;
		$this->getList( $_key, $_id, $_val, $_reply) ;
		return $_reply ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "AccountBooking") {
			$myAccountBooking	=	new AccountBooking() ;
			$myAccountBooking->AccountNo	=	$this->AccountNo ;
			$myAccountBooking->first( "AccountNo = '".$this->AccountNo."'", "AccountBookingNo DESC" ) ;
			$myContactNo	=	$myAccountBooking->AccountBookingNo ;
			$myAccountBooking->getFromPostL() ;
			$myAccountBooking->AccountBookingNo	=	sprintf( "%03d", intval( $myContactNo) + 1) ;
			$myAccountBooking->storeInDb() ;
		} else if ( $objName == "LiefAccount") {
			$this->_addDepAccount( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefAccount", $_reply) ;
		} else if ( $objName == "RechAccount") {
			$this->_addDepAccount( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechAccount", $_reply) ;
		} else if ( $objName == "AddAccount") {
			$this->_addDepAccount( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddAccount", $_reply) ;
		}
		return $_reply ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		$objName	=	$_val ;
		if ( $objName == "AccountBooking") {
			$myAccountBooking	=	new AccountBooking() ;
			$myAccountBooking->AccountNo	=	$this->AccountNo ;
			$myAccountBooking->newAccountBooking() ;
			$myAccountBooking->getFromPostL() ;
			$myAccountBooking->updateInDb() ;
			return $myAccountBooking->AccountBookingNo ;
		} else if ( $objName == "LiefAccount") {
			return $this->_addDepAccount( "L") ;
		} else if ( $objName == "RechAccount") {
			return $this->_addDepAccount( "R") ;
		} else if ( $objName == "AddAccount") {
			return $this->_addDepAccount( "A") ;
		}
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $_reply) ;
			$_reply->message	=	FTr::tr( "Contact succesfully updated!") ;
			break ;
		}
		return $_reply ;
	}

	/**
	 *
	 */
	function	newAccount( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Account.php::Account::newAccount( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  AccountNo >= '$_nsStart' AND AccountNo <= '$_nsEnd' " .
						"ORDER BY $this->keyCol DESC LIMIT 1 ), $_nsStart+1)  AS newKey" ;
		$myRow	=	FDb::queryRow( $myQuery) ;
		$keyCol	=	$this->keyCol ;
		$this->$keyCol	=	sprintf( "%06s", $myRow['newKey']) ;
		$this->Tax	=	1 ;
		$this->Remark	=	"" ;
		$this->storeInDb() ;
		$this->reload() ;
		return $this->_status ;
	}
	/**
	 * create a new member from a form sending POST data
	 */
	function	newAccountFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new AccountBooking() ;
		$myCuContact->getFromPost() ;
		if ( $this->AccountName1 == "") {
			$this->AccountName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->AccountName1) < 8) {
			self::$err['_IAccountName1']	=	true ;
		}
		if ( strlen( $myCuContact->Name) < 3) {
			self::$err['_IName']	=	true ;
		}
		if ( strlen( $myCuContact->FirstName) < 3) {
			self::$err['_IFirstName']	=	true ;
		}
		if ( strlen( $this->Street) < 3) {
			self::$err['_IStreet']	=	true ;
		}
		if ( strlen( $this->Number) < 3) {
			self::$err['_INumber']	=	true ;
		}
		if ( strlen( $this->ZIP) < 3) {
			self::$err['_IZIP']	=	true ;
		}
		if ( strlen( $this->City) < 3) {
			self::$err['_ICity']	=	true ;
		}
		/**
		 * check eMail Address for length
		 * for match
		 * all ok
		 */
		if ( strlen( $this->eMail) < 10) {
			self::$err['_IeMail']	=	true ;
		} else if ( $this->eMail != $_POST['_IeMailVerify']) {
			self::$err['_IeMail']	=	true ;
		} else {
			$this->newAccount( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->AccountNo	=	$this->AccountNo ;
			$myCuContact->newAccountBooking() ;
		}
	}

   /**
     *
     */
    function    fetchForNew() {
        $this->AccountName1   =   $_POST['_IAccountName1'] ;
        $this->AccountName2   =   $_POST['_IAccountName2'] ;
        $this->ZIP  =   $_POST['_IZIP'] ;
        $this->City  =   $_POST['_ICity'] ;
        $this->Street  =   $_POST['_IStreet'] ;
        $this->Number   =   $_POST['_INumber'] ;
        $this->Country =   $_POST['_ICountry'] ;
        $this->Phone  =   $_POST['_IPhone'] ;
        $this->Fax  =   $_POST['_IFax'] ;
        $this->Cellphone    =   $_POST['_ICellphone'] ;
    }

	/**
	 * Kommentar zu der Colli hinzufuegen
	 *
	 *	Dies Funktion fuegt einen Kommentar, $_rem, an die Colli an.
	 *	Datum/Uhrzeit sowie die Id des angemeldeten Benutzers, oder - im Falle eines unauthtntizierten Zugriffs - der Zusatz: "Hintergrunf Prozess"
	 *	werden automatisch in dem Kommentar vermerkt.
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return mixed
	 * @throws FException
	 */
	function	addRem( $_key="", $_id=-1, $_val="", $_reply=null) {
		try {
			$this->_addRem( $_POST[ '_IRem']) ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}

	/**
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 * @return string
	 */
	function	getAccountBookingMailAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myAccountBooking	=	new AccountBooking() ;
		if ( $myAccountBooking->setId( $_id)) {
			$ret	.=	"<MailData>" ;
			$ret	.=	"<eMail type='varchar(32)'>" . $myAccountBooking->eMail . "</eMail>" ;
			$ret	.=	"</MailData>" ;
		}
		return $ret ;
	}
	
	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return null|Reply
	 */
	function	getAsXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		else {
			$_reply->instClass	=	__class__ ;
			$_reply->replyingClass	=	$this->className ;
		}
		if ( $_val == "AccountBooking"){
			$this->getDepAsXML( $_key, $_id, $_val, $_reply);
		} else {
			$_reply->replyData	.=	$this->getXMLF() ;
		}
		FDbg::end() ;
		return $_reply ;
	}

	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"AccountBooking"	:
			$myAccountBooking	=	new AccountBooking() ;
			if ( $_id == -1) {
				$myAccountBooking->Id	=	-1 ;
			} else {
				$myAccountBooking->setId( $_id) ;
			}
			$_reply->replyData	=	$myAccountBooking->getXMLF() ;
			break ;
		default	:
			$_reply	=	parent::getDepAsXML( $_key, $_id, $_val, $_reply) ;
			break ;
		}
		return $_reply ;
	}

	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @return string
	 */
	function	getAccountBookingAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myAccountBooking	=	new AccountBooking() ;
		$myAccountBooking->setId( $_id) ;
		$ret	.=	$myAccountBooking->getXMLF() ;
		return $ret ;
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return mixed|Reply|null
	 * @throws FException
	 */
	function	getList( $_key="", $_id=-1, $_val="", $_reply=null) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->getClassName()) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
		case	"Account"	:
			$myObj	=	new FDbObject( "Account", "AccountNo", "def", "v_AccountSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"AccountName like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$_reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"AccountBooking"	:
			$myObj	=	new FDbObject( "AccountBooking", "Id", "def", "v_AccountBookingSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( AccountNoDebit = '" . $this->AccountNo . "' OR AccountNoCredit = '%" . $this->AccountNo . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( ["Date"]) ;
			$_reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		return $_reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getListAsJSON( $_key="", $_id=-1, $_val="", $_reply=null) {
		error_log( __CLASS__ . "::" . __METHOD__ . ": starting with _key := " . $_key) ;
		$json	=	"" ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
		case	"Account"	:
			$myObj	=	new FDbObject( "Account", "AccountNo", "def", "v_AccountSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"LastName like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$json	=	$myObj->jsonFromQuery( $myQuery) ;
			break ;
		case	"AccountBooking"	:
			$myObj	=	new FDbObject( "AccountBooking", "Id", "def", "v_BookingSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( AccountNoDebit = '" . $this->AccountNo . "' OR AccountNoCredit = '" . $this->AccountNo . "') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( ["Date"]) ;
			$json	=	$myObj->jsonFromQuery( $myQuery) ;
			break ;
		}
		return $json ;
	}

	/**
	 *
	 */
	protected	function	_postInstantiate() {
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
	}
}
?>
