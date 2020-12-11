<?php
/**
 * UI_Form.php - Definition der Basis Klasses f�r Liefn Lieferungen
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
 * UI_Form - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage UI_Form
 */
class	UI_Form	extends	UI_Object	{
	/**
	 *
	 */
	function	__construct( $_myFormName='') {
		parent::__construct( "UI_Form", "FormName") ;
		if ( strlen( $_myFormName) > 0) {
			$this->setFormName( $_myFormName) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setFormName( $_myFormName) {
		$this->FormName	=	$_myFormName ;
		if ( strlen( $_myFormName) > 0) {
			$this->reload() ;
		}
	}
	/**
	 * methods: retrieval
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "UI_FormField") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see UI_Object::getTableDepAsXML()
	 */
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see UI_Object::getTableDepAsXML()
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		switch ( $_val) {
			default	:
				$objName	=	$_val ;
				$tmpObj	=	new $objName() ;
				$tmpObj->setId( $_id) ;
				$_POST['_step']	=	$_id ;
				return $tmpObj->tableFromDb( "", "", "FormName = '$this->FormName' ", "ORDER BY SeqNo ") ;
		}
	}
}
?>
