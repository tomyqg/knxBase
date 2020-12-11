/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Software", "tabPageSoftwareSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Software" ;
	screen.coreObject	=	"Software" ;

screenSoftware	=	screen ;

$( "#SoftwareKeyData").find( "#SoftwareName1").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Software&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenSoftware.onEnter( ui.item.SoftwareNo) ;
    }
}) ;

$( "#formFilialeLogistics").find( "#DatumAnlage").datepicker({
    dateFormat: 'yy-mm-dd',
    showWeek: true
}) ;

