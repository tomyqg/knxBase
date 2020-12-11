/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "LEGSVertrag", "tabPageLEGSVertragSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"LEGSVertrag" ;
	screen.coreObject	=	"LEGSVertrag" ;

screenLEGSVertrag	=	screen ;

$( "#LEGSVertragKeyData").find( "#LEGS").keypress( function( _event) {
	if ( _event.which == 13) {
		screenLEGSVertrag.onEnter( $(this).val()) ;
   		_event.preventDefault();
	}
}) ;
