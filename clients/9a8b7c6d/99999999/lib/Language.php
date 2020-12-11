<?php
/**
 * Language.php - Class definition
 *  Domain:
 *  	- administrative 
 * 	Language references:
 * 		- n/a
 *  Language is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 * 
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package LanguageCalc
 */
/**
 * Language - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package LanguageCalc
 * @subpackage Classes
 */
class	Language	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "Language", "LanguageId") ;
//		parent::setIdKey( "br_key") ;
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
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "Language") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch ( $_val) {
			default	:
				if ( $_id >= 0) {
					parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
				} else {
					$newItem	=	new $_val ;
					$newItem->bremse_id	=	$this->br_id ;
//					$newItem->Domain	=	$this->__getUser()->Domain ;
					$newItem->getAsXML( $_key="", $_id=-1, $_val="", $reply) ;
				}
				break ;
		}
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Language.php", "Language", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::addDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Language.php", "Language", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Language.php", "Language", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::delDep()
	 */
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Language.php", "Language", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Language.php", "Language", "getList( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		else
			$sCrit	=	"" ;
		$objName	=	$_val ;
		switch ( $objName) {
			case	""	:	
				$reply->replyData	=	$this->tableFromDb( " ",
										" ",
										"1 = 1 ",
										"ORDER BY C.LanguageId ASC ",
										"Language",
										"Language") ;
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