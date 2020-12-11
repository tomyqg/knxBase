<?php
/**
 * Price.php - intermediate class to cover everything common to prices
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-06-22	PA1		khw		added to rev. control
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
class	Price	extends	AppObjectCore	{
	/**
	 *
	 * @param string	$_className
	 * @param string	$_keyCol
	 * @param string	$_db
	 */
	function	__construct( $_className, $_keyCol="Id", $_db="def") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_className', '$_keyCol', '$_db')") ;
		parent::__construct( $_className, $_keyCol, $_db) ;
		FDbg::end() ;
	}
}

?>
