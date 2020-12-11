<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * KostentraegerAnschrift - Base Class
 *
 * @package Application
 * @subpackage KostentraegerAnschrift
 */
class	KostentraegerKontakt	extends	AppObjectSani	{
	public	$Rights	=	0x00000000 ;
	/**
	 *
	 */
	function	__construct( $_myIKNr="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myIKNr')") ;
		parent::__construct( "KostentraegerKontakt", "Id", "cloud") ;
		FDBg::end() ;
	}
}
?>
