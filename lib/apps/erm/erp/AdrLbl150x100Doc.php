<?php

/**
 * AdrLbl150x100Doc.php Application Level Class for printed version of CuRFQ
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */

require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocAdrLbl150x100.php") ;

require_once( "option.php") ;

/**
 * AdrLbl150x100Doc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage AdrLbl
 */
class	AdrLbl150x100Doc	extends BDocAdrLbl150x100	{

	private	$myAdr ;
	private	$myAdrKontakt ;
	private	$cellCharFmt ;
	private	$cellParaFmtLeft ;
	private	$cellParaFmtCenter ;
	private	$cellParaFmtRight ;
	public	$_valid	=	false ;
	private	$lang ;
	
	/**
	 *
	 */
	function	__construct() {
	}
	
	function	setKey( $_adrNr) {
		$this->myAdr	=	new Kunde( $_adrNr) ;
		$this->myAdrKontakt	=	new KundeKontakt( $_adrNr, "001") ;
		$this->_valid	=	$this->myAdr->_valid & $this->myAdrKontakt->_valid ;
		$this->lang	=	$this->myAdr->Sprache ;
	}

	/**
	 *
	 */
	function	_createPDF( $_key, $_val, $_pdfName='') {
		$myColWidths	=	array( /*10,*/ 15, 15) ;

		/**
		 * 
		 */
		$this->cellCharFmt	=	new BCharFmt() ;
		$this->cellCharFmt->setCharSize( 9) ;

		$this->cellParaFmtLeft	=	new BParaFmt() ;
		$this->cellParaFmtLeft->setCharFmt( $this->cellCharFmt) ;
		$this->cellParaFmtLeft->setAlignment( BParaFmt::alignLeft) ;
		
		$this->cellParaFmtCenter	=	new BParaFmt() ;
		$this->cellParaFmtCenter->setCharFmt( $this->cellCharFmt) ;
		$this->cellParaFmtCenter->setAlignment( BParaFmt::alignCenter) ;

		$this->cellParaFmtRight	=	new BParaFmt() ;
		$this->cellParaFmtRight->setCharFmt( $this->cellCharFmt) ;
		$this->cellParaFmtRight->setAlignment( BParaFmt::alignRight) ;
		
		/**
		 *
		 */
		BDocAdrLbl150x100::__construct() ;

		$this->setRcvr( 1, $this->myAdr->FirmaName1) ;
		$this->setRcvr( 2, $this->myAdr->FirmaName2) ;
		$this->setRcvr( 3, $this->myAdrKontakt->getAddrLine()) ;
		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $this->myAdr->getAddrStreet())) ;
		$this->setRcvr( 5, $this->myAdr->getAddrCity()) ;
		$this->setRcvr( 6, $this->myAdr->getAddrCountry()) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Small package", null, $this->lang)), "") ;
		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 1", null, $this->lang)).":", $this->myAdr->KundeNr) ;
		$this->setInfo( 3, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Date", null, $this->lang)).":", $this->today()) ;
		$this->setInfo( 4, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 2", null, $this->lang)).":", "") ;
		$this->setInfo( 5, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 3", null, $this->lang)).":", "") ;
		$this->setInfo( 6, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 4", null, $this->lang)).":", "") ;
		$this->setInfo( 7, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Weight", null, $this->lang)).":", "") ;
		$this->setInfo( 8, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Width", null, $this->lang)).":", "") ;
		$this->setInfo( 9, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Length", null, $this->lang)).":", "") ;
		$this->setInfo( 10, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Height", null, $this->lang)).":", "") ;

		$this->begin() ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;

		if ( $_pdfName == '') {
			$this->end( $this->path->Archive . "Kunde/Lbl" . $this->myAdr->KundeNr . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
	}

	function	createPDF( $_key, $_val, $_pdfName='') {
		$this->_createPDF( $_key, $_val, $_pdfName) ;
		return $this->myAdr->getAsXML() ;
	}
	function	genPDF( $_key, $_val, $_pdfName='') {
		$this->_createPDF( $_key, $_val, $_pdfName) ;
	}

	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {
	}

	/**
	 *
	 */
	function	cascTokenStart( $_token) {
	}

	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
	}

}

?>
