/**
 * Einsatzart
 *
 * registers the module in the central database
 */
/**
 * @returns {Einsatzart}
 */
new Einsatzart() ;
function	Einsatzart() {
	dBegin( 1, "Einsatzart.js", "Einsatzart", "main()") ;
	wapScreen.call( this, "Einsatzart") ;
	this.package	=	"ModBase" ;
	this.module	=	"Einsatzart" ;
	this.coreObject	=	"Einsatzart" ;
	this.keyForm	=	"EinsatzartKeyData" ;
	this.keyField	=	getFormField( 'EinsatzartKeyData', 'EinsatzartId') ;
	this.delConfDialog	=	"/ModBase/Einsatzart/confEinsatzartDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Einsatzart"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	dTrace( 2, "Einsatzart.js", "Einsatzart", "*", "creating wapSelector") ;
	this.select	=	new wapSelector( this, "selEinsatzart", {
										xmlTableName:	"TableSelEinsatzart"
									,	objectClass:	"Einsatzart"
									,	module:			"ModBase"
									,	screen:			"Einsatzart"
									,	selectorName:	"selEinsatzart"
									,	formFilterName: "formSelEinsatzartFilter"
									,	formTop:		"formSelEinsatzartTop"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "Einsatzart{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcEinsatzartMain", "tcEinsatzartMain_cpGeneral") ;
															dEnd( 102, "Einsatzart{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "Einsatzart.js", "Einsatzart", "*", "creating wapGrid") ;
	this.gridEinsatzartOV	=	new wapGrid( this, "gridEinsatzartOV", {
										xmlTableName:	"TableEinsatzartOV"
									,	object:			"Einsatzart"
									,	formTop:		"formEinsatzartOVTop"
									,	screen:			"Einsatzart"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainEinsatzart{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainEinsatzart{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "Einsatzart{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageEinsatzartMainData") ;
															dEnd( 102, "Einsatzart{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 *
	 */
	dTrace( 2, "Einsatzart.js", "Einsatzart", "*", "calling this.gridEinsatzartOV._onFirstPage()") ;
	this.gridEinsatzartOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "Einsatzart.js", "Einsatzart", "*", "creating gridLeverLength") ;
	this.gridEinsatzartAssessments	=	new wapGrid( this, "gridEinsatzartAssessments", {
										xmlTableName:	"TableEinsatzartAssessment"
									,	object:			"Assessment"
									,	module :		"ModBase"
									,	screen:			"Assessment"
									,	parentDS:		this.dataSource
									,	formTop:		"formEinsatzartAssessmentTop"
//									,	editorName:		"edtEinsatzartAssessment"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainEinsatzart{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainEinsatzart{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 *
	 */
	dTrace( 2, "Einsatzart.js", "Einsatzart", "*", "calling this.gridEinsatzartAssessments._onFirstPage()") ;
	this.gridEinsatzartAssessments._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "Einsatzart.js", "Einsatzart", "fncLink()") ;
		dEnd( 1, "Einsatzart.js", "Einsatzart", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "Einsatzart.js", "Einsatzart", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "Einsatzart.js", "Einsatzart", "fncShow( <_response>)") ;
		dEnd( 1, "Einsatzart.js", "Einsatzart", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "Einsatzart.js", "Einsatzart", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "Einsatzart.js", "Einsatzart", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridEinsatzartAssessments._onFirstPage() ;							// refresh the dataSource
		}
		dEnd( 1, "Einsatzart.js", "Einsatzart", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "Einsatzart.js", "Einsatzart", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "Einsatzart.js", "Einsatzart", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageEinsatzartSurvey") ;
	dEnd( 1, "Einsatzart.js", "Einsatzart", "main()") ;
}
