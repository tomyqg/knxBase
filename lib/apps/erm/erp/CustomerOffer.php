<?php
/**
 * CustomerOffer.php Anwendungsklasses f�r Angebote
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerOffer - Anwendungsklasse f�r: Angebot
 *
 * This class acts as an interface towards the automatically generated BCustomerOffer which should
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerOffer
 */
class	CustomerOffer	extends	AppObjectERM_CR	{

	private	$tmpCustomerOfferPos ;

	public	$dep	=	array(
						"CustomerOfferItem"	=>	"CustomerOfferNo"
					) ;

	const	NEU			=	  0 ;
	const	UPDATE		=	 30 ;
	const	ONGOING		=	 50 ;
	const	REMINDED	=	 70 ;
	const	WAITINV		=	 80 ;
	const	CLOSED		=	 90 ;
	const	ONHOLD		=	980 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						CustomerOffer::NEU		=> "Neu",
						CustomerOffer::UPDATE	=> "In Bearbeitung (auch: Revision)",
						CustomerOffer::ONGOING	=> "Verschickt",
						CustomerOffer::CLOSED	=> "Abgelaufen/Beendet",
						CustomerOffer::CANCELLED => "Storniert"
					) ;

	const	DOCAA	=	"AA" ;		// order confirmation
	const	DOCMI	=	"MI" ;		// order confirmation
	private	static	$rDocType	=	array (
						CustomerOffer::DOCAA	=> "Angebotsanfrage",
						CustomerOffer::DOCMI	=> "Sonstiges"
					) ;
	/**
	 * Konstructor f�r Klasse: CustomerOffer (Angebot)
	 *
	 * Der Konstruktor kann mit mit oder ohne eine Angebotsnummer aufgerufen werden.
	 * Wenn der Konstruktor mit einer Angebotsnummer aufgerufen wird, wird versucht
	 *
	 * @param string $_myCustomerOfferNo
	 * @return void
	 */
	function	__construct( $_myCustomerOfferNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerOfferNo')") ;
		parent::__construct( "CustomerOffer", "CustomerOfferNo") ;
		if ( strlen( $_myCustomerOfferNo) > 0) {
			$this->setCustomerOfferNo( $_myCustomerOfferNo) ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerOfferNo')", "no offer number specified!") ;
		}
		FDbg::end() ;
	}

	/**
	 * set the Order Number (CustomerOfferNo)
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
	function	setCustomerOfferNo( $_myCustomerOfferNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerOfferNo')") ;
		$this->CustomerOfferNo	=	$_myCustomerOfferNo ;
		if ( strlen( $_myCustomerOfferNo) > 0) {
			$this->reload() ;
		}
		FDbg::end() ;
	}
	/**
	 * newFromMZ
	 *
	 * Legt ein neues Angebot f�r einen existierenden CuCart, spezifiziert durch die CuCart Nr., an.
	 *
	 * @return void
	 */
	function	newFromMZ( $_myMZNr) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myMZNr')") ;
		$query	=	sprintf( "CustomerOffer_newFromMZ( @status, '%s', @newCustomerOfferNo) ; ", $_myMZNr) ;
		try {
			$row	=	FDb::callProc( $query, '@newCustomerOfferNo') ;
			$this->setCustomerOfferNo( $row['@newCustomerOfferNo']) ;
			$this->addRem( "Erzeugt aus CuCart Nr. " . $_myMZNr . " ") ;
		} catch( Exception $e) {
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TCustomerOffer)
	 *
	 * @param string $_key unused
	 * @param int $_id
	 * @param mixed $_cuRFQNo	numer of the CuRFQ to create this CuQuot from
	 * @return void
	 */
	function	newFromCuRFQ( $_key, $_id, $_cuRFQNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_cuRFQNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CuRFQ", $_cuRFQNo) ;		// create a new instance
		/**
		 * loop through all items of this offer and perform offer specific actions
		 */
		$actItem	=	new CustomerOfferItem( $this->CustomerOfferNo) ;
		$actItem->setIterCond( "CustomerOfferNo = '".$this->CustomerOfferNo."' ") ;
		$actItem->setIterOrder( "ORDER BY ItemNo, SubItemNo ") ;
		foreach ( $actItem as $key => $val) {

		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TCustomerOffer)
	 *
	 * @param string $_key unused
	 * @param int $_id
	 * @param mixed $_cuRFQNo	numer of the CuRFQ to create this CuQuot from
	 * @return void
	 */
	function	newFromCustomerOffer( $_key, $_id, $_cuOffrNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_cuOffrNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CustomerOffer", $_cuOffrNo) ;		// create a new instance
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	newCustomerOffer( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$newCustomerOffer	=	new CustomerOffer() ;
		$newCustomerOffer->newFromCustomerOffer( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CustomerOffer</ObjectClass>\n<ObjectKey>$newCustomerOffer->CustomerOfferNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	newCustomerOrder( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$newCustomerOrder	=	new CuOrder() ;
		$newCustomerOrder->newFromCustomerOrder( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CustomerOrder</ObjectClass>\n<ObjectKey>$newCustomerOrder->CustomerOrderNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 * @param unknown_type $_myCustomerOfferNo
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	copy( $_myCustomerOfferNo, $_id, $_val) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CustomerOffer", $_myCustomerOfferNo) ;		// create a new instance
		$myCustomerOfferPos	=	new CustomerOfferItem( $this->CustomerOfferNo) ;
		for ( $valid = $myCustomerOfferPos->firstFromDb( "CustomerOfferNo", "", null, "", "ORDER BY PosNr, SubPosNr ") ;
				$valid ;
				$valid = $myCustomerOfferPos->nextFromDb()) {
//			$myCustomerOfferPos->updateInDb() ;
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 * updAddText
	 * @return CustomerOfferPos
	 */
	function	updAddText( $_id, $_text) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_id, '$_text')") ;
		try {
			$this->tmpCustomerOfferPos	=	new CustomerOfferItem() ;
			$this->tmpCustomerOfferPos->Id	=	$_id ;
			$this->tmpCustomerOfferPos->fetchFromDbById() ;
			if ( $this->tmpCustomerOfferPos->_valid) {
				FDbg::dumpL( 0x01000000, "CustomerOffer::updAddText: refers to PosNr=%d", $this->tmpCustomerOfferPos->PosNr) ;
				$this->tmpCustomerOfferPos->AddText	=	$_text ;
				$this->tmpCustomerOfferPos->updateInDb() ;
			} else {
				throw new Exception( 'CustomerOffer::updAddText: CustomerOfferItem[Id='.$_id.'] is INVALID !') ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $this->tmpCustomerOfferPos ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	sendByEMail( $_key="", $_id=-1, $_val="", $reply=null) {
		try {
			$this->upd( '', '', '') ;
			$newMail	=	new mimeMail( $this->eMail->Sales,
								$_POST['_IMail'],
								$this->eMail->Sales,
								FTr::tr( "Your request for quotation, our Quotation No. #1, dated #2", array( "%s:".$this->CustomerOfferNo, "%s:".convDate( $this->Datum))),
/*								"Cc:".$_POST['_IMail']."\r\n".*/
								"Bcc: ".$this->eMail->Archive.", ".$_POST['_IMailBCC']."\r\n") ;
			$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
			$myDisclaimerText	=	new SysTexte( "DisclaimerText") ;
			$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer", "#CustomerOrderDatum") ;
			$myReplTableOut	=	array( $this->myKundeKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext, $this->Datum) ;
			$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;

			$myText	=	new mimeData( "multipart/alternative") ;
			$myText->addData( "text/plain", xmlToPlain( "<div>".$myMail."</div>")) ;
			$myText->addData( "text/html", "<html><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">"
											. "<head></head><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n"
											. $myMail."</body></HTML>", "", true) ;

			$myBody	=	new mimeData( "multipart/mixed") ;
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			$myBody->addData( "application/pdf", $this->path->Archive."CustomerOffer/".$this->CustomerOfferNo.".pdf", $this->CustomerOfferNo.".pdf", true) ;

			$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
			$mailSendResult	=	$newMail->send() ;

			$this->DocVerschVia	=	Opt::DOCVIAEMAIL ;
			$this->updateColInDb( "DocVerschVia") ;
			$this->Status	=	CustomerOffer::ONGOING ;		// ueber "Normal"-FAX
			$this->_addRem( FTr::tr( "send by E-Mail to: #1 (Bcc: #2)", array( "%s:".$_POST['_IMail'],"%s:".$this->eMail->Archive))) ;
		} catch ( Exeption $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 */
	function	getAnschAsHTML( $_key="", $_id=-1, $_val="", $reply=null) {
		$myDisclaimerHTML	=	new SysTexte( "DisclaimerHTML") ;
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Signature", "#Disclaimer") ;
		$myReplTableOut	=	array( $this->myKundeKontakt->getAnrede(), $this->Datum, $this->eMail->Greeting, $myDisclaimerHTML->Volltext) ;
		$myMail	=	str_replace( $myReplTableIn, $myReplTableOut, $this->Anschreiben) ;
		return $myMail ;
	}
	/**
	 * Verschicken per FAX
	 *
	 * @return [Artikel]
	 */
	function	sendByFAX( $_key="", $_id=-1, $_val="", $reply=null) {
		require_once( "Fax.php" );
		$myFaxNr	=	$_POST['_IFAX'] ;
		sendFax( $myFaxNr,
					$this->path->Archive."CustomerOffer/".$this->CustomerOfferNo.".pdf", "", "", "miskhwe",
					3) ;
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	CustomerOffer::ONGOING ;		// ueber "Normal"-FAX
		$this->_addRem( "verschickt per FAX an " . $myFaxNr, $this->sysUser->UserId) ;
	}
	/**
	 *
	 */
	function	getRStatus() {		return self::$rStatus ;			}
	function	getRDocType() {		return self::$rDocType ;		}
	function	getXMLDocInfo() {
		$ret	=	"<Document><![CDATA[" ;
		$filename	=	$this->path->Archive . "CustomerOffer/" . $this->CustomerOfferNo . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "/CustomerOffer/" . $this->CustomerOfferNo . ".pdf" ;
		}
		$ret	.=	"]]></Document>" ;
		return $ret ;
	}
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
			$myObj	=	new FDbObject( "CustomerOffer", "CustomerOfferNo", "def", "v_CustomerOfferSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"CustomerOfferNo like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"CustomerOfferItem"	:
			$myObj	=	new FDbObject( "CustomerOfferItem", "Id", "def", "v_CustomerOfferItemList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( CustomerOfferNo = '" . $this->CustomerOfferNo . "') " ;
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
	function	getCSV( $_key="", $_id=-1, $_val="", $reply=null) {
		$buf	=	"" ;
		$quote	=	"\"" ;
		$sep	=	";" ;
		$actItem	=	new CustomerOfferItem() ;
		$actItem->addCol( "ArtikelBez1", "varchar") ;
		$actItem->addCol( "ArtikelBez2", "varchar") ;
		$actItem->setIterCond( "CustomerOfferNo = '" . $this->CustomerOfferNo."' ") ;
		$actItem->setIterJoin( "LEFT JOIN Artikel AS A ON A.ArtikelNr = C.ArtikelNr ", "A.ArtikelBez1, A.ArtikelBez2 ") ;
		$sumPrice	=	0.0 ;
		$sumRefPrice	=	0.0 ;
		$discount	=	0.0 ;
		foreach ( $actItem as $key => $val) {
			$buf	.=	"$quote" . $actItem->PosNr . "$quote"
					.	"$sep$quote" . $actItem->SubPosNr . "$quote"
					.	"$sep$quote" . $actItem->ArtikelNr . "$quote"
					.	"$sep$quote" . $actItem->ArtikelBez1 . "$quote"
					.	"$sep$quote" . $actItem->ArtikelBez2 . "$quote"
					.	"$sep$quote" . $actItem->Menge . "$quote"
					.	"$sep$quote" . str_replace( ".", ",", sprintf( "%.2f", $actItem->Preis)) . "$quote"
					.	"$sep$quote" . str_replace( ".", ",", sprintf( "%.2f", $actItem->Preis * ( 100.0 - $this->Rabatt) / 100.0)) . "$quote"
					.	"$sep$quote" . str_replace( ".", ",", sprintf( "%.2f", $actItem->Menge * $actItem->Preis * ( 100.0 - $this->Rabatt) / 100.0)) . "$quote"
					.	"\n"
					;
				$sumPrice	+=	$actItem->Preis * ( 100.0 - $this->Rabatt) / 100.0 ;
				$sumRefPrice	+=	$actItem->RefPreis ;
		}
		$buf	.=	"$quote" . "" . "$quote"
				.	"$sep$quote" . "" . "$quote"
				.	"$sep$quote" . "" . "$quote"
				.	"$sep$quote" . "Summen" . "$quote"
				.	"$sep$quote" . "" . "$quote"
				.	"$sep$quote" . "" . "$quote"
				.	"$sep$quote" . str_replace( ".", ",", sprintf( "%.2f", $actItem->Preis)) . "$quote"
				.	"$sep$quote" . str_replace( ".", ",", sprintf( "%.2f", $actItem->Preis * ( 100.0 - $this->Rabatt) / 100.0)) . "$quote"
				.	"$sep$quote" . str_replace( ".", ",", sprintf( "%.2f", $sumPrice)) . "$quote"
				.	"\n"
				;
		error_log( $buf) ;
		return $buf ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	buche( $_key="", $_id=-1, $_val="", $reply=null) {		}
	function	unbuche( $_key="", $_id=-1, $_val="", $reply=null) {		}
}
?>
