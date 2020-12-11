/**
 * mainAddress.js
 * ==============
 *
 * registers the module in the central database
 */
/**
 * @returns {mainArticle}
 */
new mainAddress() ;
/**
 * @returns {scrAddress}
 */
function	mainAddress() {
	dBegin( 1, "mainAddress.js", "mainAddress", "main()") ;
	wapScreen.call( this, "Address") ;
	this.package	=	"ModBase" ;
	this.module	=	"Address" ;
	this.coreObject	=	"Address" ;
	this.keyForm	=	"AddressKeyData" ;
	this.keyField	=	getFormField( 'AddressKeyData', 'AddressNo') ;
	this.delConfDialog	=	"/ModBase/Address/confAddressDel.php" ;
	this.dataSource	=	new wapDataSource( this, {
		 										object: "Address"
											,	objectKey:	"AddressNo"
										}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selAddress", {
											pkg:	"ModBase"
										,	script:	"loadScreen.php?screen=ModBase/Address/selAddress.xml"
										,	obj:	"Address"
										}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainAddress.js", "mainAddress", "*", "creating gridAddressOV") ;
	this.gridAddressOV	=	new wapGrid( this, "gridAddressOV", {
										object:			"Address"
									,	screen:			"Address"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainAddress{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainAddress{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainAddress{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageAddressMainDataEntry") ;
															dEnd( 102, "mainAddress{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainAddress.js", "mainAddress", "*", "calling gridAddressOV._onFirstPage()") ;
	this.gridAddressOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for AddressContact
	 */
	dTrace( 2, "mainAddress.js", "mainAddress", "*", "creating gridCust-------------omerContact") ;
	this.gridAddressContact	=	new wapGrid( this, "gridAddressContact", {
										object:			"AddressContact"
									,	module :		"ModBase"
									,	screen:			"Address"
									,	parentDS:		this.dataSource
									,	editorName:		"edtAddressContact"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Address"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainAddress{wapGrid.js}", "wapGrid{gridAddressContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainAddress{wapGrid.js}", "wapGrid{gridAddressContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainAddress{wapGrid.js}", "wapGrid{gridAddressContact}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainAddress{wapGrid.js}", "wapGrid{gridAddressContact}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * getting JS for tab 'CustFunctions'
	 */
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainAddress.js", "scrAddress", "fncLink()") ;
		dEnd( 1, "mainAddress.js", "scrAddress", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainAddress.js", "scrAddress", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainAddress.js", "scrAddress", "fncShow( <_response>)") ;
		dEnd( 1, "mainAddress.js", "scrAddress", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainAddress.js", "scrAddress", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainAddress.js", "scrAddress", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			dTrace( 2, "mainCustomer.js", "scrCustomer", "onDataSourceLoaded( ...)", "displaying main object data") ;
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridAddressContact._onFirstPage() ;
		}
		dEnd( 1, "mainAddress.js", "scrAddress", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	this.beforeDiscard	=	function() {
		var	cnt	=	0 ;
		if ( this.dataSource.objects)
			cnt	=	this.verifyData( "objects.Address[0]") ;
		return cnt ;
	} ;
	dTrace( 2, "mainAddress.js", "scrAddress", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainAddress.js", "scrAddress", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageAddressSurveyEntry") ;
	dEnd( 1, "mainAddress.js", "scrAddress", "main()") ;
}
