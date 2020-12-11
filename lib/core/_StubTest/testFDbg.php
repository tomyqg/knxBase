<?php
require_once( "../EISSCoreObject.php") ;
require_once( "../FDbg.php") ;

class 	testFDbg	{
	function	test1() {
		FDbg::begin( 0, __FILE__, __CLASS__, __METHOD__, "Test Message") ;
		FDbg::end() ;
	}
}

$myTest	=	new testFDbg() ;
$myTest->test1() ;

?>
