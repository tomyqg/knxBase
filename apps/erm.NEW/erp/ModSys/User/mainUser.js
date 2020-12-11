/**
 * regModUser
 * 
 * registers the module in the central database
 */
function	scrUser() {
	_debugL( 0x00000001, "mainUser.js::scrUser: begin\n") ;
	Screen.call( this, "User") ;				// instantiate the super-class!!!
	this.package	=	"ModSys" ;			// directory of the module
	this.module	=	"User" ;					// sub-directory of the screen
	this.coreObject	=	"User" ;
	this.keyForm	=	"UserKeyData" ;		// form
	this.keyField	=	getFormField( 'UserKeyData', '_IUserId') ;
	this.fncNew	=	null ;
	this.fncDelete	=	null ;
	this.delConfDialog	=	"/ModSys/User/confUserDel.php" ;
	/**
	 * create the selector
	 */
	this.selConfigObj	=	new selector( this, "selUser", 'ModSys', '/ModSys/User/selUser.php', 'User') ;
	/**
	 * 
	 */
	this.fncLink	=	function() {
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( _response) {
		/**
		 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
		 */
		user	=	_response.getElementsByTagName( "User")[0] ;
		attrs	=	user.childNodes ;
		dispAttrs( attrs, "UserKeyData") ;
		dispAttrs( attrs, "formUserMain") ;
		dispAttrs( attrs, "formUserModi") ;
		dispAttrs( attrs, "formUserZugriff") ;
	} ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
	_debugL( 0x00000001, "mainUser.js::scrUser: end\n") ;
}