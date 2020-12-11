/**
 * cylinder
 *
 * registers the module in the central database
 */
/**
 * @returns {cylinder}
 */
new cylinder() ;
function	cylinder() {
	dBegin( 1, "cylinder.js", "cylinder", "main()") ;
	wapScreen.call( this, "cylinder") ;
	this.package	=	"ModBase" ;
	this.module	=	"Cylinder" ;
	this.coreObject	=	"Cylinder" ;
	this.keyForm	=	"CylinderKeyData" ;
	this.keyField	=	getFormField( 'CylinderKeyData', 'CylinderId') ;
	this.delConfDialog	=	"/ModBase/Cylinder/confCylinderDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Cylinder"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
//	this.select	=	new wapSelector( this, "selcylinder", {
							// 			xmlTableName:	"TableSelCylinder"
							// 		,	objectClass:	"Cylinder"
							// 		,	module:			"ModBase"
							// 		,	screen:			"Cylinder"
							// 		,	selectorName:	"selCylinder"
							// 		,	formFilterName: "formSelCylinderFilter"
							// 		,	formTop:		"formSelCylinderTop"
							// 		,	onSelect:		function( _parent, _id) {
							// 								dBegin( 102, "cylinder{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
							// 								_parent.dataSource.load( "", _id, "") ;
							// 								_parent.gotoTab( "tccylinderMain", "tccylinderMain_cpGeneral") ;
							// 								dEnd( 102, "cylinder{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
							// 							}
							// }) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	this.gridCylinderOV	=	new wapGrid( this, "gridCylinderOV", {
										object:			"Cylinder"
									,	screen:			"cylinder"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "maincylinder{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "maincylinder{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "cylinder{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageCylinderMainDataEntry") ;
															dEnd( 102, "cylinder{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 *
	 */
	dTrace( 2, "cylinder.js", "cylinder", "*", "calling this.gridCylinderOV._onFirstPage()") ;
	this.gridCylinderOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "cylinder.js", "cylinder", "fncLink()") ;
		dEnd( 1, "cylinder.js", "cylinder", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "cylinder.js", "cylinder", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "cylinder.js", "cylinder", "fncShow( <_response>)") ;
		dEnd( 1, "cylinder.js", "cylinder", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "cylinder.js", "cylinder", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "cylinder.js", "cylinder", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
		}
		dEnd( 1, "cylinder.js", "cylinder", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "cylinder.js", "cylinder", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "cylinder.js", "cylinder", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageCylinderSurveyEntry") ;
	dEnd( 1, "cylinder.js", "cylinder", "main()") ;
}
