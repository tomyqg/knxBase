/**
 *
 */
var	selJobsDialog	=	null ;
var	selJobsMod	=	"" ;
var	selJobsApp	=	"" ;
var	selJobsFnc	=	"" ;
var	selJobsCb	=	null ;

/**
 *	_pkg	part of the path to call the script
 *	_cbCls	the class to act upon
 *	_fnc	
 *	_show	the function to call for handling the XML result
 *
 */
function	selJobs( _mod, _app, _fnc, _key, _cb) {

	selJobsMod	=	_mod ;
	selJobsApp	=	_app ;
	selJobsFnc	=	_fnc ;
	selJobsCb	=	_cb ;

	if ( selJobsDialog == null) {
		selJobsDialog	=	new dijit.Dialog( {
								title:	"<?php echo FTr::tr( "Select Job") ; ?>",
								duration:	100,
								href:	"/ModSys/Jobs/selJobs.php"
							} ) ;
	}
	selJobsDialog.show() ;
}

/**
 *
 */
function	refSelJobs( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	"/ModSys/Jobs/selJobs_action.php?"
					+ "&_fnc=" + _fnc
					;
	/**
	 *
	 */
	_debugL( 0x00000001, "selJobs.js::refSelJobs(...):\n") ;
	postVars	=	getPOSTData( _form) ;
	_debugL( 0x00000001, "selJobs.js::refSelJobs(...): url = " + url + "\n") ;
	_debugL( 0x00000001, "selJobs.js::refSelJobs(...): postVars = " + postVars + "\n") ;
	dojo.xhrPost( {
		url: url,
		handleAs: "xml",
		postData: postVars,
		load: function( response) {
			refSelJobsReply( response) ;
		}
	} ) ;
	return false ;
}

function	selJobsFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelJobs( "ModSys", "Jobs", "refSelJobs", _form) ;
}

function	selJobsPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelJobs( "ModSys", "Jobs", "refSelJobs", _form) ;
}

function	selJobsNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelJobs( "ModSys", "Jobs", "refSelJobs", _form) ;
}

function	selJobsLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelJobsReply( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableSelJobs	=	document.getElementById( "selJobsRes") ;

	myData	=	"NEUE DATEN:<br/>" ;
	Jobss	=	response.getElementsByTagName( "Jobs")[0] ;
	if ( Jobss) {
		listeJobs	=	response.getElementsByTagName( "Jobs") ;
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th><?php echo FTr::tr( "Id") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "JobName") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Schedule") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Script") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Select") ; ?></th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeJobs.length && i < 20 ; i++) {
			Jobs	=	response.getElementsByTagName( "Jobs")[i] ;
			fncData	=	Jobs.childNodes ;
			myLine	=	"<tr  onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + Jobs.getElementsByTagName( "Id")[0].textContent + "</td>" ;
			myLine	+=	"<td>"
						+ Jobs.getElementsByTagName( "JobName")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>"
						+ Jobs.getElementsByTagName( "Schedule")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>"
						+ Jobs.getElementsByTagName( "Script")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selJobsById('"
						+ Jobs.getElementsByTagName( "Id")[0].textContent
						+ "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	tableSelJobs.innerHTML	=	myData ;
}

/**
 *
 */
function	selJobsById( _id) {
	selJobsDialog.hide() ;
	requestUni( selJobsMod, selJobsApp, '/Common/hdlObject.php', selJobsFnc, '', _id, '', null, selJobsCb) ;
	return false ;
}

