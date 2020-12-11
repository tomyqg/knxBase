/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "KostentraegerGruppe", "tabPageKostentraegerGruppeSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"KostentraegerGruppe" ;
	screen.coreObject	=	"KostentraegerGruppe" ;

screenKostentraegerGruppe	=	screen ;

//$( "#KostentraegerGruppe .wapScreen").wapScreen2( {
//}) ;

$( "#formKostentraegerGruppeMain").find( "#IKNr").keypress( function( _event) {
	console.log( "Hello, world ...") ;
	if ( _event.which == 13) {
		screenKostentraegerGruppe.onEnter( $(this).val()) ;
   		_event.preventDefault();
	}
}) ;

$( "#formKostentraegerGruppeMain").find( "#Name1").autocomplete( {
	source:		"search.php?sessionId="+sessionId,
	minLength:	1,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formKostentraegerGruppeMain").find( "#Name2").val( ui.item.Name1) ;
		$( "#formKostentraegerGruppeMain").find( "#Name3").val( ui.item.kundeNr) ;
    }
}) ;

$( "#KostentraegerGruppeKeyData").find( "#IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=KostentraegerGruppe&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		screenKostentraegerGruppe.onEnter( ui.item.iKNr) ;
    }
}) ;
