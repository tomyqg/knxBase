<?php
/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * requires the WTF Debugger and the Base Class
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/BArtStruct.php") ;
/**
 * ArtStruct - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtStruct which should
 * not be modified.
 *
 * @package Application
 * @subpackage ArtGr
 */
class	ArtStruct	extends	BArtStruct	{
	/*
	 * The constructor can be passed an ArticleNr (ArtStructNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_artStructNr="") {
		if ( strlen( $_artStructNr) >= 0) {
			$this->ArtStructNr	=	$_artStructNr ;
			$this->reload() ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setArtStructNr( $_artStructNr="") {
		if ( strlen( $_artikelNr) > 0) {
			$this->ArtStructNr	=	$_artStructNr ;
			$this->reload() ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setId( $_id=-1) {
		if ( $_id >= 0) {
			$this->Id	=	$_id ;
			$this->fetchFromDbById() ;
		} else {
		}
	}
	/**
	 *
	 */
	function	reload( $_db="def") {
		FDbg::get()->dumpL( 0x01000000, "ArtStru::reload()") ;
		FDbg::get()->dumpL( 0x01000000, "ArtStru::reload(), ArtStructNr='%s'", $this->ArtStructNr) ;
		$this->fetchFromDb() ;
		FDbg::get()->dumpL( 0x01000000, "ArtStru::reload(), done") ;
	}
}
?>
