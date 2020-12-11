<?php

/**
 * TravEx.php Base class for Customer Order (TravEx)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */

require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/BTravEx.php") ;
require_once( "base/BTravExPos.php") ;
require_once( "User.php") ;
/**
 * TravEx - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BTravEx which should
 * not be modified.
 *
 * @package Application
 * @subpackage TravEx
 */
class	TravEx	extends	BTravEx	{

	private	$myUser ;
	private	$tmpTravExPos ;

	const	EXPTYPE_100	= 100 ;
	const	EXPTYPE_110	= 110 ;
	const	EXPTYPE_200	= 200 ;
	const	EXPTYPE_300	= 300 ;
	const	EXPTYPE_400	= 400 ;
	const	EXPTYPE_500	= 500 ;
	const	EXPTYPE_600	= 600 ;
	const	EXPTYPE_700	= 700 ;
	const	EXPTYPE_701	= 701 ;
	const	EXPTYPE_702	= 702 ;
	private	static	$rExpType	=	array (
						TravEx::EXPTYPE_100 => "Fahrtkosten",
						TravEx::EXPTYPE_110 => "Parken",
						TravEx::EXPTYPE_200 => "Taxi",
						TravEx::EXPTYPE_300 => "Hotel",
						TravEx::EXPTYPE_400 => "Flugticket",
						TravEx::EXPTYPE_500 => "Eintritt",
						TravEx::EXPTYPE_600 => "Bewirtung",
						TravEx::EXPTYPE_700 => "Tagespauschale",
						TravEx::EXPTYPE_701 => "Tagespauschale Deutschland",
						TravEx::EXPTYPE_702 => "Tagespauschale Spanien"
					) ;

	const	NEU		=  0 ;
	const	ONGOING	= 50 ;
	const	CLOSED	= 90 ;
	private	static	$rStatus	=	array (
						TravEx::NEU		=> "Neu",
						TravEx::ONGOING	=> "Ongoing",
						TravEx::CLOSED	=> "Abgeschlossen (Bezahlt)"
					) ;

	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (TravExNr), in which case it will automatically
	 * (try to) load the respective Customer Order via the base class from the Database
	 * If the order data was loaded the customer data, and customer contact data, will also be loaded
	 * from the database.
	 *
	 * @param string $_myTravExNr
	 * @return void
	 */
	function	__construct( $_myTravExNr='') {
		FDbg::get()->dump( "TravEx::__construct( '%s ')", $_myTravExNr) ;
		BTravEx::__construct() ;
		$thiy->myUser	=	NULL ;
		if ( strlen( $_myTravExNr) > 0) {
			$this->setTravExNr( $_myTravExNr) ;
		} else {
			FDbg::get()->dump( "TravEx::__construct(...): TravExNr not specified !") ;
		}
		FDbg::get()->dump( "TravEx::__construct(...) done") ;
	}

	/**
	 * set the Order Number (TravExNr)
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
	function	setTravExNr( $_myTravExNr) {
		FDbg::get()->dump( "TravEx::setTravExNr('%s')", $_myTravExNr) ;
		$this->TravExNr	=	$_myTravExNr ;
		if ( strlen( $_myTravExNr) > 0) {
			$this->reload() ;
		}
		FDbg::get()->dump( "TravEx::setTravExNr('%s') is done", $_myTravExNr) ;
	}

	/**
	 * set the Order Number (TravExNr)
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
	function	reload() {
		FDbg::get()->dump( "TravEx::reload()") ;
		$this->fetchFromDb() ;
		if ( $this->_valid == 1) {
			FDbg::get()->dump( "TravEx::reload(): TravEx is valid !") ;
			/**
			 *
			 */
			try {
				$this->myUser	=	new User( $this->UserId) ;
			} catch( Exception $e) {
				FDbg::get()->dumpF( "CuOrdr::reload(...): exception='%s'", $e->getMessage()) ;
			}
		}
		FDbg::get()->dump( "TravEx::reload() is done") ;
	}

	/**
	 * Access the customer data.
	 *
	 * @return [Kunde]
	 */
	function	getUser() {
		return $this->myUser ;
	}

	/**
	 * Checks to see whether or not the order is valid.
	 * This routine will terminate with an exception if an invalid condition is encountered.
	 * FOR each item in the order
	 *   check if item is valid
	 *
	 * @return void
	 */
	function	isValid() {
		try {
		} catch( Exception $e) {
			throw $e ;
		}
	}

	/**
	 * Create a new temporary order with the next available temp-order-nr and store
	 * the order in the database.
	 *
	 * @return void
	 */
	function	newTravEx() {

		FDbg::get()->dumpL( 0x01000000, "TravEx::newTravEx() starting") ;
		$query  =   sprintf( "TravEx_new( @status, @newTravExNr) ; ") ;
		try {
			$row    =   FDb::callProc( $query, "@newTravExNr") ;
			$this->setTravExNr( $row['@newTravExNr']) ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "CuOffr::newTravEx(): exception='%s'", $e->getMessage()) ;
		}
		FDbg::get()->dumpL( 0x01000000, "TravEx::newTravEx() done") ;
		return $this->_status ;
	}

	/**
	 * Renumber the complete order with the given step-width. If step-width is not passed the default
	 * of 1 is used as step-width.
	 *
	 * @return void
	 */
	function	renumber( $_step=10) {
		FDbg::get()->dump( "TravEx::renumber(_step=%d)", $_step) ;
		$query	=	sprintf( "TravEx_renum( @status, '%s', %d) ; ", $this->TravExNr, $_step) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "TravEx::renumber(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * Führt eine Neuberechnung aller abhängigen Werte der Kundenbestellung durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: TravEx_restate( @status, <TravExNr>).
	 *
	 * @return void
	 */
	function	restate() {
		FDbg::get()->dump( "TravEx::restate()") ;
		$query	=	sprintf( "TravEx_restate( @status, '%s') ; ", $this->TravExNr) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
			$this->reload() ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "TravEx::restate(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * movePosUp
	 * @return TravExPos
	 */
	function	movePosUp( $_id) {
		FDbg::get()->dump( "TravEx::movePosUp(%s)", $_id) ;
		try {
			$tmpTravExPos	=	new TravExPos() ;
			$tmpTravExPos->Id	=	$_id ;
			$tmpTravExPos->fetchFromDbById() ;
			if ( $tmpTravExPos->_valid == 1) {
				FDbg::get()->dump( "TravEx::movePosUp(...), refers to PosNr=%d", $tmpTravExPos->PosNr) ;
				$query	=	sprintf( "TravEx_movePosUp( @status, '%s', %d) ; ", $this->TravExNr, $tmpTravExPos->PosNr) ;
				$sqlRows	=	FDb::callProc( $query) ;
				$this->renumber() ;
				$tmpTravExPos->reload() ;
			} else {
				throw new Exception( 'TravEx::movePosUp: Id='.$_id.' is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::get()->dumpL( 1, "TravEx::movePosUp(%s), done", $_id) ;
		return $tmpTravExPos ;
	}

	/**
	 * movePosDown
	 * @return TravExPos
	 */
	function	movePosDown( $_id) {
		FDbg::get()->dump( "TravEx::movePosDown(%s)", $_id) ;
		try {
			$this->tmpTravExPos	=	new TravExPos() ;
			$this->tmpTravExPos->Id	=	$_id ;
			$this->tmpTravExPos->fetchFromDbById() ;
			if ( $this->tmpTravExPos->_valid == 1) {
				FDbg::get()->dump( "TravEx::movePosDown(...), refers to PosNr=%d", $this->tmpTravExPos->PosNr) ;
				$query	=	sprintf( "TravEx_movePosDown( @status, '%s', %d) ; ", $this->TravExNr, $this->tmpTravExPos->PosNr) ;
				$sqlRows	=	FDb::callProc( $query) ;
				$this->renumber() ;
				$this->tmpTravExPos->reload() ;
			} else {
				throw new Exception( 'WTA:TravEx:0000:TravExPos[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::get()->dump( "TravEx::movePosDown(%s), done", $_id) ;
		return $this->tmpTravExPos ;
	}

	/**
	 * decPos
	 * @return TravExPos
	 */
	function	decPos( $_id) {
		FDbg::get()->dump( "TravEx::decPos(%s)", $_id) ;
		try {
			$this->tmpTravExPos	=	new TravExPos() ;
			$this->tmpTravExPos->Id	=	$_id ;
			$this->tmpTravExPos->fetchFromDbById() ;
			if ( $this->tmpTravExPos->_valid == 1) {
				FDbg::get()->dump( "TravEx::decPos(...), refers to PosNr=%d", $this->tmpTravExPos->PosNr) ;
				$query	=	sprintf( "TravEx_decPosQty( @status, '%s', %d) ; ", $this->TravExNr, $this->tmpTravExPos->PosNr) ;
				$sqlRows	=	FDb::callProc( $query) ;
				$this->tmpTravExPos->reload() ;
			} else {
				throw new Exception( 'WTA:TravEx:0000:TravExPos[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::get()->dump( "TravEx::decPos(%s), done", $_id) ;
		return $this->tmpTravExPos ;
	}

	/**
	 * incPos
	 * @return TravExPos
	 */
	function	incPos( $_id) {
		FDbg::get()->dump( "TravEx::incPos(%s)", $_id) ;
		try {
			$this->tmpTravExPos	=	new TravExPos() ;
			$this->tmpTravExPos->Id	=	$_id ;
			$this->tmpTravExPos->fetchFromDbById() ;
			if ( $this->tmpTravExPos->_valid == 1) {
				FDbg::get()->dump( "TravEx::incPos(...), refers to PosNr=%d", $this->tmpTravExPos->PosNr) ;
				$query	=	sprintf( "TravEx_incPosQty( @status, '%s', %d) ; ", $this->TravExNr, $this->tmpTravExPos->PosNr) ;
				$sqlRows	=	FDb::callProc( $query) ;
				$this->tmpTravExPos->reload() ;
			} else {
				throw new Exception( 'WTA:TravEx:0000:TravExPos[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::get()->dump( "TravEx::incPos(%s), done", $_id) ;
		return $this->tmpTravExPos ;
	}

	/**
	 * delPos
	 * @return TravExPos
	 */
	function	delPos( $_id) {
		try {
			$this->tmpTravExPos	=	new TravExPos() ;
			$this->tmpTravExPos->Id	=	$_id ;
			$this->tmpTravExPos->fetchFromDbById() ;
			if ( $this->tmpTravExPos->_valid == 1) {
				$query	=	sprintf( "TravEx_delPosten( @status, '%s', %d) ; ", $this->TravExNr, $this->tmpTravExPos->PosNr) ;
				$sqlRows	=	FDb::callProc( $query) ;
				$this->renumber() ;
			} else {
				throw( 'WTA:TravEx:0000:TravExPos is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
	}

	/**
	 * updAddText
	 * @return TravExPos
	 */
	function	updAddText( $_id, $_text) {
		FDbg::get()->dump( "TravEx::updAddText(%d, '%s')", $_id, $_text) ;
		try {
			$this->tmpTravExPos	=	new TravExPos() ;
			$this->tmpTravExPos->Id	=	$_id ;
			$this->tmpTravExPos->fetchFromDbById() ;
			if ( $this->tmpTravExPos->_valid == 1) {
				FDbg::get()->dump( "TravEx::updAddText: refers to PosNr=%d", $this->tmpTravExPos->PosNr) ;
				$this->tmpTravExPos->AddText	=	$_text ;
				$this->tmpTravExPos->updateInDb() ;
			} else {
				throw new Exception( 'TravEx::updAddText: TravExPos[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::get()->dump( "TravEx::updAddText(%s), done", $_id) ;
		return $this->tmpTravExPos ;
	}

	/**
	 *
	 * @return TravExPos
	 */
	function	getFirstPos() {
		$this->tmpTravExPos	=	new TravExPos() ;
		$this->tmpTravExPos->TravExNr	=	$this->TravExNr ;
		$this->tmpTravExPos->firstFromDb() ;
		return $this->tmpTravExPos ;
	}

	/**
	 *
	 * @return TravExPos
	 */
	function	getNextPos() {
		$this->tmpTravExPos->nextFromDb() ;
		return $this->tmpTravExPos ;
	}

	/**
	 * Verschicken als E-Mail
	 *
	 * @return [Artikel]
	 */
	function	sendByEMail( $_mailAdr) {
		$myText	=	date( "Ymd/Hi") . ": " . $_SESSION['UserId'] . ": verschickt als E-Mail an " . $_mailAdr . "\n" ;
		$myText	.=	$this->Rem1 ;
		$this->Rem1	=	$myText ;
		$this->DocVerschVia	=	34 ;		// ueber "Normal"-FAX
		$this->Status	=	ONGOING ;		// ueber "Normal"-FAX
		$this->updateInDb() ;
	}

	/**
	 * Verschicken per FAX
	 *
	 * @return [Artikel]
	 */
	function	sendByFAX( $_faxNr) {
		$myText	=	date( "Ymd/Hi") . ": " . $_SESSION['UserId'] . ": verschickt per FAX an " . $_faxNr . "\n" ;
		$myText	.=	$this->Rem1 ;
		$this->Rem1	=	$myText ;
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	ONGOING ;		// ueber "Normal"-FAX
		$this->updateInDb() ;
	}

	/**
	 * Verschicken per FAX
	 *
	 * @return [Artikel]
	 */
	function	sendAsPDF() {
		$myText	=	date( "Ymd/Hi") . ": " . $_SESSION['UserId'] . ": verschickt als PDF \n" ;
		$myText	.=	$this->Rem1 ;
		$this->Rem1	=	$myText ;
		$this->DocVerschVia	=	38 ;		// ueber "Normal"-FAX
		$this->Status	=	ONGOING ;		// ueber "Normal"-FAX
		$this->updateInDb() ;
	}

	/**
	 *
	 * @return void
	 */
	function	firstFromDb( $_cond='') {
		FDbg::get()->dumpL( 99, "TravEx::TravEx::firstFromDb()") ;
		if ( strlen( $_cond) > 9) {
			$this->myCond	=	$_cond ;
		} else {
		}
		BTravEx::firstFromDb( $this->myCond) ;
	}

	/**
	 *
	 * @return void
	 */
	function	nextFromDb() {
		FDbg::get()->dumpL( 99, "TravEx::TravEx::nextFromDb()") ;
		BTravEx::nextFromDb( $this->myCond) ;
	}

	/**
	 *
	 */
	function	cons() {
		$query	=	"TravEx_cons( @a, '" . $this->TravExNr . "') ; " ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
			$this->reload() ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "TravEx::cons(...): exception='%s'", $e->getMessage()) ;
		}
		return $this->_status ;
	}

	/**
	 *
	 */
	function	getRStatus() {
		return self::$rStatus ;
	}

	/**
	 *
	 */
	function	getRExpType() {
		return self::$rExpType ;
	}
		
}

/**
 *
 */
class	TravExPos	extends	BTravExPos	{

	public	$myArtikel ;
	public	$myCond ;

	/**
	 *
	 */
	function	__construct( $_myTravExNr='') {
		FDbg::get()->dumpL( 99, "TravExPos::__constructor") ;
		BTravExPos::__construct() ;
		$this->TravExNr	=	$_myTravExNr ;
	}

	/**
	 *
	 */
	function	setId( $_id=-1) {
		if ( $_id >= 0) {
			$this->Id	=	$_id ;
			$this->reload() ;
		} else {
		}
	}

	/**
	 *
	 */
	function	reload() {
		FDbg::get()->dumpL( 99, "TravExPos::reload()") ;
		$this->fetchFromDbById() ;
		FDbg::get()->dumpL( 99, "TravExPos::reload(), done") ;
	}

	/**
	 *
	 * @return void
	 */
	function	firstFromDb( $_cond='') {
		FDbg::get()->dumpL( 99, "TravExPos::firstFromDb()") ;
		if ( strlen( $_cond) > 9) {
			$this->myCond	=	$_cond ;
		} else {
			$this->myCond	=	sprintf( "TravExNr = '%s' ORDER BY PosNr, SubPosNr ", $this->TravExNr) ;
		}
		BTravExPos::firstFromDb( $this->myCond) ;
	}

	/**
	 *
	 * @return void
	 */
	function	nextFromDb() {
		FDbg::get()->dumpL( 99, "TravExPos::nextFromDb()") ;
		BTravExPos::nextFromDb( $this->myCond) ;
	}

	/**
	 *
	 * @return void
	 */
	function	getNextPosNr() {
		$query	=	sprintf( "SELECT PosNr FROM TravExPos WHERE TravExNr='%s' ORDER BY PosNr DESC LIMIT 0, 1 ", $this->TravExNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->PosNr	=	$row[0] + 1 ;
		}
		return $this->_status ;
	}
		
}

?>
