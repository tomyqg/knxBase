/**
 * mainCustomerOffer.js
 * =============
 *
 * registers the module in the central database
 */
new mainCustomerOffer() ;
/**
 * @returns {scrCustomerOffer}
 */
function	mainCustomerOffer() {
	dBegin( 1, "scrCustomerOffer.js", "scrCustomerOffer", "__constructor()") ;
	wapScreen.call( this, "CustomerOffer") ;				// instantiate the super-class!!!
	this.package	=	"ModBase" ;			// directory of the module
	this.module	=	"CustomerOffer" ;					// sub-directory of the screen
	this.coreObject	=	"CustomerOffer" ;
	this.keyForm	=	"CustomerOfferKeyData" ;		// form
	this.keyField	=	getFormField( 'CustomerOfferKeyData', 'CustomerOfferNo') ;
	this.delConfDialog	=	"/ModSales/CustomerOffer/confCustomerOfferDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "CustomerOffer"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCustomerOffer", {
										xmlTableName:	"TableSelCustomerOffer"
									,	objectClass:	"CustomerOffer"
									,	module:			"ModSales"
									,	screen:			"CustomerOffer"
									,	selectorName:	"selCustomerOffer"
									,	formFilterName: "formSelCustomerOfferFilter"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerOfferMainDataEntry") ;
															dEnd( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
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
															dBegin( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															dEnd( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	this.selectArticleSalesPriceCache.onSelectById	=	function( _id) {
		dBegin( 1, "mainCustomerOffer.js", "scrCustomerOffer", "onSelectById( <_parent>, " + String( _id) + ")") ;
		result	=	this.parent.selectSPById( _id) ;
		dEnd( 1, "mainCustomerOffer.js", "scrCustomerOffer", "onSelectById( <_parent>, <_id>)") ;
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
	dTrace( 2, "mainCustomerOffer.js", "scrCustomerOffer", "*", "creating gridCustomerOfferOV") ;
	this.gridCustomerOfferOV	=	new wapGrid( this, "gridCustomerOfferOV", {
										xmlTableName:	"TableCustomerOfferOV"
									,	object:			"CustomerOffer"
									,	module:			"ModSales"
									,	screen:			"CustomerOffer"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerOfferMainDataEntry") ;
															dEnd( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCustomerOffer.js", "scrCustomerOffer", "*", "calling gridCustomerOfferOV._onFirstPage()") ;
	this.gridCustomerOfferOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerOfferItem
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCustomerOfferItem	=	new wapGrid( this, "gridCustomerOfferItem", {
										object:			"CustomerOfferItem"
									,	module :		"ModBase"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCustomerOfferItem"
									,	moduleName: 	"ModSales"
									,	subModuleName:	"CustomerOffer"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid{gridCustomerOfferItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid{gridCustomerOfferItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid{gridCustomerOfferItem}", "onSelectById( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCustomerOffer{wapGrid.js}", "wapGrid{gridCustomerOfferItem}", "onSelectById( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCustomerOfferItem.addItem	=	function() {
										this.parent.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
									} ;
	this.selectSPById	=	function( _id) {
		dBegin( 1, "mainCustomerOffer.js", "scrCustomerOffer", "selectSPById( <_id>)") ;
		qty	=	1 ;	// parseInt( qtyField.value) ;
		this.gridCustomerOfferItem.dataSource.onMisc( this.keyField.value, _id, qty, null, "addPos") ;
		dEnd( 1, "mainCustomerOffer.js", "scrCustomerOffer", "selectSPById( <_id>)") ;
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
	dTrace( 2, "mainCustomerOffer.js", "mainCustomerOffer", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerOffer.js", "mainCustomerOffer", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainCustomerOffer.js", "mainCustomerOffer", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerOffer.js", "scrCustomerOffer", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCustomerOffer.js", "scrCustomerOffer", "fncShow( <_response>)") ;
		dEnd( 1, "mainCustomerOffer.js", "scrCustomerOffer", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCustomerOffer.js", "scrCustomerOffer", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCustomerOffer.js", "scrCustomerOffer", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData(  _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainCustomerOffer.js", "mainCustomerOffer", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=CustomerOffer"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
			this.gridCustomerOfferItem._onFirstPage() ;
		}
		dEnd( 1, "mainCustomerOffer.js", "scrCustomerOffer", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onJS	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerOffer.js", "mainCustomerOffer", "onJS( <...>)") ;
//		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		this.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
		dEnd( 1, "mainCustomerOffer.js", "mainCustomerOffer", "onJS( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerOffer.js", "scrCustomerOffer", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectById( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCustomerOffer.js", "scrCustomerOffer", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCustomerOfferSurveyEntry") ;
	dEnd( 1, "scrCustomerOffer.js", "scrCustomerOffer", "__constructor()") ;
}
function	newCustomerOffer() {
	_debugL( 0x00000001, "mainCustomerOffer.js::newCustomerOffer(): begin\n") ;
	myScreen	=	screenShow( "screenTCustomerOffer", hookNew) ;
	if ( myScreen.isLoaded) {
		hookNew() ;
	}
	_debugL( 0x00000001, "mainCustomerOffer.js::newCustomerOffer(): end\n") ;
}
/**
 *
 */
/**
*
*/
function	addArticleBySPId( _avpId, qtyFieldName, artikelNr) {
	_debugL( 0x00000001, "mainCustomerOffer.js::addArticleBySPId(" + _avpId + "): begin\n") ;
//	qtyField	=	document.getElementById( qtyFieldName) ;
	qty	=	1 ;	// parseInt( qtyField.value) ;
	cuOrdrNo	=	getFormField( "CustomerOfferKeyData", "_ICustomerOfferNo") ;
	requestUni( 'ModBase', 'CustomerOffer', '/Common/hdlObject.php', 'addPos', cuOrdrNo.value, _avpId, qty, null, showCustomerOfferAll) ;
	_debugL( 0x00000001, "mainCustomerOffer.js::addArticleBySPId(" + _avpId + "): end\n") ;
	return false ;
}
function	showCustomerOfferEMailRcvr( response) {
	fieldEMail	=	getFormField( "formCustomerOfferDocEMail", "_IeMail") ;
	fieldEMailCC	=	getFormField( "formCustomerOfferDocEMail", "_IeMailCC") ;
	fieldEMailBCC	=	getFormField( "formCustomerOfferDocEMail", "_IeMailBCC") ;
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
function	showCustomerOffer( response) {
	var	lockInfo ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerOffer")[0] ;
	if ( cuOrdr) {

		myCustomerOfferNo	=	response.getElementsByTagName( "CustomerOfferNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "CustomerOfferKeyData") ;
		dispAttrs( attrs, "formCustomerOfferMain") ;
		dispAttrs( attrs, "formCustomerOfferModi") ;
		dispAttrs( attrs, "formCustomerOfferCalc") ;
		dispAttrs( attrs, "formCustomerOfferDocEMail") ;
		dispAttrs( attrs, "formCustomerOfferDocFAX") ;
		dispAttrs( attrs, "formCustomerOfferDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCustomerOffer") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerOffer', '/Common/hdlObject.php', 'lock', document.forms['CustomerOfferKeyData']._ICustomerOfferNo.value, '', '', null, showCustomerOffer) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerOffer', '/Common/hdlObject.php', 'unlock', document.forms['CustomerOfferKeyData']._ICustomerOfferNo.value, '', '', null, showCustomerOffer) ; \" />" ;
		}

		/**
		 *
		 */
		showCustomerOfferDocInfo( response) ;

		/**
		 *
		var	myFieldRabatt	=	getFormField( "formCustomerOfferCalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCustomerOfferCalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCustomerOfferCalc", "_CRohmarge") ;

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

function	showCustomerOfferDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCustomerOffer") ;
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
function	showTableCustomerOfferItem( response) {
	updTableHead( response, "formCustomerOfferItemTop", "formCustomerOfferItemBot") ;
	showTable( response, "TableCustomerOfferItem", "CustomerOfferItem", "CustomerOffer", document.forms['CustomerOfferKeyData']._ICustomerOfferNo.value, "showCustomerOfferAll", "refreshTableCustomerOfferItem") ;
}
function	refreshTableCustomerOfferItem( response) {
	refreshTable( response, "TableCustomerOfferItem", "CustomerOfferItem", "CustomerOffer", document.forms['CustomerOfferKeyData']._ICustomerOfferNo.value, "showCustomerOfferAll") ;
}
/**
 *
 */
function	showCustomerOfferVersand( response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerOffer")[0] ;
	if ( cuOrdr) {
		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "formCustomerOfferDocEMail") ;
		dispAttrs( attrs, "formCustomerOfferDocFAX") ;
		dispAttrs( attrs, "formCustomerOfferDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCustomerOfferDocEMail") ;
		dispAttrs( attrs, "formCustomerOfferDocFAX") ;
		dispAttrs( attrs, "formCustomerOfferDocPDF") ;
	}
}

function	showCustomerOfferDocList( response) {
	showDocList( response, "TableCustomerOfferDocs") ;
}

function	showCustomerOfferDocUpload( response) {
	myField	=	getFormField( "formCustomerOfferDocUpload", "_DRefNr") ;
	myField.value	=	response.getElementsByTagName( "CustomerOfferNo")[0].childNodes[0].nodeValue ;
}
/**
 *
 * @param _cuOrdrNo
 * @param _sonst
 * @return
 */
function	createDirBest( _cuOrdrNo, _sonst) {
	markerCustomerOfferNo	=	_cuOrdrNo ;
	confAction( '/ModBase/CustomerOffer/confCustomerOfferCreateDirBest.php', doCustomerOfferCreateDirBest) ;
	return false ;
}
function	doCustomerOfferCreateDirBest() {
	confDialog.hide() ;
	requestUni( 'ModBase', 'CustomerOffer', '/Common/hdlObject.php', 'createDirBest', markerCustomerOfferNo, '', '', null, showCustomerOffer) ;
}
/**
 *
 * @return
 */
function	showEMailCustomerOffer() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModBase/CustomerOffer/getAnschreiben.php?CustomerOfferNo="+document.forms['CustomerOfferKeyData']._ICustomerOfferNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailCustomerOffer() {
	dlgPreview.hide() ;
}
function	refCustomerOfferItem( _rng) {
	requestUni( 'ModBase', 'CustomerOffer', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CustomerOfferKeyData']._ICustomerOfferNo.value,
			_rng,
			'CustomerOfferItem',
			'formCustomerOfferItemTop',
			showTableCustomerOfferItem) ;
	return false ;
}
