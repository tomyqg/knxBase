/**
 * Mitarbeiter
 *
 * registers the module in the central database
 */
/**
 * @returns {Mitarbeiter}
 */
new Mitarbeiter() ;
function	Mitarbeiter() {
	dBegin( 1, "Mitarbeiter.js", "Mitarbeiter", "main()") ;
	wapScreen.call( this, "Mitarbeiter") ;
	this.package	=	"ModBase" ;
	this.module	=	"Mitarbeiter" ;
	this.coreObject	=	"Mitarbeiter" ;
	this.keyForm	=	"MitarbeiterKeyData" ;
	this.keyField	=	getFormField( 'MitarbeiterKeyData', 'Id') ;
	this.delConfDialog	=	"/ModBase/Mitarbeiter/confMitarbeiterDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Mitarbeiter"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	dTrace( 2, "Mitarbeiter.js", "Mitarbeiter", "*", "creating wapSelector") ;
//	this.select	=	new wapSelector( this, "selMitarbeiter", {
//										screen:			"Mitarbeiter"
//									,	selectorName:	"selMitarbeiter"
//									,	formFilterName: "formSelMitarbeiterFilter"
//									,	formTop:		"formSelMitarbeiterTop"
//									,	onSelect:		function( _parent, _id) {
//															dBegin( 102, "Mitarbeiter{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
//															_parent.dataSource.load( "", _id, "") ;
//															_parent.gotoTab( "tcMitarbeiterMain", "tcMitarbeiterMain_cpGeneral") ;
//															dEnd( 102, "Mitarbeiter{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
//														}
//							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "Mitarbeiter.js", "Mitarbeiter", "*", "creating wapGrid") ;
	this.gridMitarbeiterOV	=	new wapGrid( this, "gridMitarbeiterOV", {
										object:			"Mitarbeiter"
									,	screen:			"Mitarbeiter"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainMitarbeiter{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainMitarbeiter{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "Mitarbeiter{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageMitarbeiterMainData") ;
															dEnd( 102, "Mitarbeiter{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 *
	 */
	dTrace( 2, "Mitarbeiter.js", "Mitarbeiter", "*", "calling this.gridMitarbeiterOV._onFirstPage()") ;
	this.gridMitarbeiterOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "Mitarbeiter.js", "Mitarbeiter", "*", "creating gridLeverLength") ;
	this.gridMitarbeiterRollen	=	new wapGrid( this, "gridMitarbeiterRollen", {
										object:			"Rolle"
									,	screen:			"Rolle"
									,	parentDS:		this.dataSource
									,	editorName:		"edtMitarbeiterRolle"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainMitarbeiter{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainMitarbeiter{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 *
	 */
	dTrace( 2, "Mitarbeiter.js", "Mitarbeiter", "*", "calling this.gridMitarbeiterRollen._onFirstPage()") ;
	this.gridMitarbeiterRollen._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "Mitarbeiter.js", "Mitarbeiter", "fncLink()") ;
		dEnd( 1, "Mitarbeiter.js", "Mitarbeiter", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "Mitarbeiter.js", "Mitarbeiter", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "Mitarbeiter.js", "Mitarbeiter", "fncShow( <_response>)") ;
		dEnd( 1, "Mitarbeiter.js", "Mitarbeiter", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "Mitarbeiter.js", "Mitarbeiter", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "Mitarbeiter.js", "Mitarbeiter", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridMitarbeiterRollen._onFirstPage() ;							// refresh the dataSource
		}
		dEnd( 1, "Mitarbeiter.js", "Mitarbeiter", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "Mitarbeiter.js", "Mitarbeiter", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "Mitarbeiter.js", "Mitarbeiter", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageMitarbeiterSurvey") ;
	dEnd( 1, "Mitarbeiter.js", "Mitarbeiter", "main()") ;
}
