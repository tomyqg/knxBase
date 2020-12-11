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
 * @author	Karl-Heinz Welter <khw@wimtecc.com>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * Client - Base Class
 *
 * @package Application
 * @subpackage Client
 */
class	ClientContact	extends	AppObjectCore	{
	/**
	 *
	 */
	function	__construct( $_key="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <_key>)") ;
		parent::__construct( "ClientContact", "Id", "sys") ;
		$this->defComplexKey( array( "ClientId", "ClientContactNo")) ;
		$this->Rights	=	0x00000001 ;
		if ( is_array( $_key)) {
			$this->setComplexKey( $_key) ;
		} else if ( $_key != "") {
			$this->setKey( $_key) ;
		} else {
			$this->_valid	=	false ;
		}
		FDbg::end( $this->_valid) ;
	}
	/**
	 * Set the Key of an object and try to retrieve the object from the db
	 *
	 * This method sets the Key of the object to $_key and, if the $_key is
	 * longer than 0 characters, tries to retrieve the object from the db.
	 *
	 * @param	string	$_key			key of the object to retrieve
	 * @param	string	$_db			database alias
	 * @return	bool					success of the method, false - no success, true - success
	 */
	function	setComplexKey( $_key) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '".(is_array($_key) ? print_r( $_key, true) : $_key)."')") ;
		$keyCol	=	$this->keyCol ;
		if ( is_array( $_key)) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "key := <Array>") ;
			parent::setKey( $_key) ;
			if ( ! $this->_valid) {
				$_key[1]	=	"__appuser__" ;
				parent::setComplexKey( $_key) ;
				if ( ! $this->_valid) {
				}
			}
		} else {
			$this->_valid	=	false ;
			$e	=	new FException( basename( __FILE__), __CLASS__, __METHOD__."( <_key)", "complex key (array!) required; sijmple key provided!") ;
			FDbg::abort() ;
			throw $e ;
		}
		FDbg::end( $this->_valid) ;
		return $this->_valid ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$where	=	array() ;
			if ( isset( $_POST['ClientId'])) {
				$where[]	=	"ClientId = \"" . $_POST['ClientId'] . "\"" ;
			}
			$where[]	=	"ApplicationSystemId like '%".$sCrit."%'" ;
 			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addOrder( ["ClientId", "ApplicationSystemId"]) ;
			$myQuery->addWhere( $where) ;
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	exportXML( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$myXML	=	new DOMDocument() ;
		$myXML->xmlStandAlone	=	false ;		// avoid the <?xml version="1.0"> line
		$myXML->formatOutput	=	true ;		// make it readable
		$startNode	=	$this->_getAttributesAsXML( $myXML, $myXML) ;
		$fileName	=	$mySysSession->SessionId . "_" . $this->Id . ".xml" ;
		$myFile	=	fopen( "/srv/www/vhosts/wimtecc.de/mas_r1/data/" . $fileName, "w+") ;
		fwrite( $myFile, $myXML->saveXML( $startNode)) ;
		fclose( $myFile) ;
		$reply->replyData	=	"<Reference>$fileName</Reference>\n" ;
		return $reply ;
	}
	/**
	 *
	 */
	function	importXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "ApplicationSystem.php", "ApplicationSystem", "importXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "storage path := '$path'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "fn is set") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "$fn uploaded") ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "fn is * NOT * set") ;
			$idx	=	0 ;
			$file	=	"jelly.xml" ;
			foreach ( $_FILES as $idx => $data) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "Filename['$idx']") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", print_r( $data, true)) ;
				$filename	=	$path . $file ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "$filename") ;
				if (move_uploaded_file( $data["tmp_name"], $filename)) {
					$xmlText	=	file_get_contents( $filename, true) ;
					$myXML	=	new DOMDocument( $xmlText) ;
					$myXML->loadXML( $xmlText) ;
					$myNodes	=	$myXML->getElementsByTagName( $this->className) ;
					if ( $myNodes->length > 0) {
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "nodes available....: " . $myNodes->length) ;
						$myApplicationSystem	=	$this->_importXML( $myXML, $myNodes->item( 0)) ;
					}
				} else {
					echo "Possible file upload attack!<br/>\n";
				}
			}
		}
		FDbg::end() ;
		return $reply ;
	}
}
?>
