<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "MimeMail.php") ;
require_once( "XmlTools.php") ;
/**
 * KundeNotiz - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	KundeNotiz	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myCustomerNo="", $_myKundeNotizNo="") {
		FDbObject::__construct( "KundeNotiz", "Id") ;
		if ( strlen( $_myCustomerNo) > 0) {
			try {
				$this->setKey( array( $_myCustomerNo, $_myKundeNotizNo)) ;
			} catch ( FException $e) {
				throw $e ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
	}
}

?>
