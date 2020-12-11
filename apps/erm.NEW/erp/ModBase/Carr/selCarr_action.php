<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;

header("Content-type: text/xml");
$ret	=	'<?xml version="1.0" encoding="utf-8" ?>' ;
$ret	.=	'<Reply>' ;

error_log( "Here I am") ;

switch ( $_GET['_fnc']) {

case	"refSelCarr"	:

	$ret	.=	'<Data>' ;
	try {

		$_carrNrCrit	=	$_POST['_SCarrier'] ;
		$_nameCrit	=	$_POST['_SName'] ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT C.Id AS CId, C.Carrier AS Carrier, C.CarrName AS CarrName, C.FullName AS Fullname " ;
		$query	.=	"FROM Carr AS C " ;
		$query	.=	"WHERE ( A.Carrier like '%" . $_carrNrCrit . "%' ) " ;
		$query	.=	"  AND ( A.FullName like '%" . $_nameCrit . "%') " ;
		$query	.=	"ORDER BY C.Carrier " ;
		$query	.=	"LIMIT " . $_dbStartRow . ", 10 " ;
		FDbg::dumpL( 0x00000001, "selCarr_action.php:: " . $query) ;
		$sqlResult	=	FDb::query( $query) ;
		header("Content-type: text/xml");
		$ret	.=	'<TableCarr>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<Carr>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . "><![CDATA[" . $val . "]]></" . $ndx . ">\n" ;
			}
			$ret	.=	"</Carr>\n" ;
		}

		$ret	.=	"</TableCarr>\n" ;

	} catch ( Exception $e) {
		$statusCode	=	-1 ;
		$statusText	=	$e ;
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
FDbg::dumpL( 0x00000001, "$ret") ;
print( $ret) ;

?>
