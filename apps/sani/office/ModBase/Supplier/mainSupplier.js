/**
 * mainSupplier.js
 * ===============
 *
 * registers the module in the central database
 */
/**
 * @returns {scrCustomer}
 */
new mainSupplier() ;
/**
 * @returns {scrSupplier}
 */
function	mainSupplier() {
	dBegin( 1, "mainSupplier.js", "scrSupplier", "main()") ;
	wapScreen.call( this, "Supplier") ;
	this.package	=	"ModBase" ;
	this.module	=	"Supplier" ;
	this.coreObject	=	"Supplier" ;
	this.keyForm	=	"SupplierKeyData" ;
	this.keyField	=	getFormField( 'SupplierKeyData', 'SupplierNo') ;
	this.delConfDialog	=	"/ModBase/Supplier/confSupplierDel.php" ;
	this.dataSource	=	new wapDataSource( this, {
												object: 	"Supplier"
											,	objectKey:	"SupplierNo"
										}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "gridSupplierSelectOV", {
										objectClass:	"Supplier"
									,	moduleName:		this.package
									,	subModuleName:	"Supplier"
									,	selectorName:	"selSupplier"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainSupplier{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageSupplierMainDataEntry") ;
															dEnd( 102, "mainSupplier{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainSupplier.js", "scrSupplier", "*", "creating gridSupplierOV") ;
	this.gridSupplierOV	=	new wapGrid( this, "gridSupplierOV", {
										object:			"Supplier"
									,	screen:			"Supplier"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainSupplier{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainSupplier{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainSupplier{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageSupplierMainDataEntry") ;
															dEnd( 102, "mainSupplier{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainSupplier.js", "scrSupplier", "*", "calling gridSupplierOV._onFirstPage()") ;
	this.gridSupplierOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for SupplierContact
	 */
	dTrace( 2, "mainSupplier.js", "scrSupplier", "*", "creating gridSupplierContact") ;
	this.gridSupplierContact	=	new wapGrid( this, "gridSupplierContact", {
										object:			"SupplierContact"
									,	screen:			"Supplier"
									,	parentDS:		this.dataSource
									,	editorName:		"edtSupplierContact"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Supplier"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainSupplier{wapGrid.js}", "wapGrid{gridSupplierContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainSupplier{wapGrid.js}", "wapGrid{gridSupplierContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainSupplier{wapGrid.js}", "wapGrid{gridSupplierContact}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainSupplier{wapGrid.js}", "wapGrid{gridSupplierContact}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for SupplierDiscount
	 */
	dTrace( 2, "mainSupplier.js", "scrSupplier", "*", "creating gridSupplierDiscount") ;
	this.gridSupplierDiscount	=	new wapGrid( this, "gridSupplierDiscount", {
										object:			"SupplierDiscount"
									,	screen:			"Supplier"
									,	parentDS:		this.dataSource
									,	editorName:		"edtSupplierDiscount"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Supplier"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainSupplier{wapGrid.js}", "wapGrid{gridSupplierDiscount}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainSupplier{wapGrid.js}", "wapGrid{gridSupplierDiscount}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainSupplier{wapGrid.js}", "wapGrid{gridSupplierDiscount}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainSupplier{wapGrid.js}", "wapGrid{gridSupplierDiscount}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * getting JS for tab 'CustAddresses'
	 */
	/**
	 * getting JS for tab 'CustAddressesDeliver'
	 */
//	this.dtvLiefSupplier	=	new dataTableView( this, "dtvLiefSupplier", "TableLiefSupplier", "Supplier", "LiefSupplier", null, "ModBase", "Supplier") ;
//	this.dtvLiefSupplier.f1	=	"formLiefSupplierTop" ;
	/**
	 * getting JS for tab 'CustAddressesInvoice'
	 */
//	this.dtvRechSupplier	=	new dataTableView( this, "dtvRechSupplier", "TableRechSupplier", "Supplier", "RechSupplier", null, "ModBase", "Supplier") ;
//	this.dtvRechSupplier.f1	=	"formRechSupplierTop" ;
	/**
	 * getting JS for tab 'CustAddressesOther'
	 */
//	this.dtvAddSupplier	=	new dataTableView( this, "dtvAddSupplier", "TableAddSupplier", "Supplier", "AddSupplier", null, "ModBase", "Supplier") ;
//	this.dtvAddSupplier.f1	=	"formAddSupplierTop" ;
	/**
	 * getting JS for tab 'CustDataMining'
	 */
//	this.dtvCustOrders	=	new dataTableView( this, 'dtvCustOrders', "TableCustOrders", "DataMinerSupplier",
//			"CuOrdr", null, 'ModStats', 'dtvCustOrders') ;
//	this.dtvCustComm	=	new dataTableView( this, 'dtvCustComm', "TableCustComm", "DataMinerSupplier",
//			"CuComm", null, 'ModStats', 'dtvCustComm') ;
//	this.dtvCustDlvr	=	new dataTableView( this, 'dtvCustDlvr', "TableCustDlvr", "DataMinerSupplier",
//			"CuDlvr", null, 'ModStats', 'dtvCustDlvr') ;
//	this.dtvCustInvoices	=	new dataTableView( this, 'dtvCustInvoices', "TableCustInvoices", "DataMinerSupplier",
//			"CuInvc", null, 'ModStats', 'dtvCustInvoices') ;
	/**
	 * getting JS for tab 'CustFunctions'
	 */
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainSupplier.js", "scrSupplier", "fncLink()") ;
		dEnd( 1, "mainSupplier.js", "scrSupplier", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainSupplier.js", "scrSupplier", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainSupplier.js", "scrSupplier", "fncShow( <_response>)") ;
		dEnd( 1, "mainSupplier.js", "scrSupplier", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainSupplier.js", "scrSupplier", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainSupplier.js", "scrSupplier", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridSupplierContact._onFirstPage() ;
		}
		dEnd( 1, "mainSupplier.js", "scrSupplier", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainSupplier.js", "scrSupplier", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainSupplier.js", "scrSupplier", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageSupplierSurveyEntry") ;
	dEnd( 1, "mainSupplier.js", "scrSupplier", "main()") ;
}
