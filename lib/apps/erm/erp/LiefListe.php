<?php

/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * LiefListe - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BLiefListe which should
 * not be modified.
 *
 * @package Application
 * @subpackage Lief
 */

class	LiefListe	extends	FDbObject	{

	const	PROZCALC	= 10 ;		// erstellt auf Basis einer alten Liste mit prozentualem Aufschlag
	const	BASECSV		= 20 ;		// eingelesen als CSV
	const	BASEXLS		= 30 ;		// eingelesen als XLS
	const	BASEMAN		= 90 ;		// manuell erstellte Liste

	private	static	$rTypListe	=	array (
						LiefListe::PROZCALC	=> "Prozentualer Aufschlag",
						LiefListe::BASECSV	=> "CSV Datei",
						LiefListe::BASEXLS	=> "XLS Datei",
						LiefListe::BASEMAN	=> "Manuell"
					) ;

	/*
	 * The constructor can be passed an ArticleNr (LiefListeNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		parent::__construct( "LiefListe", "Id") ;
		if ( $_id >= 0) {
			$this->Id	=	$_id ;
			$this->reload() ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setId( $_id=-1) {
		if ( $_id >= 0) {
			$this->Id	=	$_id ;
			$this->reload() ;
		} else {
		}
	}

	/**
	 *
	 */
	function	reload() {
		FDbg::get()->dumpL( 0x01000000, "CuOrdrItem::reload()") ;
		$this->fetchFromDbById() ;
		FDbg::get()->dumpL( 0x01000000, "CuOrdrItem::reload(), done") ;
	}

	/**
	 *
	 */
	function	getRTypListe() {
		return  self::$rTypListe ;
	}
}

?>
