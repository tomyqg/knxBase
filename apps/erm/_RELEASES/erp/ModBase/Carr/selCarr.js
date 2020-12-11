/**
 *
 */
var	selCarrDialog = null ;
var	selCarrPkg	=	"" ;
var	selCarrCbCls	=	"" ;
var	selCarrCbKey	=	"" ;
var	selCarrCbFnc	=	"" ;
var	selCarrCbShow	=	null ;

/**
 *	_pkg	Module to be called
 *	_app	Application
 *	_pkg	Module to be called
 */
function	selCarr( _pkg, _cbCls, _cbKey, _cbFnc, _cbShow) {

	selCarrPkg	=	_pkg ;
	selCarrCbCls	=	_cbCls ;
	selCarrCbKey	=	_cbKey ;
	selCarrCbFnc	=	_cbFnc ;
	selCarrCbShow	=	_cbShow ;
	if ( selCarrDialog == null) {
		selCarrDialog	=	new dijit.Dialog( {
								title: "<?php echo FTr::tr( "Select Carrier") ; ?>",
								duration:	100,
								href:	"Base/Carr/selCarr.php"
							} ) ;
	}
	selCarrDialog.show() ;
}

/**
 *
 */
function	refSelCarr( _pkg, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	"/Base/Carr/selCarr_action.php?"
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
			refSelCarrReply( response) ;
		},
		error: function( response) {
			refSelCarrReply( response) ;
		}
	} ) ;
	return false ;
}

function	selCarrFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelCarr( "Base", "Carr", "refSelCarr", _form) ;
}

function	selCarrPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelCarr( "Base", "Carr", "refSelCarr", _form) ;
}

function	selCarrNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelCarr( "Base", "Carr", "refSelCarr", _form) ;
}

function	selCarrLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelCarrReply( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableSelCarr	=	document.getElementById( "divSelCarr") ;

	myData	=	"NEUE DATEN:<br/>" ;
	listeCarr	=	response.getElementsByTagName( "Carr") ;
	if ( listeCarr) {
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th><?php echo FTr::tr( "Id") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Carrier") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Full name") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Select") ; ?></th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeCarr.length && i < 20 ; i++) {
			Carr	=	response.getElementsByTagName( "Carr")[i] ;
			fncData	=	Carr.childNodes ;
			myLine	=	"<tr  onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + Carr.getElementsByTagName( "AKId")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + Carr.getElementsByTagName( "CarrName")[0].childNodes[0].nodeValue
						+ "</td>" ;
			myLine	+=	"<td>" + Carr.getElementsByTagName( "FullName")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selCarrByCarrier('"
						+ Carr.getElementsByTagName( "Carrier")[0].childNodes[0].nodeValue + "', '"
						+ Carr.getElementsByTagName( "CarrOptNr")[0].childNodes[0].nodeValue + "', '"
						+ Carr.getElementsByTagName( "AKId")[0].childNodes[0].nodeValue + "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	tableSelCarr.innerHTML	=	myData ;
}

/**
 *
 */
function	selCarrByCarrier( carrNr, carrOptNr, lkid) {
	selCarrDialog.hide() ;
	if ( selCarrCbCls == "Carr") {
		requestUni( 'Base', selCarrCbCls, '/Common/hdlObject.php', selCarrCbFnc, carrNr, '', '', null, selCarrCbShow) ;
	} else {
		requestUni( 'Base', selCarrCbCls, '/Common/hdlObject.php', selCarrCbFnc, selCarrCbKey, lkid, '', null, selCarrCbShow) ;
	}
	return false ;
}

