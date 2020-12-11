"use strict"
/**
 * Copyright (c) 2015-2018 wimtecc, Karl-Heinz Welter
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
 * 	_owner		reference to the owning object of this dataSource
 * 	_attr		array with attributes as follows:
 * 		object				name of the class/object which the dataSource handles
 * 		onDataSourceLoaded	method to be called upon dataSource loaded
 *
 * Mandatory attributes for the construction of a secondary dataSource are
 * 	_owner		reference to the owning object of this dataSource
 * 	_attr		array with attributes as follows:
 * 		parent			reference to the dataSource of the primary object
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
 * with a reference to the owner of "this" dataSource (owner) and the object "this.dataRoot" from the JSON
 * response which was received
 */
var confirmDiscardDialog	=	null ;
/**
 * class definition for wapDatasource
 *
 * wapDataSource provides basic retrieval and submission service for object data from and to a server.
 * The default communication end-point is a server application named "/api/dispatch.php". This server
 * application resembles the other end of the communication link.
 * Both together, wapDataSource.js and /api/dispatch.php define a fully function communication link.
 * On successful loading a owner-provided callback onDataSourceLoaded will be called if defined.
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
 *		load a page (list) of (dependent) objects. The primary object held in the owner-dataSource provides
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
 * @param Object $_owner	owner of this wapDataSource
 * @param string $_name		name of this wapDataSource
 * @return void
 */
function	wapDataSource( _owner, _attr) {
	this.owner	=	_owner ;			// owner (creator) of this object
	this.parent	=	null ;				// parent datasource of this datasource

	/**
	 * set needed defaults
	 */
	this.urlGet	=	"/api/dispatch.php" ;

	/**
	 * _fnc values while dispatching for an "independent" object
	 */
	this.onDataChanged	=	null ;
	this.onStart		=	null ;		// function to call before sending request
	this.onEnd			=	null ;		// function to call after all donwload finished
	this.waitDialog		=	null ;
	this.fncGet		=	"getAsXML" ;
	this.fncGetList	=	"getList" ;
	this.fncPrev	=	"getPrevAsXML" ;
	this.fncNext	=	"getNextAsXML" ;
	this.fncLast	=	"getLastAsXML" ;
    this.fncNew		=	"new" ;
	this.fncAdd		=	"add" ;
	this.fncUpd		=	"upd" ;
	this.fncDel		=	"del" ;
	this.parentObject	=	"" ;
	this.parentDS	=	null ;

	/**
	 * get attributes from parameter-object
	 * mand.:	primaryObject
	 * 			object
	 * opt.:	-
	 */
	for ( var i in _attr) {
		this[ i]	=	_attr[i] ;
	}
	console.log( "creating dataSource ... " + this.parentObject + "::" + this.object) ;
	if ( this.parentDS != null) {
		console.log( "parentDS is defined") ;
	} else {
		console.log( "no parentDS!") ;
	}

	/**
	 *
	 */
	this.key	=	"" ;
	this.id		=	-1 ;
	this.val	=	"" ;
	this.wd		=	null ;				// no active WorkingDialog
	this.showStatus	=	null ;
	this.showInfo	=	null ;

	/**
	 * load
	 */
	this.load	=	function( _key, _id, _val) {
		this.key	=	_key ;
		this.id	=	_id ;
		this.val	=	_val ;
		this.refresh( true) ;
	} ;

	/**
	 *	methods for primary-type dataSource
	 */
	/**
	 * getLast
	 */
	this.getLast	=	function( _key, _id, _val, _force) {
		this.dispatch( true, this.fncLast) ;
	} ;
	/**
	 * getNext
	 */
	this.getNext	=	function( _key, _id, _val, _force) {
		var	canDiscard	=	true ;
		var	fieldsChanged	=	0 ;
		if ( ! _force)	_force	=	false ;
		if ( this.owner.beforeDiscard && ! _force) {
			canDiscard	=	false ;
			fieldsChanged	=	this.owner.beforeDiscard() ;
			if ( fieldsChanged != 0) {
				var	myPopup	=	new wapPopup( this, "error", {
							url:	"dialogDiscardData.html"
						,	modal:	true
						,	owner:	this
						,	title:	"Error Message"
						,	onGo:	function() {
								this.hide() ;
								this.owner.getNext( _key, _id, _val, true) ;
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
	} ;
	/**
	 * getPrev
	 */
	this.getPrev	=	function( _key, _id, _val, _force) {
		var	canDiscard	=	true ;
		var	fieldsChanged	=	0 ;
		if ( this.owner.beforeDiscard && ! _force) {
			canDiscard	=	false ;
			fieldsChanged	=	this.owner.beforeDiscard() ;
			if ( fieldsChanged != 0) {
				var	myPopup	=	new wapPopup( this, "error", {
													url:	"dialogDiscardData.html"
												,	modal:	true
												,	owner:	this
												,	title:	"Error Message"
												,	onGo:	function() {
														this.hide() ;
														this.owner.getPrev( _key, _id, _val, true) ;
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
	} ;
	/**
	 * refresh
	 */
	this.refresh	=	function( _addPOST) {
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGet, null, _addPOST) ;
	} ;

	/**
	 *	methods for list-tape dataSource
	 */
	this.firstPage	=	function( _form, _addPOST) {
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGetList, _form, "step=firstPage&"+_addPOST) ;
	} ;
	this.previousPage	=	function( _form, _addPOST) {
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGetList, _form, "step=previousPage&"+_addPOST) ;
	} ;
	this.oneBackward	=	function( _form, _addPOST) {
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGetList, _form, "step=oneBackward&"+_addPOST) ;
	} ;
	this.thisPage	=	function( _form, _addPOST) {
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGetList, _form, "step=thisPage&"+_addPOST) ;
	} ;
	this.oneForward	=	function( _form, _addPOST) {
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGetList, _form, "step=oneForward&"+_addPOST) ;
	} ;
	this.nextPage	=	function( _form, _addPOST) {
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGetList, _form, "step=nextPage&"+_addPOST) ;
	} ;
	this.lastPage	=	function( _form, _addPOST) {
		if ( ! _addPOST)
			_addPOST	=	"" ;
		this.dispatch( true, this.fncGetList, _form, "step=lastPage&"+_addPOST) ;
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
		if ( this.xmlData) {
			delete this.xmlData ;
		}
		if ( this.parentObject == "") {
			console.log( "loading main object ... " + this.object) ;
			if ( Array.isArray( this.key)) {
				url	=	"/api/dispatch.php"
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
				url	=	"/api/dispatch.php"
						+ "?sessionId=" + sessionId
						+ "&_obj=" + this.object
						+ "&_fnc=" + _fnc
						+ "&_key=" + encodeURIComponent( this.key)
						+ "&_id=" + this.id
						+ "&_val=" + this.val
						;
			}
		} else {
			console.log( "dispatching for dependent object ... " + this.parentObject + "::" + this.object) ;
			url	=	"/api/dispatch.php"
				+ "?sessionId=" + sessionId
				+ "&_obj=" + this.parentObject
				+ "&_fnc=" + _fnc
				+ "&_key=" + encodeURIComponent( this.parentDS.key)
				+ "&_id=" + this.id
				+ "&_val=" + this.object
				;
		}
		postVars	=	"" ;
		if ( _form) {
			if ( _form != null) {
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
		this.myRequest	=	new XMLHttpRequest() ;
		this.myRequest.open( "POST", url, true) ;
		this.myRequest.timeout	=	3000 ;		// timeout to 3 s before killing waiting dialog
		this.myRequest.setRequestHeader( "Content-type", "application/x-www-form-urlencoded") ;
        this.myRequest.setRequestHeader( "Accept", "text/xml") ;
        this.myRequest.setRequestHeader( "Accept", "application/*") ;
		this.myRequest.owner	=	this ;
		this.myRequest.addEventListener( 'load', function() {
			// for this call:
			// this := XMLHttpRequest
			// this.owner := dataSource
			if ( this.responseText.length > 0) {
                busyDialog.hide() ;
			    if ( this.responseXML !== null)
    				this.owner.dispatchResponseXML( this.responseXML) ;
			    else {
		            var blob = new Blob([new Uint8Array(this.response)], {type: "octet/stream"});
                    showFile( this.response) ;
                }
			} else {
				busyDialog.hide() ;
				window.alert( "Empty reply.\nThe script on the server side probably died pre-maturely.\nSee PHP error log and check for FExceptions!") ;
			}
		}) ;
		this.myRequest.addEventListener( 'error', function() {
			// for this call:
			// this := XMLHttpRequest
			// this.owner := dataSource
			this.owner.dispatchErrorXML( this.reponseXML) ;
		}) ;
		this.myRequest.addEventListener( 'timeout', function() {
			// for this call:
			// this := XMLHttpRequest
			// this.owner := dataSource
			busyDialog.hide() ;
		}) ;
		this.myRequest.send( postVars) ;
		return false ;
	} ;
	/**
	 *
	 */
    this.onUpload	=	function( _attr, _addPOST, _scope) {
        var	url ;
        var	postVars ;
        this.forms	=	"" ;
        for ( var i in _attr) {
            this[ i]	=	_attr[i] ;
        }
		if ( ! _addPOST)
			_addPOST	=	"" ;
		/**
		 * setup the indefinit progress bar
		 */
		new wapBusy( { url: "dialogWait.html"}) ;
		if ( this.xmlData) {
			delete this.xmlData ;
		}
		if ( this.parent == null) {
			if ( Array.isArray( this.key)) {
				url	=	"/api/dispatch.php"
					+ "?sessionId=" + sessionId
					+ "&_obj=" + this.object
                    + "&_fnc=" + this.fncUpload
					+ "&_key0=" + encodeURIComponent( this.key[0])
					+ "&_key1=" + encodeURIComponent( this.key[1])
					+ "&_key2=" + encodeURIComponent( this.key[2])
					+ "&_key3=" + encodeURIComponent( this.key[3])
					+ "&_id=" + this.id
					+ "&_val=" + this.val
					;
			} else {
				url	=	"/api/dispatch.php"
						+ "?sessionId=" + sessionId
						+ "&_obj=" + this.object
                    	+ "&_fnc=" + this.fncUpload
						+ "&_key=" + encodeURIComponent( this.key)
						+ "&_id=" + this.id
						+ "&_val=" + this.val
						;
			}
		} else {
			url	=	"/api/dispatch.php"
				+ "?sessionId=" + sessionId
                + "&_obj=" + this.parent.object
                + "&_fnc=" + this.fncUpload
				+ "&_key=" + encodeURIComponent( this.parent.key)
				+ "&_id=" + this.id
				+ "&_val=" + this.object
				;
		}
		this.myRequest = new XMLHttpRequest();
		this.myRequest.open( 'POST', url);
        this.myRequest.setRequestHeader( "Accept", "text/xml") ;
		this.myRequest.owner	=	this ;						//request belongs to this
		this.myRequest.addEventListener( 'load', function() {
            // for this call:
            // this := XMLHttpRequest
            // this.owner := dataSource
            if ( this.responseText.length > 0) {
                busyDialog.hide() ;
                this.owner.dispatchResponseXML( this.responseXML) ;
            } else {
                busyDialog.hide() ;
//                window.alert( "Empty reply.\nThe script on the server side probably died pre-maturely.\nSee PHP error log and check for FExceptions!") ;
            }
		}) ;
		this.myRequest.addEventListener( 'error', function() {
			this.owner.dispatchErrorXML( this.reponseXML) ;
		}) ;
		var _form	=	this.forms[0] ;
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
//		busyDialog.hide() ;
		statusMsg	=	_reply.getElementsByTagName( "Status")[0] ;
		if ( statusMsg) {
			statusCode	=	parseInt( _reply.getElementsByTagName( "StatusCode")[0].childNodes[0].nodeValue) ;
			statusInfo	=	_reply.getElementsByTagName( "StatusInfo")[0].childNodes[0].nodeValue ;
			statusText	=	_reply.getElementsByTagName( "StatusText")[0].childNodes[0].nodeValue ;
			if ( statusCode >= 0) {
				if ( this.owner) {
					if ( this.owner[this.owner.object] != null) {
						delete this.owner[this.owner.object] ;
					}
					if ( this.owner.objects != null) {
						delete this.owner.objects ;
					}
				}
				/**
				 * call the watDataSource.receivedData method
				 */
				this.handled	=	false ;
				if ( _reply.getElementsByTagName( "Data")[0] != null) {
					this.receivedData( _reply.getElementsByTagName( "Data")[0]) ;	//
					this.handled	=	true ;
				}
				if ( _reply.getElementsByTagName( "References")[0] != null) {
					this.receivedReferences( _reply.getElementsByTagName( "References")[0]) ;	//
					this.handled	=	true ;
				}
				/**
				 *	IF nothing could be done
				 *		see if my owner has something to do
				 *	ENDIF
				 */
				if ( ! this.handled) {
					if ( this.owner.handleDS) {
						this.owner.handleDS() ;
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
				var	targetURL	=	_reply.getElementsByTagName( "TargetURL")[0].childNodes[0].nodeValue ;
				var	statusDiv	=	$( document.createElement( 'div')) ;
				var	displayText	=	"<br/><b>StatusCode: " + statusCode + "</b><br/>" ;
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
				$( statusDiv).html( displayText);
				$( statusDiv).dialog( {
										autoOpen:	true,
										title:		"Fehlermeldung",
										width:		"900px"
									}) ;
			}
		} else {
		}
	} ;
	/**
	 *
	 */
	this.receivedData	=	function( _xmlData) {
		/**
		 *	IF the owner object of this datasource provides a callback 'onDataSourceLoaded'
		 *		call it
		 */
		if ( typeof _xmlData === 'object') {
			this.xmlData	=	_xmlData ;
			if ( this.owner) {
				if ( this.owner.onDataSourceLoaded) {
					this.owner.onDataSourceLoaded( this.owner, this.xmlData) ;
				} else {
				}
			} else if ( this.onDataSourceLoaded) {
				this.onDataSourceLoaded( null, this.xmlData) ;
			}
		} else {
		}
	} ;
	/**
	 *
	 */
	this.receivedReferences	=	function( _xmlData) {
		/**
		 *	IF the owner object of this datasource provides a callback 'onDataSourceLoaded'
		 *		call it
		 */
		if ( typeof _xmlData === 'object') {
			this.xmlData	=	_xmlData ;
			if ( this.owner.onDataSourceLoaded) {
				this.owner.receivedReferences( this.owner, this.xmlData) ;
			} else {
			}
		} else {
		}
	} ;
	/**
	 * function to be called upon reception of invalid reply or problems during the processing
	 */
	this.dispatchErrorXML	=	function( _res, _reply) {
		busyDialog.hide() ;
		var fenster = window.open(url, "Popupfenster", "width=400,height=300,resizable=no,location=no");
		var	myPopup	=	new wapPopup( this, "error", { displayText: "Received errorneous answer from Server. Data can not be interpreted!", modal: true}) ;
		myPopup.open() ;
	} ;

    /**
     *
     */
    this.onNew	=	function( _key, _id, _val, _form, _addPOST, _scope) {
        this.key	=	_key ;
        this.id	=	_id ;
        this.val	=	_val ;
        this.dispatch( true, this.fncNew, _form, _addPOST, _scope) ;
    } ;

	/**
	 *
	 */
	this.onCreate	=	function( _key, _id, _val, _form, _addPOST, _scope) {
		this.key	=	_key ;
		this.id	=	_id ;
		this.val	=	_val ;
		this.dispatch( true, this.fncAdd, _form, _addPOST, _scope) ;
	} ;

	/**
	 *
	 */
	this.onUpdate	=	function( _attr, _addPOST, _scope) {
		this.forms	=	"" ;
		for ( var i in _attr) {
			this[ i]	=	_attr[i] ;
		}
		this.dispatch( true, this.fncUpd, this.forms, _addPOST, _scope) ;
	} ;
	/**
	 *
	 */
	this.onSave	=	function( _attr, _addPOST, _scope) {
		this.forms	=	"" ;
		for ( var i in _attr) {
			this[ i]	=	_attr[i] ;
		}
		if ( this.id == -1) {
			this.dispatch( true, this.fncAdd, this.forms, _addPOST, _scope) ;
		} else {
			this.dispatch( true, this.fncUpd, this.forms, _addPOST, _scope) ;
		}
	} ;
	/**
	 *
	 */
	this.onDelete	=	function( _attr, _form, _addPOST, _scope) {
		this.forms	=	"" ;
		for ( var i in _attr) {
			this[ i]	=	_attr[i] ;
		}
		this.dispatch( true, this.fncDel, _form, _addPOST, _scope) ;
	} ;
	/**
	 *
	 */
	this.onMisc	=	function( _attr, _addPOST, _scope) {
		this.forms	=	"" ;
		for ( var i in _attr) {
			this[ i]	=	_attr[i] ;
		}
		this.dispatch( true, this.fncMisc, this.forms, _addPOST, _scope) ;
	} ;
}


function showFile(blob) {
    // It is necessary to create a new blob object with mime-type explicitly set
    // otherwise only Chrome works like it should
    var newBlob = new Blob( [blob], { type: "application//octet-stream"}) ;

    // IE doesn't allow using a blob object directly as link href
    // instead it is necessary to use msSaveOrOpenBlob
    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveOrOpenBlob(newBlob);
        return ;
    }

    // For other browsers:
    // Create a link pointing to the ObjectURL containing the blob.
    const data = window.URL.createObjectURL(newBlob) ;
    var link = document.createElement('a') ;
    link.href = data ;
    link.download="file.jpg";
    link.click();
    setTimeout( function(){
                    window.URL.revokeObjectURL(data);
                }, 1000) ;
}