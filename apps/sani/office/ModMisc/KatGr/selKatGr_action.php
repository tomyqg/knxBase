<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "pkgs/platform/FDb.php") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;

header("Content-type: text/xml");
$ret	=	'<?xml version="1.0" encoding="utf-8" ?>' ;
$ret	.=	'<Reply>' ;

switch ( $_GET['_fnc']) {

case	"refSelKatGr"	:

	$ret	.=	'<Data>' ;
	try {

		$_suchKrit	=	$_POST['_SKatGrNr'] ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT KatGr.Id AS Id, KatGr.KatGrNr AS KatGrNr, KatGr.KatGrName AS KatGrName " ;
		$query	.=	"FROM KatGr AS KatGr " ;
//		$query	.=	"LEFT JOIN Lief AS L ON L.LiefNr = KatGr.LiefNr " ;
//		$query	.=	"LEFT JOIN LiefKontakt AS LK ON LK.LiefNr = KatGr.LiefNr AND LK.LiefKontaktNr = KatGr.LiefKontaktNr " ;
		$query	.=	"WHERE ( KatGr.KatGrNr like '%" . $_suchKrit . "%') " ;
		$query	.=	"ORDER BY KatGr.KatGrNr " ;

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
		$ret	.=	'<KatGrListe>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<KatGr>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . "><![CDATA[" . $val . "]]></" . $ndx . ">\n" ;
			}
			$ret	.=	"</KatGr>\n" ;
		}

		$ret	.=	"</KatGrListe>\n" ;

	} catch ( Exception $e) {

		$statusCode	=	-1 ;
		$statusText	=	"exception [" . $e->getMessage() . "]" ;

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
