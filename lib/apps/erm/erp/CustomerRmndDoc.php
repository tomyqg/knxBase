<?php

/**
 * KdMahnDoc.php Application Level Class for printed version of KdMahn
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocRegLetter.php") ;
/**
 * KdMahnDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage KdMahn
 */
class	KdMahnDoc	extends BDocRegLetter {

	private	$myKdMahn ;
	private	$totalProMwstSatz	=	array() ;
	private	$mwstSatz	=	array( "A" => 19, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	private	$cellCharFmt ;
	private	$cellParaFmtLeft ;
	private	$cellParaFmtCenter ;
	private	$cellParaFmtRight ;
	public	$_valid	=	false ;
	
	/**
	 *
	 */
	function	__construct() {
	}
	
	function	setKey( $_KdMahnNr) {
		FDbg::get()->dump( "KdMahnDoc::__construct( _KdMahnNr)") ;
		$this->myKdMahn	=	new KdMahn( $_KdMahnNr) ;
		$this->_valid	=	$this->myKdMahn->_valid ;
		$this->lang	=	$this->myKdMahn->getKunde()->Sprache ;
	}

	/**
	 *
	 */
	function	createPDF( $_key, $_val, $_pdfName='') {
		$this->_createPDF( $_key, $_val, $_pdfName) ;
		$myPDFs	=	array( $this->path->Archive . "KdMahn/" . $this->myKdMahn->KdMahnNr . ".pdf",
								$this->path->Archive . "CuInvc/" . $this->myKdMahn->CuInvcNo . "-Kopie.pdf") ;
		$myPDFName	=	$this->path->Archive . "KdMahn/" . $this->myKdMahn->KdMahnNr . "c.pdf" ;
		combinePDFs( $myPDFName, $myPDFs) ;
		return $this->myKdMahn->getAsXML() ;
	}
	function	_createPDF( $_key, $_val, $_pdfName='') {
		$myColWidths	=	array( /*10,*/ 10, 23, 70, 10, 17, 17, 8) ;
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
		$myTable	=	new BTable() ;
		$myTable->addCol( new BCol( 10), true) ;
		$myTable->addCols( $myColWidths) ;
		
		/**
		 * setup the first table header line
		 */
		$this->psRowMain	=	new BRow( BRow::RTHeaderPS) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Invoice no.", null, $this->lang), $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Date", null, $this->lang), $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "Invoice Amount", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "", null, $this->lang), $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( FTr::tr( "", null, $this->lang), $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRowMain) ;
		
		/**
		 * setup the first table data row
		 */
		$myRow	=	new BRow( BRow::RTDataIT) ;
		$this->cellPosNr	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellPosNr) ;
		$this->cellSubPosNr	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellSubPosNr) ;
		$this->cellArtikelNr	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArtikelNr) ;
		$this->cellArtikelBez1	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArtikelBez1) ;
		$this->cellMenge	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellMenge) ;
		$cellPreis	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellPreis) ;
		$cellGesamtPreis	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellGesamtPreis) ;
		$cellTaxKey	=	new BCell( "", $this->cellParaFmtCenter) ;
		$myRow->addCell( 0, $cellTaxKey) ;
		$myTable->addRow( $myRow) ;
		
		/**
		 *
		 */
		BDocRegLetter::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252', $this->myKdMahn->getKunde()->FirmaName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252', $this->myKdMahn->getKunde()->FirmaName2)) ;
		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252', $this->myKdMahn->getKundeKontakt()->getAddrLine())) ;
		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252', $this->myKdMahn->getKunde()->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252', $this->myKdMahn->getKunde()->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252', $this->myKdMahn->getKunde()->getAddrCountry())) ;

		$this->setInfo( 1, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Payment Reminder", null, $this->lang)), "") ;
		$this->setInfo( 2, iconv( 'UTF-8', 'windows-1252', FTr::tr( "Document no.", null, $this->lang).":"), $this->myKdMahn->KdMahnNr) ;
		$this->setInfo( 3, FTr::tr( "Date", null, $this->lang).":", $this->myKdMahn->Datum) ;
		$this->setInfo( 4, FTr::tr( "Customer no.", null, $this->lang).":", $this->myKdMahn->KundeNr . "/" . $this->myKdMahn->KundeKontaktNr) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
//		$this->setInfo( 7, FTr::tr( "Customer", null, $this->lang).":", "") ;
//		$this->setInfo( 8, FTr::tr( "Ref. no.", null, $this->lang).":", $this->myKdMahn->KdRefNr) ;
//		$this->setInfo( 9, FTr::tr( "Ref. date", null, $this->lang).":", $this->myKdMahn->KdRefDatum) ;

		$this->setRef( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Payment Reminder", null, $this->lang))) ;

		$this->begin() ;

		/**
		 *
		 */
		$myLimitDate	=	time() + ( 10 * 24 * 60 * 60) ;
		$myReplTableIn	=	array( "#Anrede", "#CuInvcNo", "#CuInvcDatum", "#Datum", "#Signature", "#LimitDate") ;
		$myReplTableOut	=	array( $this->myKdMahn->getKundeKontakt()->getAnrede(),
									$this->myKdMahn->myCuInvc->CuInvcNo,
									$this->myKdMahn->myCuInvc->Datum,
									$this->myKdMahn->Datum,
									"Karl-Heinz Welter",
									date("Y-m-d", $myLimitDate)) ;
		$myPrefix	=	str_replace( $myReplTableIn, $myReplTableOut, $this->myKdMahn->Prefix) ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPrefix)) ;
		$this->skipMyLine() ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		if ( $this->myKdMahn->CuInvcNo == "") {
			$this->addTable( $myTable) ;

			$myKdMahnPosten	=	new KdMahnPosten( $this->myKdMahn->KdMahnNr) ;

			$lineNet	=	0.0 ;
			$totalNet	=	0.0 ;
			$itemCnt	=	0 ;
//		for ( $myKdMahnPosten->firstFromDb( "KdMahnNr", "CuInvc", array(
//																		"ArtikelBez1" => "var",
//																		"ArtikelBez2" => "var",
//																		"MengenText" => "var",
//																		"MwstSatz" => "var",
//																		"Comp" => "var"
//																), "ArtikelNr") ;
//				$myKdMahnPosten->_valid == 1 ;
//				$myKdMahnPosten->nextFromDb()) {
//			FDbg::get()->dumpL( 2, "Pos=%d, ArtikelNr=%s", $myKdMahnPosten->PosNr, $myKdMahnPosten->ArtikelNr) ;
//			if ( $myKdMahnPosten->Menge > 0) {
//
//				/**
//				 * Artikel Nummer
//				 */
//				$this->cellArtikelNr->setData( $myKdMahnPosten->ArtikelNr) ;
//
//				/**
//				 * Artikel Bezeichnung zusammensetzen
//				 */
//				$myArtikelText	=	"" ;
//				if ( strlen( $myKdMahnPosten->AddText) > 0) {
//					$myArtikelText	.=	$myKdMahnPosten->AddText . "\n" ;
//				}
//				$myArtikelText	.=	iconv('UTF-8', 'windows-1252', $myKdMahnPosten->ArtikelBez1) ;
//				if ( strlen( $myKdMahnPosten->ArtikelBez2) > 0) {
//					$myArtikelText	.=	"\n" . iconv('UTF-8', 'windows-1252', $myKdMahnPosten->ArtikelBez2) ;	
//				}
//				if ( strlen( $myKdMahnPosten->MengenText) > 0) {
//					$myArtikelText	.=	"\n" . $myKdMahnPosten->MengenText ;	
//				}
//				$this->cellArtikelBez1->setData( $myArtikelText) ;
//
//				/**
//				 * determine if we need to print this line and how we have to print it
//				 * IF this is a main line, ->print it
//				 * IF this is an option to an article, ->print it
//				 */
//				$printLine	=	FALSE ;
//				if ( strlen( $myKdMahnPosten->SubPosNr) == 0) {
//					$itemCnt++ ;
//					$printLine	=	TRUE ;
//				} else if ( $myKdMahnPosten->myArtikel->ArtType == 1) {
//					if ( $myKdMahnPosten->Preis > 0.0) {
//						$printLine	=	TRUE ;
//					}
//				}
//
//				/**
//				 * IF this is the main item, output all the data
//				 */
//				error_log( "Here i am ") ;
//				if ( $printLine) {
//
//					$this->cellPosNr->setData( $itemCnt) ;
//					$this->cellSubPosNr->setData( $myKdMahnPosten->SubPosNr) ;
//
//					/**
//					 * do the required calculations
//					 */
//					$lineNet	=	$myKdMahnPosten->Menge * $myKdMahnPosten->Preis ;
//					$totalNet	+=	$lineNet ;
//					if ( ! isset( $totalProMwstSatz[ $myKdMahnPosten->MwstSatz])) 
//						$totalProMwstSatz[ $myKdMahnPosten->MwstSatz]	=	0.0 ;
//					$totalProMwstSatz[ $myKdMahnPosten->MwstSatz]	+=	$lineNet ;
//
//					/**
//					 *
//					 */
//					$buf	=	sprintf( "%d\n%6.2f%%",
//											$myKdMahnPosten->Menge,
//											( $myKdMahnPosten->Preis - $myKdMahnPosten->RefPreis ) / $myKdMahnPosten->RefPreis * 100.0) ;
//					$this->cellMenge->setData( $buf) ;
//
//					$buf	=	sprintf( "%9.2f\n%6.2f",
//											$myKdMahnPosten->RefPreis,
//											( $myKdMahnPosten->Preis - $myKdMahnPosten->RefPreis )) ;
//					$cellPreis->setData( $buf) ;
//					$buf	=	sprintf( "%9.2f",
//											$myKdMahnPosten->Menge * $myKdMahnPosten->Preis) ;
//					$cellCarryTo->setData( $totalNet) ;
//					$cellCarryFrom->setData( $totalNet) ;
//					$cellGesamtPreis->setData( $buf) ;
//					$cellTaxKey->setData( $myKdMahnPosten->MwstSatz) ;
//					$subPosHeader	=	FALSE ;
//				} else {
//					if ( ! $subPosHeader) {
//						$this->myKHRow->enable() ;
//					}
////					$this->cellSubPosNr->setData( $myKdMahnPosten->SubPosNr) ;
//					$this->cellPosNr->setData( "") ;
//					$this->cellSubPosNr->setData( "") ;
//					$buf	=	sprintf( "%d", $myKdMahnPosten->Menge) ;
//					$this->cellMenge->setData( $buf) ;
//					$cellPreis->setData( "") ;
//					$cellGesamtPreis->setData( "") ;
//					$cellTaxKey->setData( "") ;
//					$subPosHeader	=	TRUE ;
//				}
//
//				$this->punchTable() ;
//				$this->myKHRow->disable() ;		// disable in every case
//				if ( $myKdMahnPosten->getArtikel()->Comp == 1 && $myKdMahnPosten->getArtikel()->ModeKdMahn == 2) {
//					$this->cellPosNr->setData( "") ;
//					$this->cellSubPosNr->setData( "") ;
//					$cellPreis->setData( "") ;
//					$cellGesamtPreis->setData( "") ;
//					$cellTaxKey->setData( "") ;
//
//					$this->myKHRow->enable() ;
//					FDbg::get()->dump( "KdMahnDoc::getPDF(...), Artikelkomponenten werden ausgegeben") ;
//					$this->showArtikelKomp( $myKdMahnPosten->getArtikel()->ArtikelNr) ;
//				}
//			}
//		}
			/**
			 * now we can complete the setup the teble-end row
			 */
			$this->psRowMain->disable() ;
//			$this->psRow->disable() ;

			/**
			 * now we can complete the setup the teble-end row
			 */
			$myRow	=	new BRow( BRow::RTFooterTE) ;
			$myRow->addCell( 3, new BCell( FTr::tr( "Total net value", null, $this->lang), $this->cellParaFmtLeft)) ;
			$buf2	=	sprintf( "%.2f", $totalNet) ;
			$myRow->addCell( 6, new BCell( $buf2, $this->cellParaFmtRight)) ;
			$myTable->addRow( $myRow) ;

			/**
			 *
			 */
			$this->endTable() ;
		} else {
			
		}

		/**
		 *
		 */
		$myPostfix	=	str_replace( $myReplTableIn, $myReplTableOut, $this->myKdMahn->Postfix) ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;

		if ( $_pdfName == '') {
			$this->end( $this->path->Archive . "KdMahn/" . $this->myKdMahn->KdMahnNr . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
		return $this->myKdMahn->getAsXML() ;
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
		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Reminder", null, $this->lang)), $this->defParaFmt) ;
		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT',
									FTr::tr( "Reminder no. #1, dated #2",
											array( "%s:".$this->myKdMahn->KdMahnNr, "%s:".$this->myKdMahn->Datum),
											$this->lang)),
									$this->defParaFmt) ;

		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
					$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;
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
