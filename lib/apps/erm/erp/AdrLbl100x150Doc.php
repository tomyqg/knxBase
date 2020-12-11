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
require_once( "pkgs/pdfdoc/BDocAdrLbl100x150.php") ;

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
class	AdrLbl100x150Doc	extends BDocAdrLbl100x150	{

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
		FDbg::dumpL( 0x00000001, "AdrLbl100x150Doc.php::AdrLbl100x150Doc::__construct(): begin") ;
		FDbg::dumpL( 0x00000001, "AdrLbl100x150Doc.php::AdrLbl100x150Doc::__construct(): end") ;
	}
	/**
	 * 
	 * @param $_dummy
	 */
	function	setKey( $_dummy) {
		return true ;
	}
	/**
	 * 
	 * @param $_id
	 */
	function	setId( $_id) {
		return true ;
	}
	/**
	 *
	 */
	function	_createPDF( $_key, $_id, $_pdfName='') {
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
		BDocAdrLbl100x150::__construct() ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $this->myAdr->FirmaName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $this->myAdr->FirmaName2)) ;
		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $this->myAdrKontakt->getAddrLine())) ;
		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $this->myAdr->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $this->myAdr->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $this->myAdr->getAddrCountry())) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Small package", null, $this->lang)), "") ;
//		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 1", null, $this->lang)).":", $this->myAdr->KundeNr) ;
		$this->setInfo( 3, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Date", null, $this->lang)).":", $this->today()) ;
		
		$this->setInfo( 4, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 2", null, $this->lang)).":", $this->getValueIf( "Ref2")) ;
		$this->setInfo( 5, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 3", null, $this->lang)).":", $this->getValueIf( "Ref3")) ;
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
			$this->end( $this->path->Archive . "Kunde/Lbl" . "LABEL" . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
	}
	/**
	 *
	 */
	function	_createPDFNew( $_veColi, $_veColiPos, $_rcvr, $_pdfName='') {
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
		BDocAdrLbl100x150::__construct() ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $_rcvr->FirmaName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $_rcvr->FirmaName2)) ;
		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $_rcvr->getAddrLine())) ;
		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $_rcvr->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $_rcvr->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252//TRANSLIT', $_rcvr->getAddrCountry())) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Small package", null, $this->lang)), "") ;
//		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 1", null, $this->lang)).":", $this->myAdr->KundeNr) ;
		$this->setInfo( 3, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Date", null, $this->lang)).":", $this->today()) ;
		
		$this->setInfo( 4, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 2", null, $this->lang)).":", $this->getValueIf( "Ref2")) ;
		$this->setInfo( 5, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 3", null, $this->lang)).":", $this->getValueIf( "Ref3")) ;
		$this->setInfo( 6, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Ref. 4", null, $this->lang)).":", $_veColi->VeColiNr."/".$_veColiPos->PosNr) ;
		$this->setInfo( 7, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Weight", null, $this->lang)).":", $_veColiPos->Gewicht) ;
		$this->setInfo( 8, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Width", null, $this->lang)).":", $_veColiPos->EinzelDimL) ;
		$this->setInfo( 9, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Length", null, $this->lang)).":", $_veColiPos->EinzelDimB) ;
		$this->setInfo( 10, iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Height", null, $this->lang)).":", $_veColiPos->EinzelDimH) ;
		$this->setPostage( sprintf( "%3.2f Euro", $_veColiPos->VrsndKost)) ;

		$this->begin() ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;

		if ( $_pdfName == '') {
			$this->end( $this->path->Archive . "Lbl" . "LABEL" . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_pdfName
	 */
	function	createPDF( $_key, $_id, $_pdfName='', $_postage="") {
		try {
			error_log( $_key) ;
			if ( $_key == "Kunde") {
				$this->myAdrKontakt	=	new KundeKontakt() ;
				$this->myAdrKontakt->setId( $_id) ;
				$this->myAdr	=	new Kunde( $this->myAdrKontakt->KundeNr) ;
			} else if ( $_key == "Lief") {
				$this->myAdrKontakt	=	new LiefKontakt() ;
				$this->myAdrKontakt->setId( $_id) ;
				$this->myAdr	=	new Lief( $this->myAdrKontakt->LiefNr) ;
			} else if ( $_key == "Adr") {
				$this->myAdrKontakt	=	new AdrKontakt() ;
				$this->myAdrKontakt->setId( $_id) ;
				$this->myAdr	=	new Adr( $this->myAdrKontakt->AdrNr) ;
			} else if ( $_key == "CuDlvr") {
				$this->myCuDlvr	=	new CuDlvr( $_id) ;
				$this->myAdrKontakt	=	new KundeKontakt() ;
				$this->myAdrKontakt->setKundeKontaktNr( $this->myCuDlvr->KundeNr, $this->myCuDlvr->KundeKontaktNr) ;
				$this->myAdr	=	new Kunde( $this->myCuDlvr->KundeNr) ;
				$this->setValue( "Ref1", $this->myCuDlvr->CuDlvrNo) ;
				$this->setValue( "Ref2", $this->myCuDlvr->CuOrdrNo) ;
			} else if ( $_key="VeColi") {
			}
			$this->lang	=	$this->myAdr->Sprache ;
			$this->setPostage( $_postage) ;
			$this->_createPDF( $_key, $_id, $_pdfName) ;
			system( "lpr -P lbl01ac " . $_pdfName) ;
			return $this->myAdr->getXMLString() ;
		} catch ( Exception $e) {
			throw $e ;
		}
	}
	function	genPDF( $_key, $_id, $_pdfName='') {
		$this->_createPDF( $_key, $_id, $_pdfName) ;
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
