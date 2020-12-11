<?php

/**
 * KundeAuftrag.php - Basis Anwendungsklasse fuer Kundenbestellung (KundeAuftrag)
 *
 *	Definiert die Klassen:
 *		KundeAuftrag
 *
 * Einige Parameter haben eine extrem wichtige Bedeutung fuer das Totalsystem, d.h. von der
 * Bestellung ueber die Kommissionierung und den Lieferschein bis hin zur Rechnung.
 *
 * Attribut:	PosType
 *
 * Dieses Attribut beschreibt wie sich eine Position in der Bestellung in den verschiedenen Phasen
 * verhaelt.
 * Bei der Erzeugung von Kommisison, Lieferung und Rechnung werden grundsaetzlich alle Positionen
 * uebernommen deren Menge in dem entsprechenden Papier > 0 ist (Kommission: Menge noch zu liefern; Lieferschein: jetzt
 * geliefert; Rechnung: berechnete Menge).
 * Eine "NORMALe" Position wird im Lager reserviert (falls der Article an sich reserviert werden muss), wird
 * kommissioniert, geliefert und ebenfalls berechnet.
 * Eine "LieFeRuNG" Position wird im Lager reserviert (s.o.). Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp gelistet. Auf der Rechnung wird dieser Positionstyp NICHT gelistet.
 * Eine "ReCHNunG" Position wird im Lager NICHT reservert. Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp nicht gelistet. Auf der Rechnung wird dieser Typ gelistet.
 * Eine "KOMPonenten" Position wird im Lager reserviert, auf dem
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @package Application
 * @version 0.1
 * @filesource
 */
/**
 * KundeAuftrag - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BKundeAuftrag which should
 * not be modified.
 *
 * @package Application
 * @subpackage KundeAuftrag
 */
class	KundeAuftrag	extends	AppObject	{

	private	$tmpKundeAuftragPosition ;

	const	DOCAE	=	"AE" ;		// order document
	const	DOCSD	=	"SD" ;		// self-declaration (Eigenerklaerung)
	const	DOCMI	=	"MI" ;		// order confirmation
	private	static	$rDocType	=	array (
						KundeAuftrag::DOCAE	=> "Auftragserteilung",
						KundeAuftrag::DOCSD	=> "Eigenerklärung",
						KundeAuftrag::DOCMI	=> "Sonstiges"
					) ;
	public	$KVName	;
	public	$RezeptNr ;
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
		parent::__construct( "KundeAuftrag", "ERPNr") ;
		$this->addCol( "KVName", "char") ;
		$this->addCol( "RezeptNr", "char") ;
		$this->KVName	=	FTr::tr( "Nicht ausgewählt") ;
		$this->RezeptNr	=	"<<<NEU>>>" ;
		$this->_valid	=	false ;
		if ( strlen( $_myAuftragNr) > 0) {
			$this->setAuftragNr( $_myAuftragNr) ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myAuftragNr')", "no order number specified!") ;
		}
		FDbg::end() ;
	}

	/**
	 *
	 */
	function	setKey( $_myAuftragNr) {
		parent::setKey( $_myAuftragNr) ;
		return $this->_valid ;
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
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_val != "") {
			$this->_addDep( $_key, $_id, $_val, $reply) ;
		} else {
			$myKundeAuftrag	=	new KundeAuftrag() ;
			if ( $myKundeAuftrag->first( "LENGTH(ERPNr) = 12", "ERPNr DESC")) {
				$this->getFromPostL() ;
				$this->ERPNr	=	sprintf( "%012d", intval( $myKundeAuftrag->ERPNr) + 1) ;
				$this->AuftragNr	=	sprintf( "%d", intval( $myKundeAuftrag->AuftragNr) + 1) ;
				$this->storeInDb() ;
			} else {
				$this->getFromPostL() ;
				$this->ERPNr	=	sprintf( "%012d", intval( $myKundeAuftrag->ERPNr) + 1) ;
				$this->AuftragNr	=	sprintf( "%d", intval( $myKundeAuftrag->AuftragNr) + 1) ;
				$this->storeInDb() ;
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"object[".$this->cacheName."], Artikel invalid after creation!'") ;
			}
		}
		FDbg::end() ;
		return $this->getAsXML() ;
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
			if ( $_val != "") {
				$this->_delDep( $_key, $_id, $_val, $reply) ;
			} else {
				$myKundeAuftragPosition	=	new KundeAuftragPosition() ;
				$myKundeAuftragPosition->removeFromDbWhere( "AuftragNr = '" . $this->AuftragNr . "'") ;
				$this->removeFromDb() ;
			}
		} else {
			$e	=	new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)", "the object [" . $this->$keyCol . "] is locked!") ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getNextAsXML( $_key, $_id, $_val, $reply) ;
	}

	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "KundeAuftragPosition") {
			$myKundeAuftragPosition	=	new KundeAuftragPosition() ;
			$myKundeAuftragPosition->getFromPostL() ;
			$myKundeAuftragPosition->ERPNr	=	$this->ERPNr ;
			$myKundeAuftragPosition->AuftragNr	=	$this->AuftragNr ;
			$myKundeAuftragPosition->storeInDb() ;
			$reply->message	=	FTr::tr( "Auftragsposition succesfully added!") ;
			$this->getList( $_key, $_id, $objName, $reply) ;
		}
		FDbg::end() ;
		return $reply ;
	}

	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	_delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "KundeAuftragPosition") {
			try {
				$tmpObj	=	new $objName() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					$tmpObj->removeFromDb() ;
				} else {
					$e	=	new Exception( __FILE__ . "::" . __CLASS__ . "::" . __METHOD__ . ", [Id='$_id']) dependent is INVALID !") ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		}
		FDbg::end() ;
		return $reply ;
	}

	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"KundeAuftragPosition"	:
			$myKundeAuftragPosition	=	new KundeAuftragPosition() ;
			if ( $_id == -1) {
				$myKundeAuftragPosition->Id	=	-1 ;
				$myKundeAuftragPosition->ERPNr	=	$this->ERPNr ;
				$myKundeAuftragPosition->AuftragNr	=	$this->AuftragNr ;
			} else {
				$myKundeAuftragPosition->setId( $_id) ;
			}
			$reply	=	$myKundeAuftragPosition->getAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		default	:
			$reply	=	parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
		return $reply ;
	}

	/**
	 *
	 */
	function	addArtikel( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$source	=	$_POST[ "Source"] ;
		switch ( $source) {
		case	"HMVVerzeichnis"	:
			$myHMV_EP	=	new HMV_EP( $_POST[ "NeueHMVNr"]) ;
			if ( $myHMV_EP->isValid()) {
				$myKundeAuftragPosition	=	new KundeAuftragPosition() ;
				$myKundeAuftragPosition->ERPNr	=	$this->ERPNr ;
				$myKundeAuftragPosition->AuftragNr	=	$this->AuftragNr ;
				$myKundeAuftragPosition->HMVNr	=	$myHMV_EP->HMVNr ;
				$myKundeAuftragPosition->Menge	=	1 ;
				$myKundeAuftragPosition->storeInDb() ;
			} else {
				$reply->replyStatus	=	-1002 ;
				$reply->replyStatusInfo	=	$this->className ;
				$reply->replyStatusText	=	"Hilfsmittel [{$_POST[ "NeueHMVNr"]}]nicht gefunden!" ;
			}
			break ;
		case	"Artikel"	:
			$myArtikel	=	new Artikel( $_POST[ "NeueArtikelNr"]) ;
			if ( $myArtikel->isValid()) {
				$myKundeAuftragPosition	=	new KundeAuftragPosition() ;
				$myKundeAuftragPosition->ERPNr	=	$this->ERPNr ;
				$myKundeAuftragPosition->AuftragNr	=	$this->AuftragNr ;
				$myKundeAuftragPosition->ArtikelNr	=	$myArtikel->ArtikelNr ;
				$myKundeAuftragPosition->HMVNr	=	$myArtikel->HMVNr ;
				$myKundeAuftragPosition->Menge	=	2 ;
				$myKundeAuftragPosition->storeInDb() ;
			} else {
				$reply->replyStatus	=	-1003 ;
				$reply->replyStatusInfo	=	$this->className ;
				$reply->replyStatusText	=	"Artikel [{$_POST[ "NeueArtikelNr"]}]nicht gefunden!" ;
			}
			break ;
		default	:
			$reply->replyStatus	=	-1001 ;
				$reply->replyStatusInfo	=	$this->className ;
			$reply->replyStatusText	=	"Datenquelle unbekannt!" ;
			break ;
		}
		$this->getAsXML( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}

	/**
	 *
	 */
	function	rezeptNeu( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;

		/**
		 * Ermittle die LEIKNr der aktuellen Filiale
		 */
		$myFiliale	=	new Filiale( $this->__getAppConfig()->fallback->FilialeERPNr) ;
		if ( $myFiliale->isValid()) {
			$this->IKNr_LE	=	$myFiliale->IKNr ;
		} else {
			$this->IKNr_LE	=	"INVALID" ;
		}
		$myKundeAuftrag	=	new KundeAuftrag() ;
		if ( $myKundeAuftrag->first( "LENGTH(ERPNr) = 12", "ERPNr DESC")) {
			$this->getFromPostL() ;
			$this->ERPNr	=	sprintf( "<%012d>", intval( $myKundeAuftrag->ERPNr) + 1) ;
			$this->AuftragNr	=	sprintf( "<%d>", intval( $myKundeAuftrag->AuftragNr) + 1) ;
		} else {
			$this->AuftragNr	=	"<<<NEU>>>" ;
			$this->ERPNr	=	"<<<NEU>>>" ;
		}
		$this->RezeptNr	=	$_GET['sessionId'] ;
		$this->getAsXML( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}

	/**
	 *
	 */
	function	rezeptAbschluss( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;

		/**
		 * Übergebene LEIKNr prüfen
		 */
		$proceed	=	true ;
		if ( $proceed) {
			$this->Filiale	=	$this->__getAppConfig()->fallback->FilialeERPNr ;
			$myFiliale	=	new Filiale( $this->__getAppConfig()->fallback->FilialeERPNr) ;
			if ( $myFiliale->isValid()) {
			} else {
			}
		}

		/**
		 * Auftrag ERP Nr. ermitteln
		 */
		if ( $proceed) {
			$myKundeAuftrag	=	new KundeAuftrag() ;
			if ( $myKundeAuftrag->first( "LENGTH(ERPNr) = 12", "ERPNr DESC")) {
				$this->getFromPostL() ;
				$this->ERPNr	=	sprintf( "%012d", intval( $myKundeAuftrag->ERPNr) + 1) ;
				$this->AuftragNr	=	sprintf( "%d", intval( $myKundeAuftrag->AuftragNr) + 1) ;
			}
		}
		if ( $proceed) {
			$this->storeInDb() ;
		}
		$this->getAsXML( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}

	/**
	 *
	 */
	function	renumberItems( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$this->_renumber( "KundeAuftragPosition", 10) ;
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
		$actKundeAuftragPosition	=	new KundeAuftragPosition() ;
		$cond	=	"AuftragNr = '$this->AuftragNr' ORDER BY PosNr, SubPosNr " ;
		for ( $actKundeAuftragPosition->_firstFromDb( $cond) ;
				$actKundeAuftragPosition->isValid() ;
				$actKundeAuftragPosition->_nextFromDb()) {
			try {
				$actKundeAuftragPosition->_buche( $_sign) ;
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
		$actKundeAuftrag	=	new KundeAuftrag() ;
		for ( $actKundeAuftrag->_firstFromDb( "AuftragNr like '%' ") ;
				$actKundeAuftrag->_valid ;
				$actKundeAuftrag->_nextFromDb()) {
			error_log( "KundeAuftrag.php::KundeAuftrag::bucheAll(): booking AuftragNr " . $actKundeAuftrag->AuftragNr) ;
			$actKundeAuftrag->buche() ;
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
		$actKundeAuftrag	=	new KundeAuftrag() ;
		for ( $actKundeAuftrag->_firstFromDb( "AuftragNr like '%' ") ;
				$actKundeAuftrag->_valid ;
				$actKundeAuftrag->_nextFromDb()) {
			error_log( "KundeAuftrag.php::KundeAuftrag::bucheAll(): un-booking AuftragNr " . $actKundeAuftrag->AuftragNr) ;
			$actKundeAuftrag->unbuche() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 */
	static	function	_clearAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		FDb::query( "UPDATE KundeAuftragPosition SET MengeReserviert = 0 ") ;
		FDbg::end() ;
	}
	static	function	_markAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$ret	=	"" ;
		$actKundeAuftrag	=	new KundeAuftrag() ;
		$crit	=	"AuftragNr LIKE '%%' AND Status < 90 " .		// only the open ones
					"AND Datum > '$_startDate' " .					// in the given date range
					"AND Datum <= '$_endDate' " ;
		for ( $actKundeAuftrag->_firstFromDb( $crit) ;
				$actKundeAuftrag->_valid ;
				$actKundeAuftrag->_nextFromDb()) {
			$actKundeAuftrag->buche() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Create a new Customer Commission (CuComm) for this Customer Order (KundeAuftrag)
	 * If an open Commision exists a Reference to this open CuComm will be returned
	 * @param string $_key Number of the Customer RFQ (CuRFQ) which shall be turned into a Customer Quotation (CuQuot)
	 * @param int $_id unused
	 * @param mixed $_val unused
	 */
	function	newCuComm( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$newCuComm	=	new CuComm() ;
		$newCuComm->newFromKundeAuftrag( '', -1, $_key) ;
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
		$newCuInvc->newFromKundeAuftragA( '', -1, $_key) ;
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
		$newCuInvc->newFromKundeAuftragOL( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CuInvc</ObjectClass>\n<ObjectKey>$newCuInvc->CuInvcNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PKundeAuftrag)
	 *
	 * @return void
	 */
	function	newFromCuCart( $_key, $_id, $_cuCartNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_cuCartNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CuCart", $_cuCartNo, "", "", "900000", "949999") ;		// create a new instance
		$myKundeAuftragPosition	=	new KundeAuftragPosition( $this->AuftragNr) ;
		for ( $valid = $myKundeAuftragPosition->firstFromDb( "AuftragNr", "", null, "", "ORDER BY PosNr, SubPosNr ") ;
				$valid ;
				$valid = $myKundeAuftragPosition->nextFromDb()) {
//			$myKundeAuftragPosition->updateInDb() ;
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PKundeAuftrag)
	 *
	 * @return void
	 */
	function	newFromCuRFQ( $_key, $_id, $_cuRFQNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CuRFQ", $_cuRFQNo) ;		// create a new instance
		$myKundeAuftragPosition	=	new KundeAuftragPosition( $this->AuftragNr) ;
		for ( $valid = $myKundeAuftragPosition->firstFromDb( "AuftragNr", "", null, "", "ORDER BY PosNr, SubPosNr ") ;
				$valid ;
				$valid = $myKundeAuftragPosition->nextFromDb()) {
//			$myKundeAuftragPosition->updateInDb() ;
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PKundeAuftrag)
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
	 * Create a Customer Order based on the provided Temp. Customer Order (PKundeAuftrag)
	 *
	 * @return void
	 */
	function	newFromPKundeAuftrag( $_key, $_id, $_pAuftragNr) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_pAuftragNr')") ;
		$this->_newFrom( "PKundeAuftrag", $_pAuftragNr) ;		// create a new instance
		$myKundeAuftragPosition	=	new KundeAuftragPosition( $this->AuftragNr) ;
		for ( $valid = $myKundeAuftragPosition->firstFromDb( "AuftragNr", "", null, "", "ORDER BY PosNr, SubPosNr ") ;
				$valid ;
				$valid = $myKundeAuftragPosition->nextFromDb()) {
//			$myKundeAuftragPosition->updateInDb() ;
		}
		$ret	=	$this->getXMLComplete() ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Setzt den Prefix sowie den Postfix der Customernbestellung auf die Default Werte.
	 * Die Default Werte werden bestimmt durch die f�r den Customern abgespeicherten Wert
	 * f�r Language (z.B. de_de, en_us).
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: KundeAuftrag_setTexte( @status, <AuftragNr>).
	 *
	 * @return void
	 */
	function	setTexte( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		try {
			$myTexte	=	new Texte( "KundeAuftragPrefix", $this->KundeNr, $this->Customer->Language) ;
			$this->Prefix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Prefix") ;
			$myTexte	=	new Texte( "KundeAuftragPositiontfix", $this->KundeNr, $this->Customer->Language) ;
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
			$myTexte	=	new Texte( "KundeAuftragEMail", $this->KundeNr, $this->Customer->Language) ;
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
			$this->tmpKundeAuftragPosition	=	new KundeAuftragPosition() ;
			$this->tmpKundeAuftragPosition->Id	=	$_id ;
			$this->tmpKundeAuftragPosition->fetchFromDbById() ;
			if ( $this->tmpKundeAuftragPosition->_valid == 1) {
				FDbg::dumpL( 0x01000000, "KundeAuftrag::updAddText: refers to PosNr=%d", $this->tmpKundeAuftragPosition->PosNr) ;
				$this->tmpKundeAuftragPosition->AddText	=	$_text ;
				$this->tmpKundeAuftragPosition->updateInDb() ;
			} else {
				throw new Exception( 'KundeAuftrag::updAddText: KundeAuftragPosition[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->tmpKundeAuftragPosition ;
	}
	/**
	 *
	 * @return KundeAuftragPosition
	 */
	function	getFirstPos() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$this->tmpKundeAuftragPosition	=	new KundeAuftragPosition() ;
		$this->tmpKundeAuftragPosition->AuftragNr	=	$this->AuftragNr ;
		$this->tmpKundeAuftragPosition->_firstFromDb() ;
		FDbg::end() ;
		return $this->tmpKundeAuftragPosition ;
	}
	/**
	 *
	 * @return KundeAuftragPosition
	 */
	function	getNextPos() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$this->tmpKundeAuftragPosition->_nextFromDb() ;
		FDbg::end() ;
		return $this->tmpKundeAuftragPosition ;
	}
	/**
	 *
	 */
	function	updateHdlg() {
		/**	delete all handling items	**/
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		FDb::query( "DELETE FROM KundeAuftragPosition WHERE AuftragNr = '$this->AuftragNr' AND ArticleNo like 'HDLGPSCH%'") ;
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
		$this->mail( "KundeAuftragEMail", null, "", "", "", "") ;
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
					$this->path->Archive."KundeAuftrag/".$this->AuftragNr.".pdf", "", "", "miskhwe",
					3) ;
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	KundeAuftrag::ONGOING ;		// ueber "Normal"-FAX
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
	 * returns the XML-formatted list of all items in this KundeAuftrag
	 */
	function	consolidate( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 * update the quantities which have been delivered already
		 */
		$myItem	=	new FDbObject( "KundeAuftragPosition", "Id", "def", "v_KundeAuftragPositionConsolidateDeliveries") ; ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "AuftragNr = '".$this->AuftragNr."' ") ;
		$myItem->setIterOrder( [ "PosNr", "SubPosNr"]) ;
		$myKundeAuftragPosition	=	new KundeAuftragPosition() ;
		foreach ( $myItem as $actItem) {
			$myKundeAuftragPosition->setId( $actItem->Id) ;
			$myKundeAuftragPosition->QuantityDelivered	=	$actItem->DeliveredTotal ;
			$myKundeAuftragPosition->updateColInDb( "QuantityDelivered") ;
		}
		/**
		 * update the quantities which have been invoiced already
		 */
		$myItem	=	new FDbObject( "KundeAuftragPosition", "Id", "def", "v_KundeAuftragPositionConsolidateInvoices") ; ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "AuftragNr = '".$this->AuftragNr."' ") ;
		$myItem->setIterOrder( [ "PosNr", "SubPosNr"]) ;
		$myKundeAuftragPosition	=	new KundeAuftragPosition() ;
		foreach ( $myItem as $actItem) {
			$myKundeAuftragPosition->setId( $actItem->Id) ;
			$myKundeAuftragPosition->QuantityInvoiced	=	$actItem->InvoicedTotal ;
			$myKundeAuftragPosition->updateColInDb( "QuantityInvoiced") ;
		}
		/**
		 * update the quantities which have been invoiced already
		 */
		$myItem	=	new FDbObject( "KundeAuftragPosition", "Id", "def", "v_KundeAuftragPositionConsolidateOrder") ;
		$this->ItemCount	=	$myItem->getCountWhere( [ "AuftragNr = '".$this->AuftragNr."' AND SubPosNr = '' "]) ;
		$this->updateColInDb( "ItemCount") ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "AuftragNr = '".$this->AuftragNr."' ") ;
		$myItem->setIterOrder( [ "PosNr", "SubPosNr"]) ;
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
		FDbg::dump( "KundeAuftrag.php::KundeAuftrag::createDirBest( $_key, $_id, $_val): begin") ;
		/**
		 * create the (provisionary) PKundeAuftrag and CuComm for each distinct supplier
		 */
		$myLief	=	new Lief() ;
		$myKundeAuftragPosition	=	new KundeAuftragPosition( $this->AuftragNr) ;
		$myEKPriceR	=	new EKPriceR() ;
		$tmpPKundeAuftrag	=	array() ;
		$tmpCuComm	=	array() ;
		for ( $valid = $myKundeAuftragPosition->firstFromDb( "AuftragNr", "Article", array( "DirektVersand" => "txt"), "ArticleNo", "ORDER BY PosNr ") ;
				$valid ;
				$valid = $myKundeAuftragPosition->nextFromDb()) {
			if ( $myKundeAuftragPosition->DirektVersand == 1) {
				$validEKPR	=	$myEKPriceR->fetchFromDbWhere( "WHERE ArticleNo = '$myKundeAuftragPosition->ArticleNo' AND KalkBasis > 0") ;
				if ( $validEKPR) {
					FDbg::dump( "KundeAuftrag.php::createDirBest: unique EKPriceR identified ") ;
					$liefNr	=	$myEKPriceR->LiefNr ;
					if ( !isset( $tmpPKundeAuftrag[ $liefNr])) {
						$myLief->setLiefNr( $liefNr) ;
						$myPKundeAuftrag	=	new PKundeAuftrag() ;
						$myPKundeAuftrag->PAuftragNr	=	$myEKPriceR->LiefNr . "-" . $this->AuftragNr ;
						$myPKundeAuftrag->copyFrom( $this) ;
						$myPKundeAuftrag->Datum	=	$this->today() ;
						$myPKundeAuftrag->LiefNr	=	$liefNr ;
						$myPKundeAuftrag->LiefKontaktNr	=	$myLief->OrderingContact ;
						$myPKundeAuftrag->storeInDb() ;
						$tmpPKundeAuftrag[ $liefNr]	=	$myPKundeAuftrag ;

						$myCuComm	=	new CuComm() ;
						$myCuComm->add() ;			// get the CuComm no.
						$myCuComm->copyFrom( $this) ;		// copy meaningful attributes
						$myCuComm->Datum	=	$this->today() ;
						$myCuComm->updateInDb() ;
						$tmpCuComm[ $liefNr]	=	$myCuComm ;
					}
				} else if ( $status == -1) {
					FDbg::dump( "KundeAuftrag.php::createDirBest: no EKPriceR identified ") ;
				} else if ( $status == -2) {
					FDbg::dump( "KundeAuftrag.php::createDirBest: multiple EKPriceR identified ") ;
				}
			} else {
				FDbg::dump( "KundeAuftrag.php::createDirBest: article no. '$myKundeAuftragPosition->ArticleNo' not a direct delivery article") ;
			}
		}
		/**
		 * create the provisionary PKundeAuftragPosition and the CuCommItem
		 */
		$tmpPKundeAuftragPosition	=	new PKundeAuftragPosition() ;
		$tmpCuCommPos	=	new CuCommItem() ;
		for ( $valid = $myKundeAuftragPosition->firstFromDb( "AuftragNr", "Article", array( "DirektVersand" => "txt"), "ArticleNo", "ORDER BY PosNr ") ;
				$valid ;
				$valid = $myKundeAuftragPosition->nextFromDb()) {
			if ( $myKundeAuftragPosition->DirektVersand == 1) {
				$status	=	$myEKPriceR->fetchFromDbWhere( "WHERE ArticleNo = '$myKundeAuftragPosition->ArticleNo' AND KalkBasis > 0") ;
				$liefNr	=	$myEKPriceR->LiefNr ;

				$tmpPKundeAuftragPosition->copyFrom( $myKundeAuftragPosition) ;
				$tmpPKundeAuftragPosition->PAuftragNr	=	$myEKPriceR->LiefNr . "-" . $this->AuftragNr ;
				$tmpPKundeAuftragPosition->storeInDb() ;

				$tmpCuCommPos->copyFrom( $myKundeAuftragPosition) ;
				$tmpCuCommPos->CuCommNo	=	$tmpCuComm[$liefNr]->CuCommNo ;
				$tmpCuCommPos->MengeGeliefert	=	$tmpCuCommPos->Menge ;
				$tmpCuCommPos->storeInDb() ;
			}
		}
		/**
		 * create bill(s)-of-delivery
		 */
		FDbg::dump( "KundeAuftrag.php::createDirBest(): creating bill(s)-of-delivery") ;
		$myCuCommDoc	=	new CuCommDoc() ;
		$myCuDlvr	=	new CuDlvr() ;
		$myCuDlvrDoc	=	new CuDlvrDoc() ;
		foreach ( $tmpCuComm AS $liefNr => $cuComm) {
			FDbg::dump( "$liefNr :=> $cuComm->CuCommNo") ;

			FDbg::dump( "KundeAuftrag.php::createDirBest(): working on CuComm(Doc)") ;
			$cuComm->setTexte() ;
			$myCuCommDoc->setKey( $cuComm->CuCommNo) ;
			$myCuCommDoc->createPDF() ;

			FDbg::dump( "KundeAuftrag.php::createDirBest(): creating CuDlvr") ;
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
		foreach ( $tmpPKundeAuftrag AS $liefNr => $pKundeAuftrag) {
			FDbg::dump( "KundeAuftrag.php::createDirBest(): from pKundeAuftrag: $pKundeAuftrag->LiefNr / $pKundeAuftrag->LiefKontaktNr") ;

			$mySuOrdr->newFromPKundeAuftrag( "", -1, $pKundeAuftrag->PAuftragNr) ;
			FDbg::dump( "KundeAuftrag.php::createDirBest(): from SuOrdr: $mySuOrdr->LiefNr / $mySuOrdr->LiefKontaktNr") ;
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
	 * @param string $_file	opt.: pdf-file in the Archive/KundeAuftrag path to attach
	 * @param string $_from	opt.: sending mail address
	 * @param string $_to	opt.: receiving mail address
	 * @param string $_cc	opt.: cc mail address
	 * @param string $_bcc	opt.: bcc mail address
	 */
	function	mail( $_mailText, $_file="", $_fileName="", $_from="", $_to="", $_cc="", $_bcc="") {
		/**
		 * prepare the eMail attachment
		 */
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$fileName	=	"KundeAuftrag".$this->AuftragNr.".pdf" ;
		parent::mail(  $_mailText, $_file, $fileName, $_from, $_to, $_cc, $_bcc) ;
		FDbg::end() ;
	}

	/**
	 *	RETRIEVAL METHODS
	 */

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
		case	""				:
		case	"KundeAuftrag"	:
			$myObj	=	new FDbObject( "KundeAuftrag", "AuftragNr", "def", "v_KundeAuftragSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"AuftragNr like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KundeAuftragPosition"	:
			$myObj	=	new FDbObject( "KundeAuftragPosition", "Id", "def", "v_KundeAuftragPositionList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( ERPNr = '" . $this->ERPNr . "') " ;
//			$filter2	=	"( ArticleDescription like '%".$sCrit."%' )" ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
//			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( [ "PosNr", "UPosNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"KundeAuftragObjekt"	:
			$myObj	=	new FDbObject( "KundeAuftragObjekt", "Id", "def", "v_KundeAuftragObjektList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( AuftragNr = '" . $this->AuftragNr . "') " ;
//			$filter2	=	"( ArticleDescription like '%".$sCrit."%' )" ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
//			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( [ "Datum"]) ;
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
		$filename	=	$this->path->Archive . "KundeAuftrag/" . $this->AuftragNr . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "/KundeAuftrag/" . $this->AuftragNr . ".pdf" ;
		}
		$ret	.=	"]]></Document>" ;
		return $ret ;
	}
	/**
	 *
	 */
	function	checkComm( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$res	=	"Check auf Kommissionierbarkeit<br/>" ;
		$actKundeAuftragPosition	=	new KundeAuftragPosition() ;
		$actArticle	=	new Article() ;
		$actArticleBestand	=	new ArticleBestand() ;
		for ( $actKundeAuftragPosition->_firstFromDb( "AuftragNr = '$this->AuftragNr' AND Menge > MengeBereitsGeliefert ") ;
				$actKundeAuftragPosition->_valid == 1 ;
				$actKundeAuftragPosition->_nextFromDb()) {
			$actArticleBestand->getDefault( $actKundeAuftragPosition->ArticleNo) ;
			$res	.=	"["
						. $actKundeAuftragPosition->ArticleNo . " / "
						. $actKundeAuftragPosition->Menge . " / "
						. $actKundeAuftragPosition->MengeBereitsGeliefert . " / "
						. $actArticleBestand->Lagerbestand . " / "
						. "]<br/>" ;
		}
		FDbg::end() ;
		return $res ;
	}
	function	setStatComm( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$res	=	0 ;
		$canCommissionAll	=	true ;
		$canCommissionPart	=	false ;
		$actKundeAuftragPosition	=	new KundeAuftragPosition() ;
		$actArticleBestand	=	new ArticleBestand() ;
		for ( $actKundeAuftragPosition->_firstFromDb( "AuftragNr = '$this->AuftragNr' AND Menge > MengeBereitsGeliefert ") ;
				$actKundeAuftragPosition->_valid == 1 ;
				$actKundeAuftragPosition->_nextFromDb()) {
			$actArticleBestand->getDefault( $actKundeAuftragPosition->ArticleNo) ;
			if ( ( $actKundeAuftragPosition->Menge - $actKundeAuftragPosition->MengeBereitsGeliefert) <= $actArticleBestand->Lagerbestand) {
				$canCommissionPart	=	true ;
			} else {
				$canCommissionAll	=	false ;
			}
		}
		$oldStat	=	$this->StatComm ;
		if ( $canCommissionAll) {
			$res	=	KundeAuftrag::COMMFULL ;
		} else 		if ( $canCommissionPart) {
			$res	=	KundeAuftrag::COMMPART ;
		} else {
			$res	=	KundeAuftrag::COMMNO ;
		}
		$this->StatComm	=	$res ;
		$this->updateColInDb( "StatComm") ;
		if ( $oldStat != $res) {
			try {
				$this->StatComm	=	$res ;
				$this->updateColInDb( "StatComm") ;
				$this->sendCommInfoByEMail( $oldStat, $res) ;
			} catch ( Exeption $e) {
				throw( new Exception( "KundeAuftrag.php::KundeAuftrag::setStatComm(...): problem with AuftragNr: ".$this->AuftragNr)) ;
			}
		}
		FDbg::end() ;
		return $res ;
	}
	static	function	setStatCommAll( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$res	=	"" ;
		$actKundeAuftrag	=	new KundeAuftrag() ;
		for ( $actKundeAuftrag->_firstFromDb( "Status < 90 AND StatLief < 90 ") ;
				$actKundeAuftrag->_valid == 1 ;
				$actKundeAuftrag->_nextFromDb()) {
			$actKundeAuftrag->setStatComm() ;
		}
		FDbg::end() ;
		return $res ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	sendCommInfoByEMail( $_old, $_new) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		try {
			$this->upd( '', '', '') ;
			$newMail	=	new mimeMail( $this->eMail->Sales,
								"welter@modis-gmbh.eu",
								$this->eMail->Sales,
								FTr::tr( "Order No. #1, dated #2, change in article availability", array( "%s:".$this->AuftragNr, "%s:".convDate( $this->Datum))),
								"Bcc: ".$this->eMail->Archive."\n") ;

			$myMail	=	"Kommissionierf�higkeit des Austrags ge�ndert von " . self::$rStatComm[$_old] . " nach " . self::$rStatComm[$_new] ;

			$myText	=	new mimeData( "multipart/alternative") ;
			$myText->addData( "text/plain", xmlToPlain( "<div>".$myMail."</div>")) ;
			$myText->addData( "text/html", "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><head></head><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n".$myMail."</body></html>", "", true) ;

			$myBody	=	new mimeData( "multipart/mixed") ;
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			$myBody->addData( "application/pdf", $this->path->Archive."KundeAuftrag/".$this->AuftragNr.".pdf", $this->AuftragNr.".pdf", true) ;

			$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
			$mailSendResult	=	$newMail->send() ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	/**
	 * Export this order as XML file to teh XML/down directory
	 * @param	string	$_key
	 * @param	int	$_id
	 * @param	mixed	$_val
	 */
	function	export( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$oFile	=	fopen( $this->path->Archive."XML/down/KundeAuftrag".$this->AuftragNr.".xml", "w+") ;
		fwrite( $oFile, "<KundeAuftragPaket>\n") ;
		$buffer	=	$this->getXMLF() ;
		fwrite( $oFile, $buffer) ;
		$myKundeAuftragPosition	=	new KundeAuftragPosition() ;
		$myKundeAuftragPosition->AuftragNr	=	$this->AuftragNr ;
		for ( $myKundeAuftragPosition->_firstFromDb( "AuftragNr='$this->AuftragNr' ORDER BY PosNr ") ;
					$myKundeAuftragPosition->_valid == 1 ;
					$myKundeAuftragPosition->_nextFromDb()) {
			$buffer	=	$myKundeAuftragPosition->getXMLF() ;
			fwrite( $oFile, $buffer) ;
		}
		fwrite( $oFile, "</KundeAuftragPaket>\n") ;
		fclose( $oFile) ;
		return $this->getXMLComplete() ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	_consolidate( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$myKundeAuftragPosition	=	new KundeAuftragPosition() ;
		$myKundeAuftragPosition->setIterCond( "AuftragNr = '".$this->AuftragNr."' ") ;
		$myKundeAuftragPosition->setIterOrder( "ORDER BY PosNr, SubPosNr ") ;
		$myArticle	=	new Article() ;
		/**
		 * determine the total taxable amounts per tax-class
		 */
		$myTaxes	=	array() ;
		foreach ( $myKundeAuftragPosition as $key => $line) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "KundeAuftrag.php", "KundeAuftrag", "addToJournal(...)",
								"InvcNo " . $line->AuftragNr . " PosNr " . $line->PosNr . $line->SubPosNr) ;
			$myArticle->setArticleNo( $line->ArticleNo) ;
			if ( ! isset( $myTaxes[ $myArticle->TaxSatz])) {
				$myTaxes[ $myArticle->TaxSatz]	=	new Tax( $myArticle->TaxSatz) ;
				$myTaxes[ $myArticle->TaxSatz]->Total	=	0.0 ;
			}
			$myTax	=	$myTaxes[ $myArticle->TaxSatz] ;
			$myTax->Total	+=	$line->Menge * $line->Price ;
		}
		/**
		 * pull the journal line items together
		 */
		$myTotal	=	0.0 ;
		$myTotalTax	=	0.0 ;
		foreach ( $myTaxes as $ndx => $class) {
			$class->Tax	=	$class->Total * $class->ProzSatz / 100 ;
			$myTotal	+=	$class->Total ;
			$myTotalTax	+=	$class->Tax ;
		}
		$this->TotalPrice	=	$myTotal ;
		$this->updateColInDb( "TotalPrice") ;
		$this->TotalTax	=	$myTotalTax ;
		$this->updateColInDb( "TotalTax") ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 */
	function	consolidateAll( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$actKundeAuftrag	=	new KundeAuftrag() ;
		$actKundeAuftrag->setIterCond( "1 = 1 ") ;
		$actKundeAuftrag->setIterOrder( "ORDER BY AuftragNr ") ;
		foreach( $actKundeAuftrag as $ndx => $obj) {
			$obj->_consolidate() ;
		}
		$this->reload() ;
		FDbg::end() ;
		return $this->getXMLComplete() ;
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
		$myKundeAuftragDoc	=	new KundeAuftragDoc( $_key, true) ;
		$myName	=	$myKundeAuftragDoc->createPDF( $_key, $_id, $_val) ;
		$this->pdfName	=	$myKundeAuftragDoc->pdfName ;
		$this->fullPDFName	=	$myKundeAuftragDoc->fullPDFName ;
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
		$myKundeAuftragDoc	=	new KundeAuftragDoc( $_key, false) ;
		$reply->pdfName	=	$myKundeAuftragDoc->pdfName ;
		$reply->fullPDFName	=	$myKundeAuftragDoc->fullPDFName ;
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
		$myKundeAuftragDoc	=	new KundeAuftragDoc( $_key, false) ;
		$this->pdfName	=	$myKundeAuftragDoc->pdfName ;
		$this->fullPDFName	=	$myKundeAuftragDoc->fullPDFName ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}

	/**
	 *
	 */
	protected	function	_postInstantiate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}

	/**
	 *
	 */
	protected	function	_postLoad() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myKostentraeger	=	new Kostentraeger( $this->IKNr_KK) ;
		if ( $myKostentraeger->isValid()) {
			$this->KVName	=	$myKostentraeger->IKNr . ", " . $myKostentraeger->Name1 ;
		} else {
			$this->KVName	=	"ungültig" ;
		}
		FDbg::end() ;
	}
}
?>
