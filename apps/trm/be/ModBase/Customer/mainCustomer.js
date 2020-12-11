/**
 * mainCustomer.js
 * ===============
 *
 * registers the module in the central database
 */
/**
 * @returns {scrCustomer}
 */
new mainCustomer() ;
/**
 * @returns {scrCustomer}
 */
function	mainCustomer() {
	dBegin( 1, "mainCustomer.js", "scrCustomer", "main()") ;
	wapScreen.call( this, "Customer") ;
	this.package	=	"ModBase" ;
	this.module	=	"Customer" ;
	this.coreObject	=	"Customer" ;
	this.keyForm	=	"CustomerKeyData" ;
	this.keyField	=	getFormField( 'CustomerKeyData', 'CustomerNo') ;
	this.delConfDialog	=	"/ModBase/Customer/confCustomerDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Customer"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCustomer", {
											pkg:	"ModBase"
										,	script:	"loadScreen.php?screen=ModBase/Customer/selCustomer.xml"
										,	obj:	"Customer"
										}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCustomerOV") ;
	this.gridCustomerOV	=	new wapGrid( this, "gridCustomerOV", {
										object:			"Customer"
									,	screen:			"Customer"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomer{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															dEnd( 102, "mainCustomer{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCustomer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCustomerMainDataEntry") ;
															dEnd( 102, "mainCustomer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "calling gridCustomerOV._onFirstPage()") ;
	this.gridCustomerOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridCustomerContact	=	new wapGrid( this, "gridCustomerContact", {
										object:			"CustomerContact"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtCustomerContact"
									,	moduleName:		ModBase
									,	subModuleName:	Customer
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCustomer{wapGrid.js}", "wapGrid{gridCustomerContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCustomer{wapGrid.js}", "wapGrid{gridCustomerContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainCustomer{wapGrid.js}", "wapGrid{gridCustomerContact}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCustomer{wapGrid.js}", "wapGrid{gridCustomerContact}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainCustomer.js", "scrCustomer", "fncLink()") ;
		dEnd( 1, "mainCustomer.js", "scrCustomer", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCustomer.js", "scrCustomer", "fncShow( <_response>)") ;
		dEnd( 1, "mainCustomer.js", "scrCustomer", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainCustomer.js", "scrCustomer", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			dTrace( 2, "mainCustomer.js", "scrCustomer", "onDataSourceLoaded( ...)", "displaying main object data") ;
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridCustomerContact._onFirstPage() ;
		}
		dEnd( 1, "mainCustomer.js", "scrCustomer", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCustomerSurveyEntry") ;
	dEnd( 1, "mainCustomer.js", "scrCustomer", "main()") ;
}
