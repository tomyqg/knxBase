/**
 * JavaScript stuff
 * 
 * Sequence of evnts:
 */

var	xhr	=	false ;

window.onload	=	initAll ;

function	initAll() {
	document.getElementById( "MerkzettelInfo").innerHTML	=	"REFRESH" ;
	document.getElementById( "SuchBegriff").value	=	"REFRESH" ;
	refMerkzettel() ;
}

function	refMerkzettel() {
	var	TS	=	new Date() ;
	var	myTime	=	"TS=" + TS.getTime() ;
	var	myCall	=	"merkzettel.php?" + myTime ;
	reqMZInfo( myCall) ;
	return false;
}

function	reqMZInfo( url) {
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
		xhr.onreadystatechange	=	showMZInfo ;
		xhr.open( "GET", url, true) ;
		xhr.send( null) ;
	} else {
		document.getElementById( "MerkzettelInfo").innerHTML	=	"NO DATA" ;
	}
}

function	showMZInfo() {
	if ( xhr.readyState == 4) {
		if ( xhr.status == 200) {
			var	outMsg	=	xhr.responseText ;
		} else {
			var	outMsg	=	"PROBLEM" ;
		}
		refSuchen() ;
	}
	document.getElementById( "MerkzettelInfo").innerHTML	=	outMsg ;
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
		document.getElementById( "SuchBegriff").value	=	"NO DATA" ;
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
	document.getElementById( "SuchBegriff").value	=	outMsg ;
}

function	askUser( srcName) {
	var	src	=	document.getElementById( srcName) ;
	if ( confirm( "M&ouml;chten Sie wirklich<br/>" + src._IMenge.value + " St&uuml;sk des Artikels ArtikelNr, ArtikelBez1 auf dem Merkzettel notieren ?")) {
		aufMerkzettel( srcName) ;
	} else {
	}
}

function	aufMerkzettel( srcName) {
	var	src	=	document.getElementById( srcName) ;
	var	confMessage ;
	var	meinRab ;
	var	meinRabProz ;
	var	meineMenge ;
	var	meinPreis ;
	var	refPreis ;
	meinRab	=	parseFloat( src._IF1.value) + 0.000001357 ;
	refPreis	=	parseFloat( src._IPreis.value) ;
	meineMenge	=	parseInt( src._IMenge.value) ;
	meinPreis	=	refPreis * ( 1 - meinRab + meinRab / Math.sqrt( meineMenge)) ;
	meinRabProz	=	(( refPreis - meinPreis.toFixed(2)) ) / refPreis * 100.0;
	confMessage	=	"Wollen Sie " ;
	confMessage	+=	src._IMenge.value + " Stck. des Artikels " ;
	confMessage	+=	src._IArtikelNr.value + ", " + src._IArtikelBez1.value + " " ;
	confMessage	+=	"zum Preis von " + meinPreis.toFixed(2) + " " ;
	confMessage	+=	"Euro pro Stck. " ;
	confMessage	+=	"zzgl. MwSt. " ;
	confMessage	+=	"auf dem Merkzettel notieren " ;
	confMessage	+=	"(Rabatt = " + meinRabProz.toFixed(2) + "%)" ;
	confMessage	+=	"?" ;
	if ( confirm( confMessage)) {
		merken( srcName) ;
	} else {
	}
}

function	merken( srcName) {
	var	TS	=	new Date() ;
	var	src	=	document.getElementById( srcName) ;
	var	myArtikelNr	=	"_IArtikelNr=" + src._IArtikelNr.value ;
	var	myPreis	=	"_IPreis=" + src._IPreis.value ;
	var	myMenge	=	"_IMenge=" + src._IMenge.value ;
	var	myMengeProVPE	=	"_IMengeProVPE=" + src._IMengeProVPE.value ;
	var	myTime	=	"TS=" + TS.getTime() ;
	var	myCall	=	"merkzettel.php?" + myArtikelNr + "&" + myPreis + "&" + myMenge + "&" + myMengeProVPE + "&" + myTime ;
	reqMZInfo( myCall) ;
	return false;
}
