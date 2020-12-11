/**
 * regModKdMahn
 * 
 * registers the module in the central database
 */
function	regModKdMahn() {
	_debugL( 0x00000001, "regModKdMahn: \n") ;
	myScreen.screenAdd( "screenKdMahn", linkKdMahn, "KdMahn", "KdMahnKeyData", "_IKdMahnNr", showKdMahnAll, null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"KdMahn" ;
	myScreen.coreObject	=	"KdMahn" ;
	myScreen.showFunc	=	showKdMahnAll ;
	myScreen.keyField	=	getFormField( 'KdMahnKeyData', '_IKdMahnNr') ;
	// make sure everything is setup fo this module, e.g. menuitems enables/disables etc.
	myScreen.link() ;
	if ( pendingKey != "") {
		requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showKdMahnAll) ;
	}
	pendingKey	=	"" ;
}
function	linkKdMahn() {
	_debugL( 0x00000001, "linkKdMahn: \n") ;
}
/**
 *
 */
function	showKdMahnAll( response) {
	showKdMahn( response) ;
	showKdMahnKunde( response) ;
//	showTableKdMahnPosten( response) ;
//	showKdMahnVersand( response) ;
}

/**
 *
 */
function	showKdMahn( response) {
	var	lockInfo ;
	var	kunde ;
	var	attrs ;
	var	myKdMahnNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	KdMahn	=	response.getElementsByTagName( "KdMahn")[0] ;
	if ( KdMahn) {

		myKdMahnNr	=	response.getElementsByTagName( "KdMahnNr")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	KdMahn.childNodes ;
		dispAttrs( attrs, "KdMahnKeyData") ;
		dispAttrs( attrs, "formKdMahnMain") ;
		dispAttrs( attrs, "formKdMahnModi") ;
		dispAttrs( attrs, "formKdMahnDocEMail") ;
		dispAttrs( attrs, "formKdMahnDocFAX") ;
		dispAttrs( attrs, "formKdMahnDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateKdMahn") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'lock', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '', null, showKdMahn) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'Base', 'KdMahn', '/Common/hdlObject.php', 'unlock', document.forms['KdMahnKeyData']._IKdMahnNr.value, '', '', null, showKdMahn) ; \" />" ;
		}

		/**
		 *
		 */
		showKdMahnDocInfo( response) ;

	}
}

function	showKdMahnDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfKdMahn") ;
	var pdfNode	=	response.getElementsByTagName( "Document")[0] ;
	if ( pdfNode) {
		var pdfRef	=	response.getElementsByTagName( "Document")[0].childNodes[0].nodeValue ;
		if ( pdfRef != "") {
			pdfDocument.innerHTML	=	"<a href=\"" + pdfRef + "\" target=\"pdf\"><img src=\"/rsrc/gif/pdficon_large.gif\" /></a>" ;
		} else {
			pdfDocument.innerHTML	=	"" ;
		}
	}
}

/**
 *
 */
function	showKdMahnKunde( response) {
	var	lockInfo ;
	var	kunde ;
	var	attrs ;
	var	myKdMahnNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	clearForm( "formKdMahnKunde") ;

	kunde	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( kunde) {
		attrs	=	kunde.childNodes ;
		dispAttrs( attrs, "formKdMahnKunde", false) ;

		kundeKontakt	=	response.getElementsByTagName( "KundeKontakt")[0] ;
		if ( kundeKontakt) {
			attrs	=	kundeKontakt.childNodes ;
			dispAttrs( attrs, "formKdMahnKundeKontakt", false) ;
		}
	}
}

/**
 *
 */
function	showTableKdMahnPosten( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divKdMahnPosten	=	document.getElementById( "TableKdMahnPosten") ;
	divKdMahnPosten.innerHTML	=	"" ;

	myData	=	"" ;
	listeKdMahnPosten	=	response.getElementsByTagName( "KdMahnPosten") ;
	if ( listeKdMahnPosten) {
		myData	+=	"listeKdMahnPosten contains " + listeKdMahnPosten.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Pos.</th>" ;
		myData	+=	"<th>Rechnung Nr..</th>" ;
		myData	+=	"<th>Preis</th>" ;
		myData	+=	"<th>Mwst.</th>" ;
		myData	+=	"<th>Gesamtpreis</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeKdMahnPosten.length ; i++) {
			KdMahnPosten	=	response.getElementsByTagName( "KdMahnPosten")[i] ;
			myId	=	KdMahnPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue ;
			myKdMahnNr	=	KdMahnPosten.getElementsByTagName( "KdMahnNr")[0].childNodes[0].nodeValue ;
			myKdRech	=	KdMahnPosten.getElementsByTagName( "KdRechNr")[0].childNodes[0].nodeValue ;
			myPreis	=	KdMahnPosten.getElementsByTagName( "Preis")[0].childNodes[0].nodeValue ;
			myMwst	=	KdMahnPosten.getElementsByTagName( "Mwst")[0].childNodes[0].nodeValue ;
			myPreis	=	KdMahnPosten.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + KdMahnPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + KdMahnPosten.getElementsByTagName( "PosNr")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>"
						+ btnLinkTo( "screenKdRechRech", KdMahnPosten.getElementsByTagName( "KdRechNr")[0].childNodes[0].nodeValue)
						+ KdMahnPosten.getElementsByTagName( "KdRechNr")[0].childNodes[0].nodeValue
						+ "</td>" ;
			myLine	+=	"<td>" + KdMahnPosten.getElementsByTagName( "Preis")[0].childNodes[0].nodeValue.replace( ".", ",") + "</td>" ;
			myLine	+=	"<td>(" + KdMahnPosten.getElementsByTagName( "Mwst")[0].childNodes[0].nodeValue.replace( ".", ",") + ")</td>" ;
			myLine	+=	"<td>" + KdMahnPosten.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue.replace( ".", ",") + "</td>" ;
			myLine	+=	editButton( 'Base', 'KdMahn', '/Base/KdMahn/editorKdMahnPosten.php', 'getPostenAsXML', document.forms['KdMahnKeyData']._IKdMahnNr.value, KdMahnPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, 'showTableKdMahnPosten', 'KdMahnPosten') ;
			myLine	+=	moveDownButton( 'Base', 'KdMahn', '/Common/hdlObject.php', 'movePosDown', myKdMahnNr, myId, '', null, 'showTableKdMahnPosten') ;
			myLine	+=	moveUpButton( 'Base', 'KdMahn', '/Common/hdlObject.php', 'movePosUp', myKdMahnNr, myId, '', null, 'showTableKdMahnPosten') ;
			myLine	+=	deleteButton( 'Base', 'KdMahn', '/Common/hdlObject.php', 'delPos', myKdMahnNr, myId, '', null, 'showTableKdMahnPosten') ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divKdMahnPosten.innerHTML	=	myData ;
}

/**
*
*/
function	showKdMahnVersand( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myKdMahnNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	KdMahn	=	response.getElementsByTagName( "KdMahn")[0] ;
	if ( KdMahn) {
		attrs	=	KdMahn.childNodes ;
		dispAttrs( attrs, "formKdMahnDocEMail") ;
		dispAttrs( attrs, "formKdMahnDocFAX") ;
		dispAttrs( attrs, "formKdMahnDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formKdMahnDocEMail") ;
		dispAttrs( attrs, "formKdMahnDocFAX") ;
		dispAttrs( attrs, "formKdMahnDocPDF") ;
	}
}

function	showKdMahnDocList( response) {
	showDocList( response, "TableKdMahnDocs") ;
}
function	showEMailKdMahn() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/Base/KdMahn/getAnschreiben.php?KdMahnNr="+document.forms['KdMahnKeyData']._IKdMahnNr.value
	} ) ;
	dlgPreview.show() ;
}

function	hideEMailKdMahn() {
	dlgPreview.hide() ;
}

