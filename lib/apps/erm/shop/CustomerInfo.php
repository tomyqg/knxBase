<?php
/**
 * CustomerInfo.php
 *
 * This modules serves as the enter for the customer login.
 * When a user enters the site there's no session and no logged in user.
 * A session will be created by either
 *
 * @author miskhwe
 *
 */
class	CustomerInfo	extends Page	{
	/**
	 *
	 */
	function	__construct( $_parent=null, $_lang="de", $_country="de") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <_parent> ,'$_lang', '$_country')") ;
		parent::__construct( $_parent, $_lang, $_country) ;
		self::$myCustomer	=	new Customer() ;
		self::$myCustomer->setKey( self::$myShopSession->CustomerNo) ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <WebPage> ,'$_prodGrNo', '$_artGrNo', ...)") ;
		/**
		 *
		 */
		if ( isset( $_GET[ 'action'])) {
			switch ( $_GET['action']) {
			case	"login"	:
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( ...)", "login attempt ...") ;
				self::$myCustomer->setKey( $_GET['CustomerNo']) ;
				if ( self::$myCustomer->isValid()) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( ...)", "CustomerNo valid ...") ;
					if ( self::$myCustomer->identify( $_GET['Password'])) {
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( ...)", "Password correct ...") ;
						self::$myShopSession->CustomerNo	=	self::$myCustomer->CustomerNo ;
						self::$myShopSession->updateInDb() ;
					} else {
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( ...)", "Password incorrect ...") ;
					}
				} else {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( ...)", "CustomerNo invalid ...") ;
				}
				break ;
			case	"logout"	:
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( ...)", "logout attempt ...") ;
				self::$myShopSession->CustomerNo	=	"" ;
				self::$myShopSession->updateInDb() ;
				self::$myCustomer	=	null ;
				break ;
			case	"newPassword"	:
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( ...)", "logout attempt ...") ;
				self::$myShopSession->CustomerNo	=	"" ;
				self::$myShopSession->updateInDb() ;
				self::$myCustomer	=	null ;
				break ;
			}
		} else {
			if ( self::$myCustomer->isValid() === false) {
				self::$myCustomer	=	null ;
			}
		}
		/**
		 *
		 */
		$xmldoc =       new DOMDocument() ;
		$xmldoc->xmlStandAlone	=	false ;		// force the <?xml version="1.0"> line
		$xmldoc->formatOutput	=	true ;		// make it readable

		$startNode	=	$xmldoc->appendChild( $xmldoc->createElement( "pagedata")) ;
		$contentNode	=	$startNode->appendChild( $xmldoc->createElement( "content")) ;

		if ( self::$myCustomer != null) {
			$customerNode	=	self::$myCustomer->_exportXML( $xmldoc, $contentNode) ;
		}
error_log( $xmldoc->saveXML( $startNode)) ;

		return $xmldoc ;
		FDbg::end() ;
	}
}
?>
