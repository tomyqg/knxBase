<?php

/**
 * CustomerCart.php Base class for Customer Order (CustomerCart)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerCart - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCustomerCart which should
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerCart
 */
class	CustomerCart	extends	AppObjectERM_CR	{

	private	$tmpCustomerCartItem ;

	const	NEU			=	  0 ;	//	  X	  X	  X	  X	  X	  X	  X	  X	  X	  X
	const	ANGANF		=	  1 ;	//	   	   	  X
	const	ORDERED		=	  2 ;	//	  X	  X	  X	  X	  X	  X	  X	   	  X	  X
	const	DELIVERED	=	  3 ;	//	  	  	  	  	  	  	  X
	const	STORED		=	  7 ;	//	   	   	   	   	   	  X
	const	INVALID		=	  9 ;	//	  X	  X	  X	  X	  X	  X	  X	  X 	  X	  X
	public	static	$rStatus	=	array (
						CustomerCart::NEU			=> "Neu",
						CustomerCart::ANGANF		=> "Angebot angefordert",
						CustomerCart::ORDERED		=> "Bestellt",
						CustomerCart::DELIVERED	=> "Geliefert",
						CustomerCart::STORED		=> "Gespeichert",
						CustomerCart::INVALID		=> "Ungueltig") ;

	/**
	 * Constructor
	 *
	 * The constructor can be passed a OrderNr (CustomerCartNo), in which case it will automatically
	 * (try to) load the respective Customer Order via the base class from the Database
	 * If the order data was loaded the customer data, and customer contact data, will also be loaded
	 * from the database.
	 *
	 * @param string $_myCustomerCartNo
	 * @return void
	 */
	function	__construct( $_myCustomerCartNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		error_log( "..................." . $this->path->Logos) ;
		error_log( "..................." . $this->path->Archive) ;
		parent::__construct( "CustomerCart", "CustomerCartNo") ;
		if ( strlen( $_myCustomerCartNo) > 0) {
			$this->setCustomerCartNo( $_myCustomerCartNo) ;
		} else {
		}
		FDbg::end() ;
	}

	/**
	 * set the Order Number (CustomerCartNo)
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
	function	setCustomerCartNo( $_myCustomerCartNo) {
		$this->CustomerCartNo	=	$_myCustomerCartNo ;
		if ( strlen( $_myCustomerCartNo) > 0) {
			$this->reload() ;
		}
	}

	/**
	 * Create a Customer Order based on the provided Temp. Customer Order (TCustomerCart)
	 *
	 * @return void
	 */
	function	newFromTCustomerCart( $_myTCustomerCart) {
		$query	=	sprintf( "CustomerCart_newFromTCustomerCart( @status, '%s', @newCustomerCartNo) ; ", $_myTCustomerCart->TCustomerCartNo) ;
		try {
			$row	=	FDb::callProc( $query, "@newCustomerCartNo") ;
			$this->setCustomerCartNo( $row['@newCustomerCartNo']) ;
			$myText	=	date( "Ymd/Hi") . ": " . $_SERVER['PHP_AUTH_USER'] . ": erstellt aus temp. Bestellung Nr. " . $_myTCustomerCart->TCustomerCartNo . "\n" ;
			$myText	.=	$this->Rem1 ;
			$this->Rem1	=	$myText ;
			$this->updateInDb() ;
		} catch( Exception $e) {
			throw $e ;
		}
	}
	/**
	 *
	 */
	function	_updateHdlg( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myCustomerCartItem	=	new CustomerCartItem() ;
		$myCustomerCartItem->removeFromDbWhere( [ "CustomerCartNo = '$this->CustomerCartNo' AND ArticleNo like 'HDLG%'"]) ;
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
		FDbg::end() ;
	}
	function	_invalidate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$this->CustomerNo	=	"INVALID" ;
		$this->CustomerContactNo	=	"" ;
		$this->updateInDb() ;
		FDbg::end() ;
	}
	/**
	 * Create a new temporary order with the next available temp-order-nr and store
	 * the order in the database.
	 *
	 * @return void
	 */
	function	newCustomerCart() {

		$this->newKey( 6, "900000", "999999") ;
		$this->CustomerNo	=	"000000" ;
		$this->CustomerContactNo	=	"000" ;
		$this->Datum	=	$this->today() ;
		$this->RefDatum	=	$this->today() ;
		$this->KdRefDatum	=	$this->today() ;
		$this->updateInDb() ;
		try {
			$myTexte	=	new SysTexte() ;
			$myTexte->setKeys( "MZPrefix", "", "de") ;
			if ( $myTexte->_valid) {
				$this->Prefix	=	$myTexte->Volltext ;
			}
			$myTexte->setKeys( "MZPostfix", "", "de") ;
			if ( $myTexte->_valid) {
				$this->Postfix	=	$myTexte->Volltext ;
			}
			$this->updateInDb() ;
		} catch ( Exception $e) {
			error_log( $e->getMessage()) ;
		}
		return $this->_valid ;
	}

	/**
	 * Setzt den Prefix sowie den Postfix der Customernbestellung auf die Default Werte.
	 * Die Default Werte werden bestimmt durch die f�r den Customern abgespeicherten Wert
	 * f�r Sprache (z.B. de_de, en_us).
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: CustomerCart_setTexte( @status, <CustomerCartNo>).
	 *
	 * @return void
	 */
	function	setTexte( $_key="", $_id=-1, $_val="") {
		$query	=	sprintf( "CustomerCart_setTexte( @status, '%s', '') ; ", $this->CustomerCartNo) ;
		try {
			$sqlRows	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			throw $e ;
		}
	}
	/**
	 * updAddText
	 * @return CustomerCartItem
	 */
	function	updAddText( $_id, $_text) {
		try {
			$this->tmpCustomerCartItem	=	new CustomerCartItem() ;
			$this->tmpCustomerCartItem->Id	=	$_id ;
			$this->tmpCustomerCartItem->fetchFromDbById() ;
			if ( $this->tmpCustomerCartItem->_valid == 1) {
				$this->tmpCustomerCartItem->AddText	=	$_text ;
				$this->tmpCustomerCartItem->updateInDb() ;
			} else {
				$e	=	new Exception( 'CustomerCart::updAddText: CustomerCartItem[Id='.$_id.'] is INVALID !') ;
				error_log( $e->getMessage()) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->tmpCustomerCartItem ;
	}
	/**
	 *
	 */
	function	updateHdlg( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "CustomerCart.php", "CustomerCart", "updateHdlg( '$_key', $_id, '$_val')") ;
		$this->_updateHdlg( $_key, $_id, $_val) ;
		FDbg::end( 1, "CustomerCart.php", "CustomerCart", "updateHdlg( '$_key', $_id, '$_val')") ;
	}
	/**
	 * Verschicken als E-Mail
	 *
	 * @return [Article]
	 */
	function	sendByEMail( $_mailAdr) {
		$myText	=	date( "Ymd/Hi") . ": " . $_SERVER['PHP_AUTH_USER'] . ": verschickt als E-Mail an " . $_mailAdr . "\n" ;
		$myText	.=	$this->Rem1 ;
		$this->Rem1	=	$myText ;
		$this->DocVerschVia	=	34 ;		// ueber "Normal"-FAX
		$this->Status	=	ONGOING ;		// ueber "Normal"-FAX
		$this->updateInDb() ;
	}
	/**
	 * Verschicken per FAX
	 *
	 * @return [Article]
	 */
	function	sendByFAX( $_faxNr) {
		$myText	=	date( "Ymd/Hi") . ": " . $_SERVER['PHP_AUTH_USER'] . ": verschickt per FAX an " . $_faxNr . "\n" ;
		$myText	.=	$this->Rem1 ;
		$this->Rem1	=	$myText ;
		$this->DocVerschVia	=	30 ;		// ueber "Normal"-FAX
		$this->Status	=	ONGOING ;		// ueber "Normal"-FAX
		$this->updateInDb() ;
	}

	/**
	 * Verschicken per FAX
	 *
	 * @return [Article]
	 */
	function	sendAsPDF() {
		$myText	=	date( "Ymd/Hi") . ": " . $_SERVER['PHP_AUTH_USER'] . ": verschickt als PDF \n" ;
		$myText	.=	$this->Rem1 ;
		$this->Rem1	=	$myText ;
		$this->DocVerschVia	=	38 ;		// ueber "Normal"-FAX
		$this->Status	=	ONGOING ;		// ueber "Normal"-FAX
		$this->updateInDb() ;
	}
 	/**
 	 *
 	 */
	function	fetchFromDbByUniqueId() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()",
						"unique id := '" . $this->CustomerCartUniqueId . "' ") ;
		$this->_status	=	0 ;
		$this->_valid	=	false ;
		$this->fetchFromDbWhere( [ "CustomerCartUniqueId = '" . $this->CustomerCartUniqueId . "' "]) ;
		FDbg::end() ;
		return $this->_valid ;
	}
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getRStatus() {
		return self::$rStatus ;
	}
	/**
	 *
	 */
	function	getStatusT() {
		return self::$rStatus[$this->Status] ;
	}
	/**
	 *
	 */
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$ret	=	"<Document><![CDATA[" ;
		$filename	=	$this->path->Archive . "CustomerCart/" . $this->CustomerCartNo . ".pdf" ;
		if ( file_exists( $filename)) {
			$ret	.=	 $this->url->Archive . "CustomerCart/" . $this->CustomerCartNo . ".pdf" ;
		}
		$ret	.=	"]]></Document>" ;
		FDbg::end() ;
		return $ret ;
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
			$myObj	=	new FDbObject( "CustomerCart", "CustomerCartNo", "def", "v_CustomerCartSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"CustomerCartNo like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"CustomerCartItem"	:
			$myObj	=	new FDbObject( "CustomerCartItem", "Id", "def", "v_CustomerCartItemList") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"( CustomerCartNo = '" . $this->CustomerCartNo . "') " ;
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
	function	getCustomerAsXML() {
		$ret	=	"" ;

		$ret	.=	'<CustomerAdr>' ;
		$ret	.=	$this->Customer->getXMLF() ;
		$ret	.=	$this->CustomerKontakt->getXMLF() ;

		if ( $this->LiefCustomer) {
			$ret	.=	$this->LiefCustomer->getXMLF( "LiefCustomer") ;
		}
		if ( $this->LiefCustomerKontakt) {
			$ret	.=	$this->LiefCustomerKontakt->getXMLF( "LiefCustomerKontakt") ;
		}
		if ( $this->RechCustomer) {
			$ret	.=	$this->RechCustomer->getXMLF( "RechCustomer") ;
		}
		if ( $this->RechCustomerKontakt) {
			$ret	.=	$this->RechCustomerKontakt->getXMLF( "RechCustomerKontakt") ;
		}
		$ret	.=	'</CustomerAdr>' ;
		return $ret ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	getDocListAsXML( $_key, $_id, $_val) {
		$fullPath	=	$this->path->Archive . "CustomerCart/" ;
		$myDir	=	opendir( $fullPath) ;
		if ( $myDir) {
			$myFiles	=	array() ;
			while (($file = readdir( $myDir)) !== false) {
				if ( strncmp( $file, $this->CustomerCartNo, 6) == 0) {
					$myFiles[]	=	$file ;
				}
			}
		}
		closedir( $myDir);
		reset( $myFiles) ;
		asort( $myFiles) ;
		$ret	=	"<DocList>\n" ;
		$ret	.=	"<URLPath>$this->url->Archive/CustomerCart/</URLPath>\n" ;
		foreach ( $myFiles as $file) {
			$ret	.=	"<Doc>\n" ;
			if ( filetype( $fullPath . $file) == "file") {
				$ret	.=	"<RefType>FILESYSTEM</RefType>" ;
				$ret	.=	"<RefNr>FILESYSTEM</RefNr>" ;
				$ret	.=	"<Filename>$file</Filename>\n" ;
				$ret	.=	"<Filetype>" . myFiletype( $file) . "</Filetype>\n" ;
				$ret	.=	"<Filesize>" . filesize( $fullPath . $file) . "</Filesize>\n" ;
				$ret	.=	"<FileURL>" . $this->url->Archive . "CustomerCart/" . $file . "</FileURL>\n" ;
			}
			$ret	.=	"</Doc>\n" ;
		}
		$ret	.=	"</DocList>" ;
		return $ret ;
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
		$oFile	=	fopen( $this->path->Archive."XML/down/CustomerCart".$this->CustomerCartNo.".xml", "w+") ;
		fwrite( $oFile, "<CustomerCartPaket>\n") ;
		$buffer	=	$this->getXMLF() ;
		fwrite( $oFile, $buffer) ;
		$myCustomerCartItem	=	new CustomerCartItem() ;
		$myCustomerCartItem->CustomerCartNo	=	$this->CustomerCartNo ;
		for ( $myCustomerCartItem->_firstFromDb( "CustomerCartNo='$this->CustomerCartNo' ORDER BY PosNr ") ;
					$myCustomerCartItem->_valid == 1 ;
					$myCustomerCartItem->_nextFromDb()) {
			$buffer	=	$myCustomerCartItem->getXMLF() ;
			fwrite( $oFile, $buffer) ;
		}
		fwrite( $oFile, "</CustomerCartPaket>\n") ;
		fclose( $oFile) ;
		return $this->getXMLComplete() ;
	}
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
	function	mail( $_mailText, $_file="", $_fileName="", $_from="", $_to="", $_cc="", $_bcc="") {
		/**
		 * prepare the eMail attachment
		 */
		$fileName	=	"CustomerCart".$this->CustomerCartNo.".pdf" ;
		parent::mail(  $_mailText, $_file, $fileName, $_from, $_to, $_cc, $_bcc) ;
	}
}
?>
