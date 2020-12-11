/**
 * mainOptionGroup.js
 * ===============
 * 
 * registers the module in the central database
 */
/**
 * @returns {scrOptionGroup}
 */
function	scrOptionGroup() {
	dBegin( 1, "mainOptionGroup.js", "scrOptionGroup", "main()") ;
	wapScreen.call( this, "OptionGroup") ;
	this.package	=	"ModBase" ;
	this.module	=	"OptionGroup" ;
	this.coreObject	=	"OptionGroup" ;
	this.keyForm	=	"OptionGroupKeyData" ;
	this.keyField	=	getFormField( 'OptionGroupKeyData', 'OptionGroupName') ;
	this.delConfDialog	=	"/ModBase/OptionGroup/confOptionGroupDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "OptionGroup"}) ;		// dataSource for display

	/**
	 * create the grid to display the customer list on the first "Overview" tab 
	 */
	dTrace( 2, "mainOptionGroup.js", "scrOptionGroup", "*", "creating gridOptionGroupOV") ;
	this.gridOptionGroupOV	=	new wapGrid( this, "gridOptionGroupOV", {
										xmlTableName:	"TableOptionGroupOV"
									,	object:			"OptionGroup"
									,	module:			"ModSys"
									,	screen:			"OptionGroup"
									,	formTop:		"formOptionGroupOVTop"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainOptionGroup{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainOptionGroup{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainOptionGroup{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcOptionGroupMain", "tcOptionGroupMain_cpGeneral") ;
															dEnd( 102, "mainOptionGroup{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainOptionGroup.js", "scrOptionGroup", "*", "calling gridOptionGroupOV._onFirstPage()") ;
	this.gridOptionGroupOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact 
	 */
	dTrace( 2, "mainCustomer.js", "scrCustomer", "*", "creating gridCust-------------omerContact") ;
	this.gridOption	=	new wapGrid( this, "gridOption", {
										xmlTableName:	"TableOption"
									,	object:			"Option"
									,	module :		"ModSys"
									,	screen:			"OptionGroup"
									,	parentDS:		this.dataSource
									,	formTop:		"formOptionTop"
									,	editorName:		"edtOption"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainOption{wapGrid.js}", "wapGrid{gridOption}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainOption{wapGrid.js}", "wapGrid{gridOption}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainOption{wapGrid.js}", "wapGrid{gridOption}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainOption{wapGrid.js}", "wapGrid{gridOption}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainOptionGroup.js", "scrOptionGroup", "fncLink()") ;
		dEnd( 1, "mainOptionGroup.js", "scrOptionGroup", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainOptionGroup.js", "scrOptionGroup", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainOptionGroup.js", "scrOptionGroup", "fncShow( <_response>)") ;
		dEnd( 1, "mainOptionGroup.js", "scrOptionGroup", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainOptionGroup.js", "scrOptionGroup", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _response) {
		dBegin( 1, "mainOptionGroup.js", "scrOptionGroup", "onDataSourceLoaded( <_response>)") ;
		if ( _response) {
			this.displayAllData( "objects.OptionGroup[0]") ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridOption._onFirstPage() ;
		}
		dEnd( 1, "mainOptionGroup.js", "scrOptionGroup", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * 
	 */
	dTrace( 2, "mainOptionGroup.js", "scrOptionGroup", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainOptionGroup.js", "scrOptionGroup", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	dEnd( 1, "mainOptionGroup.js", "scrOptionGroup", "main()") ;
}