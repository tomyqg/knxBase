/**
 * regModTask
 * 
 * registers the module in the central database
 */
function	regModTask() {
	_debugL( 0x00000001, "regModTask: begin\n") ;
	myScreen	=	screenAdd( "screenTask", linkTask, "Task", "TaskKeyData", "_ITaskNr", showTaskAll) ;
	myScreen.package	=	"Base" ;
	myScreen.module	=	"Task" ;
	myScreen.coreObject	=	"Task" ;
	myScreen.showFunc	=	showTaskAll ;
	myScreen.keyField	=	getFormField( 'TaskKeyData', '_ITaskNr') ;
	myScreen.delConfDialog	=	"/ModManage/Task/confTaskDel.php" ;
	// make sure everything is setup fo this module, e.g. menuitems enables/disables etc.
	myScreen.link() ;
	if ( pendingKey != "") {
		requestUni( 'Base', 'Task', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showTaskAll) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	}
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	_debugL( 0x00000001, "regModTask: end\n") ;
}
function	linkTask() {
	_debugL( 0x00000001, "linkTask: \n") ;
}
/**
 *
 */
function	showTaskAll( response) {
	showTask( response) ;
}

/**
 *
 */
function	showTask( response) {
	var	user ;
	var	attrs ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	user	=	response.getElementsByTagName( "Task")[0] ;
	attrs	=	user.childNodes ;
	dispAttrs( attrs, "TaskKeyData") ;
	dispAttrs( attrs, "formTaskMain") ;
}