<?php
/**
 * Market_ebay_DE.php - Implementation of simple DHL shipping (Brief, Paeckchen etc.) without DHL processing
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
class	Market_ebay_DE	extends	Market	implements	iMarket	{
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
		error_log( "Market_ebay_DE.php::Market_ebay_DE::__construct(): begin") ;
		$myConfig	=	EISSCoreObject::__getAppConfig() ;
		$myConfig->addFromAppDb( "ebay_DE") ;
		$fullPath	=	$myConfig->path->Modules ;
		Market::__construct() ;
		Market::setName( "ebay_DE") ;
		if ( self::$marketConfig == null) {
			self::$marketConfig	=	new Config( $fullPath."/Market_ebay_DE.ini") ;
		}
		error_log( "Market_ebay_DE.php::Market_ebay_DE::__construct(): end") ;
	}
	/**
	 * @param	ArtikelEKPrice	$_pp
	 * @param	VKPriceCache 	$_sp
	 * @param	float			$_tax
	 * @return	float
	 */
	function	getPrice( $_ekpr, $_pp, $_sp, $_tax, $_marginMinQ) {
		FDbg::begin( 1, "Market_ebay_DE.php", "Market_ebay_DE", "getPrice( <ArtikelEKPrice>, <VKPriceCache>, $_tax)") ;
		if ( $_ekpr->QuantityPerPU > $_sp->QuantityPerPU) {
			$myMQF	=	$_marginMinQ ;
		} else {
			$myMQF	=	1.0 ;
		}
		$price	=	( $_pp->OwnSalesPrice * $_sp->QuantityPerPU * self::$marketConfig->ebay_DE->addMargin / $_pp->QuantityForPrice) ;
		$price	*=	$myMQF ;
		$price	*=	1.19 ;
		if ( $price < 10.0) {
			$price	=	$price + 0.5 ;
			$price	=	( floor( $price * 2) / 2) - 0.05 ;
		} else {
			$price	=	$price + 1.0 ;
			$price	=	( floor( $price * 1) / 1) - 0.05 ;
		}
		$price	/=	1.19 ;
		$_sp->Price	=	$price ;
		FDbg::end( 1, "Market_ebay_DE.php", "Market_ebay_DE", "getPrice( <ArtikelEKPrice>, <VKPriceCache>, $_tax)") ;
		return $price ;
	}
	function	createDefaultSP() {
		return true ;
	}
}
Market::register( "ebay_DE") ;
?>
