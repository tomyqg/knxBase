<?php
/**
 * MyCart.php
 *
 * Class:	MyCart
 *
 * Handles customer cart including all functions for:
 * 	- RFQ
 * 	- Ordering
 * 	- Storage and Retrieval
 *
 * Ordering flow:
 * ==============
 *
 * Ordering a cart requires multiple steps (or states).
 * State	Function
 * 	 01		Basic questions
 * 	 02		Accept terms
 * 	 03		Select payment with current cart
 * 	 031	Select payment after activating an arbitrary cart
 * 	 04		Forwarding to payment intermediate
 * 	 05		Payment finalised
 * 	 99		Distinct payment notification from intermediate
 *
 * @author miskhwe
 *
 */
class	CustomerCartData	extends Page	{
	/**
	 *
	 */
	function	__construct( $_parent=null, $_lang="de", $_country="de") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <parent>, '$_lang', '$_country')") ;
		parent::__construct( $_parent, $_lang, $_country) ;
		self::$myCustomer	=	new Customer() ;
		self::$myCustomer->setKey( self::$myShopSession->CustomerNo) ;
		if ( self::$myCustomer->isValid() === false) {
			self::$myCustomer	=	null ;
		}
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
	function	go( $_myWebPage, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		/**
		 * see if there's some action to be performed
		 */
		if ( isset( $this->itemAction)) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <...>)", "itemAction defined as '".$this->itemAction."'") ;
			$myCustomerCart	=	new CustomerCart( self::$myShopSession->CustomerCartNo) ;
			switch ( $this->itemAction) {
			case	"deleteItem"	:
				/**
				 * if the user has presses reload for this function we will get an exception
				 * since the CuCArtItem with this Id has been deleted already. Here we silently
				 * "overhear" this request.
				 */
				try {
					/**
					 * delete item
					 * update S&H cost
					 * renumber the carts items
					 */
					$myCustomerCart->_delDep( self::$myShopSession->CustomerCartNo, $this->_IId, "CustomerCartItem") ;
					$myCustomerCart->_updateHdlg() ;
					$myCustomerCart->_renumber( "CustomerCartItem") ;
					if ( $myCustomerCart->ItemCount == 0) {
						self::$myShopSession->CustomerCartNo	=	"" ;
						self::$myShopSession->CustomerCartUniqueId	=	"" ;
						self::$myShopSession->updateInDb() ;
						$myCustomerCart	=	new CustomerCart( self::$myShopSession->CustomerCartNo) ;
						$myCustomerCart->_invalidate() ;
						$buffer	.=	$this->runMyCartShow( $_myWebPage->WebPageNo, $_prodGrNo, $_artGrNo, $_articleNo, $_tmplName) ;
					}
				} catch ( Exception $e) {

				}
				break ;
			case	"moveItemUp"	:
				$myCustomerCart->_moveDep( self::$myShopSession->CustomerCartNo, $this->_IId, "CustomerCartItem", -15) ;
				break ;
			case	"moveItemDown"	:
				$myCustomerCart->_moveDep( self::$myShopSession->CustomerCartNo, $this->_IId, "CustomerCartItem", 15) ;
				break ;
			}
		}
		if ( ! isset( $this->step)) {
			$this->step	=	1 ;
		}
		/**
		 *
		 */
		switch ( $_GET['action']) {
		case	"show"	:
			$xmldoc	=	$this->runMyCartShow( $_myWebPage->WebPageNo, $_prodGrNo, $_artGrNo, $_articleNo) ;
			break ;
		case	"RFQ"	:
			$xmldoc	=	$this->runMyCartShow( $_myWebPage->WebPageNo, $_prodGrNo, $_artGrNo, $_articleNo) ;
			break ;
		case	"order"	:
			$xmldoc	=	$this->runMyCartShow( $_myWebPage->WebPageNo, $_prodGrNo, $_artGrNo, $_articleNo) ;
			break ;
		case	"store"	:
			$buffer	.=	$this->runMyCartOrder( $_myWebPage->WebPageNo) ;
			break ;
		case	"delete"	:
			self::$myShopSession->CustomerCartNo	=	"" ;
			self::$myShopSession->CustomerCartUniqueId	=	"" ;
			self::$myShopSession->updateInDb() ;
			$myCustomerCart	=	new CustomerCart( self::$myShopSession->CustomerCartNo) ;
			$myCustomerCart->_invalidate() ;
			$buffer	.=	$this->runMyCartShow( $_myWebPage->WebPageNo, $_prodGrNo, $_artGrNo, $_articleNo, $_tmplName) ;
			break ;
		}

error_log( $xmldoc->saveXML( $startNode)) ;

		FDbg::end() ;
		return $xmldoc ;
	}
	/**
	 *
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 * @return unknown
	 */
	function	runMyCartShow( $_webPageNo, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		/**
		 *
		 */
		$xmldoc =       new DOMDocument() ;
		$xmldoc->xmlStandAlone	=	false ;		// force the <?xml version="1.0"> line
		$xmldoc->formatOutput	=	true ;		// make it readable

		$startNode	=	$xmldoc->appendChild( $xmldoc->createElement( "pagedata")) ;
		$refData	=	$startNode->appendChild( $xmldoc->createElement( "refData")) ;
		$refData->setAttribute( "CurrentProdGrNo", $_prodGrNo) ;
		$opData	=	$startNode->appendChild( $xmldoc->createElement( "cartop")) ;
		if ( isset( $_GET['action'])) {
			$opData->setAttribute( "action", $_GET['action']) ;
		}
		if ( isset( $_GET['step'])) {
			$opData->setAttribute( "step", $_GET['step']) ;
		}
		$startNode->appendChild( getNavigatorXML( $xmldoc, $_prodGrNo)) ;
		$contentNode	=	$startNode->appendChild( $xmldoc->createElement( "content")) ;
		/**
		 * add Custiomer data as XML
		 */
		if ( self::$myCustomer != null) {
			$customerNode	=	self::$myCustomer->_exportXML( $xmldoc, $contentNode) ;
		}
		/**
		 * add the CustoemrCart data
		 */
		$myShopSession	=	new ShopSession() ;
		$myCustomerCart	=	new CustomerCart() ;
		$myArticle	=	new Article() ;
		$myCustomerCart->CustomerCartNo	=	$myShopSession->CustomerCartNo ;
		$myCustomerCart->CustomerCartUniqueId	=	$myShopSession->CustomerCartUniqueId ;
		$myCustomerCart->fetchFromDbByUniqueId() ;
		if ( self::$myShopSession->CustomerCartNo != "") {
			$myCustomerCart->setCustomerCartNo( self::$myShopSession->CustomerCartNo) ;
			$myCustomerCart->updateHdlg() ;
		}
		if ( $myCustomerCart->isValid()) {
			$customerCartNode	=	$myCustomerCart->_exportXML( $xmldoc, $contentNode) ;
			$customerCartItem	=	new CustomerCartItem() ;
			$customerCartItem->setIterCond( "CustomerCartNo = '" . $myCustomerCart->CustomerCartNo . "' ") ;
			$customerCartItem->setIterOrder( "ItemNo ") ;
			foreach( $customerCartItem as $ndx => $obj) {
				$customerCartItemNode	=	$obj->_exportXML( $xmldoc, $customerCartNode) ;
				$myArticle->setKey( $obj->ArticleNo) ;
				$myArticle->_exportXML( $xmldoc, $customerCartItemNode) ;
			}
		}
		FDbg::end() ;
		return $xmldoc ;
	}
	/**
	 *
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 * @return unknown
	 */
	function	runMyCartRFQ( $_webPageNo, $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		/**
		 *
		 */
		$xmldoc =       new DOMDocument() ;
		$xmldoc->xmlStandAlone	=	false ;		// force the <?xml version="1.0"> line
		$xmldoc->formatOutput	=	true ;		// make it readable

		$startNode	=	$xmldoc->appendChild( $xmldoc->createElement( "pagedata")) ;
		$refData	=	$startNode->appendChild( $xmldoc->createElement( "refData")) ;
		$refData->setAttribute( "CurrentProdGrNo", $_prodGrNo) ;

		$startNode->appendChild( getNavigatorXML( $xmldoc, $_prodGrNo)) ;
		$contentNode	=	$startNode->appendChild( $xmldoc->createElement( "content")) ;
		/**
		 * add Custiomer data as XML
		 */
		if ( self::$myCustomer != null) {
			$customerNode	=	self::$myCustomer->_exportXML( $xmldoc, $contentNode) ;
		}
		/**
		 * add the CustoemrCart data
		 */
		$myShopSession	=	new ShopSession() ;
		$myCustomerCart	=	new CustomerCart() ;
		$myArticle	=	new Article() ;
		$myCustomerCart->CustomerCartNo	=	$myShopSession->CustomerCartNo ;
		$myCustomerCart->CustomerCartUniqueId	=	$myShopSession->CustomerCartUniqueId ;
		$myCustomerCart->fetchFromDbByUniqueId() ;
		if ( self::$myShopSession->CustomerCartNo != "") {
			$myCustomerCart->setCustomerCartNo( self::$myShopSession->CustomerCartNo) ;
			$myCustomerCart->updateHdlg() ;
		}
		if ( $myCustomerCart->isValid()) {
			$customerCartNode	=	$myCustomerCart->_exportXML( $xmldoc, $contentNode) ;
			$customerCartItem	=	new CustomerCartItem() ;
			$customerCartItem->setIterCond( "CustomerCartNo = '" . $myCustomerCart->CustomerCartNo . "' ") ;
			$customerCartItem->setIterOrder( "ItemNo ") ;
			foreach( $customerCartItem as $ndx => $obj) {
				$customerCartItemNode	=	$obj->_exportXML( $xmldoc, $customerCartNode) ;
				$myArticle->setKey( $obj->ArticleNo) ;
				$myArticle->_exportXML( $xmldoc, $customerCartItemNode) ;
			}
		}
		FDbg::end() ;
		return $xmldoc ;
	}
	/**
	 *
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 * @return unknown
	 */
	function	runMyCartOrder( $_webPageNo, $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '$_tmplName')") ;
		/**
		 *
		 */
		$xmldoc =       new DOMDocument() ;
		$xmldoc->xmlStandAlone	=	false ;		// force the <?xml version="1.0"> line
		$xmldoc->formatOutput	=	true ;		// make it readable

		$startNode	=	$xmldoc->appendChild( $xmldoc->createElement( "pagedata")) ;
		$refData	=	$startNode->appendChild( $xmldoc->createElement( "refData")) ;
		$refData->setAttribute( "CurrentProdGrNo", $_prodGrNo) ;

		$startNode->appendChild( getNavigatorXML( $xmldoc, $_prodGrNo)) ;
		$contentNode	=	$startNode->appendChild( $xmldoc->createElement( "content")) ;
		/**
		 * add the CustoemrCart data
		 */
		$myShopSession	=	new ShopSession() ;
		$myCustomerCart	=	new CustomerCart() ;
		$myArticle	=	new Article() ;
		$myCustomerCart->CustomerCartNo	=	$myShopSession->CustomerCartNo ;
		$myCustomerCart->CustomerCartUniqueId	=	$myShopSession->CustomerCartUniqueId ;
		$myCustomerCart->fetchFromDbByUniqueId() ;
		if ( self::$myShopSession->CustomerCartNo != "") {
			$myCustomerCart->setCustomerCartNo( self::$myShopSession->CustomerCartNo) ;
			$myCustomerCart->updateHdlg() ;
		}
		if ( $myCustomerCart->isValid()) {
			$customerCartNode	=	$myCustomerCart->_exportXML( $xmldoc, $contentNode) ;
			$customerCartItem	=	new CustomerCartItem() ;
			$customerCartItem->setIterCond( "CustomerCartNo = '" . $myCustomerCart->CustomerCartNo . "' ") ;
			$customerCartItem->setIterOrder( "ItemNo ") ;
			foreach( $customerCartItem as $ndx => $obj) {
				$customerCartItemNode	=	$obj->_exportXML( $xmldoc, $customerCartNode) ;
				$myArticle->setKey( $obj->ArticleNo) ;
				$myArticle->_exportXML( $xmldoc, $customerCartItemNode) ;
			}
		}
		FDbg::end() ;
		return $xmldoc ;
	}
	/**
	 * this method is being called from divBodyCenterMyCart.xml during step 5 of the ordering process.
	 * step 5 means: return from payment agent with some status code
	 *
	 * called from an external payment agent
	 * @param unknown_type $_webPageNo
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 */
	function	forward( $_webPageNo, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		require_once( "Pay.php") ;		// establish the payment modules
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '<.3.>', '$_tmplName')") ;
		$buffer	=	"" ;
		if ( isset( $_POST['PaymentAgent'])) {
			$agent	=	"Pay_" . $_POST['PaymentAgent'] ;
			$pay	=	new $agent() ;
			$buffer	.=	$pay->getForm() ;
		}
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 * this method is being called from divBodyCenterMyCart.xml during step 5 of the ordering process.
	 * step 5 means: return from payment agent with some status code
	 *
	 * called from an external payment agent
	 * @param unknown_type $_webPageNo
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 */
	function	handlePayment( $_webPageNo, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		require_once( "Pay.php") ;		// establish the payment modules
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '<.3.>', '$_tmplName')") ;
		$this->Customer	=	new Customer() ;
		$this->CustomerContact	=	new CustomerContact() ;
		$myCustomerCart	=	new CustomerCart() ;
		/**
		 *
		 */
		$buffer	=	"" ;
		$myBuffer	=	"" ;
		/**
		 *
		 */
		$myPaymentAgent	=	$this->agent ;
		$myPayAgentClass	=	"Pay_" . $myPaymentAgent ;
		$myTransDetails	=	explode( ".", $_GET['transactionId']) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '<.3.>', '$_tmplName')", "step  := " . $this->step . ", agent := " . $this->agent) ;
		$myPayAgent	=	new $myPayAgentClass() ;
		$paySuccess	=	$myPayAgent->getSuccess() ;
		/**
		 *	IF payment successful
		 * 		IF CustomerCart is valid
		 * 			IF CustomerCart->CustomerNo matches Payment->CustomerNo
		 */
		if ( $paySuccess) {
			$buffer	.=	"<h3>".FTr::tr( "SHOP-ORDER-SUBMISSION-OK-HEADER")."</h3>" ;
			$buffer	.=	FTr::tr( "SHOP-ORDER-SUBMISSION-OK-BODY") ;
			/**
			 * fetch the CartNo from the returning payment redirection
			 * create the order in the FE system
			 */
			$myCartNo	=	$myTransDetails[0] ;
			$myCustomerCart->setKey( $myCartNo) ;
			if ( $myCustomerCart->isValid()) {
				if ( $myCustomerCart->CustomerNo == $myTransDetails[1] &&
						$myCustomerCart->CustomerContactNo == $myTransDetails[2] &&
						$myCustomerCart->CuOrdrNo == "") {
					/**
					 *	create the customer order (CuOrdr)
					 */
					$myCuOrdr	=	new CuOrdr() ;
					$myCuOrdr->newFromCustomerCart( '', '', $myCartNo) ;
					$myCuOrdr->fetchFromDb() ;
					$myCuOrdr->CustomerCartNo	=	$myCustomerCart->CustomerCartNo ;	// attach CustomerCartNo to the CuOrdr
					$myCuOrdr->StatPayment	=	90 ;
					$myCuOrdr->updateInDb() ;
					$myCuOrdr->updateHdlg() ;
					/**
					 * update the customer cart (CustomerCart) with the reference to the CuOrdr
					 */
					$myCustomerCart->CuOrdrNo	=	$myCuOrdr->CuOrdrNo ;
					$myCustomerCart->updateColInDb( "CuOrdrNo") ;
					$myCustomerCart->StatPayment	=	90 ;
					$myCustomerCart->updateColInDb( "StatPayment") ;
					$this->Customer->setCustomerNo( $myCuOrdr->CustomerNo) ;
					$this->CustomerContact->setCustomerContactNo( $myCuOrdr->CustomerNo, $myCuOrdr->CustomerContactNo) ;
					/**
					 * create the PDF document for this Order
					 */
					$myCuOrdrDoc	=	new CuOrdrDoc() ;
					$myCuOrdrDoc->setKey( $myCuOrdr->CuOrdrNo) ;
					$myCuOrdrDoc->_createPDF( "", "", "") ;
					$pdfName	=	$this->path->Archive . "CuOrdr/" . $myCuOrdr->CuOrdrNo . ".pdf" ;
					/**
					 * inform customer and sales department
					 */
					$myCuOrdr->mail( "CuOrdrEMail", $pdfName, "", "", "", "") ;
					$myCuOrdr->mail( "CuOrdrEMailToSales", $pdfName, "", $this->siteeMail->Sales, "", "") ;
					/**
					 * devalidate this CustomerCart
					 */
					FDbg::trace( 0, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '<.5.>')",
									"removing CustomerCart from ShopSession!") ;
					self::$myShopSession->CustomerCartNo	=	"" ;
					self::$myShopSession->CustomerCartUniqueId	=	"" ;
					self::$myShopSession->updateInDb() ;
				} else {
					FDbg::trace( 0, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '<.5.>')",
									"Customer[CustomerCart['".$myCartNo."']] is not valid!") ;
				}
			} else {
				FDbg::trace( 0, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '<.5.>')",
								"CustomerCart['".$myCartNo."'] is not valid!") ;
			}
		} else {
			$myCartNo	=	$myTransDetails[0] ;
			$myCustomerCart->setKey( $myCartNo) ;
			$myCustomerCart->_reloadCustomer() ;		// re-read it to get the customer data
			$myCustomerCart->payProblemCode	=	$myPayAgent->getCode() ;
			$myCustomerCart->payProblemDescrSys	=	$myPayAgent->getCodeDescrSys() ;
			$myCustomerCart->payProblemDescrUser	=	$myPayAgent->getCodeDescrUser() ;
			$myCustomerCart->mail( "CustomerCartEMail_PayProblem", null, "", "", "", "") ;
			$myCustomerCart->mail( "CustomerCartEMailToSales_PayProblem", null, "", $this->siteeMail->Sales, "", "") ;
			$buffer	.=	"<h3>".FTr::tr( "SHOP-PAYMENT-SUBMISSION-NOT-OK-HEADER")."</h3>" ;
			$buffer	.=	FTr::tr( "SHOP-PAYMENT-SUBMISSION-NOT-OK-BODY") ;
			$buffer	.=	FTr::tr( "SHOP-PAYMENT-NOT-OK-MESSAGE-HEADER") ;
			$buffer	.=	$payProblemDescr ;
			$buffer	.=	"<br/>" . $myPayAgent->getCodeDescrUser() ;
		}
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 * This method handles the separate Notification coming from the payment provider
	 * @param unknown_type $_webPageNo
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 */
	function	notify( $_webPageNo, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		require_once( "Pay.php") ;
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '<.3.>', '$_tmplName')") ;
		$this->Customer	=	new Customer() ;
		$this->CustomerContact	=	new CustomerContact() ;
		$myCustomerCart	=	new CustomerCart() ;
		$myCuOrdr	=	new CuOrdr() ;
		/**
		 *
		 */
		$buffer	=	"" ;
		$myBuffer	=	"" ;
		/**
		 *
		 */
		$myPaymentAgent	=	$this->agent ;
		$myPayAgentClass	=	"Pay_" . $myPaymentAgent ;
		$myTransDetails	=	explode( ".", $_GET['transactionId']) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "MyCart.php", "MyCart{Page}", "notify( '$_webPageNo', '<.3.>', '$_tmplName')", "step  := " . $this->step . ", agent := " . $this->agent) ;
		$myPayAgent	=	new $myPayAgentClass() ;
		$paySuccess	=	$myPayAgent->getSuccess() ;
		if ( $paySuccess) {
			/**
			 * create the order in the FE system
			 */
			$myCartNo	=	$myTransDetails[0] ;
			$myCustomerCart->setKey( $myCartNo) ;
			if ( $myCustomerCart->isValid()) {
				if ( $myCustomerCart->CustomerNo == $myTransDetails[1] &&
						$myCustomerCart->CustomerContactNo == $myTransDetails[2]) {
					$myCustomerCart->StatPayment	=	80 ;
					$myCustomerCart->updateColInDb( "StatPayment") ;
					if ( $myCustomerCart->CuOrdrNo == "") {
						$myCuOrdr	=	new CuOrdr() ;
						$myCuOrdr->newFromCustomerCart( '', '', $myCartNo) ;
						$myCuOrdr->fetchFromDb() ;
						$myCuOrdr->CustomerCartNo	=	$myCustomerCart->CustomerCartNo ;	// attach CustomerCartNo to the CuOrdr
						$myCuOrdr->StatPayment	=	90 ;
						$myCuOrdr->updateInDb() ;
						$myCuOrdr->updateHdlg() ;
						/**
						 * update the CustomerCart with the reference to the CuOrdr
						*/
						$myCustomerCart->CuOrdrNo	=	$myCuOrdr->CuOrdrNo ;
						$myCustomerCart->updateColInDb( "CuOrdrNo") ;
						$myCustomerCart->StatPayment	=	90 ;
						$myCustomerCart->updateColInDb( "StatPayment") ;
						$this->Customer->setCustomerNo( $myCuOrdr->CustomerNo) ;
						$this->CustomerContact->setCustomerContactNo( $myCuOrdr->CustomerNo, $myCuOrdr->CustomerContactNo) ;
						/**
						 * create the PDF document for this Order
						*/
						$myCuOrdrDoc	=	new CuOrdrDoc() ;
						$myCuOrdrDoc->setKey( $myCuOrdr->CuOrdrNo) ;
						$myCuOrdrDoc->_createPDF( "", "", "") ;
						$pdfName	=	$this->path->Archive . "CuOrdr/" . $myCuOrdr->CuOrdrNo . ".pdf" ;
						$myCuOrdr->mail( "CuOrdrEMail", null, "", "", "", "") ;
						$myCuOrdr->mail( "CuOrdrEMailToSales", null, "", $this->siteeMail->Sales, "", "") ;
					}
					/**
					 * devalidate this CustomerCart
					 */
					FDbg::trace( 0, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '<.5.>')",
									"removing CustomerCart from ShopSession!") ;
					self::$myShopSession->CustomerCartNo	=	"" ;
					self::$myShopSession->CustomerCartUniqueId	=	"" ;
					self::$myShopSession->updateInDb() ;
				} else {
					FDbg::trace( 0, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '<.5.>')",
									"Customer[CustomerCart['".$myCartNo."']] is not valid!") ;
				}
			} else {
				FDbg::trace( 0, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '<.5.>')",
								"CustomerCart['".$myCartNo."'] is not valid!") ;
			}
		} else {
		}
		/**
		 *
		 */
		FDbg::end() ;
		/**
		 * in case of notification from payment agent we shall not send anything back.
		 * the best way to achieve this is simply to die() ...
		 */
		die() ;
		return $buffer ;
	}
	/**
	 *
	 * @param unknown_type $_webPageNo
	 * @param unknown_type $_prodGrNo
	 * @param unknown_type $_artGrNo
	 * @param unknown_type $_articleNo
	 * @param unknown_type $_tmplName
	 */
	function	handleRFQ( $_webPageNo, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		$this->Customer	=	new Customer() ;
		$this->CustomerContact	=	new CustomerContact() ;
		/**
		 *
		 */
		$buffer	=	"" ;
		$myBuffer	=	"" ;
		/**
		 *
		 */
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')", "step  := " . $this->step) ;
		$buffer	.=	"<h3>".FTr::tr( "SHOP-RFQ-SUBMISSION-OK-HEADER")."</h3>" ;
		$buffer	.=	FTr::tr( "SHOP-RFQ-SUBMISSION-OK-BODY") ;
		/**
		 * create the order in the FE system
		 */
		$myCartNo	=	self::$myShopSession->CustomerCartNo ;
		$myCustomerRFQ	=	new CustomerRFQ() ;
		$myCustomerRFQ->newFromCustomerCart( '', '', $myCartNo) ;
		$myCustomerRFQ->KdRefNr	=	$this->CuRefNo ;
		$myCustomerRFQ->CustRem	=	"<div>" . $this->CustRemark . "</div>" ;
		$myCustomerRFQ->updateInDb() ;
		$myCustomerRFQ->updateHdlg() ;
//		$myCustomerRFQ->_reloadCustomer() ;		// re-read it to get the customer data
		/**
		 * create the PDF document for this RFQ
		 */
		$mycuRFQDoc	=	new CustomerRFQDoc() ;
		$mycuRFQDoc->setKey( $myCustomerRFQ->CustomerRFQNo) ;
		$mycuRFQDoc->createPDF( "", "", "") ;
		$pdfName	=	$this->path->Archive . "CustomerRFQ/" . $myCustomerRFQ->CustomerRFQNo . ".pdf" ;
		/**
		 * inform the customer and the sales department
		 */
		$myCustomerRFQ->mail( "CustomerRFQEMail", null, "", "", "", "") ;
		$myCustomerRFQ->mail( "CustomerRFQEMailToSales", null, "", $this->siteeMail->Sales, "", "") ;
		/**
		 * devalidate this CustomerCart
		 */
		FDbg::trace( 0, FDbg::mdTrcInfo1, "MyCart.php", "MyCart{Page}", "runMyCart( '<.5.>')",
						"removing CustomerCart from ShopSession!") ;
		self::$myShopSession->CustomerCartNo	=	"" ;
		self::$myShopSession->CustomerCartUniqueId	=	"" ;
		self::$myShopSession->updateInDb() ;
		/**
		 *
		 */
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 * _getMyCart
	 *
	 * Requires:
	 * 		self::$myShopSession
	 *
	 * @param string $_webPageNo
	 * @param string $_tmplName
	 * @return string
	 */
	function	_getMyCart( $_webPageNo, $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_webPageNo', '$_tmplName')") ;
		/**
		 *
		 */
		$buffer	=	"" ;
		$myArticle	=	new Article() ;
		$cart	=	new CustomerCart() ;
		if ( self::$myShopSession->CustomerCartNo != "") {
			$cart->setCustomerCartNo( self::$myShopSession->CustomerCartNo) ;
			$cart->updateHdlg() ;
		}
		if ( $cart->isValid()) {
			try {
				/**
				 * DEBUG: dump the formats
				 */
				$tmpBuffer	=	parent::run( "", "", "", "", $_tmplName) ;
//				$buffer	.=	"#####" . $tmpBuffer . "#####<br/>" ;
//				foreach ( $this->format as $ndx => $data) {
//					$buffer	.=	"Format[" . $ndx . "] := '" . $data . "' <br/>" ;
//				}
				/**
				 * generate the basic artikel data
				 */
				try {
					$myTax	=	new Tax() ;
					$cartItem	=	new CustomerCartItem() ;
					$cartItem->setIterCond( "CustomerCartNo = '" . self::$myShopSession->CustomerCartNo . "' ") ;
					$cartItem->setIterOrder( "ItemNo, SubItemNo ") ;
					$this->CartLineBlock	=	"" ;
					$this->CartTotalNet	=	0.0 ;
					$this->CartTotalGross	=	0.0 ;
					$this->CartTaxes	=	array() ;
					foreach( $cartItem as $ndx => $obj) {
						$myArticle->setArticleNo( $obj->ArticleNo) ;
						$myTax->setKey( $myArticle->TaxClass) ;
						$this->CartLine	=	$obj ;
						$this->CartLine->Fulltext	=	$myArticle->getFullText( $obj->QuantityPerPU) ;
						$this->CartLine->TaxClass	=	$myArticle->TaxClass ;
						$this->CartLine->ImageReference	=	$myArticle->ImageReference ;
						$this->CartLineBlock	.=	$this->interpret( $this->format["CartLine"]) ;
						$this->CartTotalNet	+=	$obj->TotalPrice ;
						if ( ! isset( $this->CartTaxes[ $myArticle->TaxClass]))
							$this->CartTaxes[ $myArticle->TaxClass]	=	0.0 ;
						$this->CartTaxes[ $myArticle->TaxClass]	+=	$obj->TotalPrice * $myTax->Percentage / 100.0 ;
					}
				} catch ( Exception $e) {
					error_log( $e) ;
				}
				$this->CartLineBlock	.=	$this->interpret( $this->format["CartLineNet"]) ;
				$this->CartTaxTotal	=	0.0 ;
				foreach( $this->CartTaxes as $idx => $val) {
					$myTax->setKey( $idx) ;
					$this->CartTaxClass	=	$idx . " " . $myTax->Percentage ;
					$this->CartTaxValue	=	round( $val, 2) ;
					$this->CartTaxTotal	+=	round( $val, 2) ;
					$this->CartLineBlock	.=	$this->interpret( $this->format["CartLineTax"]) ;
				}
				$cart->TotalPrice	=	$this->CartTotalNet ;
				$cart->updateColInDb( "TotalPrice") ;
				$cart->TotalTax	=	$this->CartTaxTotal ;
				$cart->updateColInDb( "TotalTax") ;
				$this->CartTaxTotal	=	round( $this->CartTaxTotal, 2) ;
				$this->CartLineBlock	.=	$this->interpret( $this->format["CartLineTotalTax"]) ;
				$this->CartTotalGross	=	$this->CartTotalNet + $this->CartTaxTotal ;
				$this->CartLineBlock	.=	$this->interpret( $this->format["CartLineGross"]) ;
				$buffer	.=	$this->interpret( $this->format["Cart"]) ;
			} catch ( Exception $e) {
				error_log( $e) ;
			}
		} else {
			$buffer	=	FTr::tr( "SHOP-INFO-CUCART-EMPTY") ;
		}
		FDbg::end() ;
		return $buffer ;
	}
}

?>
