<?php
/**
 * UI_Dict.php - Definition der Basis Klasses f�r Liefn Lieferungen
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
 * UI_Dict - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage UI_Dict
 */
class	UI_Dict	extends	UI_Object	{
	/**
	 *
	 */
	function	__construct( $_myDataItemType='') {
		parent::__construct( "UI_Dict", "DataItemType") ;
		if ( strlen( $_myDataItemType) > 0) {
			$this->setDataItemType( $_myDataItemType) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setDataItemType( $_myDataItemType) {
		$this->DataItemType	=	$_myDataItemType ;
		if ( strlen( $_myDataItemType) > 0) {
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
