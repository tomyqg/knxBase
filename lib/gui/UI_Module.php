<?php

/**
 * SysTexte.php - Specialized sub-class of Texte, table containing system text-blocks in arbitrary langages
 *
 * The class serves as an interface towards the systemized - and in fact quite versatile - system text object in the
 * database. A system text, like a regular text (therfor derived of ...) is identified by it's name, e.g. CuOrdrEMail
 * (customr order e-mail), an optional reference number and a language.
 * The reference number, however, SHOULD NOT be used for systemized text.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Texte - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BTexte which should
 * not be modified.
 *
 * @package Application
 * @subpackage Basic
 */
class	UI_Module	extends	UI_Object	{

	/*
	 * construct()
	 *
	 * assign the name, refNr and lanuage to 'this' object and tries to retrieve it from the database with the
	 * given keys.
	 * If the 'Texte' object can not be found an exception is thrown.
	 *
	 * @param string $_name 	name of the text to be retrieved
	 * @param string $_refNr	optional reference number of the text to be retrieved
	 * @param string $_sprache	optional language of the text to be retrieved, defaults to de_de (german _in_ Germany)
	 */
	function	__construct( $_moduleName="") {
		FDbg::begin( 1, "UI_Module.php", "UI_Module", "__construct( '$_moduleName'") ;
		parent::__construct( "UI_Module", "ModuleName") ;
		if ( strlen( $_moduleName) > 0) {
			try {
				$this->setKey( $_moduleName) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
		FDbg::end( 1) ;
	}
	/**
	 * (non-PHPdoc)
	 * @see UIObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		switch( $_val) {
			case	"UI_Screen"	:
				$newItem	=	new $_val() ;
				$newItem->ModuleName	=	$this->ModuleName ;
				$newItem->ScreenName	=	$_POST['_IScreenName'] ;
				$newItem->storeInDb() ;
				return $this->getTableDepAsXML( $_key, $_id, $_val) ;
				break ;
			default	:
				parent::addDep( $_key, $_id, $_val) ;
				return $this->getTableDepAsXML( $_key, $_id, $_val) ;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see UIObject::getTableDepAsXML()
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "UI_Screen") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see UIObject::getAsXML()
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		$ret	=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see UIObject::getTableDepAsXML()
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		switch ( $_val) {
			default	:
				$objName	=	$_val ;
				$tmpObj	=	new $objName() ;
				$tmpObj->setId( $_id) ;
				$_POST['_step']	=	$_id ;
				return $tmpObj->tableFromDb( "", "", "ModuleName = '$this->ModuleName' ", "ORDER BY SeqNo ") ;
		}
	}
}

?>
