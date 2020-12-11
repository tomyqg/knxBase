<?php 
	require_once("config.inc.php");
	Header("content-type: application/x-javascript");
?>
/**
 *
 */
function	gotoSearch( _searchKey, _backTo, _backKey) {
	dialogStack.push( _backTo) ;
	dialogKeyStack.push( _backKey) ;
	goto( 'screenSearch') ;
	requestUni( 'Base', 'Search', '/Common/hdlObject.php', 'getXMLComplete', _searchkey, '', '', null, showTableSearch) ;
	return false ;
}

function	retToSearch( _searchKey) {
	goto( 'screenSearch') ;
	if ( _searchKey != document.forms['SearchKeyData']._ISearchKey.value) {
		requestUni( 'Base', 'Search', '/Common/hdlObject.php', 'getXMLComplete', _searchkey, '', '', null, showSearchSearch) ;
	}
}

/**
 *
 */
function	showTableSearch( response) {
	/**
	 * die folgenden 2 Zeilen koennen zum Debuggen TEMPORAER verwendet werden
	 */
	divSearch	=	document.getElementById( "divSearch") ;
	divSearch.innerHTML	+=	"Here we are ... \n" ;

	myData	=	"" ;
	tableResult	=	response.getElementsByTagName( "Result") ;
	if ( tableResult) {
		myData	+=	"tableSearch contains " + tableResult.length + " records\n" ;
		myData	+=	"<table>" ;
		myData	+=	"<tr>" ;
		myData	+=	"<th><?php echo FTr::tr( "Type") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Data 1") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Data 2") ; ?></th>" ;
		myData	+=	"<th><?php echo FTr::tr( "Data 3") ; ?></th>" ;
		myData	+=	"</tr>" ;
		for ( i=0 ; i < tableResult.length ; i++) {
			result	=	response.getElementsByTagName( "Result")[i] ;
			type	=	result.getElementsByTagName( "Type")[0].textContent ;
			data1	=	result.getElementsByTagName( "Data1")[0].textContent ;
			myLine	=	"<tr onMouseOver=\"this.style.backgroundColor='#dddddd'\" onMouseOut=\"this.style.backgroundColor='#ffffff'\">" ;
			myLine	+=	"<td>" + result.getElementsByTagName( "Type")[0].textContent + "</td>" ;
			switch ( type) {
			case	"Article"	:
				myLine	+=	"<td>"
							+ gotoButton( "gotoArtikel", data1, 'retToSearch', document.forms['SearchKeyData']._ISearchKey.value)
							+ data1
							+ "</td>" ;
				break ;
			case	"Supp"	:
				myLine	+=	"<td>"
							+ gotoButton( "gotoLief", data1, 'retToSearch', document.forms['SearchKeyData']._ISearchKey.value)
							+ data1
							+ "</td>" ;
				break ;
			case	"Cust"	:
				myLine	+=	"<td>"
							+ gotoButton( "gotoKunde", data1, 'retToSearch', document.forms['SearchKeyData']._ISearchKey.value)
							+ data1
							+ "</td>" ;
				break ;
			default	:
				myLine	+=	"<td>"
							+ data1
							+ "</td>" ;
				break ;
			}
			myLine	+=	"<td>" + result.getElementsByTagName( "Data2")[0].textContent + "</td>" ;
			myLine	+=	"<td>" + result.getElementsByTagName( "Data3")[0].textContent + "</td>" ;
			myData	+=	myLine ;
		}
		myData	+=	"</table>" ;
	} else {
		myData	+=	"no result data" ;
	}
	divSearch.innerHTML	=	myData ;
}