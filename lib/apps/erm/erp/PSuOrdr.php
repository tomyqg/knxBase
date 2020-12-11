<?php

require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;

require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
require_once( "base/AppObjectSR.php") ;
require_once( "base/AppDepObject.php") ;

class	PSuOrdr	extends	AppObjectSR	{

	const	NEU		=	0 ;
	const	ONGOING	=	50 ;
	const	CLOSED	=	90 ;
	const	ONHOLD	=	980 ;
	const	CANCELLED	=	990 ;
	private	static	$rStatus	=	array (
						PSuOrdr::NEU		=> "open",
						PSuOrdr::ONGOING	=> "ongoing",
						PSuOrdr::CLOSED	=> "abgeschlossen",
						PSuOrdr::ONHOLD	=> "on-hold",
						PSuOrdr::CANCELLED	=> "cancelled"
					) ;
	/**
	 *
	 */
	function	__construct( $_myPSuOrdrNo='') {
		FDbg::dump( "PSuOrdr.php::PSuOrdr::__construct( '%s ')", $_myPSuOrdrNo) ;
		AppObjectSR::__construct( "PSuOrdr", "PSuOrdrNo") ;
		if ( strlen( $_myPSuOrdrNo) > 0) {
			$this->setPSuOrdrNo( $_myPSuOrdrNo) ;
		} else {
			FDbg::dump( "PSuOrdr.php::PSuOrdr::__construct(...): PSuOrdrNo not specified !") ;
		}
		FDbg::dump( "PSuOrdr.php::PSuOrdr::__construct(...) done") ;
	}
	/**
	 * 
	 * @param unknown_type $_myPSuOrdrNo
	 */
	function	setPSuOrdrNo( $_myPSuOrdrNo) {
		$this->PSuOrdrNo	=	$_myPSuOrdrNo ;
		if ( strlen( $_myPSuOrdrNo) > 0) {
			$this->reload() ;
		}
	}
	/**
	 * clean()
	 * removes all entries in the 'Provisionary Supplier Order' and 'Provisionary Supplier Order Items'
	 * return void
	 */
	function	clean() {
		FDbg::dump( "PSuOrdr.php::PSuOrdr::clean()") ;
		try {
			FDb::query( "DELETE FROM PSuOrdr ; ") ;
			FDb::query( "DELETE FROM PSuOrdrItem ; ") ;
		} catch ( Exception $e) {
			error_log( "PSuOrdr.php::PSuOrdr::clean(): exception '" . $e->getMessage() . "' ") ;
			throw $e ;
		}
	}
	/**
	 * 
	 */
	function	newSuOrdr( $_key="", $_id=-1, $_val="") {
		$newSuOrdr	=	new SuOrdr() ;
		$newSuOrdr->newFromPSuOrdr( '', -1, $_key) ;
		$ret	=	"<Reference>\n<ObjectClass>SuOrdr</ObjectClass>\n<ObjectKey>$newSuOrdr->SuOrdrNo</ObjectKey>\n</Reference>\n" ;
		return $ret ;
	}
	/**
	 * getRStatus()
	 */
	function	getRStatus() {
		return self::$rStatus ;
	}
	/**
	 * getXMLComplete()
	 */	
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getLiefAsXML() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "PSuOrdrItem") ;
		return $ret ;
	}
	/**
	 * 
	 */
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getSuOrdrItemAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$mySuOrdrItem	=	new SuOrdrItem() ;
		$mySuOrdrItem->setId( $_id) ;
		$ret	.=	$mySuOrdrItem->getXMLF() ;
		return $ret ;
	}
	/**
	 *
	 */
	function	create( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x01000000, "PSuOrdr.php::PSuOrdr::crdeate( '$_key', $_id, '$_val'): ") ;
		$this->clean() ;
		try {
			$this->_insertArticles() ;
		} catch ( Exception $e) {
			error_log( "PSuOrdr.php:PSuOrdr::create( '$_key', $_id, '$_val'): exception '" . $e->getMessage() . "' ") ;
			throw $e ;
		}
//		return $tmpArtikelBestand->getTableItemsToOrderAsXML() ;
	}
	/**
	 * _insertArticles()
	 */
	function	_insertArticles() {
		$myPSuOrdrA	=	array() ;
		$myArtikelBestand	=	new ArtikelBestand() ;
		$myPSuOrdr	=	new PSuOrdr ;
		$myPSuOrdrItem	=	new PSuOrdrItem ;
		$myEKPreisR	=	new EKPreisR() ;
		$itemNo	=	10 ;
		$crit	=	"( Lagerbestand + Bestellt ) < ( Reserviert + Mindestbestand) " ;
		for ( $myArtikelBestand->_firstFromDb( $crit) ;
				$myArtikelBestand->isValid() ;
				$myArtikelBestand->_nextFromDb()) {
			/**
			 * @var unknown_type
			 */
			$myPSuOrdrItem->ItemNo	=	$itemNo ;
			$myPSuOrdrItem->ArtikelNr	=	$myArtikelBestand->ArtikelNr ;
			$myPSuOrdrItem->Menge	=	$myArtikelBestand->Reserviert + $myArtikelBestand->Mindestbestand
									-	$myArtikelBestand->Lagerbestand - $myArtikelBestand->Bestellt ;
			$myEKPreisR->getCalcBase( $myPSuOrdrItem->ArtikelNr) ;
			if ( $myEKPreisR->isValid()) {
				$myPSuOrdrItem->PSuOrdrNo	=	$myEKPreisR->LiefNr ;
				$myPSuOrdrItem->LiefArtNr	=	$myEKPreisR->LiefArtNr ;
				$myPSuOrdrItem->MengeProVPE	=	$myEKPreisR->MengeProVPE ;
			}
			$myPSuOrdrItem->storeInDb() ;
			if ( ! isset( $myPSuOrdrA[$myEKPreisR->LiefNr])) {
				$myPSuOrdrA[$myEKPreisR->LiefNr]	=	$myEKPreisR->LiefNr ;
				$myPSuOrdr->PSuOrdrNo	=	$myPSuOrdrItem->PSuOrdrNo ;
				$myPSuOrdr->LiefNr	=	$myEKPreisR->LiefNr ;
				$myPSuOrdr->storeInDb() ;
			}
			/**
			 * 
			 * @var unknown_type
			 */
			$itemNo	+=	10 ;
		}
		/**
		 * 
		 */
		foreach ( $myPSuOrdrA AS $pSuOrdrNo) {
			$myPSuOrdr->setPSuOrdrNo( $pSuOrdrNo) ;
			if ( $myPSuOrdr->_valid) {
				$myPSuOrdr->updPrices( $myPSuOrdr->PSuOrdrNo) ;
			}
		}
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	updPSuOrdrItem( $_key, $_id, $_val) {
		$myPSuOrdrItem	=	new PSuOrdrItem() ;
		$myPSuOrdrItem->setId( $_id) ;
		$myPSuOrdrItem->getFromPostL() ;
		$myPSuOrdrItem->updateInDb() ;
		return $this->getTablePostenAsXML() ;
	}
}
/**
 *
 */
class	PSuOrdrItem	extends	AppDepObject	{
	/**
	 * 
	 * @var unknown_type
	 */
	public	$myArtikel ;
	private	$myCond ;
	/**
	 *
	 */
	function	__construct( $_pSuOrdrNo='') {
		FDbg::dumpL( 0x00000001, "PSuOrdr.php::PSuOrdrItem::__construct(): begin") ;
		AppDepObject::__construct( "PSuOrdrItem", "Id") ;
		$this->PSuOrdrNo	=	$_pSuOrdrNo ;
		FDbg::dumpL( 0x00000001, "PSuOrdr.php::PSuOrdrItem::__construct(): end") ;
	}
	/**
	 * 
	 */
	function	getNextItemNo() {
		FDbg::dumpL( 0x00000001, "PSuOrdr.php::PSuOrdrItem::getNextItemNo(): begin") ;
		$query	=	sprintf( "SELECT ItemNo FROM PSuOrdrItem WHERE PSuOrdrNo='%s' ORDER BY ItemNo DESC LIMIT 0, 1 ", $this->PSuOrdrNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->ItemNo	=	$row[0] + 10 ;
		}
		FDbg::dumpL( 0x00000001, "PSuOrdr.php::PSuOrdrItem::getNextItemNo(): end") ;
		return $this->_status ;
	}	
}
?>
