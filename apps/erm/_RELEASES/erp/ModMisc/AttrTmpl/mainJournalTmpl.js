/**
 * regModJournalTmpl
 * 
 * registers the module in the central database
 */
function	scrJournalTmpl() {
	_debugL( 0x00000001, "regModJournalTmpl: begin\n") ;
	Screen.call( this, "JournalTmpl") ;
	this.package	=	"ModFinance" ;
	this.module	=	"JournalTmpl" ;
	this.coreObject	=	"JournalTmpl" ;
	this.keyForm	=	"JournalTmplKeyData" ;
	this.keyField	=	getFormField( 'JournalTmplKeyData', '_IJournalTmplNo') ;
	this.delConfDialog	=	"/ModFinance/JournalTmpl/confJournalTmplDel.php" ;
	/**
	 * create the selector
	 */
	this.selJournalTmpl	=	new selector( this, "selJournalTmpl", 'ModFinance', '/ModFinance/JournalTmpl/selJournalTmpl.php', 'JournalTmpl') ;
	/**
	 * getting JS for tab 'AccountSubAccounts' 
	 */
	this.dtvJournalTmplItems	=	new dataTableView( this, "dtvJournalTmplItems", "TableJournalTmplItems", "JournalTmpl", "JournalTmplItem", null, "ModFinance", "JournalTmpl") ; 
	this.dtvJournalTmplItems.f1	=	"formJournalTmplItemsTop" ; 
	/**
	 * getting JS for tab 'CustAddresses' 
	 */
	/**
	 * link to this screen
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "mainJournalTmpl.js::linkJournalTmpl(): begin\n") ;
		_debugL( 0x00000001, "mainJournalTmpl.js::linkJournalTmpl(): end\n") ;
	} ;
	/**
	 * 
	 */
	this.fncNew	=	function() {
		this.dispatch( true, 'add', '', '', '', 'formJournalTmplMain') ;
	} ;
	/**
	 * 
	 */
	this.onSelect	=	function( _key) {
		_debugL( 0x00000001, "mainJournalTmpl.js::onSelect(" + _key.toString() + "): begin\n") ;
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
		_debugL( 0x00000001, "mainJournalTmpl.js::onSelect(): end\n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( _response) {
		_debugL( 0x00000001, "mainJournal.js::scrJournalTmpl::fncShow( <_response>): begin\n") ;
		if ( _response) {
			journalTmpl	=	_response.getElementsByTagName( "JournalTmpl")[0] ;
			attrs	=	journalTmpl.childNodes ;
			dispAttrs( attrs, "JournalTmplKeyData") ;
			dispAttrs( attrs, "formJournalTmplMain") ;
			this.dtvJournalTmplItems.primObjKey	=	this.keyField.value ;
			this.dtvJournalTmplItems.show( _response) ;
		}
		_debugL( 0x00000001, "mainJournal.js::scrJournalTmpl::fncShow( <_response>): end\n") ;
	} ;
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
	_debugL( 0x00000001, "regModJournalTmpl: end\n") ;
}
function	linkJournalTmpl() {
	_debugL( 0x00000001, "linkJournalTmpl: \n") ;
}
/**
 *
 */
function	showJournalTmplAll( response) {
	showJournalTmpl( response) ;
	showTableJournalTmplPosten( response) ;
}
/**
 *
 */
function	showJournalTmpl( response) {
	var	journals ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	journalTmpl	=	response.getElementsByTagName( "JournalTmpl")[0] ;
	if ( journalTmpl) {
		myJournalTmplNo	=	response.getElementsByTagName( "JournalTmplNo")[0].childNodes[0].nodeValue ;
		journals	=	journalTmpl.childNodes ;
		dispJournals( journals, "JournalTmplKeyData") ;
		dispJournals( journals, "formJournalTmplMain") ;
	}
}

function	showJournalTmplDocInfo( response) {
	var pdfDocument	=	document.getElementById( "pdfJournalTmpl") ;
	var pdfNode	=	response.getElementsByTagName( "Document")[0] ;
	if ( pdfNode) {
		var pdfRef	=	response.getElementsByTagName( "Document")[0].childNodes[0].nodeValue ;
		if ( pdfRef != "") {
			pdfDocument.innerHTML	=	"<a href=\"" + pdfRef + "\" target=\"pdf\"><img src=\"/rsrc/gif/pdficon_large.gif\" /></a>" ;
		} else {
			pdfDocument.innerHTML	=	"" ;
		}
	}
}
/**
*
*/
function	showTableJournalTmplPosten( response) {
	updTableHead( response, "formJournalTmplPostenTop", "formJournalTmplPostenBot") ;
	showTable( response, "TableJournalTmplPosten", "JournalTmplPosten", "JournalTmpl", document.forms['JournalTmplKeyData']._IJournalTmplNo.value, "showJournalTmplAll", "refreshTableJournalTmplPosten") ;
}
function	refreshTableJournalTmplPosten( response) {
	refreshTable( response, "TableJournalTmplPosten", "JournalTmplPosten", "JournalTmpl", document.forms['JournalTmplKeyData']._IJournalTmplNo.value, "showJournalTmplAll") ;
}
function	refJournalTmplPosten( _rng) {
	requestUni( 'ModBase', 'JournalTmpl', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['JournalTmplKeyData']._IJournalTmplNo.value,
			_rng,
			'JournalTmplPosten',
			'formJournalTmplPostenTop',
			showTableJournalTmplPosten) ;
	return false ; 	
}