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
 * wapTree.js
 * =============
 *
 * Product id.:
 * Version:
 *
 * Description
 * ===========
 *
 * A wapTree defines a table for the display of arbitrary data. The content of the table is defined as
 * per HTML content with wap specific extensions. These wap specific extensions define special attributes
 * of the values displayed as well as functions related to the displayed data.
 * A wapTree can be either of two types:
 *
 * 	-	either a tree of independent objects or
 * 	-	a tree of dependent objects
 *
 * A tree of independent objects does not have a owner datasource (parent := null).
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
 * @param	string	_owner			- reference to the owner object of this dataTree
 * @param	string	_name			- name (literal) of the dataTree (must be unique within the application)
 * @param	array	_attr			- additional attributes of the wagTree object to be instantiated
 * @return
 */
var	wapTrees	=	new Object() ;
function	wapTree( _owner, _name, _attr) {
	dBegin( 1, "wapTree.js", "wapTree", "__constructor( <_owner>, '"+_name+"', <_attr>)") ;
	this.owner	=	_owner ;
	wapTrees[ _name]	=	this ;
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
	this.parent	=	null ;		//  assume for now we are a tree for independent objects
	this.tdUnderEdit	=	null ;
	for ( var i in _attr) {
		this[ i]	=	_attr[i] ;
	}
	if ( this.parent !== null) {
		this.primObj	=	this.parent.object ;
	}
	this.xmlTableName	=	"table" + _name + "Data" ;
	/**
	 *	IF a treeDivName has been provided
	 *		initialize this object from the data in the HTML
	 */
	dTrace( 3, "wapTree.js", "wapTree", "__constructor()", "trying to find <div id='" + this.name + "'; will init from there ...") ;
	var	myDiv	=	document.getElementById( this.name) ;
	/**		create the dataSource for this tree		*/
	this.dataSource	=	new wapDataSource( this, {
							object: 		this.object
						,	fncGet: 		"getList"
						,	parent:		this.parent
						}) ;
	/**		determine the class of the object to be displayed in the tree		*/
	if ( this.parent !== null) {
		this.dataItemName	=	this.parent.object ;
	} else {
		this.dataItemName	=	this.object ;
	}
	/**
	 * check if an item editor exists, if not, create one
	 */
	dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>).................", _attr["formTop"]) ;
	if ( this.editorName) {
		var	edtName	=	this.editorName ;
		if ( itemEditors[edtName]) {
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "itemEditor['"+edtName+"'] exists") ;
		} else {
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "itemEditor['"+edtName+"'] does not exist") ;
			itemEditors[edtName]	=	new wapEditor( this, edtName, {
														module:			this.module
													,	moduleName: 	this.moduleName
													,	subModuleName:	this.subModuleName
													,	screen:			this.screen
													,	editorName:		this.editorName
													,	editorFormName:	this.editorFormName
													,	parent:		this.parent
													,	objectClass:	this.object
													,	onAddDone:		null
													,	onUpdDone:		null
											}) ;
		}
		if ( itemEditors[edtName]) {
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "itemEditor['"+edtName+"'] exists") ;
			this.dataItemEditor	=	itemEditors[edtName] ;
		} else {
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "itemEditor['"+edtName+"'] does not exist") ;
		}
	} else {
		dTrace( 3, "wapTree.js", "wapTree", "wapTree( <...>)", "_dataItemName := null") ;
	}
	/**
	 * clear()
	 * =======
	 *
	 * clears the data  portion of the table
	 */
	dTrace( 2, "wapTree.js", "wapTree", "wapTree()", "defining clear()") ;
	this.clear	=	function() {
		dBegin( 3, "wapTree.js", "wapTree", "wapTree()", "clear()") ;
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
			dTrace( 4, "wapTree.js", "wapTree", "clear()", "found html-table-header, has...: " + domTableHeader.rows.length.toString() + " rows") ;
			dTrace( 4, "wapTree.js", "wapTree", "clear()", "html-table-body, id............: " + domTableHeader.getAttribute( "id")) ;
			dTrace( 4, "wapTree.js", "wapTree", "clear()", "found html-table-body, has.....: " + domTableBody.rows.length.toString() + " rows") ;
			/**
			 * delete existing data rows
			 */
			dTrace( 4, "wapTree.js", "wapTree", "clear()", "will delete existing rows") ;
			var	rowCount	=	domTableBody.rows.length ;
			for ( var i=rowCount - 1 ; i >= 0 ; i--) {
				domTableBody.deleteRow( i) ;
			}
		} else {
			dTrace( 4, "wapTree.js", "wapTree", "clear()", "could not find html-table-body") ;
		}
		dEnd( 3, "wapTree.js", "wapTree", "clear()") ;
	} ;
	/**
	 * buildTHEAD creates a head section for an arbitrary result table coming as an XML response from the
	 * server
	 */
	dTrace( 2, "wapTree.js", "wapTree", "wapTree( <...>)", "defining build()") ;
	this.buildTHEAD	=	function( _response) {
		dTrace( 3, "wapTree.js", "wapTree", "wapTree( <...>)", "buildTHEAD( ..., '"+this.xmlTableName+"', ...): begin") ;
		/**
		 * find the table and delete all rows marked as 'wapRowType' = 'data'
		 */
		domTable	=	document.getElementById( this.xmlTableName) ;	// get <table></table>
		domTableHeader	=	domTable.tHead ;							// get <thead></thead>
		domTableBody	=	domTable.tBodies[0] ;						// get <tbody></tbody>
		if ( domTableHeader && domTableBody) {
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "found html-table-header, has...: " + domTableHeader.rows.length.toString() + " rows") ;
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "html-table-body, id............: " + domTableHeader.getAttribute( "id") + " rows") ;
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "found html-table-body, has.....: " + domTableBody.rows.length.toString() + " rows") ;
			/**
			 * delete existing data rows
			 */
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "wapTree.js::wapTree::buildTHEAD(): will delete existing rows") ;
			rowCount	=	domTableBody.rows.length ;
			for ( var i=rowCount - 1 ; i >= 0 ; i--) {
				domRow	=	domTableBody.rows[i] ;
				domTableBody.deleteRow( i) ;
			}
			/**
			 * add the data to the table
			 */
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "wapTree.js::wapTree::buildTHEAD(): will add new data to the table") ;
			dataTable	=	_response.getElementsByTagName( this.dataItemName) ;	// SPECIFIC
			if ( dataTable) {
				dTrace( 5, "wapTree.js", "wapTree", "wapTree( <...>)", "dataTable is valid") ;
				dTrace( 5, "wapTree.js", "wapTree", "wapTree( <...>)", "looking up first data item '"+this.dataItemName+"'") ;
				dataRow	=	_response.getElementsByTagName( this.dataItemName)[0] ;	// SPECIFIC
				var newTR	=	document.createElement("tr") ;
				newTR.setAttribute( "data-wap-row-type", "header");
				for ( var myCol = dataRow.firstChild ; myCol !== null ; myCol = myCol.nextSibling) {
					switch ( myCol.nodeType) {
					case	1	:
						dTrace( 7, "wapTree.js", "wapTree", "wapTree( <...>)", "buildTHEAD(): column := "+myCol.nodeName) ;
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
				dTrace( 5, "wapTree.js", "wapTree", "wapTree( <...>)", "could not find data-table in response") ;
			}
		} else if ( ! domTableHeader){
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "could not find html-table-header") ;
		} else {
			dTrace( 4, "wapTree.js", "wapTree", "wapTree( <...>)", "could not find html-table-body") ;
		}
		dTrace( 3, "wapTree.js", "wapTree", "wapTree( <...>)", "buildTHEAD( ..., '"+this.xmlTableName+"', ...): end") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "wapTree.js", "wapTree", "wapTree( <...>)", "defining onEditFinished()") ;
	this.onEditFinished	=	function() {
		dBegin( 1, "wapTree.js", "wapTree", "onEditFinished()") ;
		this._onRefresh() ;
		dEnd( 1, "wapTree.js", "wapTree", "onEditFinished()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "wapTree.js", "wapTree", "wapTree( <...>)", "defining show()") ;
	this.show	=	function() {
		this.onDataSourceLoaded( null, this.dataSource.xmlData) ;
	} ;
	/**
	 * _xmlData
	 */
	dTrace( 2, "wapTree.js", "wapTree", "wapTree( <...>)", "defining show()") ;
	this.onDataSourceLoaded	=	function( _owner, _xmlData) {
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
		 * find the table and delete all rows marked as 'wapRowType' = 'data'
		 */
		var	domTable	=	document.getElementById( this.xmlTableName) ;	// get <table></table>
		if ( domTable === null) {
			dTrace( 3, "wapTree.js", "wapTree", "show( ...)", "domTable not defined ---> will crashland") ;
		}
		dTrace( 3, "wapTree.js", "wapTree", "show( ...)", "domTable") ;
		var	domTableHeader	=	domTable.tHead ;							// get <thead></thead>
		dTrace( 3, "wapTree.js", "wapTree", "show( ...)", "domTable", "tBodies := " + domTable.tBodies.length.toString()) ;
		var	domTableBody	=	domTable.tBodies[0] ;						// get <tbody></tbody>
		dTrace( 3, "wapTree.js", "wapTree", "show( ...)", "domTable") ;
		/**
		 * only go-on if there's a valid table with head and body
		 */
		var	receivedDataset	=	_xmlData.getElementsByTagName( "Dataset")[0].childNodes[0].nodeValue ;
		var	receivedLevel	=	parseInt( _xmlData.getElementsByTagName( "Level")[0].childNodes[0].nodeValue) ;
		var	maxLevel	=	parseInt( _xmlData.getElementsByTagName( "MaxLevel")[0].childNodes[0].nodeValue) ;
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
				dTrace( 5, "wapTree.js", "wapTree", "onDataSourceLoaded()", "working on header.row := " + i.toString()) ;
				if ( ! domTableHeader.rows[i].dataset.wapObjectKey) {
					domTableHeader.rows[i].dataset.wapObjectKey	=	"Id" ;			// set default ...
				}
				domRow	=	domTableHeader.rows[i] ;
				wapRowType	=	( domRow.dataset.wapRowType ? domRow.dataset.wapRowType : "") ;
				var	wapDataset	=	( domRow.dataset.wapDataset ? domRow.dataset.wapDataset : "") ;
				if ( wapRowType === "header" && wapDataset === receivedDataset) {
					dTrace( 5, "wapTree.js", "wapTree", "onDataSourceLoaded()", "wapRowType := header") ;
					domHeaderRow	=	domRow ;
				} else if ( wapRowType === "data") {
				}
			}
			/**
			 * delete existing data rows, but only if this.lastTR is not set ()in which case we received additional data for this.lastTR!
			 */
			_debugL( 5, "wapTree.js::wapTree::show(): will delete existing rows\n") ;
			if ( ! this.lastTR) {
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
			}
			/**
			 * add the data to the table
			 */
			dTrace( 5, "wapTree.js", "wapTree", "show()", "will add new data to the table") ;
			dTrace( 5, "wapTree.js", "wapTree", "show()", "----------------------------------------------") ;
			var	rows	=	_xmlData.getElementsByTagName( this.object) ;
			dTrace( 5, "wapTree.js", "wapTree", "show()", "will add " + rows.length + " rows to the table") ;
			/**
			 *	iterate through all objects in the resultset
			 */
			for ( var col=0 ; domCol = domHeaderRow.getElementsByTagName("th")[col] ; col++) {
				domCol.dataset.wapLastGroupData	=	"" ;		// temporary marker
				domCol.dataset.wapLastDisplayData	=	"" ;		// temporary marker
				wapAttr	=	( domCol.dataset.wapAttr ? domCol.dataset.wapAttr : null) ;
				if ( wapAttr) {
					if ( this.colInfo[ wapAttr]) {
						dTrace( 8, "wapTree.js", "wapTree", "show()", "clearing wapAttr "+wapAttr) ;
						this.colInfo[ wapAttr].wapGroupMatch	=	"" ;
					}
				}
			}
			/**
			 * iterate through all rows of the result set
			 */
			for ( var il0=0 ; il0 < rows.length ; il0++) {
				dTrace( 6, "wapTree.js", "wapTree", "show()", "working on line # "+il0.toString()+" of "+rows.length+" rows") ;
				if ( this.lastTR) {
					var	rowNo	=	this.lastTR.rowIndex ;
					var	newTR	=	this.lastTR.parentNode.insertRow( rowNo + 1 - this.lastTR.parentNode.parentNode.tHead.rows.length) ;
					var	myRow	=	this.getBodyRow( newTR, domHeaderRow, rows[il0], il0) ;
				} else {
					var newTR	=	document.createElement("tr") ;
					var	myRow	=	this.getBodyRow( newTR, domHeaderRow, rows[il0], il0) ;
					domTableBody.appendChild( myRow) ;
				}
				newTR.setAttribute( "onclick", "wapTrees['" + this.name + "'].onSelectRow( event) ;") ;
				/**
				 *
				 */
				var	expandCellIndex	=	-1 ;
				for ( var i=0 ; i<newTR.cells.length && expandCellIndex === -1 ; i++) {
					if ( newTR.cells[i].dataset.wapAttr === domHeaderRow.dataset.wapTreeExpandattr) {
						expandCellIndex	=	i ;
					}
				}
				newTR.dataset.expandCellIndex	=	expandCellIndex ;
				/**
				 * get the key value
				 * and store as parameter in the TR
				 */
				var	myDataNode	=	rows[il0].getElementsByTagName( "Id") ;
				if ( myDataNode[0]) {
					var	myData	=	myDataNode[0].childNodes[0].nodeValue ;
				} else {
					var	myData	=	"void" ;
				}
				newTR.dataset.wapObjectId	=	"Id" ;
				newTR.dataset.wapObjectIdValue	=	myData ;
				/**
				 * get the key value
				 * and store as parameter in the TR
				 */
				var	myDataNode	=	rows[il0].getElementsByTagName( domHeaderRow.dataset.wapObjectKey) ;
				if ( myDataNode[0]) {
					var	myData	=	myDataNode[0].childNodes[0].nodeValue ;
				} else {
					var	myData	=	"void" ;
				}
				newTR.dataset.wapObjectKey	=	domHeaderRow.dataset.wapObjectKey ;
				newTR.dataset.wapObjectKeyValue	=	myData ;

				if ( ( receivedLevel + 1) <= maxLevel) {
					newTR.cells[ expandCellIndex].style.paddingLeft	=	( 16 * ( receivedLevel) + 3).toString() + "px" ;
					newTR.dataset.wapIsExpanded	=	"false" ;
					newTR.dataset.wapIsLoaded	=	"false" ;
					newTR.dataset.wapRowType	=	"data" ;

					var	myDiv	=	document.createElement( "div") ;
					myDiv.classList.add( "memu-icon") ;
					myDiv.classList.add( "sprite-folded") ;
					myDiv.classList.add( "indenter") ;
					myDiv.setAttribute( "onclick", "wapTrees['"+this.name+"'].expand( event, this) ;") ;
					newTR.cells[ expandCellIndex].insertBefore( myDiv, newTR.cells[ expandCellIndex].firstChild) ;
					newTR.dataset.wapLevel		=	( receivedLevel + 1).toString() ;
				} else {
					newTR.cells[ expandCellIndex].style.paddingLeft	=	( 16 * ( receivedLevel+1) + 3).toString() + "px" ;
					var	myDiv	=	document.createElement( "div") ;
//					mySpan.classList.add( "memu-icon") ;
//					mySpan.classList.add( "sprite-folded") ;
					myDiv.classList.add( "indenter") ;
					newTR.cells[ expandCellIndex].insertBefore( myDiv, newTR.cells[ expandCellIndex].firstChild) ;
					newTR.dataset.wapLevel		=	( receivedLevel + 1).toString() ;
				}
				this.lastTR	=	newTR ;
			}
			if ( this.lastTR) {
				delete this.lastTR ;
			}
		} else if ( ! domTableHeader){
			dTrace( 3, "wapTree.js", "wapTree", "show()", "could not find html-table-header\n") ;
		} else {
			dTrace( 3, "wapTree.js", "wapTree", "show()", "could not find html-table-body\n") ;
		}
		if ( this.onDataSourceLoadedExt) {
			this.onDataSourceLoadedExt( _owner, _xmlData) ;
		}
		if ( this.onDataSourceLoadFi) {
			this.onDataSourceLoadFi( _owner, _xmlData) ;
		}
		dEnd( 1, "wapTree.js", "wapTree", "show( ..., '"+this.xmlTableName+"', ...)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "wapTree.js", "wapTree", "wapTree( <...>)", "defining expand()") ;
	this.expand	=	function( _event, _div) {
		dBegin( 1, "wapTree.js", "wapTree", "expand( <_event>, <_tr>)") ;
		var _tr	=	_div.parentNode.parentNode ;							// remember the <tr> we worked on, will be uaed in 'this.onDataSourceLoaded'
		var	expandCellIndex	=	_tr.dataset.expandCellIndex ;
		this.lastTR	=	_tr ;
		if ( _tr.dataset.wapIsLoaded === "false") {
			dTrace( 2, "wapTree.js", "wapTree", "wapTree( <...>)", "will load ...") ;
			var	rowNo	=	_tr.rowIndex ;
			_tr.dataset.wapIsLoaded	=	"true" ;
			_tr.dataset.wapIsExpanded	=	"true" ;
			_tr.cells[ expandCellIndex].firstChild.classList.remove( "sprite-folded") ;
			_tr.cells[ expandCellIndex].firstChild.classList.add( "sprite-unfolded") ;
			if ( _tr.dataset.wapObjectKey === "Id") {
				this.dataSource.key	=	"" ;
				this.dataSource.id	=	_tr.dataset.wapObjectKeyValue ;
			} else {
				this.dataSource.key	=	_tr.dataset.wapObjectKeyValue ;
				this.dataSource.id	=	-1 ;
			}
			this.dataSource.refresh( "_treeLevel=" + _tr.dataset.wapLevel) ;
		} else if ( _tr.dataset.wapIsExpanded === "false") {
			_tr.dataset.wapIsExpanded	=	"true" ;
			_tr.cells[ expandCellIndex].firstChild.classList.remove( "sprite-folded") ;
			_tr.cells[ expandCellIndex].firstChild.classList.add( "sprite-unfolded") ;
//			_tr.classList.remove( "expanded") ;
//			_tr.classList.add( "folded") ;
			var	done	=	false ;
			var	levelToShow	=	parseInt( _tr.dataset.wapLevel) + 1 ;
			var	rows	=	_tr.parentNode.rows ;
			for ( var il0=_tr.rowIndex+1-_tr.parentNode.parentNode.tHead.rows.length ; done === false ; il0++) {
				if ( parseInt( rows[il0].dataset.wapLevel) == levelToShow) {
					rows[il0].style.display	=	"table-row" ;
				} else if ( parseInt( rows[il0].dataset.wapLevel) > levelToShow) {
				} else {
					done	=	true ;
				}
			}
		} else {
			_tr.dataset.wapIsExpanded	=	"false" ;
			_tr.cells[ expandCellIndex].firstChild.classList.remove( "sprite-unfolded") ;
			_tr.cells[ expandCellIndex].firstChild.classList.add( "sprite-folded") ;
//			_tr.classList.add( "folded") ;
//			_tr.classList.remove( "expanded") ;
			var	done	=	false ;
			var	levelToHide	=	parseInt( _tr.dataset.wapLevel) + 1 ;
			var	rows	=	_tr.parentNode.rows ;
			for ( var il0=_tr.rowIndex+1-_tr.parentNode.parentNode.tHead.rows.length ; done === false ; il0++) {
				if ( parseInt( rows[il0].dataset.wapLevel) >= levelToHide) {
					rows[il0].style.display	=	"none" ;
				} else {
					done	=	true ;
				}
			}
		}
		_event.stopPropagation() ;
		dEnd( 1, "wapTree.js", "wapTree", "expand( <_event>, <_tr>)") ;
	};
	/**
	 *
	 */
	dTrace( 2, "wapTree.js", "wapTree", "wapTree( <...>)", "defining collapse()") ;
	this.collapse	=	function( _event, _field) {

	} ;
	/**
	 *
	 */
	dTrace( 2, "wapTree.js", "wapTree", "wapTree( <...>)", "defining refresh()") ;
	this.refresh	=	function() {
		dBegin( 101, "wapTree.js", "wapTree", "refresh()") ;
		dTrace( 102, "wapTree.js", "wapTree", "refresh()", new XMLSerializer().serializeToString( this.dataSource.xmlData)) ;
		/**
		 * add the data to the table
		 */
		dataTable	=	this.dataSource.xmlData.getElementsByTagName( this.dataItemName) ;	// SPECIFIC
		dTrace( 102, "wapTree.js", "wapTree", "refresh()", "before if") ;
		if ( dataTable) {
			_debugL( 0x01000000, "wapTree.js::wapTree::refresh(...): dataTable := valid\n") ;
			for ( var i=0 ; i < dataTable.length ; i++) {
				_debugL( 0x01000000, "wapTree.js::wapTree::refresh(...): working on row # := " + i.toString() + "\n") ;
				dataRow	=	dataTable[i] ;	// SPECIFIC
				myId	=	dataRow.getElementsByTagName( this.idKey)[0].childNodes[0].nodeValue ;
				_debugL( 0x01000000, "wapTree.js::wapTree::refresh(...): working on Id # := " + myId + "\n") ;
				domTableBody	=	document.getElementById( this.xmlTableName).tBodies[0] ;					// SPECIFIC
				_debugL( 0x01000000, "wapTree.js::wapTree::refresh(...): row located \n") ;
				ndxTR	=	this.getBodyRowByIdIndex( domTableBody, parseInt( myId)) ;
				domTableBody.deleteRow( ndxTR) ;
				newTR	=	domTableBody.insertRow( ndxTR) ;
				this.getBodyRow( newTR, domHeaderRow, dataRow, i) ;
			}
		} else {
			dTrace( 103, "wapTree.js", "wapTree", "refresh()", "could not find data-table in response\n") ;
		}
		dEnd( 101, "wapTree.js", "wapTree", "refresh()") ;
	} ;
	/**
	 *
	 */
	this.showAdd	=	function( _response) {
		_debugL( 0x01000000, "wapTree.js::wapTree::showAdd( <_response>): begin\n") ;
		/**
		 *
		 */
		dataTable	=	_response.getElementsByTagName( this.dataItemName) ;	// SPECIFIC
		if ( dataTable) {
			_debugL( 0x01000000, "wapTree.js::wapTree::showAdd( <_response>): dataTable is valid\n") ;
			myRowIndex	=	this.getBodyRowByIdIndex( domTableBody, this.selectedId) ;
			for ( var i=0 ; i < dataTable.length ; i++) {
				_debugL( 0x01000000, "wapTree.js::wapTree::showAdd( <_response>): working on line " + i.toString() + "\n") ;
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
		_debugL( 0x01000000, "wapTree.js::wapTree::showAdd( <_response>): end\n") ;
	} ;
	this.getBodyRowByIdIndex	=	function( _domTableBody, _id) {
		_debugL( 0x01000000, "wapTree.js::wapTree::getBodyRowByIdIndex( <_domTableBody>, "+_id.toString()+"): begin\n") ;
		rowCount	=	_domTableBody.rows.length ;
		myRow	=	-1 ;
		for ( var rowNdx=0 ; rowNdx < rowCount && myRow == -1 ; rowNdx++) {
			_debugL( 0x01000000, "wapTree.js::wapTree::refresh(...): checking dom-row [" + rowNdx.toString() + "]\n") ;
			domRow	=	_domTableBody.rows[ rowNdx] ;
			domCell	=	domRow.cells[ colId] ;
			if ( domCell.innerHTML == _id) {
				_debugL( 0x01000000, "wapTree.js::wapTree::refresh(...): [" + rowNdx.toString() + "] is the one\n") ;
				myDomRow	=	domRow ;							// this is the row we need to update
				myRow	=	rowNdx ;
			}
		}
		_debugL( 0x01000000, "wapTree.js::wapTree::getBodyRowByIdIndex( <_domTableBody>, "+_id.toString()+"): end\n") ;
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
		dBegin( 601, "wapTree.js", "wapTree", "getBodyRow(...)") ;
		dTrace( 602, "wapTree.js", "wapTree", "getBodyRow()", new XMLSerializer().serializeToString( _obj)) ;
		var	domHeaderRow	=	_domHeaderRow ;
//		var	dataRow	=	_dataRow ;
		var newTR	=	_tr ;
		for ( var col=0 ; domCol = domHeaderRow.getElementsByTagName("th")[col] ; col++) {
			dTrace( 603, "wapTree.js", "wapTree", "getBodyRow(...)", "col := " + col.toString()) ;
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
				dTrace( 604, "wapTree.js", "wapTree", "wapAttr ["+wapAttr+"] is valid\n") ;
				myDataNode	=	_obj.getElementsByTagName( wapAttr) ;
				if ( myDataNode[0]) {
					myData	=	myDataNode[0].childNodes[0].nodeValue ;
					if ( wapSelKey == wapAttr)
						myKey	=	myData ;
					if ( wapAttr == this.idKey)
						myId	=	myData ;
					myCellId	=	wapAttr ;
					var	cellText ;
					if ( wapVT == "float") {
						cellText = document.createTextNode( myData.replace( ".", ",")) ;
					} else {
						cellText = document.createTextNode( myData) ;
					}
					if ( wapLinkTo) {
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
						if ( wapFunctions.search( /input/) != -1) {
							dTrace( 601, "wapDataTreeXML.js", "wapDataTreeXML", "getBodyRow(...)", "found input element") ;
							var	myFormName	=	"formDTVEditItem" + myCellId + myId.toString() ;
							var	newTD	=	document.createElement( "td") ;
							newTD.setAttribute( "onclick", "wapTrees['" + this.name + "']._onStartEdit( this, " + myId + ", '"+wapAttr+"') ;") ;
							newTD.setAttribute( "id", myCellId);
							newTD.setAttribute( "align", wapAlign) ;
							newTD.setAttribute( "data-wap-functions", wapFunctions);
							newTD.setAttribute( "data-wap-attr", wapAttr);
							newTD.setAttribute( "data-wap-v-t", wapVT);
							newTD.setAttribute( "data-obj", wapVT);
							newTD.setAttribute( "data-obj-id", myId);
							newTD.appendChild( cellText) ;
						} else if ( wapFunctions.search( /upload/) != -1) {
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
						} else if ( wapFunctions.search( /dig/) != -1) {
							var	newTD	=	document.createElement( "td") ;
							newTD.setAttribute( "id", myCellId);
							if ( myData != "") {
								myCount	=	parseInt( dataRow.getElementsByTagName( "_Expand")[0].childNodes[0].nodeValue) ;
								if ( myCount > 0) {
									myImgNode	=	document.createElement( "img") ;
									myImgNode.setAttribute( "src", "/Rsrc/img/tt-expand.png") ;
									myImgNode.setAttribute( "onclick", "wapTrees['" + this.name + "'].getSubTree('"+myData+"', "+myId.toString()+")") ;
									newTD.appendChild( myImgNode) ;
									myImgNode	=	document.createElement( "img") ;
									myImgNode.setAttribute( "src", "/Rsrc/img/tt-folder.png") ;
									newTD.appendChild( myImgNode) ;
								} else {
									newTD.setAttribute( "style", "padding-left: 32;") ;
								}
							} else {
								newTD.setAttribute( "style", "padding-left: 32;") ;
							}
							newTD.appendChild( cellText) ;
						} else {
							var	newTD	=	document.createElement( "td") ;
							newTD.setAttribute( "id", myCellId);
							newTD.appendChild( cellText) ;
						}
					} else {
						var	newTD	=	document.createElement( "td") ;
						newTD.setAttribute( "id", myCellId);
						newTD.setAttribute( "align", wapAlign) ;
						if ( wapAttr == this.idKey) {
							var	newDiv	=	document.createElement( "div") ;
							newDiv.setAttribute( "class", "memu-icon sprite-goto") ;
							newDiv.setAttribute( "onclick", "wapTrees['" + this.name + "']._onSelectById( '" + myId + "') ;") ;
							newTD.appendChild( newDiv) ;
							newDiv	=	null ;
						}
						if ( linkButton) {
							newTD.appendChild( linkButton) ;
							linkButton	=	null ;
						}
						if ( openURL) {
							newTD.appendChild( openURL) ;
						} else {
							newTD.appendChild( cellText) ;
						}
						newTD.dataset.wapAttr	=	wapAttr ;
					}
					newTD.setAttribute( "colspan", domCol.getAttribute( "colspan")) ;
					newTD.setAttribute( "onclick", "wapTrees['" + this.name + "'].onSelectCell( event) ;") ;
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
//		newTR.setAttribute( "onclick", "wapTrees['" + this.name + "']._onSelectById( '" + myId + "') ;");
		newTR.setAttribute( "style", "background-Color: rgb( 221, 255, 221);") ;
		newTR.setAttribute( "onMouseOver", "this.style.backgroundColor='#dddddd'") ;
		newTR.setAttribute( "onMouseOut", "this.style.backgroundColor='#ddffdd'") ;
		dEnd( 601, "wapDataTreeXML.js", "wapDataTreeXML", "getBodyRow(...)") ;
		return newTR ;
	} ;
	/**
	 * addItem
	 */
	dTrace( 2, "wapTree.js", "wapTree", "wapTree( <...>)", "defining editItem()") ;
	this.addItem	=	function() {
		dBegin( 1, "wapTree.js", "wapTree", "addItem()") ;
		this.dataItemEditor.startAdd( this.dataItemName, -1, "", this.parent) ;
		dEnd( 1, "wapTree.js", "wapTree", "addItem()") ;
	} ;
	this.editItem	=	function( _id) {
		dBegin( 1, "wapTree.js", "wapTree", "editItem( '"+_id+"')") ;
		this.dataItemEditor.startUpdate( this.dataItemName, _id, "", this.parent) ;
		dEnd( 1, "wapTree.js", "wapTree", "editItem( '"+_id+"')") ;
	} ;
	this.deleteItem	=	function( _id) {
		dBegin( 1, "wapTree.js", "wapTree", "deleteItem( '"+_id+"')") ;
		this.dataSource.id	=	_id ;
		dEnd( 1, "wapTree.js", "wapTree", "deleteItem( '"+_id+"') pre-mature") ;
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
		_debugL( 0x00000001, "wapTree.js::wapTree::selected(" + _id.toString() + "): begin\n") ;
		this.owner.selected( _id) ;
		_debugL( 0x00000001, "wapTree.js::wapTree::selected(): end\n") ;
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
		dBegin( 1, "wapTree.js", "wapTree", "getSubTree( '" + _key + "', "+_ndx.toString() + ")") ;
		reqData( this.phpHandler, this.primObj, this.phpGetCall, _key, 'f50', this.dataItemName, this, null, null);
		this.selectedId	=	_ndx ;
		this.onDone	=	this.showAdd ;
		dEnd( 1, "wapTree.js", "wapTree", "getSubTree(" + _key + "', "+_ndx.toString() + ")") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onSelectById()") ;
	this._onSelectById	=	function( _id) {
		dBegin( 102, "wapTree.js", "wapTree", "_onSelectById") ;
		/**
		 * if onSelectById() method is defined ... call it
		 */
		if ( this.onSelectById) {
			dTrace( 104, "wapTree.js", "wapTree", "_onSelectById(...)", "onSelectById-callBack defined") ;
			this.onSelectById( this.owner, _id) ;
		}
		dEnd( 102, "wapTree.js", "wapTree", "_onSelectById") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining search()") ;
	this.search	=	function( _event) {
		dBegin( 102, "wapTree.js", "wapTree", "search") ;
		myField	=	getFormField( this.formTop, "Search") ;
		if ( _event.keyCode === 13|| myField.value.length > 3) {
			return this.searchWE() ;
		}
		dEnd( 102, "wapTree.js", "wapTree", "search") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining searchWE()") ;
	this.searchWE	=	function() {
		dBegin( 102, "wapTree.js", "wapTree", "searchWE") ;
		this.dataSource.thisPage( this.formTop) ;
		dEnd( 102, "wapTree.js", "wapTree", "searchWE") ;
		return false ;
	} ;
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onFirstPage()") ;
	this._onFirstPage	=	function() {
		dBegin( 102, "wapTree.js", "wapTree", "_onFirstPage") ;
		if ( this.formFilterName)
			this.dataSource.firstPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.firstPage( this.formTop) ;
		dEnd( 102, "wapTree.js", "wapTree", "_onFirstPage") ;
	} ;
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onPreviousPage()") ;
	this._onPreviousPage	=	function() {
		dBegin( 102, "wapTree.js", "wapTree", "_onPreviousPage") ;
		if ( this.formFilterName)
			this.dataSource.previousPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.previousPage( this.formTop) ;
		dEnd( 102, "wapTree.js", "wapTree", "_onPreviousPage") ;
	} ;
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onOneBackward()") ;
	this._onOneBackward	=	function() {
		dBegin( 102, "wapTree.js", "wapTree", "_onOneBackward") ;
		if ( this.formFilterName)
			this.dataSource.oneBackward( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.oneBackward( this.formTop) ;
		dEnd( 102, "wapTree.js", "wapTree", "_onOneBackward") ;
	} ;
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onRefresh()") ;
	this._onRefresh	=	function() {
		dBegin( 102, "wapTree.js", "wapTree", "_onRefresh") ;
		if ( this.formFilterName)
			this.dataSource.thisPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.thisPage( this.formTop) ;
		dEnd( 102, "wapTree.js", "wapTree", "_onRefresh") ;
		return false ;
	} ;
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onOneForward()") ;
	this._onOneForward	=	function() {
		dBegin( 102, "wapTree.js", "wapTree", "_onOneForward") ;
		if ( this.formFilterName)
			this.dataSource.oneForward( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.oneForward( this.formTop) ;
		dEnd( 102, "wapTree.js", "wapTree", "_onOneForward") ;
	} ;
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onNextPage()") ;
	this._onNextPage	=	function() {
		dBegin( 102, "wapTree.js", "wapTree", "_onNextPage") ;
		if ( this.formFilterName)
			this.dataSource.nextPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.nextPage( this.formTop) ;
		dEnd( 102, "wapTree.js", "wapTree", "_onNextPage") ;
	} ;
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onLastPage()") ;
	this._onLastPage	=	function() {
		dBegin( 102, "wapTree.js", "wapTree", "_onLastPage") ;
		if ( this.formFilterName)
			this.dataSource.lastPage( { f1: this.formTop, f2: this.formFilterName}) ;
		else
			this.dataSource.lastPage( this.formTop) ;
		dEnd( 102, "wapTree.js", "wapTree", "_onLastPage") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onStartEdit()") ;
	this._onStartEdit	=	function( _td) {
		dBegin( 102, "wapTree.js", "wapTree", "_onStartEdit( <_td>)") ;
		if ( this.tdUnderEdit !== null) {
			this._terminateEdit() ;
		}
		this.tdUnderEdit	=	_td ;
		var	myTR	=	_td.parentNode ;
		this.cellInnerHTML	=	_td.innerHTML ;
		_td.removeAttribute( "onclick") ;
		var	newInput	=	document.createElement( "input") ;
		newInput.setAttribute( "type", "text") ;
		newInput.setAttribute( "id", "abcde") ;
		newInput.setAttribute( "name", "abcde") ;
		newInput.setAttribute( "size", "16") ;
		newInput.setAttribute( "maxlength", "32") ;
		newInput.setAttribute( "data-wap-v-t", ( _td.dataset.wapVT ? _td.dataset.wapVT : "data")) ;
		newInput.setAttribute( "onKeypress", "wapTrees['" + this.name + "']._onKeypress( this, event, "+myTR.dataset.wapObjectIdValue.toString()+", '"+_td.dataset.wapAttr+"') ;") ;
		newInput.value	=	_td.firstChild.nodeValue ;
		while ( _td.firstChild) {
			_td.removeChild( _td.firstChild);
		}
		_td.appendChild( newInput) ;
		dEnd( 102, "wapTree.js", "wapTree", "_onStartEdit") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onEndEdit()") ;
	this._onEndEdit	=	function( _inp, _id, _attr) {
		dBegin( 102, "wapTree.js", "wapTree", "_onEndEdit( "+_inp+", "+_id+", '"+_attr+"')") ;
		var	wapVT			=	( _inp.dataset.wapVT ? _inp.dataset.wapVT : "data") ;
		if ( wapVT == "float") {
			var	myData	=	_inp.value.replace( ",", ".") ;
		} else {
			var	myData	=	_inp.value ;
		}
		this.dataSource.id	=	_id ;
		this.dataSource.dispatch( true, "updDep", null, _attr+"="+myData) ;
		this._terminateEdit() ;
		dEnd( 102, "wapTree.js", "wapTree", "_onEndEdit") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _terminateEdit()") ;
	this._terminateEdit	=	function() {
		dBegin( 102, "wapTree.js", "wapTree", "_terminateEdit()") ;
		var	_obj	=	this.tdUnderEdit ;
		while ( _obj.firstChild) {
			_obj.removeChild( _obj.firstChild);
		}
		this.tdUnderEdit.innerHTML	=	this.cellInnerHTML ;
		this.tdUnderEdit.setAttribute( "onclick", "wapTrees['" + this.name + "']._onStartEdit( this, " + this.tdUnderEdit.getAttribute( "objId") + ", '"+this.tdUnderEdit.dataset.wapAttr+"') ;") ;
		this.tdUnderEdit	=	null ;
		dEnd( 102, "wapTree.js", "wapTree", "_terminateEdit") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining _onKeypress()") ;
	this._onKeypress	=	function( _obj, _event, _id, _attr) {
		dBegin( 102, "wapTree.js", "wapTree", "_onKeypress") ;
		if ( _event.keyCode === 13) {
			dTrace( 102, "wapTree.js", "wapTree", "_onKeypress", "Enter was pressed") ;
			this._onEndEdit( _obj, _id, _attr) ;
			_event.stopPropagation() ;
		}
		dEnd( 102, "wapTree.js", "wapTree", "_onKeypress") ;
	} ;
	/**
	 *
	 * @returns
	 */
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining btnCustom()") ;
	this.btnCustom	=	function( _id, _method) {
		ret =	"<div class=\"memu-icon sprite-calc\" onclick=\"wapTrees['" + this.name + "'].custom( '" + _id + "', '" + _method + "') ;\"></div>" ;
		return ret ;
	} ;
	/**
	 *
	 * @param _owner
	 * @param _id
	 * @returns {String}
	 */
	dTrace( 1, "wapTree.js", "wapTree", "wapTree( <...>)", "defining btnEdit()") ;
	this.btnEdit	=	function( _id) {
		return "<div class=\"memu-icon sprite-edit\" onclick=\"wapTrees['" + this.name + "'].editItem( '" + _id + "') ;\"></div>" ;
	} ;
	this.btnDelete	=	function( _id) {
		return "<div class=\"memu-icon sprite-garbage\" onclick=\"wapTrees['" + this.name + "'].deleteItem( '" + _id + "') ;\"></div>" ;
	} ;
	this.btnExpand	=	function( _id) {
		return "<td><input type=\"image\" src=\"/Rsrc/licon/Blue/18/object_10.png\" onclick=\"wapTrees['" + this.name + "'].expandDep( '" + _id + "') ;\" /></td>" ;
	} ;

	this.btnCollapse	=	function( _id) {
		return "<td><input type=\"image\" src=\"/Rsrc/licon/Blue/18/object_11.png\" onclick=\"wapTrees['" + this.name + "'].collapseDep( '" + _id + "') ;\" /></td>" ;
	} ;

	this.btnMoveDown	=	function( _id) {
		return "<div class=\"memu-icon sprite-idown\" onclick=\"wapTrees['" + this.name + "'].moveDepDown( '" + _id + "') ;\"></div>" ;
	} ;

	this.btnMoveUp	=	function( _id) {
		return "<div class=\"memu-icon sprite-iup\" onclick=\"wapTrees['" + this.name + "'].moveDepUp( '" + _id + "') ;\"></div>" ;
	} ;

	this.btnDec	=	function( _id, _attr) {
		return "<div class=\"memu-icon sprite-qminus\" onclick=\"wapTrees['" + this.name + "'].decDep( '" + _id + "', '" + _attr + "') ;\"></div>" ;
	} ;

	this.btnInc	=	function( _id, _attr) {
		return "<div class=\"memu-icon sprite-qplus\" onclick=\"wapTrees['" + this.name + "'].incDep( '" + _id + "', '" + _attr + "') ;\"></div>" ;
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
		return "<input type=\"image\" src=\"/Rsrc/licon/yellow/18/door.png\" onclick=\"wapTrees['" + this.name + "'].selected( '" + _id + "') ;\" /></td>" ;
	} ;
	this.btnAdd	=	function( _id) {
		return "<input type=\"image\" src=\"/Rsrc/licon/Green/18/add.png\" onclick=\"" + this.myObjName + ".addDep( '" + _id + "') ;\" />" ;
	} ;
	this.getData	=	function( 	_id, _form, _filter) {
		var	url ;
		var	postVars ;
		dBegin( 1, "wapTree.js", "wapTree", "getData(...)") ;
		dTrace( 1, "wapTree.js", "wapTree", "getData(...)", "objKey := '" + this.primObjKey + "'") ;
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
			dTrace( 3, "wapTree.js", "wapTree", "getData(...)", "getting data from form [" + _form + "]\n") ;
			postVars	=	getPOSTData( _form) ;
			if ( this.filter) {
				if ( this.filter !== "") {
					_debugL( 0x00000001, "..................." + this.filter) ;
					postVars	+=	"&" + getPOSTData( this.filter) ;
				}
			}
		} else {
			if ( this.filter) {
				dTrace( 3, "wapTree.js", "wapTree", "getData(...)", "using filter [" + _form + "]\n") ;
				if ( this.filter !== "") {
					postVars	+=	getPOSTData( this.filter) ;
				}
			}
		}
		dTrace( 2, "wapTree.js", "wapTree", "getData(...)", "about to post the request") ;
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
		dEnd( 1, "wapTree.js", "wapTree", "getData(...)") ;
		return false ;
	} ;
	dEnd( 1, "wapTree.js", "wapTree", "wapTree( ...)") ;
}
/**
 *
 */
wapTree.prototype.onSelectRow	=	function( _event) {
	dBegin( 102, "wapTree.js", "wapTree", "onSelectRow( <_event>)") ;
	var	stopped	=	false ;
	var	myTR	=	_event.target.parentNode ;
	if ( this.tdUnderEdit !== null) {

	} else if ( this.parent === null) {
		dTrace( 102, "wapTree.js", "wapTree", "onSelectRow() <_event>)", "parent === null, _event.target.tagName = '" + _event.target.tagName + "'") ;
		if ( myTR.dataset.wapObjectKey == "Id") {
			dTrace( 102, "wapTree.js", "wapTree", "onSelectRow( <_event>)", "wapObjectKey == \"Id\"") ;
			this.onSelectById( this.owner, myTR.dataset.wapObjectKeyValue) ;
			_event.stopPropagation() ;
			stopped	=	true ;
		} else {
			dTrace( 102, "wapTree.js", "wapTree", "onSelectRow() <_event>)", "wapObjectKey != \"Id\"") ;
			this.onSelectByKey( this.owner, myTR.dataset.wapObjectKeyValue) ;
			_event.stopPropagation() ;
			stopped	=	true ;
		}
	}
	dEnd( 102, "wapTree.js", "wapTree", "onSelectRow( <_event>)") ;
}
/**
 *
 */
wapTree.prototype.onSelectColumn	=	function( _event) {
	dBegin( 102, "wapTree.js", "wapTree", "onSelectColumn( <_event>)") ;
	dEnd( 102, "wapTree.js", "wapTree", "onSelectColumn( <_event>)") ;
}
/**
 * _event	fires from teh cell => _event.target == TD node
 */
wapTree.prototype.onSelectCell	=	function( _event) {
	dBegin( 102, "wapTree.js", "wapTree", "onSelectCell( <_event>)") ;
	var	stopped	=	false ;
	var	myTR	=	_event.target.parentNode ;
	dTrace( 102, "wapTree.js", "wapTree", "onSelectCell( <_event>)", "Value := '" + _event.target.innerHTML + "'") ;
	if ( this.parent !== null || _event.target.domCol.dataset.wapEditAs) {
		this._onStartEdit( _event.target) ;
		_event.stopPropagation() ;
	}
	dEnd( 102, "wapTree.js", "wapTree", "onSelectCell( <_event>)") ;
}
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
