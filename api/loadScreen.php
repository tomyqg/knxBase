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
 * load "mas" configuration
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/mas/Config/config.inc.php") ;
FDbg::setApp( "loadScreen.php") ;
FDbg::setLevel( 0) ;
FDbg::disable() ;
error_log( $_GET["screen"]) ;
if ( $mySysSession->Validity > SysSession::INVALID) {
	$mySysUser	=	$mySysSession->SysUser ;
	/**
	 * Sequence in which directories are checked:
	 * ../clients/apps/<AppSystem>/<App>/
	 * ../apps/<AppSystem>/<App>/
	 * ../apps/
	 */
	$found	=	false ;
	if ( ! $found) {
		$xmlFile	=	"../clients/".$mySysSession->ClientId."/apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/".$_GET["screen"] ;
		if ( file_exists( $xmlFile)) {
			$file	=	fopen( $xmlFile, "r") ;
			$xmlText	=	fread( $file, 65535) ;
			fclose( $file) ;
			$found	=	true ;
		}
	}
	if ( ! $found) {
		$xmlFile	=	"../apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId."/".$_GET["screen"] ;
		if ( file_exists( $xmlFile)) {
			$file	=	fopen( $xmlFile, "r") ;
			$xmlText	=	fread( $file, 65535) ;
			fclose( $file) ;
			$found	=	true ;
		}
	}
	if ( ! $found) {
		$xmlFile	=	"../apps/_base/".$_GET["screen"] ;
		if ( file_exists( $xmlFile)) {
			$file	=	fopen( $xmlFile, "r") ;
			$xmlText	=	fread( $file, 65535) ;
			fclose( $file) ;
			$found	=	true ;
		}
	}
	if ( $found) {
		/**
		 * create XML reader, assign text, create HTML tree and output it
		 */
		if ( strpos( $_GET["screen"], ".php")) {
			include( $xmlFile) ;
		} else if ( strpos( $_GET["screen"], ".html") === false) {
			$xml	=	new XMLReader() ;
			$xml->XML( $xmlText) ;
			$newTree	=	HTML::create( $xml, null) ;
			echo $newTree->nodes[0] ;
		} else {
			echo $xmlText ;
		}
	} else {
		echo "no valid xml-file ... " . $_GET["screen"] . "\n" ;
		die() ;
	}
//	error_log( $newTree->nodes[0]) ;
} else {
	$ret	=	new Reply() ;
	$ret->replyStatus	=	-899 ;
	$ret->replyStatusText	=	"dispatchXML.php: tempered session" ;
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
