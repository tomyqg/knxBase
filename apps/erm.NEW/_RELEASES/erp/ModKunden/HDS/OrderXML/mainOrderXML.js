/**
 * scrOrderXML
 * 
 * registers the module in the central database
 */
/**
 * @returns {scrOrderXML}
 */
function	scrOrderXML() {
	dBegin( 1, "mainOrderXML.js", "scrOrderXML", "main()") ;
	wapScreen.call( this, "OrderXML") ;
	this.package	=	"ModBase" ;
	this.module	=	"OrderXML" ;
	this.coreObject	=	"OrderXML" ;
	this.keyForm	=	"OrderXMLKeyData" ;
	this.keyField	=	getFormField( 'OrderXMLKeyData', 'OrderXMLNo') ;
	this.delConfDialog	=	"/ModBase/OrderXML/confOrderXMLDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "OrderXML"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selOrderXML", {
											pkg:	"ModBase"
										,	script:	"loadScreen.php?screen=ModBase/OrderXML/selOrderXML.xml"
										,	obj:	"OrderXML"
										}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab 
	 */
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "creating refList") ;
	this.dsOrderXMLOV	=	new wapDataSource( this, {
										object: "OrderXML"
									,	fncGet: "getList"
								}) ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "creating gridOrderXMLOV") ;
	this.gridOrderXMLOV	=	new wapGrid( this, "gridOrderXMLOV", {
										domTableName:	"TableOrderXMLOV"
									,	primObj:		"OrderXML"
									,	dataItemName:	"OrderXML"
									,	dataItemEditor:	null
									,	mod:			"ModBase"
									,	screen:			"OrderXML"
									,	dataSource:		this.dsOrderXMLOV
									,	formTop:		"formOrderXMLOVTop"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainOrderXML{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainOrderXML{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainOrderXML{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcOrderXMLMain", "tcOrderXMLMain_cpGeneral") ;
															dEnd( 102, "mainOrderXML{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "assigning dsOrderXMLOV.parent") ;
	this.dsOrderXMLOV.parent	=	this.gridOrderXMLOV ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "calling dsOrderXMLOV._onFirstPage()") ;
	this.gridOrderXMLOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "fncLink()") ;
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.onEnter") ;
	this.onEnter	=	function( _key) {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "onSelect( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "onSelect( '"+_key+"')") ;
	} ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.onPrev(ious)") ;
	this.onPrev	=	function() {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "onPrev()") ;
		this.dataSource.getPrev( this.keyField.value, -1, "") ;
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "onPrev()") ;
	} ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.onNext") ;
	this.onNext	=	function() {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "onNext()") ;
		this.dataSource.getNext( this.keyField.value, -1, "") ;
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "onNext()") ;
	} ;
	/**
	 * 
	 */
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.onSelect") ;
	this.onSelect	=	function( _key) {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "onSelect( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "onSelect( '"+_key+"')") ;
	} ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.onSelectByKey") ;
	this.onSelectByKey	=	function( _key) {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "onSelectByKey( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "onSelectByKey( '"+_key+"')") ;
	} ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.onSelect") ;
	this.onSelectById	=	function( _id) {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "onSelectById( '"+_id+"')") ;
		this.dataSource.load( "", _id, "") ;
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "onSelectById( '"+_id+"')") ;
	} ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.onCreate") ;
	this.onCreate	=	function( _wd, _fnc, _form) {
		this.sDispatch( _wd, _fnc, _form) ;
	} ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.onUpdate") ;
	this.onUpdate	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "onUpdate( <...>)") ;
		this.dataSource.update(this.keyField.value, -1, "", _form) ;
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "onUpdate( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "fncShow( <_response>)") ;
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _response) {
		dBegin( 1, "mainOrderXML.js", "scrOrderXML", "onDataSourceLoaded( <_response>)") ;
		if ( _response) {
			dTrace( 2, "mainOrderXML.js", "scrOrderXML", ".......:", screenTable["OrderXML"].dataSource.OrderXML.OrderXML[0].OrderXMLName1) ;
			/**
			 * 
			 */
			customer	=	_response.getElementsByTagName( "OrderXML")[0] ;
			attrs	=	customer.childNodes ;
			dTrace( 2, "mainOrderXML.js", "scrOrderXML", "onDataSourceLoaded( ...)", "displaying main object data") ;
			this.displayData( "OrderXMLKeyData", "OrderXML.OrderXML[0]") ;
			this.displayData( "formOrderXMLMain", "OrderXML.OrderXML[0]") ;
			this.displayData( "formOrderXMLModi", "OrderXML.OrderXML[0]") ;
			/**
			 * 
			 */
			dTrace( 2, "mainOrderXML.js", "scrOrderXML", "onDataSourceLoaded( ...)", "assigning main object key to grids") ;
			this.dataSource.key	=	this.keyField.value ;
		}
		dEnd( 1, "mainOrderXML.js", "scrOrderXML", "onDataSourceLoaded( <_response>)") ;
	} ;
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainOrderXML.js", "scrOrderXML", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	dEnd( 1, "mainOrderXML.js", "scrOrderXML", "main()") ;
}
