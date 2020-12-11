<?php
/**
 * Created by PhpStorm.
 * User: khwelter
 * Date: 05.09.2016
 * Time: 12:34
 */

include_once(dirname(__file__) . "/../classes/system/EISSCoreObject.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FException.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDbg.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDb.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDbObject.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FSqlQuery.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FSqlMySQLQuery.class.inc.php");
include_once(dirname(__file__) . "/../classes/khClasses.php");

FDbg::disable() ;
FDbg::enable() ;
FDbg::setLevel( 99) ;
//    FDbg::setAppToTrace( "kunden") ;
FDbg::setApp( $_GET["module"] . "::" . $_GET["op"]) ;
FDbg::setFileListExclude( ",EISSCoreObject.class.inc.php,FDb.class.inc.php,FDbObject.class.inc.php,FSqlQuery.class.inc.php,FSqlMySQLQuery.class.inc.php,dbwrapper.class.inc.php,") ;
FDbg::setFileListExclude( "") ;
FDbg::setModuleListExclude( "*") ;
FDbg::setMethodListExclude( "*") ;
FDbg::trace( 0, "", "", "", "+------------------------------------------------------------------------------------------------------------") ;
FDbg::trace( 0, "", "", "", " starting tracing subsystem for URL := " . $_SERVER[ 'REQUEST_URI']) ;
FDbg::traceArray( 0, "", "", "", "REQUEST variables in request", $_REQUEST) ;
//FDb::registerDb( KUN_DBHOST, KUN_DBUSER, KUN_DBPASS, KUN_DB_NAME, "kun", "mysql") ;
FDb::registerDb( "localhost:7188", "root", "", "optica_projekt", "kun", "mysql") ;
//FDb::registerDb( CLOUD_DBHOST, CLOUD_DBUSER, CLOUD_DBPASS, CLOUD_DB_NAME, "cloud", "mysql") ;
//FDb::registerDb( OD_DBHOST, OD_DBUSER, OD_DBPASS, OD_DB_NAME, "od", "mysql") ;
FDb::registerDb( "localhost:7188", "root", "", "od_system", "od", "mysql") ;

echo microtime() . "<br/>" ;

echo "Pre-config<br/>" ;

echo microtime() . "<br/>" ;

class Option   extends FDbObject
{
    public function __construct() {
        parent::__construct("option", "Id", "od");
    }
}

echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo "Testing creation times for new instances ...<br/>" ;
echo "will create 3 new instances of 'auftrag' and measure time instances ...<br/>" ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;

echo microtime() . "<br/>" ;
$firstAuftrag   =   new _Auftrag() ;

echo microtime() . " ... 1st instantiation <br/>" ;
$secondAuftrag   =   new _Auftrag() ;

echo microtime() . " ... 2nd instantiation <br/>" ;
$thirdAuftrag   =   new _Auftrag() ;

echo microtime() . " ... 3rd instantiation <br/>" ;

echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo "Retrieving ... a_id 1 ...<br/>" ;
echo "will retrieve one 'auftrag' by the primary key<br/>" ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo microtime() . "<br/>" ;
$firstAuftrag->setKey( 1) ;
echo microtime() . " ... setKey <br/>" ;
echo $firstAuftrag->__dump( "<br/>") ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo $firstAuftrag->_exportJSON( "<br/>") ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;

echo "Obtaining a_IK-state<br/>" ;
if ( isset( $firstAuftrag->a_IK)) {
    echo "firstAuftrag->a_IK is set ...<br/>" ;
} else {
    echo "firstAuftrag->a_IK is NOT set ...<br/>" ;
}
echo "-------------------------------------------------------------------------------------------------<br/>" ;

echo "Obtaining Lock-state<br/>" ;
if ( isset( $firstAuftrag->ock)) {
    echo "firstAuftrag->Lock is set ...<br/>" ;
} else {
    echo "firstAuftrag->Lock is NOT set ...<br/>" ;
}

echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo "setting Lock<br/>" ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;
$firstAuftrag->_declare( "Lock") ;
$firstAuftrag->setLock( "1") ;
if ( isset( $firstAuftrag->Lock)) {
    echo "firstAuftrag->Lock is set ...<br/>" ;
} else {
    echo "firstAuftrag->Lock is NOT set ...<br/>" ;
}
echo $firstAuftrag->__dump( "<br/>") ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;

echo microtime() . " ... setId <br/>" ;

$myOption   =   new Option() ;
$myOption->setIterCond( "Category = 'ShoeSize'") ;
foreach ( $myOption as $opt) {
    echo $opt->__dump( "<br/>") ;
}
?>