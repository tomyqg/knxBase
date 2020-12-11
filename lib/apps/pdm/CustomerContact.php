<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * CustomerContact - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	CustomerContact	extends	AppObject	{

	/**
	 *
	 */
	function	__construct( $_myCustomerNo="", $_myCustomerContactNo="") {
		parent::__construct( "CustomerContact", "Id") ;
	}
	
}

?>
