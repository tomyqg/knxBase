<?php
/**
 * BrakePadValue.php - Class definition
 *  Domain:
 *  	- administrative
 * 	BrakePadValue references:
 * 		- n/a
 *  BrakePadValue is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package BrakePadValueCalc
 */
/**
 * BrakePadValue - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package BrakePadValueCalc
 * @subpackage Classes
 */
class	BrakePadValue	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "BrakePadValue", "Id") ;
//		parent::setIdKey( "h_key") ;
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
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "BrakePadValue") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="") {
		switch ( $_val) {
			default	:
				if ( $_id >= 0) {
					return parent::getDepAsXML( $_key, $_id, $_val) ;
				} else {
					$newItem	=	new $_val ;
					$newItem->BrakePadValueId	=	$this->BrakePadValueId ;
					$newItem->Domain	=	$this->__getUser()->Domain ;
					return $newItem->getAsXML() ;
				}
				break ;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="") {
		switch( $_val) {
			default	:
				parent::addDep( $_key, $_id, $_val) ;
				break ;
		}
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::delDep()
	 */
	function	delDep( $_key="", $_id=-1, $_val="") {
		switch( $_val) {
			default	:
				parent::delDep( $_key, $_id, $_val) ;
				break ;
		}
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "BrakePadValue.php", "BrakePadValue", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$_POST['_step']	=	$_val ;
			$myObj	=	new FDbObject( "Brake", "Id", "def", "v_BrakeSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["BrakeId like '%" . $_POST['filterBrakeId'] . "%' ", "Description like '%" . $_POST['filterDescription'] . "%' "]) ;
			$myQuery->addOrder( ["ManufacturerId", "BrakeTypeId", "Description"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Brake") ;
		case	"BrakePadValue"	:
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$reply->replyData	=	$tmpObj->tableFromDb( "", "", "bremse_id = '$this->bremse_id' ") ;
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
