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
 * ApplicationSystem - Base Class
 *
 * @package Application
 * @subpackage ApplicationSystem
 */
class	ApplicationSystem	extends	AppObjectCore	{
	/**
	 *
	 */
	private		$Rights ;
	/**
	 *
	 */
	function	__construct( $_myApplicationSystemNo="") {
		parent::__construct( "ApplicationSystem", "ApplicationSystemId", "sys") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myApplicationSystemNo) > 0) {
			try {
				$this->setApplicationSystemNo( $_myApplicationSystemNo) ;
				$this->actApplicationSystemContact	=	new ApplicationSystemContact() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	/**
	 *
	 */
	function	setApplicationSystemNo( $_myApplicationSystemNo) {
		$this->ApplicationSystemNo	=	$_myApplicationSystemNo ;
		$this->reload() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "ApplicationSystem.php", "ApplicationSystem", "add( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->ApplicationSystemNo	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "ApplicationSystem.php::ApplicationSystem::add(): 'ApplicationSystem' invalid after creation!") ;
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
		FDbg::begin( 1, "ApplicationSystem.php", "ApplicationSystem", "upd( '$_key', $_id, '$_val')") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::dumpL( 0x00000001, "ApplicationSystem.php::ApplicationSystem::del(...)") ;
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
		FDbg::begin( 1, "ApplicationSystem.php", "ApplicationSystem", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$objName	=	$_val ;
		switch ( $objName) {
		default	:
			parent::addDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "ApplicationSystem.php", "ApplicationSystem", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "ApplicationSystem.php", "ApplicationSystem", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	function	delDep( $_key, $_id, $_val, $reply=null) {
		FDbg::begin( 1, "ApplicationSystem.php", "ApplicationSystem", "delDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$objName	=	$_val ;
		try {
			switch ( $objName) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				break ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		FDbg::end() ;
		return $reply ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "ApplicationSystem.php", "ApplicationSystem", "getAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->getClassName() ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
		return $reply ;
	}
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "ApplicationSystem.php", "ApplicationSystem", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		if ( $objName == "ApplicationSystemContact") {
			$tmpObj	=	new $objName() ;
			$tmpObj->setId( $_id) ;
			$reply->replyData	=	$tmpObj->tableFromDb( "", "", "ApplicationSystemNo = '$this->ApplicationSystemNo' ") ;
		} else if ( $objName == "LiefApplicationSystem") {
			$tmpObj	=	new ApplicationSystem() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$tmpObj->addCol( "City", "varchar") ;
			$tmpObj->addCol( "Address", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "ApplicationSystemNo like '".$this->ApplicationSystemNo."-L%' ",
												"ORDER BY ApplicationSystemNo ", "ApplicationSystemLiefApplicationSystem",
												"",
												"C.Id, C.ApplicationSystemNo, CONCAT( C.ApplicationSystemName1, \" \", C.ApplicationSystemName2) AS Company, "
													. "CONCAT( C.ZIP, \" \", C.City) AS City, "
													. "CONCAT( C.Street, \" \", C.Number) AS Address "
			) ;
			$reply->replyData	=	str_replace( "ApplicationSystem>", "LiefApplicationSystem>", $ret) ;
			return $reply ;
		} else if ( $objName == "RechApplicationSystem") {
			$tmpObj	=	new ApplicationSystem() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "ApplicationSystemNo like '".$this->ApplicationSystemNo."-R%' ",
												"ORDER BY ApplicationSystemNo ",
												"ApplicationSystemRechApplicationSystem",
												"",
												"C.Id, C.ApplicationSystemNo, CONCAT( C.ApplicationSystemName1, C.ApplicationSystemName2) AS Company ") ;
			$reply->replyData	=	str_replace( "ApplicationSystem>", "RechApplicationSystem>", $ret) ;
		} else if ( $objName == "AddApplicationSystem") {
			$tmpObj	=	new ApplicationSystem() ;
			$tmpObj->setId( $_id) ;
			$tmpObj->addCol( "Company", "varchar") ;
			$ret	=	$tmpObj->tableFromDb( "", "", "ApplicationSystemNo like '".$this->ApplicationSystemNo."-A%' ",
												"ORDER BY ApplicationSystemNo ",
												"ApplicationSystemAddApplicationSystem",
												"",
												"C.Id, C.ApplicationSystemNo, CONCAT( C.ApplicationSystemName1, C.ApplicationSystemName2) AS Company ") ;
			$reply->replyData	=	str_replace( "ApplicationSystem>", "AddApplicationSystem>", $ret) ;
		}
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "ApplicationSystem.php", "ApplicationSystem", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$objName	=	$_val ;
		switch ( $objName) {
		default	:
			parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
		return $reply ;
	}
	/**
	 *
	 */
	function	_addDepApplicationSystem( $_pref) {

		$this->_valid  =       false ;
		$kundeNrParts	=	explode( "-", $this->ApplicationSystemNo) ;
		$this->ApplicationSystemNo	=	$kundeNrParts[0] ;
		$query	=	sprintf( "SELECT ApplicationSystemNo FROM ApplicationSystem WHERE ApplicationSystemNo LIKE '%s-$_pref%%' ORDER BY ApplicationSystemNo DESC LIMIT 0, 1 ", $this->ApplicationSystemNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			$myApplicationSystemDepAdr	=	new ApplicationSystem() ;
			if ( $numrows == 0) {
				$myApplicationSystemDepAdr->ApplicationSystemNo	=	$this->ApplicationSystemNo . "-" . $_pref . "001" ;
			} else {
				$row    =       mysql_fetch_array( $sqlResult) ;
				$myApplicationSystemDepAdr->ApplicationSystemNo	=	sprintf( "%s-$_pref%03d", $this->ApplicationSystemNo, intval( substr( $row[0], 8, 3)) + 1) ;
			}
			$myApplicationSystemDepAdr->storeInDb() ;
			$myApplicationSystemDepAdr->getFromPostL() ;
			$myApplicationSystemDepAdr->updateInDb() ;
			$this->_valid  =       true ;
		}
		return $myApplicationSystemDepAdr->ApplicationSystemNo ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
				$_POST['_step']	=	$_val ;
			/**
			 *
			 */
			$myObj	=	new ApplicationSystem() ;				// no specific object we need here
 			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addOrder( ["ApplicationSystemId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"Application"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ "ApplicationSystemId = '".$this->ApplicationSystemId."'"]) ;
			$myQuery->addOrder( [ "ApplicationId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case    "ASPerClient"   :
			$_POST['_step'] =       $_val ;
			/**
			*
			*/
			$where  =       array() ;
			if ( isset( $_POST['ClientId'])) {
					$where[]        =       "ClientId = \"" . $_POST['ClientId'] . "\"" ;
			}
			$myObj  =       new FDbObject( "ApplicationSystemPerClient", "", "sys") ;               // no specific object we need here
			$myQuery        =       $myObj->getQueryObj( "Select") ;
			$myQuery->addOrder( ["ClientId"]) ;
			$myQuery->addWhere( $where) ;
			$reply->replyData       =       str_replace( "ApplicationSystemPerClient", "ApplicationSystem", $myObj->tableFromQuery( $myQuery)) ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
	function	getApplicationSystemPerClient( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		}
//		error_log( $ret) ;
		return $reply ;
	}
	/**
	 *
	 */
	function	exportXMLComplete( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		/**
		 *
		 */
		$myXML	=	new DOMDocument() ;
		$myXML->xmlStandAlone	=	false ;		// avoid the <?xml version="1.0"> line
		$myXML->formatOutput	=	true ;		// make it readable
		$startNode	=	$myXML->appendChild( $myXML->createElement( "CompleteApplicationSystemData")) ;
		$rootNode	=	$this->_exportXML( $myXML, $startNode) ;
		/**
		 *
		 */
		$myApplication	=	new Application() ;
		$myApplication->setIterCond( "ApplicationSystemId = '".$this->ApplicationSystemId."' ") ;
		foreach ( $myApplication as $application) {
			$application->_exportXML( $myXML, $rootNode) ;
		}
		$myClientApplication	=	new ClientApplication() ;
		$myClientApplication->setIterCond( "ApplicationSystemId = '".$this->ApplicationSystemId."' ") ;
		foreach ( $myClientApplication as $clientApplication) {
			$clientApplication->_exportXML( $myXML, $rootNode) ;
		}
		$mySysConfigObj	=	new SysConfigObj() ;
		$mySysConfigObj->setIterCond( "ApplicationSystemId = '".$this->ApplicationSystemId."' ") ;
		$mySysConfigObj->setIterOrder( [ "ApplicationSystemId", "ApplicationId", "ClientId", "Class", "BLock", "Parameter"]) ;
		foreach ( $mySysConfigObj as $sysConfigObj) {
			$sysConfigObj->_exportXML( $myXML, $rootNode) ;
		}
		$fileName	=	$mySysSession->SessionId . "_" . $this->ApplicationSystemId . ".txt" ;
		$myFile	=	fopen( "/srv/www/vhosts/wimtecc.de/mas_r1/data/" . $fileName, "w+") ;
		fwrite( $myFile, $myXML->saveXML( $startNode)) ;
		fclose( $myFile) ;
		$reply->replyData	=	"<Reference>$fileName</Reference>\n" ;
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
		$fileName	=	$mySysSession->SessionId . "_" . $this->ApplicationSystemId . ".txt" ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		/**
		 *
		 */
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "storage path := '$path'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "fn is set") ;
//			file_put_contents(
//				'uploads/' . $fn,
//				file_get_contents('php://input')
//			);
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "$fn uploaded") ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "fn is * NOT * set") ;
			$idx	=	0 ;
			$file	=	"jelly.xml" ;
			foreach ( $_FILES as $idx => $data) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "Filename['$idx']") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", print_r( $data, true)) ;
				$filename	=	$path . $file ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "$filename") ;
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
	/**
	 * returns the name of the PDF file which has been created
	 */
	function	getRef( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->getClassName()) ;
		$reply->replyMediaType	=	Reply::mediaTextPlain ;
		$reply->txtName	=	"test.txt" ;
		$reply->fullTXTName	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" . $_val ;
		FDbg::end() ;
		return $reply ;
	}
}
?>
