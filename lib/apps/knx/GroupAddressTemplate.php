<?php
/**
 * @author		Karl-Heinz Welter <khwelter@icloud.com>
 * @version		0.1
 * @package		GroupAddressTemplate
 * @subpackage	Client
 * @filesource
 */
/**
 * Client - Base Class
 *
 */
class	GroupAddressTemplate	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myId="") {
		parent::__construct( "GroupAddressTemplate", "TemplateName", "def") ;
		if ( strlen( $_myId) > 0) {
			try {
				$this->setId( $_myId) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
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
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "GroupAddressTemplate", "Id", "def") ;				// no specific object we need here
			$myObj->setPage( 0,99999, $_POST['step']) ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ "TemplateName LIKE '%$sCrit%' OR Description LIKE '%$sCrit%'"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"GroupAddressTemplateItem"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["TemplateName = '".$this->TemplateName."'"]) ;
			$myQuery->addOrder( ["AddressOffset"]) ;
			error_log( "-----> " . $myQuery->getQuery()) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
	/**
	 * importXML
	 *
	 * import an XML file containing a complete GroupAddressTemplate tree
	 */
	function	importXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, "GroupAddressTemplate.php", "GroupAddressTemplate", "importXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$path	=	"/srv/www/vhosts/wimtecc.de/mas_r1/data/" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddressTemplate.php", "GroupAddressTemplate", "importXML( ...)", "storage path := '$path'") ;
		$fn	=	( isset( $_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddressTemplate.php", "GroupAddressTemplate", "importXML( ...)", print_r($_FILES, true)) ;
		if ( $fn) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddressTemplate.php", "GroupAddressTemplate", "importXML( ...)", "fn is set") ;
//			file_put_contents(
//				'uploads/' . $fn,
//				file_get_contents('php://input')
//			);
			FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddressTemplate.php", "GroupAddressTemplate", "importXML( ...)", "$fn uploaded") ;
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddressTemplate.php", "GroupAddressTemplate", "importXML( ...)", "fn is * NOT * set") ;
			$idx	=	0 ;
			$file	=	"jelly.xml" ;
			foreach ( $_FILES as $idx => $data) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddressTemplate.php", "GroupAddressTemplate", "importXML( ...)", "Filename['$idx']") ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddressTemplate.php", "GroupAddressTemplate", "importXML( ...)", print_r( $data, true)) ;
				$filename	=	$path . $file ;
				FDbg::trace( 1, FDbg::mdTrcInfo1, "GroupAddressTemplate.php", "GroupAddressTemplate", "importXML( ...)", "$filename") ;
				if (move_uploaded_file( $data["tmp_name"], $filename)) {
					$xmlText	=	file_get_contents( $filename, true) ;
					$myXML	=	new DOMDocument( $xmlText) ;
					$myXML->loadXML( $xmlText) ;
					$myNodes	=	$myXML->getElementsByTagName( "GroupAddressTemplate-Export") ;	// get the root node of the ETS export
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
