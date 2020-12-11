<?php
/**
 * CustomerDelivery.php Definition der Basis Klasses f�r Kunden Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerDelivery - Basis Klasse f�r Kunden Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerDelivery
 */
class	ContainerMove	extends	AppObject	{
	/**
	 *
	 */
	function	__construct() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( "ContainerMove", "Id") ;
		parent::defComplexKey( array( "CustomerNo", "MaterialNo", "DeliveryNo", "Qty")) ;
		FDbg::end() ;
	}
}
?>
