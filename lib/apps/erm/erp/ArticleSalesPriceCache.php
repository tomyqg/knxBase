<?php
/**
 * ArticleSalesPriceCache.php - Basis Anwendungsklasse fuer Artikel Verkaufspreise (ArticleSalesPriceCache)
 *
 *	Definiert die Klassen:
 *		ArticleSalesPriceCache
 *
 * Klasse:	ArticleSalesPriceCache
 *
 * Methoden:
 *	firstFromDb		Diese Methode liefert den ersten Verkaufspreis fuer einen Artikel zurueck
 *					in der Reihenfolge:
 *						Menge pro VPE
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
 * ArticleSalesPriceCache - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArticleSalesPriceCache which should
 * not be modified.
 *
 * @package Application
 * @subpackage Article
 */
class	ArticleSalesPriceCache	extends	Price	{
	const	STANDARD	=	0 ;
	const	MANUELL	=	1 ;
	const	SPEZIAL	=	2 ;
	private	static	$rPreisTyp	=	array (
						ArticleSalesPriceCache::STANDARD	=> "Standard",
						ArticleSalesPriceCache::MANUELL	=> "Haendisch",
						ArticleSalesPriceCache::SPEZIAL	=> "Sonderpreis"
					) ;
	/*
	 * The constructor can be passed an ArticleNr (ArticleSalesPriceCacheNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( "ArticleSalesPriceCache", "Id") ;
		if ( $_id >= 0) {
			$this->setId( $_id) ;
		} else {
		}
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	reload( $_db="def") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$this->fetchFromDbById() ;
		FDbg::end() ;
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
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	$_POST['Search'] ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "ArticleSalesPriceCache", "Id", "def", "v_ArticleSalesPriceCache_1") ;
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"ArticleNo like '%" . $sCrit . "%' OR Description like '%" . $sCrit . "%'" ;
			$filter2	=	"" ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addOrder( ["ArticleNo"]) ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
}

?>
