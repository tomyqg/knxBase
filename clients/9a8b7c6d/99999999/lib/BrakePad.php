<?php
/**
 * BrakePad.php - Class definition
 *  Domain:
 *  	- administrative
 * 	BrakePad references:
 * 		- n/a
 *  BrakePad is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package BrakePadCalc
 */
/**
 * BrakePad - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package BrakePadCalc
 * @subpackage Classes
 */
class	BrakePad	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "BrakePad", "BrakePadId") ;
//		parent::setIdKey( "br_key") ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getXMLComplete()
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "BrakePad") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "BrakePad.php", "BrakePad", "getDepAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch ( $_val) {
			default	:
				if ( $_id >= 0) {
					parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
				} else {
					$newItem	=	new $_val ;
					$newItem->bremse_id	=	$this->br_id ;
//					$newItem->Domain	=	$this->__getUser()->Domain ;
					$newItem->getAsXML( $_key="", $_id=-1, $_val="", $reply) ;
				}
				break ;
		}
		return $reply ;
		FDbg::end() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "BrakePad.php", "BrakePad", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::addDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "BrakePad.php", "BrakePad", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "BrakePad.php", "BrakePad", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::delDep()
	 */
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "BrakePad.php", "BrakePad", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
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
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "BrakePad.php", "BrakePad", "getList( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$_POST['_step']	=	$_val ;
			$myObj	=	new FDbObject( "BrakePad", "Id", "def", "v_BrakePadSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["BrakePadId like '%" . $_POST['filterBrakePadId'] . "%' ", "Description like '%" . $_POST['filterDescription'] . "%' "]) ;
			$myQuery->addOrder( ["ManufacturerId", "BrakeTypeId", "Description"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Brake") ;
			break ;
		case	"BrakePadValue"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( $_POST['StartRow'], $_POST['RowCount'], $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
//			$myQuery->addField( ["Id","AxleUnitId","TrailerTypeId", "Description", "Current"]) ;
			$myQuery->addWhere( ["BrakePadId = '".$this->BrakePadId."'"]) ;
			$myQuery->addOrder( ["BrakePadId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "BrakePadValue") ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getOwnerField() {	return $this->keyCol ;	}
}
?>
