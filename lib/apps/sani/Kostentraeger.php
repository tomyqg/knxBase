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
 * Kostentraeger - Base Class
 *
 * @package Application
 * @subpackage Kostentraeger
 */
class	Kostentraeger	extends	AppObject	{
	public	$Rights	=	0x00000000 ;
	/**
	 *
	 */
	function	__construct( $_myIKNr="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myIKNr')") ;
		parent::__construct( "Kostentraeger", "IKNr", "cloud") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myIKNr) > 0) {
			try {
				$this->setKey( $_myIKNr) ;
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
	function	setIKNr( $_myIKNr) {
		$this->setKey( $_myIKNr) ;
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
		$myKostentraeger	=	new Kostentraeger() ;
		if ( $myKostentraeger->first( "LENGTH(IKNr) = 9", "IKNr DESC")) {
			$this->getFromPostL() ;
			$this->IKNr	=	sprintf( "%08d", intval( $myKostentraeger->IKNr) + 1) ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], Kostentraeger invalid after creation!'") ;
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
	function	upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$this->_upd( $_key, $_id, $_val, $_reply) ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "Kostentraeger.php", "Kostentraeger", "_upd()") ;
		parent::upd( $_key, $_id, $_val, $_reply) ;
//		$this->_addRem( FTr::tr( "Kostentraeger updated")) ;
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
		return $this->getAsXML() ;
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
		if ( $objName == "KostentraegerAdresse") {
			$myKostentraegerAdresse	=	new KostentraegerAdresse() ;
			$myKostentraegerAdresse->IKNr	=	$this->IKNr ;
			$myKostentraegerAdresse->newKostentraegerAdresse() ;
			$myKostentraegerAdresse->getFromPostL() ;
			$myKostentraegerAdresse->updateInDb() ;
			$reply->message	=	FTr::tr( "Contact succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "LiefKostentraeger") {
			$this->_addDepKostentraeger( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefKostentraeger", $reply) ;
		} else if ( $objName == "RechKostentraeger") {
			$this->_addDepKostentraeger( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechKostentraeger", $reply) ;
		} else if ( $objName == "AddKostentraeger") {
			$this->_addDepKostentraeger( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddKostentraeger", $reply) ;
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
	function	_addDep( $_key="", $_id=-1, $_val="", $reply = NULL) {
		$objName	=	$_val ;
		if ( $objName == "KostentraegerAdresse") {
			$myKostentraegerAdresse	=	new KostentraegerAdresse() ;
			$myKostentraegerAdresse->IKNr	=	$this->IKNr ;
			$myKostentraegerAdresse->newKostentraegerAdresse() ;
			$myKostentraegerAdresse->getFromPostL() ;
			$myKostentraegerAdresse->updateInDb() ;
			return $myKostentraegerAdresse->KostentraegerAdresseNo ;
		} else if ( $objName == "LiefKostentraeger") {
			return $this->_addDepKostentraeger( "L") ;
		} else if ( $objName == "RechKostentraeger") {
			return $this->_addDepKostentraeger( "R") ;
		} else if ( $objName == "AddKostentraeger") {
			return $this->_addDepKostentraeger( "A") ;
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
	function	newKostentraeger( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Kostentraeger.php::Kostentraeger::newKostentraeger( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  IKNr >= '$_nsStart' AND IKNr <= '$_nsEnd' " .
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
	 *	Funktion:	identify Kostentraeger
	 *
	 *	Versucht irgendwie den Kostentraegern per Password zu identifizieren
	 *	Zuerst wird versucht, den Kostentraegern per KostentraegernNr zu finden.
	 *	Falls keine Kostentraegernnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn Kostentraegerndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */
	/**
	 * create a new 'Kostentraeger' from a form sending POST data
	 */
	function	newKostentraegerFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new KostentraegerAdresse() ;
		$myCuContact->getFromPost() ;
		if ( $this->KostentraegerName1 == "") {
			$this->KostentraegerName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->KostentraegerName1) < 8) {
			self::$err['_IKostentraegerName1']	=	true ;
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
			$this->newKostentraeger( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->IKNr	=	$this->IKNr ;
			$myCuContact->newKostentraegerAdresse() ;
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
			FDb::query( "UPDATE Kostentraeger SET Password = '" . $this->Password . "' WHERE IKNr = '" . $this->IKNr . "' ") ;
			FDb::query( "UPDATE KostentraegerAdresse SET Password = '" . $this->Password . "' WHERE IKNr = '" . $this->IKNr . "' ") ;
		}
		FDbg::end( 1, basename( __FILE__), __CLASS__, __METHOD__."atePassword( '*')") ;
	}

  /**
     *
     */
    function    fetchForNew() {
        $this->KostentraegerName1   =   $_POST['_IKostentraegerName1'] ;
        $this->KostentraegerName2   =   $_POST['_IKostentraegerName2'] ;
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
	 */
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
		case	"LiefKostentraeger"	:
			$myKostentraeger	=	new Kostentraeger() ;
			if ( $_id == -1) {
			} else {
				$myKostentraeger->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Kostentraeger>", "LiefKostentraeger>", $myKostentraeger->getAsXML()) ;
			break ;
		case	"RechKostentraeger"	:
			$myKostentraeger	=	new Kostentraeger() ;
			if ( $_id == -1) {
			} else {
				$myKostentraeger->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Kostentraeger>", "RechKostentraeger>", $myKostentraeger->getAsXML()) ;
			break ;
		case	"AddKostentraeger"	:
			$myKostentraeger	=	new Kostentraeger() ;
			if ( $_id == -1) {
			} else {
				$myKostentraeger->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Kostentraeger>", "AddKostentraeger>", $myKostentraeger->getAsXML()) ;
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
	function	getKostentraegerAdresseAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myKostentraegerAdresse	=	new KostentraegerAdresse() ;
		$myKostentraegerAdresse->setId( $_id) ;
		$ret	.=	$myKostentraegerAdresse->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getKostentraegerLiefKostentraegerAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myKostentraegerAdr	=	new Kostentraeger() ;
		$myKostentraegerAdr->setId( $_id) ;
		$ret	.=	$myKostentraegerAdr->getXMLF( "LiefKostentraeger") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getKostentraegerRechKostentraegerAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myKostentraegerAdr	=	new Kostentraeger() ;
		$myKostentraegerAdr->setId( $_id) ;
		$ret	.=	$myKostentraegerAdr->getXMLF( "RechKostentraeger") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getKostentraegerAddKostentraegerAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myKostentraegerAdr	=	new Kostentraeger() ;
		$myKostentraegerAdr->setId( $_id) ;
		$ret	.=	$myKostentraegerAdr->getXMLF( "AddKostentraeger") ;
		return $ret ;
	}

	/**
	 *
	 */
	function	loadGKV( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	_addDepKostentraeger( $_pref) {
		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->IKNr) ;
		$this->IKNr	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT IKNr FROM Kostentraeger WHERE IKNr LIKE '%s-$_pref%%' ORDER BY IKNr DESC LIMIT 0, 1 ", $this->IKNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myKostentraegerDepAdr	=	new Kostentraeger() ;
			if ( $numrows == 0) {
				$myKostentraegerDepAdr->IKNr	=	$this->IKNr . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myKostentraegerDepAdr->IKNr	=	sprintf( "%s-$_pref%03d", $this->IKNr, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myKostentraegerDepAdr->storeInDb() ;
			$myKostentraegerDepAdr->getFromPostL() ;
			$myKostentraegerDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myKostentraegerDepAdr->IKNr ;
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
		case	"Kostentraeger"	:
			$myObj	=	new FDbObject( "Kostentraeger", "IKNr", "cloud", "v_KostentraegerSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"IKNr like '%" . $sCrit . "%' OR Name like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KostentraegerAnschrift"	:
			$myObj	=	new FDbObject( "KostentraegerAnschrift", "Id", "cloud", "v_KostentraegerAnschriftSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( IKNr = '" . $this->IKNr . "') " ;
			$filter2	=	"" ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["IKNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KostentraegerVerweis"	:
			$myObj	=	new FDbObject( "KostentraegerVerweis", "Id", "cloud", "v_KostentraegerVerweisSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( IKNr = '" . $this->IKNr . "') " ;
			$filter2	=	"" ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["IKNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KostentraegerVerweisVon"	:
			$myObj	=	new FDbObject( "KostentraegerVerweis", "Id", "cloud", "v_KostentraegerVerweisSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( IKNrVerweis = '" . $this->IKNr . "') " ;
			$filter2	=	"" ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["IKNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "KostentraegerVerweisVon") ;
			break ;
		case	"KostentraegerGKVList"	:
			$myObj	=	new FDbObject( "KostentraegerGKVList", "Id", "cloud", "v_KostentraegerGKVListSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"1 = 1 " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( ["Filename DESC"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}

	/**
	 *
	 */
	function	evaluate( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myObj	=	new FDbObject( "KostentraegerGKVList", "Id", "cloud", "v_KostentraegerGKVListSurvey") ;				// no specific object we need here
		$myObj->setId( $_id) ;
		if ( $myObj->isValid()) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
							"entry found ...") ;
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
							$_SERVER['DOCUMENT_ROOT']) ;
			$path	=	$_SERVER['DOCUMENT_ROOT'] . "/sani/rsrc/downloads/gkv/" ;
			$file	=	$myObj->Filename ;
			$filename	=	$path . $file ;
			$inFile	=	fopen( $filename, "r") ;
			if ( $inFile) {
				$this->_evaluate( $inFile) ;
				fclose( $inFile) ;
			} else {
				FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
								"file {$filename} not found ...") ;
			}
		} else {
		}
		FDbg::end() ;
		return $reply ;
	}

	function	_evaluate( $_file) {
		$state	=	0 ;
		$myKostentraeger			=	new Kostentraeger() ;
		$myKostentraegerAnschrift	=	new KostentraegerAnschrift() ;
		$myKostentraegerKontakt	=	new KostentraegerKontakt() ;
		$myKostentraegerVerweis	=	new KostentraegerVerweis() ;
		while ( $buffer = fgets( $_file, 1024)) {
//				FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)",
//								$buffer) ;
			$data	=	explode( "+", $buffer) ;
			switch ( $state) {
			case	0	:
				if ( $data[0] == "UNH") {
					$state	=	1 ;
				}
				break ;
			case	1	:		// in UNH
				if ( $data[0] == "UNT") {
					if ( ! $myKostentraeger->setKey( $this->IKNr)) {
						$this->_Id		=	0 ;
						$this->storeInDb() ;
						FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)",
										"Name .... " . $this->Name1) ;
					}
					$state	=	0 ;
				} else  if ( $data[0] == "IDK") {
					FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)",
									"IK ...... " . $data[1]) ;
					$this->IKNr		=	$data[1] ;
					$this->Name1	=	iconv( "windows-1252", "utf-8", $data[3]) ;
				} else  if ( $data[0] == "ANS") {						// Anschrift
					$myKostentraegerAnschrift->IKNr		=	$this->IKNr ;
					$myKostentraegerAnschrift->Typ		=	intval( $data[1]) ;
					$myKostentraegerAnschrift->PLZ		=	$data[2] ;
					$myKostentraegerAnschrift->Ort		=	iconv( "windows-1252", "utf-8", $data[3]) ;
					$myKostentraegerAnschrift->Strasse	=	iconv( "windows-1252", "utf-8", $data[4]) ;
					$myKostentraegerAnschrift->storeInDb() ;
				} else  if ( $data[0] == "ASP") {						// Ansprechpartner (hier: Kontakt)
					$myKostentraegerKontakt->IKNr		=	$this->IKNr ;
					$myKostentraegerKontakt->Telefon	=	iconv( "windows-1252", "utf-8", $data[2]) ;
					$myKostentraegerKontakt->Fax		=	iconv( "windows-1252", "utf-8", $data[3]) ;
					$myKostentraegerKontakt->Name		=	iconv( "windows-1252", "utf-8", $data[4]) ;
					$myKostentraegerKontakt->storeInDb() ;
				} else  if ( $data[0] == "VKG") {						// Verknuepfung (hier: Kontakt)
					$myKostentraegerVerweis->IKNr		=	$this->IKNr ;
					$myKostentraegerVerweis->Typ			=	intval( $data[1]) ;
					$myKostentraegerVerweis->IKNrVerweis	=	$data[2] ;
					$myKostentraegerVerweis->Bundesland	=	intval( $data[7]) ;
					$myKostentraegerVerweis->Bezirk		=	intval( $data[8]) ;
					$myKostentraegerVerweis->Leistungsart	=	intval( $data[9]) ;
					$myKostentraegerVerweis->storeInDb() ;
				}
				break ;
			}
		}
	}

	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	acList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$a_json = array();
		$a_json_row = array();
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaTextJSON ;
		$sCrit	=	"" ;
		if ( isset( $_REQUEST['term']))
			$sCrit	=	$_REQUEST['term'] ;
		$myKostentraeger	=	new Kostentraeger() ;
		$myKostentraeger->setIterCond( "IKNr like '%" . $sCrit . "%' OR Name1 like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $myKostentraeger as $institution) {
			if ( $il0 < 50) {
				$a_json_row["id"]		=	$institution->Id ;
				$a_json_row["value"]	=	$institution->IKNr ;
				$a_json_row["label"]	=	$institution->IKNr . ", " . $institution->Name1 ;
				$a_json_row["iKNr"]		=	$institution->IKNr ;
				$a_json_row["Name1"]	=	$institution->Name1 ;
				array_push( $a_json, $a_json_row);
			}
			$il0++ ;
		}
		$reply->data = json_encode($a_json);
		FDbg::end() ;
		return $reply ;
	}

	/**
	 *
	 */
	protected	function	_postInstantiate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}

	/**
	 *
	 */
	protected	function	_postLoad() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
}
?>
