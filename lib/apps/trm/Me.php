<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * Employee - Base Class
 *
 * @package Application
 * @subpackage Employee
 */
class	Me	extends	Employee	{
	/**
	 *
	 */
	function	__construct( $_myEmployeeNo="") {
		parent::__construct( "Employee", "EmployeeNo") ;
	}
	/**
	 *
	 */
	function	setKey( $_myEmployeeNo="") {
		$appUser	=	$this->__getAppUser() ;
		if ( $appUser) {
			$this->EmployeeNo	=	$appUser->EmployeeNo ;
			$this->reload() ;
		}
		return $this->_valid ;
	}
	/**
	 *
	 */
	function	setEmployeeNo( $_myEmployeeNo="") {
		$appUser	=	$this->__getAppUser() ;
		if ( $appUser) {
			$this->EmployeeNo	=	$appUser->EmployeeNo ;
			$this->reload() ;
		}
		$this->reload() ;
		return $this->_valid ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyData	=	$this->getXMLF( "Me") ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getNextAsXML( $_key="", $_id=-1, $_val="") {
		return $this->getXMLString( $_key, $_id, $_val) ;
	}
	/**
	 *
	 */
	function	getPrevAsXML( $_key="", $_id=-1, $_val="") {
		return $this->setKey() ;
	}
}
?>
