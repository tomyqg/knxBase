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
require_once( "base/BBrief.php") ;
require_once( "Receiver.php" );

/**
 * Brief - Brief-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BBrief which should
 * not be modified.
 *
 * @package Application
 * @subpackage Brief
 */

class	Brief	extends	BBrief	{

	private	$myReceiver ;

	/*
	 * The constructor can be passed an ArticleNr (BriefNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_myBriefNr='') {
		FDbg::get()->dumpL( 0x01000000, "Brief::__construct( '%s')", $_myBriefNr) ;
		$this->myReceiver	=	new Receiver() ;
		if ( strlen( $_myBriefNr) >= 0) {
			$this->setBriefNr( $_myBriefNr) ;
		} else {
			FDbg::get()->dumpL( 0x01000000, "Brief::__construct(...): BriefNr not specified !") ;
		}
	}

	/**
	 *
	 */
	function	setBriefNr( $_myBriefNr) {
		FDbg::get()->dumpL( 0x01000000, "Brief::setBriefNr('%s')", $_myBriefNr) ;
		$this->BriefNr	=	$_myBriefNr ;
		if ( strlen( $_myBriefNr) > 0) {
			$this->reload() ;
		}
		FDbg::get()->dumpL( 0x01000000, "Brief::setBriefNr('%s') is done", $_myBriefNr) ;
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
		FDbg::get()->dumpL( 0x01000000, "Brief::reload()") ;
		$this->fetchFromDb() ;
		if ( $this->_valid == 1) {
			FDbg::get()->dumpL( 0x01000000, "Brief::reload(): Brief is valid !") ;

			/**
			 *
			 */
			try {
				switch ( $this->AdrTyp) {
				case	Receiver::ADR	:
					$this->myReceiver->setFromAdr( $this->AdrNr, $this->AdrKontaktNr) ;
					break ;
				case	Receiver::ADRKUNDE	:
					$this->myReceiver->setFromKunde( $this->AdrNr, $this->AdrKontaktNr) ;
					break ;
				case	Receiver::ADRLIEF	:
					$this->myReceiver->setFromLief( $this->AdrNr, $this->AdrKontaktNr) ;
					break ;
				}
			} catch( Exception $e) {
				FDbg::get()->dumpF( "Brief::__construct(...): exception='%s'", $e->getMessage()) ;
			}
		}
		FDbg::get()->dumpL( 0x01000000, "Brief::reload() is done") ;
	}

	/**
	 *
	 */
	function	newBrief() {
		FDbg::dumpL( 0x00000001, "Brief::newBrief: ") ;

		try {
			$query	=	"call newBrief( @a, @newBriefNr) ; " ;
			$sqlResult	=	FDb::query( $query) ;
			if ( !$sqlResult) { 
				$this->_status  =       -1 ; 
				throw new Exception( "Brief::newBrief: Fehler bei query [" . $query . "] ") ;
			} else { 
				$query	=	"select @newBriefNr " ;
				$sqlResult	=	FDb::query( $query) ;
				if ( !$sqlResult) { 
					$this->_status  =       -1 ; 
					throw new Exception( "Brief::newBrief: Fehler bei query [" . $query . "] ") ;
				} else { 
					$row    =       mysql_fetch_array( $sqlResult) ; 
					$this->BriefNr	=	$row[0] ;
					$this->fetchFromDb() ;
				}
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->_status ;
	}

	/**
	 * newFromBrief
	 *
	 * Legt einen neuen Brief auf Basis eines existierenden Briefes an
	 *
	 * @return void
	 */
	function	newFromBrief( $_myBriefNr) {
		FDbg::get()->dumpL( 0x01000000, "Brief::newFromBrief(...)") ;

		try {
			$query	=	sprintf( "Brief_newFromBrief( @status, '%s', @newBriefNr) ; ", $_myBriefNr) ;
			$row	=	FDb::callProc( $query, '@newBriefNr') ;
			$this->setBriefNr( $row['@newBriefNr']) ;
		} catch( Exception $e) {
			FDbg::get()->dumpF( "Brief::newFromBrief(...): exception='%s'", $e->getMessage()) ;
		}
	}

	/**
	 *
	 */
	function	setReceiverFromKKId( $_kkId) {
		try {
			$this->myReceiver->setFromKKId( $_kkId) ;
			$this->AdrNr	=	$this->myReceiver->AdrNr ;
			$this->AdrKontaktNr	=	$this->myReceiver->AdrKontaktNr ;
			$this->AdrTyp	=	Receiver::ADRKUNDE ;
			$this->updateInDb() ;
			$this->reload() ;
		} catch ( Exception $e) {
			throw $e ;
		}
	}

	/**
	 *
	 */
	function	setReceiverFromLKId( $_lkId) {
		try {
			$this->myReceiver->setFromLKId( $_lkId) ;
			$this->AdrNr	=	$this->myReceiver->AdrNr ;
			$this->AdrKontaktNr	=	$this->myReceiver->AdrKontaktNr ;
			$this->AdrTyp	=	Receiver::ADRLIEF ;
			$this->updateInDb() ;
			$this->reload() ;
		} catch ( Exception $e) {
			throw $e ;
		}
	}

	/**
	 *
	 */
	function	setReceiverFromAKId( $_akId) {
		try {
			$this->myReceiver->setFromAKId( $_akId) ;
			$this->AdrNr	=	$this->myReceiver->AdrNr ;
			$this->AdrKontaktNr	=	$this->myReceiver->AdrKontaktNr ;
			$this->AdrTyp	=	Receiver::ADR ;
			$this->updateInDb() ;
			$this->reload() ;
		} catch ( Exception $e) {
			throw $e ;
		}
	}

	/**
	 *
	 */
	function	getReceiver() {
		return $this->myReceiver ;
	}
		
}

?>
