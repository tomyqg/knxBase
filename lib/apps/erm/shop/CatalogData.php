<?php
/**
 *	Block name:		Catalog.php
 *	Subsystem Id.:
 *	Block no.:		CAA ... ....
 *	Version:		R1A
 *
 * Revision history
 *
 * Date			Rev.	Who		What
 * ----------------------------------------------------------------------------
 * 2015-08-20	PAx		khw		added to rev. control
 */
class	CatalogData	extends Page	{
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
	 * @param string $_prodGrNo
	 * @param string $_prodGrNo
	 * @param string $_artGrNo
	 * @param string $_articleNo
	 * @param string $_tmplName
	 * @return string
	 */
	function	go( $_myWebPage, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		$xmldoc =       new DOMDocument() ;
		$xmldoc->xmlStandAlone	=	false ;		// force the <?xml version="1.0"> line
		$xmldoc->formatOutput	=	true ;		// make it readable

		$startNode	=	$xmldoc->appendChild( $xmldoc->createElement( "pagedata")) ;
		$refData	=	$startNode->appendChild( $xmldoc->createElement( "refData")) ;
		$refData->setAttribute( "CurrentProdGrNo", $_prodGrNo) ;

		$startNode->appendChild( getNavigatorXML( $xmldoc, $_prodGrNo)) ;
		$startNode->appendChild( getCatalogXML( $xmldoc, $_prodGrNo)) ;
		error_log( $xmldoc->saveXML( $xmldoc)) ;
		return $xmldoc ;
	}
	/**
	 *
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 * @return string
	 */
	function	runArticleDetail( $_webPageName, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		/**
		 *
		 * @var unknown_type
		 */
		$buffer	=	"" ;
		$tmpBuffer	=	parent::run( "", "", "", "", $_tmplName) ;
		$tmpBuffer	=	parent::run( "", "", "", "", "article.xml") ;
		$this->ProductGroup	=	new ProductGroup( $_prodGrNo) ;
		$this->Article	=	new Article( $_articleNo) ;
		$this->ArticleText	=	new ArticleText() ;
		$this->ArticleText->setArticleNo( $this->Article->ArticleNo, $this->lang . "_" . $this->country) ;
		$this->ArtSalesPricesTable	=	$this->getArtSalesPricesTable( $_articleNo) ;
		$this->blockArticleDetail	=	"ARTICLE DETAIL" ;
		$this->blockDocuments		=	"dd" ;
		$this->ArtAttrTable			=	$this->getArtAttrTable( $_articleNo) ;
		$this->ArtAttrTableMisc		=	$this->getArtAttrTableMisc( $_articleNo) ;
		$this->blockMiscellaneous	=	"dd" ;
		$myBuffer	=	$this->interpret( $this->format["ArticleDetail"]) ;
		FDbg::end(0) ;
		return $myBuffer ;
	}
	/**
	 * getProductGroup
	 * constructs the html-block for a complete article group
	 * this contains all articles which are part of the group
	 */
	function	getProductGroup( $_prodGrNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_prodGrNo')") ;
		$myBuffer	=	"" ;
		$this->SubGrList	=	"" ;
		/**
		 *
		 */
		$prodGr	=	new ProductGroup() ;
		$subProductGroup	=	new ProductGroup() ;
		$subArticleGroup	=	new ArticleGroup() ;
		try {
			$prodGr->setProductGroupNo( $_prodGrNo) ;
			$myBuffer	.=	"<b>" . htmlentities( $prodGr->ProductGroupName, ENT_COMPAT, "UTF-8") . "</b><br/>" ;
			$prodGrComp	=	new ProductGroupItem() ;
			$prodGrComp->setIterCond( "ProductGroupNo = '" . $prodGr->ProductGroupNo . "' ") ;
			$prodGrComp->setIterOrder( "ItemNo ") ;
			foreach( $prodGrComp as $ndx => $obj) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Catalog.php", "Catalog{Page}", "getProductGroup( '$_prodGrNo')", "ItemNo := " . $obj->ItemNo . " " .$obj->CompProductGroupNo . " " . $obj->CompArticleGroupNo) ;
				if ( $obj->CompProductGroupNo != "") {
					$subProductGroup->setProductGroupNo( $obj->CompProductGroupNo) ;
					$this->SubProductGroup	=	$subProductGroup ;
					$this->SubGrList	.=	$this->interpret( $this->format["SubProductGroup"]) ;
				} else if ( $obj->CompArticleGroupNo != "") {
					$subArticleGroup->setArticleGroupNo( $obj->CompArticleGroupNo) ;
					$this->SubArticleGroup	=	$subArticleGroup ;
					$this->SubGrList	.=	$this->interpret( $this->format["SubArticleGroup"]) ;
				}
			}
			$myBuffer	.=	"<br/>" ;
//			$this->ProductGroupSalesPricesTable	=	$this->interpret( $this->format["ProductGroupSalesPricesTable"]) ;
		} catch ( Exception $e) {
			error_log( $e) ;
		}
//		$buffer	=	$myBuffer ;
		$this->ProductGroup	=	$prodGr ;
		$buffer	=	$this->interpret( $this->format["ProductGroup"]) ;
		FDbg::end( 0) ;
		return $buffer ;
	}
	/**
	 * getArticleGroup
	 * constructs the html-block for a complete article group
	 * this contains all articles which are part of the group
	 */
	function	getArticleGroup( $_artGrNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_artGrNo')") ;
		$this->ArticleGroupSalesPricesList	=	"" ;
		$buffer	=	"" ;
		/**
		 *
		 */
		$this->ArticleGroup	=	new ArticleGroup() ;
		try {
			$this->ArticleGroup->setArticleGroupNo( $_artGrNo) ;
			$artGrComp	=	new ArticleGroupItem() ;
			$artGrComp->setIterCond( "ArticleGroupNo = '" . $this->ArticleGroup->ArticleGroupNo . "' ") ;
			$artGrComp->setIterOrder( "ItemNo ") ;
			foreach( $artGrComp as $ndx => $obj) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Catalog.php", "Catalog{Page}", "getArticleGroup( '$_artGrNo')", "ArticleNo := '".$obj->CompArticleNo."'") ;
				$this->ArticleGroupSalesPricesList	.=	$this->getArtSalesPricesList( $artGrComp->CompArticleNo) ;
			}
			if ( strlen( $this->ArticleGroupSalesPricesList) > 0) {
				$this->ArticleGroupSalesPricesTable	=	$this->interpret( $this->format["ArticleGroupSalesPricesTable"]) ;
			} else {
				$this->ArticleGroupSalesPricesTable	=	"" ;
			}
		} catch ( Exception $e) {
			error_log( $e) ;
		}
		if ( strlen( $this->ArticleGroupSalesPricesTable) > 0) {
			$buffer	=	$this->interpret( $this->format["ArticleGroup"]) ;
		}
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 * getArtSalesPricesList
	 * constructs a list of all
	 * this contains all selling prices for this article
	 */
	function	getArtSalesPricesTable( $_articleNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_articleNo')") ;
		$myBuffer	=	"" ;
		/**
		 *
		 */
		try {
			$this->ArtSalesPricesList	=	$this->getArtSalesPricesList( $_articleNo) ;
			$myBuffer	=	$this->interpret( $this->format["ArtSalesPricesTable"]) ;
		} catch ( Exception $e) {
			error_log( $e) ;
		}
		FDbg::end() ;
		return $myBuffer ;
	}
	/**
	 * getArtSalesPricesList
	 * constructs a list of all
	 * this contains all selling prices for this article
	 */
	function	getArtSalesPricesList( $_articleNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_articleNo')") ;
		$myBuffer	=	"" ;
		/**
		 *
		 */
		try {
			$this->Article	=	new Article( $_articleNo) ;
			if ( $this->Article->Rights & $this->Customer->Rights) {
				if ( $this->Article->ShopSinglePage == 1) {
					$this->LinkedArticleNo	=	$this->interpret( $this->format["ArticleNoWLink"]) ;
				} else {
					$this->LinkedArticleNo	=	$this->interpret( $this->format["ArticleNoWOLink"]) ;
				}
				$this->SalesPrice	=	new FDbObject( "ArticleSalesPriceCache", "Id", "def", "v_ArticleSalesPriceCacheForShop") ;
				$this->SalesPrice->setIterCond( "ArticleNo = '" . $this->Article->ArticleNo . "' AND MarketId = 'shop' ") ;
				$this->SalesPrice->setIterOrder( "QuantityPerPU, Quantity ") ;
				foreach( $this->SalesPrice as $ndx => $obj) {
					$myBuffer	.=	$this->interpret( $this->format["ArtSalesPriceLine"]) ;
				}
			}
		} catch ( Exception $e) {
			error_log( $e) ;
		}
		FDbg::end() ;
		return $myBuffer ;
	}
	/**
	 * getArticleAttributes
	 * constructs the html-block for a single article
	 * this contains all selling prices for this article
	 */
	function	getArtAttrTable( $_articleNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_articleNo')") ;
		$myBuffer	=	"" ;
		$buffer	=	"ERROR" ;
		/**
		 *
		 */
		try {
			$this->Attribute	=	new Attribute() ;
			$this->Attribute->setIterCond( "DataTable = 'Article' AND RefNo = '" . $_articleNo . "' ") ;
			$this->Attribute->setIterOrder( "ItemNo ") ;
			$attrEntry	=	0 ;
			foreach( $this->Attribute as $ndx => $obj) {
				if ( $attrEntry & 0x01)
					$myBuffer	.=	$this->interpret( $this->format["ArtAttrLineOdd"]) ;
				else
					$myBuffer	.=	$this->interpret( $this->format["ArtAttrLineEven"]) ;
				$attrEntry++ ;
			}
			$this->ArtAttrList	=	$myBuffer ;
			$buffer	=	$this->interpret( $this->format["ArtAttrTable"]) ;
		} catch ( Exception $e) {
			error_log( $e) ;
		}
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 * getArticleAttributes
	 * constructs the html-block for a single article
	 * this contains all selling prices for this article
	 */
	function	getArtAttrTableMisc( $_articleNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_articleNo')") ;
		$myBuffer	=	"" ;
		$buffer	=	"ERROR" ;
		/**
		 *
		 */
		$myArticle	=	new Article( $_articleNo) ;
		try {
			$this->Article	=	$myArticle ;
			$buffer	=	$this->interpret( $this->format["ArtAttrTableMisc"]) ;
		} catch ( Exception $e) {
			error_log( $e) ;
		}
		FDbg::end() ;
		return $buffer ;
	}
}


?>
