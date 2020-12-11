/**
 * sysUser
 *
 * registers the module in the central database
 */
/**
 * @returns {sysUser}
 */
new sysUser() ;
function	sysUser() {
	dBegin( 1, "sysUser.js", "sysUser", "__constructor()") ;
	wapScreen.call( this, "sysUser") ;
	this.package	=	"ModBase" ;
	this.module	=	"SysUser" ;
	this.coreObject	=	"SysUser" ;
	this.keyForm	=	"SysUserKeyData" ;
	this.keyField	=	getFormField( 'SysUserKeyData', 'UserId') ;
	this.delConfDialog	=	"/ModBase/SysUser/confSysUserDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "SysUser"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	dTrace( 2, "sysUser.js", "sysUser", "*", "creating wapSelector") ;
	this.select	=	new wapSelector( this, "selSysUser", {
										objectClass:	"SysUser"
									,	module:			"ModBase"
									,	screen:			"SysUser"
									,	selectorName:	"selSysUser"
									,	formFilterName: "formSelSysUserFilter"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "sysUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcSysUserMain", "tcSysUserMain_cpGeneral") ;
															dEnd( 102, "sysUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
													}
							}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "sysUser.js", "sysUser", "*", "creating wapGrid") ;
	this.gridSysUserOV	=	new wapGrid( this, "gridSysUserOV", {
										object:			"SysUser"
									,	screen:			"sysUser"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainsysUser{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainsysUser{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "sysUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageSysUserMainDataEntry") ;
															dEnd( 102, "sysUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "sysUser.js", "sysUser", "*", "calling this.gridSysUserOV._onFirstPage()") ;
	this.gridSysUserOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "sysUser.js", "sysUser", "*", "creating gridSysUserApplications") ;
	this.gridSysUserApplications	=	new wapGrid( this, "gridSysUserApplications", {
										object:			"SysUserApplication"
									,	screen:			"SysUserApplication"
									,	parentDS:		this.dataSource
									,	editorName:		"edtSysUserApplication"
									,	onDataSourceLoadedExt:	function( _parent, _data) {
															dBegin( 102, "mainsysUser{wapGrid.js}", "wapGrid{gridSysUserApplications}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.parent.gridSysConfig.clear() ;
															dEnd( 102, "mainsysUser{wapGrid.js}", "wapGrid{gridSysUserApplications}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainsysUser{wapGrid.js}", "wapGrid{gridSysUserApplications}", "onSelect( <_parent>, '"+_id+"')") ;
															this.parent.gridSysConfig.dataSource.id	=	_id ;
															this.parent.gridSysConfig._onFirstPage() ;
															dEnd( 102, "mainsysUser{wapGrid.js}", "wapGrid{gridSysUserApplications}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "sysUser.js", "sysUser", "*", "creating gridSysConfigObj") ;
	this.gridSysConfig	=	new wapGrid( this, "gridSysConfigObj", {
										object:			"SysConfigObj"
									,	screen:			"SysConfigObj"
									,	parentDS:		this.gridSysUserApplications.dataSource
//									,	editorName:		"edtSysUserApplication"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainsysUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainsysUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 *
	 */
	dTrace( 2, "sysUser.js", "sysUser", "*", "calling this.gridSysUserApplications._onFirstPage()") ;
	this.gridSysUserApplications._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "sysUser.js", "sysUser", "fncLink()") ;
		dEnd( 1, "sysUser.js", "sysUser", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "sysUser.js", "sysUser", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "sysUser.js", "sysUser", "fncShow( <_response>)") ;
		dEnd( 1, "sysUser.js", "sysUser", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "sysUser.js", "sysUser", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "sysUser.js", "sysUser", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridSysUserApplications._onFirstPage() ;							// refresh the dataSource
		}
		dEnd( 1, "sysUser.js", "sysUser", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "sysUser.js", "sysUser", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "sysUser.js", "sysUser", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageSysUserSurveyEntry") ;
	dEnd( 1, "sysUser.js", "sysUser", "main()") ;
}
