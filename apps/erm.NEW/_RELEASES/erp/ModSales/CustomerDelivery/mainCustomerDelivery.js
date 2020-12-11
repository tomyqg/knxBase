/**
 * mainCustomerDelivery.js
 * =============
 *
 * registers the module in the central database
 */
new mainCustomerDelivery() ;
/**
 * @returns {scrCustomerDelivery}
 */
function	mainCustomerDelivery() {
	dBegin( 1, "scrCustomerDelivery.js", "scrCustomerDelivery", "__constructor()") ;
	wapScreen.call( this, "CustomerDelivery") ;				// instantiate the super-class!!!
	this.package	=	"ModBase" ;			// directory of the module
	this.module	=	"CustomerDelivery" ;					// sub-directory of the screen
	this.coreObject	=	"CustomerDelivery" ;
	this.keyForm	=	"CustomerDeliveryKeyData" ;		// form
	this.keyField	=	getFormField( 'CustomerDeliveryKeyData', 'CustomerDeliveryNo') ;
	this.delConfDialog	=	"/ModSales/CustomerDelivery/confCustomerDeliveryDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "CustomerDelivery"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCustomerDelivery", {
										xmlTableName:	"TableSelCustomerDelivery"
									,	objectClass:	"CustomerDelivery"
									,	module:			"ModSales"
									,	screen:			"CustomerDelivery"
									,	selectorName:	"selCustomerDelivery"
									,	formFilterName: "formSelCustomerDeliveryFilter"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerDeliveryMainDataEntry") ;
															dEnd( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
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
															dBegin( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															dEnd( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	this.selectArticleSalesPriceCache.onSelectById	=	function( _id) {
		dBegin( 1, "mainCustomerDelivery.js", "scrCustomerDelivery", "onSelectById( <_parent>, " + String( _id) + ")") ;
		result	=	this.parent.selectSPById( _id) ;
		dEnd( 1, "mainCustomerDelivery.js", "scrCustomerDelivery", "onSelectById( <_parent>, <_id>)") ;
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
	dTrace( 2, "mainCustomerDelivery.js", "scrCustomerDelivery", "*", "creating gridCustomerDeliveryOV") ;
	this.gridCustomerDeliveryOV	=	new wapGrid( this, "gridCustomerDeliveryOV", {
										xmlTableName:	"TableCustomerDeliveryOV"
									,	object:			"CustomerDelivery"
									,	module:			"ModSales"
									,	screen:			"CustomerDelivery"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerDeliveryMainDataEntry") ;
															dEnd( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCustomerDelivery.js", "scrCustomerDelivery", "*", "calling gridCustomerDeliveryOV._onFirstPage()") ;
	this.gridCustomerDeliveryOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerDeliveryItem
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCustomerDeliveryItem	=	new wapGrid( this, "gridCustomerDeliveryItem", {
										object:			"CustomerDeliveryItem"
									,	module :		"ModBase"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCustomerDeliveryItem"
									,	moduleName: 	"ModSales"
									,	subModuleName:	"CustomerDelivery"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid{gridCustomerDeliveryItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid{gridCustomerDeliveryItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid{gridCustomerDeliveryItem}", "onSelectById( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCustomerDelivery{wapGrid.js}", "wapGrid{gridCustomerDeliveryItem}", "onSelectById( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCustomerDeliveryItem.addItem	=	function() {
										this.parent.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
									} ;
	this.selectSPById	=	function( _id) {
		dBegin( 1, "mainCustomerDelivery.js", "scrCustomerDelivery", "selectSPById( <_id>)") ;
		qty	=	1 ;	// parseInt( qtyField.value) ;
		this.gridCustomerDeliveryItem.dataSource.onMisc( this.keyField.value, _id, qty, null, "addPos") ;
		dEnd( 1, "mainCustomerDelivery.js", "scrCustomerDelivery", "selectSPById( <_id>)") ;
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
	dTrace( 2, "mainCustomerDelivery.js", "mainCustomerDelivery", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerDelivery.js", "mainCustomerDelivery", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainCustomerDelivery.js", "mainCustomerDelivery", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerDelivery.js", "scrCustomerDelivery", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCustomerDelivery.js", "scrCustomerDelivery", "fncShow( <_response>)") ;
		dEnd( 1, "mainCustomerDelivery.js", "scrCustomerDelivery", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCustomerDelivery.js", "scrCustomerDelivery", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCustomerDelivery.js", "scrCustomerDelivery", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData(  _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainCustomerDelivery.js", "mainCustomerDelivery", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=CustomerDelivery"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
			this.gridCustomerDeliveryItem._onFirstPage() ;
		}
		dEnd( 1, "mainCustomerDelivery.js", "scrCustomerDelivery", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onJS	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerDelivery.js", "mainCustomerDelivery", "onJS( <...>)") ;
//		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		this.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
		dEnd( 1, "mainCustomerDelivery.js", "mainCustomerDelivery", "onJS( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerDelivery.js", "scrCustomerDelivery", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectById( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCustomerDelivery.js", "scrCustomerDelivery", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCustomerDeliverySurveyEntry") ;
	dEnd( 1, "scrCustomerDelivery.js", "scrCustomerDelivery", "__constructor()") ;
}
function	newCustomerDelivery() {
	_debugL( 0x00000001, "mainCustomerDelivery.js::newCustomerDelivery(): begin\n") ;
	myScreen	=	screenShow( "screenTCustomerDelivery", hookNew) ;
	if ( myScreen.isLoaded) {
		hookNew() ;
	}
	_debugL( 0x00000001, "mainCustomerDelivery.js::newCustomerDelivery(): end\n") ;
}
/**
 *
 */
/**
*
*/
function	addArticleBySPId( _avpId, qtyFieldName, artikelNr) {
	_debugL( 0x00000001, "mainCustomerDelivery.js::addArticleBySPId(" + _avpId + "): begin\n") ;
//	qtyField	=	document.getElementById( qtyFieldName) ;
	qty	=	1 ;	// parseInt( qtyField.value) ;
	cuOrdrNo	=	getFormField( "CustomerDeliveryKeyData", "_ICustomerDeliveryNo") ;
	requestUni( 'ModBase', 'CustomerDelivery', '/Common/hdlObject.php', 'addPos', cuOrdrNo.value, _avpId, qty, null, showCustomerDeliveryAll) ;
	_debugL( 0x00000001, "mainCustomerDelivery.js::addArticleBySPId(" + _avpId + "): end\n") ;
	return false ;
}
function	showCustomerDeliveryEMailRcvr( response) {
	fieldEMail	=	getFormField( "formCustomerDeliveryDocEMail", "_IeMail") ;
	fieldEMailCC	=	getFormField( "formCustomerDeliveryDocEMail", "_IeMailCC") ;
	fieldEMailBCC	=	getFormField( "formCustomerDeliveryDocEMail", "_IeMailBCC") ;
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
function	showCustomerDelivery( response) {
	var	lockInfo ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerDelivery")[0] ;
	if ( cuOrdr) {

		myCustomerDeliveryNo	=	response.getElementsByTagName( "CustomerDeliveryNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "CustomerDeliveryKeyData") ;
		dispAttrs( attrs, "formCustomerDeliveryMain") ;
		dispAttrs( attrs, "formCustomerDeliveryModi") ;
		dispAttrs( attrs, "formCustomerDeliveryCalc") ;
		dispAttrs( attrs, "formCustomerDeliveryDocEMail") ;
		dispAttrs( attrs, "formCustomerDeliveryDocFAX") ;
		dispAttrs( attrs, "formCustomerDeliveryDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCustomerDelivery") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerDelivery', '/Common/hdlObject.php', 'lock', document.forms['CustomerDeliveryKeyData']._ICustomerDeliveryNo.value, '', '', null, showCustomerDelivery) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerDelivery', '/Common/hdlObject.php', 'unlock', document.forms['CustomerDeliveryKeyData']._ICustomerDeliveryNo.value, '', '', null, showCustomerDelivery) ; \" />" ;
		}

		/**
		 *
		 */
		showCustomerDeliveryDocInfo( response) ;

		/**
		 *
		var	myFieldRabatt	=	getFormField( "formCustomerDeliveryCalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCustomerDeliveryCalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCustomerDeliveryCalc", "_CRohmarge") ;

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

function	showCustomerDeliveryDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCustomerDelivery") ;
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
function	showTableCustomerDeliveryItem( response) {
	updTableHead( response, "formCustomerDeliveryItemTop", "formCustomerDeliveryItemBot") ;
	showTable( response, "TableCustomerDeliveryItem", "CustomerDeliveryItem", "CustomerDelivery", document.forms['CustomerDeliveryKeyData']._ICustomerDeliveryNo.value, "showCustomerDeliveryAll", "refreshTableCustomerDeliveryItem") ;
}
function	refreshTableCustomerDeliveryItem( response) {
	refreshTable( response, "TableCustomerDeliveryItem", "CustomerDeliveryItem", "CustomerDelivery", document.forms['CustomerDeliveryKeyData']._ICustomerDeliveryNo.value, "showCustomerDeliveryAll") ;
}
/**
 *
 */
function	showCustomerDeliveryVersand( response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerDelivery")[0] ;
	if ( cuOrdr) {
		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "formCustomerDeliveryDocEMail") ;
		dispAttrs( attrs, "formCustomerDeliveryDocFAX") ;
		dispAttrs( attrs, "formCustomerDeliveryDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCustomerDeliveryDocEMail") ;
		dispAttrs( attrs, "formCustomerDeliveryDocFAX") ;
		dispAttrs( attrs, "formCustomerDeliveryDocPDF") ;
	}
}

function	showCustomerDeliveryDocList( response) {
	showDocList( response, "TableCustomerDeliveryDocs") ;
}

function	showCustomerDeliveryDocUpload( response) {
	myField	=	getFormField( "formCustomerDeliveryDocUpload", "_DRefNr") ;
	myField.value	=	response.getElementsByTagName( "CustomerDeliveryNo")[0].childNodes[0].nodeValue ;
}
/**
 *
 * @param _cuOrdrNo
 * @param _sonst
 * @return
 */
function	createDirBest( _cuOrdrNo, _sonst) {
	markerCustomerDeliveryNo	=	_cuOrdrNo ;
	confAction( '/ModBase/CustomerDelivery/confCustomerDeliveryCreateDirBest.php', doCustomerDeliveryCreateDirBest) ;
	return false ;
}
function	doCustomerDeliveryCreateDirBest() {
	confDialog.hide() ;
	requestUni( 'ModBase', 'CustomerDelivery', '/Common/hdlObject.php', 'createDirBest', markerCustomerDeliveryNo, '', '', null, showCustomerDelivery) ;
}
/**
 *
 * @return
 */
function	showEMailCustomerDelivery() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModBase/CustomerDelivery/getAnschreiben.php?CustomerDeliveryNo="+document.forms['CustomerDeliveryKeyData']._ICustomerDeliveryNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailCustomerDelivery() {
	dlgPreview.hide() ;
}
function	refCustomerDeliveryItem( _rng) {
	requestUni( 'ModBase', 'CustomerDelivery', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CustomerDeliveryKeyData']._ICustomerDeliveryNo.value,
			_rng,
			'CustomerDeliveryItem',
			'formCustomerDeliveryItemTop',
			showTableCustomerDeliveryItem) ;
	return false ;
}
