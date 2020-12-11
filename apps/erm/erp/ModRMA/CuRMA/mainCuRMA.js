/**
 * regModCuRMA
 * 
 * registers the module in the central database
 */
function	regModCuRMA() {
	_debugL( 0x00000001, "regModCuRMA: \n") ;
	screenAdd( "screenCuRMA", linkCuRMA, "CuRMA", "CuRMAKeyData", "_ICuRMANr", showCuRMAAll, printCuRMA) ;
}
function	linkCuRMA() {
	_debugL( 0x00000001, "linkCuRMA: \n") ;
}
function	printCuRMA() {
	_debugL( 0x00000001, "printCuRMA: \n") ;
	cuRMANr	=	getFormField( 'CuRMAKeyData', '_ICuRMANr').value ;
	requestUni( 'Base', 'CuRMADoc', '/Common/hdlObject.php', 'createPDF', cuRMANr, '', '', null, showCuRMA) ;
}
/**
 *
 */
function	gotoCuRMA( _cuRMANr, _backTo, _backKey) {
	dialogStack.push( _backTo) ;
	dialogKeyStack.push( _backKey) ;
	myScreen	=	screenShow( 'screenCuRMA') ;
	if ( myScreen.isLoaded) {
		requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'getXMLComplete', _cuRMANr, '', '', null, showCuRMAAll) ;
	} else {
		myScreen.attr( "onDownloadEnd", function() {
				requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'getXMLComplete', _cuRMANr, '', '', null, showCuRMAAll) ;
			}) ;
	}
	return false ;
}

function	retToCuRMA( _cuRMANr) {
	screenShow( 'screenCuRMA') ;
	if ( _cuRMANr != document.forms['CuRMAKeyData']._ICuRMANr.value) {
		requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'getXMLComplete', _cuRMANr, '', '', null, showCuRMAAll) ;
	}
}

/**
 *
 */
function	prevCuRMA( _key, _id) {
	var	myCuRMANr ;
	requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'getXMLComplete', sprintf( "%06d", parseInt( _key, 10) - 1), '', '', null, showCuRMAAll) ;
	return false ;
}

/**
 *
 */
function	nextCuRMA( _key, _id) {
	var	myCuRMANr ;
	requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'getXMLComplete', sprintf( "%06d", parseInt( _key, 10) + 1), '', '', null, showCuRMAAll) ;
	return false ;
}

/**
 *
 */
function	showCuRMAAll( response) {
	showCuRMA( response) ;
	showKundeAdrs( response, "formCuRMAKunde", "formCuRMAKundeKontakt", "formCuRMALiefKunde", "formCuRMALiefKundeKontakt", "formCuRMARechKunde", "formCuRMARechKundeKontakt") ;
	showTableCuRMAItems( response) ;
	showCuRMAVersand( response) ;
}

/**
 *
 */
function	showCuRMA( response) {
	var	lockInfo ;
	var	kunde ;
	var	attrs ;
	var	myCuRMANr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuRMA	=	response.getElementsByTagName( "CuRMA")[0] ;
	if ( cuRMA) {

		myCuRMANr	=	response.getElementsByTagName( "CuRMANr")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuRMA.childNodes ;
		dispAttrs( attrs, "CuRMAKeyData") ;
		dispAttrs( attrs, "formCuRMAMain") ;
		dispAttrs( attrs, "formCuRMAModi") ;
		dispAttrs( attrs, "formCuRMACalc") ;
		dispAttrs( attrs, "formCuRMADocEMail") ;
		dispAttrs( attrs, "formCuRMADocFAX") ;
		dispAttrs( attrs, "formCuRMADocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCuRMA") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'lock', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '', null, showCuRMA) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'unlock', document.forms['CuRMAKeyData']._ICuRMANr.value, '', '', null, showCuRMA) ; \" />" ;
		}

		/**
		 *
		 */
		showCuRMADocInfo( response) ;

		/**
		 *
		 */
		var	myFieldRabatt	=	getFormField( "formCuRMACalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCuRMACalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCuRMACalc", "_CRohmarge") ;

		var	myNetto	=	parseFloat( response.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue) ;
		var	myRabatt	=	parseFloat( response.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue)
						* parseFloat( response.getElementsByTagName( "Rabatt")[0].childNodes[0].nodeValue)
						/ 100.0 ;
		var	myNettoNachRabatt	=	myNetto - myRabatt ;

		myFieldRabatt.value	=	myRabatt ;
		myFieldNettoNachRabatt.value	=	myNetto - myRabatt ;

	}
}

function	showCuRMADocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCuRMA") ;
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
function	showTableCuRMAItems( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divCuRMAItems	=	document.getElementById( "tableCuRMAItems") ;
	divCuRMAItems.innerHTML	=	"" ;

	myData	=	"" ;
	listeCuRMAItems	=	response.getElementsByTagName( "CuRMAItem") ;
	if ( listeCuRMAItems) {
		myData	+=	"listeCuRMAItems contains " + listeCuRMAItems.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Item no.</th>" ;
		myData	+=	"<th>Article no.</th>" ;
		myData	+=	"<th>Bezeichnung</th>" ;
		myData	+=	"<th>Menge (VPE)</th>" ;
		myData	+=	"<th>Menge gebucht</th>" ;
		myData	+=	"<th>Gelieferte Menge</th>" ;
		myData	+=	"<th>Berechnete Menge</th>" ;
		myData	+=	"<th>Preis</th>" ;
		myData	+=	"<th>RefPreis</th>" ;
		myData	+=	"<th>GesamtPreis</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeCuRMAItems.length ; i++) {
			cuRMAItems	=	response.getElementsByTagName( "CuRMAItem")[i] ;
			myId	=	cuRMAItems.getElementsByTagName( "Id")[0].childNodes[0].nodeValue ;
			myCuRMANr	=	cuRMAItems.getElementsByTagName( "CuRMANr")[0].childNodes[0].nodeValue ;
			myBez1	=	cuRMAItems.getElementsByTagName( "ArtikelBez1")[0].childNodes[0].nodeValue ;
			myBez2	=	cuRMAItems.getElementsByTagName( "ArtikelBez2")[0].childNodes[0].nodeValue ;
			myMT	=	cuRMAItems.getElementsByTagName( "MengenText")[0].childNodes[0].nodeValue ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + cuRMAItems.getElementsByTagName( "Id")[0].childNodes[0].nodeValue + "</td>" ;
			if ( cuRMAItems.getElementsByTagName( "SubItemNr")[0].childNodes[0].nodeValue.length > 0) {
				myLine	+=	"<td>" + cuRMAItems.getElementsByTagName( "ItemNr")[0].childNodes[0].nodeValue
							+ cuRMAItems.getElementsByTagName( "SubItemNr")[0].childNodes[0].nodeValue
							+ "</td>" ;
			} else {
				myLine	+=	"<td>" + cuRMAItems.getElementsByTagName( "ItemNr")[0].childNodes[0].nodeValue + "</td>" ;
			}
			myLine	+=	"<td>"
						+ gotoButton( "gotoArtikel", cuRMAItems.getElementsByTagName( "ArtikelNr")[0].childNodes[0].nodeValue, 'retToCuRMA', document.forms['CuRMAKeyData']._ICuRMANr.value)
						+ cuRMAItems.getElementsByTagName( "ArtikelNr")[0].childNodes[0].nodeValue
						+ "</td>" ;
			myLine	+=	"<td>" + combine( myBez1, myBez2, myMT) + "</td>" ;
			myLine	+=	"<td>"
							+ cuRMAItems.getElementsByTagName( "Menge")[0].childNodes[0].nodeValue
							+ "(" + cuRMAItems.getElementsByTagName( "MengeProVPE")[0].childNodes[0].nodeValue + ")"
							+ "</td>" ;
			myLine	+=	"<td>"
						+ cuRMAItems.getElementsByTagName( "MengeReserviert")[0].childNodes[0].nodeValue
						+ "</td>" ;
			myLine	+=	"<td>"
							+ cuRMAItems.getElementsByTagName( "GelieferteMenge")[0].childNodes[0].nodeValue
							+ "</td>" ;
			myLine	+=	"<td>"
							+ cuRMAItems.getElementsByTagName( "BerechneteMenge")[0].childNodes[0].nodeValue
							+ "</td>" ;
			myLine	+=	"<td>" + cuRMAItems.getElementsByTagName( "Preis")[0].childNodes[0].nodeValue.replace( ".", ",") + "</td>" ;
			myLine	+=	"<td>(" + cuRMAItems.getElementsByTagName( "RefPreis")[0].childNodes[0].nodeValue.replace( ".", ",") + ")</td>" ;
			myLine	+=	"<td>" + cuRMAItems.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue.replace( ".", ",") + "</td>" ;
			myLine	+=	editButton( 'ModRMA', 'CuRMA', '/ModRMA/CuRMA/editorCuRMAItems.php', 'getItemsAsXML', document.forms['CuRMAKeyData']._ICuRMANr.value, cuRMAItems.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, 'showTableCuRMAItems', 'CuRMAItems') ;
			myLine	+=	moveDownButton( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'movePosDown', myCuRMANr, myId, '', null, 'showTableCuRMAItems') ;
			myLine	+=	moveUpButton( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'movePosUp', myCuRMANr, myId, '', null, 'showTableCuRMAItems') ;
			myLine	+=	deleteButton( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'delPos', myCuRMANr, myId, '', null, 'showTableCuRMAItems') ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divCuRMAItems.innerHTML	=	myData ;
}

/**
*
*/
function	showCuRMAVersand( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myCuRMANr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuRMA	=	response.getElementsByTagName( "CuRMA")[0] ;
	if ( cuRMA) {
		attrs	=	cuRMA.childNodes ;
		dispAttrs( attrs, "formCuRMADocEMail") ;
		dispAttrs( attrs, "formCuRMADocFAX") ;
		dispAttrs( attrs, "formCuRMADocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCuRMADocEMail") ;
		dispAttrs( attrs, "formCuRMADocFAX") ;
		dispAttrs( attrs, "formCuRMADocPDF") ;
	}
}

function	showCuRMADocList( response) {
	showDocList( response, "TableCuRMADocs") ;
}

/**
 *
 */
function	newCuRMA( _mod, _app, _fnc, _key, _form) {
	var	postVars = "" ;
	var	add	=	false ;
	var	myForm ;			// the form with the user input

	/**
	 *
	 */
	var	url	=	_mod + "/" + _app + "/"
					+ "mainCuRMA_action.php?"
					+ "&_fnc=" + _fnc
					+ "&_key=" + _key
					;

	postVars	=	getPOSTData( _form) ;
	dojo.xhrPost( {
		url: url,
		handleAs: "xml",
		postData: postVars,
		load: function( response) {
			showStatus( response) ;
		},
		error: function( response) {
			showStatus( response) ;
		}
	} ) ;
	return false ;
}

function	createPdfCuRMA( _cuRMANr, _sonst) {
	requestUni( 'ModRMA', 'CuRMADoc', '/Common/hdlObject.php', 'createPDF', _cuRMANr, '', '', null, showCuRMA) ;
}

var	markerCuRMANr ;
function	delCuRMA( _cuRMANr, _sonst) {
	markerCuRMANr	=	_cuRMANr ;
	confAction( '/ModRMA/CuRMA/confCuRMADel.php', doCuRMADel) ;
	return false ;
}
function	doCuRMADel() {
	confDialog.hide() ;
	requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'del', markerCuRMANr, '', '', null, showCuRMA) ;
}

function	reloadScreenCuRMA( _cuRMANr) {
	reload( 'screenCuRMA') ;
	requestUni( 'ModRMA', 'CuRMA', '/Common/hdlObject.php', 'getXMLComplete', _cuRMANr, '', '', null, showCuRMAAll) ;
}

function	showEMailCuRMA() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModRMA/CuRMA/getAnschreiben.php?CuRMANr="+document.forms['CuRMAKeyData']._ICuRMANr.value
	} ) ;
	dlgPreview.show() ;
}

function	hideEMailCuRMA() {
	dlgPreview.hide() ;
}