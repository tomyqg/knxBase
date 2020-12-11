<?php
/**
 * KdLeih.php Base class for Customer Order (KdLeih)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/AppObjectCR.php") ;
require_once( "base/AppDepObject.php") ;
/**
 * KdLeih - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BKdLeih which should
 * not be modified.
 *
 * @package Application
 * @subpackage KdLeih
 */
class	KdLeih	extends	AppObjectCR	{
	/**
	 * 
	 * @var unknown_type
	 */
	private	$tmpKdLeihPos ;
	/**
	 * 
	 * @var unknown_type
	 */
	const	NEU		=	0 ;
	const	ONGOING	=	50 ;
	const	CLOSED	=	90 ;
	const	CLOSED_SOLD	=	95 ;
	const	ONHOLD	=	980 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						KdLeih::NEU		=> "open",
						KdLeih::ONGOING	=> "ongoing",
						KdLeih::CLOSED	=> "abgeschlossen",
						KdLeih::CLOSED_SOLD	=> "abgeschlossen/verkauft",
						KdLeih::ONHOLD	=> "on-hold",
						KdLeih::CANCELLED	=> "cancelled"
					) ;
	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (KdLeihNr), in which case it will automatically
	 * (try to) load the respective Customer Order via the base class from the Database
	 * If the order data was loaded the customer data, and customer contact data, will also be loaded
	 * from the database.
	 *
	 * @param string $_myKdLeihNr
	 * @return void
	 */
	function	__construct( $_myKdLeihNr='') {
		parent::__construct( "KdLeih", "KdLeihNr") ;
		if ( strlen( $_myKdLeihNr) > 0) {
			$this->setKdLeihNr( $_myKdLeihNr) ;
		} else {
			FDbg::dump( "KdLeih::__construct(...): KdLeihNr not specified !") ;
		}
	}
	/**
	 * set the Order Number (KdLeihNr)
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
	function	setKdLeihNr( $_myKdLeihNr) {
		$this->KdLeihNr	=	$_myKdLeihNr ;
		if ( strlen( $_myKdLeihNr) > 0) {
			$this->reload() ;
		}
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	function	buche( $_key="", $_id=-1, $_val="") {
	}
	/**
	 * Un-Books the entire order in Stock
	 *
	 * @return void
	 */
	function	unbuche( $_key="", $_id=-1, $_val="") {
	}
	/**
	 * @see AppObject::getXMLComplete()
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getKundeAsXML() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "KdLeihPosten") ;
		return $ret ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getXMLString()
	 */
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		$ret	.=	$this->getXMLDocInfo() ;
		return $ret ;
	}
	/**
	 * 
	 */
	function	getXMLDocInfo( $_key="", $_id=-1, $_val="") {
		$ret	=	"<Document><![CDATA[" ;
		$filename	=	$this->path->Archive . "KdLeih/" . $this->KdLeihNr . ".pdf" ;
		if ( file_exists( $filename)) { 
			$ret	.=	 $this->url->Archive . "KdLeih/" . $this->KdLeihNr . ".pdf" ;
		}
		$ret	.=	"]]></Document>" ;
		return $ret ;
	}
	/**
	 * methods: retrieve options
	 */
	function	getRStatus() {		return self::$rStatus ;		}
}
/**
 * 
 * @author miskhwe
 *
 */
class	KdLeihPosten	extends	AppDepObject	{

	public	$myCond ;

	function	__construct( $_myKdLeihNr='') {
		parent::__construct( "KdLeihPosten", "Id") ;
		$this->KdLeihNr	=	$_myKdLeihNr ;
	}

	/**
	 *
	 */
	function	reload() {
		$this->fetchFromDbById() ;
	}
	/**
	 *
	 * @return void
	 */
	function	buche() {
		$myArtikelBestand	=	new ArtikelBestand( $this->ArtikelNr) ;
		$myArtikelBestand->getDefault() ;
		if ( $myArtikelBestand->_valid == 1) {
			$myArtikelBestand->reserve( ( $this->Menge * $this->MengeProVPE ) - $this->MengeReserviert ) ;
			$this->MengeReserviert	+=	( ( $this->Menge * $this->MengeProVPE ) - $this->MengeReserviert ) ;
			$this->updateInDb() ;
		} else {
		}
	}
	/**
	 *
	 */
	function	unbuche() {
		$myArtikelBestand	=	new ArtikelBestand( $this->ArtikelNr) ;
		$myArtikelBestand->getDefault() ;
		if ( $myArtikelBestand->_valid == 1) {
			$myArtikelBestand->unreserve( $this->MengeReserviert ) ;
			$this->MengeReserviert	=	0 ;
			$this->updateInDb() ;
		}
	}
	/**
	 *
	 * @return void
	 */
	function	getNextPosNr() {
		$query	=	sprintf( "SELECT PosNr FROM KdLeihPosten WHERE KdLeihNr='%s' ORDER BY PosNr DESC LIMIT 0, 1 ", $this->KdLeihNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->PosNr	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}	
}
?>
