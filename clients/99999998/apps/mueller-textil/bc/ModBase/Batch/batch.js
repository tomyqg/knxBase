/**
 * scrAssessment
 *
 * registers the module in the central database
 */
var	screen	=	new wapScreen( "batch", "tabPageMainEntry") ;
screen.package	=	"ModBase" ;
screen.module	=	"Batch" ;
screen.coreObject	=	"Batch" ;
screen.onJS =   function() {
	dTrace( 1, "batch.js", "*", "screen.onJS()", "called") ;
}
