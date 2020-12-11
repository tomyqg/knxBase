<?php

require_once( "config.inc.php") ;
require_once( "pkgs/platform/FDb.php") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;

header("Content-type: text/xml");
$ret	=	'<?xml version="1.0" encoding="utf-8" ?>' ;
$ret	.=	'<Reply>' ;

switch ( $_GET['_fnc']) {

case	"refSelBankAccount"	:

	$ret	.=	'<Data>' ;
	try {

		$_erpNoCrit	=	$_POST['_SERPNo'] ;
		$_fullNameCrit	=	$_POST['_SFullName'] ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT A.Id AS Id, A.ERPNo AS ERPNo, A.FullName AS FullName " ;
		$query	.=	"FROM BankAccount AS A " ;
		$query	.=	"WHERE ( A.ERPNo like '" . $_erpNoCrit . "%' ) " ;
		$query	.=	"  AND ( A.FullName like '%" . $_fullNameCrit . "%') " ;
		$query	.=	"ORDER BY A.ERPNo " ;
			
//		if ( strlen( $_orderBy1) > 0) {
//			$query	.=	"order by " . $_orderBy1 . " " ;
//			$query	.=	$_orderBy1Dir . " " ;
//			if ( strlen( $_orderBy2) > 0) {
//				$query	.=	", " . $_orderBy2 . " " ;
//				$query	.=	$_orderBy2Dir . " " ;
//			}
//		}
		$query	.=	"LIMIT " . $_dbStartRow . ", 5 " ;

		$sqlResult	=	FDb::query( $query) ;

		header("Content-type: text/xml");
		$ret	.=	'<TableBankAccount>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<BankAccount>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . "><![CDATA[" . $val . "]]></" . $ndx . ">\n" ;
			}
			$ret	.=	"</BankAccount>\n" ;
		}

		$ret	.=	"</TableBankAccount>\n" ;

	} catch ( Exception $e) {

		$statusCode	=	-1 ;
		$statusText	=	"function " . $_GET['_fnc'] . " invalid" ;

	}
	$ret	.=	'</Data>' ;
	break ;

default	:
	$statusCode	=	-999 ;
	$statusText	=	"function " . $_GET['_fnc'] . " invalid" ;
	break ;

}

$ret	.=	'<Status>' ;
$ret	.=	'<StatusCode>' . $statusCode . '</StatusCode>' ;
$ret	.=	'<StatusText>' . $statusText . '</StatusText>' ;
$ret	.=	'</Status>' ;


$ret	.=	'</Reply>' ;

error_log( $ret) ;
print( $ret) ;

?>
