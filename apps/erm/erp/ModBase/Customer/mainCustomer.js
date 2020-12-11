/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Customer", "tabPageCustomerSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Customer" ;
	screen.coreObject	=	"Customer" ;

screenCustomer	=	screen ;

$( "#CustomerKeyData").find( "#CustomerName1").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Customer&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenCustomer.onEnter( ui.item.CustomerNo) ;
    }
}) ;
