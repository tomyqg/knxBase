<?php

/**
 * ShopSession.php
 * ===============
 *
 * Implements a class that will take care of all aspects of the ShopSession.
 * the only methods of this class which may be called upon are:
 *
 * 	-	__constructor, as this will try to get the data for a
 * 		possibly existing session or create a new one in case there
 * 		is none,
 * 	-	setCustomer(...), in order to attach a customer object or
 * 	-	clkear customer(), in order to dereference a customer
 *
 * provided that database connection is availeble the user can be certain
 * that the ShopSession object he gets from the __constructor is a valid
 * ShopSession.
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wimteccShop
 */
/**
 * ShopSession - ShopSession-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BShopSession which should
 * not be modified.
 *
 * @package wimteccShop
 * @subpackage ShopSession
 */
class	ShopSession	extends	FDbObject	{
	public	$SearchTermBase	=	"" ;
	public	$SearchTerm		=	"" ;
	/*
	 * The constructor can be passed an ArticleNr (ShopSessionNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		FDbg::begin( 0, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( "ShopSession", "ShopSessionId") ;
		$this->SearchTermBase	=	FTr::tr( "SearchTerm...") ;
		$this->SearchTerm	=	FTr::tr( "SearchTerm...") ;
		/**
		 *	IF there is a cookie
		 *
		 */
		if ( isset( $_COOKIE[$this->shop->cookieName])) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "cookie := '".$_COOKIE[$this->shop->cookieName]."'") ;
			$this->ShopSessionId	=	$_COOKIE[$this->shop->cookieName] ;
			$this->fetchFromDb() ;
		}
		if ( ! $this->_valid) {
			$this->getShopSessionId( "12345") ;
		}
		FDbg::end() ;
	}
	/**
	 * setCustomer
	 * ===========
	 *
	 * set the reference towards a customer in the fashion of CustomerNo and CustomerContactNo.
	 * the database record for this session will be updated
	 * in case there is a CustomerCartNo, the respective CustomerCart will be updated with the
	 * CustomerNo and the CustomerContactNo and the CustomerCart will be updated in the database
	 *
	 * @param string $_customerNo
	 * @param string $_customerContactNo
	 */
	public	function	setCustomer( $_customerNo, $_customerContactNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_customerNo', '$_customerContactNo')") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "ShopSession.php", "ShopSession", "setCustomer( ...)", "ShopSession Id." . $this->ShopSessionId) ;
		$this->CustomerNo	=	$_customerNo ;
		$this->CustomerContactNo	=	$_customerContactNo ;
		$this->updateInDb() ;
		if ( $this->CustomerCartNo != "") {
			$myCustomerCart	=	new CustomerCart( $this->CustomerCartNo) ;
			if ( $myCustomerCart->isValid()) {
				$myCustomerCart->CustomerNo	=	$this->CustomerNo ;
				$myCustomerCart->CustomerContactNo	=	$this->CustomerContactNo ;
				$myCustomerCart->updateColInDb( "CustomerNo") ;
				$myCustomerCart->updateColInDb( "CustomerContactNo") ;
			}
		}
		FDbg::end() ;
	}
	/**
	 * clearCustomer
	 * =============
	 *
	 * clear the reference towards a customer
	 * the database record for this session will be updated
	 * in case there is a CustomerCartNo, the respective CustomerCart will be updated, i.e.
	 * CustomerNo and the CustomerContactNo will be cleared and the CustomerCart
	 * will be updated in the database
	 */
		public	function	clearCustomer() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "ShopSession.php", "ShopSession", "clearCustomer()", "CustomerCartNo = '".$this->CustomerCartNo."'") ;
		if ( $this->CustomerCartNo != "") {
			$myCustomerCart	=	new CustomerCart( $this->CustomerCartNo) ;
			if ( $myCustomerCart->isValid()) {
				$myCustomerCart->CustomerNo	=	$this->CustomerNo ;
				$myCustomerCart->CustomerContactNo	=	$this->CustomerContactNo ;
				$myCustomerCart->updateColInDb( "CustomerNo") ;
				$myCustomerCart->updateColInDb( "CustomerContactNo") ;
			}
		}
		$this->CustomerNo	=	"" ;
		$this->CustomerContactNo	=	"" ;
		$this->updateInDb() ;
		FDbg::end() ;
	}
	/**
	 * getShopSessionId
	 * ================
	 *
	 * creates a new SessionId, stores this session in the database and sets the cookie
	 *
	 * @param string $_key	additional "salt" for creating the MD5 SessionId value
	 */
	private	function	getShopSessionId( $_key) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key')") ;
		$this->ShopSessionId	=	MD5( $_key . ">" . $_SERVER['REMOTE_ADDR'] . ":" . date("Y-m-d-H-i-s") ) ;
		$this->SearchTerm	=	FTr::tr( "SearchTerm...") ;
		error_log( "------------->") ;
		$this->storeInDb() ;
		error_log( "------------->") ;
		setcookie( $this->shop->cookieName, $this->ShopSessionId, $this->shop->cookieTime, "/", $this->shop->cookieDomain) ;
		FDbg::end() ;
	}
}
?>
