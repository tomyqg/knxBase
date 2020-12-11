<?php
/**
 * UI_DTV.php - Definition der Basis Klasses f�r Liefn Lieferungen
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
 * UI_DTV - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage UI_DTV
 */
class	UI_DTV	extends	UI_Object	{
	/**
	 *
	 */
	function	__construct( $_myDTVName='') {
		parent::__construct( "UI_DTV", "DTVName") ;
		if ( strlen( $_myDTVName) > 0) {
			$this->setDTVName( $_myDTVName) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setDTVName( $_myDTVName) {
		$this->DTVName	=	$_myDTVName ;
		if ( strlen( $_myDTVName) > 0) {
			$this->reload() ;
		}
		return $this->_valid ;
	}
	/**
	 * (non-PHPdoc)
	 * @see UI_Object::getXMLComplete()
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "UI_DTVField") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see UI_Object::getXMLString()
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
				return $tmpObj->tableFromDb( "", "", "DTVName = '$this->DTVName' ", "ORDER BY SeqNo ") ;
		}
	}
	/**
	 * 
	 * Enter description here ...
	 * @param string $_tabName
	 */
	function	getJSCode( $_screenName, $_tabName) {
		$lineEnd	=	"\n" ;
		$ret	=	"" ;
		$myScr	=	new UI_Screen( $_screenName) ;
		$scrName	=	"screen".$myScr->ScreenName ;
		$dtvName	=	$scrName.".dtv".$this->DTVName ;
		$dtvField	=	new UI_DTVField() ;
		$dict		=	new UI_Dict() ;
		/**
		 * insert JS code for the creation of the DataTableView
		 */
		$ret	=	 $dtvName." = new dataTableView( \"".$dtvName."\", "
				.	"\"Table".$this->DTVName."\", "
				.	"\"".$myScr->MainObj."\", "
				.	"\"".$this->ObjectClass."\", null) ; ".$lineEnd ;
		$ret	.=	$dtvName.".f1 = \"form".$this->DTVName."Top\" ; ".$lineEnd ;
		$ret	.=	$dtvName.".f2 = \"form".$this->DTVName."Bot\" ; ".$lineEnd ;
		/**
		 * insert JS core for each individual field of the DataTableView
		 */
		$dtvField->setIterCond( "DTVName = '". $this->DTVName."' ") ;
		$dtvField->setIterOrder( "ORDER BY SeqNo ") ;
		reset( $dtvField) ;
		foreach ( $dtvField as $key => $actDTVField) {
//			error_log( "UI_DTV.php::UI_DTV::getJSTabCode(): working on '$actDTVField->FieldName', of type '$actDTVField->DataItemType'") ;
			if ( $dict->setDataItemType( $actDTVField->DataItemType)) {
//				$actTab->getJSCode( $this->ScreenName) ;
			}
		}
		/**
		 * insert JS code for the additional dataitem editor
		 */
//		$neededEdtName	=	"screen".$myScr->ScreenName.".edt".$this->ObjectClass ;
//		$ret	.=	"if ( ".$neededEdtName.") {".$lineEnd ;
//		$ret	.=	"\t"."_debugL( 0x00000001, \"editor exists ...\") ;\n" ;
//		$ret	.=	"\t".$dtvName.".dataItemEditor = ".$neededEdtName." ;".$lineEnd ;
//		$ret	.=	"} else {".$lineEnd ;
//		$ret	.=	"\t"."_debugL( 0x00000001, \"editor does not exist ...\") ;\n" ;
//		$ret	.=	"}".$lineEnd ;
		return $ret ;
	}
}
?>
