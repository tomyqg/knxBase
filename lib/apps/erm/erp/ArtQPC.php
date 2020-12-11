<?php
/**
 * Article Quantiy Per Carton (VPE)
 *
 * This data needs to be maintained separately from the Article data as there
 * might be articles which are sold e.g. boxes of 10, 20 or 100.
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-06-24	PA1		khw		added;
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Artikel - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package Application
 * @subpackage Article
 */
class	ArtQPC	extends	FDbObject	{
	/**
	 * @var unknown_type
	 */
	const	NORMAL	=	0 ;
	const	OPTION	=	10 ;
	private	static	$rQPCTyp	=	array (
						ArtQPC::NORMAL		=> "Normal",
						ArtQPC::OPTION		=> "Option"
					) ;
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		FDbg::begin( 1, "ArtQPC.php", "ArtQPC", "__construct()") ;
		parent::__construct( "ArtQPC", "Id") ;
		FDbg::end( 1, "ArtQPC.php", "ArtQPC", "__construct()") ;
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
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
}
?>
