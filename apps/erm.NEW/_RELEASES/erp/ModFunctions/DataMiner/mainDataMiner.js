/**
 * regModDataMiner
 * 
 * registers the module in the central database
 */
function	regModDataMiner() {
	_debugL( 0x00000001, "regModDataMiner: \n") ;
	myScreen	=	screenAdd( "screenDataMiner", linkDataMiner, "DataMiner", "DataMinerKeyData", "_IDataMinerNr", null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"DataMiner" ;
	myScreen.coreObject	=	"DataMiner" ;
	myScreen.showFunc	=	null ;
	myScreen.keyField	=	null ;
	// make sure everything is setup fo this module, e.g. menuitems enables/disables etc.
	myScreen.link() ;
	// process any pending 'link-to-screen# data
//	if ( pendingKey != "") {
//		requestUni( 'Base', 'DataMiner', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showDataMinerAll) ;
//	}
	pendingKey	=	"" ;
}
function	linkDataMiner() {
	_debugL( 0x00000001, "linkDataMiner: \n") ;
}
/**
 *
 */
function	cbArticleWOP( _lc, _row) {
	var	myRes	=	"" ;
	if ( _lc == -1) {
		startRow	=	_row.getElementsByTagName( "StartRow")[0] ;
		myField	=	getFormField( "formArtikelWOPOV", "_SStartRow") ;
		if ( startRow && myField) {
			myField.value	=	startRow.childNodes[0].nodeValue ;
		}
	} else if ( _lc == 0) {
	} else if ( _row.nodeType == 1) {
	}
	return myRes ;
}
function	refOpenSuOrdrSurvey( _rng) {
	requestDataMinerNew( 'Base', 'DataMinerLfBest', '/Common/hdlObject.php', 'getTableLfBestOpen',
			_rng,	'divDMLfBestOpen', 'LfBestNr', 'screenLfBest', 'retToDataMiner', '', cbRefrOpenSuOrdrSurvey, 'formOpenSuOrdrTop', null) ;
}
function	cbRefrOpenSuOrdrSurvey( _lc, _response) {
	if ( _lc == -1) {
		updTableHead( _response, "formOpenSuOrdrTop", "formOpenSuOrdrBot") ;
	}
	return "" ;
}
