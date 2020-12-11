<?php
/**
 * ArticleStock.php - Class Implementation for ArticleStock
 *
 * ArticleStock represent additional text in specific languages.
 * An ArticleStock is uniquely identified by the ArticleNo and the Language.y
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * ArticleStock - User-Level Klasse
 *
 * This class acts as an interface towards the automatically generated BArticleStock which should
 * not be modified.
 *
 * @package Application
 * @subpackage Article
 */
class	ArticlePurchasePriceRel	extends	AppObject	{
	
	/*
	 * The constructor can be passed an ArticleNr (ArticleNo), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_articleNo
	 * @return void
	 */
	function	__construct( $_class='ArticlePurchasePriceRel', $_keyCol="Id") {
		parent::__construct( $_class, $_keyCol) ;
	}
	
	/**
	 *
	 */
	protected function _postInstantiate() {
	}
	
	/**
	 *
	 */
	protected function _postLoad() {
	}
}

