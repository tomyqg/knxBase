/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "SystemType", "tabPageSystemTypeSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"SystemType" ;
	screen.coreObject	=	"SystemType" ;

screenSystemType	=	screen ;

$( "#SystemTypeKeyData").find( "#SystemTypeName1").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=SystemType&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenSystemType.onEnter( ui.item.SystemTypeNo) ;
    }
}) ;
