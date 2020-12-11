<?php

/**
 * CuDlvrDoc.php Application Level Class for printed version of CuDlvr
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */

require_once( "config.inc.php") ;
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocRegLetter.php") ;

/**
 * KdLeihDoc - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage KdLeih
 */
class	KdLeihDoc	extends BDocRegLetter {

	private	$myKdLeih ;
	private	$totalProMwstSatz	=	array() ;
	private	$mwstSatz	=	array( "A" => 19.0, "A-" => 19.0, "B" => 7.0, "B-" => 7.0) ;
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
	
	function	setKey( $_kdLeihNr) {
		FDbg::get()->dump( "KdLeihDoc::__construct( _kdLeihNr)") ;
		$this->myKdLeih	=	new KdLeih( $_kdLeihNr) ;
		$this->_valid	=	$this->myKdLeih->_valid ;
	}

	/**
	 *
	 */
	function	archive() {
		/**
		 * create the bill-of-delivery-original (Lieferschein-Original)
		 */
		$pdfTargetName	=	$this->path->Archive . "KdLeih/" . $this->myKdLeih->KdLeihNr . ".pdf" ;
		$this->getPDF( $pdfTargetName) ;

		/**
		 * create the bill-of-delivery-copy (Lieferschein-Kopie)
		 */
		$pdfTargetNameKopie	=	$this->path->Archive . "KdLeih/" . $this->myKdLeih->KdLeihNr . "-Kopie.pdf" ;
		overlayPDF( $pdfTargetNameKopie, $pdfTargetName, $this->path->Archive . "overlay_kopie_a4.pdf") ;

	}

	/**
	 *
	 */
	function	createPDF( $_key, $_val, $_pdfName='') {
		$this->_createPDF( $_key, $_val, $_pdfName, false) ;
		$this->_createPDF( $_key, $_val, $_pdfName, true) ;
	}
	
	/**
	 *
	 */
	function	_createPDF( $_key, $_val, $_pdfName='', $_copy=false) {
		$myColWidths	=	array( /*10,*/ 10, 23, 70, 17, 17, 17) ;

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
		$myRow->addCell( 0, new BCell( "", $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( "Jetzt\ngeliefert", $this->cellParaFmtRight)) ;
		$myRow->addCell( 0, new BCell( "", $this->cellParaFmtRight)) ;
		$myTable->addRow( $myRow) ;

		// setup the second table data row
		$myKHRow	=	new BRow( BRow::RTDataIT) ;
		$cellArtKompHeader	=	new BCell( "Weitere Details zu diesem Artikel (Optionen/Komponenten):\n", $this->cellParaFmtLeft) ;
		$myKHRow->addCell( 3, $cellArtKompHeader) ;
		$myKHRow->disable() ;
		$myTable->addRow( $myKHRow) ;
		
		// setup the first table data row
		$myRow	=	new BRow( BRow::RTDataIT) ;
		$cellPosNr	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellPosNr) ;
		$cellSubPosNr	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $cellSubPosNr) ;
		$cellArtikelNr	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $cellArtikelNr) ;
		$cellArtikelBez1	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $cellArtikelBez1) ;
		$cellBereitsGeliefert	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellBereitsGeliefert) ;
		$cellJetztGeliefert	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellJetztGeliefert) ;
		$cellNochZuLiefern	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $cellNochZuLiefern) ;
		$myTable->addRow( $myRow) ;
		
		/**
		 *
		 */
		BDocRegLetter::__construct( $_copy) ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegLetter) ;

		$this->setRcvr( 1, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myKdLeih->getKunde()->CustomerName1)) ;
		$this->setRcvr( 2, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myKdLeih->getKunde()->CustomerName2)) ;
		$this->setRcvr( 3, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myKdLeih->getKundeKontakt()->getAddrLine())) ;
		$this->setRcvr( 4, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myKdLeih->getKunde()->getAddrStreet())) ;
		$this->setRcvr( 5, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myKdLeih->getKunde()->getAddrCity())) ;
		$this->setRcvr( 6, iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myKdLeih->getKunde()->getAddrCountry())) ;

		$this->setInfo( 1, "Teststellung", "") ;
		$this->setInfo( 2, "Teststellung Nr.:", $this->myKdLeih->KdLeihNr) ;
		$this->setInfo( 3, "Datum:", $this->myKdLeih->Datum) ;
		$this->setInfo( 4, "Kunde Nr.:", $this->myKdLeih->KundeNr . "/" . $this->myKdLeih->KundeKontaktNr) ;
		$this->setInfo( 5, "", "") ;
		$this->setInfo( 6, "", "") ;
		$this->setInfo( 7, "Kundenseitig:", "") ;
		$this->setInfo( 8, "Ref. Nr.:", $this->myKdLeih->KdRefNr) ;
		$this->setInfo( 9, "Ref. Datum:", $this->myKdLeih->KdRefDatum) ;

		$this->setRef( "Lieferschein Teststellung") ;

		$this->begin() ;

		//
		$this->addMyXML( iconv( 'UTF-8', 'windows-1252//TRANSLIT',$this->myKdLeih->Prefix)) ;

		$this->addMyText() ;
		$this->addMyText() ;

		//
		$lastPosNr	=	0 ;
		$this->addTable( $myTable) ;
		$myKdLeihPosten	=	new KdLeihPosten( $this->myKdLeih->KdLeihNr) ;
		$itemCnt	=	0 ;
		for ( $myKdLeihPosten->firstFromDb( "KdLeihNr", "Artikel", array(
																		"ArtikelBez1" => "var",
																		"ArtikelBez2" => "var",
																		"MengenText" => "var"
																), "ArtikelNr") ;
				$myKdLeihPosten->_valid == 1 ;
				$myKdLeihPosten->nextFromDb()) {
			if ( $myKdLeihPosten->Menge != 0) {

				/**
				 * set the table cell data
				 */
				$cellArtikelNr->setData( $myKdLeihPosten->ArtikelNr) ;
				$myArtikelText	=	$myKdLeihPosten->ArtikelBez1 ;
				if ( strlen( $myKdLeihPosten->ArtikelBez2) > 0) {
					$myArtikelText	.=	"\n" . $myKdLeihPosten->ArtikelBez2 ;	
				}
				if ( strlen( $myKdLeihPosten->MengenText) > 0) {
					$myArtikelText	.=	"\n" . $myKdLeihPosten->MengenText ;	
				}
				$cellArtikelBez1->setData( iconv( 'UTF-8', 'windows-1252//TRANSLIT',$myArtikelText)) ;
				$cellBereitsGeliefert->setData( "") ;
				$cellJetztGeliefert->setData( $myKdLeihPosten->GelieferteMenge) ;
				$cellNochZuLiefern->setData( "") ;

				/**
				 * determine if we need to print this line and how we have to print it
				 * IF this is a main line, ->print it
				 * IF this is an option to an article, ->print it
				 */
				$printLine	=	FALSE ;
				if ( strlen( $myKdLeihPosten->SubPosNr) == 0) {
					$printLine	=	TRUE ;
					$itemCnt++ ;
				} else if ( $myKdLeihPosten->Menge > 0) {
					$printLine	=	TRUE ;
				}

				/**
				 *
				 */
				if ( $lastPosNr === $myKdLeihPosten->PosNr && ! $subPosHeader) {
					$myKHRow->enable() ;
					$subPosHeader	=	TRUE ;
				}
				if ( $lastPosNr !== $myKdLeihPosten->PosNr) {
					$subPosHeader	=	FALSE ;
				}

				/**
				 * IF this is the main item, output all the data
				 */
				if ( $printLine) {
					/**
					 *
					 */
					$cellPosNr->setData( $itemCnt) ;
					$cellSubPosNr->setData( $myKdLeihPosten->SubPosNr) ;
					$this->punchTable() ;
					$myKHRow->disable() ;		// disable in every case
				}

				$lastPosNr	=	$myKdLeihPosten->PosNr ;
			}
			$this->emptyTableRow( 5) ;
		}

		//
		$this->endTable() ;

		/*
		 * 
		 */
		$myCarr	=	new Carr( $this->myKdLeih->Carrier) ;
		$myReplTableIn	=	array( "#Anrede", "#Datum", "#Carrier","#PkgCount","#TrckCodes", "#RueckDatum") ;
		$myReplTableOut	=	array( $this->myKdLeih->getKundeKontakt()->getAnrede(), $this->myKdLeih->Datum, $myCarr->FullName, $this->myKdLeih->AnzahlPakete,"", $this->myKdLeih->DatumZurueck) ;
		$myPostfix	=	str_replace( $myReplTableIn, $myReplTableOut, $this->myKdLeih->Postfix) ;
		$this->addMyXML( iconv( 'UTF-8', 'windows-1252//TRANSLIT', $myPostfix)) ;

		if ( $_pdfName == '') {
			$_pdfName	=	$this->path->Archive . "KdLeih/" . $this->myKdLeih->KdLeihNr ;
			if ( $_copy)
				$_pdfName	.=	"-Kopie" ;
			$_pdfName	.=	".pdf" ;
		}
		$this->end( $_pdfName) ;
		
		return $this->myKdLeih->getAsXML() ;

	}

	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( "Lieferschein", $this->defParaFmt) ;
		$_frm->addLine( sprintf( "Lieferung Nr. %s, %s", $this->myKdLeih->KdLeihNr, $this->myKdLeih->Datum), $this->defParaFmt) ;

		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
								$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;
			}

	//
	function	cascTokenStart( $_token) {
		switch ( $_token) {
		default	:
			FDbg::dump( "KdLeihDoc::cascTokenStart: invalid token received (non-critical)") ;
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
	function	printIt( $_prn) {
		if ( strcmp( $_prn, "-") != 0) {
			$systemCmd	=	"lpr -P " . $_prn . " " . $this->path->Archive . "KdLeih/" . $this->myKdLeih->KdLeihNr . ".pdf " ;
			FDbg::dumpL( 0x01000000, "KdLeihDoc::printIt: systemCmd='%s'", $systemCmd) ;
			system( $systemCmd) ;
		}

	}

}

?>
