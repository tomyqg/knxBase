<?php
/**
 * @author		Karl-Heinz Welter <khwelter@icloud.com>
 * @version		0.1
 * @package		DataPointSubType
 * @subpackage	Client
 * @filesource
 */
/**
 * Client - Base Class
 *
 */
class	DataPointSubType	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myId="") {
		parent::__construct( "DataPointSubType", "Id", "def") ;
		if ( strlen( $_myId) > 0) {
			try {
				$this->setId( $_myId) ;
				$this->actDataPointTypeContact	=	new DataPointTypeContact() ;
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
		$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
				$_POST['_step']	=	$_val ;
			/**
			 *
			 */
			$myObj	=	new DataPointSubType() ;				// no specific object we need here
 			$myQuery	=	new FSqlSelect( "DataPointSubType") ;
			$myQuery->addField( ["Id","DataPointSubTypeId","Description1"]) ;
			$myQuery->addOrder( ["DataPointSubTypeId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
}
?>
