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
 * LEGSVertrag - Base Class
 *
 * @package Application
 * @subpackage LEGSVertrag
 */
class	LEGSVertrag	extends	AppObject	{
	public	$Rights	=	0x00000000 ;
	public	$Remark	=	"" ;

	/**
	 *
	 */
	function	__construct( $_myLEGS="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myLEGS')") ;
		parent::__construct( "LEGSVertrag", "LEGS", "cloud") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myLEGS) > 0) {
			try {
				$this->setLEGS( $_myLEGS) ;
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
	function	setLEGS( $_myLEGS) {
		$this->LEGS	=	$_myLEGS ;
		$this->reload() ;
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
		$myLEGSVertrag	=	new LEGSVertrag() ;
		if ( $myLEGSVertrag->first( "LENGTH(IKNr) = 9", "IKNr DESC")) {
			$this->getFromPostL() ;
			$this->IKNr	=	sprintf( "%08d", intval( $myLEGSVertrag->IKNr) + 1) ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], LEGSVertrag invalid after creation!'") ;
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
		$this->_upd( $_key, $_id, $_val, $_replyl) ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "LEGSVertrag.php", "LEGSVertrag", "_upd()") ;
		parent::upd( $_key, $_id, $_val, $_replyl) ;
		$this->_addRem( FTr::tr( "LEGSVertrag updated")) ;
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
		if ( $objName == "LEGSVertragAdresse") {
			$myLEGSVertragAdresse	=	new LEGSVertragAdresse() ;
			$myLEGSVertragAdresse->IKNr	=	$this->IKNr ;
			$myLEGSVertragAdresse->newLEGSVertragAdresse() ;
			$myLEGSVertragAdresse->getFromPostL() ;
			$myLEGSVertragAdresse->updateInDb() ;
			$reply->message	=	FTr::tr( "Contact succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
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
		if ( $objName == "LEGSVertragAdresse") {
			$myLEGSVertragAdresse	=	new LEGSVertragAdresse() ;
			$myLEGSVertragAdresse->IKNr	=	$this->IKNr ;
			$myLEGSVertragAdresse->newLEGSVertragAdresse() ;
			$myLEGSVertragAdresse->getFromPostL() ;
			$myLEGSVertragAdresse->updateInDb() ;
			return $myLEGSVertragAdresse->LEGSVertragAdresseNo ;
		} else if ( $objName == "LiefLEGSVertrag") {
			return $this->_addDepLEGSVertrag( "L") ;
		} else if ( $objName == "RechLEGSVertrag") {
			return $this->_addDepLEGSVertrag( "R") ;
		} else if ( $objName == "AddLEGSVertrag") {
			return $this->_addDepLEGSVertrag( "A") ;
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
	function	newLEGSVertrag( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "LEGSVertrag.php::LEGSVertrag::newLEGSVertrag( $_nsStart, $_nsEnd):") ;
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
	 *	Funktion:	identify LEGSVertrag
	 *
	 *	Versucht irgendwie den LEGSVertragn per Password zu identifizieren
	 *	Zuerst wird versucht, den LEGSVertragn per LEGSVertragnNr zu finden.
	 *	Falls keine LEGSVertragnnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn LEGSVertragndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */
	/**
	 * create a new 'LEGSVertrag' from a form sending POST data
	 */
	function	newLEGSVertragFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new LEGSVertragAdresse() ;
		$myCuContact->getFromPost() ;
		if ( $this->LEGSVertragName1 == "") {
			$this->LEGSVertragName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->LEGSVertragName1) < 8) {
			self::$err['_ILEGSVertragName1']	=	true ;
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
			$this->newLEGSVertrag( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->IKNr	=	$this->IKNr ;
			$myCuContact->newLEGSVertragAdresse() ;
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
			FDb::query( "UPDATE LEGSVertrag SET Password = '" . $this->Password . "' WHERE IKNr = '" . $this->IKNr . "' ") ;
			FDb::query( "UPDATE LEGSVertragAdresse SET Password = '" . $this->Password . "' WHERE IKNr = '" . $this->IKNr . "' ") ;
		}
		FDbg::end( 1, basename( __FILE__), __CLASS__, __METHOD__."atePassword( '*')") ;
	}

  /**
     *
     */
    function    fetchForNew() {
        $this->LEGSVertragName1   =   $_POST['_ILEGSVertragName1'] ;
        $this->LEGSVertragName2   =   $_POST['_ILEGSVertragName2'] ;
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
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"LEGSPosition"	:
			$myLEGSPosition	=	new LEGSPosition() ;
			if ( $_id == -1) {
				$myLEGSPosition->Id	=	-1 ;
			} else {
				$myLEGSPosition->setId( $_id) ;
			}
			$reply	=	$myLEGSPosition->getAsXML( $_key, $_id, $_val, $reply) ;
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
	function	getLEGSVertragAdresseAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myLEGSVertragAdresse	=	new LEGSVertragAdresse() ;
		$myLEGSVertragAdresse->setId( $_id) ;
		$ret	.=	$myLEGSVertragAdresse->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getLEGSVertragLiefLEGSVertragAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myLEGSVertragAdr	=	new LEGSVertrag() ;
		$myLEGSVertragAdr->setId( $_id) ;
		$ret	.=	$myLEGSVertragAdr->getXMLF( "LiefLEGSVertrag") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getLEGSVertragRechLEGSVertragAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myLEGSVertragAdr	=	new LEGSVertrag() ;
		$myLEGSVertragAdr->setId( $_id) ;
		$ret	.=	$myLEGSVertragAdr->getXMLF( "RechLEGSVertrag") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getLEGSVertragAddLEGSVertragAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myLEGSVertragAdr	=	new LEGSVertrag() ;
		$myLEGSVertragAdr->setId( $_id) ;
		$ret	.=	$myLEGSVertragAdr->getXMLF( "AddLEGSVertrag") ;
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
	function	_addDepLEGSVertrag( $_pref) {
		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->IKNr) ;
		$this->IKNr	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT IKNr FROM LEGSVertrag WHERE IKNr LIKE '%s-$_pref%%' ORDER BY IKNr DESC LIMIT 0, 1 ", $this->IKNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myLEGSVertragDepAdr	=	new LEGSVertrag() ;
			if ( $numrows == 0) {
				$myLEGSVertragDepAdr->IKNr	=	$this->IKNr . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myLEGSVertragDepAdr->IKNr	=	sprintf( "%s-$_pref%03d", $this->IKNr, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myLEGSVertragDepAdr->storeInDb() ;
			$myLEGSVertragDepAdr->getFromPostL() ;
			$myLEGSVertragDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myLEGSVertragDepAdr->IKNr ;
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
		case	"LEGSVertrag"	:
			$myObj	=	new FDbObject( "LEGSVertrag", "IKNr", "cloud", "v_LEGSVertragSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"LEGS like '%" . $sCrit . "%' OR Beschreibung like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["LEGS"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"LEGSPosition"	:
			$myObj	=	new FDbObject( "LEGSPosition", "Id", "cloud", "v_LEGSPositionSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( LEGS = '" . $this->LEGS . "') " ;
			$filter2	=	"" ; // "HMVNr like '%" . $sCrit . "%' OR Bezeichnung1 like '%" . $sCrit . "%' " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["HMVNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"VertragLE"	:
			$myObj	=	new FDbObject( "VertragLE", "Id", "cloud", "v_VertragLESurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( LEGS = '" . $this->LEGS . "') " ;
			$filter2	=	"" ; // "HMVNr like '%" . $sCrit . "%' OR Bezeichnung1 like '%" . $sCrit . "%' " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["LEIKNr"]) ;
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
		$myLEGSVertrag	=	new LEGSVertrag() ;
		$myLEGSVertrag->setIterCond( "IKNr like '%" . $sCrit . "%' OR Name1 like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $myLEGSVertrag as $institution) {
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
}
?>
