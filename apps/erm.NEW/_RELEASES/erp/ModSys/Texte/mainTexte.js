/**
 * regModTexte
 * 
 * registers the module in the central database
 */
function	scrTexte() {
	Screen.call( this, "Texte") ;				// instantiate the super-class!!!
	this.package	=	"ModSys" ;			// directory of the module
	this.module	=	"Texte" ;					// sub-directory of the screen
	this.coreObject	=	"Texte" ;
	this.keyForm	=	"TexteKeyData" ;		// form
	this.keyField	=	getFormField( 'TexteKeyData', '_IId') ;
	this.fncNew	=	null ;
	this.fncDelete	=	null ;
	this.delConfDialog	=	"/ModSys/Texte/confTexteDel.php" ;
	/**
	 * create the selector
	 */
	this.selTexte	=	new selector( this, "selTexte", 'ModSys', '/ModSys/Texte/selTexte.php', 'Texte') ;
	/**
	 * create the dataTableViews
	 */
	this.dmTexteOv	=	new dataTableView( this, "dmTexteOv", "TableTexteOv", "DataMinerTexte", "Texte", null, null, null) ;
	this.dmTexteOv.f1	=	"formTexteOvTop" ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainTexte.js::scrTexte(): assigning link function\n") ;
	this.fncLink	=	function() {
		_debugL( 0x00000001, "mainTexte.js::scrTexte::fncLink(): begin\n") ;
		_debugL( 0x00000001, "mainTexte.js::scrTexte::fncLink(): end\n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( _response) {
//		gotoTab( "TexteC3s1c2tc1", "TexteCPMain") ;
		/**
		 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
		 */
		texte	=	_response.getElementsByTagName( "Texte")[0] ;
		if ( texte) {
			attrs	=	texte.childNodes ;
			dispAttrs( attrs, "TexteKeyData") ;
			dispAttrs( attrs, "formTexteMain") ;
			dijit.byId( "_RVolltextTexte").set( "value", _response.getElementsByTagName( "Volltext")[0].childNodes[0].nodeValue) ;
			dijit.byId( "_RVolltext2Texte").set( "value", _response.getElementsByTagName( "Volltext2")[0].childNodes[0].nodeValue) ;
		}
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainTexte.js::scrTexte(): assigning getPOSTData function\n") ;
	this.getPOSTData	=	function() {
		_debugL( 0x00000001, "mainTexte.js::scrTexte::getPOSTData(): begin\n") ;
		post	=	"&_IVolltext="+encodeURIComponent( dijit.byId( "_RVolltextTexte").get( "value")) ;
		post	+=	"&_IVolltext2="+encodeURIComponent( dijit.byId( "_RVolltext2Texte").get( "value")) ;
		_debugL( 0x00000001, "mainTexte.js::scrTexte::getPOSTData(): end\n") ;
		return post ;
	} ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
}