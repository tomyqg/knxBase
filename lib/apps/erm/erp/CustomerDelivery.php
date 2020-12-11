<?php
/**
 * CustomerDelivery.php Definition der Basis Klasses f�r Kunden Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerDelivery - Basis Klasse f�r Kunden Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerDelivery
 */
class	CustomerDelivery	extends	AppObjectERM_CR	{

	private	$myCustomerDelivery ;

	const	NEU			=	  0 ;
	const	UPDATE		=	 30 ;
	const	ONGOING		=	 50 ;
	const	REMINDED	=	 70 ;
	const	WAITINV		=	 80 ;
	const	CLOSED		=	 90 ;
	const	ONHOLD		=	980 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						-1					=> "ALL",
						CustomerDelivery::NEU			=>	"Neu",
						CustomerDelivery::ONGOING		=>	"Gedruckt",
						CustomerDelivery::WAITINV		=>	"Warte auf Berechnung",
						CustomerDelivery::CLOSED		=>	"Abgeschlossen (Ausgeliefert)",
						CustomerDelivery::CANCELLED	=>	"Storniert"
					) ;

	/**
	 *
	 */
	function	__construct( $_myCustomerDeliveryNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerDeliveryNo')") ;
		parent::__construct( "CustomerDelivery", "CustomerDeliveryNo") ;
		$this->myCustomerDelivery	=	NULL ;
		if ( strlen( $_myCustomerDeliveryNo) > 0) {
			$this->setCustomerDeliveryNo( $_myCustomerDeliveryNo) ;
		} else {
		}
		FDbg::end() ;
	}

	/**
	 *
	 */
	function	setCustomerDeliveryNo ( $_myCustomerDeliveryNo) {
		$this->CustomerDeliveryNo	=	$_myCustomerDeliveryNo ;
		if ( strlen( $_myCustomerDeliveryNo) > 0) {
			$this->reload() ;
		}
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_cuCommNo
	 */
	function	_newFromCustomerCommission( $_key="", $_id=-1, $_cuCommNo="") {
		$myCustomerCommission	=	new CustomerCommission( $_cuCommNo) ;				// retrieve the CustomerCommission
		if ( $myCustomerCommission->isValid()) {
			try {
				if ( $myCustomerCommission->CustomerDeliveryNo != "") {
					throw new Exception( "CustomerDelivery.php::CustomerDelivery::_newFromCustomerCommission(): commission already delivered!") ;
				}
				$this->_newFrom( "CustomerCommission", $_cuCommNo, $_cuCommNo) ;	// create a new instance and force a number
				$this->setTexte() ;
				$this->setAnschreiben() ;
				$myCustomerCommission->CustomerDeliveryNo	=	$this->CustomerDeliveryNo ;			// and set the delivery no.
				$myCustomerCommission->updateColInDb( "CustomerDeliveryNo") ;
				/**
				 * update the individual items
				 */
				$myCustomerDeliveryItem	=	new CustomerDeliveryItem() ;
				$myCustomerDeliveryItem->setIterCond( "CustomerDeliveryNo = '" . $this->CustomerDeliveryNo . "' ") ;
				foreach ( $myCustomerDeliveryItem as $key => $item) {
				}
			} catch( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "CustomerDelivery.php::CustomerDelivery::_newFromCustomerCommission(): commission not valid!") ;
			error_log( $e) ;
			throw $e ;
		}
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
			$myObj	=	new FDbObject( "CustomerDelivery", "CustomerDeliveryNo", "def", "v_CustomerDeliverySurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"CustomerDeliveryNo like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"CustomerDeliveryItem"	:
			$myObj	=	new FDbObject( "CustomerDeliveryItem", "Id", "def", "v_CustomerDeliveryItemList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( CustomerDeliveryNo = '" . $this->CustomerDeliveryNo . "') " ;
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
	 * Anlegen der Lieferung zu einer Kommission
	 *
	 * @return void
	 */
	function    newFromCustomerCommission( $_key="", $_id=-1, $_cuCommNo="") {
		try {
			$myCarrier	=	$_POST['_ICarrier'] ;
			$myPackageCount	=	$_POST['_IPkgCntCustomerCommissionSeq'] ;
		} catch ( Exception $e) {
			throw $e ;
		}
		/**
		 * create the (provisionary) PCustomerCommission and CustomerCommission for each distinct supplier
		 */
		try {
			$this->_newFromCustomerCommission( $_key, $_id, $_cuCommNo) ;	// create a new instance and force a number
			/**
			 *
			 */
			$this->Carrier	=	$myCarrier ;
			$this->AnzahlPakete	=	$myPackageCount ;
			$this->updateInDb() ;
			/**
			 * now create the colli
			 */
			$newVeColi	=	new VeColi() ;
			$newVeColi->newVeColi() ;
			$newVeColi->Carrier	=	$this->Carrier ;
			$newVeColi->getCarr()->setCarrier( $this->Carrier) ;
			$newVeColi->VeColiTyp	=	VeColi::KDLIEF ;
			$newVeColi->RefNr	=	$this->CustomerDeliveryNo ;
			$newVeColi->AnzahlPakete	=	$this->AnzahlPakete ;
			$newVeColi->updateInDb() ;
			$newVeColi->reload() ;						// must be reloaded to retrieve "receiver data"
			for ( $i=1 ; $i<=$this->AnzahlPakete ; $i++) {
				$newVeColiPos	=	$newVeColi->addPos(
										$_POST[ '_ITrckNr'][ $i],
										$_POST[ '_FWeight'][ $i],
										$_POST[ '_ILaenge'][ $i],
										$_POST[ '_IBreite'][ $i],
										$_POST[ '_IHoehe'][ $i],
										0.0
										) ;
			}
			$newVeColi->schedule() ;
		} catch( Exception $e) {
			throw $e ;
		}
		/**
		 * create the PDF document and print if (if autoprint is on by config.ini)
		 */
		try {
			$myDoc	=	new CustomerDeliveryDoc( $this->CustomerDeliveryNo) ;
			$myDoc->_createPDF() ;
			$myDoc->printPDF() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::dumpL( 0x00000001, "CustomerDelivery.php::CustomerDelivery::newFromCustomerCommission( '$_key', $_id, '$_cuCommNo'): end") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * returns the XML-formatted list of all items in this CustomerDelivery
	 */
	function	consolidate( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$queryInvc	=	"UPDATE CustomerDeliveryItem AS KdLP " .
					"SET KdLP.QuantityInvoiced = ( " .
					"SELECT SUM( QuantityInvoiced) FROM CustomerInvoiceItem AS KdRP " .
						"WHERE KdRP.CustomerDeliveryNo = KdLP.CustomerDeliveryNo " .
							"AND KdRP.ItemNo = KdLP.ItemNo " .
							"AND KdRP.ArticleNo = KdLP.ArticleNo " .
							"AND KdRP.QuantityPerPU = KdLP.QuantityPerPU " .
							"AND KdRP.FOC = KdLP.FOC " .
					") " .
					"WHERE KdLP.CustomerDeliveryNo = $this->CustomerDeliveryNo " ;
		try {
			$sqlRows	=	FDb::query( $queryInvc) ;	// consolidate the invoicing data
			$this->reload() ;
		} catch( Exception $e) {
			throw $e ;
		}
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "CustomerDeliveryItem") ;
		return $ret ;
	}
	/**
	 * F�hrt eine Neuberechnung aller abh�ngigen Werte der Kundenbestellung durch.
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CustomerDelivery_restate( @status, <CustomerDeliveryNo>).
	 *
	 * @return void
	 */
	function	alignItemNo() {
		$query	=	sprintf( "CustomerDelivery_alignItemNo( @status, '%s') ; ", $this->CustomerDeliveryNo) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
			$this->reload() ;
		} catch( Exception $e) {
			FDbg::dumpF( "CustomerDelivery::alignItemNo(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 *
	 */
	function	getCustomerOrder() {
		return $this->myCustomerOrder ;
	}
	/**
	 *
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		$actCustomerDeliveryItem	=	new CustomerDeliveryItem() ;
		$cond	=	"CustomerDeliveryNo = '$this->CustomerDeliveryNo' ORDER BY ItemNo, SubItemNo " ;
		for ( $actCustomerDeliveryItem->_firstFromDb( $cond) ;
				$actCustomerDeliveryItem->isValid() ;
				$actCustomerDeliveryItem->_nextFromDb()) {
			try {
				$actCustomerDeliveryItem->_buche( $_sign) ;
			} catch( Exception $e) {
				throw $e ;
			}
		}
	}
	function	buche( $_key="", $_id=-1, $_val="") {
		error_log( "CustomerDelivery.php::CustomerDelivery::buche( '$_key', $_id, '$_val'): begin") ;
		$ret	=	"" ;
		$this->_buche( 1) ;
		if ( $_key != "") {
			$ret	=	$this->getXMLComplete() ;
		}
		error_log( "CustomerDelivery.php::CustomerDelivery::buche(...): end") ;
		return $ret ;
	}
	function	unbuche( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$this->_buche( -1) ;
		if ( $_key != "") {
			$ret	=	$this->getXMLComplete() ;
		}
		return $ret ;
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	_clearAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDb::query( "UPDATE CustomerDeliveryItem SET QuantityGebucht = 0 ") ;
	}
	static	function	_bucheAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		error_log( "CustomerDelivery.php::CustomerDelivery::_bucheAll(): begin") ;
		$ret	=	"" ;
		$actCustomerDelivery	=	new CustomerDelivery() ;
		$crit	=	"CustomerDeliveryNo LIKE '%%' AND Status <= 90 " ;		// only the closed ones
		$crit	.=	"AND Datum > '$_startDate' " ;
		$crit	.=	"AND Datum <= '$_endDate' " ;
		for ( $actCustomerDelivery->_firstFromDb( $crit) ;
				$actCustomerDelivery->_valid ;
				$actCustomerDelivery->_nextFromDb()) {
			$actCustomerDelivery->buche() ;
		}
		error_log( "CustomerDelivery.php::CustomerDelivery::_bucheAll(): end") ;
		return $ret ;
	}
	/**
	 * Books the entire order in Stock
	 *
	 * @return void
	 */
	static	function	bucheAll( $_key, $_id, $_val) {
		$ret	=	"" ;
		$actCustomerDelivery	=	new CustomerDelivery() ;
		for ( $actCustomerDelivery->_firstFromDb( "CustomerDeliveryNo like '%' ") ;
				$actCustomerDelivery->_valid ;
				$actCustomerDelivery->_nextFromDb()) {
			$actCustomerDelivery->buche() ;
		}
		if ( $_key != "") {
			$this->setCustomerDeliveryNo( $_key) ;
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
	static	function	unbucheAll( $_key, $_id, $_val) {
		$ret	=	"" ;
		$actCustomerDelivery	=	new CustomerDelivery() ;
		for ( $actCustomerDelivery->_firstFromDb( "CustomerDeliveryNo like '%' ") ;
				$actCustomerDelivery->_valid ;
				$actCustomerDelivery->_nextFromDb()) {
			$actCustomerDelivery->unbuche() ;
		}
		return $ret ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	sendByEMail( $_key, $_id, $_val) {
		try {
			$myCarr	=	new Carr( $this->Carrier) ;
			$this->upd( '', '', '') ;
			$newMail	=	new mimeMail( $this->eMail->Sales,
								$_POST['_IeMail'],
								$this->eMail->Sales,
								FTr::tr( "Your order No. #1, dated #2, our shipment #3", array( "%s:".$this->CustomerOrderNo, "%s:".convDate( $this->Datum), "%s:".$this->CustomerDeliveryNo)),
								"Bcc: ".$this->eMail->Archive."\n") ;
			$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
			$myDisclaimerText	=	new SysTexte( "DisclaimerText") ;
			$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer", "#Carrier") ;
			$myReplTableOut	=	array( $this->myKundeKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext, $myCarr->FullName) ;
			$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;

			$myText	=	new mimeData( "multipart/alternative") ;
			$myText->addData( "text/plain", iconv( 'UTF-8', 'iso-8859-1',xmlToPlain( "<div>".$myMail."</div>")."\n\n")) ;
			$myText->addData( "text/html", "<HTML><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><HEAD></HEAD><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n".$myMail."</body></HTML>", "", true) ;

			$myBody	=	new mimeData( "multipart/mixed") ;
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			$myBody->addData( "application/pdf", $this->path->Archive."CustomerDelivery/".$this->CustomerDeliveryNo.".pdf", $this->CustomerDeliveryNo.".pdf", true) ;

			$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
			$mailSendResult	=	$newMail->send() ;

			$this->DocVerschVia	=	Opt::DOCVIAEMAIL ;
			$this->updateColInDb( "DocVerschVia") ;
			$this->_addRem( FTr::tr( "send by E-Mail to: #1 (Bcc: #2)", array( "%s:".$_POST['_IeMail'],"%s:".$this->eMail->Archive))) ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	/**
	 *
	 */
	function	getAnschAsHTML() {
		$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
		$this->Disclaimer	=	$myDisclaimerHTML->Volltext ;
		$myCarr	=	new Carr( $this->Carrier) ;
		$this->Carrier	=	$myCarr->FullName ;
		/**
		 *
		 */
		$myReplTableIn	=	array() ;
		$myReplTableOut	=	array() ;
		$in	=	$this->Anschreiben ;
		$len	=	strlen( $in) ;
		$inVar	=	false ;
		$out	=	"" ;
		for ( $i=0 ; $i < $len ; $i++) {
			$c	=	$in[$i] ;
			if ( ! $inVar ) {
				if ( $c == "#") {
					$inVar	=	true ;
					$varName	=	"" ;
				} else {
					$out	.=	$c ;
				}
			} else {
				if ( $c == "#") {
					$out	.=	"#" ;
					$inVar	=	false ;
				} else if ( ctype_alnum( $c) || $c == ".") {
					$varName	.=	$c ;
				} else {
					error_log( $varName) ;
					$v	=	explode( ".", $varName) ;
					$o	=	$this ;
					foreach ( $v as $name) {
						if ( isset ( $o->$name)) {
							$o	=	$o->$name ;
						} else {
							$o	=	"undefined" ;
						}
					}
					$out	.=	$o ;
					$varName	=	"" ;
					$inVar	=	false ;
					$out	.=	$c ;
				}
			}
		}
		/**
		 *
		 */
//		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer", "#Carrier") ;
//		$myReplTableOut	=	array( $this->myKundeKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext, $myCarr->FullName) ;
//		$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;
		$myMail	=	$out ;
		return $myMail ;
	}
	function	getXMLDocInfo( $_key="", $_id=-1, $_val="") {
		$ret	=	"<Document>" ;
		$filename	=	$this->path->Archive . "CustomerDelivery/" . $this->CustomerDeliveryNo . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "CustomerDelivery/" . $this->CustomerDeliveryNo . ".pdf" ;
		}
		$ret	.=	"</Document>" ;
		return $ret ;
	}
	/**
	 * methods:	option related
	 */
	function	getRStatus() {		return  self::$rStatus ;		}
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
		$myCustomerDeliveryDoc	=	new CustomerDeliveryDoc( $_key, true) ;
		$myName	=	$myCustomerDeliveryDoc->createPDF( $_key, $_id, $_val) ;
		$this->pdfName	=	$myCustomerDeliveryDoc->pdfName ;
		$this->fullPDFName	=	$myCustomerDeliveryDoc->fullPDFName ;
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
		$myCustomerDeliveryDoc	=	new CustomerDeliveryDoc( $_key, false) ;
		$reply->pdfName	=	$myCustomerDeliveryDoc->pdfName ;
		$reply->fullPDFName	=	$myCustomerDeliveryDoc->fullPDFName ;
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
		$myCustomerDeliveryDoc	=	new CustomerDeliveryDoc( $_key) ;
		$this->pdfName	=	$myCustomerDeliveryDoc->pdfName ;
		$this->fullPDFName	=	$myCustomerDeliveryDoc->fullPDFName ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}

}
?>
