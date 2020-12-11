/**
 * regModLief
 * 
 * registers the module in the central database
 */
function	scrLief() {
	_debugL( 0x00000001, "mainLief.js::scrLief(): begin\n") ;
	Screen.call( this, "Lief") ;
	this.package	=	"ModBase" ;
	this.module	=	"Lief" ;
	this.coreObject	=	"Lief" ;
	this.keyForm	=	"LiefKeyData" ;		// form
	this.keyField	=	getFormField( 'LiefKeyData', '_ILiefNr') ;
	this.delConfDialog	=	"/ModBase/Lief/confLiefDel.php" ;
	/**
	 * create the selector
	 */
	this.selLief	=	new selector( this, 'selLief', 'ModBase', '/ModBase/Lief/selLief.php', 'Lief') ;
	/**
	 * automatically generated code: create tab specific code 
	 */
	/**
	 * getting JS for tab 'LiefMain' 
	 */
	/**
	 * getting JS for tab 'LiefMisc' 
	 */
	/**
	 * getting JS for tab 'LiefContacts' 
	 */
	this.dtvSuppContacts	=	new dataTableView( this, 'dtvSuppContacts', "TableSuppContacts", "Lief", "LiefKontakt", null, 'ModBase', 'Lief') ; 
	this.dtvSuppContacts.f1	=	"formSuppContactsTop" ; 
	this.dtvSuppDiscounts	=	new dataTableView( this, 'dtvSuppDiscounts', "TableSuppDiscounts", "Lief", "LiefRabatt", null, 'ModBase', 'Lief') ; 
	this.dtvSuppDiscounts.f1	=	"formSuppDiscountsTop" ; 
	/**
	 * 
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "mainLief.js::linkLief(): begin\n") ;
		_debugL( 0x00000001, "mainLief.js::linkLief(): end\n") ;
	} ;
	/**
	 * 
	 */
	this.onSelect	=	function( _key) {
		_debugL( 0x00000001, "mainLief.js::scrLief::loadLiefById(" + _key.toString() + "): begin\n") ;
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
		_debugL( 0x00000001, "mainLief.js::scrLief::loadLiefById(): end\n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( response) {
		_debugL( 0x00000001, "mainLief.js::scrLief::fncShowAll( <XML>): begin\n") ;
		if ( response) {
			adr	=	response.getElementsByTagName( "Lief")[0] ;
			attrs	=	adr.childNodes ;
			dispAttrs( attrs, "LiefKeyData") ;
			dispAttrs( attrs, "formLiefMain") ;
			dispAttrs( attrs, "formLiefSonstiges") ;
		} else
			_debugL( 0x00000001, "mainLief.js::scrLief::fncShowAll( <XML>): no response\n") ;
		this.dtvSuppContacts.primObjKey	=	this.keyField.value ;
		this.dtvSuppContacts.show( response) ;
		this.dtvSuppDiscounts.primObjKey	=	this.keyField.value ;
		this.dtvSuppDiscounts.show( response) ;
		_debugL( 0x00000001, "mainLief.js::scrLief::fncShowAll( <XML>): end\n") ;
	} ;
//	this.dmSuOrdrForSupp	=	new dataMiner( 'DataMinerSupp', 'getTableSuOrdrForSupp', 'divSuOrdrForSupp', 'f50', '', 'LfBestNr', 'screenLfBest', null, 'formSuOrdrForSuppTop', null) ;
//	this.dmSuDlvrForSupp	=	new dataMiner( 'DataMinerSupp', 'getTableSuDlvrForSupp', 'divSuDlvrForSupp', 'f50', '', 'LfLiefNr', 'screenLfLief', null, 'formSuDlvrForSuppTop', null) ;
	// process any pending 'link-to-screen# data
	if ( pendingKey != "") {
		requestUni( 'ModBase', 'Lief', '/Common/hdlObject.php', 'getXMLComplete', pendingKey, '', '', null, showLiefAll) ;
	}
	hookNextObject() ;
	pendingKey	=	"" ;
}
function	linkLief() {
	_debugL( 0x00000001, "linkLief: \n") ;
}
/**
 *
 */
function	showTableLiefEKPreisR( response) {
	_debugL( 0x00000001, "Hello ") ;
	updTableHead( response, "formLiefEKPreisRTop", "formLiefEKPreisRBot") ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divLiefEKPreisR	=	document.getElementById( "tableLiefEKPreisR") ;
	divLiefEKPreisR.innerHTML	=	"" ;

	myData	=	"" ;
	tableLiefEKPreisR	=	response.getElementsByTagName( "EKPreisR") ;
	tableArtikelEKPreis	=	response.getElementsByTagName( "ArtikelEKPreis") ;
	_debugL( 0x00000001, "hier 2") ;
	if ( tableLiefEKPreisR[0]) {
		_debugL( 0x00000001, "tableLiefEKPreisR") ;
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Eigene Artikel Nr.</th>" ;
		myData	+=	"<th></th>" ;
		myData	+=	"<th>Eigene Bezeichnung</th>" ;
		myData	+=	"<th>Lief. Artikel Nr.</th>" ;
		myData	+=	"<th>Lief. Artikel Text</th>" ;
		myData	+=	"<th>Lief. VK Preis</th>" ;
		myData	+=	"<th>Einkaufspreis</th>" ;
		myData	+=	"<th>Verkaufspreis</th>" ;
		myData	+=	"<th><?php echo FTr::tr( 'Real margin') ; ?></th>" ;
		myData	+=	"</tr>" ;
		for ( var i=0 ; i < tableLiefEKPreisR.length ; i++) {
			LiefEKPreisR	=	tableLiefEKPreisR[i] ;
			myLine	=	"<tr>" ;
			myLine	+=	"<td>" + LiefEKPreisR.getElementsByTagName( "Id")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>"
						+ btnLinkTo( "screenArtikel", LiefEKPreisR.getElementsByTagName( "ArtikelNr")[0].childNodes[0].nodeValue)
						+ LiefEKPreisR.getElementsByTagName( "ArtikelNr")[0].childNodes[0].nodeValue
						+ "</td>" ;
			myLine	+=	createButton( 'ModBase', 'Artikel', '/Common/hdlObject.php', 'createFromEKPreisRId', '', LiefEKPreisR.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, 'showTableLiefRabatt') ;
			myLine	+=	"<td>" + LiefEKPreisR.getElementsByTagName( "ArtikelBez1")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + LiefEKPreisR.getElementsByTagName( "LiefArtNr")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + LiefEKPreisR.getElementsByTagName( "LiefArtText")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + LiefEKPreisR.getElementsByTagName( "LiefVKPreis")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + LiefEKPreisR.getElementsByTagName( "Preis")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + LiefEKPreisR.getElementsByTagName( "OwnVKPreis")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + LiefEKPreisR.getElementsByTagName( "RealMargin")[0].childNodes[0].nodeValue + "</td>" ;
//			myLine	+=	deleteButton( 'ModBase', 'Lief', '/Common/hdlObject.php', 'delLiefRabatt', document.forms['LiefKeyData']._ILiefNr.value, LiefRabatt.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, 'showTableLiefRabatt') ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else if ( tableArtikelEKPreis[0] != null) {
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>Eigene Artikel Nr.</th>" ;
		myData	+=	"<th></th>" ;
		myData	+=	"<th>Lief. Artikel Nr.</th>" ;
		myData	+=	"<th>Lief. Artikel Text</th>" ;
		myData	+=	"<th>Lief. VK Preis</th>" ;
		myData	+=	"<th>Einkaufspreis</th>" ;
		myData	+=	"<th>Verkaufspreis</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableArtikelEKPreis.length ; i++) {
			ArtikelEKPreis	=	tableArtikelEKPreis[i] ;
			myLine	=	"<tr>" ;
			myLine	+=	"<td>" + ArtikelEKPreis.getElementsByTagName( "Id")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + ArtikelEKPreis.getElementsByTagName( "ArtikelNr")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	createButton( 'ModBase', 'Artikel', '/Common/hdlObject.php', 'createFromArtikelEKPreisId', '', ArtikelEKPreis.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, 'showTableLiefRabatt') ;
			myLine	+=	"<td>" + ArtikelEKPreis.getElementsByTagName( "LiefArtNr")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + ArtikelEKPreis.getElementsByTagName( "LiefArtText")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + ArtikelEKPreis.getElementsByTagName( "LiefVKPreis")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + ArtikelEKPreis.getElementsByTagName( "Preis")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + ArtikelEKPreis.getElementsByTagName( "OwnVKPreis")[0].childNodes[0].nodeValue + "</td>" ;
			myLine	+=	"<td>" + deleteButton( 'ModBase', 'Lief', '/Common/hdlObject.php', 'delArtikelEKPReis', document.forms['LiefKeyData']._ILiefNr.value, ArtikelEKPreis.getElementsByTagName( "Id")[0].childNodes[0].nodeValue, '', null, null) + "</td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	divLiefEKPreisR.innerHTML	=	myData ;
}

function	showLiefDocList( response) {
	showDocList( response, "TableLiefDocs", "/ModBase/Lief/showLiefFormat.php") ;
}
var	liefListType ;
function	liefGetEKPreisR( _mode) {
	if ( _mode) {
		liefListType	=	_mode ;
	}
	_liefGetEKPreisRTop( 'f50') ;
}
function	_liefGetEKPreisRTop( _step) {
	mySlogan	=	document.getElementById( "sloganLiefEKPreisR") ;
	if ( liefListType == 0) {
		mySlogan.innerHTML	=	"<center><h3>Unrelated Articles</h3></center>" ;
	} else 	if ( liefListType == 1) {
		mySlogan.innerHTML	=	"<center><h3>Unlisted Articles</h3></center>" ;
	} else 	if ( liefListType == 2) {
		mySlogan.innerHTML	=	"<center><h3>Listed Articles</h3></center>" ;
	}
	_debugL( 0x00000001, "requesting") ;
	requestUni( 'ModBase', 'Lief', '/Common/hdlObject.php', 'getEKPreisRListAsXML', document.forms['LiefKeyData']._ILiefNr.value, liefListType, _step, 'formLiefEKPreisRTop', showTableLiefEKPreisR) ;
	_debugL( 0x00000001, "requesting, done") ;
}
function	_liefGetEKPreisRBot( _step) {
	requestUni( 'ModBase', 'Lief', '/Common/hdlObject.php', 'getEKPreisRListAsXML', document.forms['LiefKeyData']._ILiefNr.value, liefListType, _step, 'formLiefEKPreisRBot', showTableLiefEKPreisR) ;
}
function	refLiefKontakt( _rng) {
	requestUni( 'ModBase', 'Lief', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['LiefKeyData']._ILiefNr.value,
			_rng,
			'LiefKontakt',
			'formLiefKontaktTop',
			showTableLiefKontakt) ;
	return false ; 	
}
function	refLiefRabatt( _rng) {
	requestUni( 'ModBase', 'Lief', '/Common/hdlObject.php', 'getTableDepAsXML',
			document.forms['LiefKeyData']._ILiefNr.value,
			_rng,
			'LiefRabatt',
			'formLiefRabattTop',
			showTableLiefRabatt) ;
	return false ; 	
}
