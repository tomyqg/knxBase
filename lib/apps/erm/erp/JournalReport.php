<?php
/**
 * JournalReport.php Application Level Class for printed version of Journal
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * 
 * Required following POST variables to be set:
 * 
 * _FMarketId		required to filter the "Market" for this price list
 * 
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocRegReport.php") ;
require_once( "Journal.php") ;
/**
 * JournalReport - Base Class
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package Application
 * @subpackage Journal
 */
class	JournalReport	extends BDocRegReport {
	private	$myJournal ;
	/**
	 *
	 */
	function	__construct() {
		FDbg::dumpL( 0x00000002, "JournalReport.php::JournalReport::__construct():") ;
	}
	/**
	 * 
	 * @param $_invNo
	 */
	function	setKey( $_invNo="") {
		FDbg::dumpL( 0x00000002, "JournalReport.php::JournalReport::setKey( _invNo):") ;
	}
	/**
	 * createPDF
	 * create the PDF document and returns the complete filename (path+file)
	 * hooked to hdlObjectPDF()
	 */
	function	createPDF( $_key="", $_id=-1, $_pdfName="") {
		$this->_createPDF( $_key, $_id, $_pdfName) ;
	}
	function	getPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "Journal Report/" . $this->myJournal->JournalNo . ".pdf" ;
		}
		return $_pdfName ;
	}
	function	printPDF( $_key="", $_id=-1, $_pdfName="") {
		if ( $_pdfName == "") {
			$_pdfName	=	$this->path->Archive . "Journal Report/" . $this->myJournal->JournalNo . ".pdf" ;
		}
		if ( $this->cuComm->autoprint) {
			$cmd	=	"lpr -P " . $this->printer->cuComm . " " . $_pdfName ;
			system( $cmd) ;
		}
		return $_pdfName ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_pdfName
	 */
	function	_createPDF( $_key="", $_id=-1, $_pdfName="") {
		$myColWidths	=	array( 15, 20, 70, 12, 12, 15, 15) ;
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
		$myTable->addCols( $myColWidths) ;

		/**
		 * setup the first table header line
		 */
		$this->psRowMain	=	new BRow( BRow::RTHeaderPS) ;
		$this->psRowMain->addCell( 0, new BCell( "Line", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Date", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Description", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Debit", $this->cellParaFmtLeft)) ;
		$this->psRowMain->addCell( 0, new BCell( "Credit", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Debit", $this->cellParaFmtRight)) ;
		$this->psRowMain->addCell( 0, new BCell( "Credit", $this->cellParaFmtRight)) ;
		$myTable->addRow( $this->psRowMain) ;
		/**
		 * Aufsetzten der Zeile für "Übertrag von Seite ...:"
		 */
		$this->cfRow	=	new BRow( BRow::RTHeaderCF) ;
		$this->cfRow->addCell( 2, new BCell( "Uebertrag von Seite: %pp%", $this->cellParaFmtLeft)) ;
		$cellCarryFromDebit	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->cfRow->addCell( 5, $cellCarryFromDebit) ;
		$cellCarryFromCredit	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->cfRow->addCell( 6, $cellCarryFromCredit) ;
		$myTable->addRow( $this->cfRow) ;
		/**
		 * setup the first table data row
		 */
		$myRow	=	new BRow( BRow::RTDataIT) ;
		$this->cellLineNo	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellLineNo) ;
		$this->cellDate	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellDate) ;
		$this->cellDescription	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellDescription) ;
		$this->cellAccountDebit	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellAccountDebit) ;
		$this->cellAccountCredit	=	new BCell( "", $this->cellParaFmtLeft) ;
		$myRow->addCell( 0, $this->cellAccountCredit) ;
		$this->cellAmountDebit	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellAmountDebit) ;
		$this->cellAmountCredit	=	new BCell( "", $this->cellParaFmtRight) ;
		$myRow->addCell( 0, $this->cellAmountCredit) ;
		$myTable->addRow( $myRow) ;
				
		/**
		 * Aufsetzten der Zeile für "Übertrag auf Seite ...:"
		 */
		$this->ctRow	=	new BRow( BRow::RTFooterCT) ;
		$this->ctRow->addCell( 2, new BCell( "Uebertrag auf Seite: %np%", $this->cellParaFmtLeft)) ;
		$cellCarryToDebit	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->ctRow->addCell( 5, $cellCarryToDebit) ;
		$cellCarryToCredit	=	new BCell( "", $this->cellParaFmtRight) ;
		$this->ctRow->addCell( 6, $cellCarryToCredit) ;
		$myTable->addRow( $this->ctRow) ;
				
		/**
		 *
		 */
		BDocRegReport::__construct() ;
		$this->setSize( BDoc::DocSizeA4) ;
		$this->setType( BDoc::DocTypeRegReport) ;

		$this->begin() ;

		//
		$myPrefix	=	"" ;
		if ( $_POST['_IFilterAccountNo'] != "") {
			$myAccount	=	new Account( $_POST['_IFilterAccountNo']) ;
			$myPrefix	=	"Konto Nr.:  " . $_POST['_IFilterAccountNo'] ;
			$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPrefix)) ;
			$myPrefix	=	"Bezeichnung:  " . $myAccount->Description1 ;
			$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPrefix)) ;
			$this->skipLine() ;
		}
		if ( $_POST['_IFilterDateFrom'] != "") {
			$myPrefix	=	"Von :  " . $_POST['_IFilterDateFrom'] ;
			$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPrefix)) ;
			$myPrefix	=	"Bis :  " . $_POST['_IFilterDateTo'] ;
			$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPrefix)) ;
			$this->skipLine() ;
		}
		/**
		 *
		 */
//		$this->addMyXML( iconv( 'UTF-8', 'windows-1252', $this->myJournal->Prefix)) ;

		/**
		 *
		 */
		$lastCuDlvrNo	=	"" ;
		$this->addTable( $myTable) ;
		$myJournalLI	=	new JournalLineItem() ;				// no specific object we need here
		$myJournalLI->addCol( "DeAcc", "varchar") ;
		$myJournalLI->addCol( "CrAcc", "varchar") ;
		if ( $_POST['_IFilterAccountNo'] != "") {
			$cond	=	"(    C.AccountDebit  = '".$_POST['_IFilterAccountNo']."' " ;
			$cond	.=	"  OR C.AccountCredit = '".$_POST['_IFilterAccountNo']."') " ;
		}
		if ( $_POST['_IFilterDateFrom'] != "") {
			if ( $cond != "")
				$cond	.=	"AND " ;
			$cond	.=	"( '".$_POST['_IFilterDateFrom']."' <= C.Date AND C.Date <= '".$_POST['_IFilterDateTo']."') " ;
		}
		$myJournalLI->setIterCond( $cond) ;
		$myJournalLI->setIterJoin(
						"LEFT JOIN Account AS De ON De.AccountNo = C.AccountDebit AND De.SubAccountNo = '' "
					.	"LEFT JOIN Account AS Cr ON Cr.AccountNo = C.AccountCredit AND Cr.SubAccountNo = '' ",
						"De.Description1 AS DeAcc, Cr.Description1 AS CrAcc ") ;
		$myJournalLI->setIterOrder( "ORDER BY C.LineNo, C.ItemNo ") ;
		$sumDebitPage	=	0.0 ;
		$sumCreditPage	=	0.0 ;
		foreach ( $myJournalLI as $key => $obj) {
			/**
			 * set the table cell data
			 */
			$this->cellLineNo->setData( $myJournalLI->LineNo/* . "." . $myJournalLI->ItemNo*/) ;
			$this->cellDate->setData( $myJournalLI->Date) ;

			/**
			 * determine if we need to print this line and how we have to print it
			 * IF this is a main line, ->print it
			 * IF this is an option to an article, ->print it
			 */
			$printLine	=	TRUE ;
			/**
			 * IF this is the main item, output all the data
			 */

			$this->cellDescription->setData( $myJournalLI->Description) ;
			$this->cellAccountDebit->setData( $myJournalLI->AccountDebit) ;
			$this->cellAccountCredit->setData( $myJournalLI->AccountCredit) ;
			$this->cellAmountDebit->setData( $myJournalLI->AmountDebit) ;
			$this->cellAmountCredit->setData( $myJournalLI->AmountCredit) ;
			/**
			 *
			 */
			$sumDebitPage	+=	$myJournalLI->AmountDebit ;
			$sumCreditPage	+=	$myJournalLI->AmountCredit ;
			$cellCarryFromDebit->setData( $sumDebitPage) ;
			$cellCarryFromCredit->setData( $sumCreditPage) ;
			$cellCarryToDebit->setData( $sumDebitPage) ;
			$cellCarryToCredit->setData( $sumCreditPage) ;
			$subPosHeader	=	FALSE ;
			$this->punchTable() ;
		}
		$this->cellLineNo->setData( "") ;
		$this->cellDate->setData( "") ;
		$this->cellDescription->setData( "Summe aller Journaleintraege") ;
		$this->cellAccountDebit->setData( "") ;
		$this->cellAccountCredit->setData( "") ;
		$this->cellAmountDebit->setData( $sumDebitPage) ;
		$this->cellAmountCredit->setData( $sumCreditPage) ;
		$this->punchTable() ;
		
		/**
		 * now we can complete the setup the teble-end row
		 */
		$this->psRowMain->disable() ;
//		$this->psRow->disable() ;
		$this->ctRow->disable() ;
		$this->cfRow->disable() ;
		
		/**
		 * now we can complete the setup the teble-end row
		 */
		$this->endTable() ;

		//
		$myPostfix	=	"" ;
		$this->addMyXML( iconv('UTF-8', 'windows-1252', $myPostfix)) ;
		
		if ( $_pdfName == '') {
			$_pdfName	=	$this->path->Archive . "Finance/Journal/Journal_" . $_key ;
			if ( $_POST['_IFilterAccountNo'] != "") {
				$_pdfName	.=	"_" . $_POST['_IFilterAccountNo'] ;
			}
			if ( $_POST['_IFilterDateFrom'] != "") {
				$_pdfName	.=	"_" . $_POST['_IFilterDateFrom'] ;
				$_pdfName	.=	"_" . $_POST['_IFilterDateTo'] ;
			}
			$_pdfName	.=	".pdf" ;
		} else {
		}
		$this->end( $_pdfName) ;
	}
	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT', FTr::tr( "Journal Report")), $this->defParaFmt) ;
		$_frm->addLine( iconv( 'UTF-8', 'windows-1252//TRANSLIT',
									FTr::tr( "Financial Journal Report, Key date #1",
											array( "%s:".$this->today()))),
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
