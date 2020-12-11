<?php
/**
 * Stats.php - Basic class to retrieve data in a datamining fashion
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
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
/**
 * Stats - User-Level Klasse
 *
 * This class acts as an interface for data-mining in general.
 * The following core methods are provided.
 *
 *
 * @package Application
 * @subpackage Stats
 */
class	StatsCuOrdr	extends	Stats	{
	/**
	 * __construct
	 * 
	 * Creates an instance of a dataminer for an object of class <$_objName>.
	 *
	 * @param	string	$_objName	class for which a dataminer shall be created
	 */
	function	__construct( $_objName="") {
		parent::__construct( "Stats:" . $_objName) ;
	}
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		switch ( $_val) {
		case	"OrdersBookedY"	:
			$buffer	=	$this->CuOrdr( $_key, $_id, $_val) ;
			break ;
		default	:
			$buffer	=	$this->CuOrdr( $_key, $_id, $_val) ;
			break ;
		}
		return $buffer ;
	}
	/**
	 * getTableAbKorrAll
	 * 
	 * returns the table of all customer order lines which 
	 */
	function	CuOrdr( $_key, $_id, $_val) {
		$_POST['_SStartRow']	=	0 ;
		$_POST['_SRowCount']	=	1000 ;
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		switch ( $_val) {
		case	"OrdersBookedY"	:
			$myObj->addCol( "Total", "int") ;
			$myObj->addCol( "Year", "var") ;
			$myObj->addCol( "Month", "var") ;
			$ret	=	$myObj->tableFromDb( " ",
							" ",
							"true ",
							"GROUP BY year( C.Datum) ",
							"ResultSet",
							"CuOrdr",
							"C.Datum, SUM( C.GesamtPreis) AS Total, year( C.Datum) AS Year",
							"def", $_val) ;
			break ;
		case	"OrdersBookedQ"	:
			$myObj->addCol( "Total", "int") ;
			$myObj->addCol( "Year", "var") ;
			$myObj->addCol( "Month", "var") ;
			$ret	=	$myObj->tableFromDb( " ",
							" ",
							"true ",
							"GROUP BY year( C.Datum), quarter( C.Datum) ",
							"ResultSet",
							"CuOrdr",
							"C.Datum, SUM( C.GesamtPreis) AS Total, year( C.Datum) AS Year, quarter(C.Datum) AS Quarter",
							"def", $_val) ;
			break ;
		case	"OrdersBookedM"	:
			$myObj->addCol( "Total", "int") ;
			$myObj->addCol( "Year", "var") ;
			$myObj->addCol( "Month", "var") ;
			$ret	=	$myObj->tableFromDb( " ",
							" ",
							"true ",
							"GROUP BY year( C.Datum), month( C.Datum) ",
							"ResultSet",
							"CuOrdr",
							"C.Datum, SUM( C.GesamtPreis) AS Total, year( C.Datum) AS Year, monthname(C.Datum) AS Month",
							"def", $_val) ;
			break ;
		case	"ArticlesSoldM"	:
			$myObj->addCol( "ArtikelNr", "var") ;
			$myObj->addCol( "Description", "var") ;
			$myObj->addCol( "Menge", "int") ;
			$myObj->addCol( "Total", "int") ;
			$myObj->addCol( "Year", "var") ;
			$myObj->addCol( "Month", "var") ;
			$ret	=	$myObj->tableFromDb( " ",
							"LEFT JOIN CuOrdr AS CuO ON CuO.CuOrdrNo = C.CuOrdrNo LEFT JOIN Artikel AS A ON A.ArtikelNr = C.ArtikelNr  ",
							"true ",
							"GROUP BY month( CuO.Datum), C.ArtikelNr ORDER BY month( CuO.Datum), C.ArtikelNr ",
							"ResultSet",
							"CuOrdrItem",
							"C.ArtikelNr, CONCAT( A.ArtikelBez1, ', ', A.ArtikelBez2) AS Description, SUM( C.Menge) AS Menge, CuO.Datum, SUM( C.Menge * C.Preis) AS Total, year( CuO.Datum) AS Year, monthname(CuO.Datum) AS Month",
							"def", $_val) ;
			break ;
		}
		return $ret ;
	}
	/**
	 * 
	 */
	function	getOrdersBookedMGraph( $_key="", $_id=-1, $_val="") {
		$myObj	=	new FDbObject( "CuOrdr", "CuOrdrNo") ;				// no specific object we need here
		$myObj->addCol( "Total", "int") ;
		$myObj->addCol( "Year", "var") ;
		$myObj->addCol( "Month", "var") ;
		for ( $month=1 ; $month <= 12 ; $month++) {
			$query	=	"SELECT C.Datum, SUM( C.GesamtPreis) AS Total, year( C.Datum) AS Year, monthname(C.Datum) AS Month "
					.	"FROM CuOrdr AS C "
					.	"WHERE C.Datum LIKE '2013-".sprintf("%02d",$month)."-%' "
					.	"GROUP BY year( C.Datum), month( C.Datum) " ;
			$myRow	=	FDb::queryRow( $query) ;
			if ( $myRow) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, "Stats.php", "Stats", "getOrdersBookedMGraph()", "Month: '".$myRow["Month"]."' Total := ".$myRow["Total"]."' ") ;
				$myOrdersBooked[$month-1]	=	floatval( $myRow["Total"]) ;
			} else {
				$myOrdersBooked[$month-1]	=	0 ;
			}
		}
		for ( $month=1 ; $month <= 12 ; $month++) {
			$query	=	"SELECT C.Datum, SUM( C.GesamtPreis) AS Total, year( C.Datum) AS Year, monthname(C.Datum) AS Month "
					.	"FROM CuInvc AS C "
					.	"WHERE C.Datum LIKE '2013-".sprintf("%02d",$month)."-%' "
					.	"GROUP BY year( C.Datum), month( C.Datum) " ;
			$myRow	=	FDb::queryRow( $query) ;
			if ( $myRow) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, "Stats.php", "Stats", "getOrdersBookedMGraph()", "Month: '".$myRow["Month"]."' Total := ".$myRow["Total"]."' ") ;
				$myInvoicesBooked[$month-1]	=	floatval( $myRow["Total"]) ;
			} else {
				$myInvoicesBooked[$month-1]	=	0 ;
			}
		}
		$fileName	=	$this->path->Archive."Orders2013M.png" ;
		$this->createGraph( $fileName, $this->months, "by Month", $myOrdersBooked, "Orders", $myInvoicesBooked, "Invoices") ;
		$buf	=	"<GraphId>graphOBM</GraphId>" ;
		$buf	.=	"<GraphName>/Archive/Orders2013M.png</GraphName>" ;
		return $buf ;
	}
	/**
	 * 
	 */
	function	getOrdersBookedQGraph( $_key="", $_id=-1, $_val="") {
		$myObj	=	new FDbObject( "CuOrdr", "CuOrdrNo") ;				// no specific object we need here
		$myObj->addCol( "Total", "int") ;
		$myObj->addCol( "Year", "var") ;
		$myObj->addCol( "Quarter", "var") ;
		for ( $quarter=1 ; $quarter <= 4 ; $quarter++) {
			$query	=	"SELECT C.Datum, SUM( C.GesamtPreis) AS Total, year( C.Datum) AS Year, quarter(C.Datum) AS Quarter "
					.	"FROM CuOrdr AS C "
					.	"WHERE C.Datum LIKE '2013-%' AND QUARTER( C.Datum) = ".$quarter." "
					.	"GROUP BY year( C.Datum), quarter( C.Datum) " ;
			$myRow	=	FDb::queryRow( $query) ;
			if ( $myRow) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, "Stats.php", "Stats", "getOrdersBookedMGraph()", "Quarter: '".$myRow["Quarter"]."' Total := ".$myRow["Total"]."' ") ;
				$myOrdersBooked[$quarter-1]	=	floatval( $myRow["Total"]) ;
			} else {
				$myOrdersBooked[$quarter-1]	=	0 ;
			}
		}
		for ( $quarter=1 ; $quarter <= 4 ; $quarter++) {
			$query	=	"SELECT C.Datum, SUM( C.GesamtPreis) AS Total, year( C.Datum) AS Year, quarter(C.Datum) AS Quarter "
					.	"FROM CuInvc AS C "
					.	"WHERE C.Datum LIKE '2013-%' AND QUARTER( C.Datum) = ".$quarter." "
					.	"GROUP BY year( C.Datum), quarter( C.Datum) " ;
			$myRow	=	FDb::queryRow( $query) ;
			if ( $myRow) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, "Stats.php", "Stats", "getOrdersBookedMGraph()", "Quarter: '".$myRow["Quarter"]."' Total := ".$myRow["Total"]."' ") ;
				$myInvoicesBooked[$quarter-1]	=	floatval( $myRow["Total"]) ;
			} else {
				$myInvoicesBooked[$quarter-1]	=	0 ;
			}
		}
		$fileName	=	$this->path->Archive."Orders2013Q.png" ;
		$this->createGraph( $fileName, $this->quarters, "by Quarter", $myOrdersBooked, "Orders", $myInvoicesBooked, "Invoices") ;
		$buf	=	"<GraphId>graphOBQ</GraphId>" ;
		$buf	.=	"<GraphName>/Archive/Orders2013Q.png</GraphName>" ;
		return $buf ;
	}
	/**
	 * getTableAbKorrAll
	 * 
	 * returns the table of all customer order lines which 
	 */
	function	getTableOrdersInvoiced( $_key, $_id, $_val) {
		$_POST['_step']	=	$_id ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Total", "int") ;
		$myObj->addCol( "Year", "var") ;
		$myObj->addCol( "Month", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								"true ",
								"GROUP BY year( C.Datum), month( C.Datum) ",
								"ResultSet",
								"CuInvc ",
								"C.Datum, SUM( C.GesamtPreis) AS Total, year( C.Datum) AS Year, monthname(C.Datum) AS Month") ;
		return $ret ;
	}
}
?>
