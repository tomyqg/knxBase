/**
 * scrUser
 * 
 * registers the module in the central database
 */
/**
 * @returns {scrUser}
 */
function	scrUser() {
	dBegin( 1, "mainUser.js", "scrUser", "main()") ;
	wapScreen.call( this, "User") ;
	this.package	=	"ModBase" ;
	this.module	=	"User" ;
	this.coreObject	=	"User" ;
	this.keyForm	=	"UserKeyData" ;
	this.keyField	=	getFormField( 'UserKeyData', 'UserNo') ;
	this.delConfDialog	=	"/ModBase/User/confUserDel.php" ;
	this.dataSource	=	new wapDataSource( this, { object: "User"}) ;		// dataSource for display
	/**
	 * create the selector
	 */
	this.select	=	new wapSelector( this, "selUser", {
											pkg:	"ModBase"
										,	script:	"loadScreen.php?screen=ModBase/User/selUser.xml"
										,	obj:	"User"
										}) ;
	/**
	 * create the grid to display the customer list on the first "Overview" tab 
	 */
//	dTrace( 2, "mainUser.js", "scrUser", "*", "creating refList") ;
//	this.dsUserOV	=	new wapDataSource( this, {
//										object: "User"
//									,	fncGet: "getList"
//								}) ;
	dTrace( 2, "mainUser.js", "scrUser", "*", "creating gridUserOV") ;
	this.gridUserOV	=	new wapGrid( this, "gridUserOV", {
										xmlTableName:	"TableUserOV"
									,	object:			"User"
									,	module:			"ModBase"
									,	screen:			"User"
									,	formTop:		"formUserOVTop"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainUser{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainUser{wapGrid.js}", "wapGrid", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															_parent.dataSource.load( "", _id, "") ;
															_parent.gotoTab( "tcUserMain", "tcUserMain_cpGeneral") ;
															dEnd( 102, "mainUser{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
														}
								}) ;
//	dTrace( 2, "mainUser.js", "scrUser", "*", "assigning dsUserOV.parent") ;
//	this.dsUserOV.parent	=	this.gridUserOV ;
	dTrace( 2, "mainUser.js", "scrUser", "*", "calling dsUserOV._onFirstPage()") ;
	this.gridUserOV._onFirstPage() ;							// refresh the dataSource
	/**
	 * creating the datastore and siplay grid for UserContact 
	 */
	dTrace( 2, "mainUser.js", "scrUser", "*", "creating gridUserContact") ;
	this.gridUserContact	=	new wapGrid( this, "gridUserContact", {
										xmlTableName:	"TableUserContact"
									,	object:			"UserContact"
									,	module :		"ModBase"
									,	screen:			"User"
									,	parentDS:		this.dataSource
									,	formTop:		"formUserContactTop"
									,	onDataSourceLoaded:	function( _parent, _data) {
															dBegin( 102, "mainUser{wapGrid.js}", "wapGrid{gridUserContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															this.show() ;
															dEnd( 102, "mainUser{wapGrid.js}", "wapGrid{gridUserContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
									,	onSelect:		function( _parent, _id) {
															dBegin( 102, "mainUser{wapGrid.js}", "wapGrid{gridUserContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
															dEnd( 102, "mainUser{wapGrid.js}", "wapGrid{gridUserContact}", "onDataSourceLoaded( <_parent>, <_data>)") ;
														}
								}) ;
	/**
	 * getting JS for tab 'CustAddresses' 
	 */
	/**
	 * getting JS for tab 'CustAddressesDeliver' 
	 */
//	this.dtvLiefUser	=	new dataTableView( this, "dtvLiefUser", "TableLiefUser", "User", "LiefUser", null, "ModBase", "User") ; 
//	this.dtvLiefUser.f1	=	"formLiefUserTop" ; 
	/**
	 * getting JS for tab 'CustAddressesInvoice' 
	 */
//	this.dtvRechUser	=	new dataTableView( this, "dtvRechUser", "TableRechUser", "User", "RechUser", null, "ModBase", "User") ; 
//	this.dtvRechUser.f1	=	"formRechUserTop" ; 
	/**
	 * getting JS for tab 'CustAddressesOther' 
	 */
//	this.dtvAddUser	=	new dataTableView( this, "dtvAddUser", "TableAddUser", "User", "AddUser", null, "ModBase", "User") ; 
//	this.dtvAddUser.f1	=	"formAddUserTop" ; 
	/**
	 * getting JS for tab 'CustDataMining' 
	 */
//	this.dtvCustOrders	=	new dataTableView( this, 'dtvCustOrders', "TableCustOrders", "DataMinerUser",
//			"CuOrdr", null, 'ModStats', 'dtvCustOrders') ; 
//	this.dtvCustComm	=	new dataTableView( this, 'dtvCustComm', "TableCustComm", "DataMinerUser",
//			"CuComm", null, 'ModStats', 'dtvCustComm') ; 
//	this.dtvCustDlvr	=	new dataTableView( this, 'dtvCustDlvr', "TableCustDlvr", "DataMinerUser",
//			"CuDlvr", null, 'ModStats', 'dtvCustDlvr') ; 
//	this.dtvCustInvoices	=	new dataTableView( this, 'dtvCustInvoices', "TableCustInvoices", "DataMinerUser",
//			"CuInvc", null, 'ModStats', 'dtvCustInvoices') ; 
	/**
	 * getting JS for tab 'CustFunctions' 
	 */
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		dBegin( 1, "mainUser.js", "scrUser", "fncLink()") ;
		dEnd( 1, "mainUser.js", "scrUser", "fncLink()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.onEnter") ;
	this.onEnter	=	function( _key) {
		dBegin( 1, "mainUser.js", "scrUser", "onSelect( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "mainUser.js", "scrUser", "onSelect( '"+_key+"')") ;
	} ;
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.onPrev(ious)") ;
	this.onPrev	=	function() {
		dBegin( 1, "mainUser.js", "scrUser", "onPrev()") ;
		this.dataSource.getPrev( this.keyField.value, -1, "") ;
		dEnd( 1, "mainUser.js", "scrUser", "onPrev()") ;
	} ;
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.onNext") ;
	this.onNext	=	function() {
		dBegin( 1, "mainUser.js", "scrUser", "onNext()") ;
		this.dataSource.getNext( this.keyField.value, -1, "") ;
		dEnd( 1, "mainUser.js", "scrUser", "onNext()") ;
	} ;
	/**
	 * 
	 */
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.onSelect") ;
	this.onSelect	=	function( _key) {
		dBegin( 1, "mainUser.js", "scrUser", "onSelect( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "mainUser.js", "scrUser", "onSelect( '"+_key+"')") ;
	} ;
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.onSelectByKey") ;
	this.onSelectByKey	=	function( _key) {
		dBegin( 1, "mainUser.js", "scrUser", "onSelectByKey( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "mainUser.js", "scrUser", "onSelectByKey( '"+_key+"')") ;
	} ;
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.onSelect") ;
	this.onSelectById	=	function( _id) {
		dBegin( 1, "mainUser.js", "scrUser", "onSelectById( '"+_id+"')") ;
		this.dataSource.load( "", _id, "") ;
		dEnd( 1, "mainUser.js", "scrUser", "onSelectById( '"+_id+"')") ;
	} ;
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.onCreate") ;
	this.onCreate	=	function( _wd, _fnc, _form) {
		this.sDispatch( _wd, _fnc, _form) ;
	} ;
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.onUpdate") ;
	this.onUpdate	=	function( _wd, _fnc, _form) {
		dBegin( 1, "mainUser.js", "scrUser", "onUpdate( <...>)") ;
		this.dataSource.update(this.keyField.value, -1, "", _form) ;
		dEnd( 1, "mainUser.js", "scrUser", "onUpdate( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.fncShow") ;
	this.fncShow	=	function( _response) {
		dBegin( 1, "mainUser.js", "scrUser", "fncShow( <_response>)") ;
		dEnd( 1, "mainUser.js", "scrUser", "fncShow( <_response>)") ;
	} ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "mainUser.js", "scrUser", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _response) {
		dBegin( 1, "mainUser.js", "scrUser", "onDataSourceLoaded( <_response>)") ;
		if ( _response) {
			dTrace( 2, "mainUser.js", "scrUser", ".......:", screenTable["User"].dataSource.objects.User[0].UserName1) ;
			/**
			 * 
			 */
			dTrace( 2, "mainUser.js", "scrUser", "onDataSourceLoaded( ...)", "displaying main object data") ;
			this.displayData( "UserKeyData", "objects.User[0]") ;
			this.displayData( "formUserMain", "objects.User[0]") ;
			this.displayData( "formUserModi", "objects.User[0]") ;
			this.displayData( "formUserZugriff", "objects.User[0]") ;
			/**
			 * 
			 */
			dTrace( 2, "mainUser.js", "scrUser", "onDataSourceLoaded( ...)", "assigning main object key to grids") ;
			this.dataSource.key	=	this.keyField.value ;
			dTrace( 2, "mainUser.js", "scrUser", "onDataSourceLoaded( ...)", "this.keyField.value := '" + this.keyField.value + "'") ;
			this.gridUserContact._onFirstPage() ;
			/**
			 * 
			 */
//			dTrace( 2, "mainUser.js", "scrUser", "fncShow( ...)", "assigning main object key to tables viewers") ;
//			_scr.dtvCustOrders.primObjKey	=	_scr.keyField.value ;
//			_scr.dtvCustComm.primObjKey	=	_scr.keyField.value ;
//			_scr.dtvCustDlvr.primObjKey	=	_scr.keyField.value ;
//			_scr.dtvCustInvoices.primObjKey	=	_scr.keyField.value ;
			/**
			 * 
			 */
//			dTrace( 2, "mainUser.js", "scrUser", "fncShow( ...)", "displaying contact table view") ;
//			_scr.dtvUserContact.show( _response) ;
//			this.dtvLiefUser.primObjKey	=	_scr.keyField.value ;
//			this.dtvLiefUser.show( _response) ;
//			this.dtvRechUser.primObjKey	=	_scr.keyField.value ;
//			this.dtvRechUser.show( _response) ;
//			this.dtvAddUser.primObjKey	=	_scr.keyField.value ;
//			this.dtvAddUser.show( _response) ;
		}
		dEnd( 1, "mainUser.js", "scrUser", "onDataSourceLoaded( <_response>)") ;
	} ;
	dTrace( 2, "mainUser.js", "scrUser", "*", "working on pending key") ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onNext() ;
	}
	dTrace( 2, "mainUser.js", "scrUser", "*", "done with init") ;
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	dEnd( 1, "mainUser.js", "scrUser", "main()") ;
}
