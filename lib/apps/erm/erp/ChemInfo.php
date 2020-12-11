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
 * requires the WTF Debugger and the Base Class
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/BChemInfo.php") ;

require_once( "AbKorr.php" );

/**
 * ChemInfo - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BChemInfo which should
 * not be modified.
 *
 * @package Application
 * @subpackage ChemInfo
 */
class	ChemInfo	extends	BChemInfo	{

	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		FDbg::dumpL( 0x01000000, "ChemInfo::__constructor('%s')") ;
		if ( $_id >= 0) {
			$this->setId( $_id) ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setId( $_id=-1) {
		FDbg::dumpL( 0x01000000, "ChemInfo::setId(): ") ;
		if ( $_id >= 0) {
			$this->Id	=	$_id ;
			$this->reload() ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setCASNrSprache( $_casNr, $_sprache='de_de') {
		FDbg::dumpL( 0x01000000, "ChemInfo::setCASNrSprache(): " . $_casNr . " " . $_sprache . " ") ;
		if ( strlen( $_casNr) > 0) {
			try {
				$this->fetchFromDbWhere( "WHERE CASNr='" . $_casNr . "' AND Sprache='" . $_sprache . "'") ;
				$this->reload() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			throw new Exception( 'ChemInfo::setArtikelNrSprache: keinen Eintrag gefunden !') ;
		}
	}

	/**
	 *
	 */
	function    reload() {
		FDbg::dumpL( 0x01000000, "ChemInfo::reload(): ") ;
		$this->fetchFromDbById() ;
		FDbg::dumpL( 0x01000000, "ChemInfo::reload(), done") ;
	}

	/**
	 *
	 * @return void
	 */
	function	firstFromDb( $_cond='') {
		FDbg::dumpL( 0x01000000, "ChemInfo::firstFromDb()") ;
		if ( strlen( $_cond) > 9) {
			$this->myCond	=	$_cond ;
		} else {
			$this->myCond	=	sprintf( "ArtikelNr = '%s' ") ;
		}
		BChemInfo::firstFromDb( $this->myCond) ;
	}

	/**
	 *
	 * @return void
	 */
	function	nextFromDb() {
		FDbg::dumpL( 0x01000000, "ChemInfo::nextFromDb()") ;
		BChemInfo::nextFromDb( $this->myCond) ;
	}
	
	/**
	 * debug
	 */
	function	_debug() {
		if ( FDbg::enabled()) {
			printf("ChemInfo::Debug<br/>") ;
			print_r( $this) ;
		}
	}

}

?>
