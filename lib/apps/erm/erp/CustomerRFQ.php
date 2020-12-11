<?php

/**
 * CustomerRFQ.php - Custoemr 'Request for Quotation'
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Include Dateien
 */
/**
 * CustomerRFQ - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCustomerRFQ which should
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerRFQ
 */
class	CustomerRFQ	extends	AppObjectERM_CR	{

	private	$tmpCustomerRFQPos ;

	const	NEU		=	  0 ;
	const	UPDATE		=	 30 ;
	const	ONGOING		=	 50 ;
	const	REMINDED	=	 70 ;
	const	WAITINV		=	 80 ;
	const	CLOSED		=	 90 ;
	const	ONHOLD		=	980 ;
	const	CANCELLED	=	990 ;

	private	static	$rStatus	=	array (
						-1				=> "ALL",
						CustomerRFQ::NEU		=> "open",
						CustomerRFQ::ONGOING	=> "ongoing",
						CustomerRFQ::CLOSED	=> "abgeschlossen",
					) ;
	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (CustomerRFQNo), in which case it will automatically
	 * (try to) load the respective Customer Order via the base class from the Database
	 * If the order data was loaded the customer data, and customer contact data, will also be loaded
	 * from the database.
	 *
	 * @param string $_myCustomerRFQNo
	 * @return void
	 */
	function	__construct( $_myCustomerRFQNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerRFQNo')") ;
		FDbg::end() ;
		parent::__construct( "CustomerRFQ", "CustomerRFQNo") ;
		if ( strlen( $_myCustomerRFQNo) > 0) {
			$this->setCustomerRFQNo( $_myCustomerRFQNo) ;
		} else {
		}
		FDbg::end() ;
	}
	/**
	 * set the Order Number (CustomerRFQNo)
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
	function	setCustomerRFQNo( $_myCustomerRFQNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerRFQNo')") ;
		$this->CustomerRFQNo	=	$_myCustomerRFQNo ;
		if ( strlen( $_myCustomerRFQNo) > 0) {
			$this->reload() ;
		}
		FDbg::end() ;
	}
	/**
	 * Create a new Customer Quotation (CuQuot) from this Customer RFQ (CustomerRFQ)
	 * @param string $_key Number of the Customer RFQ (CustomerRFQ) which shall be turned into a Customer Quotation (CuQuot)
	 * @param int $_id unused
	 * @param mixed $_val unused
	 */
	function	newCustomerOffer( $_key="", $_id=-1, $_val="") {
		$newCuOffr	=	new CuOffr() ;
		$newCuOffr->newFromCustomerRFQ( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CuOffr</ObjectClass>\n<ObjectKey>$newCuOffr->CuOffrNo</ObjectKey>\n</Reference>\n" ;
		return $ret ;
	}
	/**
	 * Create a new Customer Order (CuOrdr) from this Customer RFQ (CustomerRFQ)
	 * @param string $_key Number of the Customer RFQ (CustomerRFQ) which shall be turned into a Customer Order (CuOrdr)
	 * @param int $_id unused
	 * @param mixed $_val unused
	 */
	function	newCustomerRFQ( $_key="", $_id=-1, $_val="") {
		$newCuOrdr	=	new CuOrdr() ;
		$newCuOrdr->newFromCustomerRFQ( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CuOrdr</ObjectClass>\n<ObjectKey>$newCuOrdr->CuOrdrNo</ObjectKey>\n</Reference>\n" ;
		return $ret ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TCustomerRFQ)
	 *
	 * @return void
	 */
	function	newFromCustomerCart( $_key, $_id, $_customerCartNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_customerCartNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CustomerCart", $_customerCartNo, "", "", "900000", "949999") ;		// create a new instance
//		$myCustomerRFQPos	=	new CustomerRFQItem() ;
//		$myCustomerRFQItem->CustomerRFQNo	=	$this->CustomerRFQNo
//		for ( $valid = $myCustomerRFQPos->firstFromDb( "CustomerRFQNo", "", null, "", "ItemNo, SubItemNo ") ;
//				$valid ;
//				$valid = $myCustomerRFQPos->nextFromDb()) {
//			$myCustomerRFQPos->updateInDb() ;
//		}
		$buffer	=	"" ;
		FDbg::end() ;
		return $buffer ;
	}
	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TCustomerRFQ)
	 *
	 * @return void
	 */
	function	newFromCustomerRFQ( $_key, $_id, $_myCustomerRFQNo) {
		FDbg::begin( 1, "CustomerRFQ.php", "CustomerRFQ", "newFromCustomerRFQ( $_key, $_id, '$_myCustomerRFQNo')") ;
		/**
		 * create the (provisionary) PCuComm and CuComm for each distinct supplier
		 */
		$this->_newFrom( "CustomerRFQ", $_myCustomerRFQNo) ;		// create a new instance
		$myCustomerRFQPos	=	new CustomerRFQItem( $this->CustomerRFQNo) ;
		for ( $valid = $myCustomerRFQPos->firstFromDb( "CustomerRFQNo", "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
				$valid ;
				$valid = $myCustomerRFQPos->nextFromDb()) {
//			$myCustomerRFQPos->updateInDb() ;
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @return CustomerRFQPos
	 */
	function	getFirstPos() {
		$this->tmpCustomerRFQPos	=	new CustomerRFQItem() ;
		$this->tmpCustomerRFQPos->CustomerRFQNo	=	$this->CustomerRFQNo ;
		$this->tmpCustomerRFQPos->firstFromDb() ;
		return $this->tmpCustomerRFQPos ;
	}
	/**
	 *
	 * @return CustomerRFQPos
	 */
	function	getNextPos() {
		$this->tmpCustomerRFQPos->nextFromDb() ;
		return $this->tmpCustomerRFQPos ;
	}
	/**
	 *
	 */
	function	updateHdlg() {
		FDbg::begin( 1, "CustomerRFQ.php", "CustomerRFQ", "_invalidate()") ;
		$myCustomerRFQItem	=	new CustomerRFQItem() ;
		$myCustomerRFQItem->removeFromDbWhere( [ "CustomerRFQNo = '$this->CustomerRFQNo' AND ArticleNo like 'HDLG%'"]) ;
		$this->_restate() ;
		if ( $this->ItemCount > 0) {
			if ( $this->TotalPrice >= 200.0 ) {

			} else if ( $this->TotalPrice >= 100.0 ) {
				$myArticleSalesPriceCache	=	new ArticleSalesPriceCache() ;
				$myArticleSalesPriceCache->fetchFromDbWhere( "ArticleNo = \"HDLGPSCHM\" ") ;
				if ( $myArticleSalesPriceCache->isValid()) {
					$this->_addPos( $myArticleSalesPriceCache->ArticleNo, $myArticleSalesPriceCache->Id, 1) ;
				}
			} else {
				$myArticleSalesPriceCache	=	new ArticleSalesPriceCache() ;
				$myArticleSalesPriceCache->fetchFromDbWhere( "ArticleNo = \"HDLGPSCHH\" ") ;
				if ( $myArticleSalesPriceCache->isValid()) {
					$this->_addPos( $myArticleSalesPriceCache->ArticleNo, $myArticleSalesPriceCache->Id, 1) ;
				}
			}
			$this->_restate() ;
		}
	}
	/**
	 *
	 */
	function	getRStatus() {		return self::$rStatus ;			}
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
			$myObj	=	new FDbObject( "CustomerRFQ", "CustomerRFQNo", "def", "v_CustomerRFQSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"CustomerRFQNo like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"CustomerRFQItem"	:
			$myObj	=	new FDbObject( "CustomerRFQItem", "Id", "def", "v_CustomerRFQItemList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( CustomerRFQNo = '" . $this->CustomerRFQNo . "') " ;
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
	function	getXMLDocInfo( $_key="", $_id=-1, $_val="") {
		$ret	=	"<Document>" ;
		$filename	=	$this->path->Archive . "CustomerRFQ/" . $this->CustomerRFQNo . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "CustomerRFQ/" . $this->CustomerRFQNo . ".pdf" ;
		}
		$ret	.=	"</Document>" ;
		return $ret ;
	}
	function	getDocListAsXML( $_key, $_id, $_val) {
		$fullPath	=	$this->path->Archive . "CustomerRFQ/" ;
		$myDir	=	opendir( $fullPath) ;
		$myFiles	=	array() ;
		while (($file = readdir( $myDir)) !== false) {
			if ( strncmp( $file, $this->CustomerRFQNo, 6) == 0) {
				$myFiles[]	=	$file ;
			}
		}
		closedir( $myDir);
		reset( $myFiles) ;
		asort( $myFiles) ;
		$ret	=	"<DocList>\n" ;
		$ret	.=	"<URLPath>".$this->url->Archive."</URLPath>\n" ;
		foreach ( $myFiles as $file) {
			$ret	.=	"<Doc>\n" ;
			if ( filetype( $fullPath . $file) == "file") {
				$ret	.=	"<Filename>$file</Filename>\n" ;
				$ret	.=	"<Filetype>" . myFiletype( $file) . "</Filetype>\n" ;
				$ret	.=	"<Filesize>" . filesize( $fullPath . $file) . "</Filesize>\n" ;
				$ret	.=	"<FileURL>" . $this->url->Archive . "CustomerRFQ/" . $file . "</FileURL>\n" ;
			}
			$ret	.=	"</Doc>\n" ;
		}
		$ret	.=	"</DocList>" ;
		return $ret ;
	}
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	/**
	 * This method sends an eMail, with the text named $_mailText coming from the 'Texte' Db-Table
	 * to the recipients
	 * @param string $_mailText	mand.: Name of the mail body in the 'Texte' Db-table
	 * @param string $_file	opt.: pdf-file in the Archive/CuOrdr path to attach
	 * @param string $_from	opt.: sending mail address
	 * @param string $_to	opt.: receiving mail address
	 * @param string $_cc	opt.: cc mail address
	 * @param string $_bcc	opt.: bcc mail address
	 */
	function	mail( $_mailText, $_file, $_from, $_to, $_cc="", $_bcc="") {
		/**
		 * prepare the eMail attachment
		 */
		$file	=	$this->path->Archive."CustomerRFQ/".$this->CustomerRFQNo.".pdf" ;
		$fileName	=	"CustomerRFQ".$this->CustomerRFQNo.".pdf" ;
		parent::mail(  $_mailText, $file, $fileName, $_from, $_to, $_cc, $_bcc) ;
	}
	/**
	 * @param	string	$_key
	 * @param	int	$_id
	 * @param	mixed	$_val
	 */
	function	export( $_key="", $_id=0, $_val="") {
		/**
		 *
		 */
		$oFile	=	fopen( $this->path->Archive."XML/down/CustomerRFQ".$this->CustomerRFQNo.".xml", "w+") ;
		fwrite( $oFile, "<CustomerRFQPaket>\n") ;
		$buffer	=	$this->getXMLF() ;
		fwrite( $oFile, $buffer) ;
		$myCustomerRFQItem	=	new CustomerRFQItem() ;
		$myCustomerRFQItem->CustomerRFQNo	=	$this->CustomerRFQNo ;
		for ( $myCustomerRFQItem->_firstFromDb( "CustomerRFQNo='$this->CustomerRFQNo' ORDER BY ItemNo ") ;
					$myCustomerRFQItem->_valid == 1 ;
					$myCustomerRFQItem->_nextFromDb()) {
			$buffer	=	$myCustomerRFQItem->getXMLF() ;
			fwrite( $oFile, $buffer) ;
		}
		fwrite( $oFile, "</CustomerRFQPaket>\n") ;
		fclose( $oFile) ;
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	buche( $_key="", $_id=-1, $_val="") {		}
	function	unbuche( $_key="", $_id=-1, $_val="") {		}
}
?>
