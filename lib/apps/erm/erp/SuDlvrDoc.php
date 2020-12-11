<?php

/**
 * SuDlvrDoc.php Application Level Class for printed version of SuDlvr
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

require_once( "SuDlvr.php" );
require_once( "ArtKomp.php") ;

/**
 * SuDlvrDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage SuDlvr
 */
class	SuDlvrDoc	extends BDocRegLetter {

	private	$mySuDlvr ;
	private	$totalProMwstSatz	=	array() ;
	private	$mwstSatz	=	array( "A" => 19, "B" => 7) ;
		
	function	__construct() {
	}

	function	setKey( $_cuOrdrNo) {
		$this->mySuDlvr	=	new SuDlvr( $_cuOrdrNo) ;
		$this->_valid	=	$this->mySuDlvr->_valid ;
	}

	function	createPDF( $_key, $_val, $_pdfName='') {
		$myColWidths	=	array( /*10,*/0, 23, 110, 10) ;

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
		$myTable->addRow( $myRow) ;

		// setup the second table data row
		$this->myKHRow	=	new BRow( BRow::RTDataIT) ;
		$cellArtKompHeader	=	new BCell( "Weitere Details zu diesem Artikel (Optionen/Komponenten):\n", $this->cellParaFmtLeft) ;
		$this->myKHRow->addCell( 3, $cellArtKompHeader) ;
		$this->myKHRow->disable() ;
		$myTable->addRow( $this->myKHRow) ;
		
		/**
		 * setup the first table data row
		 */
		$myRow	=	new BRow( BRow::RTDataIT) ;
		$this->cellItemNo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellItemNo) ;
		$myRow->addCell( 0, new BCell( "", $this->cellParaFmtRight)) ;
		$this->cellArtikelNr	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArtikelNr) ;
		$this->cellArtikelBez1	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellArtikelBez1) ;
		$this->cellMenge	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellMenge) ;
		$myTable->addRow( $myRow) ;
		
		/**
		 *setup the table 'carry-forward' line
		 */
		
//		$myDocRegLetter	=	new BDocRegLetter() ;
		BDocRegLetter::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, $this->mySuDlvr->getLief()->FirmaName1) ;
		$this->setRcvr( 2, $this->mySuDlvr->getLief()->FirmaName2) ;
		$this->setRcvr( 3, $this->mySuDlvr->getLiefKontakt()->getAddrLine()) ;
		$this->setRcvr( 4, $this->mySuDlvr->getLief()->getAddrStreet()) ;
		$this->setRcvr( 5, $this->mySuDlvr->getLief()->getAddrCity()) ;
		$this->setRcvr( 6, $this->mySuDlvr->getLief()->getAddrCountry()) ;

		$this->setInfo( 1, "Wareneingang", "") ;
		$this->setInfo( 2, "Wareneingang Nr.:", $this->mySuDlvr->SuDlvrNo) ;
		$this->setInfo( 3, "Datum:", $this->mySuDlvr->Datum) ;
		$this->setInfo( 4, "Lieferant Nr.:", $this->mySuDlvr->LiefNr . "/" . $this->mySuDlvr->LiefKontaktNr) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, "Sonstiges:", "") ;
		$this->setInfo( 8, "Ref. Nr.:", $this->mySuDlvr->RefNr) ;
		$this->setInfo( 9, "Ref. Datum:", $this->mySuDlvr->RefDatum) ;

		$this->setRef( "Wareneingang") ;

		$this->begin() ;

		/**
		 * 
		 */
		$this->addMyXML( $this->mySuDlvr->Prefix) ;

		$this->addMyText() ;
		$this->addMyText() ;

		/**
		 * 
		 */
		$lastSuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;
		$mySuDlvrItem	=	new SuDlvrItem( $this->mySuDlvr->SuDlvrNo) ;
		$lineNet	=	0.0 ;
		$totalNet	=	0.0 ;
		$itemCnt	=	0 ;
		for ( $mySuDlvrItem->firstFromDb( "SuDlvrNo", "Artikel",
												array(	"ArtikelBez1" => "var",
														"ArtikelBez2" => "var",
														"MengenText" => "var",
														"MwstSatz" => "var"
												), "ArtikelNr") ;
				$mySuDlvrItem->_valid == 1 ;
				$mySuDlvrItem->nextFromDb()) {
			FDbg::get()->dumpL( 2, "Pos=%d, ArtikelNr=%s", $mySuDlvrItem->ItemNo, $mySuDlvrItem->ArtikelNr) ;
			if ( $mySuDlvrItem->Menge > 0) {

				/**
				 * set the table cell data
				 */
				$this->cellArtikelNr->setData( $mySuDlvrItem->LiefArtNr) ;
				$myArtikelText	=	$mySuDlvrItem->myArtikel->ArtikelBez1 ;
				if ( strlen( $mySuDlvrItem->myArtikel->ArtikelBez2) > 0) {
					$myArtikelText	.=	"\n" . $mySuDlvrItem->myArtikel->ArtikelBez2 ;	
				}
				if ( strlen( $mySuDlvrItem->myArtikel->MengenText) > 0) {
					$myArtikelText	.=	"\n" . $mySuDlvrItem->myArtikel->MengenText ;	
				}
				$this->cellArtikelBez1->setData( $myArtikelText) ;
				$this->cellMenge->setData( $mySuDlvrItem->Menge) ;

				/**
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$itemCnt++ ;
				$printLine	=	TRUE ;

				/**
				 * IF this is the main item, output all the data
				 */
				if ( $printLine) {

					$this->cellItemNo->setData( $itemCnt) ;

					/**
					 * do the required calculations
					 */
					$lineNet	=	$mySuDlvrItem->GesamtPreis ;
					$totalNet	+=	$lineNet ;
					if ( ! isset( $totalProMwstSatz[ $mySuDlvrItem->myArtikel->MwstSatz])) 
						$totalProMwstSatz[ $mySuDlvrItem->myArtikel->MwstSatz]	=	0.0 ;
					$totalProMwstSatz[ $mySuDlvrItem->myArtikel->MwstSatz]	+=	$lineNet ;

					/**
					 *
					 */
					$subPosHeader	=	FALSE ;
				} else {
				}

				$this->punchTable() ;
				if ( $mySuDlvrItem->getArtikel()->Comp == 1 && $mySuDlvrItem->getArtikel()->ModeSuDlvr == 2) {
					$this->cellItemNo->setData( "") ;
					$cellPreis->setData( "") ;
					$cellGesamtPreis->setData( "") ;
					$cellTaxKey->setData( "") ;

					$this->myKHRow->enable() ;
					FDbg::get()->dump( "SuDlvrDoc::getPDF(...), Artikelkomponenten werden ausgegeben") ;
					$this->showArtikelKomp( $mySuDlvrItem->getArtikel()->ArtikelNr) ;
				}
			}
		}

		/**
		 * 
		 */
		$this->endTable() ;

		/**
		 * 
		 */
		$this->addMyXML( $this->mySuDlvr->Postfix) ;

		if ( $_pdfName == '') {
			$this->end( $this->path->Archive . "SuDlvr/" . $this->mySuDlvr->SuDlvrNo . ".pdf") ;
		} else {
			$this->end( $_pdfName) ;
		}
		return $this->mySuDlvr->getAsXML() ;

	}

	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( "Wareneingang", $this->defParaFmt) ;
		$_frm->addLine( sprintf( "Wareneingang Nr. %s, %s", $this->mySuDlvr->SuDlvrNo, $this->mySuDlvr->Datum), $this->defParaFmt) ;

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
			$this->addMyText( $this->mySuDlvr->getLiefLief()->FirmaName1) ;
			$this->addMyText( $this->mySuDlvr->getLiefLief()->FirmaName2) ;
			$this->addMyText( $this->mySuDlvr->getLiefLief()->Strasse . " " . $this->mySuDlvr->getLiefLief()->Hausnummer) ;
			break ;
		case	"invoicingadr"	:
			$this->addMyText( $this->mySuDlvr->getRechLief()->FirmaName1) ;
			$this->addMyText( $this->mySuDlvr->getRechLief()->FirmaName2) ;
			$this->addMyText( $this->mySuDlvr->getRechLief()->Strasse . " " . $this->mySuDlvr->getRechLief()->Hausnummer) ;
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
		FDbg::get()->dump( "SuDlvrDoc::showArtikelKomp( '%s')", $_artikelNr) ;
		$this->myKHRow->enable() ;
		$actArtKomp	=	new ArtKomp() ;
		$actArtKomp->ArtikelNr	=	$_artikelNr ;
		for ( $actArtKomp->firstFromDb() ; $actArtKomp->_valid == 1 ; $actArtKomp->nextFromDb()) {
			FDbg::get()->dumpL( 99, "Pos=%d, ArtikelNr=%s", $actArtKomp->ItemNo, $actArtKomp->CompArtikelNr) ;
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

}

?>
