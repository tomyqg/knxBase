<?php
/**
 * UI_Selector.php - Definition der Basis Klasses f�r Liefn Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "base/UI_Object.php") ;
/**
 * UI_Selector - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage UI_Selector
 */
class	UI_Selector	extends	UI_Object	{
	/**
	 *
	 */
	function	__construct( $_mySelectorName='') {
		parent::__construct( "UI_Selector", "SelectorName") ;
		if ( strlen( $_mySelectorName) > 0) {
			$this->setSelectorName( $_mySelectorName) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setSelectorName( $_mySelectorName) {
		$this->SelectorName	=	$_mySelectorName ;
		if ( strlen( $_mySelectorName) > 0) {
			$this->reload() ;
		}
	}
	/**
	 * methods: retrieval
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		return $ret ;
	}
	function	getTableDepAsXML( $_key="", $_id=-1, $_val) {
		switch ( $_val) {
			case	"yyy"	:
				$objName	=	$_val ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				$tmpObj	=	new $objName() ;
				$_POST['_step']	=	$_id ;
				$ret	=	$tmpObj->tableFromDb( " ", " ", "C.yyy = '" . $myKey . "' ", " ") ;
				break ;
			default	:
				parent::getTableDepAsXML( $_key, $_id, $_val) ;
				break ;
		}
		return $ret ;
	}
	function	getDepAsXML( $_key="", $_id=-1, $_val="") {
		switch ( $_val) {
			default	:
				if ( $_id >= 0) {
					return parent::getDepAsXML( $_key, $_id, $_val) ;
				} else {
					$newItem	=	new $_val ;
					return $newItem->getAsXML() ;
				}
				break ;
		}
	}
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
}
?>
