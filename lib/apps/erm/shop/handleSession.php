<?php
/**
 * handle all session related issues
 *	1.	make sure we have a valid session, if there is none, create one
 *	2.	pull all customer data associated with the session.
 * 		if there's no valid user ... there is none.
 *	3.	process some special conditions:
 *		3.1		condition:	Logoff is set
 *							=>	logoff customer, remove it from the session
 *		3.2		condition:	CustId and CustPwd are set
 *							=>	this requires to see if
 * 			3.2.a		condition:		Login is set
 * 			3.2.b		condition:		Login is not set
 * 										=>	new password requested
 *		3.3		condition:	custNew is set
 *							=>	Customer is registering
 *	4.	process the search criteria issue
 *
 * This file is being processed through Page.php - based on the current PageMain.xml - as
 * first part of the <BODY> tag. With the Page class being derived from the EISSCoreObject class
 * all static attributes of self::$ created here are available to all other classes being derived
 * from EISSCoreObject.
 *
 * When processing of this php-file is done, the core object (EISSCoreObject) contains static
 * references to:
 *
 * 	self::$myShopSession		a valid ShopSession record
 *	self::$myCustomer			a valid Customer (Customer) record, or null
 *	self::$myCustomerContact	a valid Customer Contact (CustomerContact) record, or null
 *
 */
error_log( "------------------------------------------------AAAAA------------------------>") ;
FDbg::begin( 0, "handleSession.php", "*", "main") ;
/**
 * load/create the session
 */
self::$myShopSession	=	new ShopSession() ;
if ( self::$myShopSession->isValid()) {
	FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerInfo.php", "CustomerInfo{Page}", "run(...)", "session is valid") ;
	setcookie( $this->shop->cookieName, self::$myShopSession->ShopSessionId, $this->shop->cookieTime, "/", $this->shop->cookieDomain) ;
	self::$myCustomer	=	new Customer( self::$myShopSession->CustomerNo) ;
	if ( self::$myCustomer->isValid()) {
		self::$myCustomerContact	=	new CustomerContact() ;
		self::$myCustomerContact->setCustomerContactNo( self::$myCustomer->CustomerNo, "001") ;
	} else {
		self::$myCustomer	=	null ;
	}
} else {
	FDbg::trace( 1, FDbg::mdTrcInfo1, "handleShopSession.php", "*", "main", "session is not valid") ;
}
/**
 * check if we need to process a login
 */
if ( isset( $_POST[ "Logoff"])) {
	FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerInfo.php", "CustomerInfo{Page}", "run(...)", "Logoff command received") ;
	self::$myShopSession->clearCustomer() ;
	self::$myCustomer	=	null ;
	self::$myCustomerContact	=	null ;
} else if ( isset( $_POST[ "CustId"]) && isset( $_POST[ "CustPwd"])) {
	self::$myCustomer	=	new Customer() ;
	self::$myCustomerContact	=	new CustomerContact() ;
	if ( isset( $_POST['Login'])) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerInfo.php", "CustomerInfo{Page}", "run(...)", "Login condition received") ;
		$myBuffer	=	$_POST['CustId'] . "/001/_" ;
		error_log( "CustomerInfo.php::CustomerInfo{Page}::run(...): '$myBuffer'") ;
		$parts	=	explode( "/", $myBuffer) ;
		self::$myCustomer->CustomerNo	=	$parts[0] ;
		self::$myCustomerContact->CustomerNo	=	$parts[0] ;
		self::$myCustomerContact->CustomerContactNo	=	$parts[1] ;
		self::$myCustomer->identifyCustomer( $_POST[ "CustPwd"]) ;
		if ( self::$myCustomer->isValid()) {
			self::$myShopSession->setCustomer( self::$myCustomer->CustomerNo, self::$myCustomerContact->CustomerContactNo) ;
		} else {
			self::$myCustomer	=	null ;
			self::$myCustomerContact	=	null ;
		}
		$this->customer	=	self::$myCustomer ;
	}
} else if ( isset( $_POST[ "NewPwd"])) {
	FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerInfo.php", "CustomerInfo{Page}", "run(...)", "New Password condition received") ;
	$myBuffer	=	$_POST['CustId'] . "/001/_" ;
	error_log( "CustomerInfo.php::CustomerInfo{Page}::run(...): '$myBuffer'") ;
	$parts	=	explode( "/", $myBuffer) ;
	$myCustomer	=	new Customer( $parts[0]) ;
	if ( $myCustomer->isValid()) {
		$cuCont	=	new CustomerContact( $myCustomer->CustomerNo, "001") ;
		$myCustomer->CuCont	=	$cuCont ;
		$myCustomer->getPassword() ;
		$myCustomer->mail( "EMailCustomerCustomerContact", null, "", "", "", "", "") ;
		$myCustomer->mail( "EMailCustomerCustomerContactToSales", null, "", "", $this->siteeMail->Sales, "", "") ;
	}
	self::$myCustomer	=	null ;
	self::$myCustomerContact	=	null ;
	setcookie( "flaschen24_newpwd", "new", $this->shop->cookieTime, "/", $this->shop->cookieDomain) ;
} else if ( isset( $_POST[ "custNew"])) {
	FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerInfo.php", "CustomerInfo{Page}", "run(...)", "Register condition received") ;
	/**
	 * little trick
	 */
	if ( strlen( $_POST['_IFirmaName1']) == 0) {
		$_POST['_IFirmaName1']	=	$myCustomer->FirmaName1 ;
	}
	/**
	 *
	 * @var unknown_type
	 */
	self::$myCustomer	=	new Customer() ;
	self::$myCustomer->newCustomerFromPOST() ;
	if ( self::$myCustomer->isValid()) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerInfo.php", "CustomerInfo{Page}", "run(...)", "customer valid after registration") ;
		self::$myShopSession->setCustomer( self::$myCustomer->CustomerNo, "001") ;
		/**
		 *
		 */
		self::$myCustomer->mail( "EMailCustomerCustomerContact", null, "", "", "", "", "") ;
		self::$myCustomer->mail( "EMailCustomerCustomerContactToSales", null, "", "", $this->siteeMail->Sales, "", "") ;
		setcookie( "flaschen24_registered", "new", $this->shop->cookieTime, "/", $this->shop->cookieDomain) ;
	}
}
/**
 *	preset the search term(s)
 *	IF a new search term has been posted
 *		store it alongside the session in the database
 */
self::$SearchTerm	=	self::$myShopSession->SearchTerm ;
self::$SearchTermBase	=	self::$myShopSession->SearchTermBase ;
if ( isset( $_POST['SearchTerm'])) {
	self::$myShopSession->SearchTerm	=	$_POST['SearchTerm'] ;
	if ( self::$myShopSession->SearchTerm == "") {
		self::$myShopSession->SearchTerm	=	self::$myShopSession->SearchTermBase ;
		$_POST['SearchTerm']	=	self::$myShopSession->SearchTerm ;
	}
	self::$myShopSession->updateColInDb( "SearchTerm") ;
	$this->SearchTerm	=	self::$myShopSession->SearchTerm ;
}
FDbg::end() ;
error_log( "-------------------------------------------------BBBBB----------------------->") ;
//die() ;
?>
