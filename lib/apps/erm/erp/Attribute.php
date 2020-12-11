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
 * Artikel - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package Application
 * @subpackage Attribute
 */

class	Attribute	extends	AppObject	{

	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_class='Attribute', $_keyCol="Id") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_class', $_keyCol)") ;
		parent::__construct( $_class, $_keyCol) ;
		FDbg::end() ;
	}
	
	/**
	 *
	 */
	protected function _postInstantiate() {
		FDbg ::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "()" );
		FDbg ::end();
	}
	
	/**
	 *
	 */
	protected function _postLoad() {
		FDbg ::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "()" );
		FDbg ::end();
	}
}
