/**
 * regModSuInvc
 * 
 * registers the module in the central database
 */
function	scrSuInvc() {
	_debugL( 0x00000001, "scrSuInvc.js::scrSuInvc::scrSuInvc(): begin \n") ;
	Screen.call( this, "SuInvc") ;				// instantiate the super-class!!!
	this.package	=	"ModPurchase" ;			// directory of the module
	this.module	=	"SuInvc" ;					// sub-directory of the screen
	this.coreObject	=	"SuInvc" ;
	this.keyForm	=	"SuInvcKeyData" ;		// form
	this.keyField	=	getFormField( 'SuInvcKeyData', '_ISuInvcNo') ;
	this.delConfDialog	=	"/ModPurchase/SuInvc/confSuInvcDel.php" ;
	/**
	 * create the selector
	 */
	this.select	=	new selector( this, 'selSuInvc', 'ModPurchase', '/ModPurchase/SuInvc/selSuInvc.php', 'SuInvc') ;
	/**
	 * getting JS for tab 'SuInvcItem' 
	 */
	this.dtvSuInvcItem = new dataTableView( this, 'dtvSuInvcItem', "TableSuInvcItem", "SuInvc", "SuInvcItem", null, 'ModPurchase', 'SuInvc') ; 
	this.dtvSuInvcItem.f1 = "formSuInvcItemTop" ; 
	/**
	 * 
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "scrSuInvc.js::scrSuInvc::fncLink(): begin \n") ;
		_debugL( 0x00000001, "scrSuInvc.js::scrSuInvc::fncLink(): end \n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( _response) {
		_debugL( 0x00000001, "scrSuInvc.js::scrSuInvc::fncShow(): begin \n") ;
		suInvc	=	_response.getElementsByTagName( "SuInvc")[0] ;
		attrs	=	suInvc.childNodes ;
		dispAttrs( attrs, "SuInvcKeyData") ;
		dispAttrs( attrs, "formSuInvcMain") ;
//		dispAttrs( attrs, "formSuInvcMiscellaneous") ;
		this.dtvSuInvcItem.primObjKey	=	this.keyField.value ;
		this.dtvSuInvcItem.show( _response) ;
		_debugL( 0x00000001, "scrSuInvc.js::scrSuInvc::fncShow(): end \n") ;
	} ;
	/**
	 * process any pending 'link-to-screen# data
	 */
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
	hookNextObject() ;
	_debugL( 0x00000001, "scrSuInvc.js::scrSuInvc::scrSuInvc(): end \n") ;
}
