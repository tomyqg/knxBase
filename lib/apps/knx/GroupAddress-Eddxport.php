<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.com>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * GroupRange - Base Class
 *
 * @package Application
 * @subpackage GroupRange
 */
class	GroupRange	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myId="") {
		parent::__construct( "GroupRange", "Id", "def") ;
		if ( strlen( $_myId) > 0) {
			try {
				$this->setId( $_myId) ;
				$this->actGroupRangeContact	=	new GroupRangeContact() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	/**
	 *
	 */
	function	setId( $_myId) {
		$this->Id	=	$_myId ;
		$this->reload() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "GroupRange.php", "GroupRange", "add( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->Id	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "GroupRange.php::GroupRange::add(): 'GroupRange' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 * 
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "GroupRange.php", "GroupRange", "upd( '$_key', $_id, '$_val')") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "GroupRange.php::GroupRange::del(...)") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getAsXML() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "GroupRange.php", "GroupRange", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
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
		FDbg::begin( 1, "GroupRange.php", "GroupRange", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "GroupRange.php", "GroupRange", "updDep( '$_key', $_id, '$_val')",
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
		FDbg::begin( 1, "GroupRange.php", "GroupRange", "delDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
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
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "GroupRange.php", "GroupRange", "getAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "GroupRange.php", "GroupRange", "getListAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
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
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			/**
			 *
			 */
			$myObj	=	new GroupRange() ;				// no specific object we need here
 			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addOrder( ["RangeStart"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"GroupAddress"	:
			if ( isset( $_POST['_treeLevel'])) {
				$treeLevel	=	intval( $_POST['_treeLevel']) ;
			} else {
				$treeLevel	=	0 ;
			}
			/**
			 *
			 */
			$myObj	=	new $objName() ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myObj->treeLevel	=	$treeLevel ;
			$myObj->maxLevel	=	3 ;
			switch ( $treeLevel) {
			case	0	:
				$myObj->dataset	=	"MainGroups" ;
				$myObj->level	=	0 ;
				$myQuery->addGroup( [ "Address"]) ;
				$myQuery->addOrder( [ "Address"]) ;
				break ;
			}
			/**
			 *
			 */
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
//			$myQuery->addWhere( [ "Id = '".$this->Id."'"]) ;
			$myQuery->addOrder( [ "Address"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		return $reply ;
	}
	/**
	 *
	 */
	function	exportXMLComplete( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$myXML	=	new DOMDocument() ;
		$myXML->xmlStandAlone	=	false ;		// avoid the <?xml version="1.0"> line
		$myXML->formatOutput	=	true ;		// make it readable
		$startNode	=	$myXML->appendChild( $myXML->createElement( "CompleteGroupRangeData")) ;
		$rootNode	=	$this->_exportXML( $myXML, $startNode) ;
		/**
		 *
		 */
		$myApplication	=	new Application() ;
		$myApplication->setIterCond( "Id = '".$this->Id."' ") ;
		foreach ( $myApplication as $application) {
			$application->_exportXML( $myXML, $rootNode) ;
		}
		$myClientApplication	=	new ClientApplication() ;
		$myClientApplication->setIterCond( "Id = '".$this->Id."' ") ;
		foreach ( $myClientApplication as $clientApplication) {
			$clientApplication->_exportXML( $myXML, $rootNode) ;
		}
		$mySysConfigObj	=	new SysConfigObj() ;
		$mySysConfigObj->setIterCond( "Id = '".$this->Id."' ") ;
		$mySysConfigObj->setIterOrder( [ "Id", "ApplicationId", "ClientId", "Class", "BLock", "Parameter"]) ;
		foreach ( $mySysConfigObj as $sysConfigObj) {
			$sysConfigObj->_exportXML( $myXML, $rootNode) ;
		}
		$fileName	=	$mySysSession->SessionId . "_" . $this->Id . ".txt" ;
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
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$myXML	=	new DOMDocument() ;
		$myXML->xmlStandAlone	=	false ;		// avoid the <?xml version="1.0"> line
		$myXML->formatOutput	=	true ;		// make it readable
		$startNode	=	$this->_getAttributesAsXML( $myXML, $myXML) ;
		$fileName	=	$mySysSession->SessionId . "_" . $this->Id . ".txt" ;
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
		FDbg::begin( 1, "GroupRange.php", "GroupRange", "importXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupRange.php", "GroupRange", "importXML( ...)", "storage path := '$path'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupRange.php", "GroupRange", "importXML( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupRange.php", "GroupRange", "importXML( ...)", "fn is set") ;
//			file_put_contents(
//				'uploads/' . $fn,
//				file_get_contents('php://input')
//			);
			FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupRange.php", "GroupRange", "importXML( ...)", "$fn uploaded") ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupRange.php", "GroupRange", "importXML( ...)", "fn is * NOT * set") ;
			$idx	=	0 ;
			$file	=	"jelly.xml" ;
			foreach ( $_FILES as $idx => $data) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupRange.php", "GroupRange", "importXML( ...)", "Filename['$idx']") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupRange.php", "GroupRange", "importXML( ...)", print_r( $data, true)) ;
				$filename	=	$path . $file ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupRange.php", "GroupRange", "importXML( ...)", "$filename") ;
				if (move_uploaded_file( $data["tmp_name"], $filename)) {
					$xmlText	=	file_get_contents( $filename, true) ;
					$myXML	=	new DOMDocument( $xmlText) ;
					$myXML->loadXML( $xmlText) ;
					$myNodes	=	$myXML->getElementsByTagName( $this->className) ;
					if ( $myNodes->length > 0) {
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "nodes available....: " . $myNodes->length) ;
						$myGroupRange	=	$this->_importXML( $myXML, $myNodes->item( 0)) ;
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
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaTextPlain ;
		$reply->txtName	=	"test.txt" ;
		$reply->fullTXTName	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" . $_val ;
		FDbg::end() ;
		return $reply ;
	}
}
?>
