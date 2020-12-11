/**
 * employee
 *
 * registers the module in the central database
 */
/**
 * @returns {employee}
 */
new employee() ;
function	employee() {
	dBegin( 1, "employee.js", "employee", "__constructor()") ;
	wapScreen.call( this, "employee") ;
	this.package	=	"ModBase" ;
	this.module	=	"Employee" ;
	this.coreObject	=	"Employee" ;
	this.keyForm	=	"EmployeeKeyData" ;
	this.keyField	=	getFormField( 'EmployeeKeyData', 'EmployeeNo') ;
	this.delConfDialog	=	"/ModBase/Employee/confEmployeeDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Employee"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	dTrace( 2, "employee.js", "employee", "*", "creating wapSelector") ;
	this.select	=	new wapSelector( this, "selEmployee", {
										objectClass:	"Employee"
									,	module:			"ModBase"
									,	screen:			"Employee"
									,	selectorName:	"selEmployee"
									,	formFilterName: "formSelEmployeeFilter"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "employee{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcEmployeeMain", "tcEmployeeMain_cpGeneral") ;
															dEnd( 102, "employee{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
													}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "employee.js", "employee", "*", "creating wapGrid") ;
	this.gridEmployeeOV	=	new wapGrid( this, "gridEmployeeOV", {
										object:			"Employee"
									,	screen:			"employee"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainemployee{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainemployee{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "employee{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageEmployeeMainDataEntry") ;
															dEnd( 102, "employee{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "employee.js", "employee", "*", "calling this.gridEmployeeOV._onFirstPage()") ;
	this.gridEmployeeOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for DocumentRevision
	 */
	dTrace( 2, "employee.js", "employee", "*", "creating gridEmployeeAbsence") ;
	this.gridEmployeeAbsence	=	new wapGrid( this, "gridEmployeeAbsence", {
										object:			"EmployeeAbsence"
									,	screen:			"employee"
									,	parentDS:		this.dataSource
									,	editorName:		"edtEmployeeAbsence"
									,	moduleName:		"ModBase"
									,	subModuleName:	"Employee"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainDocument{wapGrid.js}", "wapGrid{gridEmployeeAbsence}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainDocument{wapGrid.js}", "wapGrid{gridEmployeeAbsence}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainDocument{wapGrid.js}", "wapGrid{gridEmployeeAbsence}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainDocument{wapGrid.js}", "wapGrid{gridEmployeeAbsence}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "employee.js", "employee", "fncLink()") ;
		dEnd( 1, "employee.js", "employee", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "employee.js", "employee", "fncShow( <_response>)") ;
		dEnd( 1, "employee.js", "employee", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "employee.js", "employee", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridEmployeeAbsence._onFirstPage() ;
		}
		dEnd( 1, "employee.js", "employee", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "employee.js", "employee", "*", "defining this.onMisc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "employee.js", "employee", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "employee.js", "employee", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "employee.js", "employee", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "employee.js", "employee", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageEmployeeSurveyEntry") ;
	dEnd( 1, "employee.js", "employee", "main()") ;
}
