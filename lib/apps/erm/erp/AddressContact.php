<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * AddressContact - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	AddressContact	extends	AppObject	{

	/**
	 * AddressContact constructor.
	 * @param string $_class
	 * @param string $_keyCol
	 */
	function	__construct( $_class="AddressContact", $_keyCol="Id") {
		parent::__construct( $_class, $_keyCol) ;
	}
	
	/**
	 *
	 */
	protected	function	_postInstantiate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
}
?>
