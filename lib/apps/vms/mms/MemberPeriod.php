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
 * @package vms
 * @subpackage Basic
 */
class	MemberPeriod	extends	AppObject	{
	
	/**
	 *
	 */
	function	__construct() {
		parent::__construct( "MemberPeriod", "Id") ;
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
