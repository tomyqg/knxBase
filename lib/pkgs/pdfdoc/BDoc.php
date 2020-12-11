<?php

/**
 * BDoc.php - Base Class for the creation of PDF documents based on
 * FPDFLib
 *
 * A document is made up of the following basic components:
 *
 * Flows				Object: BFlow
 * Frames				Object: BFrame
 * Character Formats	Object: BCharFmt
 * Paragraph Formats	Object: BParaFmt
 * Masterpages			Object:	BPage
 *
 * Additionally Tables can be handled by the BTable object.
 *
 * A flow is what basically connects multiple pages of a document. Allthough
 * there can, and typically also will, be multiple flows in the document, there
 * is only one flow, the so called "Auto"-flow, which decides upon adding
 * new pages to the document (this is considered a constraint right now
 * but will be addressed in one of the future versions).
 *
 * The setup of the document is typically happenign in the following sequence:
 * 1. create the document
 * 2. add the master pages
 * 3. add the main flow
 * 4. add additional flows
 * 5. add the frame(s) with reference to the flow to each master page
 * 6. add character formats
 * 7. add paragraph formats (with reference to the standard character format for
 *    this paragraph
 * 8. add text to the flow
 *
 * Flows are, as mentioned earlier, what glues the various pages of the document
 * together. Therefor text which is expected to flow from one page to another needs
 * to be added by adding it to the flow. Text can also be added to a frame directly,
 * which is typically done for so called background frames.
 *
 * As BDoc is supposed to act as the main user object, there are convenience methods
 * to add text to the "Auto"-flow. These are:
 * addText( some ISO-8859-1 text, BFlowName="Auto") ;
 * addXML( some XML text, BFlowName="Auto")
 * BDoc will automatically pass these methods forward to the "Auto"-flow.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcCoreSubSystem
 * @filesource
 */
define('FPDF_FONTPATH','pkgs/fpdf/font/');
/**
 * Umwandlungsfunktion um [mm] in [dots] auf der Basis von 72 dpi um zu rechnen.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 * @param double|int $x input measure in [mm]
 * return double
 */
function mmToPt( $x ) {
	return 720 * $x / 254 ;
}

/**
 * Umwandlungsfunktion um [dots] in [mm] auf der Basis von 72 dpi um zu rechnen.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 * @param double|int $x input measure in [mm]
 * return double
 */
function ptToMm( $x ) {
	return 254 * $x / 720 ;
}

/**
 * BDoc Core Class for PDF--based printed matters
 * procedural stuff in the start of the file
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */

class	BDoc	extends	EISSCoreObject	{

	const	DocSizeA4		= 1 ;
	const	DocSizeA5		= 2 ;
	const	DocSizeLbl150x100	= 11 ;
	const	DocSizeLbl75x25		= 12 ;
	const	DocSizeLbl100x60	= 13 ;
	const	DocSizeLbl55x25		= 14 ;
	const	DocSizeLbl100x50	= 15 ;
	const	DocSizeLbl100x150	= 16 ;
	const	DocSizeLbl100x55	= 17 ;

	const	DocTypeRegLetter	=  1 ;		// regular letter
	const	DocTypePlainSheet	=  2 ;		// just a plain sheet of paper
	const	DocTypeList		=  3 ;		// list
	const	DocTypeLbl		=  4 ;		// label (single-page)
	const	DocTypeLblMP		=  5 ;		// multi-page label
	const	DocTypeRegReport	=  6 ;		// regular letter

	const	DocSidedSingle		=  1 ;		// single sided, only
	const	DocSidedDouble		=  2 ;		// double sided only odd and even pages
	const	DocSidedSingleWF	=  3 ;		// regular letter
	const	DocSidedDoubleWF	=  4 ;		// just a plain sheet of paper

	/**
	 * attributes needed to deal with the PDF library
	 */
	public	$myfpdf ;
	public	$doc ;
	public	$copy	=	false ;

	public	$pageNr ;
	private	$docSize ;
	private	$docType ;
	private	$docSided ;
	private	$myPages	=	array() ;
	private	$myFrames	=	array() ;
	private	$myFlows	=	array() ;
	private	$mySheets	=	array() ;

	/**
	 * the following are supposed to be the static values for the document
	 */
	public	$sheetWidth ;		// width of the sheet in [mm]
	public	$sheetHeight ;		// height if the sheet in [mm]

	public	$currMasterPage ;

	public	$innerBorder ;		// left border in [mm]
	public	$outerBorder ;		// left border in [mm]
	public	$leftBorder ;		// left border in [mm]
	public	$rightBorder ;		// right border [mm]
	public	$topBorder ;		// top border [mm]
	public	$bottomBorder ;		// bottom border [mm]

	private	$myTable = NULL ;
	private	$inTable ;
	private	$myTablePar = NULL ;
	private	$debugFrames = FALSE ;

	/**
	 *
	 */
	function	__construct( $_copy=false) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_copy)") ;
		EISSCoreObject::__construct( "BDoc") ;
		FError::set( "BDoc", 4712, "ErrorText") ;

		/**
		 * Bibliothek initialisieren und PDF Dokument anlegen
		 */
		$this->copy	=	$_copy ;
		$this->pageNr	=	0 ;
		$this->docSize	=	BDoc::DocSizeA4 ;
		$this->docType	=	BDoc::DocTypeRegLetter ;
		$this->docSided	=	BDoc::DocSidedSingle ;
		$this->debugFrames	=	false ;
		$this->setupDoc() ;
		FDbg::end() ;
	}

	/**
	 * set the size of the document
	 *
	 * @param int $_docSize Size of the document (see also: BDoc::Sizes)
	 */
	function	enableDebug() {
		$this->debugFrames	=	true ;
	}

	/**
	 * set the size of the document
	 *
	 * @param int $_docSize Size of the document (see also: BDoc::Sizes)
	 */
	function	disableDebug() {
		$this->debugFrames	=	false ;
	}

	/**
	 * set the size of the document
	 *
	 * @param int $_docSize Size of the document (see also: BDoc::Sizes)
	 */
	function	setSize( $_docSize) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_docSize)") ;
		$this->docSize	=	$_docSize ;
		$this->setupDoc() ;
		FDbg::end() ;
	}

	/**
	 * set the type of the document
	 *
	 * @param int $_docType Type of the document (see also: BDoc::Types)
	 */
	function	setType( $_docType) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_docType)") ;
		$this->docType	=	$_docType ;
		$this->setupDoc() ;
		FDbg::end() ;
	}

	/**
	 * set the siding of the document
	 * can be either of:
	 * BDoc::DocSidedSingle
	 * BDoc::DocSidedDouble
	 * BDoc::DocSidedSingleWF
	 * BDoc::DocSidedDoubleWF
	 *
	 * @param int $_docSided Siding of the document
	 */
	function	setSided( $_docSided) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_docSided)") ;
		$this->docSided	=	$_docSided ;
		$this->setupDoc() ;
		FDbg::end() ;
	}

	/**
	 * perform teh setup of the document
	 *
	 * @return void
	 */
	function	setupDoc() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		switch ( $this->docSize) {

			case	BDoc::DocSizeA4	:
				$this->sheetWidth	=	210 ;
				$this->sheetHeight	=	297 ;
				$this->innerBorder	=	20 ;
				$this->outerBorder	=	10 ;
				break ;

			case	BDoc::DocSizeLbl100x60	:
				$this->sheetWidth	=	100 ;
				$this->sheetHeight	=	60 ;
				$this->innerBorder	=	2 ;
				$this->outerBorder	=	2 ;
				break ;

			case	BDoc::DocSizeLbl55x25	:
				$this->sheetWidth	=	55 ;
				$this->sheetHeight	=	25 ;
				$this->innerBorder	=	0 ;
				$this->outerBorder	=	0 ;
				break ;
			case	BDoc::DocSizeLbl100x50	:
				$this->sheetWidth	=	100 ;
				$this->sheetHeight	=	50 ;
				$this->innerBorder	=	0 ;
				$this->outerBorder	=	0 ;
				break ;
			case	BDoc::DocSizeLbl100x55	:
				$this->sheetWidth	=	100 ;
				$this->sheetHeight	=	55 ;
				$this->innerBorder	=	0 ;
				$this->outerBorder	=	0 ;
				break ;
			case	BDoc::DocSizeLbl150x100	:
				$this->sheetWidth	=	148 ;
				$this->sheetHeight	=	105 ;
				$this->innerBorder	=	0 ;
				$this->outerBorder	=	0 ;
				break ;

			case	BDoc::DocSizeLbl100x150	:
				$this->sheetWidth	=	105 ;
				$this->sheetHeight	=	148 ;
				$this->innerBorder	=	0 ;
				$this->outerBorder	=	0 ;
				break ;

			case	BDoc::DocSizeLbl75x25	:
				$this->sheetWidth	=	75 ;
				$this->sheetHeight	=	25 ;
				$this->innerBorder	=	0 ;
				$this->outerBorder	=	0 ;
				break ;
		}
		error_log( $this->docSize . ">" . $this->sheetWidth . ":" . $this->sheetHeight) ;
		FDbg::end() ;
	}

	/**
	 * start the document
	 * this function is used to start the creation of the document
	 *
	 * @return void
	 */
	function	begin() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myFormat[]	=	mmToPt( $this->sheetWidth) ;
		$myFormat[]	=	mmToPt( $this->sheetHeight) ;
		error_log( $myFormat[0] . ":" . $myFormat[1]) ;
		$this->myfpdf	=	new FPDF( "P", "pt", $myFormat) ;

		switch ( $this->docType) {
			case	BDoc::DocTypeLbl	:
				break ;
		}
		$this->newPage() ;
		FDbg::end() ;
	}

	/**
	 * end the document
	 * this function is used to end the creation of the document
	 *
	 * @return void
	 */
	function	end( $_pdfName='') {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_pdfNasme')") ;
		$SapiName	=	php_sapi_name() ;
		if ( $SapiName != 'cli' ) {
			if ( strlen( $_pdfName) > 0) {
				$this->myfpdf->Output( $_pdfName, "F") ;
			} else {
				FDbg::dumpL( 0x01000000, "BDoc::end(): Kein Dateiname !") ;
				$this->mem	=	$this->myfpdf->Output( $this->doc, "F" ) ;
				header( "Content-Disposition: inline; filename = TextOut.pdf", true ) ;
				header( "Content-Type: application/pdf",  true ) ;
				header( "Content-Length: $Size", true ) ;
				echo( $this->mem );
			}
		} else {
			$this->myfpdf->Output( $_pdfName, "F") ;
			FDbg::dumpL( FDbg::DBGL2, "dbg: BDoc::end() document written") ;
		}
		FDbg::end() ;
	}

	/**
	 * create a new page for the current document
	 * this function creates a new page. if a page is still open, this page will be closed first
	 * and then a new page will be added. performs setup of the important page parameters like
	 * frame assignments (header, content, footer) for the respective page nr.
	 *
	 * @return void
	 */
	function	newPage() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$this->pageNr++ ;		// goto next page
		if ( $this->pageNr == 1) {
			$this->myfpdf->AddPage() ;
			switch ( $this->docSided) {
				case	BDoc::DocSidedSingle	:
				case	BDoc::DocSidedDouble	:
					$this->currMasterPage	=	$this->myPages["Right"] ;
					break ;
				case	BDoc::DocSidedSingleWF	:
				case	BDoc::DocSidedDoubleWF	:
					$this->currMasterPage	=	$this->myPages["First"] ;
					break ;
			}
			/**
			 *
			 */
			$this->currMasterPage->resetFrames() ;
			$this->setupFirstPage() ;
		} else {
			$this->myfpdf->AddPage() ;
			switch ( $this->docSided) {
				case	BDoc::DocSidedSingle	:
				case	BDoc::DocSidedSingleWF	:
					$this->currMasterPage	=	$this->myPages["Right"] ;
					break ;
				case	BDoc::DocSidedDouble	:
				case	BDoc::DocSidedDoubleWF	:
					$this->currMasterPage	=	$this->myPages["Right"] ;
					break ;
			}
			/**
			 *
			 */
			$this->currMasterPage->resetFrames() ;
			$this->setupMidPage() ;
		}

		/**
		 *
		 */
		if ( $this->myTable !== NULL) {
			$this->myTable->currHorPos	=	0.0 ;
			$this->myTable->currVerPos	=	0.0 ;
		}

		/**
		 *
		 */
		if ( $this->debugFrames) {
			foreach ( $this->currMasterPage->myFrames as $ndx => $frm) {
				$frm->showFrame( $this->myfpdf) ;
			}
			reset( $this->myFrames) ;
		}
		if ( $this->copy) {
			$this->myfpdf->setTextColor( 255, 1, 1) ;
			$this->myfpdf->setFontSize( 72) ;
			$this->myfpdf->Text( 200, 300, " K O P I E ") ;
		}
		FDbg::end() ;
	}

	/**
	 * setupPage
	 * should be implemented in the derived class !
	 *
	 * @return void
	 */
	function	setupPage() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}

	/**
	 * setupFirstPage
	 * should be implemented in the derived class !
	 *
	 * @return void
	 */
	function	setupFirstPage() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}

	/**
	 * setupMidPage
	 * should be implemented in the derived class !
	 *
	 * @return void
	 */
	function	setupMidPage() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}

	/**
	 * setupLastPage
	 * should be implemented in the derived class !
	 *
	 * @return void
	 */
	function	setupLastPage() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}

	/**
	 * addText
	 * writes a block of text, encoded in ISO-8859-1, to the PDF.
	 * output is inside the fiven frame and with the fiven paragraph formatting.
	 *
	 * @param BFrame $_frm Frame to be used
	 * @param BParaFmt $_par Paragraph format to be used
	 * @param string $_text Text in ISO-8859-1 encoding to be added
	 * @param bool $_nolf skip line feed at the end of the output
	 * @return void
	 */
	function	addText( $_par, $_text="", $_nolf=false) {
		$this->myFlows['Auto']->addText( $_par, $_text, $_nolf) ;
		return 0 ;
	}

	/**
	 * addBC
	 * writes a block of text, encoded in ISO-8859-1, to the PDF.
	 * output is inside the fiven frame and with the fiven paragraph formatting.
	 *
	 * @param BFrame $_frm Frame to be used
	 * @param BParaFmt $_par Paragraph format to be used
	 * @param string $_text Text in ISO-8859-1 encoding to be added
	 * @param bool $_nolf skip line feed at the end of the output
	 * @return void
	 */
	function	addBC( $_par, $_text="", $_nolf=false) {
		$this->myFlows['Auto']->addBC( $_par, $_text, $_nolf) ;
		return 0 ;
	}

	/**
	 * skipLine
	 * Inserts an empty paragraph with the height of the character set for this paragraph.
	 * output is inside the fiven frame and with the fiven paragraph formatting.
	 *
	 * @param BFrame $_frm Frame to be used
	 * @param BParaFmt $_par Paragraph format to be used
	 * @return void
	 */
	function	skipLine( $_frm, $_par="") {
//		$_frm->currVerPos	+=	$_par->getCharFmt()->getCharSize() * $_par->lineSpacing ;		// SPECIAL
//		$_frm->currHorPos	=	0.0 ;
		return 0 ;
	}

	/**
	 * addXML
	 * writes a block of text, encoded in XML, to the PDF.
	 * output is inside the given frame and with the fiven paragraph formatting.
	 *
	 * @param BFrame $_frm Frame to be used
	 * @param BParaFmt $_par Paragraph format to be used
	 * @param string $_xml Text in XML
	 * @return void
	 */
	function	addXML( $_par, $_xml) {

		$this->myFlows['Auto']->addXML( $_par, $_xml) ;

		return 0 ;
	}
	/**
	 * addTable
	 * add a table to the content-frame
	 *
	 * @param BPara $_par paragraph format to use for the table
	 * @param BTable $_table table to be added
	 */
	function	addTable( $_par, $_table) {

		$this->myFlows['Auto']->addTable( $_par, $_table) ;

		return 0 ;
	}

	/**
	 * addTable
	 * add a table to the content-frame
	 *
	 * @param BPara $_par paragraph format to use for the table
	 * @param BTable $_table table to be added
	 */
	function	punchTable( $_lastLine=false) {

		$this->myFlows['Auto']->punchTable( $_lastLine) ;

		return 0 ;
	}

	/**
	 * addTable
	 * add a table to the content-frame
	 *
	 * @param BPara $_par paragraph format to use for the table
	 * @param BTable $_table table to be added
	 */
	function	emptyTableRow( $_height) {

		$this->myFlows['Auto']->emptyTableRow( $_height) ;

		return 0 ;
	}

	/**
	 * addTable
	 * add a table to the content-frame
	 *
	 * @param BPara $_par paragraph format to use for the table
	 * @param BTable $_table table to be added
	 */
	function	endTable() {

		$this->myFlows['Auto']->endTable() ;

		return 0 ;
	}

	/**
	 * remainingHeight
	 * remains the return height in the content-frame in [mm]
	 * @return float remaining height in content frame [mm]
	 */
	function	remainingHeight() {
		$remHeight	=	$this->frmContentAct->height - $this->frmContentAct->currVerPos ;
		//		if ( $this->myTable !== NULL) {
		//			$remHeight	-=	$this->myTable->currVerPos ;
		//		}
		return $remHeight ;
	}

	/**
	 *
	 * @return void
	 */
	function	cascTokenStart( $_token) {
	}

	/**
	 *
	 * @return void
	 */
	function	cascTokenEnd( $_token) {
	}

	/**
	 * textWidth(...)
	 * Bestimmung der Breite eines Textes in dem gegebenen _fpdf Kontext
	 * Diese Funktion korrigiert den in der Sybrex Bibliothek vorhandenen Fehler
	 *
	 * @return float breite des textes in postscript pixeln
	 */
	function	textWidth( $_text) {
		$textWidth	=	0 ;
		$textWidth	=	$this->myfpdf->GetStringWidth( $_text) ;
		return $textWidth ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_frm
	 */
	function	addPage( $_page) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <BPage>)") ;
		$this->myPages[ $_page->getName()]	=	$_page ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_frm
	 * 	 */
	function	addFrame( $_frm) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <BFrame>)") ;
		$this->myFrames[ $_frm->getName()]	=	$_frm ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_frm
	 * 	 */
	function	addFlow( $_flow) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <BFlow>)") ;
		$this->myFlows[ $_flow->getName()]	=	$_flow ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 */
	function	_debug() {
		if ( FDbg::enabled()) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Nr. of Pages: " . count( $this->myPages)) ;
			foreach ( $this->myPages as $ndx => $page) {
				$page->_debug() ;
			}
			reset( $this->myFrames) ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Nr. of Flows: " . count( $this->myFlows)) ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Nr. of Frames: " . count( $this->myFrames)) ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Document own attributes") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " => docSize=" . $this->docSize) ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " => docType=" . $this->docType) ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " => docSided=" . $this->docSided) ;
		}
	}

}

?>
