/**
 * mainTax.js
 * ===============
 *
 * registers the module in the central database
 */
/**
 * @returns {mainTax}
 */
new mainTax() ;
/**
 * @returns {mainTax}
 */
function	mainTax() {
	dBegin( 1, "mainTax.js", "mainTax", "main()") ;
	wapScreen.call( this, "Tax") ;
	this.package	=	"ModBase" ;
	this.module	=	"Tax" ;
	this.coreObject	=	"Tax" ;
	this.keyForm	=	"TaxKeyData" ;
	this.keyField	=	getFormField( 'TaxKeyData', 'TaxClass') ;
	this.delConfDialog	=	"/ModBase/Tax/confTaxDel.php" ;
	this.dataSource	=	new wapDataSource( this, {
												object: "Tax"
											,	objectKey:	"TaxClass"
										}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "gridTaxSelectOV", {
										objectClass:	"Tax"
									,	moduleName:		this.package
									,	subModuleName:	"Tax"
									,	selectorName:	"selTax"
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainTax{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageTaxMainDataEntry") ;
															dEnd( 102, "mainTax{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 * create the grid to display the Tax list on the first "Overview" tab
	 */
	dTrace( 2, "mainTax.js", "mainTax", "*", "creating gridTaxOV") ;
	this.gridTaxOV	=	new wapGrid( this, "gridTaxOV", {
										object:			"Tax"
									,	screen:			"Tax"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainTax{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															dEnd( 102, "mainTax{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainTax{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageTaxMainDataEntry") ;
															dEnd( 102, "mainTax{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainTax.js", "mainTax", "*", "calling gridTaxOV._onFirstPage()") ;
	this.gridTaxOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainTax.js", "mainTax", "fncLink()") ;
		dEnd( 1, "mainTax.js", "mainTax", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainTax.js", "mainTax", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainTax.js", "mainTax", "fncShow( <_response>)") ;
		dEnd( 1, "mainTax.js", "mainTax", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainTax.js", "mainTax", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainTax.js", "mainTax", "onDataSourceLoaded( <_xmlData>)") ;
		if ( _xmlData) {
			dTrace( 2, "mainTax.js", "mainTax", "onDataSourceLoaded( ...)", "displaying main object data") ;
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
		}
		dEnd( 1, "mainTax.js", "mainTax", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainTax.js", "mainTax", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainTax.js", "mainTax", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageTaxSurveyEntry") ;
	dEnd( 1, "mainTax.js", "mainTax", "main()") ;
}
