/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Address", "tabPageAddressSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Address" ;
	screen.coreObject	=	"Address" ;

screenAddress	=	screen ;

$( "#AddressKeyData").find( "#AddressNo").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Address&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenAddress.onEnter( ui.item.value) ;
    }
}) ;

$( "#AddressKeyData").find( "#Name1").autocomplete( {
    source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Address&_fnc=acList&_key=&_id=-1&_val=",
    minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
        screenAddress.onEnter( ui.item.value) ;
    }
}) ;
