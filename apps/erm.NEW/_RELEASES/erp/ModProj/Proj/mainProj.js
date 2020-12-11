/**
 * regModProj
 * 
 * registers the module in the central database
 */
function	regModProj() {
	_debugL( 0x00000001, "regModProj: \n") ;
	screenAdd( "screenProj", linkProj, "Proj", "ProjKeyData", "_IProjNr", showProjAll, printProj) ;
}
function	linkProj() {
	_debugL( 0x00000001, "linkProj: \n") ;
}
function	printProj() {
	_debugL( 0x00000001, "printProj: \n") ;
	projNr	=	getFormField( 'ProjKeyData', '_IProjNr').value ;
	requestUni( 'Base', 'ProjDoc', '/Common/hdlObject.php', 'createPDF', projNr, '', '', null, showProj) ;
}
/**
 *
 */
var	dlgPreview	=	null ;

function	gotoProj( _projNr, _backTo, _backKey) {
	dialogStack.push( _backTo) ;
	dialogKeyStack.push( _backKey) ;
	screenShow( 'screenProj') ;
	requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'getXMLComplete', _projNr, '', '', null, showProjAll) ;
	return false ;
}

function	retToProj( _projNr) {
	screenShow( 'screenProj') ;
	if ( _projNr != document.forms['ProjKeyData']._IProjNr.value) {
		requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'getXMLComplete', _projNr, '', '', null, showProjAll) ;
	}
}

/**
 *
 */
function	prevProj( _key, _id) {
	var	myProjNr ;
	requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'getXMLComplete', sprintf( "%06d", parseInt( _key, 10) - 1), '', '', null, showProjAll) ;
	return false ;
}

/**
 *
 */
function	nextProj( _key, _id) {
	var	myProjNr ;
	requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'getXMLComplete', sprintf( "%06d", parseInt( _key, 10) + 1), '', '', null, showProjAll) ;
	return false ;
}

/**
 *
 */
function	showProjAll( response) {
	_debugL( 0x00000001, "minProj.js::showProjAll(): ")
	showProj( response) ;
	showKundeAdrs( response, "formProjKunde", "formProjKundeKontakt", "formProjLiefKunde", "formProjLiefKundeKontakt", "formProjRechKunde", "formProjRechKundeKontakt") ;
	showTableProjPosten( response) ;
	showProjVersand( response) ;
}

/**
 *
 */
function	showProj( response) {
	var	lockInfo ;
	var	kunde ;
	var	attrs ;
	var	myProjNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	proj	=	response.getElementsByTagName( "Proj")[0] ;
	if ( proj) {

		myProjNr	=	response.getElementsByTagName( "ProjNr")[0].textContent ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].textContent ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].textContent ;

		attrs	=	proj.childNodes ;
		dispAttrs( attrs, "ProjKeyData") ;
		dispAttrs( attrs, "formProjMain") ;
		dispAttrs( attrs, "formProjModi") ;
//		dispAttrs( attrs, "formProjCalc") ;
//		dispAttrs( attrs, "formProjDocEMail") ;
//		dispAttrs( attrs, "formProjDocFAX") ;
//		dispAttrs( attrs, "formProjDocPDF") ;
//		dispAttrs( attrs, "formProjDocUpload") ;

		lockInfo	=	document.getElementById( "lockStateProj") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].textContent) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'lock', document.forms['ProjKeyData']._IProjNr.value, '', '', null, showProj) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'unlock', document.forms['ProjKeyData']._IProjNr.value, '', '', null, showProj) ; \" />" ;
		}

		/**
		 *
		 */
//		showProjDocInfo( response) ;

		/**
		 *
		 */
//		var	myFieldRabatt	=	getFormField( "formProjCalc", "_FRabatt") ;
//		var	myFieldNettoNachRabatt	=	getFormField( "formProjCalc", "_FNettoNachRabatt") ;
//		var	myFieldRohmarge	=	getFormField( "formProjCalc", "_FRohmarge") ;

//		var	myNetto	=	parseFloat( response.getElementsByTagName( "GesamtPreis")[0].textContent) ;
//		var	myRabatt	=	parseFloat( response.getElementsByTagName( "GesamtPreis")[0].textContent)
//						* parseFloat( response.getElementsByTagName( "Rabatt")[0].textContent)
//						/ 100.0 ;
//		var	myNettoNachRabatt	=	myNetto - myRabatt ;
//		var	myGesamtEKPreis	=	parseFloat( response.getElementsByTagName( "GesamtEKPreis")[0].textContent) ;
//		var	myRohmarge	=	myNettoNachRabatt - myGesamtEKPreis ;

//		myFieldRabatt.value	=	myRabatt ;
//		myFieldNettoNachRabatt.value	=	myNetto - myRabatt ;
//		myFieldRohmarge.value	=	myRohmarge ;

	}
}

function	showProjDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfProj") ;
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
function	showTableProjPosten( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableProjPosten	=	document.getElementById( "TableProjPosten") ;
	tableProjPosten.innerHTML	=	"Hello, world" ;

	myData	=	"" ;
	listeProjPosten	=	response.getElementsByTagName( "ProjPosten") ;
	if ( listeProjPosten) {
		myData	+=	"listeProjPosten contains " + listeProjPosten.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Pos.</th>" ;
		myData	+=	"<th>Quotation no.</th>" ;
		myData	+=	"<th>Bezeichnung</th>" ;
		myData	+=	"<th>Menge</th>" ;
		myData	+=	"<th>Preis</th>" ;
		myData	+=	"<th>RefPreis</th>" ;
		myData	+=	"<th>GesamtPreis</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeProjPosten.length ; i++) {
			projPosten	=	response.getElementsByTagName( "ProjPosten")[i] ;
			myId	=	projPosten.getElementsByTagName( "Id")[0].childNodes[0].nodeValue ;
			myProjNr	=	projPosten.getElementsByTagName( "ProjNr")[0].childNodes[0].nodeValue ;
			myPosNr	=	projPosten.getElementsByTagName( "PosNr")[0].childNodes[0].nodeValue ;
			mySubPosNr	=	projPosten.getElementsByTagName( "SubPosNr")[0].childNodes[0].nodeValue ;
			myKdAngNr	=	projPosten.getElementsByTagName( "KdAngNr")[0].childNodes[0].nodeValue ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + myId + "</td>" ;
			if ( mySubPosNr.length > 0) {
				myLine	+=	"<td>" + myPosNr + "." + mySubPosNr + "</td>" ;
			} else {
				myLine	+=	"<td>" + myPosNr + "</td>" ;
			}
			myLine	+=	"<td>" + 
						gotoButton( "gotoKdAng", myKdAngNr, 'retToProj', document.forms['ProjKeyData']._IProjNr.value) +
						myKdAngNr +
						"</td>" ;
			myLine	+=	"<td>" + projPosten.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	editButton( 'ModProj', 'Proj', '/ModProj/Proj/editorProjPosten.php', 'getProjPostenAsXML', document.forms['ProjKeyData']._IProjNr.value, myId, '', null, 'showTableProjPosten', 'ProjPosten') ;
			myLine	+=	moveDownButton( 'ModProj', 'Proj', '/Common/hdlObject.php', 'movePosDown', myProjNr, myId, '', null, 'showTableProjPosten') ;
			myLine	+=	moveUpButton( 'ModProj', 'Proj', '/Common/hdlObject.php', 'movePosUp', myProjNr, myId, '', null, 'showTableProjPosten') ;
			myLine	+=	deleteButton( 'ModProj', 'Proj', '/Common/hdlObject.php', 'delPos', myProjNr, myId, '', null, 'showTableProjPosten') ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	tableProjPosten.innerHTML	=	myData ;
}

function	showProjDocList( response) {
	showDocList( response, "TableProjDocs") ;
}

/**
*
*/
function	showProjVersand( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myProjNr ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	proj	=	response.getElementsByTagName( "Proj")[0] ;
	if ( proj) {
		attrs	=	proj.childNodes ;
		dispAttrs( attrs, "formProjDocEMail") ;
		dispAttrs( attrs, "formProjDocFAX") ;
		dispAttrs( attrs, "formProjDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formProjDocEMail") ;
		dispAttrs( attrs, "formProjDocFAX") ;
		dispAttrs( attrs, "formProjDocPDF") ;
	}
	fieldEMail	=	getFormField( "formProjDocEMail", "_IMail") ;
	fieldEMailCC	=	getFormField( "formProjDocEMail", "_IMailCC") ;
	fieldEMailBCC	=	getFormField( "formProjDocEMail", "_IMailBCC") ;
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	custContact	=	response.getElementsByTagName( "KundeKontakt")[0] ;
	custEMail	=	"<receiver>" ;
	custContactEMail	=	"<receiver>" ;
	if ( cust) {
		custEMail	=	cust.getElementsByTagName( "eMail")[0].childNodes[0].nodeValue ;
	}
	if ( custContact) {
		custContactEMail	=	custContact.getElementsByTagName( "eMail")[0].childNodes[0].nodeValue ;
	}
	_debugL( 0x00000001, "Cust EMail: " + custEMail + "\n")
	_debugL( 0x00000001, "Cust Contact EMail: " + custContactEMail + "\n")
	fieldEMailCC.value	=	"" ;
	fieldEMailBCC.value	=	"" ;
	if ( custContactEMail.length > 0) {
		fieldEMail.value	=	custContactEMail ;
		if ( custEMail != custContactEMail) {
			fieldEMailBCC.value	=	custEMail ;
		}
	} else {
		fieldEMail.value	=	custEMail ;
	}
}

/**
 *
 */
function	newProj( _mod, _app, _fnc, _key, _form) {
	var	postVars = "" ;
	var	add	=	false ;
	var	myForm ;			// the form with the user input

	var	url	=	_mod + "/" + _app + "/"
					+ "mainProj_action.php?"
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

function	saveProj( _projNr, _sonst) {
	requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'upd', _projNr, '', '', 'formProjMain', showProj) ;
	return false ;
}

function	newProj( _projNr, _sonst) {
	requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'add', _projNr, '', '', 'formProjMain', showProj) ;
	return false ;
}

function	newProjFromProj( _projNr, _sonst) {
	requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'copy', _projNr, '', '', 'formProjMain', showProj) ;
	return false ;
}

function	copyProj( _projNr, _sonst) {
	requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'newFromProj', _projNr, '', '', 'formProjMain', showProj) ;
	return false ;
}

var	markerProjNr ;
function	delProj( _kundeNr, _sonst) {
	markerProjNr	=	_kundeNr ;
	confAction( '/Base/Proj/confProjDel.php', doProjDel) ;
	return false ;
}
function	doProjDel() {
	confDialog.hide() ;
	requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'del', markerProjNr, '', '', null, showProj) ;
}

function	createPdfProj( _projNr, _sonst) {
	requestUni( 'Base', 'ProjDoc', '/Common/hdlObject.php', 'createPDF', _projNr, '', '', null, showProj) ;
	return false ;
}

function	newKdBestFromProj( _projNr, _sonst) {
	screenShow( 'screenKdBest') ;
	requestUni( 'Base', 'KdBest', '/Common/hdlObject.php', 'newFromProj', '', '', _projNr, null, showKdBest) ;
}

function	reloadScreenProj( _projNr) {
	reload( 'screenProj') ;
	requestUni( 'Base', 'Proj', '/Common/hdlObject.php', 'getXMLComplete', _projNr, '', '', null, showProjAll) ;
}

function	showEMailProj() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/Base/Proj/getAnschreiben.php?ProjNr="+document.forms['ProjKeyData']._IProjNr.value
	} ) ;
	dlgPreview.show() ;
}

function	hideEMailProj() {
	dlgPreview.hide() ;
}

