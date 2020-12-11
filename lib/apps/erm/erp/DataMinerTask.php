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

class	DataMinerTask	extends	DataMiner	{

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

	function	getTableTasks( $_key, $_id, $_val) {
		return $this->_getIt( "C.TaskNr like '%' ", "ORDER BY C.TaskNr ") ;
	}
	
	function	getTableTasksOpen( $_key, $_id, $_val) {
		return $this->_getIt( "C.TaskNr like '%' AND C.Status != " . Task::STATCLS . " ", "ORDER BY C.TaskNr ") ;
	}
	
	function	getTableTasksClosed( $_key, $_id, $_val) {
		return $this->_getIt( "C.TaskNr like '%' AND C.Status = " . Task::STATCLS . " ", "ORDER BY C.TaskNr ") ;
	}
	
	function	_getIt( $_cond, $_order) {
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "TaskNr", "var") ;
		$myObj->addCol( "DateReg", "var") ;
		$myObj->addCol( "Description", "var") ;
		$myObj->addCol( "DateFin", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$_cond,
								$_order,
								"ResultSet",
								"Task",
								"C.TaskNr, C.DateReg, C.Description, SUBSTRING( C.Description, 1, 50) AS Description, C.DateRem, C.DateFin ") ;
		FDbg::dump( $ret) ;
		return $ret ;
	}

}

?>
