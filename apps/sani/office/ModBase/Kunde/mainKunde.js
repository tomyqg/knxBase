/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Kunde", "tabPageKundeSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Kunde" ;
	screen.coreObject	=	"Kunde" ;


$( "#formKundeKV1").find( "#Vers1IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formKundeKV1").find( "#KV1Name").val( ui.item.Name1) ;
    }
}) ;

$( "#formKundeKV2").find( "#Vers2IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formKundeKV2").find( "#KV2Name").val( ui.item.Name1) ;
    }
}) ;

$( "#formKundeKV3").find( "#Vers3IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formKundeKV3").find( "#KV3Name").val( ui.item.Name1) ;
    }
}) ;

$( "#formKundeBG").find( "#BGIKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formKundeBG").find( "#BGName").val( ui.item.Name1) ;
    }
}) ;

$( "#formKundeDetails").find( "#DatumGeburt").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#formKundeDetails").find( "#DatumVerstorben").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#formKundeLogistics").find( "#DatumAnlage").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#formKundeLogistics").find( "#DatumAenderung").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#dialogEditKundeAdresse").dialog( {
	autoOpen:	false,
	title:		"Kundenadresse bearbeiten",
	width: "800px",
	modal: true,
	buttons:	{
					"Abbrechen" :	function() {
											$( "#dialogEditKundeAdresse").dialog( "close") ;
									},
					"Speichern" :	function() {
									}
				}
}) ;

$( "#myButton2").click( function( _e) {
	_e.preventDefault() ;
	$( "#dialogEditKundeAdresse").dialog( "open") ;
}) ;
