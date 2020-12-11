<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../../Config/config.inc.php") ;

error_log( "dispatchPDF: dispatchPDF.php: begin") ;

$statusCode	=	0 ;
$statusText	=	"ok" ;
$statusInfo	=	"no info" ;

/**
 * prepare the answer in case of error
 */
/**
 * 
 */
try {
//	EISSCoreObject::dumpPOST() ;
//	EISSCoreObject::dumpGET() ;
//	EISSCoreObject::dumpFILE() ;
	$_obj	=	$_GET['_obj'] ;
	$_fnc	=	$_GET['_fnc'] ;
	$_key	=	$_GET['_key'] ;
	$_id	=	$_GET['_id'] ;
	$_val	=	$_GET['_val'] ;
	require_once( $_obj . ".php") ;
	$myObj	=	new $_obj ;
	if ( strlen( $_key) > 0) {
		error_log("dispatchPDF: fetching by key[$_key]") ;
		$myObj->setKey( $_key) ;
	} else if ( strlen( $_id) > 0) {
		error_log("dispatchPDF: fetching by id[$_id]") ;
		$myObj->setId( $_id) ;
	}
	if ( $myObj->_valid) {
		error_log( "dispatchPDF: Calling object function $_fnc( $_key, $_id, $_val)") ;
		$statusCode	=	0 ;
		$ret	=	$myObj->$_fnc( $_key, $_id, $_val) ;
		header( "content-type: application/pdf");
		header( "Content-Disposition: attachment; filename=".$myObj->pdfName."");
		header( "Content-Length: ".filesize( $myObj->fullPDFName)."");
		header( "Cache-Control: no-cache, must-revalidate") ;
		header( "Expires: -1") ;
		readfile( $myObj->fullPDFName) ;
	} else {
		error_log( "dispatchPDF: object not valid") ;
		$statusText	=	"function " . $_fnc . ", object invalid key:= " . $_key . ", id:=" . $_id ;
		$statusCode	=	-998 ;				// object not found
	}
} catch ( Exception $e) {
	error_log( "dispatchPDF: " . $statusText) ;
	$statusCode	=	-999 ;
	$statusText	=	"exception " . $e->getMessage() ;
}
/**
 * 
 */
if ( $statusCode != 0) {
	$ret	=	"" ;
	$ret	.=	"<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n" ;
	$ret	.=	"<Reply>\n" ;
	/**
	 * repeat the request
	 */
	$interimRet	=	"<Request>\n" ;
	$interimRet	.=	"<RequestObj>" . $_GET['_obj'] . "</RequestObj>\n" ;
	$interimRet	.=	"<RequestFnc>" . $_GET['_fnc'] . "</RequestFnc>\n" ;
	if ( isset( $_GET['_key'])) {
		$interimRet	.=	"<RequestKey>" . $_GET['_key'] . "</RequestKey>\n" ;
	} else {
		$il0	=	0 ;
		while ( isset( $_GET['_key'.$il0])) {
			$interimRet	.=	"<RequestKey".$il0.">" . $_GET['_key'.$il0] . "</RequestKey".$il0.">\n" ;
			$il0++ ;
		}
	}
	$interimRet	.=	"<RequestId>" . $_GET['_id'] . "</RequestId>\n" ;
	$interimRet	.=	"<RequestVal>" . $_GET['_val'] . "</RequestVal>\n" ;
	$interimRet	.=	"</Request>\n" ;
	/**
	 * 
	 */
	$ret	.=	$interimRet ;
	$interimRet	=	"<DebugData>\n" ;
	$interimRet	.=	EISSCoreObject::getGETAsXML() ;
	$interimRet	.=	EISSCoreObject::getPOSTAsXML() ;
	$interimRet	.=	"</DebugData>\n" ;
	$ret	.=	$interimRet ;
	/**
	 *
	 */
	$ret	.=	"<Status>\n" ;
	$ret	.=	"<StatusCode>" . $statusCode . "</StatusCode>\n" ;
	$ret	.=	"<StatusText>" . $statusText . "</StatusText>\n" ;
	if ( $statusInfo == "")
		$statusInfo	=	"error" ;
	$ret	.=	"<StatusInfo>" . str_replace( "\n", "<br/>", $statusInfo) . "</StatusInfo>\n" ;
	$ret	.=	"</Status>\n" ;
	$ret	.=	"</Reply>\n" ;
	header("Content-type: text/xml");
	print( $ret) ;
	
}
error_log( "dispatchPDF: dispatchPDF.php: end") ;

?>
