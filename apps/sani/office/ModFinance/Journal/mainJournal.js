/**
 * regModJournal
 * 
 * registers the module in the central database
 */
function	scrJournal() {
	_debugL( 0x00000001, "mainJournal.js::scrJournal::scrJournal(): begin") ;
	Screen.call( this, "Journal") ;				// instantiate the super-class!!!
	this.package	=	"ModSys" ;			// directory of the module
	this.module	=	"Journal" ;					// sub-directory of the screen
	this.coreObject	=	"Journal" ;
	this.keyForm	=	"JournalKeyData" ;		// form
	this.keyField	=	getFormField( 'JournalKeyData', '_IJournalNo') ;
	this.fncNew	=	null ;
	this.fncDelete	=	null ;
	this.delConfDialog	=	"/ModFinance/Journal/confJournalDel.php" ;
	/**
	 * create the selector
	 */
	this.select	=	new selector( this, "selJournal", 'ModFinance', '/ModFinance/Journal/selJournal.php', 'Journal') ;
	/**
	 * create the dataTableViews
	 */
	this.dmJournalOv	=	new dataTableView( this, "dmJournalOv", "TableJournalOv", "DataMinerJournal", "Journal", null, null, null) ;
	this.dmJournalOv.f1	=	"formJournalOvTop" ;
	/**
	 * getting JS for tab 'ArticleStocks' 
	 */
	this.dtvJournalLineItems = new dataTableView( this, "dtvJournalLineItems", "TableJournalLineItems", "Journal", "JournalLineItem", null, "ModFinance", "Journal") ; 
	this.dtvJournalLineItems.f1 = "formJournalLineItemsTop" ;
	/**
	 * getting JS for tab 'AccountSubAccounts' 
	 */

	_debugL( 0x00000001, "Defining loadTmpl method()\n") ;
	itemEditors['edtJournalLineItem'].loadTmpl	=	function() {
		_debugL( 1, "mainJournal.js::scrJournal::loadTmpl(): begin\n") ;
		myField	=	getFormField( "editorObject", "_IJournalTmplNo") ;
		_debugL( 1, "Selected Template No. " + myField.value + "\n") ;
		myTmpScreen	=	new Screen( this, "tempJournalAddEntry") ;
		myTmpScreen.coreObject	=	"JournalTmpl" ;
		myTmpScreen.keyField	=	getFormField( 'editorObject', '_IJournalTmplNo') ;
		myTmpScreen.fncShow	=	function( _response) {
			_debugL( 1, "mainJournal.js::scrJournal::loadTmpl()::fncShow(): begin\n") ;
			template	=	_response.getElementsByTagName( "JournalTmpl")[0] ;
			attrs	=	template.childNodes ;
			dispAttrs( attrs, "editorObject") ;
			this.dtvJournalLines	=	new dataTableView( this, "dtvJournalLines", "TableJournalLines", "JournalTmpl", "JournalTmplItem", null, "ModFinance", "JournalTmpl") ; 
			this.dtvJournalLines.show( _response) ;
			_debugL( 1, "mainJournal.js::scrJournal::loadTmpl()::fncShow(): end\n") ;
		} ;
		_debugL( 1, "mainJournal.js::scrJournal::loadTmpl(): end ... calling qDispatch() ...\n") ;
		myTmpScreen.qDispatch( true, "getXMLComplete") ;
	} ;
	_debugL( 0x00000001, "Defining enterAmount method()\n") ;
	itemEditors['edtJournalLineItem'].enterAmount	=	function() {
		_debugL( 1, ">>>Hello, world\n") ;
		pTotal	=	parseFloat( getFormField( "editorObject", "_IAmountTotal").value.replace( ",", ".")) ;
		_debugL( 1, "pTotal = " + pTotal.toString() + "\n") ;
		aTotal	=	parseFloat( getFormField( "editorObject", "_FAmount").value.replace( ",", ".")) ;
		_debugL( 1, "aTotal = " + aTotal.toString() + "\n") ;
		for ( var i=0 ; i<5 ; i++) {
			account	=	getFormField( "editorObject", "_IAccountDebit["+i.toString()+"]") ;
			amountP	=	getFormField( "editorObject", "_FCAD["+i.toString()+"]") ;
			amount	=	getFormField( "editorObject", "_FAmountDebit["+i.toString()+"]") ;
			if ( account.value != "" && amount) {
				fAmount	=	aTotal * parseFloat( amountP.value) / pTotal ;
				amount.value	=	(Math.round( fAmount * 100) / 100).toString() ;
			} else {
				amount.value	=	"" ;
			}
			account	=	getFormField( "editorObject", "_IAccountCredit["+i.toString()+"]") ;
			amountP	=	getFormField( "editorObject", "_FCAC["+i.toString()+"]") ;
			amount	=	getFormField( "editorObject", "_FAmountCredit["+i.toString()+"]") ;
			if ( account.value != "" && amount) {
				fAmount	=	aTotal * parseFloat( amountP.value) / pTotal ;
				amount.value	=	(Math.round( fAmount * 100) / 100).toString() ;
			} else {
				amount.value	=	"" ;
			}
		}
		_debugL( 1, ">>>Hello, world\n") ;
		return false ;
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "Defining fncLink method()\n") ;
	this.fncLink	=	function() {
	} ;
	/**
	 * 
	 */
	this.fncNew	=	function() {
		this.dispatch( true, 'add', '', '', '', 'formJournalMain') ;
	} ;
	/**
	 * 
	 */
	this.onSelect	=	function( _key) {
		_debugL( 0x00000001, "mainJournal.js::loadJournalById(" + _key.toString() + "): begin\n") ;
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
		_debugL( 0x00000001, "mainJournal.js::loadJournalById(): end\n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( _response) {
		_debugL( 0x00000001, "mainJournal.js::scrJournal::fncShow( <_response>): begin") ;
		var	attrs ;
		/**
		 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
		 */
		journal	=	_response.getElementsByTagName( "Journal")[0] ;
		if ( journal) {
			attrs	=	journal.childNodes ;
			dispAttrs( attrs, "JournalKeyData") ;
			dispAttrs( attrs, "formJournalMain") ;
			this.dtvJournalLineItems.primObjKey	=	this.keyField.value ;
			this.dtvJournalLineItems.show( _response) ;
		}
		_debugL( 0x00000001, "mainJournal.js::scrJournal::fncShow( <_response>): end") ;
	} ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
	hookPrevObject() ;
	_debugL( 0x00000001, "mainJournal.js::scrJournal::scrJournal(): end") ;
}
