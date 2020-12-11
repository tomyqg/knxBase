/**
 * regModPCuOrdr
 * 
 * registers the module in the central database
 */
function	scrPCuOrdr() {
	dBegin( 1, "mainPCuOrdr.js", "scrPCuOrdr", "main") ;
	Screen.call( this, "PCuOrdr") ;				// instantiate the super-class!!!
	this.package	=	"ModSales" ;			// directory of the module
	this.module	=	"PCuOrdr" ;					// sub-directory of the screen
	this.coreObject	=	"PCuOrdr" ;
	this.keyForm	=	"PCuOrdrKeyData" ;		// form
	this.keyField	=	getFormField( 'PCuOrdrKeyData', '_IPCuOrdrNo') ;
	this.delConfDialog	=	"/ModSales/PCuOrdr/confPCuOrdrDel.php" ;
	/**
	 * create the selector
	 */
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "creating selector") ;
	this.selPCuOrdr	=	new selector( this, 'selPCuOrdr', 'ModSales', '/ModSales/PCuOrdr/selPCuOrdr.php', 'PCuOrdr') ;
	/**
	 * create the selector for the article sales 
	 */
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "creating selector for article") ;
	this.selArticleSP	=	new selector( this, 'selArticleSP', 'ModBase', '/ModBase/Artikel/selArtikelVK.php', 'Artikel', 'VKPreisCache') ;
	this.selArticleSP.dtv.phpGetCall	=	"getSPList" ;
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining selArticleSP.selected method") ;
	this.selArticleSP.selected	=	function( _id) {
		dTrace( 3, "Selected PP Id " + _id + "\n") ;
		this.parent.dispatch( true, 'addPos', this.parent.keyField.value, _id, 1) ;
	} ;
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining showSelArticleSP method") ;
	this.showSelArticleSP	=	function() {
		this.selArticleSP.parent	=	this ;
		this.selArticleSP.show( '', -1, '') ;
	} ;
	/**
	 * create the selector for the article sales 
	 */
	_debugL( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "creating selector for customer") ;
	this.selCustomer	=	new selector( this, 'selKundeKontakt', 'ModBase', '/ModBase/Kunde/selKundeKontakt.php', 'Kunde', 'Kunde') ;
	this.selCustomer.dtv.phpGetCall	=	"getCCList" ;
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining selCustomerSelected method") ;
	this.selCustomer.selected	=	function( _id) {
		_debugL( 0x00000001, "Selected Customer Contact Id " + _id + "\n") ;
		this.parent.selCustomer.dijitDialog.hide();			
		this.parent.dispatch( true, this.parent.selCustomer.setFncName, this.parent.keyField.value, _id, 1) ;
	} ;
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining showSelCustomer method") ;
	this.showSelCustomer	=	function() {
		this.selCustomer.setFncName	=	"setKundeFromKKId" ;
		this.selCustomer.parent	=	this ;
		this.selCustomer.show( '', -1, '') ;
	} ;
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining showSelCustDlvr method") ;
	this.showSelCustDlvr	=	function() {
		this.selCustomer.setFncName	=	"setLiefKundeFromKKId" ;
		this.selCustomer.parent	=	this ;
		this.selCustomer.show( '', -1, '') ;
	} ;
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining showSelCustInvc method") ;
	this.showSelCustInvc	=	function() {
		this.selCustomer.setFncName	=	"setRechKundeFromKKId" ;
		this.selCustomer.parent	=	this ;
		this.selCustomer.show( '', -1, '') ;
	} ;
	/**
	 * getting JS for tab 'ArticleStocks' 
	 */
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "creating dtv for items") ;
	this.dtvPCuOrdrItems = new dataTableView( this, "dtvPCuOrdrItems", "TablePCuOrdrItems", "PCuOrdr", "PCuOrdrItem", null, "ModSales", "PCuOrdr") ; 
	this.dtvPCuOrdrItems.f1 = "formPCuOrdrItemsTop" ; 
	/**
	 * 
	 */
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining fncLink method") ;
	this.fncLink	=	function() {
		dBegin( 1, "mainPCuOrdr.js", "scrPCuOrdr", "fncLink()") ;
		dEnd( 1, "mainPCuOrdr.js", "scrPCuOrdr", "fncLink()") ;
	} ;
	/**
	 * 
	 */
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining onSelect method") ;
	this.onSelect	=	function( _key) {
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining show method") ;
	this.fncShow	=	function( _response) {
		reference	=	_response.getElementsByTagName( "Reference")[0] ;
		if ( reference) {
			objClass	=	_response.getElementsByTagName( "ObjectClass")[0].childNodes[0].nodeValue ;
			objKey		=	_response.getElementsByTagName( "ObjectKey")[0].childNodes[0].nodeValue ;
			screenLinkTo( objClass, objKey) ;
		} else {
			pCuOrdr	=	_response.getElementsByTagName( "PCuOrdr")[0] ;
			if ( pCuOrdr) {
				attrs	=	pCuOrdr.childNodes ;
				dispAttrs( attrs, "PCuOrdrKeyData") ;
				dispAttrs( attrs, "formPCuOrdrMain") ;
				dispAttrs( attrs, "formPCuOrdrModi") ;
			}
			showKundeAdrs( _response, "formPCuOrdrKunde", "formPCuOrdrKundeKontakt", "formPCuOrdrLiefKunde", "formPCuOrdrLiefKundeKontakt", "formPCuOrdrRechKunde", "formPCuOrdrRechKundeKontakt") ;
			this.dtvPCuOrdrItems.primObjKey	=	this.keyField.value ;
			this.dtvPCuOrdrItems.show( _response) ;
		}
	} ;
	/**
	 * 
	 */
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "defining newCuOrdr method") ;
	this.newCuOrdr	=	function() {
		confAction( this, '/ModSales/PCuOrdr/confCuOrdrFromPCuOrdr.php', 'newCuOrdr') ;
	} ;
	/**
	 * 
	 */
	dTrace( 2, "mainPCuOrdr.js", "scrPCuOrdr", "regModPCuOrdr()", "processing pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		hookPrevObject() ;
	}
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	dEnd( 1, "mainPCuOrdr.js", "scrPCuOrdr", "main") ;
}
