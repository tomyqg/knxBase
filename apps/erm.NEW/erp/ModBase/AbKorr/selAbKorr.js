/**
 *
 */
var	selAbKorrDialog	=	null ;
var	selAbKorrMod	=	"" ;
var	selAbKorrApp	=	"" ;
var	selAbKorrFnc	=	"" ;
var	selAbKorrCb	=	null ;

function	selAbKorr( _mod, _app, _fnc, _key, _cb) {

	selAbKorrMod	=	_mod ;
	selAbKorrApp	=	_app ;
	selAbKorrFnc	=	_fnc ;
	selAbKorrCb	=	_cb ;

	if ( selAbKorrDialog == null) {
		selAbKorrDialog	=	new dijit.Dialog( {
								title:	"<?php echo FTr::tr( "Selection: Stock Correction") ; ?>",
								duration:	100,
								href:	"/Base/AbKorr/selAbKorr.php"
							} ) ;
	}
	selAbKorrDialog.show() ;
}

/**
 *
 */
function	refSelAbKorr( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	_mod + "/" + _app + "/"
					+ "selAbKorr_action.php?"
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
			refSelAbKorrReply( response) ;
		}
	} ) ;
	return false ;
}

function	selAbKorrFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelAbKorr( "Base", "AbKorr", "refSelAbKorr", _form) ;
}

function	selAbKorrPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelAbKorr( "Base", "AbKorr", "refSelAbKorr", _form) ;
}

function	selAbKorrNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelAbKorr( "Base", "AbKorr", "refSelAbKorr", _form) ;
}

function	selAbKorrLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelAbKorrReply( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableSelAbKorr	=	document.getElementById( "selAbKorr") ;

	myData	=	"NEUE DATEN:<br/>" ;
	AbKorr	=	response.getElementsByTagName( "AbKorr")[0] ;
	if ( AbKorr) {
		listeAbKorr	=	response.getElementsByTagName( "AbKorr") ;
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th><?php echo FTr::tr( "Id") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Stock Correction no.") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Date") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Description") ; ?></th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeAbKorr.length && i < 20 ; i++) {
			AbKorr	=	response.getElementsByTagName( "AbKorr")[i] ;
			fncData	=	AbKorr.childNodes ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>"
						+ AbKorr.getElementsByTagName( "Id")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>"
						+ AbKorr.getElementsByTagName( "AbKorrNr")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>"
				+ AbKorr.getElementsByTagName( "Datum")[0].textContent
				+ "</td>" ;
			myLine	+=	"<td>"
				+ AbKorr.getElementsByTagName( "Description")[0].textContent
				+ "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selAbKorrByAbKorrNr('"+ AbKorr.getElementsByTagName( "AbKorrNr")[0].textContent + "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	tableSelAbKorr.innerHTML	=	myData ;
}

/**
 *
 */
function	selAbKorrByAbKorrNr( kundeNr, kundeKontaktNr) {
	selAbKorrDialog.hide() ;
	requestUni( selAbKorrMod, selAbKorrApp, '/Common/hdlObject.php', selAbKorrFnc, kundeNr, '', '', null, selAbKorrCb) ;
	return false ;
}

