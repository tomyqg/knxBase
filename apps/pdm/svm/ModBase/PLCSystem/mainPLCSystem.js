/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "PLCSystem", "tabPagePLCSystemSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"PLCSystem" ;
	screen.coreObject	=	"PLCSystem" ;

screenPLCSystem	=	screen ;

$( "#PLCSystemKeyData").find( "#PLCSystemName1").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=PLCSystem&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenPLCSystem.onEnter( ui.item.PLCSystemNo) ;
    }
}) ;
