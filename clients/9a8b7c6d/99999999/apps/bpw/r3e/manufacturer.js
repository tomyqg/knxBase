/**
 * scrManufacturer
 *
 * registers the module in the central database
 */
/**
 * @returns {scrManufacturer}
 */
new manufacturer() ;
function	manufacturer() {
	dBegin( 1, "mainManufacturer.js", "scrManufacturer", "main()") ;
	wapScreen.call( this, "manufacturer") ;
	this.package	=	"ModBase" ;
	this.module	=	"Manufacturer" ;
	this.coreObject	=	"Manufacturer" ;
	this.keyForm	=	"ManufacturerKeyData" ;
	this.keyField	=	getFormField( 'ManufacturerKeyData', 'ManufacturerId') ;
	this.delConfDialog	=	"/ModBase/Manufacturer/confManufacturerDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Manufacturer"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selManufacturer", {
										objectClass:	"Manufacturer"
									,	module:			"ModBase"
									,	screen:			"Manufacturer"
									,	selectorName:	"selManufacturer"
									,	formFilterName: "formSelManufacturerFilter"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainManufacturer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcManufacturerMain", "tcManufacturerMain_cpGeneral") ;
															dEnd( 102, "mainManufacturer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
													}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainManufacturer.js", "scrManufacturer", "*", "creating gridManufacturerOV") ;
	this.gridManufacturerOV	=	new wapGrid( this, "gridManufacturerOV", {
										object:			"Manufacturer"
									,	screen:			"Manufacturer"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainManufacturer{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainManufacturer{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainManufacturer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageManufacturerMainDataEntry") ;
															dEnd( 102, "mainManufacturer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainManufacturer.js", "scrManufacturer", "*", "calling dsManufacturerOV._onFirstPage()") ;
	this.gridManufacturerOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "mainManufacturer.js", "scrManufacturer", "*", "creating gridLeverLength") ;
	this.gridBrake	=	new wapGrid( this, "gridBrake", {
										object:			"Brake"
									,	screen:			"Manufacturer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtBrake"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridCylinder	=	new wapGrid( this, "gridCylinder", {
										object:			"Cylinder"
									,	screen:			"Manufacturer"
									,	parentDS:		this.dataSource
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	this.gridValve	=	new wapGrid( this, "gridValve", {
										object:			"Valve"
									,	screen:			"Manufacturer"
									,	parentDS:		this.dataSource
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainManufacturer{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainManufacturer.js", "scrManufacturer", "fncLink()") ;
		dEnd( 1, "mainManufacturer.js", "scrManufacturer", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainManufacturer.js", "scrManufacturer", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainManufacturer.js", "scrManufacturer", "fncShow( <_response>)") ;
		dEnd( 1, "mainManufacturer.js", "scrManufacturer", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainManufacturer.js", "scrManufacturer", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainManufacturer.js", "scrManufacturer", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridBrake._onFirstPage() ;
			this.gridCylinder._onFirstPage() ;
			this.gridValve._onFirstPage() ;
		}
		dEnd( 1, "mainManufacturer.js", "scrManufacturer", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainManufacturer.js", "scrManufacturer", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainManufacturer.js", "scrManufacturer", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageManufacturerSurveyEntry") ;
	dEnd( 1, "mainManufacturer.js", "scrManufacturer", "main()") ;
}
