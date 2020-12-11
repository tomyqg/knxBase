/**
 * regModKatGr
 * 
 * registers the module in the central database
 */
function	regModKatGr() {
	_debugL( 0x00000001, "regModKatGr: \n") ;
	myScreen	=	screenAdd( "screenKatGr", linkKatGr, "KatGr", "KatGrKeyData", "_IKatGrNr", showKatGrAll, null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"KatGr" ;
	myScreen.coreObject	=	"KatGr" ;
	myScreen.showFunc	=	showKatGrAll ;
	myScreen.keyField	=	getFormField( 'KatGrKeyData', '_IKatGrNr') ;
	// link to this screen
	myScreen.link() ;
	// process any pending 'link-to-screen# data
	if ( pendingKey != "") {
		requestUni( 'Base', 'KatGr', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showKatGrAll) ;
	}
	pendingKey	=	"" ;
}
function	linkKatGr() {
	_debugL( 0x00000001, "linkKatGr: \n") ;
}
/**
 * 
 */
function	showKatGrAll( response) {
	showKatGr( response) ;
	showTableKatGrComp( response) ;
	showTableSubKatGr( response) ;
}

/**
 * 
 */
function	showKatGr( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myKatGrNr ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	katGr	=	response.getElementsByTagName( "KatGr")[0] ;
	if ( katGr) {
		attrs	=	katGr.childNodes ;
		dispAttrs( attrs, "KatGrKeyData") ;
		dispAttrs( attrs, "formKatGrMain") ;
		dispAttrs( attrs, "formKatGrTexte") ;
		dispAttrs( attrs, "formKatGrComp") ;
		dispAttrs( attrs, "formKatGrBild") ;
		katGrBildRefDiv	=	document.getElementById( "KatGrBildRefDiv") ;
		katGrBildRefDiv.innerHTML	=	"<img src=\"/Bilder/thumbs/"
										+ response.getElementsByTagName( "PGBildRef")[0].textContent
										+ "\" />" ;
	}
}
/**
*
*/
function	showTableKatGrComp( response) {
	showTable( response, "TableKatGrComp", "KatGrComp", "KatGr", document.forms['KatGrKeyData']._IKatGrNr.value, "showKatGrAll", "refreshTableKatGrComp") ;
}
function	refreshTableKatGrComp( response) {
	refreshTable( response, "TableKatGrComp", "KatGrComp", "KatGr", document.forms['KatGrKeyData']._IKatGrNr.value, "showKatGrAll") ;
}/**
 * 
 */
function	showTableSubKatGr( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divSubKatGr	=	document.getElementById( "tableSubKatGr") ;
	divSubKatGr.innerHTML	=	"" ;

	myData	=	"" ;
	tableSubKatGr	=	response.getElementsByTagName( "SubKatGr") ;
	if ( tableSubKatGr) {
// myData += "TableSubKatGr contains " + tableSubKatGr.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Untergruppe</th>" ;
		myData	+=	"<th>Name</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableSubKatGr.length ; i++) {
			subKatGr	=	response.getElementsByTagName( "SubKatGr")[i] ;
			myId	=	subKatGr.getElementsByTagName( "Id")[0].textContent ;
			myKatGrNr	=	subKatGr.getElementsByTagName( "KatGrNr")[0].textContent ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>"
						+ gotoButton( "gotoKatGr", subKatGr.getElementsByTagName( "KatGrNr")[0].textContent, 'retToKatGr', document.forms['KatGrKeyData']._IKatGrNr.value)
						+ subKatGr.getElementsByTagName( "KatGrNr")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>" + subKatGr.getElementsByTagName( "KatGrName")[0].textContent + "</td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divSubKatGr.innerHTML	=	myData ;
}