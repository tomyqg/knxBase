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
class	SystemTypeVariant	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_mySystemTypeNo="", $_mySystemTypeVariantNo="") {
		parent::__construct( "SystemTypeVariant", "Id") ;
	}
	
}

?>
