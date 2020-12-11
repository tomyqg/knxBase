<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version	0.1
 * @package	Application
 * @filesource
 */
/**
 * Property - Base Class
 *
 * @package	Application
 * @subpackage	Property
 */
class	Property	extends	AppObjectImmo	{
	public	$LockState	=	0 ;
	/**
	 *
	 */
	function	__construct( $_myPropertyNo="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myPropertyNo')") ;
		parent::__construct( "Property", "PropertyNo") ;
		if ( strlen( $_myPropertyNo) > 0) {
			try {
				$this->setPropertyNo( $_myPropertyNo) ;
				$this->actPropertyContact	=	new PropertyContact() ;
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
	function	setPropertyNo( $_myPropertyNo) {
		$this->setKey( $_myPropertyNo) ;
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
		$myProperty	=	new Property() ;
		if ( $myProperty->first( "PropertyNo DESC", "LENGTH(PropertyNo) = 8")) {
			$this->getFromPostL() ;
			$this->PropertyNo	=	sprintf( "%08d", intval( $myProperty->PropertyNo) + 1) ;
//			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->storeInDb() ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], Property invalid after creation!'") ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
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
		return $this->getXMLString() ;
	}
	/**
	 *
	 */
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."(()") ;
		parent::upd( $_key, $_id, $_val, $_replyl) ;
		$this->_addRem( FTr::tr( "Property updated")) ;
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
		return $this->getXMLString() ;
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
		if ( $objName == "PropertyRentable") {
			$myPropertyRentable	=	new PropertyRentable() ;
			$myPropertyRentable->PropertyNo	=	$this->PropertyNo ;
			$myPropertyRentable->getFromPostL() ;
			$myPropertyRentable->storeInDb() ;
			$reply->message	=	FTr::tr( "Rrentable succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		} else if ( $objName == "LiefProperty") {
			$this->_addDepProperty( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefProperty", $reply) ;
		} else if ( $objName == "RechProperty") {
			$this->_addDepProperty( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechProperty", $reply) ;
		} else if ( $objName == "AddProperty") {
			$this->_addDepProperty( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddProperty", $reply) ;
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
		if ( $objName == "PropertyContact") {
			$myPropertyContact	=	new PropertyContact() ;
			$myPropertyContact->PropertyNo	=	$this->PropertyNo ;
			$myPropertyContact->newPropertyContact() ;
			$myPropertyContact->getFromPostL() ;
			$myPropertyContact->updateInDb() ;
			return $myPropertyContact->PropertyContactNo ;
		} else if ( $objName == "LiefProperty") {
			return $this->_addDepProperty( "L") ;
		} else if ( $objName == "RechProperty") {
			return $this->_addDepProperty( "R") ;
		} else if ( $objName == "AddProperty") {
			return $this->_addDepProperty( "A") ;
		}
	}
	/**
	 * updDep
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

	/**
	 *
	 */
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
	function	newProperty( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Property.php::Property::newProperty( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  PropertyNo >= '$_nsStart' AND PropertyNo <= '$_nsEnd' " .
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
	function    fetchForNew() {
		$this->PropertyName1   =   $_POST['_IPropertyName1'] ;
		$this->PropertyName2   =   $_POST['_IPropertyName2'] ;
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
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
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
			$myObj	=	new FDbObject( "Property", "PropertyNo", "def", "v_PropertySurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"PropertyName like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"PropertyRentable"	:
			$myObj	=	new FDbObject( "PropertyRentable", "PropertyNo", "def", "v_PropertyRentableSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"PropertyNo = " . $this->PropertyNo ;
			$filter2	=	"PropertyDescription like '%" . $sCrit . "%' " ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}
?>