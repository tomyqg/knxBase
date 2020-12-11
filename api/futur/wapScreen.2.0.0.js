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
 * wapScreen2
 *
 * this is the base object for any screen which needs to be displayed in the application.
 * it provides methods for sending requests to the server, ie.
 * qDispatch	-	quick Dispatch, needs dialog false/true and function to be executed on remote side
 * sDispatch	-	short Dispatch, needs dialog false/true, function to be executed on remote side and
 * 					form to be transmitted
 * dispatch			normal Dispatch, needs all as stated above plus _key, _id and _val
 */

/**
 *
 * @param _dialogName	name of the screen to be created
 * @return
 */

/**
 * @param	_screenName
 * @returns {wapScreen}
 */
	$.widget( "wap.wapScreen2", {
		options:	{
			func:			"",
			itemId:			-1,
			title:			"no title provided",
			debugFrames:	true,
			parentDS:		null,
			parentScreen:	null
		},
		_create:	function() {
			this.keyForm	=	$( "#" + this.element[0].id).find( ".wapKeyForm")[0] ;
			if ( this.keyForm)	console.log( "keyForm gefunden ...") ;
			this.keyField	=	$( "#" + this.element[0].id).find( ".wapKeyField")[0]
			if ( this.keyField)	console.log( "keyField gefunden ...") ;
			this.keyField.val( "12345") ;
			this.dataSource	=	new wapDataSource( this, {
												object:		this.element[0].dataset.wapCoreObject
											,	objectKey:	this.element[0].dataset.wapCoreObjectKey
								}) ;		// dataSource for display
			var	button = this.element.find( ".prevObject")[0] ;
			button.dataset.parentScreen	=	this ;
			this.element[0].find( ".prevObject").click( { parent: this}, function ( event) {
				alert( "clicked ...") ;
				var actParent	=	event.data.parent ;
				actParent.dataSource.getPrev( actParent.keyField.val()) ;
			}) ;
			/**
			 *	lookup wapTabContainer
			 *	IF there is one
			 *
			 */
			this.mainTabContainer	=	this.element[0].find( ".wapTabContainer")[0] ;
			if ( this.mainTabContainer) {
				console.log( "keyField gefunden ...") ;
				$( "#"+this.element[0],id).find( ".wapTabContainer").find( ".wapTabPage").removeClass( "active") ;
				$( "#"+this.element[0],id).find( "#" + this.mainTabContainer.dataset.wapActiveTabOnLoad).addClass( "active") ;
			}
		},
		showData:	function() {
			dTrace( 1, "wapScreen.2.0.0.js", "*", "$.wapScreen2( ...)", "showData ... ") ;
		},
		prev:	function() {
			alert( "getPrev ...") ;
			this.dataSource.getPrev() ;
		}
	}) ;

function	showScreen2( _screenName) {
	$( ".contentHoldingDiv").css( "display", "none") ;						// hide all screens
	dTrace( 1, "wapScreen.2.0.0.js", "*", "showScreen2( ...)", "showing ... " + _screenName) ;
	var	screenDiv	=	$( "#" + _screenName) ;
	var isLoaded	=	$( "#" + _screenName).data( "wapIsLoaded") ;
	if ( isLoaded) {
		dTrace( 1, "wapScreen.2.0.0.js", "*", "showScreen2( ...)", "showing ... " + _screenName + ", already loaded ...") ;
//		screenDiv.css( "display", "block") ;
//		screenDiv.wapScreen2( { func: "show"}) ;
		$( "#" + _screenName).show() ;
	} else {
		dTrace( 1, "wapScreen.2.0.0.js", "*", "showScreen2( ...)", "showing ... " + _screenName + ", not yet loaded ...") ;
		var	myScreenName	=	"" ;
		var	moduleDir	=	$( "#" + _screenName).data( "wapModuleDir")
		var	screenDir	=	$( "#" + _screenName).data( "wapScreenDir")
		myScreenName	+=	moduleDir ? moduleDir + "/" : "" ;
		myScreenName	+=	screenDir ? screenDir + "/" : "" ;
		myScreenName	+=	$( "#" + _screenName).data( "wapScreen") ;
		var	myRequest	=	new XMLHttpRequest() ;
		myRequest.open( "GET", "/api/loadScreen.php?sessionId="+sessionId+"&screen="+myScreenName) ;
		myRequest.addEventListener( 'load', function() {
			screenDiv.html( this.responseText) ;
//			screenDiv.css( "display", "block") ;
			$( "#" + _screenName).show() ;
			/**
			 *
			 */
			var fileref	=	document.createElement('script') ;
			fileref.setAttribute( "type", "text/javascript") ;
			fileref.screenName	=	myScreenName ;
			fileref.setAttribute( "src", "/api/loadScript.php?sessionId="+sessionId+"&script="+myScreenName.replace( "xml", "js").replace( "html", "js")) ;
			if (typeof fileref != "undefined")
				document.getElementsByTagName("head")[0].appendChild(fileref) ;
			$( "#" + _screenName).data( "wapIsLoaded", "true")
		}) ;
		dTrace( 1, "wapScreen.2.0.0.js", "*", "showScreen2( ...)", "submitting request for javascript ...", "") ;
		myRequest.send() ;
	}
}
