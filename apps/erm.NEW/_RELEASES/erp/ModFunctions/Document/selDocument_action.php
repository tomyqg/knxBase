<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "pkgs/platform/FDb.php") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;

header("Content-type: text/xml");
$ret	=	'<?xml version="1.0" encoding="utf-8" ?>' ;
$ret	.=	'<Reply>' ;

switch ( $_GET['_fnc']) {

case	"refSelDocument"	:

	$ret	.=	'<Data>' ;
	try {

		$_RefTypeCrit	=	$_POST['_SRefType'] ;
		$_RefNrCrit	=	$_POST['_SRefNr'] ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT A.Id AS Id, A.RefType AS RefType, A.RefNr AS RefNr, A.Filename AS Filename " ;
		$query	.=	"FROM Document AS A " ;
//		$query	.=	"WHERE ( A.DocumentNr like '" . $_DocumentNrCrit . "%' or A.DocumentBez1 like '%" . $_DocumentNrCrit . "%' ) " ;
		$query	.=	"WHERE ( A.RefType like '%" . $_RefTypeCrit . "%' ) " ;
		$query	.=	"  AND ( A.RefNr like '%" . $_RefNrCrit . "%' ) " ;
		error_log( $query) ;

//		if ( strlen( $_orderBy1) > 0) {
//			$query	.=	"order by " . $_orderBy1 . " " ;
//			$query	.=	$_orderBy1Dir . " " ;
//			if ( strlen( $_orderBy2) > 0) {
//				$query	.=	", " . $_orderBy2 . " " ;
//				$query	.=	$_orderBy2Dir . " " ;
//			}
//		}
		$query	.=	"LIMIT " . $_dbStartRow . ", 10 " ;

		$sqlResult	=	FDb::query( $query) ;

		header("Content-type: text/xml");
		$ret	.=	'<TableDocument>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<Document>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . "><![CDATA[" . $val . "]]></" . $ndx . ">\n" ;
			}
			$ret	.=	"</Document>\n" ;
		}

		$ret	.=	"</TableDocument>\n" ;

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
