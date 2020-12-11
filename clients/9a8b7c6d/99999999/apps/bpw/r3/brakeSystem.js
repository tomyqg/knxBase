/**
 * brakeSystem
 * 
 * registers the module in the central database
 */
/**
 * @returns {brakeSystem}
 */
function	brakeSystem() {
	dBegin( 1, "brakeSystem.js", "brakeSystem", "main()") ;
	wapScreen.call( this, "AxleUnit") ;
	this.package	=	"ModBase" ;
	this.module	=	"AxleUnit" ;
	this.coreObject	=	"AxleUnit" ;
	this.keyForm	=	"AxleUnitKeyData" ;
	this.keyField	=	getFormField( 'AxleUnitKeyData', 'AxleUnitId') ;
	this.delConfDialog	=	"/ModBase/AxleUnit/confAxleUnitDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "AxleUnit"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selAxleUnit", {
										xmlTableName:	"TableSelAxleUnit"
									,	objectClass:	"AxleUnit"
									,	module:			"ModBase"
									,	screen:			"AxleUnit"
									,	selectorName:	"selAxleUnit"
									,	formFilterName: "formSelAxleUnitFilter"
									,	formTop:		"formSelAxleUnitTop"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "brakeSystem{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcAxleUnitMain", "tcAxleUnitMain_cpGeneral") ;
															dEnd( 102, "brakeSystem{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab 
	 */
	this.gridAxleUnitOV	=	new wapGrid( this, "gridAxleUnitOV", {
										gridDivName:	"TableAxleUnitRoot"	
//										xmlTableName:	"TableAxleUnitOV"
//									,	object:			"AxleUnit"
//									,	module:			"ModBase"
//									,	screen:			"AxleUnit"
//									,	formTop:		"formAxleUnitOVTop"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainBrakeSystem{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainBrakeSystem{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "brakeSystem{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcAxleUnitMain", "tcAxleUnitMain_cpGeneral") ;
															dEnd( 102, "brakeSystem{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 * 
	 */
	dTrace( 2, "brakeSystem.js", "brakeSystem", "*", "calling this.gridAxleUnitOV._onFirstPage()") ;
	this.gridAxleUnitOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "brakeSystem.js", "brakeSystem", "fncLink()") ;
		dEnd( 1, "brakeSystem.js", "brakeSystem", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "brakeSystem.js", "brakeSystem", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "brakeSystem.js", "brakeSystem", "fncShow( <_response>)") ;
		dEnd( 1, "brakeSystem.js", "brakeSystem", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "brakeSystem.js", "brakeSystem", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "brakeSystem.js", "brakeSystem", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
		}
		dEnd( 1, "brakeSystem.js", "brakeSystem", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * 
	 */
	dTrace( 2, "brakeSystem.js", "brakeSystem", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "brakeSystem.js", "brakeSystem", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	dEnd( 1, "brakeSystem.js", "brakeSystem", "main()") ;
}
