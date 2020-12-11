<?php

/**
 * CustomerTempOrder.php - Basis Anwendungsklasse fuer Customernbestellung (CustomerTempOrder)
 *
 *	Definiert die Klassen:
 *		CustomerTempOrder
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
 * CustomerTempOrder - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCustomerTempOrder which should
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerTempOrder
 */
class	CustomerTempOrder	extends	AppObjectERM_CR	{

	private	$tmpCustomerTempOrderPos ;

	const	NEU		=	0 ;
	const	CONF	=	30 ;
	const	ONGOING	=	50 ;
	const	CLOSED	=	90 ;
	const	EXPORTED	=	970 ;
	const	ONHOLD	=	980 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						CustomerTempOrder::NEU		=> "open",
						CustomerTempOrder::CONF	=> "confirme",
						CustomerTempOrder::ONGOING	=> "ongoing",
						CustomerTempOrder::CLOSED	=> "abgeschlossen",
						CustomerTempOrder::EXPORTED	=>	"exportiert",
						CustomerTempOrder::ONHOLD	=> "on-hold",
						CustomerTempOrder::CANCELLED	=> "cancelled"
					) ;
	const	COMMNO		=	  0 ;
	const	COMMPART	=	 10 ;
	const	COMMFULL	=	 20 ;
	const	COMMDONE	=	 90 ;
	private	static	$rStatComm	=	array (
						CustomerTempOrder::COMMNO		=> "nein",
						CustomerTempOrder::COMMPART	=> "partiell",
						CustomerTempOrder::COMMFULL	=> "vollst�ndig",
						CustomerTempOrder::COMMDONE	=> "abgehakt"
					) ;
	const	DELIVNO		=	  0 ;
	const	DELIVPAR	=	 50 ;
	const	DELIVALL	=	 90 ;
	private	static	$rStatLief	=	array (
						CustomerTempOrder::DELIVNO		=> "nicht geliefert",
						CustomerTempOrder::DELIVPAR	=> "teilgeliefert",
						CustomerTempOrder::DELIVALL	=> "vollstaendig geliefert"
					) ;
	const	INVCDNO		=	  0 ;
	const	INVCDPAR	=	 50 ;
	const	INVCDALL	=	 90 ;
	private	static	$rStatRech	=	array (
						CustomerTempOrder::INVCDNO		=> "nicht berechnet",
						CustomerTempOrder::INVCDPAR	=> "teilberechnet",
						CustomerTempOrder::INVCDALL	=> "vollstaendig berechnet"
					) ;
	const	DOCAE	=	"AE" ;		// order document
	const	DOCSD	=	"SD" ;		// self-declaration (Eigenerkl�rung)
	const	DOCMI	=	"MI" ;		// order confirmation
	private	static	$rDocType	=	array (
						CustomerTempOrder::DOCAE	=> "Auftragserteilung",
						CustomerTempOrder::DOCSD	=> "Self-declaration",
						CustomerTempOrder::DOCMI	=> "Sonstiges"
					) ;
	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (CustomerOrderNo), in which case it will automatically
	 * (try to) load the respective Customer Order via the base class from the Database
	 * If the order data was loaded the customer data, and customer contact data, will also be loaded
	 * from the database.
	 *
	 * @param string $_myCustomerOrderNo
	 * @return void
	 */
	function	__construct( $_myCustomerOrderNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerOrderNo')") ;
		parent::__construct( "CustomerTempOrder", "CustomerOrderNo") ;
		$this->_valid	=	false ;
		if ( strlen( $_myCustomerOrderNo) > 0) {
			$this->setCustomerOrderNo( $_myCustomerOrderNo) ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerOrderNo')", "no order number specified!") ;
		}
		FDbg::end() ;
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
			$myCustomerTempOrderItem	=	new CustomerTempOrderItem() ;
			$myCustomerTempOrderItem->removeFromDbWhere( "CustomerOrderNo = '" . $this->CustomerOrderNo . "'") ;
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
		$this->_renumber( "CustomerTempOrderItem", 10) ;
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * set the Order Number (CustomerOrderNo)
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
	function	setCustomerOrderNo( $_myCustomerOrderNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '%s')", $_myCustomerOrderNo) ;
		$this->CustomerOrderNo	=	$_myCustomerOrderNo ;
		if ( strlen( $_myCustomerOrderNo) > 0) {
			$this->reload() ;
		}
		FDbg::end() ;
		return $this->_valid ;
	}
	function	setId( $_id) {
		parent::setId( $_id) ;
	}
	/**
	 *
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_sign)") ;
		$actCustomerTempOrderItem	=	new CustomerTempOrderItem() ;
		$cond	=	"CustomerOrderNo = '$this->CustomerOrderNo' ORDER BY ItemNo, SubItemNo " ;
		for ( $actCustomerTempOrderItem->_firstFromDb( $cond) ;
				$actCustomerTempOrderItem->isValid() ;
				$actCustomerTempOrderItem->_nextFromDb()) {
			try {
				$actCustomerTempOrderItem->_buche( $_sign) ;
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
		$actCustomerTempOrder	=	new CustomerTempOrder() ;
		for ( $actCustomerTempOrder->_firstFromDb( "CustomerOrderNo like '%' ") ;
				$actCustomerTempOrder->_valid ;
				$actCustomerTempOrder->_nextFromDb()) {
			error_log( "CustomerTempOrder.php::CustomerTempOrder::bucheAll(): booking CustomerOrderNo " . $actCustomerTempOrder->CustomerOrderNo) ;
			$actCustomerTempOrder->buche() ;
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
		$actCustomerTempOrder	=	new CustomerTempOrder() ;
		for ( $actCustomerTempOrder->_firstFromDb( "CustomerOrderNo like '%' ") ;
				$actCustomerTempOrder->_valid ;
				$actCustomerTempOrder->_nextFromDb()) {
			error_log( "CustomerTempOrder.php::CustomerTempOrder::bucheAll(): un-booking CustomerOrderNo " . $actCustomerTempOrder->CustomerOrderNo) ;
			$actCustomerTempOrder->unbuche() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 */
	static	function	_clearAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		FDb::query( "UPDATE CustomerTempOrderItem SET MengeReserviert = 0 ") ;
		FDbg::end() ;
	}
	static	function	_markAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$ret	=	"" ;
		$actCustomerTempOrder	=	new CustomerTempOrder() ;
		$crit	=	"CustomerOrderNo LIKE '%%' AND Status < 90 " .		// only the open ones
					"AND Datum > '$_startDate' " .					// in the given date range
					"AND Datum <= '$_endDate' " ;
		for ( $actCustomerTempOrder->_firstFromDb( $crit) ;
				$actCustomerTempOrder->_valid ;
				$actCustomerTempOrder->_nextFromDb()) {
			$actCustomerTempOrder->buche() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Create a new Customer Commission (CuComm) for this Customer Order (CustomerTempOrder)
	 * If an open Commision exists a Reference to this open CuComm will be returned
	 * @param string $_key Number of the Customer RFQ (CuRFQ) which shall be turned into a Customer Quotation (CuQuot)
	 * @param int $_id unused
	 * @param mixed $_val unused
	 */
	function	newCuComm( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$newCuComm	=	new CuComm() ;
		$newCuComm->newFromCustomerTempOrder( '', -1, $_key) ;
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
		$newCuInvc->newFromCustomerTempOrderA( '', -1, $_key) ;
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
		$newCuInvc->newFromCustomerTempOrderOL( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CuInvc</ObjectClass>\n<ObjectKey>$newCuInvc->CuInvcNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PCustomerTempOrder)
	 *
	 * @return void
	 */
	function	newFromCuCart( $_key, $_id, $_cuCartNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_cuCartNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CuCart", $_cuCartNo, "", "", "900000", "949999") ;		// create a new instance
		$myCustomerTempOrderPos	=	new CustomerTempOrderItem( $this->CustomerOrderNo) ;
		for ( $valid = $myCustomerTempOrderPos->firstFromDb( "CustomerOrderNo", "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
				$valid ;
				$valid = $myCustomerTempOrderPos->nextFromDb()) {
//			$myCustomerTempOrderPos->updateInDb() ;
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PCustomerTempOrder)
	 *
	 * @return void
	 */
	function	newFromCuRFQ( $_key, $_id, $_cuRFQNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CuRFQ", $_cuRFQNo) ;		// create a new instance
		$myCustomerTempOrderPos	=	new CustomerTempOrderItem( $this->CustomerOrderNo) ;
		for ( $valid = $myCustomerTempOrderPos->firstFromDb( "CustomerOrderNo", "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
				$valid ;
				$valid = $myCustomerTempOrderPos->nextFromDb()) {
//			$myCustomerTempOrderPos->updateInDb() ;
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (PCustomerTempOrder)
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
	 * Create a Customer Order based on the provided Temp. Customer Order (PCustomerTempOrder)
	 *
	 * @return void
	 */
	function	newFromPCustomerTempOrder( $_key, $_id, $_pCustomerOrderNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_pCustomerOrderNo')") ;
		$this->_newFrom( "PCustomerTempOrder", $_pCustomerOrderNo) ;		// create a new instance
		$myCustomerTempOrderPos	=	new CustomerTempOrderItem( $this->CustomerOrderNo) ;
		for ( $valid = $myCustomerTempOrderPos->firstFromDb( "CustomerOrderNo", "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
				$valid ;
				$valid = $myCustomerTempOrderPos->nextFromDb()) {
//			$myCustomerTempOrderPos->updateInDb() ;
		}
		$ret	=	$this->getXMLComplete() ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * Setzt den Prefix sowie den Postfix der Customernbestellung auf die Default Werte.
	 * Die Default Werte werden bestimmt durch die f�r den Customern abgespeicherten Wert
	 * f�r Language (z.B. de_de, en_us).
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CustomerTempOrder_setTexte( @status, <CustomerOrderNo>).
	 *
	 * @return void
	 */
	function	setTexte( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		try {
			$myTexte	=	new Texte( "CustomerTempOrderPrefix", $this->CustomerNo, $this->Customer->Language) ;
			$this->Prefix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Prefix") ;
			$myTexte	=	new Texte( "CustomerTempOrderPostfix", $this->CustomerNo, $this->Customer->Language) ;
			$this->Postfix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Postfix") ;
		} catch( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
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
			$myTexte	=	new Texte( "CustomerTempOrderEMail", $this->CustomerNo, $this->Customer->Language) ;
			$this->Anschreiben	=	$myTexte->Volltext ;
			$this->updateColInDb( "Anschreiben") ;
		} catch( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_id
	 * @param $_text
	 */
	function	updAddText( $_id, $_text) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_id, '$_text')") ;
		try {
			$this->tmpCustomerTempOrderPos	=	new CustomerTempOrderItem() ;
			$this->tmpCustomerTempOrderPos->Id	=	$_id ;
			$this->tmpCustomerTempOrderPos->fetchFromDbById() ;
			if ( $this->tmpCustomerTempOrderPos->_valid == 1) {
				FDbg::dumpL( 0x01000000, "CustomerTempOrder::updAddText: refers to ItemNo=%d", $this->tmpCustomerTempOrderPos->ItemNo) ;
				$this->tmpCustomerTempOrderPos->AddText	=	$_text ;
				$this->tmpCustomerTempOrderPos->updateInDb() ;
			} else {
				throw new Exception( 'CustomerTempOrder::updAddText: CustomerTempOrderItem[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->tmpCustomerTempOrderPos ;
	}
	/**
	 *
	 * @return CustomerTempOrderPos
	 */
	function	getFirstPos() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$this->tmpCustomerTempOrderPos	=	new CustomerTempOrderItem() ;
		$this->tmpCustomerTempOrderPos->CustomerOrderNo	=	$this->CustomerOrderNo ;
		$this->tmpCustomerTempOrderPos->_firstFromDb() ;
		FDbg::end() ;
		return $this->tmpCustomerTempOrderPos ;
	}
	/**
	 *
	 * @return CustomerTempOrderPos
	 */
	function	getNextPos() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$this->tmpCustomerTempOrderPos->_nextFromDb() ;
		FDbg::end() ;
		return $this->tmpCustomerTempOrderPos ;
	}
	/**
	 *
	 */
	function	updateHdlg() {
		/**	delete all handling items	**/
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		FDb::query( "DELETE FROM CustomerTempOrderItem WHERE CustomerOrderNo = '$this->CustomerOrderNo' AND ArticleNo like 'HDLGPSCH%'") ;
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
		$this->mail( "CustomerTempOrderEMail", null, "", "", "", "") ;
		FDbg::end() ;
		return $this->getXMLString() ;
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
		require_once( "Fax.php" );
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$myFaxNr	=	$_POST['_IFAX'] ;
		sendFax( $myFaxNr,
					$this->path->Archive."CustomerTempOrder/".$this->CustomerOrderNo.".pdf", "", "", "miskhwe",
					3) ;
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	CustomerTempOrder::ONGOING ;		// ueber "Normal"-FAX
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
	 * returns the XML-formatted list of all items in this CustomerTempOrder
	 */
	function	consolidate( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 * update the quantities which have been delivered already
		 */
		$myItem	=	new FDbObject( "CustomerTempOrderItem", "Id", "def", "v_CustomerTempOrderItemConsolidateDeliveries") ; ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "CustomerOrderNo = '".$this->CustomerOrderNo."' ") ;
		$myItem->setIterOrder( [ "ItemNo", "SubItemNo"]) ;
		$myCustomerTempOrderItem	=	new CustomerTempOrderItem() ;
		foreach ( $myItem as $actItem) {
			$myCustomerTempOrderItem->setId( $actItem->Id) ;
			$myCustomerTempOrderItem->QuantityDelivered	=	$actItem->DeliveredTotal ;
			$myCustomerTempOrderItem->updateColInDb( "QuantityDelivered") ;
		}
		/**
		 * update the quantities which have been invoiced already
		 */
		$myItem	=	new FDbObject( "CustomerTempOrderItem", "Id", "def", "v_CustomerTempOrderItemConsolidateInvoices") ; ;
		$myItem->clearIterCond() ;
		$myItem->setIterCond( "CustomerOrderNo = '".$this->CustomerOrderNo."' ") ;
		$myItem->setIterOrder( [ "ItemNo", "SubItemNo"]) ;
		$myCustomerTempOrderItem	=	new CustomerTempOrderItem() ;
		foreach ( $myItem as $actItem) {
			$myCustomerTempOrderItem->setId( $actItem->Id) ;
			$myCustomerTempOrderItem->QuantityInvoiced	=	$actItem->InvoicedTotal ;
			$myCustomerTempOrderItem->updateColInDb( "QuantityInvoiced") ;
		}
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
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
		FDbg::dump( "CustomerTempOrder.php::CustomerTempOrder::createDirBest( $_key, $_id, $_val): begin") ;
		/**
		 * create the (provisionary) PCustomerTempOrder and CuComm for each distinct supplier
		 */
		$myLief	=	new Lief() ;
		$myCustomerTempOrderPos	=	new CustomerTempOrderItem( $this->CustomerOrderNo) ;
		$myEKPriceR	=	new EKPriceR() ;
		$tmpPCustomerTempOrder	=	array() ;
		$tmpCuComm	=	array() ;
		for ( $valid = $myCustomerTempOrderPos->firstFromDb( "CustomerOrderNo", "Article", array( "DirektVersand" => "txt"), "ArticleNo", "ORDER BY ItemNo ") ;
				$valid ;
				$valid = $myCustomerTempOrderPos->nextFromDb()) {
			if ( $myCustomerTempOrderPos->DirektVersand == 1) {
				$validEKPR	=	$myEKPriceR->fetchFromDbWhere( "WHERE ArticleNo = '$myCustomerTempOrderPos->ArticleNo' AND KalkBasis > 0") ;
				if ( $validEKPR) {
					FDbg::dump( "CustomerTempOrder.php::createDirBest: unique EKPriceR identified ") ;
					$liefNr	=	$myEKPriceR->LiefNr ;
					if ( !isset( $tmpPCustomerTempOrder[ $liefNr])) {
						$myLief->setLiefNr( $liefNr) ;
						$myPCustomerTempOrder	=	new PCustomerTempOrder() ;
						$myPCustomerTempOrder->PCustomerOrderNo	=	$myEKPriceR->LiefNr . "-" . $this->CustomerOrderNo ;
						$myPCustomerTempOrder->copyFrom( $this) ;
						$myPCustomerTempOrder->Datum	=	$this->today() ;
						$myPCustomerTempOrder->LiefNr	=	$liefNr ;
						$myPCustomerTempOrder->LiefKontaktNr	=	$myLief->OrderingContact ;
						$myPCustomerTempOrder->storeInDb() ;
						$tmpPCustomerTempOrder[ $liefNr]	=	$myPCustomerTempOrder ;

						$myCuComm	=	new CuComm() ;
						$myCuComm->add() ;			// get the CuComm no.
						$myCuComm->copyFrom( $this) ;		// copy meaningful attributes
						$myCuComm->Datum	=	$this->today() ;
						$myCuComm->updateInDb() ;
						$tmpCuComm[ $liefNr]	=	$myCuComm ;
					}
				} else if ( $status == -1) {
					FDbg::dump( "CustomerTempOrder.php::createDirBest: no EKPriceR identified ") ;
				} else if ( $status == -2) {
					FDbg::dump( "CustomerTempOrder.php::createDirBest: multiple EKPriceR identified ") ;
				}
			} else {
				FDbg::dump( "CustomerTempOrder.php::createDirBest: article no. '$myCustomerTempOrderPos->ArticleNo' not a direct delivery article") ;
			}
		}
		/**
		 * create the provisionary PCustomerTempOrderItem and the CuCommItem
		 */
		$tmpPCustomerTempOrderPos	=	new PCustomerTempOrderItem() ;
		$tmpCuCommPos	=	new CuCommItem() ;
		for ( $valid = $myCustomerTempOrderPos->firstFromDb( "CustomerOrderNo", "Article", array( "DirektVersand" => "txt"), "ArticleNo", "ORDER BY ItemNo ") ;
				$valid ;
				$valid = $myCustomerTempOrderPos->nextFromDb()) {
			if ( $myCustomerTempOrderPos->DirektVersand == 1) {
				$status	=	$myEKPriceR->fetchFromDbWhere( "WHERE ArticleNo = '$myCustomerTempOrderPos->ArticleNo' AND KalkBasis > 0") ;
				$liefNr	=	$myEKPriceR->LiefNr ;

				$tmpPCustomerTempOrderPos->copyFrom( $myCustomerTempOrderPos) ;
				$tmpPCustomerTempOrderPos->PCustomerOrderNo	=	$myEKPriceR->LiefNr . "-" . $this->CustomerOrderNo ;
				$tmpPCustomerTempOrderPos->storeInDb() ;

				$tmpCuCommPos->copyFrom( $myCustomerTempOrderPos) ;
				$tmpCuCommPos->CuCommNo	=	$tmpCuComm[$liefNr]->CuCommNo ;
				$tmpCuCommPos->MengeGeliefert	=	$tmpCuCommPos->Menge ;
				$tmpCuCommPos->storeInDb() ;
			}
		}
		/**
		 * create bill(s)-of-delivery
		 */
		FDbg::dump( "CustomerTempOrder.php::createDirBest(): creating bill(s)-of-delivery") ;
		$myCuCommDoc	=	new CuCommDoc() ;
		$myCuDlvr	=	new CuDlvr() ;
		$myCuDlvrDoc	=	new CuDlvrDoc() ;
		foreach ( $tmpCuComm AS $liefNr => $cuComm) {
			FDbg::dump( "$liefNr :=> $cuComm->CuCommNo") ;

			FDbg::dump( "CustomerTempOrder.php::createDirBest(): working on CuComm(Doc)") ;
			$cuComm->setTexte() ;
			$myCuCommDoc->setKey( $cuComm->CuCommNo) ;
			$myCuCommDoc->createPDF() ;

			FDbg::dump( "CustomerTempOrder.php::createDirBest(): creating CuDlvr") ;
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
		foreach ( $tmpPCustomerTempOrder AS $liefNr => $pCustomerTempOrder) {
			FDbg::dump( "CustomerTempOrder.php::createDirBest(): from pCustomerTempOrder: $pCustomerTempOrder->LiefNr / $pCustomerTempOrder->LiefKontaktNr") ;

			$mySuOrdr->newFromPCustomerTempOrder( "", -1, $pCustomerTempOrder->PCustomerOrderNo) ;
			FDbg::dump( "CustomerTempOrder.php::createDirBest(): from SuOrdr: $mySuOrdr->LiefNr / $mySuOrdr->LiefKontaktNr") ;
			$mySuOrdr->CustomerOrderNo	=	$this->CustomerOrderNo ;
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
	 * @param string $_file	opt.: pdf-file in the Archive/CustomerTempOrder path to attach
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
		$fileName	=	"CustomerTempOrder".$this->CustomerOrderNo.".pdf" ;
		parent::mail(  $_mailText, $_file, $fileName, $_from, $_to, $_cc, $_bcc) ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	getRStatus() {	return self::$rStatus ;	}
	function	getRDocType() {	return self::$rDocType ;	}
	function	getRStatComm() {	return self::$rStatComm ;	}
	function	getRStatLief() {	return self::$rStatLief ;	}
	function	getRStatRech() {	return self::$rStatRech ;	}
	/**
	 *
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "CustomerTempOrder.php", "CustomerTempOrder", "getAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getXMLDocInfo() {
		$ret	=	"<Document><![CDATA[" ;
		$filename	=	$this->path->Archive . "CustomerTempOrder/" . $this->CustomerOrderNo . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "/CustomerTempOrder/" . $this->CustomerOrderNo . ".pdf" ;
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
		$actCustomerTempOrderPos	=	new CustomerTempOrderItem() ;
		$actArticle	=	new Article() ;
		$actArticleBestand	=	new ArticleBestand() ;
		for ( $actCustomerTempOrderPos->_firstFromDb( "CustomerOrderNo = '$this->CustomerOrderNo' AND Menge > MengeBereitsGeliefert ") ;
				$actCustomerTempOrderPos->_valid == 1 ;
				$actCustomerTempOrderPos->_nextFromDb()) {
			$actArticleBestand->getDefault( $actCustomerTempOrderPos->ArticleNo) ;
			$res	.=	"["
						. $actCustomerTempOrderPos->ArticleNo . " / "
						. $actCustomerTempOrderPos->Menge . " / "
						. $actCustomerTempOrderPos->MengeBereitsGeliefert . " / "
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
		$actCustomerTempOrderPos	=	new CustomerTempOrderItem() ;
		$actArticleBestand	=	new ArticleBestand() ;
		for ( $actCustomerTempOrderPos->_firstFromDb( "CustomerOrderNo = '$this->CustomerOrderNo' AND Menge > MengeBereitsGeliefert ") ;
				$actCustomerTempOrderPos->_valid == 1 ;
				$actCustomerTempOrderPos->_nextFromDb()) {
			$actArticleBestand->getDefault( $actCustomerTempOrderPos->ArticleNo) ;
			if ( ( $actCustomerTempOrderPos->Menge - $actCustomerTempOrderPos->MengeBereitsGeliefert) <= $actArticleBestand->Lagerbestand) {
				$canCommissionPart	=	true ;
			} else {
				$canCommissionAll	=	false ;
			}
		}
		$oldStat	=	$this->StatComm ;
		if ( $canCommissionAll) {
			$res	=	CustomerTempOrder::COMMFULL ;
		} else 		if ( $canCommissionPart) {
			$res	=	CustomerTempOrder::COMMPART ;
		} else {
			$res	=	CustomerTempOrder::COMMNO ;
		}
		$this->StatComm	=	$res ;
		$this->updateColInDb( "StatComm") ;
		if ( $oldStat != $res) {
			try {
				$this->StatComm	=	$res ;
				$this->updateColInDb( "StatComm") ;
				$this->sendCommInfoByEMail( $oldStat, $res) ;
			} catch ( Exeption $e) {
				throw( new Exception( "CustomerTempOrder.php::CustomerTempOrder::setStatComm(...): problem with CustomerOrderNo: ".$this->CustomerOrderNo)) ;
			}
		}
		FDbg::end() ;
		return $res ;
	}
	static	function	setStatCommAll( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$res	=	"" ;
		$actCustomerTempOrder	=	new CustomerTempOrder() ;
		for ( $actCustomerTempOrder->_firstFromDb( "Status < 90 AND StatLief < 90 ") ;
				$actCustomerTempOrder->_valid == 1 ;
				$actCustomerTempOrder->_nextFromDb()) {
			$actCustomerTempOrder->setStatComm() ;
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
								FTr::tr( "Order No. #1, dated #2, change in article availability", array( "%s:".$this->CustomerOrderNo, "%s:".convDate( $this->Datum))),
								"Bcc: ".$this->eMail->Archive."\n") ;

			$myMail	=	"Kommissionierf�higkeit des Austrags ge�ndert von " . self::$rStatComm[$_old] . " nach " . self::$rStatComm[$_new] ;

			$myText	=	new mimeData( "multipart/alternative") ;
			$myText->addData( "text/plain", xmlToPlain( "<div>".$myMail."</div>")) ;
			$myText->addData( "text/html", "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><head></head><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n".$myMail."</body></html>", "", true) ;

			$myBody	=	new mimeData( "multipart/mixed") ;
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			$myBody->addData( "application/pdf", $this->path->Archive."CustomerTempOrder/".$this->CustomerOrderNo.".pdf", $this->CustomerOrderNo.".pdf", true) ;

			$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
			$mailSendResult	=	$newMail->send() ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 * Export this order as XML file to teh XML/down directory
	 * @param	string	$_key
	 * @param	int	$_id
	 * @param	mixed	$_val
	 */
	function	export( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$oFile	=	fopen( $this->path->Archive."XML/down/CustomerTempOrder".$this->CustomerOrderNo.".xml", "w+") ;
		fwrite( $oFile, "<CustomerTempOrderPaket>\n") ;
		$buffer	=	$this->getXMLF() ;
		fwrite( $oFile, $buffer) ;
		$myCustomerTempOrderItem	=	new CustomerTempOrderItem() ;
		$myCustomerTempOrderItem->CustomerOrderNo	=	$this->CustomerOrderNo ;
		for ( $myCustomerTempOrderItem->_firstFromDb( "CustomerOrderNo='$this->CustomerOrderNo' ORDER BY ItemNo ") ;
					$myCustomerTempOrderItem->_valid == 1 ;
					$myCustomerTempOrderItem->_nextFromDb()) {
			$buffer	=	$myCustomerTempOrderItem->getXMLF() ;
			fwrite( $oFile, $buffer) ;
		}
		fwrite( $oFile, "</CustomerTempOrderPaket>\n") ;
		fclose( $oFile) ;
		return $this->getXMLComplete() ;
		FDbg::end() ;
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
			$myObj	=	new FDbObject( "CustomerTempOrder", "CustomerOrderNo", "def", "v_CustomerTempOrderSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"CustomerOrderNo like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"CustomerTempOrderItem"	:
			$myObj	=	new FDbObject( "CustomerTempOrderItem", "Id", "def", "v_CustomerTempOrderItemList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( CustomerOrderNo = '" . $this->CustomerOrderNo . "') " ;
			$filter2	=	"( ArticleDescription like '%".$sCrit."%' )" ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( [ "ItemNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"ArticleSalesPriceCache"	:
			$myObj	=	new FDbObject( "ArticleSalesPriceCache", "Id", "def", "v_ArticleSalesPriceCache_1") ;
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( ArticleNo like '%" . $sCrit . "%' OR Description like '%" . $sCrit . "%') " ;
			$filter2	=	"MarketId = '" . $this->MarketId . "' " ;
			$filter3	=	( isset( $_POST['ArticleNo']) ? "ArticleNo like '%" . $_POST['ArticleNo'] . "%' " : "") ;
			$filter4	=	( isset( $_POST['Description']) ? "Description like '%" . $_POST['Description'] . "%' " : "") ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addOrder( ["ArticleNo"]) ;
			$myQuery->addWhere( [ $filter1, $filter2, $filter3]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
	/**
	 *
	 */
	function	_consolidate( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$myCustomerTempOrderItem	=	new CustomerTempOrderItem() ;
		$myCustomerTempOrderItem->setIterCond( "CustomerOrderNo = '".$this->CustomerOrderNo."' ") ;
		$myCustomerTempOrderItem->setIterOrder( "ORDER BY ItemNo, SubItemNo ") ;
		$myArticle	=	new Article() ;
		/**
		 * determine the total taxable amounts per tax-class
		 */
		$myTaxes	=	array() ;
		foreach ( $myCustomerTempOrderItem as $key => $line) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerTempOrder.php", "CustomerTempOrder", "addToJournal(...)",
								"InvcNo " . $line->CustomerOrderNo . " ItemNo " . $line->ItemNo . $line->SubItemNo) ;
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
		$actCustomerTempOrder	=	new CustomerTempOrder() ;
		$actCustomerTempOrder->setIterCond( "1 = 1 ") ;
		$actCustomerTempOrder->setIterOrder( "ORDER BY CustomerOrderNo ") ;
		foreach( $actCustomerTempOrder as $ndx => $obj) {
			$obj->_consolidate() ;
		}
		$this->reload() ;
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 */
	function	createPDF( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myCustomerTempOrderDoc	=	new CustomerTempOrderDoc( $_key, true) ;
		$myName	=	$myCustomerTempOrderDoc->createPDF( $_key, $_id, $_val) ;
		$this->pdfName	=	$myCustomerTempOrderDoc->pdfName ;
		$this->fullPDFName	=	$myCustomerTempOrderDoc->fullPDFName ;
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
		$myCustomerTempOrderDoc	=	new CustomerTempOrderDoc( $_key, false) ;
		$reply->pdfName	=	$myCustomerTempOrderDoc->pdfName ;
		$reply->fullPDFName	=	$myCustomerTempOrderDoc->fullPDFName ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	printPDF( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$myCustomerTempOrderDoc	=	new CustomerTempOrderDoc( $_key, false) ;
		$this->pdfName	=	$myCustomerTempOrderDoc->pdfName ;
		$this->fullPDFName	=	$myCustomerTempOrderDoc->fullPDFName ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
}
?>
