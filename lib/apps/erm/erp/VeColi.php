<?php

/**
 * VeColi.php - Definition der Basis Klasses f�r Versandobjekte
 *	Attribute:
 *		VeColiTyp	Art des Paketes, kann sein:
 *					VeColi::KDLIEF		=> Lieferung an einen Kunden. Ref.Nr. referenziert die CuDlvrNo !
 *					VeColi::KDLEIH		=> Leihstellung an einen Kunden. Ref.Nr. referenziert die KdLeihNr !
 *					VeColi::KDMISC		=> Sonstige Lieferung an einen Kunden. Ref.Nr. referenziert die KundeNr-KundeKontaktNr !
 *					VeColi::LFMISC		=> Sonstige Lieferung an einen Lieferanten. Ref.Nr. referenziert die LiefNr-LiefKontaktNr !
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * VeColi - Basis Klasse f�r Kunden Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage VeColi
 */
class	VeColi	extends	AppObject	{

	public	$Receiver ;
	public	$tmpVeColiPos ;
	public	$myCarr ;
	private	$myCond ;
	

	const	OPEN	=	 0 ;
	const	LOCKED	=	90 ;
	private	static	$rStatus	=	array (
				VeColi::OPEN => "open",
				VeColi::LOCKED => "locked") ;

	const	KDSTART	=	  0 ;
	const	KDLIEF	=	110 ;		// to customer: normal delivery; ref.no. describes CuDlvrNo
	const	KDLEIH	=	120 ;		// to customer: test drive; ref.no.describes CuTestNo
	const	KDRMA	=	130 ;		// to customer: RMA return; ref.no. describes CustRMANo
	const	KDMISC	=	190 ;		// to customer: miscellaneous; ref.no. describes CustNo/CustContactNo
	const	KDRET	=	195 ;		// pick-up at customer, ordered by us
	const	KDEND	=	199 ;
	const	LFSTART	=	200 ;
	const	LFRMA	=	230 ;		// to supplier: RMA return; ref.no. describes SuppRMANo
	const	LFMISC	=	290 ;		// to supplier: miscellaneous; ref.no. describes SuppNo/SuppContactNo
	const	LFRET	=	295 ;		// from supplier: pick-up at supplier, ordered by us; ref.no. describes
	const	LFEND	=	299 ;
	const	ADRSTART=	300 ;
	const	ADRMISC	=	390 ;		// to address: miscellaneous
	const	ADREND	=	399 ;
	private	static	$rVeColiTyp	=	array (
				VeColi::KDLIEF	=> "Customer Delivery",
				VeColi::KDLEIH	=> "Customer Trial",
				VeColi::KDRMA	=> "RMA to Customer",
				VeColi::KDMISC	=> "Miscellaneous to Customer",
				VeColi::KDRET	=> "Pick-up at Customer",
				VeColi::LFRMA	=> "RMA to Supplier",
				VeColi::LFMISC	=> "Miscellaneous to Supplier",
				VeColi::LFRET	=> "Pick-up at Supplier",
				VeColi::ADRMISC	=> "Miscellaneous to Address"
			) ;
	/**
	 *
	 */
	function	__construct( $_myVeColiNr='') {
		FDbg::dumpL( 0x00000001, "VeColi::__construct( '%s')", $_myVeColiNr) ;
		parent::__construct( "VeColi", "VeColiNr") ;
		$this->Datum	=	$this->today() ;
		$this->Receiver	=	NULL ;
		$this->myCarr	=	new Carr() ;
		if ( strlen( $_myVeColiNr) > 0) {
			$this->setVeColiNr( $_myVeColiNr) ;
		} else {
			FDbg::dumpL( 0x00000001, "VeColi::__construct(...): VeColiNr not specified") ;
		}
		FDbg::dumpL( 0x00000001, "VeColi::__construct(...) done") ;
	}
	/**
	 *
	 */
	function	setVeColiNr ( $_myVeColiNr) {
		FDbg::dumpL( 0x00000001, "VeColi::setVeColiNr( '%s')", $_myVeColiNr) ;
		$this->VeColiNr	=	$_myVeColiNr ;
		if ( strlen( $_myVeColiNr) > 0) {
			$this->reload() ;
		}
		FDbg::dumpL( 0x00000001, "VeColi::setVeColiNr(...) done") ;
	}
	/**
	 *
	 */
	function	reload() {
		FDbg::dumpL( 0x00000001, "VeColi::reload()") ;
		FDbObject::reload() ;
		if ( $this->_valid == 1) {
			FDbg::dumpL( 0x00000001, "VeColi::reload(): VeColi is valid !") ;
			/**
			 *
			 */
			try {
				switch ( $this->VeColiTyp) {
				case	VECOLI::KDLIEF	:
					$tmpCuDlvr	=	new CuDlvr( $this->RefNr) ;
					$this->Receiver	=	new Receiver() ;
					$this->Receiver->setFromKunde( $tmpCuDlvr->KundeNr, $tmpCuDlvr->KundeKontaktNr) ;
					break ;
				case	VECOLI::KDLEIH	:
					$tmpKdLeih	=	new KdLeih( $this->RefNr) ;
					$this->Receiver	=	new Receiver() ;
					$this->Receiver->setFromKunde( $tmpKdLeih->KundeNr, $tmpKdLeih->KundeKontaktNr) ;
					break ;
				case	VECOLI::KDRMA	:
				case	VECOLI::KDMISC	:
				case	VECOLI::KDRET	:
					$comps	=	explode( "/", $this->RefNr) ;
					$custNo	=	$comps[0] ;
					$custContactNo	=	$comps[1] ;
					$this->Receiver	=	new Receiver() ;
					$this->Receiver->setFromKunde( $custNo, $custContactNo) ;
					break ;
				case	VECOLI::LFRMA	:
				case	VECOLI::LFMISC	:
				case	VECOLI::LFRET	:
					$this->Receiver	=	new Receiver() ;
					$this->Receiver->setFromLief( substr( $this->RefNr, 0, 6), substr( $this->RefNr, 7, 3)) ;
					break ;
				case	VECOLI::ADRMISC	:
					$this->Receiver	=	new Receiver() ;
					$this->Receiver->setFromAdr( substr( $this->RefNr, 0, 6), substr( $this->RefNr, 7, 3)) ;
					break ;
				}
			} catch ( Exception $e) {
				error_log( "Exception '$e' stopped") ;
			}

			/**
			 *
			 */
			try {
				$this->myCarr->setCarrier( $this->Carrier) ;
			} catch( Exception $e) {
				throw $e ;
			}
		}
		FDbg::dumpL( 0x00000001, "VeColi::reload() done") ;
	}
	/**
	 * F�hrt eine Neuberechnung aller abh�ngigen Werte der Kundenbestellung durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: VeColi_restate( @status, <VeColiNr>).
	 *
	 * @return void
	 */
	function	restate() {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::restate(): begin") ;
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::restate(): end") ;
	}
	/**
	 * F�hrt eine Neuberechnung aller abh�ngigen Werte der Kundenbestellung durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: VeColi_restate( @status, <VeColiNr>).
	 *
	 * @return void
	 */
	function	recalc() {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::recalc(): begin") ;
		try {
			$this->getShipFee() ;
			$this->getInsFee() ;
			$this->restate() ;
		} catch( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::recalc(): end") ;
	}
	/**
	 *
	 */
	function	getReceiver() {	return $this->Receiver ;	}
	/**
	 * addPos:
	 *
	 * Legt eine Position in dieser Coli an. Alle Parameter sind vorbesetzt mit defaults fuer den
	 * Aufruf damit eine leere VeColiPos angelegt werden kann die nachher mit den entsprechenden
	 * Werten besetzt werden kann.
	 * @param $_t
	 * @param $_w
	 * @param $_l
	 * @param $_b
	 * @param $_h
	 * @param $_wert
	 */
	function	addPos( $_t='', $_w=0.0, $_l=0, $_b=0, $_h=0, $_wert=0.0) {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::addPos(...): begin") ;
		/**
		 * establish the specific carrier object
		 * @var unknown_type
		 */
		$myCarr	=	new Carr( $this->Carrier) ;
		$carrClass	=	"Carr_" . $myCarr->CarrName ;
		FDbg::dumpL(0x00000008, "VeColi.php::VeColi::addPos(...): carrierClass := '$carrClass'") ;
		$myCarrier	=	new $carrClass() ;
		try {
			$this->tmpVeColiPos	=	new VeColiPos( $this->VeColiNr) ;
			$this->tmpVeColiPos->dumpStructure() ;
			$this->tmpVeColiPos->_newItemNr() ;
			$this->tmpVeColiPos->TrckNr	=	$_t ;
			$this->tmpVeColiPos->Gewicht	=	$_w ;
			$this->tmpVeColiPos->EinzelDimL	=	$_l ;
			$this->tmpVeColiPos->EinzelDimB	=	$_b ;
			$this->tmpVeColiPos->EinzelDimH	=	$_h ;
			$this->tmpVeColiPos->Wert	=	$_wert ;
			$this->tmpVeColiPos->VrsndKost	=	$myCarrier->getShipFee( $this, $this->tmpVeColiPos, $this->Receiver) ;
			$this->tmpVeColiPos->storeInDb() ;
			$this->restate() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::addPos(...): end") ;
		return $this->tmpVeColiPos ;
	}
	/**
	 * Create a new temporary order with the next available temp-order-nr and stores
	 * the order in the database.
	 *
	 * @return void
	 */
	function	newVeColi() {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::newVeColi(): begin") ;
		$this->newKey() ;
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::newVeColi(): end") ;
	}
	/**
	 * Gets a new item-nr for the next item to add to the temporary order
	 *
	 * @return void
	 */
	function	getVeColiPosNr() {
		$query	=	sprintf( "SELECT PosNr FROM VeColiPos WHERE VeColiNr='%s' ORDER BY PosNr DESC LIMIT 0, 1 ") ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->PosNr	=	$row[0] + 1 ;
		}
		return $this->_status ;
	}
    /**
     * Renumbers the temporary order with the given step-width
     *
     * @param int $_step
     * @return void
     */
	function	renumber( $_step=1) {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::renumber( _step=%d): begin", $_step) ;
		$query	=	sprintf( "VeColi_renum( @status, '%s', %d) ; ", $this->VeColiNr, $_step) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::renumber( _step=%d): end", $_step) ;
	}
	/**
	 *
	 */
	function	getRStatus() {	return self::$rStatus ;			}
	function	getRVeColiTyp() {	return self::$rVeColiTyp ;	}
	function	getRCarrier() {	return self::$rCarrier ;		}
	/**
	 *
	 *	@return	VeColiPos
	 */
	function	getFirst( $_cond='') {
		FDbg::dumpL( 0x00000001, "VeColi::getFirst( '%s')", $_cond) ;
		if ( strlen( $_cond) > 0) {
			$this->myCond	=	$_cond ;
		} else {
			$this->myCond	=	sprintf( "VeColiNr = '%s' ORDER BY PosNr ", $this->VeColiNr) ;
		}
		return $this->firstFromDb( $this->myCond) ;
	}

	/**
	 *
	 *	@return	VeColiPos
	 */
	function	getNext() {
		$this->nextFromDb( $this->myCond) ;
		return $this->tmpVeColiPos ;
	}

	/**
	 *
	 */
	function	getCarr() {
		return $this->myCarr ;
	}
	function	getShipFee( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::getShipFee( '$_key', $_id, '$_val'): begin") ;
		$myCarr	=	new Carr( $this->Carrier) ;
		$carrClass	=	"Carr_" . $myCarr->CarrName ;
		FDbg::dumpL(0x00000008, "VeColi.php::VeColi::getShipFee( '$_key', $_id, '$_val'): carrierClass := '$carrClass'") ;
		$myCarrier	=	new $carrClass() ;
		$actVeColiPos	=	new VeColiPos() ;
		$actVeColiPos->VeColiNr	=	$this->VeColiNr ;
		for ( $actVeColiPos->firstFromDb( "VeColiNr", "", null, "") ;
			$actVeColiPos->_valid == 1 ;
			$actVeColiPos->nextFromDb()) {
			$actVeColiPos->VrsndKost	=	$myCarrier->getShipFee( $this, $actVeColiPos, $this->Receiver) ;
			$actVeColiPos->updateColInDb( "VrsndKost") ;
		}
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::getShipFee( '$_key', $_id, '$_val'): end") ;
		return $this->getXMLComplete() ;		
	}
	function	getInsFee( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::getInsFee( '$_key', $_id, '$_val'): begin") ;
		$myCarr	=	new Carr( $this->Carrier) ;
		$carrClass	=	"Carr_" . $myCarr->CarrName ;
		FDbg::dumpL(0x00000008, "VeColi.php::VeColi::getInsFee( '$_key', $_id, '$_val'): carrierClass := '$carrClass'") ;
		$myCarrier	=	new $carrClass() ;
		$actVeColiPos	=	new VeColiPos() ;
		$actVeColiPos->VeColiNr	=	$this->VeColiNr ;
		for ( $actVeColiPos->firstFromDb( "VeColiNr", "", null, "") ;
			$actVeColiPos->_valid == 1 ;
			$actVeColiPos->nextFromDb()) {
			$actVeColiPos->VrschngKost	=	$myCarrier->getInsFee( $this, $actVeColiPos, $this->Receiver) ;
			$actVeColiPos->updateColInDb( "VrschngKost") ;
		}
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColi::getInsFee( '$_key', $_id, '$_val'): end") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Diese Methode erzeugt die fuer den hier verwendeten Carrier erforderlichen Eintr�ge in
	 * der zugehoerigen Tabelle. Fuer DPD ist dies VeColiPosDPD. Fuer UPS sollte es VeColiPosUPS sein.
	 * Derzeit ist lediglich DPD implementiert. Fuer alle anderen Carrier wird diese Routine OHNE Fehler
	 * beendet.
	 *
	 * @return VOID
	 */
	function	schedule( $_key="", $_id=-1, $_val="") {
		$myCarr	=	new Carr( $this->Carrier) ;
		$carrClass	=	"Carr_" . $myCarr->CarrName ;
		FDbg::dumpL(0x00000008, "VeColi.php::VeColi::schedule( '$_key', $_id, '$_val'): carrierClass := '$carrClass'") ;
		$myCarrier	=	new $carrClass() ;
		$myCarrier->startColli( $this) ;
		/**
		 * 
		 */
		$actVeColiPos	=	new VeColiPos() ;
		$actVeColiPos->VeColiNr	=	$this->VeColiNr ;
		for ( $actVeColiPos->firstFromDb( "VeColiNr", "", null, "") ;
			$actVeColiPos->_valid == 1 ;
			$actVeColiPos->nextFromDb()) {
			$myCarrier->addItem( $this, $actVeColiPos, $this->Receiver) ;
		}
		$myCarrier->endColli() ;
		$myCarrier->scheduleColli() ;
	}

	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTablePostenAsXML() ;
		return $ret ;
	}

	function	getXMLString() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		if ( $this->Receiver != null) {
			$ret	.=	$this->Receiver->getAsXML() ;
		}
		return $ret ;
	}

	function	getTablePostenAsXML() {
		$objName	=	$this->className . "Pos" ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$tmpObj	=	new $objName() ;
		$ret	=	$tmpObj->tableFromDb( "",
								"",
								"C." . $myKeyCol . " = '" . $myKey . "' ",
								"ORDER BY C.PosNr ") ;
		return $ret ;
	}
	
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	addVeColiPos( $_key, $_id, $_val) {
		$myVeColiPos	=	new VeColiPos() ;
		$myVeColiPos->dumpStructure() ;
		$myVeColiPos->VeColiNr	=	$this->VeColiNr ;
		$myVeColiPos->getFromPostL() ;
		$myVeColiPos->_newItemNr() ;
		$myVeColiPos->storeInDb() ;
		return $this->getTablePostenAsXML() ;
	}

	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	updVeColiPos( $_key, $_id, $_val) {
		$myVeColiPos	=	new VeColiPos() ;
		$myVeColiPos->setId( $_id) ;
		$myVeColiPos->getFromPostL() ;
		$myVeColiPos->updateInDb() ;
		return $this->getTablePostenAsXML() ;
	}

	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	delVeColiPos( $_key, $_id, $_val) {
		$myVeColiPos	=	new VeColiPos() ;
		$myVeColiPos->setId( $_id) ;
		$myVeColiPos->removeFromDb() ;
		return $this->getTablePostenAsXML() ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	getList( $_key="", $_id=-1, $_val="") {
		$_suchKrit	=	$_POST['_SVeColiNr'] ;
		$_sStatus	=	intval( $_POST['_SStatus']) ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( " ;
		$filter	.=	"C.VeColiNr like '%" . $_suchKrit . "%' " ;
		if ( $_POST['_SRefNr'] != "")
			$filter	.=	"  AND ( RefNr like '%" . $_POST['_SCompany'] . "%' ) " ;
		if ( $_sStatus != -1) {
			$filter	.=	"AND ( C.Status = " . $_sStatus . ") " ;
		}
		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "VeColiNr", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "Status", "var") ;
		$myObj->addCol( "RefNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.VeColiNr DESC ",
								"VeColi",
								"VeColi",
								"C.Id, C.VeColiNr, C.Datum, C.Status") ;
		return $ret ;
	}
}

/**
 *
 */
class	VeColiPos	extends	AppDepObject	{
	/**
	 *
	 */
	function	__construct( $_veColiNr='') {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::__constructor( '$_veColiNr'): begin") ;
		parent::__construct( "VeColiPos", "Id") ;
		$this->VeColiNr	=	$_veColiNr ;
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::__constructor( '$_veColiNr'): end") ;
	}
	/**
	 *
	 */
	function	_newItemNr() {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::_newItemNr(): begin") ;
		$query	=	sprintf( "SELECT PosNr FROM VeColiPos WHERE VeColiNr='%s' ORDER BY PosNr DESC LIMIT 0, 1 ", $this->VeColiNr) ;
		try {
			$sqlResult	=	FDb::query( $query) ;
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->PosNr	=	$row[0] + 10 ;
		} catch ( Exception $e) { 
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::_newItemNr(): end") ;
	}
	/**
	 * Diese Methode erzeugt die fuer den hier verwendeten Carrier erforderlichen Eintr�ge in
	 * der zugehoerigen Tabelle. Fuer DPD ist dies VeColiPosDPD. Fuer UPS sollte es VeColiPosUPS sein.
	 * Derzeit ist lediglich DPD implementiert. Fuer alle anderen Carrier wird diese Routine OHNE Fehler
	 * beendet.
	 *
	 * @return VOID
	 */
	function	schedule( $_carrier, $_veColiNr, $_anzahlPakete, $_rcvr) {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::refresh( $_carrier, '$_veColiNr', $_anzahlPakete, <_rcvr>): begin") ;
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::refresh( $_carrier, '$_veColiNr', $_anzahlPakete, <_rcvr>): end") ;
	}
	/**
	 * Diese Methode erzeugt die fuer den hier verwendeten Carrier erforderlichen Eintr�ge in
	 * der zugehoerigen Tabelle. Fuer DPD ist dies VeColiPosDPD. Fuer UPS sollte es VeColiPosUPS sein.
	 * Derzeit ist lediglich DPD implementiert. Fuer alle anderen Carrier wird diese Routine OHNE Fehler
	 * beendet.
	 *
	 * @return VOID
	 */
	function	refresh( $_carrier, $_veColiNr) {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::refresh( $_carrier, '$_veColiNr'): begin") ;
		/**
		 *
		 */
		switch ( $_carrier) {
		case	VeColi::VERS_DPDDELI	:
			$newCarrierPos	=	new VeColiPosDPD() ;
			$cond	=	"WHERE VeColiNr='" . $_veColiNr . "' AND PosNr = " . $this->PosNr . " " ;
			$newCarrierPos->fetchFromDbWhere( $cond) ;
			if ( $newCarrierPos->_valid == 1 && strlen( $newCarrierPos->DPD_TrckNr) > 0) {
				$this->TrckNr	=	$newCarrierPos->DPD_TrckNr ;
				$this->updateInDb() ;
			}
			break ;
		}
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::refresh( $_carrier, '$_veColiNr'): end") ;
	}
		
	function	add( $_key, $_id, $_val) {
	}

	function	upd( $_key, $_id, $_val) {
	}

	function	del( $_key, $_id, $_val) {
	}
	/**
	 *
	 */
	function	_getNextItemNo() {
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::_getNextItemNo(): begin") ;
		$query	=	"SELECT PosNr FROM VeColiPos WHERE VeColiNr='".$this->VeColiNr."' ORDER BY PosNr DESC LIMIT 0, 1 " ;
		try {
			$res	=	FDb::queryRow( $query) ;
			if ( $res) {
				$this->PosNr	=	intval( $res["PosNr"]) + 10 ;
			} else {
				$this->PosNr	=	10 ;
			}
		} catch ( Exception $e) { 
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "VeColi.php::VeColiPos::_getNextItemNo(): end") ;
	}
}
?>
