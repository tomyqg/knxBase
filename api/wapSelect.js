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
 * wapSelectXML.js
 * ===============
 *
 * Product id.:
 * Version:
 *
 * Description
 * ===========
 *
 * A wapSelect defines an interface to create dynamic, i.e. dataSource driven, selects in an html document.
 *
 * Mandatory attributes for the construction of a select are:
 * 	_owner		reference to the owning object of this select
 * 	_name		name of this select
 * 	_attr		array with attributes as follows:
 * 		object				name of the class/object which this select handles
 * 		onDataSourceLoaded	method to be called upon dataSource loaded
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-mm-dd	PA1		khw		created this class;
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
function	wapSelectXML( _owner, _name, _attr) {
	dBegin( 1, "wapddGridXML.js", "wapSelect", "wapSelect( <_owner>, '"+_name+"', <_attr>)") ;
	this.owner	=	_owner ;
	this.name	=	_name ;
	this.cond	=	"" ;
	this.order	=	"" ;
	/**
	 * get attributes from parameter-object
	 * mand.:	object				e.g. XXXXXXXXContact
	 * 			module
	 * 			xmlTableName		name of the html-table
	 * opt.:	-
	 */
	for ( var i in _attr) {
		this[ i]	=	_attr[i] ;
	}
	/**
	 * create the dataSource for this grid
	 */
	this.dataSource	=	new wapDataSource( this, {
								object: 		this.object
							,	fncGet: 		"getList"
							,	onDataSourceLoaded:	function( _owner, _data) {
														dBegin( 102, "wapSelectXML{wapGrid.js}", "wapSelect", "onDataSourceLoaded( <_owner>, <_data>)") ;
														this.show() ;
														dEnd( 102, "wapSelectXML{wapGrid.js}", "wapSelect", "onDataSourceLoaded( <_owner>, <_data>)") ;
													}
						}) ;
	/**
	 * determine the class of the object to be displayed in the grid
	 */
	this.dataItemName	=	this.object ;
	/**
	 * clear()
	 * =======
	 *
	 * clears the data  portion of the table
	 */
	dTrace( 2, "wapSelectXML.js", "wapSelect", "wapSelect( <...>)", "defining clear()") ;
	this.clear	=	function() {
		dTrace( 3, "wapSelectXML.js", "wapSelect", "wapSelect( <...>)", "clear(): begin") ;
		/**
		 * find the table and delete all rows marked as 'eissType' = 'data'
		 */
		while ( this.selectNode.options.length > 0) {
			this.selectNode.remove(0);
		}
		dTrace( 3, "wapSelectXML.js", "wapSelect", "clear( <...>)", "clear(): end") ;
	} ;
	/**
	 * build creates a head section for an arbitrary result table coming as an XML response from the
	 * server
	 */
	dTrace( 2, "wapSelectXML.js", "wapSelect", "wapSelect( <...>)", "defining refresh()") ;
	this.refresh	=	function() {
		dBegin( 1, "wapSelectXML.js", "wapSelectXML", "wapSelect( <...>)", "show( ..., '"+this.xmlTableName+"', ...)") ;
		/**
		 * add the data to the table
		 */
		this.clear() ;
		this.dataSource.firstPage( null, "&cond="+this.cond+"&order="+this.order) ;
		dEnd( 1, "wapSelectXML.js", "wapSelectXML", "show( ..., '"+this.xmlTableName+"', ...)") ;
	} ;
	/**
	 * build creates a head section for an arbitrary result table coming as an XML response from the
	 * server
	 */
	dTrace( 2, "wapSelectXML.js", "wapSelect", "wapSelect( <...>)", "defining show()") ;
	this.show	=	function() {
		var	dataTable ;
		var	dataRow ;
		dBegin( 1, "wapSelectXML.js", "wapSelectXML", "wapSelect( <...>)", "show( ..., '"+this.xmlTableName+"', ...)") ;
		/**
		 * add the data to the table
		 */
		this.clear() ;
		/**
		 * add the data to the table
		 */
		dTrace( 2, "wapSelectXML.js", "wapSelectXML", "show()", "------XXXXXXXX--------------------------------") ;
//		this.dataSource.dumpObj( this.dataSource.objects) ;
		dataTable	=	this.dataSource.xmlData.getElementsByTagName( this.dataItemName) ;
		for ( var il0=0 ; il0 < dataTable.length ; il0++) {
			_debugL( 11, "wapSelectXML.js::wapSelectXML::show(): working on line # "+il0.toString()+", "+this.dataItemName+"\n") ;
			dataRow	=	dataTable[il0] ;
			var newOption	=	document.createElement( "option") ;
			newOption.setAttribute( "value", dataRow.getElementsByTagName( this.key)[0].childNodes[0].nodeValue) ;
			var	myText	=	document.createTextNode( dataRow.getElementsByTagName( this.value)[0].childNodes[0].nodeValue) ;
			newOption.appendChild( myText) ;
			this.selectNode.appendChild( newOption) ;
		}
		if ( this.initialOption) {
			this.setOption( this.initialOption) ;
		}
		dEnd( 1, "wapSelectXML.js", "wapSelectXML", "show( ..., '"+this.xmlTableName+"', ...)") ;
	} ;
	dEnd( 1, "wapddGridXML.js", "wapSelect", "wapSelect( <_owner>, '"+_name+"', <_attr>)") ;
}
wapSelectXML.prototype.setOption	=	function( _value) {
	var	ret	=	-1 ;
	dBegin( 102, "wapSelectXML.js", "prototype", "setOption( <" + _value + ">)") ;
	for ( var i=0 ; i < this.selectNode.options.length ; i++) {
		if ( this.selectNode.options[i].value == _value) {
			this.selectNode.selectedIndex	=	i ;
			ret	=	i ;
		}
	}
	dEnd( 102, "wapSelectXML.js", "prototype", "setOption( <_value>)") ;
	return ret ;
}
wapSelectXML.prototype.getOption	=	function() {
	dBegin( 102, "wapSelectXML.js", "prototype", "setOption()") ;
	var	myData	=	this.selectNode.options[this.selectNode.selectedIndex].value ;
	dEnd( 102, "wapSelectXML.js", "prototype", "setOption()") ;
	return myData ;
}
