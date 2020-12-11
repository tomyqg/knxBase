<?php 
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
?>
/**
 *
 */
var	selKatGrDialog	=	null ;
var	selKatGrMod	=	"" ;
var	selKatGrApp	=	"" ;
var	selKatGrFnc	=	"" ;
var	selKatGrCb	=	null ;

function	selKatGr( _mod, _app, _fnc, _key, _cb) {

	selKatGrMod	=	_mod ;
	selKatGrApp	=	_app ;
	selKatGrFnc	=	_fnc ;
	selKatGrCb	=	_cb ;

	if ( selKatGrDialog == null) {
		selKatGrDialog	=	new dijit.Dialog( {
								title:	"<?php echo FTr::tr( "Select Catalog Groupe") ; ?>",
								duration:	100,
								href:	"/ModMisc/KatGr/selKatGr.php"
							} ) ;
	}
	selKatGrDialog.show() ;
}

/**
 *
 */
function	refSelKatGr( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	_mod + "/" + _app + "/"
					+ "selKatGr_action.php?"
					+ "&_fnc=" + _fnc
					;
	/**
	 *
	 */
	postVars	=	getPOSTData( _form) ;
	dojo.xhrPost( {
		url: url,
		handleAs: "xml",
		postData: postVars,
		load: function( response) {
			refSelKatGrReply( response) ;
		}
	} ) ;
	return false ;
}

function	selKatGrFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelKatGr( "ModMisc", "KatGr", "refSelKatGr", _form) ;
}

function	selKatGrPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelKatGr( "ModMisc", "KatGr", "refSelKatGr", _form) ;
}

function	selKatGrNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelKatGr( "ModMisc", "KatGr", "refSelKatGr", _form) ;
}

function	selKatGrLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelKatGrReply( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableSelKatGr	=	document.getElementById( "selKatGr") ;

	myData	=	"NEUE DATEN:<br/>" ;
	KatGr	=	response.getElementsByTagName( "KatGr")[0] ;
	if ( KatGr) {
		listeKatGr	=	response.getElementsByTagName( "KatGr") ;
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Produkt GruppeGr Nr.</th>" ;
		myData	+=	"<th>Name</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeKatGr.length && i < 20 ; i++) {
			KatGr	=	response.getElementsByTagName( "KatGr")[i] ;
			fncData	=	KatGr.childNodes ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + KatGr.getElementsByTagName( "Id")[0].textContent + "</td>" ;
			myLine	+=	"<td>" + KatGr.getElementsByTagName( "KatGrNr")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>" + KatGr.getElementsByTagName( "KatGrName")[0].textContent + "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selKatGrByKatGrNr('" + KatGr.getElementsByTagName( "KatGrNr")[0].textContent + "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	tableSelKatGr.innerHTML	=	myData ;
}

/**
 *
 */
function	selKatGrByKatGrNr( kundeNr, kundeKontaktNr) {
//	selKatGrCb( selKatGrMod, selKatGrApp, selKatGrFnc, kundeNr, kundeKontaktNr) ;
	selKatGrDialog.hide() ;
	requestUni( selKatGrMod, selKatGrApp, '/Common/hdlObject.php', selKatGrFnc, kundeNr, '', '', null, selKatGrCb) ;
	return false ;
}

