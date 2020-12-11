<?php

/**
 * DataMinesLief.php - Class to gather data related to a Supplier
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

class	DataMinerAbKorr	extends	DataMiner	{

	/**
	 * Erzeugung eines Objektes.
	 * 
	 * Erzeugt ein Artikel-Objekt und versucht ggf. diesen Artikel aus der Db zu laden.
	 *
	 * @param string $_artikelNr='' Artikelnummer
	 * @return void
	 */
	function	__construct() {
		DataMiner::__construct() ;
		return $this->valid ;
	}

	/**
	 * getTableAbKorrAll
	 * 
	 * returns the table of all customer order lines which 
	 */
	function	getTableAll( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "AbKorrNr", "var") ;
		$myObj->addCol( "Description", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.AbKorrNr like '%' ",
								"ORDER BY C.AbKorrNr DESC ",
								"ResultSet",
								"AbKorr",
								"C.Id, C.AbKorrNr, C.Description ") ;
		error_log( $ret) ;
		return $ret ;
	}
}

?>
