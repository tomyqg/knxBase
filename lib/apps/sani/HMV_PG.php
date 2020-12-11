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
 * HMV_PG - Base Class
 *
 * @package Application
 * @subpackage HMV_PG
 */
class	HMV_PG	extends	AppObject	{
	public	$Rights	=	0x00000000 ;
	/**
	 *
	 */
	function	__construct( $_myProduktGruppe="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myProduktGruppe')") ;
		parent::__construct( "HMV_PG", "ProduktGruppe", "cloud") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myProduktGruppe) > 0) {
			try {
				$this->setProduktGruppe( $_myProduktGruppe) ;
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
	function	setProduktGruppe( $_myProduktGruppe) {
		$this->ProduktGruppe	=	$_myProduktGruppe ;
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
		$myHMV_PG	=	new HMV_PG() ;
		if ( $myHMV_PG->first( "IKNr DESC", "LENGTH(IKNr) = 9")) {
			$this->getFromPostL() ;
			$this->IKNr	=	sprintf( "%08d", intval( $myHMV_PG->IKNr) + 1) ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], HMV_PG invalid after creation!'") ;
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
		FDbg::begin( 1, "HMV_PG.php", "HMV_PG", "_upd()") ;
		parent::upd( $_key, $_id, $_val, $_replyl) ;
		$this->_addRem( FTr::tr( "HMV_PG updated")) ;
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
		if ( $objName == "HMV_PGAdresse") {
			$myHMV_PGAdresse	=	new HMV_PGAdresse() ;
			$myHMV_PGAdresse->IKNr	=	$this->IKNr ;
			$myHMV_PGAdresse->newHMV_PGAdresse() ;
			$myHMV_PGAdresse->getFromPostL() ;
			$myHMV_PGAdresse->updateInDb() ;
			$reply->message	=	FTr::tr( "Contact succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "LiefHMV_PG") {
			$this->_addDepHMV_PG( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefHMV_PG", $reply) ;
		} else if ( $objName == "RechHMV_PG") {
			$this->_addDepHMV_PG( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechHMV_PG", $reply) ;
		} else if ( $objName == "AddHMV_PG") {
			$this->_addDepHMV_PG( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddHMV_PG", $reply) ;
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
		if ( $objName == "HMV_PGAdresse") {
			$myHMV_PGAdresse	=	new HMV_PGAdresse() ;
			$myHMV_PGAdresse->IKNr	=	$this->IKNr ;
			$myHMV_PGAdresse->newHMV_PGAdresse() ;
			$myHMV_PGAdresse->getFromPostL() ;
			$myHMV_PGAdresse->updateInDb() ;
			return $myHMV_PGAdresse->HMV_PGAdresseNo ;
		} else if ( $objName == "LiefHMV_PG") {
			return $this->_addDepHMV_PG( "L") ;
		} else if ( $objName == "RechHMV_PG") {
			return $this->_addDepHMV_PG( "R") ;
		} else if ( $objName == "AddHMV_PG") {
			return $this->_addDepHMV_PG( "A") ;
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
	function	newHMV_PG( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "HMV_PG.php::HMV_PG::newHMV_PG( $_nsStart, $_nsEnd):") ;
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
	 *	Funktion:	identify HMV_PG
	 *
	 *	Versucht irgendwie den HMV_PGn per Password zu identifizieren
	 *	Zuerst wird versucht, den HMV_PGn per HMV_PGnNr zu finden.
	 *	Falls keine HMV_PGnnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn HMV_PGndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */

	/**
	 * create a new 'HMV_PG' from a form sending POST data
	 */
	function	newHMV_PGFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new HMV_PGAdresse() ;
		$myCuContact->getFromPost() ;
		if ( $this->HMV_PGName1 == "") {
			$this->HMV_PGName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->HMV_PGName1) < 8) {
			self::$err['_IHMV_PGName1']	=	true ;
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
			$this->newHMV_PG( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->IKNr	=	$this->IKNr ;
			$myCuContact->newHMV_PGAdresse() ;
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
		case	"LiefHMV_PG"	:
			$myHMV_PG	=	new HMV_PG() ;
			if ( $_id == -1) {
			} else {
				$myHMV_PG->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "HMV_PG>", "LiefHMV_PG>", $myHMV_PG->getAsXML()) ;
			break ;
		case	"RechHMV_PG"	:
			$myHMV_PG	=	new HMV_PG() ;
			if ( $_id == -1) {
			} else {
				$myHMV_PG->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "HMV_PG>", "RechHMV_PG>", $myHMV_PG->getAsXML()) ;
			break ;
		case	"AddHMV_PG"	:
			$myHMV_PG	=	new HMV_PG() ;
			if ( $_id == -1) {
			} else {
				$myHMV_PG->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "HMV_PG>", "AddHMV_PG>", $myHMV_PG->getAsXML()) ;
			break ;
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
		case	"HMV_PG"	:
			$myObj	=	new FDbObject( "HMV_PG", "IKNr", "cloud", "v_HMV_PGSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"" ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$order1	=	"ProduktGruppe" ;
			$myQuery->addOrder( [ $order1]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"HMV_UG"	:
			$myObj	=	new FDbObject( "HMV_UG", "Id", "cloud", "v_HMV_UGSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( HMVNr like '{$this->ProduktGruppe}%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( ["HMVNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}
?>
