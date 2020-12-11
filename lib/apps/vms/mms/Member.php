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
 * Member - Base Class
 *
 * @package Application
 * @subpackage Member
 */
class	Member	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myMemberNo="") {
		parent::__construct( "Member", "MemberNo") ;
		if ( strlen( $_myMemberNo) > 0) {
			try {
				$this->setMemberNo( $_myMemberNo) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	
	/**
	 *
	 */
	function	setMemberNo( $_myMemberNo) {
		$this->MemberNo	=	$_myMemberNo ;
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
		$myMember	=	new Member() ;
		if ( $_val == "MemberContact"){
			$this->addDep( $_key, $_id, $_val, $_reply) ;
		} else if ( $myMember->first( "LENGTH(MemberNo) = 8", "MemberNo DESC")) {
			$this->getFromPostL() ;
			$this->MemberNo	=	sprintf( "%08d", intval( $myMember->MemberNo) + 1) ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			$this->getFromPostL() ;
			$this->MemberNo	=	sprintf( "%08d", intval( $myMember->MemberNo) + 1) ;
			if ( ! $this->storeInDb()) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"object[".$this->cacheName."], Member invalid after creation!'") ;
			}
		}
		return $this->isValid() ;
	}

	function	upd( $_key="", $_id=-1, $_val="", $_reply = NULL) {
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$this->getFromPostL( "MemberNo") ;
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
		if ( $objName == "MemberContact") {
			$myMemberContact	=	new MemberContact() ;
			$myMemberContact->MemberNo	=	$this->MemberNo ;
			$myMemberContact->first( "MemberNo = '".$this->MemberNo."'", "MemberContactNo DESC" ) ;
			$myContactNo	=	$myMemberContact->MemberContactNo ;
			$myMemberContact->getFromPostL() ;
			$myMemberContact->MemberContactNo	=	sprintf( "%03d", intval( $myContactNo) + 1) ;
			$myMemberContact->storeInDb() ;
		} else if ( $objName == "LiefMember") {
			$this->_addDepMember( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefMember", $_reply) ;
		} else if ( $objName == "RechMember") {
			$this->_addDepMember( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechMember", $_reply) ;
		} else if ( $objName == "AddMember") {
			$this->_addDepMember( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddMember", $_reply) ;
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
		if ( $objName == "MemberContact") {
			$myMemberContact	=	new MemberContact() ;
			$myMemberContact->MemberNo	=	$this->MemberNo ;
			$myMemberContact->newMemberContact() ;
			$myMemberContact->getFromPostL() ;
			$myMemberContact->updateInDb() ;
			return $myMemberContact->MemberContactNo ;
		} else if ( $objName == "LiefMember") {
			return $this->_addDepMember( "L") ;
		} else if ( $objName == "RechMember") {
			return $this->_addDepMember( "R") ;
		} else if ( $objName == "AddMember") {
			return $this->_addDepMember( "A") ;
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
	function	newMember( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Member.php::Member::newMember( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  MemberNo >= '$_nsStart' AND MemberNo <= '$_nsEnd' " .
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
	 *	Funktion:	identify Member
	 *
	 *	Versucht irgendwie den Membern per Password zu identifizieren
	 *	Zuerst wird versucht, den Membern per MembernNr zu finden.
	 *	Falls keine Membernnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn Memberndaten existieren, dann muss geprueft werden, ob das Password
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
			$this->eMail	=	mysql_escape_string( $this->MemberNo) ;
			$this->fetchFromDbByEmail() ;
		}
		if ( $this->_valid) {
			error_log( "Member.php::Member::identifyMember(...): Member is valid, MemberNo = '".$this->MemberNo."' ") ;
			$this->_valid	=	false ;
			if ( $this->Password != md5( $password)) {
				error_log( "Member.php::Member::identifyMember(...): password is not valid Db: '".$this->Password."' ".md5( $password)) ;
				$this->_status	=	-6 ;
			} else {
				error_log( "Member.php::Member::identifyMember(...): password is valid") ;
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
	function	activateMember( $key) {
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->ActivationKey	=	$key ;
		$this->fetchFromDbByActKey() ;
		if ( !$this->_valid) {
//			$this->eMail	=	$this->MemberNo ;
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
	 * create a new member from a form sending POST data
	 */
	function	newMemberFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new MemberContact() ;
		$myCuContact->getFromPost() ;
		if ( $this->MemberName1 == "") {
			$this->MemberName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->MemberName1) < 8) {
			self::$err['_IMemberName1']	=	true ;
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
			$this->newMember( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->MemberNo	=	$this->MemberNo ;
			$myCuContact->newMemberContact() ;
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
			FDb::query( "UPDATE Member SET Password = '" . $this->Password . "' WHERE MemberNo = '" . $this->MemberNo . "' ") ;
			FDb::query( "UPDATE MemberContact SET Password = '" . $this->Password . "' WHERE MemberNo = '" . $this->MemberNo . "' ") ;
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
		$query	.=	"from Member " ;

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
		$query	.=	"from Member " ;
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
	function	sendMemberNo() {
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
								FTr::tr( "Your registration, your Member No. #1", array( "%s:".$this->MemberNo)),
								"Bcc: ".$this->eMail->Archive."\n") ;

			$myMailText	=	new SysTexte() ;
			$myMailText->setKeys( "EMailMemberMemberNo") ;
			$mailData	=	array( "#MemberNo" => $this->MemberNo,
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
		FDbg::trace( 2, FDbg::mdTrcInfo1, "Member.php", "Member", "sendPassword( <...>)", "sending new password") ;
		error_log( ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> '".$newPassword."'") ;
		$this->PasswordUser =   $newPassword ;
		$this->Password =   md5( $newPassword) ;
		$this->Password =   md5( $newPassword) ;
        $this->updateInDb() ;
		$myMember	=	new Member() ;
		$myMember->setMemberNo( $this->MemberNo) ;
		if ( $myMember->isValid()) {
			$myMember->Password	=	md5( $newPassword) ;
			$myMember->updateColInDb( "Password") ;
			$myMemberContact	=	new MemberContact() ;
			$myMemberContact->setIterCond( "MemberNo = '".$this->MemberNo."'") ;
			foreach ( $myMemberContact as $customerContact) {
				$myMemberContact->Password	=	md5( $newPassword) ;
				$myMemberContact->updateColInDb( "Password") ;
			}
		}
		FDbg::end() ;
		return $this->isValid() ;
	}
   /**
     *
     */
    function    fetchForNew() {
        $this->MemberName1   =   $_POST['_IMemberName1'] ;
        $this->MemberName2   =   $_POST['_IMemberName2'] ;
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
	function	addRem( $_key="", $_id=-1, $_val="", $_reply=null) {
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
	function	getMemberContactMailAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myMemberContact	=	new MemberContact() ;
		if ( $myMemberContact->setId( $_id)) {
			$ret	.=	"<MailData>" ;
			$ret	.=	"<eMail type='varchar(32)'>" . $myMemberContact->eMail . "</eMail>" ;
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
		if ( $_val == "MemberContact"){
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
		case	"MemberContact"	:
			$myMemberContact	=	new MemberContact() ;
			if ( $_id == -1) {
				$myMemberContact->Id	=	-1 ;
			} else {
				$myMemberContact->setId( $_id) ;
			}
			$_reply->replyData	=	$myMemberContact->getXMLF() ;
			break ;
		default	:
			$_reply	=	parent::getDepAsXML( $_key, $_id, $_val, $_reply) ;
			break ;
		}
		return $_reply ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getMemberContactAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myMemberContact	=	new MemberContact() ;
		$myMemberContact->setId( $_id) ;
		$ret	.=	$myMemberContact->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getMemberLiefMemberAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myMemberAdr	=	new Member() ;
		$myMemberAdr->setId( $_id) ;
		$ret	.=	$myMemberAdr->getXMLF( "LiefMember") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getMemberRechMemberAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myMemberAdr	=	new Member() ;
		$myMemberAdr->setId( $_id) ;
		$ret	.=	$myMemberAdr->getXMLF( "RechMember") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getMemberAddMemberAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myMemberAdr	=	new Member() ;
		$myMemberAdr->setId( $_id) ;
		$ret	.=	$myMemberAdr->getXMLF( "AddMember") ;
		return $ret ;
	}
	/**
	 *
	 */
	function	_addDepMember( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->MemberNo) ;
		$this->MemberNo	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT MemberNo FROM Member WHERE MemberNo LIKE '%s-$_pref%%' ORDER BY MemberNo DESC LIMIT 0, 1 ", $this->MemberNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myMemberDepAdr	=	new Member() ;
			if ( $numrows == 0) {
				$myMemberDepAdr->MemberNo	=	$this->MemberNo . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myMemberDepAdr->MemberNo	=	sprintf( "%s-$_pref%03d", $this->MemberNo, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myMemberDepAdr->storeInDb() ;
			$myMemberDepAdr->getFromPostL() ;
			$myMemberDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myMemberDepAdr->MemberNo ;
	}
	/**
	 * @param	string	$_key
	 * @param	int	$_id
	 * @param	mixed	$_val
	 */
	function	export( $_key="", $_id=0, $_val="") {
		$oFile	=	fopen( $this->path->Archive."XML/down/Cust".$this->MemberNo.".xml", "w+") ;
		fwrite( $oFile, "<Membernregistrierung>\n") ;
		$buffer	=	$this->getXMLF() ;
		fwrite( $oFile, $buffer) ;
		$myMemberContact	=	new MemberContact() ;
		$myMemberContact->MemberNo	=	$this->MemberNo ;
		for ( $myMemberContact->_firstFromDb( "MemberNo='$this->MemberNo' ORDER BY MemberContactNo ") ;
					$myMemberContact->_valid == 1 ;
					$myMemberContact->_nextFromDb()) {
			$buffer	=	$myMemberContact->getXMLF() ;
			fwrite( $oFile, $buffer) ;
		}
		fwrite( $oFile, "</Membernregistrierung>\n") ;
		fclose( $oFile) ;
		return $this->getXMLComplete() ;
	}

	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
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
		case	"Member"	:
			$myObj	=	new FDbObject( "Member", "MemberNo", "def", "v_MemberSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"MemberName like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$_reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"MemberContact"	:
			$myObj	=	new FDbObject( "MemberContact", "Id", "def", "v_MemberContactSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( MemberNo = '" . $this->MemberNo . "') " ;
			$filter2	=	"( FirstName like '%" . $sCrit . "%' OR LastName  like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["MemberNo", "MemberContactNo"]) ;
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
		$json	=	"" ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
		case	"Member"	:
			$myObj	=	new FDbObject( "Member", "MemberNo", "def", "v_MemberSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"LastName like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$json	=	$myObj->jsonFromQuery( $myQuery) ;
			break ;
		case	"MemberContact"	:
			$myObj	=	new FDbObject( "MemberContact", "Id", "def", "v_MemberContactSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( MemberNo = '" . $this->MemberNo . "') " ;
			$filter2	=	"( FirstName like '%" . $sCrit . "%' OR LastName  like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["MemberNo", "MemberContactNo"]) ;
			$json	=	$myObj->jsonFromQuery( $myQuery) ;
			break ;
		case	"MemberPeriod"	:
			$myObj	=	new FDbObject( "MemberPeriod", "Id", "def", "v_MemberPeriodSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( MemberNo = '" . $this->MemberNo . "') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( ["MemberNo"]) ;
			$json	=	$myObj->jsonFromQuery( $myQuery) ;
			break ;
		}
		return $json ;
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
		$filter	=	"(( C.MemberNo like '" . $custNoFilter . "%' ) " ;
		$filter	.=	"  AND ( C.MemberName1 like '%" . $companyFilter . "%' OR C.MemberName2 like '%" . $companyFilter . "%' ) " ;
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
		$myObj->addCol( "MemberNo", "var") ;
		$myObj->addCol( "MemberName1", "var") ;
		$myObj->addCol( "MemberName2", "var") ;
		$myObj->addCol( "ZIP", "var") ;
		$myObj->addCol( "LastName", "var") ;
		$ret	=	$myObj->tableFromDb( ", CC.LastName AS LastName, CC.Phone AS Phone ",
								"LEFT JOIN MemberContact AS CC on CC.MemberNo = C.MemberNo ",
								$filter,
								"ORDER BY C.MemberNo ASC ",
								"Member",
								"Member",
								"CC.Id, C.MemberNo, C.MemberName1, C.MemberName2, C.ZIP ") ;
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
