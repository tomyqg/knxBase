/**
 * mainArticle
 *
 * registers the module in the central database
 */
/**
 * @returns {mainArticle}
 */
new mainArticle() ;
/**
 *
 */
function	mainArticle() {
	dBegin( 1, "mainArticle.js::mainArticle(...): begin\n") ;
	wapScreen.call( this, "Article") ;
	this.package	=	"ModBase" ;
	this.module	=	"Article" ;
	this.coreObject	=	"Article" ;
	this.keyForm	=	"ArticleKeyData" ;
	this.keyField	=	getFormField( 'ArticleKeyData', 'ArticleNo') ;
	this.delConfDialog	=	"/ModBase/Article/confArticleDel.php" ;
	this.dataSource	=	new wapDataSource( this, {
												object: "Article"
											,	objectKey:	"ArticleNo"
											}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "gridArticleSelectOV", {
										objectClass:	"Article"
									,	moduleName:		this.package
									,	subModuleName:	"Article"
									,	selectorName:	"selArticle"
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageArticleMainDataEntry") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "creating gridArticleOV") ;
	this.gridArticleOV	=	new wapGrid( this, "gridArticleOV", {
										object:			"Article"
									,	module:			"ModBase"
									,	screen:			"Article"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:	function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															showTab( "tabPageArticleMainDataEntry") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "calling dsArticleOV._onFirstPage()") ;
	this.gridArticleOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "creating gridArticlePurchasePriceRel") ;
	this.gridArticlePurchasePriceRel	=	new wapGrid( this, "gridArticlePurchasePriceRel", {
										object:			"ArticlePurchasePriceRel"
									,	module :		"ModBase"
									,	screen:			"Article"
									,	parentDS:		this.dataSource
									,	editorName:		"edtArticlePurchasePriceRel"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Article"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelectById:		function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "creating gridArticlePurchasePrice") ;
	this.gridArticlePurchasePrice	=	new wapGrid( this, "gridArticlePurchasePrice", {
										object:			"ArticlePurchasePrice"
									,	module :		"ModBase"
									,	screen:			"Article"
									,	parentDS:		this.dataSource
									,	editorName:		"edtArticlePurchasePrice"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Article"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePrice}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePrice}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePrice}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePrice}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "creating gridArticleSalesPrice") ;
	this.gridArticleSalesPrice	=	new wapGrid( this, "gridArticleSalesPrice", {
										object:			"ArticleSalesPrice"
									,	module :		"ModBase"
									,	screen:			"Article"
									,	parentDS:		this.dataSource
									,	editorName:		"edtArticleSalesPrice"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Article"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "creating gridArticleSalesPriceCache") ;
	this.gridArticleSalesPriceCache	=	new wapGrid( this, "gridArticleSalesPriceCache", {
										object:			"ArticleSalesPriceCache"
									,	module :		"ModBase"
									,	screen:			"Article"
									,	parentDS:		this.dataSource
									,	editorName:		"edtArticleSalesPriceCache"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleSalesPriceCache}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleSalesPriceCache}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleSalesPriceCache}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleSalesPriceCache}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and display grid for CustomerContact
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "creating gridArticleText") ;
	this.gridArticleText	=	new wapGrid( this, "gridArticleText", {
										object:			"ArticleText"
									,	module :		"ModBase"
									,	screen:			"Article"
									,	parentDS:		this.dataSource
									,	editorName:		"edtArticleText"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Article"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleText}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleText}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticlePurchasePriceRel}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "creating gridArticleText") ;
	this.gridAttribute	=	new wapGrid( this, "gridAttribute", {
										object:			"Attribute"
									,	module :		"ModBase"
									,	screen:			"Article"
									,	parentDS:		this.dataSource
									,	editorName:		"edtAttribute"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Article"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridAttribute}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridAttributel}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridAttribute}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridAttribute}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "creating gridArticleComponent") ;
	this.gridArticleComponent	=	new wapGrid( this, "gridArticleComponent", {
										object:			"ArticleComponent"
									,	module :		"ModBase"
									,	screen:			"Article"
									,	parentDS:		this.dataSource
									,	editorName:		"edtArticleComponent"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleComponent}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleComponent}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleComponent}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleComponent}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 * creating the datastore and siplay grid for CustomerContact
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "creating gridArticleStock") ;
	this.gridArticleStock	=	new wapGrid( this, "gridArticleStock", {
										object:			"ArticleStock"
									,	module :		"ModBase"
									,	screen:			"Article"
									,	parentDS:		this.dataSource
									,	editorName:		"edtArticleStock"
									,	moduleName: 	"ModBase"
									,	subModuleName:	"Article"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleStock}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleStock}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleStock}", "onSelect( <_parent>, '"+_id+"')") ;
															dEnd( 102, "mainArticle{wapGrid.js}", "wapGrid{gridArticleStock}", "onSelect( <_parent>, '"+_id+"')") ;
														}
								}) ;
	/**
	 *  link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainArticle.js", "mainArticle", "fncLink()") ;
		dBegin( 1, "mainArticle.js", "mainArticle", "fncLink()") ;
	} ;
	/**
	 *
	 * @param {boolean} _wd
	 * @param {string} _fnc
	 * @param {string} _form
	 * @returns {void}
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "defining this.Misc") ;
	this.onMisc	=	function( _wd, _fnc, _form, _val) {
		dBegin( 1, "mainArticle.js", "mainArticle", "onMisc( <...>)") ;
		if ( ! _val) {
			_val	=	"" ;
		}
		this.dataSource.onMisc( this.keyField.value, -1, _val, _form, _fnc) ;
		dEnd( 1, "mainArticle.js", "mainArticle", "onMisc( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainArticle.js", "mainArticle", "fncShow( <_response>)") ;
		dEnd( 1, "mainArticle.js", "mainArticle", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "mainArticle.js", "mainArticle", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true ) ;
			this.dataSource.key	=	this.keyField.value ;
			this.gridArticlePurchasePriceRel._onFirstPage() ;
			this.gridArticlePurchasePrice._onFirstPage() ;
//			this.gridArticlePurchasePrice.clear() ;
			this.gridArticleSalesPrice._onFirstPage() ;
			this.gridArticleSalesPriceCache._onFirstPage() ;
			this.gridArticleText._onFirstPage() ;
			this.gridAttribute._onFirstPage() ;
			this.gridArticleComponent._onFirstPage() ;
			this.gridArticleStock._onFirstPage() ;
			/**
			 *
			 */
//			dTrace( 2, "mainArticle.js", "mainArticle", "fncShow( ...)", "assigning main object key to tables viewers") ;
//			_scr.dtvCustOrders.primObjKey	=	_scr.keyField.value ;
//			_scr.dtvCustComm.primObjKey	=	_scr.keyField.value ;
//			_scr.dtvCustDlvr.primObjKey	=	_scr.keyField.value ;
//			_scr.dtvCustInvoices.primObjKey	=	_scr.keyField.value ;
			/**
			 *
			 */
//			dTrace( 2, "mainArticle.js", "mainArticle", "fncShow( ...)", "displaying contact table view") ;
//			_scr.dtvArticleContact.show( _response) ;
//			this.dtvLiefArticle.primObjKey	=	_scr.keyField.value ;
//			this.dtvLiefArticle.show( _response) ;
//			this.dtvRechArticle.primObjKey	=	_scr.keyField.value ;
//			this.dtvRechArticle.show( _response) ;
//			this.dtvAddArticle.primObjKey	=	_scr.keyField.value ;
//			this.dtvAddArticle.show( _response) ;
		}
		dEnd( 1, "mainArticle.js", "mainArticle", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "defining this.onDataSourceLoaded") ;
	this.receivedReferences	=	function( _scr, _xmlData) {
		dBegin( 1, "mainArticle.js", "mainArticle", "onDataSourceLoaded( <_response>)") ;
		if ( _xmlData) {
			myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					dTrace( 1, "mainArticle.js", "mainArticle", "onDataSourceLoaded", "Reference: " + _xmlData.getElementsByTagName( "Reference")[i].childNodes[0].nodeValue) ;
					refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=Article"
										+	"&_fnc=" + "getPDF"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" ;
					window.open( refUrl) ;
				}
			}
		}
		dEnd( 1, "mainArticle.js", "mainArticle", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	this.onUpload	=	function( _wd, _fnc, _form, _addPOST) {
		this.dataSource.onUpload( _wd, _fnc, _form, _addPOST) ;
	}
	/**
	 *
	 */
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainArticle.js", "mainArticle", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	showTab( "tabPageArticleSurveyEntry") ;
	dEnd( 1, "mainArticle.js", "mainArticle", "main()") ;
}
