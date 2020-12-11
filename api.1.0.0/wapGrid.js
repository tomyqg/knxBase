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
 * wapGridXML.js
 * =============
 *
 * Product id.:
 * Version:
 *
 * Description
 * ===========
 *
 * A wapGrid defines a table for the display of arbitrary data. The content of the table is defined as
 * per HTML content with wap specific extensions. These wap specific extensions define special attributes
 * of the values displayed as well as functions related to the displayed data.
 * A wapGrid can be either of two types:
 *
 * 	-	either a grid of independent objects or
 * 	-	a grid of dependent objects
 *
 * A grid of independent objects does not have a parent datasource (parentDS := null).
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
 * @param	string	_parent			- reference to the parent object of this dataGrid
 * @param	string	_name			- name (literal) of the dataGrid (must be unique within the application)
 * @param	array	_attr			- additional attributes of the wagGrid object to be instantiated
 * @return
 */
var	wapGrids	=	new Object() ;
function	wapGrid( _parent, _name, _attr) {
	dBegin( 1, "wapGridXML.js", "wapGrid", "__constructor( <_parent>, '"+_name+"', <_attr>)") ;
	this.parent	=	_parent ;
	wapGrids[ _name]	=	this ;
	this.name	=	_name ;
	/**
	 * set needed defaults
	 */
	this.formTop	=	"form" + _name + "Top" ;
	/**
	 *
	 */
	this.formBot	=	"" ;
	this.filter	=	"" ;
	this.phpHandler	=	"" ;
	this.phpGetCall	=	"L" ;
	this.addEditor	=	null ;
	this.reverse	=	false ;
	this.depObject	=	"" ;
	this.colInfo	=	new Object() ;
	this.moduleName	=	"" ;
	this.subModuleName	=	"" ;
	this.idKey	=	"Id" ;
	/**
	 * get attributes from parameter-object
	 * mand.:	object				e.g. XXXXXXXXContact
	 * 			module
	 * opt.:	-
	 */
	this.parentDS	=	null ;		//  assume for now we are a grid for independent objects
	this.tdUnderEdit	=	null ;
	for ( var i in _attr) {
		this[ i]	=	_attr[i] ;
	}
	if ( this.parentDS !== null) {
		this.primObj	=	this.parentDS.object ;
	}
	this.xmlTableName	=	"table" + _name + "Data" ;
	/**
	 *	IF a gridDivName has been provided
	 *		initialize this object from the data in the HTML
	 */
	dTrace( 3, "wapGridXML.js", "wapGrid", "__constructor()", "trying to find <div id='" + this.name + "'; will init from there ...") ;
	var	myDiv	=	document.getElementById( this.name) ;
	/**		create the dataSource for this grid		*/
	this.dataSource	=	new wapDataSource( this, {
							object: 		this.object
						,	fncGet: 		"getList"
						,	parentDS:		this.parentDS
						}) ;
	/**		determine the class of the object to be displayed in the grid		*/
	if ( this.parentDS !== null) {
		this.dataItemName	=	this.parentDS.object ;
	} else {
		this.dataItemName	=	this.object ;
	}
	/**
	 * check if an item editor exists, if not, create one
	 */
	dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>).................", _attr["formTop"]) ;
	if ( this.editorName) {
		var	edtName	=	this.editorName ;
		if ( itemEditors[edtName]) {
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "itemEditor['"+edtName+"'] exists") ;
		} else {
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "itemEditor['"+edtName+"'] does not exist") ;
			itemEditors[edtName]	=	new wapEditor( this, edtName, {
														module:			this.module
													,	moduleName: 	this.moduleName
													,	subModuleName:	this.subModuleName
													,	screen:			this.screen
													,	editorName:		this.editorName
													,	editorFormName:	this.editorFormName
													,	parentDS:		this.parentDS
													,	objectClass:	this.object
													,	onAddDone:		null
													,	onUpdDone:		null
											}) ;
		}
		if ( itemEditors[edtName]) {
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "itemEditor['"+edtName+"'] exists") ;
			this.dataItemEditor	=	itemEditors[edtName] ;
		} else {
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "itemEditor['"+edtName+"'] does not exist") ;
		}
	} else {
		dTrace( 3, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "_dataItemName := null") ;
	}
	/**
	 * clear()
	 * =======
	 *
	 * clears the data  portion of the table
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid()", "defining clear()") ;
	this.clear	=	function() {
		dBegin( 3, "wapGridXML.js", "wapGrid", "wapGrid()", "clear()") ;
		/**
		 *
		 */
		/**
		 * find the table and delete all rows marked as 'wapRowType' = 'data'
		 */
		var	domTable	=	document.getElementById( this.xmlTableName) ;	// get <table></table>
		var	domTableHeader	=	domTable.tHead ;							// get <thead></thead>
		var	domTableBody	=	domTable.tBodies[0] ;						// get <tbody></tbody>
		if ( domTableHeader && domTableBody) {
			dTrace( 4, "wapGridXML.js", "wapGrid", "clear()", "found html-table-header, has...: " + domTableHeader.rows.length.toString() + " rows") ;
			dTrace( 4, "wapGridXML.js", "wapGrid", "clear()", "html-table-body, id............: " + domTableHeader.getAttribute( "id")) ;
			dTrace( 4, "wapGridXML.js", "wapGrid", "clear()", "found html-table-body, has.....: " + domTableBody.rows.length.toString() + " rows") ;
			/**
			 * delete existing data rows
			 */
			dTrace( 4, "wapGridXML.js", "wapGrid", "clear()", "will delete existing rows") ;
			var	rowCount	=	domTableBody.rows.length ;
			for ( var i=rowCount - 1 ; i >= 0 ; i--) {
				domTableBody.deleteRow( i) ;
			}
		} else {
			dTrace( 4, "wapGridXML.js", "wapGrid", "clear()", "could not find html-table-body") ;
		}
		dEnd( 3, "wapGridXML.js", "wapGrid", "clear()") ;
	} ;
	/**
	 * buildTHEAD creates a head section for an arbitrary result table coming as an XML response from the
	 * server
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining build()") ;
	this.buildTHEAD	=	function( _response) {
		dTrace( 3, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "buildTHEAD( ..., '"+this.xmlTableName+"', ...): begin") ;
		/**
		 * find the table and delete all rows marked as 'wapRowType' = 'data'
		 */
		domTable	=	document.getElementById( this.xmlTableName) ;	// get <table></table>
		domTableHeader	=	domTable.tHead ;							// get <thead></thead>
		domTableBody	=	domTable.tBodies[0] ;						// get <tbody></tbody>
		if ( domTableHeader && domTableBody) {
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "found html-table-header, has...: " + domTableHeader.rows.length.toString() + " rows") ;
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "html-table-body, id............: " + domTableHeader.getAttribute( "id") + " rows") ;
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "found html-table-body, has.....: " + domTableBody.rows.length.toString() + " rows") ;
			/**
			 * delete existing data rows
			 */
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "wapGridXML.js::wapGridXML::buildTHEAD(): will delete existing rows") ;
			rowCount	=	domTableBody.rows.length ;
			for ( var i=rowCount - 1 ; i >= 0 ; i--) {
				domRow	=	domTableBody.rows[i] ;
				domTableBody.deleteRow( i) ;
			}
			/**
			 * add the data to the table
			 */
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "wapGridXML.js::wapGridXML::buildTHEAD(): will add new data to the table") ;
			dataTable	=	_response.getElementsByTagName( this.dataItemName) ;	// SPECIFIC
			if ( dataTable) {
				dTrace( 5, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "dataTable is valid") ;
				dTrace( 5, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "looking up first data item '"+this.dataItemName+"'") ;
				dataRow	=	_response.getElementsByTagName( this.dataItemName)[0] ;	// SPECIFIC
				var newTR	=	document.createElement("tr") ;
				newTR.setAttribute( "data-wap-row-type", "header");
				for ( var myCol = dataRow.firstChild ; myCol !== null ; myCol = myCol.nextSibling) {
					switch ( myCol.nodeType) {
					case	1	:
						dTrace( 7, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "buildTHEAD(): column := "+myCol.nodeName) ;
						var newTH	=	document.createElement( "th") ;
						newTH.appendChild( document.createTextNode( myCol.nodeName)) ;
						newTH.setAttribute( "wapattr", myCol.nodeName);
						break ;
					case	3	:
						break ;
					}
				}
				domTableHeader.appendChild( newTR) ;
			} else {
				dTrace( 5, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "could not find data-table in response") ;
			}
		} else if ( ! domTableHeader){
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "could not find html-table-header") ;
		} else {
			dTrace( 4, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "could not find html-table-body") ;
		}
		dTrace( 3, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "buildTHEAD( ..., '"+this.xmlTableName+"', ...): end") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining onEditFinished()") ;
	this.onEditFinished	=	function() {
		dBegin( 1, "wapGridXML.js", "wapGridXML", "onEditFinished()") ;
		this._onRefresh() ;
		dEnd( 1, "wapGridXML.js", "wapGridXML", "onEditFinished()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining show()") ;
	this.show	=	function() {
		this.onDataSourceLoaded( null, this.dataSource.xmlData) ;
	} ;
	/**
	 * _xmlData
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining show()") ;
	this.onDataSourceLoaded	=	function( _parent, _xmlData) {
		var	myDataStartRowE ;		// form-field reference
		var	myDataRowCountE ;
		var	myDataTotalRowsE ;
		var	myDataStartRow ;		// form-field reference
		var	myDataRowCount ;
		var	myDataTotalRows ;
		var	myInputStartRowT ;		// value
		var	myInputRowCountT ;
		var	myInputTotalRowsT ;
		var	domTable ;
		var	domTableHeader ;
		var	domHeaderRow ;
		var	domRow ;
		var	domCol ;
		var	wapSelKey	=	"" ;
		var	wapRowType ;
		var	wapAttr ;
		/**
		 * egt teh table information from the XML and display in the top form (if exists)
		 */
		myDataStartRowE	=	_xmlData.getElementsByTagName( "StartRow") ;
		myDataRowCountE	=	_xmlData.getElementsByTagName( "RowCount") ;
		myDataTotalRowsE	=	_xmlData.getElementsByTagName( "TotalRows") ;
		dTrace( 4, "wapGridXML.js", "wapGridXML", "show( ...)", "checking startrow") ;
		if ( myDataStartRowE) {
			myDataStartRow	=	myDataStartRowE[0].childNodes[0].nodeValue ;
			myDataRowCount	=	myDataRowCountE[0].childNodes[0].nodeValue ;
			myDataTotalRows	=	myDataTotalRowsE[0].childNodes[0].nodeValue ;
		} else {
			myDataStartRow	=	-1 ;
			myDataRowCount	=	-1 ;
			myDataTotalRows	=	-1 ;
		}
		dTrace( 4, "wapGridXML.js", "wapGridXML", "show( ...)", "checking rowcount from dataSource-objects") ;
		dTrace( 4, "wapGridXML.js", "wapGridXML", "show( ...)", "formTop.Name = '" + this.formTop + "'") ;
		if ( this.formTop != "") {
			myInputStartRowT	=	getFormField( this.formTop, "_SStartRow") ;
			myInputRowCountT	=	getFormField( this.formTop, "_SRowCount") ;
			myInputTotalRowsT	=	getFormField( this.formTop, "_TotalRows") ;
			myInputStartRowT.value	=	myDataStartRow ;
			myInputRowCountT.value	=	myDataRowCount ;
			myInputTotalRowsT.value	=	myDataTotalRows ;
		}
		dTrace( 4, "wapGridXML.js", "wapGridXML", "show( ...)", "formBot.Name = '" + this.formBot + "'") ;
		/**
		 * find the table and delete all rows marked as 'wapRowType' = 'data'
		 */
		var	domTable	=	document.getElementById( this.xmlTableName) ;	// get <table></table>
		if ( domTable === null) {
			dTrace( 3, "wapGridXML.js", "wapGridXML", "show( ...)", "domTable not defined ---> will crashland") ;
		}
		dTrace( 3, "wapGridXML.js", "wapGridXML", "show( ...)", "domTable") ;
		var	domTableHeader	=	domTable.tHead ;							// get <thead></thead>
		dTrace( 3, "wapGridXML.js", "wapGridXML", "show( ...)", "domTable", "tBodies := " + domTable.tBodies.length.toString()) ;
		var	domTableBody	=	domTable.tBodies[0] ;						// get <tbody></tbody>
		dTrace( 3, "wapGridXML.js", "wapGridXML", "show( ...)", "domTable") ;
		/**
		 * only go-on if there's a valid table with head and body
		 */
		if ( domTableHeader && domTableBody) {
			_debugL( 2, "found html-table-header, has " + domTableHeader.rows.length.toString() + " rows\n") ;
			_debugL( 2, "found html-table-body, has " + domTableBody.rows.length.toString() + " rows\n") ;
			_debugL( 2, "looking for elements: " + this.dataItemName + "\n") ;
			wapSelKey	=	domTable.getAttribute( "wapSelKey") ;
			if ( ! wapSelKey) {
				wapSelKey	=	"" ;
			}
			/**
			 *	IF there are no header rows in our HTML table then
			 *		we need to buildTHEAD the header from the data we received
			 *	ENDIF
			 */
			if ( domTableHeader.rows.length <= 0) {
				this.buildTHEAD( _xmlData) ;
			}
			/**
			 * find the <th></th> row which contains the parameter "wapRowType"
			 */
			for ( var i=0 ; i < domTableHeader.rows.length ; i++) {
				dTrace( 5, "wapGridXML.js", "wapGridXML", "onDataSourceLoaded()", "working on header.row := " + i.toString()) ;
				domRow	=	domTableHeader.rows[i] ;
				wapRowType	=	( domRow.dataset.wapRowType ? domRow.dataset.wapRowType : "") ;
				if ( wapRowType === "header") {
					dTrace( 5, "wapGridXML.js", "wapGridXML", "onDataSourceLoaded()", "wapRowType := header") ;
					domHeaderRow	=	domRow ;
				} else if ( wapRowType === "data") {
				} else {
				}
			}
			/**
			 * delete existing data rows
			 */
			_debugL( 5, "wapGridXML.js::wapGridXML::show(): will delete existing rows\n") ;
			var	rowCount	=	domTableBody.rows.length ;
			for ( var i=rowCount - 1 ; i >= 0 ; i--) {
				domRow	=	domTableBody.rows[i] ;
				wapRowType	=	( domRow.dataset.wapRowType ? domRow.dataset.wapRowType : "") ;
				if ( wapRowType === "header") {
				} else if ( wapRowType === "data") {
					domTableBody.deleteRow( i) ;
				} else {
				}
			}
			/**
			 * add the data to the table
			 */
			dTrace( 5, "wapGridXML.js", "wapGridXML", "show()", "will add new data to the table") ;
			dTrace( 5, "wapGridXML.js", "wapGridXML", "show()", "----------------------------------------------") ;
			var	rows	=	_xmlData.getElementsByTagName( this.object) ;
			dTrace( 5, "wapGridXML.js", "wapGridXML", "show()", "will add " + rows.length + " rows to the table") ;
			/**
			 *	iterate through all objects in the resultset
			 */
			for ( var col=0 ; domCol = domHeaderRow.getElementsByTagName("th")[col] ; col++) {
				domCol.dataset.wapLastGroupData	=	"" ;		// temporary marker
				wapAttr	=	( domCol.dataset.wapAttr ? domCol.dataset.wapAttr : null) ;
				if ( wapAttr) {
					if ( this.colInfo[ wapAttr]) {
						dTrace( 8, "wapGridXML.js", "wapGridXML", "show()", "clearing wapAttr "+wapAttr) ;
						this.colInfo[ wapAttr].wapGroupMatch	=	"" ;
					}
				}
			}
			/**
			 * iterate through all rows of the result set
			 */
			for ( var il0=0 ; il0 < rows.length ; il0++) {
				dTrace( 6, "wapGridXML.js", "wapGridXML", "show()", "working on line # "+il0.toString()+" of "+rows.length+" rows") ;
				var newTR	=	document.createElement("tr") ;
				var	myRow	=	this.getBodyRow( newTR, domHeaderRow, rows[il0], il0) ;
				domTableBody.appendChild( myRow) ;
			}
		} else if ( ! domTableHeader){
			dTrace( 3, "wapGridXML.js", "wapGridXML", "show()", "could not find html-table-header\n") ;
		} else {
			dTrace( 3, "wapGridXML.js", "wapGridXML", "show()", "could not find html-table-body\n") ;
		}
		if ( this.onDataSourceLoadedExt) {
			this.onDataSourceLoadedExt( _parent, _xmlData) ;
		}
		if ( this.onDataSourceLoadFi) {
			this.onDataSourceLoadFi( _parent, _xmlData) ;
		}
		dEnd( 1, "wapGridXML.js", "wapGridXML", "show( ..., '"+this.xmlTableName+"', ...)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining expand()") ;
	this.expand	=	function( _event, _field) {

	};
	/**
	 *
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining collapse()") ;
	this.collapse	=	function( _event, _field) {

	} ;
	/**
	 *
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining sort()") ;
	this.sort	=	function( _event, _field) {
		dBegin( 1, "wapGridXML.js", "wapGridXML", "sort( '"+_field+"')") ;
		if ( this.formTop != "") {
			myInputSortOrderT	=	getFormField( this.formTop, "_SortOrder") ;
			myOrder	=	myInputSortOrderT.value ;
			/**
			 * check if the order clause already contains this field
			 */
			if ( myOrder.indexOf( _field) >= 0) {
				if ( myOrder.indexOf( _field + " ASC") >= 0)
					newOrder	=	myOrder.replace( _field + " ASC", _field + " DESC") ;
				else
					newOrder	=	myOrder.replace( _field + " DESC", _field + " ASC") ;
				myOrder	=	newOrder ;
			} else if ( _event.shiftKey) {
				if ( myOrder !== "") {
					myOrder	+=	"," ;
				}
				myOrder	+=	_field + " ASC" ;
				var mySprite	=	document.createElement( "div") ;
				mySprite.setAttribute( "class", "memu-icon sprite-triangle-1-s") ;
				element	=	( _event.target) ? _event.target : _event.srcElement ;
				element.appendChild( mySprite) ;
			} else {
				myOrder	=	_field + " ASC" ;
			}
			myInputSortOrderT.value	=	myOrder ;
			/**
			 * clean the header line from all sorting indicators
			 */
			dTrace( 1, "wapGridXML.js", "wapGridXML", "refresh()", "cleaning out indicators") ;
			element	=	( _event.target) ? _event.target : _event.srcElement ;
			myTr	=	element.parentNode ;
			myTHs	=	myTr.getElementsByTagName( "th") ;
			for ( i=0 ; i < myTHs.length ; i++) {
				myTH	=	myTHs[i] ;
				myAttr	=	myTH.getAttribute( "wapattr") ;
				dTrace( 1, "wapGridXML.js", "wapGridXML", "refresh()", "checking ... '" + myAttr + "'") ;
				while ( myDIV = myTH.getElementsByTagName( "div")[0]) {
					myTH.removeChild( myDIV) ;
				}
				if ( myOrder.indexOf( myAttr) >= 0) {
					if ( myOrder.indexOf( myAttr + " ASC") >= 0) {
						var mySprite	=	document.createElement( "div") ;
						mySprite.setAttribute( "class", "memu-icon sprite-triangle-1-s") ;
						myTH.insertBefore( mySprite, myTH.childNodes[0]) ;
					} else {
						var mySprite	=	document.createElement( "div") ;
						mySprite.setAttribute( "class", "memu-icon sprite-triangle-1-n") ;
						myTH.insertBefore( mySprite, myTH.childNodes[0]) ;
					}
				}
			}
			/**
			 * insert all sorting indicators into the header line
			 */
			this._onRefresh() ;
		}
		dEnd( 1, "wapGridXML.js", "wapGridXML", "sort( '"+_field+"')") ;
	} ;
	/**
	 *	group( _event, _field, _id)
	 *	===========================
	 *
	 *	callback from within the grid on selection to expand or collapse an item in the view
	 *	first we need to find the line in the table containing this entry.
	 *	once we found the entry, wee need to determine the expression
	 * event
	 * _obj
	 * _attr	data-wap-attr of the field to be expanded/collapseDep
	 * _data	value of the data-wap-attr
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining sort()") ;
	this.group	=	function( _event, _obj, _attr, _data) {
		var	domCol ;
		dBegin( 3, "wapGridXML.js", "wapGridXML", "group( <_event>, <Object>, '"+_attr+"', '"+_data+"')") ;
		dTrace( 4, "wapGridXML.js", "wapGridXML", "refresh()", "tagName := '" + _obj.tagName + "'") ;
		dTrace( 4, "wapGridXML.js", "wapGridXML", "refresh()", "tagName := '" + _obj.parentNode.tagName + "'") ;
		/**
		 * get the visible <table>
		 */
		var	domTable	=	document.getElementById( this.xmlTableName) ;	// get <table></table>

		if ( domTable === null) {
			dTrace( 3, "wapGridXML.js", "wapGridXML", "show( ...)", "domTable not defined ---> will crashland") ;
		}
		/**
		 * get the header row where the wap specific data is stored
		 */
		dTrace( 3, "wapGridXML.js", "wapGridXML", "show( ...)", "domTable") ;
		var	domTableHeader	=	domTable.tHead ;							// get <thead></thead>
		var	domHeaderRow	=	null ;
		for ( var i=0 ; i < domTableHeader.rows.length && domHeaderRow == null ; i++) {
			var	domRow	=	domTableHeader.rows[i] ;
			var	wapRowType	=	( domRow.dataset.wapRowType ? domRow.dataset.wapRowType : "") ;
			if ( wapRowType === "header") {
				domHeaderRow	=	domRow ;
			}
		}
		for ( var col=0 ; domCol = domHeaderRow.getElementsByTagName("th")[col] ; col++) {
			/**
			 * get data-wap-attr of this column
			 */
			var	wapAttr	=	( domCol.dataset.wapAttr ? domCol.dataset.wapAttr : null) ;
			/**
			 * get data-wap-group-by attribute oif this column
			 */
			var	wapGroupBy	=	domCol.dataset.wapGroupBy ? ( domCol.dataset.wapGroupBy == "true" ? true : ( domCol.dataset.wapGroupBy == "yes" ? true : false)) : false  ;
			/**
			 * is a data-wap-attr is defined and it is a data-wap-group-by
			 */
			if ( wapAttr && wapGroupBy) {
				if ( wapAttr == _attr) {
					if ( ! domCol.dataset.wapVisibleGroups)
						domCol.dataset.wapVisibleGroups	=	"" ;
					if ( domCol.dataset.wapVisibleGroups.indexOf( ","+_data+",") >= 0) {
						domCol.dataset.wapVisibleGroups	=	domCol.dataset.wapVisibleGroups.replace( ","+_data+",", "") ;
					} else {
						domCol.dataset.wapVisibleGroups	+=	"," + _data + "," ;
					}
				}
			}
		}
		this.show() ;
		dEnd( 3, "wapGridXML.js", "wapGridXML", "group( <_event>, <Object>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining refresh()") ;
	this.refresh	=	function() {
		dBegin( 101, "wapGridXML.js", "wapGridXML", "refresh()") ;
		dTrace( 102, "wapGridXML.js", "wapGridXML", "refresh()", new XMLSerializer().serializeToString( this.dataSource.xmlData)) ;
		/**
		 * add the data to the table
		 */
		dataTable	=	this.dataSource.xmlData.getElementsByTagName( this.dataItemName) ;	// SPECIFIC
		dTrace( 102, "wapGridXML.js", "wapGridXML", "refresh()", "before if") ;
		if ( dataTable) {
			_debugL( 0x01000000, "wapGridXML.js::wapGridXML::refresh(...): dataTable := valid\n") ;
			for ( var i=0 ; i < dataTable.length ; i++) {
				_debugL( 0x01000000, "wapGridXML.js::wapGridXML::refresh(...): working on row # := " + i.toString() + "\n") ;
				dataRow	=	dataTable[i] ;	// SPECIFIC
				myId	=	dataRow.getElementsByTagName( this.idKey)[0].childNodes[0].nodeValue ;
				_debugL( 0x01000000, "wapGridXML.js::wapGridXML::refresh(...): working on Id # := " + myId + "\n") ;
				domTableBody	=	document.getElementById( this.xmlTableName).tBodies[0] ;					// SPECIFIC
				_debugL( 0x01000000, "wapGridXML.js::wapGridXML::refresh(...): row located \n") ;
				ndxTR	=	this.getBodyRowByIdIndex( domTableBody, parseInt( myId)) ;
				domTableBody.deleteRow( ndxTR) ;
				newTR	=	domTableBody.insertRow( ndxTR) ;
				this.getBodyRow( newTR, domHeaderRow, dataRow, i) ;
			}
		} else {
			dTrace( 103, "wapGridXML.js", "wapGridXML", "refresh()", "could not find data-table in response\n") ;
		}
		dEnd( 101, "wapGridXML.js", "wapGridXML", "refresh()") ;
	} ;
	/**
	 *
	 */
	this.showAdd	=	function( _response) {
		_debugL( 0x01000000, "wapGridXML.js::wapGridXML::showAdd( <_response>): begin\n") ;
		/**
		 *
		 */
		dataTable	=	_response.getElementsByTagName( this.dataItemName) ;	// SPECIFIC
		if ( dataTable) {
			_debugL( 0x01000000, "wapGridXML.js::wapGridXML::showAdd( <_response>): dataTable is valid\n") ;
			myRowIndex	=	this.getBodyRowByIdIndex( domTableBody, this.selectedId) ;
			for ( var i=0 ; i < dataTable.length ; i++) {
				_debugL( 0x01000000, "wapGridXML.js::wapGridXML::showAdd( <_response>): working on line " + i.toString() + "\n") ;
				/**
				 * get the single data row from the reponse
				 */
				dataRow	=	_response.getElementsByTagName( this.dataItemName)[i] ;	// SPECIFIC
				/**
				 * add a new row to the table
				 * and add the columns to this new row
				 */
				newRow	=	domTableBody.insertRow( i+myRowIndex+1) ;
				this.getBodyRow( newRow, domHeaderRow, dataRow, -1) ;
			}
		}
		_debugL( 0x01000000, "wapGridXML.js::wapGridXML::showAdd( <_response>): end\n") ;
	} ;
	this.getBodyRowByIdIndex	=	function( _domTableBody, _id) {
		_debugL( 0x01000000, "wapGridXML.js::wapGridXML::getBodyRowByIdIndex( <_domTableBody>, "+_id.toString()+"): begin\n") ;
		rowCount	=	_domTableBody.rows.length ;
		myRow	=	-1 ;
		for ( var rowNdx=0 ; rowNdx < rowCount && myRow == -1 ; rowNdx++) {
			_debugL( 0x01000000, "wapGridXML.js::wapGridXML::refresh(...): checking dom-row [" + rowNdx.toString() + "]\n") ;
			domRow	=	_domTableBody.rows[ rowNdx] ;
			domCell	=	domRow.cells[ colId] ;
			if ( domCell.innerHTML == _id) {
				_debugL( 0x01000000, "wapGridXML.js::wapGridXML::refresh(...): [" + rowNdx.toString() + "] is the one\n") ;
				myDomRow	=	domRow ;							// this is the row we need to update
				myRow	=	rowNdx ;
			}
		}
		_debugL( 0x01000000, "wapGridXML.js::wapGridXML::getBodyRowByIdIndex( <_domTableBody>, "+_id.toString()+"): end\n") ;
		return myRow ;
	} ;
	/**
	 * Create a single row for the (common)Table.
	 * _tr		table row to which the
	 */
	this.getBodyRow	=	function( _tr, _domHeaderRow, _obj, _ndx) {
		var	domCol ;
		var	openURL ;
		var	linkButton ;
		var	wapAttr ;
		var	wapFunctions ;
		var	colId ;
		var	wapAlign ;
		var	wapAlign ;
		var	wapVT ;
		var	wapSize ;
		var	wapMax ;
		var	wapCanOpen ;
		var	wapSpecialFnc ;
		var	wapMod ;
		var	wapGroupBy ;
		var	wapLinkTo ;
		var	wapMailTo ;
		var	myDataNode ;
		var	myData ;
		var	myCellId ;
		var	wapSelKey ;
		var	myId ;				// holds the "Id" key of the datarecord
		/**
		 *
		 */
		dBegin( 601, "wapGridXML.js", "wapGridXML", "getBodyRow(...)") ;
		dTrace( 602, "wapGridXML.js", "wapGridXML", "getBodyRow()", new XMLSerializer().serializeToString( _obj)) ;
		var	domHeaderRow	=	_domHeaderRow ;
//		var	dataRow	=	_dataRow ;
		var newTR	=	_tr ;
		for ( var col=0 ; domCol = domHeaderRow.getElementsByTagName("th")[col] ; col++) {
			dTrace( 603, "wapGridXML.js", "wapGridXML", "getBodyRow(...)", "col := " + col.toString()) ;
			linkButton	=	null ;
			openURL	=	null ;
			wapAttr	=	( domCol.dataset.wapAttr ? domCol.dataset.wapAttr : null) ;
			wapFunctions	=	( domCol.dataset.wapFunctions ? domCol.dataset.wapFunctions : "") ;
//			domCol.getAttribute( "wapattr") ;
			if ( wapAttr === this.idKey) {
				colId	=	col ;
			}
			wapAlign		=	( domCol.dataset.wapAlign ? domCol.dataset.wapAlign : "left") ;
			wapVT			=	( domCol.dataset.wapVT ? domCol.dataset.wapVT : "data") ;
			wapSize			=	( domCol.dataset.wapSize ? domCol.dataset.wapSize : "12") ;
			wapMax			=	( domCol.dataset.wapMax ? domCol.dataset.wapMax : "12") ;
			wapCanOpen		=	( domCol.dataset.wapCanOpen ? ( domCol.dataset.wapCanOpen === "true" ? true : false) : false) ;
			wapSpecialFnc	=	( domCol.dataset.wapSpecialFnc ? domCol.dataset.wapSpecialFnc : null) ;
			wapMod			=	( domCol.dataset.wapMod ? domCol.dataset.wapMod : "Base") ;
			wapGroupBy		=	domCol.dataset.wapGroupBy ? ( domCol.dataset.wapGroupBy == "true" ? true : ( domCol.dataset.wapGroupBy == "yes" ? true : false)) : false  ;
			wapLinkTo		=	( domCol.dataset.wapLinkTo ? domCol.dataset.wapLinkTo : null) ;
			wapMailTo		=	( domCol.dataset.wapMailTo ? domCol.dataset.wapMailTo : null) ;
			if ( wapAttr) {
				dTrace( 604, "wapGridXML.js", "wapGridXML", "wapAttr ["+wapAttr+"] is valid\n") ;
				myDataNode	=	_obj.getElementsByTagName( wapAttr) ;
				if ( myDataNode[0]) {
					myData	=	myDataNode[0].childNodes[0].nodeValue ;
					if ( wapSelKey == wapAttr)
						myKey	=	myData ;
					if ( wapAttr == this.idKey)
						myId	=	myData ;
					myCellId	=	wapAttr ;
					if ( wapFunctions.search( /step/) != -1) {
						var	newTD	=	document.createElement( "td") ;
						newTD.innerHTML	=	this.btnDec( myId, wapAttr) ;
						newTR.appendChild( newTD);
					}
					var	cellText ;
					if ( wapVT == "float") {
						cellText = document.createTextNode( myData.replace( ".", ",")) ;
					} else {
						cellText = document.createTextNode( myData) ;
					}
					if ( wapLinkTo) {
//						var	newTD	=	document.createElement( "td") ;
						if ( myData != "") {
							linkButton	=	this.btnLinkTo( wapLinkTo, myData) ;
						}
					}
					if ( wapCanOpen) {
						if ( myData != "") {
							openURL	=	this.openLink( myData) ;
						} else {
							wapCanOpen	=	false ;
						}
					}
					if ( wapFunctions) {
						if ( wapFunctions.search( /upload/) != -1) {
							var	newTD	=	document.createElement( "td") ;
							var	wapUploadAction	=	domCol.getAttribute( "wapUploadAction") ;
							newTD.innerHTML	=	"<form action=\""+wapUploadAction+"\" "
													+	"method=\"post\" name=\"formUploadDoc\" "
													+	"id=\"formLiefDocUpload\" target=\"_result\" enctype=\"multipart/form-data\">\n"
												+	"<input type=\"hidden\" name=\"_HId\" value=\"" + myId + "\" >"
												+	"<input type=\"file\" name=\"_IFilename\" >"
												+	"<input type=\"submit\" value=\"Upload\" >"
												+	"</form>" ;
						} else if ( wapFunctions.search( /imgref/) != -1) {
							var	newTD	=	document.createElement( "td") ;
							newTD.innerHTML	=	"<img src=\"/Bilder/thumbs/" + myData + "\" />\n" ;
						} else {
							var	newTD	=	document.createElement( "td") ;
							newTD.setAttribute( "id", myCellId);
							newTD.appendChild( cellText) ;
						}
					} else {
						var	newTD	=	document.createElement( "td") ;
						newTD.setAttribute( "id", myCellId);
						newTD.setAttribute( "align", wapAlign) ;
						if ( linkButton) {
							newTD.appendChild( linkButton) ;
							linkButton	=	null ;
						}
						if ( openURL) {
							newTD.appendChild( openURL) ;
						} else {
							newTD.appendChild( cellText) ;
						}
					}
					newTD.setAttribute( "onclick", "wapGrids['" + this.name + "'].onSelectCell( event) ;") ;
					newTD.domCol	=	domCol ;			// maintain direct reference to the header cell
					newTR.appendChild( newTD) ;
					if ( wapMailTo) {
						var	newTD	=	document.createElement( "td") ;
						if ( cellText.length > 0) {
							newTD.innerHTML	=	btnMail( 'wapMod', this.primObj, '/Common/mailer.php', 'getKundeKontaktMailAsXML', this.primObjKey, myId, '', null, 'showTableKundeKontakt') ;
						}
						newTR.appendChild( newTD);
					}
					if ( wapFunctions) {
						if ( wapFunctions.search( /step/) != -1) {
							var	newTD	=	document.createElement( "td") ;
							newTD.innerHTML	=	this.btnInc( myId, wapAttr) ;
							newTR.appendChild( newTD);
						} else if ( wapFunctions.search( /special/) != -1) {
							var	newTD	=	document.createElement( "td") ;
							newTD.innerHTML	=	recalcButton( wapMod, this.primObj, this.phpHandler, wapSpecialFnc, this.primObjKey, myId, this.dataItemName+"."+wapAttr, null, null) ;
							newTR.appendChild( newTD);
						} else if ( wapFunctions.search( /custom/) != -1) {
							 if ( myData == "") {
								_debugL( 0x01000000, "starting\n") ;
								wapCustFnc	=	( domCol.dataset.wapCustFnc ? domCol.dataset.wapCustFnc : "") ;
								var	newTD	=	document.createElement( "td") ;
								newTD.innerHTML	=	this.btnCustom( myId, wapCustFnc) ;
								newTR.appendChild( newTD);
								_debugL( 0x01000000, "ending\n") ;
							 } else {
								var	newTD	=	document.createElement( "td") ;
								newTR.appendChild( newTD);
							 }
						} else if ( wapFunctions.search( /custcalc/) != -1) {
							wapCustFnc	=	( domCol.dataset.wapCustFnc ? domCol.dataset.wapCustFnc : "") ;
							var	newTD	=	document.createElement( "td") ;
							newTD.innerHTML	=	this.btnCustom( myId, wapCustFnc) ;
							newTR.appendChild( newTD);
						}
					}
				} else {
					var cellText = document.createTextNode( "attribute not available") ;
					var	newTD	=	document.createElement( "td") ;
					if ( wapGroupBy === "true") {
						var myIcon = document.createElement( "div") ;
						myIcon.setAttribute( "class", "memu-icon sprite-right") ;
						newTD.appendChild( myIcon) ;
					}
					newTD.appendChild( cellText) ;
					newTR.appendChild( newTD);
				}
			} else if ( wapFunctions) {
				var	newTD	=	document.createElement( "td") ;
				newTD.innerHTML	=	"" ;
				if ( wapFunctions.search( /edit/) !== -1) {
//					var	newTD	=	document.createElement( "td") ;
					newTD.innerHTML	+=	this.btnEdit( myId) ;
//					newTR.appendChild( newTD);
				}
				if ( wapFunctions.search( /delete/) !== -1) {
//					var	newTD	=	document.createElement( "td") ;
					newTD.innerHTML	+=	this.btnDelete( myId) ;
//					newTR.appendChild( newTD);
				}
				if ( wapFunctions.search( /move/) !== -1) {
//					var	newTD	=	document.createElement( "td") ;
					newTD.innerHTML	+=	this.btnMoveUp( myId) ;
//					newTR.appendChild( newTD);
//					var	newTD	=	document.createElement( "td") ;
					newTD.innerHTML	+=	this.btnMoveDown( myId) ;
//					newTR.appendChild( newTD);
				}
				if ( wapFunctions.search( /colex/) !== -1) {
//					var	newTD	=	document.createElement( "td") ;
					newTD.innerHTML	+=	this.btnExpand( myId) ;
//					newTR.appendChild( newTD);
//					var	newTD	=	document.createElement( "td") ;
					newTD.innerHTML	+=	this.btnCollapse( myId) ;
//					newTR.appendChild( newTD);
				}
				if ( wapFunctions.search( /add/) !== -1) {
					wapEditor	=	domCol.getAttribute( "wapEditor") ;
					wapAddObj	=	domCol.getAttribute( "wapAddObj") ;
//					var	newTD	=	document.createElement( "td") ;
					newTD.innerHTML	+=	this.btnAdd( myId) ;
//					newTR.appendChild( newTD);
				}
				if ( wapFunctions.search( /custom/) !== -1) {
					_debugL( 0x01000000, "starting later\n") ;
					wapCustFnc	=	domCol.getAttribute( "wapCustFnc") ;
//					var	newTD	=	document.createElement( "td") ;
					newTD.innerHTML	+=	this.btnCustom( myId, wapCustFnc) ;
//					newTR.appendChild( newTD);
					_debugL( 0x01000000, "ending later\n") ;
				}
				if ( wapFunctions.search( /select/) !== -1) {
//					var	newTD	=	document.createElement( "td") ;
					newTD.innerHTML	+=	this.btnSelect( myKey) ;
//					newTR.appendChild( newTD);
				}
				newTR.appendChild( newTD);
			}
		}
//		_debugL( 0x01000000, "...adding TR to table\n") ;
		newTR.setAttribute( "data-wap-row-type", "data") ;
//		newTR.setAttribute( "onclick", "wapGrids['" + this.name + "']._onSelectById( '" + myId + "') ;");
		newTR.setAttribute( "style", "background-Color: rgb( 221, 255, 221);") ;
		newTR.setAttribute( "onMouseOver", "this.style.backgroundColor='#dddddd'") ;
		newTR.setAttribute( "onMouseOut", "this.style.backgroundColor='#ddffdd'") ;
		newTR.setAttribute( "onclick", "wapGrids['" + this.name + "'].onSelectRow( event) ;") ;
		dEnd( 601, "wapDataGridXML.js", "wapDataGridXML", "getBodyRow(...)") ;
		return newTR ;
	} ;
	/**
	 * addItem
	 */
	dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid( <...>)", "defining editItem()") ;
	this.addItem	=	function() {
		dBegin( 1, "wapGridXML.js", "wapGridXML", "addItem()") ;
		this.dataItemEditor.startAdd( this.dataItemName, -1, "", this.parentDS) ;
		dEnd( 1, "wapGridXML.js", "wapGridXML", "addItem()") ;
	} ;
	this.editItem	=	function( _id) {
		dBegin( 1, "wapGridXML.js", "wapGridXML", "editItem( '"+_id+"')") ;
		this.dataItemEditor.startUpdate( this.dataItemName, _id, "", this.parentDS) ;
		dEnd( 1, "wapGridXML.js", "wapGridXML", "editItem( '"+_id+"')") ;
	} ;
	this.deleteItem	=	function( _id) {
		dBegin( 1, "wapGridXML.js", "wapGridXML", "deleteItem( '"+_id+"')") ;
		this.dataSource.id	=	_id ;
		dEnd( 1, "wapGridXML.js", "wapGridXML", "deleteItem( '"+_id+"') pre-mature") ;
		this.dataSource.dispatch( true, "delDep", null, "") ;
	} ;
	this.handleDS	=	function() {
		this._onRefresh() ;
	} ;
	this.expandDep	=	function( _id) {
		this.onDone	=	this.show ;
		reqData(this.phpHandler, this.primObj, 'expandDep', this.primObjKey, _id, this.dataItemName, this);
	} ;
	this.collapseDep	=	function( _id) {
		this.onDone	=	this.show ;
		reqData(this.phpHandler, this.primObj, 'collapseDep', this.primObjKey, _id, this.dataItemName, this);
	} ;
	this.moveDepUp	=	function( _id) {
		this.onDone	=	this.show ;
		reqData(this.phpHandler, this.primObj, 'moveDepUp', this.primObjKey, _id, this.dataItemName, this);
	} ;
	this.moveDepDown	=	function( _id) {
		this.onDone	=	this.show ;
		reqData(this.phpHandler, this.primObj, 'moveDepDown', this.primObjKey, _id, this.dataItemName, this);
	} ;
	this.incDep	=	function( _id, _attr) {
		this.onDone	=	this.refresh ;
		reqData(this.phpHandler, this.primObj, 'incDep', this.primObjKey, _id, this.dataItemName + "." + _attr, this);
	} ;
	this.decDep	=	function( _id, _attr) {
		this.onDone	=	this.refresh ;
		reqData(this.phpHandler, this.primObj, 'decDep', this.primObjKey, _id, this.dataItemName + "." + _attr, this);
	} ;
	this.setDep	=	function( _id, _attr, _form) {
		this.onDone	=	this.refresh ;
		reqData(this.phpHandler, this.primObj, 'setDep', this.primObjKey, _id, this.dataItemName + "." + _attr, this);
	} ;
	this.selected	=	function ( _id) {
		_debugL( 0x00000001, "wapGridXML.js::wapGridXML::selected(" + _id.toString() + "): begin\n") ;
		this.parent.selected( _id) ;
		_debugL( 0x00000001, "wapGridXML.js::wapGridXML::selected(): end\n") ;
	} ;
	this.addDep	=	function( _id) {
		_debugL( 0x00000001, "Add button was pressed on this.myObjName := '...', Id := " + _id.toString() + "\n") ;
		this.addEdt.edit( "", _id, this.addEdtObj) ;
	} ;
	this.custom	=	function( _id, _method) {
		this.onDone	=	this.refresh ;
		reqData( this.phpHandler, this.primObj, _method, this.primObjKey, _id, this.dataItemName, this);
	} ;
	this.getSubTree	=	function( _key, _ndx) {
		dBegin( 1, "wapGridXML.js", "wapGridXML", "getSubTree( '" + _key + "', "+_ndx.toString() + ")") ;
		reqData( this.phpHandler, this.primObj, this.phpGetCall, _key, 'f50', this.dataItemName, this, null, null);
		this.selectedId	=	_ndx ;
		this.onDone	=	this.showAdd ;
		dEnd( 1, "wapGridXML.js", "wapGridXML", "getSubTree(" + _key + "', "+_ndx.toString() + ")") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onSelectById()") ;
	this._onSelectById	=	function( _id) {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onSelectById") ;
		/**
		 * if onSelectById() method is defined ... call it
		 */
		if ( this.onSelectById) {
			dTrace( 104, "wapGridXML.js", "wapGridXML", "_onSelectById(...)", "onSelectById-callBack defined") ;
			this.onSelectById( this.parent, _id) ;
		}
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onSelectById") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining search()") ;
	this.search	=	function( _event) {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "search") ;
//		myField	=	getFormField( this.formTop, "Search") ;
		if ( _event.keyCode === 13|| _event.target.value.length > 3) {
			return this.searchWE() ;
		}
		dEnd( 102, "wapGridXML.js", "wapGridXML", "search") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining searchWE()") ;
	this.searchWE	=	function() {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "searchWE") ;
		this.dataSource.thisPage( this.formTop) ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "searchWE") ;
		return false ;
	} ;
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onFirstPage()") ;
	this._onFirstPage	=	function() {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onFirstPage") ;
		if ( this.formFilterName)
			this.dataSource.firstPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.firstPage( this.formTop) ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onFirstPage") ;
	} ;
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onPreviousPage()") ;
	this._onPreviousPage	=	function() {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onPreviousPage") ;
		if ( this.formFilterName)
			this.dataSource.previousPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.previousPage( this.formTop) ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onPreviousPage") ;
	} ;
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onOneBackward()") ;
	this._onOneBackward	=	function() {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onOneBackward") ;
		if ( this.formFilterName)
			this.dataSource.oneBackward( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.oneBackward( this.formTop) ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onOneBackward") ;
	} ;
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onRefresh()") ;
	this._onRefresh	=	function() {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onRefresh") ;
		if ( this.formFilterName)
			this.dataSource.thisPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.thisPage( this.formTop) ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onRefresh") ;
		return false ;
	} ;
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onOneForward()") ;
	this._onOneForward	=	function() {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onOneForward") ;
		if ( this.formFilterName)
			this.dataSource.oneForward( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.oneForward( this.formTop) ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onOneForward") ;
	} ;
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onNextPage()") ;
	this._onNextPage	=	function() {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onNextPage") ;
		if ( this.formFilterName)
			this.dataSource.nextPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.nextPage( this.formTop) ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onNextPage") ;
	} ;
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onLastPage()") ;
	this._onLastPage	=	function() {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onLastPage") ;
		if ( this.formFilterName)
			this.dataSource.lastPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.lastPage( this.formTop) ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onLastPage") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onStartEdit()") ;
	this._onStartEdit	=	function( _td, _id) {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onStartEdit( <_td>, "+_id+")") ;
		if ( this.tdUnderEdit !== null) {
			this._terminateEdit() ;
		}
		this.tdUnderEdit	=	_td ;
		this.cellInnerHTML	=	_td.innerHTML ;
//		_td.dataset.oldCallback	=	_td.getAttribute( "onclick")
//		_td.removeAttribute( "onclick") ;
		switch ( this.tdUnderEdit.domCol.dataset.wapEditAs) {
		case 	"input"	:
			var	newInput	=	document.createElement( "input") ;
			newInput.td	=	_td ;
			newInput.setAttribute( "type", "text") ;
			newInput.setAttribute( "id", "abcde") ;
			newInput.setAttribute( "name", "abcde") ;
			newInput.setAttribute( "size", "16") ;
			newInput.setAttribute( "maxlength", "32") ;
			newInput.dataset.wapVT	=	( _td.dataset.wapVT ? _td.dataset.wapVT : "data") ;
			newInput.setAttribute( "onKeypress", "wapGrids['" + this.name + "']._onKeypress( event, "+_id+") ;") ;
			newInput.setAttribute( "onclick", "event.stopPropagation() ;") ;
			newInput.value	=	_td.firstChild.nodeValue ;
			while ( _td.firstChild) {
				_td.removeChild( _td.firstChild);
			}
			_td.appendChild( newInput) ;
			newInput.focus() ;
			break ;
		case	"select"	:
			/**
			 * clear()
			 * =======
			 *
			 * clears the data  portion of the table
			 *
			 *		<select id="ClientId" name="ClientId" class="wapField" data-wap-type="option" data-wap-attr="ClientId" data-wap-mode="edit" onchange="clientIdSelected( this) ;">
			 */
			var	newSelect	=	document.createElement( "select") ;
			newSelect.td	=	_td ;
			newSelect.setAttribute( "onclick", "event.stopPropagation() ;") ;
			dTrace( 2, "wapGridXML.js", "wapGrid", "wapGrid()", "defining selector for in-table editing") ;
			this.selector	=	new wapSelectXML( this, "mySelect", {
													selectNode:	newSelect
												,	object:		this.tdUnderEdit.domCol.dataset.wapSelObject
												,	key:		this.tdUnderEdit.domCol.dataset.wapSelKey
												,	value:		this.tdUnderEdit.domCol.dataset.wapSelValue
												,	cond:		this.tdUnderEdit.domCol.dataset.wapCond
												,	order:		this.tdUnderEdit.domCol.dataset.wapOrder
												, 	initialOption: _td.firstChild.nodeValue
												,	onDataSourceLoaded:	function( _parent, _data) {
														dBegin( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
														this.show() ;
														dEnd( 102, "login{wapSelectXML.js}", "wapSelectXML", "onDataSourceLoaded( <_parent>, <_data>)") ;
													}
										}) ;
			newSelect.setAttribute( "onKeypress", "wapGrids['" + this.name + "']._onKeypress( event, "+_id+") ;") ;
			this.selector.refresh() ;
			while ( _td.firstChild) {
				_td.removeChild( _td.firstChild);
			}
			_td.appendChild( newSelect) ;
			newSelect.focus() ;
			break ;
		}
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onStartEdit") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onEndEdit()") ;
	this._onEndEdit	=	function( _inp, _id) {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onEndEdit( "+_inp+", "+_id+")") ;
		switch ( this.tdUnderEdit.domCol.dataset.wapEditAs) {
		case 	"input"	:
			var	attr	=	_inp.td.domCol.dataset.wapAttr ;
			if ( _inp.dataset.wapVT == "float") {
				var	myData	=	_inp.value.replace( ",", ".") ;
			} else {
				var	myData	=	_inp.value ;
			}
			break ;
		case	"select"	:
			var	attr	=	_inp.td.domCol.dataset.wapAttr ;
			var	myData	=	this.selector.getOption() ;
			break ;
		}
		this.dataSource.id	=	_id ;
		this.dataSource.dispatch( true, "updDep", null, _inp.td.domCol.dataset.wapAttr+"="+myData) ;
		this._terminateEdit() ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onEndEdit") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _terminateEdit()") ;
	this._terminateEdit	=	function() {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_terminateEdit()") ;
		var	_obj	=	this.tdUnderEdit ;
		while ( _obj.firstChild) {
			_obj.removeChild( _obj.firstChild);
		}
		this.tdUnderEdit.innerHTML	=	this.cellInnerHTML ;
		this.tdUnderEdit	=	null ;
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_terminateEdit") ;
	} ;
	/**
	 * this eventhandler is fired throough an "input" element, i.e. _event.tareg refers to <input ...>
	 */
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining _onKeypress()") ;
	this._onKeypress	=	function( _event, _id) {
		dBegin( 102, "wapGridXML.js", "wapGridXML", "_onKeypress") ;
		if ( _event.keyCode === 13) {
			dTrace( 102, "wapGridXML.js", "wapGridXML", "_onKeypress", "Enter was pressed") ;
			this._onEndEdit( _event.target, _id) ;
			_event.stopPropagation() ;
		} else if ( _event.keyCode === 27) {
			dTrace( 102, "wapGridXML.js", "wapGridXML", "_onKeypress", "Enter was pressed") ;
			this._terminateEdit() ;
			_event.stopPropagation() ;
		}
		dEnd( 102, "wapGridXML.js", "wapGridXML", "_onKeypress") ;
	} ;
	/**
	 *
	 * @returns
	 */
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining btnCustom()") ;
	this.btnCustom	=	function( _id, _method) {
		ret =	"<div class=\"memu-icon sprite-calc\" onclick=\"wapGrids['" + this.name + "'].custom( '" + _id + "', '" + _method + "') ;\"></div>" ;
		return ret ;
	} ;
	/**
	 *
	 * @param _parent
	 * @param _id
	 * @returns {String}
	 */
	dTrace( 1, "wapGridXML.js", "wapGridXML", "wapGrid( <...>)", "defining btnEdit()") ;
	this.btnEdit	=	function( _id) {
		return "<div class=\"memu-icon sprite-edit\" onclick=\"wapGrids['" + this.name + "'].editItem( '" + _id + "') ;\"></div>" ;
	} ;
	this.btnDelete	=	function( _id) {
		return "<div class=\"memu-icon sprite-garbage\" onclick=\"wapGrids['" + this.name + "'].deleteItem( '" + _id + "') ;\"></div>" ;
	} ;
	this.btnExpand	=	function( _id) {
		return "<td><input type=\"image\" src=\"/Rsrc/licon/Blue/18/object_10.png\" onclick=\"wapGrids['" + this.name + "'].expandDep( '" + _id + "') ;\" /></td>" ;
	} ;

	this.btnCollapse	=	function( _id) {
		return "<td><input type=\"image\" src=\"/Rsrc/licon/Blue/18/object_11.png\" onclick=\"wapGrids['" + this.name + "'].collapseDep( '" + _id + "') ;\" /></td>" ;
	} ;

	this.btnMoveDown	=	function( _id) {
		return "<div class=\"memu-icon sprite-idown\" onclick=\"wapGrids['" + this.name + "'].moveDepDown( '" + _id + "') ;\"></div>" ;
	} ;

	this.btnMoveUp	=	function( _id) {
		return "<div class=\"memu-icon sprite-iup\" onclick=\"wapGrids['" + this.name + "'].moveDepUp( '" + _id + "') ;\"></div>" ;
	} ;

	this.btnDec	=	function( _id, _attr) {
		return "<div class=\"memu-icon sprite-qminus\" onclick=\"wapGrids['" + this.name + "'].decDep( '" + _id + "', '" + _attr + "') ;\"></div>" ;
	} ;

	this.btnInc	=	function( _id, _attr) {
		return "<div class=\"memu-icon sprite-qplus\" onclick=\"wapGrids['" + this.name + "'].incDep( '" + _id + "', '" + _attr + "') ;\"></div>" ;
	} ;
	this.btnLinkTo	=	function( _screen, _key) {
		var	myBtnDiv	=	document.createElement( "div") ;
		myBtnDiv.setAttribute( "class", "memu-icon sprite-goto") ;
		myBtnDiv.setAttribute( "onclick", "screenLinkTo( '" + _screen + "', '" + _key + "') ;") ;
		return myBtnDiv ;
	} ;
	this.openLink	=	function( _key) {
		var	myA	=	document.createElement( "a") ;
		myA.setAttribute( "href", "/api/dispatchXML.php?sessionId="+sessionId+"&_obj=Document"+"&_fnc=getLatestRevision"+"&_key="+_key+"&_id="+"&_val=") ;
		myA.setAttribute( "target", "extern") ;
		myA.appendChild( document.createTextNode( _key)) ;
		return myA ;
	}
	this.btnSelect	=	function( _id) {
		return "<input type=\"image\" src=\"/Rsrc/licon/yellow/18/door.png\" onclick=\"wapGrids['" + this.name + "'].selected( '" + _id + "') ;\" /></td>" ;
	} ;
	this.btnAdd	=	function( _id) {
		return "<input type=\"image\" src=\"/Rsrc/licon/Green/18/add.png\" onclick=\"" + this.myObjName + ".addDep( '" + _id + "') ;\" />" ;
	} ;
	this.getData	=	function( 	_id, _form, _filter) {
		var	url ;
		var	postVars ;
		dBegin( 1, "wapGridXML.js", "wapGridXML", "getData(...)") ;
		dTrace( 1, "wapGridXML.js", "wapGridXML", "getData(...)", "objKey := '" + this.primObjKey + "'") ;
		/**
		 * setup the indefinit progress bar
		 */
		url	=	this.phpHandler
				+ "?_obj=" + this.primObj
				+ "&_fnc=" + this.phpGetCall
				+ "&_key=" + encodeURIComponent(this.primObjKey)
				+ "&_id=" + _id
				+ "&_val=" + this.dataItemName
				;
		postVars	=	"" ;
		if ( _form) {
			dTrace( 3, "wapGridXML.js", "wapGridXML", "getData(...)", "getting data from form [" + _form + "]\n") ;
			postVars	=	getPOSTData( _form) ;
			if ( this.filter) {
				if ( this.filter !== "") {
					_debugL( 0x00000001, "..................." + this.filter) ;
					postVars	+=	"&" + getPOSTData( this.filter) ;
				}
			}
		} else {
			if ( this.filter) {
				dTrace( 3, "wapGridXML.js", "wapGridXML", "getData(...)", "using filter [" + _form + "]\n") ;
				if ( this.filter !== "") {
					postVars	+=	getPOSTData( this.filter) ;
				}
			}
		}
		dTrace( 2, "wapGridXML.js", "wapGridXML", "getData(...)", "about to post the request") ;
		dojo.xhrPost( {
			url: url,
			handleAs: "xml",
			postData: postVars,
			objOnDone:	this,
			load: function( response) {
				statusMsg	=	response.getElementsByTagName( "Status")[0] ;
				if ( statusMsg) {
					statusCode	=	parseInt( response.getElementsByTagName( "StatusCode")[0].childNodes[0].nodeValue) ;
					if ( statusCode === 0) {
						if ( this.objOnDone) {
							if ( this.objOnDone !== null)
								this.objOnDone.onDone( response) ;
						}
					} else {
						showStatus( response) ;
					}
				} else {
				}
			},
			error: function( response) {
				showStatus( response) ;
			}
		} ) ;
		dEnd( 1, "wapGridXML.js", "wapGridXML", "getData(...)") ;
		return false ;
	} ;
	dEnd( 1, "wapGridXML.js", "wapGrid", "wapGrid( ...)") ;
}
/**
 *
 */
wapGrid.prototype.onSelectRow	=	function( _event) {
	dBegin( 102, "wapGridXML.js", "wapGridXML", "onSelectRow( <_event>)") ;
	dEnd( 102, "wapGridXML.js", "wapGridXML", "onSelectRow( <_event>)") ;
}
/**
 *
 */
wapGrid.prototype.onSelectColumn	=	function( _event) {
	dBegin( 102, "wapGridXML.js", "wapGridXML", "onSelectColumn( <_event>)") ;
	dEnd( 102, "wapGridXML.js", "wapGridXML", "onSelectColumn( <_event>)") ;
}
/**
 * this method is called when a cell in the grid is pressed, i.e. each cell in teh grid has this callback
 * attached.
 */
wapGrid.prototype.onSelectCell	=	function( _event) {
	dBegin( 102, "wapGridXML.js", "wapGridXML", "onSelectCell( <_event>)") ;
	var	stopped	=	false ;
	dTrace( 102, "wapGridXML.js", "wapGridXML", "onSelectCell( <_event>)", "Value := '" + _event.target.innerHTML + "'") ;
	/**
	 * terminate a possible editing task
	 */
	if ( this.tdUnderEdit !== null) {
		if ( this.tdUnderEdit !== _event.target) {
			this._terminateEdit() ;
		}
	}
	if ( _event.target.id == "Id") {
		if ( this.parentDS == null) {
			this.onSelectById( this.parent, _event.target.innerHTML) ;
			_event.stopPropagation() ;
			stopped	=	true ;
		}
	} else if ( _event.target.id == this.parent.screenDiv.dataset.wapCoreObjectKey) {
		if ( this.parentDS == null) {
			this.onSelectByKey( this.parent, _event.target.innerHTML) ;
			_event.stopPropagation() ;
			stopped	=	true ;
		}
	} else {
		dTrace( 102, "wapGridXML.js", "wapGridXML", "onSelectCell( <_event>)", "cell is neither id nor key") ;
		/**
		 * try to find a column which contains an "Id"
		 */
		var	myTR	=	_event.target.parentNode ;		// go the the <table> element
		var	myTDs	=	myTR.children ;
		var	myTD	=	null ;
		for ( var i = 0 ; i < myTDs.length && myTD == null ; i++) {
			if ( myTDs[i].id == "Id") {
				myTD	=	myTDs[i] ;
			}
		}
		if ( myTD != null) {
			dTrace( 102, "wapGridXML.js", "wapGridXML", "onSelectCell( <_event>)", "found an id in this row") ;
			if ( _event.target.domCol.dataset.wapEditAs){
				dTrace( 102, "wapGridXML.js", "wapGridXML", "onSelectCell( <_event>)", "... and this.cell is editable") ;
				this._onStartEdit( _event.target, myTD.innerHTML, _event.target.domCol.wapAttr) ;
				_event.stopPropagation() ;
			} else if ( this.parentDS == null) {
				dTrace( 102, "wapGridXML.js", "wapGridXML", "onSelectCell( <_event>)", "I have no parent, I AM the parent") ;
				this.onSelectById( this.parent, myTD.innerHTML) ;
				_event.stopPropagation() ;
			}
		}
	}
	dEnd( 102, "wapGridXML.js", "wapGridXML", "onSelectCell( <_event>)") ;
}
/**
 *
 */
function	_getAttribute( _domCol, _attr, _default) {
	value	=	_domCol.getAttribute( _attr) ;
	if ( ! value) {
		value	=	_default ;
	}
	return value ;
}
function	_getFlag( _domCol, _attr, _default) {
	value	=	_domCol.getAttribute( _attr) ;
	if ( ! value) {
		value	=	_default ;
	} else {
		if ( value === "true" || value === "yes")
			value	=	false ;
	}
	return value ;
}
