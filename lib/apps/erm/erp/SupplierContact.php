<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * SupplierContact - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	SupplierContact	extends	AddressContact	{
	
	/**
	 *
	 */
	function	__construct() {
		parent::__construct( "SupplierContact", "Id") ;
	}
}
