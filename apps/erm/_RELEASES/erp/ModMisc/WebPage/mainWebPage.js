/**
 * mainWebPage.js
 * ===============
 *
 * registers the module in the central database
 */
/**
 * @returns {scrProductGroup}
 */
new mainWebPage() ;
/**
 * @returns {scrWebPage}
 */
function	mainWebPage() {
	dBegin( 1, "mainWebPage.js", "mainWebPage", "main()") ;
	wapScreen.call( this, "WebPage") ;
	this.package	=	"ModMisc" ;
	this.module	=	"WebPage" ;
	this.coreObject	=	"WebPage" ;
	this.keyForm	=	"WebPageKeyData" ;
	this.keyField	=	getFormField( 'WebPageKeyData', 'WebPageNo') ;
	this.delConfDialog	=	"/ModMisc/WebPage/confWebPageDel.php" ;
	this.dataSource	=	new wapDataSource( this, {
											object: "WebPage"
										,	objectKey:	"WebPageNo"
										}) ;		// dataSource for display
	/**
	 * perform additional initialization needed for the HTML editors
	 */
	dTrace( 2, "mainWebPage.js", "mainWebPage", "*", "pulling together RTF Editors") ;
	myDiv	=	document.getElementById( this.screenName) ;
	myEditors	=	myDiv.getElementsByTagName( "textarea") ;
	dTrace( 2, "mainWebPage.js", "mainWebPage", "*", "# of textareas := " + myEditors.length.toString()) ;
	for ( var i=0 ; i<myEditors.length ; i++) {
		myEditors[i].setAttribute( "name", myEditors[i].getAttribute( "name") + myEditors[i].getAttribute( "id")) ;
		dTrace( 2, "mainWebPage.js", "mainWebPage", "*", "this.name := '" + myEditors[i].getAttribute( "name") + "'") ;
		CKEDITOR.replace( myEditors[i].getAttribute( "name")) ;
	}
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selWebPage", {
											pkg:	"ModMisc"
										,	script:	"loadScreen.php?screen=ModMisc/WebPage/selWebPage.xml"
										,	obj:	"WebPage"
										}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainWebPage.js", "scrWebPage", "*", "creating gridWebPageOV") ;
	this.gridWebPageOV	=	new wapGrid( this, "gridWebPageOV", {
										object:			"WebPage"
									,	module:			"ModMisc"
									,	screen:			"WebPage"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainWebPage{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainWebPage{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainWebPage{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcWebPageMain", "tcWebPageMain_cpGeneral") ;
															dEnd( 102, "mainWebPage{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainWebPage.js", "scrWebPage", "*", "calling gridWebPageOV._onFirstPage()") ;
	this.gridWebPageOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainWebPage.js", "scrWebPage", "fncLink()") ;
		dEnd( 1, "mainWebPage.js", "scrWebPage", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainWebPage.js", "scrWebPage", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainWebPage.js", "scrWebPage", "fncShow( <_response>)") ;
		dEnd( 1, "mainWebPage.js", "scrWebPage", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainWebPage.js", "scrWebPage", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _response) {
		dBegin( 1, "mainWebPage.js", "scrWebPage", "onDataSourceLoaded( <_response>)") ;
		if ( _response) {
			this.displayAllData( _response, true) ;
			this.dataSource.key	=	this.keyField.value ;
		}
		dEnd( 1, "mainWebPage.js", "scrWebPage", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainWebPage.js", "scrWebPage", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainWebPage.js", "scrWebPage", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageWebPageSurveyEntry") ;
	dEnd( 1, "mainWebPage.js", "scrWebPage", "main()") ;
}
