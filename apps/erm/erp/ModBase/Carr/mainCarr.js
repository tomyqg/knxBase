/**
 * regModCarr
 * 
 * registers the module in the central database
 */
function	regModCarr() {
	_debugL( 0x00000001, "mainCarr.js::regModCarr(): begin\n") ;
	myScreen	=	screenAdd( "screenCarr", linkCarr, "Carr", "CarrKeyData", "_ICarrier", showCarrAll, null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"Carr" ;
	myScreen.coreObject	=	"Carr" ;
	myScreen.showFunc	=	showCarrAll ;
	myScreen.keyField	=	getFormField( 'CarrKeyData', '_ICarrier') ;
	myScreen.delConfDialog	=	"/Base/Carr/confCarrDel.php" ;
	// link to this screen
	myScreen.link() ;
	// process any pending 'link-to-screen# data
	if ( pendingKey != "") {
		requestUni( 'Base', 'Carr', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showCarrAll) ;
	}
	pendingKey	=	"" ;
	_debugL( 0x00000001, "mainCarr.js::regModCarr(): end\n") ;
}
function	linkCarr() {
	_debugL( 0x00000001, "mainCarr.js::linkCarr(): begin\n") ;
	_debugL( 0x00000001, "mainCarr.js::linkCarr(): end\n") ;
}
/**
 *
 */
function	showCarrAll( response) {
	showCarr( response) ;
	showTableCarrOpt( response) ;
}
/**
*
*/
function	showTableCarrOpt( response) {
	updTableHead( response, "formCarrOptTop", "formCarrOptBot") ;
	showTable( response, "TableCarrOpt", "CarrOpt", "Carr", document.forms['CarrKeyData']._ICarrier.value, "showCarrAll", "refreshTableCarrOpt") ;
}
function	refreshTableCarrOpt( response) {
	refreshTable( response, "TableCarrOpt", "CarrOpt", "Carr", document.forms['CarrKeyData']._ICarrier.value, "showCarrAll") ;
}
/**
 *
 */
function	showCarr( response) {
	var	debugInfo ;
	var	carr ;
	var	attrs ;
	_debugL( 0x00000001, "mainCarr.js::showCarr( <response>): begin\n") ;
	/**
	 * get the <Carr> package
	 */
	carr	=	response.getElementsByTagName( "Carr")[0] ;
	if ( carr) {
		attrs	=	carr.childNodes ;
		dispAttrs( attrs, "CarrKeyData") ;
		dispAttrs( attrs, "formCarrMain") ;
	}
	_debugL( 0x00000001, "mainCarr.js::showCarr( <response>): end\n") ;
}
function	refCarrOpt( _rng) {
	requestUni( 'ModBase', 'Carr', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['CarrKeyData']._ICarrier.value,
			_rng,
			'CarrOpt',
			'formCarrOptTop',
			showTableCarrOpt) ;
	return false ; 	
}
