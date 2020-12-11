<?php
/**
 * ArticleProposal.php
 * ===================
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wimteccERM
 */
/**
 * ArticleProposal - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtEmpf which should
 * not be modified.
 *
 * @package Application
 * @subpackage Article
 */
class	ArticleProposal	extends	FDbObject	{

	/*
	 * The constructor can be passed an ArticleNr (ArtEmpfNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		parent::__construct( "ArticleProposal", "Id") ;
		if ( $_id >= 0) {
			$this->Id	=	$_id ;
			$this->fetchFromDbById() ;
		} else {
		}
	}
}
?>
