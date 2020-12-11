/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Filiale", "tabPageFilialeSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Filiale" ;
	screen.coreObject	=	"Filiale" ;


$( "#formFilialeKV1").find( "#Vers1IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formFilialeKV1").find( "#KV1Name").val( ui.item.Name1) ;
    }
}) ;

$( "#formFilialeKV2").find( "#Vers2IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formFilialeKV2").find( "#KV2Name").val( ui.item.Name1) ;
    }
}) ;

$( "#formFilialeKV3").find( "#Vers3IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formFilialeKV3").find( "#KV3Name").val( ui.item.Name1) ;
    }
}) ;

$( "#formFilialeBG").find( "#BGIKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formFilialeBG").find( "#BGName").val( ui.item.Name1) ;
    }
}) ;

$( "#formFilialeDetails").find( "#DatumGeburt").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#formFilialeDetails").find( "#DatumVerstorben").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#formFilialeLogistics").find( "#DatumAnlage").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#formFilialeLogistics").find( "#DatumAenderung").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#dialogEditFilialeAdresse").dialog( {
	autoOpen:	false,
	title:		"Filialenadresse bearbeiten",
	width: "800px",
	modal: true,
	buttons:	{
					"Abbrechen" :	function() {
											$( "#dialogEditFilialeAdresse").dialog( "close") ;
									},
					"Speichern" :	function() {
									}
				}
}) ;

$( "#myButton2").click( function( _e) {
	_e.preventDefault() ;
	$( "#dialogEditFilialeAdresse").dialog( "open") ;
}) ;
