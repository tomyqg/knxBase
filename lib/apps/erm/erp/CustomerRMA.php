<?php

/**
 * CuRMA.php - Basis Anwendungsklasse fuer Kundenbestellung (CuRMA)
 *
 *	Definiert die Klassen:
 *		CuRMA
 *		CuRMAItem
 *
 * Einige Parameter haben eine extrem wichtige Bedeutung fuer das Gesamtsystem, d.h. von der
 * Bestellung ueber die Kommissionierung und den Lieferschein bis hin zur Rechnung.
 *
 * Attribut:	ItemType
 *
 * Dieses Attribut beschreibt wie sich eine Position in der Bestellung in den verschiedenen Phasen
 * verhaelt.
 * Bei der Erzeugung von Kommisison, Lieferung und Rechnung werden grundsaetzlich alle Positionen
 * uebernommen deren Menge in dem entsprechenden Papier > 0 ist (Kommission: Menge noch zu liefern; Lieferschein: jetzt
 * geliefert; Rechnung: berechnete Menge). Die EN
 * Eine "NORMALe" Position wird im Lager reserviert (falls der Artikel an sich reserviert werden muss), wird
 * kommissioniert, geliefert und ebenfalls berechnet.
 * Eine "LieFeRuNG" Position wird im Lager reserviert (s.o.). Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp gelistet. Auf der Rechnung wird dieser Positionstyp NICHT gelistet.
 * Eine "ReCHNunG" Position wird im Lager NICHT reservert. Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp nicht gelistet. Auf der Rechnung wird dieser Typ gelistet.
 * Eine "KOMPonenten" Position wird im Lager reserviert, auf dem 
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */

require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/AppObject.php") ;
require_once( "base/AppDepObject.php") ;
require_once( "MimeMail.php" );
require_once( "XmlTools.php" );

/**
 * CuRMA - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCuRMA which should
 * not be modified.
 *
 * @package Application
 * @subpackage CuRMA
 */
class	CuRMA	extends	AppObject	{

	private	$myKunde ;
	private	$myKundeKontakt ;
	private	$myRechKunde ;
	private	$myRechKundeKontakt ;
	private	$myLiefKunde ;
	private	$myLiefKundeKontakt ;
	private	$tmpCuRMAItem ;

	const	NEU		=	0 ;
	const	CONF	=	30 ;
	const	ONGOING	=	50 ;
	const	CLOSED	=	90 ;
	const	ONHOLD	=	980 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						CuRMA::NEU		=> "open",
						CuRMA::CONF	=> "confirme",
						CuRMA::ONGOING	=> "ongoing",
						CuRMA::CLOSED	=> "abgeschlossen",
						CuRMA::ONHOLD	=> "on-hold",
						CuRMA::CANCELLED	=> "cancelled"
					) ;

	const	DELIVNO		=	  0 ;
	const	DELIVPAR	=	 50 ;
	const	DELIVALL	=	 90 ;
	private	static	$rStatLief	=	array (
						CuRMA::DELIVNO		=> "nicht geliefert",
						CuRMA::DELIVPAR	=> "teilgeliefert",
						CuRMA::DELIVALL	=> "vollstaendig geliefert"
					) ;
	const	INVCDNO		=	  0 ;
	const	INVCDPAR	=	 50 ;
	const	INVCDALL	=	 90 ;
	private	static	$rStatRech	=	array (
						CuRMA::INVCDNO		=> "nicht berechnet",
						CuRMA::INVCDPAR	=> "teilberechnet",
						CuRMA::INVCDALL	=> "vollstaendig berechnet"
					) ;
	const	DOCAE	=	"AE" ;		// order document
	const	DOCMI	=	"MI" ;		// order confirmation
	private	static	$rDocType	=	array (
						CuRMA::DOCAE	=> "Auftragserteilung",
						CuRMA::DOCMI	=> "Sonstiges"
					) ;
	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (CuRMANr), in which case it will automatically
	 * (try to) load the respective Customer Order via the base class from the Database
	 * If the order data was loaded the customer data, and customer contact data, will also be loaded
	 * from the database.
	 *
	 * @param string $_myCuRMANr
	 * @return void
	 */
	function	__construct( $_myCuRMANr='') {
		FDbg::dumpL( 0x01000000, "CuRMA::__construct( '%s ')", $_myCuRMANr) ;
		AppObject::__construct( "CuRMA", "CuRMANr") ;
		$this->myKunde	=	new Kunde() ;
		$this->myKundeKontakt	=	new KundeKontakt() ;
		$this->myRechKunde	=	NULL ;
		$this->myRechKundeKontakt	=	NULL ;
		$this->myLiefKunde	=	NULL ;
		$this->myLiefKundeKontakt	=	NULL ;
		if ( strlen( $_myCuRMANr) > 0) {
			$this->setCuRMANr( $_myCuRMANr) ;
		} else {
			FDbg::dumpL( 0x01000000, "CuRMA::__construct(...): CuRMANr not specified !") ;
		}
		FDbg::dumpL( 0x01000000, "CuRMA::__construct(...) done") ;
	}

	/**
	 * set the Order Number (CuRMANr)
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
	function	setCuRMANr( $_myCuRMANr) {
		FDbg::dumpL( 0x01000000, "CuRMA::setCuRMANr('%s')", $_myCuRMANr) ;
		$this->CuRMANr	=	$_myCuRMANr ;
		if ( strlen( $_myCuRMANr) > 0) {
			$this->reload() ;
		}
		FDbg::dumpL( 0x01000000, "CuRMA::setCuRMANr('%s') is done", $_myCuRMANr) ;
	}

	/**
	 * set the Order Number (CuRMANr)
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
		FDbg::dumpL( 0x01000000, "CuRMA::reload()") ;
		$this->fetchFromDb() ;
		if ( $this->_valid == 1) {
			FDbg::dumpL( 0x01000000, "CuRMA::reload(): CuRMA is valid !") ;
			/**
			 *
			 */
			try {
				$this->myKunde->setKundeNr( $this->KundeNr) ;
				$this->myKundeKontakt->setKundeKontaktNr( $this->KundeNr, $this->KundeKontaktNr) ;
			} catch( Exception $e) {
				FDbg::dumpF( "CuRMA::reload(...): exception='%s'", $e->getMessage()) ;
			}
			/**
			 * check to see if there's a dedicated invoicing address attached
			 */
			if ( strlen( $this->RechKundeNr) > 0) {
				try {
					$this->myRechKunde	=	new Kunde( $this->RechKundeNr) ;
					$this->myRechKundeKontakt	=	new KundeKontakt( $this->myRechKunde->KundeNr, $this->RechKundeKontaktNr) ;
				} catch( Exception $e) {
					FDbg::dumpL( 0x01000000, "CuRMA::reload(...): exception='%s'", $e->getMessage()) ;
				}
			}
			/**
			 * check to see if there's a dedicated invoicing address attached
			 */
			if ( strlen( $this->LiefKundeNr) > 0) {
				try {
					$this->myLiefKunde	=	new Kunde( $this->LiefKundeNr) ;
					$this->myLiefKundeKontakt	=	new KundeKontakt( $this->myLiefKunde->KundeNr, $this->LiefKundeKontaktNr) ;
				} catch( Exception $e) {
					FDbg::dumpL( 0x01000000, "CuRMA::reload(...): exception='%s'", $e->getMessage()) ;
				}
			}
		}
		FDbg::dumpL( 0x01000000, "CuRMA::reload() is done") ;
	}

	/**
	 * Access the customer data.
	 *
	 * @return [Kunde]
	 */
	function	getKunde() {
		return $this->myKunde ;
	}

	/**
	 * Access the customer contact data.
	 *
	 * @return [KundeKontakt]
	 */
	function	getKundeKontakt() {
		return $this->myKundeKontakt ;
	}

	/**
	 * Access the customer data.
	 *
	 * @return [Kunde]
	 */
	function	newRechKunde() {
		$this->myRechKunde	=	new Kunde() ;
		$this->myRechKundeKontakt	=	new KundeKontakt() ;
	}

	/**
	 * Access the customer data.:q
	 *
	 * @return [Kunde]
	 */
	function	newLiefKunde() {
		$this->myLiefKunde	=	new Kunde() ;
		$this->myLiefKundeKontakt	=	new KundeKontakt() ;
	}

	/**
	 * Access the customer data.
	 *
	 * @return [Kunde]
	 */
	function	getLiefKunde() {
		return $this->myLiefKunde ;
	}

	/**
	 * Access the customer contact data.
	 *
	 * @return [LiefKundeKontakt]
	 */
	function	getLiefKundeKontakt() {
		return $this->myLiefKundeKontakt ;
	}

	/**
	 * Access the customer data.
	 *
	 * @return [RechKunde]
	 */
	function	getRechKunde() {
		return $this->myRechKunde ;
	}

	/**
	 * Access the customer contact data.
	 *
	 * @return [RechKundeKontakt]
	 */
	function	getRechKundeKontakt() {
		return $this->myRechKundeKontakt ;
	}

	/**
	 * 
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		$actArtikel	=	new Artikel() ;
		$actCuRMAItem	=	new CuRMAItem() ;
		$cond	=	"CuRMANr = '$this->CuRMANr' ORDER BY ItemNr, SubItemNr " ;
		for ( $actCuRMAItem->_firstFromDb( $cond) ;
				$actCuRMAItem->isValid() ;
				$actCuRMAItem->_nextFromDb()) {
//			error_log( "Reserviere: '$actCuRMAItem->ArtikelNr") ;
			try {
				if ( $actArtikel->setKey( $actCuRMAItem->ArtikelNr)) {
					if ( $_sign == -1) {
						$menge	=	$actCuRMAItem->MengeReserviert * $_sign ;
					} else {
						$menge	=	($actCuRMAItem->Menge * $actCuRMAItem->MengeProVPE) - $actCuRMAItem->MengeReserviert ;
					}
					try {
						$qtyBooked	=	$actArtikel->reserve( $menge) ;
						$actCuRMAItem->MengeReserviert	+=	$qtyBooked ;
						$actCuRMAItem->updateColInDb( "MengeReserviert") ;
					} catch( Exception $e) {
						
					}
				} else {
					throw new Exception( "CuRMA::_buche(...): Article['$actCuRMAItem->ArtikelNr'] not valid") ;
				}
			} catch( Exception $e) {
				error_log( $e->getMessage()) ;
			}
		}
	}

	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	buche( $_key="", $_id=-1, $_val="") {
		$this->_buche( 1) ;
		if ( $_key != "") {
			$ret	=	$this->getXMLComplete() ;
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
	function	unbuche( $_key="", $_id=-1, $_val="") {
		$this->_buche( -1) ;
		if ( $_key != "") {
			$ret	=	$this->getXMLComplete() ;
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
	function	bucheAll( $_key, $_id, $_val) {
		$ret	=	"" ;
		$actCuRMA	=	new CuRMA() ;
		for ( $actCuRMA->_firstFromDb( "CuRMANr like '%' ") ;
				$actCuRMA->_valid ;
				$actCuRMA->_nextFromDb()) {
			error_log( "CuRMA.php::CuRMA::bucheAll(): booking CuRMANr " . $actCuRMA->CuRMANr) ;
			$actCuRMA->buche() ;
		}
		return $ret ;
	}
	
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	unbucheAll( $_key, $_id, $_val) {
		$ret	=	"" ;
		$actCuRMA	=	new CuRMA() ;
		for ( $actCuRMA->_firstFromDb( "CuRMANr like '%' ") ;
				$actCuRMA->_valid ;
				$actCuRMA->_nextFromDb()) {
			error_log( "CuRMA.php::CuRMA::bucheAll(): un-booking CuRMANr " . $actCuRMA->CuRMANr) ;
			$actCuRMA->unbuche() ;
		}
		return $ret ;
	}
	
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TCuRMA)
	 *
	 * @return void
	 */
	function	newFromMZ( $_key, $_id, $_mzNr) {
		FDbg::dumpL( 0x01000000, "CuRMA::newFromMZ(...)") ;
		$query	=	sprintf( "CuRMA_newFromMZ( @status, '%s', @newCuRMANr) ; ", $_mzNr) ;
		try {
			$row	=	FDb::callProc( $query, "@newCuRMANr") ;
			$this->setCuRMANr( $row['@newCuRMANr']) ;
			$this->_addRem( "erstellt aus CuCart Nr. " . $_mzNr) ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA::newFromMZ(...): exception='%s'", $e->getMessage()) ;
		}
		return $this->getXMLComplete() ;
	}

	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TCuRMA)
	 *
	 * @return void
	 */
	function	newFromCuRFQ( $_key, $_id, $_cuRFQNo) {
		FDbg::dumpL( 0x01000000, "CuRMA::newFromCuRFQ(...)") ;
		$query	=	sprintf( "CuRMA_newFromCuRFQ( @status, '%s', @newCuRMANr) ; ", $_cuRFQNo) ;
		try {
			$row	=	FDb::callProc( $query, "@newCuRMANr") ;
			$this->setCuRMANr( $row['@newCuRMANr']) ;
			$this->_addRem( "erstellt aus Anfrage Nr. " . $_cuRFQNo) ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA::newFromCuRFQ(...): exception='%s'", $e->getMessage()) ;
		}
		return $this->getXMLComplete() ;
	}

	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TCuRMA)
	 *
	 * @return void
	 */
	function	newFromCuOffr( $_key, $_id, $_cuOffrNo) {
		FDbg::dumpL( 0x01000000, "CuRMA::newFromCuOffr(...)") ;
		$query	=	sprintf( "CuRMA_newFromCuOffr( @status, '%s', @newCuRMANr) ; ", $_cuOffrNo) ;
		try {
			$row	=	FDb::callProc( $query, "@newCuRMANr") ;
			$this->setCuRMANr( $row['@newCuRMANr']) ;
			$this->_addRem( "erstellt aus Angebot Nr. " . $_cuOffrNo) ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA::newFromCuOffr(...): exception='%s'", $e->getMessage()) ;
		}
		return $this->getXMLComplete() ;
	}

	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TCuRMA)
	 *
	 * @return void
	 */
	function	newFromTCuRMA( $_key, $_id, $_tCuRMANr) {
		FDbg::dumpL( 0x01000000, "CuRMA::newFromTCuRMA(...)") ;
		$query	=	sprintf( "CuRMA_newFromTCuRMA( @status, '%s', @newCuRMANr) ; ", $_tCuRMANr) ;
		try {
			$row	=	FDb::callProc( $query, "@newCuRMANr") ;
			$this->setCuRMANr( $row['@newCuRMANr']) ;
			$this->_addRem( "erstellt aus temp. Bestellung Nr. " . $_tCuRMANr) ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA::newFromTCuRMA(...): exception='%s'", $e->getMessage()) ;
		}
		return $this->getXMLComplete() ;
	}

	/**
	 * Create a new temporary order with the next available temp-order-nr and store
	 * the order in the database.
	 *
	 * @return void
	 */
	function	newCuRMA() {
		FDbg::dumpL( 0x01000000, "CuRMA::newCuRMA(...)") ;
		$query	=	sprintf( "CuRMA_new( @status, @newCuRMANr) ; ") ;
		try {
			$row	=	FDb::callProc( $query, "@newCuRMANr") ;
			$this->setCuRMANr( $row['@newCuRMANr']) ;
			if ( isset( $_SESSION['UserId'])) {
				$myText	=	date( "Ymd/Hi") . ": " . $_SESSION['UserId'] . ": Bestellung erstellt\n" ;
			} else {
				$myText	=	date( "Ymd/Hi") . ": " . "Hintergrund Prozess" . ": automatisch erstellt aus XML Bestellung \n" ;
			}
			$myText	.=	$this->Rem1 ;
			$this->Rem1	=	$myText ;
			$this->updateInDb() ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA.php::CuRMA::newCuRMA(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * set the Custoner Nr and Customer Contact Number
	 *
	 * Sets the customer nr. and the customer contact nr. for this order and fetches the related data from the database.
	 * The order is then also updated in the database with the new customer nr. and customer contact nr.
	 *
	 * @return void
	 */
	function	setKundeFromKKId( $_key, $_kkId, $_val) {
		FDbg::get()->dumpL( 0x01000000, "CuRMA::setKundeFromKKId( %d): ", $_kkId) ;
		$tmpKundeKontakt	=	new KundeKontakt() ;
		$tmpKundeKontakt->setId( $_kkId) ;
		if ( $tmpKundeKontakt->_valid == 1) {
			try {
				$this->KundeNr	=	$tmpKundeKontakt->KundeNr ;
				$this->KundeKontaktNr	=	$tmpKundeKontakt->KundeKontaktNr ;
				$this->updateInDb() ;
				$this->reload() ;
				$this->ModusLief	=	$this->myKunde->ModusLief ;
				$this->ModusRech	=	$this->myKunde->ModusRech ;
				$this->ModusSkonto	=	$this->myKunde->ModusSkonto ;
				$this->Rabatt	=	$this->myKunde->Rabatt ;
				if ( $this->Rabatt > 0) {
					$this->DiscountMode	=	Opt::DMDEALER ;
				} else {
					$this->DiscountMode	=	Opt::DMV1 ;
				}
				$this->updateInDb() ;
				$this->reload() ;
			} catch( Exception $e) {
				throw $e ;
			}
		} else {
			throw new Exception( "CuRMA::setKundeFromKKId( '$_key', '$_kkId', '$_val''): tmpKundeKontakt not valid") ;
		}
		FDbg::get()->dumpL( 0x01000000, "CuRMA::setKundeFromKKId(...): done") ;
		$ret	=	$this->getXMLString() ;
		return $ret ;
	}
	
	/**
	 * set the Custoner Nr and Customer Contact Number
	 *
	 * Sets the customer nr. and the customer contact nr. for this order and fetches the related data from the database.
	 * The order is then also updated in the database with the new customer nr. and customer contact nr.
	 *
	 * @return void
	 */
	function	setRechKundeFromKKId( $_key, $_kkId, $_val) {
		$tmpKundeKontakt	=	new KundeKontakt() ;
		$tmpKundeKontakt->setId( $_kkId) ;
		if ( $tmpKundeKontakt->_valid == 1) {
			try {
				$this->RechKundeNr	=	$tmpKundeKontakt->KundeNr ;
				$this->RechKundeKontaktNr	=	$tmpKundeKontakt->KundeKontaktNr ;
				$this->updateInDb() ;
				$this->reload() ;
			} catch( Exception $e) {
				FDbg::dumpF( "CuRMA::setKundeFromKKId(...): exception='%s'", $e->getMessage()) ;
			}
		} else {
			FDbg::get()->dump( "CuRMA::setKundeFromKKId(...): KundeKontakt not valid !") ;
		}
		$ret	=	$this->getXMLString() ;
		return $ret ;
	}
	
	/**
	 * set the Custoner Nr and Customer Contact Number
	 *
	 * Sets the customer nr. and the customer contact nr. for this order and fetches the related data from the database.
	 * The order is then also updated in the database with the new customer nr. and customer contact nr.
	 *
	 * @return void
	 */
	function	clearRechKunde() {
		try {
			$this->RechKundeNr	=	"" ;
			$this->RechKundeKontaktNr	=	"" ;
			$this->updateInDb() ;
			$this->reload() ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA::clearRechKunde(): exception='%s'", $e->getMessage()) ;
		}
	}
	
	/**
	 * set the Custoner Nr and Customer Contact Number
	 *
	 * Sets the customer nr. and the customer contact nr. for this order and fetches the related data from the database.
	 * The order is then also updated in the database with the new customer nr. and customer contact nr.
	 *
	 * @return void
	 */
	function	setLiefKundeFromKKId( $_key, $_kkId, $_val) {
		$tmpKundeKontakt	=	new KundeKontakt() ;
		$tmpKundeKontakt->setId( $_kkId) ;
		if ( $tmpKundeKontakt->_valid == 1) {
			try {
				$this->LiefKundeNr	=	$tmpKundeKontakt->KundeNr ;
				$this->LiefKundeKontaktNr	=	$tmpKundeKontakt->KundeKontaktNr ;
				$this->updateInDb() ;
				$this->reload() ;
			} catch( Exception $e) {
				FDbg::dumpF( "CuRMA::setKundeFromKKId(...): exception='%s'", $e->getMessage()) ;
			}
		} else {
			FDbg::get()->dump( "CuRMA::setKundeFromKKId(...): KundeKontakt not valid !") ;
		}
		$ret	=	$this->getXMLString() ;
		return $ret ;
	}
	
	/**
	 * set the Custoner Nr and Customer Contact Number
	 *
	 * Sets the customer nr. and the customer contact nr. for this order and fetches the related data from the database.
	 * The order is then also updated in the database with the new customer nr. and customer contact nr.
	 *
	 * @return void
	 */
	function	clearLiefKunde() {
		try {
			$this->LiefKundeNr	=	"" ;
			$this->LiefKundeKontaktNr	=	"" ;
			$this->updateInDb() ;
			$this->reload() ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA::clearLiefKunde(): exception='%s'", $e->getMessage()) ;
		}
	}
	
	/**
	 * F�hrt eine Neuberechnung aller abh�ngigen Werte der Kundenbestellung durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CuRMA_restate( @status, <CuRMANr>).
	 *
	 * @return void
	 */
	function	restate() {
		FDbg::dumpL( 0x01000000, "CuRMA::restate()") ;
		$query	=	sprintf( "CuRMA_restate( @status, '%s') ; ", $this->CuRMANr) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
			$this->reload() ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA::restate(...): exception='%s'", $e->getMessage()) ;
		}
	}

	function	setCalcMode( $_key, $_id, $_val) {
		switch ( $_val) {
		case 'v1'	:
			$query	=	sprintf( "CuRMA_setDM10( @status, '%s') ; ", $this->CuRMANr) ;
			try {
				$sqlRows	=	FDb::callProc( $query) ;
			} catch( Exception $e) {
				error_log( "CuRMA.php::CuRMA::setCalcMode(...): exception='%s'", $e->getMessage()) ;
			}
			break ;
		case 'v2'	:
			$query	=	sprintf( "CuRMA_setDM20( @status, '%s') ; ", $this->CuRMANr) ;
			try {
				$sqlRows	=	FDb::callProc( $query) ;
			} catch( Exception $e) {
				error_log( "CuRMA.php::CuRMA::setCalcMode(...): exception='%s'", $e->getMessage()) ;
			}
			break ;
		}
		return $this->getXMLString() ;
	}

	/**
	 * Setzt den Prefix sowie den Postfix der Kundenbestellung auf die Default Werte.
	 * Die Default Werte werden bestimmt durch die f�r den Kunden abgespeicherten Wert
	 * f�r Sprache (z.B. de_de, en_us).
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CuRMA_setTexte( @status, <CuRMANr>).
	 *
	 * @return void
	 */
	function	setTexte( $_key, $_id, $_val) {
		try {
			$myTexte	=	new SysTexte( "CuRMAPrefix", $this->KundeNr, $this->myKunde->Sprache) ;
			$this->Prefix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Prefix") ;
			$myTexte	=	new SysTexte( "CuRMAPostfix", $this->KundeNr, $this->myKunde->Sprache) ;
			$this->Postfix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Postfix") ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}

	function	setAnschreiben( $_key, $_id, $_val) {
		try {
			$myTexte	=	new SysTexte( "CuRMAEMail", $this->KundeNr, $this->myKunde->Sprache) ;
			$this->Anschreiben	=	$myTexte->Volltext ;
			$this->updateColInDb( "Anschreiben") ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}

	/**
	 * F�hrt eine Neuberechnung aller abh�ngigen Werte der Kundenbestellung durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CuOffr_restate( @status, <CuOffrNo>).
	 *
	 * @return void
	 */
	function	recalc() {
		FDbg::dumpL( 0x01000000, "CuRMA::recalc()") ;
		$query	=	sprintf( "CuRMA_recalc( @status, '%s') ; ", $this->CuRMANr) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA::recalc(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 * Setzt den Rabatt-Modus (DiscountMode) f�r die Bestellung auf den Rabatt Modus 1 (alte Variante)
	 * und f�hrt eine Neuberechnung der Einzelpreise, der Rabatte sowie der Gesamtpreise durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CuRMA_setDM10( @status, <CuRMANr>).
	 e
	 * @return void
	 */
	function	recalcV1() {
		FDbg::dumpL( 0x01000000, "CuRMA::recalcV1()") ;
		if ( $this->Status == 0) {
			$query	=	sprintf( "CuRMA_setDM10( @status, '%s') ; ", $this->CuRMANr) ;
			try {
				$sqlRows	=	FDb::callProc( $query) ;
			} catch( Exception $e) {
				FDbg::dumpF( "CuRMA::recalcV1(...): exception='%s'", $e->getMessage()) ;
			}
		} else {
			throw new Exception( 'CuRMA::recalcV1: Bestellung [CuRMANr='.$this->CuRMANr.'] hat falschen Status !') ;
		}
	}

	/**
	 * Setzt den Rabatt-Modus (DiscountMode) f�r die Bestellung auf den Rabatt Modus 2 (neue Variante)
	 * und f�hrt eine Neuberechnung der Einzelpreise, der Rabatte sowie der Gesamtpreise durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CuRMA_setDM20( @status, <CuRMANr>).
	 *
	 * @return void
	 */
	function	recalcV2() {
		FDbg::dumpL( 0x01000000, "CuRMA::recalcV2()") ;
		if ( $this->Status == 0) {
			$query	=	sprintf( "CuRMA_setDM20( @status, '%s') ; ", $this->CuRMANr) ;
			try {
				$sqlRows	=	FDb::callProc( $query) ;
			} catch( Exception $e) {
				FDbg::dumpF( "CuRMA::recalcV2(...): exception='%s'", $e->getMessage()) ;
			}
		} else {
			throw new Exception( 'CuRMA::recalcV1: Bestellung [CuRMANr='.$this->CuRMANr.'] hat falschen Status !') ;
		}
	}

	/**
	 * Setzt den Rabatt-Modus (DiscountMode) f�r die Bestellung auf den Rabatt Modus 3 (bottom Up;
	 * dieser Algorihtmus basiert auf Einkaufspreisen zzgl. Markup %)
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CuRMA_setDM30( @status, <CuRMANr>).
	 *
	 * @return void
	 */
	function	recalcV3() {
		FDbg::dumpL( 0x01000000, "CuRMA::recalcV3()") ;
		if ( $this->Status == 0) {
			$query	=	sprintf( "CuRMA_setDM30( @status, '%s') ; ", $this->CuRMANr) ;
			try {
				$sqlRows	=	FDb::callProc( $query) ;
			} catch( Exception $e) {
				FDbg::dumpF( "CuRMA::recalcV3(...): exception='%s'", $e->getMessage()) ;
			}
		} else {
			throw new Exception( 'CuRMA::recalcV1: Bestellung [CuRMANr='.$this->CuRMANr.'] hat falschen Status !') ;
		}
	}

	/**
	 * F�gt einen Artikel, beschrieben durch die VKPreisId, in der angegebenen Menge zu der Bestellung
	 * hinzu.
	 * Ueber die VKPreisId wird zun�chst die Artikelnr. bestimmt.
	 * 
	 * @return void
	 */
	function	addItem( $_artikelNr, $_vkpid, $_menge) {
		try {
			$myVKPreis	=	new VKPreisCache() ;
			$myVKPreis->setId( $_vkpid) ;
			if ( $myVKPreis->_valid) {
				$myArtikel	=	new Artikel() ;
				$myArtikel->setKey( $myVKPreis->ArtikelNr) ;
				if ( $myArtikel->_valid) {
					while ( strlen( $myArtikel->ArtikelNrNeu) > 0 || strlen( $myArtikel->ArtikelNrErsatz) > 0) {
						if ( strlen( $myArtikel->ArtikelNrNeu) > 0) {
							$myArtikel->setArtikelNr( $myArtikel->ArtikelNrNeu) ;
						} else if ( strlen( $myArtikel->ArtikelNrErsatz) > 0) {
							$myArtikel->setArtikelNr( $myArtikel->ArtikelNrErsatz) ;
						}
					}
					switch ( $myArtikel->Comp) {
					case	 0	:
					case	10	:
					case	20	:
					case	30	:
						$actCuRMAItem	=	new CuRMAItem( $this->CuRMANr) ;
						$cond	=	sprintf( "CuRMANr='%s' AND ArtikelNr='%s' AND MengeProVPE=%d",
												$this->CuRMANr, $myArtikel->ArtikelNr, $myVKPreis->MengeProVPE) ;
						FDbg::dumpL( 0x01000000, "CuRMA.php::add( %s, %d), cond='%s'", $_artikelNr, $_menge, $cond) ;
						$actCuRMAItem->_firstFromDb( $cond) ;
						if ( $actCuRMAItem->_valid == 1) {
							$actCuRMAItem->Menge	+=	$_menge ;
							$actCuRMAItem->updateInDb() ;
							$newCuRMAItem	=	$actCuRMAItem ;
						} else {
							$newCuRMAItem	=	new CuRMAItem( $this->CuRMANr) ;
							$newCuRMAItem->getNextItemNr() ;
							$newCuRMAItem->ItemType	=	CuRMAItem::NORMAL ;
							$newCuRMAItem->ArtikelNr	=	$myVKPreis->ArtikelNr ;
							$newCuRMAItem->Menge	=	$_menge ;
							$newCuRMAItem->Preis	=	$myVKPreis->Preis ;
							$newCuRMAItem->RefPreis	=	$myVKPreis->Preis ;
							$newCuRMAItem->MengeProVPE	=	$myVKPreis->MengeProVPE ;
							$newCuRMAItem->GesamtPreis	=	$newCuRMAItem->Menge * $newCuRMAItem->Preis ;
							$newCuRMAItem->storeInDb() ;
						}
						break ;
					case	11	:		// configurable components article
						$newCuRMAItem	=	new CuRMAItem( $this->CuRMANr) ;
						$newCuRMAItem->getNextItemNr() ;
						$newCuRMAItem->ItemType	=	CuRMAItem::NORMAL ;
						$newCuRMAItem->ArtikelNr	=	$myVKPreis->ArtikelNr ;
						$newCuRMAItem->Menge	=	$_menge ;
						$newCuRMAItem->Preis	=	$myVKPreis->Preis ;
						$newCuRMAItem->RefPreis	=	$myVKPreis->Preis ;
						$newCuRMAItem->MengeProVPE	=	$myVKPreis->MengeProVPE ;
						$newCuRMAItem->GesamtPreis	=	$newCuRMAItem->Menge * $newCuRMAItem->Preis ;
						$newCuRMAItem->storeInDb() ;
						break ;
					}
					$this->addSubItem( $newCuRMAItem->ItemNr, $myArtikel->ArtikelNr, $newCuRMAItem->Menge, "") ;
					$this->_renumber( 10) ;
					$this->restate() ;
				}
			} else {
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		$ret	=	$this->getTableItemsAsXML() ;
		return $ret ;
	}

	/**
	 * 
	 * @param int $_posNr
	 * @param string $_artikelNr
	 * @param int $_menge
	 * @param string $_posNrPrefix
	 */
	function	addSubItem( $_posNr, $_artikelNr, $_menge, $_posNrPrefix) {
		$myArtikel	=	new Artikel( $_artikelNr) ;
		$subArtikel	=	new Artikel( "") ;
		if ( $myArtikel->_valid == 1) {
			FDbg::dumpL( 0x01000000, "CuRMA.php::addSubItem(...), Article for break-up is valid") ;
			$goForIt	=	false ;
			switch ( $myArtikel->Comp) {
			case	 0	:
				break ;
			case	10	:
				$goForIt	=	true ;
				break ;
			case	11	:
				$goForIt	=	true ;
				break ;
			case	20	:
				break ;
			case	30	:
				break ;
			}
			if ( $goForIt) {
				FDbg::dumpL( 0x01000000, "CuRMA.php::addSubItem(...), Article for break-up is valid for break-up") ;
				$myArtKomp	=	new ArtKomp() ;
				$cond	=	sprintf( "ArtikelNr='%s' ", $_artikelNr) ;
				$subItemNr	=	0 ;
				for ( $myArtKomp->_firstFromDb( $cond) ;
						$myArtKomp->isValid() ;
						$myArtKomp->_nextFromDb()) {
					FDbg::dumpL( 0x01000000, "CuRMA.php::addSubItem(...), adding '%s'", $myArtKomp->CompArtikelNr) ;
					$subItemNr++ ;
					$subArtikel->setArtikelNr( $myArtKomp->CompArtikelNr) ;
					if ( strlen( $subArtikel->ArtikelNrNeu) > 0) {
						$subArtikel->ArtikelNr	=	$subArtikel->ArtikelNrNeu ;
					}
					$newCuRMAItem	=	new CuRMAItem( $this->CuRMANr) ;
					$newCuRMAItem->ItemNr	=	$_posNr ;
					$newCuRMAItem->SubItemNr	=	sprintf( "%s.%02d", $_posNrPrefix, $subItemNr) ;
					$newCuRMAItem->ItemType	=	CuRMAItem::KOMP ;
					$newCuRMAItem->ArtikelNr	=	$subArtikel->ArtikelNr ;
					$newCuRMAItem->Menge	=	$_menge * $myArtKomp->CompMenge ;
					$newCuRMAItem->Preis	=	0.0 ;
					$newCuRMAItem->RefPreis	=	0.0 ;
					$newCuRMAItem->MengeProVPE	=	$myArtKomp->CompMengeProVPE ;
					$newCuRMAItem->GesamtPreis	=	0.0 ;
					$newCuRMAItem->storeInDb() ;
					$this->addSubItem( $_posNr, $subArtikel->ArtikelNr, $newCuRMAItem->Menge, $newCuRMAItem->SubItemNr) ;
				}
			}
		}
	}

	/**
	 *
	 */
	function	expandItem( $_id, $_posNrPrefix="") {
		try {
			$actCuRMAItem	=	new CuRMAItem() ;
			$actCuRMAItem->setId( intval( $_id)) ;
			$actVKPreis	=	new VKPreis() ;
			if ( $actCuRMAItem->_valid == 1) {
				$myArtikel	=	new Artikel( $actCuRMAItem->ArtikelNr) ;
				$subArtikel	=	new Artikel( "") ;
				if ( $myArtikel->_valid == 1) {
					FDbg::dumpL( 0x01000000, "CuRMA::expandItem(...), Artikel mit ArtikelNr '%s' ist gueltig", $myArtikel->ArtikelNr) ;
					if ( $myArtikel->Comp == 0) {
						throw( new Exception( "CuRMA::expandItem: Artikel hat keine Komponenten!")) ;
					} else {
						FDbg::dumpL( 0x01000000, "CuRMA.php::expandItem(...), Artikel hat Komponenten") ;
						$myArtKomp	=	new ArtKomp() ;
						$cond	=	sprintf( "ArtikelNr='%s' ", $myArtikel->ArtikelNr) ;
						$subItemNr	=	0 ;
						$myArtKomp->_firstFromDb( $cond) ;
						while ( $myArtKomp->_valid == 1) {
							FDbg::dumpL( 0x01000000, "CuRMA.php::addSubItem(...), adding '%s'", $myArtKomp->CompArtikelNr) ;
							$actVKPreis->ArtikelNr	=	$myArtKomp->CompArtikelNr ;
							$actVKPreis->fetchFromDb() ;
							$subItemNr++ ;
							$subArtikel->setArtikelNr( $myArtKomp->CompArtikelNr) ;
							if ( strlen( $subArtikel->ArtikelNrNeu) > 0) {
								$subArtikel->ArtikelNr	=	$subArtikel->ArtikelNrNeu ;
							}
							$newCuRMAItem	=	new CuRMAItem( $this->CuRMANr) ;
							$newCuRMAItem->ItemNr	=	$actCuRMAItem->ItemNr ;
							$newCuRMAItem->SubItemNr	=	sprintf( "%s.%02d", $_posNrPrefix, $subItemNr) ;
							$newCuRMAItem->ItemType	=	0 ;
							$newCuRMAItem->ArtikelNr	=	$subArtikel->ArtikelNr ;
							$newCuRMAItem->Menge	=	$actCuRMAItem->Menge * $myArtKomp->CompMenge ;
							if ( $myArtKomp->KompTyp == ArtKomp::OPTION) {
								$newCuRMAItem->Preis	=	$actVKPreis->Preis ;
								$newCuRMAItem->RefPreis	=	$actVKPreis->Preis ;
							} else {
								$newCuRMAItem->Preis	=	0.0 ;
								$newCuRMAItem->RefPreis	=	0.0 ;
							}
							$newCuRMAItem->MengeProVPE	=	$myArtKomp->CompMengeProVPE ;
							$newCuRMAItem->GesamtPreis	=	0.0 ;
							$newCuRMAItem->storeInDb() ;
							if ( $subArtikel->ModeCuRMA == 1) {
								FDbg::dumpL( 0x01000000, "CuRMA.php::addSubItem(...), Composed Article needs to be broken up") ;
								$this->addSubItem( $_posNr, $subArtikel->ArtikelNr, $_menge * $newCuRMAItem->Menge, $newCuRMAItem->SubItemNr) ;
							}
							$myArtKomp->_nextFromDb( $cond) ;
						}
					}
				} else {
					throw( new Exception( "CuRMA::expandItem(...): Artikel mit der ArtikelNr=$actCuRMAItem->ArtikelNr existiert nicht")) ;
				}
			} else {
				throw( new Exception( "CuRMA::expandItem(...): CuRMAItem mit der Id=$_id existiert nicht")) ;
			}
		} catch ( Exception $e) {
			throw( $e) ;
		}

		return $newCuRMAItem ;
	}

	/**
	 *
	 */
	function	collapseItem( $_key, $_id, $_val) {
		try {
			$tmpCuRMAItem	=	new CuRMAItem() ;
			$tmpCuRMAItem->Id	=	$_id ;
			$tmpCuRMAItem->fetchFromDbById() ;
			if ( $tmpCuRMAItem->_valid == 1) {
				FDbg::dumpL( 0x01000000, "CuRMA::collapseItem(...), refers to ItemNr=%d", $tmpCuRMAItem->ItemNr) ;
				$query	=	sprintf( "CuRMA_collapseItem( @status, '%s', %d) ; ", $this->CuRMANr, $tmpCuRMAItem->ItemNr) ;
				$sqlRows	=	FDb::callProc( $query) ;
			} else {
				throw( new Exception( "CuRMA::expandItem(...): CuRMAItem mit der Id=$_id existiert nicht")) ;
			}
		} catch ( Exception $e) {
			throw( $e) ;
		}

		return $tmpCuRMAItem ;
	}

	function	updAddText( $_id, $_text) {
		FDbg::dumpL( 0x01000000, "CuRMA::updAddText(%d, '%s')", $_id, $_text) ;
		try {
			$this->tmpCuRMAItem	=	new CuRMAItem() ;
			$this->tmpCuRMAItem->Id	=	$_id ;
			$this->tmpCuRMAItem->fetchFromDbById() ;
			if ( $this->tmpCuRMAItem->_valid == 1) {
				FDbg::dumpL( 0x01000000, "CuRMA::updAddText: refers to ItemNr=%d", $this->tmpCuRMAItem->ItemNr) ;
				$this->tmpCuRMAItem->AddText	=	$_text ;
				$this->tmpCuRMAItem->updateInDb() ;
			} else {
				throw new Exception( 'CuRMA::updAddText: CuRMAItem[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x01000000, "CuRMA::updAddText(%s), done", $_id) ;
		return $this->tmpCuRMAItem ;
	}

	/**
	 *
	 * @return CuRMAItem
	 */
	function	getFirstItem() {
		$this->tmpCuRMAItem	=	new CuRMAItem() ;
		$this->tmpCuRMAItem->CuRMANr	=	$this->CuRMANr ;
		$this->tmpCuRMAItem->_firstFromDb() ;
		return $this->tmpCuRMAItem ;
	}

	/**
	 *
	 * @return CuRMAItem
	 */
	function	getNextItem() {
		$this->tmpCuRMAItem->_nextFromDb() ;
		return $this->tmpCuRMAItem ;
	}

	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	sendByEMail( $_key, $_id, $_val) {
		global	$mailTextSig ;
		global	$mailHtmlSig ;
		try {
			$this->upd( '', '', '') ;
			$newMail	=	new mimeMail( $this->eMail->Sales,
								$_POST['_IeMail'],
								$this->eMail->Sales,
								FTr::tr( "Your order, our Order No. #1, dated #2, order confirmation", array( "%s:".$this->CuRMANr, "%s:".convDate( $this->Datum))),
								"Bcc: ".$this->eMail->Archive."\n") ;

			$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer", "#CuRMADatum") ;
			$myReplTableOut	=	array( $this->myKundeKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $mailHtmlSig, $this->Datum) ;
			$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;

			$myText	=	new mimeData( "multipart/alternative") ;
			$myText->addData( "text/plain", xmlToPlain( "<div>".$myMail."</div>")) ;
			$myText->addData( "text/html", "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><head></head><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n".$myMail."</body></html>", "", true) ;
	
			$myBody	=	new mimeData( "multipart/mixed") ;
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			$myBody->addData( "application/pdf", $this->path->Archive."CuRMA/".$this->CuRMANr.".pdf", $this->CuRMANr.".pdf", true) ;

			$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
			$mailSendResult	=	$newMail->send() ;

			$this->DocVerschVia	=	Opt::DOCVIAEMAIL ;
			$this->_addRem( FTr::tr( "send by E-Mail to: #1 (Bcc: #2)", array( "%s:".$_POST['_IeMail'],"%s:".$this->eMail->Archive))) ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	
	function	getAnschAsHTML() {
		global	$mailTextSig ;
		global	$mailHtmlSig ;
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer") ;
		$myReplTableOut	=	array( $this->myKundeKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $mailHtmlSig) ;
		$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;
		return $myMail ;
	}					
	/**
	 * Verschicken per FAX
	 *
	 * @return [Artikel]
	 */
	function	sendByFAX( $_key, $_id, $_val) {
		require_once( "Fax.php" );
		$myFaxNr	=	$_POST['_IFAX'] ;
		sendFax( $myFaxNr,
					$this->path->Archive."CuRMA/".$this->CuRMANr.".pdf", "", "", "miskhwe",
					3) ;
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	CuRMA::ONGOING ;		// ueber "Normal"-FAX
		$this->_addRem( "verschickt per FAX an " . $myFaxNr) ;
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
	 * returns the XML-formatted list of all items in this CuOrdr
	 */
	function	consolidate( $_key, $_id, $_val) {
		$ret	=	"" ;
		$query	=	"CuRMA_cons( @a, '" . $this->CuRMANr . "') ; " ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
			$this->reload() ;
		} catch( Exception $e) {
			FDbg::dumpF( "CuRMA::cons(...): exception='%s'", $e->getMessage()) ;
		}
		$ret	.=	$this->getTableItemsAsXML() ;
		return $ret ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	infoMail() {
		$this->_status	=	0 ;
		$mailbody	=	"Halloechen, \n\n" ;
		$mailbody	.=	"Eine neue Bestellung ist reingekommen: \n" ;
		$mailbody	.= 	"Bestellung Nr....:\t" . $this->CuRMANr . "\n" ;
		$mailbody	.= 	"Kunde............:\t" . $this->KundeNr . "\n" ;
		$mailbody	.= 	"Anzahl Positionen:\t" . $this->Items . "\n" ;
		$mailbody	.= 	"Gesamtwert.......:\t" . $this->GesamtPreis . "\n" ;
		$mailbody	.= 	"\n" ;
		$mailbody	.= 	"Euer Adminstrator" ;
		mail( $this->eMail->RMACust, "Neue Bestellung", $mailbody, "From: " . $this->eMail->Site) ;
		return( 0) ;
	}

	/*
	 * 
	 */
	function	infoMail2( $file, $to) {
		global $mailHtmlSig ;

		/**
		 * get the standard e-mail body
		 */
		$myMailIn	=	"<div>Hier fehlt was !</div>" ;
		try {
			$myTexte	=	new SysTexte() ;
			$myTexte->setKeys( "CuRMAEMail_web", "", "de") ;
			if ( $myTexte->_valid) {
				$myMailIn	=	$myTexte->Volltext ;
			}
		} catch ( Exception $e) {
			error_log( $e->getMessage) ;
		}

		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer") ;
		$myReplTableOut	=	array( $this->getKundeKontakt()->getAnrede(), $this->Datum, $this->eMail->Greeting, $mailHtmlSig) ;
		$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $myMailIn) ;

		/**
		 *
		 */
		$newMail	=	new mimeMail( $this->eMail->RMACust,
							$to,
							$this->eMail->RMACust,
							"Ihre Bestellung Nr. " . $this->CuRMANr . " vom ".convDate( $this->Datum)." ",
							"Bcc: ".$this->eMail->Archive."\n") ;

		$myText	=	new mimeData( "multipart/alternative") ;
		$myText->addData( "text/plain", xmlToPlain( "<div>".$myMail."</div>")) ;
		$myText->addData( "text/html", "<HTML><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><HEAD<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n".$myMail."</HTML>", "", true) ;

		$myBody	=	new mimeData( "multipart/mixed") ;
		$myBody->addData( "multipart/mixed", $myText->getAll()) ;
		$myBody->addData( "application/pdf", $this->path->Archive."CuRMA/".$this->CuRMANr.".pdf", "Best".$this->CuRMANr.".pdf", true) ;

		$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
		$mailSendResult	=	$newMail->send() ;
		echo $mailSendResult ? "Mail sent" : "Mail failed";
	}
	/**
	 *
	 */
	function	getRStatus() {
		return self::$rStatus ;
	}
	function	getRDocType() {
		return self::$rDocType ;
	}
		
	/**
	 *
	 */
	function	getRStatLief() {
		return self::$rStatLief ;
	}
		
	/**
	 *
	 */
	function	getRStatRech() {
		return self::$rStatRech ;
	}
		
	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getKundeAsXML() ;
		$ret	.=	$this->getTableItemsAsXML() ;
		return $ret ;
	}

	function	getXMLString() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		$ret	.=	$this->getXMLDocInfo() ;
		return $ret ;
	}

	function	getXMLDocInfo() {
		$ret	=	"<Document><![CDATA[" ;
		$filename	=	$this->path->Archive . "CuRMA/" . $this->CuRMANr . ".pdf" ;
		if ( file_exists( $filename)) { 
			$ret	.=	 $this->url->Archive . "/CuRMA/" . $this->CuRMANr . ".pdf" ;
		}
		$ret	.=	"]]></Document>" ;
		return $ret ;
	}

	function	getKundeAsXML() {
		$ret	=	"" ;

		$ret	.=	'<KundeAdr>' ;
		$ret	.=	$this->myKunde->getXMLF() ;
		$ret	.=	$this->myKundeKontakt->getXMLF() ;

		if ( $this->myLiefKunde) {
			$ret	.=	$this->myLiefKunde->getXMLF( "LiefKunde") ;
		}
		if ( $this->myLiefKundeKontakt) {
			$ret	.=	$this->myLiefKundeKontakt->getXMLF( "LiefKundeKontakt") ;
		}
		if ( $this->myRechKunde) {
			$ret	.=	$this->myRechKunde->getXMLF( "RechKunde") ;
		}
		if ( $this->myRechKundeKontakt) {
			$ret	.=	$this->myRechKundeKontakt->getXMLF( "RechKundeKontakt") ;
		}
		$ret	.=	'</KundeAdr>' ;
		return $ret ;
	}

	function	getDocListAsXML( $_key, $_id, $_val) {
		$fullPath	=	$this->path->Archive . "CuRMA/" ;
		$myDir	=	opendir( $fullPath) ;
		$myFiles	=	array() ;
		while (($file = readdir( $myDir)) !== false) {
			if ( strncmp( $file, $this->CuRMANr, 6) == 0) {
				$myFiles[]	=	$file ;
			}
		}
		closedir( $myDir);
		reset( $myFiles) ;
		asort( $myFiles) ;
		$ret	=	"<DocList>\n" ;
		$ret	.=	"<URLPath>$this->url->Archive</URLPath>\n" ;
		foreach ( $myFiles as $file) {
			$ret	.=	"<Doc>\n" ;
			if ( filetype( $fullPath . $file) == "file") {
				$ret	.=	"<Filename>$file</Filename>\n" ;
				$ret	.=	"<Filetype>" . myFiletype( $file) . "</Filetype>\n" ;
				$ret	.=	"<Filesize>" . filesize( $fullPath . $file) . "</Filesize>\n" ;
				$ret	.=	"<FileURL>" . $this->url->Archive . "CuRMA/" . $file . "</FileURL>\n" ;
			}
			$ret	.=	"</Doc>\n" ;
		}
		$ret	.=	"</DocList>" ;
		return $ret ;
	}
	function	checkComm() {
		$res	=	"Check auf Kommissionierbarkeit<br/>" ;
		$actCuRMAItem	=	new CuRMAItem() ;
		$actArtikel	=	new Artikel() ;
		$actArtikelBestand	=	new ArtikelBestand() ;
		for ( $actCuRMAItem->_firstFromDb( "CuRMANr = '$this->CuRMANr' AND Menge > GelieferteMenge ") ;
				$actCuRMAItem->_valid == 1 ;
				$actCuRMAItem->_nextFromDb()) {
			$actArtikelBestand->getDefault( $actCuRMAItem->ArtikelNr) ;
			$res	.=	"["
						. $actCuRMAItem->ArtikelNr . " / "
						. $actCuRMAItem->Menge . " / "
						. $actCuRMAItem->GelieferteMenge . " / "
						. $actArtikelBestand->Lagerbestand . " / "
						. "]<br/>" ;
		}
		return $res ;
	}
}

/**
 * CuRMAItem - 
 *
 * @package Application
 * @subpackage CuRMA
 */
class	CuRMAItem	extends	AppDepObject	{

	public	$myArtikel ;
	public	$myCond ;

	const	NORMAL		=	0 ;			// normale Position
	const	LFRNG		=	1 ;			// wird geliefert aber nicht berechnet
	const	RCHNG		=	2 ;			// wird berechnet aber nicht geliefert
	const	KOMP		=	3 ;			// Komponente
	const	OPTION		=	4 ;			// Komponente
	const	_LASTNORM	=	8 ;			// Letzer "normaler" Item Typ
	const	HDLGPSCH	=	9 ;			// Handlingpauschale
	const	VRSND		=	10 ;		// Versandkosten (Versand und Versicherung)
	private	static	$rItemType	=	array (
						CuRMAItem::NORMAL	=> "Normal",
						CuRMAItem::LFRNG		=> "Liefern",
						CuRMAItem::RCHNG		=> "Berechnen",
						CuRMAItem::KOMP		=> "Komponente",
						CuRMAItem::HDLGPSCH	=> "Handling",
						CuRMAItem::VRSND		=> "Versand"
					) ;

	/**
	 *
	 */
	function	__construct( $_myCuRMANr='') {
		FDbg::dumpL( 0x01000000, "CuRMAItem::__constructor") ;
		AppDepObject::__construct( "CuRMAItem", "Id") ;
		$this->CuRMANr	=	$_myCuRMANr ;
		$this->myArtikel	=	new Artikel() ;
	}

	/**
	 *
	 * @return void
	 */
	function	buche() {
		FDbg::dumpL( 0x01000000, "CuRMAItem::buche()") ;
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
	function	copyFromTCuRMAItem( $_myTCuRMAItem) {
		$this->ItemNr	=	$_myTCuRMAItem->ItemNr ;
		$this->SubItemNr	=	$_myTCuRMAItem->SubItemNr ;
		$this->ItemType	=	$_myTCuRMAItem->ItemType ;
		$this->ArtikelNr	=	$_myTCuRMAItem->ArtikelNr ;
		$this->RevCode	=	$_myTCuRMAItem->RevCode ;
		$this->FOC	=	0 ;
		$this->Menge	=	$_myTCuRMAItem->Menge ;
		$this->GelieferteMenge	=	0 ;
		$this->BerechneteMenge	=	0 ;
		$this->Preis	=	$_myTCuRMAItem->Preis ;
		$this->RefPreis	=	$_myTCuRMAItem->RefPreis ;
		$this->MengeProVPE	=	$_myTCuRMAItem->MengeProVPE ;
		$this->GesamtPreis	=	$_myTCuRMAItem->GesamtPreis ;
		$this->MengeDirektBest	=	0 ;
		$this->MengeReserviert	=	0 ;
	}

	/**
	 *
	 * @return void
	 */
	function	getNextItemNr() {
		$query	=	sprintf( "SELECT ItemNr FROM CuRMAItem WHERE CuRMANr='%s' ORDER BY ItemNr DESC LIMIT 0, 1 ", $this->CuRMANr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->ItemNr	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
		
	/**
	 * Zugriff auf Artikel
	 *
	 * @return [Artikel]
	 */
	function	getArtikel() {
		return $this->myArtikel ;
	}

	/**
	 *
	 */
	function	getRItemType() {
		return self::$rItemType ;
	}
		
}

?>
