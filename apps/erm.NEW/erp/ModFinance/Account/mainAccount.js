/**
 * regModAccount
 *
 * registers the module in the central database
 */
function	scrAccount() {
	_debugL( 0x00000001, "mainAccount.js::scrAccount(...): begin\n") ;
	Screen.call( this, "Account") ;
	this.package	=	"ModFinance" ;
	this.module	=	"Account" ;
	this.coreObject	=	"Account" ;
	this.keyForm	=	"AccountKeyData" ;
	this.keyField	=	getFormField( 'AccountKeyData', '_IAccountNo') ;
	this.delConfDialog	=	"/ModFinance/Account/confAccountDel.php" ;
	/**
	 * create the selector
	 */
	this.select	=	new selector( this, "selAccount", 'ModFinance', '/ModFinance/Account/selAccount.php', 'Account') ;
	/**
	 * getting JS for tab 'AccountSubAccounts'
	 */
	this.dtvAccountSubAccounts	=	new dataTableView( this, "dtvAccountSubAccounts", "TableAccountSubAccounts", "Account", "AccountKontakt", null, "ModFinance", "Account") ;
	this.dtvAccountSubAccounts.f1	=	"formAccountSubAccountsTop" ;
	/**
	 * getting JS for tab 'CustAddresses'
	 */
	/**
	 * getting JS for tab 'CustAddressesDeliver'
	 */
	this.dtvCustAddressesDeliver	=	new dataTableView( this, "dtvCustAddressesDeliver", "TableCustAddressesDeliver", "Account", "LiefAccount", null, "ModFinance", "Account") ;
	this.dtvCustAddressesDeliver.f1	=	"formCustAddressesDeliverTop" ;
	/**
	 * getting JS for tab 'CustDataMining'
	 */
	/**
	 * getting JS for tab 'CustFunctions'
	 */
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "mainAccount.js::linkAccount(): begin\n") ;
		_debugL( 0x00000001, "mainAccount.js::linkAccount(): end\n") ;
	} ;
	/**
	 *
	 */
	this.fncNew	=	function() {
		this.dispatch( true, 'add', '', '', '', 'formAccountMain') ;
	} ;
	/**
	 *
	 */
	this.onSelect	=	function( _key) {
		_debugL( 0x00000001, "mainAccount.js::loadAccountById(" + _key.toString() + "): begin\n") ;
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
		_debugL( 0x00000001, "mainAccount.js::loadAccountById(): end\n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( response) {
		if (response) {
			kunde	=	response.getElementsByTagName( "Account")[0] ;
			attrs	=	kunde.childNodes ;
			dispAttrs( attrs, "AccountKeyData") ;
			dispAttrs( attrs, "formAccountMain") ;
			dispAttrs( attrs, "formAccountModi") ;
			dispAttrs( attrs, "formAccountZugriff") ;
			this.dtvAccountSubAccounts.primObjKey	=	this.keyField.value ;
			this.dtvAccountSubAccounts.show( response) ;
		}
	} ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
	hookNextObject() ;
	_debugL( 0x00000001, "mainAccount.js::scrAccount(...): end\n") ;
}
/**
 *
 * @returns
 */
function	saveAccount () {
	kundeNr	=	getFormField( 'AccountKeyData', '_IAccountNo').value ;
	requestUniA( 'ModFinance', 'Account', '/Common/hdlObject.php', 'upd', kundeNr, '', '', new Array('formAccountMain', 'formAccountModi'), showAccount) ;
	return false ;
}
