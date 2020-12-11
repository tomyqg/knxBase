<?php
/**
 * UI_Tab.php - Definition der Basis Klasses f�r Liefn Lieferungen
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
 * UI_Tab - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage UI_Tab
 */
class	UI_Tab	extends	UI_Object	{
	/**
	 *
	 */
	function	__construct( $_myTabName='') {
		parent::__construct( "UI_Tab", "TabName") ;
		$this->myCuOrdr	=	NULL ;
		if ( strlen( $_myTabName) > 0) {
			$this->setTabName( $_myTabName) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setTabName( $_myTabName) {
		$this->TabName	=	$_myTabName ;
		if ( strlen( $_myTabName) > 0) {
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
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "UI_FormToTab") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "UI_DTVToTab") ;
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
				return $tmpObj->tableFromDb( "", "", "TabName = '$this->TabName' ", "ORDER BY SeqNo ") ;
		}
	}
	/**
	 * 
	 * Enter description here ...
	 * @param string $_screenName name of the screen to which this tab belongs
	 */
	function	getJSCode( $_screenName) {
		error_log( "UI_Tab.php::UI_Tab::getJSTabCode( '$_screenName'): begin, TabName = '$this->TabName'") ;
		$ret	=	"//\n// getting JS for tab '$this->TabName' \n//\n" ;
		$subTab	=	new UI_Tab() ;
		$dtvToTab	=	new UI_DTVToTab() ;
		$dtv	=	new UI_DTV() ;
		$dtvToTab->setIterCond( "TabName = '". $this->TabName."' ") ;
		$dtvToTab->setIterOrder( "ORDER BY SeqNo ") ;
		reset( $dtvToTab) ;
		foreach ( $dtvToTab as $key => $actDtvToTab) {
			if ( $dtv->setDTVName( $actDtvToTab->DTVName)) {
//				error_log( "UI_Tab.php::UI_Tab::getJSTabCode(): working on '$dtv->DTVName'") ;
				$ret	.=	$dtv->getJSCode( $_screenName, $this->TabName) ;
				/**
				 * process potentil sub-tabs
				 */
			}
		}
		/**
		 * check if the tab has sub-tabs and process them
		 */
		$subTab->setIterCond( "ParentTabName = '". $this->TabName."' ") ;
		$subTab->setIterOrder( "ORDER BY SeqNo ") ;
		reset( $subTab) ;
		foreach ( $subTab as $key => $actSubTab) {
			$ret	.=	$actSubTab->getJSCode( $_screenName) ;
		}
//		error_log( "UI_Tab.php::UI_Tab::getJSTabCode(): end") ;
		return $ret ;
	}
}
?>
