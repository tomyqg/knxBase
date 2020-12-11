/**
 * regModMerchant
 * 
 * registers the module in the central database
 */
function	regModMerchant() {
	_debugL( 0x00000001, "regModMerchant: \n") ;
	myScreen	=	screenAdd( "screenMerchant", linkMerchant, "Merchant", "MerchantKeyData", "_IMerchantId", showMerchantAll, null) ;
	myScreen.package	=	"ModMerchant" ;
	myScreen.module	=	"Merchant" ;
	myScreen.coreObject	=	"Merchant" ;
	myScreen.showFunc	=	showMerchantAll ;
	myScreen.keyField	=	getFormField( 'MerchantKeyData', '_IMerchantId') ;
	myScreen.delConfDialog	=	"/ModMerchant/Merchant/confMerchantDel.php" ;
	myScreen.link() ;
	if ( pendingKey != "") {
		requestUni( 'ModMerchant', 'Merchant', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showMerchantAll) ;
	}
	pendingKey	=	"" ;
}
function	linkMerchant() {
	_debugL( 0x00000001, "linkMerchant: \n") ;
}
function	saveMerchant () {
	kundeNr	=	getFormField( 'MerchantKeyData', '_IMerchantId').value ;
	requestUniA( 'ModMerchant', 'Merchant', '/Common/hdlObject.php', 'upd', kundeNr, '', '', new Array('formMerchantMain', 'formMerchantModi'), showMerchant) ;
	return false ;
}
/**
 *
 */
function	showMerchantAll( response) {
	showMerchant( response) ;
//	showTableMerchantTransactions( response) ;
}
/**
 *
 */
function	showMerchant( response) {
	var	kunde ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	kunde	=	response.getElementsByTagName( "Merchant")[0] ;
	attrs	=	kunde.childNodes ;
	dispAttrs( attrs, "MerchantKeyData") ;
	dispAttrs( attrs, "formMerchantMain") ;
	dispAttrs( attrs, "formMerchantInfo") ;
}
