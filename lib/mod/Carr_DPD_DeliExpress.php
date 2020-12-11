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
class	Carr_DPD_DeliExpress	extends	Carr	implements	iCarrier	{
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
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::__construct(): begin") ;
		Carr::__construct() ;
		Carr::setName( "DPD_DeliExpress") ;
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::__construct(): end") ;
	}
	/**
	 *
	 */
	function	startColli( $_veColi) {
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::startColli(): begin") ;
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::startColli(): end") ;
	}
	/**
	 *
	 */
	function	endColli() {
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::endColli(): begin") ;
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::endColli(): end") ;
	}
	/**
	 *
	 */
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::addItem(): begin") ;
		$newCarrierPos	=	new VeColiPosDPD() ;
		$newCarrierPos->DPD_VersArt	=	"NP" ;
		$newCarrierPos->AnzahlPakete	=	1 ;
		if ( $_veColiPos->Wert > 500) {
			$newCarrierPos->DPD_VersArt	.=	"E18++HIN" ;				// und Hoeherversicherung
			$newCarrierPos->Wert	=	$_veColiPos->Wert ;
			$newCarrierPos->HVCurr	=	"EUR" ;
			$newCarrierPos->HVInhalt	=	"Lehrgeraete" ;
		} else {
			$newCarrierPos->Wert	=	0.00 ;
			$newCarrierPos->HVCurr	=	"" ;
			$newCarrierPos->HVInhalt	=	"" ;
		}
		/**
		 *
		 */
		$newCarrierPos->VeColiNr	=	$_veColi->VeColiNr ;
		$newCarrierPos->PosNr	=	$_veColiPos->PosNr ;
		$newCarrierPos->Firma	=	$_rcvr->FirmaName1 ;
		$newCarrierPos->Name	=	$_rcvr->FirmaName2 ;
		$newCarrierPos->ZHd	=	$_rcvr->getZHd() ;
		$newCarrierPos->Adresse1	=	$_rcvr->Strasse . " " . $_rcvr->Hausnummer ;
		$newCarrierPos->PLZ	=	$_rcvr->PLZ ;
		$newCarrierPos->Ort	=	$_rcvr->Ort ;
		$newCarrierPos->Land	=	strtoupper( $_rcvr->Land) ;
		$newCarrierPos->Gewicht	=	$_veColiPos->Gewicht ;
		$newCarrierPos->EinzelDimL	=	$_veColiPos->EinzelDimL ;
		$newCarrierPos->EinzelDimB	=	$_veColiPos->EinzelDimB ;
		$newCarrierPos->EinzelDimH	=	$_veColiPos->EinzelDimH ;
		$newCarrierPos->storeInDb() ;
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::addItem(): end") ;
	}
	/**
	 *
	 */
	function	scheduleColli() {
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::scheduleColli(): begin") ;
		error_log( "Carr_DPD_DeliExpress.php::Carr_DPD_DeliExpress::scheduleColli(): end") ;
	}
	/**
	 *
	 */
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {
		error_log( "Carr_DPD_DeliExpress.php::Carr_DHL_Simple::getShipFee(...): begin") ;
		$myCarrOpt	=	new CarrOpt( $_veColi->Carrier) ;
		$myShipFee	=	$myCarrOpt->getShipFee( $_veColi->Receiver->Land, $_veColiPos->Gewicht) ;
		error_log( "Carr_DPD_DeliExpress.php::CarrOpt.::getShipFee(...): fee := $myShipFee") ;
		error_log( "Carr_DPD_DeliExpress.php::Carr_DHL_Simple::getShipFee(...): end") ;
		return $myShipFee ;
	}
	/**
	 *
	 */
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {
		error_log( "Carr_DPD_DeliExpress.php::Carr_DHL_Simple::getInsFee(...): begin") ;
		$myCarrOpt	=	new CarrOpt( $_veColi->Carrier) ;
		$myInsFee	=	$myCarrOpt->getInsFee( $_veColi->Receiver->Land, $_veColiPos->Wert) ;
		error_log( "Carr_DPD_DeliExpress.php::CarrOpt.::getInsFee(...): fee := $myInsFee") ;
		error_log( "Carr_DPD_DeliExpress.php::Carr_DHL_Simple::getInsFee(...): end") ;
		return $myInsFee ;
	}
}
Carr::register( "DPD_DeliExpress") ;
/**
 *
 * @author miskhwe
 *
 */
class	VeColiPosDPD	extends	AppObjectERM	{
	function	__construct() {
		error_log( "Carr_DPD_DeliExpress.php::VeColiPosDPD::__construct(): begin") ;
		parent::__construct( "VeColiPosDPD", "Id") ;
		error_log( "Carr_DPD_DeliExpress.php::VeColiPosDPD::__construct(): end") ;
	}
}
?>
