<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * KundeNotiz - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	KundeBefreiung	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myCustomerNo="", $_myKundeNotizNo="") {
		FDbObject::__construct( "KundeBefreiung", "Id") ;
	}

}

?>
