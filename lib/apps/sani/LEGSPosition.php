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
 * LEGSPosition - Base Class
 *
 * @package Application
 * @subpackage LEGSPosition
 */
class	LEGSPosition	extends	AppObject	{
	public	$Rights	=	0x00000000 ;
	/**
	 *
	 */
	function	__construct( $_myHMVNr="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myHMVNr')") ;
		parent::__construct( "LEGSPosition", "HMVNr", "cloud") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myHMVNr) > 0) {
			try {
				$this->setHMVNr( $_myHMVNr) ;
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
	function	setHMVNr( $_myHMVNr) {
		$this->HMVNr	=	$_myHMVNr ;
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
		$myLEGSPosition	=	new LEGSPosition() ;
		if ( $myLEGSPosition->first( "IKNr DESC", "LENGTH(IKNr) = 9")) {
			$this->getFromPostL() ;
			$this->IKNr	=	sprintf( "%08d", intval( $myLEGSPosition->IKNr) + 1) ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], LEGSPosition invalid after creation!'") ;
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
		FDbg::begin( 1, "LEGSPosition.php", "LEGSPosition", "_upd()") ;
		parent::upd( $_key, $_id, $_val, $_replyl) ;
		$this->_addRem( FTr::tr( "LEGSPosition updated")) ;
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
		if ( $objName == "LEGSPositionAdresse") {
			$myLEGSPositionAdresse	=	new LEGSPositionAdresse() ;
			$myLEGSPositionAdresse->IKNr	=	$this->IKNr ;
			$myLEGSPositionAdresse->newLEGSPositionAdresse() ;
			$myLEGSPositionAdresse->getFromPostL() ;
			$myLEGSPositionAdresse->updateInDb() ;
			$reply->message	=	FTr::tr( "Contact succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "LiefLEGSPosition") {
			$this->_addDepLEGSPosition( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefLEGSPosition", $reply) ;
		} else if ( $objName == "RechLEGSPosition") {
			$this->_addDepLEGSPosition( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechLEGSPosition", $reply) ;
		} else if ( $objName == "AddLEGSPosition") {
			$this->_addDepLEGSPosition( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddLEGSPosition", $reply) ;
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
		if ( $objName == "LEGSPositionAdresse") {
			$myLEGSPositionAdresse	=	new LEGSPositionAdresse() ;
			$myLEGSPositionAdresse->IKNr	=	$this->IKNr ;
			$myLEGSPositionAdresse->newLEGSPositionAdresse() ;
			$myLEGSPositionAdresse->getFromPostL() ;
			$myLEGSPositionAdresse->updateInDb() ;
			return $myLEGSPositionAdresse->LEGSPositionAdresseNo ;
		} else if ( $objName == "LiefLEGSPosition") {
			return $this->_addDepLEGSPosition( "L") ;
		} else if ( $objName == "RechLEGSPosition") {
			return $this->_addDepLEGSPosition( "R") ;
		} else if ( $objName == "AddLEGSPosition") {
			return $this->_addDepLEGSPosition( "A") ;
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
	function	newLEGSPosition( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "LEGSPosition.php::LEGSPosition::newLEGSPosition( $_nsStart, $_nsEnd):") ;
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
	 *	Funktion:	identify LEGSPosition
	 *
	 *	Versucht irgendwie den LEGSPositionn per Password zu identifizieren
	 *	Zuerst wird versucht, den LEGSPositionn per LEGSPositionnNr zu finden.
	 *	Falls keine LEGSPositionnnumemr existiert koennte es die E-Mail Adresse sein.
	 *	Wenn LEGSPositionndaten existieren, dann muss geprueft werden, ob das Password
	 *	stimmt.
	 */

	/**
	 * create a new 'LEGSPosition' from a form sending POST data
	 */
	function	newLEGSPositionFromPOST() {
		$this->getFromPost() ;
		$myCuContact	=	new LEGSPositionAdresse() ;
		$myCuContact->getFromPost() ;
		if ( $this->LEGSPositionName1 == "") {
			$this->LEGSPositionName1	=	$myCuContact->FirstName . " " . $myCuContact->Name ;
		}
		/**
		 *
		 */
		if ( strlen( $this->LEGSPositionName1) < 8) {
			self::$err['_ILEGSPositionName1']	=	true ;
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
			$this->newLEGSPosition( "900000", "999999") ;
			$this->getPassword() ;
			$myCuContact->IKNr	=	$this->IKNr ;
			$myCuContact->newLEGSPositionAdresse() ;
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
		case	"LiefLEGSPosition"	:
			$myLEGSPosition	=	new LEGSPosition() ;
			if ( $_id == -1) {
			} else {
				$myLEGSPosition->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "LEGSPosition>", "LiefLEGSPosition>", $myLEGSPosition->getAsXML()) ;
			break ;
		case	"RechLEGSPosition"	:
			$myLEGSPosition	=	new LEGSPosition() ;
			if ( $_id == -1) {
			} else {
				$myLEGSPosition->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "LEGSPosition>", "RechLEGSPosition>", $myLEGSPosition->getAsXML()) ;
			break ;
		case	"AddLEGSPosition"	:
			$myLEGSPosition	=	new LEGSPosition() ;
			if ( $_id == -1) {
			} else {
				$myLEGSPosition->setId( $_id) ;
			}
			$reply->replyData	=	str_replace( "LEGSPosition>", "AddLEGSPosition>", $myLEGSPosition->getAsXML()) ;
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
		case	""	:
			$myObj	=	new FDbObject( "LEGSPosition", "Id", "cloud", "v_LEGSPositionSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"HMVNr like '{$sCrit}%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( [ "HMVNr", "LEGS", "LKZ"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}
?>
