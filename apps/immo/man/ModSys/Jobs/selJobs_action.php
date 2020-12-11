<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "pkgs/platform/FDb.php") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;

header("Content-type: text/xml");
$ret	=	'<?xml version="1.0" encoding="utf-8" ?>' ;
$ret	.=	'<Reply>' ;

switch ( $_GET['_fnc']) {

case	"refSelJobs"	:

	$ret	.=	'<Data>' ;
	try {

		$_userIdCrit	=	$_POST['_SJobsId'] ;
		$_nameCrit	=	$_POST['_SJobName'] ;
		$_dbStartRow	=	$_POST['_SStartRow'] ;

		$query	=	"SELECT J.Id AS Id, J.JobName AS JobName, J.Schedule, J.Script " ;
		$query	.=	"FROM Jobs AS J " ;
		$query	.=	"WHERE ( J.JobName like '%" . $_nameCrit . "%' ) " ;
		$query	.=	"ORDER BY J.JobName " ;

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
		$ret	.=	'<Jobss>' ;
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$ret	.=	"<Jobs>\n" ;
			foreach( $row as $ndx => $val) {
				$ret	.=	"<" . $ndx . ">" . iconv( "ISO-8859-1", "UTF-8", $val) . "</" . $ndx . ">\n" ;
			}
			$ret	.=	"</Jobs>\n" ;
		}

		$ret	.=	"</Jobss>\n" ;

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
