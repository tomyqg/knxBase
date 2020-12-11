<?php
/**
 * Brake.php - Class definition
 *  Domain:
 *  	- administrative
 * 	Brake references:
 * 		- n/a
 *  Brake is referenced by:
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
 * Brake - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package BrakeCalc
 * @subpackage Classes
 */
if ( 1 == 1) {
class	Brake	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "Brake", "BrakeId") ;
//		parent::setIdKey( "br_key") ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
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
		FDbg::begin( 1, "Brake.php", "Brake", "addDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, "Brake.php", "Brake", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		/**
		 *
		 */
		if ( $objName === "")
			$objName	=	$this->className ;
		/**
		 *
		 */
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		/**
		 *
		 */
		FDbg::trace( 2, "Brake.php", "Brake", "updDep( '$_key', $_id, '$_val')",
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
		FDbg::begin( 1, "Brake.php", "Brake", "updDep( '$_key', $_id, '$_val')") ;
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
	 * getList(...)
	 * ============
	 *
	 * Enter description here ...
	 *
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Brake.php", "Brake", "getList( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		if ( isset( $_POST['_treeLevel'])) {
			$treeLevel	=	intval( $_POST['_treeLevel']) ;
		} else {
			$treeLevel	=	0 ;
		}
		$objName	=	$_val ;
		$sortOrder	=	"" ;
		$sortOrder	=	"Manufacturer ASC" ;
		switch ( $objName) {
			case	"__"	:
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
				break ;
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
				$myObj->treeLevel	=	$treeLevel ;
				$myObj->maxLevel	=	3 ;
				switch ( $treeLevel) {
				case	0	:
					$myObj->dataset	=	"Manufacturers" ;
					$myObj->level	=	0 ;
					$myQuery->addGroup( [ "ManufacturerId"]) ;
					$myQuery->addOrder( [ "ManufacturerId"]) ;
					break ;
				case	1	:
					$myObj->dataset	=	"ManufacturerBrakeTypes" ;
					$myObj->level	=	1 ;
					$myQuery->addGroup( [ "BT_Description"]) ;
//					$myQuery->addOrder( [ "ManufacturerId"]) ;
					$myQuery->addWhere( [ "ManufacturerId='".$this->ManufacturerId."' "]) ;
					break ;
				case	2	:
					$myObj->dataset	=	"ManufacturerBrakes" ;
					$myObj->level	=	2 ;
					$myQuery->addGroup( [ "BT_Description", "Description"]) ;
//					$myQuery->addOrder( [ "ManufacturerId"]) ;
					$myQuery->addWhere( [ "ManufacturerId='".$this->ManufacturerId."' ", "B_BrakeTypeId='".$this->BrakeTypeId."' "]) ;
					break ;
				case	3	:
					$myObj->dataset	=	"Brake" ;
					$myObj->level	=	3 ;
//					$myQuery->addGroup( [ "BT_Description", "Description"]) ;
//					$myQuery->addOrder( [ "ManufacturerId"]) ;
					$myQuery->addWhere( [ "ManufacturerId='".$this->ManufacturerId."' ", "B_BrakeTypeId='".$this->BrakeTypeId."' ", "BrakeId='".$this->BrakeId."' "]) ;
					break ;
				}
				$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Brake") ;
				break ;
			case	"LeverLength"	:
				$myObj	=	new $objName() ;
				if ( isset( $_POST['StartRow'])) {
					$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
				}
				$myQuery	=	$myObj->getQueryObj( "Select") ;
				$myQuery->addWhere( ["BrakeId = '" . $this->BrakeId . "' "]) ;
				$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
				break ;
			case	"Assessment"	:
				$sortOrder	=	"ORDER BY TestDateSTVZO DESC " ;
				$mainFilter	=	"" ;
				$addFilter	=	"" ;
				$mainFilter	.=	"( " ;
				$mainFilter	.=	"C.ProtocolNo like '%" . $sCrit . "%' " ;
				$mainFilter	.=	") " ;
				$filter	=	"AND " . $mainFilter . $addFilter ;
				if ( isset( $_POST['SortOrder'])) {
					if ( $_POST['SortOrder'] != "") {
						$sortOrder	=	"ORDER BY " . $_POST['SortOrder'] . " " ;
					}
				}
				$myObj	=	new $objName() ;
				if ( isset( $_POST['StartRow'])) {
					$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
				}
				$myObj->addCol( "Id", "int") ;
//				$tmpObj->setId( $_id) ;
//				$reply->replyData	=	$tmpObj->tableFromDb( " ", "", "BrakeId = '$this->BrakeId' ". $filter, $sortOrder) ;
				$myQuery	=	$myObj->getQueryObj( "Select") ;
				$myQuery->addWhere( ["BrakeId = '" . $this->BrakeId . "' "]) ;
				$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
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
} else {
	throw new Exception( "Invalid security level") ;
}

?>
