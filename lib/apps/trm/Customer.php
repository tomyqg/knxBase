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
 * Customer - Base Class
 *
 * @package Application
 * @subpackage Customer
 */
class	Customer	extends	AppObjectCore	{
	/**
	 *
	 */
	function	__construct( $_myCustomerNo="") {
		parent::__construct( "Customer", "CustomerNo") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myCustomerNo) > 0) {
			try {
				$this->setCustomerNo( $_myCustomerNo) ;
				$this->actCustomerContact	=	new CustomerContact() ;
				$this->Opening	=	"Hallo" ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	/**
	 *
	 */
	function	setCustomerNo( $_myCustomerNo) {
		$this->CustomerNo	=	$_myCustomerNo ;
		$this->reload() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->CustomerNo	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "Customer.php::Customer::add(): 'Customer' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
		}
		$this->getAsXML( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$this->_upd() ;
		FDbg::end( 1, "Customer.php", "Customer", "upd( '$_key', $_id, '$_val')") ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Customer.php", "Customer", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "Customer updated")) ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Customer.php::Customer::del(...)") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "CustomerContact") {
			$myCustomerContact	=	new CustomerContact() ;
			$myCustomerContact->CustomerNo	=	$this->CustomerNo ;
			$myCustomerContact->newCustomerContact() ;
			$myCustomerContact->getFromPostL() ;
			$myCustomerContact->updateInDb() ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, $_val, $reply) ;
		} else if ( $objName == "LiefCustomer") {
			$this->_addDepCustomer( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefCustomer", $reply) ;
		} else if ( $objName == "RechCustomer") {
			$this->_addDepCustomer( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechCustomer", $reply) ;
		} else if ( $objName == "AddCustomer") {
			$this->_addDepCustomer( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddCustomer", $reply) ;
		}
		FDbg::end( 1, "Customer.php", "Customer", "addDep( '$_key', $_id, '$_val')") ;
		return $ret ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		if ( $objName == "CustomerContact") {
			$myCustomerContact	=	new CustomerContact() ;
			$myCustomerContact->CustomerNo	=	$this->CustomerNo ;
			$myCustomerContact->newCustomerContact() ;
			$myCustomerContact->getFromPostL() ;
			$myCustomerContact->updateInDb() ;
			return $myCustomerContact->CustomerContactNo ;
		} else if ( $objName == "LiefCustomer") {
			return $this->_addDepCustomer( "L") ;
		} else if ( $objName == "RechCustomer") {
			return $this->_addDepCustomer( "R") ;
		} else if ( $objName == "AddCustomer") {
			return $this->_addDepCustomer( "A") ;
		}
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Customer.php", "Customer", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			return parent::updDep( $_key, $_id, $_val) ;
			break ;
		}
		FDbg::end( 1, "Customer.php", "Customer", "updDep( '$_key', $_id, '$_val')") ;
		return $this->getTableDepAsXML( $_key, $_id, $objName) ;
	}
	function	delDep( $_key, $_id, $_val) {
		$objName	=	$_val ;
		try {
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			if ( $tmpObj->setId( $_id)) {
				$tmpObj->removeFromDb() ;
			} else {
				$e	=	new Exception( "Customer.php::Customer::delDep[Id='.$_id.'] dependent is INVALID !") ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 */
	function	getAddrStreet() {
		switch ( $this->Country) {
		case	"de"	:
			return $this->Street . " " . $this->Number ;
			break ;
		case	"uk"	:
			return $this->Number . " " . $this->Street ;
			break ;
		default	:
			return $this->Street . " " . $this->Number ;
			break ;
		}
	}
	/**
	 *
	 */
	function	getAddrCity() {
		switch ( $this->Country) {
		case	"de"	:
			return $this->ZIP . " " . $this->City ;
			break ;
		case	"uk"	:
			return $this->City . " " . $this->ZIP ;
			break ;
		default	:
			return $this->ZIP . " " . $this->City ;
			break ;
		}
	}
	/**
	 *
	 */
	function	getAddrCountry() {
		switch ( $this->Country) {
		case	""		:
			return "" ;
			break ;
		case	"de"	:
			return "" ;
			break ;
		default	:
			$laender	=	Opt::getArray( "Options", "Key", "Value", "OptionName = 'Country'") ;
			return $laender[ $this->Country] ;
			break ;
		}
	}
	/**
	 * newKey( $_digits, $_nsStart, $_nsEnd, $_store)
	 *
	 * Get a new key for the object and stores the object as an empty object in the database.
	 * The object is then reloaded.
	 * @param int $_digits	number of digits for the key
	 * @param string $_nsStart	beginning of the number range within which to fetch the new key
	 * @param string $_nsEnd	end of the number range within which to fetch the new key
	 * @return void
	 */
	function	newKey( $_digits=6, $_nsStart="000000", $_nsEnd="999999", $_store=true) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_digits, '$_nsStart', '$_nsEnd')") ;
		$keyCol	=	$this->keyCol ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addOrder( $this->keyCol . " DESC") ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		$myRow	=	FDb::queryRow( $myQuery, self::$db[$this->className]) ;
		$this->_assignFromRow( $myRow) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( $_digits, '$_nsStart', '$_nsEnd')", "Last Key := '".$this->$keyCol."'") ;
		$this->$keyCol	=	sprintf( "%06d", intval( substr( $this->$keyCol, 2)) + 1) ;
		/**
		 *
		 */
		if ( $_store) {
			$this->storeInDb() ;
			$this->reload() ;
		} else {
			$this->_valid	=	true ;
		}
		FDbg::end() ;
		return $this->$keyCol ;		// anmd return the newly assigned primary object key
	}
	/**
	 *	Funktion:	identify Customer
	 *
	 *	Versucht irgendwie den Customern per Password zu identifizieren
	 *	Zuerst wird versucht, den Customern per CustomernNr zu finden.
	 *	Falls keine Customernnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn Customerndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */
	/**
	 *
	 * @param $password
	 * @return	bool	true = password ok, false = password wrong
	 */
	function	identifyCustomer( $password) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->fetchFromDb() ;
		if ( !$this->_valid) {
			$this->eMail	=	mysql_escape_string( $this->CustomerNo) ;
			$this->fetchFromDbByEmail() ;
		}
		if ( $this->_valid) {
			error_log( "Customer.php::Customer::identifyCustomer(...): Customer is valid, CustomerNo = '".$this->CustomerNo."' ") ;
			$this->_valid	=	false ;
			if ( $this->Password != md5( $password)) {
				error_log( "Customer.php::Customer::identifyCustomer(...): password is not valid Db: '".$this->Password."' ".md5( $password)) ;
				$this->_status	=	-6 ;
			} else {
				error_log( "Customer.php::Customer::identifyCustomer(...): password is valid") ;
				$this->_valid	=	true ;
			}
		} else {
			$this->_status	=	-7 ;
		}
		return $this->_valid ;
	}
	/**
	 * Use Case Methods
	 */
	function	activateCustomer( $key) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->ActivationKey	=	$key ;
		$this->fetchFromDbByActKey() ;
		if ( !$this->_valid) {
//			$this->eMail	=	$this->CustomerNo ;
//			$this->fetchFromDbByEmail() ;
		}
		if ( $this->_valid) {
			$this->ActivationKey	=	"AUTHORIZED" ;
			$this->updateInDb() ;
			$this->_valid	=	1 ;
		} else {
			$this->_status	=	-7 ;
		}
		return $this->_status ;
	}
	/**
	 * Use Case Methods
	 */
	function	activateNoKey() {
		$this->_status	=	0 ;
		if ( $this->_valid) {
			$this->ActivationKey	=	"AUTHORIZED" ;
			$this->updateInDb() ;
		} else {
			$this->_status	=	-7 ;
		}
		return $this->_valid ;
	}
	/**
	 * create a new customer from a form sending POST data
	 */
	function	newCustomerFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new CustomerContact() ;
		$myCuContact->getFromPost() ;
		if ( $this->CustomerName1 == "") {
			$this->CustomerName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->CustomerName1) < 8) {
			self::$err['_ICustomerName1']	=	true ;
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
			$this->newCustomer( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->CustomerNo	=	$this->CustomerNo ;
			$myCuContact->newCustomerContact() ;
		}
	}
	/**
	 *
	 */
	function	updatePassword( $_pwd, $_pwdVerify) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '*')") ;
		if ( $_pwd == $_pwdVerify) {
			$this->Password	=	md5( $_pwd) ;
			$this->updateInDb() ;
			FDb::query( "UPDATE Customer SET Password = '" . $this->Password . "' WHERE CustomerNo = '" . $this->CustomerNo . "' ") ;
			FDb::query( "UPDATE CustomerContact SET Password = '" . $this->Password . "' WHERE CustomerNo = '" . $this->CustomerNo . "' ") ;
		}
		FDbg::end() ;
	}

	/**
	 *
	 */
	function	fetchFromDbByActKey() {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$query	=	"select * " ;
		$query	.=	"from Customer " ;

		$query	.=	"where ActivationKey='" . $this->ActivationKey . "' " ;
		$sqlResult      =       FDb::query( $query) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       FDb::rowCount() ;
			if ( $numrows == 1) {
				$row    =       mysql_fetch_assoc( $sqlResult) ;
				$this->assignFromRow( $row) ;
				$this->_valid   =       true ;
			} else {
				$this->_status  =       -2 ;
			}
		}
		return $this->_status ;
	}

	/**
	 *
	 */
	function	fetchFromDbByEmail() {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$query	=	"select * " ;
		$query	.=	"from Customer " ;
		$query	.=	"where eMail='" . $this->eMail . "' " ;
		$query	.=	"LIMIT 1 " ;			// nur den ersten Treffer
		$sqlResult      =       FDb::query( $query) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       FDb::rowCount() ;
			if ( $numrows == 1) {
				$row    =       mysql_fetch_assoc( $sqlResult) ;
				$this->assignFromRow( $row) ;
				$this->_valid   =       true ;
			} else {
				$this->_status  =       -2 ;
			}
		}
		return $this->_status ;
	}

	/**
	 *
	 */
	function	sendCustomerNo() {
		$newActivationKey   =   "" ;
		for ( $count=0 ; $count < 8 ; $count++) {
			$c  =   rand(0, 62) ;
			if ( $c < 10) {
				$newActivationKey   .=  chr( $c + 48) ;
			} else if ( $c < 36) {
				$newActivationKey   .=  chr( $c - 10 + 65) ;
			} else if ( $c < 62) {
				$newActivationKey   .=  chr( $c - 36 + 97) ;
			}
		}
		$this->ActivationKey    =   md5( $newActivationKey) ;
		$this->updateInDb() ;
		try {
			$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
			$newMail	=	new mimeMail( $this->eMail->Sales,
								$this->eMail,
								$this->eMail->Sales,
								FTr::tr( "Your registration, your Customer No. #1", array( "%s:".$this->CustomerNo)),
								"Bcc: ".$this->eMail->Archive."\n") ;

			$myMailText	=	new SysTexte() ;
			$myMailText->setKeys( "EMailCustomerCustomerNo") ;
			$mailData	=	array( "#CustomerNo" => $this->CustomerNo,
									"#WebSite" => $this->url->fullShop,
									"#Activationlink" => $this->url->fullShop . "index.php?ziel=aktivieren.php&key=" . $this->ActivationKey
							) ;
			$out	=	$myMailText->Volltext ;
			foreach ( $mailData as $key => $val) {
				$in	=	$out ;
				$out	=	str_replace( $key, $val, $in) ;
			}
			$myText	=	new mimeData( "multipart/alternative") ;

			$myText->addData( "text/plain", xmlToPlain( "<div>".$out."</div>")) ;
			$myText->addData( "text/html", "<HTML><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><HEAD<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n".$out.$myDisclaimerHTML->Volltext."</HTML>", "", true) ;
			$newMail->addData( "multipart/mixed", $myText->getData(), $myText->getHead()) ;
			$mailSendResult	=	$newMail->send() ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		return $this->isValid() ;
	}

	/**
	 *
	 */
	function	getPassword( $_mode="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <...>)") ;
		$newPassword    =   "" ;
        for ( $count=0 ; $count < 8 ; $count++) {
            $c  =   rand(0, 62) ;
            if ( $c < 10) {
                $newPassword    .=  chr( $c + 48) ;
            } else if ( $c < 36) {
                $newPassword    .=  chr( $c - 10 + 65) ;
            } else if ( $c < 62) {
                $newPassword    .=  chr( $c - 36 + 97) ;
            }
        }
		FDbg::trace( 2, FDbg::mdTrcInfo1, "Customer.php", "Customer", "sendPassword( <...>)", "sending new password") ;
		$this->PasswordUser =   $newPassword ;
		$this->Password =   md5( $newPassword) ;
		$this->Password =   md5( $newPassword) ;
        $this->updateInDb() ;
		FDb::query( "UPDATE Customer SET Password = '" . md5( $newPassword) . "' WHERE CustomerNo = '" . $this->CustomerNo . "' ") ;
		FDb::query( "UPDATE CustomerContact SET Password = '" . md5( $newPassword) . "' WHERE CustomerNo = '" . $this->CustomerNo . "' ") ;
		FDbg::end() ;
		return $this->isValid() ;
	}
   /**
     *
     */
    function    fetchForNew() {
        $this->CustomerName1   =   $_POST['_ICustomerName1'] ;
        $this->CustomerName2   =   $_POST['_ICustomerName2'] ;
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
	 *	@param	char	$_rem
	 *	@return	void
	 */
	function	addRem( $_key="", $_id=-1, $_val="") {
		try {
			$this->_addRem( $_POST[ '_IRem']) ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getCustomerContactMailAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myCustomerContact	=	new CustomerContact() ;
		if ( $myCustomerContact->setId( $_id)) {
			$ret	.=	"<MailData>" ;
			$ret	.=	"<eMail type='varchar(32)'>" . $myCustomerContact->eMail . "</eMail>" ;
			$ret	.=	"</MailData>" ;
		}
		return $ret ;
	}
	/**
	 *
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getAsXML() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "CustomerContact") ; ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "LiefCustomer") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "RechCustomer") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "AddCustomer") ;
		return $ret ;
	}
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
		return $reply ;
	}
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		if ( $objName == "CustomerContact") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$reply->replyData	=	$tmpObj->tableFromDb( "", "", "CustomerNo = '$this->CustomerNo' ") ;
		} else if ( $objName == "LiefCustomer") {
			$tmpObj	=	new Customer() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$tmpObj->addCol( "City", "varchar") ;
			$tmpObj->addCol( "Address", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "CustomerNo like '".$this->CustomerNo."-L%' ",
												"ORDER BY CustomerNo ", "CustomerLiefCustomer",
												"",
												"C.Id, C.CustomerNo, CONCAT( C.CustomerName1, \" \", C.CustomerName2) AS Company, "
													. "CONCAT( C.ZIP, \" \", C.City) AS City, "
													. "CONCAT( C.Street, \" \", C.Number) AS Address "
			) ;
			$reply->replyData	=	str_replace( "Customer>", "LiefCustomer>", $ret) ;
			return $reply ;
		} else if ( $objName == "RechCustomer") {
			$tmpObj	=	new Customer() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "CustomerNo like '".$this->CustomerNo."-R%' ",
												"ORDER BY CustomerNo ",
												"CustomerRechCustomer",
												"",
												"C.Id, C.CustomerNo, CONCAT( C.CustomerName1, C.CustomerName2) AS Company ") ;
			$reply->replyData	=	str_replace( "Customer>", "RechCustomer>", $ret) ;
		} else if ( $objName == "AddCustomer") {
			$tmpObj	=	new Customer() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "CustomerNo like '".$this->CustomerNo."-A%' ",
												"ORDER BY CustomerNo ",
												"CustomerAddCustomer",
												"",
												"C.Id, C.CustomerNo, CONCAT( C.CustomerName1, C.CustomerName2) AS Company ") ;
			$reply->replyData	=	str_replace( "Customer>", "AddCustomer>", $ret) ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"LiefCustomer"	:
			$myCustomer	=	new Customer() ;
			if ( $_id == -1) {
			} else {
				$myCustomer->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Customer>", "LiefCustomer>", $myCustomer->getAsXML()) ;
			break ;
		case	"RechCustomer"	:
			$myCustomer	=	new Customer() ;
			if ( $_id == -1) {
			} else {
				$myCustomer->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Customer>", "RechCustomer>", $myCustomer->getAsXML()) ;
			break ;
		case	"AddCustomer"	:
			$myCustomer	=	new Customer() ;
			if ( $_id == -1) {
			} else {
				$myCustomer->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Customer>", "AddCustomer>", $myCustomer->getAsXML()) ;
			break ;
		default	:
			parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getCustomerContactAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myCustomerContact	=	new CustomerContact() ;
		$myCustomerContact->setId( $_id) ;
		$ret	.=	$myCustomerContact->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getCustomerLiefCustomerAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myCustomerAdr	=	new Customer() ;
		$myCustomerAdr->setId( $_id) ;
		$ret	.=	$myCustomerAdr->getXMLF( "LiefCustomer") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getCustomerRechCustomerAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myCustomerAdr	=	new Customer() ;
		$myCustomerAdr->setId( $_id) ;
		$ret	.=	$myCustomerAdr->getXMLF( "RechCustomer") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getCustomerAddCustomerAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myCustomerAdr	=	new Customer() ;
		$myCustomerAdr->setId( $_id) ;
		$ret	.=	$myCustomerAdr->getXMLF( "AddCustomer") ;
		return $ret ;
	}
	/**
	 *
	 */
	function	_addDepCustomer( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->CustomerNo) ;
		$this->CustomerNo	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT CustomerNo FROM Customer WHERE CustomerNo LIKE '%s-$_pref%%' ORDER BY CustomerNo DESC LIMIT 0, 1 ", $this->CustomerNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myCustomerDepAdr	=	new Customer() ;
			if ( $numrows == 0) {
				$myCustomerDepAdr->CustomerNo	=	$this->CustomerNo . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myCustomerDepAdr->CustomerNo	=	sprintf( "%s-$_pref%03d", $this->CustomerNo, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myCustomerDepAdr->storeInDb() ;
			$myCustomerDepAdr->getFromPostL() ;
			$myCustomerDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myCustomerDepAdr->CustomerNo ;
	}
	/**
	 * @param	string	$_key
	 * @param	int	$_id
	 * @param	mixed	$_val
	 */
	function	export( $_key="", $_id=0, $_val="") {
		$oFile	=	fopen( $this->path->Archive."XML/down/Cust".$this->CustomerNo.".xml", "w+") ;
		fwrite( $oFile, "<Customernregistrierung>\n") ;
		$buffer	=	$this->getXMLF() ;
		fwrite( $oFile, $buffer) ;
		$myCustomerContact	=	new CustomerContact() ;
		$myCustomerContact->CustomerNo	=	$this->CustomerNo ;
		for ( $myCustomerContact->_firstFromDb( "CustomerNo='$this->CustomerNo' ORDER BY CustomerContactNo ") ;
					$myCustomerContact->_valid == 1 ;
					$myCustomerContact->_nextFromDb()) {
			$buffer	=	$myCustomerContact->getXMLF() ;
			fwrite( $oFile, $buffer) ;
		}
		fwrite( $oFile, "</Customernregistrierung>\n") ;
		fclose( $oFile) ;
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "CustomerSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
//				$_customerNoCrit	=	$_POST['_SCustNo'] ;
// 				$_firmaCrit	=	$_POST['_SCompany'] ;
// 				$_nameCrit	=	$_POST['_SName'] ;
// 				$_phoneCrit	=	$_POST['_SPhone'] ;
// 				$_eMail	=	$_POST['_SeMail'] ;
			$filter	=	"( C.CustomerName1 like '%" . $_customerNoCrit . "%' OR C.CustomerName2  like '%" . $_customerNoCrit . "%') " ;
//				$filter	=	"( C.CustomerNo like '%" . $_customerNoCrit . "%' ) " ;
// 				$filter	.=	"  AND ( C.CustomerName1 like '%" . $_firmaCrit . "%' OR C.CustomerName2 LIKE '%" . $_firmaCrit . "%') " ;
// 				if ( $_POST['_SName'] != "")
// 					$filter	.=	"  AND ( KK.FirstName like '%" . $_POST['_SName'] . "%' OR KK.Name like '%" . $_POST['_SName'] . "%' ) " ;
// 				if ( $_POST['_SZIP'] != "")
// 					$filter	.=	"  AND ( C.ZIP like '%" . $_POST['_SZIP'] . "%' ) " ;
// 				$filter	.=	"AND ( C.eMail LIKE '%" . $_eMail . "%') " ;
			/**
			*
			*/
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addOrder( ["CustomerNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Customer") ;
			break ;
		case	"CustomerContact"	:
			$myObj	=	new FDbObject( "CustomerContactSurvey", "Id") ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["CustomerNo = '".$this->CustomerNo."' "]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "CustomerContact") ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getCCList( $_key="", $_id=-1, $_val="") {
		$custNoFilter	=	$_POST['_SCustNo'] ;
		$companyFilter	=	$_POST['_SCompany'] ;
		$_POST['_step']	=	$_id ;
		$filter	=	"(( C.CustomerNo like '" . $custNoFilter . "%' ) " ;
		$filter	.=	"  AND ( C.CustomerName1 like '%" . $companyFilter . "%' OR C.CustomerName2 like '%" . $companyFilter . "%' ) " ;
		if ( $_POST['_SZIP'] != "")
			$filter	.=	"  AND ( C.ZIP like '%" . $_POST['_SZIP'] . "%' ) " ;
		if ( $_POST['_SName'] != "")
			$filter	.=	"  AND ( CC.LastName like '%" . $_POST['_SLastName'] . "%' ) " ;
		if ( $_POST['_SPhone'] != "")
			$filter	.=	"  AND ( C.Phone like '%" . $_POST['_SPhone'] . "%' OR CC.Phone like '%" . $_POST['_SPhone'] . "%' )" ;
		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "CustomerNo", "var") ;
		$myObj->addCol( "CustomerName1", "var") ;
		$myObj->addCol( "CustomerName2", "var") ;
		$myObj->addCol( "ZIP", "var") ;
		$myObj->addCol( "LastName", "var") ;
		$ret	=	$myObj->tableFromDb( ", CC.LastName AS LastName, CC.Phone AS Phone ",
								"LEFT JOIN CustomerContact AS CC on CC.CustomerNo = C.CustomerNo ",
								$filter,
								"ORDER BY C.CustomerNo ASC ",
								"Customer",
								"Customer",
								"CC.Id, C.CustomerNo, C.CustomerName1, C.CustomerName2, C.ZIP ") ;
//		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * This method sends an eMail, with the text named $_mailText coming from the 'Texte' Db-Table
	 * to the recipients
	 * @param string $_mailText	mand.: Name of the mail body in the 'Texte' Db-table
	 * @param string $_file	opt.: pdf-file in the Archive/CuOrdr path to attach
	 * @param string $_from	opt.: sending mail address
	 * @param string $_to	opt.: receiving mail address
	 * @param string $_cc	opt.: cc mail address
	 * @param string $_bcc	opt.: bcc mail address
	 */
	function	mail( $_mailText, $_file=null, $_fileName="", $_from="", $_to="", $_cc="", $_bcc="") {
		/**
		 * get the eMail subject and make it accessible by the interpreter
		 */
		$myText	=	new Texte() ;
		$myText->setKeys( $_mailText."Subject") ;
		$this->mailSubject	=	$myText->Volltext ;
		/**
		 * get the eMail body
		 */
		$myText->setKeys( $_mailText."Text") ;
		$this->mailBodyText	=	xmlToPlain( "<div>".iconv( 'UTF-8', 'iso-8859-1//TRANSLIT', $myText->Volltext)."</div>") ;
		$myText->setKeys( $_mailText."HTML") ;
		$this->mailBodyHTML	=	$myText->Volltext ;
		/**
		 * get the eMail disclaimer and make it accessible by the interpreter
		 */
		$myText->setKeys( "DisclaimerText") ;
		$this->DisclaimerText	=	xmlToPlain( "<div>".iconv( 'UTF-8', 'iso-8859-1//TRANSLIT', $myText->Volltext)."</div>") ;
		$myText->setKeys( "DisclaimerHTML") ;
		$this->DisclaimerHTML	=	$myText->Volltext ;
		/**
		 * do some interpretation on the body contents
		 */
		$subjectText	=	$this->interpret( $this->mailSubject) ;
		$bodyText	=	$this->interpret( $this->mailBodyText) ;
		$bodyHTML	=	$this->interpret( $this->mailBodyHTML) ;
		/**
		 *
		 */
		$mailFrom	=	$_from ;
		if ( $mailFrom == "") {
			$mailFrom	=	$this->siteeMail->Sales ;
		}
		$mailTo	=	$_to ;
		if ( $mailTo == "") {
			$mailTo	=	$this->eMail ;
		}
		$mailCC	=	$_cc ;
		$mailBCC	=	"Bcc: " . $this->siteeMail->Archive ;
		if ( $_bcc != "") {
			$mailBCC	.=	"," . $_bcc ;
		}
		$mailBCC	.=	"\n" ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "mail( ...)", $mailFrom) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "mail( ...)", $mailTo) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "mail( ...)", $mailCC) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "mail( ...)", $mailBCC) ;
		$myMail	=	new mimeMail( $mailFrom,
							$mailTo,
							"",
							$subjectText,
							$mailBCC) ;
		$myText	=	new mimeData( "multipart/alternative") ;
		$myText->addData( "text/plain", $bodyText) ;
		$myText->addData( "text/html", mimeMail::getHTMLBody( $bodyHTML), "", true) ;
		/**
		 * prepare the eMail attachment
		 */
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$file	=	$_file ;
		$fileName	=	$_fileName ;
		$myBody	=	new mimeData( "multipart/mixed") ;
//		$myBody->addData( "text/html", mimeMail::getHTMLBody( $bodyHTML)) ;
		if ( $file != null) {
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			$myBody->addData( "application/pdf", $file, $fileName, true) ;
		} else {
			$myBody->addData( "multipart/mixed", $myText->getAll(), "", true) ;
		}
		$myMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
		/**
		 * and send it
		 */
		$mailSendResult	=	$myMail->send() ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getListAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		$_adrNrCrit	=	"" ;
		$_firmaCrit	=	"" ;
		$_nameCrit	=	"" ;
		$_zipCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		if ( isset( $_POST['_SCustomerNo']))
			$_adrNrCrit	=	$_POST['_SCustomerNo'] ;
		if ( isset( $_POST['_SCompany']))
			$_firmaCrit	=	$_POST['_SCompany'] ;
		if ( isset( $_POST['_SName']))
			$_nameCrit	=	$_POST['_SName'] ;
		if ( isset( $_POST['_SZIP']))
			$_zipCrit	=	$_POST['_SZIP'] ;
		$_POST['_step']	=	$_id ;
		$filter	.=	"(" ;
		$filter	.=	"( C.CustomerNo like '%" . $_adrNrCrit . "%' OR C.CustomerNo like '%" . $_searchCrit . "%' ) " ;
		$filter	.=	"  AND ( C.CustomerName1 like '%" . $_firmaCrit . "%' OR C.CustomerName2 LIKE '%" . $_firmaCrit . "%') " ;
		if ( $_nameCrit != "")
			$filter	.=	"  AND ( AK.FirstName like '%$_nameCrit%' OR AK.LastName like '%$_nameCrit%') " ;
		if ( $_zipCrit != "")
			$filter	.=	"  AND ( C.ZIP like '%$_zipCrit%' ) " ;
		if ( $_searchCrit != "")
			$filter	.=	"  AND ( C.CustomerName1 like '%$_searchCrit%' OR C.CustomerName2 like '%$_searchCrit%' OR AK.FirstName like '%$_searchCrit%' OR AK.LastName like '%$_searchCrit%') " ;
		$filter	.=	")" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "CustomerNo", "var") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "ZIP", "var") ;
		$myObj->addCol( "ContactName", "var") ;
		$reply->replyData	=	$myObj->tableFromDb( ", CONCAT( C.CustomerName1, \" \", C.CustomerName2) AS Name, CONCAT( AK.FirstName, \" \", AK.LastName) AS ContactName ",
								"LEFT JOIN CustomerContact AS AK ON AK.CustomerNo = C.CustomerNo ",
								$filter,
								"ORDER BY C.CustomerNo ASC ",
								"Customer",
								"Customer",
								"C.Id, C.CustomerNo, C.ZIP ") ;
		FDbg::end() ;
		return $reply ;
	}
}
?>
