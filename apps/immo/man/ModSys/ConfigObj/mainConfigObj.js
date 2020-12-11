/**
 * regModConfigObj
 * 
 * registers the module in the central database
 */
function	scrConfigObj() {
	dBegin( 1, "mainConfigObj.js", "scrConfigObj", "main()") ;
	wapScreen.call( this, "ConfigObj") ;
	this.package	=	"ModSys" ;			// directory of the module
	this.module	=	"ConfigObj" ;					// sub-directory of the screen
	this.coreObject	=	"ConfigObj" ;
	this.keyForm	=	"ConfigObjKeyData" ;		// form
	this.keyField	=	getFormField( 'ConfigObjKeyData', 'Id') ;
	this.fncNew	=	null ;
	this.fncDelete	=	null ;
	this.delConfDialog	=	"/ModSys/ConfigObj/confConfigObjDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "ConfigObj"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selConfigObj", {
											pkg:	"ModBase"
										,	script:	"loadScreen.php?screen=ModSys/ConfigObj/selConfigObj.xml"
										,	obj:	"ConfigObj"
										}) ;
	/**
	 * create the dataTableViews
	 */
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "main()", "creating gridConfigObjOV") ;
	this.gridConfigObjOV	=	new wapGrid( this, "gridConfigObjOV", {
										xmlTableName:	"TableConfigObjOV"
									,	object:			"ConfigObj"
									,	module:			"ModSys"
									,	screen:			"ConfigObj"
									,	formTop:		"formConfigObjOVTop"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainConfigObj{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainConfigObj{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainConfigObj{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcConfigObjMain", "tcConfigObjMain_cpGeneral") ;
															dEnd( 102, "mainConfigObj{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "main()", "calling dsConfigObjOV._onFirstPage()") ;
	this.gridConfigObjOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * 
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "fncLink()") ;
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.onEnter") ;
	this.onEnter	=	function( _key) {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "onSelect( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "onSelect( '"+_key+"')") ;
	} ;
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.onPrev(ious)") ;
	this.onPrev	=	function() {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "onPrev()") ;
		this.dataSource.getPrev( this.keyField.value, -1, "") ;
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "onPrev()") ;
	} ;
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.onNext") ;
	this.onNext	=	function() {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "onNext()") ;
		this.dataSource.getNext( this.keyField.value, -1, "") ;
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "onNext()") ;
	} ;
	/**
	 * 
	 */
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.onSelect") ;
	this.onSelect	=	function( _key) {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "onSelect( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "onSelect( '"+_key+"')") ;
	} ;
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.onSelectByKey") ;
	this.onSelectByKey	=	function( _key) {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "onSelectByKey( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "onSelectByKey( '"+_key+"')") ;
	} ;
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.onSelect") ;
	this.onSelectById	=	function( _id) {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "onSelectById( '"+_id+"')") ;
		this.dataSource.load( "", _id, "") ;
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "onSelectById( '"+_id+"')") ;
	} ;
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.onCreate") ;
	this.onCreate	=	function( _wd, _fnc, _form) {
		this.sDispatch( _wd, _fnc, _form) ;
	} ;
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.onUpdate") ;
	this.onUpdate	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "onUpdate( <...>)") ;
		this.dataSource.update(this.keyField.value, -1, "", _form) ;
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "onUpdate( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "fncShow( <_response>)") ;
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _response) {
		dBegin( 1, "mainConfigObj.js", "scrConfigObj", "onDataSourceLoaded( <_response>)") ;
		if ( _response) {
			dTrace( 2, "mainConfigObj.js", "scrConfigObj", ".......:", screenTable["ConfigObj"].dataSource.objects.ConfigObj[0].ConfigObjName1) ;
			/**
			 * 
			 */
			dTrace( 2, "mainConfigObj.js", "scrConfigObj", "onDataSourceLoaded( ...)", "displaying main object data") ;
			this.displayData( "ConfigObjKeyData", "objects.ConfigObj[0]") ;
			this.displayData( "formConfigObjMain", "objects.ConfigObj[0]") ;
			this.displayData( "formConfigObjModi", "objects.ConfigObj[0]") ;
			this.displayData( "formConfigObjZugriff", "objects.ConfigObj[0]") ;
			/**
			 * 
			 */
			dTrace( 2, "mainConfigObj.js", "scrConfigObj", "onDataSourceLoaded( ...)", "assigning main object key to grids") ;
			this.dataSource.key	=	this.keyField.value ;
			dTrace( 2, "mainConfigObj.js", "scrConfigObj", "onDataSourceLoaded( ...)", "this.keyField.value := '" + this.keyField.value + "'") ;
			this.gridConfigObjContact._onFirstPage() ;
		}
		dEnd( 1, "mainConfigObj.js", "scrConfigObj", "onDataSourceLoaded( <_response>)") ;
	} ;
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainConfigObj.js", "scrConfigObj", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	dEnd( 1, "mainConfigObj.js", "scrConfigObj", "main()") ;
}