<?php
/**
 * CustomerCart.php Base class for Customer Order (CustomerCart)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 *
 */
class	CustomerCartItem	extends	AppObjectERM	{
	/**
	 *
	 */
	function	__construct( $_myCustomerCartNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( "CustomerCartItem", "Id") ;
		$this->CustomerCartNo	=	$_myCustomerCartNo ;
		FDbg::end() ;
	}
	/**
	 *
	 * @return void
	 */
	function	copyFromTCustomerCartItem( $_myTCustomerCartItem) {
		$this->ItemNo	=	$_myTCustomerCartItem->ItemNo ;
		$this->SubItemNo	=	$_myTCustomerCartItem->SubItemNo ;
		$this->PosType	=	$_myTCustomerCartItem->PosType ;
		$this->ArticleNo	=	$_myTCustomerCartItem->ArticleNo ;
		$this->RevCode	=	$_myTCustomerCartItem->RevCode ;
		$this->FOC	=	0 ;
		$this->Menge	=	$_myTCustomerCartItem->Menge ;
		$this->GelieferteMenge	=	0 ;
		$this->BerechneteMenge	=	0 ;
		$this->Price	=	$_myTCustomerCartItem->Price ;
		$this->ReferencePrice	=	$_myTCustomerCartItem->ReferencePrice ;
		$this->QuantityPerPU	=	$_myTCustomerCartItem->QuantityPerPU ;
		$this->TotalPrice	=	$_myTCustomerCartItem->TotalPrice ;
		$this->MengeDirektBest	=	0 ;
		$this->MengeReserviert	=	0 ;
	}
}

?>
