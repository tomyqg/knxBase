<?php 
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
	Header("content-type: application/x-javascript");
?>
/**
 *
 */
function	gotoWA( _lfBestNr, _backTo, _backKey) {
	dialogStack.push( _backTo) ;
	dialogKeyStack.push( _backKey) ;
	goto( 'screenWA') ;
//	requestUni( 'Base', 'WA', '/Common/hdlObject.php', 'getXMLComplete', _lfBestNr, '', '', null, showWAAll) ;
	return false ;
}

function	retToWA( _dummy) {
	goto( 'screenWA') ;
//	requestUni( 'Base', 'WA', '/Common/hdlObject.php', 'getXMLComplete', _lfBestNr, '', '', null, showWAAll) ;
}

/**
 *
 */
function	showWAAll( response) {
	showWA( response) ;
	showTableWAPosten( response) ;
}

/**
 *
 */
function	showWA( response) {
}

/**
 *
 */
function	showTableWAPosten( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divWAPosten	=	document.getElementById( "TableWAPosten") ;
	divWAPosten.innerHTML	=	"Here we are ... \n" ;

	myData	=	"" ;
	wAStatusListe	=	response.getElementsByTagName( "WAStatusListe") ;
	if ( wAStatusListe) {
		kdBestListe	=	response.getElementsByTagName( "KdBest") ;
		divWAPosten.innerHTML	+=	"Here we are ... " + kdBestListe.length + "\n" ;
		myData	+=	"KdBestListe contains " + kdBestListe.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th rowspan=\"2\">Auftrag Nr.</th>" ;
		myData	+=	"<th colspan=\"3\">Stati</th>" ;
		myData	+=	"<th rowspan=\"2\">Kommissionierbar</th>" ;
		myData	+=	"</tr>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Gesamt</th>" ;
		myData	+=	"<th>Lief.</th>" ;
		myData	+=	"<th>Rech.</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < kdBestListe.length ; i++) {
			kdBest	=	kdBestListe[i] ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + kdBest.getElementsByTagName( "KdBestNr")[0].childNodes[0].nodeValue
						+ gotoButton( "gotoKdBest", kdBest.getElementsByTagName( "KdBestNr")[0].childNodes[0].nodeValue, 'retToWA', '')
						+ "</td>" ;
			myLine	+=	"<td>" + kdBest.getElementsByTagName( "Status")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + kdBest.getElementsByTagName( "StatLief")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + kdBest.getElementsByTagName( "StatRech")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + kdBest.getElementsByTagName( "StatComm")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divWAPosten.innerHTML	=	myData ;
}

function	createPdfWA( _lfBestNr, _sonst) {
	requestUni( 'Base', 'WAStatusDoc', '/Common/hdlObject.php', 'createPDF', '', '', '', null, showWAAll) ;
	return false ;
}

function	reloadScreenWA( _lfBestNr) {
	reload( 'screenWA') ;
	requestUni( 'Base', 'WAStatus', '/Common/hdlObject.php', 'gettableAsXML', '', '', '', null, showWAAll) ;
}
