<?php
/**
 * Copyright (c) 2015, 2016 wimtecc, Karl-Heinz Welter
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
 * loadImage.php
 * ===========
 *
 * Load a CSS file from the server.
 *
 * Revision History
 *
 * Date			Ver		User	Comment
 * 2015/02/16	PA1		khw		initial version
 * 2015/03/02	PA2		khw		comments added
 *
 * The basic request from the client looks as follows:
 *
 *	-	loadImage.php?sessionId=<sessionId>&css=<scriptName>
 *
 * Only if the sessionId points to a valid session the script will be executed at all. Otherwise an error message
 * is returned. It's up to the borwser (programming) to react accordingly.
 *
 * The loader checks in the following pathes for the file (1st match):
 *
 * 	-	../clients/<ClientId>/apps/<ApplicationSystemId>/<ApplicationId>/rsrc/images/
 * 	-	../clients/<ClientId>/apps/<ApplicationSystemId>/rsrc/images/
 * 	-	../clients/<ClientId>/apps/rsrc/images/
 * 	-	../clients/<ClientId>/rsrc/images/
 * 	-	../apps/<ApplicationSystemId>/<ApplicationId>/rsrc/images/
 * 	-	../apps/<ApplicationSystemId>/rsrc/images/
 * 	-	../apps/rsrc/images/
 *
 * The advantage of using this script, in comparison to loading through the "normal" webserver is that no data - except an
 * error message - is returned if no valid credentials are supplied.
 *
 */
$debugBoot	=	false ;
require_once( $_SERVER["DOCUMENT_ROOT"]."/mas/Config/config.inc.php") ;
FDbg::setApp( "loadImage.php") ;
FDbg::setLevel( 0) ;
FDbg::disable( true) ;
$referer	=	"" ;
if ( isset( $_SERVER['HTTP_REFERER']))
	$referer	=	$_SERVER['HTTP_REFERER'] ;
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
		$imageFile	=	"../clients/".$mySysSession->ClientId."/apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/rsrc/images/".$_GET["image"] ;
		if ( file_exists( $imageFile)) {
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$imageFile	=	"../clients/".$mySysSession->ClientId."/apps/".$mySysSession->ApplicationSystemId."/rsrc/images/".$_GET["image"] ;
		if ( file_exists( $imageFile)) {
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$imageFile	=	"../clients/".$mySysSession->ClientId."/apps/rsrc/images/".$_GET["image"] ;
		if ( file_exists( $imageFile)) {
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$imageFile	=	"../clients/".$mySysSession->ClientId."/rsrc/images/".$_GET["image"] ;
		if ( file_exists( $imageFile)) {
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$imageFile	=	"../apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/rsrc/images/".$_GET["image"] ;
		if ( file_exists( $imageFile)) {
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$imageFile	=	"../apps/".$mySysSession->ApplicationSystemId."/rsrc/images/".$_GET["image"] ;
		if ( file_exists( $imageFile)) {
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$imageFile	=	"../apps/rsrc/images/".$_GET["image"] ;
		if ( file_exists( $imageFile)) {
			$done	=	true ;
		}
	}
	if ( ! $done) {
		$imageFile	=	"../rsrc/images/".$_GET["image"] ;
		if ( file_exists( $imageFile)) {
			$done	=	true ;
		}
	}
	if ( ! $done) {
/**
 * perform some error handling
 */
	} else {
		$file	=	fopen( $imageFile, "r") ;
		$imageData	=	fread( $file, filesize( $imageFile)) ;
		fclose( $file) ;
		if ( strpos( $_GET["image"], "jpg") !== false) {
			header( "content-type: image/jpeg");
		} else if ( strpos( $_GET["image"], "png") !== false) {
			header( "content-type: image/png");
		}
		echo $imageData ;
	}
	/**
	 * create XML reader, assign text, create HTML tree and output it
	 */
//	error_log( $imageData) ;
} else {
	$ret	=	new Reply() ;
	$ret->replyStatus	=	-899 ;
	$ret->replyStatusText	=	"loadImage.php: tempered session" ;
	$ret->replyStatusInfo	=	"" ;
?>
It apperas as if you are working with an invalid session.<br/>
Though this might have happened due to some programming error, it is more likely that
this session has been tempered with.<br/>
Please reload the page and try again.<br/>
If the problem persists please get in touch with your software administrator.
<?php
}

?>
