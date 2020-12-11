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
class	Carr_DHL_Paket	extends	Carr	implements	iCarrier	{
	private	static	$carrConfig	=	null ;
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
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::__construct(): begin") ;
		Carr::__construct() ;
		Carr::setName( "DHL_Paket") ;
		if ( self::$carrConfig == null) {
			self::$carrConfig	=	new Config( "modules/Carr_DHL_Paket.ini") ;
		}
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::__construct(): end") ;
	}
	/**
	 *
	 */
	function	startColli( $_veColi) {
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::startColli(): begin") ;
		$this->veColiNr	=	$_veColi->VeColiNr ;
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::startColli(): end") ;
	}
	/**
	 *
	 */
	function	endColli() {
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::endColli(): begin") ;
		if ( self::$carrConfig->DHL_Paket->removeLbl) {
			$sysCmd	=	"rm " . $this->path->Archive . "VeColi/" . $this->veColiNr . "*.pdf" ;
			FDbg::dumpL( 0x00000008, "Carr_DHL_Paket.php::Carr_DHL_Paket::scheduleColli(): sysCmd = '$sysCmd'") ;
			system( $sysCmd) ;
		}
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::endColli(): end") ;
	}
	/**
	 *
	 */
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::addItem(): begin") ;
		$newLabel	=	new AdrLbl100x150Doc() ;
		if ( $_veColiPos->Wert > 500) {
		} else {
		}
		/**
		 *
		 */
		$pdfFile	=	$this->path->Archive."VeColi/".$_veColi->VeColiNr."_".$_veColiPos->PosNr.".pdf" ;
		$newLabel->_createPDFNew( $_veColi, $_veColiPos, $_rcvr, $pdfFile) ;
		system( "lpr -P " . self::$carrConfig->DHL_Paket->printer . " " . $pdfFile . " " . self::$carrConfig->DHL_Simple->prnOpt . " ") ;
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::addItem(): end") ;
	}
	/**
	 *
	 */
	function	scheduleColli() {
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::scheduleColli(): begin") ;
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::scheduleColli(): end") ;
	}
	/**
	 *
	 */
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::getShipFee(...): begin") ;
		$myCarrOpt	=	new CarrOpt( $_veColi->Carrier) ;
		$myShipFee	=	$myCarrOpt->getShipFee( $_veColi->Receiver->Land, $_veColiPos->Gewicht) ;
		FDbg::dumpL( 0x00000008, "Carr_DHL_Paket.php::CarrOpt.::getShipFee(...): fee := $myShipFee") ;
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::getShipFee(...): end") ;
		return $myShipFee ;
	}
	/**
	 *
	 */
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::getInsFee(...): begin") ;
		$myCarrOpt	=	new CarrOpt( $_veColi->Carrier) ;
		$myInsFee	=	$myCarrOpt->getInsFee( $_veColi->Receiver->Land, $_veColiPos->Gewicht) ;
		FDbg::dumpL( 0x00000008, "Carr_DHL_Paket.php::Carr_DHL_Paket::getInsFee(...): fee := $myInsFee") ;
		FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.php::Carr_DHL_Paket::getInsFee(...): end") ;
		return $myShipFee ;
	}
}
Carr::register( "DHL_Paket") ;
?>
