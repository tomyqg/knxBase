<?php
/**
 * SuInvc.php - Definition der Basis Klasses f�r Liefn Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
require_once( "base/AppObjectSRNew.php") ;
require_once( "base/AppDepObject.php") ;
require_once( "XmlTools.php" );
/**
 * SuInvc - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage SuInvc
 */
class	SuInvc	extends	AppObjectSRNew	{
	/**
	 * 
	 * @var unknown_type
	 */
	public	$myCuInvc ;
	const	NEU		=	0 ;
	const	ONGOING	=	50 ;
	const	CLOSED	=	90 ;
	const	ONHOLD	=	980 ;
	const	CANCELLED	=	0x010000000 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						SuInvc::NEU		=> "open",
						SuInvc::ONGOING	=> "ongoing",
						SuInvc::CLOSED	=> "closed",
						SuInvc::ONHOLD	=> "on-hold",
						SuInvc::CANCELLED	=> "cancelled"
					) ;
	/**
	 * 
	 * @var unknown_type
	 */
	const	DOCAB	=	"AB" ;		// order confirmation
	const	DOCCI	=	"CI" ;		// commercial invoice (Handelsrechnung)
	const	DOCPL	=	"PL" ;		// packing list (Packzettel)
	const	DOCMI	=	"MI" ;		// miscellaneous
	const	DOCSN	=	"SN" ;		// shipmment notification (Versandavis)
	private	static	$rDocType	=	array (
						SuInvc::DOCAB	=> "Order Confirmation",
						SuInvc::DOCPL	=> "Packing List",
						SuInvc::DOCCI	=> "Commercial Invoice",
						SuInvc::DOCSN	=> "Shipment Notification",
						SuInvc::DOCMI	=> "Miscellaneous"
					) ;
	/**
	 *
	 */
	function	__construct( $_mySuInvcNo='') {
		parent::__construct( "SuInvc", "SuInvcNo") ;
		$this->myCuInvc	=	NULL ;
		if ( strlen( $_mySuInvcNo) > 0) {
			$this->setSuInvcNo( $_mySuInvcNo) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setSuInvcNo( $_mySuInvcNo) {
		$this->SuInvcNo	=	$_mySuInvcNo ;
		if ( strlen( $_mySuInvcNo) > 0) {
			$this->reload() ;
		}
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PCuInvc)
	 *
	 * @return void
	 */
	function	newFromPSuInvc( $_key="", $_id=-1, $_pSuInvcNo="") {
		/**
		 * create a new SuInvc
		 */
		$this->_newFrom( "PSuInvc", $_pSuInvcNo) ;		// create a new instance
		$mySuInvcPos	=	new SuInvcItem( $this->SuInvcNo) ;
		for ( $valid = $mySuInvcPos->firstFromDb( "SuInvcNo", "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
				$valid ;
				$valid = $mySuInvcPos->nextFromDb()) {
//			$mySuInvcPos->updateInDb() ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_suDlvrNo
	 */
	function	newFromSuDlvr( $_key="", $_id=-1, $_suDlvrNo) {
		FDbg::begin( 1, "SuInvc.php", "SuInvc", "newFromSuInvc( '$_key', $_id, '$_suDlvrNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "SuDlvr", $_suDlvrNo) ;		// create a new instance
		$mySuInvcPos	=	new SuInvcItem( $this->SuInvcNo) ;
		for ( $valid = $mySuInvcPos->firstFromDb( "SuInvcNo", "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
				$valid ;
				$valid = $mySuInvcPos->nextFromDb()) {
			$mySuInvcPos->MengeEmpfangen	=	$mySuInvcPos->Menge ;
			$mySuInvcPos->updateInDb() ;
		}
		FDbg::end( 1, "SuInvc.php", "SuInvc", "newFromSuInvc( '$_key', $_id, '$_suDlvrNo')") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a new Customer Commission (CuComm) for this Customer Order (CuInvc)
	 * If an open Commision exists a Reference to this open CuComm will be returned
	 * @param string $_key Number of the Customer RFQ (CuRFQ) which shall be turned into a Customer Quotation (CuQuot)
	 * @param int $_id unused
	 * @param mixed $_val unused
	 */
	function	newSuDlvr( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "SuInvc.php", "SuInvc", "newSuDlvr( '$_key', $_id, '$_val')") ;
		$newSuDlvr	=	new SuDlvr() ;
		$newSuDlvr->newFromSuInvc( "", -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>SuDlvr</ObjectClass>\n<ObjectKey>$newSuDlvr->SuDlvrNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end( 1, "SuInvc.php", "SuInvc", "newSuDlvr( '$_key', $_id, '$_val')") ;
		return $ret ;
	}
	/**
	 * Renumber the complete order with the given step-width. If step-width is not passed the default
	 * of 1 is used as step-width.
	 *
	 * @return void
	 */
	function	restate() {
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::restate(): begin") ;
		$query	=	sprintf( "SuInvc_restate( @status, '%s') ; ", $this->SuInvcNo) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::dumpF( "SuInvc::restate(...): exception='%s'", $e->getMessage()) ;
		}
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::restate(): end") ;
	}
	/**
	 * Renumber the complete order with the given step-width. If step-width is not passed the default
	 * of 1 is used as step-width.
	 *
	 * @return void
	 */
	function	cons( $_key="", $_id='', $_val="") {
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::cons( '$_key, $_id, '$_val'): begin") ;
		try {
			$query	=	"UPDATE SuInvcItem AS BP "
					.	"SET BP.MengeBereitsEmpfangen = ( "
					.	"SELECT SUM( MengeEmpfangen) FROM SuDlvrItem AS a, SuDlvr AS b "
					.	"WHERE a.SuDlvrNo = b.SuDlvrNo "
					.	"AND b.SuInvcNo = BP.SuInvcNo "
					.	"AND a.ArtikelNr = BP.ArtikelNr "
					.	"AND a.MengeProVPE = BP.MengeProVPE) "
					.	"WHERE BP.SuInvcNo = '$this->SuInvcNo' " ;
			FDb::query( $query) ;
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::cons( '$_key, $_id, '$_val'): end") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Renumber the complete order with the given step-width. If step-width is not passed the default
	 * of 1 is used as step-width.
	 *
	 * @return void
	 */
	function	open() {
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::open(): begin") ;
		$query	=	sprintf( "SuInvc_open( @status, '%s') ; ", $this->SuInvcNo) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
			$this->reload() ;
		} catch( Exception $e) {
			FDbg::dumpF( "SuInvc::open(...): exception='%s'", $e->getMessage()) ;
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::open(): end") ;
	}
	/**
	 * Renumber the complete order with the given step-width. If step-width is not passed the default
	 * of 1 is used as step-width.
	 *
	 * @return void
	 */
	function	close() {
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	function	buche( $_key="", $_id=0, $_val="") {
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::buche( '$_key, $_id, '$_val'): begin") ;
		$actSuInvcPos	=	new SuInvcItem( $this->SuInvcNo) ;
		for ( $actSuInvcPos->firstFromDb( "SuInvcNo", "", null, "", "ORDER BY MO.ItemNo ASC ") ;
				$actSuInvcPos->_valid ;
				$actSuInvcPos->nextFromDb()) {
			try {
				$actSuInvcPos->buche() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
		if ( $_key != "") {
			$ret	=	$this->getTablePostenAsXML() ;
		} else {
			$ret	=	"" ;
		}
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::buche( '$_key, $_id, '$_val'): end") ;
		return $ret ;
	}
	
	function	unbuche( $_key="", $_id=0, $_val="") {
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::unbuche( '$_key, $_id, '$_val'): begin") ;
		$actArtikel	=	new Artikel() ;
		$actSuInvcPos	=	new SuInvcItem( $this->SuInvcNo) ;
		for ( $actSuInvcPos->firstFromDb( "SuInvcNo", "", null, "", "ORDER BY MO.ItemNo ASC ") ;
				$actSuInvcPos->_valid ;
				$actSuInvcPos->nextFromDb()) {
			try {
				$actSuInvcPos->unbuche() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
		if ( $_key != "") {
			$ret	=	$this->getTablePostenAsXML() ;
		} else {
			$ret	=	"" ;
		}
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::unbuche( '$_key, $_id, '$_val'): end") ;
		return $ret ;
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	bucheAll( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::bucheAll( '$_key, $_id, '$_val'): begin") ;
		$ret	=	"" ;
		$actSuInvc	=	new SuInvc() ;
		for ( $actSuInvc->_firstFromDb( "SuInvcNo like '%' ") ;
				$actSuInvc->_valid ;
				$actSuInvc->_nextFromDb()) {
			$actSuInvc->buche() ;
		}
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::bucheAll( '$_key, $_id, '$_val'): end") ;
		return $ret ;
	}
	
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	unbucheAll( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::unbucheAll( '$_key, $_id, '$_val'): begin") ;
		$ret	=	"" ;
		$actSuInvc	=	new SuInvc() ;
		for ( $actSuInvc->_firstFromDb( "SuInvcNo like '%' ") ;
				$actSuInvc->_valid ;
				$actSuInvc->_nextFromDb()) {
			$actSuInvc->unbuche() ;
		}
		FDbg::dumpL( 0x00000001, "SuInvc.php::SuInvc::unbucheAll( '$_key, $_id, '$_val'): end") ;
		return $ret ;
	}
	/**
	 *
	 */
	function	getRStatus() {	return self::$rStatus ;	}
	function	getRDocType() {	return self::$rDocType ;	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	updSuInvcItem( $_key="", $_id=-1, $_val="") {
		$mySuInvcItem	=	new SuInvcItem() ;
		$mySuInvcItem->setId( $_id) ;
		$mySuInvcItem->getFromPostL() ;
		$mySuInvcItem->updateInDb() ;
		return $this->getTablePostenAsXML() ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	delDep( $_key="", $_id=-1, $_val="") {
		$this->unbuche() ;
		$mySuInvcItem	=	new SuInvcItem() ;
		$mySuInvcItem->setId( $_id) ;
		$mySuInvcItem->removeFromDb() ;
		$this->buche() ;
		return $this->getTableDepAsXML( $_key, $_id, "SuInvcItem") ;
	}
	/**
	 * methods: retrieval
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
//		$ret	.=	$this->getLiefAsXML() ;
//		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "SuInvcItem") ;
		return $ret ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		$ret	.=	$this->getXMLDocInfo() ;
		return $ret ;
	}
	function	getXMLDocInfo( $_key="", $_id=-1, $_val="") {
		$ret	=	"<Document>" ;
		$filename	=	$this->path->Archive . "SuInvc/" . $this->SuInvcNo . ".pdf" ;
		if ( file_exists( $filename)) { 
			$ret	.=	 $this->url->Archive . "SuInvc/" . $this->SuInvcNo . ".pdf" ;
		}
		$ret	.=	"</Document>" ;
		return $ret ;
	}
	function	getSuInvcItemAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$mySuInvcItem	=	new SuInvcItem() ;
		$mySuInvcItem->setId( $_id) ;
		$ret	.=	$mySuInvcItem->getXMLF() ;
		return $ret ;
	}
	function	getEMailAsHTML( $_key="", $_id=-1, $_val="") {
		$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer") ;
		$myReplTableOut	=	array( $this->myLiefKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext) ;
		$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;
		return $myMail ;
	}					
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	sendByEMail( $_key="", $_id=-1, $_val="") {
		try {
			$this->upd( '', '', '') ;
			$newMail	=	new mimeMail( $this->eMail->Purchasing,
								$_POST['_IBestellEMail'],
								$this->eMail->Purchasing,
								FTr::tr( "Our order No. #1, dated #2", array( "%s:".$this->SuInvcNo, "%s:".convDate( $this->Datum)), $this->myLief->Sprache),
								"Bcc: ".$this->eMail->Archive."\n") ;

			$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
			$myDisclaimerText	=	new SysTexte( "DisclaimerText") ;
			$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer", "#Datum") ;
			$myReplTableOut	=	array( $this->myLiefKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext, $this->Datum) ;
			$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;

			$myText	=	new mimeData( "multipart/alternative") ;
			$myText->addData( "text/plain", xmlToPlain( "<div>".$myMail."</div>")) ;
			$myText->addData( "text/html", "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">"
											. "<head></head><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n"
											. $myMail."</body></html>", "", true) ;
	
			$myBody	=	new mimeData( "multipart/mixed") ;
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			if ( $this->CuDlvrNo != "") {
				error_log( "====================================>>>>>>>>>>>") ;
				$myBody->addData( "application/pdf", $this->path->Archive."SuInvc/".$this->SuInvcNo.".pdf", $this->SuInvcNo.".pdf", false) ;
				$myBody->addData( "application/pdf", $this->path->Archive."CuDlvr/".$this->CuDlvrNo.".pdf", $this->CuDlvrNo.".pdf", true) ;
			} else {
				$myBody->addData( "application/pdf", $this->path->Archive."SuInvc/".$this->SuInvcNo.".pdf", $this->SuInvcNo.".pdf", true) ;
			}
			$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
			$mailSendResult	=	$newMail->send() ;

			$this->DocVerschVia	=	Opt::DOCVIAEMAIL ;
			$this->_addRem( FTr::tr( "send by E-Mail to: #1 (Bcc: #2)", array( "%s:".$_POST['_IBestellEMail'],"%s:".$this->eMail->Archive))) ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	/**
	 * Verschicken per FAX
	 */
	function	sendByFAX( $_key="", $_id=-1, $_val="") {
		require_once( "Fax.php" );
		$myFaxNr	=	$_POST['_IBestellFAX'] ;
		if ( strlen( $this->CuDlvrNo) > 0) {
			sendFax( $myFaxNr, array(
								$this->path->Archive."SuInvc/".$this->SuInvcNo.".pdf",
								$this->path->Archive."CuDlvr/".$this->CuDlvrNo.".pdf"
						), "", "", "miskhwe",
						3) ;
		} else {
			sendFax( $myFaxNr,
						$this->path->Archive."SuInvc/".$this->SuInvcNo.".pdf", "", "", "miskhwe",
						3) ;
		}
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	SuInvc::ONGOING ;		// ueber "Normal"-FAX
		$this->_addRem( "verschickt per FAX an " . $myFaxNr) ;
	}
	/**
	 * methods: internal
	 */
	static	function	_clearAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDb::query( "UPDATE SuInvcItem SET MengeGebucht = 0 ") ;
	}
	static	function	_markAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		$ret	=	"" ;
		$actSuInvc	=	new SuInvc() ;
		$crit	=	"SuInvcNo LIKE '%%' AND Status < 90 " .		// only the open ones
					"AND Datum > '$_startDate' " .					// in the given date range
					"AND Datum <= '$_endDate' " ;
		for ( $actSuInvc->_firstFromDb( $crit) ;
				$actSuInvc->_valid ;
				$actSuInvc->_nextFromDb()) {
			$actSuInvc->buche() ;
		}
		return $ret ;
	}
}

/**
 * SuInvcItem - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage SuInvc
 */
class	SuInvcItem	extends	AppDepObject	{
	/**
	 *
	 */
	function	__construct( $_suInvcNo='') {
		parent::__construct( "SuInvcItem", "Id") ;
		$this->SuInvcNo	=	$_suInvcNo ;
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
	function	getNextItemNo() {
		$query	=	sprintf( "SELECT ItemNo FROM SuInvcItem WHERE SuInvcNo='%s' ORDER BY ItemNo DESC LIMIT 0, 1 ", $this->SuInvcNo) ;
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
	 */
	function	buche() {
		$myArtikel	=	new Artikel( $this->ArtikelNr) ;
		$qtyOrdered	=	$myArtikel->order( $this->Menge - $this->MengeGebucht - $this->MengeBereitsEmpfangen) ;
		$this->MengeGebucht	+=	$qtyOrdered ;
		$this->updateColInDb( "MengeGebucht") ;
	}
	/**
	 *
	 */
	function	unbuche() {
		$myArtikel	=	new Artikel( $this->ArtikelNr) ;
		$qtyOrdered	=	$myArtikel->unorder( $this->MengeGebucht) ;
		$this->MengeGebucht	=	0 ;
		$this->updateColInDb( "MengeGebucht") ;
	}
}
?>
