"use strict"
/**
 * Copyright (c) 2015, 2016 wimtecc, Karl-Heinz Welter
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 *
 */
var	confDialog	=	null ;
var	workDialog	=	null ;
var	dialogStack	=	new Array() ;
var	dialogKeyStack	=	new Array() ;
var	dialogFncStack	=	new Array() ;
/**
 *
 */
function	showStatus( response) {
	var	statusMsg ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	statusMsg	=	response.getElementsByTagName( "Status")[0] ;
	if ( statusMsg) {
		statusCode	=	response.getElementsByTagName( "StatusCode")[0].childNodes[0].nodeValue ;
		statusText	=	response.getElementsByTagName( "StatusText")[0].childNodes[0].nodeValue ;
		statusInfo	=	response.getElementsByTagName( "StatusInfo")[0].childNodes[0].nodeValue ;
		if ( parseInt( statusCode) != 0) {
			displayText	=	"StatusCode: " + statusCode + "<br/>" ;
			displayText	+=	"StatusText: " + statusText + "<br/>" ;
			displayText	+=	"StatusInfo: " + statusInfo ;
		}
	} else {
	}
}
/**
*
*/
function	showInfo( response) {
	var	statusMsg ;
	_debugL( 1, "common.js::showInfo( <_resp>): begin\n") ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	statusMsg	=	response.getElementsByTagName( "StatusInfo")[0].childNodes[0].nodeValue ;
	if ( statusMsg != "ok") {
		_debugL( 1, "common.js::showInfo( <_resp>): statusMsg is defined \n") ;
		statusCode	=	response.getElementsByTagName( "StatusCode")[0].childNodes[0].nodeValue ;
		statusInfo	=	response.getElementsByTagName( "StatusInfo")[0].childNodes[0].nodeValue ;
		displayText	=	"<b>StatusCode: " + statusCode + "</b><br/>" ;
		displayText	+=	"StatusInfo: " + statusInfo ;
	} else {
	}
	_debugL( 1, "common.js::showInfo( <_resp>): end\n") ;
}

/**
 * getPOSTData
 * ==============
 *
 * Get the data in the given _form suitable for a POST request
 * @param	_form	string	name of the form containing the fields
 * @return	string	string containing all "edit"able fields of the form suitable for a POST request
 */
function	getPOSTData( _form, _scope) {
	var	actField ;
	var	wapType, wapAttr, wapMode ;
	var	formField ;
	var l_lang;
	var	postVars	=	"" ;
	var	dateValue ;					// date value to write to the POST request in en_US format
	/**
	 *
	 */
	dBegin( 1, "wapCommon.js", "*", "getPOSTData( '"+_form+"')") ;
	var	myForm	=	getForm( _form, _scope) ;
	if (navigator.userLanguage)			// Explorer
		l_lang = navigator.userLanguage;
	else if (navigator.language)		// FF
		l_lang = navigator.language;
	else
		l_lang = "de";
	if ( myForm) {
		var	myFields	=	myForm.getElementsByClassName( "wapField") ;
		/**
		 *
		 */
		for ( var i1=0 ; i1 < myFields.length ; i1++) {
			actField	=	myFields[i1] ;						// HTML form input element
			wapType	=	actField.dataset.wapType ;
			formField	=	null ;								// XML node with value to be displayed
			if ( wapType) {
				dTrace( 2, "screen.js", "screen", "getPOSTData( ...)", "working on '"+actField.name+"', wapType := '"+wapType+"'") ;
				wapAttr	=	actField.dataset.wapAttr ;
				wapMode	=	actField.dataset.wapMode ;
				if ( wapMode == "edit") {
					formField	=	actField ;
				}
			}
			if ( formField != null) {
				if ( postVars != "") {
					postVars	+=	"&" ;
				}
				switch ( wapType) {
				case	"tinyint"	:
				case	"smallint"	:
				case	"mediumint"	:
				case	"int"		:
				case	"bigint"	:
					postVars	+=	wapAttr + "=" + formField.value ;
					break ;
				case	"float"	:
				case	"double"	:
					postVars	+=	wapAttr + "=" + formField.value.replace( ",", ".") ;
					break ;
				case	"date"	:		// H = Hidden
					if ( l_lang == "en") {
						dateValue	=	formField.value ;
					} else if ( l_lang == "de") {
						dateValue	=	zFill( parseInt( formField.value.substr(6, 4)), 4) + "-"
									+	zFill( parseInt( formField.value.substr(3, 2)), 2) + "-"
									+	zFill( parseInt( formField.value.substr(0, 2)), 2) ;
					}
					postVars	+=	wapAttr + "=" + dateValue ;
					break ;
				case	"datetime"	:		// H = Hidden
					if ( l_lang == "en") {
						dateValue	=	formField.value ;
					} else if ( l_lang == "de") {
						dateValue	=	zFill( parseInt( formField.value.substr(6, 4)), 4) + "-"
									+	zFill( parseInt( formField.value.substr(3, 2)), 2) + "-"
									+	zFill( parseInt( formField.value.substr(0, 2)), 2) + " "
									+	formField.value.substr( 11, 8) ;
					}
					postVars	+=	wapAttr + "=" + dateValue ;
					break ;
				case	"text"	:		// H = Hidden
				case	"longtext"	:		// H = Hidden
					postVars	+=	wapAttr + "=" + formField.value ;
					break ;
				case	"rtf"	:		// H = Hidden
					postVars	+=	wapAttr + "=" + encodeURIComponent( CKEDITOR.instances[actField.id].getData()) ;
					break ;
				case	"option"	:
					dTrace( 11, "screen.js", "screen", "getPOSTData( ...)", "data :='" + formField.value + "'") ;
					postVars	+=	wapAttr + "=" + encodeURIComponent( formField.value) ;
					break ;
				case	"check"	:
					dTrace( 11, "screen.js", "screen", "getPOSTData( ...)", "working on check") ;
					var nameCheck	=	myFields[i1].name ;
					var val	=	0 ;
					dTrace( 11, "screen.js", "screen", "getPOSTData( ...)", "working on check, starting loop") ;
					for ( var n1=i1 ; n1 < myFields.length && myFields[n1].name == nameCheck ; n1++) {
						dTrace( 11, "screen.js", "screen", "getPOSTData( ...)", "checking "+n1.toString()+", name := '"+myFields[n1].name+"'") ;
						if ( myFields[n1].checked) {
							val	+=	parseInt( myFields[n1].value) ;
						}
					}
					i1	=	n1 - 1 ;
					postVars	+=	wapAttr + "=" + encodeURIComponent( val.toString()) ;
					break ;
				case	"flag"	:
					if ( formField.checked) {
						postVars	+=	wapAttr + "=" + encodeURIComponent( formField.value) ;
					}
					break ;
				/**
				 * default:
				 * here we will land with waptypes "image" and "upload"
				 */
				default	:
					break ;
				}
			} else {
				dTrace( 2, "screen.js", "screen", "getPOSTData( ...)", "no wapType attribute found") ;
				var	myType	=	actField.getAttribute( "type") ;
				if ( myType) {
					switch ( myType) {
					case	"hidden"	:
						var	name	=	actField.getAttribute( "name") ;
						postVars	+=	name + "=" + encodeURIComponent( actField.value) ;
						break ;
					}
				}
			}
		}
	} else {
		dTrace( 0, "screen.js", "screen", "getPOSTData( ...)", "form['"+_form+"'] not found!") ;
	}
	dTrace( 2, "screen.js", "screen", "getPOSTData( ...)", "postData := '" + postVars + "'") ;
	/**
	 *
	 */
	dEnd( 1, "screen.js", "screen", "getPOSTData( '"+_form+"')") ;
	return	postVars ;
}
/**
 *
 * @param _form
 */
function	clrForm( _form) {
	clearForm( _form) ;
}
function	clearForm( _form) {
	var	myForm ;
	var	myFields ;
	var	myTargetField	=	null ;
	dBegin( 1, "wapCommon.js", "*", "clearForm( '" + _form + "')") ;
	/**
	 *
	 */
	if ( _form != "") {
		myForm	=	getForm( _form) ;
		if ( myForm) {
			myFields	=	myForm.getElementsByClassName( "wapField") ;
			/**
			 * find a field for the respective XML-attribute
			 */
			for ( var i=0 ; i < myFields.length ; i++) {
					dTrace( 5, "wapCommon.js", "*", "clearForm()", "clearing...: " + myFields[i].id) ;
				switch ( myFields[i].type) {
				case	"text"	:
					myFields[i].value	=	"" ;
					myFields[i].style.backgroundColor	=	"#ffffcc" ;
					break ;
				case	"textarea"	:
					myFields[i].value	=	"" ;
					myFields[i].style.backgroundColor	=	"#ffffcc" ;
					break ;
				case	"select-one"	:
					break ;
				case	"submit"	:
					break ;
				default	:
					break ;
				}
			}
		} else {
			dTrace( 3, "wapCommon.js", "*", "clearForm()", "form[" + _form + "] not found!") ;
		}
	}
	dEnd( 1, "wapCommon.js", "*", "clearForm( '" + _form + "')") ;
}
function	hookSetMenuItems() {
	if ( screenCurrent) {
		screenCurrent.setMenuItems() ;
	}
}
/**
 *	hookEnterKey
 *
 *	this event handler must be added on document level. however, it needs to be made sure that the event can
 *	call the screenCurrent.enterKey only in case the key was pressed in
 */
function	hookEnterKey( _event) {
	dBegin( 1, "wapCommon.js", "*", "hookEnterKey( <_event>") ;
	if ( popupCurrent) {
		dEnd( 1, "wapCommon.js", "*", "hookEnterKey( <_event>") ;
		return popupCurrent.enterKey( _event) ;
	} else if ( screenCurrent) {
		dTrace( 3, "wapCommon.js", "*", "hookEnterKey( ...)", "keyCode := " + _event.keyCode.toString()) ;
//		if ( _event.keyCode == 13) {
			dEnd( 1, "wapCommon.js", "*", "hookEnterKey( <_event>") ;
			return screenCurrent.enterKey( _event) ;
//		} else {
//			dEnd( 1, "wapCommon.js", "*", "hookEnterKey( <_event>") ;
//			return true ;
//		}
	} else {
		dEnd( 1, "wapCommon.js", "*", "hookEnterKey( <_event>") ;
		return true ;
	}
}
function	dispatchForm( _res, _wd, _fnc, _form) {
	if ( screenCurrent) {
		screenCurrent.dispatch( _wd, _fnc, screenCurrent.keyField.value, '', '', _form) ;
	} else {
		_debugL( 0x00000001, "!screenCurrent\n") ;
	}
	return false ;
}
var	pendingKey	=	"" ;
var	pendingFnc	=	null ;
function	screenLinkTo( _screenName, _key, _fnc) {
	dBegin( 1, "wapCommon.js", "*", "screenLinkTo( '"+_screenName+"', '"+_key+"', '"+_fnc+")") ;
	if ( ! _key)
		_key	=	"" ;
	if ( ! _fnc)
		_fnc	=	"" ;
	/**
	 * only 'push' the current screen on the stack if the link-to-screen
	 * is a different one
	 */
	dTrace( 1, "wapCommon.js", "*", "screenLinkTo( '"+_screenName+"', '"+_key+"')", "going from " + screenCurrent.screenName + " to " + _screenName) ;
	if ( screenCurrent.screenName != _screenName) {
		dTrace( 1, "wapCommon.js", "*", "screenLinkTo( '"+_screenName+"', '"+_key+"')", "pushing to stack ...") ;
		screenPush( screenCurrent.screenName, screenCurrent.keyField.value, "") ;
	}
	/**
	 * show the new screen
	 * if it's already loaded, request the date
	 * else store the key and 'assume' the register function of the screen
	 * wil take care
	 */
	dTrace( 1, "wapCommon.js", "*", "screenLinkTo( '"+_screenName+"', '"+_key+"')", "") ;
	var	myScreen	=	showScreen2( _screenName, false, _key, _fnc) ;
	dEnd( 1, "wapCommon.js", "*", "screenLinkTo(...)", "screenLinkTo(...)") ;
	return false ;
}
/**
 *
 * @param _screen
 * @param _tabCntrl
 * @param _tab
 * @return
 */
function	gotoFull( _screen, _tabCntrl, _tab) {
	screenShow( _screen) ;
	gotoTab( _tabCntrl, _tab) ;
}
/**
 *
 * @param string	_screen		id of the screen to be reloaded
 * @return
 */
function	reload( _screen) {
	var	myScreen ;
	dBegin( 1, "wapCommon.js", "*", "reload( '"+_screen+"')") ;
	myScreen	=	dijit.byId( _screen) ;
	if ( myScreen) {
		dTrace( 1, "wapCommon.js", "*", "reload( '"+_screen+"')", "dijit.ById found") ;
        myScreen.refresh() ;
	} else {
		dTrace( 1, "wapCommon.js", "*", "reload( '"+_screen+"')", "dijit.ById * NOT * found") ;
	}
	dEnd( 1, "wapCommon.js", "*", "reload( '"+_screen+"')") ;
	return myScreen ;
}
/**
 *
 * @return
 */
function	hookBack() {
	dBegin( 1, "wapCommon.js", "*", "back()") ;
	if ( dialogStack.length > 0) {
		dTrace( 1, "wapCommon.js", "*", "back()", "dialog stack contains items ...") ;
		var screenName	=	dialogStack.pop() ;
		dTrace( 1, "wapCommon.js", "*", "back()", "dialogFnc something else but 'function'") ;
		myScreen	=	showScreen2( screenName, false, dialogKeyStack.pop(), dialogFncStack.pop()) ;
	} else {
		dTrace( 1, "wapCommon.js", "*", "back()", "dialog stack is empty") ;
	}
	dEnd( 1, "wapCommon.js", "*", "back()") ;
}
function	screenPush( _name, _key, _fnc) {
	dBegin( 901, "wapCommon.js", "*", "screenPush( '"+_name+"', '"+_key+"', '"+_fnc+"')") ;
	dialogStack.push( _name) ;
	dialogKeyStack.push( _key) ;
	dialogFncStack.push( _fnc) ;
	dEnd( 901, "wapCommon.js", "*", "screenPush()") ;
}
function	combine( _s1, _s2, _s3) {
	myLine	=	_s1 ;
	if ( _s2.length > 0)
		myLine	+=	"<br/>" + _s2 ;
	if ( _s3.length > 0)
		myLine	+=	"<br/>" + _s3 ;
	return myLine ;
}
/*															*/
/*			START OF DATA MINER CODE						*/
/*															*/
var	dmDivName ;
var	dmTag ;
var	dmGotoFnc ;
var	dmRetFnc ;
var	dmRetVal ;

/*															*/
/*			END OF DATA MINER CODE							*/
/*															*/
var	markerConfObj		=	null ;
function	confAction( _screen, _question, _fnc) {
//	_debugL( 0x01000000, _question) ;
	if ( ! _fnc)
		_fnc	=	"del" ;
	if ( confDialog)
		confDialog.destroyRecursive() ;
	confDialog	=	null ;
	if ( confDialog == null) {
		confDialog	=	new dijit.Dialog( {
									title:	"Confirmation",
									duration:	100,
									href:	_question,
									fnc:	_fnc
								} ) ;
	}
	confDialog.show() ;
}
function	confCancel() {
	confDialog.hide() ;
}
function	confGo() {
	confDialog.hide() ;
	requestUni( screenCurrent.package, screenCurrent.module,
					'/api/hdlObject.php', confDialog.fnc,
					screenCurrent.keyField.value, '', '',
					null, screenCurrent.fncShow) ;
}
function	hookLogoff() {
	window.location	=	"/logout.php?logoff=&sessionId="+sessionId ;
}
/*
 *
 */
var	createPDFFunc	=	null ;
var	showPDFFunc	=	null ;
function	hookNew() {
	if ( screenCurrent) {
		if ( screenCurrent.fncNew != null) {
			screenCurrent.fncNew() ;
		} else {
			requestUni( screenCurrent.package, screenCurrent.coreObject,
					'/Common/hdlObject.php', 'add',
					screenCurrent.keyField.value, '', '',
					null, screenCurrent.fncShow) ;
		}
	} else {
		_debugL( 0x01000000, "screenCurrent := null\n") ;
	}
	_debugL( 0x00000001, "common.js::hookNew(): end\n") ;
}
function	hookCopy() {
	if ( screenCurrent) {
		if ( screenCurrent.fncCopy != null) {
			screenCurrent.fncCopy() ;
		} else {
			requestUni( screenCurrent.package, screenCurrent.coreObject,
					'/Common/hdlObject.php', 'copy',
					screenCurrent.keyField.value, '', '',
					null, screenCurrent.fncShowAll) ;
///			_debugL( 0x01000000, "no function connected\n") ;
		}
	} else {
//		_debugL( 0x01000000, "screenCurrent := null\n") ;
	}
}
function	hookSaveTab() {

}
var	markerKeyDel ;
function	hookDelete() {
	if ( screenCurrent) {
		if ( screenCurrent.fncDelete != null) {
			screenCurrent.fncDelete() ;
		} else {
			confAction( screenCurrent, screenCurrent.delConfDialog, "del") ;
			return false ;
		}
	} else {
		_debugL( 0x01000000, "hookDelete: screenCurrent := null\n") ;
	}
}
function	hookReload() {
	dBegin( 1, "wapCommon.js", "*", "hookReload()") ;
	if ( screenCurrent) {
		if ( showScreen2( screenCurrent.screenName, true)) ;
	}
	dEnd( 1, "wapCommon.js", "*", "hookReload()") ;
}
function	_reloadRefresh() {
	dBegin( 1, "wapCommon.js", "*", "_reloadRefresh()") ;
	requestUni( screenCurrent.package, screenCurrent.module,
			'/Common/hdlObject.php', 'getXMLComplete',
			screenCurrent.keyField.value, '', '',
			null, screenCurrent.fncShowAll) ;
	dEnd( 1, "wapCommon.js", "*", "_reloadRefresh()") ;
}
function	hookSaveAllTabs() {

}
/**
 * hooks for
 * - CSV creation (must return the CSV)
 * @return
 */
function	hookCSVCreate() {
	if ( screenCurrent != null) {
		if ( screenCurrent.fncGetCSV) {
			screenCurrent.fncGetCSV() ;
		} else {
			requestCSV( screenCurrent.package, screenCurrent.module,
							'/Common/hdlObjectCSV.php', 'getCSV',
							screenCurrent.keyField.value, '', '') ;
		}
	} else {
		_debugL( 0x01000000, "hookCSVCreateShow: no function connected\n") ;
	}
}
/**
 * hooks for
 * - PDF creation (must return normal XML Object)
 * - PDF display (must return PDF mime-type)
 * - PDF printing (must return PDF mime-type)
 * @return
 */
function	hookPDFCreate() {
//	_debugL( 0x01000000, "CreatePDF pressed\n") ;
	if ( screenCurrent) {
		if ( screenCurrent.fncPDFCreate != null) {
			screenCurrent.fncPDFCreate() ;
		} else {
			requestUni( screenCurrent.package, screenCurrent.module+'Doc',
							'/Common/hdlObject.php', 'createPDF',
							screenCurrent.keyField.value, '', '',
							null, screenCurrent.fncShowAll) ;
		}
	} else {
		_debugL( 0x01000000, "hookPDFCreate: screenCurrent := null\n") ;
	}
}
function	hookPDFShow() {
//	_debugL( 0x01000000, "ShowPDF pressed\n") ;
	if ( screenCurrent != null) {
		if ( screenCurrent.fncPDFShow != null) {
			screenCurrent.fncPDFShow() ;
		} else {
			requestPDF( screenCurrent.package, screenCurrent.module+'Doc',
							'/Common/hdlObjectPDF.php', 'getPDF',
							screenCurrent.keyField.value, '', '') ;
		}
	} else {
		_debugL( 0x01000000, "hookPDFShow: no function connected\n") ;
	}
}
function	hookPDFPrint() {
	_debugL( 0x01000000, "hookPDFPrint pressed\n") ;
	if ( screenCurrent != null) {
		_debugL( 0x01000000, "hookPDFPrint pressed\n") ;
		if ( screenCurrent.fncPDFPrint != null) {
			screenCurrent.fncPDFPrint() ;
		} else {
			requestPDF( screenCurrent.package, screenCurrent.module+'Doc',
							'/Common/hdlObjectPDF.php', 'printPDF',
							screenCurrent.keyField.value, '', '') ;
		}
	} else {
		_debugL( 0x01000000, "hookPDFPrint: no function connected\n") ;
	}
}

function	hookAbout() {
	aboutDialog	=	new dijit.Dialog( {
									title:	"About",
									duration:	100,
									href:	"/Tools/about.php"
								} ) ;
	aboutDialog.show() ;
}

function	hookSession() {
	var	sessionDialog	=	new wapPopup( null, "SessionInfo", {
									url:	"/Tools/session.php" + "?sessionId=" + sessionId
								,	modal:	true
								} ) ;
	sessionDialog.show() ;
}

function	hookPrevObject() {
	if ( screenCurrent) {
		screenCurrent.onPrev() ;
	} else {
		dTrace( 1, "wapCommon.js", "*", "hookPrevObject", "screenCurrent := null") ;
	}
}
function	hookNextObject() {
	dBegin( 101, "wapCommon.js", "*", "hookNextObject()") ;
	if ( screenCurrent) {
		screenCurrent.onNext() ;
	} else {
		dTrace( 102, "wapCommon.js", "*", "hookNextObject", "screenCurrent := null") ;
	}
	dEnd( 101, "wapCommon.js", "*", "hookNextObject()") ;
}
function	hookSelect() {
	if ( screenCurrent) {
		screenCurrent.select.show( '', -1, '') ;
	} else {
		dTrace( 1, "wapCommon.js", "*", "hookNextObject", "hookSelect: screenCurrent := null\n") ;
	}
}
function	hookUserDoc() {
}

function	hookProdDoc() {
}
function	docLoaded() {
}
/**
 * xmlToObj
 * Converts an XML structure into a structured object which can be access by cascaded tag-names.
 * E.g.
 * <xml><main><atr1>1</atr1><atr2>2</atr2><sub><atr1>1.1</atr1><atr2>1.2</atr2></sub></main>
 * will create an object which can be accessed by:
 * obj.main.atr1
 * obj.main.atr2
 * obj.main.sub.atr1
 * obj.main.sub.atr2
 *
 * @param _xml object
 * @returns {Object}
 */
function	xmlToObj( _xml) {
	var myObj	=	new Object() ;
	dBegin( 1, "wapCommon.js", "*", "xmlToObj( <xml>)") ;
	dTrace( 2, "wapCommon.js", "*", "xmlToObj( <xml>)", "This: NodeType: " + _xml.nodeType+ ", Name: " + _xml.nodeName + " " + _xml.hasChildNodes() + "") ;
	if ( _xml.hasChildNodes()) {
		for ( var actChild = _xml.firstChild ; actChild ; actChild = actChild.nextSibling) {
			dTrace( 2, "wapCommon.js", "*", "xmlToObj( <xml>)", "Child: NodeType: " + actChild.nodeType+ " " + actChild.nodeName + ", has childNodes = " + actChild.hasChildNodes() + "") ;
			if ( actChild.hasChildNodes()) {
				dTrace( 2, "wapCommon.js", "*", "xmlToObj( <xml>)", "Child: childNodes.length (" + actChild.childNodes.length + ")") ;
				if ( actChild.childNodes.length == 1) {
					if ( actChild.firstChild.nodeType == 3) {				// text node
						dTrace( 2, "wapCommon.js", "*", "xmlToObj( <xml>)", "-> 3: assigning value to myObj."+actChild.nodeName+"("+actChild.firstChild.nodeValue+")") ;
						myObj[actChild.nodeName]	=	actChild.firstChild.nodeValue ;
					} else if ( actChild.firstChild.nodeType == 4) {		// CDATA section
						dTrace( 2, "wapCommon.js", "*", "xmlToObj( <xml>)", "-> 4: assigning value to myObj."+actChild.nodeName+"("+actChild.firstChild.nodeValue+")") ;
						myObj[actChild.nodeName]	=	actChild.firstChild.nodeValue ;
					} else {
						dTrace( 2, "wapCommon.js", "*", "xmlToObj( <xml>)", "-> *: assigning object to myObj."+actChild.nodeName+"") ;
						mySubObj	=	xmlToObj( actChild) ;
						myObj[actChild.nodeName]	=	xmlToObj( actChild) ;
					}
				} else {
					dTrace( 2, "wapCommon.js", "*", "xmlToObj( <xml>)", "-> *: assigning object to myObj."+actChild.nodeName+"") ;
					myObj[actChild.nodeName]	=	xmlToObj( actChild) ;
				}
			} else {
				_debugL( 0x00000001, "-> actChild: node doesn't have children '"+actChild.nodeName+"', value := '"+actChild.nodeValue+"'") ;
			}
		}
	} else {
		_debugL( 0x00000001, "-> *: _xml.node doesn't have children '"+_xml.nodeName+"'") ;
	}
	dEnd( 1, "wapCommon.js", "*", "xmlToObj( <xml>)") ;
	return myObj ;
}
function	dumpObj( _obj, _pref) {
	dTrace( 3, "wapCommon.js", "*", "dumpObj( <obj>)", "----------------------------------------------------------------------^^^----") ;
	if ( ! _pref) _pref	=	"" ;
	for (var key in _obj) {
		if (_obj.hasOwnProperty(key)) {
			var obj = _obj[key];
			for (var prop in obj) {
				if ( typeof obj[prop] == "object array") {
					dumpObj( obj[prop], _pref + "." + prop + ".") ;
				} else if (obj.hasOwnProperty(prop)) {
					dTrace( 3, "wapCommon.js", "*", "dumpObj()", _pref + prop + " := '" + obj[prop] + "'") ;
				}
			}
		}
	}
	dTrace( 3, "wapCommon.js", "*", "dumpObj( <obj>)", "----------------------------------------------------------------------vvv----") ;
}
function	myInfo() {
	new wapPopup( null, "Session Info", { url: "/api/loadScreen.php?sessionId="+sessionId+"&screen=SessionInfo.php", modal: true}).show() ;
}
