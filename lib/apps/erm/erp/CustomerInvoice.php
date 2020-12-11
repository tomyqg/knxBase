<?php
/**
 * CustomerInvoice.php Definition der Basis Klasses f�r Customern Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerDelivery - Basis Klasse f�r Customern Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerInvoice
 */
class	CustomerInvoice	extends	AppObjectERM_CR	{
	const	NEU			=	  0 ;
	const	ONGOING		=	 50 ;
	const	REMINDED	=	 70 ;
	const	CLOSED		=	 90 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						CustomerInvoice::NEU		=> "Neu",
						CustomerInvoice::ONGOING	=> "Verschickt",
						CustomerInvoice::REMINDED	=> "Angemahnt",
						CustomerInvoice::CLOSED	=> "Abgeschlossen (Bezahlt)",
						CustomerInvoice::CANCELLED	=> "Storniert"
					) ;
	/**
	 * OBJECT INSTANTIATION AND RETRIEVAL
	 */
	/**
	 *
	 */
	function	__construct( $_myCustomerInvoiceNo="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerInvoiceNo')") ;
		parent::__construct( "CustomerInvoice", "CustomerInvoiceNo") ;
		if ( strlen( $_myCustomerInvoiceNo) > 0) {
			$this->setCustomerInvoiceNo( $_myCustomerInvoiceNo) ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerInvoiceNo')", "no invoice number specified!") ;
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setCustomerInvoiceNo( $_myCustomerInvoiceNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '%s')", $_myCustomerInvoiceNo) ;
		$this->CustomerInvoiceNo	=	$_myCustomerInvoiceNo ;
		if ( strlen( $_myCustomerInvoiceNo) > 0) {
			$this->reload() ;
		}
		FDbg::end() ;
	}
	/**
	 *	DEPENDENT OBJECT ADD/UPD/DEL
	 */
	/**
	 * Updates a dependent object of AppObejctCR
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws Exception
	 * @return string
	 */
	function	updDep( $_key="", $_id=-1, $_val="") {
		if ( $this->LockState == 0) {
			try {
				$tmpObj	=	new CustomerInvoiceItem() ;
				if ( $tmpObj->setId( $_id)) {
					$tmpObj->getFromPostL() ;
					$tmpObj->TotalPrice	=	$tmpObj->QuantityInvoiced * $tmpObj->Price ;
					$tmpObj->updateInDb() ;
				} else {
					$e	=	new Exception( 'AppObject::updPos[Id='.$_id.'] is INVALID !') ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'CustomerInvoice.php::CustomerInvoice::updDep(...): object is locked!') ;
			error_log( $e) ;
			throw $e ;
		}
		$ret	=	"<" . $this->className . ">\n" .
					$this->getDepAsXML( $_key, $_id, "CustomerInvoiceItem") .
					"</" . $this->className . ">\n" ;
		return $ret ;
	}
	/**
	 * methods: business logic
	 */
	function	newFromCustomerInvoiceOL( $_key="", $_id=-1, $_cuOrdrNo="") {
		$this->_newFrom( "CustomerOrder", $_cuOrdrNo, "", "AND 0 = 1 ") ;		// create a new instance
		$this->setTexte() ;
		$this->setAnschreiben() ;
		$this->_addOL() ;
		$myCustomerInvoicePos	=	new CustomerInvoiceItem( $this->CustomerInvoiceNo) ;
		return $this->getXMLComplete() ;
	}
	function	newFromCustomerOrderA( $_key="", $_id=-1, $_cuOrdrNo) {
		$this->_newFrom( "CustomerOrder", $_cuOrdrNo) ;		// create a new instance
		$this->setTexte() ;
		$this->setAnschreiben() ;
		$this->_addOL() ;
		$this->_addA() ;
		$myCustomerInvoicePos	=	new CustomerInvoiceItem( $this->CustomerInvoiceNo) ;
		return $this->getXMLComplete() ;
	}
	function	addHdlgPsch() {
		try {
			$newCustomerInvoiceItem	=	$this->_addSpezial( CustomerOrderItem::HDLGPSCH, "HDLGPSCH") ;
			$this->restate() ;
		} catch ( Exception $e) {
			throw $e ;
		}

		return $newCustomerInvoiceItem ;
	}
	function	addVrsnd() {
		try {
			$newCustomerInvoiceItem	=	$this->_addSpezial( CustomerOrderItem::VRSND, "VRSND") ;
			$this->restate() ;
		} catch ( Exception $e) {
			throw $e ;
		}

		return $newCustomerInvoiceItem ;
	}
	function	addVrschng() {
		try {
			$newCustomerInvoiceItem	=	$this->_addSpezial( CustomerOrderItem::VRSND, "VRSCHNG") ;
					$this->restate() ;
		} catch ( Exception $e) {
			throw $e ;
		}

		return $newCustomerInvoiceItem ;
	}
	function	addColli() {
		$actCustomerInvoiceItem	=	new CustomerInvoiceItem() ;
		$lastCustomerDeliveryNo	=	"" ;
		for ( $myCustomerInvoiceItem->firstFromDb( "", "", null, "") ;
				$myCustomerInvoiceItem->_valid == 1 ;
				$myCustomerInvoiceItem->nextFromDb()) {
			FDbg::dumpL( 0x00000001, "CustomerInvoice::addColli, CustomerInvoicePos->ItemNo=%d", $actCustomerInvoicePos->ItemNo) ;
			FDbg::dumpL( 0x00000001, "CustomerInvoice::addColli, CustomerDeliveryNo=%s", $actCustomerInvoicePos->CustomerDeliveryNo) ;
			if ( strcmp( $actCustomerInvoicePos->CustomerDeliveryNo, $lastCustomerDeliveryNo) != 0) {
				FDbg::dumpL( 0x00000002, "CustomerInvoice::addColli, muss angefuegt werden") ;
				$crit	=	sprintf( "VeColiTyp = %d AND RefNr = '%s' ", VeColi::KDLIEF, $actCustomerInvoicePos->CustomerDeliveryNo) ;
				$actVeColi	=	new VeColi() ;
				for ( $actVeColi->getFirst( $crit) ;
						$actVeColi->_valid == 1 ;
						$actVeColi = $actVeColi->getNextPos( $crit)) {
					FDbg::dumpL( 0x00000004, "CustomerInvoice::addColli, VeColiNr=%s", $actVeColi->VeColiNr) ;
					for ( $actVeColiPos = $actVeColi->getFirstPos() ;
							$actVeColiPos->_valid == 1 ;
							$actVeColiPos = $actVeColi->getNextPos()) {

						FDbg::dumpL( 0x00000008, "CustomerInvoice::addColli, VeColiPos->ItemNo=%d", $actVeColiPos->ItemNo) ;
						$newCustomerInvoicePos	=	$this->addVrsnd() ;
						$newCustomerInvoicePos->Price	=	$actVeColiPos->VrsndKost ;
						$newCustomerInvoicePos->ReferencePrice	=	$actVeColiPos->VrsndKost ;
						$newCustomerInvoicePos->Quantity	=	1 ;
						$newCustomerInvoicePos->TotalPrice	=	$actVeColiPos->VrsndKost ;
						$newCustomerInvoicePos->AddText	=	"Paketversand, TrckNr=" . $actVeColiPos->TrckNr . " " ;
						$newCustomerInvoicePos->updateInDb() ;

						if ( $actVeColiPos->VrschngKost > 0) {
							$newCustomerInvoicePos	=	$this->addVrschng() ;
							$newCustomerInvoicePos->Price	=	$actVeColiPos->VrschngKost ;
							$newCustomerInvoicePos->ReferencePrice	=	$actVeColiPos->VrschngKost ;
							$newCustomerInvoicePos->Quantity	=	1 ;
							$newCustomerInvoicePos->TotalPrice	=	$actVeColiPos->VrschngKost ;
							$newCustomerInvoicePos->AddText	=	"Paketversand, TrckNr=" . $actVeColiPos->TrckNr . " " ;
							$newCustomerInvoicePos->updateInDb() ;
						}
					}
				}
			}
			$lastCustomerDeliveryNo	=	$actCustomerInvoicePos->CustomerDeliveryNo ;
		}
	}
	function	updAddText( $_id, $_text) {
		FDbg::dump( "CustomerInvoice::updAddText(%d, '%s')", $_id, $_text) ;
		try {
			$this->tmpCustomerInvoicePos	=	new CustomerInvoiceItem() ;
			$this->tmpCustomerInvoicePos->Id	=	$_id ;
			$this->tmpCustomerInvoicePos->fetchFromDbById() ;
			if ( $this->tmpCustomerInvoicePos->_valid == 1) {
				FDbg::dump( "CustomerInvoice::updAddText: refers to ItemNo=%d", $this->tmpCustomerInvoicePos->ItemNo) ;
				$this->tmpCustomerInvoicePos->AddText	=	$_text ;
				$this->tmpCustomerInvoicePos->updateInDb() ;
			} else {
				throw new Exception( 'CustomerInvoice::updAddText: CustomerInvoiceItem[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dump( "CustomerInvoice::updAddText(%s), done", $_id) ;
		return $this->tmpCustomerInvoicePos ;
	}
	function	paidWithSkonto( $_key="", $_id="", $_val="") {
		$this->BezahltDatum	=	$_POST['_IBezahltDatumKDRO'] ;
		$this->BezahltBetrag	=	$this->TotalPrice ;
		$this->updateInDb() ;
	}

	function	paidWithoutSkonto( $_key="", $_id="", $_val="") {
		$this->BezahltDatum	=	$_POST['_IBezahltDatumKDRO'] ;
		$this->BezahltBetrag	=	$this->TotalPrice ;
		$this->updateInDb() ;
	}
	function	paidOther( $_key="", $_id="", $_val="") {
		$this->BezahltDatum	=	$_POST['_IBezahltDatumKDRO'] ;
		$this->BezahltBetrag	=	$_POST['_IBezahltBetragKDRO'] ;
		$this->updateInDb() ;
	}
	function	alignItemNo( $_key, $_id, $_val) {
		try {
			$query	=	sprintf( "CustomerInvoice_alignItemNo( @status, '%s') ; ", $this->CustomerInvoiceNo) ;
			$sqlRows	=	FDb::callProc( $query) ;
		} catch ( Exception $e) {
			throw( $e) ;
		}
		return $this->getXMLComplete() ;
	}
	function	sendByEMail( $_key, $_id, $_val) {
		try {
			$this->upd( '', '', '') ;
			$newMail	=	new mimeMail( $this->eMail->Sales,
								$_POST['_IeMail'],
								$this->eMail->Sales,
								FTr::tr( "Your order, our No. #1, dated #2, invoice #3", array( "%s:".$this->CustomerOrderNo, "%s:".convDate( $this->Datum),"%s:".$this->CustomerInvoiceNo)),
								"Bcc: ".$this->eMail->Archive."\n") ;
			$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
			$myDisclaimerText	=	new SysTexte( "DisclaimerText") ;
			$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer") ;
			$myReplTableOut	=	array( $this->myCustomerContact->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext) ;
			$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;

			$myText	=	new mimeData( "multipart/alternative") ;
			$myText->addData( "text/plain", xmlToPlain( "<div>".$myMail."</div>")) ;
			$myText->addData( "text/html", "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><head></head><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n".$myMail."</html>", "", true) ;

			$myBody	=	new mimeData( "multipart/mixed") ;
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			$myBody->addData( "application/pdf", $this->path->Archive."CustomerInvoice/".$this->CustomerInvoiceNo.".pdf", $this->CustomerInvoiceNo.".pdf", true) ;

			$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
			$mailSendResult	=	$newMail->send() ;

			$this->DocVerschVia	=	Opt::DOCVIAEMAIL ;
			$this->_addRem( FTr::tr( "send by E-Mail to: #1 (Bcc: #2)", array( "%s:".$_POST['_IeMail'],"%s:".$this->eMail->Archive))) ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	function	sendByFAX( $_key, $_id, $_val) {
		require_once( "Fax.php" );
		$myFaxNr	=	$_POST['_IFAX'] ;
		sendFax( $myFaxNr,
					$this->path->Archive."CustomerInvoice/".$this->CustomerInvoiceNo.".pdf", "", "", "miskhwe",
					3) ;
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	CustomerInvoice::ONGOING ;		// ueber "Normal"-FAX
		$this->_addRem( "verschickt per FAX an " . $myFaxNr) ;
	}
	/**
	 *
	 */
	function	_consolidate( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '<val>')") ;
		$myCustomerInvoiceItem	=	new CustomerInvoiceItem() ;
		$myCustomerInvoiceItem->setIterCond( "CustomerInvoiceNo = '".$this->CustomerInvoiceNo."' ") ;
		$myCustomerInvoiceItem->setIterOrder( "ORDER BY ItemNo, SubItemNo ") ;
		$myArticle	=	new Article() ;
		/**
		 * determine the total taxable amounts per tax-class
		 */
		$myTaxes	=	array() ;
		foreach ( $myCustomerInvoiceItem as $key => $line) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerInvoice.php", "CustomerInvoice", "addToJournal(...)",
								"InvcNo " . $line->CustomerInvoiceNo . " ItemNo " . $line->ItemNo . $line->SubItemNo) ;
			$myArticle->setArticleNo( $line->ArticleNo) ;
			if ( ! isset( $myTaxes[ $myArticle->MwstSatz])) {
				$myTaxes[ $myArticle->MwstSatz]	=	new Mwst( $myArticle->MwstSatz) ;
				$myTaxes[ $myArticle->MwstSatz]->Total	=	0.0 ;
			}
			$myTax	=	$myTaxes[ $myArticle->MwstSatz] ;
			$myTax->Total	+=	$line->QuantityInvoiced * $line->Price ;
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
		$this->GesamtMwst	=	$myTotalTax ;
		$this->updateColInDb( "GesamtMwst") ;
		FDbg::end( 1, "CustomerInvoice.php", "CustomerInvoice", "addToJournal( '$_key', $_id, '<val>')") ;
	}
	function	consolidate( $_key="", $_id=-1, $_val="") {
		$this->_consolidate() ;
		$this->reload() ;
		return $this->getXMLComplete() ;
	}
	function	buche( $_key="", $_id=-1, $_val="") {		}
	function	unbuche( $_key="", $_id=-1, $_val="") {		}
	function	getXMLDocInfo( $_key="", $_id="", $_val="") {
		$ret	=	"<Document>" ;
		$filename	=	$this->path->Archive . "CustomerInvoice/" . $this->CustomerInvoiceNo . ".pdf" ;
		$filenameC	=	$this->path->Archive . "CustomerInvoice/" . $this->CustomerInvoiceNo . "c.pdf" ;
		if ( file_exists( $filenameC)) {
			$ret	.=	 $this->url->Archive . "CustomerInvoice/" . $this->CustomerInvoiceNo . "c.pdf" ;
		} else if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "CustomerInvoice/" . $this->CustomerInvoiceNo . ".pdf" ;
		}
		$ret	.=	"</Document>" ;
		return $ret ;
	}
	function	getAnschAsHTML(  $_key="", $_id="", $_val="") {
		$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer") ;
		$myReplTableOut	=	array( $this->myCustomerContact->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext) ;
		$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;
		return $myMail ;
	}
	/**
	 * methods: option related
	 */
	function	getRStatus() {	return  self::$rStatus ;		}
	/**
	 * methods: internal
	 */
	function	_addOL() {
		$myCustomerDelivery	=	new CustomerDelivery() ;
		$myCustomerDelivery->setIterCond( "CustomerOrderNo = $this->CustomerOrderNo ") ;
		$myCustomerDeliveryItem	=	new CustomerDeliveryItem() ;
		$myCustomerInvoiceItem	=	new CustomerInvoiceItem() ;
		foreach ( $myCustomerDelivery AS $keyDlvr => $dataDlvr) {
			$myCustomerDeliveryItem->setIterCond( "CustomerDeliveryNo = $myCustomerDelivery->CustomerDeliveryNo AND QuantityInvoiced < QuantityDelivered") ;
			foreach ( $myCustomerDeliveryItem AS $keyDlvrItem => $dataDlvrItem) {
				$myCustomerInvoiceItem->copyFrom( $myCustomerDeliveryItem) ;
				$myCustomerInvoiceItem->CustomerInvoiceNo	=	$this->CustomerInvoiceNo ;
				$myCustomerInvoiceItem->QuantityInvoiced	=	$myCustomerDeliveryItem->QuantityDelivered - $myCustomerDeliveryItem->QuantityInvoiced ;
				$myCustomerInvoiceItem->TotalPrice	=	$myCustomerInvoiceItem->QuantityInvoiced * $myCustomerInvoiceItem->Price ;
				$myCustomerInvoiceItem->storeInDb() ;
				$myCustomerDeliveryItem->QuantityInvoiced	=	$myCustomerDeliveryItem->QuantityDelivered ;
				$myCustomerDeliveryItem->updateInDb() ;
			}
		}
	}
	/**
	 *
	 */
	function	_addA() {
		$myCustomerOrderItem	=	new CustomerOrderItem( $this->CustomerOrderNo) ;
		$myCustomerOrderItem->setIterCond( "CustomerOrderNo = $this->CustomerOrderNo AND MemngerBereitsInvoiced < Quantity ") ;
		$myCustomerInvoiceItem	=	new CustomerInvoiceItem() ;
		foreach ( $myCustomerOrderItem as $key => $item) {
			$myCustomerInvoiceItem->copyFrom( $myCustomerOrderItem) ;
			$myCustomerInvoiceItem->CustomerInvoiceNo	=	$this->CustomerInvoiceNo ;
			$myCustomerInvoiceItem->QuantityInvoiced	=	$myCustomerOrderItem->Quantity - $myCustomerOrderItem->QuantityBereitsInvoiced ;
			$myCustomerInvoiceItem->TotalPrice	=	$myCustomerOrderItem->QuantityInvoiced * $myCustomerOrderItem->Price ;
			$myCustomerInvoiceItem->storeInDb() ;
			$myCustomerInvoiceItem->QuantityBereitsInvoiced	=	$myCustomerOrderItem->Quantity ;
			$myCustomerInvoiceItem->updateInDb() ;
		}
	}
	private	function	_addSpezial( $_posType, $_artikelNr) {
		try {
			$newCustomerInvoiceItem	=	new CustomerInvoiceItem( $this->CustomerInvoiceNo) ;
			$newCustomerInvoiceItem->getNextItemNo( $_posType) ;
			$newCustomerInvoiceItem->PosType	=	$_posType ;
			$newCustomerInvoiceItem->ArticleNo	=	$_artikelNr ;
			$newCustomerInvoiceItem->Quantity	=	1 ;
			$newCustomerInvoiceItem->InvoicedeQuantity	=	1 ;
			$newCustomerInvoiceItem->Price	=	0.0 ;
			$newCustomerInvoiceItem->ReferencePrice	=	0.0 ;
			$newCustomerInvoiceItem->QuantityPerPU	=	1 ;
			$newCustomerInvoiceItem->TotalPrice	=	0.0 ;
			$newCustomerInvoiceItem->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}

		return $newCustomerInvoiceItem ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 */
	function	consolidateAll( $_key="", $_id=-1, $_val="") {
		$actCustomerInvoice	=	new CustomerInvoice() ;
		$actCustomerInvoice->setIterCond( "1 = 1 ") ;
		$actCustomerInvoice->setIterOrder( "ORDER BY CustomerInvoiceNo ") ;
		foreach( $actCustomerInvoice as $ndx => $obj) {
			$obj->_consolidate() ;
		}
		$this->reload() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * add to financial journal
	 */
	function	addToJournal( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '<val>')") ;
		$myCustomerInvoiceItem	=	new CustomerInvoiceItem() ;
		$myCustomerInvoiceItem->setIterCond( "CustomerInvoiceNo = '".$this->CustomerInvoiceNo."' ") ;
		$myCustomerInvoiceItem->setIterOrder( "ORDER BY ItemNo, SubItemNo ") ;
		$myArticle	=	new Article() ;
		/**
		 * determine the total taxable amounts per tax-class
		 */
		$myTaxes	=	array() ;
		foreach ( $myCustomerInvoiceItem as $key => $line) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerInvoice.php", "CustomerInvoice", "addToJournal(...)",
								"InvcNo " . $line->CustomerInvoiceNo . " ItemNo " . $line->ItemNo . $line->SubItemNo) ;
			$myArticle->setArticleNo( $line->ArticleNo) ;
			if ( ! isset( $myTaxes[ $myArticle->MwstSatz])) {
				$myTaxes[ $myArticle->MwstSatz]	=	new Mwst( $myArticle->MwstSatz) ;
				$myTaxes[ $myArticle->MwstSatz]->Total	=	0.0 ;
			}
			$myTax	=	$myTaxes[ $myArticle->MwstSatz] ;
			$myTax->Total	+=	$line->QuantityInvoiced * $line->Price ;
		}
		/**
		 * pull the journal line items together
		 */
		$myTotal	=	0.0 ;
		$myTotalWTax	=	0.0 ;
		$myJournal	=	new Journal( substr( $this->Datum, 0, 4)) ;
		$myJournalLI	=	new JournalLineItem() ;
		$myJournalLI->JournalNo	=	$myJournal->JournalNo ;
		$myJournalLI->_getNextLineNo() ;
		$myJournalLI->RefNo	=	$this->CustomerInvoiceNo ;
		$myJournalLI->Date	=	$this->Datum ;
		$myJournalLI->Description	=	"Customernrechnung" ;
		$myJournalLI->JournalNo	=	substr( $this->Datum, 0, 4) ;
		$myJournalLI->ItemNo	=	30 ;
		foreach ( $myTaxes as $ndx => $class) {
			$class->Tax	=	$class->Total * $class->ProzSatz / 100 ;
			FDbg::trace( 2, FDbg::mdTrcInfo1, "CustomerInvoice.php", "CustomerInvoice", "addToJournal(...)",
								"Class " . $class->MwstSatz . ", VAT " . $class->ProzSatz . ", Sum " . $class->Total . ", Tax " . $class->Tax) ;
			$myJournalLI->AccountCredit	=	$class->AccountNo ;
			$myJournalLI->AmountCredit	=	$class->Tax ;
			$myJournalLI->storeInDb() ;
			$myTotal	+=	$class->Total ;
			$myTotalWTax	+=	$class->Total + $class->Tax ;
			$myJournalLI->ItemNo	+=	10 ;
		}
		$myJournalLI->AccountDebit	=	"1200" ;	// Forderungen aus Lieferungen und Leistungen
		$myJournalLI->AccountCredit	=	"" ;
		$myJournalLI->AmountDebit	=	$myTotalWTax ;
		$myJournalLI->AmountCredit	=	0.0 ;
		$myJournalLI->ItemNo	=	10 ;
		$myJournalLI->storeInDb() ;
		$myJournalLI->AccountDebit	=	"" ;
		$myJournalLI->AccountCredit	=	"4000" ;	// Umsatzerl�se
		$myJournalLI->AmountDebit	=	"" ;
		$myJournalLI->AmountCredit	=	$myTotal ;
		$myJournalLI->ItemNo	=	20 ;
		$myJournalLI->storeInDb() ;

		FDbg::end( 1, "CustomerInvoice.php", "CustomerInvoice", "addToJournal( '$_key', $_id, '<val>')") ;
	}
	/**
	 *	RETRIEVAL METHODS
	 */
	/**
	 *
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
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
			$myObj	=	new FDbObject( "CustomerInvoice", "CustomerInvoiceNo", "def", "v_CustomerInvoiceSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"CustomerInvoiceNo like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"CustomerInvoiceItem"	:
			$myObj	=	new FDbObject( "CustomerInvoiceItem", "Id", "def", "v_CustomerInvoiceItemList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( CustomerInvoiceNo = '" . $this->CustomerInvoiceNo . "') " ;
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
	 *	PRINTING FUNCTIONS
	 */
	/**
	 *
	 */
	function	createPDF( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myCustomerInvoiceDoc	=	new CustomerInvoiceDoc( $_key, true) ;
		$myName	=	$myCustomerInvoiceDoc->createPDF( $_key, $_id, $_val) ;
		$this->pdfName	=	$myCustomerInvoiceDoc->pdfName ;
		$this->fullPDFName	=	$myCustomerInvoiceDoc->fullPDFName ;
		$reply->replyData	=	"<Reference>" . $this->fullPDFName . "</Reference>\n" ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * returns the name of the PDF file which has been created
	 */
	function	getPDF( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaAppPDF ;
		$myCustomerInvoiceDoc	=	new CustomerInvoiceDoc( $_key, false) ;
		$reply->pdfName	=	$myCustomerInvoiceDoc->pdfName ;
		$reply->fullPDFName	=	$myCustomerInvoiceDoc->fullPDFName ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	printPDF( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myCustomerInvoiceDoc	=	new CustomerInvoiceDoc( $_key) ;
		$this->pdfName	=	$myCustomerInvoiceDoc->pdfName ;
		$this->fullPDFName	=	$myCustomerInvoiceDoc->fullPDFName ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
}
?>
