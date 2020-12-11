/**
 * mainCustomerCommission.js
 * =============
 *
 * registers the module in the central database
 */
new mainCustomerCommission() ;
/**
 * @returns {scrCustomerCommission}
 */
function	mainCustomerCommission() {
	dBegin( 1, "scrCustomerCommission.js", "scrCustomerCommission", "__constructor()") ;
	wapScreen.call( this, "CustomerCommission") ;				// instantiate the super-class!!!
	this.package	=	"ModBase" ;			// directory of the module
	this.module	=	"CustomerCommission" ;					// sub-directory of the screen
	this.coreObject	=	"CustomerCommission" ;
	this.keyForm	=	"CustomerCommissionKeyData" ;		// form
	this.keyField	=	getFormField( 'CustomerCommissionKeyData', 'CustomerCommissionNo') ;
	this.delConfDialog	=	"/ModSales/CustomerCommission/confCustomerCommissionDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "CustomerCommission"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCustomerCommission", {
										xmlTableName:	"TableSelCustomerCommission"
									,	objectClass:	"CustomerCommission"
									,	module:			"ModSales"
									,	screen:			"CustomerCommission"
									,	selectorName:	"selCustomerCommission"
									,	formFilterName: "formSelCustomerCommissionFilter"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerCommissionMainDataEntry") ;
															dEnd( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
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
															dBegin( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															dEnd( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	this.selectArticleSalesPriceCache.onSelectById	=	function( _id) {
		dBegin( 1, "mainCustomerCommission.js", "scrCustomerCommission", "onSelectById( <_parent>, " + String( _id) + ")") ;
		result	=	this.parent.selectSPById( _id) ;
		dEnd( 1, "mainCustomerCommission.js", "scrCustomerCommission", "onSelectById( <_parent>, <_id>)") ;
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
	dTrace( 2, "mainCustomerCommission.js", "scrCustomerCommission", "*", "creating gridCustomerCommissionOV") ;
	this.gridCustomerCommissionOV	=	new wapGrid( this, "gridCustomerCommissionOV", {
										xmlTableName:	"TableCustomerCommissionOV"
									,	object:			"CustomerCommission"
									,	module:			"ModSales"
									,	screen:			"CustomerCommission"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerCommissionMainDataEntry") ;
															dEnd( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCustomerCommission.js", "scrCustomerCommission", "*", "calling gridCustomerCommissionOV._onFirstPage()") ;
	this.gridCustomerCommissionOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerCommissionItem
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCustomerCommissionItem	=	new wapGrid( this, "gridCustomerCommissionItem", {
										object:			"CustomerCommissionItem"
									,	module :		"ModBase"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCustomerCommissionItem"
									,	moduleName: 	"ModSales"
									,	subModuleName:	"CustomerCommission"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid{gridCustomerCommissionItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid{gridCustomerCommissionItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid{gridCustomerCommissionItem}", "onSelectById( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCustomerCommission{wapGrid.js}", "wapGrid{gridCustomerCommissionItem}", "onSelectById( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCustomerCommissionItem.addItem	=	function() {
										this.parent.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
									} ;
	this.selectSPById	=	function( _id) {
		dBegin( 1, "mainCustomerCommission.js", "scrCustomerCommission", "selectSPById( <_id>)") ;
		qty	=	1 ;	// parseInt( qtyField.value) ;
		this.gridCustomerCommissionItem.dataSource.onMisc( this.keyField.value, _id, qty, null, "addPos") ;
		dEnd( 1, "mainCustomerCommission.js", "scrCustomerCommission", "selectSPById( <_id>)") ;
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
	dTrace( 2, "mainCustomerCommission.js", "mainCustomerCommission", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerCommission.js", "mainCustomerCommission", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainCustomerCommission.js", "mainCustomerCommission", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerCommission.js", "scrCustomerCommission", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCustomerCommission.js", "scrCustomerCommission", "fncShow( <_response>)") ;
		dEnd( 1, "mainCustomerCommission.js", "scrCustomerCommission", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCustomerCommission.js", "scrCustomerCommission", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCustomerCommission.js", "scrCustomerCommission", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData(  _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainCustomerCommission.js", "mainCustomerCommission", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=CustomerCommission"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
			this.gridCustomerCommissionItem._onFirstPage() ;
		}
		dEnd( 1, "mainCustomerCommission.js", "scrCustomerCommission", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onJS	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerCommission.js", "mainCustomerCommission", "onJS( <...>)") ;
//		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		this.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
		dEnd( 1, "mainCustomerCommission.js", "mainCustomerCommission", "onJS( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerCommission.js", "scrCustomerCommission", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectById( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCustomerCommission.js", "scrCustomerCommission", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCustomerCommissionSurveyEntry") ;
	dEnd( 1, "scrCustomerCommission.js", "scrCustomerCommission", "__constructor()") ;
}
function	newCustomerCommission() {
	_debugL( 0x00000001, "mainCustomerCommission.js::newCustomerCommission(): begin\n") ;
	myScreen	=	screenShow( "screenTCustomerCommission", hookNew) ;
	if ( myScreen.isLoaded) {
		hookNew() ;
	}
	_debugL( 0x00000001, "mainCustomerCommission.js::newCustomerCommission(): end\n") ;
}
/**
 *
 */
/**
*
*/
function	addArticleBySPId( _avpId, qtyFieldName, artikelNr) {
	_debugL( 0x00000001, "mainCustomerCommission.js::addArticleBySPId(" + _avpId + "): begin\n") ;
//	qtyField	=	document.getElementById( qtyFieldName) ;
	qty	=	1 ;	// parseInt( qtyField.value) ;
	cuOrdrNo	=	getFormField( "CustomerCommissionKeyData", "_ICustomerCommissionNo") ;
	requestUni( 'ModBase', 'CustomerCommission', '/Common/hdlObject.php', 'addPos', cuOrdrNo.value, _avpId, qty, null, showCustomerCommissionAll) ;
	_debugL( 0x00000001, "mainCustomerCommission.js::addArticleBySPId(" + _avpId + "): end\n") ;
	return false ;
}
function	showCustomerCommissionEMailRcvr( response) {
	fieldEMail	=	getFormField( "formCustomerCommissionDocEMail", "_IeMail") ;
	fieldEMailCC	=	getFormField( "formCustomerCommissionDocEMail", "_IeMailCC") ;
	fieldEMailBCC	=	getFormField( "formCustomerCommissionDocEMail", "_IeMailBCC") ;
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
function	showCustomerCommission( response) {
	var	lockInfo ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerCommission")[0] ;
	if ( cuOrdr) {

		myCustomerCommissionNo	=	response.getElementsByTagName( "CustomerCommissionNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "CustomerCommissionKeyData") ;
		dispAttrs( attrs, "formCustomerCommissionMain") ;
		dispAttrs( attrs, "formCustomerCommissionModi") ;
		dispAttrs( attrs, "formCustomerCommissionCalc") ;
		dispAttrs( attrs, "formCustomerCommissionDocEMail") ;
		dispAttrs( attrs, "formCustomerCommissionDocFAX") ;
		dispAttrs( attrs, "formCustomerCommissionDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCustomerCommission") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerCommission', '/Common/hdlObject.php', 'lock', document.forms['CustomerCommissionKeyData']._ICustomerCommissionNo.value, '', '', null, showCustomerCommission) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerCommission', '/Common/hdlObject.php', 'unlock', document.forms['CustomerCommissionKeyData']._ICustomerCommissionNo.value, '', '', null, showCustomerCommission) ; \" />" ;
		}

		/**
		 *
		 */
		showCustomerCommissionDocInfo( response) ;

		/**
		 *
		var	myFieldRabatt	=	getFormField( "formCustomerCommissionCalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCustomerCommissionCalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCustomerCommissionCalc", "_CRohmarge") ;

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

function	showCustomerCommissionDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCustomerCommission") ;
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
function	showTableCustomerCommissionItem( response) {
	updTableHead( response, "formCustomerCommissionItemTop", "formCustomerCommissionItemBot") ;
	showTable( response, "TableCustomerCommissionItem", "CustomerCommissionItem", "CustomerCommission", document.forms['CustomerCommissionKeyData']._ICustomerCommissionNo.value, "showCustomerCommissionAll", "refreshTableCustomerCommissionItem") ;
}
function	refreshTableCustomerCommissionItem( response) {
	refreshTable( response, "TableCustomerCommissionItem", "CustomerCommissionItem", "CustomerCommission", document.forms['CustomerCommissionKeyData']._ICustomerCommissionNo.value, "showCustomerCommissionAll") ;
}
/**
 *
 */
function	showCustomerCommissionVersand( response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerCommission")[0] ;
	if ( cuOrdr) {
		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "formCustomerCommissionDocEMail") ;
		dispAttrs( attrs, "formCustomerCommissionDocFAX") ;
		dispAttrs( attrs, "formCustomerCommissionDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCustomerCommissionDocEMail") ;
		dispAttrs( attrs, "formCustomerCommissionDocFAX") ;
		dispAttrs( attrs, "formCustomerCommissionDocPDF") ;
	}
}

function	showCustomerCommissionDocList( response) {
	showDocList( response, "TableCustomerCommissionDocs") ;
}

function	showCustomerCommissionDocUpload( response) {
	myField	=	getFormField( "formCustomerCommissionDocUpload", "_DRefNr") ;
	myField.value	=	response.getElementsByTagName( "CustomerCommissionNo")[0].childNodes[0].nodeValue ;
}
/**
 *
 * @param _cuOrdrNo
 * @param _sonst
 * @return
 */
function	createDirBest( _cuOrdrNo, _sonst) {
	markerCustomerCommissionNo	=	_cuOrdrNo ;
	confAction( '/ModBase/CustomerCommission/confCustomerCommissionCreateDirBest.php', doCustomerCommissionCreateDirBest) ;
	return false ;
}
function	doCustomerCommissionCreateDirBest() {
	confDialog.hide() ;
	requestUni( 'ModBase', 'CustomerCommission', '/Common/hdlObject.php', 'createDirBest', markerCustomerCommissionNo, '', '', null, showCustomerCommission) ;
}
/**
 *
 * @return
 */
function	showEMailCustomerCommission() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModBase/CustomerCommission/getAnschreiben.php?CustomerCommissionNo="+document.forms['CustomerCommissionKeyData']._ICustomerCommissionNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailCustomerCommission() {
	dlgPreview.hide() ;
}
function	refCustomerCommissionItem( _rng) {
	requestUni( 'ModBase', 'CustomerCommission', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CustomerCommissionKeyData']._ICustomerCommissionNo.value,
			_rng,
			'CustomerCommissionItem',
			'formCustomerCommissionItemTop',
			showTableCustomerCommissionItem) ;
	return false ;
}
