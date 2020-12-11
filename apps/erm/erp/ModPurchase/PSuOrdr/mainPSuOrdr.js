/**
 * 
 * 
 * 
 */
function	scrPSuOrdr() {
	_debugL( 0x00000001, "mainCuComm.js::regModPSuOrdr(): begin\n") ;
	Screen.call( this, "PSuOrdr") ;
	this.package	=	"ModPurchase" ;
	this.module		=	"PSuOrdr" ;
	this.coreObject	=	"PSuOrdr" ;
	this.keyForm	=	"PSuOrdrKeyData" ;
	this.keyField	=	getFormField( 'PSuOrdrKeyData', '_IPSuOrdrNo') ;
	/**
	 * create the selector
	 */
	this.selPSuOrdr	=	new selector( this, 'selPSuOrdr', 'ModPurchase', '/ModPurchase/PSuOrdr/selPSuOrdr.php', 'PSuOrdr') ;
	/**
	 * create DTV for 'Provisionary Supplier Order Items' 
	 */
	this.dtvPSuOrdrItems 	= new dataTableView( this, "dtvPSuOrdrItems", "TablePSuOrdrItems", "PSuOrdr", "PSuOrdrItem", null, "ModPurchase", "PSuOrdr") ; 
	this.dtvPSuOrdrItems.f1 = "formPSuOrdrItemsTop" ; 
	/**
	 * create the selector for the article sales 
	 */
	_debugL( 0x00000001, "mainCuComm.js::regModPSuOrdr(): creating article purchase price selector\n") ;
	this.selArticlePP	=	new selector( this, "selArticlePP", 'ModBase', '/ModBase/Artikel/selArtikelEK.php', 'Artikel', 'ArtikelEKPreis') ;
	this.selArticlePP.dtv.phpGetCall	=	"getPPList" ;
	this.selArticlePP.selected	=	function( _id) {
		_debugL( 0x00000001, "Selected PP Id " + _id + "\n") ;
		this.parent.dispatch( true, 'addPos', this.parent.keyField.value, _id, 1) ;
	} ;
	this.showSelArticlePP	=	function() {
		this.selArticlePP.parent	=	this ;
		this.selArticlePP.show( '', -1, '') ;
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainCuComm.js::regModPSuOrdr(): assigning link function\n") ;
	this.fncLink	=	function() {
		_debugL( 0x00000001, "scrPSuOrdr::scrPSuOrdr::fncLink(): begin\n") ;
		_debugL( 0x00000001, "scrPSuOrdr::scrPSuOrdr::fncLink(): end\n") ;
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainCuComm.js::regModPSuOrdr(): assigning onSelect function\n") ;
	this.onSelect	=	function( _key) {
		_debugL( 0x00000001, "mainAdr.js::scrAdr::loadAdrById(" + _key.toString() + "): begin\n") ;
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
		_debugL( 0x00000001, "mainAdr.js::scrAdr::loadAdrById(): end\n") ;
	} ;
	/**
	 *
	 */
	_debugL( 0x00000001, "mainCuComm.js::regModPSuOrdr(): assigning fncShow function\n") ;
	this.fncShow	=	function( _response) {
		reference	=	_response.getElementsByTagName( "Reference")[0] ;
		if ( reference) {
			objClass	=	_response.getElementsByTagName( "ObjectClass")[0].childNodes[0].nodeValue ;
			objKey		=	_response.getElementsByTagName( "ObjectKey")[0].childNodes[0].nodeValue ;
			screenLinkTo( objClass, objKey) ;
		} else {
			showPSuOrdr( _response) ;
			showPSuOrdrLief( _response) ;
			this.dtvPSuOrdrItems.primObjKey	=	this.keyField.value ;
			this.dtvPSuOrdrItems.show( _response) ;
		}
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainCuComm.js::regModPSuOrdr(): assigning newSuOrdr function\n") ;
	this.newSuOrdr	=	function() {
		confAction( this, '/ModPurchase/PSuOrdr/confSuOrdrFromPSuOrdr.php', 'newSuOrdr') ;
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainCuComm.js::regModPSuOrdr(): assigning getPOSTData function\n") ;
	this.getPOSTData	=	function() {
		_debugL( 0x00000001, "scrPSuOrdr::scrPSuOrdr::getPOSTData(): begin\n") ;
		post	=	"&_IPrefix="+encodeURIComponent( dijit.byId( "_RPrefixPSuOrdr").get( "value")) ;
		post	+=	"&_IPostfix="+encodeURIComponent( dijit.byId( "_RPostfixPSuOrdr").get( "value")) ;
		_debugL( 0x00000001, "scrPSuOrdr::scrPSuOrdr::getPOSTData(): end\n") ;
		return post ;
	} ;
	/**
	 * 
	 */
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
	hookPrevObject() ;
	_debugL( 0x00000001, "mainCuComm.js::regModPSuOrdr(): end\n") ;
}
/**
 *
 */
function	showPSuOrdr( _response) {
	var	attrs ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	suOrdr	=	_response.getElementsByTagName( "PSuOrdr")[0] ;
	if ( suOrdr) {

		myPSuOrdrNo	=	_response.getElementsByTagName( "PSuOrdrNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	_response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	_response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

//		keyField	=	getFormField( "CoreKeyData", "_IKey") ;
//		data1Field	=	getFormField( "CoreKeyData", "_DData1") ;
//		data2Field	=	getFormField( "CoreKeyData", "_DData2") ;
//		keyField.value	=	myPSuOrdrNo ;
//		data1Field.value	=	_response.getElementsByTagName( "FirmaName1")[0].childNodes[0].nodeValue ;
//		data2Field.value	=	_response.getElementsByTagName( "FirmaName2")[0].childNodes[0].nodeValue ;
//		this.lastKey	=	keyField.value ;
//		this.lastData1	=	data1Field.value ;
//		this.lastData2	=	data2Field.value ;

		attrs	=	suOrdr.childNodes ;
		dispAttrs( attrs, "PSuOrdrKeyData") ;
		dispAttrs( attrs, "formPSuOrdrMain") ;
		dispAttrs( attrs, "formPSuOrdrModi") ;
		dijit.byId( "_RPrefixPSuOrdr").set( "value", _response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue) ;
		dijit.byId( "_RPostfixPSuOrdr").set( "value", _response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue) ;

//		pdfDocument	=	document.getElementById( "pdfDocument") ;
//		pdfDocument.innerHTML	=	"<a href=\"/Archiv/PSuOrdr/" + myPSuOrdrNo + ".pdf\" target=\"pdf\"><img src=\"/rsrc/gif/pdficon_large.gif\" title=\"PDF Dokument in NEUEM Fenster anzeigen\" /></a>" ;
	}
}

/**
 *
 */
function	showPSuOrdrLief( _response) {
	var	lief ;
	var	attrs ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	clearForm( "formPSuOrdrLief") ;
	lief	=	_response.getElementsByTagName( "Lief")[0] ;
	if ( lief) {
		attrs	=	lief.childNodes ;
		dispAttrs( attrs, "formPSuOrdrLief", false) ;

		liefKontakt	=	_response.getElementsByTagName( "LiefKontakt")[0] ;
		if ( liefKontakt) {
			attrs	=	liefKontakt.childNodes ;
			dispAttrs( attrs, "formPSuOrdrLiefKontakt", false) ;
		}
	}
}