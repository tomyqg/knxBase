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
 * Currency - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCurrency which should
 * not be modified.
 *
 * @package Application
 * @subpackage Currency
 */

class	Currency	extends	FDbObject	{

	const	EUR	=	"EUR" ;
	const	USD	=	"USD" ;
	const	CAD	=	"CAD" ;
	const	CLP	=	"CLP" ;
	const	GBP	=	"GBP" ;
	private	static	$rCurrency	=	array (
						Currency::EUR	=> "Euro",
						Currency::USD	=> "USD",
						Currency::CAD	=> "Canadian",
						Currency::CLP	=> "Chilean Peso",
						Currency::GBP	=> "British Pound"
					) ;

	/*
	 * The constructor can be passed an ArticleNr (CurrencyNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		parent::__construct( "Currency", "Id") ;
		if ( $_id >= 0) {
			$this->Id	=	$_id ;
			$this->fetchFromDbById() ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setKeys( $_fromCode, $_toCode, $_date) {
		FDbg::get()->dumpL( 0x02000000, "Currency::setKeys( _fromCode='%s', _toCode='%s', _date='%s')", $_fromCode, $_toCode, $_date) ;
		$this->VonWaehrung	=	$_fromCode ;
		$this->NachWaehrung	=	$_toCode ;
		$this->date	=	$_date ;
		try {
			$this->reload() ;
		} catch ( Exception $e) {
			throw $e ;
		}
	}

	/**
	 *
	 */
	function	reload() {
		FDbg::get()->dumpL( 0x02000000, "Currency::reload()") ;
		$cond	=	sprintf( "WHERE VonWaehrung = '%s' AND NachWaehrung = '%s' AND CGueltigVon <= '%s' AND '%s' < CGueltigBis ",
								$this->VonWaehrung, $this->NachWaehrung,
								$this->date, $this->date) ;
		FDbg::get()->dumpL( 0x04000000, "Currency::__construct(...), WHERE='%s'", $cond) ;
		$this->fetchFromDbWhere( $cond) ;
		if ( $this->_valid != 1) {
			throw new Exception( "WTA:Currency:0001:inv") ;
		}
		FDbg::get()->dumpL( 0x02000000, "Currency::reload() done") ;
	}

	/**
	 *
	 */
	function	getRCurrency() {
		return self::$rCurrency ;
	}
		
	function	add( $_key, $_id, $_val) {
		FDbg::dumpL( 0x01000000, "Artikel::add(...)") ;
		try {
			$this->getFromPostL() ;
			$this->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}

	function	upd( $_key, $_id, $_val) {
		try {
			FDbg::dumpL( 0x01000000, "Artikel::upd(...)") ;
			$this->getFromPostL() ;
			$this->updateInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}

	function	del( $_key, $_id, $_val) {
		$myCurrency	=	new ArtKomp() ;
		$myCurrency->setId( $_id) ;
		$myCurrency->removeFromDb() ;
		return $this->getTableCurrencyAsXML() ;
	}

}

?>
