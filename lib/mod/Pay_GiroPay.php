<?php

/**
 * Pay_GiroPay.php Base class for Customer Order (Carr)
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
/**
	* Carr - User-Level Class for usage by the application
	*
	* This class acts as an interface towards the automatically generated BCarr which should
	* not be modified.
	*
	* @package Application
	* @subpackage Carrier
	*/
class	Pay_GiroPay	extends	Pay	implements	iPay	{
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
		FDbg::begin( 1, "Pay_GiroPay.php", "Pay_GiroPay{Pay:iPay}", "__construct()") ;
		Pay::__construct() ;
		Pay::setName( "GiroPay") ;
		if ( self::$payConfig == null) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Pay_GiroPay.php", "Pay_GiroPay{Pay:iPay}", "_reading giropay configuration") ;
			self::$payConfig	=	new Config( "modules/Pay_GiroPay.ini") ;
		} else {
			FDbg::trace( 3, FDbg::mdTrcInfo1, "Pay_GiroPay.php", "Pay_GiroPay{Pay:iPay}", "*NOT* _reading giropay configuration") ;
		}
		FDbg::end( 1, "Pay_GiroPay.php", "Pay_GiroPay{Pay:iPay}", "__construct()") ;
	}
	function	getConfig() {
		return self::$payConfig ;
	}
	/**
	* Generiert einen MD5 HMAC Hash über den übergebenen String
	* anhand des Keys.
	* Hinweis: Die verwendete PHP Funktion hash_hmac ist erst ab 
	*          PHP Version 5.1.2 verfügbar
	*          Für frühere PHP Versionen ist diese Funktionalität
	*          als PHP Code implementiert
	*
	* @param string $data Daten über den der Hash generiert werden soll
	* @param string $key Passphrase für den Hash
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
		FDbg::begin( 1, "Pay_GiroPay.php", "Pay_GiroPay{Pay:iPay}", "getSuccess()") ;
		/**
		 * 
		 */
		$myGpCode	=	$_GET['gpCode'] ;
		$isOk	=	$this->codeIsOk( $myGpCode) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "Pay_GiroPay.php", "Pay_GiroPay{Pay:iPay}", "getSuccess()", "gpCode := " . $myGpCode) ;
		/**
		 * 
		 */
		FDbg::end( 1, "Pay_GiroPay.php", "Pay_GiroPay{Pay:iPay}", "getSuccess()") ;
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
		$txtName	=	"PAY_GiroPay_InfoSys_" . $myGpCode ;
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
		$txtName	=	"PAY_GiroPay_InfoUser_" . $myGpCode ;
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
		FDbg::begin( 1, "Pay_GiroPay.php", "Pay_GiroPay{Pay:iPay}", "getForm()") ;
		$myConfig	=	$this->getConfig() ;
		/**
		 * 
		 */
		if ( isset( $_COOKIE['SessionId'])) {
			$this->mySession	=	new Session( $_COOKIE['SessionId']) ;
		}
		$cart	=	new CuCart( $this->mySession->CuCartNo) ;
		/**
		 * 
		 * @var unknown_type
		 */
		$myPaymentAgent	=	$_POST['PaymentAgent'] ;
		$myTransactionId	=	$cart->CuCartNo . "." . $cart->KundeNr . "." . $cart->KundeKontaktNr ;
		$myAmount	=	$cart->GesamtPreis + $cart->GesamtMwst ;
		$myBankCode	=	$_POST['bankcode'] ;
		$myMerchantId	=	$myConfig->GiroPay->MerchantId ;
		$myProjectId	=	$myConfig->GiroPay->ProjectId ;
		$myCurrency	=	"" ;
		$myVWZ	=	$myTransactionId ;
		$mySubmitURL	=	$myConfig->GiroPay->urlSubmit ;
		$myRedirectURL	=	$myConfig->GiroPay->urlBackToShop."&transactionId=".$myTransactionId ;
		///
		/// SPECIAL CODE
		///
		$myNotifyURL	=	$myConfig->GiroPay->urlNotifyPayment."&transactionId=".$myTransactionId ;
		$myHash	=	$this->generateHash( $myMerchantId.$myProjectId.$myTransactionId.$myAmount.$myVWZ.$myBankCode.$myRedirectURL.$myNotifyURL, "psion0") ;

		$myBuffer	=	"<form name=\"goPay\" method=\"post\" action=\"$mySubmitURL\">"
  					.	"<input type=\"hidden\" name=\"cartId\" value=\"".$cart->CuCartNo."\" />"
					.	"<input type=\"hidden\" name=\"paymentAgent\" value=\"$myPaymentAgent\" />\n"
					.	"<input type=\"hidden\" name=\"merchantId\" value=\"$myMerchantId\" />\n"
					.	"<input type=\"hidden\" name=\"projectId\" value=\"$myProjectId\" />\n"
					.	"<input type=\"hidden\" name=\"transactionId\" value=\"$myTransactionId\" />\n"
					.	"<input type=\"hidden\" name=\"amount\" value=\"$myAmount\" />\n"
					.	"<input type=\"hidden\" name=\"vwz\" value=\"$myVWZ\" />\n"
					.	"<input type=\"hidden\" name=\"bankcode\" value=\"$myBankCode\" />\n"
					.	"<input type=\"hidden\" name=\"urlRedirect\" value=\"$myRedirectURL\" />\n"
					.	"<input type=\"hidden\" name=\"urlNotify\" value=\"$myNotifyURL\" />\n"
					.	"<input type=\"hidden\" name=\"hash\" value=\"$myHash\" />\n"
					.	"Bitte einen Augenblick Geduld.<br/>"
					.	"Sie werden auf die Seite Ihrer Bank weitergeleitet-<br/><br/>\n"
					.	"You are being redirected to the bank with bank code (BLZ) <b>$myBankCode</b>.<br/>\n"
					.	"Please remain patient.<br/>\n"
					.	"<input type=\"submit\" value=\"Continue\" />\n"
					.	"</form>"
					.	"<script type=\"text/javascript\">document.goPay.submit();</script>"
					.	"" ;
		FDbg::end( 1, "Pay_GiroPay.php", "Pay_GiroPay{Pay:iPay}", "getForm()") ;
		return $myBuffer ;
	}
	function	getSubmitURL() {
		$myConfig	=	$this->getConfig() ;
		return( $myConfig->GiroPay->urlSubmit) ;
	}
}
Pay::register( "GiroPay") ;
?>
