/**
 * scrTrailer
 *
 * registers the module in the central database
 */
/**
 * @returns {scrTrailer}
 */
new trailer() ;
function	trailer() {
	dBegin( 1, "mainTrailer.js", "scrTrailer", "main()") ;
	wapScreen.call( this, "trailer") ;
	this.package	=	"ModCalc" ;
	this.module	=	"Trailer" ;
	this.coreObject	=	"Trailer" ;
	this.keyForm	=	"TrailerKeyData" ;
	this.keyField	=	getFormField( 'TrailerKeyData', 'TrailerId') ;
	this.delConfDialog	=	"/ModCalc/Trailer/confTrailerDel.php" ;
	this.dataSource	=	new wapDataSource( this, {
		 										object: "Trailer"
											,	objectKey:	"TrailerId"
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
	this.select	=	new wapSelector( this, "selTrailer", {
										objectClass:	"Trailer"
									,	module:			this.package
									,	screen:			"Trailer"
									,	selectorName:	"selTrailer"
									,	formFilterName: "formTrailerSelectFilter"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainTrailer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageTrailerMainDataEntry") ;
															dEnd( 102, "mainTrailer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainTrailer.js", "scrTrailer", "*", "creating gridTrailerOV") ;
	this.gridTrailerOV	=	new wapGrid( this, "gridTrailerOV", {
										object:			"Trailer"
									,	screen:			"Trailer"
									,	onDataSourceLoaded:	function( _parent, _data) {
																dBegin( 102, "mainTrailer{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
																this.show() ;
																dEnd( 102, "mainTrailer{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainTrailer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageTrailerMainDataEntry") ;
															dEnd( 102, "mainTrailer{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainTrailer.js", "scrTrailer", "*", "calling dsTrailerOV._onFirstPage()") ;
	this.gridTrailerOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and diplay grid for CustomerContact
	 */
	dTrace( 2, "mainTrailer.js", "scrTrailer", "*", "creating gridTrailerAxle") ;
	this.gridTrailerAxle	=	new wapGrid( this, "gridTrailerAxle", {
										object:			"TrailerAxle"
									,	screen:			"Trailer"
									,	parentDS:		this.dataSource
									,	editorName:		"edtTrailerAxle"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainTrailer{wapGrid.js}", "wapGrid{gridTrailerAxle}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainTrailer{wapGrid.js}", "wapGrid{gridTrailerAxle}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainTrailer{wapGrid.js}", "wapGrid{gridTrailerAxle}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainTrailer{wapGrid.js}", "wapGrid{gridTrailerAxle}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 *
	 */
	myFZGSelect	=	new wapSelectXML( this, "mySelect", {
											selectNode:	getFormField( "formTrailerMain", "TrailerTypeId")
										,	object:		"TrailerType"
										,	key:		"TrailerTypeId"
										,	value:		"Options"
										,	onDataSourceLoaded:	function( _parent, _data) {
												dBegin( 102, "mainTrailer{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
												this.show() ;
												dEnd( 102, "mainTrailer{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
											}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainTrailer.js", "scrTrailer", "fncLink()") ;
		dEnd( 1, "mainTrailer.js", "scrTrailer", "fncLink()") ;
	} ;
	/**
	 *
	 * @param {boolean} _wd
	 * @param {string} _fnc
	 * @param {string} _form
	 * @returns {void}
	 */
	dTrace( 2, "mainTrailer.js", "mainTrailer", "*", "defining this.onMisc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainArticle.js", "mainTrailer", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainArticle.js", "mainTrailer", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainTrailer.js", "scrTrailer", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainTrailer.js", "scrTrailer", "fncShow( <_response>)") ;
		dEnd( 1, "mainTrailer.js", "scrTrailer", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainTrailer.js", "scrTrailer", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 3, "mainTrailer.js", "scrTrailer", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridTrailerAxle._onFirstPage() ;
		}
		dEnd( 3, "mainTrailer.js", "scrTrailer", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * setTrailerTypeNo
	 * ================
	 *
	 *
	 */
	dTrace( 2, "mainTrailer.js", "scrTrailer", "*", "defining this.onSetTrailerTypeNo") ;
	this.onSetTrailerTypeNo	=	function( _obj) {
		dBegin( 1, "mainTrailer.js", "scrTrailer", "onSetTrailerTypeNo()") ;
		myFZGSelect.refresh() ;
		dEnd( 1, "mainTrailer.js", "scrTrailer", "onSetTrailerTypeNo()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainTrailer.js", "scrTrailer", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainTrailer.js", "scrTrailer", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageTrailerSurveyEntry") ;
	dEnd( 1, "mainTrailer.js", "scrTrailer", "main()") ;
}
