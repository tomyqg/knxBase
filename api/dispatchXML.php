<?php
/**
 * Copyright (c) 2015-2018 wimtecc, Karl-Heinz Welter
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 * Protocoll Specification
 *
 * Revision History
 *
 * Date			Ver		User	Comment
 * 2014/03/13	PA1		khw		initial clean-up
 * 2014/07/01	PA2		khw		protocol commented
 *
 * The basic request from the client looks as follows:
 *
 *	-	dispatchXML.php?_obj=<class>&_fnc=<function>&_key=<key>&_id=<id>&_val=<value> or
 *	-	dispatchXML.php?_obj=<class>&_fnc=<function>&_key0=<key0>[&_key1=<key1>[&_key2=<key2>[&_key3=<key3>]]]&_id=<id>&_val=<value>
 *
 * The call to class->function needs to return a Reply object.
 *
 * Depending on the type of the reply-object, this can be either of:
 * 	-	text/xml or
 * 	-	application/pdf
 * the reply will be send as either XML data (text/xml) or specific (application/pdf) data.
 *
 * The following requests are part of the basic object implementation
 * 	-	getAsXML				get the data of a specified object
 * 	-	getPrevAsXML
 * 	-	getNextAsXML
 * 	-	add, upd, del			add, update and delete an instance
 * 	-	addDep, updDep, delDep	add, update and delete a dependent instance
 * 	-	getDepAsXML				get a single dependent object
 * 	-	getTableDepAsXML		get a table of dependent objects
 * 	-	getList					get a list of matching objects
 * 	-	getListAsXML			get a list of matching objects with joined information
 * 								from other tables (e.g. for a detailed selection list)
 *
 * The structure of the XML response has the following format:
 *
 *	<Reply>
 * 		debugmessage
 * 		<Status>
 * 			<StatusCode>___</StatusCode>
 * 			<StatusText>___</StatusText>
 * 			<StatusInfo>___</StatusInfo>
 * 			<InstantiatedClass>-BASE-CLASS-</InstantiatedClass>
 * 			<ReplyingCLass>-CLASS-</ReplyingCLass>
 * 		</Status>
 * 		<Data>
 * 			data-section
 *		</Data>
 *	</Reply>
 *
 * Status codes have the following meaning:
 *    0	=	everything ok
 * -999	=	Exception during execution,
 * 				<StatusInfo> will contain the exception message
 * -998	=	Invalid key. An object with the given key could not be found
 *				<StatusInfo> will contain class/object specific information or a general message
 * All other Status codes have class/object specific meaning and shall be > 0.
 * Status codes < 0 are reserved for platform usage.
 *
 * The data-section can have various formats, ultimately determined only by the implementation of the
 * individual class which is refernces by the call. The following formats are considered standard formats:
 *
 *	<_class_name_>
 * 		<_attribute_name>attribute value</_attribute_name>
 * 		<_attribute_name>attribute value</_attribute_name>
 * 		...
 * 		<_attribute_name>attribute value</_attribute_name>
 *	</_class_name>
 *
 *	<Table_class_name>
 *		<TableInfo>
 *			<StartRow>index of the starting row in the result set</StartRow>
 *			<RowCount>number of requested rows<</RowCount>
 *			<PageCound>page count</PageCount>
 *			<TotalRows>total number of matching rows</TotalRows>
 *		</TableInfo>
 *		<_class_name_>
 * 			<_attribute_name>attribute value</_attribute_name>
 * 			<_attribute_name>attribute value</_attribute_name>
 * 			...
 * 			<_attribute_name>attribute value</_attribute_name>
 *		</_class_name>
 *		<_class_name_>
 * 			...
 *		</_class_name>
 *	</Table_class_name>
 *
 */
$rp	=	"" ;
$rp	=	"/mas" ;
require_once( $_SERVER["DOCUMENT_ROOT"].$rp."/Config/config.inc.php") ;
//FDbg::setApp( "dispatchXML.php::{$_REQUEST['_obj']}") ;
//FDbg::setAppToTrace( "dispatchXML.php::{$_REQUEST['_obj']}") ;
//FDbg::begin( 0, "dispatchXML.php", "*", "main()") ;
//FDbg::trace( 0, FDbg::mdTrcInfo1, "dispatchXML.php", "*", "main()", "session Validity := ".$mySysSession->Validity) ;
//FDbg::traceData( 2, FDbg::mdTrcInfo1, "dispatchXML.php", "*", "*", "request uri", $_SERVER['REQUEST_URI']) ;
if ( $mySysSession->Validity != SysSession::INVAULOFF) {


	if ( isset( $HTTP_RAW_POST_DATA))
		FDbg::traceData( 2, FDbg::mdTrcInfo1, "dispatchXML.php", "*", "*", "request raw_post", $HTTP_RAW_POST_DATA) ;
	if ( $mySysSession->Validity > SysSession::INVALID) {
		try {
			$_obj	=	$_REQUEST['_obj'] ;
			$_fnc	=	$_REQUEST['_fnc'] ;
			if ( isset( $_REQUEST['_key'])) {
				$_key	=	$_REQUEST['_key'] ;
			} else if ( isset( $_REQUEST['_key0'])) {
				$_key	=	array() ;
				$_key[0]	=	$_REQUEST['_key0'] ;
				if ( isset( $_REQUEST['_key1'])) {
					$_key[1]	=	$_REQUEST['_key1'] ;
					if ( isset( $_REQUEST['_key2'])) {
						$_key[2]	=	$_REQUEST['_key2'] ;
						if ( isset( $_REQUEST['_key3'])) {
							$_key[3]	=	$_REQUEST['_key3'] ;
						}
					}
				}
			}
			$_id	=	$_REQUEST['_id'] ;
			$_val	=	$_REQUEST['_val'] ;
			try {
				$myObj	=	new $_obj();
			} catch ( Exception $e) {
//				$myObj	=	new AppObject( $_obj, "Id") ;
			}
			if ( is_array( $_key)) {
				FDbg::trace( 0, FDbg::mdTrcInfo1, "dispatchXML.php", "*", "*", "choosing multiple column-key") ;
				$myObj->setComplexKey( $_key) ;
				if ( $myObj->_valid) {
					$ret	=	$myObj->$_fnc( $_key, $_id, $_val) ;
				} else {
					$ret	=	new Reply() ;
					$ret->replyStatus	=	$myObj->_status ;
					$ret->replyStatusText	=	"function " . $_fnc . ", object invalid key:= " . print_r( $_key, true) . ", id:=" . $_id ;
					if ( isset( $myObj->_statusInfo))
						$ret->replyStatusInfo	=	$myObj->_statusInfo ;
					else
						$ret->replyStatusInfo	=	"no information on object error available" ;
				}
			} else {
				FDbg::trace( 0, FDbg::mdTrcInfo1, "dispatchXML.php", "*", "*", "choosing single column-key") ;
				if ( strlen( $_key) > 0) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, "dispatchXML.php", "*", "*", "key := '$_key'") ;
					$myObj->setKey( $_key) ;
				} else if ( intval( $_id) > 0) {
					$myObj->setId( $_id) ;
				} else {
					$myObj->_valid	=	false ;
				}
				/**
				 *	IF a valid object could be instantiated
				 *		apply the required function
				 *	ELSE
				 *		IF there's a function which does not provide reference to a valid object
				 *			apply the required function
				 *		ELSE
				 *			issue an error message
				 *		ENDIF
				 *	ENDIF
				 */
				if ( $myObj->_valid) {
					$ret	=	$myObj->$_fnc( $_key, $_id, $_val) ;
					SysSession::$data["LastObject"]		=	$_REQUEST['_obj'] ;
					SysSession::$data["LastObjectFnc"]	=	$_REQUEST['_fnc'] ;
					SysSession::$data["LastObjectKey"]	=	$_REQUEST['_key'] ;
					SysSession::$data["LastObjectId"]	=	$_REQUEST['_id'] ;
					SysSession::$data["LastObjectVal"]	=	$_REQUEST['_val'] ;
				} else {
					/**
					 * the following functions can be performed without having a valid object before
					 */
					switch ( $_fnc) {
					case	"acList"	:			// create a new object without any values
					case	"create"	:			// create a new object without any values
					case	"add"		:			// create a new object with given key(s)
					case 	"new"		:			// create a new object and automatically assign key(s)
					case	"getPrev"	:
					case	"getNext"	:
					case	"getAsXML"	:
					case	"getPrevAsXML"	:
					case	"getNextAsXML"	:
					case	"getLastAsXML"	:
					case	"getListAsXML"	:
					case	"getList"	:
					case	"getSPList"	:
					case	"importXML"	:
					case	"uploadData"	:
					case	"evaluate"	:
						$ret	=	$myObj->$_fnc( $_key, $_id, $_val) ;
						break ;
					default	:
						$ret	=	$myObj->$_fnc( $_key, $_id, $_val) ;
						// $ret	=	new Reply() ;
						// $ret->replyStatus	=	-998 ;
						// $ret->replyStatusText	=	"function " . $_fnc . ", object invalid key:= " . $_key . ", id:=" . $_id ;
						// if ( isset( $myObj->_statusInfo))
						// 	$ret->replyStatusInfo	=	$myObj->_statusInfo ;
						// else
						// 	$ret->replyStatusInfo	=	"no information on object error available" ;
						break ;
					}
				}
			}
		} catch ( Exception $e) {
			error_log( "------------------------------------------------------") ;
			$ret	=	new Reply() ;
			if ( isset( $e->ec1)) {
				$ret->replyStatus	=	$e->ec1 ;
			} else {
				$ret->replyStatus	=	-999 ;
			}
			$ret->replyStatusText	=	"dispatchXML.php: exception" ;
			$ret->replyStatusInfo	=	$e->getMessage() ;
		}
	} else {
		$ret	=	new Reply() ;
		$ret->replyStatus	=	-899 ;
		$ret->replyStatusText	=	"dispatchXML.php: tempered session" ;
		$ret->replyStatusInfo	=	"" ;
	}
	$mySysSession->save() ;
	/**
	 *
	 */
	$ret->replyDebugMessage	=	getHTTPVars() ;
	switch ( $ret->replyMediaType) {
		case	Reply::mediaTextPlain	:
			header("Content-type: text/plain");
			$reply	=	"" ;
			$reply	.=	$ret ;
			readfile( $ret->fullTXTName) ;
			break ;
		case	Reply::mediaTextCSV	:
		case	Reply::mediaTextCSV	:
			header("Content-type: ".$ret->replyMediaType);
			$reply	=	"" ;
			$reply	.=	$ret->getReply() ;
			readfile( $ret->fullTXTName) ;
			break ;
		case	Reply::mediaTextXML	:
			header("Content-type: text/xml");
			$reply	=	"" ;
			$reply	.=	"<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n" ;
			$reply	.=	$ret ;
			print( $reply) ;
			break ;
		case	Reply::mediaAppPDF	:
			header( "content-type: application/pdf");
			header( "Content-Disposition: attachment; filename=".$ret->pdfName."");
			header( "Content-Length: ".filesize( $ret->fullPDFName)."");
			header( "Cache-Control: no-cache, must-revalidate") ;
			header( "Expires: -1") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')", "PDF.....: " . $ret->pdfName) ;
			readfile( $ret->fullPDFName) ;
			break ;
		case	Reply::mediaImgPng	:
			header( "content-type: image/png");
			imagepng( $ret->gdImage) ;
			imagedestroy( $ret->gdImage) ;
			break ;
		case	Reply::mediaImgJpg	:
			header( "content-type: image/jpg");
			imagejpeg( $ret->gdImage) ;
			imagedestroy( $ret->gdImage) ;
			break ;
	}
	$ret->dump() ;
	error_log( print_r( $ret, true)) ;
} else {
	$ret	=	new Reply() ;
	$ret->replyStatus	=	-9 ;
	$ret->replyStatusText	=	"Session invalid due to inactivity" ;
	$ret->replyStatusInfo	=	"---" ;
	$ret->targetURL	=	"/login.html" ;
	/**
	 *
	 */
	header("Content-type: text/xml");
	$reply	=	"" ;
	$reply	.=	"<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n" ;
	$reply	.=	$ret->getReply() ;
	print( $reply) ;
}
FDbg::end( 0, "hdlObjectXML.php", "*", "main()") ;
/**
 *
 * @return string
 */
function	getRequest() {
	$interimRet	=	"<Request>\n" ;
	$interimRet	.=	"<RequestObj>" . $_REQUEST['_obj'] . "</RequestObj>\n" ;
	$interimRet	.=	"<RequestFnc>" . $_REQUEST['_fnc'] . "</RequestFnc>\n" ;
	if ( isset( $_REQUEST['_key'])) {
		$interimRet	.=	"<RequestKey>" . $_REQUEST['_key'] . "</RequestKey>\n" ;
	} else {
		$il0	=	0 ;
		while ( isset( $_REQUEST['_key'.$il0])) {
			$interimRet	.=	"<RequestKey".$il0.">" . $_REQUEST['_key'.$il0] . "</RequestKey".$il0.">\n" ;
			$il0++ ;
		}
	}
	$interimRet	.=	"<RequestId>" . $_REQUEST['_id'] . "</RequestId>\n" ;
	$interimRet	.=	"<RequestVal>" . $_REQUEST['_val'] . "</RequestVal>\n" ;
	$interimRet	.=	"</Request>\n" ;
	return $interimRet ;
}

function	getHTTPVars() {
	$interimRet	=	"<DebugData>\n" ;
	$interimRet	.=	EISSCoreObject::getGETAsXML() ;
	$interimRet	.=	EISSCoreObject::getPOSTAsXML() ;
	$interimRet	.=	"</DebugData>\n" ;
	return $interimRet ;
}

?>
