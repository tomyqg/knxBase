<?php
/**
 * Manufacturer.php - Class definition
 *  Domain:
 *  	- administrative
 * 	Manufacturer references:
 * 		- n/a
 *  Manufacturer is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package BrakeCalc
 */
/**
 * Manufacturer - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package BrakeCalc
 * @subpackage Classes
 */
class	Manufacturer	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "Manufacturer", "ManufacturerId") ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
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
					$newItem->ManufacturerId	=	$this->ManufacturerId ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$_POST['_step']	=	$_val ;
			$myObj	=	new FDbObject( "Manufacturer", "") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["ManufacturerId like '%" . $_POST['filterManufacturerId'] . "%' ", "Description like '%" . $_POST['filterDescription'] . "%' "]) ;
			$myQuery->addOrder( [$sortOrder]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"Brake"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["ManufacturerId = '".$this->ManufacturerId."'"]) ;
			$myQuery->addOrder( ["BrakeId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Brake") ;
			break ;
		case	"Cylinder"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["ManufacturerId = '".$this->ManufacturerId."'"]) ;
			$myQuery->addOrder( ["CylinderId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Cylinder") ;
			break ;
		case	"Valve"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["ManufacturerId = '".$this->ManufacturerId."'"]) ;
			$myQuery->addOrder( ["ValveId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Valve") ;
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
