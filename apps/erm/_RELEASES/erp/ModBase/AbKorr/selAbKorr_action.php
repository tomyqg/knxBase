<?php
require_once( "config.inc.php") ;
require_once( "pkgs/platform/FDb.php") ;

/**
 * these are variable definitions, therefor need to be loaded after everything
 * else has been loaded
 */
require_once( "config.inc.php") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;

header("Content-type: text/xml");
$ret	=	'<?xml version="1.0" encoding="utf-8" ?>' ;
$ret	.=	'<Reply>' ;

switch ( $_GET['_fnc']) {

case	"refSelAbKorr"	:

	$ret	.=	'<Data>' ;
	try {

		$_suchKrit	=	$_POST['_SAbKorrNr'] ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT AbK.Id AS Id, AbK.AbKorrNr AS AbKorrNr, AbK.Datum AS Datum, AbK.Description AS Description " ;
		$query	.=	"FROM AbKorr AS AbK " ;
		$query	.=	"WHERE ( " ;
		$query	.=	"AbK.AbKorrNr like '%" . $_suchKrit . "%' " ;
		$query	.=	") " ;
		$query	.=	"ORDER BY AbK.AbKorrNr DESC " ;
		$query	.=	"LIMIT " . $_dbStartRow . ", 10 " ;

		$sqlResult	=	FDb::query( $query) ;

		header("Content-type: text/xml");
		$ret	.=	'<AbKorrListe>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<AbKorr>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . "><![CDATA[" . $val . "]]></" . $ndx . ">\n" ;
			}
			$ret	.=	"</AbKorr>\n" ;
		}

		$ret	.=	"</AbKorrListe>\n" ;

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
