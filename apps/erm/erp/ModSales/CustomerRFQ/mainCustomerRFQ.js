/**
 * mainCustomerRFQ.js
 * =============
 *
 * registers the module in the central database
 */
new mainCustomerRFQ() ;
/**
 * @returns {scrCustomerRFQ}
 */
function	mainCustomerRFQ() {
	dBegin( 1, "scrCustomerRFQ.js", "scrCustomerRFQ", "__constructor()") ;
	wapScreen.call( this, "CustomerRFQ") ;				// instantiate the super-class!!!
	this.package	=	"ModBase" ;			// directory of the module
	this.module	=	"CustomerRFQ" ;					// sub-directory of the screen
	this.coreObject	=	"CustomerRFQ" ;
	this.keyForm	=	"CustomerRFQKeyData" ;		// form
	this.keyField	=	getFormField( 'CustomerRFQKeyData', 'CustomerRFQNo') ;
	this.delConfDialog	=	"/ModSales/CustomerRFQ/confCustomerRFQDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "CustomerRFQ"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCustomerRFQ", {
										xmlTableName:	"TableSelCustomerRFQ"
									,	objectClass:	"CustomerRFQ"
									,	module:			"ModSales"
									,	screen:			"CustomerRFQ"
									,	selectorName:	"selCustomerRFQ"
									,	formFilterName: "formSelCustomerRFQFilter"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerRFQMainDataEntry") ;
															dEnd( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	/**
	 * create the article sales-price selector
	 */
	this.selectArticleSalesPriceCache	=	new wapSelector( this, "gridArticleSalesPriceSelectOV", {
										xmlTableName:	"TableSelArticleSalesPriceCache"
									,	objectClass:	"ArticleSalesPriceCache"
									,	module:			"ModBase"
									,	screen:			"Article"
									,	selectorName:	"selArticleSalesPriceCache"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Article"
									,	formFilterName: "formSelArticleSalesPriceCacheFilter"
									,	parentDS:		this.dataSource
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															dEnd( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	this.selectArticleSalesPriceCache.onSelectById	=	function( _id) {
		dBegin( 1, "mainCustomerRFQ.js", "scrCustomerRFQ", "onSelectById( <_parent>, " + String( _id) + ")") ;
		result	=	this.parent.selectSPById( _id) ;
		dEnd( 1, "mainCustomerRFQ.js", "scrCustomerRFQ", "onSelectById( <_parent>, <_id>)") ;
		return result ;
	} ;
	/**
	 * create the selector for the article sales
	 */
//	this.selCustomer	=	new selector( this, 'selKundeKontakt', 'ModBase', '/ModBase/Kunde/selKundeKontakt.php', 'Kunde', 'Kunde') ;
//	this.selCustomer.dtv.phpGetCall	=	"getCCList" ;
//	this.selCustomer.selected	=	function( _id) {
//		_debugL( 0x00000001, "Selected Customer Contact Id " + _id + "\n") ;
//		this.parent.selCustomer.dijitDialog.hide();
//		this.parent.dispatch( true, 'setKundeFromKKId', this.parent.keyField.value, _id, 1) ;
//	} ;
//	this.showSelCustomer	=	function() {
//		this.selCustomer.parent	=	this ;
//		this.selCustomer.show( '', -1, '') ;
//	} ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainCustomerRFQ.js", "scrCustomerRFQ", "*", "creating gridCustomerRFQOV") ;
	this.gridCustomerRFQOV	=	new wapGrid( this, "gridCustomerRFQOV", {
										xmlTableName:	"TableCustomerRFQOV"
									,	object:			"CustomerRFQ"
									,	module:			"ModSales"
									,	screen:			"CustomerRFQ"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerRFQMainDataEntry") ;
															dEnd( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCustomerRFQ.js", "scrCustomerRFQ", "*", "calling gridCustomerRFQOV._onFirstPage()") ;
	this.gridCustomerRFQOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerRFQItem
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCustomerRFQItem	=	new wapGrid( this, "gridCustomerRFQItem", {
										object:			"CustomerRFQItem"
									,	module :		"ModBase"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCustomerRFQItem"
									,	moduleName: 	"ModSales"
									,	subModuleName:	"CustomerRFQ"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid{gridCustomerRFQItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid{gridCustomerRFQItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid{gridCustomerRFQItem}", "onSelectById( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCustomerRFQ{wapGrid.js}", "wapGrid{gridCustomerRFQItem}", "onSelectById( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCustomerRFQItem.addItem	=	function() {
										this.parent.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
									} ;
	this.selectSPById	=	function( _id) {
		dBegin( 1, "mainCustomerRFQ.js", "scrCustomerRFQ", "selectSPById( <_id>)") ;
		qty	=	1 ;	// parseInt( qtyField.value) ;
		this.gridCustomerRFQItem.dataSource.onMisc( this.keyField.value, _id, qty, null, "addPos") ;
		dEnd( 1, "mainCustomerRFQ.js", "scrCustomerRFQ", "selectSPById( <_id>)") ;
		return true ;
	} ;
	/**
	 *
	 */
	this.fncLink	=	function() {
	} ;
	/**
	 *
	 * @param {boolean} _wd
	 * @param {string} _fnc
	 * @param {string} _form
	 * @returns {void}
	 */
	dTrace( 2, "mainCustomerRFQ.js", "mainCustomerRFQ", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerRFQ.js", "mainCustomerRFQ", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainCustomerRFQ.js", "mainCustomerRFQ", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerRFQ.js", "scrCustomerRFQ", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCustomerRFQ.js", "scrCustomerRFQ", "fncShow( <_response>)") ;
		dEnd( 1, "mainCustomerRFQ.js", "scrCustomerRFQ", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCustomerRFQ.js", "scrCustomerRFQ", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCustomerRFQ.js", "scrCustomerRFQ", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData(  _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainCustomerRFQ.js", "mainCustomerRFQ", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=CustomerRFQ"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
			this.gridCustomerRFQItem._onFirstPage() ;
		}
		dEnd( 1, "mainCustomerRFQ.js", "scrCustomerRFQ", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onJS	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerRFQ.js", "mainCustomerRFQ", "onJS( <...>)") ;
//		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		this.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
		dEnd( 1, "mainCustomerRFQ.js", "mainCustomerRFQ", "onJS( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerRFQ.js", "scrCustomerRFQ", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectById( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCustomerRFQ.js", "scrCustomerRFQ", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCustomerRFQSurveyEntry") ;
	dEnd( 1, "scrCustomerRFQ.js", "scrCustomerRFQ", "__constructor()") ;
}
function	newCustomerRFQ() {
	_debugL( 0x00000001, "mainCustomerRFQ.js::newCustomerRFQ(): begin\n") ;
	myScreen	=	screenShow( "screenTCustomerRFQ", hookNew) ;
	if ( myScreen.isLoaded) {
		hookNew() ;
	}
	_debugL( 0x00000001, "mainCustomerRFQ.js::newCustomerRFQ(): end\n") ;
}
/**
 *
 */
/**
*
*/
function	addArticleBySPId( _avpId, qtyFieldName, artikelNr) {
	_debugL( 0x00000001, "mainCustomerRFQ.js::addArticleBySPId(" + _avpId + "): begin\n") ;
//	qtyField	=	document.getElementById( qtyFieldName) ;
	qty	=	1 ;	// parseInt( qtyField.value) ;
	cuOrdrNo	=	getFormField( "CustomerRFQKeyData", "_ICustomerRFQNo") ;
	requestUni( 'ModBase', 'CustomerRFQ', '/Common/hdlObject.php', 'addPos', cuOrdrNo.value, _avpId, qty, null, showCustomerRFQAll) ;
	_debugL( 0x00000001, "mainCustomerRFQ.js::addArticleBySPId(" + _avpId + "): end\n") ;
	return false ;
}
function	showCustomerRFQEMailRcvr( response) {
	fieldEMail	=	getFormField( "formCustomerRFQDocEMail", "_IeMail") ;
	fieldEMailCC	=	getFormField( "formCustomerRFQDocEMail", "_IeMailCC") ;
	fieldEMailBCC	=	getFormField( "formCustomerRFQDocEMail", "_IeMailBCC") ;
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	custContact	=	response.getElementsByTagName( "KundeKontakt")[0] ;
	dlvrCust	=	response.getElementsByTagName( "LiefKunde")[0] ;
	dlvrCustContact	=	response.getElementsByTagName( "LiefKundeKontakt")[0] ;
	custEMail	=	"" ;
	custContactEMail	=	"" ;
	dlvrCustEMail	=	"" ;
	dlvrCustContactEMail	=	"" ;
	if ( cust) {
		custEMail	=	cust.getElementsByTagName( "eMail")[0].childNodes[0].nodeValue ;
	}
	if ( custContact) {
		custContactEMail	=	custContact.getElementsByTagName( "eMail")[0].childNodes[0].nodeValue ;
	}
	if ( dlvrCust) {
		dlvrCustEMail	=	dlvrCust.getElementsByTagName( "eMail")[0].childNodes[0].nodeValue ;
	}
	if ( dlvrCustContact) {
		dlvrCustContactEMail	=	dlvrCustContact.getElementsByTagName( "eMail")[0].childNodes[0].nodeValue ;
	}
//	_debugL( 0x00000001, "    cust EMail: " + custEMail + "\n")
//	_debugL( 0x00000001, "    cust Contact EMail: " + custContactEMail + "\n")
//	_debugL( 0x00000001, "dlvrCust EMail: " + dlvrCustEMail + "\n")
//	_debugL( 0x00000001, "dlvrCust Contact EMail: " + dlvrCustContactEMail + "\n")
	fieldEMail.value	=	"" ;
	fieldEMailCC.value	=	"" ;
	fieldEMailBCC.value	=	"" ;
	if ( custContactEMail.length > 0) {
		fieldEMail.value	=	custContactEMail ;
		if ( custContactEMail != custEMail) {
			fieldEMailCC.value	=	custEMail ;
		}
	} else {
		fieldEMail.value	=	custEMail ;
	}
	if ( dlvrCustEMail.length > 0) {
		if ( fieldEMailCC.value.length > 0) {
			fieldEMailCC.value	+=	"," + dlvrCustEMail ;
		} else {
			fieldEMailCC.value	+=	dlvrCustEMail ;
		}
	}
	if ( dlvrCustContactEMail.length > 0 && dlvrCustContactEMail != dlvrCustEMail) {
		if ( fieldEMailCC.value.length > 0) {
			fieldEMailCC.value	+=	"," + dlvrCustContactEMail ;
		} else {
			fieldEMailCC.value	+=	dlvrCustContactEMail ;
		}
	}
}
/**
 *
 */
function	showCustomerRFQ( response) {
	var	lockInfo ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerRFQ")[0] ;
	if ( cuOrdr) {

		myCustomerRFQNo	=	response.getElementsByTagName( "CustomerRFQNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "CustomerRFQKeyData") ;
		dispAttrs( attrs, "formCustomerRFQMain") ;
		dispAttrs( attrs, "formCustomerRFQModi") ;
		dispAttrs( attrs, "formCustomerRFQCalc") ;
		dispAttrs( attrs, "formCustomerRFQDocEMail") ;
		dispAttrs( attrs, "formCustomerRFQDocFAX") ;
		dispAttrs( attrs, "formCustomerRFQDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCustomerRFQ") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerRFQ', '/Common/hdlObject.php', 'lock', document.forms['CustomerRFQKeyData']._ICustomerRFQNo.value, '', '', null, showCustomerRFQ) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerRFQ', '/Common/hdlObject.php', 'unlock', document.forms['CustomerRFQKeyData']._ICustomerRFQNo.value, '', '', null, showCustomerRFQ) ; \" />" ;
		}

		/**
		 *
		 */
		showCustomerRFQDocInfo( response) ;

		/**
		 *
		var	myFieldRabatt	=	getFormField( "formCustomerRFQCalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCustomerRFQCalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCustomerRFQCalc", "_CRohmarge") ;

		var	myNetto	=	parseFloat( response.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue) ;
		var	myRabatt	=	parseFloat( response.getElementsByTagName( "GesamtPreis")[0].childNodes[0].nodeValue)
						* parseFloat( response.getElementsByTagName( "Rabatt")[0].childNodes[0].nodeValue)
						/ 100.0 ;
		var	myNettoNachRabatt	=	myNetto - myRabatt ;

		myFieldRabatt.value	=	myRabatt ;
		myFieldNettoNachRabatt.value	=	myNetto - myRabatt ;
		 */

	}
}

function	showCustomerRFQDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCustomerRFQ") ;
	var pdfNode	=	response.getElementsByTagName( "Document")[0] ;
	if ( pdfNode) {
		var pdfRef	=	response.getElementsByTagName( "Document")[0].textContent ;
		if ( pdfRef != "") {
			pdfDocument.innerHTML	=	"<a href=\"" + pdfRef + "\" target=\"pdf\"><img src=\"/Rsrc/gif/pdficon_large.gif\" /></a>" ;
		} else {
			pdfDocument.innerHTML	=	"" ;
		}
	}
}
/**
 *
 */
function	showTableCustomerRFQItem( response) {
	updTableHead( response, "formCustomerRFQItemTop", "formCustomerRFQItemBot") ;
	showTable( response, "TableCustomerRFQItem", "CustomerRFQItem", "CustomerRFQ", document.forms['CustomerRFQKeyData']._ICustomerRFQNo.value, "showCustomerRFQAll", "refreshTableCustomerRFQItem") ;
}
function	refreshTableCustomerRFQItem( response) {
	refreshTable( response, "TableCustomerRFQItem", "CustomerRFQItem", "CustomerRFQ", document.forms['CustomerRFQKeyData']._ICustomerRFQNo.value, "showCustomerRFQAll") ;
}
/**
 *
 */
function	showCustomerRFQVersand( response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerRFQ")[0] ;
	if ( cuOrdr) {
		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "formCustomerRFQDocEMail") ;
		dispAttrs( attrs, "formCustomerRFQDocFAX") ;
		dispAttrs( attrs, "formCustomerRFQDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCustomerRFQDocEMail") ;
		dispAttrs( attrs, "formCustomerRFQDocFAX") ;
		dispAttrs( attrs, "formCustomerRFQDocPDF") ;
	}
}

function	showCustomerRFQDocList( response) {
	showDocList( response, "TableCustomerRFQDocs") ;
}

function	showCustomerRFQDocUpload( response) {
	myField	=	getFormField( "formCustomerRFQDocUpload", "_DRefNr") ;
	myField.value	=	response.getElementsByTagName( "CustomerRFQNo")[0].childNodes[0].nodeValue ;
}
/**
 *
 * @param _cuOrdrNo
 * @param _sonst
 * @return
 */
function	createDirBest( _cuOrdrNo, _sonst) {
	markerCustomerRFQNo	=	_cuOrdrNo ;
	confAction( '/ModBase/CustomerRFQ/confCustomerRFQCreateDirBest.php', doCustomerRFQCreateDirBest) ;
	return false ;
}
function	doCustomerRFQCreateDirBest() {
	confDialog.hide() ;
	requestUni( 'ModBase', 'CustomerRFQ', '/Common/hdlObject.php', 'createDirBest', markerCustomerRFQNo, '', '', null, showCustomerRFQ) ;
}
/**
 *
 * @return
 */
function	showEMailCustomerRFQ() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModBase/CustomerRFQ/getAnschreiben.php?CustomerRFQNo="+document.forms['CustomerRFQKeyData']._ICustomerRFQNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailCustomerRFQ() {
	dlgPreview.hide() ;
}
function	refCustomerRFQItem( _rng) {
	requestUni( 'ModBase', 'CustomerRFQ', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CustomerRFQKeyData']._ICustomerRFQNo.value,
			_rng,
			'CustomerRFQItem',
			'formCustomerRFQItemTop',
			showTableCustomerRFQItem) ;
	return false ;
}
