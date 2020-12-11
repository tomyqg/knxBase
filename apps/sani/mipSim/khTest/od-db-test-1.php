<?php
/**
 * Created by PhpStorm.
 * User: khwelter
 * Date: 04.10.2016
 * Time: 08:22
 */

include_once(dirname(__file__) . "/../classes/system/EISSCoreObject.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FException.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDbg.class.inc.php");

include_once(dirname(__file__) . "/../classes/system/dbconn.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/db.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/db_access.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/class/dbconn.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/classes/dbready.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/classes/dbwrapper.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/syslog.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/settings.class.inc.php");

FDbg::setLevel( 99) ;
FDbg::disable() ;
FDbg::enable() ;
//    FDbg::setAppToTrace( "kunden") ;
FDbg::setApp( $_GET["module"] . "::" . $_GET["op"]) ;
FDbg::setFileListExclude( ",EISSCoreObject.class.inc.php,FDb.class.inc.php,FDbObject.class.inc.php,FSqlQuery.class.inc.php,FSqlMySQLQuery.class.inc.php,dbwrapper.class.inc.php,") ;
FDbg::setFileListExclude( "") ;
FDbg::setModuleListExclude( "*") ;
FDbg::setMethodListExclude( "*") ;
FDbg::trace( 0, "", "", "", "+------------------------------------------------------------------------------------------------------------") ;
FDbg::trace( 0, "", "", "", " starting tracing subsystem for URL := " . $_SERVER[ 'REQUEST_URI']) ;
FDbg::traceArray( 0, "", "", "", "REQUEST variables in request", $_REQUEST) ;

echo "<h1>OD Database Test and Demo</h1>" ;

define('KUN_DBHOST', "localhost:7188");
define("KUN_DBUSER", "root");
define("KUN_DBPASS", "");
define("KUN_DB_NAME", "optica_projekt");

/**
 *  get a dbaccess object for the Db 'K
 */
$dbac   =   DBConn::GetDBAccess( "menu");

/**
 *  read * w/o condition but only into DBMySQl Object
 */
echo "<h2>Reading all records from Menu w/o order and w/o limit ONLY into MySQL Object</h2>" ;
$res    =   $dbac->readSql() ;
echo "Starting table ... : <br/>" ;
if ( $res != null) {
    echo "res is != null<br/>" ;
    while ( $dbac->ReadRow() == 1) {
        echo "Starting row ... : <br/>" ;
        $row    =   $dbac->RowData() ;
        foreach ( $row as $ci => $col) {
            echo "$ci := $col<br/>" ;
        }
    }
}

/**
 *  read * w/o condition
 */
echo "<h2>Reading all records from Menu w/o order and w/o limit</h2>" ;
$res    =   $dbac->readComplete() ;
echo "Starting table ... : <br/>" ;
foreach ( $res as $idx => $row) {
    echo "Starting row ... : <br/>" ;
    foreach ( $row as $ci => $col) {
        echo "$ci := $col<br/>" ;
    }
}

/**
 *  read * w/o condition
 */
echo "<h2>Reading all records from Menu w/ order and w/o limit</h2>" ;
$res    =   $dbac->readComplete( "1=1", "MenuNo") ;
echo "Starting table ... : <br/>" ;
foreach ( $res as $idx => $row) {
    echo "Starting row ... : <br/>" ;
    foreach ( $row as $ci => $col) {
        echo "$ci := $col<br/>" ;
    }
}

/**
 *  read MenuNo
 */
echo "<h2>Reading all records from Menu w/ order and w/o limit</h2>" ;
$res    =   $dbac->readSomeFields( "MenuNo", "1=1", "MenuNo") ;
echo "Starting table ... : <br/>" ;
foreach ( $res as $idx => $row) {
    echo "Starting row ... : <br/>" ;
    foreach ( $row as $ci => $col) {
        echo "$ci := $col<br/>" ;
    }
}

echo "<h1>End of OD Database Test and Demo</h1>" ;
