/**
 * scrAppUser
 *
 * registers the module in the central database
 */
/**
 * @returns {scrAppUser}
 */
new appUser() ;
function	appUser() {
	dBegin( 1, "appUser.js", "scrAppUser", "__constructor()") ;
	wapScreen.call( this, "appUser") ;
	this.package	=	"ModBase" ;
	this.module	=	"AppUser" ;
	this.coreObject	=	"AppUser" ;
	this.keyForm	=	"AppUserKeyData" ;
	this.keyField	=	getFormField( 'AppUserKeyData', 'UserId') ;
	this.delConfDialog	=	"/ModBase/AppUser/confAppUserDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "AppUser"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selAppUser", {
										objectClass:	"AppUser"
									,	module:			"ModBase"
									,	screen:			"AppUser"
									,	selectorName:	"selAppUser"
									,	formFilterName: "formSelAppUserFilter"
									,	formTop:		"formSelAppUserTop"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "appUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcAppUserMain", "tcAppUserMain_cpGeneral") ;
															dEnd( 102, "appUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
													}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "appUser.js", "scrAppUser", "*", "creating gridAppUserOV") ;
	this.gridAppUserOV	=	new wapGrid( this, "gridAppUserOV", {
										object:			"AppUser"
									,	screen:			"appUser"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "appUser{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "appUser{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "appUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageAppUserMainDataEntry") ;
															dEnd( 102, "appUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "appUser.js", "scrAppUser", "*", "calling dsAppUserOV._onFirstPage()") ;
	this.gridAppUserOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "appUser.js", "scrAppUser", "*", "creating gridLeverLength") ;
	this.gridBrakePad	=	new wapGrid( this, "gridBrakePad", {
										object:			"BrakePad"
									,	screen:			"AppUser"
									,	parentDS:		this.dataSource
									,	onDataSourceLoadedExt:	function( _parent, _data) {
															dBegin( 102, "appUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.parent.gridBrakePadValue.clear() ;
															dEnd( 102, "appUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "appUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
															this.parent.gridBrakePadValue.dataSource.id	=	_id ;
															this.parent.gridBrakePadValue._onFirstPage() ;
															dEnd( 102, "appUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "appUser.js", "scrAppUser", "*", "creating gridLeverLength") ;
	this.gridBrakePadValue	=	new wapGrid( this, "gridBrakePadValue", {
										object:			"BrakePadValue"
									,	screen:			"AppUser"
									,	parentDS:		this.gridBrakePad.dataSource
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "appUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															dEnd( 102, "appUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "appUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "appUser{wapGrid.js}", "wapGrid{gridLeverLength}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "appUser.js", "scrAppUser", "fncLink()") ;
		dEnd( 1, "appUser.js", "scrAppUser", "fncLink()") ;
	} ;
	/**
	 *
	 * @param {boolean} _wd
	 * @param {string} _fnc
	 * @param {string} _form
	 * @returns {void}
	 */
	dTrace( 2, "mainBrakePad.js", "scrBrakePad", "*", "defining this.onUpdate") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainBrakePad.js", "scrBrakePad", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainBrakePad.js", "scrBrakePad", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "appUser.js", "scrAppUser", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "appUser.js", "scrAppUser", "fncShow( <_response>)") ;
		dEnd( 1, "appUser.js", "scrAppUser", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "appUser.js", "scrAppUser", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "appUser.js", "scrAppUser", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridBrakePad._onFirstPage() ;
		}
		dEnd( 1, "appUser.js", "scrAppUser", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "appUser.js", "scrAppUser", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "appUser.js", "scrAppUser", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageAppUserSurveyEntry") ;
	dEnd( 1, "appUser.js", "scrAppUser", "main()") ;
}
