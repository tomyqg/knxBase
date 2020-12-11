/**
 * regModMerchant_Ebay
 * 
 * registers the module in the central database
 */
function	scrMerchant_Ebay() {
	Screen.call( this, "Merchant_Ebay") ;
	this.package	=	"ModMerchant_Ebay" ;
	this.module	=	"Merchant_Ebay" ;
	this.coreObject	=	"Merchant_Ebay" ;
	this.showFunc	=	null ;
	this.keyForm	=	"Merchant_EbayKeyData" ;
	this.keyField	=	getFormField( 'Merchant_EbayKeyData', '_IMerchant_EbayId') ;
	this.delConfDialog	=	null ;
	/**
	 * create the DTVs
	 */
	/**	Shipping Addresses		*/
	this.dtvEbay_ShippingAddress = new dataTableView( this, "dtvEbay_ShippingAddress", "TableEbay_ShippingAddress", "Merchant_Ebay", "Ebay_ShippingAddress", null) ; 
	this.dtvEbay_ShippingAddress.f1 = "formEbay_ShippingAddressTop" ; 
	this.dtvEbay_ShippingAddress.primObjKey	=	this.keyField.value ;
	/**	Item					*/
	this.dtvEbay_Item = new dataTableView( this, "dtvEbay_Item", "TableEbay_Item", "Merchant_Ebay", "Ebay_Item", null) ; 
	this.dtvEbay_Item.f1 = "formEbay_ItemTop" ; 
	this.dtvEbay_Item.primObjKey	=	this.keyField.value ;
	/**	Order					*/
	this.dtvEbay_Order = new dataTableView( this, "dtvEbay_Order", "TableEbay_Order", "Merchant_Ebay", "Ebay_Order", null) ; 
	this.dtvEbay_Order.f1 = "formEbay_OrderTop" ; 
	this.dtvEbay_Order.primObjKey	=	this.keyField.value ;
	/**	Transction				*/
	this.dtvEbay_Transaction = new dataTableView( this, "dtvEbay_Transaction", "TableEbay_Transaction", "Merchant_Ebay", "Ebay_Transaction", null) ; 
	this.dtvEbay_Transaction.f1 = "formEbay_TransactionTop" ; 
	this.dtvEbay_Transaction.primObjKey	=	this.keyField.value ;
	/**
	 * 
	 */
	this.fncLink	=	function() {
		_debugL( 0x00000001, "linkMerchant_Ebay(): begin\n") ;
		_debugL( 0x00000001, "linkMerchant_Ebay(): end\n") ;
	}
	_debugL( 0x00000001, "regModMerchant_Ebay(): end\n") ;
}