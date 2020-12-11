<?php

/**
 * Pay.php Base class for Customer Order (Pay)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Pay - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BPay which should
 * not be modified.
 *
 * @package Application
 * @subpackage Payier
 */
class	Market	extends	FDbObject	{

	private	static	$agents	=	array() ;
	public	$valid ;
	private	$marketId ;

	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (PayNr), in which case it will automatically
	 * (try to) load the respective Payier Data from the Database
	 *
	 * @param string $_myPayNr
	 * @return void
	 */
	function	__construct( $_marketId='') {
		parent::__construct( "Market") ;
		$this->valid	=	true ;
	}
	function	_setName( $_marketId) {
		$this->MarketId	=	$_marketId ;
		return $this->valid ;
	}
	function	setName( $_marketId) {
		if ( ! $this->_setName( $_marketId)) {
		}
	}
	/**
	 * register
	 * this function must be called by each plug-in module
	 * @param unknown_type $_name
	 */
	static	function	register( $_marketId) {
		$tmpMarket	=	new Market() ;
		if ( $tmpMarket->_setName( $_marketId)) {
			self::$agents[$tmpMarket->MarketId]	=	$_marketId ;
		}
	}
	function	getMarkets() {
		return self::$agents ;
	}
}
/**
 * load the market modules
 * @var unknown_type
 */
error_log( "Loading market modules ............................................") ;
$myConfig	=	EISSCoreObject::__getAppConfig() ;
$fullPath	=	$myConfig->path->Modules ;
$myDir	=	opendir( $fullPath) ;
if ( $myDir) {
	$myFiles	=	array() ;
	while (($file = readdir( $myDir)) !== false) {
		if ( strncmp( $file, "Market_", 7) == 0 && strpos( $file, ".php") > 0 && strpos( $file, "dbInit") === false) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Market.php", "*", "main", "Including market module '$file'") ;
			include( "$fullPath/".$file) ;
		}
	}
}
closedir( $myDir);
?>
