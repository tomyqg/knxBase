<?php
/**
 * Market_default.php - Implementation of simple DHL shipping (Brief, Paeckchen etc.) without DHL processing
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package	Modules_Carrier
 */
require_once( "iMarket.php") ;
/**
 * Carr - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCarr which should
 * not be modified.
 *
 * @package Application
 * @subpackage Carrier
 */
class	Market_default	extends	Market	implements	iMarket	{
	private	static	$marketConfig	=	null ;
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
		error_log( "Market_default.php::Market_default::__construct(): begin") ;
		$myConfig	=	EISSCoreObject::__getAppConfig() ;
		$myConfig->addFromAppDb( "default") ;
		$fullPath	=	$myConfig->path->Modules ;
		Market::__construct() ;
		Market::setName( "default") ;
		if ( self::$marketConfig == null) {
			self::$marketConfig	=	new Config( $fullPath."/Market_default.ini") ;
		}
		error_log( "Market_default.php::Market_default::__construct(): end") ;
	}
	/**
	 *
	 * @param VKPrice $_price
	 * @param float $_pp
	 * @param float $_msrp
	 * @return number
	 */
	function	getPrice( $_ekpr, $_pp, $_sp, $_tax, $_marginMinQ) {
		if ( $_ekpr->QuantityPerPU > $_sp->QuantityPerPU) {
			$myMQF	=	$_marginMinQ ;
		} else {
			$myMQF	=	1.0 ;
		}
		$_sp->Price	=	$_pp->Price * $_sp->QuantityPerPU * self::$marketConfig->default->addMargin * $myMQF ;
		return $_sp->Price ;
	}
	function	createDefaultSP() {
		return self::$marketConfig->default->createDefSP ;
	}
}
Market::register( "default") ;
?>
