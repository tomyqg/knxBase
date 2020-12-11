/**
 * 
 */
function	regModStock() {
	_debugL( 0x00000001, "regModStock: \n") ;
	myScreen	=	screenAdd( "screenStock", linkStock, "Stock", "StockKeyData", "_IStockId", showStockAll, null) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"Stock" ;
	myScreen.coreObject	=	"Stock" ;
	myScreen.showFunc	=	showStockAll ;
	myScreen.keyField	=	getFormField( 'StockKeyData', '_IStockId') ;
	myScreen.delConfDialog	=	"/Base/Stock/confStockDel.php" ;
	myScreen.link() ;
	if ( pendingKey != "") {
		requestUni( 'Base', 'Stock', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showStockAll) ;
	}
	pendingKey	=	"" ;
}
function	linkStock() {
	_debugL( 0x00000001, "linkStock: \n") ;
}
/**
 *
 */
function	showStockAll( response) {
	showStock( response) ;
	showTableStockLocation( response) ;
}

/**
 *
 */
function	showStock( response) {
	var	lockInfo ;
	var	lief ;
	var	attrs ;
	var	myStockId ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	stock	=	response.getElementsByTagName( "Stock")[0] ;
	if ( stock) {

		myStockId	=	response.getElementsByTagName( "StockId")[0].childNodes[0].nodeValue ;

		attrs	=	stock.childNodes ;
		dispAttrs( attrs, "StockKeyData") ;
		dispAttrs( attrs, "formStockMain") ;
		dispAttrs( attrs, "formStockLocation") ;

//		pdfDocument	=	document.getElementById( "pdfDocument") ;
//		pdfDocument.innerHTML	=	"<a href=\"/Archiv/Stock/" + myStockId + ".pdf\" target=\"pdf\"><img src=\"/rsrc/gif/pdficon_large.gif\" title=\"PDF Dokument in NEUEM Fenster anzeigen\" /></a>" ;
	}
}
/**
*
*/
function	showTableStockLocation( response) {
	updTableHead( response, "formStockLocationTop", "formStockLocationBot") ;
	showTable( response, "TableStockLocation", "StockLocation", "Stock", document.forms['StockKeyData']._IStockId.value, "showStockAll", "refreshTableStockLocation") ;
}
function	refreshTableStockLocation( response) {
	refreshTable( response, "TableStockLocation", "StockLocation", "Stock", document.forms['StockKeyData']._IStockId.value, "showStockAll") ;
}
//			myLine	+=	prnLblButton( 'Base', 'StockLabelDoc', '/Common/hdlObject.php', 'createPDF', '', stockLocation.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, null) ;
//			myLine	+=	prnLblButton( 'Base', 'StockLabelDoc', '/Common/hdlObject.php', 'createPDFList', '', stockLocation.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, null) ;
//			myLine	+=	prnLblButton( 'Base', 'Stock', '/Common/hdlObjectXML.php', 'report', 'halfstatic', -1, stockLocation.getElementsByTagName( "StockId")[0].childNodes[0].nodeValue, 'formStockReport', null, null) ;
/**
*
*/
function	refreshTableStockLocation( response) {
	
}
/**
 *
 */
function	showTableStockOV( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divStockLocation	=	document.getElementById( "TableStockOV") ;
	divStockLocation.innerHTML	=	"Hello" ;

	myData	=	"" ;
	tableStockLocation	=	response.getstockLocationByTagName( "Stock") ;
	if ( tableStockLocation) {
		myData	+=	"TableResultSet contains " + tableStockLocation.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Stock Nr.</th>" ;
		myData	+=	"<th>Description</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableStockLocation.length ; i++) {
			stock	=	response.getElementsByTagName( "Stock")[i] ;
			myId	=	stock.getElementsByTagName( "Id")[0].childNodes[0].nodeValue ;
			myStockId	=	stock.getElementsByTagName( "StockId")[0].childNodes[0].nodeValue ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>"
							+ stock.getElementsByTagName( "Id")[0].childNodes[0].nodeValue
							+ "</td>" ;
			myLine	+=	"<td>"
							+ stock.getElementsByTagName( "StockId")[0].childNodes[0].nodeValue
							+ "</td>" ;
//			myLine	+=	"<td>" + combine( myBez1, myBez2, myMT) ;
			myLine	+=	"<td>"
							+ stock.getElementsByTagName( "Description")[0].childNodes[0].nodeValue
							+ "</td>" ;
			myLine	+=	editButton( 'Base', 'Stock', '/Base/Stock/editorStockLocation.php', 'getStockLocationAsXML', document.forms['StockKeyData']._IStockId.value, stock.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, 'showTableStockLocation', 'StockLocation') ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divStockLocation.innerHTML	=	myData ;
}
function	showStockDocList( response) {
	showDocList( response, "tableStockDocs", "", stockUpdCb) ;
}
function	stockUpdCb( _id) {
	myLine	=	"<td>" + btnProcess( 'Base', 'Stock', '/Common/hdlObject.php', 'updStocks', '', _id, '', null, null) + "</td>" ;
	return myLine ;
}
function	refStockLocation( _rng) {
	requestUni( 'ModBase', 'Stock', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['StockKeyData']._IStockId.value,
			_rng,
			'StockLocation',
			'formStockLocationTop',
			showTableStockLocation) ;
	return false ; 	
}

