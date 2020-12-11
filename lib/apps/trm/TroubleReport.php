<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * TroubleReport - Base Class
 *
 * @package Application
 * @subpackage TroubleReport
 */
class	TroubleReport	extends	AppObject_R2	{
	/**
	 *
	 */
	function	__construct( $_myTroubleReportNo="") {
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "__construct( '$_myTroubleReportNo')") ;
		parent::__construct( "TroubleReport", "TroubleReportNo") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myTroubleReportNo) > 0) {
			try {
				$this->setTroubleReportNo( $_myTroubleReportNo) ;
				$this->actTroubleReportAction	=	new TroubleReportAction() ;
				$this->Opening	=	"Hallo" ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setTroubleReportNo( $_myTroubleReportNo) {
		$this->TroubleReportNo	=	$_myTroubleReportNo ;
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
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "add( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 8, "00000000", "99999999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->TroubleReportNo	=	$myKey ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
			$this->_addRem( FTr::tr( "TroubleReport created")) ;
		} else {
			$e	=	new Exception( "TroubleReport.php::TroubleReport::add(): 'TroubleReport' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "add( '$_key', $_id, '$_val')") ;
		return $this->getAsXML() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "upd( '$_key', $_id, '$_val')") ;
		$this->_upd() ;
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "upd( '$_key', $_id, '$_val')") ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "TroubleReport updated")) ;
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "_upd()") ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "TroubleReport.php::TroubleReport::del(...)") ;
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
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "TroubleReportAction") {
			$myTroubleReportAction	=	new TroubleReportAction() ;
			$myTroubleReportAction->TroubleReportNo	=	$this->TroubleReportNo ;
			$myTroubleReportAction->newTroubleReportAction() ;
			$myTroubleReportAction->getFromPostL() ;
			$myTroubleReportAction->updateInDb() ;
		}
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "addDep( '$_key', $_id, '$_val')") ;
		return $reply ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "_addDep( '$_key', $_id, '$_val')") ;
		$objName	=	$_val ;
		switch ( $objName) {
		default	:
			$myTroubleReportAction	=	new TroubleReportAction() ;
			$myTroubleReportAction->TroubleReportNo	=	$this->TroubleReportNo ;
			$myTroubleReportAction->newTroubleReportAction() ;
			$myTroubleReportAction->getFromPostL() ;
			$myTroubleReportAction->updateInDb() ;
			break ;
		}
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "updDep( '$_key', $_id, '$_val')") ;
		return $this->getTableDepAsXML( $_key, $_id, $objName) ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "updDep( '$_key', $_id, '$_val')") ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "TroubleReport.php", "TroubleReport", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			return parent::updDep( $_key, $_id, $_val) ;
			break ;
		}
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "updDep( '$_key', $_id, '$_val')") ;
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
				$e	=	new Exception( "TroubleReport.php::TroubleReport::delDep[Id='.$_id.'] dependent is INVALID !") ;
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
	function	newTroubleReport( $_nsStart="12345678", $_nsEnd="34567890") {
		FDbg::dumpL( 0x00000001, "TroubleReport.php::TroubleReport::newTroubleReport( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  TroubleReportNo >= '$_nsStart' AND TroubleReportNo <= '$_nsEnd' " .
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
	 *	Funktion:	identify TroubleReport
	 *
	 *	Versucht irgendwie den TroubleReportn per Password zu identifizieren
	 *	Zuerst wird versucht, den TroubleReportn per TroubleReportnNr zu finden.
	 *	Falls keine TroubleReportnnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn TroubleReportndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */
	/**
	 * 
	 * @param $password
	 * @return	bool	true = password ok, false = password wrong
	 */
	function	identifyTroubleReport( $password) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->fetchFromDb() ;
		if ( !$this->_valid) {
			$this->eMail	=	mysql_escape_string( $this->TroubleReportNo) ;
			$this->fetchFromDbByEmail() ;
		}
		if ( $this->_valid) {
			error_log( "TroubleReport.php::TroubleReport::identifyTroubleReport(...): TroubleReport is valid, TroubleReportNo = '".$this->TroubleReportNo."' ") ;
			$this->_valid	=	false ;
			if ( $this->Password != md5( $password)) {
				error_log( "TroubleReport.php::TroubleReport::identifyTroubleReport(...): password is not valid Db: '".$this->Password."' ".md5( $password)) ;
				$this->_status	=	-6 ;
			} else {
				error_log( "TroubleReport.php::TroubleReport::identifyTroubleReport(...): password is valid") ;
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
	function	activateTroubleReport( $key) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->ActivationKey	=	$key ;
		$this->fetchFromDbByActKey() ;
		if ( !$this->_valid) {
//			$this->eMail	=	$this->TroubleReportNo ;
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
	function	newTroubleReportFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new TroubleReportAction() ;
		$myCuContact->getFromPost() ;
		if ( $this->TroubleReportName1 == "") {
			$this->TroubleReportName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 * 
		 */
		if ( strlen( $this->TroubleReportName1) < 8) {
			self::$err['_ITroubleReportName1']	=	true ;
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
			$this->newTroubleReport( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->TroubleReportNo	=	$this->TroubleReportNo ;
			$myCuContact->newTroubleReportAction() ;
		}
	}
	/**
	 *
	 */
	function	updatePassword( $_pwd, $_pwdVerify) {
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "updatePassword( '*')") ;
		if ( $_pwd == $_pwdVerify) {
			$this->Password	=	md5( $_pwd) ;
			$this->updateInDb() ;
			FDb::query( "UPDATE TroubleReport SET Password = '" . $this->Password . "' WHERE TroubleReportNo = '" . $this->TroubleReportNo . "' ") ;
			FDb::query( "UPDATE TroubleReportAction SET Password = '" . $this->Password . "' WHERE TroubleReportNo = '" . $this->TroubleReportNo . "' ") ;
		}
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "updatePassword( '*')") ;
	}

	/**
	 *
	 */
	function	fetchFromDbByActKey() {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$query	=	"select * " ;
		$query	.=	"from TroubleReport " ;

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
		$query	.=	"from TroubleReport " ;
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
	function	sendTroubleReportNo() {
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
								FTr::tr( "Your registration, your TroubleReport No. #1", array( "%s:".$this->TroubleReportNo)),
								"Bcc: ".$this->eMail->Archive."\n") ;

			$myMailText	=	new SysTexte() ;
			$myMailText->setKeys( "EMailTroubleReportTroubleReportNo") ;
			$mailData	=	array( "#TroubleReportNo" => $this->TroubleReportNo,
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
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "sendPassword( <...>)") ;
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
		FDbg::trace( 2, FDbg::mdTrcInfo1, "TroubleReport.php", "TroubleReport", "sendPassword( <...>)", "sending new password") ;
		$this->PasswordUser =   $newPassword ;
		$this->Password =   md5( $newPassword) ;
		$this->Password =   md5( $newPassword) ;
        $this->updateInDb() ;
		FDb::query( "UPDATE TroubleReport SET Password = '" . md5( $newPassword) . "' WHERE TroubleReportNo = '" . $this->TroubleReportNo . "' ") ;
		FDb::query( "UPDATE TroubleReportAction SET Password = '" . md5( $newPassword) . "' WHERE TroubleReportNo = '" . $this->TroubleReportNo . "' ") ;
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "sendPassword( <...>)") ;
		return $this->isValid() ;
	}
   /**
     *
     */
    function    fetchForNew() {
        $this->TroubleReportName1   =   $_POST['_ITroubleReportName1'] ;
        $this->TroubleReportName2   =   $_POST['_ITroubleReportName2'] ;
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
	function	getTroubleReportActionMailAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myTroubleReportAction	=	new TroubleReportAction() ;
		if ( $myTroubleReportAction->setId( $_id)) {
			$ret	.=	"<MailData>" ;
			$ret	.=	"<eMail type='varchar(32)'>" . $myTroubleReportAction->eMail . "</eMail>" ;
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
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "TroubleReportAction") ; ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "LiefTroubleReport") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "RechTroubleReport") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "AddTroubleReport") ;
		return $ret ;
	}
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "getAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "getAsXML( '$_key', $_id, '$_val')") ;
		return $reply ;
	}
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		if ( $objName == "TroubleReportAction") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$reply->replyData	=	$tmpObj->tableFromDb( "", "", "TroubleReportNo = '$this->TroubleReportNo' ") ;
		} else if ( $objName == "LiefTroubleReport") {
			$tmpObj	=	new TroubleReport() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$tmpObj->addCol( "City", "varchar") ;
			$tmpObj->addCol( "Address", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "TroubleReportNo like '".$this->TroubleReportNo."-L%' ",
												"ORDER BY TroubleReportNo ", "TroubleReportLiefTroubleReport",
												"",
												"C.Id, C.TroubleReportNo, CONCAT( C.TroubleReportName1, \" \", C.TroubleReportName2) AS Company, "
													. "CONCAT( C.ZIP, \" \", C.City) AS City, "
													. "CONCAT( C.Street, \" \", C.Number) AS Address "
			) ;
			$reply->replyData	=	str_replace( "TroubleReport>", "LiefTroubleReport>", $ret) ;
			return $reply ;
		} else if ( $objName == "RechTroubleReport") {
			$tmpObj	=	new TroubleReport() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "TroubleReportNo like '".$this->TroubleReportNo."-R%' ",
												"ORDER BY TroubleReportNo ",
												"TroubleReportRechTroubleReport",
												"",
												"C.Id, C.TroubleReportNo, CONCAT( C.TroubleReportName1, C.TroubleReportName2) AS Company ") ;
			$reply->replyData	=	str_replace( "TroubleReport>", "RechTroubleReport>", $ret) ;
		} else if ( $objName == "AddTroubleReport") {
			$tmpObj	=	new TroubleReport() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "TroubleReportNo like '".$this->TroubleReportNo."-A%' ",
												"ORDER BY TroubleReportNo ",
												"TroubleReportAddTroubleReport",
												"",
												"C.Id, C.TroubleReportNo, CONCAT( C.TroubleReportName1, C.TroubleReportName2) AS Company ") ;
			$reply->replyData	=	str_replace( "TroubleReport>", "AddTroubleReport>", $ret) ;
		}
		return $reply ;	
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"LiefTroubleReport"	:
			$myTroubleReport	=	new TroubleReport() ;
			if ( $_id == -1) {
			} else {
				$myTroubleReport->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "TroubleReport>", "LiefTroubleReport>", $myTroubleReport->getAsXML()) ;
			break ;
		case	"RechTroubleReport"	:
			$myTroubleReport	=	new TroubleReport() ;
			if ( $_id == -1) {
			} else {
				$myTroubleReport->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "TroubleReport>", "RechTroubleReport>", $myTroubleReport->getAsXML()) ;
			break ;
		case	"AddTroubleReport"	:
			$myTroubleReport	=	new TroubleReport() ;
			if ( $_id == -1) {
			} else {
				$myTroubleReport->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "TroubleReport>", "AddTroubleReport>", $myTroubleReport->getAsXML()) ;
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
	function	getTroubleReportActionAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myTroubleReportAction	=	new TroubleReportAction() ;
		$myTroubleReportAction->setId( $_id) ;
		$ret	.=	$myTroubleReportAction->getXMLF() ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTroubleReportLiefTroubleReportAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myTroubleReportAdr	=	new TroubleReport() ;
		$myTroubleReportAdr->setId( $_id) ;
		$ret	.=	$myTroubleReportAdr->getXMLF( "LiefTroubleReport") ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTroubleReportRechTroubleReportAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myTroubleReportAdr	=	new TroubleReport() ;
		$myTroubleReportAdr->setId( $_id) ;
		$ret	.=	$myTroubleReportAdr->getXMLF( "RechTroubleReport") ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTroubleReportAddTroubleReportAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myTroubleReportAdr	=	new TroubleReport() ;
		$myTroubleReportAdr->setId( $_id) ;
		$ret	.=	$myTroubleReportAdr->getXMLF( "AddTroubleReport") ;
		return $ret ;
	}
	/**
	 *
	 */
	function	_addDepTroubleReport( $_pref) {
		
		$this->_valid  =       false ; 
		$kundeNrParts	=	explode( "-", $this->TroubleReportNo) ;
		$this->TroubleReportNo	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT TroubleReportNo FROM TroubleReport WHERE TroubleReportNo LIKE '%s-$_pref%%' ORDER BY TroubleReportNo DESC LIMIT 0, 1 ", $this->TroubleReportNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myTroubleReportDepAdr	=	new TroubleReport() ;
			if ( $numrows == 0) {
				$myTroubleReportDepAdr->TroubleReportNo	=	$this->TroubleReportNo . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ; 
				$myTroubleReportDepAdr->TroubleReportNo	=	sprintf( "%s-$_pref%03d", $this->TroubleReportNo, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myTroubleReportDepAdr->storeInDb() ;
			$myTroubleReportDepAdr->getFromPostL() ;
			$myTroubleReportDepAdr->updateInDb() ;
			$this->_valid  =       true ; 
		}
		return $myTroubleReportDepAdr->TroubleReportNo ;
	}
	/**
	 * @param	string	$_key
	 * @param	int	$_id
	 * @param	mixed	$_val
	 */
	function	export( $_key="", $_id=0, $_val="") {
		$oFile	=	fopen( $this->path->Archive."XML/down/Cust".$this->TroubleReportNo.".xml", "w+") ;
		fwrite( $oFile, "<TroubleReportnregistrierung>\n") ;
		$buffer	=	$this->getXMLF() ;
		fwrite( $oFile, $buffer) ;
		$myTroubleReportAction	=	new TroubleReportAction() ;
		$myTroubleReportAction->TroubleReportNo	=	$this->TroubleReportNo ;
		for ( $myTroubleReportAction->_firstFromDb( "TroubleReportNo='$this->TroubleReportNo' ORDER BY TroubleReportActionNo ") ;
					$myTroubleReportAction->_valid == 1 ;
					$myTroubleReportAction->_nextFromDb()) {
			$buffer	=	$myTroubleReportAction->getXMLF() ;
			fwrite( $oFile, $buffer) ;
		}
		fwrite( $oFile, "</TroubleReportnregistrierung>\n") ;
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
				$_kundeNrCrit	=	$sCrit ;
 				$_POST['_step']	=	$_val ;
				$filter	=	"( C.TroubleReportNo like '%" . $_kundeNrCrit . "%' OR C.Slogan  like '%" . $_kundeNrCrit . "%') " ;
				$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
				$myObj->addCol( "Id", "int") ;
				$myObj->addCol( "TroubleReportNo", "var") ;
				$myObj->addCol( "Slogan", "var") ;
				$myObj->addCol( "CustomerName", "var") ;
				$myObj->addCol( "DateOfIssue", "var") ;
				$reply->replyData	=	$myObj->tableFromDb( ", CONCAT( Cu.CustomerName1, \" \", Cu.CustomerName2) AS CustomerName ",
										"LEFT JOIN Customer AS Cu ON Cu.CustomerNo = C.CustomerNo ",
										$filter,
										"ORDER BY C.TroubleReportNo ASC ",
										"TroubleReport",
										"TroubleReport",
										"C.Id, C.TroubleReportNo, C.Slogan, C.DateOfIssue ") ;
				break ;
			case	"TroubleReportAction"	:
				$tmpObj	=	new $objName() ;
				$tmpObj->setId( $_id) ;
				$reply->replyData	=	$tmpObj->tableFromDb( "", "", "TroubleReportNo = '$this->TroubleReportNo' ") ;
				break ;
			case	"LiefTroubleReport"	:
				$tmpObj	=	new TroubleReport() ;
				$tmpObj->setId( $_id) ;
				$tmpObj->addCol( "Company", "varchar") ;
				$tmpObj->addCol( "City", "varchar") ;
				$tmpObj->addCol( "Address", "varchar") ;
				$ret	=	$tmpObj->tableFromDb( "", "", "TroubleReportNo like '".$this->TroubleReportNo."-L%' ",
													"ORDER BY TroubleReportNo ", "TroubleReportLiefTroubleReport",
													"",
													"C.Id, C.TroubleReportNo, CONCAT( C.TroubleReportName1, \" \", C.TroubleReportName2) AS Company, "
														. "CONCAT( C.ZIP, \" \", C.City) AS City, "
														. "CONCAT( C.Street, \" \", C.Number) AS Address "
				) ;
				$reply->replyData	=	str_replace( "TroubleReport>", "LiefTroubleReport>", $ret) ;
				break ;
			case	"RechTroubleReport"	:
				$tmpObj	=	new TroubleReport() ;
				$tmpObj->setId( $_id) ;
				$tmpObj->addCol( "Company", "varchar") ;
				$ret	=	$tmpObj->tableFromDb( "", "", "TroubleReportNo like '".$this->TroubleReportNo."-R%' ",
													"ORDER BY TroubleReportNo ",
													"TroubleReportRechTroubleReport",
													"",
													"C.Id, C.TroubleReportNo, CONCAT( C.TroubleReportName1, C.TroubleReportName2) AS Company ") ;
				$reply->replyData	=	str_replace( "TroubleReport>", "RechTroubleReport>", $ret) ;
				break ;
			case	"AddTroubleReport"	:
				$tmpObj	=	new TroubleReport() ;
				$tmpObj->setId( $_id) ;
				$tmpObj->addCol( "Company", "varchar") ;
				$ret	=	$tmpObj->tableFromDb( "", "", "TroubleReportNo like '".$this->TroubleReportNo."-A%' ",
													"ORDER BY TroubleReportNo ",
													"TroubleReportAddTroubleReport",
													"",
													"C.Id, C.TroubleReportNo, CONCAT( C.TroubleReportName1, C.TroubleReportName2) AS Company ") ;
				$reply->replyData	=	str_replace( "TroubleReport>", "AddTroubleReport>", $ret) ;
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
		$filter	=	"(( C.TroubleReportNo like '" . $custNoFilter . "%' ) " ;
		$filter	.=	"  AND ( C.TroubleReportName1 like '%" . $companyFilter . "%' OR C.TroubleReportName2 like '%" . $companyFilter . "%' ) " ;
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
		$myObj->addCol( "TroubleReportNo", "var") ;
		$myObj->addCol( "TroubleReportName1", "var") ;
		$myObj->addCol( "TroubleReportName2", "var") ;
		$myObj->addCol( "ZIP", "var") ;
		$myObj->addCol( "LastName", "var") ;
		$ret	=	$myObj->tableFromDb( ", CC.LastName AS LastName, CC.Phone AS Phone ",
								"LEFT JOIN TroubleReportAction AS CC on CC.TroubleReportNo = C.TroubleReportNo ",
								$filter,
								"ORDER BY C.TroubleReportNo ASC ",
								"TroubleReport",
								"TroubleReport",
								"CC.Id, C.TroubleReportNo, C.TroubleReportName1, C.TroubleReportName2, C.ZIP ") ;
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
		FDbg::begin( 1, "TroubleReport.php", "TroubleReport", "getListAsXML( '$_key', $_id, '$_val')") ;
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
		if ( isset( $_POST['_STroubleReportNo']))
			$_adrNrCrit	=	$_POST['_STroubleReportNo'] ;
		if ( isset( $_POST['_SCompany']))
			$_firmaCrit	=	$_POST['_SCompany'] ;
		if ( isset( $_POST['_SName']))
			$_nameCrit	=	$_POST['_SName'] ;
		if ( isset( $_POST['_SZIP']))
			$_zipCrit	=	$_POST['_SZIP'] ;
		$_POST['_step']	=	$_id ;
		$filter	.=	"(" ;
		$filter	.=	"( C.TroubleReportNo like '%" . $_adrNrCrit . "%' OR C.Slogan like '%" . $_searchCrit . "%' ) " ;
		$filter	.=	"  AND ( C.TroubleReportSlogan like '%" . $_firmaCrit . "%' OR C.TroubleReportName2 LIKE '%" . $_firmaCrit . "%') " ;
		if ( $_nameCrit != "")
			$filter	.=	"  AND ( AK.FirstName like '%$_nameCrit%' OR AK.LastName like '%$_nameCrit%') " ;
		if ( $_zipCrit != "")
			$filter	.=	"  AND ( C.ZIP like '%$_zipCrit%' ) " ;
		if ( $_searchCrit != "")
			$filter	.=	"  AND ( C.TroubleReportName1 like '%$_searchCrit%' OR C.TroubleReportName2 like '%$_searchCrit%' OR AK.FirstName like '%$_searchCrit%' OR AK.LastName like '%$_searchCrit%') " ;
		$filter	.=	")" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "TroubleReportNo", "var") ;
		$myObj->addCol( "Slogan", "var") ;
		$reply->replyData	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.TroubleReportNo ASC ",
								"TroubleReport",
								"TroubleReport",
								"C.Id, C.TroubleReportNo, C.S ") ;
		FDbg::end( 1, "TroubleReport.php", "TroubleReport", "getListAsXML( '$_key', $_id, '$_val')") ;
		return $reply ;
	}
}
?>
