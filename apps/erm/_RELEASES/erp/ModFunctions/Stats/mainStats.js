/**
 * regModStats
 * 
 * registers the module in the central database
 */
function	scrStats() {
	_debugL( 0x00000001, "mainStats.js::scrStats::scrStats(): begin\n") ;
	Screen.call( this, "Stats") ;
	this.package	=	"ModFunctions" ;
	this.module	=	"Stats" ;
	this.coreObject	=	"Stats" ;
	this.keyForm	=	null ;
	this.keyField	=	null ;
	this.dtvOrdersBookedY	=	new dataTableView( this, 'dtvOrdersBookedY', "TableOrdersBookedY", "StatsCuOrdr",
										"OrdersBookedY", null, 'ModStats', 'OrdersBookedY') ; 
	this.dtvOrdersBookedQ	=	new dataTableView( this, 'dtvOrdersBookedQ', "TableOrdersBookedQ", "StatsCuOrdr",
										"OrdersBookedQ", null, 'ModStats', 'OrdersBookedQ') ; 
	this.dtvOrdersBookedM	=	new dataTableView( this, 'dtvOrdersBookedM', "TableOrdersBookedM", "StatsCuOrdr",
										"OrdersBookedM", null, 'ModStats', 'OrdersBookedM') ; 
	this.dtvArticlesSoldM	=	new dataTableView( this, 'dtvArticlesSoldM', "TableArticlesSoldM", "StatsCuOrdr",
										"ArticlesSoldM", null, 'ModStats', 'ArticlesSoldM') ; 
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainStats.js::scrStats::scrStats(): defining function fncLnk\n") ;
	this.fncLink	=	function() {
		_debugL( 0x00000001, "mainStats.js::scrStats::fncLink(): begin \n") ;
		_debugL( 0x00000001, "mainStats.js::scrStats::fncLink(): end \n") ;
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainStats.js::scrStats::scrStats(): defining function fncShow\n") ;
	this.fncShow	=	function( _response) {
		_debugL( 0x00000001, "mainStats.js::scrStats::fncShow(): begin \n") ;
		graphId	=	_response.getElementsByTagName( "GraphId")[0].childNodes[0].nodeValue ;
		graphName	=	_response.getElementsByTagName( "GraphName")[0].childNodes[0].nodeValue ;
		var	myImg	=	document.createElement( "img") ;
		myImg.setAttribute( "src", graphName+"?rnd="+Math.floor((Math.random()*100)+1).toString()) ;
		graphDiv	=	document.getElementById( graphId) ;
		while ( graphDiv.firstChild)
			graphDiv.removeChild( graphDiv.firstChild) ;
		graphDiv.appendChild( myImg) ;
//			+ _response.getElementsByTagName( "BildRef")[0].childNodes[0].nodeValue
//			+ "\" />" ;
		_debugL( 0x00000001, "mainStats.js::scrStats::fncShow(): end \n") ;
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainStats.js::scrStats::scrStats(): defining function getOrdersBooked\n") ;
	this.getOrdersBookedY	=	function() {
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): begin\n") ;
		this.dtvOrdersBookedY.getStats() ;
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): end\n") ;
	} ;
	this.getOrdersBookedQ	=	function() {
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): begin\n") ;
		this.dtvOrdersBookedQ.getStats() ;
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): end\n") ;
	} ;
	this.getOrdersBookedM	=	function() {
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): begin\n") ;
		this.dtvOrdersBookedM.getStats() ;
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): end\n") ;
	} ;
	this.getArticlesSoldM	=	function() {
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): begin\n") ;
		this.dtvArticlesSoldM.getStats() ;
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): end\n") ;
	} ;
	this.getOrdersBookedQGraph	=	function() {
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): begin\n") ;
		this.sDispatchCO( true, "getOrdersBookedQGraph", null, "StatsCuOrdr") ;
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): end\n") ;
	} ;
	this.getOrdersBookedMGraph	=	function() {
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): begin\n") ;
		this.sDispatchCO( true, "getOrdersBookedMGraph", null, "StatsCuOrdr") ;
		_debugL( 0x00000001, "mainStats.js::getOrdersBooked(): end\n") ;
	} ;
	_debugL( 0x00000001, "mainStats.js::scrStats::scrStats(): end\n") ;
}