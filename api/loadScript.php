<?php
/**
 * Copyright (c) 2015, 2016, 2017 wimtecc, Karl-Heinz Welter
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
 * loadScript.php
 * ==============
 *
 * Load a (Java)script file from the server.
 *
 * Revision History
 *
 * Date		Ver	User	Comment
 * 2015/02/16	PA1	khw	initial version
 * 2015/03/02	PA2	khw	comments added
 *
 * The basic request from the client looks as follows:
 *
 *	-	loadScript.php?sessionId=<sessionId>&script=<scriptName>
 *
 * Only if the sessionId points to a valid session the script will be executed at all. Otherwise an error message
 * is returned. It's up to the borwser (programming) to react accordingly.
 *
 * The loader checks in the standard api folder and afterwards in the application root folder for the script.
 *
 * The advantage of using this script, in comparison to loading through the "normal" webserver is that no data - except an
 * error message - is returned if no valid credentials are supplied.
 */
$debugBoot	=	false ;
require_once( $_SERVER["DOCUMENT_ROOT"]."/mas/Config/config.inc.php") ;
FDbg::setApp( "loadScript.php") ;
FDbg::setLevel( 9) ;
//FDbg::enable() ;
//if ( $mySysSession->Validity > SysSession::INVALID) {
$referer	=	"" ;
if ( isset( $_SERVER['HTTP_REFERER']))
	$referer	=	$_SERVER['HTTP_REFERER'] ;
error_log( "loadScript------------------------------__>>>>>>>>>>>>>>>>>>" . $referer) ;
error_log( "loadScript------------------------------__>>>>>>>>>>>>>>>>>>" . $mySysConfig->server->allowedHost) ;
error_log( "loadScript------------------------------__>>>>>>>>>>>>>>>>>>" . $_GET["script"]) ;
if ( strpos( $referer, $mySysConfig->server->allowedHost) !== false) {
	$mySysUser	=	$mySysSession->SysUser ;
	/**
	 *	IF file exists in client space
	 *		load it
	 *	ELSE
	 * 		IF file exists in global space
	 * 			load it
	 * 		ELSE
	 * 			return error and bail out
	 * 		ENDIF
	 *	ENDIF
	 */
	$done	=	false ;
	if ( ! $done) {
		$scriptFile	=	"../api/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../clients/".$mySysSession->ClientId."/apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../clients/".$mySysSession->ClientId."/apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/rsrc/scripts/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../clients/".$mySysSession->ClientId."/apps/".$mySysSession->ApplicationSystemId."/rsrc/scripts/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../clients/".$mySysSession->ClientId."/apps/rsrc/scripts/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../clients/".$mySysSession->ClientId."/rsrc/scripts/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/rsrc/scripts/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../apps/".$mySysSession->ApplicationSystemId."/rsrc/scripts/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../apps/rsrc/scripts/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../rsrc/scripts/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$scriptFile	=	"../apps/_base/".$_GET["script"] ;
		if ( file_exists( $scriptFile)) {
			$file	=	fopen( $scriptFile, "r") ;
			$scriptText	=	fread( $file, filesize( $scriptFile)) ;
			fclose( $file) ;
			$done	=	true ;
		}
	}
	if ( ! $done) {
		error_log( "could not locate a instance of ................................................ '".$_GET["script"]."'") ;
	}
	/**
	 * create XML reader, assign text, create HTML tree and output it
	 */
	header( "content-type: text/javascript");
	echo $scriptText ;
//	error_log( $scriptText) ;
} else {
	$ret	=	new Reply() ;
	$ret->replyStatus	=	-899 ;
	$ret->replyStatusText	=	"loadCSS.php: tempered session" ;
	$ret->replyStatusInfo	=	"" ;
?>
It appears as if you are working with an invalid session.<br/>
Though this might have happened due to some programming error, it is more likely that
this session has been tempered with.<br/>
Please reload the page and try again.<br/>
If the problem persists please get in touch with your software administrator.
<?php
}

?>
