/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Supplier", "tabPageSupplierSurveyEntry") ;
    screen.package	=	"ModBase" ;
    screen.module	=	"Supplier" ;
    screen.coreObject	=	"Supplier" ;

screenSupplier	=	screen ;

$( "#SupplierKeyData").find( "#SupplierNo").autocomplete( {
    source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Supplier&_fnc=acList&_key=&_id=-1&_val=",
    minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
        screenSupplier.onEnter( ui.item.value) ;
    }
}) ;

$( "#SupplierKeyData").find( "#Name1").autocomplete( {
    source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Supplier&_fnc=acList&_key=&_id=-1&_val=",
    minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
        screenSupplier.onEnter( ui.item.value) ;
    }
}) ;
