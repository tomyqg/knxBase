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
 * Artikel - Base Class
 *
 * @package Application
 * @subpackage Artikel
 */
class	Artikel	extends	AppObject	{
	public	$Rights	=	0x00000000 ;
	/**
	 *
	 */
	function	__construct( $_myArtikelNr="", $_className="Artikel", $_keyCol="ERPNr", $_db="def") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myArtikelNr')") ;
		parent::__construct( $_className, $_keyCol, $_db) ;
		$this->Rights	=	0x00000001 ;
		if ( $_myArtikelNr != "") {
			try {
				$this->setArtikelNr( $_myArtikelNr) ;
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
	function	setKey( $_myArtikelNr) {
		parent::setKey( $_myArtikelNr) ;
//		if ( ! $this->_valid) {
//			$cloudArtikel	=	new CloudArtikel( $_myArtikelNr) ;
//			$this->copyFrom( $cloudArtikel) ;
//		}
		return $this->_valid ;
	}

	/**
	 *
	 */
	function	setArtikelNr( $_myArtikelNr) {
		$this->fetchFromDbWhere( array( "ArtikelNr = '{$_myArtikelNr}'")) ;
		if ( ! $this->_valid) {
			$cloudArtikel	=	new CloudArtikel( $_myArtikelNr) ;
			$this->copyFrom( $cloudArtikel) ;
		}
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
		$myArtikel	=	new Artikel() ;
		if ( $myArtikel->first( "LENGTH(ERPNr) = 12", "ERPNr DESC")) {
			$this->getFromPostL() ;
			$this->ERPNr	=	sprintf( "%012d", intval( $myArtikel->ERPNr) + 1) ;
			$this->MwstTyp	=	1 ;
			$this->MwstSatz	=	19.0 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			$this->getFromPostL() ;
			$this->ERPNr	=	sprintf( "%012d", intval( $myArtikel->ERPNr) + 1) ;
			$this->MwstTyp	=	1 ;
			$this->MwstSatz	=	19.0 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], Artikel invalid after creation!'") ;
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
		FDbg::begin( 1, "Artikel.php", "Artikel", "_upd()") ;
		parent::upd( $_key, $_id, $_val, $_replyl) ;
		$this->_addRem( FTr::tr( "Article updated")) ;
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
		if ( $objName == "ArtikelAdresse") {
			$myArtikelAdresse	=	new ArtikelAdresse() ;
			$myArtikelAdresse->ArtikelNr	=	$this->ArtikelNr ;
			$myArtikelAdresse->newArtikelAdresse() ;
			$myArtikelAdresse->getFromPostL() ;
			$myArtikelAdresse->updateInDb() ;
			$reply->message	=	FTr::tr( "Contact succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "LiefArtikel") {
			$this->_addDepArtikel( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefArtikel", $reply) ;
		} else if ( $objName == "RechArtikel") {
			$this->_addDepArtikel( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechArtikel", $reply) ;
		} else if ( $objName == "AddArtikel") {
			$this->_addDepArtikel( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddArtikel", $reply) ;
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
	function	_addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		$objName	=	$_val ;
		if ( $objName == "ArtikelAdresse") {
			$myArtikelAdresse	=	new ArtikelAdresse() ;
			$myArtikelAdresse->ArtikelNr	=	$this->ArtikelNr ;
			$myArtikelAdresse->newArtikelAdresse() ;
			$myArtikelAdresse->getFromPostL() ;
			$myArtikelAdresse->updateInDb() ;
			return $myArtikelAdresse->ArtikelAdresseNo ;
		} else if ( $objName == "LiefArtikel") {
			return $this->_addDepArtikel( "L") ;
		} else if ( $objName == "RechArtikel") {
			return $this->_addDepArtikel( "R") ;
		} else if ( $objName == "AddArtikel") {
			return $this->_addDepArtikel( "A") ;
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
	function	newArtikel( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Artikel.php::Artikel::newArtikel( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  ArtikelNr >= '$_nsStart' AND ArtikelNr <= '$_nsEnd' " .
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
	 *	Funktion:	identify Artikel
	 *
	 *	Versucht irgendwie den Artikeln per Password zu identifizieren
	 *	Zuerst wird versucht, den Artikeln per ArtikelnNr zu finden.
	 *	Falls keine Artikelnnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn Artikelndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */
	/**
	 * create a new 'Artikel' from a form sending POST data
	 */
	function	newArtikelFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new ArtikelAdresse() ;
		$myCuContact->getFromPost() ;
		if ( $this->ArtikelName1 == "") {
			$this->ArtikelName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->ArtikelName1) < 8) {
			self::$err['_IArtikelName1']	=	true ;
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
			$this->newArtikel( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->ArtikelNr	=	$this->ArtikelNr ;
			$myCuContact->newArtikelAdresse() ;
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
			FDb::query( "UPDATE Artikel SET Password = '" . $this->Password . "' WHERE ArtikelNr = '" . $this->ArtikelNr . "' ") ;
			FDb::query( "UPDATE ArtikelAdresse SET Password = '" . $this->Password . "' WHERE ArtikelNr = '" . $this->ArtikelNr . "' ") ;
		}
		FDbg::end( 1, basename( __FILE__), __CLASS__, __METHOD__."atePassword( '*')") ;
	}

  /**
     *
     */
    function    fetchForNew() {
        $this->ArtikelName1   =   $_POST['_IArtikelName1'] ;
        $this->ArtikelName2   =   $_POST['_IArtikelName2'] ;
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
		case	"LiefArtikel"	:
			$myArtikel	=	new Artikel() ;
			if ( $_id == -1) {
			} else {
				$myArtikel->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Artikel>", "LiefArtikel>", $myArtikel->getAsXML()) ;
			break ;
		case	"RechArtikel"	:
			$myArtikel	=	new Artikel() ;
			if ( $_id == -1) {
			} else {
				$myArtikel->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Artikel>", "RechArtikel>", $myArtikel->getAsXML()) ;
			break ;
		case	"AddArtikel"	:
			$myArtikel	=	new Artikel() ;
			if ( $_id == -1) {
			} else {
				$myArtikel->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "Artikel>", "AddArtikel>", $myArtikel->getAsXML()) ;
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
	function	getArtikelAdresseAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myArtikelAdresse	=	new ArtikelAdresse() ;
		$myArtikelAdresse->setId( $_id) ;
		$ret	.=	$myArtikelAdresse->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getArtikelLiefArtikelAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myArtikelAdr	=	new Artikel() ;
		$myArtikelAdr->setId( $_id) ;
		$ret	.=	$myArtikelAdr->getXMLF( "LiefArtikel") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getArtikelRechArtikelAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myArtikelAdr	=	new Artikel() ;
		$myArtikelAdr->setId( $_id) ;
		$ret	.=	$myArtikelAdr->getXMLF( "RechArtikel") ;
		return $ret ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getArtikelAddArtikelAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myArtikelAdr	=	new Artikel() ;
		$myArtikelAdr->setId( $_id) ;
		$ret	.=	$myArtikelAdr->getXMLF( "AddArtikel") ;
		return $ret ;
	}
	/**
	 *
	 */
	function	_addDepArtikel( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->ArtikelNr) ;
		$this->ArtikelNr	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT ArtikelNr FROM Artikel WHERE ArtikelNr LIKE '%s-$_pref%%' ORDER BY ArtikelNr DESC LIMIT 0, 1 ", $this->ArtikelNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myArtikelDepAdr	=	new Artikel() ;
			if ( $numrows == 0) {
				$myArtikelDepAdr->ArtikelNr	=	$this->ArtikelNr . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myArtikelDepAdr->ArtikelNr	=	sprintf( "%s-$_pref%03d", $this->ArtikelNr, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myArtikelDepAdr->storeInDb() ;
			$myArtikelDepAdr->getFromPostL() ;
			$myArtikelDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myArtikelDepAdr->ArtikelNr ;
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
		case	"Artikel"	:
			$myObj	=	new FDbObject( "Artikel", "ArtikelNr", "def", "v_ArtikelSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"ArtikelNr like '{$sCrit}%' OR Bezeichnung like '%{$sCrit}%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"ArtikelAdresse"	:
			$myObj	=	new FDbObject( "ArtikelAdresse", "Id", "def", "v_ArtikelSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( ArtikelNr = '" . $this->ArtikelNr . "') " ;
			$filter2	=	"( Name like '%" . $sCrit . "%' OR Vorname  like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["ArtikelNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
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
