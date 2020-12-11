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

class	DataMinerTexte	extends	DataMiner	{

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
	function	setId( $_id=-1) {
		
	}
	/**
	 * getTableCuInvcForCuOrdr
	 *
	 * returns the table of all customer invoices
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableDepAsXML( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "Name", "var") ;
		$myObj->addCol( "Sprache", "var") ;
		$addCond	=	"" ;
		if ( $_POST['_ISearch'] != "") {
			$addCond	=	"AND ( C.Name like '%" . $_POST['_ISearch'] . "%' OR C.Name like '%" . $_POST['_ISearch'] . "%') " ;
		}
		$ret	=	$myObj->tableFromDb( "  ",
								" ",
								"TRUE " . $addCond,
								"ORDER BY C.Name ASC ",
								"ResultSet",
								"Texte",
								"C.Id, C.Name, C.Sprache ") ;
		error_log( $ret) ;
		return $ret ;
	}
}

?>
