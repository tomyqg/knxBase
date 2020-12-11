/**
 * mainDocument.js
 * ===============
 *
 * registers the module in the central database
 */
/**
 * @returns {scrCustomer}
 */
new mainDocument() ;
/**
 * @returns {scrDocument}
 */
function	mainDocument() {
	dBegin( 1, "mainDocument.js", "scrDocument", "main()") ;
	wapScreen.call( this, "Document") ;
	this.package	=	"ModDocMgmt" ;
	this.module	=	"Document" ;
	this.coreObject	=	"Document" ;
	this.keyForm	=	"DocumentKeyData" ;
	this.keyField	=	getFormField( 'DocumentKeyData', 'DocumentNo') ;
	this.delConfDialog	=	"/ModDocMgmt/Document/confDocumentDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "Document"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selDocument", {
											pkg:	"ModDocMgmt"
										,	script:	"loadScreen.php?screen=ModDocMgmt/Document/selDocument.xml"
										,	obj:	"Document"
										}) ;
	/**
	 * create the grid to display the Document list on the first "Overview" tab
	 */
	dTrace( 2, "mainDocument.js", "scrDocument", "*", "creating gridDocumentOV") ;
	this.gridDocumentOV	=	new wapGrid( this, "gridDocumentOV", {
										object:			"Document"
									,	screen:			"Document"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainDocument{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainDocument{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainDocument{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageDocumentMainDataEntry") ;
															dEnd( 102, "mainDocument{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainDocument.js", "scrDocument", "*", "calling gridDocumentOV._onFirstPage()") ;
	this.gridDocumentOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for DocumentRevision
	 */
	dTrace( 2, "mainDocument.js", "scrDocument", "*", "creating gridCust-------------omerContact") ;
	this.gridDocumentRevision	=	new wapGrid( this, "gridDocumentRevision", {
										object:			"DocumentRevision"
									,	screen:			"Document"
									,	parentDS:		this.dataSource
									,	editorName:		"edtDocumentRevision"
									,	moduleName:		"ModDocMgmt"
									,	subModuleName:	"Document"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainDocument{wapGrid.js}", "wapGrid{gridDocumentRevision}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainDocument{wapGrid.js}", "wapGrid{gridDocumentRevision}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainDocument{wapGrid.js}", "wapGrid{gridDocumentRevision}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainDocument{wapGrid.js}", "wapGrid{gridDocumentRevision}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainDocument.js", "scrDocument", "fncLink()") ;
		dEnd( 1, "mainDocument.js", "scrDocument", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainDocument.js", "scrDocument", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainDocument.js", "scrDocument", "fncShow( <_response>)") ;
		dEnd( 1, "mainDocument.js", "scrDocument", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainDocument.js", "scrDocument", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainDocument.js", "scrDocument", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			dTrace( 2, "mainCustomer.js", "scrCustomer", "onDataSourceLoaded( ...)", "displaying main object data") ;
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridDocumentRevision._onFirstPage() ;
		}
		dEnd( 1, "mainDocument.js", "scrDocument", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainDocument.js", "scrDocument", "*", "defining this.onMisc") ;
	this.onMisc	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainDocument.js", "scrDocument", "onMisc( <...>)") ;
		this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
		dEnd( 1, "mainDocument.js", "scrDocument", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	this.onUpload	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainDocument.js", "scrDocument", "upload( <...>)") ;
		this.dataSource.onUpload( true, _fnc, _form, "") ;
		dEnd( 1, "mainDocument.js", "scrDocument", "upload( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainDocument.js", "scrDocument", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainDocument.js", "scrDocument", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageDocumentSurveyEntry") ;
	dEnd( 1, "mainDocument.js", "scrDocument", "main()") ;
}
