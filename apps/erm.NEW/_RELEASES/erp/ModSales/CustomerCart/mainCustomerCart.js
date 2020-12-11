/**
 * mainCustomerCart.js
 * =============
 *
 * registers the module in the central database
 */
new mainCustomerCart() ;
/**
 * @returns {scrCustomerCart}
 */
function	mainCustomerCart() {
	dBegin( 1, "scrCustomerCart.js", "scrCustomerCart", "__constructor()") ;
	wapScreen.call( this, "CustomerCart") ;				// instantiate the super-class!!!
	this.package	=	"ModBase" ;			// directory of the module
	this.module	=	"CustomerCart" ;					// sub-directory of the screen
	this.coreObject	=	"CustomerCart" ;
	this.keyForm	=	"CustomerCartKeyData" ;		// form
	this.keyField	=	getFormField( 'CustomerCartKeyData', 'CustomerCartNo') ;
	this.delConfDialog	=	"/ModSales/CustomerCart/confCustomerCartDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "CustomerCart"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCustomerCart", {
										xmlTableName:	"TableSelCustomerCart"
									,	objectClass:	"CustomerCart"
									,	module:			"ModSales"
									,	screen:			"CustomerCart"
									,	selectorName:	"selCustomerCart"
									,	formFilterName: "formSelCustomerCartFilter"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerCart{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerCartMainDataEntry") ;
															dEnd( 102, "mainCustomerCart{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
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
															dBegin( 102, "mainCustomerCart{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															dEnd( 102, "mainCustomerCart{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	this.selectArticleSalesPriceCache.onSelectById	=	function( _id) {
		dBegin( 1, "mainCustomerCart.js", "scrCustomerCart", "onSelectById( <_parent>, " + String( _id) + ")") ;
		result	=	this.parent.selectSPById( _id) ;
		dEnd( 1, "mainCustomerCart.js", "scrCustomerCart", "onSelectById( <_parent>, <_id>)") ;
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
	dTrace( 2, "mainCustomerCart.js", "scrCustomerCart", "*", "creating gridCustomerCartOV") ;
	this.gridCustomerCartOV	=	new wapGrid( this, "gridCustomerCartOV", {
										xmlTableName:	"TableCustomerCartOV"
									,	object:			"CustomerCart"
									,	module:			"ModSales"
									,	screen:			"CustomerCart"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerCart{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerCart{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerCart{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerCartMainDataEntry") ;
															dEnd( 102, "mainCustomerCart{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCustomerCart.js", "scrCustomerCart", "*", "calling gridCustomerCartOV._onFirstPage()") ;
	this.gridCustomerCartOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerCartItem
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCustomerCartItem	=	new wapGrid( this, "gridCustomerCartItem", {
										object:			"CustomerCartItem"
									,	module :		"ModBase"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCustomerCartItem"
									,	moduleName: 	"ModSales"
									,	subModuleName:	"CustomerCart"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomerCart{wapGrid.js}", "wapGrid{gridCustomerCartItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomerCart{wapGrid.js}", "wapGrid{gridCustomerCartItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomerCart{wapGrid.js}", "wapGrid{gridCustomerCartItem}", "onSelectById( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCustomerCart{wapGrid.js}", "wapGrid{gridCustomerCartItem}", "onSelectById( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCustomerCartItem.addItem	=	function() {
										this.parent.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
									} ;
	this.selectSPById	=	function( _id) {
		dBegin( 1, "mainCustomerCart.js", "scrCustomerCart", "selectSPById( <_id>)") ;
		qty	=	1 ;	// parseInt( qtyField.value) ;
		this.gridCustomerCartItem.dataSource.onMisc( this.keyField.value, _id, qty, null, "addPos") ;
		dEnd( 1, "mainCustomerCart.js", "scrCustomerCart", "selectSPById( <_id>)") ;
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
	dTrace( 2, "mainCustomerCart.js", "mainCustomerCart", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerCart.js", "mainCustomerCart", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainCustomerCart.js", "mainCustomerCart", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerCart.js", "scrCustomerCart", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCustomerCart.js", "scrCustomerCart", "fncShow( <_response>)") ;
		dEnd( 1, "mainCustomerCart.js", "scrCustomerCart", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCustomerCart.js", "scrCustomerCart", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCustomerCart.js", "scrCustomerCart", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData(  _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainCustomerCart.js", "mainCustomerCart", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=CustomerCart"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
			this.gridCustomerCartItem._onFirstPage() ;
		}
		dEnd( 1, "mainCustomerCart.js", "scrCustomerCart", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onJS	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCustomerCart.js", "mainCustomerCart", "onJS( <...>)") ;
//		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		this.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
		dEnd( 1, "mainCustomerCart.js", "mainCustomerCart", "onJS( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomerCart.js", "scrCustomerCart", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectById( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCustomerCart.js", "scrCustomerCart", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCustomerCartSurveyEntry") ;
	dEnd( 1, "scrCustomerCart.js", "scrCustomerCart", "__constructor()") ;
}
function	newCustomerCart() {
	_debugL( 0x00000001, "mainCustomerCart.js::newCustomerCart(): begin\n") ;
	myScreen	=	screenShow( "screenTCustomerCart", hookNew) ;
	if ( myScreen.isLoaded) {
		hookNew() ;
	}
	_debugL( 0x00000001, "mainCustomerCart.js::newCustomerCart(): end\n") ;
}
/**
 *
 */
/**
*
*/
function	addArticleBySPId( _avpId, qtyFieldName, artikelNr) {
	_debugL( 0x00000001, "mainCustomerCart.js::addArticleBySPId(" + _avpId + "): begin\n") ;
//	qtyField	=	document.getElementById( qtyFieldName) ;
	qty	=	1 ;	// parseInt( qtyField.value) ;
	cuOrdrNo	=	getFormField( "CustomerCartKeyData", "_ICustomerCartNo") ;
	requestUni( 'ModBase', 'CustomerCart', '/Common/hdlObject.php', 'addPos', cuOrdrNo.value, _avpId, qty, null, showCustomerCartAll) ;
	_debugL( 0x00000001, "mainCustomerCart.js::addArticleBySPId(" + _avpId + "): end\n") ;
	return false ;
}
function	showCustomerCartEMailRcvr( response) {
	fieldEMail	=	getFormField( "formCustomerCartDocEMail", "_IeMail") ;
	fieldEMailCC	=	getFormField( "formCustomerCartDocEMail", "_IeMailCC") ;
	fieldEMailBCC	=	getFormField( "formCustomerCartDocEMail", "_IeMailBCC") ;
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
function	showCustomerCart( response) {
	var	lockInfo ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerCart")[0] ;
	if ( cuOrdr) {

		myCustomerCartNo	=	response.getElementsByTagName( "CustomerCartNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "CustomerCartKeyData") ;
		dispAttrs( attrs, "formCustomerCartMain") ;
		dispAttrs( attrs, "formCustomerCartModi") ;
		dispAttrs( attrs, "formCustomerCartCalc") ;
		dispAttrs( attrs, "formCustomerCartDocEMail") ;
		dispAttrs( attrs, "formCustomerCartDocFAX") ;
		dispAttrs( attrs, "formCustomerCartDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCustomerCart") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerCart', '/Common/hdlObject.php', 'lock', document.forms['CustomerCartKeyData']._ICustomerCartNo.value, '', '', null, showCustomerCart) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CustomerCart', '/Common/hdlObject.php', 'unlock', document.forms['CustomerCartKeyData']._ICustomerCartNo.value, '', '', null, showCustomerCart) ; \" />" ;
		}

		/**
		 *
		 */
		showCustomerCartDocInfo( response) ;

		/**
		 *
		var	myFieldRabatt	=	getFormField( "formCustomerCartCalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCustomerCartCalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCustomerCartCalc", "_CRohmarge") ;

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

function	showCustomerCartDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCustomerCart") ;
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
function	showTableCustomerCartItem( response) {
	updTableHead( response, "formCustomerCartItemTop", "formCustomerCartItemBot") ;
	showTable( response, "TableCustomerCartItem", "CustomerCartItem", "CustomerCart", document.forms['CustomerCartKeyData']._ICustomerCartNo.value, "showCustomerCartAll", "refreshTableCustomerCartItem") ;
}
function	refreshTableCustomerCartItem( response) {
	refreshTable( response, "TableCustomerCartItem", "CustomerCartItem", "CustomerCart", document.forms['CustomerCartKeyData']._ICustomerCartNo.value, "showCustomerCartAll") ;
}
/**
 *
 */
function	showCustomerCartVersand( response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CustomerCart")[0] ;
	if ( cuOrdr) {
		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "formCustomerCartDocEMail") ;
		dispAttrs( attrs, "formCustomerCartDocFAX") ;
		dispAttrs( attrs, "formCustomerCartDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCustomerCartDocEMail") ;
		dispAttrs( attrs, "formCustomerCartDocFAX") ;
		dispAttrs( attrs, "formCustomerCartDocPDF") ;
	}
}

function	showCustomerCartDocList( response) {
	showDocList( response, "TableCustomerCartDocs") ;
}

function	showCustomerCartDocUpload( response) {
	myField	=	getFormField( "formCustomerCartDocUpload", "_DRefNr") ;
	myField.value	=	response.getElementsByTagName( "CustomerCartNo")[0].childNodes[0].nodeValue ;
}
/**
 *
 * @param _cuOrdrNo
 * @param _sonst
 * @return
 */
function	createDirBest( _cuOrdrNo, _sonst) {
	markerCustomerCartNo	=	_cuOrdrNo ;
	confAction( '/ModBase/CustomerCart/confCustomerCartCreateDirBest.php', doCustomerCartCreateDirBest) ;
	return false ;
}
function	doCustomerCartCreateDirBest() {
	confDialog.hide() ;
	requestUni( 'ModBase', 'CustomerCart', '/Common/hdlObject.php', 'createDirBest', markerCustomerCartNo, '', '', null, showCustomerCart) ;
}
/**
 *
 * @return
 */
function	showEMailCustomerCart() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModBase/CustomerCart/getAnschreiben.php?CustomerCartNo="+document.forms['CustomerCartKeyData']._ICustomerCartNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailCustomerCart() {
	dlgPreview.hide() ;
}
function	refCustomerCartItem( _rng) {
	requestUni( 'ModBase', 'CustomerCart', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CustomerCartKeyData']._ICustomerCartNo.value,
			_rng,
			'CustomerCartItem',
			'formCustomerCartItemTop',
			showTableCustomerCartItem) ;
	return false ;
}
