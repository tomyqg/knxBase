<?php
/**
 * SuOrdr.php - Definition der Basis Klasses f�r Liefn Lieferungen
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
require_once( "XmlTools.php" );
/**
 * SuOrdr - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage SuOrdr
 */
class	SuOrdr	extends	AppObjectSR	{
	/**
	 * 
	 * @var unknown_type
	 */
	public	$myCuOrdr ;
	const	NEU		=	0 ;
	const	ONGOING	=	50 ;
	const	CLOSED	=	90 ;
	const	ONHOLD	=	980 ;
	const	CANCELLED	=	0x010000000 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						SuOrdr::NEU		=> "open",
						SuOrdr::ONGOING	=> "ongoing",
						SuOrdr::CLOSED	=> "closed",
						SuOrdr::ONHOLD	=> "on-hold",
						SuOrdr::CANCELLED	=> "cancelled"
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
						SuOrdr::DOCAB	=> "Order Confirmation",
						SuOrdr::DOCPL	=> "Packing List",
						SuOrdr::DOCCI	=> "Commercial Invoice",
						SuOrdr::DOCSN	=> "Shipment Notification",
						SuOrdr::DOCMI	=> "Miscellaneous"
					) ;
	/**
	 *
	 */
	function	__construct( $_mySuOrdrNo='') {
		parent::__construct( "SuOrdr", "SuOrdrNo") ;
		$this->myCuOrdr	=	NULL ;
		if ( strlen( $_mySuOrdrNo) > 0) {
			$this->setSuOrdrNo( $_mySuOrdrNo) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setSuOrdrNo( $_mySuOrdrNo) {
		$this->SuOrdrNo	=	$_mySuOrdrNo ;
		if ( strlen( $_mySuOrdrNo) > 0) {
			$this->reload() ;
		}
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PCuOrdr)
	 *
	 * @return void
	 */
	function	newFromPSuOrdr( $_key="", $_id=-1, $_pSuOrdrNo="") {
		/**
		 * create a new SuOrdr
		 */
		$this->_newFrom( "PSuOrdr", $_pSuOrdrNo) ;		// create a new instance
		$mySuOrdrPos	=	new SuOrdrItem( $this->SuOrdrNo) ;
		for ( $valid = $mySuOrdrPos->firstFromDb( "SuOrdrNo", "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
				$valid ;
				$valid = $mySuOrdrPos->nextFromDb()) {
//			$mySuOrdrPos->updateInDb() ;
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
		FDbg::begin( 1, "SuOrdr.php", "SuOrdr", "newFromSuOrdr( '$_key', $_id, '$_suDlvrNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "SuDlvr", $_suDlvrNo) ;		// create a new instance
		$mySuOrdrPos	=	new SuOrdrItem( $this->SuOrdrNo) ;
		for ( $valid = $mySuOrdrPos->firstFromDb( "SuOrdrNo", "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
				$valid ;
				$valid = $mySuOrdrPos->nextFromDb()) {
			$mySuOrdrPos->MengeEmpfangen	=	$mySuOrdrPos->Menge ;
			$mySuOrdrPos->updateInDb() ;
		}
		FDbg::end( 1, "SuOrdr.php", "SuOrdr", "newFromSuOrdr( '$_key', $_id, '$_suDlvrNo')") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a new Customer Commission (CuComm) for this Customer Order (CuOrdr)
	 * If an open Commision exists a Reference to this open CuComm will be returned
	 * @param string $_key Number of the Customer RFQ (CuRFQ) which shall be turned into a Customer Quotation (CuQuot)
	 * @param int $_id unused
	 * @param mixed $_val unused
	 */
	function	newSuDlvr( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "SuOrdr.php", "SuOrdr", "newSuDlvr( '$_key', $_id, '$_val')") ;
		$newSuDlvr	=	new SuDlvr() ;
		$newSuDlvr->newFromSuOrdr( "", -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>SuDlvr</ObjectClass>\n<ObjectKey>$newSuDlvr->SuDlvrNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end( 1, "SuOrdr.php", "SuOrdr", "newSuDlvr( '$_key', $_id, '$_val')") ;
		return $ret ;
	}
	/**
	 * Renumber the complete order with the given step-width. If step-width is not passed the default
	 * of 1 is used as step-width.
	 *
	 * @return void
	 */
	function	restate() {
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::restate(): begin") ;
		$query	=	sprintf( "SuOrdr_restate( @status, '%s') ; ", $this->SuOrdrNo) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::dumpF( "SuOrdr::restate(...): exception='%s'", $e->getMessage()) ;
		}
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::restate(): end") ;
	}
	/**
	 * Renumber the complete order with the given step-width. If step-width is not passed the default
	 * of 1 is used as step-width.
	 *
	 * @return void
	 */
	function	cons( $_key="", $_id='', $_val="") {
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::cons( '$_key, $_id, '$_val'): begin") ;
		try {
			$query	=	"UPDATE SuOrdrItem AS BP "
					.	"SET BP.MengeBereitsEmpfangen = ( "
					.	"SELECT SUM( MengeEmpfangen) FROM SuDlvrItem AS a, SuDlvr AS b "
					.	"WHERE a.SuDlvrNo = b.SuDlvrNo "
					.	"AND b.SuOrdrNo = BP.SuOrdrNo "
					.	"AND a.ArtikelNr = BP.ArtikelNr "
					.	"AND a.MengeProVPE = BP.MengeProVPE) "
					.	"WHERE BP.SuOrdrNo = '$this->SuOrdrNo' " ;
			FDb::query( $query) ;
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::cons( '$_key, $_id, '$_val'): end") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Renumber the complete order with the given step-width. If step-width is not passed the default
	 * of 1 is used as step-width.
	 *
	 * @return void
	 */
	function	open() {
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::open(): begin") ;
		$query	=	sprintf( "SuOrdr_open( @status, '%s') ; ", $this->SuOrdrNo) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
			$this->reload() ;
		} catch( Exception $e) {
			FDbg::dumpF( "SuOrdr::open(...): exception='%s'", $e->getMessage()) ;
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::open(): end") ;
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
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::buche( '$_key, $_id, '$_val'): begin") ;
		$actSuOrdrPos	=	new SuOrdrItem( $this->SuOrdrNo) ;
		for ( $actSuOrdrPos->firstFromDb( "SuOrdrNo", "", null, "", "ORDER BY MO.ItemNo ASC ") ;
				$actSuOrdrPos->_valid ;
				$actSuOrdrPos->nextFromDb()) {
			try {
				$actSuOrdrPos->buche() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
		if ( $_key != "") {
			$ret	=	$this->getTablePostenAsXML() ;
		} else {
			$ret	=	"" ;
		}
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::buche( '$_key, $_id, '$_val'): end") ;
		return $ret ;
	}
	
	function	unbuche( $_key="", $_id=0, $_val="") {
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::unbuche( '$_key, $_id, '$_val'): begin") ;
		$actArtikel	=	new Artikel() ;
		$actSuOrdrPos	=	new SuOrdrItem( $this->SuOrdrNo) ;
		for ( $actSuOrdrPos->firstFromDb( "SuOrdrNo", "", null, "", "ORDER BY MO.ItemNo ASC ") ;
				$actSuOrdrPos->_valid ;
				$actSuOrdrPos->nextFromDb()) {
			try {
				$actSuOrdrPos->unbuche() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
		if ( $_key != "") {
			$ret	=	$this->getTablePostenAsXML() ;
		} else {
			$ret	=	"" ;
		}
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::unbuche( '$_key, $_id, '$_val'): end") ;
		return $ret ;
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	bucheAll( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::bucheAll( '$_key, $_id, '$_val'): begin") ;
		$ret	=	"" ;
		$actSuOrdr	=	new SuOrdr() ;
		for ( $actSuOrdr->_firstFromDb( "SuOrdrNo like '%' ") ;
				$actSuOrdr->_valid ;
				$actSuOrdr->_nextFromDb()) {
			$actSuOrdr->buche() ;
		}
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::bucheAll( '$_key, $_id, '$_val'): end") ;
		return $ret ;
	}
	
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	unbucheAll( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::unbucheAll( '$_key, $_id, '$_val'): begin") ;
		$ret	=	"" ;
		$actSuOrdr	=	new SuOrdr() ;
		for ( $actSuOrdr->_firstFromDb( "SuOrdrNo like '%' ") ;
				$actSuOrdr->_valid ;
				$actSuOrdr->_nextFromDb()) {
			$actSuOrdr->unbuche() ;
		}
		FDbg::dumpL( 0x00000001, "SuOrdr.php::SuOrdr::unbucheAll( '$_key, $_id, '$_val'): end") ;
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
	function	updSuOrdrItem( $_key="", $_id=-1, $_val="") {
		$mySuOrdrItem	=	new SuOrdrItem() ;
		$mySuOrdrItem->setId( $_id) ;
		$mySuOrdrItem->getFromPostL() ;
		$mySuOrdrItem->updateInDb() ;
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
		$mySuOrdrItem	=	new SuOrdrItem() ;
		$mySuOrdrItem->setId( $_id) ;
		$mySuOrdrItem->removeFromDb() ;
		$this->buche() ;
		return $this->getTableDepAsXML( $_key, $_id, "SuOrdrItem") ;
	}
	/**
	 * methods: retrieval
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getLiefAsXML() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "SuOrdrItem") ;
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
		$filename	=	$this->path->Archive . "SuOrdr/" . $this->SuOrdrNo . ".pdf" ;
		if ( file_exists( $filename)) { 
			$ret	.=	 $this->url->Archive . "SuOrdr/" . $this->SuOrdrNo . ".pdf" ;
		}
		$ret	.=	"</Document>" ;
		return $ret ;
	}
	function	getSuOrdrItemAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$mySuOrdrItem	=	new SuOrdrItem() ;
		$mySuOrdrItem->setId( $_id) ;
		$ret	.=	$mySuOrdrItem->getXMLF() ;
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
								FTr::tr( "Our order No. #1, dated #2", array( "%s:".$this->SuOrdrNo, "%s:".convDate( $this->Datum)), $this->myLief->Sprache),
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
				$myBody->addData( "application/pdf", $this->path->Archive."SuOrdr/".$this->SuOrdrNo.".pdf", $this->SuOrdrNo.".pdf", false) ;
				$myBody->addData( "application/pdf", $this->path->Archive."CuDlvr/".$this->CuDlvrNo.".pdf", $this->CuDlvrNo.".pdf", true) ;
			} else {
				$myBody->addData( "application/pdf", $this->path->Archive."SuOrdr/".$this->SuOrdrNo.".pdf", $this->SuOrdrNo.".pdf", true) ;
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
								$this->path->Archive."SuOrdr/".$this->SuOrdrNo.".pdf",
								$this->path->Archive."CuDlvr/".$this->CuDlvrNo.".pdf"
						), "", "", "miskhwe",
						3) ;
		} else {
			sendFax( $myFaxNr,
						$this->path->Archive."SuOrdr/".$this->SuOrdrNo.".pdf", "", "", "miskhwe",
						3) ;
		}
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	SuOrdr::ONGOING ;		// ueber "Normal"-FAX
		$this->_addRem( "verschickt per FAX an " . $myFaxNr) ;
	}
	/**
	 * methods: internal
	 */
	static	function	_clearAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDb::query( "UPDATE SuOrdrItem SET MengeGebucht = 0 ") ;
	}
	static	function	_markAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		$ret	=	"" ;
		$actSuOrdr	=	new SuOrdr() ;
		$crit	=	"SuOrdrNo LIKE '%%' AND Status < 90 " .		// only the open ones
					"AND Datum > '$_startDate' " .					// in the given date range
					"AND Datum <= '$_endDate' " ;
		for ( $actSuOrdr->_firstFromDb( $crit) ;
				$actSuOrdr->_valid ;
				$actSuOrdr->_nextFromDb()) {
			$actSuOrdr->buche() ;
		}
		return $ret ;
	}
}

/**
 * SuOrdrItem - Basis Klasse f�r Liefn Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage SuOrdr
 */
class	SuOrdrItem	extends	AppDepObject	{
	/**
	 *
	 */
	function	__construct( $_suOrdrNo='') {
		parent::__construct( "SuOrdrItem", "Id") ;
		$this->SuOrdrNo	=	$_suOrdrNo ;
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
		$query	=	sprintf( "SELECT ItemNo FROM SuOrdrItem WHERE SuOrdrNo='%s' ORDER BY ItemNo DESC LIMIT 0, 1 ", $this->SuOrdrNo) ;
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
