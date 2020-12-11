<?php

/**
 * Pay.php Base class for Customer Order (Pay)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/AppObject.php") ;
require_once( "base/AppObjectCR.php") ;
require_once( "base/AppDepObject.php") ;
/**
 * Pay - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BPay which should
 * not be modified.
 *
 * @package Application
 * @subpackage Payier
 */
class	Pay	extends	FDbObject	{

	private	static	$agents	=	array() ;
	public	$valid ;
	private	$agentName ;

	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (PayNr), in which case it will automatically
	 * (try to) load the respective Payier Data from the Database
	 *
	 * @param string $_myPayNr
	 * @return void
	 */
	function	__construct( $_myPayNr='') {
		$this->valid	=	true ;
	}
	function	_setName( $_name) {
		$this->AgentName	=	$_name ;
		return $this->valid ;
	}
	function	setName( $_name) {
		if ( ! $this->_setName( $_name)) {
		}
	}
	/**
	 * register
	 * this function must be called by each plug-in module
	 * @param unknown_type $_name
	 */
	static	function	register( $_name) {
		$tmpPay	=	new Pay() ;
		if ( $tmpPay->_setName( $_name)) {
			self::$agents[$tmpPay->AgentName]	=	$_name ;
		}
	}
	/**
	 * 
	 */
	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTablePayOptAsXML() ;
		return $ret ;
	}
	/**
	 * 
	 */
	function	getXMLString() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	function	getTablePayOptAsXML() {
		$tmpObj	=	new PayOpt() ;
		$ret	=	$tmpObj->tableFromDb( "",
								"",
								"C.Payier = '" . $this->Payier . "' ",
								"ORDER BY ValidTo DESC, CountryFrom, CountryTo, VersOpt ASC, WeightMin ASC ") ;
		return $ret ;
	}
	/**
	 * 
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x01000000, "Pay.php::Pay::getDepAsXML( '$_key', $_id, '$_val'): begin") ;
		$myPayOpt	=	new PayOpt() ;
		$myPayOpt->setId( $_id) ;
		FDbg::dumpL( 0x01000000, "Pay.php::Pay::getDepAsXML( '$_key', $_id, '$_val'): end") ;
		return $myPayOpt->tableFromDb( "", "", "Id = '$_id'") ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	addPayOpt( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "Pay.php::Pay::::addPayOpt( '$_key', $_id, '$_val'): begin") ;
		try {
			$myPayOpt	=	new PayOpt() ;
			$myPayOpt->getFromPostL() ;
			$myPayOpt->Payier	=	$this->Payier ;
			$myPayOpt->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "Pay.php::Pay::::addPayOpt( '$_key', $_id, '$_val'): end") ;
		return $this->getTablePayOptAsXML() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	updPayOpt( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "Pay.php::Pay::::updPayOpt( '$_key', $_id, '$_val'): begin") ;
		try {
			$myPayOpt	=	new PayOpt() ;
			if ( $myPayOpt->setId( $_id)) {
				$myPayOpt->getFromPostL() ;
				$myPayOpt->updateInDb() ;
				$myPayOpt->getNextPosNr() ;
			} else {
				$e	=	new Exception( 'Pay.php::Pay::::updPayOpt[Id='.$_id.'] is INVALID !') ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "Pay.php::Pay::::updPayOpt( '$_key', $_id, '$_val'): end") ;
		return $this->getTablePayOptAsXML() ;
	}
}
/**
 * load the carrier modules
 * @var unknown_type
 */
$myConfig	=	EISSCoreObject::__getConfig() ;
$fullPath	=	$myConfig->path->Modules ;
$myDir	=	opendir( $fullPath) ;
if ( $myDir) {
	$myFiles	=	array() ;
	while (($file = readdir( $myDir)) !== false) {
		if ( strncmp( $file, "Pay_", 4) == 0 && strpos( $file, ".php") > 0 && strpos( $file, "dbInit") === false) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Pay.php", "*", "main", "Including payment module '$file'") ;
			include( "modules/".$file) ;
		}
	}
}
closedir( $myDir);
?>
