<?php
/**
 * MyCart.php
 *
 * Class:	MyCart
 *
 * Handles display portion of the customer cart.
 * @author miskhwe
 *
 */
class	MyAccount	extends Page	{
	/**
	 *
	 */
	function	__construct( $_parent=null, $_lang="de", $_country="de") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <_parent>, '$_lang', '$_country')") ;
		parent::__construct( $_parent, $_lang, $_country) ;
		$this->custValid	=	( self::$myCustomer != null) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."(...)", "Customer := " . ( self::$myCustomer != null)) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."(...)", "Customer := " . ( $this->custValid)) ;
		if ( ! isset( $this->show)) {
			$this->show	=	"MyAccount" ;
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
	function	run( $_webPageNo, $_prodGrNo="", $_artGrNo="", $_articleNo="", $_tmplName="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')") ;
		/**
		 * process actions related to "MyAccount"
		 */
		if ( isset( $this->custUpd)) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <...>)", "action defined as 'custUpd'") ;
			if ( self::$myCustomer != null) {
				self::$myCustomer->_upd() ;
				self::$myCustomerContact->setCustomerContactNo( self::$myCustomer->CustomerNo, "001") ;
				self::$myCustomerContact->_upd() ;
			}
		} else if ( isset( $this->custUpdPwd)) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <...>)", "action defined as 'custUpdPwd'") ;
			if ( self::$myCustomer != null) {
				self::$myCustomer->updatePassword( $this->_INewPwd, $this->_INewPwdV) ;
			}
		} else if ( isset( $this->custNewPwd)) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <...>)", "action defined as 'custNewPwd'") ;
			if ( self::$myCustomer->isValid()) {
				/**
				 *
				 */
				self::$myCustomer->getPassword() ;
				self::$myCustomer->mail( "EMailKundeCustomerContact", null, "", "", "", "", "") ;
				self::$myCustomer->mail( "EMailKundeCustomerContactToSales", null, "", "", $this->siteeMail->Sales, "", "") ;
			}
		} else {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <...>)", "no action defined") ;
		}
		/**
		 *
		 */
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( '$_prodGrNo', '$_artGrNo', '$_articleNo', '$_tmplName')",
						"now we really start") ;
		$buffer	=	"" ;
		$tmpBuffer	=	parent::run( "", "", "", "", $_tmplName) ;
		$buffer	=	"" ;
		if ( self::$myCustomer != null) {
			switch ( $this->show) {
			case	"MyAccount"	:
				$this->Customer	=	self::$myCustomer ;
				$this->CustomerContact	=	self::$myCustomerContact ;
				$buffer	=	$this->interpret( $this->format["MyAccountDetails"]) ;
				break ;
			case	"MyPassword"	:
				$this->Customer	=	self::$myCustomer ;
				$this->CustomerContact	=	self::$myCustomerContact ;
				$buffer	=	$this->interpret( $this->format["MyPassword"]) ;
				break ;
			case	"MyOrders"	:
				$buffer	=	$this->getMyOrders( self::$myCustomer) ;
				break ;
			case	"MyCarts"	:
				$buffer	=	$this->getMyCarts( self::$myCustomer) ;
				break ;
			}
		}
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 *
	 * @param unknown_type $_customer
	 * @return unknown
	 */
	function	getMyOrders( $_customer) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <Cust>)") ;
		$this->CustomerOrder	=	new CustomerOrder() ;
		$this->CustomerOrder->setIterCond( "CustomerNo = '" . $_customer->CustomerNo . "' ") ;
		$this->CustomerOrder->setIterOrder( "CustomerOrderNo DESC ") ;
		$this->MyOrders	=	"" ;
		foreach( $this->CustomerOrder as $myOrdr) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <Cust>)", "CustomerOrderNo :=".$this->CustomerOrder->CustomerOrderNo) ;
			if ( $this->CustomerOrder->StatPayment == 0) {
				$this->PayInfo	=	$this->interpret( $this->format["MyOrderPayNow"]) ;
			} else {
				$this->PayInfo	=	$this->interpret( $this->format["MyOrderPaid"]) ;
			}
			$this->MyOrders	.=	$this->interpret( $this->format["MyOrder"]) ;
		}
		$buffer	=	$this->interpret( $this->format["MyOrdersTable"]) ;
		FDbg::end() ;
		return( $buffer) ;
	}
	/**
	 *
	 * @param unknown_type $_customer
	 * @return unknown
	 */
	function	getMyCarts( $_customer) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <Cust>)") ;
		$this->CustomerCart	=	new CustomerCart() ;
		$this->CustomerCart->setIterCond( "CustomerNo = '" . $_customer->CustomerNo . "' ") ;
		$this->CustomerCart->setIterOrder( "CustomerCartNo DESC ") ;
		$this->MyCarts	=	"" ;
		foreach( $this->CustomerCart as $myCart) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__."{Page}", __METHOD__."( <Cust>)", "CustomerCartNo :=".$this->CustomerCart->CustomerCartNo) ;
			$this->MyCarts	.=	$this->interpret( $this->format["MyCart"]) ;
		}
		$buffer	=	$this->interpret( $this->format["MyCartsTable"]) ;
		FDbg::end() ;
		return( $buffer) ;
	}
}

?>
