/**
 * regModSysTexte
 * 
 * registers the module in the central database
 */
function	scrSysTexte() {
	Screen.call( this, "SysTexte") ;				// instantiate the super-class!!!
	this.package	=	"ModSys" ;			// directory of the module
	this.module	=	"SysTexte" ;					// sub-directory of the screen
	this.coreObject	=	"SysTexte" ;
	this.keyForm	=	"SysTexteKeyData" ;		// form
	this.keyField	=	getFormField( 'SysTexteKeyData', '_IId') ;
	this.fncNew	=	null ;
	this.fncDelete	=	null ;
	this.delConfDialog	=	"/ModSys/SysTexte/confSysTexteDel.php" ;
	/**
	 * create the selector
	 */
	this.selSysTexte	=	new selector( this, "selSysTexte", 'ModSys', '/ModSys/SysTexte/selSysTexte.php', 'SysTexte') ;
	/**
	 * 
	 */
	this.fncLink	=	function() {
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( _response) {
		showSysTexte( _response) ;
	} ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
}
function	newSysTexte() {
	requestUni( 'ModSys', 'SysTexte', '/Common/hdlObject.php', 'add', '', '', '', 'formSysTexteMain', showSysTexteAll) ;
}
/**
 *
 */
function	showSysTexte( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	mySysTexteNr ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	texte	=	response.getElementsByTagName( "SysTexte")[0] ;
	if ( texte) {
		attrs	=	texte.childNodes ;
		dispAttrs( attrs, "SysTexteKeyData") ;
		dispAttrs( attrs, "formSysTexteMain") ;
	}
}