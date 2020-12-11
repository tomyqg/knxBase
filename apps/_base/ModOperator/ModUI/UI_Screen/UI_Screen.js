/**
 * regModUI_Screen
 * 
 * registers the module in the central database
 */
function	scrUI_Screen() {
	_debugL( 0x00000001, "scrUI_Screen.js::scrUI_Screen::scrUI_Screen(): begin \n") ;
	Screen.call( this, "UI_Screen") ;				// instantiate the super-class!!!
	this.package	=	"ModUI" ;			// directory of the module
	this.module	=	"UI_Screen" ;					// sub-directory of the screen
	this.coreObject	=	"UI_Screen" ;
	this.keyForm	=	"UI_ScreenKeyData" ;		// form
	this.keyField	=	getFormField( 'UI_ScreenKeyData', '_IId') ;
	this.delConfDialog	=	"/ModUI/UI_Screen/confUI_ScreenDel.php" ;
	/**
	 * create the selector
	 */
	this.select	=	new selector( this, 'selUI_Screen', 'ModUI', '/ModUI/UI_Screen/selUI_Screen.php', 'UI_Screen') ;
	/**
	 * getting JS for tab 'UI_ScreenKontakt' 
	 */
//	this.dtvUI_ScreenKontakt = new dataTableView( this, 'dtvUI_ScreenKontakt', "TableUI_ScreenKontakt", "UI_Screen", "UI_ScreenKontakt", null, 'ModBase', 'UI_Screen') ; 
//	this.dtvUI_ScreenKontakt.f1 = "formUI_ScreenKontaktTop" ; 
	/**
	 * 
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "scrUI_Screen.js::scrUI_Screen::fncLink(): begin \n") ;
		_debugL( 0x00000001, "scrUI_Screen.js::scrUI_Screen::fncLink(): end \n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( _response) {
		adr	=	_response.getElementsByTagName( "UI_Screen")[0] ;
		attrs	=	adr.childNodes ;
		dispAttrs( attrs, "UI_ScreenKeyData") ;
		dispAttrs( attrs, "formUI_ScreenMain") ;
//		this.dtvUI_ScreenKontakt.primObjKey	=	this.keyField.value ;
//		this.dtvUI_ScreenKontakt.show( _response) ;
	} ;
	/**
	 * process any pending 'link-to-screen# data
	 */
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
	hookNextObject() ;
	_debugL( 0x00000001, "scrUI_Screen.js::scrUI_Screen::scrUI_Screen(): end \n") ;
}
