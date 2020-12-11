/**
 * mainProductGroup.js
 * ===============
 *
 * registers the module in the central database
 */
/**
 * @returns {scrProductGroup}
 */
new mainProductGroup() ;
/**
 *
 */
function	mainProductGroup() {
	dBegin( 1, "mainProductGroup.js", "scrProductGroup", "main()") ;
	wapScreen.call( this, "ProductGroup") ;
	this.package	=	"ModMisc" ;
	this.module	=	"ProductGroup" ;
	this.coreObject	=	"ProductGroup" ;
	this.keyForm	=	"ProductGroupKeyData" ;
	this.keyField	=	getFormField( 'ProductGroupKeyData', 'ProductGroupNo') ;
	this.delConfDialog	=	"/ModMisc/ProductGroup/confProductGroupDel.php" ;
	this.dataSource	=	new wapDataSource( this, {
											object: 	"ProductGroup"
										,	objectKey:	"ProductGroupNo"
										}) ;		// dataSource for display
	/**
	 * perform additional initialization needed for the HTML editors
	 */
	dTrace( 2, "mainProductGroup.js", "mainProductGroup", "*", "pulling together RTF Editors") ;
	myDiv	=	document.getElementById( this.screenName) ;
	myEditors	=	myDiv.getElementsByTagName( "textarea") ;
	dTrace( 2, "mainProductGroup.js", "mainProductGroup", "*", "# of textareas := " + myEditors.length.toString()) ;
	for ( var i=0 ; i<myEditors.length ; i++) {
		myEditors[i].setAttribute( "name", myEditors[i].getAttribute( "name") + myEditors[i].getAttribute( "id")) ;
		dTrace( 2, "mainProductGroup.js", "mainProductGroup", "*", "this.name := '" + myEditors[i].getAttribute( "name") + "'") ;
		CKEDITOR.replace( myEditors[i].getAttribute( "name")) ;
	}
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selProductGroup", {
											pkg:	"ModMisc"
										,	script:	"loadScreen.php?screen=ModMisc/ProductGroup/selProductGroup.xml"
										,	obj:	"ProductGroup"
										}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainProductGroup.js", "scrProductGroup", "*", "creating gridProductGroupOV") ;
	this.gridProductGroupOV	=	new wapGrid( this, "gridProductGroupOV", {
										object:			"ProductGroup"
									,	module:			"ModMisc"
									,	screen:			"ProductGroup"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainProductGroup{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainProductGroup{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:	function( _parent, _id) {
															dBegin( 102, "mainProductGroup{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageProductGroupMainDataEntry") ;
															dEnd( 102, "mainProductGroup{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainProductGroup.js", "scrProductGroup", "*", "calling gridProductGroupOV._onFirstPage()") ;
	this.gridProductGroupOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for ProductGroupItem
	 */
	dTrace( 2, "mainProductGroup.js", "scrProductGroup", "*", "creating gridCust-------------omerContact") ;
	this.gridProductGroupItem	=	new wapGrid( this, "gridProductGroupItem", {
										object:			"ProductGroupItem"
									,	module :		"ModMisc"
									,	screen:			"ProductGroup"
									,	parentDS:		this.dataSource
									,	editorName:		"edtProductGroupItem"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainProductGroup{wapGrid.js}", "wapGrid{gridProductGroupItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainProductGroup{wapGrid.js}", "wapGrid{gridProductGroupItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainProductGroup{wapGrid.js}", "wapGrid{gridProductGroupItem}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainProductGroup{wapGrid.js}", "wapGrid{gridProductGroupItem}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainProductGroup.js", "scrProductGroup", "fncLink()") ;
		dEnd( 1, "mainProductGroup.js", "scrProductGroup", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainProductGroup.js", "scrProductGroup", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainProductGroup.js", "scrProductGroup", "fncShow( <_response>)") ;
		dEnd( 1, "mainProductGroup.js", "scrProductGroup", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainProductGroup.js", "scrProductGroup", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _response) {
		dBegin( 1, "mainProductGroup.js", "scrProductGroup", "onDataSourceLoaded( <_response>)") ;
		if ( _response) {
			this.displayAllData( _response, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridProductGroupItem._onFirstPage() ;
		}
		dEnd( 1, "mainProductGroup.js", "scrProductGroup", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	this.upload	=	function() {
		this.dataSource.upload( true, "setImage", "formProductGroupImage", "") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainProductGroup.js", "scrProductGroup", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainProductGroup.js", "scrProductGroup", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageProductGroupSurveyEntry") ;
	dEnd( 1, "mainProductGroup.js", "scrProductGroup", "main()") ;
}
