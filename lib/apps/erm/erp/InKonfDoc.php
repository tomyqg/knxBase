<?php

/**
 * InKonfDoc.php Application Level Class for printed version of InKonf
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesourceInKonf
 */

require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocRegLetter.php") ;

require_once( "Kunde.php") ;
require_once( "InKonf.php" );
require_once( "ArtikelBestand.php") ;

/**
 * InKonfDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage InKonfDoc
 */
class	InKonfDoc	extends BDocRegLetter {

	public	$_valid	=	false ;
	private	$myInKonf ;
	private	$totalProMwstSatz	=	array() ;
	private	$mwstSatz	=	array( "A" => 19.0, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
	private	$cellCharFmt ;
	private	$cellParaFmtLeft ;
	private	$cellParaFmtCenter ;
	private	$cellParaFmtRight ;
	
	function	__construct() {
	}

	function	setKey( $_inKonfNr) {
		$this->myInKonf	=	new InKonf( $_inKonfNr) ;
		$this->_valid	=	$this->myInKonf->_valid ;
	}

	function	createPDF( $_key, $_val, $_pdfName='') {
		$myColWidths	=	array( /*10,*/ 10, 23, 70, 10, 17, 17) ;

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
		$myRow	=	new BRow( BRow::RTHeaderPS) ;
		$myRow->addCell( 0, new BCell( "Pos.", $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( "", $this->cellParaFmtLeft)) ;
		$myRow->addCell( 0, new BCell( "Artikel Nr.", $this->cellParaFmtLeft)) ;
		$myRow->addCell( 0, new BCell( "Bezeichung", $this->cellParaFmtLeft)) ;
		$myRow->addCell( 0, new BCell( "Menge", $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( "Geliefert", $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( "Fehlend", $this->cellParaFmtRight)) ;
		$myTable->addRow( $myRow) ;

		// setup the second table data row
		$this->myKHRow	=	new BRow( BRow::RTDataIT) ;
		$cellArtKompHeader	=	new BCell( "Weitere Details zu diesem Artikel (Optionen/Komponenten):\n", $this->cellParaFmtLeft) ;
		$this->myKHRow->addCell( 3, $cellArtKompHeader) ;
		$this->myKHRow->disable() ;
		$myTable->addRow( $this->myKHRow) ;
		
		// setup the first table data row
		$myRow	=	new BRow( BRow::RTDataIT) ;
		$this->cellPosNr	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellPosNr) ;
		$this->cellSubPosNr	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellSubPosNr) ;
		$this->cellArtikelNr	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArtikelNr) ;
		$this->cellArtikelBez1	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArtikelBez1) ;
		$this->cellMenge	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellMenge) ;
		$cellGeliefert	=	new BCell( "", $this->cellParaFmtRight) ;
		$cellGeliefert->setBorder( BCell::BTFull) ;
		$myRow->addCell( 0, $cellGeliefert) ;
		$cellFehlt	=	new BCell( "", $this->cellParaFmtRight) ;
		$cellFehlt->setBorder( BCell::BTFull) ;
		$myRow->addCell( 0, $cellFehlt) ;
		$myTable->addRow( $myRow) ;
		
		// setup the first table header line
		$stockRow	=	new BRow( BRow::RTDataIT) ;
		$lagerOrtCell	=	new BCell( "", $this->cellParaFmtLeft) ;
		$stockRow->addCell( 3, $lagerOrtCell) ;
		$lagerMengeCell	=	new BCell( "", $this->cellParaFmtRight) ;
		$stockRow->addCell( 4, $lagerMengeCell) ;
		$myTable->addRow( $stockRow) ;

		/**
		 *
		 */
		BDocRegLetter::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setInfo( 1, "Konfektionierschein", "") ;
		$this->setInfo( 2, "Konfektion Nr.:", $this->myInKonf->InKonfNr) ;
		$this->setInfo( 3, "Datum:", $this->myInKonf->Datum) ;
		$this->setInfo( 4, "", "") ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, "", "") ;
		$this->setInfo( 8, "", "") ;
		$this->setInfo( 9, "", "") ;

		$this->setRef( "Konfektionierauftrag") ;

		$this->begin() ;

		//
		$this->addMyXML( $this->myInKonf->Prefix) ;

		$this->skipMyLine() ;

		//
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;
		$myInKonfPosten	=	new InKonfPosten( $this->myInKonf->InKonfNr) ;
		error_log( "======> " . $this->myInKonf->InKonfNr) ;
		$myArtikel	=	new Artikel() ;
		$myArtikelBestand	=	new ArtikelBestand() ;
		$itemCnt	=	0 ;
		for ( $myInKonfPosten->firstFromDb( "InKonfNr", "Artikel", array(
																		"ArtikelBez1" => "var",
																		"ArtikelBez2" => "var",
																		"MengenText" => "var",
																		"MengenEinheit" => "var",
																		"ModeCuOrdr" => "var",
																		"Comp" => "var"
																	), "ArtikelNr") ;
				$myInKonfPosten->_valid == 1 ;
				$myInKonfPosten->nextFromDb()) {
			if ( $myInKonfPosten->MengeErf > 0) {

				/**
				 * Lagerbestand einlesen
				 */
				$myArtikelBestand->_valid	=	0 ;
				$myArtikelBestand->ArtikelNr	=	$myInKonfPosten->ArtikelNr ;
				$myArtikelBestand->getDefault() ;
				if ( $myArtikelBestand->_valid == 1) {
					$lagerOrtCell->setData( "Lager: " . $myArtikelBestand->LagerOrt) ;
					$lagerMengeCell->setData( sprintf( "%d", $myArtikelBestand->Lagerbestand)) ;
				} else {
					$lagerOrtCell->setData( "kein gueltiger Lagerort vorhanden") ;
					$lagerMengeCell->setData( "INV") ;
				}

				/**
				 * set the table cell data
				 */
				$myArtikel->setArtikelNr( $myInKonfPosten->ArtikelNr) ;
				$this->cellPosNr->setData( $myInKonfPosten->PosNr) ;
				$this->cellArtikelNr->setData( $myInKonfPosten->ArtikelNr) ;
				$myArtikelText	=	$myArtikel->getFullTextLF( $myInKonfPosten->MengeProVPE) ;
				$this->cellArtikelBez1->setData( iconv('UTF-8', 'windows-1252', $myArtikelText)) ;
				$this->cellMenge->setData( $myInKonfPosten->MengeErfGes) ;

				/**
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$printLine	=	FALSE ;
				if ( strlen( $myInKonfPosten->SubPosNr) == 0) {
					$itemCnt++ ;
					$printLine	=	TRUE ;
				} else if ( $myInKonfPosten->myArtikel->ArtType == 1) {
					if ( $myInKonfPosten->MengeErf > 0) {
						$printLine	=	TRUE ;
					}
				}

				/**
				 * IF this is the main item, output all the data
				 */
				if ( $printLine) {
					/**
					 *
					 */
					$this->cellSubPosNr->setData( $myInKonfPosten->SubPosNr) ;
					$cellGeliefert->setData( "") ;
					$cellFehlt->setData( "") ;
					$subPosHeader	=	FALSE ;
				} else {
					if ( ! $subPosHeader) {
						$this->myKHRow->enable() ;
					}
					$this->cellSubPosNr->setData( $myInKonfPosten->SubPosNr) ;
					$cellGeliefert->setData( "") ;
					$cellFehlt->setData( "") ;
					$subPosHeader	=	TRUE ;
				}

				$this->cellPosNr->setData( $itemCnt) ;
				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
				if ( $myInKonfPosten->Comp == 1 && $myInKonfPosten->ModeCuOrdr == 2) {
					$this->cellPosNr->setData( "") ;
					$this->cellSubPosNr->setData( "") ;

					$this->myKHRow->enable() ;
					FDbg::get()->dump( "InKonfDoc::getPDF(...), Artikelkomponenten werden ausgegeben") ;
					$this->showArtikelKomp( $myInKonfPosten->getArtikel()->ArtikelNr) ;
				}
			}
			$this->emptyTableRow( 5) ;
		}
		$this->endTable() ;
		$this->addMyXML( $this->myInKonf->Postfix) ;

		if ( $_pdfName == '') {
			$this->end( $this->path->Archive . "InKonf/" . $this->myInKonf->InKonfNr . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
		return $this->myInKonf->getAsXML() ;
	}

	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( "Konfektionierung", $this->defParaFmt) ;
		$_frm->addLine( sprintf( "Konfektion Nr. %s, %s", $this->myInKonf->InKonfNr, $this->myInKonf->Datum), $this->defParaFmt) ;

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
		switch ( $_token) {
		case	"shippingadr"	:
			break ;
		case	"invoicingadr"	:
			break ;
		}
	}

	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
	}

	/**
	 *
	 */
	function	showArtikelKomp( $_artikelNr) {
		FDbg::get()->dump( "InKonfDoc::showArtikelKomp( '%s')", $_artikelNr) ;
		$this->myKHRow->enable() ;
		$actArtKomp	=	new ArtKomp() ;
		$actArtKomp->ArtikelNr	=	$_artikelNr ;
		for ( $actArtKomp->firstFromDb() ; $actArtKomp->_valid == 1 ; $actArtKomp->nextFromDb()) {
			FDbg::get()->dumpL( 99, "Pos=%d, ArtikelNr=%s", $actArtKomp->PosNr, $actArtKomp->CompArtikelNr) ;
			if ( $actArtKomp->CompMenge > 0) {

				/**
				 * set the table cell data
				 */
				$this->cellArtikelNr->setData( $actArtKomp->getArtikel()->ArtikelNr) ;
				$myArtikelText	=	$actArtKomp->getArtikel()->getFullTextLF( $actArtKomp->CompMengeProVPE) ;
				$this->cellArtikelBez1->setData( $myArtikelText) ;
				$this->cellMenge->setData( $actArtKomp->CompMenge) ;

				$this->punchTable() ;
				$this->myKHRow->disable() ;		// disable in every case
			}
		}
	}

	/**
	 *
	 */
	function	printIt( $_prn) {
		if ( strcmp( $_prn, "-") != 0) {
			$systemCmd	=	"lpr -P " . $_prn . " " . $this->path->Archive . "Packzettel/" . $this->myInKonf->InKonfNr . ".pdf " ;
			system( $systemCmd) ;
		}

	}

}

?>
