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
require_once( "base/BMailing.php") ;

/**
 * Mailing - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BMailing which should
 * not be modified.
 *
 * @package Application
 * @subpackage Mail
 */

class	Mailing	extends	BMailing	{

	/*
	 * The constructor can be passed an ArticleNr (MailingNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_mailingNr='') {
		FDbg::dumpL( 0x01000000, "Mailing::__construct(...): ") ;
		if ( strlen( $_mailingNr) > 0) {
			$this->setMailingNr( $_mailingNr) ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setMailingNr( $_mailingNr='') {
		FDbg::dumpL( 0x01000000, "Mailing::setMailingNr(...): ") ;
		if ( strlen( $_mailingNr) > 0) {
			$this->MailingNr	=	$_mailingNr ;
			$this->fetchFromDb() ;
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
		FDbg::dumpL( 0x01000000, "Mailing::reload(...): ") ;
		$this->fetchFromDb() ;
	}

}

?>
