/**
 * 
 * 
 * 
 */
function	scrSuOrdr() {
	_debugL( 0x00000001, "mainSuOrdr.js::scrSuOrdr.__constructor(): begin\n") ;
	Screen.call( this, "SuOrdr") ;
	this.package	=	"ModPurchase" ;
	this.module	=	"SuOrdr" ;
	this.coreObject	=	"SuOrdr" ;
	this.keyForm	=	"SuOrdrKeyData" ;
	this.keyField	=	getFormField( 'SuOrdrKeyData', '_ISuOrdrNo') ;
	this.delConfDialog	=	"/ModSales/SuOrdr/confSuOrdrDel.php" ;
	/**
	 * create the selector
	 */
	this.select	=	new selector( this, 'selSuOrdr', 'ModPurchase', '/ModPurchase/SuOrdr/selSuOrdr.php', 'SuOrdr') ;
	/**
	 * create the selector for the article sales 
	 */
	this.selArticlePP	=	new selector( this, "selArticlePP", 'ModBase', '/ModBase/Artikel/selArtikelEK.php', 'Artikel', 'ArtikelEKPreis') ;
	this.selArticlePP.dtv.phpGetCall	=	"getPPList" ;
	this.selArticlePP.selected	=	function( _id) {
		_debugL( 0x00000001, "Selected PP Id " + _id + "\n") ;
		this.parent.dispatch( true, 'addPos', this.parent.keyField.value, _id, 1) ;
	} ;
	/**
	 * 
	 */
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
	this.dtvSuOrdrItems = new dataTableView( this, "dtvSuOrdrItems", "TableSuOrdrItems", "SuOrdr", "SuOrdrItem", null, "ModPurchase", "SuOrdr") ; 
	this.dtvSuOrdrItems.f1 = "formSuOrdrItemsTop" ; 
	/**
	 * 
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "scrSuOrdr::scrSuOrdr::fncLink(): begin\n") ;
		_debugL( 0x00000001, "scrSuOrdr::scrSuOrdr::fncLink(): end\n") ;
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
	_debugL( 0x00000001, "mainCuComm.js::regModPSuOrdr(): assigning fncShow function\n") ;
	this.fncShow	=	function( _response) {
		reference	=	_response.getElementsByTagName( "Reference")[0] ;
		if ( reference) {
			objClass	=	_response.getElementsByTagName( "ObjectClass")[0].childNodes[0].nodeValue ;
			objKey		=	_response.getElementsByTagName( "ObjectKey")[0].childNodes[0].nodeValue ;
			screenLinkTo( objClass, objKey) ;
		} else {
			showSuOrdr( _response) ;
			showSuOrdrLief( _response) ;
			this.dtvSuOrdrItems.primObjKey	=	this.keyField.value ;
			this.dtvSuOrdrItems.show( _response) ;
		}
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainSuOrdr.js::regModSuOrdr(): assigning newSuDlvr function\n") ;
	this.newSuDlvr	=	function() {
		confAction( this, '/ModPurchase/SuOrdr/confSuDlvrFromSuOrdr.php', 'newSuDlvr') ;
	} ;
	/**
	 * 
	 */
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
	hookNextObject() ;
	_debugL( 0x00000001, "scrSuOrdr.js::scrSuOrdr.__constructor(): end \n") ;
}
/**
 *
 */
function	showSuOrdr( _response) {
	var	lockInfo ;
	var	attrs ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	suOrdr	=	_response.getElementsByTagName( "SuOrdr")[0] ;
	if ( suOrdr) {

		mySuOrdrNo	=	_response.getElementsByTagName( "SuOrdrNo")[0].textContent ;
		myPrefix	=	_response.getElementsByTagName( "Prefix")[0].textContent ;
		myPostfix	=	_response.getElementsByTagName( "Postfix")[0].textContent ;

		attrs	=	suOrdr.childNodes ;
		dispAttrs( attrs, "SuOrdrKeyData") ;
		dispAttrs( attrs, "formSuOrdrMain") ;
		dispAttrs( attrs, "formSuOrdrModi") ;
		dispAttrs( attrs, "formSuOrdrDocUpload") ;

		lockInfo	=	document.getElementById( "lockStateSuOrdr") ;
		myLockState	=	parseInt( _response.getElementsByTagName( "LockState")[0].textContent) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'lock', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', '', null, showSuOrdr) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'unlock', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, '', '', null, showSuOrdr) ; \" />" ;
		}

		/**
		 *
		 */
		showSuOrdrDocInfo( _response) ;
	}
}

/**
 *
 */
function	showSuOrdrDocInfo( _response) {
	var pdfDocument	=	document.getElementById( "pdfSuOrdr") ;
	var pdfNode	=	_response.getElementsByTagName( "Document")[0] ;
	if ( pdfNode) {
		var pdfRef	=	_response.getElementsByTagName( "Document")[0].textContent ;
		if ( pdfRef != "") {
			pdfDocument.innerHTML	=	"<a href=\"" + pdfRef + "\" target=\"pdf\"><img src=\"/Rsrc/gif/pdficon_large.gif\" /></a>" ;
		} else {
			pdfDocument.innerHTML	=	"" ;
		}
	}
}

/**
 *
 */
function	showSuOrdrLief( _response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	mySuOrdrNo ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	clearForm( "formSuOrdrLief") ;
	lief	=	_response.getElementsByTagName( "Lief")[0] ;
	if ( lief) {
		attrs	=	lief.childNodes ;
		dispAttrs( attrs, "formSuOrdrLief", false) ;

		liefKontakt	=	_response.getElementsByTagName( "LiefKontakt")[0] ;
		if ( liefKontakt) {
			attrs	=	liefKontakt.childNodes ;
			dispAttrs( attrs, "formSuOrdrLiefKontakt", false) ;
		}
	}
}

function	showSuOrdrDocUpload( _response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	mySuOrdrNo ;
	myField	=	getFormField( "formSuOrdrDocUpload", "_DRefNr") ;
	myField.value	=	_response.getElementsByTagName( "SuOrdrNo")[0].childNodes[0].nodeValue ;
}
/**
 *
 */
function	showTableSuOrdrItem( _response) {
	updTableHead( _response, "formSuOrdrItemTop", "formSuOrdrItemBot") ;
	showTable( _response, "TableSuOrdrItem", "SuOrdrItem", "SuOrdr", document.forms['SuOrdrKeyData']._ISuOrdrNo.value, "showSuOrdrAll", "refreshTableSuOrdrItem") ;
}
function	refreshTableSuOrdrItem( _response) {
	refreshTable( _response, "TableSuOrdrItem", "SuOrdrItem", "SuOrdr", document.forms['SuOrdrKeyData']._ISuOrdrNo.value, "showSuOrdrAll") ;
}
/**
 *
 */
function	showSuOrdrVersand( _response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	mySuOrdrNo ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	suOrdr	=	_response.getElementsByTagName( "SuOrdr")[0] ;
	if ( suOrdr) {
		attrs	=	suOrdr.childNodes ;
		dispAttrs( attrs, "formSuOrdrDocEMail") ;
		dispAttrs( attrs, "formSuOrdrDocFAX") ;
		dispAttrs( attrs, "formSuOrdrDocPDF") ;
	}
	lief	=	_response.getElementsByTagName( "Lief")[0] ;
	if ( suOrdr) {
		attrs	=	lief.childNodes ;
		dispAttrs( attrs, "formSuOrdrDocEMail") ;
		dispAttrs( attrs, "formSuOrdrDocFAX") ;
		dispAttrs( attrs, "formSuOrdrDocPDF") ;
	}
}
function	showSuOrdrDocList( _response) {
	showDocList( _response, "TableSuOrdrDocs") ;
}
/**
 * 
 * @param _mod
 * @param _app
 * @param _fnc
 * @param _key
 * @param _subkey
 * @return
 */
function	setSuOrdrLief( _mod, _app, _fnc, _key, _subkey) {
	fieldSuOrdrNo	=	getFormField( "formSuOrdrMain", "_ISuOrdrNo") ;
	fieldLiefNr	=	getFormField( "formSuOrdrMain", "_ILiefNr") ;
	fieldLiefNr.value	=	_key ;
	fieldLiefKontaktNr	=	getFormField( "formSuOrdrMain", "_ILiefKontaktNr") ;
	fieldLiefKontaktNr.value	=	_subkey ;
	requestUni( 'Base', 'SuOrdr', 'mainSuOrdr_action.php', 'updSuOrdr', fieldSuOrdrNo.value, '', '', 'formSuOrdrMain', showSuOrdrLief) ;

}
/**
 * 
 * @param _suOrdrNo
 * @param _p2
 * @return
 */
function	saveSuOrdr ( _suOrdrNo, _p2) {
	requestUniA( 'Base', 'SuOrdr', '/Common/hdlObject.php', 'upd', _suOrdrNo, '', '', new Array('formSuOrdrMain', 'formSuOrdrModi'), showSuOrdr) ;
	return false ;
}
/**
 * 
 * @return
 */
function	hideEMailSuOrdr() {
	dlgPreview.hide() ;
}
/**
 * 
 */
var     markerSuOrdrNo ;
function        newSuDlvrFromSuOrdr() {
	markerSuOrdrNo  =       document.forms['SuOrdrKeyData']._ISuOrdrNo.value ;
	confAction( '/Base/SuOrdr/confSuDlvrFromSuOrdr.php', doNewSuDlvrFromSuOrdr) ;
}
function        doNewSuDlvrFromSuOrdr() {
	confDialog.hide() ;
	screenLinkTo( "screenSuDlvr", "", _newSuDlvrFromSuOrdr) ;
}
function	_newSuDlvrFromSuOrdr() {
	requestUni( 'Base', 'SuDlvr', '/Common/hdlObject.php', 'newFromSuOrdr', '', '', markerSuOrdrNo, null, showSuDlvrAll) ;
}
function	SuOrdr_selArtikelEK() {
	myGET	=	"?LiefNr=" + document.forms['formSuOrdrMain']._ILiefNr.value + "" ;
	selArtikelEK( 'Base', 'SuOrdr', document.forms['SuOrdrKeyData']._ISuOrdrNo.value, 'addPos', showTableSuOrdrItem, myGET) ;
}
/**
 * 
 * @return
 */
function	showEMailSuOrdr() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/Base/SuOrdr/getAnschreiben.php?SuOrdrNo="+document.forms['SuOrdrKeyData']._ISuOrdrNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailSuOrdr() {
	dlgPreview.hide() ;
}
function	refSuOrdrItem( _rng) {
	requestUni( 'ModBase', 'SuOrdr', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['SuOrdrKeyData']._ISuOrdrNo.value,
			_rng,
			'SuOrdrItem',
			'formSuOrdrItemTop',
			showTableSuOrdrItem) ;
	return false ; 	
}
