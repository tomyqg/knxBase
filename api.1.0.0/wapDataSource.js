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
 * wapDataSourceXML.js
 * ===================
 *
 * Product id.:
 * Version:
 *
 * Description
 * ===========
 *
 * wapDataSource implements a source of data coming from an http server.
 * this class encpaulates the complete process of sending a request to the server and
 * retrieving the data into a JS object.
 * wapDataSource can be a primary dataSource or a secondary dataSource. As a secondary dataSource
 * the object needs ro reference a primary object. The keys to retrieve the data in a secondary dataSource
 * is defined by the primary dataSource.
 *
 * Mandatory attributes for the construction of a primary dataSoure are:
 * 	_parent		reference to the owning object of this dataSource
 * 	_attr		array with attributes as follows:
 * 		object				name of the class/object which the dataSource handles
 * 		onDataSourceLoaded	method to be called upon dataSource loaded
 *
 * Mandatory attributes for the construction of a secondary dataSource are:
 * 	_parent		reference to the owning object of this dataSource
 * 	_attr		array with attributes as follows:
 * 		parentDS			reference to the dataSource of the primary object
 *
 *
 * getData( _key, _id, _val)
 * tell the dataSource to fetch the
 *
 * getPrev( _key, _id, _val)
 *
 * getNext( _key, _id, _val)
 *
 * refresh()
 * In case the dataSource has a method "onDataLoaded" this is called after the data has been loaded
 * with a reference to the owner of "this" dataSource (parent) and the object "this.dataRoot" from the JSON
 * response which was received
 */
var confirmDiscardDialog	=	null ;
/**
 * class definition for wapDatasource
 *
 * wapDataSource provides basic retrieval and submission service for object data from and to a server.
 * The default communication end-point is a server application named "/api/dispatchXML.php". This server
 * application resembles the other end of the communication link.
 * Both together, wapDataSource.js and /api/dispatchXML.php define a fully function communication link.
 * On successful loading a parent-provided callback onDataSourceLoaded will be called if defined.
 * wapDataSource can deal with primary objects, i.e. objects which exist for itself, as well as dependent objects,
 * i.e. objects which belong to a primary object. This typically is the case in a one to many relationship, as for
 * example in a customer -> customer-contact relation. One customer (primary object) has one or mor dependent objects
 * (Customer Contacts).
 *
 * Basic functions are:
 *
 * 	dataSource.load( string key, int id, mixed val)
 * 		load an object by key or id from the server into this dataSource. On successful retrieval a callback funtion
 *	dataSource.getNext( string key, int id, mixed val, bool force)
 * 		load the next object (in server order) from the server into this dataSource
 *	dataSource.getNext( string key, int id, mixed val, bool force)
 * 		load the previous object (in server order) from the server into this dataSource
 *	dataSource.refresh()
 * 		refresh the current object from the server into this dataSource
 * 	dataSource.( string form)
 * 	dataSource.previousPage( string form)
 * 	dataSource.thisPage( string form)
 * 	dataSource.nextPage( string form)
 * 	dataSource.lastPage( string form)
 *		load a page (list) of (dependent) objects. The primary object held in the parent-dataSource provides
 *		the object for which the dependent objects will be retrieved. <form> allows to speficy basic data
 *		e.g. number of entries to retrieve.
 *	dataSource.onCreate( string key, int id, mixed val, string form)
 *		called to create a primary object with data given in the <form> named form
 *	dataSource.onUpdate( string key, int id, mixed val, string form)
 *		called to update a primary object with data given in the <form> named form
 *	dataSource.onMisc( string key, int id, mixed val, string form, string fnc)
 *		called to execute an arbitrary function on an object
 *
 *
 * @param Object $_parent	parent of this wapDataSource
 * @param string $_name		name of this wapDataSource
 * @return void
 */
function	wapDataSource( _parent, _attr) {
	dBegin( 101, "wapDataSourceXML.js", "wapDataSourceXML", "wapDataSource( <_parent>, <OBJ>)") ;
	this.parent	=	_parent ;
	this.parentDS	=	null ;
	/**
	 * set needed defaults
	 */
	this.urlGet	=	"/api/dispatchXML.php" ;
	/**
	 * _fnc values while dispatching for an "independent" object
	 */
	this.fncGet	=	"getAsXML" ;
	this.fncNext	=	"getNextAsXML" ;
	this.fncPrev	=	"getPrevAsXML" ;
	this.fncLast	=	"getLastAsXML" ;
	this.fncAdd	=	"add" ;
	this.fncUpd	=	"upd" ;
	this.fncDel	=	"del" ;
	this.onDataChanged	=	null ;
	this.onStart	=	null ;			// function to call before sending request
	this.onEnd		=	null ;		// function to call after all donwload finished
	this.waitDialog	=	null ;
	/**
	 *
	 */
	this.objs	=	null ;
	/**
	 * get attributes from parameter-object
	 * mand.:	primaryObject
	 * 			object
	 * opt.:	-
	 */
	this.parentDS	=	null ;
	for ( var i in _attr) {
		this[ i]	=	_attr[i] ;
	}
	/**
	 *	IF we are a dataSource for a dependent obejct
	 * 		update the names of the functions we need to call
	 */
	if ( this.parentDS !== null) {
		/**
		 * _fnc values while dispatching for a dependent object
		 */
		this.fncGet	=	"getList" ;
		this.fncAdd	=	"addDep" ;
		this.fncUpd	=	"updDep" ;
		this.fncDel	=	"delDep" ;
		this.fncNext	=	null ;
		this.fncPrev	=	null ;
	}
	this.key	=	"" ;
	this.id	=	-1 ;
	this.val	=	"" ;
	this.wd	=	null ;				// no active WorkingDialog
	this.showStatus	=	null ;
	this.showInfo	=	null ;
	/**
	 * refresh
	 */
	this.load	=	function( _key, _id, _val) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "load(...)") ;
		this.key	=	_key ;
		this.id	=	_id ;
		this.val	=	_val ;
		this.refresh( true) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "load(...)") ;
	} ;
	/**
	 * refresh
	 */
	this.getLast	=	function( _key, _id, _val, _force) {
		var	canDiscard	=	true ;
		var	fieldsChanged	=	0 ;
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "getLast(...)") ;
		this.key	=	_key ;
		this.id	=	_id ;
		this.val	=	_val ;
		this.dispatch( true, this.fncLast) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "getLast(...)") ;
	} ;
	/**
	 * refresh
	 */
	this.getNext	=	function( _key, _id, _val, _force) {
		var	canDiscard	=	true ;
		var	fieldsChanged	=	0 ;
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "getNext(...)") ;
		if ( ! _force)	_force	=	false ;
		if ( this.parent.beforeDiscard && ! _force) {
			canDiscard	=	false ;
			fieldsChanged	=	this.parent.beforeDiscard() ;
			dTrace( 1, "wapDataSourceXML.js", "wapDataSourceXML", "getNext( '<key>', <_id>, <_val>, <_force>", "fieldsChanged = " + fieldsChanged.toString()) ;
			if ( fieldsChanged != 0) {
				var	myPopup	=	new wapPopup( this, "error", {
							url:	"dialogDiscardData.html"
						,	modal:	true
						,	parent:	this
						,	title:	"Error Message"
						,	onGo:	function() {
								this.hide() ;
								this.parent.getNext( _key, _id, _val, true) ;
							}
						,	onCancel:	function() {
								this.hide() ;
							}
					}) ;
				myPopup.open() ;
			} else {
				canDiscard	=	true ;
			}
		}
		if ( canDiscard) {
			this.key	=	_key ;
			this.id	=	_id ;
			this.val	=	_val ;
			this.dispatch( true, this.fncNext) ;
		}
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "getNext(...)") ;
	} ;
	/**
	 * refresh
	 */
	this.getPrev	=	function( _key, _id, _val, _force) {
		var	canDiscard	=	true ;
		var	fieldsChanged	=	0 ;
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "getPrev(...)") ;
		if ( this.parent.beforeDiscard && ! _force) {
			canDiscard	=	false ;
			fieldsChanged	=	this.parent.beforeDiscard() ;
			dTrace( 1, "wapDataSourceXML.js", "wapDataSourceXML", "fieldsChanged = " + String( fieldsChanged)) ;
			if ( fieldsChanged != 0) {
				var	myPopup	=	new wapPopup( this, "error", {
													url:	"dialogDiscardData.html"
												,	modal:	true
												,	parent:	this
												,	title:	"Error Message"
												,	onGo:	function() {
														this.hide() ;
														this.parent.getPrev( _key, _id, _val, true) ;
													}
												,	onCancel:	function() {
														this.hide() ;
													}
												}) ;
				myPopup.open() ;
			} else {
				canDiscard	=	true ;
			}
		}
		if ( canDiscard) {
			this.key	=	_key ;
			this.id	=	_id ;
			this.val	=	_val ;
			this.dispatch( true, this.fncPrev) ;
		}
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "getPrev(...)") ;
	} ;
	/**
	 * refresh
	 */
	this.refresh	=	function( _addPOST) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "refresh(...)") ;
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGet, null, _addPOST) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "refresh(...)") ;
	} ;
	this.firstPage	=	function( _form, _addPOST) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "firstPage( '"+_form+"')") ;
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGet, _form, "step=firstPage&"+_addPOST) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "firstPage( '"+_form+"')") ;
	} ;
	this.previousPage	=	function( _form, _addPOST) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "previousPage( '"+_form+"')") ;
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGet, _form, "step=previousPage&"+_addPOST) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "previousPage( '"+_form+"')") ;
	} ;
	this.oneBackward	=	function( _form, _addPOST) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "oneBackward( '"+_form+"')") ;
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGet, _form, "step=oneBackward&"+_addPOST) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "oneBackward( '"+_form+"')") ;
	} ;
	this.thisPage	=	function( _form, _addPOST) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "thisPage( '"+_form+"')") ;
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGet, _form, "step=thisPage&"+_addPOST) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "thisPage( '"+_form+"')") ;
	} ;
	this.oneForward	=	function( _form, _addPOST) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "oneForward( '"+_form+"')") ;
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGet, _form, "step=oneForward&"+_addPOST) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "oneForward( '"+_form+"')") ;
	} ;
	this.nextPage	=	function( _form, _addPOST) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "nextPage( '"+_form+"')") ;
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGet, _form, "step=nextPage&"+_addPOST) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "nextPage( '"+_form+"')") ;
	} ;
	this.lastPage	=	function( _form, _addPOST) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "lastPage( '"+_form+"')") ;
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGet, _form, "step=lastPage&"+_addPOST) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "lastPage( '"+_form+"')") ;
	} ;
	/**
	 * dispatch( _wd, _fnc, _form, _addPOST)
	 * dispatch = Dispatch
	 * Dispatches a method call to an object, together with the key, an id and a miscellaneous value and with form data
	 * _wd		bool	working dialog no or yes
	 * _fnc		string	method to call on the object
	 * _key		string	object key to dispatch the call to
	 * _id		int		object id to dispatch the call to (will only be used if there's no object[_key]
	 * _val		mixed	parameter as the _val for the dispatch
	 * _form	string	name of the form where the data will be fetched from
	 */
	this.dispatch	=	function( _wd, _fnc, _form, _addPOST, _scope) {
		var	url ;
		var	postVars ;
		var	myForm ;
		/**
		 *
		 */
		if ( ! _addPOST)
			_addPOST	=	"" ;
		/**
		 * setup the indefinit progress bar
		 */
		new wapBusy( { url: "dialogWait.html"}) ;
		dBegin( 1, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch( <_wd>, '"+_fnc+", '"+_form+"')") ;
		if ( this.xmlData) {
			dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "deleting wapDataSource.xmlData") ;
			delete this.xmlData ;
		}
		if ( this.parentDS == null) {
			dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "parentDS == null") ;
			if ( Array.isArray( this.key)) {
				url	=	"/api/dispatchXML.php"
					+ "?sessionId=" + sessionId
					+ "&_obj=" + this.object
					+ "&_fnc=" + _fnc
					+ "&_key0=" + encodeURIComponent( this.key[0])
					+ "&_key1=" + encodeURIComponent( this.key[1])
					+ "&_key2=" + encodeURIComponent( this.key[2])
					+ "&_key3=" + encodeURIComponent( this.key[3])
					+ "&_id=" + this.id
					+ "&_val=" + this.val
					;
			} else {
				url	=	"/api/dispatchXML.php"
						+ "?sessionId=" + sessionId
						+ "&_obj=" + this.object
						+ "&_fnc=" + _fnc
						+ "&_key=" + encodeURIComponent( this.key)
						+ "&_id=" + this.id
						+ "&_val=" + this.val
						;
			}
		} else {
			dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "parentDS != null") ;
			url	=	"/api/dispatchXML.php"
				+ "?sessionId=" + sessionId
				+ "&_obj=" + this.parentDS.object
				+ "&_fnc=" + _fnc
				+ "&_key=" + encodeURIComponent( this.parentDS.key)
				+ "&_id=" + this.id
				+ "&_val=" + this.object
				;
		}
		dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "url = '"+url+"'") ;
		postVars	=	"" ;
		if ( _form) {
			if ( _form != null) {
				dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "_form:='"+_form+"'") ;
				if ( Object.prototype.toString.call( _form) === '[object Array]') {
					for ( var i = 0 ; i < _form.length ; i++) {
						myForm	=	_form[i] ;
						postVars	+=	"&" + getPOSTData( myForm, _scope) ;
					}
				} else if ( Object.prototype.toString.call( _form) === '[object Object]') {
					for ( var i in _form) {
						myForm	=	_form[i] ;
						postVars	+=	"&" + getPOSTData( myForm, _scope) ;
					}
				} else {
					postVars	=	getPOSTData( _form, _scope) ;
				}
			}
		}
		if ( postVars != "")
			postVars	+=	"&" + _addPOST ;
		else
			postVars	+=	_addPOST ;
		dEnd( 1, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch( ...): calling xhrPost(); POST = '" + postVars + "'") ;
		this.myRequest	=	new XMLHttpRequest() ;
		this.myRequest.open( "POST", url, true) ;
		this.myRequest.timeout	=	3000 ;		// timeout to 3 s before killing waiting dialog
		this.myRequest.setRequestHeader( "Content-type", "application/x-www-form-urlencoded") ;
		this.myRequest.parent	=	this ;
		this.myRequest.addEventListener( 'load', function() {
			// for this call:
			// this := XMLHttpRequest
			// this.parent := dataSource
			if ( this.responseText.length > 0) {
				this.parent.dispatchResponseXML( this.responseXML) ;
			} else {
				busyDialog.hide() ;
				window.alert( "Empty reply.\nThe script on the server side probably died pre-maturely.\nSee PHP error log and check for FExceptions!") ;
			}
		}) ;
		this.myRequest.addEventListener( 'error', function() {
			// for this call:
			// this := XMLHttpRequest
			// this.parent := dataSource
			this.parent.dispatchErrorXML( this.reponseXML) ;
		}) ;
		this.myRequest.addEventListener( 'timeout', function() {
			// for this call:
			// this := XMLHttpRequest
			// this.parent := dataSource
			busyDialog.hide() ;
		}) ;
		this.myRequest.send( postVars) ;
		dEnd( 1, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch( <_wd>, '"+_fnc+", '"+_form+"')") ;
		return false ;
	} ;
	/**
	 *
	 */
	this.onUpload	=	function( _wd, _fnc, _form, _addPOST) {
		var	url ;
		var	postVars ;
		if ( ! _addPOST)
			_addPOST	=	"" ;
		/**
		 * setup the indefinit progress bar
		 */
		new wapBusy( { url: "dialogWait.html"}) ;
		dBegin( 1, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch( <_wd>, '"+_fnc+", '"+_form+"')") ;
		if ( this.xmlData) {
			dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "deleting wapDataSource.xmlData") ;
			delete this.xmlData ;
		}
		if ( this.parentDS == null) {
			dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "parentDS == null") ;
			if ( Array.isArray( this.key)) {
				url	=	"/api/dispatchXML.php"
					+ "?sessionId=" + sessionId
					+ "&_obj=" + this.object
					+ "&_fnc=" + _fnc
					+ "&_key0=" + encodeURIComponent( this.key[0])
					+ "&_key1=" + encodeURIComponent( this.key[1])
					+ "&_key2=" + encodeURIComponent( this.key[2])
					+ "&_key3=" + encodeURIComponent( this.key[3])
					+ "&_id=" + this.id
					+ "&_val=" + this.val
					;
			} else {
				url	=	"/api/dispatchXML.php"
						+ "?sessionId=" + sessionId
						+ "&_obj=" + this.object
						+ "&_fnc=" + _fnc
						+ "&_key=" + encodeURIComponent( this.key)
						+ "&_id=" + this.id
						+ "&_val=" + this.val
						;
			}
		} else {
			dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "parentDS != null") ;
			url	=	"/api/dispatchXML.php"
				+ "?sessionId=" + sessionId
				+ "&_obj=" + this.parentDS.object
				+ "&_fnc=" + _fnc
				+ "&_key=" + encodeURIComponent( this.parentDS.key)
				+ "&_id=" + this.id
				+ "&_val=" + this.object
				;
		}
		dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "url = '"+url+"'") ;
		dEnd( 1, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch( ...): calling xhrPost(); POST = '" + postVars + "'") ;
		this.myRequest = new XMLHttpRequest();
		this.myRequest.open( 'POST', url);
		this.myRequest.parent	=	this ;						//request belongs to this
		this.myRequest.addEventListener( 'load', function() {
			this.parent.dispatchResponseXML( this.responseXML) ;
		}) ;
		this.myRequest.addEventListener( 'error', function() {
			this.parent.dispatchErrorXML( this.reponseXML) ;
		}) ;
		this.myRequest.send( new FormData( document.getElementById( _form)));
		return false ;
	} ;
	/**
	 * function to be called upon reception of valid reply
	 * here: this already refers to datasource
	 */
	this.dispatchResponseXML	=	function( _reply) {
		var	statusMsg ;
		var	statusInfo	=	""
		var	statusText	=	"" ;
		var	statusCode	=	0 ;
		dTrace( 102, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchResponseXML(...)", "load(ed)") ;
		busyDialog.hide() ;
		dTrace( 102, "wapDataSourceXML.js", "wapDataSourceXML", "onLoaded( <_reply>)", new XMLSerializer().serializeToString( _reply)) ;
		statusMsg	=	_reply.getElementsByTagName( "Status")[0] ;
		if ( statusMsg) {
			dTrace( 103, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchResponseXML(...)", "statusMsg defined") ;
			statusCode	=	parseInt( _reply.getElementsByTagName( "StatusCode")[0].childNodes[0].nodeValue) ;
			dTrace( 103, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchResponseXML(...)", "1") ;
			statusInfo	=	_reply.getElementsByTagName( "StatusInfo")[0].childNodes[0].nodeValue ;
			statusText	=	_reply.getElementsByTagName( "StatusText")[0].childNodes[0].nodeValue ;
			dTrace( 103, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchResponseXML(...)", "2") ;
			if ( statusCode >= 0) {
				if ( this.parent) {
					if ( this.parent[this.parent.object] != null) {
						dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "deleting wapDataSource.parent[this.parent.object]") ;
						delete this.parent[this.parent.object] ;
					}
					if ( this.parent.objects != null) {
						dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "deleting wapDataSource.parent.objects") ;
						delete this.parent.objects ;
					}
				}
				/**
				 * call the watDataSource.receivedData method
				 */
				this.handled	=	false ;
				if ( _reply.getElementsByTagName( "Data")[0] != null) {
					dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatch(...)", "received data, at least I believe so") ;
					this.receivedData( _reply.getElementsByTagName( "Data")[0]) ;	//
					this.handled	=	true ;
				}
				if ( _reply.getElementsByTagName( "References")[0] != null) {
					this.receivedReferences( _reply.getElementsByTagName( "References")[0]) ;	//
					this.handled	=	true ;
				}
				/**
				 *	IF nothing could be done
				 *		see if my parent has something to do
				 *	ENDIF
				 */
				if ( ! this.handled) {
					if ( this.parent.handleDS) {
						this.parent.handleDS() ;
					}
				}
				/**
				 *	IF there is a message in the reply
				 *		show it inside a modal (user must close before continuing) dialog
				 *	ENDIF
				 */
				var	myMessage	=	_reply.getElementsByTagName( "Message")[0] ;
				if ( myMessage) {
					var	myPopup	=	new wapPopup( this, "error", { displayText: myMessage.childNodes[0].nodeValue, modal: true}) ;
					myPopup.open() ;
				}
			} else {
				dTrace( 104, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchResponseXML(...)", "statusCode < 0!") ;
				var	targetURL	=	_reply.getElementsByTagName( "TargetURL")[0].childNodes[0].nodeValue ;
				var	displayText	=	"<h1>Fehlermeldung:</h1>" ;
				displayText	+=	"<br/><b>StatusCode: " + statusCode + "</b><br/>" ;
				displayText	+=	"statusInfo: " + statusInfo + "<br/>" ;
				displayText	+=	"statusText: " + statusText + "<br/>" ;
				var	instantiatedClass	=	_reply.getElementsByTagName( "InstantiatedClass")[0].childNodes[0].nodeValue ;
				var	replyingClass	=	_reply.getElementsByTagName( "ReplyingClass")[0].childNodes[0].nodeValue ;
				if ( instantiatedClass) {
					displayText	+=	"instantiatedClass: " + instantiatedClass + "<br/>" ;
				}
				if ( replyingClass) {
					displayText	+=	"replyingClass: " + replyingClass + "<br/>" ;
				}
				var	myPopup	=	new wapPopup( this, "error", { displayText: displayText, modal: true}) ;
				myPopup.redirectOnClose	=	targetURL ;
				myPopup.open() ;
				dTrace( 104, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchResponseXML(...)", "done with error handling") ;
			}
		} else {
			dTrace( 103, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchResponseXML( ...)", "no statusMsg defined") ;
		}
//		screenCurrent.focus() ;
	} ;
	/**
	 *
	 */
	this.receivedData	=	function( _xmlData) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "receivedData( <_reply>)") ;
		dTrace( 103, "wapDataSourceXML.js", "wapDataSourceXML", "receivedData( <_reply>)", new XMLSerializer().serializeToString( _xmlData)) ;
		/**
		 *	IF the parent object of this datasource provides a callback 'onDataSourceLoaded'
		 *		call it
		 */
		if ( typeof _xmlData === 'object') {
			this.xmlData	=	_xmlData ;
			if ( this.parent) {
				if ( this.parent.onDataSourceLoaded) {
					dTrace( 103, "wapDataSourceXML.js", "wapDataSourceXML", "receivedData", "calling parent onDataSourceLoaded") ;
					this.parent.onDataSourceLoaded( this.parent, this.xmlData) ;
				} else {
				}
			} else if ( this.onDataSourceLoaded) {
				this.onDataSourceLoaded( null, this.xmlData) ;
			}
		} else {
			dTrace( 104, "wapDataSourceXML.js", "wapDataSourceXML", "receivedData( <_reply>)", "_xmlData is not an object") ;
		}
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "receivedData( <_reply>)") ;
	} ;
	/**
	 *
	 */
	this.receivedReferences	=	function( _xmlData) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "receivedReferences( <_reply>)") ;
		dTrace( 103, "wapDataSourceXML.js", "wapDataSourceXML", "receivedReferences( <_reply>)", new XMLSerializer().serializeToString( _xmlData)) ;
		/**
		 *	IF the parent object of this datasource provides a callback 'onDataSourceLoaded'
		 *		call it
		 */
		if ( typeof _xmlData === 'object') {
			this.xmlData	=	_xmlData ;
			if ( this.parent.onDataSourceLoaded) {
				dTrace( 103, "wapDataSourceXML.js", "wapDataSourceXML", "receivedReferences", "calling parent onDataSourceLoaded") ;
				this.parent.receivedReferences( this.parent, this.xmlData) ;
			} else {
			}
		} else {
			dTrace( 104, "wapDataSourceXML.js", "wapDataSourceXML", "receivedReferences( <_reply>)", "_xmlData is not an object") ;
		}
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "receivedReferences( <_reply>)") ;
	} ;
	/**
	 * function to be called upon reception of invalid reply or problems during the processing
	 */
	this.dispatchErrorXML	=	function( _res, _reply) {
		dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchError( ...)", "error") ;
		dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchError( ...)", "'" + _res + "'") ;
		dTrace( 2, "wapDataSourceXML.js", "wapDataSourceXML", "dispatchError( ...)", "'" + _reply + "'") ;
		busyDialog.hide() ;
		fenster = window.open(url, "Popupfenster", "width=400,height=300,resizable=no,location=no");
		var	myPopup	=	new wapPopup( this, "error", { displayText: "Received errorneous answer from Server. Data can not be interpreted!", modal: true}) ;
		myPopup.open() ;
	} ;
	/**
	 *
	 */
	this.onCreate	=	function( _key, _id, _val, _form, _addPOST, _scope) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "onCreate( '"+_key+"', "+_id+", '"+_val+"', <_form>)") ;
		this.key	=	_key ;
		this.id	=	_id ;
		this.val	=	_val ;
		this.dispatch( true, this.fncAdd, _form, _addPOST, _scope) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "onCreate( ...)") ;
	} ;
	/**
	 *
	 */
	this.onUpdate	=	function( _key, _id, _val, _form, _addPOST, _scope) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "onUpdate( '"+_key+"', "+_id+", '"+_val+"', <_form>)") ;
		this.key	=	_key ;
		this.id	=	_id ;
		this.val	=	_val ;
		this.dispatch( true, this.fncUpd, _form, _addPOST, _scope) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "onUpdate( ...)") ;
	} ;
	/**
	 *
	 */
	this.onDelete	=	function( _key, _id, _val, _form, _addPOST, _scope) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "onDelete( '"+_key+"', "+_id+", '"+_val+"', <_form>)") ;
		this.key	=	_key ;
		this.id	=	_id ;
		this.val	=	_val ;
		this.dispatch( true, this.fncDel, _form, _addPOST, _scope) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "onDelete( ...)") ;
	} ;
	/**
	 *
	 */
	this.onMisc	=	function( _key, _id, _val, _form, _fnc, _addPOST) {
		dBegin( 102, "wapDataSourceXML.js", "wapDataSourceXML", "onMisc( '"+_key+"', "+_id+", '"+_val+"', <_form>)") ;
		this.key	=	_key ;
		this.id	=	_id ;
		this.val	=	_val ;
		this.dispatch( true, _fnc, _form, _addPOST) ;
		dEnd( 102, "wapDataSourceXML.js", "wapDataSourceXML", "onMisc( ...)") ;
	} ;
	dEnd( 101, "wapDataSourceXML.js", "wapDataSourceXML", "wapDataSource( <_parent>, <OBJ>") ;
}
