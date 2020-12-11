<?php
/**
 * @author		Karl-Heinz Welter <khwelter@icloud.com>
 * @version		0.1
 * @package		GroupAddress
 * @subpackage	Client
 * @filesource
 */
/**
 * Client - Base Class
 *
 */
class	GroupAddress	extends	GroupRange	{
	/**
	 *
	 */
	function	__construct( $_id="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_id)") ;
		parent::__construct( "GroupAddress", "Id", "def") ;
		$this->Central	=	0 ;
		if ( strlen( $_id) > 0) {
			try {
				$this->setId( $_id) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	setId( $_id=-1) {
		$this->Id	=	$_id ;
		$this->reload() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws Exception
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
						"object is not locked") ;
		switch ( $objName)	{
		case	"GroupAddressBlock"	:
			$objName	=	"GroupAddress" ;
			$tmpObj	=	new $objName() ;
			$myKeyCol	=	$this->keyCol ;
			if ( $tmpObj->setId( $_id)) {
				FDbg::trace( 2, "Role.php", "Role", "updDep( '$_key', $_id, '$_val')",
								"object is valid") ;
								$tmpObj->getFromPostL() ;
								$tmpObj->updateInDb() ;
							} else {
								$e	=	new Exception( 'Role::updDep[Id='.$_id.'] is INVALID !') ;
								error_log( $e) ;
								throw $e ;
							}
			$this->getList( $_key, $_id, $_val, $reply) ;
			break ;
		default	:
			$this->getDepAsXML( $_key, $_id, $objName, $reply) ;
			break ;
		}
		FDbg::end( 1, "Role.php", "Role", "updDep( '$_key', $_id, '$_val')") ;
		return $reply ;
	}
	/**
	 * getList( $_key, $_id, $_val, $reply)
	 *
	 * Get list of objects.
	 *
	 * @param	string		$_key			Key of the application to work with
	 * @param	int			$_id			Id of the application to work with
	 * @param	mixed		$_val			Optional additional parameter
	 * @param	Reply		$reply			Reply where the result goes. If null
	 *										a new Reply object will be instantiated
	 * @return	Reply						Reply object containing the result
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$sStep	=	"thisPage" ;
		if ( isset( $_POST['step']))
			$sStep	=	$_POST['step'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "GroupAddress", "Id", "def") ;				// no specific object we need here
			$myObj->setPage( 0,99999, $sStep) ;
			/**
			 *
			 */
			if ( isset( $_POST['_treeLevel'])) {
				$treeLevel	=	intval( $_POST['_treeLevel']) ;
			} else {
				$treeLevel	=	0 ;
			}
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myObj->treeLevel	=	$treeLevel ;
			$myObj->maxLevel	=	2 ;
			switch ( $treeLevel) {
			case	0	:
				$myObj->dataset	=	"MainGroup" ;
				$myObj->level	=	0 ;
				$myQuery->addWhere( [ "MiddleGroup IS NULL"]) ;
//				$myQuery->addGroup( [ "TopGroup"]) ;
				$myQuery->addOrder( [ "TopGroup"]) ;
				break ;
			case	1	:
				$myObj->dataset	=	"MiddleGroup" ;
				$myObj->level	=	1 ;
				$myQuery->addWhere( [ "TopGroup = ".intval( $this->TopGroup)."", "MiddleGroup IS NOT NULL", "Object IS NULL"]) ;
//				$myQuery->addGroup( [ "MiddleGroup"]) ;
				$myQuery->addOrder( [ "TopGroup ASC", "MiddleGroup ASC"]) ;
				error_log( $myQuery->getQuery()) ;
				break ;
			case	2	:
				$myObj->dataset	=	"GroupObject" ;
				$myObj->level	=	2 ;
				$myQuery->addWhere( [ "TopGroup = ".intval( $this->TopGroup)."", "MiddleGroup = ".intval( $this->MiddleGroup)."", "Object IS NOT NULL"]) ;
				$myQuery->addOrder( [ "TopGroup ASC", "MiddleGroup ASC", "Object ASC"]) ;
				error_log( $myQuery->getQuery()) ;
				break ;
			}
			/**
			 *
			 */
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"GroupAddressBlock"	:
			$myObj	=	new FDbObject( "GroupAddressBlock", "Id", "def", "GroupAddress") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter	=	"InsertGroup = " . $this->InsertGroup . " " ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter]) ;
			$myQuery->addOrder( [ "GroupAddressDec", "Address"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
	/**
	 * importXML
	 *
	 * import an XML file containing a complete GroupAddress tree
	 */
	function	importXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "GroupAddress.php", "GroupAddress", "importXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddress.php", "GroupAddress", "importXML( ...)", "storage path := '$path'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddress.php", "GroupObjectupAddress", "importXML( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddress.php", "GroupAddress", "importXML( ...)", "fn is set") ;
//			file_put_contents(
//				'uploads/' . $fn,
//				file_get_contents('php://input')
//			);
			FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddress.php", "GroupAddress", "importXML( ...)", "$fn uploaded") ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddress.php", "GroupAddress", "importXML( ...)", "fn is * NOT * set") ;
			$idx	=	0 ;
			$file	=	"jelly.xml" ;
			foreach ( $_FILES as $idx => $data) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddress.php", "GroupAddress", "importXML( ...)", "Filename['$idx']") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddress.php", "GroupAddress", "importXML( ...)", print_r( $data, true)) ;
				$filename	=	$path . $file ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddress.php", "GroupAddress", "importXML( ...)", "$filename") ;
				if (move_uploaded_file( $data["tmp_name"], $filename)) {
					$xmlText	=	file_get_contents( $filename, true) ;
					$myXML	=	new DOMDocument( $xmlText) ;
					$myXML->loadXML( $xmlText) ;
					$myNodes	=	$myXML->getElementsByTagName( "GroupAddress-Export") ;	// get the root node of the ETS export
					if ( $myNodes->length > 0) {
						$this->removeFromDbWhere( "") ;
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( <_doc>, <_node>)", "nodes available....: " . $myNodes->length) ;
						$this->_importXML( $myXML, $myNodes->item( 0), true) ;
					}
				} else {
					echo "Possible file upload attack!<br/>\n";
				}
			}
		}
		FDbg::end() ;
		return $reply ;
	}
	function	storeInDb( $_exec=true) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $this->Central == "true")
			$this->Central	=	1 ;
		parent::storeInDb( $_exec) ;
		FDbg::end() ;
	}
}
?>
