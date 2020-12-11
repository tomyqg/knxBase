/**
 * scrCalculation
 *
 * registers the module in the central database
 */
/**
 * @returns {scrCalculation}
 */
new calculation() ;
function	calculation() {
	dBegin( 1, "mainCalculation.js", "scrCalculation", "main()") ;
	wapScreen.call( this, "calculation") ;
	this.package	=	"ModCalc" ;
	this.module	=	"Calculation" ;
	this.coreObject	=	"Calculation" ;
	this.keyForm	=	"CalculationKeyData" ;
	this.keyField	=	getFormField( 'CalculationKeyData', 'CalculationId') ;
	this.delConfDialog	=	"/ModCalc/Calculation/confCalculationDel.php" ;
	this.dataSource	=	new wapDataSource( this, {
		 										object: "Calculation"
											,	objectKey:	"CalculationId"
											}) ;		// dataSource for display
	this.dataSourceBS	=	new wapDataSource( this, {
												object: "BrakeSystem"
											}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selCalculation", {
										objectClass:	"Calculation"
									,	module:			this.package
									,	screen:			"Calculation"
									,	selectorName:	"selCalculation"
									,	formFilterName: "formCalculationSelectFilter"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainCalculation{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCalculationMainDataEntry") ;
															dEnd( 102, "mainCalculation{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainCalculation.js", "scrCalculation", "*", "creating gridCalculationOV") ;
	this.gridCalculationOV	=	new wapGrid( this, "gridCalculationOV", {
										object:			"Calculation"
									,	screen:			"Calculation"
									,	onDataSourceLoaded:	function( _parent, _data) {
																dBegin( 102, "mainCalculation{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
																this.show() ;
																dEnd( 102, "mainCalculation{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainCalculation{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCalculationMainDataEntry") ;
															dEnd( 102, "mainCalculation{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainCalculation.js", "scrCalculation", "*", "calling dsCalculationOV._onFirstPage()") ;
	this.gridCalculationOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and diplay grid for CustomerContact
	 */
	dTrace( 2, "mainCalculation.js", "scrCalculation", "*", "creating gridConfiguration") ;
	this.gridConfiguration	=	new wapGrid( this, "gridConfiguration", {
										object:			"Configuration"
									,	screen:			"Calculation"
									,	parentDS:		this.dataSource
									,	editorName:		"edtConfiguration"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCalculation{wapGrid.js}", "wapGrid{gridConfiguration}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCalculation{wapGrid.js}", "wapGrid{gridConfiguration}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainCalculation{wapGrid.js}", "wapGrid{gridConfiguration}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCalculation{wapGrid.js}", "wapGrid{gridConfiguration}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and diplay grid for CustomerContact
	 */
	dTrace( 2, "mainCalculation.js", "scrCalculation", "*", "creating gridConfiguration") ;
	this.gridTrailerData	=	new wapGrid( this, "gridTrailerData", {
										object:			"TrailerData"
									,	screen:			"Calculation"
									,	parentDS:		this.dataSource
									,	editorName:		"edtTrailerData"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainCalculation{wapGrid.js}", "wapGrid{gridTrailerData}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainCalculation{wapGrid.js}", "wapGrid{gridTrailerData}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainCalculation{wapGrid.js}", "wapGrid{gridTrailerData}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainCalculation{wapGrid.js}", "wapGrid{gridTrailerData}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and display grid for CustomerContact
	 */
	dTrace( 2, "mainBrakeSystem.js", "scrBrakeSystem", "*", "creating gridValveSequence") ;
	this.gridCValveSequence	=	new wapGrid( this, "gridCValveSequence", {
										object:			"ValveSequence"
									,	screen:			"BrakeSystem"
									,	parentDS:		this.dataSourceBS
									,	editorName:		"edtValveSequence"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainBrakeSystem{wapGrid.js}", "wapGrid{gridValveSequence}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainBrakeSystem{wapGrid.js}", "wapGrid{gridValveSequence}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainBrakeSystem{wapGrid.js}", "wapGrid{gridValveSequence}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainBrakeSystem{wapGrid.js}", "wapGrid{gridValveSequence}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 *
	 */
	myFZGSelect	=	new wapSelectXML( this, "mySelect", {
											selectNode:	getFormField( "formCalculationMain", "TrailerTypeId")
										,	object:		"TrailerType"
										,	key:		"TrailerTypeId"
										,	value:		"Options"
										,	onDataSourceLoaded:	function( _parent, _data) {
												dBegin( 102, "mainCalculation{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
												this.show() ;
												dEnd( 102, "mainCalculation{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
											}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainCalculation.js", "scrCalculation", "fncLink()") ;
		dEnd( 1, "mainCalculation.js", "scrCalculation", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCalculation.js", "scrCalculation", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainCalculation.js", "scrCalculation", "fncShow( <_response>)") ;
		dEnd( 1, "mainCalculation.js", "scrCalculation", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainCalculation.js", "scrCalculation", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 3, "mainCalculation.js", "scrCalculation", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridTrailerData._onFirstPage() ;
			this.gridConfiguration._onFirstPage() ;
			this.dataSourceBS.key	=	getFormField( "formCalculationBrakeSystem", "BrakeSystemId").value ;
			this.gridCValveSequence._onFirstPage() ;
		}
		dEnd( 3, "mainCalculation.js", "scrCalculation", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * setTrailerTypeNo
	 * ================
	 *
	 *
	 */
	dTrace( 2, "mainCalculation.js", "scrCalculation", "*", "defining this.onSetTrailerTypeNo") ;
	this.onSetTrailerTypeNo	=	function( _obj) {
		dBegin( 1, "mainCalculation.js", "scrCalculation", "onSetTrailerTypeNo()") ;
		myFZGSelect.refresh() ;
		dEnd( 1, "mainCalculation.js", "scrCalculation", "onSetTrailerTypeNo()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainCalculation.js", "scrCalculation", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainCalculation.js", "scrCalculation", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCalculationSurveyEntry") ;
	dEnd( 1, "mainCalculation.js", "scrCalculation", "main()") ;
}
