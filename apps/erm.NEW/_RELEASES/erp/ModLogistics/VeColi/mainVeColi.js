/**
 * regModVeColi
 * 
 * registers the module in the central database
 */
function	scrVeColi() {
	Screen.call( this, "VeColi") ;				// instantiate the super-class!!!
	this.package	=	"ModLogistics" ;			// directory of the module
	this.module	=	"VeColi" ;					// sub-directory of the screen
	this.coreObject	=	"VeColi" ;
	this.keyForm	=	"VeColiKeyData" ;		// form
	this.keyField	=	getFormField( 'VeColiKeyData', '_IVeColiNr') ;
	this.delConfDialog	=	"/ModLogistics/VeColi/confVeColiDel.php" ;
	/**
	 * create the selector
	 */
	this.selVeColi	=	new selector( this, 'selVeColi', 'ModLogistics', '/ModLogistics/VeColi/selVeColi.php', 'VeColi') ;
	/**
	 * getting JS for tab 'VeColiPosten' 
	 */
	this.dtvVeColiItems = new dataTableView( this, "dtvVeColiItems", "TableVeColiItems", "VeColi", "VeColiPos", null, "ModLogistics", "VeColi") ; 
	this.dtvVeColiItems.f1 = "formVeColiItemsTop" ; 
	/**
	 * make sure everything is setup fo this module, e.g. menuitems enables/disables etc.
	 */
	this.fncLink	=	function() {
	} ;
	/**
	 * 
	 */
	_debugL( 0x00000001, "mainVeColi.js::regModVeColi(): assigning onSelect function\n") ;
	this.onSelect	=	function( _key) {
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
	} ;
	/**
	 * 
	 */
	this.fncShow	=	function( _response) {
		showVeColi( _response) ;
		showKundeAdrs( _response, "formVeColiKunde", "formVeColiKundeKontakt", "", "", "", "") ;
		this.dtvVeColiItems.primObjKey	=	this.keyField.value ;
		this.dtvVeColiItems.show( _response) ;
	} ;
	/**
	 * this.onSelect( pendingKey) ;
	 */
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	}
	pendingKey	=	"" ;
}
/**
 *
 */
function	showVeColi( _response) {
	var	lockInfo ;
	var	kunde ;
	var	attrs ;
	var	myVeColiNr ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	veColi	=	_response.getElementsByTagName( "VeColi")[0] ;
	if ( veColi) {
		myVeColiNr	=	_response.getElementsByTagName( "VeColiNr")[0].textContent ;
		attrs	=	veColi.childNodes ;
		dispAttrs( attrs, "VeColiKeyData") ;
		dispAttrs( attrs, "formVeColiMain") ;
		dispAttrs( attrs, "formVeColiModi") ;
	}
}

/**
 *
 */
function	showVeColiKunde( _response) {
	var	lockInfo ;
	var	kunde ;
	var	attrs ;
	var	myVeColiNr ;
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	clearForm( "showVeColiKunde") ;
	clearForm( "showVeColiLiefKunde") ;
	clearForm( "showVeColiRechKunde") ;

//	veColi	=	_response.getElementsByTagName( "VeColi")[0] ;
//	attrs	=	veColi.childNodes ;
//	dispAttrs( attrs, "formVeColiMain") ;

	kunde	=	_response.getElementsByTagName( "Kunde")[0] ;
	if ( kunde) {
		attrs	=	kunde.childNodes ;
		dispAttrs( attrs, "formVeColiKunde", false) ;

		kundeKontakt	=	_response.getElementsByTagName( "KundeKontakt")[0] ;
		if ( kundeKontakt) {
			attrs	=	kundeKontakt.childNodes ;
			dispAttrs( attrs, "formVeColiKundeKontakt", false) ;
		}

		liefkunde	=	_response.getElementsByTagName( "LiefKunde")[0] ;
		if ( liefkunde) {
			attrs	=	liefkunde.childNodes ;
			dispAttrs( attrs, "formVeColiLiefKunde", false) ;
		}

		liefKundeKontakt	=	_response.getElementsByTagName( "LiefKundeKontakt")[0] ;
		if ( liefKundeKontakt) {
			attrs	=	liefKundeKontakt.childNodes ;
			dispAttrs( attrs, "formVeColiLiefKundeKontakt", false) ;
		}

		rechkunde	=	_response.getElementsByTagName( "RechKunde")[0] ;
		if ( rechkunde) {
			attrs	=	rechkunde.childNodes ;
			dispAttrs( attrs, "formVeColiRechKunde") ;
		}

		rechKundeKontakt	=	_response.getElementsByTagName( "RechKundeKontakt")[0] ;
		if ( rechKundeKontakt) {
			attrs	=	rechKundeKontakt.childNodes ;
			dispAttrs( attrs, "formVeColiRechKundeKontakt") ;
		}
	}
}