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
 * what's required
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
class	FmkCharFmt	{

	const	CharWeightNorm	=	0 ;
	const	CharWeightBold	=	1 ;

	static	$charFormats	=	array() ;

	private	$parent	=	null ;
	private	$name	=	"" ;
	public	$charSize ;					// in pt
	private	$charWeight ;
	private	$fontId ;
	public	$fontName ;
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
	static	$loadedFonts	=	array() ;

	/**
	 * constructor for the BCharFmt-class
	 *
	 * initializes the object, sets Helvetica as the 'default' fond in 3mm, stretch 100%
	 *
	 * @return void
	 */
	function	__construct( $_parent, $_name) {
		FDbg::begin( 1, "FmkCharFmt.php", "FmkCharFmt", "__construct( <_parent>, '$_name')") ;
		$this->parent	=	$_parent ;
		$this->name	=	$_name ;
		$this->loaded	=	FALSE ;
		$this->fontId	=	-1 ;
		$this->fontName	=	"Arial" ;
		$this->charSize	=	10 ;				// in pt
		$this->charStretch	=	100.0 ;
		$this->bold	=	false ;
		$this->rVal	=	255 ;
		$this->gVal	=	255 ;
		$this->bVal	=	255 ;
		self::$charFormats[ $_name]	=	$this ;
		FDbg::end( 1, "FmkCharFmt.php", "FmkCharFmt", "__construct( <_parent>, '$_name')") ;
	}
	static	function	getFormat( $_name) {
		if ( isset( self::$charFormats[ $_name])) {
			return self::$charFormats[ $_name] ;
		} else {
			return null ;
		}
	}

	/**
	 * sets the name of the font used in this character format
	 *
	 * @return void
	 */
	function	setFontName( $_fontName) {
		FDbg::trace( 1, FDbg::mdTrcInfo1, "FmkCharFmt.php", "FmkCharFmt", "setFontName( '$_fontName')") ;
		$this->fontName	=	$_fontName ;
	}

	/**
	 * get the name of the font used in this character format
	 *
	 * @return string Name of the font associated with this character format
	 */
	function	getFontName() {
		FDbg::trace( 1, FDbg::mdTrcInfo1, "FmkCharFmt.php", "FmkCharFmt", "getFontName()", "getFontName := '".$this->fontName."'") ;
		return $this->fontName ;
	}

	/**
	 * sets the name of the font used in this character format
	 *
	 * @return void
	 */
	function	setBCName( $_bcName) {
		FDbg::trace( 1, FDbg::mdTrcInfo1, "FmkCharFmt.php", "FmkCharFmt", "setBCName( '$_bcName')") ;
		$this->bcName	=	$_bcName ;
	}

	/**
	 * get the name of the font used in this character format
	 *
	 * @return string Name of the font associated with this character format
	 */
	function	getBCName() {
		FDbg::trace( 1, FDbg::mdTrcInfo1, "FmkCharFmt.php", "FmkCharFmt", "getBCName()", "getBCName := '".$this->bcName."'") ;
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
		FDbg::trace( 1, FDbg::mdTrcInfo1, "FmkCharFmt.php", "FmkCharFmt", "setRGB( $_r, $_g, $_g)") ;
		$this->rVal	=	$_r ;
		$this->gVal	=	$_g ;
		$this->bVal	=	$_b ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_fontId
	 */
	function	setFontId( $_fontId) {
		FDbg::trace( 1, FDbg::mdTrcInfo1, "FmkCharFmt.php", "FmkCharFmt", "setFontId( $_fontId)") ;
		$this->fontId	=	$_fontId ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getFontId() {
		FDbg::trace( 1, FDbg::mdTrcInfo1, "FmkCharFmt.php", "FmkCharFmt", "getFontId()", "getFontId := ".$this->fontId."") ;
		if ( $this->fontId >= 0) {
			return $this->fontId ;
		} else {
			return FALSE ;
		}
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
		FDbg::begin( 1, "FmkCharFmt.php", "FmkCharFmt", "activate( <_fpdf>)") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "FmkCharFmt.php", "FmkCharFmt", "activate( <_fpdf>)", "fontName='".$this->fontName."'") ;
		if ( $this->fontId == -1) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "FmkCharFmt", "FmkCharFmt", "activate( <fpdf>)", "lade Font aus ttf-Datei") ;
			if ( $this->fontName !== "Arial" && ! isset( self::$loadedFonts[ $this->fontName])) {
				$_fpdf->AddFont( $this->fontName, '', $this->fontName.".php") ;
				self::$loadedFonts[ $this->fontName]	=	"loaded" ;
			}
			$this->fontId	=	1 ;
		}
		$_fpdf->SetFont( $this->fontName, "", 10) ;
		$_fpdf->SetTextColor( 255-$this->rVal, 255-$this->gVal, 255-$this->bVal) ;
		$_fpdf->SetDrawColor( 255-$this->rVal, 255-$this->gVal, 255-$this->bVal) ;
		$_fpdf->SetFontSize( $this->charSize) ;
		$_fpdf->SetFontStretch( $this->charStretch) ;
		FDbg::end( 1, "FmkCharFmt.php", "FmkCharFmt", "activate( <_fpdf>") ;
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
	function	set( $_attr, $_value) {
		switch ( $_attr) {
		case	"rVal"	:
		case	"gVal"	:
		case	"rVal"	:
			$this->$_attr	=	intval( $_value) ;
			break ;
		default	:
			FDbg::trace( 2, FDbg::mdTrcInfo1, "FmkCharFmt.php", "FmkCharFmt", "set( $_attr, $_value)") ;
			$this->$_attr	=	$_value ;
			break ;
		}
	}
	function	_debug() {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "_debug") ;
	}
}

?>
