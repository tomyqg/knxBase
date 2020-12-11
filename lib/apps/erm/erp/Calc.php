<?php
/**
 * Pay.php
 * =======
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
class	Calc	extends	FDbObject	{

	private	static	$agents	=	array() ;
	public	$valid ;
	private	$calcId ;

	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (PayNr), in which case it will automatically
	 * (try to) load the respective Payier Data from the Database
	 *
	 * @param string $_myPayNr
	 * @return void
	 */
	function	__construct( $_calcId='') {
		$this->valid	=	true ;
	}
	function	_setName( $_calcId) {
		$this->CalcId	=	$_calcId ;
		return $this->valid ;
	}
	function	setName( $_calcId) {
		if ( ! $this->_setName( $_calcId)) {
		}
	}
	/**
	 * register
	 * this function must be called by each plug-in module
	 * @param unknown_type $_name
	 */
	static	function	register( $_calcId) {
		$tmpCalc	=	new Calc() ;
		if ( $tmpCalc->_setName( $_calcId)) {
			self::$agents[$tmpCalc->CalcId]	=	$_calcId ;
		}
	}
	function	getCalcs() {
		return self::$agents ;
	}
}
/**
 * load the calculation modules
 * @var unknown_type
 */
$myConfig	=	EISSCoreObject::__getConfig() ;
$fullPath	=	$myConfig->path->Modules ;
$myDir	=	opendir( $fullPath) ;
if ( $myDir) {
	$myFiles	=	array() ;
	while (($file = readdir( $myDir)) !== false) {
		if ( strncmp( $file, "Calc_", 5) == 0 && strpos( $file, ".php") > 0 && strpos( $file, "dbInit") === false) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Calc.php", "*", "main", "Including market module '$file'") ;
			include( "modules/".$file) ;
		}
	}
}
closedir( $myDir);
?>
