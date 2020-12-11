/**
 * tilgungsPlan
 *
 * registers the module in the central database
 */
/**
 * @returns {tilgungsPlan}
 */
new tilgungsPlan() ;
function	tilgungsPlan() {
	dBegin( 1, "tilgungsPlan.js", "tilgungsPlan", "main()") ;
	wapScreen.call( this, "tilgungsPlan") ;
	this.package	=	"." ;
	this.module	=	"." ;
	this.coreObject	=	"TilgPlan" ;
	this.keyForm	=	"TilgPlanKeyData" ;
	this.keyField	=	getFormField( 'TilgPlanKeyData', 'TilgPlanNo') ;
	this.delConfDialog	=	null ;
	this.dataSource	=	new wapDataSource( this, { object: "TilgPlan"}) ;		// dataSource for display
	/**
	 * creating the datastore and siplay grid for TilgPlanMonat
	 */
	dTrace( 2, "mainTilgPlan.js", "scrCustomer", "*", "creating gridTilgPlanMonat") ;
	this.gridTilgPlanMonat	=	new wapGrid( this, "gridTilgPlanMonat", {
										object:			"TilgPlanMonat"
									,	screen:			"Customer"
									,	parentDS:		this.dataSource
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainTilgPlan{wapGrid.js}", "wapGrid{gridTilgPlanMonat}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainTilgPlan{wapGrid.js}", "wapGrid{gridTilgPlanMonat}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "tilgungsPlan.js", "tilgungsPlan", "fncLink()") ;
		dEnd( 1, "tilgungsPlan.js", "tilgungsPlan", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "tilgungsPlan.js", "tilgungsPlan", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "tilgungsPlan.js", "tilgungsPlan", "fncShow( <_response>)") ;
		dEnd( 1, "tilgungsPlan.js", "tilgungsPlan", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "tilgungsPlan.js", "tilgungsPlan", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "tilgungsPlan.js", "tilgungsPlan", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridTilgPlanMonat._onFirstPage() ;
		}
		dEnd( 1, "tilgungsPlan.js", "tilgungsPlan", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "tilgungsPlan.js", "tilgungsPlan", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "tilgungsPlan.js", "tilgungsPlan", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageTilgungsPlanEntry") ;
	dEnd( 1, "tilgungsPlan.js", "tilgungsPlan", "main()") ;
}
