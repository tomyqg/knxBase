<?php
/**
 * CustomerRFQItem - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCustomerRFQ which should
 * not be modified.
 *
 * @package Base
 * @subpackage CustomerRFQ
 */
class	CustomerRFQItem	extends	AppObjectERM	{
	/**
	 *
	 * @var unknown_type
	 */
	public	$myArticle ;
	public	$myCond ;
	/**
	 *
	 */
	function	__construct( $_myCustomerRFQNo='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( "CustomerRFQItem", "Id") ;
		$this->CustomerRFQNo	=	$_myCustomerRFQNo ;
		$this->myArticle	=	new Article() ;
		FDbg::end() ;
	}
	/**
	 *
	 * @return void
	 */
	function	getNextItemNo() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addWhere( "CustomerRFQNo='" . $this->CustomerRFQNo . "'") ;
		$myQuery->addOrder( ["ItemNo DESC"]) ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		try {
			$row	=	FDb::queryRow( $myQuery) ;
			error_log( $this->ItemNo . "..............................> " . $row['ItemNo']) ;
			if ( $row) {
			error_log( $this->ItemNo . "..............................> " . $row['ItemNo']) ;
				$this->ItemNo	=	sprintf( "%d", intval( $row['ItemNo']) + 10) ;
			} else {
				$this->ItemNo	=	"10" ;
			}
		} catch ( Exception $e) {
			FDbg::abort() ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->_status ;
	}
}

?>
