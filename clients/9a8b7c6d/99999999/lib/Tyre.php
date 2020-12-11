<?php
/**
 * Tyre.php - Class definition
 *  Domain:
 *  	- administrative
 * 	Tyre references:
 * 		- n/a
 *  Tyre is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package TyreCalc
 */
/**
 * Tyre - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package TyreCalc
 * @subpackage Classes
 */
class	Tyre	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "Tyre", "TyreId") ;
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
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "Tyre") ;
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
		FDbg::begin( 1, "Tyre.php", "Tyre", "addDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, "Tyre.php", "Tyre", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Tyre.php", "Tyre", "updDep( '$_key', $_id, '$_val')",
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
		FDbg::begin( 1, "Tyre.php", "Tyre", "updDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, "Tyre.php", "Tyre", "getList( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		else
			$sCrit	=	"" ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$filter	=	"" ;
			$_searchCrit	=	"" ;
			$_idCrit	=	"" ;
			$_descriptionCrit	=	"" ;
			if ( isset( $_POST['_SSearch']))
				$_searchCrit	=	$_POST['_SSearch'] ;
			$filter	.=	"(" ;
			$filter	.=	"( TyreId like '%" . $_searchCrit . "%' OR Description like '%" . $_searchCrit . "%' ) " ;
			$filter	.=	")" ;
			/**
			 *
			 */
			$myObj	=	new FDbObject( "Tyre", "Id", "def", "v_TyreSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
//			$myQuery->addField( ["Id","AxleUnitId","TrailerTypeId", "Description", "Current"]) ;
			$myQuery->addWhere( [$filter]) ;
			$myQuery->addOrder( ["TyreId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Tyre") ;
			break ;
		case	"Assessment"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
//			$myQuery->addField( ["Id","AxleUnitId","TrailerTypeId", "Description", "Current"]) ;
			$myQuery->addWhere( ["TyreId = '".$this->TyreId."'"]) ;
			$myQuery->addOrder( ["AssessmentId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Assessment") ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * Get a new key for the object and stores the object as an empty object in the database.
	 * The object is then reloaded.
	 * @param int $_digits	number of digits for the key
	 * @param string $_nsStart	beginning of the number range within which to fetch the new key
	 * @param string $_nsEnd	end of the number range within which to fetch the new key
	 * @return void
	 */
	function	newKey( $_digits=6, $_nsStart="000000", $_nsEnd="999999", $_store=true) {
		FDbg::begin( 1, "Tyre.php", "Tyre", "newKey( $_digits, '$_nsStart', '$_nsEnd')") ;
		$lastObj	=	new Tyre() ;
		$lastObj->first( "TyreId DESC") ;
		$this->TyreId	=	$lastObj->TyreId ;
		$idParts	=	explode( ".", $lastObj->TyreId) ;
		if ( count( $idParts) == 2) {
			$newNo	=	sprintf( "%d", intVal( $idParts[1]) + 1) ;
			$this->TyreId	=	$idParts[0] . "." . str_pad( $newNo, strlen( $idParts[1]), "0", STR_PAD_LEFT) ;
		}
		FDbg::trace( 2, "Tyre.php", "Tyre", "newKey( $_digits, '$_nsStart', '$_nsEnd')", "will store") ;
		if ( $_store) {
			$this->storeInDb() ;
			$this->reload() ;		// reload; dependent objects might have changed, e.g. customer/supplier/-contacs
		} else {
			$this->_valid	=	true ;
		}
		FDbg::end() ;
		return $this->TyreId ;
	}
	/**
	 *
	 */
	function	getOwnerField() {	return $this->keyCol ;	}
}
?>
