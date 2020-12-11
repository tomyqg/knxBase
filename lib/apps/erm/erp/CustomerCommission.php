<?php
/**
 * CustomerCommission.php - Declaration of the base class for a Customer Commission (CustomerCommission)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerCommission - Customer Commission
 * @package Application
 * @subpackage CustomerCommission
 */
class	CustomerCommission	extends	AppObjectERM_CR	{
	private	$tmpCustomerCommissionPos ;

	const	NEU			=	  0 ;
	const	ONGOING		=	 50 ;
	const	CLOSED		=	 90 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						-1				=> "ALL",
						CustomerCommission::NEU		=> "Neu",
						CustomerCommission::ONGOING	=> "Ongoing",
						CustomerCommission::CLOSED	=> "Beendet",
						CustomerCommission::CANCELLED	=> "Storniert"
					) ;
	/**
	 *
	 */
	function	__construct( $_myCustomerCommissionNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerCommissionNo')") ;
		parent::__construct( "CustomerCommission", "CustomerCommissionNo") ;
		if ( strlen( $_myCustomerCommissionNo) > 0) {
			$this->setCustomerCommissionNo( $_myCustomerCommissionNo) ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerCommissionNo')", "no commission number specified!") ;
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setCustomerCommissionNo( $_myCustomerCommissionNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerCommissionNo')") ;
		$this->CustomerCommissionNo	=	$_myCustomerCommissionNo ;
		if ( strlen( $_myCustomerCommissionNo) > 0) {
			$this->reload() ;
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	newCuDlvr( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$newCuDlvr	=	new CuDlvr() ;
		$newCuDlvr->newFromCustomerCommission( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>CuDlvr</ObjectClass>\n<ObjectKey>$newCuDlvr->CuDlvrNo</ObjectKey>\n</Reference>\n" ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * business logic methods
	 */
	/**
	 *
	 * Enter description here ...
	 * @param string $_key			unusd
	 * @param int $_id				unused
	 * @param string $_cuOrdrNo		nr. of the CuOrdr to generate this CustomerCommission for
	 * @param int $_direktVersand
	 * @throws Exception
	 * @return string	XML representation of this object
	 */
	function	newFromCuOrdr( $_key, $_id, $_cuOrdrNo, $_direktVersand=0) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_cuOrdrNo', <Reply>)") ;
		$whereClause	=	"WHERE CuOrdrNo = '$_cuOrdrNo' AND CuDlvrNo = '' " ;
		$openCustomerCommissionCnt	=	$this->existWhere( $whereClause) ;
		if (  $openCustomerCommissionCnt <= 0) {
			$this->_newFrom( "CuOrdr", $_cuOrdrNo, "", "AND Quantity > QuantityBereitsDelivered ") ;		// create a new instance
			$myCuOrdr	=	new CuOrdr( $_cuOrdrNo) ;
			if ( $myCuOrdr->isValid()) {
				if ( $myCuOrdr->LiefCustomerNo != "") {
					$this->CustomerNo	=	$myCuOrdr->LiefCustomerNo ;
					$this->CustomerContactNo	=	$myCuOrdr->LiefCustomerContactNo ;
					$this->updateInDb() ;
				}
			}
			/**
			 * update the individual items
			 */
	//		$myCuOrdrItem	=	new CuOrdrItem( $_cuOrdrNo) ;
			$myCustomerCommissionItem	=	new CustomerCommissionItem() ;
			$myCustomerCommissionItem->setIterCond( "CustomerCommissionNo = $this->CustomerCommissionNo ") ;
			foreach ( $myCustomerCommissionItem as $key => $item) {
				$myCustomerCommissionItem->QuantityDelivered	=	$myCustomerCommissionItem->Quantity - $myCustomerCommissionItem->QuantityBereitsDelivered ;
				$myCustomerCommissionItem->updateInDb() ;
			}
			/**
			 * create the PDF document and print if (if autoprint is on by config.ini)
			 */
			try {
				$myDoc	=	new CustomerCommissionDoc( $this->CustomerCommissionNo) ;
				$myDoc->_createPDF() ;	// create the pdf document
				$myDoc->printPDF() ;	// and print (if autoprint enable in config.ini)
			} catch ( Exception $e) {
				throw $e ;
			}
		} else if ( $openCustomerCommissionCnt == 1) {
			$this->fetchFromDbWhere( $whereClause) ;
		} else {
			$e	=	new Exception( "CustomerCommission.php::newFromCuOrdr(): multiple open CustomerCommission exist for this CuOrdr!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 */
	function	buche( $_key="", $_id=-1, $_val="") {
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
	 */
	function	unbuche( $_key="", $_id=-1, $_val="") {
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
	 *
	 */
	function	bucheAll( $_key, $_id, $_val) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$ret	=	"" ;
		$actCustomerCommission	=	new CustomerCommission() ;
		for ( $actCustomerCommission->_firstFromDb( "CustomerCommissionNo like '%' ") ;
				$actCustomerCommission->_valid ;
				$actCustomerCommission->_nextFromDb()) {
			$actCustomerCommission->buche() ;
		}
		if ( $_key != "") {
			$this->setCustomerCommissionNo( $_key) ;
			$ret	=	$this->getTablePostenAsXML() ;
		} else {
			$ret	=	"" ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 */
	static	function	unbucheAll( $_key, $_id, $_val) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$ret	=	"" ;
		$actCustomerCommission	=	new CustomerCommission() ;
		for ( $actCustomerCommission->_firstFromDb( "CustomerCommissionNo like '%' ") ;
				$actCustomerCommission->_valid ;
				$actCustomerCommission->_nextFromDb()) {
			$actCustomerCommission->unbuche() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * @return string fullNameOfArchivedFile
	 */
	function	archivePDF() {
		require_once( "CustomerCommissionDoc.php" );
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$pdfTargetName	=	$this->path->Archive . "Packzettel/" . $this->CustomerCommissionNo . ".pdf" ;
		$actCustomerCommissionDoc	=	new CustomerCommissionDoc( $this->CustomerCommissionNo) ;
		$actCustomerCommissionDoc->getPDF( $pdfTargetName) ;
		FDbg::end() ;
		return $pdfTargetName ;
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
			$myObj	=	new FDbObject( "CustomerCommission", "CustomerCommissionNo", "def", "v_CustomerCommissionSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"CustomerCommissionNo like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"CustomerCommissionItem"	:
			$myObj	=	new FDbObject( "CustomerCommissionItem", "Id", "def", "v_CustomerCommissionItemList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( CustomerCommissionNo = '" . $this->CustomerCommissionNo . "') " ;
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
	 * object retrieval methods
	 */
	function	getXMLDocInfo( $_key="", $_id=-1, $_val="") {
		$ret	=	"<Document>" ;
		$filename	=	$this->path->Archive . "CustomerCommission/" . $this->CustomerCommissionNo . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "CustomerCommission/" . $this->CustomerCommissionNo . ".pdf" ;
		}
		$ret	.=	"</Document>" ;
		FDbg::end() ;
		return $ret ;
	}
	/**
	 * methods:	option related
	 */
	function	getRStatus() {		return self::$rStatus ;			}
	/**
	 * internal methods
	 */
	static	function	_clearAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		FDb::query( "UPDATE CustomerCommissionItem SET QuantityGebucht = 0 ") ;
		FDbg::end() ;
	}
	static	function	_markAll( $_key="", $_id=-1, $_val="", $_startDate="2000-01-01", $_endDate="2099-12-31") {
		$ret	=	"" ;
		$actCustomerCommission	=	new CustomerCommission() ;
		$crit	=	"CustomerCommissionNo LIKE '%%' AND Status < 90 " .		// only the open ones
					"AND Datum > '$_startDate' " .					// in the given date range
					"AND Datum <= '$_endDate' " ;
		for ( $actCustomerCommission->_firstFromDb( $crit) ;
				$actCustomerCommission->_valid ;
				$actCustomerCommission->_nextFromDb()) {
			$actCustomerCommission->buche() ;
		}
		FDbg::end() ;
		return $ret ;
	}
	function	_buche( $_sign) {
		$actCustomerCommissionItem	=	new CustomerCommissionItem() ;
		$cond	=	"CustomerCommissionNo = '$this->CustomerCommissionNo' ORDER BY ItemNo, SubItemNo " ;
		for ( $actCustomerCommissionItem->_firstFromDb( $cond) ;
				$actCustomerCommissionItem->isValid() ;
				$actCustomerCommissionItem->_nextFromDb()) {
			try {
				$actCustomerCommissionItem->buche( $_sign) ;
			} catch( Exception $e) {
				throw $e ;
			}
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	createPDF( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "CustomerCommission.php", "CustomerCommission", "createPDF( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myCustomerInvoiceDoc	=	new CustomerInvoiceDoc( $_key) ;
		$myName	=	$myCustomerInvoiceDoc->createPDF( $_key, $_id, $_val) ;
		$this->pdfName	=	$myCustomerInvoiceDoc->pdfName ;
		$this->fullPDFName	=	$myCustomerInvoiceDoc->fullPDFName ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 * returns the name of the PDF file which has been created
	 */
	function	getPDF( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "CustomerCommission.php", "CustomerCommission", "getPDF( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myCustomerInvoiceDoc	=	new CustomerInvoiceDoc( $_key) ;
		$this->pdfName	=	$myCustomerInvoiceDoc->pdfName ;
		$this->fullPDFName	=	$myCustomerInvoiceDoc->fullPDFName ;
		FDbg::end() ;
		return $this->pdfName ;
	}
	/**
	 *
	 */
	function	printPDF( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "CustomerCommission.php", "CustomerCommission", "printPDF( '$_key', $_id, '$_val')") ;
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
