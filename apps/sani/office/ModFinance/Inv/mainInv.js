/**
 * regModInv
 * 
 * registers the module in the central database
 */
function	scrInv() {
	Screen.call( this, "Inv") ;				// instantiate the super-class!!!
	this.package	=	"ModFinance" ;			// directory of the module
	this.module	=	"Inv" ;					// sub-directory of the screen
	this.coreObject	=	"Inv" ;
	this.keyForm	=	"InvKeyData" ;		// form
	this.keyField	=	getFormField( 'InvKeyData', '_IInvNo') ;
	this.delConfDialog	=	"/ModFinance/Inv/confInvDel.php" ;
	/**
	 * create the selector
	 */
	this.select	=	new selector( this, 'selInv', 'ModFinance', '/ModFinance/Inv/selInv.php', 'Inv') ;
	/**
	 * getting JS for tab 'InvItems' 
	 */
	this.dtvInvItems = new dataTableView( this, 'dtvInvItems', "TableInvItems", "Inv", "InvItem", null, 'ModFinance', 'Inv') ; 
	this.dtvInvItems.f1 = "formInvItemsTop" ;
	/**
	 * 
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "mainInv.js::linkInv(): begin\n") ;
		_debugL( 0x00000001, "mainInv.js::linkInv(): end\n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( _response) {
		if ( _response) {
			inventory	=	_response.getElementsByTagName( "Inv")[0] ;
			attrs	=	inventory.childNodes ;
			dispAttrs( attrs, "InvKeyData") ;
			dispAttrs( attrs, "formInvMain") ;
			dispAttrs( attrs, "formInvSonstiges") ;
		} else
			_debugL( 0x00000001, "mainInv.js::scrInv::fncShowAll( <XML>): no response\n") ;
		this.dtvInvItems.primObjKey	=	this.keyField.value ;
		this.dtvInvItems.show( _response) ;
	} ;
	/**
	 * process any pending 'link-to-screen# data
	 */
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	hookNextObject() ;
	pendingKey	=	"" ;
}
/**
 *
 */
function	showInv( _response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myInvNo ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	inventory	=	_response.getElementsByTagName( "Inv")[0] ;
	if ( inventory) {

		myInvNo	=	_response.getElementsByTagName( "InvNo")[0].childNodes[0].nodeValue ;

		attrs	=	inventory.childNodes ;
		dispAttrs( attrs, "InvKeyData") ;
		dispAttrs( attrs, "formInvMain") ;
		dispAttrs( attrs, "formInvItems") ;
	}
}
