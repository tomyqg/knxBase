<?php

/**
 * BDocFlow.php - Base Class for the creation of PDF documents based on
 * FPDFLib
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcCoreSubSystem
 * @filesource
 */
/**
 * BFlow - Base Class for Flow Definition
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	BFlow	{

	const	typeAuto	=	0 ;
	const	typeMan		=	1 ;
	const	typeCutoff	=	2 ;

	private	$name ;
	private	$myDoc ;
	private	$currFrame ;		// frame which is currently used for the flow
	private	$saveZone ;			//
	private	$type ;

	/**
	 * Enter description here...
	 *
	 * @param BDoc $_doc
	 * @param string $_name
	 */
	function	__construct( $_doc, $_name, $_flowType=BFlow::typeAuto, $_saveZone=20) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
		$this->myDoc	=	$_doc ;
		$this->name		=	$_name ;
		$this->saveZone	=	$_saveZone ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @return sring
	 */
	function	getName() {
		return $this->name ;
	}

	/**
	 * addText
	 * writes a block of text, encoded in ISO-8859-1, to the PDF.
	 * output is inside the frame which is part of the "Auto"-connect flow
	 * and with the fiven paragraph formatting.
	 *
	 * @param BParaFmt	$_par
	 * @param string	$_text
	 * @return void
	 */
	function	addText( $_par, $_text="", $_noAutoLf=false) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
		$_par->getCharFmt()->activate( $this->myDoc->myfpdf) ;
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
		if ( $frm !== false) {
			$tok	=	strtok( $_text, " ");
			$buf	=	"" ;
			while ( $tok !== false) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "token '$tok'") ;
				$bufWidth	=	$this->textWidth( $buf) ;
				$tokWidth	=	$this->textWidth( $tok) ;
				$totWidth	=	$bufWidth + $tokWidth ;
				if ( ( $bufWidth + $tokWidth) < $frm->remWidth()) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "still under the line length [$totWidth/$buf/$tok]") ;
					if ( strlen( $buf) > 0) {
						$buf	.=	" " ;
					}
					$buf	.=	$tok  ;
					$tok	=	"" ;
					$tok	=	strtok( " ");
				} else {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "line length will be exceeded[%s/%s]", $buf, $tok) ;
					$remHeight	=	$frm->addLine( $buf, $_par) ;
					$this->currVerPos	+=	( $_par->getCharFmt()->getCharSize() * $_par->lineSpacing) ;
					$this->currHorPos	=	0.0 ;
					$buf	=	"" ;
					if ( $remHeight < $this->saveZone) {
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "creating new page in between (saveZone := " . mmToPt( $this->saveZone) . ")") ;
						$this->myDoc->newPage() ;
						$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
					}
				}
			}
			if ( strlen( $buf) > 0) {
				$remHeight	=	$frm->addLine( $buf, $_par, $_noAutoLf) ;
				if ( $remHeight < $this->saveZone) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "creating new page at the end") ;
					$this->myDoc->newPage() ;
					$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
				}
			}

		}
		FDbg::end() ;
		return 0 ;
	}

	/**
	 * addText
	 * writes a block of text, encoded in ISO-8859-1, to the PDF.
	 * output is inside the frame which is part of the "Auto"-connect flow
	 * and with the fiven paragraph formatting.
	 *
	 * @param BParaFmt	$_par
	 * @param string	$_text
	 * @return void
	 */
	function	addBC( $_par, $_text="", $_noAutoLf=false) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
		$_par->getCharFmt()->activate( $this->myDoc->myfpdf) ;
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
		if ( $frm !== false) {
			$remHeight	=	$frm->addLine( $_text, $_par) ;
		}
		FDbg::end() ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
		$_par->getCharFmt()->activate( $this->myDoc->myfpdf) ;

		$buffer	=	"" ;
		if ( strlen( $_xml) == 0) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "XML data is empty") ;
			return ;
		}

		$inFormula	=	FALSE ;
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
		$remHeight	=	$frm->remHeight() ;
		$xml	=	new XMLReader() ;
		$xml->XML( iconv( "ISO-8859-1", "UTF-8", "<text>".$_xml."</text>")) ;
		while ( $xml->read()) {
			switch ( $xml->nodeType) {
				case	1	:			// start element
					if ( strcmp( $xml->name, "text") == 0) {
					} else if ( strcmp( $xml->name, "div") == 0) {
					} else if ( strcmp( $xml->name, "ul") == 0) {
					} else if ( strcmp( $xml->name, "li") == 0) {
						$buffer	=	"* " ;
					} else if ( strcmp( $xml->name, "f") == 0) {
						$this->addXMLFormula( $frm, $_par, $xml) ;
						$inFormula	=	TRUE ;
						$buffer	=	"" ;
					} else if ( strcmp( $xml->name, "p") == 0) {
						$buffer	=	"" ;
					} else if ( strcmp( $xml->name, "b") == 0) {
					} else if ( strcmp( $xml->name, "a") == 0) {
					} else if ( strcmp( $xml->name, "br") == 0) {
						$buffer	=	"" ;
						$remHeight	=	$frm->skipLine( $_par) ;
					} else {
						$buffer	.=	$this->cascTokenStart( $xml->name) ;
						$remHeight	=	$this->addText( $_par, $buffer, true) ;
						$buffer	=	"" ;
					}
					break ;
				case	3	:			// text node
					if ( mb_check_encoding( $xml->value, "ISO-8859-1") && ! $inFormula) {
						$buffer	.=	iconv( "UTF-8", "ISO-8859-1//TRANSLIT", $xml->value) ;
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "buffer='" . $buffer . "'") ;
						$remHeight	=	$this->addText( $_par, $buffer, true) ;
					} else {
					}
					$buffer	=	"" ;
					break ;
				case	14	:			// whitespace node
					$buffer	.=	iconv( "UTF-8", "ISO-8859-1", $xml->value) ;
					break ;
				case	15	:			// end element
					if ( strcmp( $xml->name, "text") == 0) {
					} else if ( strcmp( $xml->name, "div") == 0) {
					} else if ( strcmp( $xml->name, "ul") == 0) {
					} else if ( strcmp( $xml->name, "li") == 0) {
						$remHeight	=	$frm->skipLine( $_par) ;
						$buffer	=	"" ;
					} else if ( strcmp( $xml->name, "f") == 0) {
						$inFormula	=	FALSE ;
						FDbg::dumpL( 0x01000000, "BDoc::addXML[15], buffer='%s'", $buffer) ;
						$remHeight	=	$frm->skipLine( $_par) ;
						$buffer	=	"" ;
					} else if ( strcmp( $xml->name, "p") == 0) {
						$remHeight	=	$frm->skipLine( $_par) ;
					} else if ( strcmp( $xml->name, "b") == 0) {
					} else if ( strcmp( $xml->name, "a") == 0) {
					} else if ( strcmp( $xml->name, "br") == 0) {
						$buffer	=	"" ;
					} else {
						$buffer	.=	$this->cascTokenEnd( $xml->name) ;
					}
				case	16	:			// end element
					break ;
			}

			/**
			 * if there's less than 20 mm on this page
			 * goto next page
			 */
			$remHeight	=	$frm->remHeight() ;
			if ( $remHeight < mmToPt( 15)) {
				FDbg::dumpL( 0x08000000, "BDocFlow::addText(): creating new page at the end") ;
				$this->myDoc->newPage() ;
				$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
				$remHeight	=	$frm->remHeight() ;
			}
		}

		/**
		 * since we handle XML text without Auto-Linefeed at the end of the line, we need
		 * to insert an additional linefeed once we are done.
		 */
		$remHeight	=	$frm->skipLine( $_par) ;
		if ( $remHeight < mmToPt( 15)) {
			FDbg::dumpL( 0x08000000, "BDocFlow::addText(): creating new page at the end") ;
			$this->myDoc->newPage() ;
			$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
			$remHeight	=	$frm->remHeight() ;
		}
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_token
	 * @return unknown
	 */
	function	cascTokenStart( $_token) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_token')") ;
		FDbg::end() ;
		return $this->myDoc->cascTokenStart( $_token) ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_token
	 * @return unknown
	 */
	function	cascTokenEnd( $_token) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_token')") ;
		FDbg::end() ;
		return $this->myDoc->cascTokenEnd( $_token) ;
	}

	/**
	 * addXMLFormula
	 * writes a formula, encoded in XML format, to the PDF.
	 * output is inside the fiven frame and with the fiven paragraph formatting.
	 *
	 * @param int $_xml identifier
	 * @return void
	 */
	function	addXMLFormula( $_frm, $_par, $_xml) {
		$buffer	=	"" ;
		$inFormula	=	TRUE ;
		$_horPos	=	0.0 ;
		$_verPos	=	0.0 ;
		$verOffs	=	0.0 ;

		while ( $_xml->read() && $inFormula) {
			switch ( $_xml->nodeType) {
				case	1	:			// start element
					if ( strcmp( $_xml->name, "div") == 0) {
					} else if ( strcmp( $_xml->name, "f") == 0) {
						$inFormula	=	TRUE ;
					} else if ( strcmp( $_xml->name, "n") == 0) {
						$_par->getCharFmt()->activate( $this->myDoc->myfpdf) ;
						$verOffs	=	1.5 ;
					} else {
					}
					break ;
				case	3	:			// text node
					if ( mb_check_encoding( $_xml->value, "ISO-8859-1")) {
						$buffer	.=	iconv( "UTF-8", "ISO-8859-1//TRANSLIT", $_xml->value) ;
						FDbg::dumpL( 0x01000000, "BDoc::addXMLFormula, buffer='%s'", $buffer) ;
						$textWidth	=	$this->textWidth( $buffer) ;
						$this->myDoc->myfpdf->Text( $_frm->horOffs + $_frm->currHorPos + $_horPos,
														$_frm->verOffs + $_frm->currVerPos + $_verPos + $_par->getCharFmt()->getCharSize(),
														$buffer) ;
						$_horPos	+=	$textWidth ;
					} else {
					}
					$buffer	=	"" ;
					break ;
				case	14	:			// whitespace node
					$buffer	.=	iconv( "UTF-8", "ISO-8859-1", $_xml->value) ;
					break ;
				case	15	:			// end element
					if ( strcmp( $_xml->name, "div") == 0) {
					} else if ( strcmp( $_xml->name, "f") == 0) {
						$inFormula	=	FALSE ;
					} else if ( strcmp( $_xml->name, "n") == 0) {
						$this->myDoc->myfpdf->Text( $_frm->horOffs + $_frm->currHorPos + $_horPos,
														$_frm->verOffs + $_frm->currVerPos + $_verPos + $_par->getCharFmt()->getCharSize(),
														$buffer) ;
						$verOffs	=	0 ;
					} else {
					}
				case	16	:			// end element
					break ;
			}
		}
	}

	/**
	 * addTable
	 * add a table to the content-frame
	 *
	 * @param BPara $_par paragraph format to use for the table
	 * @param BTable $_table table to be added
	 */
	function	addTable( $_par, $_table) {
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
		$this->myTablePar	=	$_par ;
		$this->myTable	=	$_table ;
		$this->inTable	=	FALSE ;
		$this->cellFrame	=	new BFrame( $this->myDoc, "CellFrame", "", 0.0, 0.0, 0.0, 0.0) ;
		//
		for ( $actRow=$this->myTable->getFirstRow( BRow::RTHeaderTS) ; $actRow !== FALSE ; $actRow=$this->myTable->getNextRow()) {
			$this->punchTableRow( $actRow) ;
		}
		$frm->currVerPos	+=	1.5 ;		// now add the height of everythign we have output'
		$this->tableHead() ;
		$this->inTable	=	TRUE ;
	}

	/**
	 * tableHead
	 * output the table head, comprising:
	 *  - header block page start (PS = PageStart-Head)
	 *  - continuation block (CF = CarryFrom-Line), but only if not on first page
	 *
	 * @return void
	 */
	function	tableHead() {
		//
		//		fpdf_set_active_font( $this->myfpdf, $this->fontId, mmToPt(3), false, false );
		//		fpdf_set_fill_color( $this->myfpdf, ul_color_from_rgb( 0.1, 0.1, 0.1 ) );
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
		$frm->currVerPos	+=	3.5 ;		// now add some additional room for readability
		for ( $actRow=$this->myTable->getFirstRow( BRow::RTHeaderPS) ; $actRow !== FALSE ; $actRow=$this->myTable->getNextRow()) {
			if ( $actRow->isEnabled()) {
				$this->punchTableRow( $actRow) ;
			}
		}
//		$this->frmContent->currVerPos	+=	1.5 ;		// now add the height of everythign we have output'
		if ( $this->inTable) {
			for ( $actRow=$this->myTable->getFirstRow( BRow::RTHeaderCF) ; $actRow !== FALSE ; $actRow=$this->myTable->getNextRow()) {
				if ( $actRow->isEnabled()) {
					$this->punchTableRow( $actRow) ;
				}
			}
			$frm->currVerPos	+=	1.5 ;		// now add the height of everythign we have output'
		}
	}

	/**
	 * punchTable
	 * output the table data (PageStart-Head)
	 *
	 * @return void
	 */
	function	punchTable( $_lastLine=false) {
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
		for ( $actRow=$this->myTable->getFirstRow( BRow::RTDataIT) ; $actRow !== FALSE ; $actRow=$this->myTable->getNextRow()) {
			if ( $actRow->isEnabled()) {
				$this->punchTableRow( $actRow) ;
			}
		}
		$frm->currVerPos	+=	1.5 ;		// now add the height of everythign we have output'

		/**
		 * if there's less than 20 mm on this page
		 *	goto next page
		 */
		$remHeight	=	$frm->remHeight() ;
		if ( $remHeight < mmToPt( $this->saveZone) && !$_lastLine) {
			$this->tableFoot() ;
			$this->myDoc->newPage() ;
			$this->tableHead() ;
		}
	}

	/**
	 * punchTable
	 * output the table foot, comprising:
	 *  - continuation block (CT = CarryTo-Line)
	 *  - footer block page end (PE = PageEnd-Line)
	 *
	 * @return void
	 */
	function	tableFoot() {

		/**
		 *
		 */
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
		if ( $this->myDoc->pageNr >= 1) {
			for ( $actRow=$this->myTable->getFirstRow( BRow::RTFooterCT) ; $actRow !== FALSE ; $actRow=$this->myTable->getNextRow()) {
				if ( $actRow->isEnabled()) {
					$this->punchTableRow( $actRow) ;
				}
			}
			$frm->currVerPos	+=	1.5 ;		// now add the height of everything we have output'
		}
		for ( $actRow=$this->myTable->getFirstRow( BRow::RTFooterPE) ; $actRow !== FALSE ; $actRow=$this->myTable->getNextRow()) {
			$this->punchTableRow( $actRow) ;
		}
		$frm->currVerPos	+=	1.5 ;		// now add the height of everythign we have output'
	}

	/**
	 * endTable
	 * output the table foot, comprising:
	 *  - table-end block (TE = TableEnd-Line(s))
	 *
	 * @return void
	 */
	function	endTable() {

		/**
		 *
		 */
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
		for ( $actRow=$this->myTable->getFirstRow( BRow::RTFooterTE) ; $actRow !== FALSE ; $actRow=$this->myTable->getNextRow()) {
			$this->punchTableRow( $actRow) ;
		}
		$frm->currVerPos	+=	$this->myTable->currVerPos ;
		$frm->currVerPos	+=	1.5 ;		// now add the height of everythign we have output'

		/**
		 * if there's less than 20 mm on this page
		 *	goto next page
		 */
//		$remHeight	=	$frm->skipLine(	$this->myTablePar) ;
//		if ( $remHeight < mmToPt( $this->saveZone)) {
//			$this->tableFoot() ;
//			$this->myDoc->newPage() ;
//			$this->tableHead() ;
//		}
	}

	/**
	 * punchTableRow
	 * writes a table row to the PDF file in the content-frame
	 *
	 * @param int $_myRow Row type to be written
	 * @return void
	 */
	function	punchTableRow ( $_myRow) {
		$maxHeight	=	0.0 ;
		$myHeight	=	0.0 ;

		/**
		 * Alle Spalten-Inhalte der Tabelle ausgeben
		 */
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;
		$remHeight	=	$frm->remHeight() ;
		for ( $actCol=$this->myTable->getFirstCol() ;
			$actCol !== FALSE ;
			$actCol=$this->myTable->getNextCol()) {
			$colNdx	=	$this->myTable->getColNdx() ;
			$actCell	=	$_myRow->getCellAt( $colNdx) ;
			if ( $actCell !== FALSE) {
				$actData	=	$actCell->getData() ;
				if ( is_string( $actData)) {
					$altBuf	=	sprintf( "%s", $actData) ;
					$actBuf	=	str_replace( "%np%", $this->myDoc->pageNr + 1, $altBuf) ;
					$altBuf	=	$actBuf ;
					$actBuf	=	str_replace( "%pp%", $this->myDoc->pageNr - 1, $altBuf) ;
				} else if ( is_int( $actData)) {
					$actBuf	=	sprintf( "%d", $actData) ;
				} else if ( is_float( $actData)) {
					$actBuf	=	sprintf( "%9.2f", $actData) ;
				}
				if ( mb_check_encoding( $actBuf, "UTF-8")) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "UTF-8 to latin1 encoding required") ;
					$myBuffer	=	mb_convert_encoding( $actBuf, "latin1") ;
					$actBuf	=	$myBuffer ;
				}
				$textWidth	=	$this->textWidth( $actBuf) ;
				$this->cellFrame->horOffs	=	$frm->horOffs + mmToPt( $actCol->getHorPos()) ;
				$this->cellFrame->verOffs	=	$frm->verOffs + $frm->currVerPos ;
				$this->cellFrame->width	=	mmToPt( $actCol->getWidth()) ;
				$this->cellFrame->height	=	30 ;
				$this->cellFrame->currHorPos	=	0.0 ;
				$this->cellFrame->currVerPos	=	0.0 ;

				$this->addCellText( $this->cellFrame, $actCell->getParFmt(), $actBuf) ;

				if ( $this->cellFrame->currVerPos > $maxHeight) {
					$maxHeight	=	$this->cellFrame->currVerPos ;
				}
			}
		}

		/**
		 * Alle Spalten-Rahmen der Tabelle ausgeben
		 */
		for ( $actCol=$this->myTable->getFirstCol() ;
		$actCol !== FALSE ;
		$actCol=$this->myTable->getNextCol()) {
			$colNdx	=	$this->myTable->getColNdx() ;
			$actCell	=	$_myRow->getCellAt( $colNdx) ;
			if ( $actCell !== FALSE) {
				$this->cellFrame->horOffs	=	$actCol->getHorPos() ;
				$this->cellFrame->verOffs	=	$this->myTable->currVerPos ;
				$this->cellFrame->width	=	$actCol->getWidth() ;
				$this->cellFrame->height	=	30 ;
				$this->cellFrame->currHorPos	=	0.0 ;
				$this->cellFrame->currVerPos	=	0.0 ;
				if ( $actCell->getBorder() == BCell::BTFull) {
					//					fpdf_new_path( $this->myfpdf) ;
					//					fpdf_moveto( $this->myfpdf,
					//						mmToPt( $this->frmContentAct->horOffs + $this->cellFrame->horOffs),
					//						mmToPt( $this->frmContentAct->verOffs + $this->frmContentAct->currVerPos + $this->myTablePar->getCharFmt()->getCharSize())) ;
					//					fpdf_lineto( $this->myfpdf,
					//						mmToPt( $this->frmContentAct->horOffs + $this->cellFrame->horOffs + $this->cellFrame->width),
					//						mmToPt( $this->frmContentAct->verOffs + $this->frmContentAct->currVerPos + $this->myTablePar->getCharFmt()->getCharSize())) ;
					//					fpdf_close_path( $this->myfpdf) ;
					//					fpdf_stroke( $this->myfpdf) ;
				}
			}
		}
		$frm->currVerPos	+=	$maxHeight ;		// now add the height of everythign we have output'
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "currVerPos=" . $this->myTable->currVerPos) ;
		return 0 ;
	}

	/**
	 *
	 * writes a table row to the PDF file in the content-frame
	 *
	 * @param int $_myRow Row type to be written
	 * @return void
	 */
	function	emptyTableRow ( $_height) {

		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "Auto") ;

		/**
		 *
		 */
		$frm->currVerPos	+=	mmToPt( $_height) ;		// now add the height of everythign we have output'
	}

	/**
	 * addText
	 * writes a block of text, encoded in ISO-8859-1, to the PDF.
	 * output is inside the fiven frame and with the fiven paragraph formatting.
	 *
	 * @param BFrame $_frm Frame to be used
	 * @param BParaFmt $_par Paragraph format to be used
	 * @param string $_text Text in ISO-8859-1 encoding to be added
	 * @param float $_width
	 * @param float $_horPos additional horizontal offset
	 * @param float $_verPOs additional vertical offset
	 * @param bool $_nolf skip line feed at the end of the output
	 * @return void
	 */
	function	addCellText( $_frm, $_par, $_text="") {
		$_par->getCharFmt()->activate( $this->myDoc->myfpdf) ;
		$_width	=	$_frm->width ;
		$currBuf	=	"" ;
		$nextBuf	=	"" ;
		$doIt	=	FALSE ;
		for ( $i0=0 ; $i0 < strlen( $_text) ; $i0++) {
			switch ( $_text[$i0]) {
				case	"\n"	:
					$currBuf	.=	$nextBuf ;
					$nextBuf	=	"" ;
					$doIt	=	TRUE ;
					break ;
				case	" "	:
					$currBuf	.=	$nextBuf . " " ;
					$nextBuf	=	"" ;
					break ;
				default	:
					$currWidth	=	$this->textWidth( $currBuf . $nextBuf . $_text[$i0]) ;
					if ( $currWidth > $_width) {
						$doIt	=	TRUE ;
					}
					$nextBuf	.=	$_text[$i0] ;
					break ;
			}
			if ( ( $i0 + 1) == strlen( $_text)) {
				$currBuf	.=	$nextBuf ;
				$nextBuf	=	"" ;
				$doIt	=	TRUE ;
			}
			if ( $doIt) {
				$_frm->addLine( $currBuf, $_par) ;
				$currBuf	=	"" ;
				$doIt	=	FALSE ;
			}
		}
		return 0 ;
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
		$textWidth	=	$this->myDoc->myfpdf->GetStringWidth( $_text) ;
		return $textWidth ;
	}

}

?>
