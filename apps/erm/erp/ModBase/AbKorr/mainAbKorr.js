/**
 * regModAbKorr
 * 
 * registers the module in the central database
 */
function	regModAbKorr() {
	_debug( "regModAbKorr: \n") ;
	myScreen	=	screenAdd( "screenAbKorr", linkAbKorr, "AbKorr", "AbKorrKeyData", "_IAbKorrNr", showAbKorrAll, null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"AbKorr" ;
	myScreen.coreObject	=	"AbKorr" ;
	myScreen.showFunc	=	showAbKorrAll ;
	myScreen.keyField	=	getFormField( 'AbKorrKeyData', '_IAbKorrNr') ;
	myScreen.delConfDialog	=	"/Base/AbKorr/confAbKorrDel.php" ;
	// link to this screen
	myScreen.link() ;
	// process any pending 'link-to-screen# data
	if ( pendingKey != "") {
		requestUni( 'Base', 'AbKorr', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showAbKorrAll) ;
	}
	pendingKey	=	"" ;
}
function	linkAbKorr() {
	_debug( "linkAbKorr: \n") ;
}
function	saveAbKorrAll( _abKorrNr, _p2) {
	abKorrNr	=	getFormField( 'AbKorrKeyData', '_IAbKorrNr').value ;
	requestUniA( 'Base', 'AbKorr', '/Common/hdlObject.php', 'upd', _abKorrNr, '', '', new Array('formAbKorrMain', 'formAbKorrModi'), showAbKorr) ;
	return false ;
}
/**
 *
 */
function	showAbKorrAll( response) {
	showAbKorr( response) ;
	showTableAbKorrPosten( response) ;
}
/**
 *
 */
function	showAbKorr( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myAbKorrNr ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	abKorr	=	response.getElementsByTagName( "AbKorr")[0] ;
	if ( abKorr) {

		myAbKorrNr	=	response.getElementsByTagName( "AbKorrNr")[0].childNodes[0].nodeValue ;

		attrs	=	abKorr.childNodes ;
		dispAttrs( attrs, "AbKorrKeyData") ;
		dispAttrs( attrs, "formAbKorrMain") ;
		dispAttrs( attrs, "formAbKorrItems") ;

//		pdfDocument	=	document.getElementById( "pdfDocument") ;
//		pdfDocument.innerHTML	=	"<a href=\"/Archiv/AbKorr/" + myAbKorrNr + ".pdf\" target=\"pdf\"><img src=\"/rsrc/gif/pdficon_large.gif\" title=\"PDF Dokument in NEUEM Fenster anzeigen\" /></a>" ;
	}
}
/**
*
*/
function	showTableAbKorrPosten( response) {
	showTable( response, "TableAbKorrPosten", "AbKorrPosten", "AbKorr", document.forms['AbKorrKeyData']._IAbKorrNr.value, "showAbKorrAll", "refreshTableAbKorrPosten") ;
}
function	refreshTableAbKorrPosten( response) {
	refreshTable( response, "TableAbKorrPosten", "AbKorrPosten", "AbKorr", document.forms['AbKorrKeyData']._IAbKorrNr.value, "showAbKorrAll") ;
}
/**
 *
 */
function	showTableAbKorrOV( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divAbKorrPosten	=	document.getElementById( "TableAbKorrOV") ;
	divAbKorrPosten.innerHTML	=	"Hello" ;

	myData	=	"" ;
	tableAbKorrPosten	=	response.getElementsByTagName( "AbKorr") ;
	if ( tableAbKorrPosten) {
		myData	+=	"TableResultSet contains " + tableAbKorrPosten.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th><?php echo FTr::tr( "Id") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "AbKorr no.") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Description") ; ?></th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableAbKorrPosten.length ; i++) {
			abKorr	=	response.getElementsByTagName( "AbKorr")[i] ;
			myId	=	abKorr.getElementsByTagName( "Id")[0].childNodes[0].nodeValue ;
			myAbKorrNr	=	abKorr.getElementsByTagName( "AbKorrNr")[0].childNodes[0].nodeValue ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>"
							+ abKorr.getElementsByTagName( "Id")[0].childNodes[0].nodeValue
							+ "</td>" ;
			myLine	+=	"<td>"
							+ btnLinkTo( "screenAbKorr", abKorr.getElementsByTagName( "AbKorrNr")[0].childNodes[0].nodeValue)
							+ abKorr.getElementsByTagName( "AbKorrNr")[0].childNodes[0].nodeValue
							+ "</td>" ;
//			myLine	+=	"<td>" + combine( myBez1, myBez2, myMT) ;
			myLine	+=	"<td>"
							+ abKorr.getElementsByTagName( "Description")[0].childNodes[0].nodeValue
							+ "</td>" ;
			myLine	+=	editButton( 'Base', 'AbKorr', '/Base/AbKorr/editorAbKorrPosten.php', 'getAbKorrPostenAsXML', document.forms['AbKorrKeyData']._IAbKorrNr.value, abKorr.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, 'showTableAbKorrPosten', 'AbKorrPosten') ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divAbKorrPosten.innerHTML	=	myData ;
}
