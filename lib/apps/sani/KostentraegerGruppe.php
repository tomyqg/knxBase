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
 * KostentraegerGruppe - Base Class
 *
 * @package Application
 * @subpackage KostentraegerGruppe
 */
class	KostentraegerGruppe	extends	AppObject	{
	public	$Rights	=	0x00000000 ;

	/**
	 *
	 */
	function	__construct( $_myKTGruppeNr="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myKTGruppeNr')") ;
		parent::__construct( "KostentraegerGruppe", "KTGruppeNr", "cloud") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myKTGruppeNr) > 0) {
			try {
				$this->setKTGruppeNr( $_myKTGruppeNr) ;
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
	function	setKTGruppeNr( $_myKTGruppeNr) {
		$this->KTGruppeNr	=	$_myKTGruppeNr ;
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
			$myKostentraegerGruppe	=	new KostentraegerGruppe() ;
			$this->getFromPostL() ;
			$this->KTGruppeNr	=	$_POST[ 'KTGruppeNr'] ;
			$this->storeInDb() ;
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
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		if ( $_val != "") {
			$this->_updDep( $_key, $_id, $_val, $reply) ;
		} else {
			$this->_upd( $_key, $_id, $_val, $_reply) ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "KostentraegerGruppe.php", "KostentraegerGruppe", "_upd()") ;
		parent::_upd( $_key, $_id, $_val, $_reply) ;
//		$this->_addRem( FTr::tr( "KostentraegerGruppe updated")) ;
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
	function	_addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "KostentraegerGruppeKostentraeger") {
			$myKostentraegerGruppeKostentraeger	=	new KostentraegerGruppeKostentraeger() ;
			$myKostentraegerGruppeKostentraeger->KTGruppeNr	=	$this->KTGruppeNr ;
			$myKostentraegerGruppeKostentraeger->getFromPostL() ;
			$myKostentraegerGruppeKostentraeger->storeInDb() ;
			$reply->message	=	FTr::tr( "Kostentraeger succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		}
		FDbg::end() ;
		return $reply ;
	}

	/**
	 * buche
	 */
	function	_updDep( $_key="", $_id=-1, $_val="", $reply=null) {
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
			parent::_updDep( $_key, $_id, $_val, $reply) ;
			$reply->message	=	FTr::tr( "Kostentraeger succesfully updated!") ;
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
	function	newKostentraegerGruppe( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "KostentraegerGruppe.php::KostentraegerGruppe::newKostentraegerGruppe( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  KTGruppeNr >= '$_nsStart' AND KTGruppeNr <= '$_nsEnd' " .
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
	 *	Funktion:	identify KostentraegerGruppe
	 *
	 *	Versucht irgendwie den KostentraegerGruppen per Password zu identifizieren
	 *	Zuerst wird versucht, den KostentraegerGruppen per KostentraegerGruppenNr zu finden.
	 *	Falls keine KostentraegerGruppennumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn KostentraegerGruppendaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */
	/**
	 * create a new 'KostentraegerGruppe' from a form sending POST data
	 */
	function	newKostentraegerGruppeFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new KostentraegerGruppeAdresse() ;
		$myCuContact->getFromPost() ;
		if ( $this->KostentraegerGruppeName1 == "") {
			$this->KostentraegerGruppeName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->KostentraegerGruppeName1) < 8) {
			self::$err['_IKostentraegerGruppeName1']	=	true ;
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
			$this->newKostentraegerGruppe( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->KTGruppeNr	=	$this->KTGruppeNr ;
			$myCuContact->newKostentraegerGruppeAdresse() ;
		}
	}

	/**
     *
     */
    function    fetchForNew() {
        $this->KostentraegerGruppeName1   =   $_POST['_IKostentraegerGruppeName1'] ;
        $this->KostentraegerGruppeName2   =   $_POST['_IKostentraegerGruppeName2'] ;
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
		case	"KostentraegerGruppeKostentraeger"	:
			$myKostentraegerGruppeKostentraeger	=	new KostentraegerGruppeKostentraeger() ;
			if ( $_id == -1) {
				$myKostentraegerGruppeKostentraeger->Id	=	-1 ;
			} else {
				$myKostentraegerGruppeKostentraeger->setId( $_id) ;
			}
//			$reply	=	parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			$reply	=	$myKostentraegerGruppeKostentraeger->getAsXML( $_key, $_id, $_val, $reply) ;
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
	function	getKostentraegerGruppeAdresseAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myKostentraegerGruppeAdresse	=	new KostentraegerGruppeAdresse() ;
		$myKostentraegerGruppeAdresse->setId( $_id) ;
		$ret	.=	$myKostentraegerGruppeAdresse->getXMLF() ;
		return $ret ;
	}

	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getKostentraegerGruppeLiefKostentraegerGruppeAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myKostentraegerGruppeAdr	=	new KostentraegerGruppe() ;
		$myKostentraegerGruppeAdr->setId( $_id) ;
		$ret	.=	$myKostentraegerGruppeAdr->getXMLF( "LiefKostentraegerGruppe") ;
		return $ret ;
	}

	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getKostentraegerGruppeRechKostentraegerGruppeAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myKostentraegerGruppeAdr	=	new KostentraegerGruppe() ;
		$myKostentraegerGruppeAdr->setId( $_id) ;
		$ret	.=	$myKostentraegerGruppeAdr->getXMLF( "RechKostentraegerGruppe") ;
		return $ret ;
	}

	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getKostentraegerGruppeAddKostentraegerGruppeAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;

		/**
		 *
		 */
		$myKostentraegerGruppeAdr	=	new KostentraegerGruppe() ;
		$myKostentraegerGruppeAdr->setId( $_id) ;
		$ret	.=	$myKostentraegerGruppeAdr->getXMLF( "AddKostentraegerGruppe") ;
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
	function	_addDepKostentraegerGruppe( $_pref) {
		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->KTGruppeNr) ;
		$this->KTGruppeNr	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT KTGruppeNr FROM KostentraegerGruppe WHERE KTGruppeNr LIKE '%s-$_pref%%' ORDER BY KTGruppeNr DESC LIMIT 0, 1 ", $this->KTGruppeNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myKostentraegerGruppeDepAdr	=	new KostentraegerGruppe() ;
			if ( $numrows == 0) {
				$myKostentraegerGruppeDepAdr->KTGruppeNr	=	$this->KTGruppeNr . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myKostentraegerGruppeDepAdr->KTGruppeNr	=	sprintf( "%s-$_pref%03d", $this->KTGruppeNr, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myKostentraegerGruppeDepAdr->storeInDb() ;
			$myKostentraegerGruppeDepAdr->getFromPostL() ;
			$myKostentraegerGruppeDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myKostentraegerGruppeDepAdr->KTGruppeNr ;
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
		case	"KostentraegerGruppe"	:
			$myObj	=	new FDbObject( "KostentraegerGruppe", "KTGruppeNr", "cloud", "v_KostentraegerGruppeSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"KTGruppeNr like '%" . $sCrit . "%' OR Name like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KostentraegerGruppeKostentraeger"	:
			$myObj	=	new FDbObject( "KostentraegerGruppeKostentraeger", "Id", "cloud", "v_KostentraegerGruppeKostentraegerSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( KTGruppeNr = '" . $this->KTGruppeNr . "') " ;
			$filter2	=	"" ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["KTGruppeNr"]) ;
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
		$myObj	=	new FDbObject( "KostentraegerGruppeGKVList", "Id", "cloud", "v_KostentraegerGruppeGKVListSurvey") ;				// no specific object we need here
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
		$myKostentraegerGruppe	=	new KostentraegerGruppe() ;
		$myKostentraegerGruppe->setIterCond( "KTGruppeNr like '%" . $sCrit . "%' OR Name1 like '%" . $sCrit . "%' ") ;
		$il0	=	0 ;
		foreach ( $myKostentraegerGruppe as $institution) {
			if ( $il0 < 50) {
				$a_json_row["id"]		=	$institution->Id ;
				$a_json_row["value"]	=	$institution->KTGruppeNr ;
				$a_json_row["label"]	=	$institution->KTGruppeNr . ", " . $institution->Name1 ;
				$a_json_row["iKNr"]		=	$institution->KTGruppeNr ;
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
