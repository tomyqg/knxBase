/**
 * regModJobs
 * 
 * registers the module in the central database
 */
function	regModJobs() {
	_debugL( 0x00000001, "regModJobs: \n") ;
	myScreen	=	screenAdd( "screenJobs", linkJobs, "Jobs", "JobsKeyData", "_IJobsNr", showJobsAll, null) ;
	myScreen.package	=	"ModSys" ;
	myScreen.module	=	"Jobs" ;
	myScreen.coreObject	=	"Jobs" ;
	myScreen.showFunc	=	showJobsAll ;
	myScreen.keyField	=	getFormField( 'JobsKeyData', '_IId') ;
	myScreen.delConfDialog	=	"/ModSys/Jobs/confJobsDel.php" ;
	// link to this screen
	myScreen.link() ;
	// process any pending 'link-to-screen# data
	if ( pendingKey != "") {
		requestUni( 'ModSys', 'Jobs', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showJobsAll) ;
	}
	pendingKey	=	"" ;
}
function	linkJobs() {
	_debugL( 0x00000001, "linkJobs: \n") ;
}
/**
 *
 */
function	showJobsAll( response) {
	showJobs( response) ;
}
/**
 *
 */
function	showJobs( response) {
	var	user ;
	var	attrs ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	user	=	response.getElementsByTagName( "Jobs")[0] ;
	attrs	=	user.childNodes ;
	dispAttrs( attrs, "JobsKeyData") ;
	dispAttrs( attrs, "formJobsMain") ;
}