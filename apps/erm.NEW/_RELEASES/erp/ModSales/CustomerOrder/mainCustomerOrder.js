/**
 * mainCustomerOrder.js
 * =============
 *
 * registers the module in the central database
 */
new mainCustomerOrder() ;
/**
 * @returns {scrCustomerOrder}
 */
function	mainCustomerOrder() {
	dBegin( 1, "scrCustomerOrder.js", "scrCustomerOrder", "__constructor()") ;
	wapScreen.call( this, "CustomerOrder") ;				// instantiate the super-class!!!
	this.package	=	"ModBase" ;			// directory of the module
	this.module	=	"CustomerOrder" ;					// sub-directory of the screen
	this.coreObject	=	"CustomerOrder" ;
	this.keyForm	=	"CustomerOrderKeyData" ;		// form
	this.keyField	=	getFormField( 'CustomerOrderKeyData', 'CustomerOrderNo') ;
	this.delConfDialog	=	"/ModSales/CustomerOrder/confCustomerOrderDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "CustomerOrder"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCustomerOrder", {
										xmlTableName:	"TableSelCustomerOrder"
									,	objectClass:	"CustomerOrder"
									,	module:			"ModSales"
									,	screen:			"CustomerOrder"
									,	selectorName:	"selCustomerOrder"
									,	formFilterName: "formSelCustomerOrderFilter"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerOrderMainDataEntry") ;
															dEnd( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
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
															dBegin( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															dEnd( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	this.selectArticleSalesPriceCache.onSelectById	=	function( _id) {
		dBegin( 1, "mainCustomerOrder.js", "scrCustomerOrder", "onSelectById( <_parent>, " + String( _id) + ")") ;
		result	=	this.parent.selectSPById( _id) ;
		dEnd( 1, "mainCustomerOrder.js", "scrCustomerOrder", "onSelectById( <_parent>, <_id>)") ;
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
	dTrace( 2, "mainCustomerOrder.js", "scrCustomerOrder", "*", "creating gridCustomerOrderOV") ;
	this.gridCustomerOrderOV	=	new wapGrid( this, "gridCustomerOrderOV", {
										xmlTableName:	"TableCustomerOrderOV"
									,	object:			"CustomerOrder"
									,	module:			"ModSales"
									,	screen:			"CustomerOrder"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerOrderMainDataEntry") ;
															dEnd( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCustomerOrder.js", "scrCustomerOrder", "*", "calling gridCustomerOrderOV._onFirstPage()") ;
	this.gridCustomerOrderOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerOrderItem
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCustomerOrderItem	=	new wapGrid( this, "gridCustomerOrderItem", {
										object:			"CustomerOrderItem"
									,	module :		"ModBase"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCustomerOrderItem"
									,	moduleName: 	"ModSales"
									,	subModuleName:	"CustomerOrder"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid{gridCustomerOrderItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid{gridCustomerOrderItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid{gridCustomerOrderItem}", "onSelectById( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCustomerOrder{wapGrid.js}", "wapGrid{gridCustomerOrderItem}", "onSelectById( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCustomerOrderItem.addItem	=	function() {
										this.parent.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
									} ;
	this.selectSPById	=	function( _id) {
		dBegin( 1, "mainCustomerOrder.js", "scrCustomerOrder", "selectSPById( <_id>)") ;
		qty	=	1 ;	// parseInt( qtyField.value) ;
		this.gridCustomerOrderItem.dataSource.onMisc( this.keyField.value, _id, qty, null, "addPos") ;
		dEnd( 1, "mainCustomerOrder.js", "scrCustomerOrder", "selectSPById( <_id>)") ;
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
	dTrace( 2, "mainCustomerOrder.js", "mainCustomerOrder", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerOrder.js", "mainCustomerOrder", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainCustomerOrder.js", "mainCustomerOrder", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerOrder.js", "scrCustomerOrder", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCustomerOrder.js", "scrCustomerOrder", "fncShow( <_response>)") ;
		dEnd( 1, "mainCustomerOrder.js", "scrCustomerOrder", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCustomerOrder.js", "scrCustomerOrder", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCustomerOrder.js", "scrCustomerOrder", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData(  _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainCustomerOrder.js", "mainCustomerOrder", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=CustomerOrder"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
			this.gridCustomerOrderItem._onFirstPage() ;
		}
		dEnd( 1, "mainCustomerOrder.js", "scrCustomerOrder", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onJS	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerOrder.js", "mainCustomerOrder", "onJS( <...>)") ;
//		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		this.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
		dEnd( 1, "mainCustomerOrder.js", "mainCustomerOrder", "onJS( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerOrder.js", "scrCustomerOrder", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectById( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCustomerOrder.js", "scrCustomerOrder", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCustomerOrderSurveyEntry") ;
	dEnd( 1, "scrCustomerOrder.js", "scrCustomerOrder", "__constructor()") ;
}
function	newCustomerOrder() {
	_debugL( 0x00000001, "mainCustomerOrder.js::newCustomerOrder(): begin\n") ;
	myScreen	=	screenShow( "screenTCustomerOrder", hookNew) ;
	if ( myScreen.isLoaded) {
		hookNew() ;
	}
	_debugL( 0x00000001, "mainCustomerOrder.js::newCustomerOrder(): end\n") ;
}
/**
 *
 */
/**
*
*/
function	addArticleBySPId( _avpId, qtyFieldName, artikelNr) {
	_debugL( 0x00000001, "mainCustomerOrder.js::addArticleBySPId(" + _avpId + "): begin\n") ;
//	qtyField	=	document.getElementById( qtyFieldName) ;
	qty	=	1 ;	// parseInt( qtyField.value) ;
	cuOrdrNo	=	getFormField( "CustomerOrderKeyData", "_ICustomerOrderNo") ;
	requestUni( 'ModBase', 'CustomerOrder', '/Common/hdlObject.php', 'addPos', cuOrdrNo.value, _avpId, qty, null, showCustomerOrderAll) ;
	_debugL( 0x00000001, "mainCustomerOrder.js::addArticleBySPId(" + _avpId + "): end\n") ;
	return false ;
}
function	showCustomerOrderEMailRcvr( response) {
	fieldEMail	=	getFormField( "formCustomerOrderDocEMail", "_IeMail") ;
	fieldEMailCC	=	getFormField( "formCustomerOrderDocEMail", "_IeMailCC") ;
	fieldEMailBCC	=	getFormField( "formCustomerOrderDocEMail", "_IeMailBCC") ;
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
function	showCustomerOrder( response) {
	var	lockInfo ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerOrder")[0] ;
	if ( cuOrdr) {

		myCustomerOrderNo	=	response.getElementsByTagName( "CustomerOrderNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "CustomerOrderKeyData") ;
		dispAttrs( attrs, "formCustomerOrderMain") ;
		dispAttrs( attrs, "formCustomerOrderModi") ;
		dispAttrs( attrs, "formCustomerOrderCalc") ;
		dispAttrs( attrs, "formCustomerOrderDocEMail") ;
		dispAttrs( attrs, "formCustomerOrderDocFAX") ;
		dispAttrs( attrs, "formCustomerOrderDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCustomerOrder") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerOrder', '/Common/hdlObject.php', 'lock', document.forms['CustomerOrderKeyData']._ICustomerOrderNo.value, '', '', null, showCustomerOrder) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerOrder', '/Common/hdlObject.php', 'unlock', document.forms['CustomerOrderKeyData']._ICustomerOrderNo.value, '', '', null, showCustomerOrder) ; \" />" ;
		}

		/**
		 *
		 */
		showCustomerOrderDocInfo( response) ;

		/**
		 *
		var	myFieldRabatt	=	getFormField( "formCustomerOrderCalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCustomerOrderCalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCustomerOrderCalc", "_CRohmarge") ;

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

function	showCustomerOrderDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCustomerOrder") ;
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
function	showTableCustomerOrderItem( response) {
	updTableHead( response, "formCustomerOrderItemTop", "formCustomerOrderItemBot") ;
	showTable( response, "TableCustomerOrderItem", "CustomerOrderItem", "CustomerOrder", document.forms['CustomerOrderKeyData']._ICustomerOrderNo.value, "showCustomerOrderAll", "refreshTableCustomerOrderItem") ;
}
function	refreshTableCustomerOrderItem( response) {
	refreshTable( response, "TableCustomerOrderItem", "CustomerOrderItem", "CustomerOrder", document.forms['CustomerOrderKeyData']._ICustomerOrderNo.value, "showCustomerOrderAll") ;
}
/**
 *
 */
function	showCustomerOrderVersand( response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerOrder")[0] ;
	if ( cuOrdr) {
		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "formCustomerOrderDocEMail") ;
		dispAttrs( attrs, "formCustomerOrderDocFAX") ;
		dispAttrs( attrs, "formCustomerOrderDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCustomerOrderDocEMail") ;
		dispAttrs( attrs, "formCustomerOrderDocFAX") ;
		dispAttrs( attrs, "formCustomerOrderDocPDF") ;
	}
}

function	showCustomerOrderDocList( response) {
	showDocList( response, "TableCustomerOrderDocs") ;
}

function	showCustomerOrderDocUpload( response) {
	myField	=	getFormField( "formCustomerOrderDocUpload", "_DRefNr") ;
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
	confAction( '/ModBase/CustomerOrder/confCustomerOrderCreateDirBest.php', doCustomerOrderCreateDirBest) ;
	return false ;
}
function	doCustomerOrderCreateDirBest() {
	confDialog.hide() ;
	requestUni( 'ModBase', 'CustomerOrder', '/Common/hdlObject.php', 'createDirBest', markerCustomerOrderNo, '', '', null, showCustomerOrder) ;
}
/**
 *
 * @return
 */
function	showEMailCustomerOrder() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModBase/CustomerOrder/getAnschreiben.php?CustomerOrderNo="+document.forms['CustomerOrderKeyData']._ICustomerOrderNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailCustomerOrder() {
	dlgPreview.hide() ;
}
function	refCustomerOrderItem( _rng) {
	requestUni( 'ModBase', 'CustomerOrder', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CustomerOrderKeyData']._ICustomerOrderNo.value,
			_rng,
			'CustomerOrderItem',
			'formCustomerOrderItemTop',
			showTableCustomerOrderItem) ;
	return false ;
}
