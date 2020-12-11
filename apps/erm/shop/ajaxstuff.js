/**
 * JavaScript stuff
 *
 * Sequence of evnts:
 */

var	xhr	=	false ;

window.onload	=	initAll ;

function	initAll() {
	document.getElementById( "CustomerCartInfo").innerHTML	=	"REFRESH" ;
	document.getElementById( "SearchTerm").value	=	"REFRESH" ;
	document.getElementById( "CustomerInfo").innerHTML	=	"REFRESH" ;
	refMerkzettel() ;
	getCustomerInfo( "CustomerInfo") ;
}

function	refMerkzettel() {
	var	TS	=	new Date() ;
	var	myTime	=	"TS=" + TS.getTime() ;
	var	myCall	=	"CustomerCartInfo?" + myTime ;
	requestCustomerCartInfo( myCall) ;
	return false;
}

function	requestCustomerCartInfo( url) {
	if ( window.XMLHttpRequest) {
		xhr	=	new XMLHttpRequest() ;
	} else if ( window.ActiveXObject) {
		try {
			xhr	=	new ActiveXObject( "Microsoft.XMLHTTP") ;
		}
		catch( e) {
		}
	}
	if ( xhr) {
		xhr.onreadystatechange	=	showCustomerCartInfo ;
		xhr.open( "GET", url, true) ;
		xhr.send( null) ;
	} else {
		document.getElementById( "CustomerCartInfo").innerHTML	=	"NO DATA" ;
	}
}

function	showCustomerCartInfo() {
	if ( xhr.readyState == 4) {
		if ( xhr.status == 200) {
			var	outMsg	=	xhr.responseText ;
		} else {
			var	outMsg	=	"PROBLEM" ;
		}
		refSuchen() ;
	}
	document.getElementById( "CustomerCartInfo").innerHTML	=	outMsg ;
}

function	refSuchen() {
	var	TS	=	new Date() ;
	var	myTime	=	"TS=" + TS.getTime() ;
	var	myCall	=	"suchen.php?" + myTime ;
	reqSuchenInfo( myCall) ;
	return false;
}

function	reqSuchenInfo( url) {
	if ( window.XMLHttpRequest) {
		xhr	=	new XMLHttpRequest() ;
	} else if ( window.ActiveXObject) {
		try {
			xhr	=	new ActiveXObject( "Microsoft.XMLHTTP") ;
		}
		catch( e) {
		}
	}
	if ( xhr) {
		xhr.onreadystatechange	=	showSuchenInfo ;
		xhr.open( "GET", url, true) ;
		xhr.send( null) ;
	} else {
		document.getElementById( "SearchTerm").value	=	"NO DATA" ;
	}
}

function	showSuchenInfo() {
	if ( xhr.readyState == 4) {
		if ( xhr.status == 200) {
			var	outMsg	=	xhr.responseText ;
		} else {
			var	outMsg	=	"PROBLEM" ;
		}
	}
	document.getElementById( "SearchTerm").value	=	outMsg ;
}

function	askUser( srcName) {
	var	src	=	document.getElementById( srcName) ;
	if ( confirm( "M&ouml;chten Sie wirklich<br/>" + src._IMenge.value + " St&uuml;sk des Artikels ArtikelNr, ArtikelBez1 auf dem Merkzettel notieren ?")) {
		aufMerkzettel( srcName) ;
	} else {
	}
}

function	addToCuCart( _myFormId) {
	var	myForm	=	document.getElementById( _myFormId) ;
	var	confMessage ;
	var	meinRab ;
	var	meinRabProz ;
	var	meineMenge ;
	var	meinPreis ;
	var	refPreis ;
//	meinRab	=	parseFloat( myForm._IF1.value) + 0.000001357 ;
	meinRab	=	1 ;
	refPreis	=	parseFloat( myForm._IPrice.value) ;
	meineMenge	=	parseInt( myForm._IQuantity.value) ;
	meinPreis	=	refPreis * ( 1 - meinRab + meinRab / Math.sqrt( meineMenge)) ;
	meinRabProz	=	(( refPreis - meinPreis.toFixed(2)) ) / refPreis * 100.0;
	confMessage	=	"Wollen Sie " ;
	confMessage	+=	myForm._IQuantity.value + " Stck. des Artikels " ;
	confMessage	+=	myForm._IArticleNo.value + ", " + myForm._IArticleDescription1.value + " " ;
	confMessage	+=	"zum Preis von " + meinPreis.toFixed(2) + " " ;
	confMessage	+=	"Euro pro Stck. " ;
	confMessage	+=	"zzgl. MwSt. " ;
	confMessage	+=	"auf dem Merkzettel notieren " ;
	confMessage	+=	"(Rabatt = " + meinRabProz.toFixed(2) + "%)" ;
	confMessage	+=	"?" ;
	if ( confirm( confMessage)) {
		reallyAddToCuCart( _myFormId) ;
	} else {
	}
}

function	reallyAddToCuCart( _myFormId) {
	var	TS	=	new Date() ;
	var	myForm	=	document.getElementById( _myFormId) ;
	var	myArtikelNr	=	"_IArticleNo=" + myForm._IArticleNo.value ;
	var	myPreis	=	"_IPrice=" + myForm._IPrice.value ;
	var	myMenge	=	"_IQuantity=" + myForm._IQuantity.value ;
	var	myMengeProVPE	=	"_IQuantityPerPU=" + myForm._IQuantityPerPU.value ;
	var	myTime	=	"TS=" + TS.getTime() ;
	var	myCall	=	"CustomerCartInfo?" + myArtikelNr + "&" + myPreis + "&" + myMenge + "&" + myMengeProVPE + "&" + myTime ;
	requestCustomerCartInfo( myCall) ;
	return false;
}

function	login() {
	var	myDiv	=	document.getElementById( "CustomerInfo") ;
	myRequest	=	new XMLHttpRequest() ;
	myRequest.open( "GET", "CustomerInfo?action=login&CustomerNo="+document.getElementById( "CustomerNo").value+"&Password="+document.getElementById( "Password").value);
	myRequest.addEventListener( 'load', function() {
		myDiv.innerHTML	=	myRequest.response ;
	}) ;
	myRequest.addEventListener( 'error', function() {
		alert( "Error loading htmlURL := '"+htmlURL+"'") ;
		startRefresh() ;
	}) ;
	postVars	=	"" ;
	myRequest.send( postVars) ;
}

function	logout() {
	var	myDiv	=	document.getElementById( "CustomerInfo") ;
	myRequest	=	new XMLHttpRequest() ;
	myRequest.open( "GET", "CustomerInfo?action=logout");
	myRequest.addEventListener( 'load', function() {
		myDiv.innerHTML	=	myRequest.response ;
	}) ;
	myRequest.addEventListener( 'error', function() {
		alert( "Error loading htmlURL := '"+htmlURL+"'") ;
		startRefresh() ;
	}) ;
	postVars	=	"" ;
	myRequest.send( postVars) ;
}

function	newPassword() {
	var	myDiv	=	document.getElementById( "CustomerInfo") ;
	myRequest	=	new XMLHttpRequest() ;
	myRequest.open( "POST", "CustomerInfo?action=newPassword");
	myRequest.addEventListener( 'load', function() {
		myDiv.innerHTML	=	myRequest.response ;
	}) ;
	myRequest.addEventListener( 'error', function() {
		alert( "Error loading htmlURL := '"+htmlURL+"'") ;
		startRefresh() ;
	}) ;
	postVars	=	"" ;
	myRequest.send( postVars) ;
}

function	getCustomerInfo() {
	var	myDiv	=	document.getElementById( "CustomerInfo") ;
	myRequest	=	new XMLHttpRequest() ;
	myRequest.open( "POST", "CustomerInfo");
	myRequest.addEventListener( 'load', function() {
		myDiv.innerHTML	=	myRequest.response ;
	}) ;
	myRequest.addEventListener( 'error', function() {
		alert( "Error loading htmlURL := '"+htmlURL+"'") ;
		startRefresh() ;
	}) ;
	postVars	=	"" ;
	myRequest.send( postVars) ;
}

function enable( _src, _target) {
	if ( document.getElementById( _src0).checked) {
		document.getElementById( _target).disabled    =       false ;
	} else {
		document.getElementById( _target).disabled    =       true ;
	}
}
