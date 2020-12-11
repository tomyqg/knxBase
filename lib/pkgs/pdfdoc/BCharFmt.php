<?php

/**
 * BDocCharFmt.php - Base Class for the creation of PDF documents based on
 * FPDFLib
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcCoreSubSystem
 * @filesource
 */
/**
 * BCharFmt - Base Class for Character Formatting
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	BCharFmt	{

	const	CharWeightNorm	=	0 ;
	const	CharWeightBold	=	1 ;

	private	$name ;
	private	$charSize ;					// in pt
	private	$charWeight ;
	private	$fontId ;
	private	$fontName ;
	private	$bcName ;
	private	$charStretch ;
	private	$opacity ;
	private	$underline ;
	private	$strikeout ;
	private	$italic ;
	private	$bold ;
	public	$rVal ;
	public	$gVal ;
	public	$bVal ;

	/**
	 * constructor for the BCharFmt-class
	 *
	 * initializes the object, sets Helvetica as the 'default' fond in 3mm, stretch 100%
	 *
	 * @return void
	 */
	function	__construct() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$this->loaded	=	FALSE ;
		$this->fontId	=	-1 ;
		$this->fontName	=	"Arial" ;
		$this->charSize	=	10 ;				// in pt
		$this->charStretch	=	100.0 ;
		$this->bold	=	false ;
		$this->rVal	=	255 ;
		$this->gVal	=	255 ;
		$this->bVal	=	255 ;
		FDbg::end() ;
	}

	/**
	 * sets the name of the font used in this character format
	 *
	 * @return void
	 */
	function	setFontName( $_fontName) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_fontName')") ;
		FDbg::end() ;
		$this->fontName	=	$_fontName ;
	}

	/**
	 * get the name of the font used in this character format
	 *
	 * @return string Name of the font associated with this character format
	 */
	function	getFontName() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
		return $this->fontName ;
	}

	/**
	 * sets the name of the font used in this character format
	 *
	 * @return void
	 */
	function	setBCName( $_bcName) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_bcName')") ;
		FDbg::end() ;
		$this->bcName	=	$_bcName ;
	}

	/**
	 * get the name of the font used in this character format
	 *
	 * @return string Name of the font associated with this character format
	 */
	function	getBCName() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
		return $this->bcName ;
	}

	/**
	 * Set the composition of the color used for the charcater set.
	 * 0.0 means no color, 0.1 means a tiny little bit, 0.2 a bit more and so on. 1.0 is the max. value.
	 *
	 * @param $_r red-value
	 * @param $_g green-value
	 * @param $_b blue-value
	 * @return void
	 */
	function	setRGB( $_r, $_g, $_b) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_r, $_g, $_b)") ;
		$this->rVal	=	$_r ;
		$this->gVal	=	$_g ;
		$this->bVal	=	$_b ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_fontId
	 */
	function	setFontId( $_fontId) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_fontId)") ;
		$this->fontId	=	$_fontId ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getFontId() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		if ( $this->fontId >= 0) {
			return $this->fontId ;
		} else {
			return FALSE ;
		}
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 */
	function	setBold() {
		$this->bold	=	true ;
	}

	/**
	 * Enter description here...
	 *
	 */
	function	setNoBold() {
		$this->bold	=	false ;
	}

	/**
	 * Enter description here...
	 *
	 */
	function	setItalic() {
		$this->italic	=	true ;
	}

	/**
	 * Enter description here...
	 *
	 */
	function	setNoItalic() {
		$this->italic	=	false ;
	}

	/**
	 * Activate the font for this character format
	 *
	 * Activates the font for this character format. If the font has not yet been loaded
	 * it's loaded first and then activated.
	 *
	 * @return void
	 */
	function	activate( $_fpdf) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <fpdf>)") ;
		if ( $this->fontId == -1) {
			if ( $this->fontName !== "Arial") {
				$_fpdf->AddFont( $this->fontName,'',$this->fontName.'.php');
			}
			$this->fontId	=	1 ;
		}
		$_fpdf->SetFont( $this->fontName, "", 10) ;
		$_fpdf->SetTextColor( 255-$this->rVal, 255-$this->gVal, 255-$this->bVal) ;
		$_fpdf->SetDrawColor( 255-$this->rVal, 255-$this->gVal, 255-$this->bVal) ;
		$_fpdf->SetFontSize( $this->charSize) ;
		$_fpdf->SetFontStretch( $this->charStretch) ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_charSize
	 */
	function	setCharSize( $_charSize) {
		$this->charSize	=	$_charSize ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getCharSize() {
		return $this->charSize ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_charStretch
	 */
	function	setCharStretch( $_charStretch) {
		$this->charStretch	=	$_charStretch ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getCharStretch() {
		return $this->charStretch ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_charWeight
	 */
	function	setCharWeight( $_charWeight) {
		$this->charWeight	=	$_charWeight ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getCharWeight() {
		return $this->charWeight ;
	}
}

?>
