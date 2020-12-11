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
 * DataMiner - User-Level Klasse
 *
 * This class acts as an interface for data-mining in general.
 * The following core methods are provided.
 *
 *
 * @package Application
 * @subpackage DataMiner
 */

class	DataMinerArtikelWOP	extends	DataMiner	{

	/**
	 * Erzeugung eines Objektes.
	 * 
	 * Erzeugt ein Artikel-Objekt und versucht ggf. diesen Artikel aus der Db zu laden.
	 *
	 * @param string $_artikelNr='' Artikelnummer
	 * @return void
	 */
	function	__construct( $_key="", $_id="", $_val="") {
		DataMiner::__construct() ;
		return $this->valid ;
	}
	
	function	setKey( $_key, $_val="", $_id="") {
		$this->objName	=	$_key ;
		return $this->valid ;
	}

	/**
	 * 
	 */
	function	getTableArticleWOP() {
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "ArtikelNr", "varchar") ;
		$myObj->addCol( "ArtikelBez1", "varchar") ;
		$myObj->addCol( "ArtikelBez2", "varchar") ;
		$ret	.=	"<StartRow><![CDATA[" . $this->startRow . "]]></StartRow>" ;
		$ret	.=	$myObj->tableFromDb( " ",
										" ", 
										"NOT EXISTS ( SELECT ArtikelNr FROM VKPreisCache WHERE ArtikelNr = C.ArtikelNr ) ",
										"ORDER BY C.ArtikelNr ASC LIMIT ". $this->startRow . ", " . $this->rowCount . " ",
										"ResultSet",
										"Artikel",
										"C.ArtikelNr,C.ArtikelBez1,C.ArtikelBez2"
					) ;
		return $ret ;
	}
	
}

?>
