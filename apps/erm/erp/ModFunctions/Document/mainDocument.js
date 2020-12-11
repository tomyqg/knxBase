/**
 * regModDocument
 * 
 * registers the module in the central database
 */
function	regModDocument() {
	_debugL( 0x00000001, "regModDocument: \n") ;
	myScreen	=	screenAdd( "screenDocument", linkDocument, "Document", "DocumentKeyData", "_IDocumentNr", showDocumentAll, null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"Document" ;
	myScreen.coreObject	=	"Document" ;
	myScreen.showFunc	=	showDocumentAll ;
	myScreen.keyField	=	getFormField( 'DocumentKeyData', '_IId') ;
	// link to this screen
	myScreen.link() ;
	// process any pending 'link-to-screen# data
	if ( pendingKey != "") {
		requestUni( 'Base', 'KdRech', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showKdRechAll) ;
	}
	pendingKey	=	"" ;
}
function	linkDocument() {
	_debugL( 0x00000001, "linkDocument: \n") ;
}
/**
 * 
 * @param response
 * @return
 */
function	showDocumentDocList( response) {
	_debugL( 0x00000001, "Hello shit\n") ; 
	showDocListWithEdit( response, "divDocumentList", "showDocumentDocList") ;
}
/**
 *
 */
function	showDocumentAll( response) {
	gotoTab( "DivDocumentTCMain", "DivDocumentTCMainCPMain") ;
	showDocument( response) ;
//	showDocListWithEdit( response, "divDocumentList", "showDocumentAll") ;
	return true ;
}
/**
 *
 */
function	showDocument( response) {
	var	Document ;
	var	attrs ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	Document	=	response.getElementsByTagName( "Document")[0] ;
	attrs	=	Document.childNodes ;
	dispAttrs( attrs, "DocumentKeyData") ;
	dispAttrs( attrs, "formDocumentMain") ;
}
function	cbDocumentDMRes( _lc, _row) {
	var	myRes	=	"" ;
	if ( _lc == -1) {
		startRow	=	_row.getElementsByTagName( "StartRow")[0] ;
		myField	=	getFormField( "formDocumentOV", "_SStartRow") ;
		if ( startRow && myField) {
			myField.value	=	startRow.childNodes[0].nodeValue ;
		}
	} else if ( _lc == 0) {
		myRes	+=	"<th colspan=\"3\">Special Functions</th>" ;
	} else if ( _row.nodeType == 1) {
		if ( _row.getElementsByTagName( "Id")[0]) {
			myId	=	_row.getElementsByTagName( "Id")[0].childNodes[0].nodeValue ;
//			myRes	+=	"<td><form id=\"KdRechPayInfo\" name=\"KdRechPayInfo\" onsubmit=\"return false ;\">" ;
//			myRes	+=	"<input id=\"_IBezahltDatumKDRO\" name=\"_IBezahltDatumKDRO\" size=\"10\"></input>" ;
//			myRes	+=	"<input name=\"_IBezahltBetragKDRO\" value=\"0,00\" size=\"10\"></input>" ;
//			myRes	+=	_btnReqUniNoTD( "/licon/Blue/18/charts05.png", 'Base', 'KdRech', '/Common/hdlObject.php', 'method', 'key', '', '', 'KdRechPayInfo', 'showTableKdRechPosten', 'KdRechPosten') ;
//			myRes	+=	_btnReqUniNoTD( "/licon/Blue/18/charts05.png", 'Base', 'KdRech', '/Common/hdlObject.php', 'method', 'key', '', '', 'KdRechPayInfo', 'showTableKdRechPosten', 'KdRechPosten') ;
//			myRes	+=	_btnReqUniNoTD( "/licon/Blue/18/charts05.png", 'Base', 'KdRech', '/Common/hdlObject.php', 'method', 'key', '', '', 'KdRechPayInfo', 'showTableKdRechPosten', 'KdRechPosten') ;
//			myRes	+=	"</form></td>" ;
		}
	}
	return myRes ;
}
function	copyArtkelNrToCompDocumentNr( _response) {
	Document	=	_response.getElementsByTagName( "Document")[0] ;
	attrs	=	Document.childNodes ;
	myField	=	getFormField( "editorObject", "_ICompDocumentNr") ;
	myField.value	=	_response.getElementsByTagName( "DocumentNr")[0].childNodes[0].nodeValue
}