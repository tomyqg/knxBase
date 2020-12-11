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
 * Class:	selector
 *
 * Displays a selector to select an item from any database table
 *
 * @param _screen	Screen to which this editor belongs
 * @param _pkg		Package where the selector resides, e.g. ModBase, ModCalc, ...
 * @param _script	Name of the php script with the selector mask
 * @param _obj		Class of the object to be selected
 *
 * The following
 *
 * The following signals are emitted:
 * onSelectById(..)	Emitted when select button is clicked
 * onInput(..)	Emitted when ENTER is pressed on an input field
 * onClose()	Emitted when dialog is closed
 *
 * The following signals are processes internally:
 *
 */
/**
 * class definition for selector
 * @param string $_screen	owner of this selector
 * @param string $_name		name of this selector
 * @return void
 */
var	objSelectors	=	new Object() ;
var	selectorCurrent ;
function	wapSelector( _owner, _name, _attr) {
	dBegin( 1, "wapSelector.js", "wapSelector", "__constructor( <_owner>, '"+_name+"', <_attr>)") ;
	this.owner =	_owner ;
	this.name	=	_name ;
	this.dialog	=	null ; 					// ref. to the dijit.dialog
	this.form	=	null ;
//	objSelectors[ _name]	=	this ;		// assign the "static" access for this selector
	/**
	 * get attributes from parameter-object
	 */
	this.parent	=	null ;
	for ( var i in _attr) {
		this[ i]	=	_attr[i] ;
	}
	/**
	 * create the grid to display the customer list on the first "Overview" tab
	 */
	dTrace( 102, "wapSelector.js", "wapSelector", "*", "creating gridSelector") ;
	if ( wapGrids[this.name]) {
		this.gridSelector	=	wapGrids[this.name] ;
	} else {
		this.gridSelector	=	new wapGrid( this, this.name, {
												object:			this.objectClass
											,	module:			this.module
											,	screen:			this.screen
											,	parent:		this.parent
// the following line was commented out. Why??
											,	formFilterName:	this.formFilterName
												/**
												 * wapGrid::onDataSourceLoaded
												 * called from:	wapGrid::dataSource::_onLoaded
												 * calls to:	wapGrid::show
												 */
											,	onDataSourceLoaded:	function( _owner, _data) {
																	dBegin( 102, "wapSelector.js", "wapSelector", "onDataSourceLoaded( <_owner>, <_data>)") ;
																	this.show() ;
																	dEnd( 102, "wapSelector.js", "wapSelector", "onDataSourceLoaded( <_owner>, <_data>)") ;
																}
												/**
												 * wapGrid::onSelectById
												 * called from:	wapGrid::onSelectById
												 * calls to:	owner::onSelectById
												 */
											,	onSelectById:		function( _owner, _id) {
																	dBegin( 102, "wapSelector.js", "wapSelector", "onSelectById( <_owner>, <_data>)") ;
																	this.owner.onSelectById( _id) ;
																	dEnd( 102, "wapSelector.js", "wapSelector", "onSelectById( <_owner>, <_data>)") ;
																}
										}) ;
	}
	/**
	 *
	 */
	dTrace( 102, "wapSelector.js", "wapSelector", "*", "__constructor( <...>)", "defining this.show") ;
	this.startSelect = function( _key, _id, _val) {
		dTrace( 1, "wapSelector.js", "wapSelector", "startSelect", "this.objectClass := '" + this.objectClass + "'");
		/**
		 *
		 */
		this.gridSelector.owner	=	this ;
		selectorCurrent		=	this ;
		this.href	=	"/api/loadScreen.php?" +
						"sessionId="+sessionId + "&" +
						"screen=" + (this.moduleName ? this.moduleName + "/" : "") + (this.subModuleName ? this.subModuleName + "/" : "") + this.selectorName + ".xml" ;
		if (this.dialog != null)
			this.dialog.destroyRecursive();
		this.dialog	=	null ;
		this.dialog =	new wapPopup( this, "Editor", {url: this.href, width: 800, height: 600}) ;
		dTrace( 1, "wapSelector.js", "wapSelector", "startSelect()", "about to show") ;
		this.dialog.show() ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapSelector.js", "wapSelector", "*", "__constructor( <...>)", "defining this.onScreenLoaded() \n");
	this.onScreenLoaded = function() {
		dBegin( 1, "wapSelector.js", "wapSelector", "onScreenLoaded( <...>)") ;
		this.gridSelector._onRefresh() ;
		dEnd( 1, "wapSelector.js", "wapSelector", "onScreenLoaded( <...>) (pre-mature)") ;
	};
	/**
	 *
	 */
	dTrace( 102, "wapSelector.js", "wapSelector", "__constructor( <...>)", "defining this.selected") ;
	this.onSelectById	=	function( _id) {
		dBegin( 2, "wapSelector.js", "wapSelector", "selected(" + _id.toString() + ")") ;
		if ( this.owner.onSelectById != null) {
			this.dialog.close();
			this.owner.onSelectById( _id) ;
		} else {
			this.dialog.close();
		}
		this.dialog	=	null ;
		dEnd( 2, "wapSelector.js", "wapSelector", "selected( <_id>)") ;
	} ;
	/**
	 * will be called when the dialog mask has finished loading
	 */
	dTrace( 102, "wapSelector.js", "wapSelector", "__constructor( <...>)", "defining this.refresh") ;
	this.refresh = function() {
		dBegin( 103, "wapSelector.js", "wapSelector", "refresh()") ;
		dEnd( 103, "wapSelector.js", "wapSelector", "refresh()") ;
	} ;
	/**
	 *
	 */
	dEnd( 1, "wapSelector.js", "wapSelector", "__constructor( <_pkg>, '"+_name+"', <_attr>)") ;
}
