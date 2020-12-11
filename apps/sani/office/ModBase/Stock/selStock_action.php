<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
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

case	"refSelStock"	:

	$ret	.=	'<Data>' ;
	try {

		$_suchKrit	=	$_POST['_SStockId'] ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT St.Id AS Id, St.StockId AS StockId " ;
		$query	.=	"FROM Stock AS St " ;
		$query	.=	"WHERE ( " ;
		$query	.=	"St.StockId like '%" . $_suchKrit . "%' " ;
		$query	.=	") " ;
		$query	.=	"ORDER BY St.StockId DESC " ;
		$query	.=	"LIMIT " . $_dbStartRow . ", 10 " ;

		$sqlResult	=	FDb::query( $query) ;

		header("Content-type: text/xml");
		$ret	.=	'<StockListe>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<Stock>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . "><![CDATA[" . $val . "]]></" . $ndx . ">\n" ;
			}
			$ret	.=	"</Stock>\n" ;
		}

		$ret	.=	"</StockListe>\n" ;

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
