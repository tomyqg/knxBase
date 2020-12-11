<?php 
require_once("config.inc.php");
Header("content-type: application/x-javascript");
?>
/**
 *
 */
function	gotoInKonf( _inKonfNr, _backTo, _backKey) {
	dialogStack.push( _backTo) ;
	dialogKeyStack.push( _backKey) ;
	goto( 'screenInKonf') ;
	requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'getXMLComplete', _inKonfNr, '', '', null, showInKonfAll) ;
	return false ;
}

function	retToInKonf( _inKonfNr) {
	goto( 'screenInKonf') ;
	if ( _inKonfNr != document.forms['InKonfKeyData']._IInKonfNr.value) {
		requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'getXMLComplete', _inKonfNr, '', '', null, showInKonfAll) ;
	}
}

/**
 *
 */
function	prevInKonf( _key, _id) {
	var	myInKonfNr ;
	requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'getXMLComplete', sprintf( "%06d", parseInt( _key, 10) - 1), '', '', null, showInKonfAll) ;
	return false ;
}

/**
 *
 */
function	nextInKonf( _key, _id) {
	var	myInKonfNr ;
	requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'getXMLComplete', sprintf( "%06d", parseInt( _key, 10) + 1), '', '', null, showInKonfAll) ;
	return false ;
}

/**
 *
 */
function	showInKonfAll( response) {
	showInKonf( response) ;
	showTableInKonfPosten( response) ;
	showInKonfVersand( response) ;
}

/**
 *
 */
function	showInKonf( response) {
	var	lockInfo ;
	var	kunde ;
	var	attrs ;
	var	myInKonfNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	inKonf	=	response.getElementsByTagName( "InKonf")[0] ;
	if ( inKonf) {

		myInKonfNr	=	response.getElementsByTagName( "InKonfNr")[0].textContent ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].textContent ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].textContent ;

		attrs	=	inKonf.childNodes ;
		dispAttrs( attrs, "InKonfKeyData") ;
		dispAttrs( attrs, "formInKonfMain") ;
		dispAttrs( attrs, "formInKonfModi") ;

		lockInfo	=	document.getElementById( "lockStateInKonf") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].textContent) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'lock', document.forms['InKonfKeyData']._IInKonfNr.value, '', '', null, showInKonf) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'unlock', document.forms['InKonfKeyData']._IInKonfNr.value, '', '', null, showInKonf) ; \" />" ;
		}

		/**
		 *
		 */
		showInKonfDocInfo( response) ;
	}
}

function	showInKonfDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfInKonf") ;
	var pdfNode	=	response.getElementsByTagName( "Document")[0] ;
	if ( pdfNode) {
		var pdfRef	=	response.getElementsByTagName( "Document")[0].textContent ;
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
function	showTableInKonfPosten( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divInKonfPosten	=	document.getElementById( "TableInKonfPosten") ;
	divInKonfPosten.innerHTML	=	"" ;

	myData	=	"" ;
	listeInKonfPosten	=	response.getElementsByTagName( "InKonfPosten") ;
	if ( listeInKonfPosten) {
		myData	+=	"listeInKonfPosten contains " + listeInKonfPosten.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Pos.</th>" ;
		myData	+=	"<th>Art.-Nr.</th>" ;
		myData	+=	"<th>Bezeichnung</th>" ;
		myData	+=	"<th>Menge</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeInKonfPosten.length ; i++) {
			inKonfPosten	=	response.getElementsByTagName( "InKonfPosten")[i] ;
			myId	=	inKonfPosten.getElementsByTagName( "Id")[0].textContent ;
			myInKonfNr	=	inKonfPosten.getElementsByTagName( "InKonfNr")[0].textContent ;
			myBez1	=	inKonfPosten.getElementsByTagName( "ArtikelBez1")[0].textContent ;
			myBez2	=	inKonfPosten.getElementsByTagName( "ArtikelBez2")[0].textContent ;
			myMT	=	inKonfPosten.getElementsByTagName( "MengenText")[0].textContent ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + inKonfPosten.getElementsByTagName( "Id")[0].textContent + "</td>" ;
			if ( inKonfPosten.getElementsByTagName( "SubPosNr")[0].textContent.length > 0) {
				myLine	+=	"<td>" + inKonfPosten.getElementsByTagName( "PosNr")[0].textContent
							+ inKonfPosten.getElementsByTagName( "SubPosNr")[0].textContent
							+ "</td>" ;
			} else {
				myLine	+=	"<td>" + inKonfPosten.getElementsByTagName( "PosNr")[0].textContent + "</td>" ;
			}
			myLine	+=	"<td>"
						+ gotoButton( "gotoArtikel", inKonfPosten.getElementsByTagName( "ArtikelNr")[0].textContent, 'retToInKonf', document.forms['InKonfKeyData']._IInKonfNr.value)
						+ inKonfPosten.getElementsByTagName( "ArtikelNr")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>" + combine( myBez1, myBez2, myMT) ;
			myLine	+=	"<td>"
							+ inKonfPosten.getElementsByTagName( "MengeErf")[0].textContent
							+ "</td>" ;
			myLine	+=	editButton( 'Base', 'InKonf', '/Base/InKonf/editorInKonfPosten.php', 'getPostenAsXML', document.forms['InKonfKeyData']._IInKonfNr.value, inKonfPosten.getElementsByTagName( "Id")[0].textContent, '', null, 'showTableInKonfPosten', 'InKonfPosten') ;
			myLine	+=	moveDownButton( 'Base', 'InKonf', '/Common/hdlObject.php', 'movePosDown', myInKonfNr, myId, '', null, 'showTableInKonfPosten') ;
			myLine	+=	moveUpButton( 'Base', 'InKonf', '/Common/hdlObject.php', 'movePosUp', myInKonfNr, myId, '', null, 'showTableInKonfPosten') ;
			myLine	+=	deleteButton( 'Base', 'InKonf', '/Common/hdlObject.php', 'delPos', myInKonfNr, myId, '', null, 'showTableInKonfPosten') ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divInKonfPosten.innerHTML	=	myData ;
}

/**
*
*/
function	showInKonfVersand( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myInKonfNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	lfBest	=	response.getElementsByTagName( "InKonf")[0] ;
	if ( lfBest) {
		attrs	=	lfBest.childNodes ;
		dispAttrs( attrs, "formInKonfDocEMail") ;
		dispAttrs( attrs, "formInKonfDocFAX") ;
		dispAttrs( attrs, "formInKonfDocPDF") ;
	}
	lief	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( lfBest) {
		attrs	=	lief.childNodes ;
		dispAttrs( attrs, "formInKonfDocEMail") ;
		dispAttrs( attrs, "formInKonfDocFAX") ;
		dispAttrs( attrs, "formInKonfDocPDF") ;
	}
}

function	showInKonfDocList( response) {
	showDocList( response, "TableInKonfDocs") ;
}

/**
 *
 */
function	newInKonf( _inKonfNr, _sonst) {
	requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'add', _inKonfNr, '', '', 'formInKonfMain', showInKonf) ;
	return false ;
}

function	createPdfInKonf( _inKonfNr, _sonst) {
	requestUni( 'Base', 'InKonfDoc', '/Common/hdlObject.php', 'createPDF', _inKonfNr, '', '', null, showInKonf) ;
}

var	markerInKonfNr ;
function	delInKonf( _kundeNr, _sonst) {
	markerInKonfNr	=	_kundeNr ;
	confAction( '/Base/InKonf/confInKonfDel.php', doInKonfDel) ;
	return false ;
}
function	doInKonfDel() {
	confDialog.hide() ;
	requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'del', markerInKonfNr, '', '', null, showInKonf) ;
}

function	reloadScreenInKonf( _inKonfNr) {
	reload( 'screenInKonf') ;
	requestUni( 'Base', 'InKonf', '/Common/hdlObject.php', 'getXMLComplete', _inKonfNr, '', '', null, showInKonfAll) ;
}
