/**
 * mainCustomerInvoice.js
 * =============
 *
 * registers the module in the central database
 */
new mainCustomerInvoice() ;
/**
 * @returns {scrCustomerInvoice}
 */
function	mainCustomerInvoice() {
	dBegin( 1, "scrCustomerInvoice.js", "scrCustomerInvoice", "__constructor()") ;
	wapScreen.call( this, "CustomerInvoice") ;				// instantiate the super-class!!!
	this.package	=	"ModBase" ;			// directory of the module
	this.module	=	"CustomerInvoice" ;					// sub-directory of the screen
	this.coreObject	=	"CustomerInvoice" ;
	this.keyForm	=	"CustomerInvoiceKeyData" ;		// form
	this.keyField	=	getFormField( 'CustomerInvoiceKeyData', 'CustomerInvoiceNo') ;
	this.delConfDialog	=	"/ModSales/CustomerInvoice/confCustomerInvoiceDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "CustomerInvoice"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCustomerInvoice", {
										xmlTableName:	"TableSelCustomerInvoice"
									,	objectClass:	"CustomerInvoice"
									,	module:			"ModSales"
									,	screen:			"CustomerInvoice"
									,	selectorName:	"selCustomerInvoice"
									,	formFilterName: "formSelCustomerInvoiceFilter"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerInvoiceMainDataEntry") ;
															dEnd( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
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
															dBegin( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															dEnd( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	this.selectArticleSalesPriceCache.onSelectById	=	function( _id) {
		dBegin( 1, "mainCustomerInvoice.js", "scrCustomerInvoice", "onSelectById( <_parent>, " + String( _id) + ")") ;
		result	=	this.parent.selectSPById( _id) ;
		dEnd( 1, "mainCustomerInvoice.js", "scrCustomerInvoice", "onSelectById( <_parent>, <_id>)") ;
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
	dTrace( 2, "mainCustomerInvoice.js", "scrCustomerInvoice", "*", "creating gridCustomerInvoiceOV") ;
	this.gridCustomerInvoiceOV	=	new wapGrid( this, "gridCustomerInvoiceOV", {
										xmlTableName:	"TableCustomerInvoiceOV"
									,	object:			"CustomerInvoice"
									,	module:			"ModSales"
									,	screen:			"CustomerInvoice"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerInvoiceMainDataEntry") ;
															dEnd( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCustomerInvoice.js", "scrCustomerInvoice", "*", "calling gridCustomerInvoiceOV._onFirstPage()") ;
	this.gridCustomerInvoiceOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerInvoiceItem
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCustomerInvoiceItem	=	new wapGrid( this, "gridCustomerInvoiceItem", {
										object:			"CustomerInvoiceItem"
									,	module :		"ModBase"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCustomerInvoiceItem"
									,	moduleName: 	"ModSales"
									,	subModuleName:	"CustomerInvoice"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid{gridCustomerInvoiceItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid{gridCustomerInvoiceItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid{gridCustomerInvoiceItem}", "onSelectById( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCustomerInvoice{wapGrid.js}", "wapGrid{gridCustomerInvoiceItem}", "onSelectById( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCustomerInvoiceItem.addItem	=	function() {
										this.parent.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
									} ;
	this.selectSPById	=	function( _id) {
		dBegin( 1, "mainCustomerInvoice.js", "scrCustomerInvoice", "selectSPById( <_id>)") ;
		qty	=	1 ;	// parseInt( qtyField.value) ;
		this.gridCustomerInvoiceItem.dataSource.onMisc( this.keyField.value, _id, qty, null, "addPos") ;
		dEnd( 1, "mainCustomerInvoice.js", "scrCustomerInvoice", "selectSPById( <_id>)") ;
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
	dTrace( 2, "mainCustomerInvoice.js", "mainCustomerInvoice", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerInvoice.js", "mainCustomerInvoice", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainCustomerInvoice.js", "mainCustomerInvoice", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerInvoice.js", "scrCustomerInvoice", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCustomerInvoice.js", "scrCustomerInvoice", "fncShow( <_response>)") ;
		dEnd( 1, "mainCustomerInvoice.js", "scrCustomerInvoice", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCustomerInvoice.js", "scrCustomerInvoice", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCustomerInvoice.js", "scrCustomerInvoice", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData(  _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainCustomerInvoice.js", "mainCustomerInvoice", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=CustomerInvoice"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
			this.gridCustomerInvoiceItem._onFirstPage() ;
		}
		dEnd( 1, "mainCustomerInvoice.js", "scrCustomerInvoice", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onJS	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerInvoice.js", "mainCustomerInvoice", "onJS( <...>)") ;
//		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		this.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
		dEnd( 1, "mainCustomerInvoice.js", "mainCustomerInvoice", "onJS( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerInvoice.js", "scrCustomerInvoice", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectById( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCustomerInvoice.js", "scrCustomerInvoice", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCustomerInvoiceSurveyEntry") ;
	dEnd( 1, "scrCustomerInvoice.js", "scrCustomerInvoice", "__constructor()") ;
}
function	newCustomerInvoice() {
	_debugL( 0x00000001, "mainCustomerInvoice.js::newCustomerInvoice(): begin\n") ;
	myScreen	=	screenShow( "screenTCustomerInvoice", hookNew) ;
	if ( myScreen.isLoaded) {
		hookNew() ;
	}
	_debugL( 0x00000001, "mainCustomerInvoice.js::newCustomerInvoice(): end\n") ;
}
/**
 *
 */
/**
*
*/
function	addArticleBySPId( _avpId, qtyFieldName, artikelNr) {
	_debugL( 0x00000001, "mainCustomerInvoice.js::addArticleBySPId(" + _avpId + "): begin\n") ;
//	qtyField	=	document.getElementById( qtyFieldName) ;
	qty	=	1 ;	// parseInt( qtyField.value) ;
	cuOrdrNo	=	getFormField( "CustomerInvoiceKeyData", "_ICustomerInvoiceNo") ;
	requestUni( 'ModBase', 'CustomerInvoice', '/Common/hdlObject.php', 'addPos', cuOrdrNo.value, _avpId, qty, null, showCustomerInvoiceAll) ;
	_debugL( 0x00000001, "mainCustomerInvoice.js::addArticleBySPId(" + _avpId + "): end\n") ;
	return false ;
}
function	showCustomerInvoiceEMailRcvr( response) {
	fieldEMail	=	getFormField( "formCustomerInvoiceDocEMail", "_IeMail") ;
	fieldEMailCC	=	getFormField( "formCustomerInvoiceDocEMail", "_IeMailCC") ;
	fieldEMailBCC	=	getFormField( "formCustomerInvoiceDocEMail", "_IeMailBCC") ;
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
function	showCustomerInvoice( response) {
	var	lockInfo ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerInvoice")[0] ;
	if ( cuOrdr) {

		myCustomerInvoiceNo	=	response.getElementsByTagName( "CustomerInvoiceNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "CustomerInvoiceKeyData") ;
		dispAttrs( attrs, "formCustomerInvoiceMain") ;
		dispAttrs( attrs, "formCustomerInvoiceModi") ;
		dispAttrs( attrs, "formCustomerInvoiceCalc") ;
		dispAttrs( attrs, "formCustomerInvoiceDocEMail") ;
		dispAttrs( attrs, "formCustomerInvoiceDocFAX") ;
		dispAttrs( attrs, "formCustomerInvoiceDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCustomerInvoice") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerInvoice', '/Common/hdlObject.php', 'lock', document.forms['CustomerInvoiceKeyData']._ICustomerInvoiceNo.value, '', '', null, showCustomerInvoice) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerInvoice', '/Common/hdlObject.php', 'unlock', document.forms['CustomerInvoiceKeyData']._ICustomerInvoiceNo.value, '', '', null, showCustomerInvoice) ; \" />" ;
		}

		/**
		 *
		 */
		showCustomerInvoiceDocInfo( response) ;

		/**
		 *
		var	myFieldRabatt	=	getFormField( "formCustomerInvoiceCalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCustomerInvoiceCalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCustomerInvoiceCalc", "_CRohmarge") ;

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

function	showCustomerInvoiceDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCustomerInvoice") ;
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
function	showTableCustomerInvoiceItem( response) {
	updTableHead( response, "formCustomerInvoiceItemTop", "formCustomerInvoiceItemBot") ;
	showTable( response, "TableCustomerInvoiceItem", "CustomerInvoiceItem", "CustomerInvoice", document.forms['CustomerInvoiceKeyData']._ICustomerInvoiceNo.value, "showCustomerInvoiceAll", "refreshTableCustomerInvoiceItem") ;
}
function	refreshTableCustomerInvoiceItem( response) {
	refreshTable( response, "TableCustomerInvoiceItem", "CustomerInvoiceItem", "CustomerInvoice", document.forms['CustomerInvoiceKeyData']._ICustomerInvoiceNo.value, "showCustomerInvoiceAll") ;
}
/**
 *
 */
function	showCustomerInvoiceVersand( response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerInvoice")[0] ;
	if ( cuOrdr) {
		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "formCustomerInvoiceDocEMail") ;
		dispAttrs( attrs, "formCustomerInvoiceDocFAX") ;
		dispAttrs( attrs, "formCustomerInvoiceDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCustomerInvoiceDocEMail") ;
		dispAttrs( attrs, "formCustomerInvoiceDocFAX") ;
		dispAttrs( attrs, "formCustomerInvoiceDocPDF") ;
	}
}

function	showCustomerInvoiceDocList( response) {
	showDocList( response, "TableCustomerInvoiceDocs") ;
}

function	showCustomerInvoiceDocUpload( response) {
	myField	=	getFormField( "formCustomerInvoiceDocUpload", "_DRefNr") ;
	myField.value	=	response.getElementsByTagName( "CustomerInvoiceNo")[0].childNodes[0].nodeValue ;
}
/**
 *
 * @param _cuOrdrNo
 * @param _sonst
 * @return
 */
function	createDirBest( _cuOrdrNo, _sonst) {
	markerCustomerInvoiceNo	=	_cuOrdrNo ;
	confAction( '/ModBase/CustomerInvoice/confCustomerInvoiceCreateDirBest.php', doCustomerInvoiceCreateDirBest) ;
	return false ;
}
function	doCustomerInvoiceCreateDirBest() {
	confDialog.hide() ;
	requestUni( 'ModBase', 'CustomerInvoice', '/Common/hdlObject.php', 'createDirBest', markerCustomerInvoiceNo, '', '', null, showCustomerInvoice) ;
}
/**
 *
 * @return
 */
function	showEMailCustomerInvoice() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModBase/CustomerInvoice/getAnschreiben.php?CustomerInvoiceNo="+document.forms['CustomerInvoiceKeyData']._ICustomerInvoiceNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailCustomerInvoice() {
	dlgPreview.hide() ;
}
function	refCustomerInvoiceItem( _rng) {
	requestUni( 'ModBase', 'CustomerInvoice', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CustomerInvoiceKeyData']._ICustomerInvoiceNo.value,
			_rng,
			'CustomerInvoiceItem',
			'formCustomerInvoiceItemTop',
			showTableCustomerInvoiceItem) ;
	return false ;
}
