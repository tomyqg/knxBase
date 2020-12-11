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

class	DataMinerCuOrdr	extends	DataMiner	{

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
	 * getTableCuCommForCuOrdr
	 *
	 * returns the table of all customer invoices
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableCuCommForCuOrdr( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuCommNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.CuOrdrNo = '" . $this->key . "' ",
								"ORDER BY C.CuCommNo DESC ",
								"ResultSet",
								"CuComm",
								"C.CuCommNo,C.Datum") ;
		error_log( $ret) ;
		return $ret ;
	}

	/**
	 * getTableCuDlvrForCuOrdr
	 *
	 * returns the table of all customer invoices
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableCuDlvrForCuOrdr( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuDlvrNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.CuOrdrNo = '" . $this->key . "' ",
								"ORDER BY C.CuDlvrNo DESC ",
								"ResultSet",
								"CuDlvr",
								"C.CuDlvrNo,C.Datum") ;
		error_log( $ret) ;
		return $ret ;
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
	function	getTableCuInvcForCuOrdr( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuInvcNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.CuOrdrNo = '" . $this->key . "' ",
								"ORDER BY C.CuInvcNo DESC ",
								"ResultSet",
								"CuInvc",
								"C.CuInvcNo,C.Datum") ;
		error_log( $ret) ;
		return $ret ;
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
		$myObj->addCol( "CuOrdrNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "Location", "var") ;
		$myObj->addCol( "Contact", "var") ;
		$addCond	=	"" ;
//		if ( $_POST['_ISearch'] != "") {
//			$addCond	=	"AND ( K.FirmaName1 like '%" . $_POST['_ISearch'] . "%' OR KK.Name like '%" . $_POST['_ISearch'] . "%') " ;
//		}
		$ret	=	$myObj->tableFromDb( ",K.FirmaName1, concat( K.PLZ, ' ', K.Ort) AS Location, concat( KK.Anrede, ' ', KK.Vorname, ' ', KK.Name) AS Contact ",
								"LEFT JOIN Kunde AS K ON K.KundeNr = C.KundeNr " .
									"LEFT JOIN KundeKontakt AS KK ON KK.KundeNr = C.KundeNr AND KK.KundeKontaktNr = C.KundeKontaktNr ",
								"C.Status < 90 " . $addCond,
								"ORDER BY C.CuOrdrNo DESC ",
								"ResultSet",
								"CuOrdr",
								"C.CuOrdrNo, C.Datum, K.FirmaName1 ") ;
		error_log( $ret) ;
		return $ret ;
	}
	
	static	function	_getTableCuOrdrComm( $_statComm) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuOrdrNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "StatComm", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$ret	=	$myObj->tableFromDb( ",K.FirmaName1 ",
								"JOIN Kunde AS K ON K.KundeNr = C.KundeNr ",
								"C.StatComm ".$_statComm." AND C.Status < 90 ",
								"ORDER BY C.CuOrdrNo DESC ",
								"ResultSet",
								"CuOrdr",
								"C.CuOrdrNo, C.Datum, K.FirmaName1, C.StatComm") ;
		error_log( $ret) ;
		return $ret ;
	}

	function	getTableCuOrdrComm( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		return DataMinerCuOrdr::_getTableCuOrdrComm( " > 0 ") ;
	}

	function	getTableCuOrdrCommPart( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		return DataMinerCuOrdr::_getTableCuOrdrComm( " = 10 ") ;
	}

	function	getTableCuOrdrCommFull( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		return DataMinerCuOrdr::_getTableCuOrdrComm( " = 20 ") ;
	}
}

?>
