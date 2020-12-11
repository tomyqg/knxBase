/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "SoftwareVersion", "tabPageSoftwareVersionSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"SoftwareVersion" ;
	screen.coreObject	=	"SoftwareVersion" ;

screenSoftwareVersion	=	screen ;

$( "#SoftwareVersionKeyData").find( "#SoftwareVersionName1").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=SoftwareVersion&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenSoftwareVersion.onEnter( ui.item.SoftwareVersionNo) ;
    }
}) ;

$( "#formSoftwareVersionDates").find( "#DateReview").datepicker({
    dateFormat: 'yy-mm-dd',
    showWeek: true
}) ;
$( "#formSoftwareVersionDates").find( "#DateApproved").datepicker({
    dateFormat: 'yy-mm-dd',
    showWeek: true
}) ;
$( "#formSoftwareVersionDates").find( "#DateAvailable").datepicker({
    dateFormat: 'yy-mm-dd',
    showWeek: true
}) ;
$( "#formSoftwareVersionDates").find( "#DateEndOfLife").datepicker({
    dateFormat: 'yy-mm-dd',
    showWeek: true
}) ;

