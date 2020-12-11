/**
 *
 */
window.onload	=	initAll ;

var	xhr	=	false ;

function	initAll() {
	cookieName	=	"flaschen24_terms_accepted" ;
	if ( ! getCookie( cookieName)) {
//		var	w	=	window.open( "legal.html", "Rechtlicher Hinweis", "width=320,height=240") ;
		res	=	confirm( "Diese Internetseite verwendet Cookies und JavaScript.\n" +
				"Wenn Sie damit einverstanden sind dann klicken Sie bitte unten auf OK.\n" +
				"Diese Meldung erscheint dann innerhalb der naechsten 30 Tage nicht erneut, es sei denn,\n" +
				"dass Sie ueber die Funktionen Ihres Browsers das Cookie mit dem Namen '"+cookieName+"' " +
				"loeschen.") ;
		if ( res) {
			setCookie( cookieName, "yes", 30);
		} else {

		}
	}
//	cookieName	=	"flaschen24_newpwd" ;
//	if ( getCookie( cookieName) == "new") {
//		res	=	confirm( "Ein neues Passwort wurde Ihnen soeben per e-Mail gesendet.\n" +
//				"Bitte schauen Sie in Ihrem e-Mail Postfach nach.\n"
//				+ document.cookie) ;
//		setCookie( cookieName, "done", 1);
//	}
//	cookieName	=	"flaschen24_registered" ;
//	if ( getCookie( cookieName) == "new") {
//		res	=	confirm( "Ihre Registrierung ist abgeschlossen.\n" +
//				"Bitte schauen Sie in Ihrem e-Mail Postfach nach. Dort finden Sie ein Passwort f�r Ihre Anmeldung.") ;
//		setCookie( cookieName, "done", 1);
//	}
	/**
	 * refresh the shopping cart info
	 */
	document.getElementById( "CartInfo").innerHTML	=	"REFRESH" ;
	refCuCart() ;
}
function getCookie( c_name) {
	var c_value = document.cookie;
	var c_start = c_value.indexOf(" " + c_name + "=");
	if (c_start == -1) {
		c_start = c_value.indexOf(c_name + "=");
	}
	if (c_start == -1) {
		c_value = null;
	} else {
		c_start = c_value.indexOf("=", c_start) + 1;
		var c_end = c_value.indexOf(";", c_start);
		if (c_end == -1) {
			c_end = c_value.length;
		}
		c_value = unescape(c_value.substring(c_start,c_end));
	}
	return c_value;
}
function setCookie( c_name, value, exdays) {
	var	exdate	=	new Date() ;
	exdate.setDate(exdate.getDate() + exdays);
	var	c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie	=	c_name + "=" + c_value;
}
function deleteCookie( c_name) {
  // Delete a cookie by setting the date of expiry to yesterday
  date	=	new Date();
  date.setDate( date.getDate() -1);
  document.cookie	=	escape(c_name) + '=;expires=' + date;
}

function	refCuCart() {
	var	TS	=	new Date() ;
	var	myTime	=	"TS=" + TS.getTime() ;
	var	myCall	=	"/htmlMan/mzInfo.php?" + myTime ;
	makeRequestMZ( myCall) ;
	return false;
}

function	addToCustomerCart( srcName) {
	var	src	=	document.getElementById( srcName) ;
	var	confMessage ;
	var	meinRab ;
	var	meinRabProz ;
	var	meineQuantity ;
	var	meinPrice ;
	var	refPrice ;
	meinRab	=	parseFloat( src._IF1.value) + 0.000001357 ;
	refPrice	=	parseFloat( src._IPrice.value) ;
	meineQuantity	=	parseInt( src._IQuantity.value) ;
	meinPrice	=	refPrice * ( 1 - meinRab + meinRab / Math.sqrt( meineQuantity)) ;
	meinRabProz	=	(( refPrice - meinPrice.toFixed(2)) ) / refPrice * 100.0;
	confMessage	=	"<?php echo FTr::tr( 'Do you really wanto add') ; ?> " ;
	confMessage	+=	src._IQuantity.value + " <?php echo FTr::tr( 'pc(s). of Article No.') ?> " ;
	confMessage	+=	src._IArticleNo.value + ", '" + src._IArticleDescription1.value + "' " ;
	confMessage	+=	"<?php echo FTr::tr( 'at a value of') ; ?> " + meinPrice.toFixed(2) ;
	confMessage	+=	" ( qty. / pack = " + src._IQuantityPerPU.value + " ) " ;
	confMessage	+=	"<?php echo FTr::tr( 'Euro a pc. plus applicable taxes to your wishlist') ; ?> " ;
	confMessage	+=	"<?php echo FTr::tr( '(Discount =') ; ?> " + meinRabProz.toFixed(2) + "%) ?" ;
	if ( confirm( confMessage)) {
		merken( srcName) ;
	} else {
	}
}

function	merken( srcName) {
	var	TS	=	new Date() ;
	var	src	=	document.getElementById( srcName) ;
	var	myArticleNo	=	"_IArticleNo=" + src._IArticleNo.value ;
	var	myPrice	=	"_IPrice=" + src._IPrice.value ;
	var	myQuantity	=	"_IQuantity=" + src._IQuantity.value ;
	var	myQuantityPerPU	=	"_IQuantityPerPU=" + src._IQuantityPerPU.value ;
	var	myTime	=	"TS=" + TS.getTime() ;
	var	myCall	=	"/htmlMan/mzInfo.php?" + myArticleNo + "&" + myPrice + "&" + myQuantity + "&" + myQuantityPerPU + "&" + myTime ;
	makeRequestMZ( myCall) ;
	return false;
}

function	makeRequestMZ( url) {
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
		document.getElementById( "CuCartInfo").innerHTML	=	"NO DATA" ;
	}
}

function	showMZInfo() {
	if ( xhr.readyState == 4) {
		if ( xhr.status == 200) {
			var	outMsg	=	xhr.responseText ;
//			makeRequestSearch( "/htmlMan/suchen.php?") ;
		} else {
			var	outMsg	=	"PROBLEM" ;
		}
	}
	document.getElementById( "CartInfo").innerHTML	=	outMsg ;
}

function	makeRequestSearch( url) {
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
		xhr.onreadystatechange	=	showInfoSearch ;
		xhr.open( "GET", url, true) ;
		xhr.send( null) ;
	} else {
		document.getElementById( "CartInfo").innerHTML	=	"NO DATA" ;
	}
}

function	showInfoSearch() {
	if ( xhr.readyState == 4) {
		if ( xhr.status == 200) {
			var	outMsg	=	xhr.responseText ;
		} else {
			var	outMsg	=	"PROBLEM" ;
		}
	}
	document.getElementById( "SearchTerm").value	=	outMsg ;
}

function	enterSearch() {
	var	currVal	=	document.getElementById( "Suchbegriff").value ;
	if ( currVal == "<?php echo FTr::tr( 'Searchterm...') ; ?>") {
		document.getElementById( "SearchTerm").value	=	"" ;
	}
}

function	classChange(styleChange,item) {
	item.className	=	styleChange;
}

function	activate_descr() {
	myDiv	=	document.getElementById( 'subnav_descr') ;
	myDiv.className	=	"product_subnav_active" ;
	myDiv	=	document.getElementById( 'subnav_attrs') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'subnav_docs') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'subnav_misc') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'cont_descr') ;
	myDiv.style.display	=	"block" ;
	myDiv	=	document.getElementById( 'cont_attrs') ;
	myDiv.style.display	=	"none" ;
	myDiv	=	document.getElementById( 'cont_docs') ;
	myDiv.style.display	=	"none" ;
	myDiv	=	document.getElementById( 'cont_misc') ;
	myDiv.style.display	=	"none" ;
}

function	activate_attrs() {
	myDiv	=	document.getElementById( 'subnav_descr') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'subnav_attrs') ;
	myDiv.className	=	"product_subnav_active" ;
	myDiv	=	document.getElementById( 'subnav_docs') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'subnav_misc') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'cont_descr') ;
	myDiv.style.display	=	"none" ;
	myDiv	=	document.getElementById( 'cont_attrs') ;
	myDiv.style.display	=	"block" ;
	myDiv	=	document.getElementById( 'cont_docs') ;
	myDiv.style.display	=	"none" ;
	myDiv	=	document.getElementById( 'cont_misc') ;
	myDiv.style.display	=	"none" ;
}

function	activate_docs() {
	myDiv	=	document.getElementById( 'subnav_descr') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'subnav_attrs') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'subnav_docs') ;
	myDiv.className	=	"product_subnav_active" ;
	myDiv	=	document.getElementById( 'subnav_misc') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'cont_descr') ;
	myDiv.style.display	=	"none" ;
	myDiv	=	document.getElementById( 'cont_attrs') ;
	myDiv.style.display	=	"none" ;
	myDiv	=	document.getElementById( 'cont_docs') ;
	myDiv.style.display	=	"block" ;
	myDiv	=	document.getElementById( 'cont_misc') ;
	myDiv.style.display	=	"none" ;
}

function	activate_misc() {
	myDiv	=	document.getElementById( 'subnav_descr') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'subnav_attrs') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'subnav_docs') ;
	myDiv.className	=	"product_subnav_inactive" ;
	myDiv	=	document.getElementById( 'subnav_misc') ;
	myDiv.className	=	"product_subnav_active" ;
	myDiv	=	document.getElementById( 'cont_descr') ;
	myDiv.style.display	=	"none" ;
	myDiv	=	document.getElementById( 'cont_attrs') ;
	myDiv.style.display	=	"none" ;
	myDiv	=	document.getElementById( 'cont_docs') ;
	myDiv.style.display	=	"none" ;
	myDiv	=	document.getElementById( 'cont_misc') ;
	myDiv.style.display	=	"block" ;
}
