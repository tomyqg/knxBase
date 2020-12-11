/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Firma", "tabPageFirmaSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Firma" ;
	screen.coreObject	=	"Firma" ;


$( "#formFirmaKV1").find( "#Vers1IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formFirmaKV1").find( "#KV1Name").val( ui.item.Name1) ;
    }
}) ;

$( "#formFirmaKV2").find( "#Vers2IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formFirmaKV2").find( "#KV2Name").val( ui.item.Name1) ;
    }
}) ;

$( "#formFirmaKV3").find( "#Vers3IKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formFirmaKV3").find( "#KV3Name").val( ui.item.Name1) ;
    }
}) ;

$( "#formFirmaBG").find( "#BGIKNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
		$( "#formFirmaBG").find( "#BGName").val( ui.item.Name1) ;
    }
}) ;

$( "#formFirmaDetails").find( "#DatumGeburt").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#formFirmaDetails").find( "#DatumVerstorben").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#formFirmaLogistics").find( "#DatumAnlage").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#formFirmaLogistics").find( "#DatumAenderung").datepicker({
	dateFormat: 'yy-mm-dd',
	showWeek: true
}) ;

$( "#dialogEditFirmaAdresse").dialog( {
	autoOpen:	false,
	title:		"Firmanadresse bearbeiten",
	width: "800px",
	modal: true,
	buttons:	{
					"Abbrechen" :	function() {
											$( "#dialogEditFirmaAdresse").dialog( "close") ;
									},
					"Speichern" :	function() {
									}
				}
}) ;

$( "#myButton2").click( function( _e) {
	_e.preventDefault() ;
	$( "#dialogEditFirmaAdresse").dialog( "open") ;
}) ;
