<?php

require_once( "config.inc.php") ;
require_once( "pkgs/platform/FDb.php") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;

header("Content-type: text/xml");
$ret	=	'<?xml version="1.0" encoding="utf-8" ?>' ;
$ret	.=	'<Reply>' ;

switch ( $_GET['_fnc']) {

case	"refSelCuRMA"	:

	$ret	.=	'<Data>' ;
	try {

		$_suchKrit	=	$_POST['_SCuRMANr'] ;
		$_sStatus	=	intval( $_POST['_SStatus']) ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT KdB.Id AS Id, KdB.CuRMANr AS CuRMANr, KdB.Datum AS Datum, KdB.Status AS Status, K.FirmaName1 AS FirmaName1, K.FirmaName2 AS FirmaName2, K.PLZ AS PLZ, KK.Vorname AS Vorname, KK.Name AS Name " ;
		$query	.=	"FROM CuRMA AS KdB " ;
		$query	.=	"LEFT JOIN Kunde AS K ON K.KundeNr = KdB.KundeNr " ;
		$query	.=	"LEFT JOIN KundeKontakt AS KK ON KK.KundeNr = KdB.KundeNr AND KK.KundeKontaktNr = KdB.KundeKontaktNr " ;
		$query	.=	"WHERE ( " ;
		$query	.=	"KdB.CuRMANr like '%" . $_suchKrit . "%' " ;
		if ( $_POST['_SCompany'] != "")
			$query	.=	"  AND ( FirmaName1 like '%" . $_POST['_SCompany'] . "%' OR FirmaName2 LIKE '%" . $_POST['_SCompany'] . "%') " ;
		if ( $_POST['_SZIP'] != "")
			$query	.=	"  AND ( PLZ like '%" . $_POST['_SZIP'] . "%' ) " ;
		if ( $_POST['_SContact'] != "")
			$query	.=	"  AND ( Name like '%" . $_POST['_SContact'] . "%' OR Vorname LIKE '%" . $_POST['_SContact'] . "%') " ;
		if ( $_sStatus != -1) {
			$query	.=	"AND ( KdB.Status = " . $_sStatus . ") " ;
		}
		$query	.=	") " ;
		$query	.=	"ORDER BY CuRMANr DESC " ;
		
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
		$ret	.=	'<CuRMAn>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<CuRMA>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . "><![CDATA[" . $val . "]]></" . $ndx . ">\n" ;
			}
			$ret	.=	"</CuRMA>\n" ;
		}

		$ret	.=	"</CuRMAn>\n" ;

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

print( $ret) ;

?>
