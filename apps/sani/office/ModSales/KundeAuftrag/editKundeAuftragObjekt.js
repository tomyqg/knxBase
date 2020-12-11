/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "KundeAuftragObjekt", "tabPageKundeAuftragObjektKTEntry") ;
	screen.package	=	"ModSales" ;
	screen.module	=	"KundeAuftrag" ;
	screen.coreObject	=	"KundeAuftragObjekt" ;

screenEditKundeAuftragObjekt	=	screen ;
//$("div[data-wap-screen='KundeAuftragObjekt']").css( "display", "none") ;

// $( "#KundeAuftragObjekt").wapDialog( {
// 	title:	"Auftragsposition bearbeiten",
// 	width:	"900px",
// 	parentDS:	screen.dataSource
// }) ;
