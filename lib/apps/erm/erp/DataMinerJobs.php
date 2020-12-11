<?php
/**
 * DataMinesArtikel.php - Class to gather data related to an Article
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
 * DataMinerArtikel - DataMiner for drilling into Artikel-related data.
 *
 * Methods:<br/>
 * <ul>
 * <li>getTableSuOrdrForArtikel - return a table containing all supplier orders containing this Artikel</li>
 * <li>getTableSuDlvrForArtikel - return a table containing all supplier goods-receivable containing this Artikel</li>
 * <li>getTableCuOrdrForArtikel - return a table containing all customer orders containing this Artikel</li>
 * <li>getTableCuCommForArtikel - return a table containing all customer commissions containing this Artikel</li>
 * <li>getTableCuDlvrForArtikel - return a table containing all customer deliveries containing this Artikel</li>
 * <li>getTableCuInvcForArtikel - return a table containing all customer invoices containing this Artikel</li>
 * <li>getTableArticleUnreserved - return a table containing all customer order lines where the Artikel is not correctly reserved</li>
 * <li>getTableArticleToOrder - return a table containing all Articles which need to be ordered</li>
 * <li>getTableArticlePricing - return a table containing all Articles which have a sales price which is lower than the purchasing price</li>
 * </ul>
 *
 * @package Application
 * @subpackage DataMiner
 */
class	DataMinerJobs	extends	DataMiner	{
	function	__construct( $_key="", $_id="", $_val="") {
		DataMiner::__construct() ;
		return $this->valid ;
	}
	function	getTableJobsHourly( $_key="", $_id=0, $_val="") {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "var") ;
		$myObj->addCol( "Position", "var") ;
		$myObj->addCol( "JobName", "var") ;
		$myObj->addCol( "Schedule", "var") ;
		$myObj->addCol( "Status", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.Schedule = 'hourly' ",
								"ORDER BY C.Position ASC ",
								"ResultSet",
								"Jobs",
								"C.Id, C.Schedule, C.Position, C.JobName, C.Status") ;
		error_log( $ret) ;
		return $ret ;
	}
	function	getTableJobsDaily( $_key="", $_id=0, $_val="") {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "var") ;
		$myObj->addCol( "Position", "var") ;
		$myObj->addCol( "JobName", "var") ;
		$myObj->addCol( "Schedule", "var") ;
		$myObj->addCol( "Status", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.Schedule = 'daily' ",
								"ORDER BY C.Position ASC ",
								"ResultSet",
								"Jobs",
								"C.Id, C.Schedule, C.Position, C.JobName, C.Status") ;
		error_log( $ret) ;
		return $ret ;
	}
	function	getTableJobsWeekly( $_key="", $_id=0, $_val="") {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "var") ;
		$myObj->addCol( "Position", "var") ;
		$myObj->addCol( "JobName", "var") ;
		$myObj->addCol( "Schedule", "var") ;
		$myObj->addCol( "Status", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.Schedule like 'weekly%' ",
								"ORDER BY C.Position ASC ",
								"ResultSet",
								"Jobs",
								"C.Id, C.Schedule, C.Position, C.JobName, C.Status") ;
		error_log( $ret) ;
		return $ret ;
	}
	function	getTableJobsMonthly( $_key="", $_id=0, $_val="") {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "var") ;
		$myObj->addCol( "Position", "var") ;
		$myObj->addCol( "JobName", "var") ;
		$myObj->addCol( "Schedule", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.Schedule = 'monthly' ",
								"ORDER BY C.Schedule, C.Position ASC ",
								"ResultSet",
								"Jobs",
								"C.Id, C.Schedule, C.JobName, C.Position") ;
		error_log( $ret) ;
		return $ret ;
	}
	function	getTableJobsInterval( $_key="", $_id=0, $_val="") {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "var") ;
		$myObj->addCol( "Position", "var") ;
		$myObj->addCol( "JobName", "var") ;
		$myObj->addCol( "Schedule", "var") ;
		$myObj->addCol( "Periode", "int") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"C.Schedule = 'interval' ",
								"ORDER BY C.Schedule, C.Position ASC ",
								"ResultSet",
								"Jobs",
								"C.Id, C.Schedule, C.JobName, C.Position, C.Periode") ;
		error_log( $ret) ;
		return $ret ;
	}
}
?>
