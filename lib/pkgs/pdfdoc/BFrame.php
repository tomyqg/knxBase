<?php

/**
 * BDoc.php - Base Class for the creation of PDF documents based on
 * FPDFLib
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcCoreSubSystem
 * @filesource
 */
/**
 * BFrame - Base Class for Frame Definition
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	BFrame	{

	const	FrameTypeFlow	=	 1 ;
	const	FrameTypeBG	=	11 ;

	const	AnchorTypePage	=	 1 ;	// frame anchored relativ to page
	const	AnchorTypePara	=	 2 ;	// frame anchored relative to higher level paragraph

	public	$myDoc ;
	private	$name ;
	private	$flowName ;
	private	$frameType ;
	public	$alignment ;
	public	$horOffs ;
	public	$verOffs ;
	public	$width ;
	public	$height ;
	public	$currHorPos ;
	public	$currVerPos ;

	/**
	 * Enter description here...
	 *
	 * @param BDoc		$_doc		Document to which this flow belongs
	 * @param string	$_flowName	Name of the flow
	 * @param float		$_horOffs	horizontal position of frame on the page in [mm]
	 * @param float		$_verOffs	vertical position of frame on the page in [mm]
	 * @param float		$_width		width of the frame in [mm]
	 * @param float		$_height	height of the frame in [mm]
	 */
	function	__construct( $_doc, $_name, $_flowName, $_horOffs, $_verOffs, $_width, $_height) {
		$this->myDoc	=	$_doc ;
		$this->name		=	$_name ;
		$this->flowName		=	$_flowName ;
		$this->frameType	=	BFrame::FrameTypeFlow ;
		$this->horOffs		=	mmToPt( $_horOffs) ;	// relative to page
		$this->verOffs		=	mmToPt( $_verOffs) ;	// relative to page
		$this->width		=	mmToPt( $_width) ;	// relative to page
		$this->height		=	mmToPt( $_height) ;	// relative to page
		$this->currHorPos	=	0 ;		// current position inside this frame
		$this->currVerPos	=	0 ;		// current position inside this frame
	}

	/**
	 * returns the vertical start position relativ to the page
	 *
	 * @return float Vertical offset of frame relative to the page
	 */
	function	getVerOffs() {
		return $this->verOffs ;
	}
	/**
	 * returns the horizontal start position relativ to the page
	 *
	 * @return float Horizontal offset of frame relative to the page
	 */
	function	getHorOffs() {
		return $this->horOffs ;
	}
	/**
	 * returns the width of the frame
	 *
	 * @return float Width of frame
	 */
	function	getWidth() {
		return $this->width ;
	}

	/**
	 * returns the height of the frame
	 *
	 * @return float Height of frame
	 */
	function	getHeight() {
		return $this->height ;
	}

	/**
	 * returns the height of the frame
	 *
	 * @return float Height of frame
	 */
	function	getName() {
		return $this->name ;
	}

	/**
	 * returns the height of the frame
	 *
	 * @return float Height of frame
	 */
	function	getFlowName() {
		return $this->flowName ;
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
	function	skipLine( $_par) {
		$this->currVerPos	+=	$_par->getCharFmt()->getCharSize() * $_par->lineSpacing ;		// SPECIAL
		$this->currHorPos	=	0.0 ;
		return $this->height - $this->currVerPos ;
	}

	/**
	 * addText
	 * writes a block of text, encoded in ISO-8859-1, to the PDF.
	 * output is inside the fiven frame and with the fiven paragraph formatting.
	 *
	 * @param BParaFmt $_par Paragraph format to be used
	 * @param string $_text Text in ISO-8859-1 encoding to be added
	 * @return void
	 */
	function	addLine( $_text="", $_par, $_noAutoLf=false) {
		$_par->getCharFmt()->activate( $this->myDoc->myfpdf) ;
		$textWidth	=	$this->textWidth( $_text) ;
		switch ( $_par->getAlignment()) {
			case	BParaFmt::alignLeft	:
				$this->myDoc->myfpdf->Text( $this->horOffs + $this->currHorPos,
				$this->verOffs + $this->currVerPos + $_par->getCharFmt()->getCharSize(),
				$_text) ;
				$this->currHorPos	+=	$textWidth ;
				break ;
			case	BParaFmt::alignCenter	:
				$this->myDoc->myfpdf->Text( $this->horOffs + $this->currHorPos + ( $this->getWidth() - $textWidth) / 2,
				$this->verOffs + $this->currVerPos + $_par->getCharFmt()->getCharSize(),
				$_text) ;
				$this->currHorPos	+=	$textWidth ;
				break ;
			case	BParaFmt::alignRight	:
				$this->myDoc->myfpdf->Text( $this->horOffs + $this->currHorPos + ( $this->getWidth() - $textWidth),
				$this->verOffs + $this->currVerPos + $_par->getCharFmt()->getCharSize(),
				$_text) ;
				$this->currHorPos	+=	$textWidth ;
				break ;
		}
		if ( ! $_noAutoLf) {
			$this->currHorPos	=	0 ;
			$this->currVerPos	+=	$_par->getCharFmt()->getCharSize() * $_par->getLineSpacing() ;
		}
		$this->currHorOffs	=	0 ;
		$this->remainingHeight	=	$this->height - $this->currVerPos ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."(...)", "height := " . $this->height) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."(...)", "currXXVerPos := " . $this->currVerPos) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."(...)", "remaining height := " . $this->remainingHeight) ;
		return $this->height - $this->currVerPos ;
	}

	/**
	 * addText
	 * writes a block of text, encoded in ISO-8859-1, to the PDF.
	 * output is inside the fiven frame and with the fiven paragraph formatting.
	 *
	 * @param BParaFmt $_par Paragraph format to be used
	 * @param string $_text Text in ISO-8859-1 encoding to be added
	 * @return void
	 */
	function	addBC( $_text="", $_par, $_noAutoLf=false) {
		$_par->getCharFmt()->activate( $this->myDoc->myfpdf) ;
		$textWidth	=	$this->textWidth( $_text) ;
		error_log( "BDocFrame::addBC: verPos    := $this->currVerPos") ;
		error_log( "BDocFrame::addBC: verOffset := $this->verOffs") ;
		switch ( $_par->getAlignment()) {
			case	BParaFmt::alignLeft	:
				$this->myDoc->myfpdf->Code39( $this->horOffs + $this->currHorPos, $this->verOffs + $this->currVerPos, $_text, 2.5, mmToPt( 7)) ;
				$this->currHorPos	+=	$textWidth ;
				break ;
			case	BParaFmt::alignCenter	:
//				$this->myDoc->myfpdf->Code39( $this->horOffs + $this->currHorPos + ( $this->getWidth() - $textWidth) / 2, $_text, 3, mmToPt( 7)) ;
				if ( $_par->getCharFmt()->getBCName() == "Code128") {
					$this->myDoc->myfpdf->Code128( $this->horOffs + $this->currHorPos, $this->verOffs + $this->currVerPos, $_text, 2*($_par->getCharFmt()->getCharStretch()/100.0), $_par->getCharFmt()->getCharSize()) ;
				} else {
					$this->myDoc->myfpdf->Code39( $this->horOffs + $this->currHorPos, $this->verOffs + $this->currVerPos, $_text, 2*($_par->getCharFmt()->getCharStretch()/100.0), $_par->getCharFmt()->getCharSize()) ;
				}
				$this->currHorPos	+=	$textWidth ;
				break ;
			case	BParaFmt::alignRight	:
//				$this->myDoc->myfpdf->Code39( $this->horOffs + $this->currHorPos + ( $this->getWidth() - $textWidth), $_text, 3, mmToPt( 7)) ;
				$this->myDoc->myfpdf->Code39( $this->horOffs + $this->currHorPos, $this->verOffs + $this->currVerPos, $_text, 3, $_par->getCharFmt()->getCharSize()) ;
				$this->currHorPos	+=	$textWidth ;
				break ;
		}
		if ( ! $_noAutoLf) {
			$this->currHorPos	=	0 ;
			$this->currVerPos	+=	$_par->getCharFmt()->getCharSize() * $_par->getLineSpacing() ;
		}
		$this->currHorOffs	=	0 ;
		return $this->height - $this->currVerPos ;
	}

	/**
	 *
	 */
	function	remHeight() {
		return $this->height - $this->currVerPos ;
	}

	/**
	 *
	 */
	function	remWidth() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
		FDbg::end() ;
		return $this->width - $this->currHorPos ;
	}

	/**
	 * Enter description here...
	 *
	 */
	function	_dump() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( ...)") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "flowId='" . $this->flowName . "'") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "verOffs=" . $this->verOffs) ;
		FDbg::end() ;
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

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_fpdf
	 */
	function	showFrame( $_fpdf) {
		$_fpdf->Line( $this->horOffs, $this->verOffs, $this->horOffs + $this->width, $this->verOffs) ;
		$_fpdf->Line( $this->horOffs + $this->width, $this->verOffs, $this->horOffs + $this->width, $this->verOffs + $this->height) ;
		$_fpdf->Line( $this->horOffs + $this->width, $this->verOffs + $this->height, $this->horOffs, $this->verOffs + $this->height) ;
		$_fpdf->Line( $this->horOffs, $this->verOffs + $this->height, $this->horOffs, $this->verOffs) ;
	}
}

?>
