<?php
/**
 * UI_DTVToTab.php - Definition der Basis Klasses f�r Liefn Lieferungen
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
 * UI_DTVToTab - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage UI_DTVToTab
 */
class	UI_DTVToTab	extends	UI_Object	{
	/**
	 *
	 */
	function	__construct( $_myDTVToTabName='') {
		parent::__construct( "UI_DTVToTab", "Id") ;
		$this->myCuOrdr	=	NULL ;
		if ( strlen( $_myDTVToTabName) > 0) {
			$this->setDTVToTabName( $_myDTVToTabName) ;
		} else {
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
