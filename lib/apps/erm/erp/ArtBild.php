<?php
/**
 * ArticleImage.php
 * ================
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.

 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wimteccERM
 */
/**
 * Artikel - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package wimteccERM
 * @subpackage Article
 */
class	ArticleImage	extends	FDbObject	{

	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "ArticleImage", "Id") ;
	}
}
?>
