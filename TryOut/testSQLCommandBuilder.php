<?php
date_default_timezone_set( "Europe/Berlin") ;
include( "../lib/core/FDbg.php") ;
include( "../lib/core/FSqlQuery.php") ;
include( "../lib/core/FSqlMySQLQuery.php") ;
include( "../lib/core/FSqlMSSQLQuery.php") ;
include( "../lib/core/FException.php") ;

// ----------------------------------------------

$myQuery = new FSqlMySQLSelect( "SampleTable") ;
echo $myQuery->getQuery() ;
echo "\n\n" ;
$myQuery->setAs( "ALIAS") ;
echo $myQuery->getQuery() ;
echo "\n\n" ;
$myQuery->addLimit( new FSqlLimit()) ;
echo $myQuery->getQuery() ;
echo "\n\n" ;
$myQuery->addLimit( new FSqlLimit( 5, 10)) ;
echo $myQuery->getQuery() ;
echo "\n\n" ;

// ----------------------------------------------

$myQuery = new FSqlMySQLSelect( "SampleTable") ;
$myQuery->addField( ["Col1", "Col3"]) ;
echo $myQuery->getQuery() ;
echo "\n\n" ;
$myQuery->setAs( "ANOTHER_ALIAS") ;
echo $myQuery->getQuery() ;
echo "\n\n" ;

// ----------------------------------------------

$myQuery = new FSqlMySQLSelect( "SampleTable") ;
echo $myQuery->getCountQuery() ;
echo "\n\n" ;
$myQuery->addWhere( "Attribute = 'MeinWert' ") ;
echo $myQuery->getCountQuery() ;
echo "\n\n" ;

// ----------------------------------------------

$myQuery = new FSqlMySQLStructure( "SampleTable") ;
echo $myQuery->getQuery() ;
echo "\n\n" ;

// ----------------------------------------------

$myQuery = new FSqlMSSQLStructure( "SampleTable") ;
echo $myQuery->getQuery() ;
echo "\n\n" ;

// ----------------------------------------------

$myFException = new FException( "__FILE__", "__CLASS__", "__METHOD__", "Exception message") ;
echo $myFException . "\n" ;

// ----------------------------------------------

try {
    // statement which results in an exception
    throw ( new Exception( "TEST FROM SYSTEM")) ;
} catch ( Exception $e) {
    $myFException = new FException( "__FILE__", "__CLASS__", "__METHOD__", $e->getMessage()) ;
    echo $myFException . "\n" ;
}

?>
