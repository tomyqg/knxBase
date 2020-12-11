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

class	DataMinerSupp	extends	DataMiner	{

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
	 * getTableSuppAll
	 * 
	 * returns the table of all customer order lines which 
	 */
	function	getTableSuppliers( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "LiefNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "LiefPrefix", "var") ;
		$myObj->addCol( "Marge", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.LiefNr like '%' ",
								"ORDER BY C.LiefNr ",
								"ResultSet",
								"Lief",
								"C.LiefNr, C.FirmaName1, C.LiefPrefix AS 'LiefPrefix', C.Marge ") ;
		error_log( $ret) ;
		return $ret ;
	}
	
	/**
	 * getTableSuppAll
	 * 
	 * returns the table of all customer order lines which 
	 */
	function	getTableSuppliersByPrefix( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "LiefNr", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "LiefPrefix", "var") ;
		$myObj->addCol( "Marge", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.LiefNr like '%' ",
								"ORDER BY C.LiefPrefix, C.FirmaName1 ",
								"ResultSet",
								"Lief",
								"C.LiefNr, C.FirmaName1, C.LiefPrefix AS 'LiefPrefix', C.Marge ") ;
		error_log( $ret) ;
		return $ret ;
	}
	
	/**
	 * getTableCuInvcForArtikel
	 * 
	 * returns the table of all customer order lines which 
	 */
	function	getTableSuOrdrForSupp( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "SuOrdrNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "LiefNr", "var") ;
		$myObj->addCol( "LiefKontaktNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.LiefNr = '" . $this->key . "' ",
								"ORDER BY C.LiefNr ",
								"ResultSet",
								"SuOrdr",
								"C.SuOrdrNo,C.Datum,C.LiefNr,C.LiefKontaktNr") ;
//		error_log( $ret) ;
		return $ret ;
	}
	
	/**
	 * getTableCuInvcForArtikel
	 * 
	 * returns the table of all customer order lines which 
	 */
	function	getTableSuDlvrForSupp( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "SuDlvrNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "LiefNr", "var") ;
		$myObj->addCol( "LiefKontaktNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.LiefNr = '" . $this->key . "' ",
								"ORDER BY C.LiefNr ",
								"ResultSet",
								"SuDlvr",
								"C.SuDlvrNo,C.Datum,C.LiefNr,C.LiefKontaktNr") ;
		error_log( $ret) ;
		return $ret ;
	}
}

?>
