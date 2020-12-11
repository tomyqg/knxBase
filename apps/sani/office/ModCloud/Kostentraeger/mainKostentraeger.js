/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Kostentraeger", "tabPageKostentraegerSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Kostentraeger" ;
	screen.coreObject	=	"Kostentraeger" ;

screenKostentraeger	=	screen ;

//$( "#Kostentraeger .wapScreen").wapScreen2( {
//}) ;

$( "#formKostentraegerMain").find( "#IKNr").keypress( function( _event) {
	console.log( "Hello, world ...") ;
	if ( _event.which == 13) {
		screenKostentraeger.onEnter( $(this).val()) ;
   		_event.preventDefault();
	}
}) ;

$( "#formKostentraegerMain").find( "#Name1").autocomplete( {
	source:		"search.php?sessionId="+sessionId,
	minLength:	1,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formKostentraegerMain").find( "#Name2").val( ui.item.Name1) ;
		$( "#formKostentraegerMain").find( "#Name3").val( ui.item.kundeNr) ;
    }
}) ;

$( "#KostentraegerKeyData").find( "#IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenKostentraeger.onEnter( ui.item.iKNr) ;
    }
}) ;
