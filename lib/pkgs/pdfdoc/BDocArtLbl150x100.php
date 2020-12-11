<?php

/**
 * BDocArtLbl150x100.php Base class for PDF-format based label for Article with
 * a format of 55 x 25 mm
 *
 * This class is based on the basic BDoc class.
 * The label consists of 2 frames, the general article description
 * and the article nr. (comprising barcode and printed nr.)
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcCoreSubSystem
 */
/**
 * BDocArtLabel150x100 - Class for the Label
 *
 * This class implements the particular label.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	BDocArtLbl150x100 extends BDoc	{

	/**#@+
	 * private variable, no further explanation
	 */
	private	$defCharFmt ;
	private	$defParaFmt ;
	private	$bcCharFmt ;
	private	$bcParaFmt ;
	private	$bctCharFmt ;
	private	$bctParaFmt ;
	private	$descr	=	"" ;	// description, i.e. ArtikelBez1 + ArtikelBez2
	private	$qtyText	=	"" ;
	private	$artNr	=	"" ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		BDoc::__construct() ;
		BDoc::setSize( BDoc::DocSizeLbl150x100) ;
		BDoc::setType( BDoc::DocTypeLbl) ;
		BDoc::setSided( BDoc::DocSidedSingle) ;
		$this->descr	=	"" ;
		$this->qtyText	=	"" ;
		$this->artNr	=	"" ;

		BDoc::addPage( $this->label=new BPage( $this, "Right")) ;
		BDoc::addFlow( new BFlow( $this, "Auto"), BFlow::typeAuto, 0) ;

		/**
		 * setup the frames
		 */
		$this->label->addFrame( $this->frmHeader=new BFrame( $this, "Header", "", 0, 0, 0, 0)) ;
		$this->label->addFrame( $this->frmContent=new BFrame( $this, "Content", "Auto", 2, 2, 146, 47)) ;
		$this->label->addFrame( $this->frmFooter=new BFrame( $this, "Footer", "", 0, 0, 0, 0)) ;
		$this->label->addFrame( $this->frmBarcode=new BFrame( $this, "Barcode", "", 4, 51, 146, 47)) ;

		/**
		 * setup the default formats
		 */
		$this->defCharFmt	=	new BCharFmt() ;
		$this->defCharFmt->setFontName( "Arial") ;
		$this->defCharFmt->setCharSize(24) ;
		$this->defParaFmt	=	new BParaFmt() ;
		$this->defParaFmt->setCharFmt( $this->defCharFmt) ;
		$this->defParaFmt->setLineSpacing( 1.0) ;
		$this->defCharFmt->setCharStretch( 100.0) ;

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
		$this->bcCharFmt->setBCName( "Code128") ;
		$this->bcCharFmt->setCharStretch( 150) ;
		$this->bcCharFmt->setCharSize( mmToPt( 30.0)) ;

		$this->bcParaFmt	=	new BParaFmt() ;
		$this->bcParaFmt->setLineSpacing( 1.0) ;
		$this->bcParaFmt->setAlignment( BParaFmt::alignCenter) ;
		$this->bcParaFmt->setCharFmt( $this->bcCharFmt) ;

		$this->bctCharFmt	=	new BCharFmt() ;
		$this->bctCharFmt->setFontName( "Arial") ;
		$this->bctCharFmt->setCharSize( 14) ;

		$this->bctParaFmt	=	new BParaFmt() ;
		$this->bctParaFmt->setLineSpacing( 0.9) ;
		$this->bctParaFmt->setAlignment( BParaFmt::alignCenter) ;
		$this->bctParaFmt->setCharFmt( $this->bctCharFmt) ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param $_text
	 */
	function	addMyText( $_text='') {
//		$this->frmContent->addLine( $_text, $this->defParaFmt) ;
		BDoc::addText( $this->defParaFmt, $_text, false) ;
	}
	/**
	 *
	 */
	function	skipMyLine() {
		BDoc::skipLine( $this->defParaFmt) ;
	}
	/**
	 * Enter description here...
	 *
	 */
	function	addArticleNr( $_text, $_text2="") {
		$this->frmBarcode->addBC( $_text, $this->bcParaFmt) ;
		$this->frmBarcode->addLine( $_text, $this->bctParaFmt) ;
		if ( $_text2 != "") {
			$this->frmBarcode->addLine( $_text2, $this->bctParaFmt) ;
		}
	}
	/**
	 * Enter description here...
	 *
	 */
	function	setFormat() {
	}
	/**
	 * Enter description here...
	 *
	 */
	function	setupFirstPage() {
	}
	/**
	 * Enter description here...
	 *
	 */
	function	setupMidPage() {
	}
	/**
	 * Enter description here...
	 *
	 */
	function	setupLastPage() {
	}
	/**
	 * Enter description here...
	 *
	 */
	function	setupHeaderFirst() {
	}
	/**
	 * Enter description here...
	 *
	 */
	function	setupHeaderMid() {
	}
	/**
	 * Enter description here...
	 *
	 */
	function	setupFooter() {
	}
	/**
	 * Enter description here...
	 *
	 */
	function	cascTokenStart( $_token) {
		echo "Here's my token: " . $_token . " to start \n" ;
	}
	/**
	 * Enter description here...
	 *
	 */
	function	cascTokenEnd( $_token) {
		echo "Here's my token: " . $_token . " to end \n" ;
	}
}
?>
