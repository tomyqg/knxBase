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

class	DataMinerKunde	extends	DataMiner	{

	/**
	 * __construct
	 * 
	 * Creates a dataminer object
	 *
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "KundeObj") ;
		return $this->valid ;
	}
	function	getTableDepAsXML( $_key, $_id, $_val) {
		FDbg::begin( 1, "DataMinerKunde.php", "DataMinerKunde", "getTableDepAsXML( '$_key', $_id, '$_val')") ;
		$this->KundeNr	=	"%" ;
		switch ( $_val) {
			case	"CuOrdr"	:
				$buf	=	$this->getTableCuOrdrForKunde( $_key, $_id, $_val) ;
				break ;
			case	"CuComm"	:
				$buf	=	$this->getTableCuCommForKunde( $_key, $_id, $_val) ;
				break ;
			case	"CuDlvr"	:
				$buf	=	$this->getTableCuDlvrForKunde( $_key, $_id, $_val) ;
				break ;
			case	"CuInvc"	:
				$buf	=	$this->getTableCuInvcForKunde( $_key, $_id, $_val) ;
				break ;
		}
		FDbg::end( 1, "DataMinerKunde.php", "DataMinerKunde", "getTableDepAsXML( '$_key', $_id, '$_val')") ;
		return $buf ;
	}
	/**
	 * getTableCuRFQForKunde
	 *
	 * returns the table of all customer RFQs
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableCuRFQForKunde( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuRFQNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.KundeNr like '%',	// " . $this->key . "' ",
								"ORDER BY C.KundeNr ",
								"ResultSet",
								"CuRFQ",
								"C.CuRFQNo,C.Datum,C.KundeNr,C.KundeKontaktNr") ;
		error_log( $ret) ;
		return $ret ;
	}

	/**
	 * getTableCuOffrForKunde
	 *
	 * returns the table of all customer offers
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableCuOffrForKunde( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuOffrNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.KundeNr = '" . $this->key . "' ",
								"ORDER BY C.KundeNr ",
								"ResultSet",
								"CuOffr",
								"C.CuOffrNo,C.Datum,C.KundeNr,C.KundeKontaktNr") ;
		error_log( $ret) ;
		return $ret ;
	}

	/**
	 * getTableCuOrdrForKunde
	 *
	 * returns the table of all customer offers
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableCuOrdrForKunde( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuOrdrNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.KundeNr = '" . $this->key . "' ",
								"ORDER BY C.Datum DESC ",
								"ResultSet",
								"CuOrdr",
								"C.CuOrdrNo,C.Datum,C.KundeNr,C.KundeKontaktNr") ;
		error_log( $ret) ;
		return $ret ;
	}

	/**
	 * getTableCuCommForKunde
	 *
	 * returns the table of all customer commissions
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableCuCommForKunde( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuCommNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.KundeNr = '" . $this->key . "' ",
								"ORDER BY C.Datum DESC ",
								"ResultSet",
								"CuComm",
								"C.CuCommNo,C.Datum,C.KundeNr,C.KundeKontaktNr") ;
		error_log( $ret) ;
		return $ret ;
	}

	/**
	 * getTableCuDlvrForKunde
	 *
	 * returns the table of all customer deliveries
	 * 
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @param	string	$_key	not-used
	 * @return	string	XML result tree
	 */
	function	getTableCuDlvrForKunde( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuDlvrNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.KundeNr = '" . $this->key . "' ",
								"ORDER BY C.Datum DESC ",
								"ResultSet",
								"CuDlvr",
								"C.CuDlvrNo,C.Datum,C.KundeNr,C.KundeKontaktNr") ;
		error_log( $ret) ;
		return $ret ;
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
	function	getTableCuInvcForKunde( $_key, $_id, $_val) {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "CuInvcNo", "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "KundeNr", "var") ;
		$myObj->addCol( "KundeKontaktNr", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.KundeNr = '" . $this->key . "' ",
								"ORDER BY C.Datum DESC ",
								"ResultSet",
								"CuInvc",
								"C.CuInvcNo,C.Datum,C.KundeNr,C.KundeKontaktNr") ;
		error_log( $ret) ;
		return $ret ;
	}
}

?>
