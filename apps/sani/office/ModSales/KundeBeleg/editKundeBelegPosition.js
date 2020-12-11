/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "KundeBelegPosition", "tabPageKundeBelegPositionKTEntry") ;
	screen.package	=	"ModSales" ;
	screen.module	=	"KundeBeleg" ;
	screen.coreObject	=	"KundeBelegPosition" ;

screenEditKundeBelegPosition	=	screen ;
//$("div[data-wap-screen='KundeBelegPosition']").css( "display", "none") ;

// $( "#KundeBelegPosition").wapDialog( {
// 	title:	"Belegsposition bearbeiten",
// 	width:	"900px",
// 	parentDS:	screen.dataSource
// }) ;
