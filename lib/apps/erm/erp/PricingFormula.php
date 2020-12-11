<?php

// Formelsammlung fuer MODIS GmbH web site
//
// Bedeutung der Kuerzel:
//
// VK	MODIS Verkaufspreis (in Waehrung)
// EK	MODIS Einkaufspreis (in Waehrung)
// VP	Verkaufspreis anderer Anbieter (auch: Lieferant) (in Waehrung)
// MM	Mindestmarge (in % / 100)
// SP	Spassfaktor (dimensionslos)
// HR	Haendlerrabatt (in %)
// MR	Maximaler Rabatt (in %)

// calcEK
//
// Berechnet den EinkaufsPreis wenn UVP und Haendlerrabatt bekannt sind

class	PricingFormula	{
	function	calcEK( $_vp, $_hr) {
		return $_vp * ( 100.0 - $_hr ) / 100.0 ;
	}
	/**
	 * calcVP
	 * Berechnet den VerkaufsPreis wenn Einkaufspreis und Haendlerrabatt bekannt sind
	 */	
	function	calcVP( $_ek, $_hr) {
		return ( $_ek / ( 1 - $_hr / 100.0 )) ;
	}
	/**
	 * calcHR
	 * Berechnet den Haendlerrabatt wenn Einkaufspreis und Verkaufspreis bekannt sind
	 */
	function	calcHR( $_ek, $_vp) {
		return ( 100.0 * ( 1 - $_ek / $_vp)) ;
	}
	
	/**
	 * calcMM
	 * Berechnet die Mindestmarge bei gegebenen Haendlerrabatt
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
	function	calcMODISVK( $_ekp, $_mkf, $_msrp, $_a_markup, $_s_markup, $_ekpr_markup, $_fc=1.0, $_tc=1.0) {
		FDbg::dump( "PricingFormula.php::PricingForm,ula::calcMODISVK(...): ") ;
		$mySP	=	-1.0 ;
		$myEK	=	( $_ekp * $_tc / $_fc) / $_mkf ;
		$myMSRP	=	(( $_msrp * $_tc / $_fc) * $_a_markup * $_s_markup * $_ekpr_markup ) / $_mkf ;
		$myHr	=	$this->calcHR( $myEK, $myMSRP) ;
		FDbg::dump( " hr := $myHr ") ;
		$myMm	=	$this->calcMM( $myHr) ;
		FDbg::dump( " mm := $myMm ") ;
		$mySp	=	$this->calcSP( $myMSRP, $myEK, $myMm) ;
		FDbg::dump( " sp := $mySp ") ;
		$myVk	=	$myEK * ( 1.0 + $myMm + $mySp) ;
		FDbg::dump( " vk := $myVk ") ;
		return $myVk ;
	}
	/**
	 *
	 */
	function	calcMODISMR( $_ekp, $_mkf, $_msrp, $_a_markup, $_s_markup, $_ekpr_markup, $_fc=1.0, $_tc=1.0) {
		FDbg::dump( "PricingFormula.php::PricingForm,ula::calcMODISVK(...): ") ;
		$mySP	=	-1.0 ;
		$myEK	=	( $_ekp * $_tc / $_fc) / $_mkf ;
		$myMSRP	=	(( $_msrp * $_tc / $_fc) * $_a_markup * $_s_markup * $_ekpr_markup ) / $_mkf ;
		$myHr	=	$this->calcHR( $myEK, $myMSRP) ;
		FDbg::dump( " hr := $myHr ") ;
		$myMm	=	$this->calcMM( $myHr) ;
		FDbg::dump( " mm := $myMm ") ;
		$mySp	=	$this->calcSP( $myMSRP, $myEK, $myMm) ;
		FDbg::dump( " sp := $mySp ") ;
		$myVk	=	$myEK * ( 1.0 + $myMm + $mySp) ;
		FDbg::dump( " vk := $myVk ") ;
		$myMr	=	$this->calcMR( $myEK, $myVk, $myMm, $myMSRP) ;
		return $myMr ;
	}
}
?>
