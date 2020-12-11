var	screen	=	new wapScreen( "Rezept", "") ;
	screen.package	=	"ModSales" ;
	screen.module	=	"Rezept" ;
	screen.coreObject	=	"Auftrag" ;

screenRezept	=	screen ;

$( "#formRezeptMain").find( "#IKNr_KK").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kostentraeger&_fnc=acList&_key=&_id=-1&_val=",
	minLength:	2,
   	html:		true,			// optional (jquery.ui.autocomplete.html.js required)
   	select: function( event, ui) {
		$( "#formRezeptMain").find( "#KVName").val( ui.item.label) ;
   	}
}) ;

$( "#formRezeptMain").find( "#Kunde").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Kunde&_fnc=acList&_key=&_id=-1&_val=2",
	minLength:	2,
   	html:		true,			// optional (jquery.ui.autocomplete.html.js required)
   	select: function( event, ui) {
		$( "#formRezeptMain").find( "#Kunde").val( ui.item.FullAddress) ;
   	}
}) ;

$( "#formRezeptMain").find( "#IKNr_LE").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Filiale&_fnc=acList&_key=&_id=-1&_val=3",
	minLength:	0,
   	html:		true,			// optional (jquery.ui.autocomplete.html.js required)
//   	select: function( event, ui) {
//		$( "#formRezeptMain").find( "#LEIKNr").val( ui.item.FullAddress) ;
//   	}
}) ;

$( "#formRezeptMain").find( "#ArtikelSuchText").autocomplete( {
	source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=ArtikelInfo&_fnc=acList&_key=&_id=-1&_val=2",
	minLength:	3,
   	html:		true,			// optional (jquery.ui.autocomplete.html.js required)
   	select: function( event, ui) {
		$( "#formRezeptMain").find( "#ArtikelDaten").val( ui.item.label) ;
		$( "#formRezeptMain").find( "#NeueArtikelNr").val( ui.item.ArtikelNr) ;
		$( "#formRezeptMain").find( "#NeueHMVNr").val( ui.item.HMVNr) ;
		$( "#formRezeptMain").find( "#Source").val( ui.item.Source) ;
   	}
}) ;

$.datepicker.setDefaults( $.datepicker.regional[ "de" ] );

$( "#formRezeptMain").find( "#RezeptDatum").datepicker({
  showWeek: true
}) ;
