<?php

/**
 * BDocParaFmt.php - Base Class for the creation of PDF documents based on
 * FPDFLib
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcCoreSubSystem
 * @filesource
 */
/**
 * BParaFmt - Base Class for Paragraph Formatting
 * procedural stuff in the start of the file
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	FmkParFmt	{

	static	$parFmts	=	array() ;

	const	alignLeft	=	1 ;
	const	alignCenter	=	2 ;
	const	alignRight	=	3 ;
	const	alignDec	=	4 ;

	private	$parent	=	null ;
	public	$name	=	"" ;
	public	$charFmt	=	"" ;
	public	$charSize ;					// in pt
	private	$charWeight ;
	public	$fontName ;
	public	$indent ;
	public	$indentFirst ;
	public	$lineSpacing ;
	public	$alignment ;
	public	$tabsPos	=	array() ;
	public	$tabsType	=	array() ;
	public	$currTabStop	=	0 ;

	/**
	 * Enter description here...
	 *
	 */
	function	__construct( $_parent, $_name) {
		FDbg::begin( 1, "FmkParFmt.php", "FmkParFmt", "__construct( <_parent>, '$_name')") ;
		$this->parent	=	$_parent ;
		$this->name	=	$_name ;
		$this->lineSpacing	=	1.5 ;
		$this->indent	=	0 ;
		$this->indentFirst	=	0 ;
		$this->alignment	=	FmkParFmt::alignLeft ;
		$this->fontName	=	"" ;
		$this->charSize	=	12 ;
		self::$parFmts[ $_name]	=	$this ;
		FDbg::end( 1, "FmkParFmt.php", "FmkParFmt", "__construct( <_parent>, '$_name')") ;
	}
	static	function	getFormat( $_name) {
		if ( isset( self::$parFmts[ $_name])) {
			return self::$parFmts[ $_name] ;
		} else {
			return null ;
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_charFmt
	 */
	function	setCharFmt( $_charFmt) {
		$this->charFmt	=	$_charFmt ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getCharFmt() {
		return $this->charFmt ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_lineSpacing
	 */
	function	setLineSpacing( $_lineSpacing) {
		$this->lineSpacing	=	$_lineSpacing ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getLineSpacing() {
		return $this->lineSpacing ;
	}

	/**
	 * returns the height of the frame
	 *
	 * @return float Height of frame
	 */
	function	setAlignment( $_alignment) {
		$this->alignment	=	$_alignment ;
	}

	/**
	 * returns the height of the frame
	 *
	 * @return float Height of frame
	 */
	function	getAlignment() {
		return $this->alignment ;
	}
	function	set( $_attr, $_value) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, "FmkParFmt.php", "FmkParFmt", "set( $_attr, $_value)") ;
		switch ( $_attr) {
		case	"charFmt"	:
			FDbg::trace( 2, FDbg::mdTrcInfo1, "FmkParFmt.php", "FmkParFmt", "setting character format") ;
			$this->charFmt	=	$this->parent->charFmt[ $_value] ;
			break ;
		case	"lineSpacing"	:
			$this->$_attr	=	floatval( $_value) ;
			break ;
		default	:
			$this->$_attr	=	$_value ;
			break ;
		}
	}
	function	getTabStopPos( &$_type) {
		$pos	=	0 ;
		$_type	=	"l" ;
		if ( isset( $this->tabsType[ $this->currTabStop])) {
			$_type	=	$this->tabsType[ $this->currTabStop] ;
			$pos	=	$this->tabsPos[ $this->currTabStop] ;
			if ( isset( $this->tabsType[ $this->currTabStop+1])) {
				$this->currTabStop++ ;
			}
		}
		FDbg::trace( 2, FDbg::mdTrcInfo1, "FmkParFmt.php", "FmkParFmt", "returning pos $pos") ;
		return $pos ;
	}
	function	resetTabs() {
		$this->currTabStop	=	0 ;
	}
	/**
	 * dump the object for diagnistic purpose
	 */
	function	_debug( $_prefix="") {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix."+--------------------------------------------------") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." Paragraph own attributes") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => name=".$this->name) ;
		foreach ( $this->tabsPos as $index => $obj) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." " . $this->tabsType[$index] . "-tab at ".$this->tabsPos[$index]." mm") ;
		}
	}
}

?>
