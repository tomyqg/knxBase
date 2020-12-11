<?php
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/AppObject.php") ;
class	CustomerDeliveryPaket	extends	AppDepObject	{

	/**
	 *
	 */
	const	PTCustomerDelivery	=  1 ;
	const	PTKdLeih	=  2 ;
	const	PTLfRet		=  3 ;
	const	PTLief		=  11 ;
	const	PTKunde		=  12 ;
	const	PTAdr		=  13 ;

	/**
	 *
	 */
	public	$myCond ;

	/**
	 *
	 */
	function	__construct( $_myCustomerDeliveryNo='') {
		FDbg::get()->dumpL( 0x01000000, "CustomerDeliveryPaket::__constructor") ;
		AppDepObject::__construct( "CustomerDeliveryPaket", "Id") ;
		$this->CustomerDeliveryNo	=	$_myCustomerDeliveryNo ;
	}

}

?>
