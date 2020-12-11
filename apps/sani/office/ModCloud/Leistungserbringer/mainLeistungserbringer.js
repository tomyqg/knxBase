/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Leistungserbringer", "tabPageLeistungserbringerSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Leistungserbringer" ;
	screen.coreObject	=	"Leistungserbringer" ;
screenLeistungserbringer	=	screen ;

$( "#formLeistungserbringerMain").find( "#IKNr").keypress( function( _event) {
	console.log( "Hello, world ...") ;
	if ( _event.which == 13) {
		screenLeistungserbringer.onEnter( $(this).val()) ;
   		_event.preventDefault();
	}
}) ;

$( "#formLeistungserbringerMain").find( "#Name1").autocomplete( {
	source:		"search.php?sessionId="+sessionId,
	minLength:	1,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formLeistungserbringerMain").find( "#Name2").val( ui.item.Name1) ;
		$( "#formLeistungserbringerMain").find( "#Name3").val( ui.item.kundeNr) ;
    }
}) ;

$( "#LeistungserbringerKeyData").find( "#IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Leistungserbringer&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenLeistungserbringer.onEnter( ui.item.iKNr) ;
    }
}) ;
