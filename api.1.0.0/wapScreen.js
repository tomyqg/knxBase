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
 * wapScreen
 *
 * this is the base object for any screen which needs to be displayed in the application.
 * it provides methods for sending requests to the server, ie.
 * qDispatch	-	quick Dispatch, needs dialog false/true and function to be executed on remote side
 * sDispatch	-	short Dispatch, needs dialog false/true, function to be executed on remote side and
 * 					form to be transmitted
 * dispatch			normal Dispatch, needs all as stated above plus _key, _id and _val
 */
var	wapScreens		=	new Object() ;		// here we keep track of all instantiated screens, indexed by _screenName
var	screenCurrent	=	null ;				// here we keep track of the current screen
var	pendingKey ;
var	pendingFnc ;
var	debugFrames	=	true ;
/**
 *
 * @param _screenName	name of the screen to be created
 * @return
 */
/**
 * @param _screenName
 * @returns {wapScreen}
 */
function	wapScreen( _screenName) {
	dBegin( 1, "wapScreen.js", "wapScreen", "wapScreen( '"+_screenName+"')") ;
	/**
	 *	IF a screen with this _screenName is already loaded, remove it. we want to start from
	 *	scratch.
	 */
	wapScreens[_screenName]	=	this ;	// add this screen to the array of known screens
	this.lastKey	=	"" ;
//	this.lastData1	=	"" ;
//	this.lastData2	=	"" ;
//	this.lastImage	=	"" ;
	this.screenName	=	_screenName ;
	this.fncLink	=	null ;
	this.fncNew	=	null ;
	this.fncCopy	=	null ;
	this.fncSaveTab	=	null ;
	this.fncSaveAllTabs	=	null ;
	this.fncDelete	=	null ;
	this.fncPDFCreate	=	null ;
	this.fncPDFShow	=	null ;
	this.fncPDFPrint	=	null ;
	this.fncReload	=	null ;
	this.autoClose	=	false ;
	this.myForms	=	[] ;
	/**
	 *	try to initialize from the <div id=<_screenName> ...>.
	 */
	dTrace( 1, "wapScreen.js", "wapScreen", "*()", "trying to find <div id='"+this.screenName+"'>!") ;
	this.ownDiv	=	document.getElementById( this.screenName) ;
	if ( this.ownDiv) {
		dTrace( 2, "wapScreen.js", "wapScreen", "*()", "found 'ownDiv', will init from here ...") ;
		this.screenDiv	=	this.ownDiv.getElementsByClassName( "wapScreen")[0] ;
		if ( this.screenDiv) {
			/**
			 * attach handlers for: previous-button, next-button and enter-key-field
			 */
			$( this.screenDiv).find( ".prevObject").click( function() {
																wapScreens[_screenName].onPrev() ;
															}) ;
			$( this.screenDiv).find( ".nextObject").click( function() {
																wapScreens[_screenName].onNext() ;
															}) ;
			$( this.screenDiv).find( ".wapPrimKey").keypress( function( e) {
																wapScreens[_screenName].enterKey( e) ;
															}) ;
			$( ".wapBtnCreate", "#"+_screenName).on( "click", { screen: this}, function( e) {
																e.data.screen.onCreate( e) ;
															}) ;
			$( ".wapBtnUpdate", "#"+_screenName).on( "click", { screen: this}, function( e) {
																e.data.screen.onUpdate( e) ;
															}) ;
			$( ".wapBtnDelete", "#"+_screenName).on( "click", { screen: this}, function( e) {
																e.data.screen.onDelete( e) ;
															}) ;
			$( ".wapBtnMisc", "#"+_screenName).on( "click", { screen: this}, function( e) {
																e.data.screen.onMisc( e) ;
															}) ;
			$( ".wapBtnCancel", "#"+_screenName).on( "click", { screen: this}, function( e) {
																e.data.screen.onCancel( e) ;
															}) ;
			/**
			 * create the datasource for the main object, it's needed through out the initialisation of
			 * gridViews and treeViews
			 */
			this.dataSource	=	new wapDataSource( this, {
												object:			this.screenDiv.dataset.wapCoreObject
											,	objectKey:		this.screenDiv.dataset.wapCoreObjectKey
								}) ;		// dataSource for display
			/**
			 *
			 */
			dTrace( 2, "wapScreen.js", "wapScreen", "*()", "initializing from <div id='"+this.ownDiv.id+"'>!") ;
			this.keyForm	=	this.screenDiv.getElementsByClassName( "wapKeyForm")[0].id ;
			this.keyField	=	getFormField( this.keyForm, this.screenDiv.dataset.wapCoreObjectKey) ;
			/**
			 *
			 */
			dTrace( 2, "wapScreen.js", "wapScreen", "*()", "initializing from <div id='"+this.ownDiv.id+"'>!") ;
			this.myTabs	=	this.ownDiv.getElementsByClassName( "wapTabPage") ;
			for ( var il0=0 ; il0<this.myTabs.length ; il0++) {
				dTrace( 3, "wapScreen.js", "wapScreen", "*()", "working on tab ...", this.myTabs[il0].id) ;
				var	actTab	=	this.myTabs[il0] ;
				/** 	mark <div> for diagnostics			*/
				if ( debugFrames === true) {
					actTab.style.borderWidth	=	"2px" ;
					actTab.style.borderStyle	=	"dotted" ;
					actTab.style.borderColor	=	"red" ;
				}
			}
			var	myTabContainer	=	this.ownDiv.getElementsByClassName( "wapTabContainer") ;
			if ( myTabContainer[0]) {
				showTab( myTabContainer[0].dataset.wapActiveTabOnload) ;
			}
			/**
			 *
			 */
			dTrace( 2, "wapScreen.js", "wapScreen", "*()", "initializing from <div id='"+this.ownDiv.id+"'>; looking up wapForms") ;
			this.myForms	=	this.ownDiv.getElementsByClassName( "wapForm") ;
			for ( var il0=0 ; il0<this.myForms.length ; il0++) {
				var	actForm	=	this.myForms[il0] ;
				/** 	mark <div> for diagnostics			*/
				if ( debugFrames === true) {
					actForm.style.borderWidth	=	"1px" ;
					actForm.style.borderStyle	=	"dotted" ;
					actForm.style.borderColor	=	"red" ;
				}
				/** 	marke <div> for diagnostics			*/
				dTrace( 3, "wapScreen.js", "wapScreen", "*()", "working on form ...'" + actForm.id + "'") ;
				var	myEditors	=	actForm.getElementsByClassName( "wapRTF") ;
				dTrace( 2, "wapScreen.js", "wapScreen", "*", "# of textareas := " + myEditors.length.toString()) ;
				for ( var i=0 ; i<myEditors.length ; i++) {
					myEditors[i].setAttribute( "name", myEditors[i].getAttribute( "name") + myEditors[i].getAttribute( "id")) ;
					dTrace( 2, "mainArticleGroup.js", "mainArticleGroup", "*", "this.name := '" + myEditors[i].getAttribute( "name") + "'") ;
					CKEDITOR.replace( myEditors[i].getAttribute( "name")) ;
				}
			}
			/**
			 *
			 */
			dTrace( 2, "wapScreen.js", "wapScreen", "*()", "initializing from <div id='"+this.ownDiv.id+"'>; looking up wapForms") ;
			this.myGrids	=	this.ownDiv.getElementsByClassName( "wapGrid") ;
			for ( var il0=0 ; il0<this.myGrids.length ; il0++) {
				var	actGrid	=	this.myGrids[il0] ;
				/** 	mark <div> for diagnostics			*/
				if ( debugFrames === true) {
					actGrid.style.borderWidth	=	"1px" ;
					actGrid.style.borderStyle	=	"dotted" ;
					actGrid.style.borderColor	=	"blue" ;
				}
				/** 	marke <div> for diagnostics			*/
				dTrace( 3, "wapScreen.js", "wapScreen", "*()", "working on grid ...'" + actGrid.id + "'") ;
				var	parentDS	=	null ;						// we are independent at first
				if ( actGrid.dataset.wapObject != this.screenDiv.dataset.wapCoreObject) {
					parentDS	=	this.dataSource ;
				}
				this[ actGrid.id]	=	new wapGrid( this, actGrid.id, {
											object:			actGrid.dataset.wapObject
										,	screen:			this.screenDiv.dataset.wapScreen
										,	parentDS:		parentDS
										,	editorName:		actGrid.dataset.wapEditor
										,	moduleName: 	this.screenDiv.dataset.wapModule
										,	subModuleName:	this.screenDiv.dataset.wapScreen
										,	onSelectById:	function( _parent, _id) {
																/**
																 * scope info:
																 *	this	refers to the wapGrid itself
																 *	_parent refers to the owner of the wapGrid, ie. the wapScree
																 */
																dBegin( 102, "mainBrake{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
																_parent.dataSource.load( "", _id, "") ;
																var	myGrid	=	document.getElementById( this.name) ;
																showTab( myGrid.dataset.wapTabOnselect) ;
																dEnd( 102, "mainBrake{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															}
										,	onSelectByKey:	function( _parent, _key) {
																/**
																 * scope info:
																 *	this	refers to the wapGrid itself
																 *	_parent refers to the owner of the wapGrid, ie. the wapScree
																 */
																dBegin( 102, "mainBrake{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
																_parent.dataSource.load( _key, -1, "") ;
																var	myGrid	=	document.getElementById( this.name) ;
																showTab( myGrid.dataset.wapTabOnselect) ;
																dEnd( 102, "mainBrake{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															}
									}) ;
				if ( actGrid.dataset.wapObject != this.screenDiv.dataset.wapCoreObject) {
					this[ actGrid.id].parentDS	=	this.dataSource ;
				}
				if ( actGrid.dataset.wapLoadOnInit == "true") {
					this[ actGrid.id]._onFirstPage() ;
				}
			}
			/**
			 *
			 */
			dTrace( 2, "wapScreen.js", "wapScreen", "*()", "initializing from <div id='"+this.ownDiv.id+"'>; looking up wapTrees") ;
			this.myTrees	=	this.ownDiv.getElementsByClassName( "wapTree") ;
			for ( var il0=0 ; il0<this.myTrees.length ; il0++) {
				var	actTree	=	this.myTrees[il0] ;
				/** 	mark <div> for diagnostics			*/
				if ( debugFrames === true) {
					actTree.style.borderWidth	=	"1px" ;
					actTree.style.borderStyle	=	"ridge" ;
					actTree.style.borderColor	=	"green" ;
				}
				/** 	marke <div> for diagnostics			*/
				dTrace( 3, "wapScreen.js", "wapScreen", "*()", "working on tree ...'" + actTree.id + "'") ;
				var	parentDS	=	null ;						// we are independent at first
				if ( actTree.dataset.wapObject != this.screenDiv.dataset.wapCoreObject) {
					parentDS	=	this.dataSource ;
				}
				this[ actTree.id]	=	new wapTree( this, actTree.id, {
											object:			this.screenDiv.dataset.wapCoreObject
										,	screen:			this.screenDiv.dataset.wapScreen
										,	parentDS:		parentDS
										,	editorName:		actTree.dataset.wapEditor
										,	moduleName: 	this.screenDiv.dataset.wapModule
										,	subModuleName:	this.screenDiv.dataset.wapScreen
										,	onSelectById:	function( _parent, _id) {
																/**
																 * scope info:
																 *	this	refers to the wapGrid itself
																 *	_parent refers to the owner of the wapGrid, ie. the wapScree
																 */
																dBegin( 102, "mainBrake{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
																_parent.dataSource.load( "", _id, "") ;
																var	myTree	=	document.getElementById( this.name) ;
																showTab( myTree.dataset.wapTabOnselect) ;
																dEnd( 102, "mainBrake{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															}
										,	onSelectByKey:	function( _parent, _key) {
																/**
																 * scope info:=
																 *	this	refers to the wapGrid itself
																 *	_parent refers to the owner of the wapGrid, ie. the wapScree
																 */
																dBegin( 102, "mainBrake{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
																_parent.dataSource.load( _key, -1, "") ;
																var	myTree	=	document.getElementById( this.name) ;
																showTab( myTree.dataset.wapTabOnselect) ;
																dEnd( 102, "mainBrake{wapGrid.js}", "wapGrid", "onSelect( <_parent>, <_data>)") ;
															}
									}) ;
				if ( actTree.dataset.wapLoadOnInit == "true") {
					this[ actTree.id]._onFirstPage() ;
				}
			}
		}
	}
	/**
	 *
	 */
	dTrace( 2, "wapScreen.js", "wapScreen", "*", "defining this.setMenuItems") ;
	this.setMenuItems	=	function() {
		dBegin( 102, "wapScreen.js", "wapScreen", "setMenuItems()") ;
		dEnd( 102, "wapScreen.js", "wapScreen", "setMenuItems()") ;
	} ;
	/**
	 *
	 */
	dTrace( 2, "wapScreen.js", "wapScreen", "*", "defining this.onDataSourceLoaded") ;
	this.onDataSourceLoaded	=	function( _scr, _xmlData) {
		dBegin( 0, "wapScreen.js", "wapScreen", "onDataSourceLoaded( <_xmlData>)") ;
		if ( _xmlData) {
			this.displayAllData( _xmlData, true) ;
			this.dataSource.key	=	this.keyField.value ;
			for ( var il0=0 ; il0<this.myGrids.length ; il0++) {
				var	actGrid	=	this.myGrids[il0] ;
				/**
				 * only load the grid data if the data is dependent on the main data source
				 */
				if ( actGrid.dataset.wapObject != this.screenDiv.dataset.wapCoreObject) {
					this[ actGrid.id]._onFirstPage() ;
				}
			}
			var	myRefs	=	_xmlData.getElementsByTagName( "Reference") ;
			if ( myRefs.length > 0) {
				for ( var i=0 ; i<myRefs.length ; i++) {
					var	refUrl	=	"/api/dispatchXML.php?sessionId=" + sessionId
										+	"&_obj=" + this.screenDiv.dataset.wapCoreObject
										+	"&_fnc=" + "getRef"
										+	"&_key=" + this.keyField.value
										+	"&_id="
										+	"&_val=" + myRefs[i].childNodes[0].nodeValue ;
					window.open( refUrl) ;
				}
			}
		}
		dEnd( 0, "wapScreen.js", "wapScreen", "onDataSourceLoaded( <_response>)") ;
	} ;
	/**
	 *
	 */
	this.focus	=	function() {
		dBegin( 102, "wapScreen.js", "wapScreen", "focus()") ;
		if ( this.keyField !== null) {
			dTrace( 103, "wapScreen.js", "wapScreen", "focus()", "keyField '"+this.keyField.name+"' defined; will focus on this!") ;
			this.keyField.focus() ;
		} else {
			dTrace( 103, "wapScreen.js", "wapScreen", "focus()", "no keyField defined (non-critical)") ;
		}
		dEnd( 102, "wapScreen.js", "wapScreen", "focus()") ;
	} ;
	/**
	 *
	 */
	this.displayAllData	=	function( _dataXML, _clear) {
		dBegin( 1, "wapScreen.js", "wapScreen", "displayAllData()") ;
		dTrace( 1, "wapScreen.js", "wapScreen", "displayAllData( <_reply>)", new XMLSerializer().serializeToString( _dataXML)) ;
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
	this.displayData	=	function( _form, _dataXML, _clear, _verifyOnly) {
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
		var	formattedVal	=	"" ;
		var	valueNode ;
		var	dspValue ;				// displayed value in case of number and date in the localized version!
		var	myCoreObject	=	_dataXML.getElementsByTagName( this.coreObject)[0] ;
		var	formattedValue ;

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
		 * determine if we need to do something at all. in general the answer is yes, but
		 * if we are in verification mode only and the form does not need confirmation
		 * before discarding we can leave right away
		 */
		processForm	=	true ;
		if ( _verifyOnly) {
			var	confirmDiscard	=	myForm.dataset.wapConfirmDiscard ;
			if ( confirmDiscard) {
				if ( confirmDiscard == "false")
					processForm	=	false ;
			}
		}
		/**
		 *
		 */
		if ( myForm && myCoreObject && processForm) {
			if ( _clear && ! _verifyOnly) 				// force-avoid clearing in verify-mode
				clearForm( _form) ;
//			dTrace( 1, "wapScreen.js", "wapScreen", "displayData( <_reply>)", new XMLSerializer().serializeToString( myCoreObject)) ;
			myFields	=	myForm.getElementsByClassName( "wapField") ;		// get all the fields in the form
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
						dTrace( 3, "wapScreen.js", "wapScreen", "displayData( ...)", "wapType is defined as := '"+wapType+"'") ;
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
						case	"text"	:				//
						case	"longtext"	:			//
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
									dTrace( 1, "wapScreen.js", "wapScreen", "displayData( <_source>)", "checking flag["+n.toString()+"]") ;
									if ( parseInt( myFields[n].value) == parseInt( sourceVal)) {
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
//							switch ( actField.type) {
//							case	"text"	:
//								dTrace( 1, "wapScreen.js", "wapScreen", "displayData(...)",
//										"actField.id := '" + actField.id + "', "
//								+	"actField.type := '" + actField.type + "', "
//										+	"sourceVal := '" + sourceVal +	"'"
//								) ;
//								actField.value	=	sourceVal ;
//								break ;
//							}
							break ;
						}
					}
				}
				dTrace( 3, "wapScreen.js", "wapScreen", "displayData( ...)", "changeCount := " + changeCount.toString()) ;
			}
		} else {
			dTrace( 2, "wapScreen.js", "wapScreen", "displayData( ...)", "form['"+_form+"'] not found!") ;
		}
		dEnd( 1, "wapScreen.js", "wapScreen", "displayData( <_attrs>, '"+_form+"', <_clear>)") ;
		return changeCount ;
	} ;
	/**
	 *
	 */
	this.showStatus	=	function( _reply) {
		dBegin( 102, "wapScreen.js", "wapScreen", "showStatus( <_reply>)") ;
		var	statusMsg ;
		/**
		 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
		 */
		statusMsg	=	_reply.getElementsByTagName( "Status")[0] ;
		if ( statusMsg) {
			statusCode	=	_reply.getElementsByTagName( "StatusCode")[0].childNodes[0].nodeValue ;
			statusText	=	_reply.getElementsByTagName( "StatusText")[0].childNodes[0].nodeValue ;
			statusInfo	=	_reply.getElementsByTagName( "StatusInfo")[0].childNodes[0].nodeValue ;
			if ( parseInt( statusCode) != 0) {
				displayText	=	"StatusCode: " + statusCode + "<br/>\n" ;
				displayText	+=	"StatusText: " + statusText + "<br/>\n" ;
				displayText	+=	"StatusInfo: " + statusInfo ;
				statusDialog	=	new dijit.Dialog( {
										title:	"Status Meldung",
										duration:	100,
										content:	displayText
									} ) ;
				statusDialog.show() ;
			}
		} else {
		}
		dEnd( 102, "wapScreen.js", "wapScreen", "showStatus( <_reply>)") ;
	} ;
	/**
	 *
	 */
	this.onShowTabEntry	=	function( _tabName) {
		dBegin( 1, "wapScreen.js", "wapScreen", "onShowTabEntry( '"+_tabName+"')") ;
		dEnd( 1, "wapScreen.js", "wapScreen", "onShowTabEntry()") ;
	} ;
	/**
	*
	*/
	this.showInfo	=	function( _reply) {
		dBegin( 102, "wapScreen.js", "wapScreen", "showInfo( <_reply>)") ;
		statusMsg	=	_reply.getElementsByTagName( "StatusInfo")[0].childNodes[0].nodeValue ;
		if ( statusMsg != "ok") {
			_debugL( 1, "common.js::showInfo( <_resp>): statusMsg is defined \n") ;
			statusCode	=	_reply.getElementsByTagName( "StatusCode")[0].childNodes[0].nodeValue ;
			statusInfo	=	_reply.getElementsByTagName( "StatusInfo")[0].childNodes[0].nodeValue ;
			displayText	=	"<b>StatusCode: " + statusCode + "</b><br/>" ;
			displayText	+=	"StatusInfo: " + statusInfo ;
			statusDialog	=	new dijit.Dialog( {
									title:	"Info Meldung",
									duration:	100,
									content:	displayText
								} ) ;
			statusDialog.show() ;
		} else {
		}
		dEnd( 102, "wapScreen.js", "wapScreen", "showInfo( <_reply>)") ;
	} ;
	/**
	 *
	 */
	this.beforeDiscard	=	function() {
		var	cnt	=	0 ;
		if ( this.dataSource.xmlData)
			cnt	=	this.verifyAllData( this.dataSource.xmlData) ;
		return cnt ;
	} ;
	/**
	 *
	 */
	this.verifyAllData	=	function( _dataXML) {
		dBegin( 1, "wapScreen.js", "wapScreen", "verifyAllData()") ;
//		dTrace( 1, "wapScreen.js", "wapScreen", "verifyAllData( <_reply>)", new XMLSerializer().serializeToString( _dataXML)) ;
		var	changeCount	=	0 ;
		for ( var i=0 ; i<this.myForms.length ; i++) {
			dTrace( 1, "wapScreen.js", "wapScreen", "verifyAllData()", "working on form '" + this.myForms[i].id + "'") ;
			changeCount	+=	this.displayData( this.myForms[i].id, _dataXML, false, true) ;
		}
		dEnd( 1, "wapScreen.js", "wapScreen", "verifyAllData(), result " + changeCount.toString()) ;
		return changeCount ;
	} ;
	/**
	 *
	 */
	this.onEnter	=	function( _key) {
		dBegin( 1, "wapScreen.js", "wapScreen", "onEnter( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "wapScreen.js", "wapScreen", "onEnter( '"+_key+"')") ;
	} ;
	/**
	 *
	 */
	this.onPrev	=	function() {
		dBegin( 1, "wapScreen.js", "wapScreen", "onPrev()") ;
		this.dataSource.getPrev( this.keyField.value, -1, "") ;
		dEnd( 1, "wapScreen.js", "wapScreen", "onPrev()") ;
	} ;
	/**
	 *
	 */
	this.onNext	=	function() {
		dBegin( 1, "wapScreen.js", "wapScreen", "onNext()[:"+this.screenName+"]") ;
		if ( this.dataSource) {
			this.dataSource.getNext( this.keyField.value, -1, "") ;
		}
		dEnd( 1, "wapScreen.js", "wapScreen", "onNext()") ;
	} ;
	/**
	 *
	 */
	this.onLast	=	function() {
		dBegin( 1, "wapScreen.js", "wapScreen", "onLast()[:"+this.screenName+"]") ;
		if ( this.dataSource) {
			this.dataSource.getLast( this.keyField.value, -1, "") ;
		}
		dEnd( 1, "wapScreen.js", "wapScreen", "onLast()") ;
	} ;
	/**
	 *
	 */
	this.onSelectByKey	=	function( _key) {
		dBegin( 1, "wapScreen.js", "wapScreen", "onSelectByKey( '"+_key+"')") ;
		this.dataSource.load( _key, -1, "") ;
		dEnd( 1, "wapScreen.js", "wapScreen", "onSelectByKey( '"+_key+"')") ;
	} ;
	this.enterKey	=	function( _event) {
		var	res	=	true ;
		var target	=	_event.target || _event.srcElement ;

		dBegin( 1, "wapScreen.js", "wapScreen", "enterKey( '"+_event.keyCode+"')"+target.name) ;
		if ( getFormField( this.keyForm, target.name) === this.keyField) {
		if ( _event.keyCode == 13) {
			if ( this.dataSource) {
				this.dataSource.load( this.keyField.value, -1, "") ;
				_event.stopPropagation ? _event.stopPropagation() : (_event.cancelBubble=true) ;
				res	=	false ;
			}
		}
		}
		dEnd( 1, "wapScreen.js", "wapScreen", "enterKey( '"+_event.keyCode+"')") ;
		return res ;
	}
	/**
	 *
	 */
	screenCurrent	=	this ;			// make this screen the current screen
	if ( pendingKey != "") {
		this.onSelectByKey( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		this.onLast() ;
	}
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	dEnd( 1, "wapScreen.js", "wapScreen", "wapScreen( '"+_screenName+"')") ;
} ;
/**
 *
 */
wapScreen.prototype.onUpdate	=	function( _wd, _fnc, _form) {
	dBegin( 1, "wapScreen.js", "wapScreen.prototype", "onUpdate( <...>)") ;
	this.dataSource.onUpdate( this.keyField.value, -1, "", _form) ;
	dEnd( 1, "wapScreen.js", "wapScreen.prototype", "onUpdate( <...>)") ;
} ;
/**
 *
 */
wapScreen.prototype.onSelectById	=	function( _id) {
	dBegin( 1, "wapScreen.js", "wapScreen.prototype", "onSelectById( '"+_id+"')") ;
	this.dataSource.load( "", _id, "") ;
	dEnd( 1, "wapScreen.js", "wapScreen.prototype", "onSelectById( '"+_id+"')") ;
} ;
/**
 *
 */
wapScreen.prototype.onCreate	=	function( _wd, _fnc, _form) {
	dBegin( 1, "wapScreen.js", "wapScreen.prototype", "onCreate( <...>)") ;
	this.dataSource.onCreate(this.keyField.value, -1, "", _form) ;
	dEnd( 1, "wapScreen.js", "wapScreen.prototype", "onCreate( <...>)") ;
} ;
/**
 *
 */
wapScreen.prototype.onDelete	=	function( _wd, _fnc, _form) {
	dBegin( 1, "wapScreen.js", "wapScreen.prototype", "onDelete( <...>)") ;
	this.dataSource.onDelete(this.keyField.value, -1, "", _form) ;
	dEnd( 1, "wapScreen.js", "wapScreen.prototype", "onDelete( <...>)") ;
} ;
/**
 *
 */
wapScreen.prototype.onMisc	=	function( _wd, _fnc, _form) {
	dBegin( 1, "wapScreen.js", "wapScreen.prototype", "onMisc( <...>)") ;
	this.dataSource.onMisc( this.keyField.value, -1, "", _form, _fnc) ;
	dEnd( 1, "wapScreen.js", "wapScreen.prototype", "onMisc( <...>)") ;
} ;
wapScreen.prototype.onUpload	=	function( _wd, _fnc, _form, _addPOST) {
	dBegin( 1, "wapScreen.js", "wapScreen.prototype", "onUpload( <...>)") ;
	this.dataSource.onUpload( _wd, _fnc, _form, _addPOST) ;
	dEnd( 1, "wapScreen.js", "wapScreen.prototype", "onUpload( <...>)") ;
}
wapScreen.prototype.logoff	=	function( _wd, _fnc, _form, _addPOST) {
	dBegin( 1, "wapScreen.js", "wapScreen.prototype", "logoff( <...>)") ;
	document.ClientApplicationKeyData.action	=	"index.php" ;		// this.dataSource.objects.ClientApplication[0].PathApplication ;
	dEnd( 1, "wapScreen.js", "wapScreen.prototype", "logoff( <...>)") ;
}
/**
 *	screenGet( _screenName)
 *
 *	returns a reference to a waqpScreen object which has the given _screenName.
 *	if no wapScreen with the given name exists, null is returned.
 *	@param	string	_screenName
 *	@return	wapScreen
 */
function	screenGet( _screenName) {
	dBegin( 1, "wapScreen.php", "*", "screenGet( '"+_screenName+"')") ;
	var	res     =       null ;
	if ( wapScreens[_screenName]) {
		res     =       wapScreens[_screenName] ;
		dTrace( 1, "wapScreen.php", "*", "screenGet( '"+_screenName+"')", "found it ... as '" + res.screenName + "'") ;
	}
	dEnd( 1, "wapScreen.js", "*", "screenGet( '"+_screenName+"')") ;
	return res ;
}
function	showScreen( _screen, _forceReload, _key, _fnc) {
	dBegin( 1, "index.php", "*", "showScreen( '"+_screen+"', <_forceReload>, '"+_key+"', '"+_fnc+"')") ;
	if ( ! _forceReload)
		_forceReload	=	false ;
	/**
	 *	hide the currently active screen
	 */
	if ( screenCurrent != null) {
		dTrace( 1, "wapScreen.php", "*", "screenGet( '"+_screen+"')", "hiding current screen '" + screenCurrent.screenName + "'") ;
		var	myDiv	=	document.getElementById( screenCurrent.screenName) ;
		myDiv.style.display	=	"none" ;
	}
	if ( document.getElementById( _screen)) {
		dTrace( 1, "wapScreen.php", "*", "screenGet( '"+_screen+"')", "found div with name '" + _screen + "'") ;
		/**
		 *	show the new screen
		 */
		var	myDiv	=	document.getElementById( _screen) ;
		myDiv.style.display	=	"block" ;
		var	wapIsLoaded	=	myDiv.dataset.wapIsLoaded ;
		if ( wapIsLoaded && ! _forceReload) {
			dTrace( 1, "wapScreen.php", "*", "screenGet( '"+_screen+"')", "wapIsLoaded!") ;
			screenCurrent	=	screenGet( _screen) ;
			if ( _key)
				screenCurrent.onEnter( _key) ;
		} else {
			dTrace( 1, "index.php", "*", "showScreen( ...)", "div is not yet loaded ...", "") ;
			myDiv.dataset.wapIsLoaded	=	"true" ;
			if ( wapIsLoaded)
				myDiv.innerHTML	=	"reloading ..." ;
			else
				myDiv.innerHTML	=	"loading ..." ;
			var	myScreenName	=	"" ;
			myScreenName	+=	myDiv.dataset.wapModuleDir ? ( myDiv.dataset.wapModuleDir) + "/" : "" ;
			myScreenName	+=	myDiv.dataset.wapScreenDir ? ( myDiv.dataset.wapScreenDir) + "/" : "" ;
			myScreenName	+=	myDiv.dataset.wapScreen ;
			if ( ! myScreenName) {
				myScreenName	=	"testScreen.xml" ;
			}
			/**
			 *
			 */
			if ( _key)
				pendingKey	=	_key ;
			if ( _fnc)
				pendingFnc	=	_fnc ;
			var	myRequest	=	new XMLHttpRequest() ;
			myRequest.open( "GET", "/api/loadScreen.php?sessionId="+sessionId+"&screen="+myScreenName) ;
			myRequest.targetDiv	=		myDiv ;
			myRequest.addEventListener( 'load', function() {
				dTrace( 1, "index.php", "*", "showScreen( ...)", "loaded data ...", "") ;
				this.targetDiv.innerHTML	=	this.responseText ;
				/**
				 *
				 */
				var fileref	=	document.createElement('script') ;
				fileref.setAttribute( "type", "text/javascript") ;
				fileref.screenName	=	myScreenName ;
				fileref.setAttribute( "src", "/api/loadScript.php?sessionId="+sessionId+"&script="+myScreenName.replace( "xml", "js").replace( "html", "js")) ;
				if (typeof fileref != "undefined")
					document.getElementsByTagName("head")[0].appendChild(fileref) ;
			}) ;
			dTrace( 1, "index.php", "*", "showScreen( ...)", "submitting request for javascript ...", "") ;
			myRequest.send() ;
		}
	} else {
		screenCurrent	=	null ;
	}
	dEnd( 1, "index.php", "*", "showScreen( '"+_screen+"', <_forceReload>)") ;
}
/**
 *	showTab( _tabPage)
 *	==================
 *
 * _tabPage			id of the div to activate
 */
function	showTab( _tabPage) {
	dBegin( 1, "index.php", "*", "showTab( '"+_tabPage+"')") ;
	var	myTabPage	=	document.getElementById( _tabPage) ;		// will find an <li>
	var	myTabCntrl	=	document.getElementById( myTabPage.parentNode.dataset.wapTabControllerId) ;
	if ( myTabCntrl && myTabPage) {
		dTrace( 1, "index.php", "*", "showTab( ...)", "found both") ;
		/**
		 *	de-activate the currently active tab
		 */
		dTrace( 1, "index.php", "*", "showTab( ...)", "trying to de-activate the current tabPage") ;
		var	myActiveTabPageId	=	myTabCntrl.dataset.wapActiveTabPageId ;
		if ( myActiveTabPageId) {
			dTrace( 1, "index.php", "*", "showTab( ...)", "wapActiveTabPageId is defined") ;
			var	myActiveTabPage	=	document.getElementById( myActiveTabPageId) ;
			var	myActiveTabPageContentId	=	myActiveTabPage.dataset.wapContentId ;
			var	myActiveTabPageContent	=	document.getElementById( myActiveTabPageContentId) ;
			myActiveTabPage.className			=	myActiveTabPage.className.replace( /(?:^|\s)active(?!\S)/g , '' );
			myActiveTabPageContent.className	=	myActiveTabPageContent.className.replace( /(?:^|\s)active(?!\S)/g , '' );
		}
		/**
		 *
		 */
		dTrace( 1, "index.php", "*", "showTab( ...)", "trying to activate the new tabPage") ;
		myTabPage.className	+=	" active" ;		// activate the LI entry
		myTabCntrl.dataset.wapActiveTabPageId	=	_tabPage ;
		myActiveTabPageContentId	=	myTabPage.dataset.wapContentId ;
		myActiveTabPageContent	=	document.getElementById( myActiveTabPageContentId) ;
		myActiveTabPageContent.className	+=	" active" ;
	} else if ( myTabCntrl) {
		dTrace( 1, "index.php", "*", "showTab( ...)", "myTabCntrl is defined, myTabPage is NOT defined!") ;
	} else {
		dTrace( 1, "index.php", "*", "showTab( ...)", "myTabCntrl is NOT defined, myTabPage is defined!") ;
	}
	dEnd( 1, "index.php", "*", "showTab( ...)", "starting") ;
}
/**
 *	fold( _item)
 *	============
 *
 *	find an HTML node with the id = _item and toggle its style.display attribute between block and none
 */
function	fold( _item) {
	var	myDiv ;
	myDiv	=	document.getElementById( _item) ;
	if ( myDiv) {
		if ( myDiv.style.display == "block")
			myDiv.style.display	=	"none" ;
		else
			myDiv.style.display	=	"block" ;
	}
}
/**
 *
 */
function	getForm( _form, _scope) {
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
			if ( myForms[i].id == _form) {
				targetForm	=	myForms[i] ;
			}
		}
	}
	if ( targetForm == null && _scope != document) {
		myForms	=	document.getElementsByClassName( "wapForm") ;
		for ( var i=0 ; i < myForms.length ; i++) {
			if ( myForms[i].id == _form) {
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
function	getFormField( _form, _field, _scope) {
	var	myForm = null ;
	var	myFields ;
	var	targetField	=	null ;
	dBegin( 501, "common.js", "*", "getFormField( '"+_form+"', '"+_field+"')") ;
	/**
	 * find the form we shall fill out
	 */
	myForm	=	getForm( _form, _scope) ;
	if ( myForm) {
		myFields	=	myForm.getElementsByClassName( "wapField") ;
		/**
		 * loop through all XML-attributes
		 */
		for ( var i=0 ; i < myFields.length && targetField == null ; i++) {
			if ( myFields[i].name == _field) {
				targetField	=	myFields[i] ;
			}
		}
		if ( targetField) {
		} else {
			dTrace( 504, "common.js", "*", "getFormField()", "form["+_form+"], field["+_field+"] not found!") ;
		}
	} else {
		dTrace( 503, "common.js", "*", "getFormField()", "form["+_form+"] not found!") ;
	}
	dEnd( 501, "common.js", "*", "getFormField( '"+_form+"', '"+_field+"')") ;
	return targetField ;
}

/**
 *	_dumpScreens()
 *	==============
 *
 *	display a list of all screenNames in screenTable[].
 */
function	_dumpScreenTable() {
	dTrace( 1, "wapScreen.js", "*", "_dumpScreenTable()", "--screenTable------------------") ;
	for ( var myScreen in wapScreens) {
		dTrace( 1, "wapScreen.js", "*", "_dumpScreenTable()", "Screen[" + myScreen + "] " + wapScreens[myScreen].screenName + " --> " + wapScreens[myScreen].module) ;
	}
	dTrace( 1, "wapScreen.js", "*", "_dumpScreenTable()", "--wapGrids--------------------") ;
	for ( var myGrid in wapGrids) {
		dTrace( 1, "wapScreen.js", "*", "_dumpScreenTable()", "grid[" + myGrid + "]" + wapGrids[myGrid].name) ;
	}
	dTrace( 1, "wapScreen.js", "*", "_dumpScreenTable()", "====================") ;
} ;
function	_dumpForms() {
	dTrace( 1, "wapScreen.js", "*", "_dumpForms()", "DIV-Forms===========^") ;
	myForms	=	screenCurrent.ownDiv.getElementsByClassName( "wapForm") ;
	for ( var i=0 ; i< myForms.length ; i++) {
		dTrace( 1, "wapScreen.js", "*", "_dumpForms()", "Id...: " + myForms[i].id) ;
	}
	dTrace( 1, "wapScreen.js", "*", "_dumpForms()", "DIV-Forms===========v") ;
} ;
function	myDebug() {
		_dumpScreenTable() ;
		_dumpForms() ;
} ;
function	zFill( n, l) {
	return ( l > n.toString().length) ? ( ( Array(l).join( '0') + n).slice( -l)) : n ;
} ;
