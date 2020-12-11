<?php
interface	iCarrier	{
	/**
	 *
	 */
	public	function	startColli( $_veColi) ;
	/**
	 *
	 */
	public	function	endColli() ;
	/**
	 *
	 * @param unknown_type $_veColiPos
	 * @param unknown_type $_veColiNr
	 * @param unknown_type $_anzahlPakete
	 * @param unknown_type $_rcvr
	 */
	public	function	addItem( $_veColi, $_veColiPos, $_rcvr) ;
	/**
	 *
	 */
	public	function	scheduleColli() ;
	/**
	 *
	 */
	public	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) ;
	/**
	 *
	 */
	public	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) ;
}
?>
