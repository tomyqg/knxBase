<?php

require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocStockLabel75x25.php") ;
require_once( "pkgs/pdfdoc/BDocStockLabel55x25.php") ;
require_once( "pkgs/pdfdoc/BDocStockLabel150x100.php") ;

class	StockLabelDoc	extends	EISSCoreObject	{

	public	$_valid ;
	private	$myArticle ;

	function	__construct( $_stockNr="") {
		error_log( "StockLabelDoc::__construct(\"$_stockNr\")") ;
		if ( $_stockNr != "") {
			$this->myStockLocation	=	new Stock( $_stockNr) ;
		}
	}

	function	setKey( $_stockNr) {
		error_log( "StockLabelDoc::setKey(\"$_stockNr\")") ;
		$this->myStockLocation	=	new StockLocation( $_stockNr) ;
		$this->_valid	=	$this->myStockLocation->_valid ;
	}

	function	setId( $_id) {
		error_log( "StockLabelDoc::setKey(\"$_id\")") ;
		$this->myStockLocation	=	new StockLocation() ;
		$this->myStockLocation->setId( $_id) ;
		$this->_valid	=	$this->myStockLocation->_valid ;
	}

	function	getPDF() {

		$myLbl	=	new BDocArtLbl55x25() ;

		$myLbl->begin() ;
		$myLbl->end( "/srv/www/vhosts/modis-gmbh.eu/Archiv/test.pdf") ;

	}

	function	createPDF( $_key="", $_id=0, $_val="") {
		switch ( $_val) {
			case	"0"	:
				$myLbl	=	new BDocStockLabel55x25() ;
				$myLbl->begin() ;
				$myLbl->addStockId( $this->myStockLocation->StockId . $this->myStockLocation->ShelfId) ;
				$myLbl->end( $this->path->Archive . "test.pdf") ;
				$cmd	=	"lpr -P " . $this->printer->artlabel55x25 . " " . $this->path->Archive . "test.pdf" ;
				break ;
			case	"1"	:
				$myLbl	=	new BDocStockLabel75x25() ;
				$myLbl->begin() ;
				$myLbl->addStockId( $this->myStockLocation->StockId . $this->myStockLocation->ShelfId) ;
				$myLbl->end( $this->path->Archive . "test.pdf") ;
				$cmd	=	"lpr -P " . $this->printer->artlabel55x25 . " " . $this->path->Archive . "test.pdf" ;
				break ;
			case	"2"	:
				$myLbl	=	new BDocStockLabel150x100() ;
				$myLbl->begin() ;
				$myLbl->addStockId( $this->myStockLocation->StockId . $this->myStockLocation->ShelfId) ;
				$myLbl->end( $this->path->Archive . "test.pdf") ;
				$cmd	=	"lpr -o landscape -P " . $this->printer->stocklabel150x100 . " " . $this->path->Archive . "test.pdf" ;
				break ;
			default	:
				$myLbl	=	new BDocStockLabel55x25() ;
				$myLbl->begin() ;
				$myLbl->addStockId( $this->myStockLocation->StockId . $this->myStockLocation->ShelfId) ;
				$myLbl->end( $this->path->Archive . "test.pdf") ;
				$cmd	=	"lpr -P " . $this->printer->artlabel55x25 . " " . $this->path->Archive . "test.pdf" ;
				break ;
		}
		FDbg::dumpL( 0x00000008, "StockLblDoc.php::StockLabelDoc::createPDF(...): cmd ='$cmd'") ;
		system( $cmd) ;
	}

	function	createPDFList( $_key="", $_id=0, $_val="") {
		switch ( $_val) {
			case	"0"	:
				$myLbl	=	new BDocStockLabel55x25() ;
				$prn	=	$this->printer->artlabel55x25 ;
				break ;
			case	"1"	:
				$myLbl	=	new BDocStockLabel75x25() ;
				$prn	=	$this->printer->artlabel75x25 ;
				break ;
			case	"2"	:
				$myLbl	=	new BDocStockLabel150x100() ;
				$prn	=	$this->printer->artlabel150x100 ;
				break ;
			default	:
				$myLbl	=	new BDocStockLabel55x25() ;
				$prn	=	$this->printer->artlabel55x25 ;
				break ;
		}

		$myLbl->begin() ;
		for ( $this->myStockLocation->_firstFromDb( "StockId = \"" . $this->myStockLocation->StockId . "\" AND ShelfId like \"" . $this->myStockLocation->ShelfId . "__\" ORDER BY StockId, ShelfId ") ;
				$this->myStockLocation->_valid ;
				$this->myStockLocation->_nextFromDb()) {
			$myLbl->addStockId( $this->myStockLocation->StockId . $this->myStockLocation->ShelfId) ;
			$myLbl->newPage() ;
		}
		$myLbl->end( $this->path->Archive . "test.pdf") ;
		$cmd	=	"lpr -P " . $prn . " " . $this->path->Archive . "test.pdf" ;
		system( $cmd) ;
	}

	//
	function	cascTokenStart( $_token) {
		error_log( "StockLabelDoc::cascTokenStart: Here's my token: $_token to start") ;
	}

	//
	function	cascTokenEnd( $_token) {
		error_log( "StockLabelDoc::cascTokenEnd: Here's my token: $_token to end") ;
	}

}

?>
