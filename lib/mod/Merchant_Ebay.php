<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "HTTP/Request2.php") ;
/**
 * Merchant - Base Class
 *
 * @package Application
 * @subpackage Merchant
 */
class	Merchant_Ebay	extends	Merchant	{
	/**
	 *
	 */
	function	__construct( $_myMerchantId="") {
		parent::__construct( $_myMerchantId) ;
	}
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$objName	=	$_key . "_" . $_val ;
		if ( $objName == "Ebay_ShippingAddress") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			if ( $_POST['_SSearch'] == "NEW") {
				$res	=	$tmpObj->tableFromDb( "", "", "_CustomerNo = '' ", "ORDER BY AddressID DESC ", "ShippingAddress") ;
			} else {
				$res	=	$tmpObj->tableFromDb( "", "", "Name LIKE '%".$_POST['_SSearch']."%'", "ORDER BY AddressID DESC ", "ShippingAddress") ;
			}
					} else if ( $objName == "Ebay_Order") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$res	=	$tmpObj->tableFromDb( "", "", "true ", "ORDER BY OrderID DESC ") ;
		} else if ( $objName == "Ebay_Item") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$res	=	$tmpObj->tableFromDb( "", "", "true ", "ORDER BY _TransactionID DESC ") ;
		} else if ( $objName == "Ebay_Transaction") {
			$tmpObj	=	new $objName() ;
			$tmpObj->addCol( "SKU", "varchar") ;
			$tmpObj->addCol( "ArticleDescr1", "varchar") ;
			$res	=	"" ;
			$res	.=	$tmpObj->tableFromDb(
											", EbI.SKU",	/* , A.ArtikelBez1 AS ArticleDescr1 ",	*/
											"LEFT JOIN Ebay_Item AS EbI ON EbI._TransactionID = C.TransactionID ",
			/*									"LEFT JOIN Artikel AS A ON A.ArtikelNr = EbI.SKU ",		*/
			/* where clause */				"true ",
			/* order clause	*/				"ORDER BY TransactionID DESC ") ;
		}
		return $res ;
	}
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		if ( $objName == "Ebay_ShippingAddress") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$res	=	$tmpObj->getXML() ;
		} else if ( $objName == "Ebay_Order") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$res	=	$tmpObj->getXML() ;
		} else if ( $objName == "Ebay_Item") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$res	=	$tmpObj->getXML() ;
		} else if ( $objName == "Ebay_Transaction") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$res	=	$tmpObj->getXML() ;
		}
		return $res ;
	}
	/**
	 *
	 */
	function	receiveOrders( $_key="", $_id=-1, $_val="") {
		$req	=	new HTTP_Request2( $this->UrlXML, HTTP_Request2::METHOD_POST) ;
		$req->setHeader( array(
							'X-EBAY-API-COMPATIBILITY-LEVEL' => '759',
							'X-EBAY-API-DEV-NAME' => $this->Misc1,
							'X-EBAY-API-APP-NAME' => $this->Misc2,
							'X-EBAY-API-CERT-NAME' => $this->Misc3,
							'X-EBAY-API-CALL-NAME' => 'GetOrders',
							'X-EBAY-API-SITEID' => '0',
							'Content-Type' => 'text/xml')) ;
		$reqBody	=	"<?xml version='1.0' encoding='utf-8'?>"
	  	  			.	"<GetOrdersRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">"
	  	  			.	"  <RequesterCredentials>"
	  	  			.	"    <eBayAuthToken>" . $this->Misc4 . "</eBayAuthToken>"
	  	  			.	"  </RequesterCredentials>" ;
		$nowTime	=	$this->now() ;
		if ( $this->LastGetOrderTime != "") {
			$reqBody	.=	"  <ModTimeFrom>".$this->before( $this->LastGetOrderTime)."</ModTimeFrom>" ;
		} else {
			$reqBody	.=	"  <ModTimeFrom>".$this->longestBefore( $nowTime)."</ModTimeFrom>" ;
		}
		$reqBody	.=	"  <ModTimeTo>".$nowTime."</ModTimeTo>" ;
		$reqBody	.=	"  <OrderRole>Seller</OrderRole>"
//					.	"  <OrderStatus>Active</OrderStatus>"
	    			.	"  <ErrorLanguage>de_DE</ErrorLanguage>"
	  	     		.	"</GetOrdersRequest>" ;
		error_log( $reqBody) ;
		$req->setBody( $reqBody) ;
		$reply	=	$req->send() ;
		error_log( $reply->getBody()) ;
		$xml	=	new XMLReader() ;
//		$xml->XML( iconv( 'ISO-8859-1', 'UTF-8', $contents)) ;
error_log( $reply->getBody()) ;
		$xml->XML( $reply->getBody()) ;
		$this->_fetchFromXML( $xml) ;
		$this->LastGetOrderTime	=	$nowTime ;
		$this->Ebay_GetOrdersResponse->dump() ;
		if ( $this->Ebay_GetOrdersResponse->Ack == "Success") {
			$this->updateColInDb( "LastGetOrderTime") ;
		} else {
			$e	=	new Exception( "Merchant_Ebay.php::Merchane_Ebay::receiveOrders(...): failure '$this->ShortMessage'!") ;
			error_log( $e) ;
			throw $e ;
		}
	}
	/**

	 */
	function	_fetchFromXML( $_xml) {
		error_log( "Merchant_Ebay.php::Merchant_Ebay::_fetchFromXML( 'XML'): begin") ;
		$inObject	=	false ;
		$buffer	=	"" ;
		$this->_valid	=	false ;
		$newObj	=	null ;
		$objStack	=	array() ;
		while ( $_xml->read() && ! $this->_valid) {
			switch ( $_xml->nodeType) {
			case	1	:			// start element
				$className	=	"Ebay_" . $_xml->name ;
				if ( class_exists( $className, false)) {
					error_log( "Found $className for which a class definition exists") ;
					if ( $newObj != null)
						array_push( $objStack, $newObj) ;		// save reference to current object
					$newObj	=	new $className() ;
					$this->$className	=	$newObj ;
					$inObject	=	true ;
				} else {
//					error_log( "Found $className for which NO class definition exists") ;
				}
				break ;
			case	3	:			// text node
			case	4	:
				$buffer	=	$_xml->value ;
				break ;
			case	14	:			// whitespace node
				break ;
			case	15	:			// end element
				if ( $inObject) {
					$className	=	"Ebay_" . $_xml->name ;
					if ( strcmp( $className, $newObj->className) == 0) {
						$newObj->_valid	=	true ;
						if ( $className == "Ebay_Order") {
							$newObj->_AddressID	=	$this->Ebay_ShippingAddress->AddressID ;
						} else if ( $className == "Ebay_ShippingAddress") {
						} else if ( $className == "Ebay_Transaction") {
							$newObj->_OrderID	=	$this->Ebay_Order->OrderID ;
							$this->Ebay_Item->_TransactionID	=	$this->Ebay_Transaction->TransactionID ;
							$this->Ebay_Item->updateColInDb( "_TransactionID") ;
						} else if ( $className == "Ebay_Item") {
						}
						/**
						 *
						 */
						$newObj->store() ;
						if ( sizeof( $objStack) > 0) {
							$newObj	=	array_pop( $objStack) ;	// go back to last object
							error_log( "will continue with $newObj->className") ;
						} else {
							$newObj	=	null ;
							$inObject	=	false ;
						}
					} else {
						$colName	=	$_xml->name ;
						if ( isset( $newObj->$colName)) {
							$newObj->$colName	=	$buffer ;
							$buffer	=	"" ;
						}
					}
				}
				break ;
			case	16	:			// end element
				break ;
			}
		}
		error_log( "Merchant_Ebay.php::Merchant_Ebay::_fetchFromXML( 'XML'): end") ;
		return $this->_valid ;
	}
	/**
	 *
	 */
	function	now() {
		return date( "Y-m-d")."T".date( "H:i:s").".000Z" ;
	}
	function	before( $_date) {
		$myTime	=	mktime (
						substr( $_date, 11, 2),
						substr( $_date, 14, 2),
						substr( $_date, 17, 2),
						substr( $_date,  5, 2),
						substr( $_date,  8, 2),
						substr( $_date,  0, 4)) ;
		$myNewTime	=	$myTime - ( 2 * 60 * 60) ;
		return date( "Y-m-d", $myNewTime)."T".date( "H:i:s", $myNewTime).".000Z" ;
	}
	function	longestBefore( $_date) {
		$myTime	=	mktime (
						substr( $_date, 11, 2),
						substr( $_date, 14, 2),
						substr( $_date, 17, 2),
						substr( $_date,  5, 2),
						substr( $_date,  8, 2),
						substr( $_date,  0, 4)) ;
		$myNewTime	=	$myTime - ( 30 * 24 * 60 * 60) ;
		return date( "Y-m-d", $myNewTime)."T".date( "H:i:s", $myNewTime).".000Z" ;
	}
	/**
	 *
	 * @param void	$_key
	 * @param int	$_id	id of the record in Ebay_ShippingAddress to be inserted as "our" customer
	 * @param void	$_val
	 */
	function	insertCustomer( $_key="", $_id=-1, $_val="") {
		error_log( "Merchant_Ebay.php::insertCustomer( '$_key', $_id, '$_val'): begin") ;
		$shippingAddress	=	new Ebay_ShippingAddress() ;
		if ( $shippingAddress->setId( $_id)) {
			if ( $shippingAddress->_CustomerNo == "") {
				$myCust	=	new Customer() ;
				$myCust->fetchFromDbWhere( "WHERE FirmaName1 = '".$shippingAddress->Name."' ") ;
				if ( $myCust->isValid()) {
					$shippingAddress->_CustomerNo	=	$myCust->CustomerNo ;
				} else {
					$myCust->FirmaName1	=	$shippingAddress->Name ;
					$names	=	explode( " ", $shippingAddress->Street1) ;
					foreach ( $names as $i => $name) {
						if ( $i < ( sizeof( $names) - 1)) {
							if ( strlen( $myCust->Strasse) > 0)		$myCust->Strasse	.=	" " ;
							$myCust->Strasse	.=	$name ;
						} else {
							$myCust->Hausnummer	=	$name ;
						}
					}
					$myCust->PLZ	=	$shippingAddress->PostalCode ;
					$myCust->Ort	=	$shippingAddress->CityName ;
					$myCust->ModusSkonto	=	1 ;
					$myCust->newCustomer() ;
					$shippingAddress->_CustomerNo	=	$myCust->CustomerNo ;
					$myCustContact	=	new CustomerContact() ;
					$myCustContact->CustomerNo	=	$myCust->CustomerNo ;
					$names	=	explode( " ", $myCust->FirmaName1) ;
					foreach ( $names as $i => $name) {
						if ( $i < ( sizeof( $names) - 1)) {
							if ( strlen( $myCustContact->Vorname) > 0)		$myCustContact->Vorname	.=	" " ;
							$myCustContact->Vorname	.=	$name ;
						} else {
							$myCustContact->Name	=	$name ;
						}
					}
					$myCustContact->newCustomerContact() ;
				}
				$shippingAddress->updateColInDb( "_CustomerNo") ;
			} else {
				$e	=	new Exception( /*__FILE__."::".__CLASS__."::".*/__METHOD__."[".__LINE__."]( $_key, $_id, $_val): customer already has a Customer No.!") ;
				error_log( $e) ;
				throw $e ;
			}
		} else {
			error_log( "not valid!") ;
		}
		error_log( "Merchant_Ebay.php::insertCustomer( '$_key', $_id, '$_val'): end") ;
		return $this->getDepAsXML($_key, $_id, "Ebay_ShippingAddress") ;
	}
	/**
	 *
	 * @param void	$_key
	 * @param int	$_id	id of the record in Ebay_ShippingAddress to be inserted as "our" customer
	 * @param void	$_val
	 */
	function	insertOrder( $_key="", $_id=-1, $_val="") {
		error_log( __FILE__."::".__CLASS__."::".__METHOD__."( '$_key', $_id, '$_val'): begin") ;
		$order	=	new Ebay_Order() ;
		if ( $order->setId( $_id)) {
			/**
			 *
			 */
			$myCuOrdr	=	new CuOrdr() ;
			$myCuOrdr->add() ;
			$myCuOrdr->KdRefNr	=	"ebay" ;
			// fetch the shipping address
			$address	=	new Ebay_ShippingAddress() ;
			$address->fetchFromDbWhere( "WHERE AddressID = '".$order->_AddressID."' ") ;
			if ( $address->isValid()) {
				error_log( __FILE__."::".__CLASS__."::".__METHOD__."(): found Shipping Address Id := '".$order->_AddressID."', '".$address->_CustomerNo."' ") ;
				$myCuOrdr->CustomerNo	=	$address->_CustomerNo ;
				$myCuOrdr->CustomerContactNo	=	"001" ;
			} else
				error_log( __FILE__."::".__CLASS__."::".__METHOD__."(): can't find Shipping Address Id := '".$order->_AddressID."' ") ;
			$myCuOrdr->updateInDb() ;
			/**
			 *
			 */
			error_log( __FILE__."::".__CLASS__."::".__METHOD__."(): new order no. := '".$myCuOrdr->CuOrdrNo."' ") ;
			$order->_CuOrdrNo	=	$myCuOrdr->CuOrdrNo ;
			$order->updateInDb() ;
			/**
			 *
			 */
			$trans	=	new Ebay_Transaction() ;
			$trans->setIterCond( "_OrderID = '".$order->OrderID."' ") ;
			$trans->setIterOrder( " ") ;
			$item	=	new Ebay_Item() ;
			foreach( $trans as $ndx => $obj) {
				$parts	=	explode( "-", $obj->_OrderID) ;
				$item->fetchFromDbWhere( "WHERE ItemID = '".$parts[0]."' AND _TransactionID='".$parts[1]."' ") ;
				error_log( __FILE__."::".__CLASS__."::".__METHOD__."(): [0]. := '".$parts[0]."' ") ;
				error_log( __FILE__."::".__CLASS__."::".__METHOD__."(): [1]. := '".$parts[1]."' ") ;
				error_log( __FILE__."::".__CLASS__."::".__METHOD__."(): SKU. := '".$item->SKU."' ") ;
			}
		} else {
			error_log( "not valid!") ;
		}
		error_log( __FILE__."::".__CLASS__."::".__METHOD__."( '$_key', $_id, '$_val'): end") ;
		return $this->getDepAsXML($_key, $_id, "Ebay_Order") ;
	}
}
class	Ebay_ShippingAddress	extends	FDbObject	{
	function	__construct() {
		parent::__construct( "Ebay_ShippingAddress", "AddressID") ;
	}
	function	store() {
		error_log( "storing Ebay_ShippingAddress") ;
		$tmpObj	=	new Ebay_ShippingAddress() ;
		if ( $tmpObj->setKey( $this->AddressID)) {
			error_log( "Key '$this->AddressID' of 'Ebay_ShippingAddress' already in database") ;
		} else {
			$this->storeInDb() ;
		}
	}
}
class	Ebay_Order	extends	FDbObject	{
	function	__construct() {
		parent::__construct( "Ebay_Order", "OrderID") ;
	}
	function	store() {
		error_log( "storing Ebay_Order") ;
		$tmpObj	=	new Ebay_Order() ;
		if ( $tmpObj->setKey( $this->OrderID)) {
			error_log( "Key of 'Ebay_Order' already in database") ;
		} else {
			$this->storeInDb() ;
		}
	}
}
class	Ebay_Transaction	extends	FDbObject	{
	function	__construct() {
		parent::__construct( "Ebay_Transaction", "TransactionID") ;
	}
	function	store() {
		error_log( "storing Ebay_Transaction") ;
		$tmpObj	=	new Ebay_Transaction() ;
		if ( $tmpObj->setKey( $this->TransactionID)) {
			error_log( "Key of 'Ebay_Transaction' already in database") ;
		} else {
			$this->storeInDb() ;
		}
	}
}
class	Ebay_Item	extends	FDbObject	{
	function	__construct() {
		parent::__construct( "Ebay_Item", "ItemID") ;
	}
	function	store() {
		error_log( "storing $this->className") ;
		$tmpObj	=	new Ebay_Item() ;
		if ( $tmpObj->setKey( $this->ItemID)) {
			error_log( "Key of 'Ebay_Item' already in database") ;
		} else {
			$this->storeInDb() ;
		}
	}
}
class	Ebay_GetOrdersResponse	extends	EISSCoreObject	{
	function	__construct() {
		$this->className	=	"Ebay_GetOrdersResponse" ;
		$this->Ack	=	"" ;
		$this->Timestamp	=	"" ;
		$this->Version	=	"" ;
		$this->Build	=	"" ;
	}
	function	store() {
	}
}
?>
