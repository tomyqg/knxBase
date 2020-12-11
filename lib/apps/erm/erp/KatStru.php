<?php

/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package WTA
 */

/**
 * requires the WTF Debugger and the Base Class
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "base/BKatStru.php") ;
require_once( "base/BKatStruKomp.php") ;

/**
 * KatStru - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BKatStru which should
 * not be modified.
 *
 * @package WTA
 * @subpackage Article
 */

class	KatStru	extends	BKatStru	{

	private	$tmpKatStruKomp ;
	private	$myCond ;

	/*
	 * The constructor can be passed an ArticleNr (KatStruNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_katStruNr='') {
		if ( strlen( $_katStruNr) > 0) {
			$this->setKatStruNr( $_katStruNr) ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setKatStruNr( $_katStruNr='') {
		if ( strlen( $_katStruNr) > 0) {
			$this->KatStruNr	=	$_katStruNr ;
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
			$this->fetchFromDbById() ;
		} else {
		}
	}

	/**
	 *
	 */
	function	reload() {
		FDbg::get()->dump( "KatStru::reload()") ;
		FDbg::get()->dump( "KatStru::reload(), KatStruNr='%s'", $this->KatStruNr) ;
		$this->fetchFromDb() ;
		FDbg::get()->dump( "KatStruNr::reload(), done") ;
	}

	/**
	 *
	 * @return void
	 */
	function	firstFromDb( $_cond='') {
		FDbg::get()->dumpL( 0x01000000, "KatStru::firstFromDb()") ;
		FDbg::get()->dumpL( 0x01000000, "KatStru::firstFromDb(): query = '%s'", $_cond) ;
		if ( strlen( $_cond) > 0) {
			$this->myCond	=	$_cond ;
		} else {
			$this->myCond	=	sprintf( "KatStruNr = '%s' ORDER BY PosNr ", $this->KatStruNr) ;
		}
		BKatStru::firstFromDb( $this->myCond) ;
	}

	/**
	 *
	 * @return void
	 */
	function	nextFromDb() {
		FDbg::get()->dumpL( 0x01000000, "KatStruKomp::nextFromDb()") ;
		BKatStru::nextFromDb( $this->myCond) ;
	}

	/**
	 *
	 * @return CuOrdrPos
	 */
	function	getFirstKomp() {
		$this->tmpKatStruKomp	=	new KatStruKomp() ;
		$this->tmpKatStruKomp->KatStruNr	=	$this->KatStruNr ;
		$this->tmpKatStruKomp->firstFromDb() ;
		return $this->tmpKatStruKomp ;
	}

	/**
	 *
	 * @return CuOrdrPos
	 */
	function	getNextKomp() {
		$this->tmpKatStruKomp->nextFromDb() ;
		return $this->tmpKatStruKomp ;
	}

}


/**
 * KatStruComp - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BKatStruComp which should
 * not be modified.
 *
 * @package WTA
 * @subpackage Article
 */

class	KatStruKomp	extends	BKatStruKomp	{

	private	$myCond ;

	/*
	 * The constructor can be passed an ArticleNr (KatStruCompNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		if ( $_id >= 0) {
			$this->setId( $_id) ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setId( $_id='') {
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
		FDbg::get()->dump( "KatStruKomp::reload()") ;
		FDbg::get()->dump( "KatStruKomp::reload(), KatStruNr='%s'", $this->KatStruNr) ;
		$this->fetchFromDbById() ;
		FDbg::get()->dump( "KatStruNrKomp::reload(), done") ;
	}

	/**
	 *
	 * @return void
	 */
	function	firstFromDb( $_cond='') {
		FDbg::get()->dumpL( 0x01000000, "KatStruKomp::firstFromDb()") ;
		if ( strlen( $_cond) > 0) {
			$this->myCond	=	$_cond ;
		} else {
			$this->myCond	=	sprintf( "KatStruNr = '%s' ORDER BY PosNr ", $this->KatStruNr) ;
		}
		BKatStruKomp::firstFromDb( $this->myCond) ;
	}

	/**
	 *
	 * @return void
	 */
	function	nextFromDb() {
		FDbg::get()->dumpL( 0x01000000, "KatStruKomp::nextFromDb()") ;
		BKatStruKomp::nextFromDb( $this->myCond) ;
	}

}

?>
