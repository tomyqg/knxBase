<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "pkgs/platform/FDb.php") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;

header("Content-type: text/xml");
$ret	=	'<?xml version="1.0" encoding="utf-8" ?>' ;
$ret	.=	'<Reply>' ;

switch ( $_GET['_fnc']) {

case	"refSelTask"	:

	$ret	.=	'<Data>' ;
	try {

		$_userIdCrit	=	$_POST['_STaskNr'] ;
		$_nameCrit	=	$_POST['_SRspUserId'] ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT T.TaskNr AS TaskNr, T.RspUserId, T.DateReg AS DateReg, T.DateEsc " ;
		$query	.=	"FROM Task AS T " ;
		$query	.=	"WHERE ( T.RspUserId like '%" . $_nameCrit . "%' ) " ;
		$query	.=	"ORDER BY T.DateReg ASC " ;

//		if ( strlen( $_orderBy1) > 0) {
//			$query	.=	"order by " . $_orderBy1 . " " ;
//			$query	.=	$_orderBy1Dir . " " ;
//			if ( strlen( $_orderBy2) > 0) {
//				$query	.=	", " . $_orderBy2 . " " ;
//				$query	.=	$_orderBy2Dir . " " ;
//			}
//		}
		$query	.=	"LIMIT " . $_dbStartRow . ", 10 " ;
error_log( $query) ;

		$sqlResult	=	FDb::query( $query) ;

		header("Content-type: text/xml");
		$ret	.=	'<Tasks>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<Task>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . ">" . iconv( "ISO-8859-1", "UTF-8", $val) . "</" . $ndx . ">\n" ;
			}
			$ret	.=	"</Task>\n" ;
		}

		$ret	.=	"</Tasks>\n" ;

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
