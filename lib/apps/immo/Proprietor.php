<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * Proprietor - Base Class
 *
 * @package Application
 * @subpackage Proprietor
 */
class	Proprietor	extends	AppObjectImmo	{

	/**
	 *
	 */
	function	__construct( $_myProprietorNo="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myProprietorNo')") ;
		parent::__construct( "Proprietor", "ProprietorNo") ;
		if ( strlen( $_myProprietorNo) > 0) {
			try {
				$this->setProprietorNo( $_myProprietorNo) ;
				$this->actProprietorContact	=	new ProprietorContact() ;
				$this->Opening	=	"Hallo" ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
		FDBg::end() ;
	}
	/**
	 *
	 */
	function	setProprietorNo( $_myProprietorNo) {
		$this->setKey( $_myProprietorNo) ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$myProprietor	=	new Proprietor() ;
		if ( $myProprietor->first( "ProprietorNo DESC", "LENGTH(ProprietorNo) = 8")) {
			$this->getFromPostL() ;
			$this->ProprietorNo	=	sprintf( "%08d", intval( $myProprietor->ProprietorNo) + 1) ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], Proprietor invalid after creation!'") ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$this->_upd( $_key, $_id, $_val, $_replyl) ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "Proprietor.php", "Proprietor", "_upd()") ;
		parent::upd( $_key, $_id, $_val, $_replyl) ;
		$this->_addRem( FTr::tr( "Proprietor updated")) ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getXMLString() ;
		FDbg::end() ;
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
		if ( $objName == "ProprietorContact") {
			$myProprietorContact	=	new ProprietorContact() ;
			$myProprietorContact->ProprietorNo	=	$this->ProprietorNo ;
			$myProprietorContact->newProprietorContact() ;
			$myProprietorContact->getFromPostL() ;
			$myProprietorContact->updateInDb() ;
			$reply->message	=	FTr::tr( "Contact succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "LiefProprietor") {
			$this->_addDepProprietor( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefProprietor", $reply) ;
		} else if ( $objName == "RechProprietor") {
			$this->_addDepProprietor( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechProprietor", $reply) ;
		} else if ( $objName == "AddProprietor") {
			$this->_addDepProprietor( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddProprietor", $reply) ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		if ( $objName == "ProprietorContact") {
			$myProprietorContact	=	new ProprietorContact() ;
			$myProprietorContact->ProprietorNo	=	$this->ProprietorNo ;
			$myProprietorContact->newProprietorContact() ;
			$myProprietorContact->getFromPostL() ;
			$myProprietorContact->updateInDb() ;
			return $myProprietorContact->ProprietorContactNo ;
		} else if ( $objName == "LiefProprietor") {
			return $this->_addDepProprietor( "L") ;
		} else if ( $objName == "RechProprietor") {
			return $this->_addDepProprietor( "R") ;
		} else if ( $objName == "AddProprietor") {
			return $this->_addDepProprietor( "A") ;
		}
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			$reply->message	=	FTr::tr( "Contact succesfully updated!") ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		try {
			switch( $objName) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				$reply->message	=	FTr::tr( "Contact succesfully deleted!") ;
				break ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $reply ;
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
			$laender	=	Opt::getArray( "AppOptions", "Key", "Value", "OptionName = 'Country'") ;
			return $laender[ $this->Country] ;
			break ;
		}
	}
	/**
	 *
	 */
	function	newProprietor( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Proprietor.php::Proprietor::newProprietor( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  ProprietorNo >= '$_nsStart' AND ProprietorNo <= '$_nsEnd' " .
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
	 *	Funktion:	identify Proprietor
	 *
	 *	Versucht irgendwie den Proprietorn per Password zu identifizieren
	 *	Zuerst wird versucht, den Proprietorn per ProprietornNr zu finden.
	 *	Falls keine Proprietornnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn Proprietorndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */
	/**
	 *
	 * @param $password
	 * @return	bool	true = password ok, false = password wrong
	 */
	function	identify( $password) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->fetchFromDb() ;
		if ( !$this->_valid) {
			$this->eMail	=	mysql_escape_string( $this->ProprietorNo) ;
			$this->fetchFromDbByEmail() ;
		}
		if ( $this->_valid) {
			error_log( "Proprietor.php::Proprietor::identifyProprietor(...): Proprietor is valid, ProprietorNo = '".$this->ProprietorNo."' ") ;
			$this->_valid	=	false ;
			if ( $this->Password != md5( $password)) {
				error_log( "Proprietor.php::Proprietor::identifyProprietor(...): password is not valid Db: '".$this->Password."' ".md5( $password)) ;
				$this->_status	=	-6 ;
			} else {
				error_log( "Proprietor.php::Proprietor::identifyProprietor(...): password is valid") ;
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
	function	activateProprietor( $key) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->ActivationKey	=	$key ;
		$this->fetchFromDbByActKey() ;
		if ( !$this->_valid) {
//			$this->eMail	=	$this->ProprietorNo ;
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
	 * create a new Proprietor from a form sending POST data
	 */
	function	newProprietorFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new ProprietorContact() ;
		$myCuContact->getFromPost() ;
		if ( $this->ProprietorName1 == "") {
			$this->ProprietorName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->ProprietorName1) < 8) {
			self::$err['_IProprietorName1']	=	true ;
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
			$this->newProprietor( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->ProprietorNo	=	$this->ProprietorNo ;
			$myCuContact->newProprietorContact() ;
		}
	}
	/**
	 *
	 */
	function	updatePassword( $_pwd, $_pwdVerify) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."atePassword( '*')") ;
		if ( $_pwd == $_pwdVerify) {
			$this->Password	=	md5( $_pwd) ;
			$this->updateInDb() ;
			FDb::query( "UPDATE Proprietor SET Password = '" . $this->Password . "' WHERE ProprietorNo = '" . $this->ProprietorNo . "' ") ;
			FDb::query( "UPDATE ProprietorContact SET Password = '" . $this->Password . "' WHERE ProprietorNo = '" . $this->ProprietorNo . "' ") ;
		}
		FDbg::end( 1, basename( __FILE__), __CLASS__, __METHOD__."atePassword( '*')") ;
	}

	/**
	 *
	 */
	function	fetchFromDbByActKey() {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$query	=	"select * " ;
		$query	.=	"from Proprietor " ;

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
		$query	.=	"from Proprietor " ;
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
	function	sendProprietorNo() {
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
								FTr::tr( "Your registration, your Proprietor No. #1", array( "%s:".$this->ProprietorNo)),
								"Bcc: ".$this->eMail->Archive."\n") ;

			$myMailText	=	new SysTexte() ;
			$myMailText->setKeys( "EMailProprietorProprietorNo") ;
			$mailData	=	array( "#ProprietorNo" => $this->ProprietorNo,
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
		FDbg::trace( 2, FDbg::mdTrcInfo1, "Proprietor.php", "Proprietor", "sendPassword( <...>)", "sending new password") ;
		error_log( ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> '".$newPassword."'") ;
		$this->PasswordUser =   $newPassword ;
		$this->Password =   md5( $newPassword) ;
		$this->Password =   md5( $newPassword) ;
        $this->updateInDb() ;
		$myProprietor	=	new Proprietor() ;
		$myProprietor->setProprietorNo( $this->ProprietorNo) ;
		if ( $myProprietor->isValid()) {
			$myProprietor->Password	=	md5( $newPassword) ;
			$myProprietor->updateColInDb( "Password") ;
			$myProprietorContact	=	new ProprietorContact() ;
			$myProprietorContact->setIterCond( "ProprietorNo = '".$this->ProprietorNo."'") ;
			foreach ( $myProprietorContact as $ProprietorContact) {
				$myProprietorContact->Password	=	md5( $newPassword) ;
				$myProprietorContact->updateColInDb( "Password") ;
			}
		}
		FDbg::end() ;
		return $this->isValid() ;
	}
   /**
     *
     */
    function    fetchForNew() {
        $this->ProprietorName1   =   $_POST['_IProprietorName1'] ;
        $this->ProprietorName2   =   $_POST['_IProprietorName2'] ;
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
	function	getProprietorContactMailAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myProprietorContact	=	new ProprietorContact() ;
		if ( $myProprietorContact->setId( $_id)) {
			$ret	.=	"<MailData>" ;
			$ret	.=	"<eMail type='varchar(32)'>" . $myProprietorContact->eMail . "</eMail>" ;
			$ret	.=	"</MailData>" ;
		}
		return $ret ;
	}
	/**
	 *
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
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
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"LiefProprietor"	:
			$myProprietor	=	new Proprietor() ;
			if ( $_id == -1) {
			} else {
				$myProprietor->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Proprietor>", "LiefProprietor>", $myProprietor->getXMLString()) ;
			break ;
		case	"RechProprietor"	:
			$myProprietor	=	new Proprietor() ;
			if ( $_id == -1) {
			} else {
				$myProprietor->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Proprietor>", "RechProprietor>", $myProprietor->getXMLString()) ;
			break ;
		case	"AddProprietor"	:
			$myProprietor	=	new Proprietor() ;
			if ( $_id == -1) {
			} else {
				$myProprietor->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Proprietor>", "AddProprietor>", $myProprietor->getXMLString()) ;
			break ;
		default	:
			$reply	=	parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
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
	function	getProprietorContactAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myProprietorContact	=	new ProprietorContact() ;
		$myProprietorContact->setId( $_id) ;
		$ret	.=	$myProprietorContact->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getProprietorLiefProprietorAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myProprietorAdr	=	new Proprietor() ;
		$myProprietorAdr->setId( $_id) ;
		$ret	.=	$myProprietorAdr->getXMLF( "LiefProprietor") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getProprietorRechProprietorAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myProprietorAdr	=	new Proprietor() ;
		$myProprietorAdr->setId( $_id) ;
		$ret	.=	$myProprietorAdr->getXMLF( "RechProprietor") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getProprietorAddProprietorAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myProprietorAdr	=	new Proprietor() ;
		$myProprietorAdr->setId( $_id) ;
		$ret	.=	$myProprietorAdr->getXMLF( "AddProprietor") ;
		return $ret ;
	}
	/**
	 *
	 */
	function	_addDepProprietor( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->ProprietorNo) ;
		$this->ProprietorNo	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT ProprietorNo FROM Proprietor WHERE ProprietorNo LIKE '%s-$_pref%%' ORDER BY ProprietorNo DESC LIMIT 0, 1 ", $this->ProprietorNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myProprietorDepAdr	=	new Proprietor() ;
			if ( $numrows == 0) {
				$myProprietorDepAdr->ProprietorNo	=	$this->ProprietorNo . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myProprietorDepAdr->ProprietorNo	=	sprintf( "%s-$_pref%03d", $this->ProprietorNo, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myProprietorDepAdr->storeInDb() ;
			$myProprietorDepAdr->getFromPostL() ;
			$myProprietorDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myProprietorDepAdr->ProprietorNo ;
	}
	/**
	 * @param	string	$_key
	 * @param	int	$_id
	 * @param	mixed	$_val
	 */
	function	export( $_key="", $_id=0, $_val="") {
		$oFile	=	fopen( $this->path->Archive."XML/down/Cust".$this->ProprietorNo.".xml", "w+") ;
		fwrite( $oFile, "<Proprietornregistrierung>\n") ;
		$buffer	=	$this->getXMLF() ;
		fwrite( $oFile, $buffer) ;
		$myProprietorContact	=	new ProprietorContact() ;
		$myProprietorContact->ProprietorNo	=	$this->ProprietorNo ;
		for ( $myProprietorContact->_firstFromDb( "ProprietorNo='$this->ProprietorNo' ORDER BY ProprietorContactNo ") ;
					$myProprietorContact->_valid == 1 ;
					$myProprietorContact->_nextFromDb()) {
			$buffer	=	$myProprietorContact->getXMLF() ;
			fwrite( $oFile, $buffer) ;
		}
		fwrite( $oFile, "</Proprietornregistrierung>\n") ;
		fclose( $oFile) ;
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "Proprietor", "ProprietorNo", "def", "v_ProprietorProprietorContactSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"ProprietorName like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"ProprietorContact"	:
			$myObj	=	new FDbObject( "ProprietorContact", "Id", "def", "v_ProprietorContactSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_ProprietorNoCrit	=	$sCrit ;
			$filter1	=	"( ProprietorNo = '" . $this->ProprietorNo . "') " ;
			$filter2	=	"( FirstName like '%" . $sCrit . "%' OR LastName  like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["ProprietorNo", "ProprietorContactNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
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
		$filter	=	"(( C.ProprietorNo like '" . $custNoFilter . "%' ) " ;
		$filter	.=	"  AND ( C.ProprietorName1 like '%" . $companyFilter . "%' OR C.ProprietorName2 like '%" . $companyFilter . "%' ) " ;
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
		$myObj->addCol( "ProprietorNo", "var") ;
		$myObj->addCol( "ProprietorName1", "var") ;
		$myObj->addCol( "ProprietorName2", "var") ;
		$myObj->addCol( "ZIP", "var") ;
		$myObj->addCol( "LastName", "var") ;
		$ret	=	$myObj->tableFromDb( ", CC.LastName AS LastName, CC.Phone AS Phone ",
								"LEFT JOIN ProprietorContact AS CC on CC.ProprietorNo = C.ProprietorNo ",
								$filter,
								"ORDER BY C.ProprietorNo ASC ",
								"Proprietor",
								"Proprietor",
								"CC.Id, C.ProprietorNo, C.ProprietorName1, C.ProprietorName2, C.ZIP ") ;
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
}
?>
