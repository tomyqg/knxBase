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
 * ArticleGroupItem - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArticleGroupItem which should
 * not be modified.
 *
 * @package WTA
 * @subpackage Article
 */
class	ArticleGroupItem	extends	FDbObject	{

	/*
	 * The constructor can be passed an ArticleNr (ArticleGroupItemNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		parent::__construct( "ArticleGroupItem", "Id") ;
		if ( $_id >= 0) {
			$this->setId( $_id) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	_getNextItemNo() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$query	=	"SELECT ItemNo FROM ArticleGroupItem WHERE ArticleGroupNo='".$this->ArticleGroupNo."' ORDER BY ItemNo DESC LIMIT 0, 1 " ;
		try {
			$row	=	FDb::queryRow( $query) ;
			if ( $row) {
				$this->ItemNo	=	sprintf( "%d", ( intval( $row['ItemNo']) / 10) * 10 + 10) ;
			} else {
				$this->ItemNo	=	"10" ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
	}
}
?>
