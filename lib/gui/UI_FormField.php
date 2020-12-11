<?php
/**
 * UI_FormField.php - Definition der Basis Klasses f�r Liefn Lieferungen
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
 * UI_FormField - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage UI_FormField
 */
class	UI_FormField	extends	UI_Object	{
	/**
	 *
	 */
	function	__construct( $_myFieldName='') {
		parent::__construct( "UI_FormField", "Id") ;
		$this->myCuOrdr	=	NULL ;
		if ( strlen( $_myFieldName) > 0) {
			$this->setFieldName( $_myFieldName) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setFieldName( $_myFieldName) {
		$this->FieldName	=	$_myFieldName ;
		if ( strlen( $_myFieldName) > 0) {
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
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
}
?>
