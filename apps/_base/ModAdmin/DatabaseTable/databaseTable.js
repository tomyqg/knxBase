/**
 * databaseTable
 *
 * registers the module in the central database
 */
/**
 * @returns {databaseTable}
 */
new databaseTable() ;
function	databaseTable() {
	dBegin( 1, "databaseTable.js", "databaseTable", "__constructor()") ;
	wapScreen.call( this, "databaseTable") ;
	this.package	=	"ModBase" ;
	this.module		=	"DatabaseTable" ;
	this.coreObject	=	"DatabaseTable" ;
	this.keyForm	=	"DatabaseTableKeyData" ;
	this.keyField	=	getFormField( 'DatabaseTableKeyData', 'TableName') ;
	this.delConfDialog	=	null ;
	this.dataSource	=	new wapDataSource( this, { object: "DatabaseTable"}) ;		// dataSource for display
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "databaseTable.js", "databaseTable", "*", "creating wapGrid") ;
	this.gridDatabaseTableOV	=	new wapGrid( this, "gridDatabaseTableOV", {
										object:			"DatabaseTable"
									,	screen:			"databaseTable"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "maindatabaseTable{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "maindatabaseTable{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "databaseTable{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageDatabaseTableMainDataEntry") ;
															dEnd( 102, "databaseTable{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "databaseTable.js", "databaseTable", "*", "calling this.gridDatabaseTableOV._onFirstPage()") ;
	this.gridDatabaseTableOV._onFirstPage() ;							// refresh the dataSource
	/**
	 *
	 */
	dTrace( 2, "databaseTable.js", "databaseTable", "*", "calling this.gridDatabaseTableApplications._onFirstPage()") ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "databaseTable.js", "databaseTable", "fncLink()") ;
		dEnd( 1, "databaseTable.js", "databaseTable", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "databaseTable.js", "databaseTable", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "databaseTable.js", "databaseTable", "fncShow( <_response>)") ;
		dEnd( 1, "databaseTable.js", "databaseTable", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "databaseTable.js", "databaseTable", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "databaseTable.js", "databaseTable", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
		}
		dEnd( 1, "databaseTable.js", "databaseTable", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "databaseTable.js", "databaseTable", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
//		this.onNext() ;
	}
	dTrace( 2, "databaseTable.js", "databaseTable", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageDatabaseTableSurveyEntry") ;
	dEnd( 1, "databaseTable.js", "databaseTable", "main()") ;
}
