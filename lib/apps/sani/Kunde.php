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
 * Kunde - Base Class
 *
 * @package Application
 * @subpackage Kunde
 */
class	Kunde	extends	AppObject	{
	public	$Rights	=	0x00000000 ;
	public	$UserId	=	"*" ;
	public	$Remark	=	"" ;
	public	$KV1Name ;
	public	$KV2Name ;
	public	$KV3Name ;
	public	$BGName ;

	/**
	 *
	 */
	public	$NameKomplett ;

	/**
	 *
	 */
	function	__construct( $_myKundeNr="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myKundeNr')") ;
		parent::__construct( "Kunde", "ERPNr") ;
		$this->Rights	=	0x00000001 ;
		$this->addCol( "KV1Name", "char") ;
		$this->addCol( "KV2Name", "char") ;
		$this->addCol( "KV3Name", "char") ;
		$this->addCol( "BGName", "char") ;
		if ( strlen( $_myKundeNr) > 0) {
			try {
				$this->setKundeNr( $_myKundeNr) ;
				$this->actKundeAdresse	=	new KundeAdresse() ;
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
	function	setKundeNr( $_myKundeNr) {
		$this->KundeNr	=	$_myKundeNr ;
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
		if ( $_val != "") {
			$this->_addDep( $_key, $_id, $_val, $reply) ;
		} else {
			$myKunde	=	new Kunde() ;
			if ( $myKunde->first( "LENGTH(ERPNr) = 12", "ERPNr DESC")) {
				$this->getFromPostL() ;
				$this->ERPNr	=	sprintf( "%012d", intval( $myKunde->ERPNr) + 1) ;
				$this->storeInDb() ;
			} else {
				$this->getFromPostL() ;
				$this->ERPNr	=	sprintf( "%012d", intval( $myKunde->ERPNr) + 1) ;
				$this->storeInDb() ;
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"object[".$this->cacheName."], Kunde invalid after creation!'") ;
			}
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
		if ( $_val != "") {
			$this->_updDep( $_key, $_id, $_val, $reply) ;
		} else {
			$this->_upd( $_key, $_id, $_val, $_reply) ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "Kunde.php", "Kunde", "_upd()") ;
		parent::_upd( $_key, $_id, $_val, $_reply) ;
		$this->_addRem( FTr::tr( "Kunde updated " . ( $this->lastUpdateList != "" ? "\n" : "") . str_replace( ",", "\n", $this->lastUpdateList) . "")) ;
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
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="", $reply = NULL) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "KundeAdresse") {
			$myKundeAdresse	=	new KundeAdresse() ;
			$myKundeAdresse->getFromPostL() ;
			$myKundeAdresse->ERPNr	=	$this->ERPNr ;
			$myKundeAdresse->KundeNr	=	$this->KundeNr ;
			$myKundeAdresse->storeInDb() ;
			$reply->message	=	FTr::tr( "Kundenadresse hinzugefügt!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "KundeBefreiung") {
			$myKundeBefreiung	=	new KundeBefreiung() ;
			$myKundeBefreiung->getFromPostL() ;
			$myKundeBefreiung->ERPNr	=	$this->ERPNr ;
			$myKundeBefreiung->storeInDb() ;
			$reply->message	=	FTr::tr( "Befreiungszeitraum hinzugefügt!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "KundeNotiz") {
			$myKundeNotiz	=	new KundeNotiz() ;
			$myKundeNotiz->getFromPostL() ;
			$myKundeNotiz->ERPNr	=	$this->ERPNr ;
			$myKundeNotiz->KundeNr	=	$this->KundeNr ;
			$myKundeNotiz->storeInDb() ;
			$reply->message	=	FTr::tr( "Kundennotiz hinzugefügt!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
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
	function	newKunde( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Kunde.php::Kunde::newKunde( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  KundeNr >= '$_nsStart' AND KundeNr <= '$_nsEnd' " .
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
	 *
	 */
	function	updatePassword( $_pwd, $_pwdVerify) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."atePassword( '*')") ;
		if ( $_pwd == $_pwdVerify) {
			$this->Password	=	md5( $_pwd) ;
			$this->updateInDb() ;
			FDb::query( "UPDATE Kunde SET Password = '" . $this->Password . "' WHERE KundeNr = '" . $this->KundeNr . "' ") ;
			FDb::query( "UPDATE KundeAdresse SET Password = '" . $this->Password . "' WHERE KundeNr = '" . $this->KundeNr . "' ") ;
		}
		FDbg::end( 1, basename( __FILE__), __CLASS__, __METHOD__."atePassword( '*')") ;
	}

	/**
     *
     */
    function    fetchForNew() {
        $this->KundeName1   =   $_POST['_IKundeName1'] ;
        $this->KundeName2   =   $_POST['_IKundeName2'] ;
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
		case	"KundeAdresse"	:
			$myKundeAdresse	=	new KundeAdresse() ;
			if ( $_id == -1) {
				$myKundeAdresse->Id	=	-1 ;
				$myKundeAdresse->ERPNr	=	$this->ERPNr ;
				$myKundeAdresse->KundeNr	=	$this->KundeNr ;
			} else {
				$myKundeAdresse->setId( $_id) ;
			}
			$reply	=	$myKundeAdresse->getAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		case	"KundeBefreiung"	:
			$myKundeBefreiung	=	new KundeBefreiung() ;
			if ( $_id == -1) {
				$myKundeBefreiung->Id	=	-1 ;
				$myKundeBefreiung->ERPNr	=	$this->ERPNr ;
			} else {
				$myKundeBefreiung->setId( $_id) ;
			}
			$reply	=	$myKundeBefreiung->getAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		case	"KundeNotiz"	:
			$myKundeNotiz	=	new KundeNotiz() ;
			if ( $_id == -1) {
				$myKundeNotiz->Id	=	-1 ;
				$myKundeNotiz->ERPNr	=	$this->ERPNr ;
				$myKundeNotiz->KundeNr	=	$this->KundeNr ;
			} else {
				$myKundeNotiz->setId( $_id) ;
			}
			$reply	=	$myKundeNotiz->getAsXML( $_key, $_id, $_val, $reply) ;
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
	function	getKundeAdresseAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myKundeAdresse	=	new KundeAdresse() ;
		$myKundeAdresse->setId( $_id) ;
		$ret	.=	$myKundeAdresse->getXMLF() ;
		return $ret ;
	}

	/**
	 *
	 */
	function	_addDepKunde( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->KundeNr) ;
		$this->KundeNr	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT KundeNr FROM Kunde WHERE KundeNr LIKE '%s-$_pref%%' ORDER BY KundeNr DESC LIMIT 0, 1 ", $this->KundeNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myKundeDepAdr	=	new Kunde() ;
			if ( $numrows == 0) {
				$myKundeDepAdr->KundeNr	=	$this->KundeNr . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myKundeDepAdr->KundeNr	=	sprintf( "%s-$_pref%03d", $this->KundeNr, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myKundeDepAdr->storeInDb() ;
			$myKundeDepAdr->getFromPostL() ;
			$myKundeDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myKundeDepAdr->KundeNr ;
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
		case	"Kunde"	:
			$myObj	=	new FDbObject( "Kunde", "KundeNr", "def", "v_KundeSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"Name like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KundeAdresse"	:
			$myObj	=	new FDbObject( "KundeAdresse", "Id", "def", "v_KundeAdresseSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( ERPNr = '" . $this->ERPNr . "') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( ["KundeAdresseNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KundeBefreiung"	:
			$myObj	=	new FDbObject( "KundeBefreiung", "Id", "def", "v_KundeBefreiungSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( ERPNr = '" . $this->ERPNr . "') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( ["DatumBefreiungVon"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KundeNotiz"	:
			$myObj	=	new FDbObject( "KundeNotiz", "Id", "def", "v_KundeNotizSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( ERPNr = '" . $this->ERPNr . "') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( ["KundeNotizNr"]) ;
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
		$myKunde	=	new Kunde() ;
		$myKunde->setIterCond( "KundeNr like '%" . $sCrit . "%' OR Name1 like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $myKunde as $kunde) {
			if ( $il0 < 50) {
				switch ( $_val) {
				case	2	:
					$a_json_row["id"]		=	$kunde->Id ;
					$a_json_row["value"]	=	$kunde->KundeNr . "\n"
											.	$kunde->Vorname . " " . $kunde->Name1 . "\n"
											.	$kunde->Strasse . " " . $kunde->Hausnummer . "\n"
											.	$kunde->PLZ . " " . $kunde->Ort ;
					array_push( $a_json, $a_json_row);
					break ;
				default	:
					$a_json_row["id"]		=	$kunde->Id ;
					$a_json_row["value"]	=	$kunde->KundeNr ;
					$a_json_row["label"]	=	$kunde->KundeNr . ", " . $kunde->Name1 ;
					$a_json_row["kundeNr"]	=	$kunde->KundeNr ;
					$a_json_row["Name1"]	=	$kunde->Name1 ;
					array_push( $a_json, $a_json_row);
					break ;
				}
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
		$this->addCol( "NameKomplett", "char") ;
		$this->NameKomplett	=	$this->Vorname . " " . $this->Name1 ;
		$myKostentraeger	=	new Kostentraeger() ;
		if ( $this->Vers1IKNr != "") {
			if ( $myKostentraeger->setKey(  $this->Vers1IKNr)) {
				$this->KV1Name	=	$myKostentraeger->IKNr . ", " . $myKostentraeger->Name1 ;
			}
		}
		if ( $this->Vers2IKNr != "") {
			if ( $myKostentraeger->setKey(  $this->Vers2IKNr)) {
				$this->KV2Name	=	$myKostentraeger->IKNr . ", " . $myKostentraeger->Name1 ;
			}
		}
		FDbg::end() ;
	}
}
?>
