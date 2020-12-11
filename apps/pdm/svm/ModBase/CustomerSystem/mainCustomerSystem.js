/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "CustomerSystem", "tabPageCustomerSystemSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"CustomerSystem" ;
	screen.coreObject	=	"CustomerSystem" ;

screenCustomerSystem    =   screen ;

$( "#formCustomerSystemMain").find( "#CustomerNo").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Customer&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
        $( "#formCustomerSystemMain").find( "#CustomerNo").val( ui.item.value) ;
    }
}) ;
