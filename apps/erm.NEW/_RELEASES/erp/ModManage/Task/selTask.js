/**
 *
 */
var	selTaskDialog	=	null ;
var	selTaskMod	=	"" ;
var	selTaskApp	=	"" ;
var	selTaskFnc	=	"" ;
var	selTaskCb	=	null ;

/**
 *	_pkg	part of the path to call the script
 *	_cbCls	the class to act upon
 *	_fnc	
 *	_show	the function to call for handling the XML result
 *
 */
function	selTask( _mod, _app, _fnc, _key, _cb) {

	selTaskMod	=	_mod ;
	selTaskApp	=	_app ;
	selTaskFnc	=	_fnc ;
	selTaskCb	=	_cb ;

	if ( selTaskDialog == null) {
		selTaskDialog	=	new dijit.Dialog( {
								title:	"<?php echo FTr::tr( 'Select Task') ; ?>",
								duration:	100,
								href:	"/ModManage/Task/selTask.php"
							} ) ;
	}
	selTaskDialog.show() ;
}

/**
 *
 */
function	refSelTask( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	"/ModManage/Task/selTask_action.php?"
					+ "&_fnc=" + _fnc
					;
	/**
	 *
	 */
	_debugL( 0x00000001, "selTask.js::refSelTask(...):\n") ;
	postVars	=	getPOSTData( _form) ;
	_debugL( 0x00000001, "selTask.js::refSelTask(...): url = " + url + "\n") ;
	_debugL( 0x00000001, "selTask.js::refSelTask(...): postVars = " + postVars + "\n") ;
	dojo.xhrPost( {
		url: url,
		handleAs: "xml",
		postData: postVars,
		load: function( response) {
			refSelTaskReply( response) ;
		}
	} ) ;
	return false ;
}

function	selTaskFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelTask( "ModManage", "Task", "refSelTask", _form) ;
}

function	selTaskPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 10)
		startRow.value	=	parseInt( startRow.value) - 10 ;
	else
		startRow.value	=	0 ;
	refSelTask( "ModManage", "Task", "refSelTask", _form) ;
}

function	selTaskNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 10 ;
	refSelTask( "ModManage", "Task", "refSelTask", _form) ;
}

function	selTaskLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelTaskReply( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	tableSelTask	=	document.getElementById( "selTaskRes") ;

	myData	=	"NEUE DATEN:<br/>" ;
	Tasks	=	response.getElementsByTagName( "Task")[0] ;
	if ( Tasks) {
		listeTask	=	response.getElementsByTagName( "Task") ;
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th><?php echo FTr::tr( "TaskNr") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "RspUserId") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "DateReg") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "DateEsc") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Select") ; ?></th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < listeTask.length && i < 20 ; i++) {
			Task	=	response.getElementsByTagName( "Task")[i] ;
			fncData	=	Task.childNodes ;
			myLine	=	"<tr  onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + Task.getElementsByTagName( "TaskNr")[0].textContent + "</td>" ;
			myLine	+=	"<td>"
						+ Task.getElementsByTagName( "RspUserId")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>"
						+ Task.getElementsByTagName( "DateReg")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>"
						+ Task.getElementsByTagName( "DateEsc")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selTaskByTaskNr('"
						+ Task.getElementsByTagName( "TaskNr")[0].textContent
						+ "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	tableSelTask.innerHTML	=	myData ;
}

/**
 *
 */
function	selTaskByTaskNr( _taskNr) {
	selTaskDialog.hide() ;
	requestUni( selTaskMod, selTaskApp, '/Common/hdlObject.php', selTaskFnc, _taskNr, '', '', null, selTaskCb) ;
	return false ;
}

