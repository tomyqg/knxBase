<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "pkgs/platform/FDb.php") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;

header("Content-type: text/xml");
$ret	=	'<?xml version="1.0" encoding="utf-8" ?>' ;
$ret	.=	'<Reply>' ;

switch ( $_GET['_fnc']) {

case	"refSelInKonf"	:

	$ret	.=	'<Data>' ;
	try {

		$_suchKrit	=	$_POST['_SInKonfNr'] ;
		$_descrKrit	=	$_POST['_SDescr'] ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT Ik.Id AS Id, Ik.InKonfNr AS InKonfNr, Ik.Datum AS Datum, Ik.ArtikelNr AS ArtikelNr " ;
		$query	.=	"FROM InKonf AS Ik " ;
		$query	.=	"WHERE ( Ik.InKonfNr like '%" . $_suchKrit . "%' ) " ;

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
		$ret	.=	'<InKonfn>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<InKonf>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . ">" . $val . "</" . $ndx . ">\n" ;
			}
			$ret	.=	"</InKonf>\n" ;
		}

		$ret	.=	"</InKonfn>\n" ;

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
