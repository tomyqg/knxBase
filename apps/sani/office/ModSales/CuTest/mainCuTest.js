/**
 * regModKdLeih
 * 
 * registers the module in the central database
 */
function	regModKdLeih() {
	_debugL( 0x00000001, "regModKdLeih: \n") ;
	myScreen	=	screenAdd( "screenKdLeih", linkKdLeih, "KdLeih", "KdLeihKeyData", "_IKdLeihNr", showKdLeihAll, null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"KdLeih" ;
	myScreen.coreObject	=	"KdLeih" ;
	myScreen.showFunc	=	showKdLeihAll ;
	myScreen.keyField	=	getFormField( 'KdLeihKeyData', '_IKdLeihNr') ;
	
	// make sure everything is setup fo this module, e.g. menuitems enables/disables etc.
	myScreen.link() ;
	if ( pendingKey != "") {
		requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showKdLeihAll) ;
	}
	pendingKey	=	"" ;
}
function	linkKdLeih() {
	_debugL( 0x00000001, "linkKdLeih: \n") ;
}
/**
 *
 */
function	showKdLeihAll( response) {
	showKdLeih( response) ;
	showKundeAdrs( response, "formKdLeihKunde", "formKdLeihKundeKontakt", "formKdLeihLiefKunde", "formKdLeihLiefKundeKontakt", "formKdLeihRechKunde", "formKdLeihRechKundeKontakt") ;
	showTableKdLeihPosten( response) ;
	showKdLeihVersand( response) ;
}
/**
 *
 */
function	showKdLeih( response) {
	var	lockInfo ;
	var	kunde ;
	var	attrs ;
	var	myKdLeihNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	kdLeih	=	response.getElementsByTagName( "KdLeih")[0] ;
	if ( kdLeih) {

		myKdLeihNr	=	response.getElementsByTagName( "KdLeihNr")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	kdLeih.childNodes ;
		dispAttrs( attrs, "KdLeihKeyData") ;
		dispAttrs( attrs, "formKdLeihMain") ;
		dispAttrs( attrs, "formKdLeihModi") ;
		dispAttrs( attrs, "formKdLeihDocEMail") ;
		dispAttrs( attrs, "formKdLeihDocFAX") ;
		dispAttrs( attrs, "formKdLeihDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateKdLeih") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'lock', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', null, showKdLeih) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'Base', 'KdLeih', '/Common/hdlObject.php', 'unlock', document.forms['KdLeihKeyData']._IKdLeihNr.value, '', '', null, showKdLeih) ; \" />" ;
		}

		/**
		 *
		 */
		showKdLeihDocInfo( response) ;

	}
}

function	showKdLeihDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfKdLeih") ;
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
function	showTableKdLeihPosten( response) {
	updTableHead( response, "formKdLeihPostenTop", "formKdLeihPostenBot") ;
	showTable( response, "TableKdLeihPosten", "KdLeihPosten", "KdLeih", document.forms['KdLeihKeyData']._IKdLeihNr.value, "showKdLeihAll", "refreshTableKdLeihPosten") ;
}
function	refreshTableKdLeihPosten( response) {
	refreshTable( response, "TableKdLeihPosten", "KdLeihPosten", "KdLeih", document.forms['KdLeihKeyData']._IKdLeihNr.value, "showKdLeihAll") ;
}
/**
 *
 */
function	showTableKdLeihPostenOld( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divKdLeihPosten	=	document.getElementById( "TableKdLeihPosten") ;
	divKdLeihPosten.innerHTML	=	"" ;

	myData	=	"" ;
	listeKdLeihPosten	=	response.getElementsByTagName( "KdLeihPosten") ;
	if ( listeKdLeihPosten) {
		myData	+=	"listeKdLeihPosten contains " + listeKdLeihPosten.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Pos.</th>" ;
		myData	+=	"<th>Art.-Nr.</th>" ;
		myData	+=	"<th>Bezeichnung</th>" ;
		myData	+=	"<th>Menge (Gebucht)</th>" ;
		myData	+=	"<th>Gelieferte Menge (back)</th>" ;
		myData	+=	"<th>Preis</th>" ;
		myData	+=	"<th>RefPreis</th>" ;
		myData	+=	"<th>GesamtPreis</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeKdLeihPosten.length ; i++) {
			kdLeihPosten	=	response.getElementsByTagName( "KdLeihPosten")[i] ;
			myId	=	kdLeihPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue ;
			myKdLeihNr	=	kdLeihPosten.getElementsByTagName( "KdLeihNr")[0].childNodes[0].nodeValue ;
			myArtikelNr	=	kdLeihPosten.getElementsByTagName( "ArtikelNr")[0].childNodes[0].nodeValue ;
			myBez1	=	kdLeihPosten.getElementsByTagName( "ArtikelBez1")[0].childNodes[0].nodeValue ;
			myBez2	=	kdLeihPosten.getElementsByTagName( "ArtikelBez2")[0].childNodes[0].nodeValue ;
			myMT	=	kdLeihPosten.getElementsByTagName( "MengenText")[0].childNodes[0].nodeValue ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + kdLeihPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue + "</td>" ;
			if ( kdLeihPosten.getElementsByTagName( "SubPosNr")[0].childNodes[0].nodeValue.length > 0) {
				myLine	+=	"<td>" + kdLeihPosten.getElementsByTagName( "PosNr")[0].childNodes[0].nodeValue
							+ kdLeihPosten.getElementsByTagName( "SubPosNr")[0].childNodes[0].nodeValue
							+ "</td>" ;
			} else {
				myLine	+=	"<td>" + kdLeihPosten.getElementsByTagName( "PosNr")[0].childNodes[0].nodeValue + "</td>" ;
			}
			myLine	+=	"<td>"
						+ btnLinkTo( "screenArtikel", myArtikelNr)
						+ kdLeihPosten.getElementsByTagName( "ArtikelNr")[0].childNodes[0].nodeValue
						+ "</td>" ;
			myLine	+=	"<td>" + combine( myBez1, myBez2, myMT) ;
			myLine	+=	"<td>"
							+ kdLeihPosten.getElementsByTagName( "Menge")[0].childNodes[0].nodeValue
							+ "(" + kdLeihPosten.getElementsByTagName( "MengeGebucht")[0].childNodes[0].nodeValue + ")"
							+ "</td>" ;
			myLine	+=	"<td>"
							+ kdLeihPosten.getElementsByTagName( "GelieferteMenge")[0].childNodes[0].nodeValue + " / "
							+ kdLeihPosten.getElementsByTagName( "MengeZurueck")[0].childNodes[0].nodeValue
							+ "</td>" ;
			myLine	+=	"<td>" + kdLeihPosten.getElementsByTagName( "Preis")[0].childNodes[0].nodeValue.replace( ".", ",") + "</td>" ;
			myLine	+=	"<td>(" + kdLeihPosten.getElementsByTagName( "RefPreis")[0].childNodes[0].nodeValue.replace( ".", ",") + ")</td>" ;
			myLine	+=	"<td>" + kdLeihPosten.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue.replace( ".", ",") + "</td>" ;
			myLine	+=	editButton( 'Base', 'KdLeih', '/Base/KdLeih/editorKdLeihPosten.php', 'getPostenAsXML', document.forms['KdLeihKeyData']._IKdLeihNr.value, kdLeihPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, 'showTableKdLeihPosten', 'KdLeihPosten') ;
			myLine	+=	moveDownButton( 'Base', 'KdLeih', '/Common/hdlObject.php', 'movePosDown', myKdLeihNr, myId, '', null, 'showTableKdLeihPosten') ;
			myLine	+=	moveUpButton( 'Base', 'KdLeih', '/Common/hdlObject.php', 'movePosUp', myKdLeihNr, myId, '', null, 'showTableKdLeihPosten') ;
			myLine	+=	deleteButton( 'Base', 'KdLeih', '/Common/hdlObject.php', 'delPos', myKdLeihNr, myId, '', null, 'showTableKdLeihPosten') ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divKdLeihPosten.innerHTML	=	myData ;
}

/**
*
*/
function	showKdLeihVersand( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myKdLeihNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	kdLeih	=	response.getElementsByTagName( "KdLeih")[0] ;
	if ( kdLeih) {
		attrs	=	kdLeih.childNodes ;
		dispAttrs( attrs, "formKdLeihDocEMail") ;
		dispAttrs( attrs, "formKdLeihDocFAX") ;
		dispAttrs( attrs, "formKdLeihDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formKdLeihDocEMail") ;
		dispAttrs( attrs, "formKdLeihDocFAX") ;
		dispAttrs( attrs, "formKdLeihDocPDF") ;
	}
}

function	showKdLeihDocList( response) {
	showDocList( response, "TableKdLeihDocs") ;
}
/**
 *
 */
function	newKdKommFromKdLeih( _kdLeihNr, _sonst) {
	screenShow( 'screenKdKomm') ;
	requestUni( 'Base', 'KdKomm', '/Common/hdlObject.php', 'newFromKdLeih', '', '', _kdLeihNr, null, showKdLeih) ;
}

function	newKdRechFromKdLeihOL( _kdLeihNr, _sonst) {
	screenShow( 'screenKdRech') ;
	requestUni( 'Base', 'KdRech', '/Common/hdlObject.php', 'newFromKdLeihOL', '', '', _kdLeihNr, null, showKdLeih) ;
}

function	newKdRechFromKdLeihA( _kdLeihNr, _sonst) {
	screenShow( 'screenKdRech') ;
	requestUni( 'Base', 'KdRech', '/Common/hdlObject.php', 'newFromKdLeihA', '', '', _kdLeihNr, null, showKdLeih) ;
}
function	showEMailKdLeih() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/Base/KdLeih/getAnschreiben.php?KdLeihNr="+document.forms['KdLeihKeyData']._IKdLeihNr.value
	} ) ;
	dlgPreview.show() ;
}

function	hideEMailKdLeih() {
	dlgPreview.hide() ;
}
function	refKdLeihPosten( _rng) {
	requestUni( 'ModBase', 'KdLeih', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['KdLeihKeyData']._IKdLeihNr.value,
			_rng,
			'KdLeihPosten',
			'formKdLeihPostenTop',
			showTableKdLeihPosten) ;
	return false ; 	
}
 

