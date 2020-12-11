<?php
/**
 * MySearch.php
 *
 * Class:	MySearch
 *
 * Revision history
 *
 * Date			User		Change
 * ----------------------------------------------------------------------------
 * 2013-04-30	miskhwe		started coding
 *
 * Handles display portion of the customer cart.
 * @author miskhwe
 *
 */
$lastArt = "" ;
class	Search	extends Page	{
	/**
	 *
	 */
	/**
	 *
	 */
	function	__construct( $_parent=null, $_lang="de", $_country="de") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <parent>, '$_lang', '$_country')") ;
		parent::__construct( $_parent, $_lang, $_country) ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 * @return string
	 */
	function	go( $_webPage, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		/**
		 *
		 */
		$buffer	=	"" ;
		$tmpBuffer	=	parent::run( "", "", "", "", $_tmplName) ;
		$tmpBuffer	=	parent::run( "", "", "", "", "MySearchFormats.xml") ;
		/**
		 * as we need information about the customer, esp. the rights-valud to select the articles
		 * which may be displayed, we need to handle the session beforehand
		 * load/create the session
		 */
		$this->Customer	=	new Customer() ;
		$this->myShopSession	=	new ShopSession() ;
		/**
		 * see if there's some action to be performed
		 */
		if ( isset( $this->itemAction)) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "MySearch.php", "MySearch{Page}", "run( <...>)", "itemAction defined as '".$this->itemAction."'") ;
			$myCuCart	=	new CuCart( $this->myShopSession->CuCartNo) ;
			switch ( $this->itemAction) {
			case	"deleteItem"	:
				/**
				 * if the user has presses reload for this function we will get an exception
				 * since the CuCArtItem with this Id has been deleted already. Here we silently
				 * "overhear" this request.
				 */
				try {

				} catch ( Exception $e) {

				}
				break ;
			default	:
				break ;
			}
		}
		/**
		 *
		 */
		switch ( $this->action) {
		case	"search"	:
			$buffer	.=	$this->runMySearchShow( $_webPageNo, $_prodGrNo, $_artGrNo, $_articleNo, $_tmplName) ;
			break ;
		default	:
			$buffer	.=	"There is no valid action defined!" ;
			break ;
		}
		FDbg::end( 1, "MySearch.php", "MySearch{Page}", "run( '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		return $buffer ;
	}
	/**
	 *
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 * @return unknown
	 */
	function	runMySearchShow( $_webPageNo, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_webPageNo', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		/**
		 *
		 */
		$buffer	=	"" ;
		$myBuffer	=	"" ;
		$myTexte	=	new Texte() ;
		/**
		 * generate the basic artikel data
		 */
		FDbg::trace( 2, FDbg::mdTrcInfo1, "MySearch.php", "MySearch{Page}", "runMySearchShow( '$_webPageNo', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName'): try-ing") ;
		$this->myShopSession	=	new ShopSession() ;
		/**
		 * get the needed formats
		 * @var unknown_type
		 */
		$tmpBuffer	=	parent::run( "", "", "", "", $_tmplName) ;
		/**
		 * create the search result
		 * @var unknown_type
		 */
		$buffer	=	$this->_getMySearch( $_webPageNo, $_tmplName="") ;
		FDbg::end( 1, "MySearch.php", "MySearch{Page}", "runMySearchShow( '$_webPageNo', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		return $buffer ;
	}
	/**
	 * _getMySearch
	 *
	 * Requires:
	 * 		$this->myShopSession
	 *
	 * @param string $_webPageNo
	 * @param string $_tmplName
	 * @return string
	 */
	function	_getMySearch( $_webPageNo, $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_webPageNo', '$_tmplName')") ;
		/**
		 *
		 */
		$buffer	=	"" ;
		if ( isset( $_POST['SearchTerm'])) {
			$this->myShopSession->SearchTerm	=	$_POST['SearchTerm'] ;
			$this->myShopSession->updateColInDb( "SearchTerm") ;
			$this->SearchTerm	=	$_POST['SearchTerm'] ;
			$buffer	=	"searchresult for <b>" . $this->SearchTerm . "</b>" ;
			$this->ProductGroup	=	new ProductGroup() ;
			$this->ProductGroup->ProductGroupNo	=	"00000000" ;
			$this->Article	=	new Article() ;
			$this->Article->setIterCond( "ArticleNo like '%" . $this->SearchTerm . "%' "
										. "OR ArticleDescription1 like '%" . $this->SearchTerm . "%' ") ;
			$this->Article->setIterOrder( "ORDER BY ArticleNo ASC ") ;
			/**
			 *
			 */
			$this->ArticleList	=	"" ;
			$lastArt	=	"" ;
			$this->linesCnt	=	0 ;
			$this->ArtSalesPricesList	=	"" ;
			$lastArtGrNo	=	"" ;
			foreach( $this->Article as $ndx => $obj) {
				if ( $this->Article->Rights & $this->Customer->Rights) {
					$this->LinkedArticleNo	=	$this->interpret( $this->format["ArticleNoWLink"]) ;
					$myParts	=	explode( ".", $obj->ArticleNo) ;
					if ( isset( $myParts[1])) {
						if ( $myParts[0] != $lastArtGrNo) {
							if ( $this->linesCnt > 0) {
								$this->ArticleList	.=	$this->interpret( $this->format["ArtSalesPricesTable"]) ;
								$this->linesCnt	=	0 ;
								$this->ArtSalesPricesList	=	"" ;
							}
						}
					} else {
						if ( $this->linesCnt > 0) {
							$this->ArticleList	.=	$this->interpret( $this->format["ArtSalesPricesTable"]) ;
							$this->linesCnt	=	0 ;
							$this->ArtSalesPricesList	=	"" ;
						}
					}
					$this->ArtSalesPricesList	.=	$this->getArtSalesPricesList( $obj->ArticleNo) ;
					$lastArtGrNo	=	$myParts[0] ;
				}
			}
			if ( $this->linesCnt > 0) {
				$this->ArticleList	.=	$this->interpret( $this->format["ArtSalesPricesTable"]) ;
			}
			$this->PageSelector	=	"PageSelector" ;
			$buffer	=	$this->interpret( $this->format["SearchResult"]) ;
		} else {
			$buffer	=	"no SearchTerm " ;
		}
		FDbg::end( 1, "MySearch.php", "MySearch{Page}", "_getMySearch( '$_webPageNo', '$_tmplName')") ;
		return $buffer ;
	}
	/**
	 * getArtSalesPricesList
	 * constructs a list of all
	 * this contains all selling prices for this article
	 */
	function	getArtSalesPricesList( $_articleNo) {
		global	$lastArt ;
		$myBuffer	=	"" ;
		$this->SalesPrice	=	new VKPreisCache() ;
		/**
		 *
		 */
		try {
			$lastArticleNo	=	"" ;
			$lastArt	=	"" ;
			$linesInBuffer	=	0 ;
			$this->SalesPrice->setIterCond( "ArticleNo = '" . $_articleNo . "' AND MarketId = 'shop' ") ;
			$this->SalesPrice->setIterOrder( "ORDER BY MengeProVPE, Menge ") ;
			$this->SalesPrice->rewind() ;
			$this->iterCnt	=	$this->SalesPrice->getIterCount() ;
			$i	=	0 ;
			foreach ( $this->SalesPrice as $ndxPrc => $salesPrice) {
				$this->linesCnt++ ;
				if ( $i == 0) {
					$myBuffer	.=	$this->interpret( $this->format["ArtSalesPriceLine1st"]) ;
				} else {
					$myBuffer	.=	$this->interpret( $this->format["ArtSalesPriceLine"]) ;
				}
				$i++ ;
			}
		} catch ( Exception $e) {
			error_log( $e) ;
		}
		return $myBuffer ;
	}
}

?>
