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

FDbg::setLevel( 0) ;
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
FDb::registerDb( "localhost:7188", "root", "", "test.kriwat", "kun", "mysql") ;

echo microtime() . "<br/>" ;

$mwst   =   array( ">> NULL <<", "19% inkl.", "7% inkl.", "19% exkl.", "7% exkl.", "keine") ;

echo "Pre-config<br/>" ;

echo microtime() . "<br/>" ;

echo "-------------------------------------------------------------------------------------------------<br/>" ;
echo "Script for fixing Kriwat AuftragPos issues ...<br/>" ;
echo "-------------------------------------------------------------------------------------------------<br/>" ;

echo microtime() . "<br/>" ;
$myAuftrag      =   new _Auftrag() ;
$myAuftragPos   =   new _AuftragPosition() ;

$myAuftragPos->setIterOrder( array( "ap_AuftragId", "ap_PosNo", "ap_UPosNo")) ;
echo "<table>" ;
$lastAuftragNo  =   "" ;
$thisConsAuftragNo  =   "" ;
$thisAuftragFaulty  =   false ;
$consNetto  =   0.0 ;
$consMwst   =   0.0 ;
$consBrutto =   0.0 ;
foreach ( $myAuftragPos as $aufPos) {
    if ( $thisConsAuftragNo != "" && $thisAuftragFaulty && $thisConsAuftragNo != $myAuftragPos->ap_AuftragId) {
        echo "<tr>" ;
        echo "<td colspan='4'>Konsolidierte Auftragswerte...:</td>" ;
        echo "<td>".$consNetto."(".$myAuftrag->a_GesamtpreisNetto.")</td>" ;
        echo "<td>".$consMwst."(".$myAuftrag->a_GesamtvkpreisBrutto-$myAuftrag->a_GesamtpreisNetto.")</td>" ;
        echo "<td>".$consBrutto."(".$myAuftrag->a_GesamtvkpreisBrutto.")</td>" ;
        echo "</tr>" ;
        $thisConsAuftragNo  = "" ;
        $thisAuftragFaulty  =   false ;
    }
    $faultyItem =   false ;
    $diff   =   0.0 ;
    $mwstText   =   "" ;
    switch ( $myAuftragPos->ap_MWSTKEY) {
        case    0   ;
            $diff   =   $myAuftragPos->ap_VKPREISNETTO - $myAuftragPos->ap_VKPREISBRUTTO ;
            break ;
        case    1   ;               // 19% inklusive
            $diff   =   ( $myAuftragPos->ap_VKPREISBRUTTO / 1.19) - $myAuftragPos->ap_VKPREISNETTO ;
            break ;
        case    2   ;               // 7% inklusive
            $diff   =   ( $myAuftragPos->ap_VKPREISBRUTTO / 1.07) - $myAuftragPos->ap_VKPREISNETTO ;
            break ;
        case    3   ;               // 19% +
            $diff   =   ( $myAuftragPos->ap_VKPREISNETTO * 1.19) - $myAuftragPos->ap_VKPREISBRUTTO ;
            break ;
        case    4   ;               // 7% +
            $diff   =   ( $myAuftragPos->ap_VKPREISNETTO * 1.07) - $myAuftragPos->ap_VKPREISBRUTTO ;
            break ;
        case    5   :
            break ;
        default :
            break ;
    }
    $mwstText   =   $mwst[$myAuftragPos->ap_MWSTKEY] ;
    if ( abs( $diff) > 0.01 && $myAuftragPos->ap_VKPREISBRUTTO > 0)
        $faultyItem =   true ;
    if ( $faultyItem) {
        $thisAuftragFaulty  =   true ;
        $myAuftrag->setId( $myAuftragPos->ap_AuftragId) ;
        if ( $lastAuftragNo != $myAuftrag->a_AuftragId) {
            echo "<tr>" ;
            echo "<td>".$myAuftrag->a_AuftragId."</td>" ;
            echo "<td>".$myAuftrag->a_Datum."</td>" ;
            echo "</tr>" ;
        }
        $lastAuftragNo  =   $myAuftrag->a_AuftragId ;
        echo "<tr>" ;
        echo "<td colspan='2'>Position.....:</td>" ;
        echo "<td>".$myAuftragPos->ap_PosNo." / ".$myAuftragPos->ap_UPosNo." / ".$mwstText."</td>" ;
        echo "<td>".$myAuftragPos->ap_bausatz_id."</td>" ;
        echo "<td>".$myAuftragPos->ap_VKPREISNETTO."</td>" ;
        echo "<td>".$myAuftragPos->ap_MWST."</td>" ;
        echo "<td>".$myAuftragPos->ap_VKPREISBRUTTO."</td>" ;
        echo "<td>".$diff."</td>" ;
        switch ( $myAuftragPos->ap_MWSTKEY) {
            case    0   :
                break ;
            case    1   :
                $myAuftragPos->ap_VKPREISNETTO  =   round( $myAuftragPos->ap_VKPREISBRUTTO / 1.19, 2) ;
                $myAuftragPos->ap_MWST  =   $myAuftragPos->ap_VKPREISBRUTTO - $myAuftragPos->ap_VKPREISNETTO ;
                echo "<td>Neuer netto-Preis...: ".$myAuftragPos->ap_VKPREISNETTO." zzgl. ".$myAuftragPos->ap_MWST." &euro; Mwst.</td>" ;
                break ;
            case    2   :
                $myAuftragPos->ap_VKPREISNETTO  =   round( $myAuftragPos->ap_VKPREISBRUTTO / 1.07, 2) ;
                $myAuftragPos->ap_MWST  =   $myAuftragPos->ap_VKPREISBRUTTO - $myAuftragPos->ap_VKPREISNETTO ;
                echo "<td>Neuer netto-Preis...: ".$myAuftragPos->ap_VKPREISNETTO." zzgl. ".$myAuftragPos->ap_MWST." &euro; Mwst.</td>" ;
                break ;
            case    3   :
                $myAuftragPos->ap_VKPREISBRUTTO  =   round( $myAuftragPos->ap_VKPREISBRUTTO * 1.19, 2) ;
                $myAuftragPos->ap_MWST  =   $myAuftragPos->ap_VKPREISBRUTTO - $myAuftragPos->ap_VKPREISNETTO ;
                echo "<td>Neuer brutto-Preis...: ".$myAuftragPos->ap_VKPREISNETTO." zzgl. ".$myAuftragPos->ap_MWST." &euro; Mwst.</td>" ;
                break ;
            case    4   :
                $myAuftragPos->ap_VKPREISBRUTTO  =   round( $myAuftragPos->ap_VKPREISBRUTTO * 1.07, 2) ;
                $myAuftragPos->ap_MWST  =   $myAuftragPos->ap_VKPREISBRUTTO - $myAuftragPos->ap_VKPREISNETTO ;
                echo "<td>Neuer brutto-Preis...: ".$myAuftragPos->ap_VKPREISNETTO." zzgl. ".$myAuftragPos->ap_MWST." &euro; Mwst.</td>" ;
                break ;
            case    5   :
                break ;
        }
        echo "</tr>" ;
    }
    if ( $thisConsAuftragNo == $myAuftragPos->ap_AuftragId) {
        $consNetto     +=   $myAuftragPos->ap_VKPREISNETTO ;
        $consBrutto    +=   $myAuftragPos->ap_VKPREISBRUTTO ;
        $consMwst      +=   $myAuftragPos->ap_MWST ;
    } else {
        $thisConsAuftragNo  =   $myAuftragPos->ap_AuftragId ;
        $consNetto     =   $myAuftragPos->ap_VKPREISNETTO ;
        $consBrutto    =   $myAuftragPos->ap_VKPREISBRUTTO ;
        $consMwst      =   $myAuftragPos->ap_MWST ;
    }
}
if ( $thisConsAuftragNo != "") {
    echo "<tr>" ;
    echo "<td colspan='4'>Konsolidierte Auftragswerte...:</td>" ;
    echo "<td>".$consNetto."</td>" ;
    echo "<td>".$consMwst."</td>" ;
    echo "<td>".$consBrutto."</td>" ;
    echo "<td>".$diff."</td>" ;
    echo "</tr>" ;
}
?>