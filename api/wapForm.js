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
 * wapForm
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2015-09-24	PA1		khw		inception
 *
 */
var	wapForms		=	new Object() ;
/**
 *
 * @param _screenName	name of the screen to be created
 * @return
 */
/**
 * @param _screenName
 * @returns {wapScreen}
 */
function	wapForm( _owner, _formName) {
	dBegin( 1, "wapForm.js", "wapForm", "wapForm( <_owner>, '"+_formName+"')") ;
	/**
	 *	register this form in the global pool
	 */
	wapForms[_formName]	=	this ;	// add this screen to the array of known screens
	dEnd( 1, "wapScreen.js", "wapScreen", "wapScreen( '"+_screenName+"')") ;
}
wapForm.prototype.displayAllData	=	function( _dataXML, _clear) {
	dBegin( 1, "wapScreen.js", "wapScreen", "displayAllData()") ;
	dTrace( 1, "wapScreen.js", "wapScreen", "displayAllData( <_reply>)", new XMLSerializer().serializeToString( _dataXML)) ;
	if ( ! this.myForms)
		this.collectForms() ;
	for ( var i=0 ; i<this.myForms.length ; i++) {
		dTrace( 1, "wapScreen.js", "wapScreen", "displayAllData()", "working on form '" + this.myForms[i].id + "'") ;
		this.displayData( this.myForms[i].id, _dataXML, _clear) ;
	}
	dEnd( 1, "wapScreen.js", "wapScreen", "displayAllData()") ;
} ;
/**
 * Display or verify given XML attributes in the form.
 * Clear the form beforehand if specified (only in display mode)
 * @param	_attrs	array	array of XML nodes containing the data to be displayed
 * @param	_form	string	name of the form to display the values to
 * @param	_clear	bool	clear form fields before output
 * @param	_verifyOnly	bool	perform verification only
 * @return	int				number of modified fields
 */
wapForm.prototype.displayData	=	function( _form, _dataXML, _clear, _verifyOnly) {
	var	changeCount	=	0 ;		// count of changed fields
	var l_lang ;				// user-language, needed for localization of numbers and dates
	var	myForm ;
	var	myFields ;				// array of all fields in _form
	var	actField ;
	var	processForm ;	// whether or not the form needs to be processed
	var	processField ;	// whether or not actField needs to be processed
	var	actFieldChanged ;
	var	confirmDiscard ;
	var	wapType, wapAttr, wapMode ;
	var	sourceVal	=	"" ;
	var	valueNode ;
	var	formattedValue ;
	var	dspValue ;				// displayed value in case of number and date in the localized version!
	var	myCoreObject	=	_dataXML.getElementsByTagName( this.coreObject)[0] ;

	dBegin( 1, "wapScreen.js", "wapScreen", "displayData( <_attrs>, '"+_form+"', <_clear>)") ;
	if ( ! _verifyOnly)
		_verifyOnly	=	false ;
	myForm	=	getForm( _form) ;
	/**
	 *	figure out the needed localization
	 */
	if (navigator.userLanguage)				// Explorer
		l_lang = navigator.userLanguage;
	else if (navigator.language)			// FF
		l_lang = navigator.language;
	else
		l_lang = "de";
	/**
	 *	IF we are in verification mode, see if form needs to be processed at all
	 */
	processForm	=	true ;
	if ( _verifyOnly) {
		var	confirmDiscard	=	myForm.dataset.wapConfirmDiscard ;
		if ( confirmDiscard) {
			if ( confirmDiscard == "false")
				processForm	=	false ;
		}
	}
	if ( myForm && myCoreObject && processForm) {
		if ( _clear && ! _verifyOnly)				// force-avoid clearing in verify-mode
			clearForm( _form) ;
//			dTrace( 1, "wapScreen.js", "wapScreen", "displayData( <_reply>)", new XMLSerializer().serializeToString( myCoreObject)) ;
		myFields	=	myForm.getElementsByClassName( "wapField") ;
		/**
		 * iterate through all the fields in the form
		 */
		for ( var i1=0 ; i1 < myFields.length ; i1++) {
			dTrace( 3, "wapScreen.js", "wapScreen", "displayData( ...)", "working on field # " + String( i1) + ", id := " + myFields[i1].id) ;
			actField	=	myFields[i1] ;						// HTML form input element
			/**
			 *	IF we are in verification mode, see if field needs to be processed at all
			 */
			processField	=	true ;
			if ( _verifyOnly) {
				var	confirmDiscard	=	actField.dataset.wapConfirmDiscard ;
				if ( confirmDiscard) {
					if ( confirmDiscard == "false")
						processField	=	false ;
				}
			}
			if ( processField) {
				actField.style.backgroundColor	=	"#ffffff" ;
				wapType	=	actField.dataset.wapType ;
				if ( wapType) {
					dTrace( 3, "wapScreen.js", "wapScreen", "displayData( ...)", "wapType is defined!") ;
					wapAttr	=	actField.dataset.wapAttr ;
					wapMode	=	actField.dataset.wapMode ;
					valueNode	=	myCoreObject.getElementsByTagName( wapAttr) ;
				} else if ( actField.id && actField.type != "button") {
					dTrace( 3, "wapScreen.js", "wapScreen", "displayData( ...)", "wapType is * NOT * defined!") ;
					valueNode	=	myCoreObject.getElementsByTagName( actField.id) ;
				}
				if ( valueNode[0]) {
					dTrace( 1, "wapScreen.js", "wapScreen", "displayData( <_reply>)", "valueNode[0]" + new XMLSerializer().serializeToString( valueNode[0])) ;
					actFieldChanged	=	false ;
					sourceVal	=	valueNode[0].childNodes[0].nodeValue ;		// value in datasource
					dTrace( 3, "wapScreen.js", "wapScreen", "displayData( ...)", "evaluates to '"+sourceVal+"'") ;
					switch ( wapType) {
					case	"tinyint"	:
					case	"smallint"	:
					case	"mediumint"	:
					case	"int"		:
					case	"bigint"	:
						if ( _verifyOnly) {
							if ( actField.value != sourceVal) {
								actField.style.background	=	"#ff8888" ;
								changeCount++ ;
							}
						} else {
							actField.value	=	sourceVal ;
						}
						break ;
					case	"text"	:		// H = Hidden
					case	"longtext"	:		// H = Hidden
						if ( _verifyOnly) {
							if ( actField.value != sourceVal) {
								actField.style.background	=	"#ff8888" ;
								changeCount++ ;
							}
						} else {
							actField.value	=	sourceVal ;
						}
						break ;
					case	"float"	:
					case	"double"	:
						if ( l_lang == "en") {
							formattedValue	=	sourceVal ;
						} else if ( l_lang == "de") {
							formattedValue	=	sourceVal.replace( ".", ",") ;
						}
						if ( _verifyOnly) {
							if ( actField.value != formattedValue) {
								actField.style.background	=	"#ff8888" ;
								changeCount++ ;
							}
						} else {
							actField.value	=	formattedValue ;
						}
						break ;
					case	"date"	:
						if ( l_lang == "en") {
							formattedValue	=	sourceVal ;
						} else if ( l_lang == "de") {
							formattedValue	=	zFill( parseInt( sourceVal.substr(8, 2)), 2) + "."
											+	zFill( parseInt( sourceVal.substr(5, 2)), 2) + "."
											+	zFill( parseInt( sourceVal.substr(0, 4)), 4) ;
						}
						if ( _verifyOnly) {
							if ( actField.value != formattedValue) {
								actField.style.background	=	"#ff8888" ;
								changeCount++ ;
							}
						} else {
							actField.value	=	formattedValue ;
						}
						break ;
					case	"rtf"	:
						if ( _verifyOnly) {
							if ( CKEDITOR.instances[actField.getAttribute( "id")].getData() != sourceVal) {
								changeCount++ ;
							}
						} else {
							CKEDITOR.instances[actField.getAttribute( "id")].setData( sourceVal) ;
						}
						break ;
					case	"option"	:
						var	selData	=	actField.options ;
						for ( var i=0 ; i < selData.length ; i++) {
							dTrace( 5, "wapScreen.js", "wapScreen", "displayData(...)", "'" + selData[i].value + " <-> '" + sourceVal + "'") ;
							if ( _verifyOnly) {
								for ( var i=0 ; i < selData.length ; i++) {
									dTrace( 905, "wapScreen.js", "wapScreen", "displayData(...)", "'" + selData[i].value + " <-> '" + sourceVal + "'") ;
									if ( selData[i].value == sourceVal) {
										if ( selData[i].selected != true) {
											actField.style.background	=	"#ff8888" ;
											changeCount++ ;
										dTrace( 1, "wapScreen.js", "wapScreen", "displayData( <_source>)", actField.id) ;
										}
									}
								}
							} else {
								if ( selData[i].value == sourceVal) {
									selData[i].selected	=	true ;
									selData.selectedIndex	=	i ;
								}
							}
						}
						break ;
					case	"check"	:
						if ( _verifyOnly) {
							if ( actField.value & sourceVal) {
								if ( actField.checked != true) {
									actField.style.background	=	"#ff8888" ;
									changeCount++ ;
									dTrace( 1, "wapScreen.js", "wapScreen", "displayData( <_source>)", actField.id) ;
								}
							} else {
								if ( actField.checked != false) {
									actField.style.background	=	"#ff8888" ;
									changeCount++ ;
									dTrace( 1, "wapScreen.js", "wapScreen", "displayData( <_source>)", actField.id) ;
								}
							}
						} else {
							actField.checked	=	false ;
							if ( actField.value & sourceVal) {
								actField.checked	=	true ;
							}
						}
						break ;
					case	"flag"	:
						var	fieldName	=	actField.name ;
						var	val	=	0 ;
						if ( _verifyOnly) {
							for ( var n=i1 ; n < myFields.length && myFields[n].name == fieldName; n++) {
								if ( parseInt( myFields[n].value) == parseInt( sourceVal)) {
									if ( myFields[n].checked != true) {
										actField.style.background	=	"#ff8888" ;
										changeCount++ ;
									dTrace( 1, "wapScreen.js", "wapScreen", "displayData( <_source>)", actField.id) ;
									}
								} else {
									if ( myFields[n].checked != false) {
										actField.style.background	=	"#ff8888" ;
										changeCount++ ;
									dTrace( 1, "wapScreen.js", "wapScreen", "displayData( <_source>)", actField.id) ;
									}
								}
							}
							i1	=	n - 1 ;
						} else {
							for ( var n=i1 ; n < myFields.length && myFields[n].name == fieldName; n++) {
								if ( parseInt( myFields[n].value) ==
										parseInt( sourceVal)) {
									myFields[n].checked	=	true ;
								} else {
									myFields[n].checked	=	false ;
								}
							}
							i1	=	n - 1 ;
						}
						break ;
					case	"image"	:
						if ( _verifyOnly) {
						} else {
							var	myImageRef	=	document.getElementById( actField.dataset.wapImageRef) ;
							myImageRef.src	=	"/api/dispatchXML.php?sessionId=" + sessionId
											+	"&_obj=" + actField.dataset.wapImageObj
											+	"&_fnc=" + "getImage"
											+	"&_key=" + encodeURIComponent( myCoreObject.getElementsByTagName( this.dataSource.objectKey)[0].childNodes[0].nodeValue)
											+	"&_id="
											+	"&_val=" + sourceVal ;
						}
						break ;
					case	"upload"	:
						dTrace( 4, "wapScreen.js", "wapScreen", "displayData(...)", "actField.type := '" + actField.type + "'") ;
						if ( _verifyOnly) {
						} else {
						}
						break ;
					default	:
						break ;
					}
				}
			}
		}
	} else {
		dTrace( 2, "wapScreen.js", "wapScreen", "displayData( ...)", "form['"+_form+"'] not found!") ;
	}
	dEnd( 1, "wapScreen.js", "wapScreen", "displayData( <_attrs>, '"+_form+"', <_clear>)") ;
	return changeCount ;
} ;
wapForm.prototype.verifyAllData	=	function( _dataXML) {
	dBegin( 1, "wapScreen.js", "wapScreen", "verifyAllData()") ;
	var	changeCount	=	0 ;
	if ( ! this.myForms)
		this.collectForms() ;
	for ( var i=0 ; i<this.myForms.length ; i++) {
		dTrace( 1, "wapScreen.js", "wapScreen", "verifyAllData()", "working on form '" + this.myForms[i].id + "'") ;
		changeCount	+=	this.displayData( this.myForms[i].id, _dataXML, false, true) ;
	}
	dEnd( 1, "wapScreen.js", "wapScreen", "verifyAllData(), result " + changeCount.toString()) ;
	return changeCount ;
} ;
/**
 * locate the wapForm _form in the given _scope
 */
function	getForm( _formId, _scope) {
	var	myForms ;
	var	targetForm ;
	/**
	 *	IF no screen has been loaded
	 *		=>	we do not have a valid 'screenCurrent'. Thus we need to set the scope for lookup
	 *			to the entire document.
	 *	ENDIF
	 */
	if ( ! _scope) {
		if ( ! screenCurrent) {
			_scope	=	document ;
		} else {
			_scope	=	screenCurrent.ownDiv ;
		}
	}
	if ( _scope) {
		/**
		 * find the form
		 */
		myForms	=	_scope.getElementsByClassName( "wapForm") ;
		for ( var i=0 ; i < myForms.length ; i++) {
			if ( myForms[i].id == _formId) {
				targetForm	=	myForms[i] ;
			}
		}
	}
	if ( targetForm == null && _scope != document) {
		myForms	=	document.getElementsByClassName( "wapForm") ;
		for ( var i=0 ; i < myForms.length ; i++) {
			if ( myForms[i].id == _formId) {
				targetForm	=	myForms[i] ;
			}
		}
	}
	return targetForm ;
}
/**
 * locate a named input element in a named form
 *
 * _form	name of the form
 * _field	name of the field
 *
 * return
 * reference to the input element or null
 */
function	getFormField( _formId, _fieldName, _scope) {
	var	myForm = null ;
	var	myFields ;
	var	targetField	=	null ;
	dBegin( 501, "common.js", "*", "getFormField( '"+_formId+"', '"+_fieldName+"')") ;
	/**
	 * find the form we shall fill out
	 */
	myForm	=	getForm( _formId, _scope) ;
	if ( myForm) {
		myFields	=	myForm.getElementsByClassName( "wapField") ;
		/**
		 * loop through all XML-attributes
		 */
		for ( var i=0 ; i < myFields.length && targetField == null ; i++) {
			if ( myFields[i].name == _fieldName) {
				targetField	=	myFields[i] ;
			}
		}
		if ( targetField) {
		} else {
			dTrace( 504, "common.js", "*", "getFormField()", "form["+_formId+"], field["+_fieldName+"] not found!") ;
		}
	} else {
		dTrace( 503, "common.js", "*", "getFormField()", "form["+_formId+"] not found!") ;
	}
	dEnd( 501, "common.js", "*", "getFormField( '"+_formId+"', '"+_fieldName+"')") ;
	return targetField ;
}
