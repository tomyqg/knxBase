/**
 *
 */
var	selProjDialog	=	null ;
var	selProjPkg	=	"" ;
var	selProjCbCls	=	"" ;
var	selProjCbKey	=	"" ;
var	selProjCbFnc	=	"" ;
var	selProjCbShow	=	null ;

/**
*
*/
function	selProj( _pkg, _cbCls, _cbKey, _cbFnc, _cbShow) {

	selProjPkg	=	_pkg ;
	selProjCbCls	=	_cbCls ;
	selProjCbKey	=	_cbKey ;
	selProjCbFnc	=	_cbFnc ;
	selProjCbShow	=	_cbShow ;
	
	if ( selProjDialog == null) {
		selProjDialog	=	new dijit.Dialog( {
								title:	"<?php echo FTr::tr( "Select Project") ; ?>",
								duration:	100,
								href:	"ModProj/Proj/selProj.php"
							} ) ;
	}
	selProjDialog.show() ;
}

/**
 *
 */
function	refSelProj( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	"/ModProj/Proj/selProj_action.php?"
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
			refSelProjReply( response) ;
		}
	} ) ;
	return false ;
}

function	selProjFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelProj( "ModProj", "Proj", "refSelProj", _form) ;
}

function	selProjPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelProj( "ModProj", "Proj", "refSelProj", _form) ;
}

function	selProjNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelProj( "ModProj", "Proj", "refSelProj", _form) ;
}

function	selProjLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelProjReply( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableSelProj	=	document.getElementById( "selProj") ;
	tableSelProj.innerHTML	=	"kdjhfkljdfhalksjd" ;

	myData	=	"NEUE DATEN:<br/>" ;
	Proj	=	response.getElementsByTagName( "Proj")[0] ;
	if ( Proj) {
		listeProj	=	response.getElementsByTagName( "Proj") ;
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Proj Nr.</th>" ;
		myData	+=	"<th>Datum</th>" ;
		myData	+=	"<th>Name</th>" ;
		myData	+=	"<th>Ansprechpartner</th>" ;
		myData	+=	"<th>Status</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeProj.length && i < 20 ; i++) {
			Proj	=	response.getElementsByTagName( "Proj")[i] ;
			fncData	=	Proj.childNodes ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + Proj.getElementsByTagName( "Id")[0].textContent + "</td>" ;
			myLine	+=	"<td>" + Proj.getElementsByTagName( "ProjNr")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>" + Proj.getElementsByTagName( "Datum")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>" + Proj.getElementsByTagName( "FirmaName1")[0].textContent + "</td>" ;
			myLine	+=	"<td>" + Proj.getElementsByTagName( "PLZ")[0].textContent + "</td>" ;
			myLine	+=	"<td>"
				+ Proj.getElementsByTagName( "Vorname")[0].childNodes[0].nodeValue + " "
				+ Proj.getElementsByTagName( "Name")[0].childNodes[0].nodeValue
				+ "</td>" ;
			myLine	+=	"<td>" + Proj.getElementsByTagName( "Status")[0].textContent + "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selProjByProjNr('" + Proj.getElementsByTagName( "ProjNr")[0].textContent + "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	tableSelProj.innerHTML	=	myData ;
}

/**
*
*/
function	selProjByProjNr( projNr) {
	selProjDialog.hide() ;
	requestUni( 'Base', selProjCbCls, '/Common/hdlObject.php', selProjCbFnc, projNr, '', '', null, selProjCbShow) ;
	return false ;
}

