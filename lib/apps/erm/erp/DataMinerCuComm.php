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

class	DataMinerCuComm	extends	DataMiner	{

	/**
	 * __construct
	 * 
	 * Creates a dataminer object
	 *
	 * @return void
	 */
	function	__construct() {
		DataMiner::__construct() ;
		return $this->valid ;
	}

	/**
	 * getTableCuCommForKunde
	 *
	 * returns the table of all customer invoices
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableDepAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuCommNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$ret	=	$myObj->tableFromDb( ",K.FirmaName1 ",
								"LEFT JOIN Kunde AS K on K.KundeNr = C.KundeNr",
								"C.Status < 90 ",
								"ORDER BY C.CuCommNo ",
								"ResultSet",
								"CuComm",
								"C.CuCommNo,C.Datum,C.KundeNr,C.KundeKontaktNr") ;
		error_log( $ret) ;
		return $ret ;
	}
}

?>