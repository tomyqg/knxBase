<?php
/**
 * AxleUnit.php - Class definition
 *  Domain:
 *  	- administrative 
 * 	AxleUnit references:
 * 		- n/a
 *  AxleUnit is referenced by:
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
 * AxleUnit - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package BrakeCalc
 * @subpackage Classes
 */
class	AxleUnit	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "AxleUnit", "AxleUnitId") ;
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
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "AxleUnit") ;
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
					$newItem->AxleUnitId	=	$this->AxleUnitId ;
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
		FDbg::begin( 1, "AxleUnit.php", "AxleUnit", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:	
			$_POST['_step']	=	$_val ;
// 			$mainFilter	=	"" ;
// 			$addFilter	=	"" ;
// 			$mainFilter	.=	"( " ;0
// 			$mainFilter	.=	"C.AxleUnitId like '%" . $sCrit . "%' " ;
// 			$mainFilter	.=	"OR C.Description like '%" . $sCrit . "%' " ;
// 			$mainFilter	.=	") " ;
// 			if ( isset( $_POST['filterAxleUnitId'])) {
// 				$addFilter	.=	"AND ( " ;
// 				$addFilter	.=	"C.AxleUnitId like '%" . $_POST['filterAxleUnitId'] . "%' " ;
// 				$addFilter	.=	"AND C.Description like '%" . $_POST['filterDescription'] . "%' " ;
// 				$addFilter	.=	") " ;
// 			}
// 			$filter	=	$mainFilter . $addFilter ;
			/**
			 * 
			 */
			$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
 			$myQuery	=	new FSqlSelect( "AxleUnit") ;
			$myQuery->addField( ["Id","AxleUnitId","TrailerTypeId", "Description", "Current"]) ;
			$myQuery->addWhere( ["AxleUnitId LIKE '%$sCrit%'"]) ;
			$myQuery->addOrder( ["AxleUnitId"]) ;
			$myJoin	=	new FSqlJoin( $myQuery, "TrailerType") ;
			$myJoin->addField( ["Options"]) ;
			$myJoin->joinOn( "TrailerTypeId") ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			/**
			 * 
			 */
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
