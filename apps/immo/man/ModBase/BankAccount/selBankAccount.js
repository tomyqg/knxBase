/**
 * 
 */
var	selBankAccountDialog = null ;
var	selBankAccountMod = "" ;
var	selBankAccountApp = "" ;
var	selBankAccountFnc = "" ;
var	selBankAccountCb = null ;

/**
 *
 */
function	selBankAccount( _mod, _app, _fnc, _key, _cb) {

	selBankAccountMod	=	_mod ;
	selBankAccountApp	=	_app ;
	selBankAccountFnc	=	_fnc ;
	selBankAccountCb	=	_cb ;
	if ( selBankAccountDialog == null) {
		selBankAccountDialog	=	new dijit.Dialog( {
								title:	"Auswahl BankAccount",
								duration:	100,
								href:	"/Base/BankAccount/selBankAccount.php"
							} ) ;
	}
	selBankAccountDialog.show() ;
}

/**
 *
 */
function	refSelBankAccount( _mod, _app, _fnc, _form) {
	var	myForm = null ;
	var	postVars	=	"" ;
	var	fields ;
	var	url	=	_mod + "/" + _app + "/"
					+ "selBankAccount_action.php?"
					+ "&_fnc=" + _fnc
					;
	/**
	 *
	 */
	postVars	=	getPOSTData( _form) ;
	dojo.xhrPost( {
		url: url,
		handleAs: "xml",
		postData: postVars,
		load: function( response) {
			refSelBankAccountReply( response) ;
			showStatus( response) ;
		},
		error: function( response) {
			showStatus( response) ;
		}
	} ) ;
	return false ;
}

function	refSelBankAccountCont( _mod, _app, _fnc, _form) {
	if ( _SBankAccountNr.value.length > 3) {
		refSelBankAccount( _mod, _app, _fnc, _form) ;
	}
	return true ;
}

function	selBankAccountFirstTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
	refSelBankAccount( "Base", "BankAccount", "refSelBankAccount", _form) ;
}

function	selBankAccountPrevTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	if ( parseInt( startRow.value) > 5)
		startRow.value	=	parseInt( startRow.value) - 5 ;
	else
		startRow.value	=	0 ;
	refSelBankAccount( "Base", "BankAccount", "refSelBankAccount", _form) ;
}

function	selBankAccountNextTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	parseInt( startRow.value) + 5 ;
	refSelBankAccount( "Base", "BankAccount", "refSelBankAccount", _form) ;
}

function	selBankAccountLastTen( _form) {
	var	startRow ;
	startRow	=	getFormField( _form, "_SStartRow") ;
	startRow.value	=	0 ;
}

/**
 *
 */
function	refSelBankAccountReply( response) {

	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divSelBankAccount	=	document.getElementById( "selBankAccount") ;
	divSelBankAccount.innerHTML	=	"" ;

	myData	=	"NEUE DATEN:<br/>" ;
	tableBankAccount	=	response.getElementsByTagName( "BankAccount") ;
	if ( tableBankAccount) {
		myData	=	"" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th>Id</th>" ;
		myData	+=	"<th>ERP no.</th>" ;
		myData	+=	"<th>Name</th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableBankAccount.length && i < 20 ; i++) {
			BankAccount	=	tableBankAccount[i] ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#ddffdd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + BankAccount.getElementsByTagName( "Id")[0].textContent + "</td>" ;
			myLine	+=	"<td>"
						+ BankAccount.getElementsByTagName( "ERPNo")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td>"
						+ BankAccount.getElementsByTagName( "FullName")[0].textContent
						+ "</td>" ;
			myLine	+=	"<td><input type=\"image\" src=\"/licon/yellow/18/door.png\" name=\"\" onclick=\"selBankAccountByERPNo('"
						+ BankAccount.getElementsByTagName( "ERPNo")[0].textContent
						+ "') ;\" /></td>" ;
			myLine	+=	"</tr>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
	}
	divSelBankAccount.innerHTML	=	myData ;
}

/**
 *
 */
function	selBankAccountByERPNo( _erpNo) {
	selBankAccountDialog.hide() ;
	requestUni( selBankAccountMod, selBankAccountApp, '/Common/hdlObject.php', selBankAccountFnc, _erpNo, '', '', null, selBankAccountCb) ;
	return false ;
}
