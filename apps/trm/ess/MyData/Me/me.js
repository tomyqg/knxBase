/**
 * me
 *
 * registers the module in the central database
 */
/**
 * @returns {me}
 */
new me() ;
function	me() {
	dBegin( 1, "me.js", "me", "__constructor()") ;
	wapScreen.call( this, "me") ;
	this.package	=	"MyData" ;
	this.module	=	"Me" ;
	this.coreObject	=	"Me" ;
	this.keyForm	=	"MeKeyData" ;
	this.keyField	=	getFormField( 'MeKeyData', 'EmployeeNo') ;
	this.delConfDialog	=	null ;
	this.dataSource	=	new wapDataSource( this, { object: "Me"}) ;		// dataSource for display
	/**
	 * creating the datastore and siplay grid for DocumentRevision
	 */
	dTrace( 2, "me.js", "me", "*", "creating gridMeAbsence") ;
	this.gridMeAbsence	=	new wapGrid( this, "gridMeAbsence", {
										object:			"EmployeeAbsence"
									,	screen:			"me"
									,	parentDS:		this.dataSource
									,	editorName:		"edtMeAbsence"
									,	moduleName:		"ModBase"
									,	subModuleName:	"Me"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainDocument{wapGrid.js}", "wapGrid{gridMeAbsence}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainDocument{wapGrid.js}", "wapGrid{gridMeAbsence}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainDocument{wapGrid.js}", "wapGrid{gridMeAbsence}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainDocument{wapGrid.js}", "wapGrid{gridMeAbsence}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "me.js", "me", "fncLink()") ;
		dEnd( 1, "me.js", "me", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "me.js", "me", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "me.js", "me", "fncShow( <_response>)") ;
		dEnd( 1, "me.js", "me", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "me.js", "me", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "me.js", "me", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridMeAbsence._onFirstPage() ;
		}
		dEnd( 1, "me.js", "me", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "me.js", "me", "*", "defining this.onMisc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "me.js", "me", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "me.js", "me", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "me.js", "me", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "me.js", "me", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageMeMainDataEntry") ;
	dEnd( 1, "me.js", "me", "main()") ;
}
