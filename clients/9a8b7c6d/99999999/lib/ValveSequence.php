<?php
/**
 * ValveSequence.php - Class definition
 *  Domain:
 *  	- administrative 
 * 	ValveSequence references:
 * 		- n/a
 *  ValveSequence is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 * 
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package ValveSequenceCalc
 */
/**
 * ValveSequence - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package ValveSequenceCalc
 * @subpackage Classes
 */
class	ValveSequence	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "ValveSequence", "Id") ;
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
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "ValveSequence") ;
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
					$newItem->ValveSequenceId	=	$this->ValveSequenceId ;
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
		FDbg::begin( 1, "ValveSequence.php", "ValveSequence", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
			case	""	:	
				$_POST['_step']	=	$_val ;
				$filter	=	"" ;
				$_searchCrit	=	"" ;
				$_idCrit	=	"" ;
				$_descriptionCrit	=	"" ;
				if ( isset( $_POST['_SSearch']))
					$_searchCrit	=	$_POST['_SSearch'] ;
				$filter	.=	"(" ;
				$filter	.=	"( C.br_id like '%" . $_idCrit . "%' OR C.br_bezeichnung like '%" . $_descriptionCrit . "%' ) " ;
				$filter	.=	")" ;
				$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
				$myObj->addCol( "Id", "int") ;
				$myObj->addCol( "br_key", "var") ;
				$myObj->addCol( "br_id", "var") ;
				$myObj->addCol( "br_bezeichnung", "var") ;
				$myObj->addCol( "hersteller_id", "var") ;
				$reply->replyData	=	$myObj->tableFromDb( " ",
										" ",
										$filter,
										"ORDER BY C.br_id ASC ",
										"ValveSequence",
										"ValveSequence",
										"C.br_key AS Id, C.br_id, C.br_key, C.Hersteller_id, C.br_bezeichnung ") ;
			case	"ValveSequence"	:
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
