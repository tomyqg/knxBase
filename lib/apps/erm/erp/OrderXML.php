<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/AppObject_R2.php") ;
require_once( "MimeMail.php" );
require_once( "XmlTools.php" );
/**
 * OrderXML - Base Class
 *
 * @package Application
 * @subpackage OrderXML
 */
class	OrderXML	extends	AppObject_R2	{
	/**
	 *
	 */
	function	__construct( $_myOrderXMLNo="") {
		parent::__construct( "OrderXML", "OrderXMLNo") ;
		if ( strlen( $_myOrderXMLNo) > 0) {
			try {
				$this->setOrderXMLNo( $_myOrderXMLNo) ;
				$this->actOrderXMLContact	=	new OrderXMLContact() ;
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
	function	setOrderXMLNo( $_myOrderXMLNo) {
		$this->OrderXMLNo	=	$_myOrderXMLNo ;
		$this->reload() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "add( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->OrderXMLNo	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "OrderXML.php::OrderXML::add(): 'OrderXML' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "upd( '$_key', $_id, '$_val')") ;
		$this->_upd() ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "OrderXML updated")) ;
		FDbg::end( 1, "OrderXML.php", "OrderXML", "_upd()") ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "OrderXML.php::OrderXML::del(...)") ;
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
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "OrderXMLContact") {
			$myOrderXMLContact	=	new OrderXMLContact() ;
			$myOrderXMLContact->OrderXMLNo	=	$this->OrderXMLNo ;
			$myOrderXMLContact->newOrderXMLContact() ;
			$myOrderXMLContact->getFromPostL() ;
			$myOrderXMLContact->updateInDb() ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, $_val, $reply) ;
		} else if ( $objName == "LiefOrderXML") {
			$this->_addDepOrderXML( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefOrderXML", $reply) ;
		} else if ( $objName == "RechOrderXML") {
			$this->_addDepOrderXML( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechOrderXML", $reply) ;
		} else if ( $objName == "AddOrderXML") {
			$this->_addDepOrderXML( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddOrderXML", $reply) ;
		}
		FDbg::end() ;
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
		if ( $objName == "OrderXMLContact") {
			$myOrderXMLContact	=	new OrderXMLContact() ;
			$myOrderXMLContact->OrderXMLNo	=	$this->OrderXMLNo ;
			$myOrderXMLContact->newOrderXMLContact() ;
			$myOrderXMLContact->getFromPostL() ;
			$myOrderXMLContact->updateInDb() ;
			return $myOrderXMLContact->OrderXMLContactNo ;
		} else if ( $objName == "LiefOrderXML") {
			return $this->_addDepOrderXML( "L") ;
		} else if ( $objName == "RechOrderXML") {
			return $this->_addDepOrderXML( "R") ;
		} else if ( $objName == "AddOrderXML") {
			return $this->_addDepOrderXML( "A") ;
		}
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "updDep( '$_key', $_id, '$_val')") ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "OrderXML.php", "OrderXML", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		case	"LiefOrderXML"	:
			$myLiefOrderXML	=	new OrderXML() ;
			$myLiefOrderXML->setId( $_id) ;
			$myLiefOrderXML->_upd() ;
			return $this->getDepAsXML( $_key, $_id, $_val) ;
			break ;
		case	"RechOrderXML"	:
			$myRechOrderXML	=	new OrderXML() ;
			$myRechOrderXML->setId( $_id) ;
			$myRechOrderXML->_upd() ;
			return $this->getDepAsXML( $_key, $_id, $_val) ;
			break ;
		case	"AddOrderXML"	:
			$myAddOrderXML	=	new OrderXML() ;
			$myAddOrderXML->setId( $_id) ;
			$myAddOrderXML->_upd() ;
			return $this->getDepAsXML( $_key, $_id, $_val) ;
			break ;
		default	:
			return parent::updDep( $_key, $_id, $_val) ;
			break ;
		}
		FDbg::end( 1, "OrderXML.php", "OrderXML", "updDep( '$_key', $_id, '$_val')") ;
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
				$e	=	new Exception( "OrderXML.php::OrderXML::delDep[Id='.$_id.'] dependent is INVALID !") ;
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
	 *
	 */
	function	newOrderXML( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "OrderXML.php::OrderXML::newOrderXML( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  OrderXMLNo >= '$_nsStart' AND OrderXMLNo <= '$_nsEnd' " .
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
	 *	Funktion:	identify OrderXML
	 *
	 *	Versucht irgendwie den OrderXMLn per Password zu identifizieren
	 *	Zuerst wird versucht, den OrderXMLn per OrderXMLnNr zu finden.
	 *	Falls keine OrderXMLnnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn OrderXMLndaten existieren, dann muss geprueft werden, ob das Passwort
	 *	stimmt.
	 */
	/**
	 *
	 * @param $password
	 * @return	bool	true = password ok, false = password wrong
	 */
	function	identifyOrderXML( $password) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->fetchFromDb() ;
		if ( !$this->_valid) {
			$this->eMail	=	mysql_escape_string( $this->OrderXMLNo) ;
			$this->fetchFromDbByEmail() ;
		}
		if ( $this->_valid) {
			error_log( "OrderXML.php::OrderXML::identifyOrderXML(...): OrderXML is valid, OrderXMLNo = '".$this->OrderXMLNo."' ") ;
			$this->_valid	=	false ;
			if ( $this->Passwort != md5( $password)) {
				error_log( "OrderXML.php::OrderXML::identifyOrderXML(...): password is not valid Db: '".$this->Passwort."' ".md5( $password)) ;
				$this->_status	=	-6 ;
			} else {
				error_log( "OrderXML.php::OrderXML::identifyOrderXML(...): password is valid") ;
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
	function	activateOrderXML( $key) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->ActivationKey	=	$key ;
		$this->fetchFromDbByActKey() ;
		if ( !$this->_valid) {
//			$this->eMail	=	$this->OrderXMLNo ;
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
	function	newOrderXMLFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new OrderXMLContact() ;
		$myCuContact->getFromPost() ;
		if ( $this->OrderXMLName1 == "") {
			$this->OrderXMLName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->OrderXMLName1) < 8) {
			self::$err['_IOrderXMLName1']	=	true ;
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
			$this->newOrderXML( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->OrderXMLNo	=	$this->OrderXMLNo ;
			$myCuContact->newOrderXMLContact() ;
		}
	}
	/**
	 *
	 */
	function	updatePassword( $_pwd, $_pwdVerify) {
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "updatePassword( '*')") ;
		if ( $_pwd == $_pwdVerify) {
			$this->Passwort	=	md5( $_pwd) ;
			$this->updateInDb() ;
			FDb::query( "UPDATE OrderXML SET Passwort = '" . $this->Passwort . "' WHERE OrderXMLNo = '" . $this->OrderXMLNo . "' ") ;
			FDb::query( "UPDATE OrderXMLContact SET Passwort = '" . $this->Passwort . "' WHERE OrderXMLNo = '" . $this->OrderXMLNo . "' ") ;
		}
		FDbg::end( 1, "OrderXML.php", "OrderXML", "updatePassword( '*')") ;
	}

	/**
	 *
	 */
	function	fetchFromDbByActKey() {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$query	=	"select * " ;
		$query	.=	"from OrderXML " ;

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
		$query	.=	"from OrderXML " ;
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
	function	sendOrderXMLNo() {
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
								FTr::tr( "Your registration, your OrderXML No. #1", array( "%s:".$this->OrderXMLNo)),
								"Bcc: ".$this->eMail->Archive."\n") ;

			$myMailText	=	new SysTexte() ;
			$myMailText->setKeys( "EMailOrderXMLOrderXMLNo") ;
			$mailData	=	array( "#OrderXMLNo" => $this->OrderXMLNo,
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
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "sendPassword( <...>)") ;
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
		FDbg::trace( 2, FDbg::mdTrcInfo1, "OrderXML.php", "OrderXML", "sendPassword( <...>)", "sending new password") ;
		$this->PasswordUser =   $newPassword ;
		$this->Password =   md5( $newPassword) ;
		$this->Passwort =   md5( $newPassword) ;
        $this->updateInDb() ;
		FDb::query( "UPDATE OrderXML SET Passwort = '" . md5( $newPassword) . "' WHERE OrderXMLNo = '" . $this->OrderXMLNo . "' ") ;
		FDb::query( "UPDATE OrderXMLContact SET Passwort = '" . md5( $newPassword) . "' WHERE OrderXMLNo = '" . $this->OrderXMLNo . "' ") ;
		FDbg::end( 1, "OrderXML.php", "OrderXML", "sendPassword( <...>)") ;
		return $this->isValid() ;
	}
   /**
     *
     */
    function    fetchForNew() {
        $this->OrderXMLName1   =   $_POST['_IOrderXMLName1'] ;
        $this->OrderXMLName2   =   $_POST['_IOrderXMLName2'] ;
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
	function	getOrderXMLContactMailAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myOrderXMLContact	=	new OrderXMLContact() ;
		if ( $myOrderXMLContact->setId( $_id)) {
			$ret	.=	"<MailData>" ;
			$ret	.=	"<eMail type='varchar(32)'>" . $myOrderXMLContact->eMail . "</eMail>" ;
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
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "OrderXMLContact") ; ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "LiefOrderXML") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "RechOrderXML") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "AddOrderXML") ;
		return $ret ;
	}
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "getAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		return $reply ;
	}
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		if ( $objName == "OrderXMLContact") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$reply->replyData	=	$tmpObj->tableFromDb( "", "", "OrderXMLNo = '$this->OrderXMLNo' ") ;
		} else if ( $objName == "LiefOrderXML") {
			$tmpObj	=	new OrderXML() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$tmpObj->addCol( "City", "varchar") ;
			$tmpObj->addCol( "Address", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "OrderXMLNo like '".$this->OrderXMLNo."-L%' ",
												"ORDER BY OrderXMLNo ", "OrderXMLLiefOrderXML",
												"",
												"C.Id, C.OrderXMLNo, CONCAT( C.OrderXMLName1, \" \", C.OrderXMLName2) AS Company, "
													. "CONCAT( C.ZIP, \" \", C.City) AS City, "
													. "CONCAT( C.Street, \" \", C.Number) AS Address "
			) ;
			$reply->replyData	=	str_replace( "OrderXML>", "LiefOrderXML>", $ret) ;
			return $reply ;
		} else if ( $objName == "RechOrderXML") {
			$tmpObj	=	new OrderXML() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "OrderXMLNo like '".$this->OrderXMLNo."-R%' ",
												"ORDER BY OrderXMLNo ",
												"OrderXMLRechOrderXML",
												"",
												"C.Id, C.OrderXMLNo, CONCAT( C.OrderXMLName1, C.OrderXMLName2) AS Company ") ;
			$reply->replyData	=	str_replace( "OrderXML>", "RechOrderXML>", $ret) ;
		} else if ( $objName == "AddOrderXML") {
			$tmpObj	=	new OrderXML() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "OrderXMLNo like '".$this->OrderXMLNo."-A%' ",
												"ORDER BY OrderXMLNo ",
												"OrderXMLAddOrderXML",
												"",
												"C.Id, C.OrderXMLNo, CONCAT( C.OrderXMLName1, C.OrderXMLName2) AS Company ") ;
			$reply->replyData	=	str_replace( "OrderXML>", "AddOrderXML>", $ret) ;
		}
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"LiefOrderXML"	:
			$myOrderXML	=	new OrderXML() ;
			if ( $_id == -1) {
			} else {
				$myOrderXML->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "OrderXML>", "LiefOrderXML>", $myOrderXML->getAsXML()) ;
			break ;
		case	"RechOrderXML"	:
			$myOrderXML	=	new OrderXML() ;
			if ( $_id == -1) {
			} else {
				$myOrderXML->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "OrderXML>", "RechOrderXML>", $myOrderXML->getAsXML()) ;
			break ;
		case	"AddOrderXML"	:
			$myOrderXML	=	new OrderXML() ;
			if ( $_id == -1) {
			} else {
				$myOrderXML->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "OrderXML>", "AddOrderXML>", $myOrderXML->getAsXML()) ;
			break ;
		default	:
			parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
		return $reply ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getOrderXMLContactAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myOrderXMLContact	=	new OrderXMLContact() ;
		$myOrderXMLContact->setId( $_id) ;
		$ret	.=	$myOrderXMLContact->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getOrderXMLLiefOrderXMLAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myOrderXMLAdr	=	new OrderXML() ;
		$myOrderXMLAdr->setId( $_id) ;
		$ret	.=	$myOrderXMLAdr->getXMLF( "LiefOrderXML") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getOrderXMLRechOrderXMLAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myOrderXMLAdr	=	new OrderXML() ;
		$myOrderXMLAdr->setId( $_id) ;
		$ret	.=	$myOrderXMLAdr->getXMLF( "RechOrderXML") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getOrderXMLAddOrderXMLAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myOrderXMLAdr	=	new OrderXML() ;
		$myOrderXMLAdr->setId( $_id) ;
		$ret	.=	$myOrderXMLAdr->getXMLF( "AddOrderXML") ;
		return $ret ;
	}
	/**
	 *
	 */
	function	_addDepOrderXML( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->OrderXMLNo) ;
		$this->OrderXMLNo	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT OrderXMLNo FROM OrderXML WHERE OrderXMLNo LIKE '%s-$_pref%%' ORDER BY OrderXMLNo DESC LIMIT 0, 1 ", $this->OrderXMLNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myOrderXMLDepAdr	=	new OrderXML() ;
			if ( $numrows == 0) {
				$myOrderXMLDepAdr->OrderXMLNo	=	$this->OrderXMLNo . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myOrderXMLDepAdr->OrderXMLNo	=	sprintf( "%s-$_pref%03d", $this->OrderXMLNo, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myOrderXMLDepAdr->storeInDb() ;
			$myOrderXMLDepAdr->getFromPostL() ;
			$myOrderXMLDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myOrderXMLDepAdr->OrderXMLNo ;
	}
	/**
	 * @param	string	$_key
	 * @param	int	$_id
	 * @param	mixed	$_val
	 */
	function	export( $_key="", $_id=0, $_val="") {
		$oFile	=	fopen( $this->path->Archive."XML/down/Cust".$this->OrderXMLNo.".xml", "w+") ;
		fwrite( $oFile, "<OrderXMLnregistrierung>\n") ;
		$buffer	=	$this->getXMLF() ;
		fwrite( $oFile, $buffer) ;
		$myOrderXMLContact	=	new OrderXMLContact() ;
		$myOrderXMLContact->OrderXMLNo	=	$this->OrderXMLNo ;
		for ( $myOrderXMLContact->_firstFromDb( "OrderXMLNo='$this->OrderXMLNo' ORDER BY OrderXMLContactNo ") ;
					$myOrderXMLContact->_valid == 1 ;
					$myOrderXMLContact->_nextFromDb()) {
			$buffer	=	$myOrderXMLContact->getXMLF() ;
			fwrite( $oFile, $buffer) ;
		}
		fwrite( $oFile, "</OrderXMLnregistrierung>\n") ;
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
		$_kundeNrCrit	=	$sCrit ;
//		$_kundeNrCrit	=	$_POST['_SCustNo'] ;
// 		$_firmaCrit	=	$_POST['_SCompany'] ;
// 		$_nameCrit	=	$_POST['_SName'] ;
// 		$_phoneCrit	=	$_POST['_SPhone'] ;
// 		$_eMail	=	$_POST['_SeMail'] ;
 		$_POST['_step']	=	$_val ;
		$filter	=	"( C.OrderXMLNo like '%" . $sCrit . "%'" . ") " ;
//		$filter	=	"( C.OrderXMLNo like '%" . $_kundeNrCrit . "%' ) " ;
// 		$filter	.=	"  AND ( C.OrderXMLName1 like '%" . $_firmaCrit . "%' OR C.OrderXMLName2 LIKE '%" . $_firmaCrit . "%') " ;
// 		if ( $_POST['_SName'] != "")
// 			$filter	.=	"  AND ( KK.FirstName like '%" . $_POST['_SName'] . "%' OR KK.Name like '%" . $_POST['_SName'] . "%' ) " ;
// 		if ( $_POST['_SZIP'] != "")
// 			$filter	.=	"  AND ( C.ZIP like '%" . $_POST['_SZIP'] . "%' ) " ;
// 		$filter	.=	"AND ( C.eMail LIKE '%" . $_eMail . "%') " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "OrderXMLNo", "var") ;
		$myObj->addCol( "DatNam", "var") ;
		$reply->replyData	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.OrderXMLNo ASC ",
								"OrderXML",
								"OrderXML",
								"C.Id, C.OrderXMLNo, C.DatNam ") ;
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
		$filter	=	"(( C.OrderXMLNo like '" . $custNoFilter . "%' ) " ;
		$filter	.=	"  AND ( C.OrderXMLName1 like '%" . $companyFilter . "%' OR C.OrderXMLName2 like '%" . $companyFilter . "%' ) " ;
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
		$myObj->addCol( "OrderXMLNo", "var") ;
		$myObj->addCol( "OrderXMLName1", "var") ;
		$myObj->addCol( "OrderXMLName2", "var") ;
		$myObj->addCol( "ZIP", "var") ;
		$myObj->addCol( "LastName", "var") ;
		$ret	=	$myObj->tableFromDb( ", CC.LastName AS LastName, CC.Phone AS Phone ",
								"LEFT JOIN OrderXMLContact AS CC on CC.OrderXMLNo = C.OrderXMLNo ",
								$filter,
								"ORDER BY C.OrderXMLNo ASC ",
								"OrderXML",
								"OrderXML",
								"CC.Id, C.OrderXMLNo, C.OrderXMLName1, C.OrderXMLName2, C.ZIP ") ;
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
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "getListAsXML( '$_key', $_id, '$_val')") ;
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
		if ( isset( $_POST['_SOrderXMLNo']))
			$_adrNrCrit	=	$_POST['_SOrderXMLNo'] ;
		if ( isset( $_POST['_SCompany']))
			$_firmaCrit	=	$_POST['_SCompany'] ;
		if ( isset( $_POST['_SName']))
			$_nameCrit	=	$_POST['_SName'] ;
		if ( isset( $_POST['_SZIP']))
			$_zipCrit	=	$_POST['_SZIP'] ;
		$_POST['_step']	=	$_id ;
		$filter	.=	"(" ;
		$filter	.=	"( C.OrderXMLNo like '%" . $_adrNrCrit . "%' OR C.OrderXMLNo like '%" . $_searchCrit . "%' ) " ;
		$filter	.=	"  AND ( C.OrderXMLName1 like '%" . $_firmaCrit . "%' OR C.OrderXMLName2 LIKE '%" . $_firmaCrit . "%') " ;
		if ( $_nameCrit != "")
			$filter	.=	"  AND ( AK.FirstName like '%$_nameCrit%' OR AK.LastName like '%$_nameCrit%') " ;
		if ( $_zipCrit != "")
			$filter	.=	"  AND ( C.ZIP like '%$_zipCrit%' ) " ;
		if ( $_searchCrit != "")
			$filter	.=	"  AND ( C.OrderXMLName1 like '%$_searchCrit%' OR C.OrderXMLName2 like '%$_searchCrit%' OR AK.FirstName like '%$_searchCrit%' OR AK.LastName like '%$_searchCrit%') " ;
		$filter	.=	")" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "OrderXMLNo", "var") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "ZIP", "var") ;
		$myObj->addCol( "ContactName", "var") ;
		$reply->replyData	=	$myObj->tableFromDb( ", CONCAT( C.OrderXMLName1, \" \", C.OrderXMLName2) AS Name, CONCAT( AK.FirstName, \" \", AK.LastName) AS ContactName ",
								"LEFT JOIN OrderXMLContact AS AK ON AK.OrderXMLNo = C.OrderXMLNo ",
								$filter,
								"ORDER BY C.OrderXMLNo ASC ",
								"OrderXML",
								"OrderXML",
								"C.Id, C.OrderXMLNo, C.ZIP ") ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	readFiles( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "OrderXML.php", "OrderXML", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		FDbg::end() ;
		return $reply ;
	}
}
?>
