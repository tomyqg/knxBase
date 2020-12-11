var	lastArticleNo	=	"*" ;
function	dataEntered() {
	_debugL( 0x00000001, "mainStockScan.js::dataEntered\n") ;
	data	=	getFormField( "StockScanData", "_IData") ;
	stockId	=	getFormField( "StockScanData", "_IStockId") ;
	articleNo	=	getFormField( "StockScanData", "_IArticleNo") ;
	if ( data.value.substr(0,3).toUpperCase() == "WMS") {
		stockId.value	=	data.value.toUpperCase() ;
	} else {
		articleNo.value	=	data.value ;
		if ( lastArticleNo != articleNo.value && stockId.value != "") {
			requestUniF( 'Base', 'Artikel', '/Common/hdlObject.php', 'setStockLocationForArticle', articleNo.value, '', stockId.value, null, refocus) ;
			lastArticleNo	=	articleNo.value ;
			_debugL( 0x00000001, "mainStockScan.js::dataEntered: requesting update of article no. " + articleNo.value + " to stock id " + stockId.value + "\n") ;
		}
	}
	data.focus() ;
	data.select() ;
}

function	forceStockScanUpdate() {
	_debugL( 0x00000001, "mainStockScan.js::forceStockScanUpdate\n") ;
}

function	refocus() {
	data.focus() ;
	data.select() ;
}
