/**
 * mainProject.js
 * ===============
 *
 * registers the module in the central database
 */
/**
 * @returns {scrProject}
 */
new mainProject() ;
/**
 * @returns {scrProject}
 */
function	mainProject() {
	dBegin( 1, "mainProject.js", "scrProject", "main()") ;
	wapScreen.call( this, "Project") ;
	this.package	=	"ModBase" ;
	this.module	=	"Project" ;
	this.coreObject	=	"Project" ;
	this.keyForm	=	"ProjectKeyData" ;
	this.keyField	=	getFormField( 'ProjectKeyData', 'ProjectNo') ;
	this.delConfDialog	=	"/ModBase/Project/confProjectDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Project"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selProject", {
											pkg:	"ModBase"
										,	script:	"loadScreen.php?screen=ModBase/Project/selProject.xml"
										,	obj:	"Project"
										}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainProject.js", "scrProject", "*", "creating gridProjectOV") ;
	this.gridProjectOV	=	new wapGrid( this, "gridProjectOV", {
										object:			"Project"
									,	screen:			"Project"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainProject{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainProject{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainProject{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageProjectMainDataEntry") ;
															dEnd( 102, "mainProject{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainProject.js", "scrProject", "*", "calling gridProjectOV._onFirstPage()") ;
	this.gridProjectOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for ProjectDocument
	 */
	dTrace( 2, "mainProject.js", "scrProject", "*", "creating gridCustomerContact") ;
	this.gridProjectDocument	=	new wapGrid( this, "gridProjectDocument", {
										object:			"Document"
									,	screen:			"Project"
									,	parentDS:		this.dataSource
									,	editorName:		"edtProjectDocument"
									,	moduleName:		"ModBase"
									,	subModuleName:	"Project"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainProject{wapGrid.js}", "wapGrid{gridProjectDocument}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainProject{wapGrid.js}", "wapGrid{gridProjectDocument}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainProject{wapGrid.js}", "wapGrid{gridProjectDocument}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainProject{wapGrid.js}", "wapGrid{gridProjectDocument}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainProject.js", "scrProject", "fncLink()") ;
		dEnd( 1, "mainProject.js", "scrProject", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainProject.js", "scrProject", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainProject.js", "scrProject", "fncShow( <_response>)") ;
		dEnd( 1, "mainProject.js", "scrProject", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainProject.js", "scrProject", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainProject.js", "scrProject", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			dTrace( 2, "mainProject.js", "scrProject", "onDataSourceLoaded( ...)", "displaying main object data") ;
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridProjectDocument._onFirstPage() ;
		}
		dEnd( 1, "mainProject.js", "scrProject", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainProject.js", "scrProject", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainProject.js", "scrProject", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageProjectSurveyEntry") ;
	dEnd( 1, "mainProject.js", "scrProject", "main()") ;
}
