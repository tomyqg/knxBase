<?php

/**
 * DataMinesArtikel.php - Class to gather data related to an Customer
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
require_once( "Selector.php" );

/**
 * Selector - User-Level Klasse
 *
 * This class acts as an interface for data-mining in general.
 * The following core methods are provided.
 *
 *
 * @package Application
 * @subpackage Selector
 */

class	SelectorArtikel	extends	Selector	{

	/**
	 * __construct
	 * 
	 * Creates a dataminer object
	 *
	 * @return void
	 */
	function	__construct() {
		parent::__construct() ;
		return $this->valid ;
	}
	function	setId( $_id=-1) {
		
	}
	/**
	 * 
	 * Fetches the items for this selector from the database-table
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	static	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		$_suchKrit	=	$_POST['_SArtikelNr'] ;
		$_POST['_step']	=	$_id ;
		$filter	.=	"( " ;
		$filter	.=	"C.ArtikelNr like '%" . $_suchKrit . "%' " ;
		if ( $_POST['_SArtikelBez'] != "")
			$filter	.=	"  AND ( ArtikelBez1 like '%" . $_POST['_SArtikelBez'] . "%' OR ArtikelBez2 LIKE '%" . $_POST['_SArtikelBez'] . "%') " ;
		if ( $_POST['_SArtikelBez1'] != "")
			$filter	.=	"  AND ( ArtikelBez1 like '%" . $_POST['_SArtikelBez1'] . "%') " ;
		if ( $_POST['_SArtikelBez2'] != "")
			$filter	.=	"  AND ( ArtikelBez2 like '%" . $_POST['_SArtikelBez2'] . "%' ) " ;
		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "ArtikelNr", "var") ;
		$myObj->addCol( "ArtikelBez1", "var") ;
		$myObj->addCol( "ArtikelBez2", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.ArtikelNr ASC ",
								"Artikel",
								"Artikel",
								"C.Id, C.ArtikelNr, C.ArtikelBez1, C.ArtikelBez2") ;
		return $ret ;
	}

}

?>
