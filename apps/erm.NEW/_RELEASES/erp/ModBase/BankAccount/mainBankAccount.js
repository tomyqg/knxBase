/**
 * regModBankAccount
 * 
 * registers the module in the central database
 */
function	regModBankAccount() {
	_debugL( 0x00000001, "regModBankAccount: \n") ;
	myScreen	=	screenAdd( "screenBankAccount", linkBankAccount, "BankAccount", "BankAccountKeyData", "_IBankAccountNr", showBankAccountAll, null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"BankAccount" ;
	myScreen.coreObject	=	"BankAccount" ;
	myScreen.showFunc	=	showBankAccountAll ;
	myScreen.keyField	=	getFormField( 'BankAccountKeyData', '_IERPNo') ;
	myScreen.delConfDialog	=	"/Base/BankAccount/confBankAccountDel.php" ;
	myScreen.link() ;
	if ( pendingKey != "") {
		requestUni( 'Base', 'BankAccount', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showBankAccountAll) ;
	}
	pendingKey	=	"" ;
}
function	linkBankAccount() {
	_debugL( 0x00000001, "linkBankAccount: \n") ;
}
/**
 *
 */
function	showBankAccountAll( response) {
	showBankAccount( response) ;
	return true ;
}

/**
 *
 */
function	showBankAccount( response) {
	var	artikel ;
	var	attrs ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	artikel	=	response.getElementsByTagName( "BankAccount")[0] ;
	attrs	=	artikel.childNodes ;
	dispAttrs( attrs, "BankAccountKeyData") ;
	dispAttrs( attrs, "formBankAccountMain") ;
}