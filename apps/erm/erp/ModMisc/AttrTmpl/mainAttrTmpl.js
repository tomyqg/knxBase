/**
 * scrAttrTmpl
 * 
 * registers the module in the central database
 */
function	scrAttrTmpl() {
	_debugL( 0x00000001, "mainAttrTmpl.js::scrAttrTmpl(): begin\n") ;
	Screen.call( this, "AttrTmpl") ;				// instantiate the super-class!!!
	this.package	=	"ModMisc" ;
	this.module	=	"AttrTmpl" ;
	this.coreObject	=	"AttrTmpl" ;
	this.keyForm	=	"AttrTmplKeyData" ;		// form
	this.keyField	=	getFormField( 'AttrTmplKeyData', '_IAttrTmplNo') ;
	this.delConfDialog	=	"/ModMisc/AttrTmpl/confAttrTmplDel.php" ;
	/**
	 * create the selector
	 */
	_debugL( 0x00000001, "mainAttrTmpl.js::scrAttrTmpl(): creating selector\n") ;
	this.selAttrTmpl	=	new selector( this, 'selAttrTmpl', 'ModMisc', '/ModMisc/AttrTmpl/selAttrTmpl.php', 'AttrTmpl') ;
	/**
	 * getting JS for tab 'ArticleStocks' 
	 */
	_debugL( 0x00000001, "mainAttrTmpl.js::scrAttrTmpl(): creating item view\n") ;
	this.dtvAttrTmplItems = new dataTableView( this, "dtvAttrTmplItems", "TableAttrTmplItems", "AttrTmpl", "AttrTmplItem", null, "ModMisc", "AttrTmpl") ; 
	this.dtvAttrTmplItems.f1 = "formAttrTmplItemsTop" ; 
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainAttrTmpl.js::regModAttrTmpl(): assigning link function\n") ;
	this.fncLink	=	function() {
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainAttrTmpl.js::regModAttrTmpl(): assigning onSelect function\n") ;
	this.onSelect	=	function( _key) {
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
	} ;
	/**
	 *
	 */
	_debugL( 0x00000001, "mainAttrTmpl.js::regModAttrTmpl(): assigning fncShow function\n") ;
	this.fncShow	=	function( _response) {
		reference	=	_response.getElementsByTagName( "Reference")[0] ;
		if ( reference) {
			objClass	=	_response.getElementsByTagName( "ObjectClass")[0].childNodes[0].nodeValue ;
			objKey		=	_response.getElementsByTagName( "ObjectKey")[0].childNodes[0].nodeValue ;
			screenLinkTo( objClass, objKey) ;
		} else {
			showAttrTmpl( _response) ;
			this.dtvAttrTmplItems.primObjKey	=	this.keyField.value ;
			this.dtvAttrTmplItems.show( _response) ;
		}
	} ;
	/**
	 *  process any pending 'link-to-screen# data
	 */
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	}
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	_debugL( 0x00000001, "mainAttrTmpl.js::scrAttrTmpl(): end\n") ;
}
function	linkAttrTmpl() {
	_debugL( 0x00000001, "linkAttrTmpl: \n") ;
}
/**
 *
 */
function	showAttrTmplAll( _response) {
	showAttrTmpl( _response) ;
	showTableAttrTmplPosten( _response) ;
}
/**
 *
 */
function	showAttrTmpl( _response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	attrTmpl	=	_response.getElementsByTagName( "AttrTmpl")[0] ;
	if ( attrTmpl) {

		myAttrTmplNo	=	_response.getElementsByTagName( "AttrTmplNo")[0].childNodes[0].nodeValue ;

		attrs	=	attrTmpl.childNodes ;
		dispAttrs( attrs, "AttrTmplKeyData") ;
		dispAttrs( attrs, "formAttrTmplMain") ;

	}
}