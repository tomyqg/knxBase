<?php
/**
 * Market_shop.php - Implementation of simple DHL shipping (Brief, Paeckchen etc.) without DHL processing
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
class	Market_shop	extends	Market	implements	iMarket	{
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
		error_log( "Market_shop.php::Market_shop::__construct(): begin") ;
		$myConfig	=	EISSCoreObject::__getAppConfig() ;
		$myConfig->addFromAppDb( "shop") ;
		$fullPath	=	$myConfig->path->Modules ;
		Market::__construct() ;
		Market::setName( "shop") ;
		if ( self::$marketConfig == null) {
			self::$marketConfig	=	new Config( $fullPath."/Market_shop.ini") ;
		}
		error_log( "Market_shop.php::Market_shop::__construct(): end") ;
	}
	function	getPrice( $_ekpr, $_pp, $_sp, $_tax, $_marginMinQ) {
		FDbg::begin( 1, "Market_shop.php", "Market_shop", "getPrice( <ArtikelEKPrice>, <VKPriceCache>, $_tax)") ;
		if ( $_ekpr->QuantityPerPU > $_sp->QuantityPerPU) {
			$myMQF	=	$_marginMinQ ;
		} else {
			$myMQF	=	1.0 ;
		}
		$_sp->Price	=	$_pp->OwnSalesPrice * self::$marketConfig->shop->addMargin * $myMQF ;
		$_sp->Rabatt	=	$this->_calcMR( $_pp) ;
		$_sp->Price		*=	$_sp->QuantityPerPU ;
		$_sp->Price		/=	$_pp->QuantityForPrice ;
		FDbg::end( 1, "Market_shop.php", "Market_shop", "getPrice( <ArtikelEKPrice>, <VKPriceCache>, $_tax)") ;
		return $_sp->Price ;
	}
	function	createDefaultSP() {
		return self::$marketConfig->shop->createDefSP ;
	}
	/**
	 *
	 */
	function	_calcMR( $_pp, $_fc=1.0, $_tc=1.0) {
		FDbg::dumpL( 0x0000008, "ArtikelEKPrice.php::ArtikelEKPrice::calcMODISVK(...): ") ;
		FDbg::dumpL( 0x0000008, "Purchasing price: $_pp->Price") ;
		FDbg::dumpL( 0x0000008, " qty. correction: $_pp->MKF") ;
		FDbg::dumpL( 0x0000008, "            MSRP: $_pp->SupplierSalesPrice") ;
		FDbg::dumpL( 0x0000008, "         Own SRP: $_pp->OwnSalesPrice") ;
		FDbg::dumpL( 0x0000008, "    fromCurrency: $_fc") ;
		FDbg::dumpL( 0x0000008, "      toCurrency: $_tc") ;
		$mySP	=	-1.0 ;
		$myEK	=	( $_pp->Price * $_tc / $_fc) / $_pp->MKF ;
		FDbg::dumpL( 0x0000008, "            myEK:= $myEK ") ;
		$myMSRP	=	( $_pp->OwnSalesPrice * $_tc / $_fc) / $_pp->MKF ;
		FDbg::dumpL( 0x0000008, "          myMSRP:= $myMSRP ") ;
		$myHr	=	$this->calcHR( $myEK, $myMSRP) ;
		FDbg::dumpL( 0x0000008, "              hr:= $myHr ") ;
		$myMm	=	$this->calcMM( $myHr) ;
		FDbg::dumpL( 0x0000008, "              mm:= $myMm ") ;
		$mySp	=	$this->calcSP( $myMSRP, $myEK, $myMm) ;
		FDbg::dumpL( 0x0000008, "              sp:= $mySp ") ;
		$myVk	=	$myEK * ( 1.0 + $myMm + $mySp) ;
		FDbg::dumpL( 0x0000008, "              vk:= $myVk ") ;
		$myMr	=	$this->calcMR( $myEK, $myVk, $myMm, $myMSRP) ;
		FDbg::dumpL( 0x0000008, "              mr:= $myMr ") ;
		return $myMr ;
	}
	/**
	 * calcHR
	 * return the discount for the given sales price and purchasing price
	 * @param float $_ek
	 * @param float $_hr
	 */
	function	calcHR( $_ek, $_vp) {
		return ( 100.0 * ( 1 - $_ek / $_vp)) ;
	}
	/**
	 * calcMM
	 * Berechnet die Mindestmarge bei gegebenen Haendlerrabatt
	 * return the minimum margin for the given (sales price) discount
	 * @param float $_ek
	 * @param float $_hr
	 */
	function	calcMM( $_hr) {
		return ( 0.1 + ($_hr / 250.0)) ;
	}
	/**
	 * calcSP
	 * Berechnet den "Spass"-Faktor fuer gegeben VErkaufspreis, EInkaufspreis und Mindestmarge
	 */
	function	calcSP( $_vp, $_ek, $_mm) {
		return ( ( $_vp / $_ek ) - 1.0 - $_mm ) * 0.8 ;
	}
	/**
	 * calcVK
	 * Berechnet den MODIS Verkaufspreis
	 */
	function	calcVK( $_ek, $_mm, $_sp) {
		return ( $_ek * ( 1.0 + $_mm + $_sp )) ;
	}
	/**
	 * calcMR
	 *
	 * Berechnet den maximalen Rabatt
	 *
	 * khw	2008/07/03	corrected to reflect the initial discount on the MSRP to make up the Modis VK
	 */
	function	calcMR( $_ek, $_vk, $_mm, $_vp) {
		return ( 1 - ( $_ek * ( 1 + $_mm) / $_vk) - ( ( $_vp - $_vk ) / $_vp )) ;
	}
	/**
	 * calcPR
	 *
	 * @param unknown_type $_mr
	 * @param unknown_type $_vk
	 * @param unknown_type $_n
	 */
	function	calcPR( $_mr, $_vk, $_n) {
		return ( $_mr * $_vk * $_n * ( 1 - exp( -0.05 * ( sqrt( 1 + pow( $_n, 2 ) * $_vk ) - 1.0 ) ) )) ;
	}
	/**
	 *
	 * @param $_pr
	 */
	function	_calcRR( $_pr) {
		$summePR	=	0.0 ;
		while ( list( $eiv, $ec) = each( $_pr)) {
			$summePR	+=	$_pr[ $eiv] ;
		}
		$rr	=	$summePR * ( 1 - exp( -0.1 * ( sqrt( 1 + $summePR ) - 1 ) ) ) ;
		return $rr ;
	}
	/**
	 *
	 * @param $_vk
	 * @param $_rr
	 */
	function	_calcRRP( $_vk, $_rr) {
		$summeVK	=	0.0 ;
		while ( list( $eiv, $ec) = each( $_vk)) {
			$summeVK	+=	$_vk[ $eiv] ;
		}
		$rrp	=	$_rr / $summeVK * 100 ;
		return $rrp ;
	}
	/**
	 *
	 * @param $_pr
	 */
	function	calcRR( $_pr) {
		return ( $_pr * ( 1 - exp( -0.1 * ( sqrt( 1 + $_pr ) - 1 ) ) )) ;
	}
	/**
	 *
	 * @param unknown_type $_vk
	 * @param unknown_type $_rr
	 */
	function	calcRRP( $_vk, $_rr) {
		return ( $_rr / $_vk * 100) ;
	}
	/**
	 *
	 */
	function	calcMODISVK( $_ekp, $_msrp, $_a_markup, $_s_markup, $_fc=1.0, $_tc=1.0) {
		FDbg::dumpL( 0x0000008, "ArtikelEKPrice.php::ArtikelEKPrice::calcMODISVK(...): ") ;
		$mySP	=	-1.0 ;
		$myEK	=	( $_ekp * $_tc / $_fc) / $this->MKF ;
		$myMSRP	=	(( $_msrp * $_tc / $_fc) * $_a_markup * $_s_markup * $this->Marge) / $this->MKF ;
		$myHr	=	ArtikelEKPrice::calcHR( $myEK, $myMSRP) ;
		FDbg::dumpL( 0x0000008, " hr := $myHr ") ;
		$myMm	=	$this->calcMM( $myHr) ;
		FDbg::dumpL( 0x0000008, " mm := $myMm ") ;
		$mySp	=	$this->calcSP( $myMSRP, $myEK, $myMm) ;
		FDbg::dumpL( 0x0000008, " sp := $mySp ") ;
		if ( $this->autoPriceSrc == Artikel::ARTAPRCOWN) {
			$myVk	=	$myEK * ( 1.0 + $myMm + $mySp) ;
		} else {
			$myVk	=	$myMSRP ;
		}
		FDbg::dumpL( 0x0000008, " vk := $myVk ") ;
		return $myVk ;
	}
}
Market::register( "shop") ;
?>
