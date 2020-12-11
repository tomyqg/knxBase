<?php
/**
 * SuDlvr.php - Definition of base class for supplier devlivery
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
require_once( "base/AppObjectSR.php") ;
require_once( "base/AppDepObject.php") ;
/**
 * SuDlvr - Base class for supplier delivery
 *
 * not be modified.
 *
 * @package Application
 * @subpackage SuDlvr
 */
class	SuDlvr	extends	AppObjectSR	{

	const	NEU		=	0 ;
	const	ONGOING	=	50 ;
	const	CLOSED	=	90 ;
	const	ONHOLD	=	980 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						SuDlvr::NEU		=> "open",
						SuDlvr::ONGOING	=> "ongoing",
						SuDlvr::CLOSED	=> "abgeschlossen",
						SuDlvr::ONHOLD	=> "on-hold",
						SuDlvr::CANCELLED	=> "cancelled"
					) ;
	const	DOCLS	=	"LS" ;		// order confirmation
	const	DOCMI	=	"MI" ;		// order confirmation
	private	static	$rDocType	=	array (
						SuDlvr::DOCLS	=> "Bill of delivery",
						SuDlvr::DOCMI	=> "Miscellaneous"
					) ;
	/**
	 *
	 */
	function	__construct( $_mySuDlvrNo='') {
		AppObjectSR::__construct( "SuDlvr", "SuDlvrNo") ;
		$this->setSuDlvrNo( $_mySuDlvrNo) ;
	}
	/**
	 *
	 */
	function	setSuDlvrNo( $_mySuDlvrNo) {
		$this->SuDlvrNo	=	$_mySuDlvrNo ;
		if ( strlen( $_mySuDlvrNo) > 0) {
			$this->reload() ;
		}
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PCuOrdr)
	 *
	 * @return void
	 */
	function	newFromSuOrdr( $_key="", $_id=-1, $_suOrdrNo="") {
		FDbg::begin( 1, "SuDlvr.php", "SuDlvr", "newFromSuOrdr( '$_key', $_id, '$_suOrdrNo')") ;
		$this->_newFromSuOrdr( $_key, $_id, $_suOrdrNo) ;
		$ret	=	$this->getXMLComplete() ;
		FDbg::end( 1, "SuDlvr.php", "SuDlvr", "newFromSuOrdr( '$_key', $_id, '$_suOrdrNo')") ;
		return $ret ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PCuOrdr)
	 *
	 * @return void
	 */
	function	_newFromSuOrdr( $_key="", $_id=-1, $_suOrdrNo="") {
		FDbg::begin( 1, "SuDlvr.php", "SuDlvr", "newFromSuOrdr( '$_key', $_id, '$_suOrdrNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$mySuOrdr	=	new SuOrdr( $_suOrdrNo) ;
		if ( $mySuOrdr->isValid()) {
			$this->_newFrom( "SuOrdr", $_suOrdrNo) ;		// create a new instance
//			$this->setTexte() ;
//			$this->setAnschreiben() ;
			/**
			 * update the individual items
			 */
			$mySuDlvrItem	=	new SuDlvrItem( $this->SuDlvrNo) ;
			$mySuDlvrItem->setIterCond( "SuDlvrNo = $this->SuDlvrNo ") ;
			foreach ( $mySuDlvrItem as $key => $item) {
			}
		} else {
			$e	=	new Exception( "SuDlvr.php::SuDlvr::_newFromSuOrdr(): supplier order not valid!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end( 1, "SuDlvr.php", "SuDlvr", "newFromSuOrdr( '$_key', $_id, '$_suOrdrNo')") ;
	}
	/**
	 * Create a new Customer Commission (CuComm) for this Customer Order (CuOrdr)
	 * If an open Commision exists a Reference to this open CuComm will be returned
	 * @param string $_key Number of the Customer RFQ (CuRFQ) which shall be turned into a Customer Quotation (CuQuot)
	 * @param int $_id unused
	 * @param mixed $_val unused
	 */
	function	newSuOrdr( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "SuOrdr.php", "SuOrdr", "newSuOrdr( '$_key', $_id, '$_val')") ;
		$newSuOrdr	=	new SuOrdr() ;
		$newSuOrdr->newFromSuDlvr( "", -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>SuOrdr</ObjectClass>\n<ObjectKey>$newSuOrdr->SuOrdrNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end( 1, "SuOrdr.php", "SuOrdr", "newSuOrdr( '$_key', $_id, '$_val')") ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		$actSuDlvrItem	=	new SuDlvrItem() ;
		$cond	=	"SuDlvrNo = '$this->SuDlvrNo' ORDER BY ItemNo, SubItemNo " ;
		for ( $actSuDlvrItem->_firstFromDb( $cond) ;
				$actSuDlvrItem->isValid() ;
				$actSuDlvrItem->_nextFromDb()) {
			try {
				$actSuDlvrItem->_buche( $_sign) ;
			} catch( Exception $e) {
				throw $e ;
			}
		}
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	function	buche( $_key="", $_id=0, $_val="") {
		$this->_buche( 1) ;
		if ( $_key != "") {
			$ret	=	$this->getTablePostenAsXML() ;
		} else {
			$ret	=	"" ;
		}
		return $ret ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	unbuche( $_key="", $_id=0, $_val="") {
		$this->_buche( 1) ;
		if ( $_key != "") {
			$ret	=	$this->getTablePostenAsXML() ;
		} else {
			$ret	=	"" ;
		}
		return $ret ;
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	_clearAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDb::query( "UPDATE SuDlvrItem SET MengeGebucht = 0 ") ;
	}
	static	function	_bucheAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		$ret	=	"" ;
		$actSuDlvr	=	new SuDlvr() ;
		$crit	=	"SuDlvrNo LIKE '%%' AND Status <= 90 " .		// only the closed ones
					"AND Datum > '$_startDate' " .					// in the given date range
					"AND Datum <= '$_endDate' " ;
		for ( $actSuDlvr->_firstFromDb( $crit) ;
				$actSuDlvr->_valid ;
				$actSuDlvr->_nextFromDb()) {
			$actSuDlvr->buche() ;
		}
		return $ret ;
	}
	static	function	bucheAll( $_key="", $_id=-1, $_val="") {
		self::_bucheAll( $_key, $_id, $_val) ;
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	_unbucheAll( $_key, $_id, $_val) {
		$ret	=	"" ;
		$actSuDlvr	=	new SuDlvr() ;
		for ( $actSuDlvr->_firstFromDb( "SuDlvrNo like '%' ") ;
				$actSuDlvr->_valid ;
				$actSuDlvr->_nextFromDb()) {
			$actSuDlvr->unbuche() ;
		}
		return $ret ;
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
	function	getRDocType() {
		return self::$rDocType ;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $database
	 * @return unknown
	 */
	function	setAllRcvd( $_key="", $_id=-1, $_val="") {
		$actArtikel	=	new Artikel() ;
		$actSuDlvrPos	=	new SuDlvrItem( $this->SuDlvrNo) ;
		if ( $this->LockState == 0) {
			for ( $actSuDlvrPos->firstFromDb( "SuDlvrNo", "", null, "", "ORDER BY MO.ItemNo ASC ") ;
					$actSuDlvrPos->_valid ;
					$actSuDlvrPos->nextFromDb()) {
				try {
					$actArtikel->setArtikelNr( $actSuDlvrPos->ArtikelNr) ;
					if ( $actArtikel->_valid) {
						$actSuDlvrPos->MengeEmpfangen	=	$actSuDlvrPos->Menge ;
						$actSuDlvrPos->updateColInDb( "MengeEmpfangen") ;
					} else {
						error_log( "SuDlvr.php::buche2: could not find article no. " . $actSuDlvrPos->ArtikelNr . " in database") ;
					}
				} catch ( Exception $e) {
					throw $e ;
				}
			}
			$this->buche( $_key, $_id, $_val) ;
		} else {
			throw new Exception( 'SuDlvr::setAllRcvd: the object is locked!') ;
		}
		$ret	=	$this->getTableDepAsXML( $_key, $_id, "SuDlvrItem") ;
		return $ret ;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $database
	 * @return unknown
	 */
	function	setNoneRcvd( $_key="", $_id=-1, $_val="") {
		$actArtikel	=	new Artikel() ;
		$actSuDlvrPos	=	new SuDlvrItem( $this->SuDlvrNo) ;
		if ( $this->LockState == 0) {
			$this->unbuche( $_key, $_id, $_val) ;
			for ( $actSuDlvrPos->firstFromDb( "SuDlvrNo", "", null, "", "ORDER BY MO.ItemNo ASC ") ;
					$actSuDlvrPos->_valid ;
					$actSuDlvrPos->nextFromDb()) {
				try {
					$actArtikel->setArtikelNr( $actSuDlvrPos->ArtikelNr) ;
					if ( $actArtikel->_valid) {
						$actSuDlvrPos->EmpfangeneMenge	=	0 ;
						$actSuDlvrPos->updateColInDb( "MengeEmpfangen") ;
					} else {
						error_log( "SuDlvr.php::buche2: could not find article no. " . $actSuDlvrPos->ArtikelNr . " in database") ;
					}
				} catch ( Exception $e) {
					throw $e ;
				}
			}
		} else {
			throw new Exception( 'SuDlvr::setNoneRcvd: the object is locked!') ;
		}
		$ret	=	$this->getTableDepAsXML( $_key, $_id, "SuDlvrItem") ;
		return $ret ;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $database
	 * @return unknown
	 */
	function	open( $_key="", $_id=-1, $_val="") {
		$this->Status	=	SuDlvr::OPEN ;
		$this->updateColInDb( "Status") ;
		return $this->_status ;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $database
	 * @return unknown
	 */
	function	close( $_key="", $_id=-1, $_val="") {
		$this->Status	=	SuDlvr::CLOSED ;
		$this->updateColInDb( "Status") ;
		return $this->_status ;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $database
	 * @param unknown_type $_step
	 * @return unknown
	 */
	function	restate( $_key="", $_id=-1, $_val="") {
		try {
			$this->buche( $_key, $_val, $_id) ;
			$this->close( $_key, $_val, $_id) ;
			$this->lock( $_key, $_val, $_id) ;
			$mySuOrdr	=	new SuOrdr() ;
			if ( $mySuOrdr->setSuOrdrNo( $this->SuOrdrNo)) {
				$mySuOrdr->restate() ;
			}
			
		} catch ( Exception $e) {
			throw( $e) ;
		}
		$ret	=	$this->getXMLComplete() ;
		return $ret ;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_db
	 * @param unknown_type $_mt
	 * @param unknown_type $_size
	 * @return unknown
	 */
	function	genLabels( $_db, $_mt, $_size) {
	}
	/**
	 * methods: retrieval
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getLiefAsXML() ;
		$ret	.=	$this->getTableDepAsXML( $_key="", $_id=-1, "SuDlvrItem") ;
		return $ret ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	function	getLiefAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	'<LiefAdr>' ;
		$ret	.=	$this->myLief->getXMLF() ;
		if ( $this->myLiefKontakt)
			$ret	.=	$this->myLiefKontakt->getXMLF() ;
		$ret	.=	'</LiefAdr>' ;
		return $ret ;
	}
	function	getDepAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$mySuDlvrItem	=	new SuDlvrItem() ;
		$mySuDlvrItem->setId( $_id) ;
		$ret	.=	$mySuDlvrItem->getXMLF() ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	updSuDlvrItem( $_key, $_id, $_val) {
		$mySuDlvrItem	=	new SuDlvrItem() ;
		$mySuDlvrItem->setId( $_id) ;
		$mySuDlvrItem->getFromPostL() ;
		$mySuDlvrItem->updateInDb() ;
		return $this->getTablePostenAsXML() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	delPos( $_key, $_id, $_val) {
		$mySuDlvrItem	=	new SuDlvrItem() ;
		$mySuDlvrItem->setId( $_id) ;
		$mySuDlvrItem->removeFromDb() ;
		return $this->getTablePostenAsXML() ;
	}
}
/**
 * SuDlvrItem - Base class fur sppplier delivery item
 *
 * not be modified.
 *
 * @package Application
 * @subpackage SuDlvr
 */
class	SuDlvrItem	extends	AppDepObject	{
	/**
	 *
	 */
	function	__construct( $_suDlvrNo="") {
		parent::__construct( "SuDlvrItem", "Id") ;
		$this->SuDlvrNo	=	$_suDlvrNo ;
	}
	/**
	 * 
	 */
	function	getNextItemNo() {
		$query	=	sprintf( "SELECT ItemNo FROM SuDlvrItem WHERE SuDlvrNo='$this->SuDlvrNo' ORDER BY ItemNo DESC LIMIT 0, 1 ") ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->ItemNo	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
	/**
	 * 
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		if ( $_sign == -1) {
			$menge	=	$this->MengeGebucht * $_sign ;
		} else {
			$menge	=	($this->MengeEmpfangen /* * $this->MengeProVPE */ ) - $this->MengeGebucht ;
		}
		try {
			$myArtikel	=	new Artikel() ;
			$myArtikel->setArtikelNr( $this->ArtikelNr) ;
			$qtyBooked	=	$myArtikel->receive( $menge) ;		// qtyPerPack already considered above!!!
			$this->MengeGebucht	+=	$qtyBooked ;
			$this->updateColInDb( "MengeGebucht") ;
		} catch( Exception $e) {
			
		}
	}
	function	buche() {		$this->_buche( 1) ;			}
	function	unbuche() {		$this->_buche( -1) ;		}
}

?>
