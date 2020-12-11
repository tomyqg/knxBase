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

class	DataMinerSuDlvr	extends	DataMiner	{

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
	 * getTableSuDlvrForKunde
	 *
	 * returns the table of all customer invoices
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableSuDlvrOpen( $_key, $_id, $_val) {
		$_POST['_step']	=	$_key ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "SuDlvrNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "LiefNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$ret	=	$myObj->tableFromDb( ",S.FirmaName1 ",
								"LEFT JOIN Lief AS S ON S.LiefNr = C.LiefNr ",
								"C.Status < 90 ",
								"ORDER BY C.SuDlvrNo ASC ",
								"ResultSet",
								"SuDlvr",
								"C.SuDlvrNo,C.Datum,C.LiefNr") ;
		error_log( $ret) ;
		return $ret ;
			}
}

?>
