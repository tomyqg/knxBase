<?php

/**
 * DataMinesAdr.php - Class to gather data related to an Customer
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

class	SelectorUI_Module	extends	Selector	{

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
//		$_searchCrit	=	$_POST['_SModuleName'] ;
		$_searchCrit	=	"" ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( " ;
		$filter	.=	"C.ModuleName like '%" . $_searchCrit . "%' " ;
//		if ( $_POST['_SCompany'] != "")
//			$filter	.=	"  AND ( FirmaName1 like '%" . $_POST['_SCompany'] . "%' OR FirmaName2 LIKE '%" . $_POST['_SCompany'] . "%') " ;
		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "ModuleName", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.SeqNo ASC ",
								"UI_Module",
								"UI_Module",
								"C.Id, C.ModuleName ") ;
		return $ret ;
	}

}

?>
