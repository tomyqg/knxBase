/**
 * mainCashSale.js
 * =============
 *
 * registers the module in the central database
 */
new mainCashSale() ;
/**
 * @returns {scrCashSale}
 */
function	mainCashSale() {
	dBegin( 1, "scrCashSale.js", "scrCashSale", "__constructor()") ;
	wapScreen.call( this, "CashSale") ;				// instantiate the super-class!!!
	this.package	=	"ModBase" ;			// directory of the module
	this.module	=	"CashSale" ;					// sub-directory of the screen
	this.coreObject	=	"CashSale" ;
	this.keyForm	=	"CashSaleKeyData" ;		// form
	this.keyField	=	getFormField( 'CashSaleKeyData', 'CustomerOrderNo') ;
	this.delConfDialog	=	null ;
	this.dataSource	=	new wapDataSource( this, { object: "CashSale"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCashSale", {
										xmlTableName:	"TableSelCashSale"
									,	objectClass:	"CashSale"
									,	module:			"ModSales"
									,	screen:			"CashSale"
									,	selectorName:	"selCashSale"
									,	formFilterName: "formSelCashSaleFilter"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCashSale{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCashSaleMainDataEntry") ;
															dEnd( 102, "mainCashSale{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
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
									,	formFilterName: "formSelArticleSalesPriceCacheFilter"
									,	parentDS:		this.dataSource
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCashSale{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															dEnd( 102, "mainCashSale{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
							}) ;
	this.selectArticleSalesPriceCache.onSelectById	=	function( _id) {
		dBegin( 1, "mainCashSale.js", "scrCashSale", "onSelectById( <_parent>, " + String( _id) + ")") ;
		result	=	this.parent.selectSPById( _id) ;
		dEnd( 1, "mainCashSale.js", "scrCashSale", "onSelectById( <_parent>, <_id>)") ;
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
	dTrace( 2, "mainCashSale.js", "scrCashSale", "*", "creating gridCashSaleOV") ;
	this.gridCashSaleOV	=	new wapGrid( this, "gridCashSaleOV", {
										xmlTableName:	"TableCashSaleOV"
									,	object:			"CashSale"
									,	module:			"ModSales"
									,	screen:			"CashSale"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCashSale{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCashSale{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCashSale{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCashSaleMainDataEntry") ;
															dEnd( 102, "mainCashSale{wapGrid.js}", "wapGrid", "onSelectById( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCashSale.js", "scrCashSale", "*", "calling gridCashSaleOV._onFirstPage()") ;
	this.gridCashSaleOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CashSaleItem
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCashSaleItem	=	new wapGrid( this, "gridCashSaleItem", {
										object:			"CashSaleItem"
									,	module :		"ModBase"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCashSaleItem"
									,	moduleName: 	"ModSales"
									,	subModuleName:	"CashSale"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCashSale{wapGrid.js}", "wapGrid{gridCashSaleItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCashSale{wapGrid.js}", "wapGrid{gridCashSaleItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCashSale{wapGrid.js}", "wapGrid{gridCashSaleItem}", "onSelectById( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCashSale{wapGrid.js}", "wapGrid{gridCashSaleItem}", "onSelectById( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCashSaleItem.addItem	=	function() {
										this.parent.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
									} ;
	this.selectSPById	=	function( _id) {
		dBegin( 1, "mainCashSale.js", "scrCashSale", "selectSPById( <_id>)") ;
		qty	=	1 ;	// parseInt( qtyField.value) ;
		this.gridCashSaleItem.dataSource.onMisc( this.keyField.value, _id, qty, null, "addPos") ;
		dEnd( 1, "mainCashSale.js", "scrCashSale", "selectSPById( <_id>)") ;
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
	dTrace( 2, "mainCashSale.js", "mainCashSale", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCashSale.js", "mainCashSale", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainCashSale.js", "mainCashSale", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCashSale.js", "scrCashSale", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCashSale.js", "scrCashSale", "fncShow( <_response>)") ;
		dEnd( 1, "mainCashSale.js", "scrCashSale", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCashSale.js", "scrCashSale", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCashSale.js", "scrCashSale", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData(  _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainCashSale.js", "mainCashSale", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=CashSale"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
			this.gridCashSaleItem._onFirstPage() ;
		}
		dEnd( 1, "mainCashSale.js", "scrCashSale", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onJS	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainCashSale.js", "mainCashSale", "onJS( <...>)") ;
//		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		this.selectArticleSalesPriceCache.startSelect( '', -1, '') ;
		dEnd( 1, "mainCashSale.js", "mainCashSale", "onJS( <...>)") ;
	} ;
	/**
	 *
	 */
	this.addToField	=	function( _key) {
		dBegin( 1, "mainCashSale.js", "mainCashSale", "onJS( <...>)") ;
		dEnd( 1, "mainCashSale.js", "mainCashSale", "onJS( <...>)") ;
	}
	/**
	 *
	 */
	dTrace( 2, "mainCashSale.js", "scrCashSale", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectById( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCashSale.js", "scrCashSale", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCashSaleSurveyEntry") ;
	dEnd( 1, "scrCashSale.js", "scrCashSale", "__constructor()") ;
}
function	newCashSale() {
	_debugL( 0x00000001, "mainCashSale.js::newCashSale(): begin\n") ;
	myScreen	=	screenShow( "screenTCashSale", hookNew) ;
	if ( myScreen.isLoaded) {
		hookNew() ;
	}
	_debugL( 0x00000001, "mainCashSale.js::newCashSale(): end\n") ;
}
/**
 *
 */
/**
*
*/
function	addArticleBySPId( _avpId, qtyFieldName, artikelNr) {
	_debugL( 0x00000001, "mainCashSale.js::addArticleBySPId(" + _avpId + "): begin\n") ;
//	qtyField	=	document.getElementById( qtyFieldName) ;
	qty	=	1 ;	// parseInt( qtyField.value) ;
	cuOrdrNo	=	getFormField( "CashSaleKeyData", "_ICashSaleNo") ;
	requestUni( 'ModBase', 'CashSale', '/Common/hdlObject.php', 'addPos', cuOrdrNo.value, _avpId, qty, null, showCashSaleAll) ;
	_debugL( 0x00000001, "mainCashSale.js::addArticleBySPId(" + _avpId + "): end\n") ;
	return false ;
}
function	showCashSaleEMailRcvr( response) {
	fieldEMail	=	getFormField( "formCashSaleDocEMail", "_IeMail") ;
	fieldEMailCC	=	getFormField( "formCashSaleDocEMail", "_IeMailCC") ;
	fieldEMailBCC	=	getFormField( "formCashSaleDocEMail", "_IeMailBCC") ;
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
function	showCashSale( response) {
	var	lockInfo ;
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CashSale")[0] ;
	if ( cuOrdr) {

		myCashSaleNo	=	response.getElementsByTagName( "CashSaleNo")[0].childNodes[0].nodeValue ;
		myPrefix	=	response.getElementsByTagName( "Prefix")[0].childNodes[0].nodeValue ;
		myPostfix	=	response.getElementsByTagName( "Postfix")[0].childNodes[0].nodeValue ;

		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "CashSaleKeyData") ;
		dispAttrs( attrs, "formCashSaleMain") ;
		dispAttrs( attrs, "formCashSaleModi") ;
		dispAttrs( attrs, "formCashSaleCalc") ;
		dispAttrs( attrs, "formCashSaleDocEMail") ;
		dispAttrs( attrs, "formCashSaleDocFAX") ;
		dispAttrs( attrs, "formCashSaleDocPDF") ;

		lockInfo	=	document.getElementById( "lockStateCashSale") ;
		myLockState	=	parseInt( response.getElementsByTagName( "LockState")[0].childNodes[0].nodeValue) ;
		if ( myLockState == 0) {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/Green/32/unlocked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CashSale', '/Common/hdlObject.php', 'lock', document.forms['CashSaleKeyData']._ICashSaleNo.value, '', '', null, showCashSale) ; \" />" ;
		} else {
			lockInfo.innerHTML	=	"<input type=\"image\" src=\"/Rsrc/licon/yellow/32/locked.png\" "
						+ "onclick=\"requestUni( 'ModBase', 'CashSale', '/Common/hdlObject.php', 'unlock', document.forms['CashSaleKeyData']._ICashSaleNo.value, '', '', null, showCashSale) ; \" />" ;
		}

		/**
		 *
		 */
		showCashSaleDocInfo( response) ;

		/**
		 *
		var	myFieldRabatt	=	getFormField( "formCashSaleCalc", "_CRabatt") ;
		var	myFieldNettoNachRabatt	=	getFormField( "formCashSaleCalc", "_CNettoNachRabatt") ;
		var	myFieldRohmarge	=	getFormField( "formCashSaleCalc", "_CRohmarge") ;

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

function	showCashSaleDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfCashSale") ;
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
function	showTableCashSaleItem( response) {
	updTableHead( response, "formCashSaleItemTop", "formCashSaleItemBot") ;
	showTable( response, "TableCashSaleItem", "CashSaleItem", "CashSale", document.forms['CashSaleKeyData']._ICashSaleNo.value, "showCashSaleAll", "refreshTableCashSaleItem") ;
}
function	refreshTableCashSaleItem( response) {
	refreshTable( response, "TableCashSaleItem", "CashSaleItem", "CashSale", document.forms['CashSaleKeyData']._ICashSaleNo.value, "showCashSaleAll") ;
}
/**
 *
 */
function	showCashSaleVersand( response) {
	var	attrs ;

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	cuOrdr	=	response.getElementsByTagName( "CashSale")[0] ;
	if ( cuOrdr) {
		attrs	=	cuOrdr.childNodes ;
		dispAttrs( attrs, "formCashSaleDocEMail") ;
		dispAttrs( attrs, "formCashSaleDocFAX") ;
		dispAttrs( attrs, "formCashSaleDocPDF") ;
	}
	cust	=	response.getElementsByTagName( "Kunde")[0] ;
	if ( cust) {
		attrs	=	cust.childNodes ;
		dispAttrs( attrs, "formCashSaleDocEMail") ;
		dispAttrs( attrs, "formCashSaleDocFAX") ;
		dispAttrs( attrs, "formCashSaleDocPDF") ;
	}
}

function	showCashSaleDocList( response) {
	showDocList( response, "TableCashSaleDocs") ;
}

function	showCashSaleDocUpload( response) {
	myField	=	getFormField( "formCashSaleDocUpload", "_DRefNr") ;
	myField.value	=	response.getElementsByTagName( "CashSaleNo")[0].childNodes[0].nodeValue ;
}
/**
 *
 * @param _cuOrdrNo
 * @param _sonst
 * @return
 */
function	createDirBest( _cuOrdrNo, _sonst) {
	markerCashSaleNo	=	_cuOrdrNo ;
	confAction( '/ModBase/CashSale/confCashSaleCreateDirBest.php', doCashSaleCreateDirBest) ;
	return false ;
}
function	doCashSaleCreateDirBest() {
	confDialog.hide() ;
	requestUni( 'ModBase', 'CashSale', '/Common/hdlObject.php', 'createDirBest', markerCashSaleNo, '', '', null, showCashSale) ;
}
/**
 *
 * @return
 */
function	showEMailCashSale() {
	if ( dlgPreview !== null) {
		dlgPreview.destroyRecursive() ;
	}
	dlgPreview	=	new dijit.Dialog( {
		title:	"Preview",
		preventCache:	true,
		duration:	100,
		href:	"/ModBase/CashSale/getAnschreiben.php?CashSaleNo="+document.forms['CashSaleKeyData']._ICashSaleNo.value
	} ) ;
	dlgPreview.show() ;
}
function	hideEMailCashSale() {
	dlgPreview.hide() ;
}
function	refCashSaleItem( _rng) {
	requestUni( 'ModBase', 'CashSale', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CashSaleKeyData']._ICashSaleNo.value,
			_rng,
			'CashSaleItem',
			'formCashSaleItemTop',
			showTableCashSaleItem) ;
	return false ;
}
