/**
 * 
 */
function	scrSuDlvr() {
	Screen.call( this, "SuDlvr") ;
	this.package	=	"ModPurchase" ;
	this.module	=	"SuDlvr" ;
	this.coreObject	=	"SuDlvr" ;
	this.keyForm	=	"SuDlvrKeyData" ;
	this.keyField	=	getFormField( 'SuDlvrKeyData', '_ISuDlvrNo') ;
	this.delConfDialog	=	"/Base/SuDlvr/confSuDlvrDel.php" ;
	/**
	 * create the selector
	 */
	this.selSuDlvr	=	new selector( this, 'selSuDlvr', 'ModPurchase', '/ModPurchase/SuDlvr/selSuDlvr.php', 'SuDlvr') ;
	/**
	 * create the selector for the article sales 
	 */
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
	 * create the selector for the article sales 
	 */
	this.selSupplier	=	new selector( this, 'selLiefKontakt', 'ModBase', '/ModBase/Lief/selLiefKontakt.php', 'Lief', 'Lief') ;
	this.selSupplier.dtv.phpGetCall	=	"getSCList" ;
	this.selSupplier.selected	=	function( _id) {
		_debugL( 0x00000001, "Selected Supplier Contact Id " + _id + "\n") ;
		this.parent.selSupplier.dijitDialog.hide();			
		this.parent.dispatch( true, 'setLiefFromLKId', this.parent.keyField.value, _id, 1) ;
	} ;
	/**
	 * 
	 */
	this.showSelSupplier	=	function() {
		this.selSupplier.parent	=	this ;
		this.selSupplier.show( '', -1, '') ;
	} ;
	/**
	 * create DTV for 'Provisionary Supplier Order Items' 
	 */
	this.dtvSuDlvrItems = new dataTableView( this, "dtvSuDlvrItems", "TableSuDlvrItems", "SuDlvr", "SuDlvrItem", null, "ModPurchase", "SuDlvr") ; 
	this.dtvSuDlvrItems.f1 = "formSuDlvrItemsTop" ; 
	/**
	 * make sure everything is setup fo this module, e.g. menuitems enables/disables etc.
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "mainSuDlvr.js::scrSuDlvr::fncLink(): begin \n") ;
		_debugL( 0x00000001, "mainSuDlvr.js::scrSuDlvr::fncLink(): end \n") ;
	} ;
	/**
	 * 
	 */
	this.onSelect	=	function( _key) {
		_debugL( 0x00000001, "mainSuOrdr.js::scrAdr::loadAdrById(" + _key.toString() + "): begin\n") ;
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
		_debugL( 0x00000001, "mainSuOrdr.js::scrAdr::loadAdrById(): end\n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( _response) {
		reference	=	_response.getElementsByTagName( "Reference")[0] ;
		if ( reference) {
			objClass	=	_response.getElementsByTagName( "ObjectClass")[0].childNodes[0].nodeValue ;
			objKey		=	_response.getElementsByTagName( "ObjectKey")[0].childNodes[0].nodeValue ;
			screenLinkTo( objClass, objKey) ;
		} else {
			showSuDlvr( _response) ;
			showSuDlvrLief( _response) ;
			this.dtvSuDlvrItems.primObjKey	=	this.keyField.value ;
			this.dtvSuDlvrItems.show( _response) ;
			showSuDlvrDocUpload( _response) ;
		}
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainSuOrdr.js::regModSuOrdr(): assigning newSuOrdr function\n") ;
	this.newSuOrdr	=	function() {
		confAction( this, '/ModPurchase/SuDlvr/confSuOrdrFromSuDlvr.php', 'newSuOrdr') ;
	} ;
	/**
	 * 
	 */
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	}
	pendingKey	=	"" ;
	pendingFnc	=	null ;
}
function	showSuDlvr( _response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	mySuDlvrNo ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	suDlvr	=	_response.getElementsByTagName( "SuDlvr")[0] ;
	if ( suDlvr) {

		mySuDlvrNo	=	_response.getElementsByTagName( "SuDlvrNo")[0].textContent ;
		myPrefix	=	_response.getElementsByTagName( "Prefix")[0].textContent ;
		myPostfix	=	_response.getElementsByTagName( "Postfix")[0].textContent ;

		attrs	=	suDlvr.childNodes ;
		dispAttrs( attrs, "SuDlvrKeyData") ;
		dispAttrs( attrs, "formSuDlvrMain") ;
		dispAttrs( attrs, "formSuDlvrModi") ;

		
		lockInfo	=	document.getElementById( "lockStateSuDlvr") ;
		myLockState	=	parseInt( _response.getElementsByTagName( "LockState")[0].textContent) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'Base', 'SuDlvr', '/Common/hdlObject.php', 'lock', document.forms['SuDlvrKeyData']._ISuDlvrNo.value, '', '', null, showSuDlvr) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'Base', 'SuDlvr', '/Common/hdlObject.php', 'unlock', document.forms['SuDlvrKeyData']._ISuDlvrNo.value, '', '', null, showSuDlvr) ; \" />" ;
		}

		pdfDocument	=	document.getElementById( "pdfDocument") ;
		pdfDocument.innerHTML	=	"<a href=\"/Archiv/SuDlvr/" + mySuDlvrNo + ".pdf\" target=\"pdf\"><img src=\"/Rsrc/gif/pdficon_large.gif\" /></a>" ;
	}
}

/**
 *
 */
function	showSuDlvrLief( _response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	mySuDlvrNo ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	clearForm( "formSuDlvrLief") ;
	lief	=	_response.getElementsByTagName( "Lief")[0] ;
	if ( lief) {
		attrs	=	lief.childNodes ;
		dispAttrs( attrs, "formSuDlvrLief", false) ;

		liefKontakt	=	_response.getElementsByTagName( "LiefKontakt")[0] ;
		if ( liefKontakt) {
			attrs	=	liefKontakt.childNodes ;
			dispAttrs( attrs, "formSuDlvrLiefKontakt", false) ;
		}
	}
}
/**
 * 
 * @param _response
 */
function	showSuDlvrDocUpload( _response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	mySuDlvrNo ;
	myField	=	getFormField( "formSuDlvrDocUpload", "_DRefNr") ;
	myField.value	=	_response.getElementsByTagName( "SuDlvrNo")[0].childNodes[0].nodeValue ;
}
/**
 * 
 * @param _response
 */
function	showSuDlvrDocList( _response) {
	showDocList( _response, "TableSuDlvrDocs") ;
}
/**
 * 
 * @param _mod
 * @param _app
 * @param _fnc
 * @param _key
 * @param _subkey
 */
function	setSuDlvrLief( _mod, _app, _fnc, _key, _subkey) {
	fieldSuDlvrNo	=	getFormField( "formSuDlvrMain", "_ISuDlvrNo") ;
	fieldLiefNr	=	getFormField( "formSuDlvrMain", "_ILiefNr") ;
	fieldLiefNr.value	=	_key ;
	fieldLiefKontaktNr	=	getFormField( "formSuDlvrMain", "_ILiefKontaktNr") ;
	fieldLiefKontaktNr.value	=	_subkey ;

	requestUni( 'Base', 'SuDlvr', 'mainSuDlvr_action.php', 'updSuDlvr', fieldSuDlvrNo.value, '', '', 'formSuDlvrMain', showSuDlvrLief) ;

}
/**
 * 
 * @param _suDlvrNo
 * @param _sonst
 * @return
 */
/**
 * 
 */
var     markerSuDlvrNo ;
function        newFromSuDlvr() {
	markerSuDlvrNo  =       document.forms['SuDlvrKeyData']._ISuDlvrNo.value ;
	confAction( '/Base/SuDlvr/confSuOrdrFromSuDlvr.php', doNewSuOrdrFromSuDlvr) ;
}
function        doNewSuOrdrFromSuDlvr() {
	confDialog.hide() ;
	myScreen	=	screenShow( 'screenSuOrdr', _newSuOrdrFromSuDlvr) ;
	if ( myScreen.isLoaded) {
		_newSuOrdrFromSuDlvr() ;
	}
}
function	_newSuOrdrFromSuDlvr() {
	requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'newFromSuDlvr', '', '', markerSuDlvrNo, null, showSuOrdrAll) ;
}
function	refSuDlvrItem( _rng) {
	requestUni( 'ModBase', 'SuDlvr', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['SuDlvrKeyData']._ISuDlvrNo.value,
			_rng,
			'SuDlvrItem',
			'formSuDlvrItemTop',
			showTableSuDlvrItem) ;
	return false ; 	
}
