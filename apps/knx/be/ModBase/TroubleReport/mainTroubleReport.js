/**
 * mainTroubleReport.js
 * ===============
 * 
 * registers the module in the central database
 */
/**
 * @returns {scrTroubleReport}
 */
function	scrTroubleReport() {
	dBegin( 1, "mainTroubleReport.js", "scrTroubleReport", "main()") ;
	wapScreen.call( this, "TroubleReport") ;
	this.package	=	"ModBase" ;
	this.module	=	"TroubleReport" ;
	this.coreObject	=	"TroubleReport" ;
	this.keyForm	=	"TroubleReportKeyData" ;
	this.keyField	=	getFormField( 'TroubleReportKeyData', 'TroubleReportNo') ;
	this.delConfDialog	=	"/ModBase/TroubleReport/confTroubleReportDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "TroubleReport"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selTroubleReport", {
											pkg:	"ModBase"
										,	script:	"loadScreen.php?screen=ModBase/TroubleReport/selTroubleReport.xml"
										,	obj:	"TroubleReport"
										}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab 
	 */
	dTrace( 2, "mainTroubleReport.js", "scrTroubleReport", "*", "creating gridTroubleReportOV") ;
	this.gridTroubleReportOV	=	new wapGrid( this, "gridTroubleReportOV", {
										xmlTableName:	"TableTroubleReportOV"
									,	object:			"TroubleReport"
									,	module:			"ModBase"
									,	screen:			"TroubleReport"
									,	formTop:		"formTroubleReportOVTop"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainTroubleReport{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainTroubleReport{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainTroubleReport{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcTroubleReportMain", "tcTroubleReportMain_cpGeneral") ;
															dEnd( 102, "mainTroubleReport{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainTroubleReport.js", "scrTroubleReport", "*", "calling gridTroubleReportOV._onFirstPage()") ;
	this.gridTroubleReportOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for TroubleReportAction 
	 */
	dTrace( 2, "mainTroubleReport.js", "scrTroubleReport", "*", "creating gridCust-------------omerContact") ;
	this.gridTroubleReportAction	=	new wapGrid( this, "gridTroubleReportAction", {
										xmlTableName:	"TableTroubleReportAction"
									,	object:			"TroubleReportAction"
									,	module :		"ModBase"
									,	screen:			"TroubleReport"
									,	parentDS:		this.dataSource
									,	formTop:		"formTroubleReportActionTop"
									,	editorName:		"edtTroubleReportAction"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainTroubleReport{wapGrid.js}", "wapGrid{gridTroubleReportAction}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainTroubleReport{wapGrid.js}", "wapGrid{gridTroubleReportAction}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainTroubleReport{wapGrid.js}", "wapGrid{gridTroubleReportAction}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainTroubleReport{wapGrid.js}", "wapGrid{gridTroubleReportAction}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * getting JS for tab 'CustAddresses' 
	 */
	/**
	 * getting JS for tab 'CustFunctions' 
	 */
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainTroubleReport.js", "scrTroubleReport", "fncLink()") ;
		dEnd( 1, "mainTroubleReport.js", "scrTroubleReport", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainTroubleReport.js", "scrTroubleReport", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainTroubleReport.js", "scrTroubleReport", "fncShow( <_response>)") ;
		dEnd( 1, "mainTroubleReport.js", "scrTroubleReport", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainTroubleReport.js", "scrTroubleReport", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainTroubleReport.js", "scrTroubleReport", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridTroubleReportAction._onFirstPage() ;
		}
		dEnd( 1, "mainTroubleReport.js", "scrTroubleReport", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * 
	 */
	dTrace( 2, "mainTroubleReport.js", "scrTroubleReport", "*", "defining this.onCityChanged") ;
	this.onCityChanged	=	function() {
		dBegin( 1, "mainTroubleReport.js", "scrTroubleReport", "onCityChanged()") ;
		dEnd( 1, "mainTroubleReport.js", "scrTroubleReport", "onCityChanged()") ;
	} ;
	dTrace( 2, "mainTroubleReport.js", "scrTroubleReport", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainTroubleReport.js", "scrTroubleReport", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	dEnd( 1, "mainTroubleReport.js", "scrTroubleReport", "main()") ;
}
