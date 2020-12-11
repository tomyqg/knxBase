/**
 * mainArticleGroup.js
 * ===============
 *
 * registers the module in the central database
 */
/**
 * @returns {scrArticleGroup}
 */
new mainArticleGroup() ;
/**
 *
 */
function	mainArticleGroup() {
	dBegin( 1, "mainArticleGroup.js", "scrArticleGroup", "main()") ;
	wapScreen.call( this, "ArticleGroup") ;
	this.package	=	"ModMisc" ;
	this.module	=	"ArticleGroup" ;
	this.coreObject	=	"ArticleGroup" ;
	this.keyForm	=	"ArticleGroupKeyData" ;
	this.keyField	=	getFormField( 'ArticleGroupKeyData', 'ArticleGroupNo') ;
	this.delConfDialog	=	"/ModMisc/ArticleGroup/confArticleGroupDel.php" ;
	this.dataSource	=	new wapDataSource( this, {
											object: 	"ArticleGroup"
										,	objectKey:	"ArticleGroupNo"
										}) ;		// dataSource for display
	dTrace( 2, "mainArticleGroup.js", "mainArticleGroup", "*", "pulling together RTF Editors") ;
	myDiv	=	document.getElementById( this.screenName) ;
	myEditors	=	myDiv.getElementsByTagName( "textarea") ;
	dTrace( 2, "mainArticleGroup.js", "mainArticleGroup", "*", "# of textareas := " + myEditors.length.toString()) ;
	for ( var i=0 ; i<myEditors.length ; i++) {
		myEditors[i].setAttribute( "name", myEditors[i].getAttribute( "name") + myEditors[i].getAttribute( "id")) ;
		dTrace( 2, "mainArticleGroup.js", "mainArticleGroup", "*", "this.name := '" + myEditors[i].getAttribute( "name") + "'") ;
		CKEDITOR.replace( myEditors[i].getAttribute( "name")) ;
	}
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selArticleGroup", {
											pkg:	"ModMisc"
										,	script:	"loadScreen.php?screen=ModMisc/ArticleGroup/selArticleGroup.xml"
										,	obj:	"ArticleGroup"
										}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainArticleGroup.js", "scrArticleGroup", "*", "creating gridArticleGroupOV") ;
	this.gridArticleGroupOV	=	new wapGrid( this, "gridArticleGroupOV", {
										object:			"ArticleGroup"
									,	module:			"ModMisc"
									,	screen:			"ArticleGroup"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticleGroup{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticleGroup{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:	function( _parent, _id) {
															dBegin( 102, "mainArticleGroup{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageArticleGroupMainDataEntry") ;
															dEnd( 102, "mainArticleGroup{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainArticleGroup.js", "scrArticleGroup", "*", "calling gridArticleGroupOV._onFirstPage()") ;
	this.gridArticleGroupOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for ArticleGroupItem
	 */
	dTrace( 2, "mainArticleGroup.js", "scrArticleGroup", "*", "creating gridCust-------------omerContact") ;
	this.gridArticleGroupItem	=	new wapGrid( this, "gridArticleGroupItem", {
										object:			"ArticleGroupItem"
									,	module :		"ModMisc"
									,	screen:			"ArticleGroup"
									,	parentDS:		this.dataSource
									,	editorName:		"edtArticleGroupItem"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticleGroup{wapGrid.js}", "wapGrid{gridArticleGroupItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticleGroup{wapGrid.js}", "wapGrid{gridArticleGroupItem}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainArticleGroup{wapGrid.js}", "wapGrid{gridArticleGroupItem}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainArticleGroup{wapGrid.js}", "wapGrid{gridArticleGroupItem}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainArticleGroup.js", "scrArticleGroup", "fncLink()") ;
		dEnd( 1, "mainArticleGroup.js", "scrArticleGroup", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainArticleGroup.js", "scrArticleGroup", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainArticleGroup.js", "scrArticleGroup", "fncShow( <_response>)") ;
		dEnd( 1, "mainArticleGroup.js", "scrArticleGroup", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainArticleGroup.js", "scrArticleGroup", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _response) {
		dBegin( 1, "mainArticleGroup.js", "scrArticleGroup", "onDataSourceLoaded( <_response>)") ;
		if ( _response) {
			this.displayAllData( _response, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridArticleGroupItem._onFirstPage() ;
		}
		dEnd( 1, "mainArticleGroup.js", "scrArticleGroup", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	this.upload	=	function() {
		this.dataSource.upload( true, "setImage", "formArticleGroupImage", "") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainArticleGroup.js", "scrArticleGroup", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainArticleGroup.js", "scrArticleGroup", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageArticleGroupSurveyEntry") ;
	dEnd( 1, "mainArticleGroup.js", "scrArticleGroup", "main()") ;
}
