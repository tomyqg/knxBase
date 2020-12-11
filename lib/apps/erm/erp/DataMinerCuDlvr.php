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

class	DataMinerCuDlvr	extends	DataMiner	{

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
	 * getTableCuInvcForKunde
	 *
	 * returns the table of all customer invoices
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableVeColiPosForCuDlvr( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "VeColiNr", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "RefNr", "var") ;
		$myObj->addCol( "DPD_TrckNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								"LEFT JOIN VeColiPosDPD AS VeCP on VeCP.VeColiNr = C.VeColiNr",
								"C.RefNr = '" . $_key . "' ",
								"ORDER BY VeCP.PosNr ",
								"ResultSet",
								"VeColi",
								"C.VeColiNr,C.Datum,C.RefNr,VeCP.PosNr,VeCP.DPD_TrckNr ") ;
		error_log( $ret) ;
		return $ret ;
	}
}

?>
