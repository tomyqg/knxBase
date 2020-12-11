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
 * wapBusy.js
 * ===============
 *
 * Product id.:
 * Version:
 *
 * Description
 * ===========
 *
 * A wapBusy defines an interface to create a popup windows as an overlay div.
 * Code is fully self-contained.
 *
 * Mandatory attributes for the construction of a select are:
 * 	_owner		reference to the owning object of this select
 * 	_name		name of this popup window
 * 	_attr		array with attributes as follows:
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2015-03-dd	PA1		khw		created this class;
 * 2015-09-10	PA2		khw		added move and resize functions;
 *
 * ToDo
 *
 * Date			what
 * ----------------------------------------------------------------------------
 * 2013-mm-dd
 *
 * @param	string	_owner			- reference to the owner object of this dataGrid
 * @param	string	_name			- name (literal) of the dataGrid (must be unique within the application)
 * @param	array	_attr			- additional attributes of the wagGrid object to be instantiated
 * @return
 */
var	busyDialog	=	null ;
function	wapBusy( _attr) {
	/**
	 *
	 */
	if ( busyDialog === null) {
		for ( var i in _attr) {
			this[ i]	=	_attr[i] ;
		}
		busyDialog	=	this ;
		this.refCounter	=	0 ;
		this.popupDiv	=	document.createElement( "div") ;
		this.popupDiv.className	=	"busy" ;
		this.popupDiv.style.positon	=	"absolute" ;
		this.popupDiv.style.width	=	100 ;
		this.popupDiv.style.height	=	100 ;
		document.body.appendChild( this.popupDiv) ;
		this.contentDiv	=	document.createElement( "div") ;
		this.contentDiv.className	=	"busyContent" ;
		this.contentDiv.style.width	=	this.width - 20 ;
		this.contentDiv.style.height	=	this.height - 20 ;
		this.popupDiv.appendChild( this.contentDiv) ;
		/**
		 *
		 */
		if ( this.url) {
			/**
			 *
			 */
			var	myRequest	=	new XMLHttpRequest() ;
			myRequest.open( "GET", this.url) ;
			myRequest.owner	=	this ;
			myRequest.addEventListener( 'load', function() {
				this.owner.contentDiv.innerHTML	=	this.responseText ;
				this.owner._onScreenLoaded() ;
			}) ;
			myRequest.send() ;
		} ;
		this.show	=	function() {
			var	myDiv	=	document.getElementsByClassName( "ic-Spin-cycle--classic") ;
			this.popupDiv.style.top	=	((window.innerHeight - parseInt( this.popupDiv.style.height)) / 2).toString() + "px" ;
			this.popupDiv.style.left	=	((window.innerWidth - parseInt( this.popupDiv.style.width)) / 2).toString() + "px" ;
			this.refCounter++ ;
			this.popupDiv.style.zIndex	=	25 ;
		}
		this.hide	=	function() {
			this.refCounter-- ;
			if ( this.refCounter == 0) {
				this.popupDiv.style.zIndex	=	-1 ;
			}
		}
		this._onScreenLoaded	=	function() {
			if ( this.owner) {
				if ( this.owner.onScreenLoaded) {
					this.owner.onScreenLoaded() ;
				}
			}
		}
	} else {
	}
	busyDialog.show() ;
}
