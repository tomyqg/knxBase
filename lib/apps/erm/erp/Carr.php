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
class	Carr	extends	AppObject	{

	private	$tmpCarrPos ;

	private	static	$carriers	=	array() ;

	function	getRCarrier() {
//		return  self::$rCarrier ;
		return  self::$carriers ;
	}

	const	VRSND	=	10 ;			// Versandkosten
	const	VRSCHNG	=	20 ;			// Versicherungskosten
	const	UBRMSL	=	30 ;			// Uebermass Laenge
	const	UBRMSG	=	40 ;			// Uebermass Gurtmass
	const	QUDFAIL	=	50 ;			// Nicht quaderfoermig
	private	static	$rVersOpt	=	array (
				Carr::VRSND		=> "Versand",
				Carr::VRSCHNG	=> "Versicherung",
				Carr::UBRMSL	=> "Uebermass Laenge",
				Carr::UBRMSG	=> "Uebermass Gurtmass",
				Carr::QUDFAIL	=> "Nicht quaderf.") ;

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
		parent::__construct( "Carrier", "Carrier") ;
		if ( strlen( $_myCarrNr) > 0) {
			$this->setCarrier( $_myCarrNr) ;
		} else {
		}
	}

	/**
	 * set the Order Number (CarrNr)
	 *
	 * Sets the order number for this object and tries to load the order from the database.
	 * If the order could successfully be loaded from the database the respective customer data
	 * as well as customer contact data is retrieved as well.
	 * If the order has a separate Invoicing address, identified through a populated field, this
	 * data is read as well.
	 * If the order has a separate Delivery address, identified through a populated field, this
	 * data is read as well.
	 *
	 * @return void
	 */
	function	setCarrier( $_myCarrier) {
		$this->Carrier	=	$_myCarrier ;
		if ( strlen( $_myCarrier) > 0) {
			$this->reload() ;
		}
		return $this->_valid ;
	}
	function	_setName( $_name) {
		return $this->fetchFromDbWhere( "CarrName = '$_name' ") ;
	}
	function	setName( $_name) {
		if ( ! $this->_setName( $_name)) {
//			include( "modules/Carr_DPD_DeliExpress_dbInit.php") ;
			if ( ! $this->fetchFromDbWhere( "CarrName = '$_name' ")) {
				$e	=	new Exception( "Carr.php::setname( '$_name'): carrier not found in db!") ;
				error_log( $e) ;
				throw $e ;
			}
		}
		return $this->_valid ;
	}
	/**
	 *
	 * @return CarrPos
	 */
	function	getFirstPos() {
		$this->tmpCarrPos	=	new CarrOpt() ;
		$this->tmpCarrPos->CarrNr	=	$this->CarrNr ;
		$this->tmpCarrPos->firstFromDb() ;
		return $this->tmpCarrPos ;
	}

	/**
	 *
	 * @return CarrPos
	 */
	function	getNextPos() {
		$this->tmpCarrPos->nextFromDb() ;
		return $this->tmpCarrPos ;
	}

	/**
	 *
	 * @return void
	 */
	function	firstFromDb( $_cond='') {
		FDbg::dumpL( 0x00000001, "Carr::firstFromDb()") ;
		if ( strlen( $_cond) > 9) {
			$this->myCond	=	$_cond ;
		} else {
		}
		BCarr::firstFromDb( $this->myCond) ;
	}

	/**
	 *
	 * @return void
	 */
	function	nextFromDb() {
		FDbg::dumpL( 0x00000001, "Carr::nextFromDb()") ;
		BCarr::nextFromDb( $this->myCond) ;
	}

	/**
	 *
	 */
	function	getRVersOpt() {	return self::$rVersOpt ;	}
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
		FDbg::dumpL( 0x00000001, "Carr.php::Carr::upd( '$_key', $_id, '$_val'): begin") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::dumpL( 0x00000001, "Carr.php::Carr::upd( '$_key', $_id, '$_val'): end") ;
		return $this->getXMLString() ;
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
	 * register
	 * this function must be called by each plug-in module
	 * @param unknown_type $_name
	 */
	static	function	register( $_name) {
		$tmpCarr	=	new Carr() ;
		if ( $tmpCarr->_setName( $_name)) {
			self::$carriers[$tmpCarr->Carrier]	=	$_name ;
		}
	}
	/**
	 *
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key="", $_id=-1, "CarrOpt") ;
		return $ret ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		switch ( $_val) {
		case	"CarrOpt"	:
			$_POST['_step']	=	$_id ;
			$tmpObj	=	new CarrOpt() ;
			$ret	=	$tmpObj->tableFromDb( "",
									"",
									"C.Carrier = '" . $this->Carrier . "' ",
									"ORDER BY ValidTo DESC, CountryFrom, CountryTo, VersOpt ASC, WeightMin ASC, WeightMax ASC ") ;
			break ;
		}
		return $ret ;
	}
	/**
	 *
	 */
	function	addDep( $_key="", $_id=-1, $_val="") {
		try {
			$myCarrOpt	=	new CarrOpt() ;
			$myCarrOpt->getFromPostL() ;
			$myCarrOpt->Carrier	=	$this->Carrier ;
			$myCarrOpt->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getTableDepAsXML( $_key, $_id, "CarrOpt") ;
	}
}
/**
 * load the carrier modules
 * @var unknown_type
 */
$myConfig	=	EISSCoreObject::__getAppConfig() ;
$fullPath	=	$myConfig->path->Modules ;
$myDir	=	opendir( $fullPath) ;
if ( $myDir) {
	$myFiles	=	array() ;
	while (($file = readdir( $myDir)) !== false) {
		if ( strncmp( $file, "Carr_", 5) == 0 && strpos( $file, ".php") > 0 && strpos( $file, "dbInit") === false) {
			include( "$fullPath/".$file) ;
		}
	}
	closedir( $myDir);
}
?>
