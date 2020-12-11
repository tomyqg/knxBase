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
 * @package wtcCoreSubSystem
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
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
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
	private	$contCharFmt ;
	private	$contParaFmt ;
	private	$hdrCharFmt ;
	private	$hdrParaFmt ;
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
		FDbg::dumpL( 0x01000000, "dbg: BDocChemLbl100x60::__constructor:<br/>\n") ;
		BDoc::__construct() ;
		BDoc::setSize( BDoc::DocSizeLbl100x60) ;
		BDoc::setType( BDoc::DocTypeLbl) ;
		BDoc::setSided( BDoc::DocSidedSingle) ;
		
		BDoc::addPage( $this->label=new BPage( $this, "Right")) ;
		BDoc::addFlow( new BFlow( $this, "Auto", BFlow::typeCutoff, 0)) ;

		/**
		 * setup the frames
		 */
		$this->label->addFrame( $this->frmHeader=new BFrame( $this, "Header", "", 0, 0, 0, 0)) ;
		$this->label->addFrame( $this->frmContent=new BFrame( $this, "Content", "Auto", 30, 2, 68, 44)) ;
		$this->label->addFrame( $this->frmFooter=new BFrame( $this, "Footer", "", 0, 0, 0, 0)) ;
		$this->label->addFrame( $this->frmBarcode=new BFrame( $this, "Barcode", "", 30, 48, 38, 10)) ;
		$this->label->addFrame( $this->frmCont=new BFrame( $this, "Cont", "", 80, 48, 28, 10)) ;
		$this->label->addFrame( $this->frmSymbols=new BFrame(  $this, "Symbols", "", 2,  2, 26, 56)) ;

		/**
		 * setup the default formats
		 * here: normal characters
		 */
		$this->defCharFmt	=	new BCharFmt() ;
		$this->defCharFmt->setFontName( "Arial") ;
		$this->defCharFmt->setCharSize( 8) ;				// 8pt font
		$this->defParaFmt	=	new BParaFmt() ;
		$this->defParaFmt->setCharFmt( $this->defCharFmt) ;
		$this->defParaFmt->setLineSpacing( 1.2) ;
		
		/**
		 * setup the default formats
		 * here: bold characters
		 */
		$this->boldCharFmt	=	new BCharFmt() ;
		$this->boldCharFmt->setFontName( "Arial") ;
		$this->boldCharFmt->setCharSize( 6) ;
		$this->boldCharFmt->setBold() ;
		$this->boldParaFmt	=	new BParaFmt() ;
		$this->boldParaFmt->setCharFmt( $this->boldCharFmt) ;

		/**
		 * setup the default formats
		 */
		$this->modisCharFmt	=	new BCharFmt() ;
		$this->modisCharFmt->setFontName( "Arial") ;
		$this->modisCharFmt->setCharSize( 4) ;				// 3pt font
		$this->modisParaFmt	=	new BParaFmt() ;
		$this->modisParaFmt->setCharFmt( $this->modisCharFmt) ;
		$this->modisParaFmt->setLineSpacing( 1.2) ;

		/**
		 * setup the special formats for the barcode field
		 */
		$this->hdrCharFmt	=	new BCharFmt() ;
		$this->hdrCharFmt->setFontName( "Arial") ;
		$this->hdrCharFmt->setCharSize( 14.0) ;	// 14pt font
		$this->hdrCharFmt->setCharStretch( 100.0) ;
		$this->hdrParaFmt	=	new BParaFmt() ;
		$this->hdrParaFmt->setCharFmt( $this->hdrCharFmt) ;
		$this->hdrParaFmt->setLineSpacing( 1.2) ;

		/**
		 * setup the special formats for the barcode field
		 */
		$this->bcCharFmt	=	new BCharFmt() ;
		$this->bcCharFmt->setFontName( "Code128") ;
		$this->bcCharFmt->setCharSize( mmToPt( 7.0)) ;
		$this->bcCharFmt->setCharStretch( 200.0) ;
		$this->bcParaFmt	=	new BParaFmt() ;
		$this->bcParaFmt->setCharFmt( $this->bcCharFmt) ;
		$this->bcParaFmt->setLineSpacing( 1.0) ;

		/**
		 * setup the special formats for the barcode field
		 */
		$this->contCharFmt	=	new BCharFmt() ;
		$this->contCharFmt->setFontName( "Arial") ;
		$this->contCharFmt->setCharSize( 20.0) ;
		$this->contCharFmt->setCharStretch( 100.0) ;
		$this->contParaFmt	=	new BParaFmt() ;
		$this->contParaFmt->setCharFmt( $this->contCharFmt) ;
		$this->contParaFmt->setLineSpacing( 1.0) ;

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
			$this->myfpdf->Image( $this->path->Symbols . "c-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		case	BDocChemLbl100x60::SymbolE	:
			$this->myfpdf->Image( $this->path->Symbols . "e-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		case	BDocChemLbl100x60::SymbolF	:
			$this->myfpdf->Image( $this->path->Symbols . "f-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		case	BDocChemLbl100x60::SymbolFF	:
			$this->myfpdf->Image( $this->path->Symbols . "ff-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		case	BDocChemLbl100x60::SymbolN	:
			$this->myfpdf->Image( $this->path->Symbols . "n-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		case	BDocChemLbl100x60::SymbolO	:
			$this->myfpdf->Image( $this->path->Symbols . "o-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		case	BDocChemLbl100x60::SymbolT	:
			$this->myfpdf->Image( $this->path->Symbols . "t-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		case	BDocChemLbl100x60::SymbolTT	:
			$this->myfpdf->Image( $this->path->Symbols . "tt-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		case	BDocChemLbl100x60::SymbolXI	:
			$this->myfpdf->Image( $this->path->Symbols . "xi-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		case	BDocChemLbl100x60::SymbolXN	:
			$this->myfpdf->Image( $this->path->Symbols . "xn-300.jpg", $this->frmSymbols->horOffs + mmToPt( $this->symbolHorNdx * 14), $this->frmSymbols->verOffs + mmToPt( $this->symbolVerNdx * 18), mmToPt(12), mmToPt(16), "jpg") ;
			break ;
		}
		$this->symbolVerNdx++ ;
		if ( $this->symbolVerNdx > 2) {
			$this->symbolHorNdx++ ;
			$this->symbolVerNdx	=	0 ;
		}
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	addMyHead1( $_text) {
		BDoc::addText( $this->hdrParaFmt, $_text) ;
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	addContent( $_text) {
		$this->frmCont->addLine( $_text, $this->hdrParaFmt) ;
		$this->frmCont->skipLine( $this->modisParaFmt) ;
		$this->frmCont->addLine( "mein-mikroskop.de , 2010", $this->modisParaFmt) ;
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	addMyText( $_text='') {
		BDoc::addText( $this->defParaFmt, $_text) ;
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	skipMyLine() {
		BDoc::skipLine( $this->defParaFmt) ;
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	addMyXML( $_text) {
		BDoc::addXML( $this->defParaFmt, $_text) ;
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	addArticleNr( $_text) {
		$this->frmBarcode->addBC( $_text, $this->bcParaFmt) ;
		$this->frmBarcode->addLine( "Ref " . $_text, $this->defParaFmt) ;
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	setupFirstPage() {
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	setupMidPage() {
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	setupLastPage() {
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	setupHeaderFirst() {
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	setupHeaderMid() {
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	setupFooter() {
	}


	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	cascTokenStart( $_token) {
		echo "Here's my token: " . $_token . " to start \n" ;
	}

	/**
	 *
	 *
	 * @param unknown_type $_text
	 */
	function	cascTokenEnd( $_token) {
		echo "Here's my token: " . $_token . " to end \n" ;
	}

}

?>
