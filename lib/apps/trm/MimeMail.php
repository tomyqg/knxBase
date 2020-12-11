<?php

#
# Class Definition for: 'Mail'
#

class	mimeData {

	var	$mime_boundary ;
	var	$mimeHead ;
	var	$mimeData ;

	function	mimeData( $_type) {
		$myConfig	=	EISSCoreObject::__getConfig() ;
		$this->mimeData	=	"" ;
		$this->mimeHead	=	"" ;
		switch ( $_type) {
		case	"multipart/mixed"	:
			$this->mime_boundary	=	$myConfig->siteeMail->boundary.md5(time()).sprintf( "_MM_%05d", rand( 1, 500)) ;
			$this->mimeHead .=	"Content-Type: multipart/mixed; charset=utf-8; " ;
			$this->mimeHead .=	"boundary=\"".$this->mime_boundary."\"\n\n" ;
			$this->mimeData	.=	"--" . $this->mime_boundary . "\n" ;
			break ;
		case	"multipart/alternative"	:
			$this->mime_boundary	=	$myConfig->siteeMail->boundary.md5(time()).sprintf( "_MA_%05d", rand( 1, 500)) ;
			$this->mimeHead .=	"Content-Type: multipart/alternative; charset=utf-8; " ;
			$this->mimeHead .=	"boundary=\"".$this->mime_boundary."\"\n\n" ;
			$this->mimeData	.=	"--" . $this->mime_boundary . "\n" ;
			break ;
		}
	}
	function	getAll() {
		$retData	=	$this->mimeHead . $this->mimeData ;
//		error_log( "\n>------getAll--------------\n".$retData."\n<------getAll--------------\n") ;
		return $retData ;
	}
	function	getData() {
		$retData	=	$this->mimeData ;
//		error_log( "\n>>-----getData-------------\n".$retData."\n<<-------------------------\n") ;
		return $retData ;
	}
	function	getHead() {
		$retData	=	$this->mimeHead ;
//		error_log( "\n>>>----getHead-------------\n".$retData."\n<<<----getHead-------------\n") ;
		return $retData ;
	}
	function	addData( $_type, $_mimeData, $_name="", $last=false) {
		$mimeData	=	"" ;
		switch ( $_type) {
		case	"text/plain"	:
			$mimeData	.=	"Content-Type: text/plain; charset=\"ISO-8859-1\";\n" ;
			$mimeData	.=	"Content-Transfer-Encoding: 8-bit\n\n";
			$mimeData	.=	$_mimeData ;
			break ;
		case	"text/html"	:
			$mimeData	.=	"Content-Type: text/html; charset=\"UTF-8\";\n" ;
			$mimeData	.=	"Content-Transfer-Encoding: 8-bit\n\n";
			$mimeData	.=	$_mimeData ;
			break ;
		case	"application/pdf"	:
			$mimeData	.=	"Content-Type: application/pdf; name=\"".$_name."\"\n" ;
			$mimeData	.=	"Content-Transfer-Encoding: base64\n";
			$mimeData	.=	"Content-Disposition: attachment; filename=\"".$_name."\"\n\n";
			$pdfInFile	=	fopen( $_mimeData, "rb") ;
//			$mimeData	.=	base64_encode( fread( $pdfInFile, filesize( $_mimeData))) ;
			$buffer	=	base64_encode( fread( $pdfInFile, filesize( $_mimeData))) ;
			fclose( $pdfInFile) ;
			$len	=	strlen( $buffer) ;
			$lines	=	( $len - 1 ) / 64 + 1 ;
			for ( $i=0 ; $i < $lines ; $i++) {
				$mimeData	.=	substr( $buffer, $i * 64, 64) ;
				$mimeData	.=	"\n" ;
			}
			$mimeData	.=	"\n" ;
			break ;
		case	"multipart/mixed"	:
			$mimeData	.=	$_mimeData ;
			break ;
		}
		$this->mimeData	.=	$mimeData ;
		$this->mimeData	.=	"\n" ;
		$this->mimeData	.=	"--" . $this->mime_boundary ;
		if ( $last)
			$this->mimeData	.=	"--" ;
		$this->mimeData	.=	"\n" ;

		return( $mimeData) ;
	}

	function	_dump() {
		printf( "+----------------mimeMime........................dump starts ---------------------------- \n") ;
		printf( "%s", $this->getAll()) ;
		printf( "+----------------mimeMime........................dump ends ------------------------------ \n") ;
	}

}

class mimeMail extends	EISSCoreObject	{

	var	$mime_mixed_boundary ;
	var	$from ;
	var	$to ;
	var	$replyTo ;
	var	$subject ;
	var	$headers ;
	var	$message ;

	function	mimeMail( $_from, $_to, $_replyTo, $_subject, $_addHeaders="") {
		$myConfig	=	EISSCoreObject::__getConfig() ;
		$this->message	=	"" ;
		$this->mime_mixed_boundary	=	$myConfig->siteeMail->boundary.md5(time()).sprintf( "_mailMM_%05d", rand( 1, 500)) ;
		$this->from	=	$_from ;
		$this->to	=	$_to ;
		$this->replyTo	=	$_replyTo ;
		$this->subject	=	$_subject ;
		$this->headers	=	"From: " . $this->from . "\n" ;
		if ( $this->replyTo <> "") {
			$this->headers	.=	"Reply-To: " . $this->replyTo . "\n" ;
		}
		$this->headers	.=	$_addHeaders ;
	}

	function	addData( $_type, $_message, $_header="") {
		switch ( $_type) {
		case	"multipart/mixed"	:
			$this->headers	.=	"MIME-Version: 1.0\n" ;
			$this->headers	.=	$_header ;
			$this->message	.=	"\nThis is a multi-part message in MIME Format.\n" ;
			$this->message	.=	$_message ;
			break ;
		case	"multipart/alternative"	:
			$this->headers	.=	"MIME-Version: 1.0\n" ;
			$this->headers	.=	$_header ;
			$this->message	.=	"\nThis is a multi-part message in MIME Format.\n" ;
			$this->message	.=	$_message ;
			break ;
		default	:
			$this->message	.=	$_message ;
			break ;
		}
	}

	function	send() {
		if ( $this->mode->eMail) {
			FDbg::trace( 1, FDBg::mdTrcInfo1, "MimeMail.php", "MimeMail", "send()", "will send eMail") ;
			$mailSent	=	mail( $this->to, $this->subject, $this->message, $this->headers) ;
		} else {
			FDbg::trace( 1, FDBg::mdTrcInfo1, "MimeMail.php", "MimeMail", "send()", "will NOT send eMail") ;
			$mailSent	=	true ;
		}
		$this->_dump() ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "MimeMail.php", "mimeMail", "send()", "status := ".$mailSent) ;
		return $mailSent ;
	}
	/**
	 * This static method embraces a plain html formatted text with the required html-framework,
	 * i.e. the html/head/body text. The provided $_text is inserted into a body-tag.
	 * @param string $_text
	 * @return string
	 */
	static	function	getHTMLBody( $_text) {
		$buffer	=	""
				.	"<html>\n"
				.	"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n"
				.	"<head>\n"
				.	"</head>\n"
				.	"<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">"
				.	$_text
				.	"</body>\n"
				.	"</html>\n" ;
		return $buffer ;
	}
	function	_dump() {
		error_log(	"\n+----------------mimeMail........................dump starts ---------------------------- \n"
				.	$this->headers
				.	$this->message
				.	"+----------------mimeMail........................dump ends ------------------------------ \n") ;
	}

}

?>
