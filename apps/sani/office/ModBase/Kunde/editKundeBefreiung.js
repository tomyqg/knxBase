/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "KundeBefreiung", "") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Kunde" ;

$.datepicker.setDefaults( $.datepicker.regional[ "de" ] );

$( "#formKundeBefreiungMain").find( "#DatumBefreiungVon").datepicker({
  showWeek: true
}) ;

//$( "#formKundeBefreiungMain").find( "#DatumBefreiungBis").datepicker({
//  showWeek: true
//}) ;
