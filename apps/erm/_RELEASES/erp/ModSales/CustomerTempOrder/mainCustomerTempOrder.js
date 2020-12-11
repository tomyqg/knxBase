/**
 * mainCustomerTempOrder.js
 * =============
 *
 * registers the module in the central database
 */
new mainCustomerTempOrder() ;
/**
 * @returns {scrCustomerTempOrder}
 */
function	mainCustomerTempOrder() {
	dBegin( 1, "scrCustomerTempOrder.js", "scrCustomerTempOrder", "__constructor()") ;
	wapScreen.call( this, "CustomerTempOrder") ;				// instantiate the super-class!!!
	this.package	=	"ModBase" ;			// directory of the module
	this.module	=	"CustomerTempOrder" ;					// sub-directory of the screen
	this.coreObject	=	"CustomerTempOrder" ;
	this.keyForm	=	"CustomerTempOrderKeyData" ;		// form
	this.keyField	=	getFormField( 'CustomerTempOrderKeyData', 'CustomerOrderNo') ;
	this.delConfDialog	=	"/ModSales/CustomerTempOrder/confCustomerTempOrderDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "CustomerTempOrder"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCustomerTempOrder", {
										xmlTableName:	"TableSelCustomerTempOrder"
									,	objectClass:	"CustomerTempOrder"
									,	module:			"ModSales"
									,	screen:			"CustomerTempOrder"
									,	selectorName:	"selCustomerTempOrder"
									,	formFilterName: "formSelCustomerTempOrderFilter"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerTempOrderMainDataEntry") ;
															dEnd( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
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
															dBegin( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															dEnd( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	this.selectArticleSalesPriceCache.onSelectById	=	function( _id) {
		dBegin( 1, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "onSelectById( <_parent>, " + String( _id) + ")") ;
		result	=	this.parent.selectSPById( _id) ;
		dEnd( 1, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "onSelectById( <_parent>, <_id>)") ;
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
	dTrace( 2, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "*", "creating gridCustomerTempOrderOV") ;
	this.gridCustomerTempOrderOV	=	new wapGrid( this, "gridCustomerTempOrderOV", {
										xmlTableName:	"TableCustomerTempOrderOV"
									,	object:			"CustomerTempOrder"
									,	module:			"ModSales"
									,	screen:			"CustomerTempOrder"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerTempOrderMainDataEntry") ;
															dEnd( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "*", "calling gridCustomerTempOrderOV._onFirstPage()") ;
	this.gridCustomerTempOrderOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerTempOrderItem
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCustomerTempOrderItem	=	new wapGrid( this, "gridCustomerTempOrderItem", {
										object:			"CustomerTempOrderItem"
									,	module :		"ModBase"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCustomerTempOrderItem"
									,	moduleName: 	"ModSales"
									,	subModuleName:	"CustomerTempOrder"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid{gridCustomerTempOrderItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid{gridCustomerTempOrderItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid{gridCustomerTempOrderItem}", "onSelectById( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCustomerTempOrder{wapGrid.js}", "wapGrid{gridCustomerTempOrderItem}", "onSelectById( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCustomerTempOrderItem.addItem	=	function() {
										this.parent.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
									} ;
	this.selectSPById	=	function( _id) {
		dBegin( 1, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "selectSPById( <_id>)") ;
		qty	=	1 ;	// parseInt( qtyField.value) ;
		this.gridCustomerTempOrderItem.dataSource.onMisc( this.keyField.value, _id, qty, null, "addPos") ;
		dEnd( 1, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "selectSPById( <_id>)") ;
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
	dTrace( 2, "mainCustomerTempOrder.js", "mainCustomerTempOrder", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerTempOrder.js", "mainCustomerTempOrder", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainCustomerTempOrder.js", "mainCustomerTempOrder", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "fncShow( <_response>)") ;
		dEnd( 1, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData(  _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainCustomerTempOrder.js", "mainCustomerTempOrder", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=CustomerTempOrder"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
			this.gridCustomerTempOrderItem._onFirstPage() ;
		}
		dEnd( 1, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onJS	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerTempOrder.js", "mainCustomerTempOrder", "onJS( <...>)") ;
//		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		this.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
		dEnd( 1, "mainCustomerTempOrder.js", "mainCustomerTempOrder", "onJS( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectById( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCustomerTempOrder.js", "scrCustomerTempOrder", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCustomerTempOrderSurveyEntry") ;
	dEnd( 1, "scrCustomerTempOrder.js", "scrCustomerTempOrder", "__constructor()") ;
}
function	newCustomerTempOrder() {
	_debugL( 0x00000001, "mainCustomerTempOrder.js::newCustomerTempOrder(): begin\n") ;
	myScreen	=	screenShow( "screenTCustomerTempOrder", hookNew) ;
	if ( myScreen.isLoaded) {
		hookNew() ;
	}
	_debugL( 0x00000001, "mainCustomerTempOrder.js::newCustomerTempOrder(): end\n") ;
}
/**
 *
 */
/**
*
*/
function	addArticleBySPId( _avpId, qtyFieldName, artikelNr) {
	_debugL( 0x00000001, "mainCustomerTempOrder.js::addArticleBySPId(" + _avpId + "): begin\n") ;
//	qtyField	=	document.getElementById( qtyFieldName) ;
	qty	=	1 ;	// parseInt( qtyField.value) ;
	cuOrdrNo	=	getFormField( "CustomerTempOrderKeyData", "_ICustomerOrderNo") ;
	requestUni( 'ModBase', 'CustomerTempOrder', '/Common/hdlObject.php', 'addPos', cuOrdrNo.value, _avpId, qty, null, showCustomerTempOrderAll) ;
	_debugL( 0x00000001, "mainCustomerTempOrder.js::addArticleBySPId(" + _avpId + "): end\n") ;
	return false ;
}
function	showCustomerTempOrderEMailRcvr( response) {
	fieldEMail	=	getFormField( "formCustomerTempOrderDocEMail", "_IeMail") ;
	fieldEMailCC	=	getFormField( "formCustomerTempOrderDocEMail", "_IeMailCC") ;
	fieldEMailBCC	=	getFormField( "formCustomerTempOrderDocEMail", "_IeMailBCC") ;
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
function	showCustomerTempOrder( response) {
	var	lockInfo ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerTempOrder")[0] ;
	if ( cuOrdr) {

		myCustomerOrderNo	=	response.getElementsByTagName( "CustomerOrderNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "CustomerTempOrderKeyData") ;
		dispAttrs( attrs, "formCustomerTempOrderMain") ;
		dispAttrs( attrs, "formCustomerTempOrderModi") ;
		dispAttrs( attrs, "formCustomerTempOrderCalc") ;
		dispAttrs( attrs, "formCustomerTempOrderDocEMail") ;
		dispAttrs( attrs, "formCustomerTempOrderDocFAX") ;
		dispAttrs( attrs, "formCustomerTempOrderDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCustomerTempOrder") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerTempOrder', '/Common/hdlObject.php', 'lock', document.forms['CustomerTempOrderKeyData']._ICustomerOrderNo.value, '', '', null, showCustomerTempOrder) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerTempOrder', '/Common/hdlObject.php', 'unlock', document.forms['CustomerTempOrderKeyData']._ICustomerOrderNo.value, '', '', null, showCustomerTempOrder) ; \" />" ;
		}

		/**
		 *
		 */
		showCustomerTempOrderDocInfo( response) ;

		/**
		 *
		var	myFieldRabatt	=	getFormField( "formCustomerTempOrderCalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCustomerTempOrderCalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCustomerTempOrderCalc", "_CRohmarge") ;

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

function	showCustomerTempOrderDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCustomerTempOrder") ;
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
function	showTableCustomerTempOrderItem( response) {
	updTableHead( response, "formCustomerTempOrderItemTop", "formCustomerTempOrderItemBot") ;
	showTable( response, "TableCustomerTempOrderItem", "CustomerTempOrderItem", "CustomerTempOrder", document.forms['CustomerTempOrderKeyData']._ICustomerOrderNo.value, "showCustomerTempOrderAll", "refreshTableCustomerTempOrderItem") ;
}
function	refreshTableCustomerTempOrderItem( response) {
	refreshTable( response, "TableCustomerTempOrderItem", "CustomerTempOrderItem", "CustomerTempOrder", document.forms['CustomerTempOrderKeyData']._ICustomerOrderNo.value, "showCustomerTempOrderAll") ;
}
/**
 *
 */
function	showCustomerTempOrderVersand( response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerTempOrder")[0] ;
	if ( cuOrdr) {
		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "formCustomerTempOrderDocEMail") ;
		dispAttrs( attrs, "formCustomerTempOrderDocFAX") ;
		dispAttrs( attrs, "formCustomerTempOrderDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCustomerTempOrderDocEMail") ;
		dispAttrs( attrs, "formCustomerTempOrderDocFAX") ;
		dispAttrs( attrs, "formCustomerTempOrderDocPDF") ;
	}
}

function	showCustomerTempOrderDocList( response) {
	showDocList( response, "TableCustomerTempOrderDocs") ;
}

function	showCustomerTempOrderDocUpload( response) {
	myField	=	getFormField( "formCustomerTempOrderDocUpload", "_DRefNr") ;
	myField.value	=	response.getElementsByTagName( "CustomerOrderNo")[0].childNodes[0].nodeValue ;
}
/**
 *
 * @param _cuOrdrNo
 * @param _sonst
 * @return
 */
function	createDirBest( _cuOrdrNo, _sonst) {
	markerCustomerOrderNo	=	_cuOrdrNo ;
	confAction( '/ModBase/CustomerTempOrder/confCustomerTempOrderCreateDirBest.php', doCustomerTempOrderCreateDirBest) ;
	return false ;
}
function	doCustomerTempOrderCreateDirBest() {
	confDialog.hide() ;
	requestUni( 'ModBase', 'CustomerTempOrder', '/Common/hdlObject.php', 'createDirBest', markerCustomerOrderNo, '', '', null, showCustomerTempOrder) ;
}
/**
 *
 * @return
 */
function	showEMailCustomerTempOrder() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModBase/CustomerTempOrder/getAnschreiben.php?CustomerOrderNo="+document.forms['CustomerTempOrderKeyData']._ICustomerOrderNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailCustomerTempOrder() {
	dlgPreview.hide() ;
}
function	refCustomerTempOrderItem( _rng) {
	requestUni( 'ModBase', 'CustomerTempOrder', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CustomerTempOrderKeyData']._ICustomerOrderNo.value,
			_rng,
			'CustomerTempOrderItem',
			'formCustomerTempOrderItemTop',
			showTableCustomerTempOrderItem) ;
	return false ;
}
