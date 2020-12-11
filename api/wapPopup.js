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
 * wapPopup.js
 * ===============
 *
 * Product id.:
 * Version:
 *
 * Description
 * ===========
 *
 * A wapPopup defines an interface to create a popup windows as an overlay div.
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
var	popupCurrent	=	null ;
function	wapPopup( _owner, _name, _attr) {
	dBegin( 1, "wapPopup.js", "wapPopup", "wapPopup( <_owner>, '"+_name+"', <_attr>)") ;
	var	myCloseButton ;
	this.owner	=	_owner ;
	this.name	=	_name ;
	this.width	=	500 ;
	this.height	=	350 ;
	this.modal	=	false ;
	this.titleBarHeight	=	15 ;
	this.buttonBarHeight	=	30 ;
	this.statusBarHeight	=	15 ;
	this.title	=	"UNNAMED POPUP WINDOW" ;
	this.status	=	"MODAL ... User Input required" ;
	for ( var i in _attr) {
		this[ i]	=	_attr[i] ;
	}
	this.dragging	=	false ;
	/**
	 *
	 */
	this.popupDiv	=	document.createElement( "div") ;
	this.popupDiv.className	=	"popup" ;
	this.popupDiv.style.positon	=	"absolute" ;
	this.popupDiv.style.width	=	this.width ;
	this.popupDiv.style.height	=	this.height ;
	this.popupDiv.style.top	=	((window.innerHeight - parseInt( this.popupDiv.style.height)) / 2).toString() + "px" ;
	this.popupDiv.style.left	=	((window.innerWidth - parseInt( this.popupDiv.style.width)) / 2).toString() + "px" ;
	this.popupDiv.owner	=	this ;
	this.redirectOnClose	=	"" ;
	document.body.appendChild( this.popupDiv) ;
	/**
	 *	add the title bar
	 */
	this.titleDiv	=	document.createElement( "div") ;
	this.titleDiv.className	=	"popupTitle" ;
	this.titleDiv.style.width	=	this.width ;
	this.titleDiv.style.height	=	this.titleBarHeight ;
	this.titleDiv.owner	=	this ;
	this.popupDiv.appendChild( this.titleDiv) ;
	this.titleDiv.addEventListener( "mousedown", function( _evt) {
		_evt.target.setCapture();
		this.dragging	=	true ;
		this.offsX	=	_evt.clientX ;
		this.offsY	=	_evt.clientY ;
	}) ;
	this.titleDiv.addEventListener( "mouseup", function( _evt) {
		this.dragging	=	false ;
	}) ;
	this.titleDiv.addEventListener( "mousemove", function( _evt) {
		if ( this.dragging) {
			var	moveByX	=	_evt.clientX - this.offsX ;
			var	moveByY	=	_evt.clientY - this.offsY ;
			var	newX	=	parseInt( this.owner.popupDiv.style.left) + moveByX ;
			var	newY	=	parseInt( this.owner.popupDiv.style.top) + moveByY ;
			if ( newX > 5 && newX < window.innerWidth && newY > 5 && newY < window.innerHeight) {
				this.owner.popupDiv.style.left	=	newX.toString() + "px" ;
				this.owner.popupDiv.style.top	=	newY.toString() + "px" ;
				this.offsX	=	_evt.clientX ;
				this.offsY	=	_evt.clientY ;
			}
		}
	}) ;
	this.enterKey	=	function( _evt) {
		dTrace( 1, "wapPopup.php", "wapPopup", "enterKey( ...)", "key pressed ..." + _evt.keyCode.toString()) ;
		if ( _evt.keyCode == 27) {
			dTrace( 1, "wapPopup.php", "wapPopup", "enterKey( ...)", "calling this.close") ;
			this.close() ;
		}
		return true ;
	} ;
	/**
	 *
	 */
	this.contentDiv	=	document.createElement( "div") ;
	this.contentDiv.className	=	"popupContent" ;
    this.contentDiv.style.width	=	this.width - 20 ;
    this.contentDiv.style.height	=	this.height - this.titleBarHeight - this.buttonBarHeight - this.statusBarHeight - 35 ;
	this.popupDiv.appendChild( this.contentDiv) ;
	/**
	 *	add the button bar
	 */
	this.buttonDiv	=	document.createElement( "div") ;
	this.buttonDiv.className	=	"popupButtonArea" ;
    this.buttonDiv.style.width	=	this.width ;
    this.buttonDiv.style.height	=	this.buttonBarHeight ;
	this.popupDiv.appendChild( this.buttonDiv) ;
	/**
	 *	add the title bar
	 */
	this.statusDiv	=	document.createElement( "div") ;
	this.statusDiv.className	=	"popupStatus" ;
    this.statusDiv.style.width	=	this.width ;
    this.statusDiv.style.height	=	this.statusBarHeight ;
	this.popupDiv.appendChild( this.statusDiv) ;
	/**
	 *	add the title bar
	 */
	this.resizeDiv	=	document.createElement( "div") ;
	this.resizeDiv.className	=	"popupResize" ;
	this.resizeDiv.style.width	=	"15px" ;
	this.resizeDiv.style.height	=	"15px" ;
	this.resizeDiv.style.right	=	"0px" ;
	this.resizeDiv.style.bottom	=	"0px" ;
	this.resizeDiv.owner	=	this ;
	this.statusDiv.appendChild( this.resizeDiv) ;
	this.resizeDiv.addEventListener( "mousedown", function( _evt) {
		_evt.target.setCapture();
		this.dragging	=	true ;
		this.offsX	=	_evt.clientX ;
		this.offsY	=	_evt.clientY ;
	}) ;
	this.resizeDiv.addEventListener( "mouseup", function( _evt) {
		this.dragging	=	false ;
	}) ;
	this.resizeDiv.addEventListener( "mousemove", function( _evt) {
		if ( this.dragging) {
			var	resizeByX	=	_evt.clientX - this.offsX ;
			var	resizeByY	=	_evt.clientY - this.offsY ;
			var	newWidth	=	parseInt( this.owner.popupDiv.style.width) + resizeByX ;
			var	newHeight	=	parseInt( this.owner.popupDiv.style.height) + resizeByY ;
			this.owner.popupDiv.style.width	=	newWidth.toString() + "px" ;
			this.owner.popupDiv.style.height	=	newHeight.toString() + "px" ;
			this.owner.titleDiv.style.width	=	this.owner.popupDiv.style.width ;
			this.owner.buttonDiv.style.width	=	this.owner.popupDiv.style.width ;
			this.owner.contentDiv.style.width	=	( newWidth - 20).toString() + "px" ;
			this.owner.contentDiv.style.height	=	( newHeight - this.owner.titleBarHeight - this.owner.buttonBarHeight - this.owner.statusBarHeight - 35).toString() + "px" ;
			this.owner.statusDiv.style.width	=	this.owner.popupDiv.style.width ;
			this.offsX	=	_evt.clientX ;
			this.offsY	=	_evt.clientY ;
		}
	}) ;
	/**
	 *
	 */
	if ( this.modal) {
		this.hiderDiv	=	document.createElement( "div") ;
		this.hiderDiv.className	=	"popupModal" ;
		document.body.appendChild( this.hiderDiv) ;
	} else {
		this.hiderDiv   =   null ;
	}
	/**
	 *
	 */
	this.titleDiv.innerHTML	=	this.title ;
	var myText	=	document.createTextNode( this.status) ;
	this.statusDiv.appendChild( myText) ;
	if ( this.displayText) {
		this.contentDiv.innerHTML	=	this.displayText ;
		this._addButtons() ;
	} else if ( this.url) {
		/**
		 *
		 */
		var	myRequest	=	new XMLHttpRequest() ;
		myRequest.open( "GET", this.url) ;
		myRequest.owner	=	this ;
		myRequest.addEventListener( 'load', function() {
			dTrace( 1, "index.php", "*", "activate( ...)", "loaded data ...", "") ;
			this.owner.contentDiv.innerHTML	=	this.responseText ;
			this.owner._addButtons() ;
			this.owner._onScreenLoaded() ;
		}) ;
		myRequest.send() ;
	} ;
	this.placeButtons	=	function() {
//		this.myCloseButton.style.left	=	"100px" ;
	} ;
	this.placeButtons() ;
	/**
	 *
	 */
	this.open	=	function() {
		popupCurrent	=	this ;			// make this screen the current screen
		this.popupDiv.style.zIndex	=	100 ;
		if ( this.hiderDiv)
			this.hiderDiv.style.zIndex	=	99 ;
		this.popupDiv.focus() ;
	}
	this.show	=	function() {
		popupCurrent	=	this ;			// make this screen the current screen
		this.popupDiv.style.zIndex	=	100 ;
		if ( this.hiderDiv)
			this.hiderDiv.style.zIndex	=	9 ;
	}
	this.hide	=	function() {
		popupCurrent	=	null ;
		this.close() ;
	}
	this.close	=	function() {
		dBegin( 1, "index.php", "*", "close( <_node>)") ;
		popupCurrent	=	null ;
		if ( this.hiderDiv !== null) {
			this.remove( this.hiderDiv) ;
		}
		var	myRDs	=	document.getElementsByClassName( "rd-container") ;
		for ( var i=0 ; i<myRDs.length ; i++) {
			myRDs[i].parentNode.removeChild( myRDs[i]) ;
		}
		this.remove( this.popupDiv) ;
		if ( this.redirectOnClose != "") {
			document.location.href	=	this.redirectOnClose ;
		}
		dEnd( 1, "index.php", "*", "close( <_node>)") ;
	}
	/**
	 *
	 */
	this.destroyRecursive	=	function() {
	}
	/**
	 *
	 */
	this.remove	=	function( _node) {
		dBegin( 1, "index.php", "*", "remove( <_node>)") ;
		popupCurrent	=	null ;
		_node.parentNode.removeChild( _node) ;
		dEnd( 1, "index.php", "*", "remove( <_node>)") ;
	}
	this._onScreenLoaded	=	function() {
		dBegin( 1, "index.php", "*", "remove( <_node>)") ;
		if ( this.owner) {
			if ( this.owner.onScreenLoaded) {
				this.owner.onScreenLoaded() ;
			}
		}
		dEnd( 1, "index.php", "*", "remove( <_node>)") ;
	}
    dEnd( 1, "wapPopup.js", "wapPopup", "wapPopup( <_owner>, '"+_name+"', <_attr>)") ;
}
/**
 *	wapPopup.onClose
 *
 * If the owner has an onClose-calback defined, call it. In this case we will NOT close
 * this dialog popup.
 */
wapPopup.prototype.onClose	=	function() {
	dBegin( 1, "wapPopup.js", "wapPopup", "prototype.onClose()") ;
	if ( this.owner) {
		if ( this.owner.onClose) {
			this.owner.onClose() ;
		} else {
			this.close() ;
		}
	} else {
		this.close() ;
	}
	dEnd( 1, "wapPopup.js", "wapPopup", "prototype.onClose()") ;
}
wapPopup.prototype.onCancel	=	function() {
	dBegin( 1, "wapPopup.js", "wapPopup", "prototype.onCancel()") ;
	if ( this.owner) {
		if ( this.owner.onCancel) {
			this.owner.onCancel() ;
		} else {
			this.close() ;
		}
	} else {
		this.close() ;
	}
	dEnd( 1, "wapPopup.js", "wapPopup", "prototype.onCancel()") ;
}
wapPopup.prototype.onGo	=	function() {
	dBegin( 1, "wapPopup.js", "wapPopup", "prototype.onGo()") ;
	if ( this.owner) {
		if ( this.owner.onGo) {
			this.owner.onGo() ;
		} else {
			this.close() ;
		}
	} else {
		this.close() ;
	}
	dEnd( 1, "wapPopup.js", "wapPopup", "prototype.onGo()") ;
}
/**
 *
 */
wapPopup.prototype._addButtons 	=	function() {
	dBegin( 1, "wapPopup.js", "wapPopup", "prototype._addButtons()") ;
	/**
	 *
	 */
	var	myCloseButton	=	document.createElement( "div") ;
	myCloseButton.className	=	"popupButton popupClose" ;
	myCloseButton.innerHTML	=	"Close" ;
	myCloseButton.owner	=	this ;
	myCloseButton.onclick	=	function() {
									if ( this.owner.onClose) {
										this.owner.onClose() ;
									}
								} ;
	this.buttonDiv.appendChild( myCloseButton) ;
	/**
	 *
	 */
	var	confirmButton	=	document.createElement( "div") ;
	confirmButton.className	=	"popupButton popupGo" ;
	confirmButton.innerHTML	=	"Go" ;
	confirmButton.owner	=	this ;
	confirmButton.onclick	=	function() {
									if ( this.owner.onGo) {
										this.owner.onGo() ;
									}
								} ;
	this.buttonDiv.appendChild( confirmButton) ;
	/**
	 *
	 */
	var	abortButton	=	document.createElement( "div") ;
	abortButton.className	=	"popupButton popupAbort" ;
	abortButton.innerHTML	=	"Cancel" ;
	abortButton.owner	=	this ;
	abortButton.onclick	=	function() {
								if ( this.owner.onCancel) {
									this.owner.onCancel() ;
								}
							} ;
	this.buttonDiv.appendChild( abortButton) ;
	dEnd( 1, "wapPopup.js", "wapPopup", "prototype._addButtons()") ;
}
