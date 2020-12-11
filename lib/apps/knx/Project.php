<?php
/**
 * Project
 *
 * A project acts as umbrella for all KNX data, from groupobjects to facility structure.
 *
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
class	Project	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myId="") {
		parent::__construct( "Project", "ProjectNo", "def") ;
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
			$myQuery->addOrder( [ "ProjectNo"]) ;
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	newKey( $_digits=6, $_nsStart="000000", $_nsEnd="999999", $_store=true) {
		$newKey	=	"0000000001" ;
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$myObj	=	new Project() ;
		$myObj->first( "ProjectNo DESC") ;
		if ( $myObj->isValid()) {
			$this->ProjectNo	=	sprintf( "%010d", intval( $myObj->ProjectNo) + 1) ;
		}
		FDbg::end() ;
		return $this->ProjectNo ;
	}
}
?>
