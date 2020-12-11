/**
 *
 */
var	selCuRMADialog	=	null ;
var	selCuRMAPkg	=	"" ;
var	selCuRMACbCls	=	"" ;
var	selCuRMACbKey	=	"" ;
var	selCuRMACbFnc	=	"" ;
var	selCuRMACbShow	=	null ;

/**
*
*/
function	selCuRMA( _pkg, _cbCls, _cbKey, _cbFnc, _cbShow) {

	selCuRMAPkg	=	_pkg ;
	selCuRMACbCls	=	_cbCls ;
	selCuRMACbKey	=	_cbKey ;
	selCuRMACbFnc	=	_cbFnc ;
	selCuRMACbShow	=	_cbShow ;
	
	if ( selCuRMADialog == null) {
		selCuRMADialog	=	new dijit.Dialog( {
								title:	"Auswahl CuRMA",
								duration:	100,
								href:	"ModRMA/CuRMA/selCuRMA.php"
							} ) ;
	}
	selCuRMADialog.show() ;
}

/**
 *
 */
function	refSelCuRMA( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	"/ModRMA/CuRMA/selCuRMA_action.php?"
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
			refSelCuRMAReply( response) ;
		}
	} ) ;
	return false ;
}

function	selCuRMAFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelCuRMA( "ModRMA", "CuRMA", "refSelCuRMA", _form) ;
}

function	selCuRMAPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelCuRMA( "ModRMA", "CuRMA", "refSelCuRMA", _form) ;
}

function	selCuRMANextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelCuRMA( "ModRMA", "CuRMA", "refSelCuRMA", _form) ;
}

function	selCuRMALastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelCuRMAReply( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableSelCuRMA	=	document.getElementById( "selCuRMA") ;
	tableSelCuRMA.innerHTML	=	"kdjhfkljdfhalksjd" ;

	myData	=	"NEUE DATEN:<br/>" ;
	CuRMAn	=	response.getElementsByTagName( "CuRMA")[0] ;
	if ( CuRMAn) {
		listeCuRMA	=	response.getElementsByTagName( "CuRMA") ;
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>CuRMA Nr.</th>" ;
		myData	+=	"<th>Datum</th>" ;
		myData	+=	"<th>Status</th>" ;
		myData	+=	"<th>Name</th>" ;
		myData	+=	"<th>PLZ</th>" ;
		myData	+=	"<th>Ansprechpartner</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeCuRMA.length && i < 20 ; i++) {
			CuRMA	=	response.getElementsByTagName( "CuRMA")[i] ;
			fncData	=	CuRMA.childNodes ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + CuRMA.getElementsByTagName( "Id")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + CuRMA.getElementsByTagName( "CuRMANr")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + CuRMA.getElementsByTagName( "Datum")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + CuRMA.getElementsByTagName( "Status")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + CuRMA.getElementsByTagName( "FirmaName1")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + CuRMA.getElementsByTagName( "PLZ")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>"
						+ CuRMA.getElementsByTagName( "Vorname")[0].childNodes[0].nodeValue + " "
						+ CuRMA.getElementsByTagName( "Name")[0].childNodes[0].nodeValue
						+ "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selCuRMAByCuRMANr('" + CuRMA.getElementsByTagName( "CuRMANr")[0].childNodes[0].nodeValue + "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	tableSelCuRMA.innerHTML	=	myData ;
}

/**
 *
 */
function	selCuRMAByCuRMANr( cuRMANr) {
	selCuRMADialog.hide() ;
	requestUni( 'ModRMA', selCuRMACbCls, '/Common/hdlObject.php', selCuRMACbFnc, cuRMANr, '', '', null, selCuRMACbShow) ;
	return false ;
}

