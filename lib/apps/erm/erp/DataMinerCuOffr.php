<?php

/**
 * DataMinesKunde.php - Class to gather data related to an Customer
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */

/**
 * requires mostly platform stuff
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "DataMiner.php" );

/**
 * DataMiner - User-Level Klasse
 *
 * This class acts as an interface for data-mining in general.
 * The following core methods are provided.
 *
 *
 * @package Application
 * @subpackage DataMiner
 */

class	DataMinerCuOffr	extends	DataMiner	{

	function	__construct() {
		DataMiner::__construct() ;
		return $this->valid ;
	}

	function	getTableCuOffrPos( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuOffrNo", "var") ;
		$myObj->addCol( "PosNr", "var") ;
		$myObj->addCol( "ArtikelNr", "var") ;
		$myObj->addCol( "DiscountMode", "var") ;
		$myObj->addCol( "Rabatt", "var") ;
		$myObj->addCol( "Menge", "var") ;
		$myObj->addCol( "Preis", "var") ;
		$myObj->addCol( "LineTotal", "var") ;
		$myObj->addCol( "LiefArtNr", "var") ;
		$myObj->addCol( "EK_Preis", "var") ;
		$myObj->addCol( "Waehrung", "var") ;
		$myObj->addCol( "TotalEK", "var") ;		
		$myObj->addCol( "TotalMarge", "var") ;		
		$ret	=	$myObj->tableFromDb( ", KdA.DiscountMode, KdA.Rabatt, ROUND( C.Menge*C.Preis*(100.0-KdA.Rabatt)/100.0, 2) AS LineTotal, EKPR.LiefArtNr, AEP.Preis AS EK_Preis, ROUND(Curr.VonKurs/Curr.NachKurs,2) AS Waehrung, ROUND((C.Menge*AEP.Preis/Curr.VonKurs)*Curr.NachKurs,2) AS TotalEK, ROUND((C.Menge*C.Preis*(100.0-KdA.Rabatt)/100.0)-((C.Menge*AEP.Preis/Curr.VonKurs)*Curr.NachKurs),2) AS TotalMarge ",
								"JOIN CuOffr AS KdA ON KdA.CuOffrNo = C.CuOffrNo ".
									"JOIN EKPreisR AS EKPR ON EKPR.ArtikelNr = C.ArtikelNr AND EKPR.KalkBasis > 0 ".
									"JOIN ArtikelEKPreis AS AEP ON AEP.LiefArtNr = EKPR.LiefArtNr AND AEP.LiefNr = EKPR.LiefNr ".
									"JOIN Currency AS Curr ON Curr.VonWaehrung = AEP.Waehrung AND Curr.NachWaehrung=\"EUR\" AND Curr.CGueltigVon <= CURDATE() AND CURDATE() <= Curr.CGueltigBis ",
								"C.CuOffrNo = '" . $this->key . "' ",
								"ORDER BY C.PosNr ASC ",
								"ResultSet",
								"CuOffrItem",
								"C.CuOffrNo, C.ArtikelNr, C.PosNr, C.Menge, C.Preis ") ;
		error_log( $ret) ;
		return $ret ;
	}

}

?>
