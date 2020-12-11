<?php
require_once( "../EISSCoreObject.php") ;
require_once( "../FDbg.php") ;
require_once( "../FDb.php") ;
require_once( "../FDbObject.php") ;
require_once( "../FException.php") ;

date_default_timezone_set( "Europe/Berlin") ;	// needs to be here, otherwise php complains

class 	testFDbObject	{
	function	test1() {
		FDbg::begin( 0, __FILE__, __CLASS__, __METHOD__, "Test Message") ;
		FDb::registerDb( "localhost", "abc", "cba", "refa") ;
		echo( "-------------------------------------------------------------------------------------------------------\n") ;
		echo( "Database...........: " . FDb::getDbName() . "\n") ;
		echo( "  Driver...........: " . FDb::getDriver() . "\n") ;
		echo( "-------------------------------------------------------------------------------------------------------\n") ;
		FDbg::end() ;
	}
	function	test2() {
		FDbg::begin( 0, __FILE__, __CLASS__, __METHOD__, "Test Message") ;
		$myObject	=	new FDbObject( "blog", "id") ;
		$myObject->setId( 4) ;
		echo( "-------------------------------------------------------------------------------------------------------\n") ;
		echo( "myObject->isValid()...........: " . $myObject->isValid() . "\n") ;
		echo( "myObject->getXMLF()...........: \n" . $myObject->getXMLF() . "\n") ;
		echo( "-------------------------------------------------------------------------------------------------------\n") ;
		FDbg::end() ;
	}
	function	test3() {
		FDbg::begin( 0, __FILE__, __CLASS__, __METHOD__, "Test Message") ;
		$myObject	=	new FDbObject( "blog", "id") ;
		$myQuery	=	$myObject->getQueryObj( "Select") ;
		echo( "-------------------------------------------------------------------------------------------------------\n") ;
		echo( "myObject->tableFromQuery()....: \n" . $myObject->tableFromQuery( $myQuery) . "\n") ;
		echo( "-------------------------------------------------------------------------------------------------------\n") ;
		FDbg::end() ;
	}
}

$myTest	=	new testFDbObject() ;

echo( "Running...........: test1\n") ;
$myTest->test1() ;

echo( "Running...........: test2\n") ;
$myTest->test2() ;

echo( "Running...........: test3\n") ;
$myTest->test3() ;

?>
