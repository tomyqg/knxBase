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
 * what's required
 */
/**
 * BFrame - Base Class for Frame Definition
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	FmkFrame	{

	const	FrameTypeFlow	=	 1 ;
	const	FrameTypeBG	=	11 ;

	const	AnchorTypePage	=	 1 ;	// frame anchored relativ to page
	const	AnchorTypePara	=	 2 ;	// frame anchored relative to higher level paragraph

	public	$parent	=	null ;
	private	$name	=	"" ;
	private	$flowName	=	"" ;
	public	$flow	=	null ;
	private	$frameType ;
	public	$alignment ;
	public	$horOffs ;
	public	$verOffs ;
	public	$width ;
	public	$height ;
	public	$currHorPos ;
	public	$currVerPos ;
	private	$bgText	=	"" ;

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
	function	__construct( $_parent, $_name, $_flowName="", $_horOffs=0, $_verOffs=0, $_width=0, $_height=0) {
		FDbg::begin( 1, "FmkFrame.php", "FmkFrame", "__construct( <_parent>, '$_name')") ;
		FDbg::trace( 11, FDbg::mdTrcInfo1, "FmkFrame.php", "FmkFrame", "__construct()", "parent := '".$_parent->getName()."'") ;
		$this->parent	=	$_parent ;			// := $myDoc
		$this->name		=	$_name ;
		$this->flowName		=	$_flowName ;
		$this->frameType	=	FmkFrame::FrameTypeFlow ;
		$this->horOffs		=	$_horOffs ;	// relative to page
		$this->verOffs		=	$_verOffs ;	// relative to page
		$this->width		=	$_width ;	// relative to page
		$this->height		=	$_height ;	// relative to page
		$this->currHorPos	=	0 ;		// current position inside this frame
		$this->currVerPos	=	0 ;		// current position inside this frame
		FDbg::end( 1, "FmkFrame.php", "FmkFrame", "__construct( <_parent>, '$_name')") ;
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
	 *
	 * @param string $_xmlString	complete string <doc>...</doc> including tags
	 * @return remainder more data to be processed
	 */
	function	addText( $_xml, $_remainder) {
		FDbg::begin( 1, "FmkFrame.php", "FmkFrame", "addText( <_xmlString>)") ;
		FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFrame.php", "FmkFrame", "addText( <...>)", "I am '".$this->name."'") ;
		/**
		 *
		 */
		$myBox	=	new FmkBox( $this, $this->parent->parent->myFPDF) ;
		$remainder	=	$myBox->addText( $_xml, $_remainder) ;
		/**
		 *
		 */
		FDbg::end( 1, "FmkFrame.php", "FmkFrame", "addText( <_xmlString>)") ;
		return $remainder ;
	}
	function	showBGText() {
		$this->flow->showBGText( $this) ;
	}
	/**
	 *
	 * @param unknown $_xmlString
	 */
	function	addTable( $_xmlString) {
		FDbg::begin( 1, "FmkFrame.php", "FmkFrame", "addTable( <_xmlString>)") ;
		FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFrame.php", "FmkFrame", "addTable( <...>)", "I am '".$this->name."'") ;
		FDbg::end( 1, "FmkFrame.php", "FmkFrame", "addTable( <_xmlString>)") ;
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
		FDbg::dumpL( 0x01000000, "BFrame::remWidth(): width-currHorPos=%d", $this->width - $this->currHorPos) ;
		return $this->width - $this->currHorPos ;
	}

	/**
	 * Enter description here...
	 *
	 */
	function	_dump() {
		FDbg::dumpL( 0x01000000, "BFrame::_dumpL( 0x01000000,)") ;
		FDbg::dumpL( 0x01000000, "BFrame::_dumpL( 0x01000000,): flowId='%s'", $this->flowName) ;
		FDbg::dumpL( 0x01000000, "BFrame::_dumpL( 0x01000000,): verOffs=%d", $this->verOffs) ;
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
		$textWidth	=	$this->parent->parent->myfpdf->GetStringWidth( $_text) ;
		return $textWidth ;
	}
	function	set( $_attr, $_value) {
		FDbg::trace( 2, FDbg::mdTrcInfo1, "FmkFrame.php", "FmkFrame", "set( $_attr, $_value)") ;
		switch ( $_attr) {
		case	"flowName"	:
			$this->flowName	=	$_value ;
			break ;
		case	"horOffs"	:
		case	"verOffs"	:
		case	"width"		:
		case	"height"	:
			$this->$_attr	=	mmToPt( $_value) ;
			break ;
		default	:
			$this->$_attr	=	$_value ;
			break ;
		}
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
	function	_debug( $_prefix="") {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix."+--------------------------------------------------") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." Frame own attributes") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => name=%s", $this->name) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => flowName=%s", $this->flowName) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => horOffs=%dpt", $this->horOffs) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => verOffs=%dpt", $this->verOffs) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => width=%dpt", $this->width) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => height=%dpt", $this->height) ;
		if ( $this->flow != null) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " ...") ;
		}
	}
}

?>
