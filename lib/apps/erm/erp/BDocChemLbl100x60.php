<?php

/**
 * BDocChemLabel100x60.php Base class for PDF-format based label for Chemical with
 * a format of 100 x 60 mm
 * 
 * This class is based on the basic BDoc class.
 * The label consits of 4 frames, for the symbols, the heading, the genral description
 * and the article nr. (comprising barcode and printed nr.)
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */

/**
 * require the WTF-Debugger and the WTF-PDF-Document
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "BDoc.php") ;

/**
 * BDocChemLabel100x60 - Class for the Label
 *
 * This class implements the particular label.
 *
 * @package Application
 * @subpackage BDoc
 */
class	BDocChemLbl100x60 extends BDoc	{

	/**#@+
	 * constant for referencing a specific danger-symbol
	 */
	const	SymbolC		=	 1 ;
	const	SymbolE		=	 2 ;
	const	SymbolF		=	 3 ;
	const	SymbolFF	=	 4 ;
	const	SymbolN		=	 5 ;
	const	SymbolO		=	 6 ;
	const	SymbolT		=	 7 ;
	const	SymbolTT	=	 8 ;
	const	SymbolXI	=	 9 ;
	const	SymbolXN	=	10 ;
	/**#@-*/

	/**#@+
	 * private variable, no further explanation
	 */
	private	$defCharFmt ;
	private	$defParaFmt ;
	private	$boldCharFmt ;
	private	$boldParaFmt ;
	private	$bcCharFmt ;
	private	$bcParaFmt ;
	private	$symbolHorNdx	=	0 ;
	private	$symbolVerNdx	=	0 ;
	/**#@-*/

	/**
	 * Constructor
	 *
	 * Performs some very basic setups like setting up the document size [DocSiyeLbl100x60], the type [DocTypeLbl]
	 * and the siding [DocSidedSingle].
	 * Furthermore it creates the required 4 frames, the 4 paragraph formats and associates the required character formats
	 * with these paragraphs.
	 */
	function	__construct() {
		FDbg::get()->dump( "dbg: BDocChemLbl100x60::__constructor:<br/>\n") ;
		BDoc::__construct() ;
		BDoc::setSize( BDoc::DocSizeLbl100x60) ;
		BDoc::setType( BDoc::DocTypeLbl) ;
		BDoc::setSided( BDoc::DocSidedSingle) ;

		/**
		 * setup the default formats
		 */
		$this->defCharFmt	=	new BCharFmt() ;
		$this->defCharFmt->setCharSize( 2.8) ;
		$this->defParaFmt	=	new BParaFmt() ;
		$this->defParaFmt->setCharFmt( $this->defCharFmt) ;

		$this->boldCharFmt	=	new BCharFmt() ;
		$this->boldCharFmt->setFontName( "HVB____S.TTF") ;
		$this->boldCharFmt->setCharSize( 5.0) ;
		$this->boldParaFmt	=	new BParaFmt() ;
		$this->boldParaFmt->setCharFmt( $this->boldCharFmt) ;

		/**
		 * setup the special formats for the barcode field
		 */
		$this->bcCharFmt	=	new BCharFmt() ;
		$this->bcCharFmt->setFontName( "code128.ttf") ;
		$this->bcCharFmt->setCharSize( 7.0) ;
		$this->bcCharFmt->setCharStretch( 160.0) ;
		$this->bcParaFmt	=	new BParaFmt() ;
		$this->bcParaFmt->setCharFmt( $this->bcCharFmt) ;

		/**
		 * setup the frames
		 */
		$this->frmHeader	=	new BFrame( 0, 0, 0, 0) ;
		$this->frmContent	=	new BFrame( 34, 2, 65, 44) ;
		$this->frmFooter	=	new BFrame( 0, 0, 0, 0) ;
		$this->frmHeaderFirst	=	new BFrame( 0, 0, 0, 0) ;
		$this->frmContentFirst	=	new BFrame( 35, 2, 0, 0) ;
		$this->frmFooterFirst	=	new BFrame( 0, 0, 0, 0) ;
		$this->frmBarcode	=	new BFrame( 34, 27, 65, 100) ;
		$this->frmSymbols	=	new BFrame(  4,  2, 40, 56) ;
	}

	/**
	 * addSymbol
	 *
	 * Adds a danger-symbol to the respective (printable) frame. This method is position-aware, this next symbol being added
	 * by this function will automatically be located in the proper place. There is - however - no built-in pre-caution for
	 * overflowing this area !
	 * 
	 * @param int $_symbol Index of the symbol to be printed (must be one of the pre-defined symbols, see: constants)
	 */
	function	addSymbol( $_symbol) {
	switch ( $_symbol) {
		case	BDocChemLbl100x60::SymbolC	:
			pbx_show_image( $this->pbx, $this->symC, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		case	BDocChemLbl100x60::SymbolE	:
			pbx_show_image( $this->pbx, $this->symE, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		case	BDocChemLbl100x60::SymbolF	:
			pbx_show_image( $this->pbx, $this->symF, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		case	BDocChemLbl100x60::SymbolFF	:
			pbx_show_image( $this->pbx, $this->symFF, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		case	BDocChemLbl100x60::SymbolN	:
			pbx_show_image( $this->pbx, $this->symN, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		case	BDocChemLbl100x60::SymbolO	:
			pbx_show_image( $this->pbx, $this->symO, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		case	BDocChemLbl100x60::SymbolT	:
			pbx_show_image( $this->pbx, $this->symT, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		case	BDocChemLbl100x60::SymbolTT	:
			pbx_show_image( $this->pbx, $this->symTT, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		case	BDocChemLbl100x60::SymbolXI	:
			pbx_show_image( $this->pbx, $this->symXI, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		case	BDocChemLbl100x60::SymbolXN	:
			pbx_show_image( $this->pbx, $this->symXN, cx( $this->frmSymbols->horOffs + $this->symbolHorNdx * 14), cy( $this->frmSymbols->verOffs + $this->symbolVerNdx * 18), cx(12), cy(16), 0) ;
			break ;
		}
		$this->symbolHorNdx++ ;
		if ( $this->symbolHorNdx > 1) {
			$this->symbolVerNdx++ ;
			$this->symbolHorNdx	=	0 ;
		}
	}

	function	addMyHead1( $_text) {
		BDoc::addText( $this->frmContentAct, $this->boldParaFmt, $_text) ;
		BDoc::addText( $this->frmContentAct, $this->defParaFmt, $_text) ;
		BDoc::addText( $this->frmContentAct, $this->defParaFmt, $_text) ;
	}

	function	addMyText( $_text='') {
		BDoc::addText( $this->frmContentAct, $this->defParaFmt, $_text) ;
	}

	function	addMyXML( $_text) {
		BDoc::addXML( $this->frmContentAct, $this->defParaFmt, $_text) ;
	}

	function	addArticleNr( $_text) {
		BDoc::addText( $this->frmBarcode, $this->bcParaFmt, $_text) ;
		BDoc::addText( $this->frmBarcode, $this->defParaFmt, $_text) ;
	}

	function	setupFirstPage() {
	}

	//
	function	setupMidPage() {
	}

	//
	function	setupLastPage() {
	}

	function	setupHeaderFirst() {
	}

	function	setupHeaderMid() {
	}

	function	setupFooter() {
	}


	function	cascTokenStart( $_token) {
echo "Here's my token: " . $_token . " to start \n" ;
	}

	//
	function	cascTokenEnd( $_token) {
echo "Here's my token: " . $_token . " to end \n" ;
	}

}

?>
