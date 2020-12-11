<?php
/**
 * ArticleText.php - Class Implementation for ArticleText
 *
 * ArticleText represent additional text in specific languages.
 * An ArticleText is uniquely identified by the ArticleNo and the Language.y
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * ArticleText - User-Level Klasse
 *
 * �This class acts as an interface towards the automatically generated BArticleText which should
 * not be modified.
 *
 * @package Application
 * @subpackage Article
 */
class	ArticleText	extends	AppObject	{
	
	/*
	 * The constructor can be passed an ArticleNr (ArticleNo), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_articleNo
	 * @return void
	 */
	function	__construct( $_class='ArticleText', $_keyCol="Id") {
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

