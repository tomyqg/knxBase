<?php 
	require_once("config.inc.php");
	Header("content-type: application/x-javascript");
?>
/**
 *
 */
var	selInKonfDialog	=	null ;
var	selInKonfPkg	=	"" ;
var	selInKonfCbCls	=	"" ;
var	selInKonfCbKey	=	"" ;
var	selInKonfCbFnc	=	"" ;
var	selInKonfCbShow	=	null ;

/**
*
*/
function	selInKonf( _pkg, _cbCls, _cbKey, _cbFnc, _cbShow) {

	selInKonfPkg	=	_pkg ;
	selInKonfCbCls	=	_cbCls ;
	selInKonfCbKey	=	_cbKey ;
	selInKonfCbFnc	=	_cbFnc ;
	selInKonfCbShow	=	_cbShow ;
	
	if ( selInKonfDialog == null) {
		selInKonfDialog	=	new dijit.Dialog( {
								title:	"<?php FTr::tr( "Select Assembly Order") ; ?>",
								duration:	100,
								href:	"Base/InKonf/selInKonf.php"
							} ) ;
	}
	selInKonfDialog.show() ;
}

/**
 *
 */
function	refSelInKonf( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	"/Base/InKonf/selInKonf_action.php?"
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
			refSelInKonfReply( response) ;
		}
	} ) ;
	return false ;
}

function	selInKonfFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelInKonf( "Base", "InKonf", "refSelInKonf", _form) ;
}

function	selInKonfPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelInKonf( "Base", "InKonf", "refSelInKonf", _form) ;
}

function	selInKonfNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelInKonf( "Base", "InKonf", "refSelInKonf", _form) ;
}

function	selInKonfLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelInKonfReply( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableSelInKonf	=	document.getElementById( "selInKonf") ;
	tableSelInKonf.innerHTML	=	"kdjhfkljdfhalksjd" ;

	myData	=	"NEUE DATEN:<br/>" ;
	InKonf	=	response.getElementsByTagName( "InKonf")[0] ;
	if ( InKonf) {
		listeInKonf	=	response.getElementsByTagName( "InKonf") ;
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>InKonf Nr.</th>" ;
		myData	+=	"<th>Datum</th>" ;
		myData	+=	"<th>Artikel Nr.</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeInKonf.length && i < 20 ; i++) {
			InKonf	=	response.getElementsByTagName( "InKonf")[i] ;
			fncData	=	InKonf.childNodes ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + InKonf.getElementsByTagName( "Id")[0].textContent + "</td>" ;
			myLine	+=	"<td>" + InKonf.getElementsByTagName( "InKonfNr")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>" + InKonf.getElementsByTagName( "Datum")[0].textContent + "</td>" ;
			myLine	+=	"<td>" + InKonf.getElementsByTagName( "ArtikelNr")[0].textContent + "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selInKonfByInKonfNr('" + InKonf.getElementsByTagName( "InKonfNr")[0].textContent + "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	tableSelInKonf.innerHTML	=	myData ;
}

/**
 *
 */
function	selInKonfByInKonfNr( inKonfNr) {
	requestUni( 'Base', selInKonfCbCls, '/Common/hdlObject.php', selInKonfCbFnc, inKonfNr, '', '', null, selInKonfCbShow) ;
	selInKonfDialog.hide() ;
	return false ;
}

