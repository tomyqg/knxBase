<?php

class	CustomerCartInfo	extends	Page	{
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		/**
		 *
		 */
		$mySysConfig	=	EISSCoreObject::__getSysConfig() ;
		$myAppConfig	=	EISSCoreObject::__getAppConfig() ;
		$myAppConfig->addFromAppDb( $mySysConfig->classId) ;
		/**
		 * create a "CustomerCart"
		 * IF "ArticleNo" is defined we need to add to "CustomerCart" THEN
		 *	IF "CustomerCart" already exists (cookie?) THEN
		 *		fetch CustomerCart from db
		 *		IF "CustomerCart" exists in db
		 *			get CustomerCart from db
		 *			...
		 *		ELSE "CustomerCart" did not exist in db
		 *			create CustomerCart
		 *			... this should make us suspicious ...
		 *		END
		 *	ELSE
		 *		create CustomerCart
		 *	END
		 */
		$myCustomerCart	=	new CustomerCart() ;
		if ( isset( $_GET['_IArticleNo'])) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "taking path 1") ;
			error_log( "mzinfo.php::main: cookie name '" . $myAppConfig->shop->cookieName . "' ") ;
			error_log( "mzinfo.php::main: ArticleNo is defined") ;
			if ( isset( $_COOKIE[ $myAppConfig->shop->cookieName])) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "taking path 1.1") ;
				FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "ShopSession Cookie ist definiert") ;
				if ( strcmp( self::$myShopSession->CustomerCartNo, "") != 0) {
					FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "taking path 1.1.1") ;
					setcookie( $myAppConfig->shop->cookieName, self::$myShopSession->ShopSessionId, $myAppConfig->shop->cookieTime, "/", $myAppConfig->shop->cookieDomain) ;
					$myCustomerCart->CustomerCartUniqueId	=	self::$myShopSession->CustomerCartUniqueId ;
					$myCustomerCart->fetchFromDbByUniqueId() ;
				} else {
					FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "taking path 1.1.2") ;
					$myCustomerCart->newCustomerCart() ;
					$myCustomerCart->CustomerCartUniqueId	=	$myCustomerCart->CustomerCartNo . ">" . $_SERVER['REMOTE_ADDR'] . ":" . date( "y-m-d-H-i-s") ;
					$myCustomerCart->Datum	=	date( "Y-m-d") ;
					$myCustomerCart->CustomerNo	=	self::$myShopSession->CustomerNo ;
					$myCustomerCart->CustomerContactNo	=	self::$myShopSession->CustomerContactNo ;
					$myCustomerCart->updateInDb() ;
					self::$myShopSession->CustomerCartNo	=	$myCustomerCart->CustomerCartNo ;
					self::$myShopSession->CustomerCartUniqueId	=	$myCustomerCart->CustomerCartUniqueId ;
					self::$myShopSession->updateInDb() ;
				}
			} else {
				FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "taking path 1.2") ;
				FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "ShopSession Cookie ist nicht definiert") ;
				//
				// create the CustomerCart
				//
				$myCustomerCart->newCustomerCart() ;
				$myCustomerCart->CustomerNo	=	"000000" ;
				$myCustomerCart->CustomerContactNo	=	"000" ;
				$myCustomerCart->CustomerCartUniqueId	=	$myCustomerCart->CustomerCartNo . ">" . $_SERVER['REMOTE_ADDR'] . ":" . date( "y-m-d-H-i-s") ;
				$myCustomerCart->Datum	=	date( "Y-m-d") ;
				$myCustomerCart->updateInDb() ;
				//
				// create the session
				//
				self::$myShopSession->ShopSessionId	=	MD5( $myCustomerCart->CustomerCartUniqueId) ;
				self::$myShopSession->CustomerNo	=	"000000" ;
				self::$myShopSession->CustomerContactNo	=	"000" ;
				self::$myShopSession->CustomerCartNo	=	$myCustomerCart->CustomerCartNo ;
				self::$myShopSession->CustomerCartUniqueId	=	$myCustomerCart->CustomerCartUniqueId ;
				self::$myShopSession->Datum	=	date( "Y-m-d") ;
				self::$myShopSession->Status	=	0 ;
				self::$myShopSession->storeInDb() ;
				//
				FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "setting cookie ") ;
				setcookie( $myAppConfig->shop->cookieName, self::$myShopSession->ShopSessionId, $myAppConfig->shop->cookieTime, "/", $myAppConfig->shop->cookieDomain) ;
			}
		}
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "taking path 2") ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "ArticleNo not defined, ShopSession Cookie is") ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "taking path 2.1") ;
		$myCustomerCart->CustomerCartNo	=	self::$myShopSession->CustomerCartNo ;
		$myCustomerCart->CustomerCartUniqueId	=	self::$myShopSession->CustomerCartUniqueId ;
		$myCustomerCart->fetchFromDbByUniqueId() ;
		if ( $myCustomerCart->_valid) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "taking path 2.1.1") ;
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "setting cookie ") ;
			setcookie( $myAppConfig->shop->cookieName, self::$myShopSession->ShopSessionId, $myAppConfig->shop->cookieTime, "/", $myAppConfig->shop->cookieDomain) ;
		} else {			// should never happen ...
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "taking path 2.1.2") ;
		}

		if ( $myAppConfig->cuCart->debug) {
			error_log( "ShopSession Id................: " . self::$myShopSession->ShopSessionId) ;
			error_log( "  ..CustomerCart Id.............: " . self::$myShopSession->CustomerCartUniqueId) ;
			error_log( "  ..Merkzette Nr..........: " . self::$myShopSession->CustomerCartNo) ;
			error_log( "CustomerCart Unique Id..........: " . $myCustomerCart->CustomerCartUniqueId) ;
			error_log( "  ..CustomerCart Id.............: " . $myCustomerCart->Id) ;
			error_log( "  ..CustomerCart No.............: " . $myCustomerCart->CustomerCartNo) ;
			error_log( "  ..CustomerCart Item count.....: " . $myCustomerCart->ItemCount) ;
		}

		//
		// IF Article Nummer definiert
		//	neuen Article in Liste aufnehmen
		// ENDIF
		// Anzahl Article

		if ( isset( $_GET['_IArticleNo'])) {
			$myArticle	=	new Article() ;
			$myArticle->setArticleNo( $_GET['_IArticleNo']) ;
			if ( $myArticle->_valid) {
				$myArticleSalesPrice	=	new ArticleSalesPriceCache() ;
				$myArticleSalesPrice->ArticleNo	=	$_GET['_IArticleNo'] ;
				if ( isset( $_GET['_IQuantityPerPU'])) {
					$myArticleSalesPrice->QuantityPerPU	=	$_GET['_IQuantityPerPU'] ;
				} else {
					$myArticleSalesPrice->QuantityPerPU	=	1 ;
				}
				$myArticleSalesPrice->fetchFromDbWhere( [ "ArticleNo = '$myArticleSalesPrice->ArticleNo'",
															"MarketId='".$myAppConfig->shop->defaultMarketId."'",
															"QuantityPerPU=".$myArticleSalesPrice->QuantityPerPU]) ;
				if ( $myArticleSalesPrice->isValid()) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "myArticleSalesPrice->isValid == true") ;
					$myCustomerCart->_addPos( $myArticle->ArticleNo, $myArticleSalesPrice->Id, $_GET['_IQuantity']) ;
					$myCustomerCart->_renumber( "CustomerCartItem", 10) ;
					$myCustomerCart->_restate() ;
				} else {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "myArticleSalesPrice->isValid != true") ;
					$myArticleSalesPrice->dump( "", "") ;
				}
			} else {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "can't find article!") ;
			}
		} else {
		//	echo "Articlenummer nicht besetzt ... <br /> \n" ;
		//	echo "Irgendetwas stimmt hier nicht ... <br /> \n" ;
		}

		$totalNet	=	$myCustomerCart->TotalPrice ;
		$totalGross	=	$myCustomerCart->TotalPrice * 1.19 ;
		/**
		 *
		 */
		$xmldoc =       new DOMDocument() ;
		$xmldoc->xmlStandAlone	=	false ;		// force the <?xml version="1.0"> line
		$xmldoc->formatOutput	=	true ;		// make it readable

		$startNode	=	$xmldoc->appendChild( $xmldoc->createElement( "pagedata")) ;
		$contentNode	=	$startNode->appendChild( $xmldoc->createElement( "content")) ;
		$itemCountNode	=	$contentNode->appendChild( $xmldoc->createElement( "ItemCount")) ;
		$itemCountNode->appendChild( $xmldoc->createCDATASection( "1234")) ;
		$totalNetNode	=	$contentNode->appendChild( $xmldoc->createElement( "TotalNet")) ;
		$totalNetNode->appendChild( $xmldoc->createCDATASection( "9876")) ;

		error_log( $xmldoc->saveXML( $startNode)) ;

		FDbg::end() ;
		return $xmldoc ;
	}
}


?>
