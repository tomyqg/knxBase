/**
 * regModKdGuts
 * 
 * registers the module in the central database
 */
function	regModKdGuts() {
	_debugL( 0x00000001, "regModKdGuts: \n") ;
	myScreen	=	screenAdd( "screenKdGuts", linkKdGuts, "KdGuts", "KdGutsKeyData", "_IKdGutsNr", showKdGutsAll, null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"KdGuts" ;
	myScreen.coreObject	=	"KdGuts" ;
	myScreen.showFunc	=	showKdGutsAll ;
	myScreen.keyField	=	getFormField( 'KdGutsKeyData', '_IKdGutsNr') ;
	// make sure everything is setup fo this module, e.g. menuitems enables/disables etc.
	myScreen.link() ;
	if ( pendingKey != "") {
		requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showKdGutsAll) ;
	}
	pendingKey	=	"" ;
}
function	linkKdGuts() {
	_debugL( 0x00000001, "linkKdGuts: \n") ;
}
/**
 *
 */
function	showKdGutsAll( response) {
	showKdGuts( response) ;
	showKundeAdrs( response, "formKdGutsKunde", "formKdGutsKundeKontakt", "formKdGutsLiefKunde", "formKdGutsLiefKundeKontakt", "formKdGutsRechKunde", "formKdGutsRechKundeKontakt") ;
	showTableKdGutsPosten( response) ;
	showKdGutsVersand( response) ;
}

/**
 *
 */
function	showKdGuts( response) {
	var	lockInfo ;
	var	kunde ;
	var	attrs ;
	var	myKdGutsNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	kdGuts	=	response.getElementsByTagName( "KdGuts")[0] ;
	if ( kdGuts) {

		myKdGutsNr	=	response.getElementsByTagName( "KdGutsNr")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	kdGuts.childNodes ;
		dispAttrs( attrs, "KdGutsKeyData") ;
		dispAttrs( attrs, "formKdGutsMain") ;
		dispAttrs( attrs, "formKdGutsModi") ;
		dispAttrs( attrs, "formKdGutsDocEMail") ;
		dispAttrs( attrs, "formKdGutsDocFAX") ;
		dispAttrs( attrs, "formKdGutsDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateKdGuts") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'lock', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', null, showKdGuts) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'Base', 'KdGuts', '/Common/hdlObject.php', 'unlock', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', null, showKdGuts) ; \" />" ;
		}

		/**
		 *
		 */
		showKdGutsDocInfo( response) ;

	}
}

function	showKdGutsDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfKdGuts") ;
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
function	showTableKdGutsPosten( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divKdGutsPosten	=	document.getElementById( "TableKdGutsPosten") ;
	divKdGutsPosten.innerHTML	=	"" ;

	myData	=	"" ;
	listeKdGutsPosten	=	response.getElementsByTagName( "KdGutsPosten") ;
	if ( listeKdGutsPosten) {
		myData	+=	"listeKdGutsPosten contains " + listeKdGutsPosten.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Pos.</th>" ;
		myData	+=	"<th>Art.-Nr.</th>" ;
		myData	+=	"<th>Bezeichnung</th>" ;
		myData	+=	"<th>Menge (Gebucht)</th>" ;
		myData	+=	"<th>Berechnete Menge</th>" ;
		myData	+=	"<th>Preis</th>" ;
		myData	+=	"<th>RefPreis</th>" ;
		myData	+=	"<th>GesamtPreis</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeKdGutsPosten.length ; i++) {
			kdGutsPosten	=	response.getElementsByTagName( "KdGutsPosten")[i] ;
			myId	=	kdGutsPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue ;
			myKdGutsNr	=	kdGutsPosten.getElementsByTagName( "KdGutsNr")[0].childNodes[0].nodeValue ;
			myBez1	=	kdGutsPosten.getElementsByTagName( "ArtikelBez1")[0].childNodes[0].nodeValue ;
			myBez2	=	kdGutsPosten.getElementsByTagName( "ArtikelBez2")[0].childNodes[0].nodeValue ;
			myMT	=	kdGutsPosten.getElementsByTagName( "MengenText")[0].childNodes[0].nodeValue ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + kdGutsPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue + "</td>" ;
			if ( kdGutsPosten.getElementsByTagName( "SubPosNr")[0].childNodes[0].nodeValue.length > 0) {
				myLine	+=	"<td>" + kdGutsPosten.getElementsByTagName( "PosNr")[0].childNodes[0].nodeValue
							+ kdGutsPosten.getElementsByTagName( "SubPosNr")[0].childNodes[0].nodeValue
							+ "</td>" ;
			} else {
				myLine	+=	"<td>" + kdGutsPosten.getElementsByTagName( "PosNr")[0].childNodes[0].nodeValue + "</td>" ;
			}
			myLine	+=	"<td>"
						+ btnLinkTo( "screenArtikel", kdGutsPosten.getElementsByTagName( "ArtikelNr")[0].childNodes[0].nodeValue)
						+ kdGutsPosten.getElementsByTagName( "ArtikelNr")[0].childNodes[0].nodeValue
						+ "</td>" ;
			myLine	+=	"<td>" + combine( myBez1, myBez2, myMT) ;
			myLine	+=	"<td>"
							+ kdGutsPosten.getElementsByTagName( "Menge")[0].childNodes[0].nodeValue
							+ "</td>" ;
			myLine	+=	"<td>"
							+ kdGutsPosten.getElementsByTagName( "BerechneteMenge")[0].childNodes[0].nodeValue
							+ "</td>" ;
			myLine	+=	"<td>" + kdGutsPosten.getElementsByTagName( "Preis")[0].childNodes[0].nodeValue.replace( ".", ",") + "</td>" ;
			myLine	+=	"<td>(" + kdGutsPosten.getElementsByTagName( "RefPreis")[0].childNodes[0].nodeValue.replace( ".", ",") + ")</td>" ;
			myLine	+=	"<td>" + kdGutsPosten.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue.replace( ".", ",") + "</td>" ;
			myLine	+=	editButton( 'Base', 'KdGuts', '/Base/KdGuts/editorKdGutsPosten.php', 'getPostenAsXML', document.forms['KdGutsKeyData']._IKdGutsNr.value, kdGutsPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, 'showTableKdGutsPosten', 'KdGutsPosten') ;
			myLine	+=	moveDownButton( 'Base', 'KdGuts', '/Common/hdlObject.php', 'movePosDown', myKdGutsNr, myId, '', null, 'showTableKdGutsPosten') ;
			myLine	+=	moveUpButton( 'Base', 'KdGuts', '/Common/hdlObject.php', 'movePosUp', myKdGutsNr, myId, '', null, 'showTableKdGutsPosten') ;
			myLine	+=	deleteButton( 'Base', 'KdGuts', '/Common/hdlObject.php', 'delPos', myKdGutsNr, myId, '', null, 'showTableKdGutsPosten') ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divKdGutsPosten.innerHTML	=	myData ;
}

/**
*
*/
function	showKdGutsVersand( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myKdGutsNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	kdGuts	=	response.getElementsByTagName( "KdGuts")[0] ;
	if ( kdGuts) {
		attrs	=	kdGuts.childNodes ;
		dispAttrs( attrs, "formKdGutsDocEMail") ;
		dispAttrs( attrs, "formKdGutsDocFAX") ;
		dispAttrs( attrs, "formKdGutsDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formKdGutsDocEMail") ;
		dispAttrs( attrs, "formKdGutsDocFAX") ;
		dispAttrs( attrs, "formKdGutsDocPDF") ;
	}
}

function	showKdGutsDocList( response) {
	showDocList( response, "TableKdGutsDocs") ;
}
/**
 * 
 * @param _formKunde
 * @param _formKundeKontakt
 * @return
 */
function	createKdGutsKunde( _formKunde, _formKundeKontakt) {
	requestUniA( 'Base', 'KdGuts', '/Common/hdlObject.php', 'addKundeAll', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', new Array( _formKunde, _formKundeKontakt), showKdGutsAll) ;
	return false ;
}
/**
 * 
 * @param _formKunde
 * @param _formKundeKontakt
 * @return
 */
function	createKdGutsKundeKontakt( _formKunde, _formKundeKontakt) {
	requestUniA( 'Base', 'KdGuts', '/Common/hdlObject.php', 'addKundeKontakt', document.forms['KdGutsKeyData']._IKdGutsNr.value, '', '', new Array( _formKunde, _formKundeKontakt), showKdGutsAll) ;
	return false ;
}
/**
 * 
 * @return
 */
function	showEMailKdGuts() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/Base/KdGuts/getAnschreiben.php?KdGutsNr="+document.forms['KdGutsKeyData']._IKdGutsNr.value
	} ) ;
	dlgPreview.show() ;
}
/**
 * 
 * @return
 */
function	hideEMailKdGuts() {
	dlgPreview.hide() ;
}

