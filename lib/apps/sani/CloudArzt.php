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
 * Arzt - Base Class
 *
 * @package Application
 * @subpackage Arzt
 */
class	CloudArzt	extends	Arzt	{
	public	$Rights	=	0x00000000 ;
	/**
	 *
	 */
	function	__construct( $_myArztNr="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myArztNr')") ;
		Arzt::__construct( $_myArztNr, "Arzt", "ArztNr", "cloud") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myArztNr) > 0) {
			try {
				$this->setArztNr( $_myArztNr) ;
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
	function	setArztNr( $_myArztNr) {
		$this->ArztNr	=	$_myArztNr ;
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
		$myArzt	=	new Arzt() ;
		if ( $myArzt->first( "ArztNr DESC", "LENGTH(ArztNr) = 8")) {
			$this->getFromPostL() ;
			$this->ArztNr	=	sprintf( "%08d", intval( $myArzt->ArztNr) + 1) ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], Arzt invalid after creation!'") ;
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
		FDbg::begin( 1, "Arzt.php", "Arzt", "_upd()") ;
		parent::upd( $_key, $_id, $_val, $_replyl) ;
		$this->_addRem( FTr::tr( "Arzt updated")) ;
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
		if ( $objName == "ArztAdresse") {
			$myArztAdresse	=	new ArztAdresse() ;
			$myArztAdresse->ArztNr	=	$this->ArztNr ;
			$myArztAdresse->newArztAdresse() ;
			$myArztAdresse->getFromPostL() ;
			$myArztAdresse->updateInDb() ;
			$reply->message	=	FTr::tr( "Contact succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "LiefArzt") {
			$this->_addDepArzt( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefArzt", $reply) ;
		} else if ( $objName == "RechArzt") {
			$this->_addDepArzt( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechArzt", $reply) ;
		} else if ( $objName == "AddArzt") {
			$this->_addDepArzt( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddArzt", $reply) ;
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
		if ( $objName == "ArztAdresse") {
			$myArztAdresse	=	new ArztAdresse() ;
			$myArztAdresse->ArztNr	=	$this->ArztNr ;
			$myArztAdresse->newArztAdresse() ;
			$myArztAdresse->getFromPostL() ;
			$myArztAdresse->updateInDb() ;
			return $myArztAdresse->ArztAdresseNo ;
		} else if ( $objName == "LiefArzt") {
			return $this->_addDepArzt( "L") ;
		} else if ( $objName == "RechArzt") {
			return $this->_addDepArzt( "R") ;
		} else if ( $objName == "AddArzt") {
			return $this->_addDepArzt( "A") ;
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
	function	newArzt( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Arzt.php::Arzt::newArzt( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  ArztNr >= '$_nsStart' AND ArztNr <= '$_nsEnd' " .
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
	 *	Funktion:	identify Arzt
	 *
	 *	Versucht irgendwie den Arztn per Password zu identifizieren
	 *	Zuerst wird versucht, den Arztn per ArztnNr zu finden.
	 *	Falls keine Arztnnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn Arztndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */
	/**
	 * create a new 'Arzt' from a form sending POST data
	 */
	function	newArztFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new ArztAdresse() ;
		$myCuContact->getFromPost() ;
		if ( $this->ArztName1 == "") {
			$this->ArztName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->ArztName1) < 8) {
			self::$err['_IArztName1']	=	true ;
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
			$this->newArzt( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->ArztNr	=	$this->ArztNr ;
			$myCuContact->newArztAdresse() ;
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
			FDb::query( "UPDATE Arzt SET Password = '" . $this->Password . "' WHERE ArztNr = '" . $this->ArztNr . "' ") ;
			FDb::query( "UPDATE ArztAdresse SET Password = '" . $this->Password . "' WHERE ArztNr = '" . $this->ArztNr . "' ") ;
		}
		FDbg::end( 1, basename( __FILE__), __CLASS__, __METHOD__."atePassword( '*')") ;
	}

  /**
     *
     */
    function    fetchForNew() {
        $this->ArztName1   =   $_POST['_IArztName1'] ;
        $this->ArztName2   =   $_POST['_IArztName2'] ;
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
		case	"LiefArzt"	:
			$myArzt	=	new Arzt() ;
			if ( $_id == -1) {
			} else {
				$myArzt->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Arzt>", "LiefArzt>", $myArzt->getAsXML()) ;
			break ;
		case	"RechArzt"	:
			$myArzt	=	new Arzt() ;
			if ( $_id == -1) {
			} else {
				$myArzt->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Arzt>", "RechArzt>", $myArzt->getAsXML()) ;
			break ;
		case	"AddArzt"	:
			$myArzt	=	new Arzt() ;
			if ( $_id == -1) {
			} else {
				$myArzt->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Arzt>", "AddArzt>", $myArzt->getAsXML()) ;
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
	function	getArztAdresseAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myArztAdresse	=	new ArztAdresse() ;
		$myArztAdresse->setId( $_id) ;
		$ret	.=	$myArztAdresse->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getArztLiefArztAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myArztAdr	=	new Arzt() ;
		$myArztAdr->setId( $_id) ;
		$ret	.=	$myArztAdr->getXMLF( "LiefArzt") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getArztRechArztAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myArztAdr	=	new Arzt() ;
		$myArztAdr->setId( $_id) ;
		$ret	.=	$myArztAdr->getXMLF( "RechArzt") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getArztAddArztAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myArztAdr	=	new Arzt() ;
		$myArztAdr->setId( $_id) ;
		$ret	.=	$myArztAdr->getXMLF( "AddArzt") ;
		return $ret ;
	}
	/**
	 *
	 */
	function	_addDepArzt( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->ArztNr) ;
		$this->ArztNr	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT ArztNr FROM Arzt WHERE ArztNr LIKE '%s-$_pref%%' ORDER BY ArztNr DESC LIMIT 0, 1 ", $this->ArztNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myArztDepAdr	=	new Arzt() ;
			if ( $numrows == 0) {
				$myArztDepAdr->ArztNr	=	$this->ArztNr . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myArztDepAdr->ArztNr	=	sprintf( "%s-$_pref%03d", $this->ArztNr, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myArztDepAdr->storeInDb() ;
			$myArztDepAdr->getFromPostL() ;
			$myArztDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myArztDepAdr->ArztNr ;
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
			$myObj	=	new FDbObject( "Arzt", "ArztNr", "cloud", "v_ArztSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"Bezeichnung like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"ArztAdresse"	:
			$myObj	=	new FDbObject( "ArztAdresse", "Id", "def", "v_ArztSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( ArztNr = '" . $this->ArztNr . "') " ;
			$filter2	=	"( Name like '%" . $sCrit . "%' OR Vorname  like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["ArztNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}
?>
