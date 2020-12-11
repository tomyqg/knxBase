/**
 * client
 *
 * registers the module in the central database
 */
/**
 * @returns {client}
 */
new client() ;
function	client() {
	dBegin( 1, "client.js", "client", "__constructor()") ;
	wapScreen.call( this, "client") ;
	this.package	=	"ModBase" ;
	this.module	=	"Client" ;
	this.coreObject	=	"Client" ;
	this.keyForm	=	"ClientKeyData" ;
	this.keyField	=	getFormField( 'ClientKeyData', 'ClientId') ;
	this.delConfDialog	=	"/ModBase/Client/confClientDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Client"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	dTrace( 2, "client.js", "client", "*", "creating wapSelector") ;
	this.select	=	new wapSelector( this, "selClient", {
										objectClass:	"Client"
									,	module:			"ModBase"
									,	screen:			"Client"
									,	selectorName:	"selClient"
									,	formFilterName: "formSelClientFilter"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "client{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcClientMain", "tcClientMain_cpGeneral") ;
															dEnd( 102, "client{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
													}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "client.js", "client", "*", "creating wapGrid") ;
	this.gridClientOV	=	new wapGrid( this, "gridClientOV", {
										object:			"Client"
									,	screen:			"client"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainclient{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainclient{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "client{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageClientMainDataEntry") ;
															dEnd( 102, "client{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "client.js", "client", "*", "calling this.gridClientOV._onFirstPage()") ;
	this.gridClientOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "client.js", "client", "*", "creating gridClientApplications") ;
	this.gridClientApplications	=	new wapGrid( this, "gridClientApplications", {
										object:			"ClientApplication"
									,	screen:			"ClientApplication"
									,	parentDS:		this.dataSource
									,	editorName:		"edtClientApplication"
									,	onDataSourceLoadedExt:	function( _parent, _data) {
															dBegin( 102, "mainclient{wapGrid.js}", "wapGrid{gridClientApplications}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.parent.gridSysConfig.clear() ;
															dEnd( 102, "mainclient{wapGrid.js}", "wapGrid{gridClientApplications}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainclient{wapGrid.js}", "wapGrid{gridClientApplications}", "onSelect( <_parent>, '"+_id+"')") ;
															this.parent.gridSysConfig.dataSource.id	=	_id ;
															this.parent.gridSysConfig._onFirstPage() ;
															dEnd( 102, "mainclient{wapGrid.js}", "wapGrid{gridClientApplications}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "client.js", "client", "*", "creating gridSysConfigObj") ;
	this.gridSysConfig	=	new wapGrid( this, "gridSysConfigObj", {
										object:			"SysConfigObj"
									,	screen:			"SysConfigObj"
									,	parentDS:		this.gridClientApplications.dataSource
//									,	editorName:		"edtClientApplication"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainclient{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainclient{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 *
	 */
	dTrace( 2, "client.js", "client", "*", "calling this.gridClientApplications._onFirstPage()") ;
	this.gridClientApplications._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "client.js", "client", "fncLink()") ;
		dEnd( 1, "client.js", "client", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "client.js", "client", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "client.js", "client", "fncShow( <_response>)") ;
		dEnd( 1, "client.js", "client", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "client.js", "client", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "client.js", "client", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridClientApplications._onFirstPage() ;							// refresh the dataSource
		}
		dEnd( 1, "client.js", "client", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "client.js", "client", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "client.js", "client", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageClientSurveyEntry") ;
	dEnd( 1, "client.js", "client", "main()") ;
}
