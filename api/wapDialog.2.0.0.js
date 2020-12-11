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
 * wapDialog
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
 * @param _dialogName
 * @returns {wapDialog}
 */
;( function ( $) {
	$.fn.wapDialog	=	function( arg, callback) {
		var	options	=	$.extend({}, $.fn.wapDialog.defaults,
 										arg,
										{ callback:callback}) ;
		return this.each( function() {
			if ( ! this.initialized) {
//				alert( options.screenName + " ... " + options.gridName) ;
				$(this).data( "info", { screenName: options.screenName, gridName: options.gridName}).dialog( {
					autoOpen:	false,
					title:		options.title,
					width:		"900px",
					minHeight:	"900px",
					modal:		true,
					buttons:	{
									"Abbrechen" :	function() {
															$( this).dialog( "close") ;
													},
									"Speichern" :	function() {
															wapScreens[ $( this).data( "info").screenName].onSave() ;
															wapGrids[ $( this).data( "info").gridName]._onRefresh()
															$( this).dialog( "close") ;
													}
								}
				}) ;
				this.initialized	=	true ;
			}
			switch ( options.func) {
			case	"open"	:
				$( this).dialog( "open") ;
				break ;
			}
		}) ;
	} ;
	$.fn.wapDialog.defaults	=	{
		func:			"",
		itemId:			-1,
		title:			"no title provided",
		debugFrames:	true,
		editorName:		"",
		screenName:		"",
		gridName:		""
	} ;
})(jQuery) ;
