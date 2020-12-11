<?php

require_once( "pkgs/platform/FDbg.php") ;

require_once( "Artikel.php") ;
require_once( "ArtikelBestand.php") ;
require_once( "base/BInKonfPosten.php") ;

class	InKonfPosten	extends	BInKonfPosten	{

	public	$myArtikel ;
	public	$myCond ;

	function	__construct( $_myInKonfNr='') {
		FDbg::get()->dumpL( 99, "InKonfPosten::__constructor") ;
		BInKonfPosten::__construct() ;
		$this->InKonfNr	=	$_myInKonfNr ;
		$this->myArtikel	=	new Artikel() ;
	}

	/**
	 *
	 * @return void
	 */
	function	firstFromDb() {
		FDbg::get()->dumpL( 99, "InKonfPosten::firstFromDb()") ;
		$this->myCond	=	sprintf( "InKonfNr = '%s' ORDER BY PosNr, SubPosNr ", $this->InKonfNr) ;
		BInKonfPosten::firstFromDb( $this->myCond) ;
		$this->myArtikel->ArtikelNr	=	$this->ArtikelNr ;
		$this->myArtikel->fetchFromDb( FDb::get()) ;
	}

	/**
	 *
	 * @return void
	 */
	function	nextFromDb() {
		FDbg::get()->dumpL( 99, "InKonfPosten::nextFromDb()") ;
		BInKonfPosten::nextFromDb( $this->myCond) ;
		$this->myArtikel->ArtikelNr	=	$this->ArtikelNr ;
		$this->myArtikel->fetchFromDb( FDb::get()) ;
	}

	/**
	 *
	 * @return void
	 */
	function	buche() {
		FDbg::get()->dumpL( 99, "InKonfPosten::buche()") ;
		$myArtikelBestand	=	new ArtikelBestand( $this->ArtikelNr) ;
		$myArtikelBestand->getDefault() ;
		if ( $myArtikelBestand->_valid == 1) {
			$myArtikelBestand->reserve( ( $this->Menge * $this->MengeProVPE ) - $this->MengeReserviert ) ;
			$this->MengeReserviert	+=	( ( $this->Menge * $this->MengeProVPE ) - $this->MengeReserviert ) ;
			$this->updateInDb() ;
		} else {
		}
	}

	/**
	 *
	 */
	function	unbuche() {
		$myArtikelBestand	=	new ArtikelBestand( $this->ArtikelNr) ;
		$myArtikelBestand->getDefault() ;
		if ( $myArtikelBestand->_valid == 1) {
			$myArtikelBestand->unreserve( $this->MengeReserviert ) ;
			$this->MengeReserviert	=	0 ;
			$this->updateInDb() ;
		}
	}

	/**
	 *
	 * @return void
	 */
	function	getNextPosNr() {
		$query	=	sprintf( "SELECT PosNr FROM InKonfPosten WHERE InKonfNr='%s' ORDER BY PosNr DESC LIMIT 0, 1 ", $this->InKonfNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$row    =       mysql_fetch_array( $sqlResult) ;
			$this->PosNr	=	$row[0] + 1 ;
		}
		return $this->_status ;
	}

}

?>
