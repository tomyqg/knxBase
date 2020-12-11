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
 * ProductGroupItem - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BProductGroupItem which should
 * not be modified.
 *
 * @package WTA
 * @subpackage Article
 */

class	ProductGroupItem	extends	FDbObject	{

	/*
	 * The constructor can be passed an ArticleNr (ProductGroupItemNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_prodGrNo')") ;
		parent::__construct( "ProductGroupItem", "Id") ;
		if ( $_id >= 0) {
			$this->setId( $_id) ;
		} else {
		}
		FDbg::end() ;
	}
}
?>
