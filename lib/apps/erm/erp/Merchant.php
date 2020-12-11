<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "HTTP/Request2.php") ;
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/AppObject.php") ;
/**
 * Merchant - Base Class
 *
 * @package Application
 * @subpackage Merchant
 */
class	Merchant	extends	AppObject	{
	/**
	 * 
	 * @var unknown_type
	 */
	const	TC_C		=	0 ;		// TypeCust:	0= Consumer
	const	TC_B		=	1 ;		// TypeCust:	1= Business
	private	static	$rTypeCust	=	array (
						-1				=> "ALL",
						Merchant::TC_C		=> "Consumer",
						Merchant::TC_B		=> "Business"
					) ;
	const	MP_COP		=	10 ;	// ModeOfPayment:	 0= cash on pickup
	const	MP_COD		=	20 ;	// ModeOfPayment:	10= cash on delivery
	const	MP_COO		=	30 ;	// ModeOfPayment:	20= cash on order
	const	MP_COO_GP	=	31 ;	// ModeOfPayment:	21= cash on order, GiroPay
	const	MP_INVC		=	40 ;	// ModeOfPayment:	30= invoice
	private	static	$rModePmnt	=	array (
						-1					=>	"ALL",
						Merchant::MP_COP		=> 	"Cash on Pickup",
						Merchant::MP_COD		=>	"Cash on Delivery",
						Merchant::MP_COO		=>	"Cash on Order",
						Merchant::MP_COO_GP	=>	"Cash on Order, GiroPay",
						Merchant::MP_INVC		=>	"Invoice"
					) ;
	const	MD_DC		=	  0 ;
	const	MD_DPAC		=	 10 ;
	const	MD_DP		=	 20 ;
	const	MD_DA		=	 30 ;
	private	static	$rModeDlvr	=	array (
						-1					=>	"All",
						Merchant::MD_DC		=>	"don't care",
						Merchant::MD_DPAC		=>	"Deliver partially at cost",
						Merchant::MD_DP		=>	"Deliver partially",
						Merchant::MD_DA		=>	"Deliver all"
					) ;
	const	MI_DC		=	  0 ;
	const	MI_IPP		=	 10 ;
	const	MI_IPC		=	 20 ;
	private	static	$rModeInvc	=	array (
						-1					=>	"All",
						Merchant::MI_DC		=>	"don't care",
						Merchant::MI_IPP		=>	"Invoice partially",
						Merchant::MI_IPC		=>	"Invoice complete"
					) ;
	/**
	 *
	 */
	function	__construct( $_myMerchantId="") {
		parent::__construct( "Merchant", "MerchantId") ;
		if ( strlen( $_myMerchantId) > 0) {
			try {
				$this->setMerchantId( $_myMerchantId) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	/**
	 *
	 */
	function	setMerchantId( $_myMerchantId) {
		$this->MerchantId	=	$_myMerchantId ;
		$this->reload() ;
		if ( ! $this->_valid) {
			$e	=	new Exception( "Merchant.php::Merchant::setMerchantId( '$_myMerchantId'): merchant not valid!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->_valid ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		return $this->getXMLString() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		global	$myUser ;
		FDbg::dumpL( 0x00000001, "Merchant::upd(...)") ;
		$this->getFromPostL() ;
		$myText	=	date( "Ymd/Hi") . ": " . $myUser->UserId . ": " . FTr::tr( "Customer updated") . "\n" ;
		$myText	.=	$this->Rem ;
		$this->Rem	=	$myText ;
		$this->updateInDb() ;
		return $this->getXMLString() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Merchant.php::Merchant::del(...)") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 */
	function	newMerchant( $_nsStart="000000", $_nsEnd="899999") {
		FDbg::dumpL( 0x00000001, "Merchant.php::Merchant::newMerchant( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  MerchantId >= '$_nsStart' AND MerchantId <= '$_nsEnd' " .
						"ORDER BY $this->keyCol DESC LIMIT 1 ), $_nsStart+1)  AS newKey" ;
		$myRow	=	FDb::queryRow( $myQuery) ;
		$keyCol	=	$this->keyCol ;
		$this->$keyCol	=	sprintf( "%06s", $myRow['newKey']) ;
		$this->Tax	=	1 ;
		$this->storeInDb() ;
		$this->reload() ;
		return $this->_status ;
	}
	/**
	 * Kommentar zu der Colli hinzufuegen
	 *
	 *	Dies Funktion fuegt einen Kommentar, $_rem, an die Colli an.
	 *	Datum/Uhrzeit sowie die Id des angemeldeten Benutzers, oder - im Falle eines unauthtntizierten Zugriffs - der Zusatz: "Hintergrunf Prozess"
	 *	werden automatisch in dem Kommentar vermerkt.
	 *
	 *	@param	char	$_rem
	 *	@return	void
	 */
	function	addRem( $_rem) {
		FDbg::dumpL( 0x00000001, "Merchant::addRem(...)") ;
		try {
			if ( isset( $_SESSION['UserId'])) {
				$myText	=	date( "Ymd/Hi") . ": " . $_SESSION['UserId'] . ": " . $_rem . "\n" ;
			} else {
				$myText	=	date( "Ymd/Hi") . ": " . "Hintergrund Prozess" . ": " . $_rem . "\n" ;
			}
			$myText	.=	$this->Rem ;
			$this->Rem	=	$myText ;
			$this->updateInDb() ;
		} catch( Exception $e) {
			throw $e ;
		}
	}
	/**
	 * 
	 */
	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		return $ret ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTableMerchantKontaktAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;

		/**
		 *
		 */
		$myMerchantKontakt	=	new MerchantKontakt() ;
		$ret	=	$myMerchantKontakt->tableFromDb( "",
								"",
								"C.MerchantId  = '" . $this->MerchantId . "' ",
								"ORDER BY C.MerchantKontaktNr ") ;
		return $ret ;
	}
	/**
	 *
	 */
	static	function	getRTypeCust() {	return self::$rTypeCust ;	}
	static	function	getRModeDlvr() {	return self::$rModeDlvr ;	}
	static	function	getRModeInvc() {	return self::$rModeInvc ;	}
	static	function	getRModePmnt() {	return self::$rModePmnt ;	}
	
	/**
	 * 
	 */
	function	receiveOrders( $_key="", $_id=-1, $_val="") {
		$objName	=	"Merchant_" . $_key ;
		$myMerchant	=	new $objName( $this->MerchantId) ;
		return $myMerchant->receiveOrders() ;
	}
	function	now() {
		$myMerchant	=	new Merchant_Ebay( $this->MerchantId) ;
		return $myMerchant->now() ;
	}
	function	before( $_date) {
		$myMerchant	=	new Merchant_Ebay( $this->MerchantId) ;
		return $myMerchant->before( $_date) ;
	}
	function	insertCustomer( $_key="", $_id=-1, $_val="") {
		$myMerchant	=	new Merchant_Ebay( $this->MerchantId) ;
		$myMerchant->insertCustomer( $_key, $_id, $_val) ;
		return $this->getXMLString( $_key, $_id, $_val) ;
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
		if ( isset( $_POST['_SSearch']))
			$searchCrit	=	$_POST['_SSearch'] ;
		$objName	=	$_key . "_" . $_val ;
		if ( $objName == "Ebay_ShippingAddress") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			if ( $searchCrit == "NEW") {
				$res	=	$tmpObj->tableFromDb( "", "", "_CustomerNo = '' ", "ORDER BY AddressID DESC ", "ShippingAddress") ;
			} else {
				$res	=	$tmpObj->tableFromDb( "", "", "Name LIKE '%".$searchCrit."%'", "ORDER BY AddressID DESC ", "ShippingAddress") ;
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
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	getListAsXML( $_key="", $_id=-1, $_val="") {
//		$_suchKrit	=	$_POST['_SCuOrdrNo'] ;
//		$_sStatus	=	intval( $_POST['_SStatus']) ;
//		$_POST['_step']	=	$_id ;
		$filter	=	"true " ;
//		$filter	.=	"C.CuOrdrNo like '%" . $_suchKrit . "%' " ;
//		if ( $_POST['_SCompany'] != "")
//			$filter	.=	"  AND ( FirmaName1 like '%" . $_POST['_SCompany'] . "%' OR FirmaName2 LIKE '%" . $_POST['_SCompany'] . "%') " ;
//		if ( $_POST['_SZIP'] != "")
//			$filter	.=	"  AND ( PLZ like '%" . $_POST['_SZIP'] . "%' ) " ;
//		if ( $_POST['_SContact'] != "")
//			$filter	.=	"  AND ( Name like '%" . $_POST['_SContact'] . "%' OR Vorname LIKE '%" . $_POST['_SContact'] . "%') " ;
//		if ( $_sStatus != -1) {
//			$filter	.=	"AND ( C.Status = " . $_sStatus . ") " ;
//		}
//		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "MerchantId", "var") ;
		$myObj->addCol( "MarketPlace", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.MerchantId ASC ",
								"Merchant",
								"Merchant",
								"C.Id, C.MerchantId, C.MarketPlace ") ;
		return $ret ;
	}
}
/**
 * load the carrier modules
 * @var unknown_type
 */
$myConfig	=	EISSCoreObject::__getConfig() ;
$fullPath	=	$myConfig->path->Modules ;
$myDir	=	opendir( $fullPath) ;
if ( $myDir) {
	$myFiles	=	array() ;
	while (($file = readdir( $myDir)) !== false) {
		if ( strncmp( $file, "Merchant_", 9) == 0 && strpos( $file, ".php") > 0 && strpos( $file, "dbInit") === false) {
			error_log( "Including .....:   modules/".$file) ;
			include( "modules/".$file) ;
		}
	}
	closedir( $myDir);
}
?>
