/**
 * tyre
 *
 * registers the module in the central database
 */
/**
 * @returns {tyre}
 */
new tyre() ;
function	tyre() {
	dBegin( 1, "tyre.js", "tyre", "__constructor()") ;
	wapScreen.call( this, "tyre") ;
	this.package	=	"ModBase" ;
	this.module	=	"Tyre" ;
	this.coreObject	=	"Tyre" ;
	this.keyForm	=	"TyreKeyData" ;
	this.keyField	=	getFormField( 'TyreKeyData', 'TyreId') ;
	this.delConfDialog	=	"/ModBase/Tyre/confTyreDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Tyre"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	dTrace( 2, "tyre.js", "tyre", "*", "creating wapSelector") ;
	this.select	=	new wapSelector( this, "selTyre", {
										objectClass:	"Tyre"
									,	module:			"ModBase"
									,	screen:			"Tyre"
									,	selectorName:	"selTyre"
									,	formFilterName: "formSelTyreFilter"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "tyre{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcTyreMain", "tcTyreMain_cpGeneral") ;
															dEnd( 102, "tyre{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
													}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "tyre.js", "tyre", "*", "creating wapGrid") ;
	this.gridTyreOV	=	new wapGrid( this, "gridTyreOV", {
										object:			"Tyre"
									,	screen:			"tyre"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "maintyre{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "maintyre{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "tyre{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageTyreMainDataEntry") ;
															dEnd( 102, "tyre{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "tyre.js", "tyre", "*", "calling this.gridTyreOV._onFirstPage()") ;
	this.gridTyreOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "tyre.js", "tyre", "*", "creating gridLeverLength") ;
	this.gridTyreAssessments	=	new wapGrid( this, "gridTyreAssessments", {
										object:			"Assessment"
									,	screen:			"Assessment"
									,	parentDS:		this.dataSource
//									,	editorName:		"edtTyreAssessment"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "maintyre{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "maintyre{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 *
	 */
	dTrace( 2, "tyre.js", "tyre", "*", "calling this.gridTyreAssessments._onFirstPage()") ;
	this.gridTyreAssessments._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "tyre.js", "tyre", "fncLink()") ;
		dEnd( 1, "tyre.js", "tyre", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "tyre.js", "tyre", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "tyre.js", "tyre", "fncShow( <_response>)") ;
		dEnd( 1, "tyre.js", "tyre", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "tyre.js", "tyre", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "tyre.js", "tyre", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridTyreAssessments._onFirstPage() ;							// refresh the dataSource
		}
		dEnd( 1, "tyre.js", "tyre", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "tyre.js", "tyre", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "tyre.js", "tyre", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageTyreSurveyEntry") ;
	dEnd( 1, "tyre.js", "tyre", "main()") ;
}
