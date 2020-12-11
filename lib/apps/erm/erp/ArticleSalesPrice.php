<?php
/**
 * ArticleSalesPrice.php - Basis Anwendungsklasse fuer Artikel Verkaufspreise (ArticleSalesPrice)
 *
 *	Definiert die Klassen:
 *		ArticleSalesPrice
 *
 * Klasse:	ArticleSalesPrice
 *
 * Methoden:
 *	firstFromDb		Diese Methode liefert den ersten Verkaufspreis fuer einen Artikel zurueck
 *					in der Reihenfolge:
 *						Quantity pro VPE
 *							Aktuell gueltige Verkaufspreise
 *								kuerzeste Gueltigkeitsdauer
 *									spaestes Gueltig-Bis Datum
 *							Letzte gueltige Verkaufspreise
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * ArticleSalesPrice - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArticleSalesPrice which should
 * not be modified.
 *
 * @package Application
 * @subpackage Article
 */
class	ArticleSalesPrice	extends	Price	{
	/**
	 *
	 * @var unknown_type
	 */
	const	STANDARD	=	0 ;
	const	MANUELL	=	1 ;
	const	SPEZIAL	=	2 ;
	private	static	$rPriceTyp	=	array (
						ArticleSalesPrice::STANDARD	=> "Standard",
						ArticleSalesPrice::MANUELL	=> "Haendisch",
						ArticleSalesPrice::SPEZIAL	=> "Sonderpreis"
					) ;
	/*
	 * The constructor can be passed an ArticleNr (ArticleSalesPriceNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		Price::__construct( "ArticleSalesPrice", "Id") ;
		if ( $_id >= 0) {
			$this->setId( $_id) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	reload( $_db="def") {
		FDbg::get()->dumpL( 0x01000000, "ArticleSalesPrice::reload()") ;
		$this->fetchFromDbById() ;
		FDbg::get()->dumpL( 0x01000000, "ArticleSalesPrice::reload(), done") ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
	}
	/**
	 *
	 */
	function	firstFromView( $_view, $where='', $start=0) {
		$this->_status	=	0 ;
		$this->_valid	=	0 ;
		$this->_currRow	=	$start ;
		$this->myCond	=	sprintf( "ArticleNo = '%s' ", $this->ArticleNo) ;
		$this->myCond	.=	sprintf( "ORDER BY QuantityPerPU DESC, PriceTyp DESC") ;
		$query	=	"select * " ;
		$query	.=	"from " . $_view . " " ;
		$query	.=	"where " . $this->myCond . " " ;
		$sqlResult      =       mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
			throw new Exception( "ArticleSalesPrice::firstFromView: database error, no data found") ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			if ( $numrows == 1) {
				$row    =       mysql_fetch_assoc( $sqlResult) ;
				$this->assignFromRow( $row) ;
				$this->_valid   =       1 ;
				$this->_currRow   =       0 ;
				$this->_lastRow   =       0 ;
			} else if ( $numrows > 1) {
				$row    =       mysql_fetch_assoc( $sqlResult) ;
				$this->assignFromRow( $row) ;
				$this->_valid   =       1 ;
				$this->_currRow   =       0 ;
				$this->_lastRow   =       $numrows - 1 ;
			} else {
				$this->_status   =       -5 ;
				throw new Exception( "ArticleSalesPrice::firstFromView: no data found") ;
			}
		}
		return $this->_status ;
	}
	/**
	 *
	 */
	function	nextFromView( $_view, $where='') {
		$this->_status	=	0 ;
		$this->_valid	=	0 ;
		if ( $this->_currRow < $this->_lastRow) {
			$this->_currRow++ ;
			$query	=	"select * " ;
			$query	.=	"from " . $_view . " " ;
			$query	.=	"where " . $this->myCond . " " ;
			$query	.=	"limit " . $this->_currRow . ", 1 " ;
			$sqlResult      =       mysql_query( $query, FDb::get()) ;
			$row    =       mysql_fetch_assoc( $sqlResult) ;
			$this->assignFromRow( $row) ;
			$this->_valid	=	1 ;
		} else {
			$this->_status	=	-3 ;
		}
		return $this->_status ;
	}
	/**
	 *
	 */
	function	getRPriceTyp() {
		return  self::$rPriceTyp ;
	}
}
?>
