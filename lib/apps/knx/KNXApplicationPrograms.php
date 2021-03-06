<?php
/**
 * @author		Karl-Heinz Welter <khwelter@icloud.com>
 * @version		0.1
 * @package		KNXManufacturer
 * @subpackage	Client
 * @filesource
 */
/**
 * Client - Base Class
 *
 */
class	KNXApplicationPrograms	extends	AppObjectKNX	{
	/**
	 *
	 */
	function	__construct( $_id="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_id)") ;
		parent::__construct( "KNXApplicationPrograms", "Id", "def") ;
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
}
?>
