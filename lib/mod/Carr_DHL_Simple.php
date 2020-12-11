<?php
/**
 * Carr_DHL_Simple.php - Implementation of simple DHL shipping (Brief, Paeckchen etc.) without DHL processing
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package	Modules_Carrier
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
class	Carr_DHL_Simple	extends	Carr	implements	iCarrier	{
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
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::__construct(): begin") ;
		$myConfig	=	EISSCoreObject::__getConfig() ;
		$myConfig->addFromDb( "DHL_Simple") ;
		$fullPath	=	$myConfig->path->Modules ;
		Carr::__construct() ;
		Carr::setName( "DHL_Simple") ;
		if ( self::$carrConfig == null) {
			self::$carrConfig	=	new Config( $fullPath."/Carr_DHL_Simple.ini") ;
		}
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::__construct(): end") ;
	}
	/**
	 * startColli - start processing a colli, do everything needed to prepare for the scheduling
	 * @param string $_veColi	reference to a VeColi object
	 * @return bool	status, true= everything ok, false= error
	 */
	function	startColli( $_veColi) {
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::startColli(): begin") ;
		$this->veColiNr	=	$_veColi->VeColiNr ;
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::startColli(): end") ;
		return true ;
	}
	/**
	 * endColli() - end processing a coli, do everything is needed to conclude the scheduling
	 * here we can - depending on the configuration parameter - remove the pdf-label(s) from the label directory
	 * @return bool	status, true= everything ok, false= error
	 */
	function	endColli() {
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::endColli(): begin") ;
		if ( self::$carrConfig->DHL_Simple->removeLbl) {
			$sysCmd	=	"rm " . $this->path->Archive . "VeColi/" . $this->veColiNr . "*.pdf" ;
			error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::scheduleColli(): sysCmd = '$sysCmd'") ;
			system( $sysCmd) ;
		}
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::endColli(): end") ;
		return true ;
	}
	/**
	 *
	 */
	function	addItem( $_veColi, $_veColiPos, $_rcvr) {
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::addItem(): begin") ;
		$newLabel	=	new AdrLbl100x150Doc() ;
		if ( $_veColiPos->Wert > 500) {
		} else {
		}
		/*
		 * fetch the printer configuration for the printer requested by the user
		 */
		/**
		 *
		 */
		$pdfFile	=	$this->path->Archive."VeColi/".$_veColi->VeColiNr."_".$_veColiPos->PosNr.".pdf" ;
		$newLabel->_createPDFNew( $_veColi, $_veColiPos, $_rcvr, $pdfFile) ;
		$sysCmd	=	"lpr -P " . $this->ColiLblPrn->PrnName . " " . $pdfFile . " " . $this->ColiLblPrn->PrnCmd . " " ;
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::addItem(): print command '$sysCmd'") ;
		system( $sysCmd) ;
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::addItem(): end") ;
	}
	/**
	 *
	 */
	function	scheduleColli() {
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::scheduleColli(): begin") ;
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::scheduleColli(): end") ;
	}
	/**
	 *
	 */
	function	getShipFee( $_veColi, $_veColiPos, $_rcvr) {
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::getShipFee(...): begin") ;
		$myCarrOpt	=	new CarrOpt( $_veColi->Carrier) ;
		$myShipFee	=	$myCarrOpt->getShipFee( $_veColi->Receiver->Land, $_veColiPos->Gewicht, $_veColiPos->EinzelDimL, $_veColiPos->EinzelDimB, $_veColiPos->EinzelDimH) ;
		error_log( "Carr_DHL_Simple.php::CarrOpt.::getShipFee(...): fee := $myShipFee") ;
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::getShipFee(...): end") ;
		return $myShipFee ;
	}
	/**
	 *
	 */
	function	getInsFee( $_veColi, $_veColiPos, $_rcvr) {
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::getInsFee(...): begin") ;
		$myCarrOpt	=	new CarrOpt( $_veColi->Carrier) ;
		$myInsFee	=	$myCarrOpt->getInsFee( $_veColi->Receiver->Land, $_veColiPos->Gewicht) ;
		error_log( "Carr_DHL_Simple.php::CarrOpt.::getInsFee(...): fee := $myInsFee") ;
		error_log( "Carr_DHL_Simple.php::Carr_DHL_Simple::getInsFee(...): end") ;
		return $myShipFee ;
	}
}
Carr::register( "DHL_Simple") ;
?>
