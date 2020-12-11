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
class	ClientApplication	extends	AppObjectCore	{
	private		$Rights	=	0 ;
	/**
	 *
	 */
	function	__construct( $_key="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <_key>)") ;
		parent::__construct( "ClientApplication", "Id", "sys") ;
		$this->Rights	=	0x00000001 ;
		if ( is_array( $_key) || $_key != "") {
			$this->setKey( $_key) ;
		} else {
			$this->invalidate() ;
		}
		FDbg::end( $this->isValid()) ;
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
		$myKeyArray	=	array() ;
		if ( is_array( $_key)) {
			$myKeyArray[ "ClientId"]	=	$_key[0] ;
			$myKeyArray[ "UserId"]	=	$_key[1] ;
			$myKeyArray[ "ApplicationSystemId"]	=	$_key[2] ;
			$myKeyArray[ "ApplicationId"]	=	$_key[3] ;
			parent::setKey( $myKeyArray) ;
			if ( ! $this->isValid()) {
				$_key[1]	=	"__appuser__" ;
				parent::setKey( $myKeyArray) ;
				if ( ! $this->isValid()) {
				}
			}
		} else {
			$this->invalidate() ;
			$e	=	new FException( basename( __FILE__), __CLASS__, __METHOD__."( <_key)", "complex key (array!) required; simple key provided!") ;
			throw $e ;
		}
		return $this->isValid() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->isValid()) {
			$this->getFromPostL() ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "Client.php::Client::add(): 'Client' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$this->_upd() ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	function	_upd() {
		FDbg::begin( 1, "Client.php", "Client", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		$this->_addRem( FTr::tr( "Client updated")) ;
		FDbg::end( 1, "Client.php", "Client", "_upd()") ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->getClassName() ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->getClassName() . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$objName	=	$_val ;
		if ( $objName == "ClientContact") {
			$myClientContact	=	new ClientContact() ;
			$myClientContact->ClientNo	=	$this->ClientNo ;
			$myClientContact->newClientContact() ;
			$myClientContact->getFromPostL() ;
			$myClientContact->updateInDb() ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, $_val, $reply) ;
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		if ( $objName == "ClientContact") {
			$myClientContact	=	new ClientContact() ;
			$myClientContact->ClientNo	=	$this->ClientNo ;
			$myClientContact->newClientContact() ;
			$myClientContact->getFromPostL() ;
			$myClientContact->updateInDb() ;
			return $myClientContact->ClientContactNo ;
		} else if ( $objName == "LiefClient") {
			return $this->_addDepClient( "L") ;
		} else if ( $objName == "RechClient") {
			return $this->_addDepClient( "R") ;
		} else if ( $objName == "AddClient") {
			return $this->_addDepClient( "A") ;
		}
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			$tmpObj	=	new $objName() ;
			if ( $tmpObj->setId( $_id)) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"object is valid") ;
				$tmpObj->getFromPostL() ;
				$tmpObj->updateInDb() ;
				$this->getList( $_key, $_id, $objName, $reply) ;
				break ;
			}
			break ;
		}
		FDbg::end( 1, "Client.php", "Client", "updDep( '$_key', $_id, '$_val')") ;
		return $reply ;
	}
	function	delDep( $_key, $_id, $_val) {
		$objName	=	$_val ;
		try {
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			if ( $tmpObj->setId( $_id)) {
				$tmpObj->removeFromDb() ;
			} else {
				$e	=	new Exception( "Client.php::Client::delDep[Id='.$_id.'] dependent is INVALID !") ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 * Kommentar zu der Colli hinzufuegen
	 *
	 *	Dies Funktion fuegt einen Kommentar, $_rem, an die Colli an.
	 *	Datum/Uhrzeit sowie die Id des angemeldeten Benutzers, oder - im Falle eines unauthtntizierten Zugriffs - der Zusatz: "Hintergrunf Prozess"
	 *	werden automatisch in dem Kommentar vermerkt.
	 *
	 *	@param	char	$_rem
	 *	@return	void
	 */
	function	addRem( $_key="", $_id=-1, $_val="", $_reply=null) {
		try {
			$this->_addRem( $_POST[ '_IRem']) ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param integer $_id
	 * @param string $_val
	 * @param string $reply
	 * @return Reply
	 */
	function	getAppData( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '<_key>', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->getClassName() ;
		}
		if ( ! $this->isValid()) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)",
								"Client Application not found! Will check for __appuser__!") ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
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
		case	"SysConfigObj"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$cond	=	"(( Class='' AND ApplicationSystemId = '' AND ApplicationSystemId='' AND ClientId = '') OR "																				// core configuration
					.	"( Class='' AND ApplicationSystemId = '".$this->ApplicationSystemId."' AND ApplicationSystemId='' AND ClientId = '') OR "												// ApplicationSystem
					.	"( Class='' AND ApplicationSystemId = '".$this->ApplicationSystemId."' AND ApplicationSystemId='".$this->ApplicationId."' AND ClientId = '') OR "						// ApplicationSystem/Application
					.	"( Class='' AND ApplicationSystemId = '".$this->ApplicationSystemId."' AND ApplicationSystemId='".$this->ApplicationId."' AND ClientId = '".$this->ClientId."') OR "	// ApplicationSystem/Application/Client
					.	"( Class='def' AND ApplicationSystemId = '".$this->ApplicationSystemId."' AND ApplicationId = '".$this->ApplicationId."' AND ClientId = '".$this->ClientId."') OR "
					.	"( Class='UI' AND ApplicationSystemId = '".$this->ApplicationSystemId."' AND ApplicationId = '".$this->ApplicationId."') OR "
					.	"( Class='UI' AND ApplicationSystemId = '".$this->ApplicationSystemId."' AND ApplicationId = '".$this->ApplicationId."' AND ClientId = '".$this->ClientId."') OR "
					.	"(  ApplicationSystemId = '".$this->ApplicationSystemId."' AND ApplicationId = '".$this->ApplicationId."' AND ClientId = '".$this->ClientId."')) "
					;
			$order	=	"LENGTH( ClientId) ASC, Class, Block, Parameter " ;
			$myQuery->addWhere( [ $cond, "ApplicationSystemId like '%".$sCrit."%'"]) ;
			$myQuery->addOrder( [ "ApplicationSystemId", "ApplicationId", "Class", "Block", "Parameter"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "SysConfigObj") ;
			break ;
		case	"Application"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$cond	=	"(  ApplicationSystemId = '".$this->ApplicationSystemId."' AND ApplicationId = '".$this->ApplicationId."') "
					;
			$myQuery->addWhere( [ $cond]) ;
			$myQuery->addOrder( [ $order]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	exportXML( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
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
		if ( $_reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		/**
		 *
		 */
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "storage path := '$path'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "ApplicationSystem.php", "ApplicationSystem", "importXML( ...)", "fn is set") ;
//			file_put_contents(
//				'uploads/' . $fn,
//				file_get_contents('php://input')
//			);
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
					$myNodes	=	$myXML->getElementsByTagName( $this->getClassName()) ;
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

	/**
	 *
	 */
	protected	function	_postInstantiate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
}
?>
