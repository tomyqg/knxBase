<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * LetsintserbringerLEGS - Relational Description
 *
 * @package Application
 * @subpackage LeistungserbringerLEGS
 */
class	LeistungserbringerLEGS	extends	AppObject	{
	public	$Rights	=	0x00000000 ;
	/**
	 *
	 */
	function	__construct( $_myLEIKNr="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myLEIKNr')") ;
		parent::__construct( "LeistungserbringerLEGS", "Id", "cloud") ;
		FDBg::end() ;
	}
}
?>
