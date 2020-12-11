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
 * HMV_AO - Base Class
 *
 * @package Application
 * @subpackage HMV_AO
 */
class	HMV_AO	extends	AppObject	{
	public	$Rights	=	0x00000000 ;
	/**
	 *
	 */
	function	__construct( $_myAnwendungsort="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myAnwendungsort')") ;
		parent::__construct( "HMV_AO", "Anwendungsort", "cloud") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myAnwendungsort) > 0) {
			try {
				$this->setAnwendungsort( $_myAnwendungsort) ;
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
	function	setAnwendungsort( $_myAnwendungsort) {
		$this->Anwendungsort	=	$_myAnwendungsort ;
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
		$myHMV_AO	=	new HMV_AO() ;
		if ( $myHMV_AO->first( "IKNr DESC", "LENGTH(IKNr) = 9")) {
			$this->getFromPostL() ;
			$this->IKNr	=	sprintf( "%08d", intval( $myHMV_AO->IKNr) + 1) ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], HMV_AO invalid after creation!'") ;
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
		FDbg::begin( 1, "HMV_AO.php", "HMV_AO", "_upd()") ;
		parent::upd( $_key, $_id, $_val, $_replyl) ;
		$this->_addRem( FTr::tr( "HMV_AO updated")) ;
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
		if ( $objName == "HMV_AOAdresse") {
			$myHMV_AOAdresse	=	new HMV_AOAdresse() ;
			$myHMV_AOAdresse->IKNr	=	$this->IKNr ;
			$myHMV_AOAdresse->newHMV_AOAdresse() ;
			$myHMV_AOAdresse->getFromPostL() ;
			$myHMV_AOAdresse->updateInDb() ;
			$reply->message	=	FTr::tr( "Contact succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "LiefHMV_AO") {
			$this->_addDepHMV_AO( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefHMV_AO", $reply) ;
		} else if ( $objName == "RechHMV_AO") {
			$this->_addDepHMV_AO( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechHMV_AO", $reply) ;
		} else if ( $objName == "AddHMV_AO") {
			$this->_addDepHMV_AO( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddHMV_AO", $reply) ;
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
		if ( $objName == "HMV_AOAdresse") {
			$myHMV_AOAdresse	=	new HMV_AOAdresse() ;
			$myHMV_AOAdresse->IKNr	=	$this->IKNr ;
			$myHMV_AOAdresse->newHMV_AOAdresse() ;
			$myHMV_AOAdresse->getFromPostL() ;
			$myHMV_AOAdresse->updateInDb() ;
			return $myHMV_AOAdresse->HMV_AOAdresseNo ;
		} else if ( $objName == "LiefHMV_AO") {
			return $this->_addDepHMV_AO( "L") ;
		} else if ( $objName == "RechHMV_AO") {
			return $this->_addDepHMV_AO( "R") ;
		} else if ( $objName == "AddHMV_AO") {
			return $this->_addDepHMV_AO( "A") ;
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
	function	newHMV_AO( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "HMV_AO.php::HMV_AO::newHMV_AO( $_nsStart, $_nsEnd):") ;
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
	 *	Funktion:	identify HMV_AO
	 *
	 *	Versucht irgendwie den HMV_AOn per Password zu identifizieren
	 *	Zuerst wird versucht, den HMV_AOn per HMV_AOnNr zu finden.
	 *	Falls keine HMV_AOnnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn HMV_AOndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */

	/**
	 * create a new 'HMV_AO' from a form sending POST data
	 */
	function	newHMV_AOFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new HMV_AOAdresse() ;
		$myCuContact->getFromPost() ;
		if ( $this->HMV_AOName1 == "") {
			$this->HMV_AOName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->HMV_AOName1) < 8) {
			self::$err['_IHMV_AOName1']	=	true ;
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
			$this->newHMV_AO( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->IKNr	=	$this->IKNr ;
			$myCuContact->newHMV_AOAdresse() ;
		}
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
		default	:
			$reply	=	parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
		return $reply ;
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
		case	"HMV_AO"	:
			$myObj	=	new FDbObject( "HMV_AO", "IKNr", "cloud", "v_HMV_AOSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"" ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$order1	=	"Anwendungsort" ;
			$myQuery->addOrder( [ $order1]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}
?>
