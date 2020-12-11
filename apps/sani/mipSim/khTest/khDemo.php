<?php
/**
 * Created by PhpStorm.
 * User: khwelter
 * Date: 12.08.2016
 * Time: 15:06
 */

include_once(dirname(__file__) . "/../classes/system/EISSCoreObject.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FException.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDbg.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDb.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDbObject.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FSqlQuery.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FSqlMySQLQuery.class.inc.php");
include_once(dirname(__file__) . "/../classes/khClasses.php");

FDbg::setLevel( 9) ;
FDbg::enable() ;
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

echo "Post-config<br/>" ;

echo "<h1>Pre-Auftrag</h1>" ;

echo "Pre-AuftragPos<br/>" ;

$lastMemUsage   =   memory_get_usage() ;
echo "===> " . memory_get_usage() . " ===> " . microtime() . "<br/>" ;
echo "<h2>Assigning myAuftrag</h2>" ;
$lastMemUsage   =   memory_get_usage() ;
$firstAuftrag  =   new _Auftrag() ;
$memUsage   =   memory_get_usage() - $lastMemUsage ;
echo "===> " . memory_get_usage() . " ===> " . microtime() . " =d=> " . $memUsage . "<br/>" ;
$lastMemUsage   =   memory_get_usage() ;
$myAuftrag  =   new _Auftrag() ;
$memUsage   =   memory_get_usage() - $lastMemUsage ;
echo "===> " . memory_get_usage() . " ===> " . microtime() . " =d=> " . $memUsage . "<br/>" ;

echo "---> " . memory_get_usage() . " ---> " . microtime() . "<br/>" ;
echo "<h2>Assigning myAuftragPos</h2>" ;
$firstAuftragPos   =   new _AuftragPosition() ;
echo "---> " . memory_get_usage() . " ---> " . microtime() . "<br/>" ;
$myAuftragPos   =   new _AuftragPosition() ;
echo "---> " . memory_get_usage() . " ---> " . microtime() . "<br/>" ;

echo microtime() . "<br/>" ;

$aufId  =   12345678 ;
if ( $myAuftrag->setKey( $aufId)) {
    echo "Auftrag {$aufId} gefunden<br/>" ;
    echo "   Datum ............. : " . $myAuftrag->a_Datum . "<br/>" ;
} else {
    echo "Auftrag {$aufId} nicht gefunden<br/>" ;
}

echo microtime() . "<br/>" ;

$aufId  =   368 ;
if ( $myAuftrag->setKey( $aufId)) {
    echo "Auftrag {$aufId} gefunden<br/>" ;
    echo microtime() . "<br/>" ;
    echo "   Datum .(getter)............ : " . $myAuftrag->geta_Datum() . "<br/>" ;
    echo microtime() . "<br/>" ;
    echo "   Datum .(attribut).......... : " . $myAuftrag->a_Datum . "<br/>" ;
    echo microtime() . "<br/>" ;
} else {
    echo "Auftrag {$aufId} nicht gefunden<br/>" ;
}

echo microtime() . "<br/>" ;

echo "<h2>Read and echo all 'Auftrag' and all 'AuftragPos'</h2>>" ;
$recordCounter =   0 ;
foreach ( $myAuftrag as $key => $auftrag) {
    $recordCounter++ ;
    echo "Auftrag Nummer ........ : " . $auftrag->a_AuftragId . "<br/>" ;
    $myAuftragPos->clearIterCond() ;
    $myAuftragPos->setIterCond( "ap_AuftragId = ".$auftrag->a_id) ;
    foreach ( $myAuftragPos as $apos) {
        $recordCounter++ ;
        echo " ..... Position HMV Nummer ... : " . $apos->ap_HMV . "<br/>" ;
    }
}
echo "records read ... := " . $recordCounter . "<br/>" ;
echo microtime() . "<br/>" ;

echo "---> " . memory_get_usage() . " ---> " . microtime() . "<br/>" ;

echo "<h2>Read one 'Auftrag' and 'read and update' all 'AuftragPos' x 100</h2>>" ;
$recordCounter =   0 ;
if ( $myAuftrag->isValid()) {
    $recordCounter++ ;
    for ( $il0=0 ; $il0 < 100 ; $il0++) {
        $myAuftragPos->clearIterCond() ;
        $myAuftragPos->setIterCond( "ap_AuftragId = ".$myAuftrag->a_id) ;
        foreach ( $myAuftragPos as $apos) {
            $recordCounter++ ;
            $apos->updateInDb() ;
        }
    }
} else {
    echo "Auftrag not valid ...<br/>" ;
}
echo "records read and updated ... := " . $recordCounter . "<br/>" ;
echo microtime() . "<br/>" ;

echo "---> " . memory_get_usage() . " ---> " . microtime() . "<br/>" ;

echo "<h2>Read one 'Auftrag' and all 'AuftragPos' x 1000</h2>" ;
$recordCounter =   0 ;
$myAuftrag->fetchFromDbWhere( array( "a_AuftragId = 6529")) ;
if ( $myAuftrag->isValid()) {
    $recordCounter++ ;
    for ( $il0=0 ; $il0 < 1000 ; $il0++) {
        $myAuftragPos->clearIterCond() ;
        $myAuftragPos->setIterCond( "ap_AuftragId = ".$myAuftrag->a_id) ;
        foreach ( $myAuftragPos as $apos) {
            $recordCounter++ ;
        }
    }
} else {
    echo "Auftrag not valid ...<br/>" ;
}
echo "records read ... := " . $recordCounter . "<br/>" ;
echo microtime() . "<br/>" ;

echo "---> " . memory_get_usage() . " ---> " . microtime() . "<br/>" ;

?>

