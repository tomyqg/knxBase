<?php

/**
 * Carr.php Base class for Customer Order (Carr)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Carr - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCarr which should
 * not be modified.
 *
 * @package Application
 * @subpackage Carrier
 */
class	Carr_Direktversand_000	extends	Carr	implements	iCarrier	{
	function	__construct( $_myCarrNr='') {
		Carr::__construct() ;
		Carr::setName( "Direktversand_000") ;
	}
	function	startColli( $_veColi) {	}
	function	endColli() {	}
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {	}
	function	scheduleColli() {	}
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {	}
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {	}
}
class	Carr_Sammel_910	extends	Carr	implements	iCarrier	{
	function	__construct( $_myCarrNr='') {
		Carr::__construct() ;
		Carr::setName( "Sammel_910") ;
	}
	function	startColli( $_veColi) {	}
	function	endColli() {	}
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {	}
	function	scheduleColli() {	}
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {	}
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {	}
}
class	Carr_Direktversand_990	extends	Carr	implements	iCarrier	{
	function	__construct( $_myCarrNr='') {
		Carr::__construct() ;
		Carr::setName( "Direktversand_990") ;
	}
	function	startColli( $_veColi) {	}
	function	endColli() {	}
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {	}
	function	scheduleColli() {	}
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {	}
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {	}
}
class	Carr_Spedition	extends	Carr	implements	iCarrier	{
	function	__construct( $_myCarrNr='') {
		Carr::__construct() ;
		Carr::setName( "Spedition") ;
	}
	function	startColli( $_veColi) {	}
	function	endColli() {	}
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {	}
	function	scheduleColli() {	}
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {	}
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {	}
}
class	Carr_PersAn	extends	Carr	implements	iCarrier	{
	function	__construct( $_myCarrNr='') {
		Carr::__construct() ;
		Carr::setName( "PersAn") ;
	}
	function	startColli( $_veColi) {	}
	function	endColli() {	}
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {	}
	function	scheduleColli() {	}
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {	}
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {	}
}
class	Carr_PersAb	extends	Carr	implements	iCarrier	{
	function	__construct( $_myCarrNr='') {
		Carr::__construct() ;
		Carr::setName( "PersAb") ;
	}
	function	startColli( $_veColi) {	}
	function	endColli() {	}
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {	}
	function	scheduleColli() {	}
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {	}
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {	}
}
Carr::register( "Direktversand_000") ;
Carr::register( "Sammel_910") ;
Carr::register( "Direktversand_990") ;
Carr::register( "Spedition") ;
Carr::register( "Sammel") ;
Carr::register( "PersAn") ;
Carr::register( "PersAb") ;
?>
