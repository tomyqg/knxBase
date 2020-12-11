<?php
/**
 * Pay_PostPay.php Base class for Customer Order (Carr)
 *
 * Test mode:
 * BLZ		12345679
 * Acc.no.	1110000300	transaction ok, return code 4000
 * Acc.no.	1110000310	transaction not approved, return code 4900
 * Acc.no.	1110000390	transaction uncertain, return code 4500
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "modules/iPay.php") ;
require_once( "HTTP/Request2.php") ;
/**
	* Carr - User-Level Class for usage by the application
	*
	* This class acts as an interface towards the automatically generated BCarr which should
	* not be modified.
	*
	* @package Application
	* @subpackage Carrier
	*/
class	Pay_PostPay	extends	Pay	implements	iPay	{
	private	static	$payConfig	=	null ;
	/**
	* Constructor
	*
	* The constructor can be passed a OrderNr (CarrNr), in which case it will automatically
	* (try to) load the respective Carrier Data from the Database
	*
	* @param string $_myCarrNr
	* @return void
	*/
	function	__construct( $_myPayNr='') {
		FDbg::begin( 1, "Pay_PostPay.php", "Pay_PostPay{Pay:iPay}", "__construct()") ;
		Pay::__construct() ;
		Pay::setName( "PostPay") ;
		if ( self::$payConfig == null) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Pay_PostPay.php", "Pay_PostPay{Pay:iPay}", "_reading PostPay configuration") ;
			self::$payConfig	=	new Config( "modules/Pay_PostPay.ini") ;
		} else {
			FDbg::trace( 3, FDbg::mdTrcInfo1, "Pay_PostPay.php", "Pay_PostPay{Pay:iPay}", "*NOT* _reading giropay configuration") ;
		}
		FDbg::end( 1, "Pay_PostPay.php", "Pay_PostPay{Pay:iPay}", "__construct()") ;
	}
	function	getConfig() {
		return self::$payConfig ;
	}
	/**
	* create the MD5 HMAC Hash for the given string with the given key
	* Note:	The used PHP function hash_hmac has become available as of 
	*		PHP version 5.1.2.
	*		For earlier versions of PHP this function is implemented locally.
	*
	* @param	string $data Daten über den der Hash generiert werden soll
	* @param	string $key Passphrase für den Hash
	* @return string generierter Hash-Code
	*/
	function generateHash( $data, $key ) {
		// hash_hmac Funktion ist erst seit PHP Version 5.1.2 verfügbar
		if( function_exists( 'hash_hmac' ) ) {
			return hash_hmac( 'md5', $data, $key );
		}
			
		// Implementierung f?r PHP4
		$b = 64; // byte length for md5
		if( strlen( $key ) > $b )
			$key = pack( "H*", md5( $key ) );
		$key = str_pad( $key, $b, chr(0x00) );
		$ipad = str_pad( '', $b, chr(0x36) );
		$opad = str_pad( '', $b, chr(0x5c) );
		$k_ipad = $key ^ $ipad ;
		$k_opad = $key ^ $opad;
		return md5( $k_opad . pack( "H*", md5( $k_ipad . $data ) ) );
	}
	function	getSuccess() {
		FDbg::begin( 1, "Pay_PostPay.php", "Pay_PostPay{Pay:iPay}", "getSuccess()") ;
		/**
		 * 
		 */
		$myGpCode	=	$_GET['gpCode'] ;
		$isOk	=	$this->codeIsOk( $myGpCode) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "Pay_PostPay.php", "Pay_PostPay{Pay:iPay}", "getSuccess()", "gpCode := " . $myGpCode) ;
		/**
		 * 
		 */
		FDbg::end( 1, "Pay_PostPay.php", "Pay_PostPay{Pay:iPay}", "getSuccess()") ;
		return( $isOk) ;
	}
	/**
	 * Liefert zu einem Giropay Fehlercode (gpCode) die 
	 * deutsche Fehlerbeschreibung.
	 *
	 * @param integer $gpCode Fehlercode von Giropay
	 * @return string Fehlerbeschreibung
	 */
	function getCodeDescrSys( $_gpCode=0, $_langCode="de_DE") {
		if ( $_gpCode == 0)
			$myGpCode	=	$_GET['gpCode'] ;
		$txtName	=	"PAY_PostPay_InfoSys_" . $myGpCode ;
		$myText	=	new Texte() ;
		$myText->setKeys( $txtName) ;
		return $myText->Volltext ;
	}
	
	/**
	 * Liefert einen Text, der dem Benutzer nach der Bezahlung über Giropay angezeigt wird
	 *
	 * @param integer $gpCode Fehler Code
	 * @return string Meldung f?r den Benutzer
	 */
	function getCodeDescrUser( $_gpCode=0, $_langCode="de_DE") {
		if ( $_gpCode == 0)
			$myGpCode	=	$_GET['gpCode'] ;
		$txtName	=	"PAY_PostPay_InfoUser_" . $myGpCode ;
		$myText	=	new Texte() ;
		$myText->setKeys( $txtName) ;
		return $myText->Volltext ;
	}
	/**
	 * Liefert, ob der angegebene Code OK bedeutet
	 *
	 * @param integer $gpCode Error Code
	 * @return boolean
	 */
	function getCode( $_gpCode=0) {
		if ( $_gpCode == 0)
			$myGpCode	=	$_GET['gpCode'] ;
		return $myGpCode ;
	}
	/**
	 * Liefert, ob der angegebene Code OK bedeutet
	 *
	 * @param integer $gpCode Error Code
	 * @return boolean
	 */
	function codeIsOK( $gpCode ) {
		if( $gpCode == '4000' )
			return true;
		return false;
	}
	/**
	 * Liefert, ob der angegebene Code ein Unbekannter ausgang ist
	 *
	 * @param integer $gpCode Error Code
	 * @return boolean
	 */
	function codeIsUnbekannt( $gpCode ) {
		if( $gpCode == '4500' )
			return true;

		return false;
	}
	/**
	 * Liefert, ob der angegebene Code ein Fehler ist
	 *
	 * @param integer $gpCode Error Code
	 * @return boolean
	 */
	function codeIsFehler( $gpCode ) {
		if( $gpCode != '4000' && $gpCode != '4500' )
			return true;

	}
	function	getForm() {
		FDbg::begin( 1, "Pay_PostPay.php", "Pay_PostPay{Pay:iPay}", "getForm()") ;
		$myConfig	=	$this->getConfig() ;
		/**
		 * get the current cart
		 */
		if ( isset( $_COOKIE['SessionId'])) {
			$this->mySession	=	new Session( $_COOKIE['SessionId']) ;
		}
		$this->cart	=	new CuCart( $this->mySession->CuCartNo) ;
		/**
		 * 
		 * @var unknown_type
		 */
		$myPaymentAgent	=	$_POST['PaymentAgent'] ;
		$myTransactionId	=	$this->cart->CuCartNo . "." . $this->cart->KundeNr . "." . $this->cart->KundeKontaktNr ;
		$this->myAmount	=	$this->cart->GesamtPreis + $this->cart->GesamtMwst ;
		/**
		 * submit the "API" call 'submitCartRequest'
		 */
		$myURL	=	$this->_getRedirectURL( $myTransactionId) ;
		///
		/// SPECIAL CODE
		///
		$myBuffer	=	"<form name=\"goPay\" method=\"post\" action=\"$myURL\">"
					.	"Bitte einen Augenblick Geduld.<br/>"
					.	"Sie werden auf die Seite Ihrer Bank weitergeleitet-<br/><br/>\n"
					.	"You are being redirected to the bank with bank code (BLZ) <b>$myBankCode</b>.<br/>\n"
					.	"Please remain patient.<br/>\n"
					.	"<input type=\"submit\" value=\"Continue\" />\n"
					.	"</form>"
					.	"<script type=\"text/javascript\">document.goPay.submit();</script>"
					.	"" ;
				FDbg::end( 1, "Pay_PostPay.php", "Pay_PostPay{Pay:iPay}", "getForm()") ;
		return $myBuffer ;
	}
	function	getSubmitURL() {
		$myConfig	=	$this->getConfig() ;
		return( $myConfig->PostPay->urlSubmit) ;
	}
	function	_getRedirectURL( $_transactionId) {
		$myConfig	=	$this->getConfig() ;
		$myURLAPI		=	$myConfig->PostPay->urlAPI ;
		$myURLSuccess	=	$myConfig->PostPay->urlSuccess."&transactionId=".$_transactionId ;
		$myURLError	=	$myConfig->PostPay->urlError."&transactionId=".$_transactionId ;
		$myURLBack	=	$myConfig->PostPay->urlBack."&transactionId=".$_transactionId ;
		/**
		 *
		 */
		$submitCartRequest	=
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<submitCartRequest xmlns='http://www.meinpaket.de/xsd/dietmar/1.0/checkout' 
xmlns:common='http://www.meinpaket.de/xsd/dietmar/1.0/common' version='1.0'>
	<header xmlns=\"http://www.meinpaket.de/xsd/dietmar/1.0/common\">
		<login>".$myConfig->PostPay->username."</login>
		<password>".$myConfig->PostPay->password."</password>
		<language>de</language>
		<multiplierId>UNKNOWN</multiplierId>
	</header>
	<shoppingCart>
		<cartId><![CDATA[".$this->cart->CuCartNo.$this->today().rand( 0, 9999)."]]></cartId>
		<shoppingCartItem>
			<productId>CUCART</productId>
			<name><![CDATA[Warenkorb lt. Anlage]]></name>
			<basePrice>".$this->myAmount."</basePrice>
			<tax>Standard</tax>
			<quantity>1</quantity>
		</shoppingCartItem>
		<shippingCost>0</shippingCost>
		<redirectURLSuccess><![CDATA[".$myURLSuccess."]]></redirectURLSuccess>
		<redirectURLError><![CDATA[".$myURLError."]]></redirectURLError>
		<redirectURLBack><![CDATA[".$myURLBack."]]></redirectURLBack>
<!--		<notificationId>".$_transactionId."</notificationId>		-->
	</shoppingCart>
</submitCartRequest>" ;
		$req	=	new HTTP_Request2( $myURLAPI, HTTP_Request2::METHOD_POST) ;
		$req->setHeader( array(
							'Content-Type' => 'text/xml')) ;
		error_log( $submitCartRequest) ;
		$req->setBody( $submitCartRequest) ;
		$reply	=	$req->send() ;
		$myBody	=	$reply->getBody() ;
		error_log( $myBody) ;
		/**
		 * create XML reader
		 */
		$xml	=	new XMLReader() ;
		$xml->XML( $myBody) ;
		$redirectURL	=	$this->_evalAnswer( $xml) ;
		return $redirectURL ;
	}
	/**
	 * 
	 * @param unknown $_xml
	 * @return boolean
	 */
	function	_evalAnswer( $_xml) {
		error_log( "Pay_PostPay.php::Pay_PostPay::_fetchFromXML( 'XML'): begin") ;
		$inObject	=	false ;
		$buffer	=	"" ;
		$this->_valid	=	false ;
		$newObj	=	null ;
		$objStack	=	array() ;
		while ( $_xml->read() && ! $this->_valid) {
			switch ( $_xml->nodeType) {
			case	1	:			// start element
				if ( $_xml->name)
				break ;
			case	3	:			// text node
			case	4	:
				$buffer	=	$_xml->value ;
				break ;
			case	14	:			// whitespace node
				break ;
			case	15	:			// end element
				switch ( $_xml->name) {
				case	"ns4:cartId"	:
					break ;
				case	"ns4:redirectURL"	:
					$redirectURL	=	$buffer ;
					break ;
				}
				break ;
			case	16	:			// end element
				break ;
			}
		}
		error_log( "Pay_PostPay.php::Pay_PostPay::_fetchFromXML( 'XML'): end") ;
		return $redirectURL ;
	}
}
Pay::register( "PostPay") ;
?>
