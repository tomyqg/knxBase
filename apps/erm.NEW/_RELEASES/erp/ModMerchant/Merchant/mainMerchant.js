/**
 * regModMerchant
 * 
 * registers the module in the central database
 */
function	scrMerchant() {
	_debugL( 0x00000001, "mainMerchant.js::scrMerchant.__constructor(): begin\n") ;
	Screen.call( this, "Merchant") ;
	this.package	=	"ModMerchant" ;
	this.module	=	"Merchant" ;
	this.coreObject	=	"Merchant" ;
	this.keyForm	=	"MerchantKeyData" ;
	this.keyField	=	getFormField( 'MerchantKeyData', '_IMerchantId') ;
	/**
	 * create the selector
	 */
	this.selMerchant	=	new selector( this, 'selMerchant', 'ModMerchant', '/ModMerchant/Merchant/selMerchant.php', 'Merchant') ;
	/**
	 * 
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "mainMerchant.js::scrMerchant::fncLink(): begin\n") ;
		_debugL( 0x00000001, "mainMerchant.js::scrMerchant::fncLink(): end\n") ;
	} ;
	/**
	 * 
	 */
	this.onSelect	=	function( _key) {
		_debugL( 0x00000001, "mainMerchant.js::scrAdr::loadAdrById(" + _key.toString() + "): begin\n") ;
		this.dispatch( true, 'getXMLComplete', _key, '', '') ;
		_debugL( 0x00000001, "mainMerchant.js::scrAdr::loadAdrById(): end\n") ;
	} ;
	/**
	 *
	 */
	this.fncShow	=	function( response) {
		_debugL( 0x00000001, "mainMerchant.js::scrAdr::fncShow(...): begin\n") ;
		obj	=	response.getElementsByTagName( "Merchant")[0] ;
		attrs	=	obj.childNodes ;
		dispAttrs( attrs, "MerchantKeyData") ;
		dispAttrs( attrs, "formMerchantMain") ;
		dispAttrs( attrs, "formMerchantInfo") ;
		_debugL( 0x00000001, "mainMerchant.js::scrAdr::fncShow(...): end\n") ;
	} ;
	/**
	 * 
	 */
	if ( pendingKey != "") {
		this.onSelect( pendingKey) ;
	} else if ( pendingFnc != null) {
		pendingFnc() ;
	} else {
		hookPrevObject() ;		// automatically go to last invoice upon load
	}
	pendingKey	=	"" ;
	pendingFnc	=	null ;
	_debugL( 0x00000001, "mainMerchant.js::scrMerchant.__constructor(): end\n") ;
}