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
include_once(dirname(__file__) . "/../classes/khAuftrag.php");

FDbg::enable() ;
FDbg::setLevel( 99) ;
FDbg::disable() ;
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

echo microtime() . "<br/>" ;

echo "Pre-config<br/>" ;

echo microtime() . "<br/>" ;

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

echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo "Retrieving ... a_AuftragId \"6490\" ...<br/>" ;
echo "will retrieve one 'auftrag' by the id-key (different from default column name of Id)<br/>" ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo microtime() . "<br/>" ;
$secondAuftrag->setKey( array( "a_AuftragId" => "6490")) ;
echo microtime() . " ... setKey( array[1]) <br/>" ;
echo $secondAuftrag->__dump( "<br/>") ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;

echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo "Working with 'auf_pos'<br/>" ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo "Retrieving ... ap_AuftragId '{$secondAuftrag->geta_id()}' ...<br/>" ;
echo "will retrieve one 'auf_pos' by the id-key (differnet from default column name of Id)<br/>" ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo microtime() . "<br/>" ;
$firstAuftragPos    =   new _AuftragPosition() ;
echo microtime() . " ... 1st instantiation <br/>" ;
$firstAuftragPos->setKey( array( "ap_AuftragId" => $secondAuftrag->geta_id(), "ap_PosNo" => "1")) ;
echo microtime() . " ... setKey( array[2]) <br/>" ;
echo $firstAuftragPos->__dump( "<br/>") ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;

echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo "Retrieving ... ap_id := 770 ...<br/>" ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo microtime() . "<br/>" ;
$secondAuftragPos    =   new _AuftragPosition() ;
echo microtime() . " ... 2nd instantiation <br/>" ;
$secondAuftragPos->setId( 770) ;
echo microtime() . " ... setId <br/>" ;
echo $secondAuftragPos->__dump( "<br/>") ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;

echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo "Testing foreach for auftrag<br/>" ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo microtime() . " ... setId <br/>" ;
foreach ( $thirdAuftrag as $key => $auftrag) {
    echo "Auftrag Nummer ........ : " . $auftrag->a_id . " ==> AuftragId ....... : " . $auftrag->a_AuftragId . "<br/>";
}
echo microtime() . " ... setId <br/>" ;
echo microtime() . " Dumping firstAuftrag<br/>" ;

//echo $firstAuftrag->__dump( "<br/>") ;
?>