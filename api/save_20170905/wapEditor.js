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
 * add/update editor dialog
 *
 * this general editor can be opened in two ways, either:
 * - to create a new record of dependent data or
 * - to edit an existing record of dependent data
 *
 * @param _owner	object which owns this object
 * @param _name		name of this editor
 * @param _attr		array with additional attributes
 */
var	itemEditors	=	new Object() ;
var	editorCurrent	=	null ;
function wapEditor( _owner, _name, _attr) {
	dBegin( 1, "wapEditor.js", "wapEditor", "__constructor( <_owner>, '"+_name+"', <_attr>)") ;
	this.owner =	_owner ;
	this.name	=	_name ;
	this.dialog	=	null; // ref. to the dijit.dialog
	/**
	 * get attributes from parameter-object
	 */
	this.parent	=	null ;			//
	for ( var i in _attr) {
		this[ i]	=	_attr[i] ;
	}
	/**
	 * do everything which depends on the parameters passed as an array
	 */
	this.dataSource	=	new wapDataSource( this, {
							object: 	this.objectClass
						,	parent:	this.parent
					}) ;
	this.dataSource.fncGet	=	"getDepAsXML" ;
	/**
	 * onDataSourceLoaded:	to be called when the main data has been loaded
	 */
	dTrace( 2, "wapEditor.js", "wapEditor", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 1, "wapEditor.js", "wapEditor", "onDataSourceLoaded( <...>)") ;
		if ( _xmlData) {
			dTrace( 2, "wapEditor.js", "wapEditor", "onDataSourceLoaded( ...)", "displaying main object data") ;
			this.displayAllData( _xmlData, true) ;
		}
		dEnd( 1, "wapEditor.js", "wapEditor", "onDataSourceLoaded( <...>)") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "wapEditor.js", "wapEditor", "*", "defining this.edit") ;
	this.startAdd	=	function(_key, _id, _val, _parent) {
		dBegin( 1, "wapEditor.js", "wapEditor", "startEdit( '"+_key+"', "+_id+", '"+_val+"')") ;
		dTrace( 1, "wapEditor.js", "wapEditor", "startEdit( '"+_key+"', "+_id+", '"+_val+"')", "this.object := '" + this.objectClass + "' \n");
		this.dataSource.parent	=	_parent ;
		editorCurrent	=	this ;
		this.mode	=	"add" ;
		this.href	=	"/api/loadScreen.php?"
					+	"sessionId="+sessionId + "&"
					+	"screen=" + (this.moduleName ? this.moduleName + "/" : "") + (this.subModuleName ? this.subModuleName + "/" : "") + this.editorName+".xml",
		this.dataSource.id	=	_id ;
		if (this.dialog != null)
			this.dialog.destroyRecursive();
		this.dialog	=	null ;
		this.dialog =	new wapPopup( this, "Editor", {url: this.href}) ;
		this.dialog.show() ;
	};
	/**
	 *
	 */
	dTrace( 2, "wapEditor.js", "wapEditor", "*", "defining this.edit") ;
	this.startUpdate	=	function(_key, _id, _val, _parent) {
		dBegin( 1, "wapEditor.js", "wapEditor", "startEdit( '"+_key+"', "+_id+", '"+_val+"')") ;
		dTrace( 1, "wapEditor.js", "wapEditor", "startEdit( '"+_key+"', "+_id+", '"+_val+"')", "this.object := '" + this.objectClass + "' \n");
		this.dataSource.parent	=	_parent ;
		editorCurrent	=	this ;
		this.mode	=	"update" ;
		this.href	=	"/api/loadScreen.php?"
					+	"sessionId="+sessionId + "&"
					+	"screen=" + (this.moduleName ? this.moduleName + "/" : "") + (this.subModuleName ? this.subModuleName + "/" : "") + this.editorName+".xml" ;
		this.dataSource.id	=	_id ;
		if (this.dialog != null)
			this.dialog.destroyRecursive();
		this.dialog	=	null ;
		this.dialog =	new wapPopup( this, "Editor", {url: this.href}) ;
		this.dialog.show();
	};
	/**
	 *
	 */
	dTrace( 1, "wapEditor.js", "wapEditor", "*", "defining onLoaded() \n");
	this.onScreenLoaded = function() {
		dBegin( 1, "wapEditor.js", "wapEditor", "onScreenLoaded( <...>)") ;
		/**
		 * perform additional initialization needed for the RTF editors
		 */
		dTrace( 2, "wapEditor.js", "wapEditor", "*", "pulling together RTF Editors") ;
		var	myDiv	=	document.getElementById( this.name) ;
		this.myEditors	=	myDiv.getElementsByClassName( "wapRTF") ;
		dTrace( 2, "wapEditor.js", "wapEditor", "*", "# of textareas := " + this.myEditors.length.toString()) ;
		for ( var i=0 ; i<this.myEditors.length ; i++) {
			this.myEditors[i].setAttribute( "name", this.myEditors[i].getAttribute( "name") + this.myEditors[i].getAttribute( "id")) ;
			dTrace( 2, "wapEditor.js", "wapEditor", "*", "this.name := '" + this.myEditors[i].getAttribute( "name") + "'") ;
			CKEDITOR.replace( this.myEditors[i].getAttribute( "name")) ;
		}
		this.myDate	=	myDiv.getElementsByClassName( "wapDate") ;
		for ( var i=0 ; i<this.myDate.length ; i++) {
			var	myRome	=	rome( this.myDate[i], { "inputFormat": "DD.MM.YYYY HH:mm:ss"}) ;
		}
		var	myRDs	=	document.getElementsByClassName( "rd-container") ;
		for ( var i=0 ; i<myRDs.length ; i++) {
			myRDs[i].style.zIndex	=	"200" ;
		}
		/**
		 * perform additional initialization needed for selectors
		 */
		dTrace( 2, "wapEditor.js", "wapEditor", "*", "pulling together selectors") ;
		myDiv	=	document.getElementById( this.name) ;
		this.mySelects	=	myDiv.getElementsByTagName( "select") ;
		dTrace( 2, "wapEditor.js", "wapEditor", "*", "# of selects := " + this.mySelects.length.toString()) ;
		for ( var i=0 ; i<this.mySelects.length ; i++) {
		}
		/**
		 *
		 */
		dEnd( 1, "wapEditor.js", "wapEditor", "onScreenLoaded( <...>) (pre-mature)") ;
		if ( this.dataSource.id === -1) {

		} else {

		}
		this.dataSource.load( this.parent.object, this.dataSource.id, this.objectClass) ;
	};
	/**
	 *
	 */
	dTrace( 1, "wapEditor.js", "wapEditor", "*", "defining add() \n");
	this.onCreate	=	function( _wd, _fnc, _form) {
		dBegin( 1, "wapEditor.js", "wapEditor", "onCreate( "+_wd+", '"+_fnc+"', '"+_form+"')") ;
		this.owner.dataSource.onCreate( "", this.dataSource.id, "", _form, null, document) ;
		/**
		 * cehck if this window shall remain open after submission
		 */
		var	keepBox = getFormField( _form, "wapEditorKeep");
		if ( keepBox) {
			if ( keepBox.checked === true) {
			} else {
				this.dialog.hide();
			}
		} else {
			this.dialog.hide();
		}
		if ( this.owner.onEditFinished)
			this.owner.onEditFinished() ;
		dEnd( 1, "wapEditor.js", "wapEditor", "onCreate() (pre-mature)") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapEditor.js", "wapEditor", "*", "defining onUpdate() \n");
	this.onUpdate	=	function( _wd, _fnc, _form) {
		dBegin( 1, "wapEditor.js", "wapEditor", "onUpdate( "+_wd+", '"+_fnc+"', '"+_form+"')") ;
		this.owner.dataSource.onUpdate( "", this.dataSource.id, "", _form, null, document) ;
		this.dialog.hide();
		if ( this.owner.onEditFinished)
			this.owner.onEditFinished() ;
		dEnd( 1, "wapEditor.js", "wapEditor", "onUpdate()") ;
	} ;
	this.onGo	=	function( _wd, _fnc, _form) {
		dBegin( 1, "wapEditor.js", "wapEditor", "onGo( "+_wd+", '"+_fnc+"', '"+_form+"')") ;
		switch ( this.mode) {
		case	"add"	:
			this.onCreate( false, null, this.myForms[0].id) ;
			break ;
		case	"update"	:
			this.onUpdate( false, null, this.myForms[0].id) ;
			break ;
		}
		dEnd( 1, "wapEditor.js", "wapEditor", "onGo()") ;
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapEditor.js", "wapEditor", "*", "defining onCancel() \n");
	this.onCancel = function( _wd, _fnc, _form) {
		dBegin( 1, "wapEditor.js", "wapEditor", "onCancel()") ;
		dEnd( 1, "wapEditor.js", "wapEditor", "onCancel() (pre-mature)") ;
		this.dialog.hide();
	} ;
	/**
	 *
	 */
	dTrace( 1, "wapEditor.js", "wapEditor", "*", "defining display() \n");
	this.display = function(response) {
		var myObject;
		var attrs;
		_debugL(0x00000001, "wapEditor.js::objEditor::display( ...): begin\n");
		_debugL(0x00000001, "wapEditor.js::objEditor: this.objToEdit := '"
				+ this.objToEdit + "' \n");
		/**
		 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet
		 * werden
		 */
		toField = getFormField("wapEditorObject", "_HKey");
		myVal = toField.value;
		myObject = response.getElementsByTagName(this.objToEdit)[0];
		if (myObject) {
			attrs = myObject.childNodes;
			dispAttrs(attrs, "wapEditorObject");
		}
		toField.value = myVal;
		_debugL(0x01000000, "wapEditor.js::objEditor::display( ...): end\n");
	} ;
	/**
	 * link to this screen
	 */
	dTrace( 1, "wapEditor.js", "wapEditor", "*", "defining fncLink() \n");
	this.fncLink	=	function() {
		dBegin( 1, "wapEditor.js", "wapEditor", "fncLink()") ;
		dEnd( 1, "wapEditor.js", "wapEditor", "fncLink()") ;
	} ;
	dEnd( 1, "wapEditor.js", "objEditor", "wapEditor( ...)") ;
}
/**
* collectForms
* ============
*
* pulls together an array which contains the names of all forms which belong to this screen
*/
wapEditor.prototype.collectForms	=	function() {
	dBegin( 1, "wapEditor.js", "wapEditor", "collectForms()") ;
	dTrace( 1, "wapEditor.js", "wapEditor", "collectForms()", "collecting forms for '" + this.name + "'") ;
	/**
	* find the <div> which contains our "screen"
	*/
	var	myDiv	=	document.getElementById( this.name) ;
	if ( myDiv) {
		/**
		* get an array containing all forms in our <div>
		*/
		this.myForms	=	myDiv.getElementsByClassName( "wapForm") ;
		for ( var i=0 ; i<this.myForms.length ; i++) {
			dTrace( 1, "wapEditor.js", "wapEditor", "collectForms()", "collected form '" + this.myForms[i].id + "'") ;
		}
	} else {
		dTrace( 1, "wapEditor.js", "wapEditor", "collectForms()", "can't find " + this.name + "<div>") ;
	}
	dEnd( 1, "wapEditor.js", "wapEditor", "collectForms()") ;
} ;
/**
 *
 */
wapEditor.prototype.displayAllData	=	function( _dataXML, _clear) {
	dBegin( 1, "wapEditor.js", "wapEditor", "displayAllData()") ;
	dTrace( 1, "wapEditor.js", "wapEditor", "displayAllData( <_reply>)", new XMLSerializer().serializeToString( _dataXML)) ;
	if ( ! this.myForms)
		this.collectForms() ;
	for ( var i=0 ; i<this.myForms.length ; i++) {
		dTrace( 1, "wapEditor.js", "wapEditor", "displayAllData()", "working on form '" + this.myForms[i].id + "'") ;
		this.displayData( this.myForms[i].id, _dataXML, _clear) ;
	}
	dEnd( 1, "wapEditor.js", "wapEditor", "displayAllData()") ;
} ;
/**
 * Display given XML attributes in the form. clear form beforehand if specified
 * @param	_attrs	array	array of XML nodes containing the data to be displayed
 * @param	_form	string	name of the form to display the values to
 * @param	_clear	bool	clear form fields before output
 * @return
 */
wapEditor.prototype.displayData	=	function( _form, _dataXML, _clear) {
	var l_lang ;
	var	actField ;
	var	wapType, wapAttr, wapMode ;
	var	sourceVal	=	"" ;								// value to be displayed
	var	valueNode ;
	var	selData ;
	/**
	 * find the form we shall fill out
	 */
	dBegin( 1, "wapEditorXML.js", "wapEditor", "displayData( <_attrs>, '"+_form+"', <_clear>)") ;
	var	myForm	=	getForm( _form, document) ;
	if (navigator.userLanguage) // Explorer
		l_lang = navigator.userLanguage;
	else if (navigator.language) // FF
		l_lang = navigator.language;
	else
		l_lang = "de";
	var	myCoreObject	=	_dataXML.getElementsByTagName( this.dataSource.object)[0] ;
	if ( myForm && myCoreObject) {
		if ( _clear)
			clearForm( _form) ;
		var	myFields	=	myForm.getElementsByClassName( "wapField") ;
		/**
		* iterate through all the fields in the form
		*/
		for ( var i1=0 ; i1 < myFields.length ; i1++) {
			dTrace( 3, "wapEditorXML.js", "wapEditor", "displayData( ...)", "working on field # " + String( i1) + ", id := " + myFields[i1].id) ;
			actField	=	myFields[i1] ;						// HTMNL form input element
			actField.style.backgroundColor	=	"#ffffff" ;
			wapType	=	actField.dataset.wapType ;
			if ( wapType) {
				dTrace( 3, "wapEditorXML.js", "wapEditor", "displayData( ...)", "wapType is defined!") ;
				wapAttr	=	actField.dataset.wapAttr ;
				wapMode	=	actField.dataset.wapMode ;
				valueNode	=	myCoreObject.getElementsByTagName( wapAttr) ;
			} else if ( actField.id && actField.type != "button") {
				dTrace( 3, "wapEditorXML.js", "wapEditor", "displayData( ...)", "wapType is * NOT * defined!") ;
				valueNode	=	myCoreObject.getElementsByTagName( actField.id) ;
			}
			if ( valueNode[0]) {
				dTrace( 1, "wapEditor.js", "wapEditor", "displayData( <_reply>)", "valueNode[0]" + new XMLSerializer().serializeToString( valueNode[0])) ;
				sourceVal	=	valueNode[0].childNodes[0].nodeValue ;
				dTrace( 3, "wapEditorXML.js", "wapEditor", "displayData( ...)", "evaluates to '"+sourceVal+"'") ;
				switch ( wapType) {
				case	"tinyint"	:
				case	"smallint"	:
				case	"mediumint"	:
				case	"int"		:
				case	"bigint"	:
					actField.value	=	sourceVal ;
					break ;
				case	"float"	:
				case	"double"	:
					if ( l_lang == "en") {
						actField.value	=	sourceVal ;
					} else if ( l_lang == "de") {
						actField.value	=	sourceVal.replace( ".", ",") ;
					}
					break ;
				case	"date"	:
					if ( l_lang == "en") {
						actField.value	=	sourceVal ;
					} else if ( l_lang == "de") {
						actField.value	=	zFill( parseInt( sourceVal.substr(8, 2)), 2) + "."
										+	zFill( parseInt( sourceVal.substr(5, 2)), 2) + "."
										+	zFill( parseInt( sourceVal.substr(0, 4)), 4) ;					}
					actField.style.backgroundColor	=	"#ffffff" ;
					break ;
				case	"datetime"	:
					if ( l_lang == "en") {
						actField.value	=	sourceVal ;
					} else if ( l_lang == "de") {
						actField.value	=	zFill( parseInt( sourceVal.substr(8, 2)), 2) + "."
										+	zFill( parseInt( sourceVal.substr(5, 2)), 2) + "."
										+	zFill( parseInt( sourceVal.substr(0, 4)), 4) + " "
										+	sourceVal.substr( 11, 8) ;
					}
					actField.style.backgroundColor	=	"#ffffff" ;
					break ;
				case	"text"	:		// H = Hidden
				case	"longtext"	:
					actField.style.backgroundColor	=	"#ffffff" ;
					actField.value	=	sourceVal ;
					break ;
				case	"html"	:
					dTrace( 4, "wapEditorXML.js", "wapEditor", "displayData(...)", "HTML field found := '" + myFields[i1].name + "'") ;
					for ( var i=0 ; i < attrs.length ; i++) {
						dTrace( 904, "wapEditorXML.js", "wapEditor", "displayData(...)", "HTML field found := '" + myFields[i1].name + "'") ;
						if ( myFields[i1].name.indexOf( attrs[i].nodeName) >= 0) {
							dTrace( 905, "wapEditorXML.js", "wapEditor", "displayData(...)", "HTML field found := '" + myFields[i1].name + "'") ;
							myEditor	=	dijit.byId( myFields[i1].name) ;
							myEditor.attr( 'value', attrs[i].childNodes[0].nodeValue) ;
							dTrace( 905, "wapEditorXML.js", "wapEditor", "displayData(...)", "Attribute value '" + attrs[i].childNodes[0].nodeValue + "'") ;
						}
					}
					break ;
				case	"rtf"	:
					CKEDITOR.instances[actField.getAttribute( "id")].setData( sourceVal) ;
					break ;
				case	"option"	:
					selData	=	actField.options ;
					for ( var i=0 ; i < selData.length ; i++) {
						dTrace( 5, "wapEditorXML.js", "wapEditor", "displayData(...)", "'" + selData[i].value + " <-> '" + sourceVal + "'") ;
						if ( selData[i].value == sourceVal) {
							selData[i].selected	=	true ;
							selData.selectedIndex	=	i ;
						}
					}
					break ;
				case	"check"	:
					actField.checked	=	false ;
					if ( actField.value & sourceVal) {
						actField.checked	=	true ;
					}
					break ;
				case	"flag"	:
					var	flagName	=	actField.name ;
					var	val	=	0 ;
					for ( var n=i1 ; n < myFields.length && myFields[n].name == flagName; n++) {
						if ( parseInt( myFields[n].value) ==
								parseInt( sourceVal)) {
							myFields[n].checked	=	true ;
						} else {
							myFields[n].checked	=	false ;
						}
					}
					i1	=	n - 1 ;
					break ;
				case	"image"	:
					var	myImageRef	=	document.getElementById( actField.dataset.wapImageRef) ;
					myImageRef.src	=	"/api/dispatchXML.php?sessionId=" + sessionId
									+	"&_obj=" + actField.dataset.wapImageObj
									+	"&_fnc=" + "getImage"
									+	"&_key=" + encodeURIComponent( myCoreObject.getElementsByTagName( this.dataSource.objectKey)[0].childNodes[0].nodeValue)
									+	"&_id="
									+	"&_val=" + sourceVal ;
//					Images/thumbs/" + sourceVal ;
					break ;
				case	"upload"	:
					dTrace( 4, "wapEditorXML.js", "wapEditor", "displayData(...)", "actField.type := '" + actField.type + "'") ;
					break ;
				default	:
					break ;
				}
			}
		}
	} else if ( myForm) {
		dTrace( 2, "wapEditorXML.js", "wapEditor", "displayData( ...)", "object['"+this.coreObject+"'] not found!") ;
	} else {
		dTrace( 2, "wapEditorXML.js", "wapEditor", "displayData( ...)", "form['"+_form+"'] not found!") ;
	}
	dEnd( 1, "wapEditorXML.js", "wapEditor", "displayData( <_attrs>, '"+_form+"', <_clear>)") ;
} ;
