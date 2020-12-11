<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * AccountingPeriod - Base Class
 *
 * @package Application
 * @subpackage AccountingPeriod
 */
class	AccountingPeriod	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myMaterialNo="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myMaterialNo')") ;
		parent::__construct( "AccountingPeriod", "AccountingPeriodId") ;
		parent::defComplexKey( array( "PeriodStart", "PeriodEnd")) ;
		if ( strlen( $_myMaterialNo) > 0) {
			try {
				$this->setMaterialNo( $_myMaterialNo) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
		FDBg::end() ;
	}
	/**
	 *
	 */
	function	setMaterialNo( $_myMaterialNo) {
		$this->MaterialNo	=	$_myMaterialNo ;
		$this->reload() ;
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
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->MaterialNo	=	$_POST['MaterialNo'] ;
			$this->storeInDb() ;
		} else {
			$e	=	new Exception( "AccountingPeriod.php::AccountingPeriod::add(): 'AccountingPeriod' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$this->_upd( $_key, $_id, $_val, $_replyl) ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "AccountingPeriod.php", "AccountingPeriod", "_upd()") ;
		parent::upd( $_key, $_id, $_val, $_replyl) ;
		$this->_addRem( FTr::tr( "AccountingPeriod updated")) ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		return $this->getXMLString() ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		case "AccountingPeriodItem"	:
			$myAccountingPeriodItem	=	new AccountingPeriodRent() ;
			$myAccountingPeriodItem->getFromPostL() ;
			$myAccountingPeriodItem->MaterialNo	=	$this->MaterialNo ;
			$myAccountingPeriodItem->storeInDb() ;
			break;
		default	:
			break ;
		}
		$this->getList( $_key, $_id, $objName, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
//			$reply->message	=	FTr::tr( "Data succesfully updated!") ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		try {
			switch( $objName) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				$reply->message	=	FTr::tr( "Data succesfully deleted!") ;
				break ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
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
		default	:
			$reply	=	parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
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
			$myObj	=	new FDbObject( "AccountingPeriod", "MaterialNo") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"( PeriodEnd like '%$sCrit%')" ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( [ "PeriodEnd DESC"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"AccountingPeriodItem"	:
			$myObj	=	new FDbObject( "AccountingPeriodItem", "Id", "def", "v_AccountingPeriodItem_1") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_materialNoCrit	=	$sCrit ;
			$filter1	=	"( AccountingPeriodId = '" . $this->AccountingPeriodId . "')" ;
			$filter2	=	"CustomerNo like '%$sCrit%'" ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( [ "ItemNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	importCSVAz( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "storage path := '$path'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "fn is set") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "$fn uploaded") ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "fn is * NOT * set") ;
			$idx	=	0 ;
			$file	=	"jelly.txt" ;
			foreach ( $_FILES as $idx => $data) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Filename['$idx']") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", print_r( $data, true)) ;
				$filename	=	$path . $file ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "$filename") ;
				if (move_uploaded_file( $data["tmp_name"], $filename)) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "file moved....................., will start import") ;
					error_log( $filename) ;
					$myFile	=	fopen( $filename, "r") ;
					$myCustomer	=	new Customer() ;
					$myInvoicedItem	=	new InvoicedItem() ;
					$myAccountingPeriod	=	new AccountingPeriod() ;
					$lastCustomerNo	=	"" ;
					$i	=	0 ;
					$itemsAdded	=	0 ;
					while ( ! feof( $myFile)) {
						$myRawLine	=	fgets( $myFile, 1024) ;
						$i++ ;
						if ( $i >= 5 && strlen( $myRawLine) > 10) {
							$myLine	=	str_replace( "\n", "", iconv("UTF-16", "UTF-8", $myRawLine)) ;
							$val	=	explode( "\t", $myLine) ;
							$myInvoicedItem->CustomerNo	=	$val[0] ;
							$myInvoicedItem->InvoiceDate	=	substr( $val[3], 6, 4) . "-" . substr( $val[3], 3, 2) . "-" . substr( $val[3], 0, 2) ;
							$myInvoicedItem->MaterialNo		=	$val[4] ;
							$myInvoicedItem->PeriodStart	=	substr( $val[11], 6, 4) . "-" . substr( $val[11], 3, 2) . "-" . substr( $val[11], 0, 2) ;
							$myInvoicedItem->PeriodEnd		=	substr( $val[12], 6, 4) . "-" . substr( $val[12], 3, 2) . "-" . substr( $val[12], 0, 2) ;
							$myInvoicedItem->setKey( array( $myInvoicedItem->CustomerNo, $myInvoicedItem->InvoiceDate, $myInvoicedItem->MaterialNo)) ;
							if ( ! $myInvoicedItem->isValid()) {
								$myInvoicedItem->CustomerName	=	$val[1] ;
								$myInvoicedItem->QtyRef			=	floatval( str_replace( ",", ".", $val[5])) ;
								$myInvoicedItem->QtyCurrent		=	floatval( str_replace( ",", ".", $val[6])) ;
								$myInvoicedItem->QtyDiff		=	floatval( str_replace( ",", ".", $val[7])) ;
								$myInvoicedItem->PeriodStart	=	substr( $val[11], 6, 4) . "-" . substr( $val[11], 3, 2) . "-" . substr( $val[11], 0, 2) ;
								$myInvoicedItem->PeriodEnd	=	substr( $val[12], 6, 4) . "-" . substr( $val[12], 3, 2) . "-" . substr( $val[12], 0, 2) ;
								$myInvoicedItem->storeInDb() ;
								$itemsAdded++ ;
								if ( $myInvoicedItem->CustomerNo != $lastCustomerNo) {
									$myCustomer->setKey( $myInvoicedItem->CustomerNo) ;
									if ( $myCustomer->isValid() == false) {
										$myCustomer->CustomerName1	=	$myInvoicedItem->CustomerName ;
										$myCustomer->storeInDb() ;
									}
									$lastCustomerNo	=	$myCustomer->CustomerNo ;
								}
							}
							$myAccountingPeriod->setKey( array( $myInvoicedItem->PeriodStart, $myInvoicedItem->PeriodEnd)) ;
							if ( ! $myAccountingPeriod->isValid()) {
								$myAccountingPeriod->PeriodStart	=	$myInvoicedItem->PeriodStart ;
								$myAccountingPeriod->PeriodEnd		=	$myInvoicedItem->PeriodEnd ;
								$myAccountingPeriod->storeInDb() ;
							}
						}
					}
				} else {
					echo "Possible file upload attack!<br/>\n";
				}
			}
		}
		if ( $itemsAdded == 0) {
			$reply->message	=	FTr::tr( "No ContainerInvoiceItems were added to the database. "
											.	"This most likely means, that the file you just uploaded has been loaded earlier already.") ;
		} else {
			$reply->message	=	FTr::tr( "$itemsAdded ContainerInvoiceItems were added to the database.") ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	importCSVB( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "storage path := '$path'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "fn is set") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "$fn uploaded") ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "fn is * NOT * set") ;
			$idx	=	0 ;
			$file	=	"jelly.txt" ;
			foreach ( $_FILES as $idx => $data) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Filename['$idx']") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", print_r( $data, true)) ;
				$filename	=	$path . $file ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "$filename") ;
				if (move_uploaded_file( $data["tmp_name"], $filename)) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "file moved....................., will start import") ;
					error_log( $filename) ;
					$myFile	=	fopen( $filename, "r") ;
					$myCustomer	=	new Customer() ;
					$myContainerMove=	new ContainerMove() ;
					$lastCustomerNo	=	"" ;
					$i	=	0 ;
					$itemsAdded	=	0 ;
					$linesRead	=	0 ;
					while ( ! feof( $myFile)) {
						$myRawLine	=	fgets( $myFile, 1024) ;
						$i++ ;
						$linesRead++ ;
						if ( $i >= 2 && strlen( $myRawLine) > 10) {
							$myLine	=	str_replace( "\n", "", iconv("UTF-16", "UTF-8", $myRawLine)) ;
							$val	=	explode( "\t", $myLine) ;
							$myContainerMove->CustomerNo	=	$val[0] ;
							$myContainerMove->MaterialNo	=	$val[2] ;
							$myContainerMove->Qty			=	intval( $val[4]) ;
							$myContainerMove->DeliveryNo	=	$val[5] ;
							$myContainerMove->setKey( array( $myContainerMove->CustomerNo, $myContainerMove->MaterialNo, $myContainerMove->DeliveryNo, $myContainerMove->Qty)) ;
							if ( ! $myContainerMove->isValid()) {
								$myContainerMove->CustomerName	=	$val[1] ;
								$myContainerMove->Date			=	substr( $val[3], 6, 4) . "-" . substr( $val[3], 3, 2) . "-" . substr( $val[3], 0, 2) ;
								$myContainerMove->storeInDb() ;
								$itemsAdded++ ;
								if ( $myContainerMove->CustomerNo != $lastCustomerNo) {
									$myCustomer->setKey( $myContainerMove->CustomerNo) ;
									if ( $myCustomer->isValid() == false) {
										$myCustomer->CustomerName1	=	$myContainerMove->CustomerName ;
										$myCustomer->storeInDb() ;
									}
									$lastCustomerNo	=	$myCustomer->CustomerNo ;
								}
							} else {
								error_log( "line $i seems to be in database already") ;
							}
						} else {
							error_log( "line $i skipped due to index or length requirement") ;
						}
					}
				} else {
					echo "Possible file upload attack!<br/>\n";
				}
			}
		}
		if ( $itemsAdded == 0) {
			$reply->message	=	FTr::tr( "No ContainerInvoiceItems were added to the database. "
											.	"This most likely means, that the file you just uploaded has been loaded earlier already."
											.	"A total of $linesRead was read.") ;
		} else {
			$reply->message	=	FTr::tr( "$itemsAdded ContainerInvoiceItems were added to the database."
											.	"A total of $linesRead was read.") ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	createAccountingPeriodItems( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$myInvoicedItem	=	new InvoicedItem() ;
		$myInvoicedItem->setIterCond( "PeriodStart = \"".$this->PeriodStart."\" AND PeriodEnd = \"".$this->PeriodEnd."\"") ;
		$myInvoicedItem->setIterOrder( "CustomerNo ASC, MaterialNo ASC") ;
		$myAccountingPeriodItem	=	new AccountingPeriodItem() ;
		$myAccountingPeriodItem->AccountingPeriodId	=	$this->AccountingPeriodId ;
		$myAccountingPeriodItem->ItemNo	=	0 ;
		$myContainerMove	=	new ContainerMove() ;
				error_log( "Period Start := ".$this->PeriodStart) ;
				error_log( "Period End   := ".$this->PeriodEnd) ;
		foreach ( $myInvoicedItem as $ndx => $invoicedItem) {
			error_log( "InvoicedItem->CustomerNo := ".$invoicedItem->CustomerNo) ;
			$myAccountingPeriodItem->ItemNo	+=	10 ;
			$myAccountingPeriodItem->CustomerNo	=	$invoicedItem->CustomerNo ;
			$myAccountingPeriodItem->MaterialNo	=	$invoicedItem->MaterialNo ;
			$myAccountingPeriodItem->QtyBeginPeriod		=	$invoicedItem->QtyCurrent ;
			$myAccountingPeriodItem->QtyReturnedInPeriod	=	0 ;
			$myAccountingPeriodItem->Qty				=	$myAccountingPeriodItem->QtyBeginPeriod ;
			$myContainerMove->clearIterCond() ;
			$myContainerMove->setIterCond( "CustomerNo = \"".$invoicedItem->CustomerNo."\" AND Date >= \"".$this->PeriodStart."\" AND Date <= \"".$this->PeriodEnd."\" AND MaterialNo = \"".$myAccountingPeriodItem->MaterialNo."\"") ;
			foreach ( $myContainerMove as $containerMove) {
				error_log( "containerMove->MaterialNo := ".$containerMove->MaterialNo) ;
				$myAccountingPeriodItem->QtyReturnedInPeriod	+=	$containerMove->Qty ;
				$myAccountingPeriodItem->Qty	-=	$containerMove->Qty ;
			}
			$myAccountingPeriodItem->storeInDb() ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	createCSV( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		$filename	=	$path . "Abrechnung_" . $this->PeriodStart . "_" . $this->PeriodEnd . ".csv" ;
		$myFile	=	fopen( $filename, "w+") ;
		$myAccountingPeriodItem	=	new AccountingPeriodItem() ;
		$myAccountingPeriodItem->setIterCond( "AccountingPeriodId = \"".$this->AccountingPeriodId."\" AND Invoice = 1") ;
		$myAccountingPeriodItem->setIterOrder( "CustomerNo ASC, MaterialNo ASC") ;
		fwrite( $myFile, sprintf( "\"Kunden Nr.\"\t\"Kunde\"\t\"Material Nr.\"\tAnfangsbestand\tRetourniert\tZu berechnen\n")) ;
		$customer	=	new Customer() ;
		foreach ( $myAccountingPeriodItem as $accountingPeriodItem) {
			if ( $accountingPeriodItem->CustomerNo != $customer->CustomerNo) {
				$customer->setKey( $accountingPeriodItem->CustomerNo) ;
			}
			fwrite( $myFile, sprintf( "\"%s\"\t\"%s\"\t\"%s\"\t%d\t%d\t%d\n",
										$accountingPeriodItem->CustomerNo,
										$customer->CustomerName1,
										$accountingPeriodItem->MaterialNo,
										$accountingPeriodItem->QtyBeginPeriod,
										$accountingPeriodItem->QtyReturnedInPeriod,
										$accountingPeriodItem->Qty
								)) ;
		}
		fclose( $myFile) ;
		$reply->message	=	FTr::tr( "A CSV file as input for Microsoft Excel or OpenOffice was succesfully generated.") ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	downloadCSV( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		$filename	=	$path . "Abrechnung_" . $this->PeriodStart . "_" . $this->PeriodEnd . ".csv" ;
		$reply->replyData	=	"<Reference>" . $filename . "</Reference>\n" ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * returns the name of the PDF file which has been created
	 */
	function	getRef( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaTextCSV ;
		$reply->txtName	=	"test.txt" ;
		$reply->fullTXTName	=	$_val ;
		FDbg::end() ;
		return $reply ;
	}
}
?>
