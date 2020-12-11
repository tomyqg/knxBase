<?php

/**
 * Carr.php Base class for Customer Order (Carr)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "modules/iCarrier.php") ;
/**
 * Carr - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCarr which should
 * not be modified.
 *
 * @package Application
 * @subpackage Carrier
 */
class	Carr_Example	extends	Carr	implements	iCarrier	{
	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (CarrNr), in which case it will automatically
	 * (try to) load the respective Carrier Data from the Database
	 *
	 * @param string $_myCarrNr
	 * @return void
	 */
	function	__construct( $_myCarrNr='') {
		Carr::__construct() ;
		Carr::setName( "Example") ;
	}
	/**
	 * 
	 */
	function	startColli( $_veColi) {
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::startColli(): begin") ;
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::startColli(): end") ;
	}
	/**
	 * 
	 */
	function	endColli() {
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::endColli(): begin") ;
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::endColli(): end") ;
	}
	/**
	 * 
	 */
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::addItem(): begin") ;
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::addItem(): end") ;
	}
	/**
	 * 
	 */
	function	scheduleColli() {
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::scheduleColli(): begin") ;
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::scheduleColli(): end") ;
	}
	/**
	 * 
	 */
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::getShipFee(): begin") ;
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::getShipFee(): end") ;
			}
	/**
	 * 
	 */
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::getInsFee(): begin") ;
		FDbg::dumpL( 0x00000001, "Carr_Example::Example::getInsFee(): end") ;
	}
}
Carr::register( "Example") ;
?>