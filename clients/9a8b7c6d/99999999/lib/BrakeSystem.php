<?php
/**
 * BrakeSystem.php - Class definition
 *  Domain:
 *  	- administrative
 * 	BrakeSystem references:
 * 		- n/a
 *  BrakeSystem is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package BrakeSystemCalc
 */
/**
 * BrakeSystem - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package BrakeSystemCalc
 * @subpackage Classes
 */
class	BrakeSystem	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "BrakeSystem", "BrakeSystemId") ;
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
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "BrakeSystem") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
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
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "BrakeSystem.php", "BrakeSystem", "addDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, "BrakeSystem.php", "BrakeSystem", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "BrakeSystem.php", "BrakeSystem", "updDep( '$_key', $_id, '$_val')",
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
		FDbg::begin( 1, "BrakeSystem.php", "BrakeSystem", "updDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, "BrakeSystem.php", "BrakeSystem", "getList( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
				$objName	=	$_val ;
		$sortOrder	=	"ORDER BY C.BrakeSystemId ASC " ;
		switch ( $objName) {
		case	""	:
			$_POST['_step']	=	$_val ;
			$mainFilter	=	"" ;
			$addFilter	=	"" ;
			$mainFilter	.=	"( " ;
			$mainFilter	.=	"C.BrakeSystemId like '%" . $sCrit . "%' " ;
			$mainFilter	.=	"OR C.Description like '%" . $sCrit . "%' " ;
			$mainFilter	.=	") " ;
			if ( isset( $_POST['filterBrakeSystemId'])) {
				$addFilter	.=	"AND ( " ;
				$addFilter	.=	"C.BrakeSystemId like '%" . $_POST['filterBrakeSystemId'] . "%' " ;
				$addFilter	.=	"AND C.Description like '%" . $_POST['filterDescription'] . "%' " ;
				$addFilter	.=	") " ;
			}
			$filter	=	$mainFilter . $addFilter ;
									if ( isset( $_POST['SortOrder'])) {
				if ( $_POST['SortOrder'] != "") {
					$sortOrder	=	"ORDER BY " . $_POST['SortOrder'] . " " ;
				}
			}
			$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
			$myObj->addCol( "Id", "var") ;
			$myObj->addCol( "BrakeSystemId", "var") ;
			$myObj->addCol( "Description", "var") ;
			$myObj->addCol( "ValveSequenceDescription", "var") ;
			$myObj->addCol( "Manufacturer", "var") ;
			$myObj->addCol( "Options", "var") ;
			$reply->replyData	=	$myObj->tableFromDb( ", M.Description AS Manufacturer, TT.Options AS Options ",
									"LEFT JOIN Manufacturer AS M ON M.ManufacturerId = C.ManufacturerId "
									.	"LEFT JOIN TrailerType as TT on TT.TrailerTypeNo = C.TrailerTypeNo AND TT.TrailerSubTypeNo = C.TrailerSubTypeNo ",
									$filter,
									$sortOrder,
									"BrakeSystem",
									"BrakeSystem",
									"C.Id, C.BrakeSystemId, C.Description,C.ValveSequenceDescription ") ;
			break ;
		case	"ValveSequence"	:
			$myObj	=	new FDbObject( "ValveSequenceSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
//			$myQuery->addField( ["Id","AxleUnitId","TrailerTypeId", "Description", "Current"]) ;
			$myQuery->addWhere( [ "BrakeSystemId = '".$this->BrakeSystemId."'"]) ;
			$myQuery->addOrder( [ "AxleNo", "ValveNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "ValveSequence") ;
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
