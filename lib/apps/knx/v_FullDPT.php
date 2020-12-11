<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.com>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * DataPointType - Base Class
 *
 * @package Application
 * @subpackage DataPointType
 */
class	v_FullDPT	extends	FDbObject	{
	/**
	 *
	 */
	function	__construct( $_myId="") {
		parent::__construct( "v_FullDPT", "Id", "def") ;
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
	function	setId( $_id=-1) {
		$this->Id	=	$_id ;
		$this->reload() ;
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
			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addOrder( [ "Id"]) ;
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		FDbg::end() ;
		return $reply ;
	}
}
?>
