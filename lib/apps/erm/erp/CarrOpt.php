<?php
/**
 * Carr.php Base class for Customer Order (Carr)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 *
 */
class	CarrOpt	extends	FDbObject	{
	public	$myCond ;
	/**
	 *
	 */
	function	__construct( $_myCarrier='') {
		FDbg::dumpL( 0x00000001, "Carr.php::CarrOpt::__construct( $_myCarrier): begin") ;
		parent::__construct( "CarrOpt", "ID") ;
		$this->Carrier	=	$_myCarrier ;
		FDbg::dumpL( 0x00000001, "Carr.php::CarrOpt::__construct( $_myCarrier): end") ;
	}
	/**
	 *
	 * @return void
	 */
	function	firstFromDb( $_cond='') {
		FDbg::dumpL( 0x00000001, "Carr.php::CarrOpt::firstFromDb( '$_cond'): begin") ;
		if ( strlen( $_cond) > 9) {
			$this->myCond	=	$_cond ;
		} else {
			$this->myCond	=	sprintf( "CarrNr = '%s' ORDER BY PosNr, SubPosNr ", $this->CarrNr) ;
		}
		FDbg::dumpL( 0x00000001, "CarrOpt::firstFromDb(): done") ;
		parent::_firstFromDb( $this->myCond) ;
		FDbg::dumpL( 0x00000001, "Carr.php::CarrOpt::firstFromDb( '$_cond'): end") ;
	}

	/**
	 *
	 * @return void
	 */
	function	nextFromDb() {
		FDbg::dumpL( 0x00000001, "CarrOpt::CarrOpt::nextFromDb(): begin") ;
		parent::_nextFromDb() ;
		FDbg::dumpL( 0x00000001, "CarrOpt::CarrOpt::nextFromDb(): end") ;
	}

	/**
	 *
	 * @return void
	 */
	function	getNextPosNr() {
		$query	=	sprintf( "SELECT CarrOptPos FROM CarrOpt WHERE Carrier='%s' ORDER BY CarrOptPos DESC LIMIT 0, 1 ", $this->Carrier) ;
		$sqlResult	=	FDb::query( $query) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->CarrOptPos	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	add( $_key, $_id, $_val) {
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key, $_id, $_val) {
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key, $_id, $_val) {
	}
	/**
	 * 
	 */
	function	getShipFee( $_country, $_weight, $_length=0, $_width=0, $_height=0, $_gm=0) {
		$myShipFee	=	-1.0 ;
		$crit	=	"Carrier = ".$this->Carrier." AND VersOpt = 10 " .
					"AND CountryTo = '$_country' " . 
					"AND WeightMin < " . $_weight . " AND " . $_weight . " <= WeightMax " .
					"AND HeightMin < " . $_height . " AND " . $_height . " <= HeightMax " ;
		$this->firstFromDb( $crit) ;
		if ( $this->isValid()) {
			$myShipFee	=	$this->Preis ;
			FDbg::dumpL( 0x00000008, "Carr.php::CarrOpt.::getShipFee(...): fee := $myShipFee") ;
		}
		return $myShipFee ;
	}
	/**
	 * 
	 */
	function	getInsFee( $_country, $_value) {
		$myInsFee	=	-1.0 ;
		$crit	=	"Carrier = ".$this->Carrier." AND VersOpt = 20 " .
					"AND CountryTo = '$_country' " . 
					"AND ValueMin <= " . $_value . " AND " . $_value . " < ValueMax " ;
		$this->firstFromDb( $crit) ;
		if ( $this->isValid()) {
			$myInsFee	=	$this->Preis ;
			FDbg::dumpL( 0x00000008, "Carr.php::CarrOpt.::getShipFee(...): fee := $myInsFee") ;
		}
		return $myInsFee ;
	}
}
?>
