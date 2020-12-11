<?php

/**
 * KundeBeleg.php - Basis Anwendungsklasse fuer Customernbestellung (KundeBeleg)
 *
 *	Definiert die Klassen:
 *		KundeBeleg
 *
 * Einige Parameter haben eine extrem wichtige Bedeutung fuer das Totalsystem, d.h. von der
 * Bestellung ueber die Kommissionierung und den Lieferschein bis hin zur Beleg.
 *
 * Attribut:	PosType
 *
 * Dieses Attribut beschreibt wie sich eine Position in der Bestellung in den verschiedenen Phasen
 * verhaelt.
 * Bei der Erzeugung von Kommisison, Lieferung und Beleg werden grundsaetzlich alle Positionen
 * uebernommen deren Menge in dem entsprechenden Papier > 0 ist (Kommission: Menge noch zu liefern; Lieferschein: jetzt
 * geliefert; Beleg: berechnete Menge).
 * Eine "NORMALe" Position wird im Lager reserviert (falls der Article an sich reserviert werden muss), wird
 * kommissioniert, geliefert und ebenfalls berechnet.
 * Eine "LieFeRuNG" Position wird im Lager reserviert (s.o.). Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp gelistet. Auf der Beleg wird dieser Positionstyp NICHT gelistet.
 * Eine "ReCHNunG" Position wird im Lager NICHT reservert. Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp nicht gelistet. Auf der Beleg wird dieser Typ gelistet.
 * Eine "KOMPonenten" Position wird im Lager reserviert, auf dem
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @package Application
 * @version 0.1
 * @filesource
 */
/**
 * KundeBeleg - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BKundeBeleg which should
 * not be modified.
 *
 * @package Application
 * @subpackage KundeBeleg
 */
class	KundeBeleg	extends	AppObject	{

	private	$tmpKundeBelegPos ;

	const	DOCAE	=	"AE" ;		// order document
	const	DOCSD	=	"SD" ;		// self-declaration (Eigenerklaerung)
	const	DOCMI	=	"MI" ;		// order confirmation
	private	static	$rDocType	=	array (
						KundeBeleg::DOCAE	=> "Auftragserteilung",
						KundeBeleg::DOCSD	=> "Self-declaration",
						KundeBeleg::DOCMI	=> "Sonstiges"
					) ;
	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (AuftragNr), in which case it will automatically
	 * (try to) load the respective Customer Order via the base class from the Database
	 * If the order data was loaded the customer data, and customer contact data, will also be loaded
	 * from the database.
	 *
	 * @param string $_myAuftragNr
	 * @return void
	 */
	function	__construct( $_myAuftragNr='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myAuftragNr')") ;
		parent::__construct( "KundeBeleg", "Id") ;
		$this->_valid	=	false ;
		if ( strlen( $_myAuftragNr) > 0) {
			$this->setAuftragNr( $_myAuftragNr) ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myAuftragNr')", "no order number specified!") ;
		}
		FDbg::end() ;
	}
	/**
	 * set the Order Number (AuftragNr)
	 *
	 * Sets the order number for this object and tries to load the order from the database.
	 * If the order could successfully be loaded from the database the respective customer data
	 * as well as customer contact data is retrieved as well.
	 * If the order has a separate Invoicing address, identified through a populated field, this
	 * data is read as well.
	 * If the order has a separate Delivery address, identified through a populated field, this
	 * data is read as well.
	 *
	 * @param	string	Order number to be set
	 * @return	bool	Validity of the object (not loaded from Db = false, loaded = true)
	 */
	function	setAuftragNr( $_myAuftragNr) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '%s')", $_myAuftragNr) ;
		$this->AuftragNr	=	$_myAuftragNr ;
		if ( strlen( $_myAuftragNr) > 0) {
			$this->reload() ;
		}
		FDbg::end() ;
		return $this->_valid ;
	}
	/**
	 * del
	 * deletes the current object from the database
	 * if the object has a LockState attribute and this is set to true, no update will be performed and an exception is thrown
	 * (non-PHPdoc)
	 * @see FDbObject::upd()
	 */
	function	del( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			$myKundeBelegPosition	=	new KundeBelegPosition() ;
			$myKundeBelegPosition->removeFromDbWhere( "AuftragNr = '" . $this->AuftragNr . "'") ;
			$this->removeFromDb() ;
		} else {
			$e	=	new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)", "the object [" . $this->$keyCol . "] is locked!") ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getNextAsXML( $_key, $_id, $_val, $reply) ;
	}
	/**
	 *
	 */
	function	renumberItems( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "sub-class := '$_result'") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$this->_renumber( "KundeBelegPosition", 10) ;
		$this->getAsXML( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_sign)") ;
		$actKundeBelegPosition	=	new KundeBelegPosition() ;
		$cond	=	"AuftragNr = '$this->AuftragNr' ORDER BY PosNo, SubPosNo " ;
		for ( $actKundeBelegPosition->_firstFromDb( $cond) ;
				$actKundeBelegPosition->isValid() ;
				$actKundeBelegPosition->_nextFromDb()) {
			try {
				$actKundeBelegPosition->_buche( $_sign) ;
			} catch( Exception $e) {
				error_log( $e->getMessage()) ;
			}
		}
		FDbg::end() ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	buche( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$ret	=	"" ;
		$this->_buche( 1) ;
		if ( $_key != "") {
			$ret	=	$this->getXMLComplete() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	unbuche( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$ret	=	"" ;
		$this->_buche( -1) ;
		if ( $_key != "") {
			$ret	=	$this->getXMLComplete() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	bucheAll( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$ret	=	"" ;
		$actKundeBeleg	=	new KundeBeleg() ;
		for ( $actKundeBeleg->_firstFromDb( "AuftragNr like '%' ") ;
				$actKundeBeleg->_valid ;
				$actKundeBeleg->_nextFromDb()) {
			error_log( "KundeBeleg.php::KundeBeleg::bucheAll(): booking AuftragNr " . $actKundeBeleg->AuftragNr) ;
			$actKundeBeleg->buche() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	unbucheAll( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$ret	=	"" ;
		$actKundeBeleg	=	new KundeBeleg() ;
		for ( $actKundeBeleg->_firstFromDb( "AuftragNr like '%' ") ;
				$actKundeBeleg->_valid ;
				$actKundeBeleg->_nextFromDb()) {
			error_log( "KundeBeleg.php::KundeBeleg::bucheAll(): un-booking AuftragNr " . $actKundeBeleg->AuftragNr) ;
			$actKundeBeleg->unbuche() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 */
	static	function	_clearAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		FDb::query( "UPDATE KundeBelegPosition SET MengeReserviert = 0 ") ;
		FDbg::end() ;
	}
	static	function	_markAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$ret	=	"" ;
		$actKundeBeleg	=	new KundeBeleg() ;
		$crit	=	"AuftragNr LIKE '%%' AND Status < 90 " .		// only the open ones
					"AND Datum > '$_startDate' " .					// in the given date range
					"AND Datum <= '$_endDate' " ;
		for ( $actKundeBeleg->_firstFromDb( $crit) ;
				$actKundeBeleg->_valid ;
				$actKundeBeleg->_nextFromDb()) {
			$actKundeBeleg->buche() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Create a new Customer Commission (CuComm) for this Customer Order (KundeBeleg)
	 * If an open Commision exists a Reference to this open CuComm will be returned
	 * @param string $_key Number of the Customer RFQ (CuRFQ) which shall be turned into a Customer Quotation (CuQuot)
	 * @param int $_id unused
	 * @param mixed $_val unused
	 */
	function	newCuComm( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$newCuComm	=	new CuComm() ;
		$newCuComm->newFromKundeBeleg( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CuComm</ObjectClass>\n<ObjectKey>$newCuComm->CuCommNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 */
	function	newCuInvcA( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$newCuInvc	=	new CuInvc() ;
		$newCuInvc->newFromKundeBelegA( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CuComm</ObjectClass>\n<ObjectKey>$newCuComm->CuCommNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 */
	function	newCuInvcOL( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$newCuInvc	=	new CuInvc() ;
		$newCuInvc->newFromKundeBelegOL( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CuInvc</ObjectClass>\n<ObjectKey>$newCuInvc->CuInvcNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PKundeBeleg)
	 *
	 * @return void
	 */
	function	newFromCuCart( $_key, $_id, $_cuCartNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_cuCartNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CuCart", $_cuCartNo, "", "", "900000", "949999") ;		// create a new instance
		$myKundeBelegPos	=	new KundeBelegPosition( $this->AuftragNr) ;
		for ( $valid = $myKundeBelegPos->firstFromDb( "AuftragNr", "", null, "", "ORDER BY PosNo, SubPosNo ") ;
				$valid ;
				$valid = $myKundeBelegPos->nextFromDb()) {
//			$myKundeBelegPos->updateInDb() ;
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PKundeBeleg)
	 *
	 * @return void
	 */
	function	newFromCuRFQ( $_key, $_id, $_cuRFQNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CuRFQ", $_cuRFQNo) ;		// create a new instance
		$myKundeBelegPos	=	new KundeBelegPosition( $this->AuftragNr) ;
		for ( $valid = $myKundeBelegPos->firstFromDb( "AuftragNr", "", null, "", "ORDER BY PosNo, SubPosNo ") ;
				$valid ;
				$valid = $myKundeBelegPos->nextFromDb()) {
//			$myKundeBelegPos->updateInDb() ;
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PKundeBeleg)
	 *
	 * @return void
	 */
	function	newFromCuOffr( $_key, $_id, $_cuOffrNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$this->_newFrom( "CuOffr", $_cuOffrNo) ;		// create a new instance
		FDBg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PKundeBeleg)
	 *
	 * @return void
	 */
	function	newFromPKundeBeleg( $_key, $_id, $_pAuftragNr) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_pAuftragNr')") ;
		$this->_newFrom( "PKundeBeleg", $_pAuftragNr) ;		// create a new instance
		$myKundeBelegPos	=	new KundeBelegPosition( $this->AuftragNr) ;
		for ( $valid = $myKundeBelegPos->firstFromDb( "AuftragNr", "", null, "", "ORDER BY PosNo, SubPosNo ") ;
				$valid ;
				$valid = $myKundeBelegPos->nextFromDb()) {
//			$myKundeBelegPos->updateInDb() ;
		}
		$ret	=	$this->getXMLComplete() ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Setzt den Prefix sowie den Postfix der Customernbestellung auf die Default Werte.
	 * Die Default Werte werden bestimmt durch die f�r den Customern abgespeicherten Wert
	 * f�r Language (z.B. de_de, en_us).
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: KundeBeleg_setTexte( @status, <AuftragNr>).
	 *
	 * @return void
	 */
	function	setTexte( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		try {
			$myTexte	=	new Texte( "KundeBelegPrefix", $this->KundeNr, $this->Customer->Language) ;
			$this->Prefix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Prefix") ;
			$myTexte	=	new Texte( "KundeBelegPostfix", $this->KundeNr, $this->Customer->Language) ;
			$this->Postfix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Postfix") ;
		} catch( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	setAnschreiben( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		try {
			$myTexte	=	new Texte( "KundeBelegEMail", $this->KundeNr, $this->Customer->Language) ;
			$this->Anschreiben	=	$myTexte->Volltext ;
			$this->updateColInDb( "Anschreiben") ;
		} catch( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 * @param $_id
	 * @param $_text
	 */
	function	updAddText( $_id, $_text) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_id, '$_text')") ;
		try {
			$this->tmpKundeBelegPos	=	new KundeBelegPosition() ;
			$this->tmpKundeBelegPos->Id	=	$_id ;
			$this->tmpKundeBelegPos->fetchFromDbById() ;
			if ( $this->tmpKundeBelegPos->_valid == 1) {
				FDbg::dumpL( 0x01000000, "KundeBeleg::updAddText: refers to PosNo=%d", $this->tmpKundeBelegPos->PosNo) ;
				$this->tmpKundeBelegPos->AddText	=	$_text ;
				$this->tmpKundeBelegPos->updateInDb() ;
			} else {
				throw new Exception( 'KundeBeleg::updAddText: KundeBelegPosition[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->tmpKundeBelegPos ;
	}
	/**
	 *
	 * @return KundeBelegPos
	 */
	function	getFirstPos() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$this->tmpKundeBelegPos	=	new KundeBelegPosition() ;
		$this->tmpKundeBelegPos->AuftragNr	=	$this->AuftragNr ;
		$this->tmpKundeBelegPos->_firstFromDb() ;
		FDbg::end() ;
		return $this->tmpKundeBelegPos ;
	}
	/**
	 *
	 * @return KundeBelegPos
	 */
	function	getNextPos() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$this->tmpKundeBelegPos->_nextFromDb() ;
		FDbg::end() ;
		return $this->tmpKundeBelegPos ;
	}
	/**
	 *
	 */
	function	updateHdlg() {
		/**	delete all handling items	**/
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		FDb::query( "DELETE FROM KundeBelegPosition WHERE AuftragNr = '$this->AuftragNr' AND ArticleNo like 'HDLGPSCH%'") ;
		$this->restate() ;
		if ( $this->TotalPrice >= 200.0 ) {
		} else if ( $this->TotalPrice >= 100.0 ) {
			$myVKPriceCache	=	new VKPriceCache() ;
			$myVKPriceCache->fetchFromDbWhere( "WHERE ArticleNo = \"HDLGPSCHM\" ") ;
			if ( $myVKPriceCache->isValid()) {
				$this->addPos( $myVKPriceCache->ArticleNo, $myVKPriceCache->Id, 1) ;
			}
		} else {
			$myVKPriceCache	=	new VKPriceCache() ;
			$myVKPriceCache->fetchFromDbWhere( "WHERE ArticleNo = \"HDLGPSCHH\" ") ;
			if ( $myVKPriceCache->isValid()) {
				$this->addPos( $myVKPriceCache->ArticleNo, $myVKPriceCache->Id, 1) ;
			}
		}
		$this->restate() ;
		FDbg::end() ;
	}
	/**
	 * Sends an order confirmation eMail to the eMail recipient associated with this
	 * orders customer.
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	sendByEMail( $_key="", $_id=-1, $_val="", $reply=null) {
		/**
		 * get the eMail body
		 */
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$this->mail( "KundeBelegEMail", null, "", "", "", "") ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 */
	function	getAnschAsHTML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer") ;
		$myReplTableOut	=	array( $this->CustomerKontakt->getOpening(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext) ;
		$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;
		FDbg::end() ;
		return $myMail ;
	}
	/**
	 * Verschicken per FAX
	 *
	 * @return [Article]
	 */
	function	sendByFAX( $_key="", $_id=-1, $_val="", $reply=null) {
		require_once( "Fax.php") ;
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$myFaxNr	=	$_POST['_IFAX'] ;
		sendFax( $myFaxNr,
					$this->path->Archive."KundeBeleg/".$this->AuftragNr.".pdf", "", "", "miskhwe",
					3) ;
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	KundeBeleg::ONGOING ;		// ueber "Normal"-FAX
		$this->_addRem( "verschickt per FAX an " . $myFaxNr, $$this->sysUser->UserId) ;
		FDbg::end() ;
	}
	/**
	 * Verschicken per FAX
	 *
	 * @return [Article]
	 */
	function	sendAsPDF() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$myText	=	date( "Ymd/Hi") . ": " . $_SESSION['UserId'] . ": verschickt als PDF \n" ;
		$myText	.=	$this->Rem1 ;
		$this->Rem1	=	$myText ;
		$this->DocVerschVia	=	38 ;		// ueber "Normal"-FAX
		$this->Status	=	ONGOING ;		// ueber "Normal"-FAX
		$this->updateInDb() ;
		FDbg::end() ;
	}
	/**
	 * returns the XML-formatted list of all items in this KundeBeleg
	 */
	function	consolidate( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 * update the quantities which have been delivered already
		 */
		$myItem	=	new FDbObject( "KundeBelegPosition", "Id", "def", "v_KundeBelegPositionConsolidateDeliveries") ; ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "AuftragNr = '".$this->AuftragNr."' ") ;
		$myItem->setIterOrder( [ "PosNo", "SubPosNo"]) ;
		$myKundeBelegPosition	=	new KundeBelegPosition() ;
		foreach ( $myItem as $actItem) {
			$myKundeBelegPosition->setId( $actItem->Id) ;
			$myKundeBelegPosition->QuantityDelivered	=	$actItem->DeliveredTotal ;
			$myKundeBelegPosition->updateColInDb( "QuantityDelivered") ;
		}
		/**
		 * update the quantities which have been invoiced already
		 */
		$myItem	=	new FDbObject( "KundeBelegPosition", "Id", "def", "v_KundeBelegPositionConsolidateReceipts") ; ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "AuftragNr = '".$this->AuftragNr."' ") ;
		$myItem->setIterOrder( [ "PosNo", "SubPosNo"]) ;
		$myKundeBelegPosition	=	new KundeBelegPosition() ;
		foreach ( $myItem as $actItem) {
			$myKundeBelegPosition->setId( $actItem->Id) ;
			$myKundeBelegPosition->QuantityReceiptd	=	$actItem->ReceiptdTotal ;
			$myKundeBelegPosition->updateColInDb( "QuantityReceiptd") ;
		}
		/**
		 * update the quantities which have been invoiced already
		 */
		$myItem	=	new FDbObject( "KundeBelegPosition", "Id", "def", "v_KundeBelegPositionConsolidateOrder") ;
		$this->ItemCount	=	$myItem->getCountWhere( [ "AuftragNr = '".$this->AuftragNr."' AND SubPosNo = '' "]) ;
		$this->updateColInDb( "ItemCount") ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "AuftragNr = '".$this->AuftragNr."' ") ;
		$myItem->setIterOrder( [ "PosNo", "SubPosNo"]) ;
		$this->TotalPrice	=	0.0 ;
		$this->TotalTax	=	0.0 ;
		foreach ( $myItem as $actItem) {
			$this->TotalPrice	+=	$myItem->TotalPrice ;
			$this->TotalTax		+=	$myItem->TotalPrice * (  $myItem->Percentage / 100.0) ;
		}
		$this->updateColInDb( "TotalPrice") ;
		$this->updateColInDb( "TotalTax") ;
		/**
		 *
		 */
		$this->getAsXML( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $database
	 * @return unknown
	 */
	function	createDirBest( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		FDbg::setLevel( 0x00ffffff) ;
		FDbg::enable() ;
		FDbg::dump( "KundeBeleg.php::KundeBeleg::createDirBest( $_key, $_id, $_val): begin") ;
		/**
		 * create the (provisionary) PKundeBeleg and CuComm for each distinct supplier
		 */
		$myLief	=	new Lief() ;
		$myKundeBelegPos	=	new KundeBelegPosition( $this->AuftragNr) ;
		$myEKPriceR	=	new EKPriceR() ;
		$tmpPKundeBeleg	=	array() ;
		$tmpCuComm	=	array() ;
		for ( $valid = $myKundeBelegPos->firstFromDb( "AuftragNr", "Article", array( "DirektVersand" => "txt"), "ArticleNo", "ORDER BY PosNo ") ;
				$valid ;
				$valid = $myKundeBelegPos->nextFromDb()) {
			if ( $myKundeBelegPos->DirektVersand == 1) {
				$validEKPR	=	$myEKPriceR->fetchFromDbWhere( "WHERE ArticleNo = '$myKundeBelegPos->ArticleNo' AND KalkBasis > 0") ;
				if ( $validEKPR) {
					FDbg::dump( "KundeBeleg.php::createDirBest: unique EKPriceR identified ") ;
					$liefNr	=	$myEKPriceR->LiefNr ;
					if ( !isset( $tmpPKundeBeleg[ $liefNr])) {
						$myLief->setLiefNr( $liefNr) ;
						$myPKundeBeleg	=	new PKundeBeleg() ;
						$myPKundeBeleg->PAuftragNr	=	$myEKPriceR->LiefNr . "-" . $this->AuftragNr ;
						$myPKundeBeleg->copyFrom( $this) ;
						$myPKundeBeleg->Datum	=	$this->today() ;
						$myPKundeBeleg->LiefNr	=	$liefNr ;
						$myPKundeBeleg->LiefKontaktNr	=	$myLief->OrderingContact ;
						$myPKundeBeleg->storeInDb() ;
						$tmpPKundeBeleg[ $liefNr]	=	$myPKundeBeleg ;

						$myCuComm	=	new CuComm() ;
						$myCuComm->add() ;			// get the CuComm no.
						$myCuComm->copyFrom( $this) ;		// copy meaningful attributes
						$myCuComm->Datum	=	$this->today() ;
						$myCuComm->updateInDb() ;
						$tmpCuComm[ $liefNr]	=	$myCuComm ;
					}
				} else if ( $status == -1) {
					FDbg::dump( "KundeBeleg.php::createDirBest: no EKPriceR identified ") ;
				} else if ( $status == -2) {
					FDbg::dump( "KundeBeleg.php::createDirBest: multiple EKPriceR identified ") ;
				}
			} else {
				FDbg::dump( "KundeBeleg.php::createDirBest: article no. '$myKundeBelegPos->ArticleNo' not a direct delivery article") ;
			}
		}
		/**
		 * create the provisionary PKundeBelegPosition and the CuCommItem
		 */
		$tmpPKundeBelegPos	=	new PKundeBelegPosition() ;
		$tmpCuCommPos	=	new CuCommItem() ;
		for ( $valid = $myKundeBelegPos->firstFromDb( "AuftragNr", "Article", array( "DirektVersand" => "txt"), "ArticleNo", "ORDER BY PosNo ") ;
				$valid ;
				$valid = $myKundeBelegPos->nextFromDb()) {
			if ( $myKundeBelegPos->DirektVersand == 1) {
				$status	=	$myEKPriceR->fetchFromDbWhere( "WHERE ArticleNo = '$myKundeBelegPos->ArticleNo' AND KalkBasis > 0") ;
				$liefNr	=	$myEKPriceR->LiefNr ;

				$tmpPKundeBelegPos->copyFrom( $myKundeBelegPos) ;
				$tmpPKundeBelegPos->PAuftragNr	=	$myEKPriceR->LiefNr . "-" . $this->AuftragNr ;
				$tmpPKundeBelegPos->storeInDb() ;

				$tmpCuCommPos->copyFrom( $myKundeBelegPos) ;
				$tmpCuCommPos->CuCommNo	=	$tmpCuComm[$liefNr]->CuCommNo ;
				$tmpCuCommPos->MengeGeliefert	=	$tmpCuCommPos->Menge ;
				$tmpCuCommPos->storeInDb() ;
			}
		}
		/**
		 * create bill(s)-of-delivery
		 */
		FDbg::dump( "KundeBeleg.php::createDirBest(): creating bill(s)-of-delivery") ;
		$myCuCommDoc	=	new CuCommDoc() ;
		$myCuDlvr	=	new CuDlvr() ;
		$myCuDlvrDoc	=	new CuDlvrDoc() ;
		foreach ( $tmpCuComm AS $liefNr => $cuComm) {
			FDbg::dump( "$liefNr :=> $cuComm->CuCommNo") ;

			FDbg::dump( "KundeBeleg.php::createDirBest(): working on CuComm(Doc)") ;
			$cuComm->setTexte() ;
			$myCuCommDoc->setKey( $cuComm->CuCommNo) ;
			$myCuCommDoc->createPDF() ;

			FDbg::dump( "KundeBeleg.php::createDirBest(): creating CuDlvr") ;
			$myCuDlvr->newFromCuComm( "", -1, $cuComm->CuCommNo) ;
			$myCuDlvr->setTexte() ;
			$myCuDlvr->buche() ;
			$myCuDlvrDoc->setKey( $myCuDlvr->CuDlvrNo) ;
			$myCuDlvrDoc->createPDF() ;
		}
		/**
		 * create the real supplier orders
		 */
		$mySuOrdr	=	new SuOrdr() ;
		$mySuOrdrDoc	=	new SuOrdrDoc() ;
		$mySuDlvr	=	new SuDlvr() ;
		foreach ( $tmpPKundeBeleg AS $liefNr => $pKundeBeleg) {
			FDbg::dump( "KundeBeleg.php::createDirBest(): from pKundeBeleg: $pKundeBeleg->LiefNr / $pKundeBeleg->LiefKontaktNr") ;

			$mySuOrdr->newFromPKundeBeleg( "", -1, $pKundeBeleg->PAuftragNr) ;
			FDbg::dump( "KundeBeleg.php::createDirBest(): from SuOrdr: $mySuOrdr->LiefNr / $mySuOrdr->LiefKontaktNr") ;
			$mySuOrdr->AuftragNr	=	$this->AuftragNr ;
			$mySuOrdr->CuDlvrNo	=	$tmpCuComm[$liefNr]->CuCommNo ;
			$mySuOrdr->updateInDb() ;
			$mySuOrdr->setTexte() ;
			$mySuOrdr->setAnschreiben() ;
			$mySuOrdr->buche() ;

			$mySuOrdrDoc->setkey( $mySuOrdr->SuOrdrNo) ;
			$mySuOrdrDoc->createPDF() ;

			$mySuDlvr->newFromSuOrdr( "", -1, $mySuOrdr->SuOrdrNo) ;
			$mySuDlvr->setAllRcvd() ;
			$mySuDlvr->buche() ;

			$mySuOrdr->cons() ;
		}
		/**
		 * consolidate this order
		 */
		FDbg::end() ;
		return $this->consolidate() ;
	}
	/**
	 * This method sends an eMail, with the text named $_mailText coming from the 'Texte' Db-Table
	 * to the recipients
	 * @param string $_mailText	mand.: Name of the mail body in the 'Texte' Db-table
	 * @param string $_file	opt.: pdf-file in the Archive/KundeBeleg path to attach
	 * @param string $_from	opt.: sending mail address
	 * @param string $_to	opt.: receiving mail address
	 * @param string $_cc	opt.: cc mail address
	 * @param string $_bcc	opt.: bcc mail address
	 */
	function	mail( $_mailText, $_file, $_from, $_to, $_cc="", $_bcc="") {
		/**
		 * prepare the eMail attachment
		 */
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$fileName	=	"KundeBeleg".$this->AuftragNr.".pdf" ;
		parent::mail(  $_mailText, $_file, $fileName, $_from, $_to, $_cc, $_bcc) ;
		FDbg::end() ;
	}
	/**
	 *	RETRIEVAL METHODS
	 */
	/**
	 *
	 */
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "KundeBeleg", "AuftragNr", "def", "v_KundeBelegSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"AuftragNr like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KundeBelegPosition"	:
			$myObj	=	new FDbObject( "KundeBelegPosition", "Id", "def", "v_KundeBelegPositionList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( BelegNr = '" . $this->BelegNr . "') " ;
//			$filter2	=	"( ArticleDescription like '%".$sCrit."%' )" ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
//			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( [ "PosNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getXMLDocInfo() {
		$ret	=	"<Document><![CDATA[" ;
		$filename	=	$this->path->Archive . "KundeBeleg/" . $this->AuftragNr . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "/KundeBeleg/" . $this->AuftragNr . ".pdf" ;
		}
		$ret	.=	"]]></Document>" ;
		return $ret ;
	}
	/**
	 *	PRINTING FUNCTIONS
	 */
	/**
	 *
	 */
	function	createPDF( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myKundeBelegDoc	=	new KundeBelegDoc( $_key, true) ;
		$myName	=	$myKundeBelegDoc->createPDF( $_key, $_id, $_val) ;
		$this->pdfName	=	$myKundeBelegDoc->pdfName ;
		$this->fullPDFName	=	$myKundeBelegDoc->fullPDFName ;
		$reply->replyData	=	"<Reference>" . $this->fullPDFName . "</Reference>\n" ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * returns the name of the PDF file which has been created
	 */
	function	getPDF( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaAppPDF ;
		$myKundeBelegDoc	=	new KundeBelegDoc( $_key, false) ;
		$reply->pdfName	=	$myKundeBelegDoc->pdfName ;
		$reply->fullPDFName	=	$myKundeBelegDoc->fullPDFName ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	printPDF( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myKundeBelegDoc	=	new KundeBelegDoc( $_key, false) ;
		$this->pdfName	=	$myKundeBelegDoc->pdfName ;
		$this->fullPDFName	=	$myKundeBelegDoc->fullPDFName ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
}
?>
