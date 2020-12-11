/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "HMISystem", "tabPageHMISystemSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"HMISystem" ;
	screen.coreObject	=	"HMISystem" ;

screenHMISystem	=	screen ;

$( "#HMISystemKeyData").find( "#HMISystemName1").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=HMISystem&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenHMISystem.onEnter( ui.item.HMISystemNo) ;
    }
}) ;
