/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "KundeAuftrag", "tabPageKundeAuftragSurveyEntry") ;
	screen.package	=	"ModSales" ;
	screen.module	=	"KundeAuftrag" ;
	screen.coreObject	=	"KundeAuftrag" ;

screenKundeAuftrag	=	screen ;

console.log( "loaded ...") ;
console.log( "-------------------------------------------------------------------") ;

// $( "#KundeAuftrag .wapScreen").wapScreen2( {
// }) ;

$( "#formKundeAuftragMain").find( "#IKNr_KK").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
//		screenKundeAuftrag.onEnter( ui.item.iKNr) ;
    }
}) ;

$( "#formKundeAuftragMain").find( "#IKNr_KVVA").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
//		screenKundeAuftrag.onEnter( ui.item.iKNr) ;
    }
}) ;

$( "#formKundeAuftragMain").find( "#IKNr_Rechnung").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
//		screenKundeAuftrag.onEnter( ui.item.iKNr) ;
    }
}) ;

$( "#formKundeAuftragMain").find( "#KundeNr").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kunde&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
//		screenKundeAuftrag.onEnter( ui.item.kundeNr) ;
    }
}) ;

// $( "#KundeAuftragPosition").wapDialog( {
// 	title:	"Auftragsposition bearbeiten",
// 	width:	"900px",
// 	parentDS:	screen.dataSource
// }) ;

$.datepicker.setDefaults( $.datepicker.regional[ "de" ] );

$( "#formKundeAuftragMain").find( "#Datum").datepicker({
  showWeek: true
}) ;
$( "#formKundeAuftragKVVA").find( "#KVVADatum").datepicker({
  showWeek: true
}) ;
$( "#formKundeAuftragKVVA").find( "#KVVADatumVerschickt").datepicker({
  showWeek: true
}) ;
$( "#formKundeAuftragKVVA").find( "#KVVADatumUpdate").datepicker({
  showWeek: true
}) ;
$( "#formKundeAuftragKVVA").find( "#KVVADatumGenehmigung").datepicker({
  showWeek: true
}) ;
