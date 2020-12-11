<?php

/**
 * Texte.php - Base class for Texte, table containing general text-blocks in arbitrary languages
 *
 * The class serves as an interface towards a general - and in fact quite versatile - text object in the
 * database. A text is identified by it's name, e.g. CuOrdrEMail_Text, CuOrdrEMail_HTML (customr order e-mail),
 * an optional reference number and a language.
 *
 * Note:	The Db-table beheind this class stores 2 texts, one called Volltext and the other called Volltext2
 * 			Both will be initiated with the same value upon creation.
 * 			Volltext, however, is supposed to contain the payload, ie. the text to be used, whereas
 * 			Volltext2 is supposed to be used in an explanatory fashion
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
 * @subpackage Texte
 */
class	Texte	extends	FDbObject	{

	/*
	 * construct()
	 *
	 * assign the name, refNr and lanuage to 'this' object and tries to retrieve it from the database with the
	 * given keys.
	 * If the 'Texte' object can not be found an exception is thrown.
	 *
	 * @param string $_name 	name of the text to be retrieved
	 * @param string $_refNr	optional reference number of the text to be retrieved
	 * @param string $_sprache	optional language of the text to be retrieved, defaults to de_DE (german _in_ Germany)
	 */
	function	__construct( $_name="", $_refNr="", $_sprache="de_DE") {
		parent::__construct( "Texte", "Id") ;
		if ( strlen( $_name) > 0) {
			try {
				$this->setKeys( $_name, $_refNr, $_sprache) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
	}

	/**
	 * setKeys(...)
	 *
	 * assign the name, refNr and lanuage to 'this' object and tries to retrieve it from the database with the
	 * given keys. If the 'Texte' object can not be found an exception is thrown.
	 *
	 * @param string $_name 	name of the text to be retrieved
	 * @param string $_refNr	optional reference number of the text to be retrieved
	 * @param string $_sprache	optional language of the text to be retrieved, defaults to de_DE (german _in_ Germany)
	 */
	function	setKeys( $_name, $_refNr="", $_sprache="de_DE") {
		FDbg::begin( 1, "Texte.php", "Texte", "setKeys( '$_name', '$_refNr', '$_sprache')") ;
		if ( strlen( $_name) > 0) {
			$this->Name	=	$_name ;
			$this->RefNr	=	$_refNr ;
			$this->Sprache	=	$_sprache ;
			try {
				$this->reloadByKeys() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
		FDbg::end( 1, "Texte.php", "Texte", "setKeys( '$_name', '$_refNr', '$_sprache')") ;
		return $this->_valid ;
	}

	/**
	 * Tries to retrieve a matching object from the Db. This method brakes down this->Sprache into
	 * 2 fragments, separated by '_'. First it tries to find the named text with the full language_COUNTRY
	 * code, e.g. en_US. If this fails, the language code portion is used to fetch the string. If all of this
	 * fails the below defined languages will be added to the Db AUTOMATICALLY.
	 */
	function	reloadByKeys() {
		FDbg::begin( 1, "Texte.php", "Texte", "reloadByKeys()") ;
		$found	=	false ;
		$basicLang	=	explode( "_", str_replace( "/", "_", $this->Sprache)) ;
		if ( ! isset( $basicLang[1])) {
			$basicLang[1]	=	"DE" ;
		}
		$cond1	=	sprintf( "Name = '%s' AND ( RefNr = '%s' OR RefNr = '') AND Sprache='%s' ", $this->Name, $this->RefNr, $this->Sprache) ;
		$cond2	=	sprintf( "Name = '%s' AND ( RefNr = '%s' OR RefNr = '') AND Sprache='%s' ", $this->Name, $this->RefNr, $basicLang[0]) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "Texte.php", "Texte", "reloadByKeys()", $cond1) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "Texte.php", "Texte", "reloadByKeys()", $cond2) ;
		if ( $this->fetchFromDbWhere( $cond1)) {
			$found	=	true ;
		} else if ( $this->fetchFromDbWhere( $cond2)) {
			$found	=	true ;
		}
		if ( ! $found) {
			/**
			 *
			 * @var unknown_type
			 */
			$this->Volltext	=	$this->Name ;
			$this->Volltext2	=	$this->Name ;
			$lang	=	array(	"de", "de_DE", "de_AU", "de_CH",
								"en", "en_US", "en_UK",
								"fr", "fr_FR", "fr_CA",
								"es", "es_ES", "es_CL") ;
			$this->RefNr	=	"" ;
			foreach ( $lang as $this->Sprache) {
				/**
				 * check if this language already exists
				 * @var unknown_type
				 */
				$cond	=	"WHERE Name = '".$this->Name."' "
						.	"  AND ( RefNr = '') "
						.	"  AND ( Sprache='".$this->Sprache."') " ;
				$this->existWhere( $cond) ;
				if ( $this->isValid()) {
					/**
					 * if there was no entry found, create the Text
					 */
					if ( $this->_status == 0) {
						FDbg::trace( 0, FDbg::mdTrcInfo1, "Texte.php", "Texte", "reloadByKeys()", "auto-add for '".$this->Name."', Lang '".$this->Sprache."' ") ;
						$this->storeInDb() ;
					}
				}
			}
//			$e	=	new Exception( "Texte::reload: object 'Texte' not valid after reload!") ;
//			error_log( $e->getMessage()) ;
//			throw $e ;
		}
		FDbg::end( 1, "Texte.php", "Texte", "reloadByKeys()") ;
	}
	/**
	 * (non-PHPdoc)
	 * @see DbObject::upd()
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		try {
			$this->getFromPostL() ;
			$this->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( $_key="", $_id=-1, $_val="") ;
	}
	/**
	 * (non-PHPdoc)
	 * @see DbObject::upd()
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		$this->getFromPostL() ;
		$this->updateInDb() ;
		return $this->getXMLString( $_key="", $_id=-1, $_val="", $reply=null) ;
	}
	/**
	 * (non-PHPdoc)
	 * @see DbObject::upd()
	 */
	function	del( $_key="", $_id=-1, $_val="") {
	}
	/**
	 * object retrieval methods
	 */
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @return string
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		return $ret ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @return string
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @return string
	 */
	function	getList( $_key="", $_id=-1, $_val="") {
		$nameCrit	=	$_POST['_SName'] ;
		$textCrit	=	$_POST['_SText'] ;
		$languageCrit	=	$_POST['_SLanguage'] ;
		$translatedCrit	=	$_POST['_STranslated'] ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( " ;
		$filter	.=	"C.Name like '%" . $nameCrit . "%' " ;
		$filter	.=	"AND C.Volltext like '%" . $textCrit . "%' " ;
		if ( $_POST['_SLanguage'] != "")
			$filter	.=	"  AND Sprache = '" . $_POST['_SLanguage'] . "' " ;
			if ( $translatedCrit == 1) {
				$filter	.=	"  AND Volltext = Volltext2 " ;
			}
		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "Volltext", "var") ;
		$myObj->addCol( "Volltext2", "var") ;
		$myObj->addCol( "Sprache", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.Id ASC ",
								$this->className,
								$this->className,
								"C.Id, C.Name, SUBSTR( C.Volltext, 1, 30) AS Volltext, SUBSTR( C.Volltext2, 1, 30) AS Volltext2, C.Sprache ") ;
//		error_log( $ret) ;
		return $ret ;
	}
}

?>
