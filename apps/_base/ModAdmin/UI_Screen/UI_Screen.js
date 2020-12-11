/**
 * regModScreen
 *
 * registers the module in the central database
 */
new Screen() ;
/**
 *
 */
function	Screen() {
	dBegin( 1, "screen.js", "screen", "__constructor()") ;
	wapScreen.call( this, "Screen") ;				// instantiate the super-class!!!
	this.package	=	"ModAdmin" ;			// directory of the module
	this.module	=	"Screen" ;					// sub-directory of the screen
	this.coreObject	=	"Screen" ;
	this.keyForm	=	"ScreenKeyData" ;		// form
	this.keyField	=	getFormField( 'ScreenKeyData', 'Id') ;
	this.delConfDialog	=	null ;
	this.dataSource	=	new wapDataSource( this, { object: "Screen"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	dTrace( 2, "screen.js", "screen", "*", "creating wapSelector") ;
//	this.select	=	new selector( this, 'selScreen', 'ModUI', '/ModUI/Screen/selScreen.php', 'Screen') ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "screen.js", "screen", "*", "creating wapGrid") ;
	this.gridScreenOV	=	new wapGrid( this, "gridScreenOV", {
										object:			"Screen"
									,	screen:			"Screen"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainscreen{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainscreen{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "screen{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageScreenMainDataEntry") ;
															dEnd( 102, "screen{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "screen.js", "screen", "*", "calling this.gridScreenOV._onFirstPage()") ;
	this.gridScreenOV._onFirstPage() ;							// refresh the dataSource
	/**
	 *
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "scrScreen.js::scrScreen::fncLink(): begin \n") ;
		_debugL( 0x00000001, "scrScreen.js::scrScreen::fncLink(): end \n") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "screen.js", "screen", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "screen.js", "screen", "fncShow( <_response>)") ;
		dEnd( 1, "screen.js", "screen", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "screen.js", "screen", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "screen.js", "screen", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
		}
		dEnd( 1, "screen.js", "screen", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "screen.js", "screen", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "screen.js", "screen", "onMisc( <...>)") ;
	} ;
	/**
	 * process any pending 'link-to-screen# data
	 */
	dTrace( 2, "screen.js", "screen", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "screen.js", "screen", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageScreenSurveyEntry") ;
	dEnd( 1, "screen.js", "screen", "main()") ;
}
