<?php

/**
 * Created by PhpStorm.
 * User: khwelter
 * Date: 17.05.2017
 * Time: 10:42
 */
include_once(dirname(__file__) . "/../classes/system/EISSCoreObject.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FException.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDbg.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDb.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FDbObject.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FSqlQuery.class.inc.php");
include_once(dirname(__file__) . "/../classes/system/FSqlMySQLQuery.class.inc.php");
include_once(dirname(__file__) . "/../classes/khClasses.php");

FDbg::enable() ;
FDbg::setLevel( 99) ;
FDbg::enable() ;
//    FDbg::setAppToTrace( "kunden") ;
FDbg::setApp( $_GET["module"] . "::" . $_GET["op"]) ;
FDbg::setFileListExclude( ",EISSCoreObject.class.inc.php,FDb.class.inc.php,FDbObject.class.inc.php,FSqlQuery.class.inc.php,FSqlMySQLQuery.class.inc.php,dbwrapper.class.inc.php,") ;
//FDbg::setFileListExclude( "") ;
FDbg::setModuleListExclude( "") ;
FDbg::setMethodListExclude( "") ;
FDbg::trace( 0, "", "", "", "+------------------------------------------------------------------------------------------------------------") ;
FDbg::trace( 0, "", "", "", " starting tracing subsystem for URL := " . $_SERVER[ 'REQUEST_URI']) ;
FDbg::traceArray( 0, "", "", "", "REQUEST variables in request", $_REQUEST) ;
//FDb::registerDb( KUN_DBHOST, KUN_DBUSER, KUN_DBPASS, KUN_DB_NAME, "kun", "mysql") ;
FDb::registerDb( "localhost:7188", "root", "", "optica_projekt", "kun", "mysql") ;
//FDb::registerDb( CLOUD_DBHOST, CLOUD_DBUSER, CLOUD_DBPASS, CLOUD_DB_NAME, "cloud", "mysql") ;
FDb::registerDb( "localhost:7188", "root", "", "od_system", "od", "mysql") ;

if ( isset( $_POST[ "func"]) && $_POST[ "func"] != "") {
    $func   =   $_POST[ "func"] ;
    $LEIKNr =   $_POST[ "LEIKNr"] ;
    $KTIKNr =   $_POST[ "KTIKNr"] ;
    $HMVNr  =   $_POST[ "HMVNummer"] ;
} else {
    $func   =   "none" ;
    $LEIKNr =   "340103059" ;
    $KTIKNr =   "101575519" ;
    $HMVNr  =   "0803021000" ;
}

$HMVNr  =   str_replace( "%", "", $HMVNr) ;

if ( strlen( $HMVNr) < 10) {
    $HMVNr  .=  "%" ;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
    <title>Vertragsabfrage</title>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
</head>
<body>
<form action="vertragsabfrage.php" method="post">
    <label for="LEIKNr">Leistungserbringer IK#</label>
        <input type="text" name="LEIKNr" value="<?php echo $LEIKNr ; ?>" /><br/>
    <label for="KTIKNr">Kostenträger IK#</label>
        <input type="text" name="KTIKNr" value="<?php echo $KTIKNr ; ?>" /><br/>
    <label for="Leistungskennziffer">Leistungskennziffer</label>
        <input type="text" name="Leistungskennziffer" value="00" /><br/>
    <label for="HMVNummer">Hilfsmittelnummer</label>
        <input type="text" name="HMVNummer" value="<?php echo $HMVNr ; ?>" /><br/>
    <label for="Datum">Datum</label><input type="text" name="Datum" value="2017-05-17" /><br/>
    <input type="submit" name="func" value="f1">Kostentraeger Daten {KT-IK}</input><br/>
    <input type="submit" name="func" value="f2">Leistungserbringer Vertraege {LE-IK}</input><br/>
    <input type="submit" name="func" value="f6">Vertrag {LE-IK, KT-IK}</input><br/>
    <input type="submit" name="func" value="f3">Preis {LE-IK, KT-IK, HMV#}</input><br/>
    <input type="submit" name="func" value="f4">Vertrag {HMV#}</input><br/>
    <input type="submit" name="func" value="f5">Vertrag {KT-IK}</input><br/>
</form>

<h1>Ergebnis der Abfrage</h1>
<table>
    <tbody>
<?php

switch ( $func) {
case    "f1"    :
    f1( $KTIKNr) ;
    break ;
case    "f2"    :
    f2( $LEIKNr) ;
    break ;
case    "f3"    :
    f3( $LEIKNr, $KTIKNr, $HMVNr) ;
    break ;
case    "f4"    :
    break ;
case    "f5"    :
    break ;
case    "f6"    :
    f6( $LEIKNr, $KTIKNr, $HMVNr) ;
    break ;
}

?>
</tbody>
</table>

</body>
</html>

<?php

function    f1( $_KTIKNr) {
    _showKT( $_KTIKNr) ;
}

/**
 * Vertraege des Leistungserbringers
 *
 * @param $_myKrankenkasse
 * @param $_LEIKNr
 */
function    f2( $_LEIKNr) {

    $myFiliale      =   new _Filiale() ;
    $myFiliale->getByLEIKNr( $_LEIKNr) ;

    /**
     * Verträge an denen der Leistungserbringer beteiligt ist
     */
    $myVertragR=new _VertragR();
    $myVertragLEGSData=new _VertragLEGSData();
    echo "<h2>Verträge des Leistungserbringers: {$_LEIKNr}, {$myFiliale->Name1}</h2>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<td rowspan='2'>Preis Id.</td><td rowspan='2'>LE-IK Nr.</td><td rowspan='2'>LEGS</td><td rowspan='2'>letzte Aktualisierung</td><td colspan='2'>Gueltigkeit</td><td>Kassenart</td><td>Ersatzkassenart</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>von</td><td>bis</td><td colspan='3'>aus: preislink_new</td>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    $myVertragR->setIterCond(array("ip_ik = {$_LEIKNr}"));
    foreach($myVertragR as $vertragR){
        $myVertragLEGSData->getByContractNo( $myVertragR->ip_preisid) ;
        echo "<tr>";
        echo "<td>{$vertragR->ip_preisid}</td>" ;
        echo "<td>{$vertragR->ip_ik}</td><td>{$myVertragLEGSData->prl_legs}</td><td>{$vertragR->ip_updatum}</td>";
        echo "<td>{$myVertragLEGSData->prl_gueltig_ab}</td><td>{$myVertragLEGSData->prl_gueltig_bis}</td>";
        echo "<td>{$myVertragLEGSData->prl_kassenart}</td><td>{$myVertragLEGSData->prl_ekkart}</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

/**
 * Vertraege des Leistungserbringers mit dem KOstenträger
 *
 * @param $_myKrankenkasse
 * @param $_LEIKNr
 */
function    f6( $_LEIKNr, $_KTIKNr, $_HMVNr) {

    $myKrankenkasse =   _showKT( $_KTIKNr);
    if( $myKrankenkasse->isValid()){

        /**
         *
         */
        $myFiliale=new _Filiale();
        $myFiliale->getByLEIKNr($_LEIKNr);

        /**
         * Verträge an denen der Leistungserbringer beteiligt ist
         */
        $myVertragR         =   new _VertragR();
        $myVertrag          =   new _Vertrag();
        $myVertragLEGSData  =   new _VertragLEGSData();
		$myVertragHMVData   =   new _VertragHMVData();

        /**
         *
         */
        echo "<h2>Allgemeingültige Verträge des Leistungserbringers: {$_LEIKNr}, {$myFiliale->Name1}</h2>";
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<td rowspan='2'>Preis Id.</td><td rowspan='2'>LEGS</td><td rowspan='2'>LE-IK Nr.</td><td rowspan='2'>letzte Aktualisierung</td><td colspan='2'>Gueltigkeit</td><td rowspan='2'>Kassenart</td><td rowspan='2'>Ersatzkassenart</td><td rowspan='2'>Prio.</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>von</td><td>bis</td>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        $myVertragR->setIterCond(array("ip_ik = {$_LEIKNr}"));
        foreach($myVertragR as $vertragR){
            $myVertrag->clearIterCond();
            $myVertrag->clearIterOrder();
            $myVertragLEGSData->getByContractNo($myVertragR->ip_preisid);
            $myVertrag->setIterCond( array(
                    "pk_preisid = {$vertragR->ip_preisid}"
                ,   "(pk_kassenart_angeschlossen = 'RVO' OR pk_tarifbereich_angeschlossen = '17')"
                )) ;
            $i=0;
            foreach($myVertrag as $vertrag){
                echo "<tr>";
                echo "<td>{$vertragR->ip_preisid}</td>";
                echo "<td>{$myVertragLEGSData->prl_legs}</td><td>{$vertragR->ip_ik}</td><td>{$vertragR->ip_updatum}</td>";
                echo "<td>{$myVertragLEGSData->prl_gueltig_ab}</td><td>{$myVertragLEGSData->prl_gueltig_bis}</td>";
                echo "<td>{$myVertragLEGSData->prl_kassenart}</td><td>{$myVertragLEGSData->prl_ekkart}</td><td>{$myVertragLEGSData->prl_prioritaet}</td>";
                echo "</tr>";
                $i++ ;
            }
			$myVertragLEGSData->clearIterCond();
			$myVertragLEGSData->clearIterOrder();
			$myVertragLEGSData->setIterCond( array(
				"prl_preisid = {$vertragR->ip_preisid}"
			,   "(prl_kassenart = 'RVO')"
			)) ;
			$i=0;
			foreach($myVertragLEGSData as $vertrag){
				echo "<tr>";
				echo "<td>{$vertragR->ip_preisid}</td>";
				echo "<td>{$vertrag->prl_legs}</td><td>{$vertragR->ip_ik}</td><td>{$vertragR->ip_updatum}</td>";
				echo "<td>{$vertrag->prl_gueltig_ab}</td><td>{$vertrag->prl_gueltig_bis}</td>";
				echo "<td>{$vertrag->prl_kassenart}</td><td>{$vertrag->prl_ekkart}</td><td>{$vertrag->prl_prioritaet}</td>";
				echo "</tr>";
				echo "</tr>";
				$myVertragHMVData->clearIterCond() ;
				$myVertragHMVData->clearIterOrder() ;
				$myVertragHMVData->setIterCond( array( "pl_preisId = '{$vertragR->ip_preisid}'", "( pl_legs = '' OR pl_legs = '{$vertrag->prl_legs}')", "pl_hmv like '{$_HMVNr}'")) ;
				foreach( $myVertragHMVData as $vertragHMVData) {
					echo "<tr><td colspan='9'></td>" ;
					echo "<td>{$vertragHMVData->pl_hmv}</td>";
					echo "<td>{$vertragHMVData->pl_preis_primaerkassen}</td>";
					echo "<td>{$vertragHMVData->pl_preis_ersatzkassen}</td>";
					echo "</tr>";
				}
				$i++ ;
			}
        }
        echo "</tbody>";
        echo "</table>";

        /**
         *
         */
        if ( $myKrankenkasse->kl_kassenart != "EKK") {
            echo "<h2>Verträge des Leistungserbringers {$_LEIKNr}, {$myFiliale->Name1} per Kassenart:</h2>";
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<td rowspan='2'>Preis Id.</td><td rowspan='2'>LEGS</td><td rowspan='2'>LE-IK Nr.</td><td rowspan='2'>letzte Aktualisierung</td><td colspan='2'>Gueltigkeit</td><td rowspan='2'>Kassenart</td><td rowspan='2'>Ersatzkassenart</td><td rowspan='2'>Prio.</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>von</td><td>bis</td>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            $myVertragR->clearIterCond();
            $myVertragR->clearIterOrder();
            $myVertragR->setIterCond(array("ip_ik = {$_LEIKNr}"));
            foreach($myVertragR as $vertragR){
                $myVertrag->clearIterCond();
                $myVertrag->clearIterOrder();
                $myVertragLEGSData->getByContractNo($myVertragR->ip_preisid);
                $myVertrag->setIterCond( array(
                        "pk_preisid = {$vertragR->ip_preisid}"
                    ,   "( pk_kassenart_angeschlossen = '{$myKrankenkasse->kl_kassenart}' AND pk_ekkart_angeschlossen = '' AND ( pk_tarifbereich_angeschlossen = '{$myKrankenkasse->kl_tarifbereich}' OR pk_tarifbereich_angeschlossen = '17'))"
                    )) ;
                $i=0;
                foreach($myVertrag as $vertrag){
                    echo "<tr>";
                    echo "<td>{$vertragR->ip_preisid}</td>";
                    echo "<td>{$myVertragLEGSData->prl_legs}</td><td>{$vertragR->ip_ik}</td><td>{$vertragR->ip_updatum}</td>";
                    echo "<td>{$myVertragLEGSData->prl_gueltig_ab}</td><td>{$myVertragLEGSData->prl_gueltig_bis}</td>";
                    echo "<td>{$myVertragLEGSData->prl_kassenart}</td><td>{$myVertragLEGSData->prl_ekkart}</td><td>{$myVertragLEGSData->prl_prioritaet}</td>";
                    echo "<td>{$vertrag->pk_kassenart_angeschlossen}</td><td>{$vertrag->pk_ekkart_angeschlossen}</td><td>{$vertrag->pk_kassenik_angeschlossen}</td><td>{$vertrag->pk_tarifbereich_angeschlossen}</td>";
                    echo "<td>{$vertrag->pk_gueltig_ab}</td><td>{$vertrag->pk_gueltig_bis}</td>";
                    echo "</tr>";
                    $myVertragHMVData->clearIterCond() ;
                    $myVertragHMVData->clearIterOrder() ;
                    $myVertragHMVData->setIterCond( array( "pl_preisId = '{$vertragR->ip_preisid}'", "( pl_legs = '' OR pl_legs = '{$myVertragLEGSData->prl_legs}')", "pl_hmv like '{$_HMVNr}'")) ;
                    foreach( $myVertragHMVData as $vertragHMVData) {
                        echo "<tr><td colspan='15'></td>" ;
						echo "<td>{$vertragHMVData->pl_hmv}</td>";
                        echo "<td>{$vertragHMVData->pl_preis_primaerkassen}</td>";
                        echo "<td>{$vertragHMVData->pl_preis_ersatzkassen}</td>";
                        echo "</tr>";
                    }
                    $i++ ;
                }
            }
            echo "</tbody>";
            echo "</table>";
		} else {

            /**
             *
             */
            echo "<h2>Verträge des Leistungserbringers {$_LEIKNr}, {$myFiliale->Name1} per (Ersatz-)Kassenart:</h2>";
            echo "<table>";
            echo "<thead>";
            echo "<tr>";
            echo "<td rowspan='2'>Preis Id.</td><td rowspan='2'>LEGS</td><td rowspan='2'>LE-IK Nr.</td><td rowspan='2'>letzte Aktualisierung</td><td colspan='2'>Gueltigkeit</td><td rowspan='2'>Kassenart</td><td rowspan='2'>Ersatzkassenart</td><td rowspan='2'>Prio.</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td>von</td><td>bis</td>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            $myVertragR->clearIterCond();
            $myVertragR->clearIterOrder();
            $myVertragR->setIterCond(array("ip_ik = {$_LEIKNr}"));
            foreach($myVertragR as $vertragR){
                $myVertrag->clearIterCond();
                $myVertrag->clearIterOrder();
                $myVertragLEGSData->getByContractNo($myVertragR->ip_preisid);
                $myVertrag->setIterCond( array(
                    "pk_preisid = {$vertragR->ip_preisid}"
                ,   "( pk_kassenart_angeschlossen = '{$myKrankenkasse->kl_kassenart}' AND pk_ekkart_angeschlossen = '{$myKrankenkasse->kl_ekkart}' AND ( pk_tarifbereich_angeschlossen = '{$myKrankenkasse->kl_tarifbereich}' OR pk_tarifbereich_angeschlossen = '17'))"
                )) ;
                $i=0;
                foreach($myVertrag as $vertrag){
                    echo "<tr>";
                    echo "<td>{$vertragR->ip_preisid}</td>";
                    echo "<td>{$myVertragLEGSData->prl_legs}</td><td>{$vertragR->ip_ik}</td><td>{$vertragR->ip_updatum}</td>";
                    echo "<td>{$myVertragLEGSData->prl_gueltig_ab}</td><td>{$myVertragLEGSData->prl_gueltig_bis}</td>";
                    echo "<td>{$myVertragLEGSData->prl_kassenart}</td><td>{$myVertragLEGSData->prl_ekkart}</td><td>{$myVertragLEGSData->prl_prioritaet}</td>";
                    echo "<td>{$vertrag->pk_kassenart_angeschlossen}</td><td>{$vertrag->pk_ekkart_angeschlossen}</td><td>{$vertrag->pk_kassenik_angeschlossen}</td><td>{$vertrag->pk_tarifbereich_angeschlossen}</td>";
                    echo "<td>{$vertrag->pk_gueltig_ab}</td><td>{$vertrag->pk_gueltig_bis}</td>";
                    echo "</tr>";
                    $myVertragHMVData->clearIterCond() ;
                    $myVertragHMVData->clearIterOrder() ;
                    $myVertragHMVData->setIterCond( array( "pl_preisId = '{$vertragR->ip_preisid}'", "( pl_legs = '' OR pl_legs = '{$myVertragLEGSData->prl_legs}')", "pl_hmv like '{$_HMVNr}'")) ;
                    foreach( $myVertragHMVData as $vertragHMVData) {
                        echo "<tr><td colspan='15'></td>" ;
						echo "<td>{$vertragHMVData->pl_hmv}</td>";
                        echo "<td>{$vertragHMVData->pl_preis_primaerkassen}</td>";
                        echo "<td>{$vertragHMVData->pl_preis_ersatzkassen}</td>";
                        echo "</tr>";
                    }
                    $i++ ;
                }
            }
            echo "</tbody>";
            echo "</table>";
		}


		/**
         *
         */
        echo "<h2>Verträge des Leistungserbringers {$_LEIKNr}, {$myFiliale->Name1} per (Ersatz-)Kassenart und Tarifbereich des Leistungsempfaengers</h2>";
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<td rowspan='2'>Preis Id.</td><td rowspan='2'>LEGS</td><td rowspan='2'>LE-IK Nr.</td><td rowspan='2'>letzte Aktualisierung</td><td colspan='2'>Gueltigkeit</td><td rowspan='2'>Kassenart</td><td rowspan='2'>Ersatzkassenart</td><td rowspan='2'>Prio.</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>von</td><td>bis</td>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
		$myVertragR->clearIterCond();
		$myVertragR->clearIterOrder();
        $myVertragR->setIterCond(array("ip_ik = {$_LEIKNr}"));
        foreach($myVertragR as $vertragR){
            $myVertrag->clearIterCond();
            $myVertrag->clearIterOrder();
            $myVertragLEGSData->getByContractNo($myVertragR->ip_preisid);
            $myVertrag->setIterCond( array(
                "pk_preisid = {$vertragR->ip_preisid}"
            ,   "( pk_kassenart_angeschlossen = '{$myKrankenkasse->kl_kassenart}' AND pk_ekkart_angeschlossen = '{$myKrankenkasse->kl_ekkart}' AND ( pk_tarifbereich_angeschlossen = '{$myKrankenkasse->kl_tarifbereich}' OR pk_tarifbereich_angeschlossen = '17'))"
            )) ;
            $i=0;
            foreach($myVertrag as $vertrag){
                echo "<tr>";
                echo "<td>{$vertragR->ip_preisid}</td>";
                echo "<td>{$myVertragLEGSData->prl_legs}</td><td>{$vertragR->ip_ik}</td><td>{$vertragR->ip_updatum}</td>";
                echo "<td>{$myVertragLEGSData->prl_gueltig_ab}</td><td>{$myVertragLEGSData->prl_gueltig_bis}</td>";
                echo "<td>{$myVertragLEGSData->prl_kassenart}</td><td>{$myVertragLEGSData->prl_ekkart}</td><td>{$myVertragLEGSData->prl_prioritaet}</td>";
                echo "<td>{$vertrag->pk_kassenart_angeschlossen}</td><td>{$vertrag->pk_ekkart_angeschlossen}</td><td>{$vertrag->pk_kassenik_angeschlossen}</td><td>{$vertrag->pk_tarifbereich_angeschlossen}</td>";
                echo "<td>{$vertrag->pk_gueltig_ab}</td><td>{$vertrag->pk_gueltig_bis}</td>";
				$myVertragHMVData->clearIterCond() ;
				$myVertragHMVData->clearIterOrder() ;
				$myVertragHMVData->setIterCond( array( "pl_preisId = '{$vertragR->ip_preisid}'", "( pl_legs = '' OR pl_legs = '{$myVertragLEGSData->prl_legs}')", "pl_hmv like '{$_HMVNr}'")) ;
				foreach( $myVertragHMVData as $vertragHMVData) {
					echo "<tr><td colspan='15'></td>" ;
					echo "<td>{$vertragHMVData->pl_hmv}</td>";
					echo "<td>{$vertragHMVData->pl_preis_primaerkassen}</td>";
					echo "<td>{$vertragHMVData->pl_preis_ersatzkassen}</td>";
					echo "</tr>";
				}
                echo "</tr>";
                $i++ ;
            }
        }
        echo "</tbody>";
        echo "</table>";

        /**
         *
         */
        echo "<h2>Verträge des Leistungserbringers {$_LEIKNr}, {$myFiliale->Name1} per KT IK#:</h2>";
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<td rowspan='2'>Preis Id.</td><td rowspan='2'>LEGS</td><td rowspan='2'>LE-IK Nr.</td><td rowspan='2'>letzte Aktualisierung</td><td colspan='2'>Gueltigkeit</td><td rowspan='2'>Kassenart</td><td rowspan='2'>Ersatzkassenart</td><td rowspan='2'>Prio.</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>von</td><td>bis</td>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
		$myVertragR->clearIterCond();
		$myVertragR->clearIterOrder();
        $myVertragR->setIterCond(array("ip_ik = {$_LEIKNr}"));
        foreach($myVertragR as $vertragR){
            $myVertrag->clearIterCond();
            $myVertrag->clearIterOrder();
            $myVertragLEGSData->getByContractNo($myVertragR->ip_preisid);
            $myVertrag->setIterCond( array(
                    "pk_preisid = {$vertragR->ip_preisid}"
                ,   "pk_kassenik_angeschlossen = '{$myKrankenkasse->kl_kvkik}'"
                )) ;
            $i=0;
            foreach($myVertrag as $vertrag){
                echo "<tr>";
                echo "<td>{$vertragR->ip_preisid}</td>";
                echo "<td>{$myVertragLEGSData->prl_legs}</td><td>{$vertragR->ip_ik}</td><td>{$vertragR->ip_updatum}</td>";
                echo "<td>{$myVertragLEGSData->prl_gueltig_ab}</td><td>{$myVertragLEGSData->prl_gueltig_bis}</td>";
                echo "<td>{$myVertragLEGSData->prl_kassenart}</td><td>{$myVertragLEGSData->prl_ekkart}</td><td>{$myVertragLEGSData->prl_prioritaet}</td>";
                echo "<td>{$vertrag->pk_kassenart_angeschlossen}</td><td>{$vertrag->pk_ekkart_angeschlossen}</td><td>{$vertrag->pk_kassenik_angeschlossen}</td>";
                echo "</tr>";
				$myVertragHMVData->clearIterCond() ;
				$myVertragHMVData->clearIterOrder() ;
				$myVertragHMVData->setIterCond( array( "pl_preisId = '{$vertragR->ip_preisid}'", "( pl_legs = '' OR pl_legs = '{$myVertragLEGSData->prl_legs}')", "pl_hmv like '{$_HMVNr}'")) ;
				foreach( $myVertragHMVData as $vertragHMVData) {
					echo "<tr><td colspan='15'></td>" ;
					echo "<td>{$vertragHMVData->pl_hmv}</td>";
					echo "<td>{$vertragHMVData->pl_preis_primaerkassen}</td>";
					echo "<td>{$vertragHMVData->pl_preis_ersatzkassen}</td>";
					echo "</tr>";
				}
                $i++ ;
            }
        }
        echo "</tbody>";
        echo "</table>";

        /**
         *
         * View Definition
         *
DROP VIEW IF EXISTS v_Vertrag ;
CREATE VIEW v_Vertrag AS SELECT PKN.*, KGZ.kg_grpnr, KGZ.kg_grpnr2, KGZ.kg_grpnr3 FROM preislink_kassenarten_new AS PKN LEFT JOIN kassengruppen_zuordnung AS KGZ ON KGZ.kg_ik = PKN.pk_kassenik_angeschlossen ;
         */
		$myKrankenkasseGruppen  =   new _KrankenkasseGruppen( $_KTIKNr);
        echo "<h2>Verträge des Leistungserbringers per Kassengruppe {$myKrankenkasseGruppen->kg_grpnr2} von KT IK#: {$_LEIKNr}, {$myFiliale->Name1}</h2>";
        echo "<table>";

        echo "<thead>";
        echo "<tr>";
        echo "<td rowspan='2'>Preis Id.</td><td rowspan='2'>LEGS</td><td rowspan='2'>LE-IK Nr.</td><td rowspan='2'>letzte Aktualisierung</td><td colspan='2'>Gueltigkeit</td><td rowspan='2'>Kassenart</td><td rowspan='2'>Ersatzkassenart</td><td rowspan='2'>Prio.</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>von</td><td>bis</td>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        $myVertragView  =   new FDbObject( "vertrag_view", "pk_preisid", "od", "v_Vertrag") ;
		$myVertragR->clearIterCond();
		$myVertragR->clearIterOrder();
        $myVertragR->setIterCond(array("ip_ik = {$_LEIKNr}"));
        foreach ( $myVertragR as $vertragR){
			$myVertragView->clearIterCond();
			$myVertragView->clearIterOrder();
            $myVertragLEGSData->getByContractNo($myVertragR->ip_preisid);
			$myVertragView->setIterCond( array(
                "pk_preisid = {$vertragR->ip_preisid}"
            ,   "kg_grpnr = '{$myKrankenkasseGruppen->kg_grpnr}'"
            )) ;
            $i=0;
            foreach($myVertragView as $vertrag){
                echo "<tr>";
                echo "<td>{$vertragR->ip_preisid}</td>";
                echo "<td>{$myVertragLEGSData->prl_legs}</td><td>{$vertragR->ip_ik}</td><td>{$vertragR->ip_updatum}</td>";
                echo "<td>{$myVertragLEGSData->prl_gueltig_ab}</td><td>{$myVertragLEGSData->prl_gueltig_bis}</td>";
                echo "<td>{$myVertragLEGSData->prl_kassenart}</td><td>{$myVertragLEGSData->prl_ekkart}</td><td>{$myVertragLEGSData->prl_prioritaet}</td>";
                echo "<td>{$vertrag->pk_kassenart_angeschlossen}</td><td>{$vertrag->pk_ekkart_angeschlossen}</td><td>{$vertrag->pk_kassenik_angeschlossen}</td>";
                echo "</tr>";
				$myVertragHMVData->clearIterCond() ;
				$myVertragHMVData->clearIterOrder() ;
				$myVertragHMVData->setIterCond( array( "pl_preisId = '{$vertragR->ip_preisid}'", "( pl_legs = '' OR pl_legs = '{$myVertragLEGSData->prl_legs}')", "pl_hmv = '{$_HMVNr}'")) ;
				foreach( $myVertragHMVData as $vertragHMVData) {
					echo "<tr><td colspan='15'></td>" ;
					echo "<td>{$vertragHMVData->pl_hmv}</td>";
					echo "<td>{$vertragHMVData->pl_preis_primaerkassen}</td>";
					echo "<td>{$vertragHMVData->pl_preis_ersatzkassen}</td>";
					echo "</tr>";
				}
                $i++ ;
            }
        }
        echo "</tbody>";
        echo "</table>";
    }
}

function    f3( $_LEIKNr, $_KTIKNr, $_HMVNr) {
    $myKrankenkasse =   _showKT( $_KTIKNr);
    if( $myKrankenkasse->isValid()){

        /**
         * Verträge an denen der Leistungserbringer beteiligt ist
         */
        $myVertragR=new _VertragR();
        $myVertrag=new _Vertrag();
        $myVertragHMVData=new _VertragHMVData();
        $myVertragLEGSData=new _VertragLEGSData();
        echo "<h2>Verträge des Leistungserbringers: {$_LEIKNr}</h2>";
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<td>Preis Id.</td><td>LE-IK Nr.</td><td>letzte Aktualisierung</td><td>Gueltigkeit</td>";
        echo "<td>Kassenart</td><td>EK-Kassenart</td><td>KT-IK Nr.</td><td>Tarifbereich</td>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        $myVertragR->setIterCond(array("ip_ik = {$_LEIKNr}"));
        foreach($myVertragR as $vertragR){
            $myVertragLEGSData->setId($vertragR->ip_preisid);
            $myVertrag->clearIterCond();
            $myVertrag->clearIterOrder();
            $myVertrag->setIterCond( array(
                    "pk_preisid = {$vertragR->ip_preisid}",
                    "( ( pk_kassenart_angeschlossen = '{$myKrankenkasse->kl_kassenart}' AND pk_ekkart_angeschlossen = '') OR " .
                    "  ( pk_kassenart_angeschlossen = 'RVO') OR " .
                    "  ( pk_kassenart_angeschlossen = '{$myKrankenkasse->kl_kassenart}' AND pk_ekkart_angeschlossen = '{$myKrankenkasse->kl_ekkart}' ) OR " .
                    "  ( pk_kassenik_angeschlossen = '{$myKrankenkasse->kl_kvkik}' ) )"));
            $i=0;
            foreach($myVertrag as $vertrag){
                $myVertragHMVData->fetchFromDbWhere(array("pl_preisid = {$vertragR->ip_preisid}","pl_hmv = {$_HMVNr}"));
                echo "<tr>";
                if($i==0){
                    echo "<td>{$vertragR->ip_preisid}<br/>LEGS: {$myVertragLEGSData->prl_legs}</td><td>{$vertragR->ip_ik}</td><td>{$vertragR->ip_updatum}</td>";
                }else{
                    echo "<td colspan=\"3\"></td>";
                }
                echo "<td>{$vertrag->pk_gueltig_ab}-{$vertrag->pk_gueltig_bis}</td>";
                echo "<td>{$vertrag->pk_kassenart_angeschlossen}</td><td>{$vertrag->pk_ekkart_angeschlossen}</td><td>{$vertrag->pk_kassenik_angeschlossen}</td><td>{$vertrag->pk_tarifbereich_angeschlossen}</td><td>{$myVertragHMVData->pl_preis_primaerkassen}</td><td>{$myVertragHMVData->pl_preis_ersatzkassen}</td>";
                echo "</tr>";
                $i++;
            }
        }
        echo "</tbody>";
        echo "</table>";
    }else{
        echo "Keine Krankenkasse mit der IK# {$_POST[ "KTIKNr"]}<br/>";
    }
}

function    f4( $_HMV) {

}

function    f5( $_HMV) {

}

function    _showKT( $_KTIKNr) {
    $myKrankenkasse =   new _Krankenkasse($_KTIKNr);
    if( $myKrankenkasse->isValid()){

        /**
         *
         */
        $myKrankenkasseGruppen=new _KrankenkasseGruppen( $_KTIKNr);
        echo "<h2>Krankenkassendaten:</h2>";
        echo "<table style='border=1'><tbody>";
        echo "<tr><td>IK#</td><td>{$myKrankenkasse->kl_kvkik}</td></tr>";
        echo "<tr><td>Name</td><td>{$myKrankenkasse->Name}</td></tr>";
        echo "<tr><td>Tarifbereich</td><td>{$myKrankenkasse->kl_tarifbereich}</td></tr>";
        echo "<tr><td>Kassenart</td><td>{$myKrankenkasse->kl_kassenart} / {$myKrankenkasse->kl_ekkart}</td></tr>";
        if($myKrankenkasseGruppen->isValid()){
            echo "<tr><td>Gruppen Nr. 1</td><td>{$myKrankenkasseGruppen->kg_grpnr}</td></tr>";
            echo "<tr><td>Gruppen Nr. 2</td><td>{$myKrankenkasseGruppen->kg_grpnr2}</td></tr>";
            echo "<tr><td>Gruppen Nr. 3</td><td>{$myKrankenkasseGruppen->kg_grpnr3}</td></tr>";
        }else{
            echo "<tr>Keine Krankenkasse-Gruppen für diese Kasse gefunden!</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "Sorry, aber diese IK Nummer kenn' ich leider nich'." ;
    }
    return $myKrankenkasse ;
}

?>
