/**
 * applicationSystem
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "Article", "tabPageArticleSurveyEntry") ;
	screen.package	=	"ModBase" ;
	screen.module	=	"Article" ;
	screen.coreObject	=	"Article" ;

screenAddress	=	screen ;

$( "#ArticleKeyData").find( "#ArticleNo").autocomplete( {
    source:		"../../api/dispatchJSON.php?sessionId="+sessionId+"&_obj=Article&_fnc=acList&_key=&_id=-1&_val=",
    minLength:	2,
    html:		true,			// optional (jquery.ui.autocomplete.html.js required)
    select: function( event, ui) {
        screenAddress.onEnter( ui.item.value) ;
    }
}) ;

