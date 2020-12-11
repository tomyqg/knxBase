/**
 * 
 */
var	selDocumentDialog = null ;
var	selDocumentMod = "" ;
var	selDocumentApp = "" ;
var	selDocumentFnc = "" ;
var	selDocumentCb = null ;

/**
 *
 */
function	selDocument( _mod, _app, _fnc, _key, _cb) {

	selDocumentMod	=	_mod ;
	selDocumentApp	=	_app ;
	selDocumentFnc	=	_fnc ;
	selDocumentCb	=	_cb ;
	if ( selDocumentDialog == null) {
		selDocumentDialog	=	new dijit.Dialog( {
								title:	"<?php echo FTr::tr( 'Auswahl Document') ; ?>",
								duration:	100,
								href:	"/Base/Document/selDocument.php"
							} ) ;
	}
	selDocumentDialog.show() ;
}

/**
 *
 */
function	refSelDocument( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	_mod + "/" + _app + "/"
					+ "selDocument_action.php?"
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
			refSelDocumentReply( response) ;
			showStatus( response) ;
		},
		error: function( response) {
			showStatus( response) ;
		}
	} ) ;
	return false ;
}

function	selDocumentFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelDocument( "Base", "Document", "refSelDocument", _form) ;
}

function	selDocumentPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelDocument( "Base", "Document", "refSelDocument", _form) ;
}

function	selDocumentNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelDocument( "Base", "Document", "refSelDocument", _form) ;
}

function	selDocumentLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelDocumentReply( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divSelDocument	=	document.getElementById( "divSelDocument") ;
	divSelDocument.innerHTML	=	"" ;

	myData	=	"<?php echo FTr::tr( "New Data:") ; ?><br/>" ;
	tableDocument	=	response.getElementsByTagName( "Document") ;
	if ( tableDocument) {
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th><?php echo FTr::tr( "Id") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Ref. type") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Ref. no.") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Filename") ; ?></th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableDocument.length && i < 20 ; i++) {
			Document	=	tableDocument[i] ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#ddffdd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + Document.getElementsByTagName( "Id")[0].textContent + "</td>" ;
			myLine	+=	"<td>" + Document.getElementsByTagName( "RefType")[0].textContent + "</td>" ;
			myLine	+=	"<td>" + Document.getElementsByTagName( "RefNr")[0].textContent + "</td>" ;
			myLine	+=	"<td>" + Document.getElementsByTagName( "Filename")[0].textContent + "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selDocumentByDocumentNr('"
						+ Document.getElementsByTagName( "Id")[0].textContent
						+ "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	divSelDocument.innerHTML	=	myData ;
}

/**
 *
 */
function	selDocumentByDocumentNr( DocumentNr) {
	selDocumentDialog.hide() ;
	requestUni( selDocumentMod, selDocumentApp, '/Common/hdlObject.php', selDocumentFnc, DocumentNr, '', '', null, selDocumentCb) ;
	return false ;
}
