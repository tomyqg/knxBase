/**
 * clientApplication
 *
 * registers the module in the central database
 */
/**
 * @returns {clientApplication}
 */
new clientApplication() ;
function	clientApplication() {
	dBegin( 1, "clientApplication.js", "clientApplication", "__constructor()") ;
	wapScreen.call( this, "clientApplication") ;
	this.package	=	"ModBase" ;
	this.module	=	"ClientApplication" ;
	this.coreObject	=	"ClientApplication" ;
	this.keyForm	=	"ClientApplicationKeyData" ;
	this.keyField	=	getFormField( 'ClientApplicationKeyData', 'Id') ;
	this.delConfDialog	=	"/ModBase/ClientApplication/confClientApplicationDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "ClientApplication"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	dTrace( 2, "clientApplication.js", "clientApplication", "*", "creating wapSelector") ;
	this.select	=	new wapSelector( this, "selClientApplication", {
										objectClass:	"ClientApplication"
									,	module:			"ModBase"
									,	screen:			"ClientApplication"
									,	selectorName:	"selClientApplication"
									,	formFilterName: "formSelClientApplicationFilter"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "clientApplication{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcClientApplicationMain", "tcClientApplicationMain_cpGeneral") ;
															dEnd( 102, "clientApplication{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
													}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "clientApplication.js", "clientApplication", "*", "creating wapGrid") ;
	this.gridClientApplicationOV	=	new wapGrid( this, "gridClientApplicationOV", {
										object:			"ClientApplication"
									,	screen:			"clientApplication"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainclientApplication{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainclientApplication{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "clientApplication{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageClientApplicationMainDataEntry") ;
															dEnd( 102, "clientApplication{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "clientApplication.js", "clientApplication", "*", "calling this.gridClientApplicationOV._onFirstPage()") ;
	this.gridClientApplicationOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "clientApplication.js", "clientApplication", "*", "creating gridLeverLength") ;
	this.gridApplications	=	new wapGrid( this, "gridApplications", {
										object:			"Application"
									,	screen:			"Application"
									,	parentDS:		this.dataSource
									,	editorName:		"edtApplication"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainclientApplication{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainclientApplication{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 *
	 */
	dTrace( 2, "clientApplication.js", "clientApplication", "*", "calling this.gridApplications._onFirstPage()") ;
	this.gridApplications._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "clientApplication.js", "clientApplication", "fncLink()") ;
		dEnd( 1, "clientApplication.js", "clientApplication", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "clientApplication.js", "clientApplication", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "clientApplication.js", "clientApplication", "fncShow( <_response>)") ;
		dEnd( 1, "clientApplication.js", "clientApplication", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "clientApplication.js", "clientApplication", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "clientApplication.js", "clientApplication", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridApplications._onFirstPage() ;							// refresh the dataSource
		}
		dEnd( 1, "clientApplication.js", "clientApplication", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "clientApplication.js", "clientApplication", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "clientApplication.js", "clientApplication", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageClientApplicationSurveyEntry") ;
	dEnd( 1, "clientApplication.js", "clientApplication", "main()") ;
}
