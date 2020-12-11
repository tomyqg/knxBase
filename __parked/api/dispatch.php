<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
FDbg::begin( 1, "dispatch.php", "*", "*", "(behind require_once(...))") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;
$statusInfo	=	"ok" ;

header("Content-type: text/xml");
$ret	=	"<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n" ;
$ret	.=	"<Reply>\n" ;

$interimRet	=	"<Request>\n" ;
$interimRet	.=	"<RequestObj>" . $_GET['_obj'] . "</RequestObj>\n" ;
$interimRet	.=	"<RequestFnc>" . $_GET['_fnc'] . "</RequestFnc>\n" ;
$interimRet	.=	"<RequestKey>" . $_GET['_key'] . "</RequestKey>\n" ;
$interimRet	.=	"<RequestId>" . $_GET['_id'] . "</RequestId>\n" ;
$interimRet	.=	"<RequestVal>" . $_GET['_val'] . "</RequestVal>\n" ;
$interimRet	.=	"</Request>\n" ;
FDbg::traceData( 2, FDbg::mdTrcInfo1, "index.php", "*", "*", "request data", $interimRet) ;
$ret	.=	$interimRet ;
$ret	.=	"<DebugData>\n" ;
$ret	.=	EISSCoreObject::getGETAsXML() ;
$ret	.=	EISSCoreObject::getPOSTAsXML() ;
$ret	.=	"</DebugData>\n" ;

$ret	.=	"<Data>\n" ;
try {
	EISSCoreObject::dumpPOST() ;
//	EISSCoreObject::dumpGET() ;
//	EISSCoreObject::dumpFILE() ;
	$_obj	=	$_GET['_obj'] ;
	$_fnc	=	$_GET['_fnc'] ;
	$_key	=	$_GET['_key'] ;
	$_id	=	$_GET['_id'] ;
	$_val	=	$_GET['_val'] ;
	require_once( $_obj . ".php") ;
//	if ( $_key == "static") {
//		error_log( "ERP_0.0.2: Calling static object function $_fnc( $_key, $_id, $_val)") ;
//		$ret	=	$_obj::$_fnc( $_key, $_id, $_val) ;
//	} else if ( $_key == "halfstatic") {
	if ( $_key == "halfstatic") {
		error_log( "ERP_0.0.2: Calling half-static object function $_fnc( $_key, $_id, $_val)") ;
		$myObj	=	new $_obj ;
		$ret	.=	$myObj->$_fnc( $_key, $_id, $_val) ;
	} else {
		$myObj	=	new $_obj ;
		if ( $_fnc == "createFromEKPreisRId") {
		} else if ( strlen( $_key) > 0) {
			error_log("dispatch: fetching by key[$_key]") ;
			$myObj->setKey( $_key) ;
		} else if ( strlen( $_id) > 0) {
			error_log("dispatch: fetching by id[$_id]") ;
			$myObj->setId( $_id) ;
		}
		if ( $myObj->_valid) {
			error_log("ERP_0.0.2: Calling object function $_fnc( $_key, $_id, $_val)") ;
			$ret	.=	$myObj->$_fnc( $_key, $_id, $_val) ;
		} else if ( $_fnc == "add" || $_fnc == "createPDF" || $_key == "") {
			error_log("ERP_0.0.2: Calling object function $_fnc( $_key, $_id, $_val)") ;
			$ret	.=	$myObj->$_fnc( $_key, $_id, $_val) ;
		} else {
			$statusCode	=	-997 ;
			$statusText	=	"function " . $_fnc . ", object invalid key:= " . $_key . ", id:=" . $_id ;
			error_log("ERP_0.0.2: " . $statusText) ;
		}
	}
	if ( isset( $myObj->StatusInfo)) {
		if ( $myObj->StatusInfo != "" && $statusCode == 0) {
			$statusInfo	=	$myObj->StatusInfo ;
		}
	}
} catch ( Exception $e) {
	$statusCode	=	-999 ;
	$statusText	=	"ERP_0.0.2: exception '".$e->getMessage()."'" ;
	error_log("ERP_0.0.2: " . $statusText) ;
}
$ret	.=	"</Data>\n" ;

$ret	.=	"<Status>\n" ;
$ret	.=	"<StatusCode>" . $statusCode . "</StatusCode>\n" ;
$ret	.=	"<StatusText>" . $statusText . "</StatusText>\n" ;
if ( $statusInfo == "")		$statusInfo	=	"ok" ;
$ret	.=	"<StatusInfo>" . str_replace( "\n", "<br/>", $statusInfo) . "</StatusInfo>\n" ;
$ret	.=	"</Status>\n" ;

$ret	.=	"</Reply>\n" ;
FDbg::traceData( 2, FDbg::mdTrcInfo1, "dispatch.php", "*", "*", "reply data", $ret) ;

FDbg::end( 1, "index.php", "*", "*", "") ;

print( $ret) ;

?>
