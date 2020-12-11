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
 * Reply.php	-	Class definition
 *
 * Reply collects the results as well as status information.
 *
 * Status codes:
 *    0	:=	everything ok, Status text must read "ok"
 * -995	:=
 * -996	:=
 * -997	:=
 * -998	:=
 * -999	:=	start object not found
 *
 * @author miskhwe
 *
 */
class Reply	{
	/**
	 *
	 * @var unknown
	 */
	const	mediaTextHTML	=	"text/html" ;
	const	mediaTextXML	=	"text/xml" ;
	const	mediaTextJSON	=	"text/json" ;
	const	mediaTextPlain	=	"text/plain" ;
	const	mediaTextCSV	=	"text/csv" ;
	const	mediaAppCSV		=	"application/csv" ;
	const	mediaAppPDF		=	"application/pdf" ;
	const	mediaAppOctet	=	"application/octet-stream" ;
	const	mediaAppMisc	=	"application/*" ;
	const	mediaImgPng		=	"image/png" ;
	const	mediaImgJpg		=	"image/jpg" ;
	/**
	 *
	 * @var unknown
	 */
	public	$replyMediaType		=	Reply::mediaTextXML ;
	public	$replyMediaTypeAccepted		=	"" ;
	public	$replyData			=	"" ;
	public	$replySupportingData=	"" ;
	public	$replyReferences	=	"" ;
	public	$replyStatus		=	0 ;				// 0	:=	everything ok
	public	$replyStatusText	=	"ok" ;		// "ok"	:=	everything ok
	public	$replyStatusInfo	=	"" ;		// ""	:=	no additional information
	public	$targetURL			=	"" ;
	public	$replyDebugMessage	=	"" ;
	public	$replyingClass		=	"" ;
	public	$message			=	"" ;
	public	$instClass			=	"" ;
	public	$fileName			=	"" ;				// filename of the attachment
	public	$fullFileName		=	"" ;			// place where to read the filecontent from
	
	/**
	 * Reply constructor.
	 * @param string $_replyingClass
	 * @param string $_instClass
	 */
	function	__construct( $_replyingClass="-BASE_CLASS-", $_instClass="-CLASS-", $_replyMediaType=Reply::mediaTextXML) {
		$this->replyingClass	=	$_replyingClass ;
		$this->instClass	=	$_instClass ;
		$this->replyMediaType   =   $_replyMediaType ;
		if ( isset( $_SERVER[ "HTTP_ACCEPT"])) {
			$this->replyMediaTypeAccepted   =   $_SERVER[ "HTTP_ACCEPT"] ;
		}
		error_log( "-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_" . $this->replyMediaType . "-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_") ;
		error_log( "-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_" . $this->replyMediaTypeAccepted . "-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_") ;
	}
	
	/**
	 *
	 */
	function	getReply() {
		return $this ;
	}
	
	function __toString() {
		switch ( $this->replyMediaType) {
			case	"text/json"	:
				$reply	=	$this->data ;
				break ;
			case	"text/xml"	:
				$reply	=	"<Reply>" ;
				$reply	.=	$this->getDebugMessage() ;
				$reply	.=	$this->getStatus() ;
				$reply	.=	$this->getData() ;
				$reply	.=	$this->getSupportingData() ;
				if ( $this->message != "") {
					$reply	.=	"<Message>\n" ;
					$reply	.=	$this->message ;
					$reply	.=	"</Message>\n" ;
				}
				$reply	.=	"</Reply>\n" ;
				break ;
			case	"application/octet-stream"	:
				$reply	=	"<Reply>" ;
				$reply	.=	$this->getDebugMessage() ;
				$reply	.=	$this->getStatus() ;
				$reply	.=	$this->getFile() ;
				$reply	.=	$this->getSupportingData() ;
				if ( $this->message != "") {
					$reply	.=	"<Message>\n" ;
					$reply	.=	$this->message ;
					$reply	.=	"</Message>\n" ;
				}
				$reply	.=	"</Reply>\n" ;
				break ;
			default	:
				$reply	=	"<Reply>" ;
				$reply	.=	$this->getDebugMessage() ;
				$reply	.=	$this->getStatus() ;
				$reply	.=	$this->getData() ;
				$reply	.=	$this->getSupportingData() ;
				$reply	.=	$this->getFile() ;
				if ( $this->message != "") {
					$reply	.=	"<Message>\n" ;
					$reply	.=	$this->message ;
					$reply	.=	"</Message>\n" ;
				}
				$reply	.=	"</Reply>\n" ;
				break ;
		}
		return $reply ;
	}
	
	/**
	 *
	 */
	function	getPDFReply() {
		$reply	=	"<Reply>" ;
		$reply	.=	$this->getDebugMessage() ;
		$reply	.=	$this->getStatus() ;
		$reply	.=	$this->getPDFData() ;
		if ( $this->message != "") {
			$reply	.=	"<Message>\n" ;
			$reply	.=	$this->message ;
			$reply	.=	"</Message>\n" ;
		}

		$reply	.=	"</Reply>\n" ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getStatus() {
		$ret	=	"<Status>\n" ;
		$ret	.=	"<StatusCode>" . $this->replyStatus . "</StatusCode>\n" ;
		$ret	.=	"<StatusText>" . "<![CDATA[" . $this->replyStatusText . "]]></StatusText>\n" ;
		$ret	.=	"<StatusInfo>" . ( $this->replyStatusInfo != "" ? "<![CDATA[" . $this->replyStatusInfo . "]]>" : "empty") . "</StatusInfo>\n" ;
		$ret	.=	"<TargetURL><![CDATA[" . $this->targetURL . "]]></TargetURL>\n" ;
		if ( $this->replyingClass != "") {
			$ret	.=	"<InstantiatedClass>" . $this->instClass . "</InstantiatedClass>\n" ;
			$ret	.=	"<ReplyingClass>" . $this->replyingClass . "</ReplyingClass>\n" ;
		}
		$ret	.=	"</Status>\n" ;
		return $ret ;
	}
	/**
	 *
	 */
	function	getData() {
		if ( $this->replyData != "") {
			$ret	=	"<Data>\n" ;
			$ret	.=	$this->replyData ;
			$ret	.=	"</Data>\n" ;
		} else if ( $this->replyReferences != "") {
			$ret	=	"<References>\n" ;
			$ret	.=	$this->replyReferences ;
			$ret	.=	"</References>\n" ;
		} else {
			$ret	=	"<Data>\n" ;
			$ret	.=	"INVALID REPLY. NO DATA AVAILABLE<" . $this->replyMediaType . ">" ;
			$ret	.=	"</Data>\n" ;
		}
		return $ret ;
	}
	/**
	 *
	 */
	function	getSupportingData() {
		$ret	=	"" ;
		if ( $this->replySupportingData != "") {
			$ret	=	"<SupportingData>\n" ;
			$ret	.=	$this->replyData ;
			$ret	.=	"</SupportingData>\n" ;
		}
		return $ret ;
	}
	
	/**
	 *
	 */
	function	getFile() {
		$ret	=	"" ;
		$ret	.=	"<Filename>" . $this->fileName . "</Filename>\n" ;
		$ret	.=	"<FullFilename>" . $this->fullFileName . "</FullFilename>\n" ;
		return $ret ;
	}
	/**
	 *
	 */
	function	getPDFData() {
		$ret	=	"<Data>\n" ;
		$ret	.=	"<url>/0000000002-PC16.pdf</url>" ;
		$ret	.=	"</Data>\n" ;
		return $ret ;
	}
	
	/**
	 *
	 */
	function	getTextData() {
		$ret	=	"<Data>\n" ;
		$ret	.=	"<url>/0000000002-PC16.txt</url>" ;
		$ret	.=	"</Data>\n" ;
		return $ret ;
	}
	
	/**
	 *
	 */
	function	getDebugMessage() {
		$ret	=	"<Debug>\n" ;
		$ret	.=	$this->replyDebugMessage ;
		$ret	.=	"</Debug>\n" ;
		return $ret ;
	}
	
	/**
	 *
	 */
	function	dump() {
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()",
						$this->getReply()) ;
	}
}
?>
