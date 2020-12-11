/**
 *
 */
var	selStockDialog	=	null ;
var	selStockMod	=	"" ;
var	selStockApp	=	"" ;
var	selStockFnc	=	"" ;
var	selStockCb	=	null ;

function	selStock( _mod, _app, _fnc, _key, _cb) {

	selStockMod	=	_mod ;
	selStockApp	=	_app ;
	selStockFnc	=	_fnc ;
	selStockCb	=	_cb ;

	if ( selStockDialog == null) {
		selStockDialog	=	new dijit.Dialog( {
								title:	"<?php echo FTr::tr( "Selection: Stock") ; ?>",
								duration:	100,
								href:	"/Base/Stock/selStock.php"
							} ) ;
	}
	selStockDialog.show() ;
}

/**
 *
 */
function	refSelStock( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	_mod + "/" + _app + "/"
					+ "selStock_action.php?"
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
			refSelStockReply( response) ;
		}
	} ) ;
	return false ;
}

function	selStockFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelStock( "Base", "Stock", "refSelStock", _form) ;
}

function	selStockPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelStock( "Base", "Stock", "refSelStock", _form) ;
}

function	selStockNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelStock( "Base", "Stock", "refSelStock", _form) ;
}

function	selStockLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelStockReply( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableSelStock	=	document.getElementById( "selStock") ;

	myData	=	"NEUE DATEN:<br/>" ;
	Stock	=	response.getElementsByTagName( "Stock")[0] ;
	if ( Stock) {
		listeStock	=	response.getElementsByTagName( "Stock") ;
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th><?php echo FTr::tr( "Id") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Stock no.") ; ?></th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeStock.length && i < 20 ; i++) {
			Stock	=	response.getElementsByTagName( "Stock")[i] ;
			fncData	=	Stock.childNodes ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>"
						+ Stock.getElementsByTagName( "Id")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>"
						+ Stock.getElementsByTagName( "StockId")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selStockByStockId('"+ Stock.getElementsByTagName( "StockId")[0].textContent + "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	tableSelStock.innerHTML	=	myData ;
}

/**
 *
 */
function	selStockByStockId( kundeNr, kundeKontaktNr) {
	selStockDialog.hide() ;
	requestUni( selStockMod, selStockApp, '/Common/hdlObject.php', selStockFnc, kundeNr, '', '', null, selStockCb) ;
	return false ;
}

