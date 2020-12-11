<?php

/**
 * FmkBox.php - typesetting box
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package FmkCore
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
class	FmkBox	{
	var	$myFPDF	=	null ;		// must refer to the object which contains a reference to the fpdf object
	var	$horOffs ;				// where it starts on the page
	var	$verOffs ;
	var	$width ;				// how big it is
	var	$height ;
	var	$currHorPos ;			// where we are writing
	var	$currVerPos ;
	var	$currParaFmt	=	null ;
	var	$currCharFormat	=	null ;
	/**
	 * Enter description here...
	 *
	 * @param BDoc		$_parent	Reference to the frame above
	 * @param string	$_flowName	Name of the flow
	 * @param float		$_horOffs	horizontal position of frame on the page in [mm]
	 * @param float		$_verOffs	vertical position of frame on the page in [mm]
	 * @param float		$_width		width of the frame in [mm]
	 * @param float		$_height	height of the frame in [mm]
	 */
	function	__construct( $_frame, $_fpdf) {
		FDbg::begin( 1, "FmkBox.php", "FmkBox", "__construct( <_frame>, <_fpdf>)") ;
		FDbg::trace( 11, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "__construct()", "frame := '".$_frame->getName()."'") ;
		/**
		 *
		 */
		$this->frame	=	$_frame ;			// := $myDoc
		$this->myFPDF	=	$_fpdf ;
		/**
		 *
		 */
		$this->horOffs		=	$_frame->horOffs ;	// relative to page
		$this->verOffs		=	$_frame->verOffs ;	// relative to page
		$this->width		=	$_frame->width ;	// relative to page
		$this->height		=	$_frame->height ;	// relative to page
		$this->currHorPos	=	0 ;		// current position inside this frame
		$this->currVerPos	=	0 ;		// current position inside this frame
		/**
		 *
		 */
		$this->currPage	=	$this->frame->parent ;
		$this->currParaFmt	=	$this->currPage->parent->paraFmt[ "default"] ;
		$this->currCharFormat	=	$this->currParaFmt->charFmt ;
		/**
		 *
		 */
		FDbg::end( 1, "FmkBox.php", "FmkBox", "__construct( <_frame>, <_fpdf>)") ;
	}
	function	reset() {
		$this->currHorPos	=	0 ;
		$this->currVerPos	=	0 ;
	}
	/**
	 *
	 * @param string $_xmlString	complete string <doc>...</doc> including tags
	 */
	function	addText( $_xml, $_remainder=null) {
		FDbg::begin( 1, "FmkBox.php", "FmkBox", "addText( <_xmlString>)") ;
		/**
		 * process remaining data from last call
		 */
		if ( $_remainder != null) {
			$remainder	=	$this->addLine( $_remainder->buffer) ;
		} else {
			$remainder	=	null ;
		}
		/**
		 * create XML reader, assign text, create HTML tree and output it
		 */
		$xml	=	$_xml ;
		while ( $xml->read() && $remainder == null) {
			switch ( $xml->nodeType) {
			case	1	:			// start element
				FDbg::trace( 103, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "begin element encountered") ;
				switch ( $xml->name) {
				case	"br"	:
					FDbg::trace( 104, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "<br> encountered") ;
					$remainder	=	$this->skipLine() ;
					$this->currParaFmt->resetTabs() ;
					if ( $remainder) {
						FDbg::trace( 104, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "could not process everything on <br/>, remaining buffer '".$remainder->buffer."'") ;
					}
					break ;
				case	"para"	:
					$paraFmt	=	$xml->getAttribute( "paraFmt") ;
					if ( $paraFmt != null) {
						$this->currParaFmt	=	FmkParFmt::getFormat(  $paraFmt) ;
						$this->currCharFormat	=	$this->currParaFmt->charFmt ;
						$this->currParaFmt->resetTabs() ;
					}
					break ;
				case	"tab"	:
					FDbg::trace( 99, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "<tab> encountered") ;
					$tabType	=	"*" ;
					$newPos	=	$this->currParaFmt->getTabStopPos( $tabType) ;
					if ( $newPos > $this->currHorPos) {
						$this->currHorPos	=	$newPos ;
						$remainder	=	$this->addLine( $tabType) ;
					}
					break ;
				case	"table"	:
					$tableFormat	=	$xml->getAttribute( "tableFormat") ;
					if ( $tableFormat != null) {
						$this->currTableFormat	=	FmkTableFmt::getFormat( $tableFormat) ;
						$this->currCharFormat	=	$this->currParaFmt->charFmt ;
					}
//					$this->myFlows['Auto']->addTable( $_par, $_table) ;
					break ;
				}
				break ;
			case	3	:
				$text	=	trim( $xml->value, "\n\t") ;
				FDbg::trace( 103, FDBg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( ...)", "text := '".$text."'") ;
				$remainder	=	$this->addLine( $text) ;
				if ( $remainder) {
					FDbg::trace( 104, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "could not process everything on plain text, remaining buffer '".$remainder->buffer."'") ;
				}
				break ;
			case	15	:			// end element
				FDbg::trace( 103, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "end element encountered") ;
				switch ( $xml->name) {
				case	"br"	:
					FDbg::trace( 104, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "<br> encountered") ;
					break ;
				case	"tab"	:
					FDbg::trace( 104, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "<tab> encountered") ;
					break ;
				case	"para"	:
					FDbg::trace( 104, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "<para> encountered") ;
					$this->newLine() ;
					break ;
				case	"tab"	:
					FDbg::trace( 99, FDbg::mdTrcInfo1, "FmkBox.php", "FmkBox", "addText( <...>)", "<tab> encountered") ;
					$tabType	=	"*" ;
					$newPos	=	$this->currParaFmt->getTabStopPos( $tabType) ;
					if ( $newPos > $this->currHorPos) {
						$this->currHorPos	=	$newPos ;
						$remainder	=	$this->addLine( $tabType) ;
					}
					break ;
				}
				break ;
			}
		}
		FDbg::end( 1, "FmkBox.php", "FmkBox", "addText( <_xmlString>)") ;
		return $remainder ;
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
	function	addLine( $_text="") {
		FDbg::begin( 101, "FmkBox.php", "FmkBox", "addLine( '$_text')") ;
		$myRemainder	=	null ;
		$this->currCharFormat->activate( $this->myFPDF) ;
		$textWidth	=	$this->textWidth( $_text) ;
		switch ( $this->currParaFmt->getAlignment()) {
			case	FmkParFmt::alignLeft	:
				$myWords	=	explode( " ", $_text) ;
				$itMayFit	=	true ;
				$wc	=	0 ;
				$fetchWord	=	true ;
				while ( $itMayFit && ( isset( $myWords[ $wc]) && $myRemainder == null)) {
					if ( $fetchWord) {
						$word	=	$myWords[ $wc] ;
						$wc++ ;
					}
					$fetchWord	=	false ;
					/**
					 * determine length of word
					 */
					$wordWidth	=	$this->textWidth( $word) ;
					if ( ( $this->currHorPos + $wordWidth) <= $this->width) {
						$this->myFPDF->Text( $this->horOffs + $this->currHorPos,
													$this->verOffs + $this->currVerPos + $this->currCharFormat->getCharSize(),
													$word) ;
						$this->currHorPos	+=	$wordWidth ;
						$this->currHorPos	+=	$this->textWidth( " ") ;
						$word	=	"" ;
						$fetchWord	=	true ;
					} else {
						$this->currVerPos	+=	$this->currCharFormat->getCharSize() * $this->currParaFmt->getLineSpacing() ;
						$this->currHorPos	=	0 ;
						if ( ( $this->currVerPos + $this->currCharFormat->getCharSize()) > $this->height) {
							$buffer	=	$word ;
							for ( ; isset( $myWords[ $wc]) ; $wc++) {
								$buffer	.=	" " . $myWords[ $wc] ;
							}
							$myRemainder	=	 new FmkRemainder( $buffer) ;
						}
					}
				}
				break ;
			case	FmkParFmt::alignCenter	:
				$this->myFPDF->Text( $this->horOffs + $this->currHorPos + ( $this->getWidth() - $textWidth) / 2,
											$this->verOffs + $this->currVerPos + $this->currCharFormat->getCharSize(),
											$_text) ;
				$this->currHorPos	+=	$textWidth ;
				break ;
			case	FmkParFmt::alignRight	:
				$this->myFPDF->Text( $this->horOffs + $this->currHorPos + ( $this->getWidth() - $textWidth),
											$this->verOffs + $this->currVerPos + $this->currCharFormat->getCharSize(),
											$_text) ;
				$this->currHorPos	+=	$textWidth ;
				break ;
		}
		$this->currHorOffs	=	0 ;
		FDbg::end( 101, "FmkBox.php", "FmkBox", "addLine( '$_text')") ;
		return $myRemainder ;
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
	function	newLine() {
		$myRemainder	=	null ;
		if ( $this->currHorPos > 0) {
			$this->currVerPos	+=	$this->currCharFormat->getCharSize() * $this->currParaFmt->lineSpacing ;		// SPECIAL
			$this->currHorPos	=	0.0 ;
			if ( ( $this->currVerPos + $this->currCharFormat->getCharSize()) > $this->height) {
				$myRemainder	=	 new FmkRemainder( "") ;
			}
		}
		return $myRemainder ;
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
	function	skipLine() {
		$myRemainder	=	null ;
		$this->currVerPos	+=	$this->currCharFormat->getCharSize() * $this->currParaFmt->lineSpacing ;		// SPECIAL
		$this->currHorPos	=	0.0 ;
		if ( ( $this->currVerPos + $this->currCharFormat->getCharSize()) > $this->height) {
			$myRemainder	=	 new FmkRemainder( "") ;
		}
		return $myRemainder ;
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
		return $this->width - $this->currHorPos ;
	}
	/**
	 * textWidth(...)
	 * Bestimmung der Breite eines Textes in dem gegebenen _fpdf Kontext
	 *
	 * @return float breite des textes in postscript pixeln
	 */
	function	textWidth( $_text) {
		$textWidth	=	$this->myFPDF->GetStringWidth( $_text) ;
		return $textWidth ;
	}
}

class	FmkRemainder {
	var	$buffer	=	"" ;
	function	__construct( $_buffer) {
		$this->buffer	=	$_buffer ;
	}
	function	add( $_remainder) {
		if ( $_remainder != null) {
			$this->buffer	.=	" " . $_remainder->buffer ;
		}
	}
	function	getBuffer() {
		return $this->buffer ;
	}
}

?>
