<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * MemberContact - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	MemberContact	extends	AppObject	{
	
	/**
	 *
	 */
	function	__construct() {
		parent::__construct( "MemberContact", "Id") ;
	}
	
	/**
	 *
	 */
	public 		function	setId( $_id=-1) {
		parent::setId( $_id) ;
		return $this->getJSON() ;
	}
	/**
	 *
	 */
	protected	function	_postInstantiate() {
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
	}
}
