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
FDb::registerDb( localhost, "root", "", "optica_projekt", "kun", "mysql") ;
//FDb::registerDb( CLOUD_DBHOST, CLOUD_DBUSER, CLOUD_DBPASS, CLOUD_DB_NAME, "cloud", "mysql") ;
//FDb::registerDb( OD_DBHOST, OD_DBUSER, OD_DBPASS, OD_DB_NAME, "od", "mysql") ;

echo microtime() . "<br/>" ;

echo "Pre-config<br/>" ;

echo microtime() . "<br/>" ;

class KHAuftrag   extends FDbObject
{
    public function __construct() {
        parent::__construct("auftrag", "a_id", "kun");
        parent::setIdKey( "a_id") ;
    }
}

$myAuftrag  =   new KHAuftrag() ;

echo str_replace( array( "\n", "\t"), array( "<br/>", "-->&nbsp;"), print_r( $myAuftrag, true)) ;

echo "----------------------------------------------------<br/>" ;

?>