<?php

/**
 * BDocArtLbl150x100.php Base class for PDF-format based label for Article with
 * a format of 150 x 100 mm
 * 
 * This class is based on the basic BDoc class.
 * The label consists of 2 frames, the general article description
 * and the article nr. (comprising barcode and printed nr.)
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
 * BDocArtLabel100x50 - Class for the Label
 *
 * This class implements the particular label.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	BDocAdrLbl100x150 extends BDoc	{
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
	private	$rcvrLines	=	array() ;
	private	$infoTitle	=	array() ;
	private	$infoData	=	array() ;
	private	$postage	=	"POSTAGE" ;
	/**
	 * Constructor
	 *
	 * Performs some very basic setups like setting up the document size [DocSiyeLbl100x60], the type [DocTypeLbl]
	 * and the siding [DocSidedSingle].
	 * Furthermore it creates the required 4 frames, the 4 paragraph formats and associates the required character formats
	 * with these paragraphs.
	 */
	function	__construct() {
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::__construct(): begin\n") ;
		BDoc::__construct() ;
		BDoc::setSize( BDoc::DocSizeLbl100x150) ;
		BDoc::setType( BDoc::DocTypeLbl) ;
		BDoc::setSided( BDoc::DocSidedSingle) ;
		$this->descr	=	"" ;
		$this->qtyText	=	"" ;
		$this->artNr	=	"" ;

		BDoc::addPage( $this->label=new BPage( $this, "Right")) ;
		BDoc::addFlow( new BFlow( $this, "Auto", BFlow::typeCutoff, 0)) ;

		/**
		 * setup the frames
		 */
		$this->label->addFrame( $this->frmHeader=new BFrame( $this, "Header", "", 0, 0, 0, 0)) ;
		$this->label->addFrame( $this->frmInfo=new BFrame( $this, "Info", "", 3, 5, 50, 68)) ;
		$this->label->addFrame( $this->frmRcvrAddr=new BFrame( $this, "RcvrAddr", "Auto", 3, 80, 99, 68)) ;
		$this->label->addFrame( $this->frmContent=new BFrame( $this, "Sender", "", 3, 74, 99, 6)) ;
		$this->label->addFrame( $this->frmStamp=new BFrame( $this, "Sender", "", 77, 3, 20, 20)) ;
		$this->label->addFrame( $this->frmFooter=new BFrame( $this, "Footer", "", 0, 0, 0, 0)) ;

		/**
		 * setup the default formats
		 */
		$this->defCharFmt	=	new BCharFmt() ;
		$this->defCharFmt->setCharSize( 8) ;
		$this->defParaFmt	=	new BParaFmt() ;
		$this->defParaFmt->setCharFmt( $this->defCharFmt) ;
		//
		$this->vsCharFmt	=	new BCharFmt() ;
		$this->vsCharFmt->setCharSize( 4) ;
		$this->vsParaFmt	=	new BParaFmt() ;
		$this->vsParaFmt->setLineSpacing( 1.0) ;
		$this->vsParaFmt->setCharFmt( $this->defCharFmt) ;
		//
		$this->smallCharFmt	=	new BCharFmt() ;
		$this->smallCharFmt->setCharSize( 6) ;
		$this->smallParaFmt	=	new BParaFmt() ;
		$this->smallParaFmt->setCharFmt( $this->defCharFmt) ;
		
		$this->rcvrCharFmt	=	new BCharFmt() ;
		$this->rcvrCharFmt->setCharSize( 12) ;
		$this->rcvrParaFmt	=	new BParaFmt() ;
		$this->rcvrParaFmt->setCharFmt( $this->rcvrCharFmt) ;

		$this->infoCharFmt	=	new BCharFmt() ;
//		$this->infoCharFmt->setRGB( 204, 204, 204) ;
		$this->infoParaFmt	=	new BParaFmt() ;
		$this->infoParaFmt->setCharFmt( $this->infoCharFmt) ;

		$this->cdCharFmt	=	new BCharFmt() ;
//		$this->cdCharFmt->setRGB( 153, 153, 153) ;
		$this->cdCharFmt->setCharSize( 9) ;				// 9pt font
		$this->cdParaFmt	=	new BParaFmt() ;
		$this->cdParaFmt->setCharFmt( $this->cdCharFmt) ;
		$this->cdParaFmt->setLineSpacing( 1.0) ;
		$this->cdParaFmt->setAlignment( BParaFmt::alignRight) ;

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
		$this->bcCharFmt->setCharStretch( 160.0) ;
		$this->bcCharFmt->setCharSize( mmToPt( 6.0)) ;

		$this->bcParaFmt	=	new BParaFmt() ;
		$this->bcParaFmt->setLineSpacing( 1.0) ;
		$this->bcParaFmt->setAlignment( BParaFmt::alignCenter) ;
		$this->bcParaFmt->setCharFmt( $this->bcCharFmt) ;

		$this->bctCharFmt	=	new BCharFmt() ;
		$this->bctCharFmt->setFontName( "Arial") ;
		$this->bctCharFmt->setCharSize( 5.0) ;
		
		$this->bctParaFmt	=	new BParaFmt() ;
		$this->bctParaFmt->setAlignment( BParaFmt::alignCenter) ;
		$this->bctParaFmt->setCharFmt( $this->bctCharFmt) ;
		
		/**
		 * initialize own attributes
		 */
		for ( $ndx = 1 ; $ndx <= 6 ; $ndx++) {
			$this->rcvrLines[ $ndx]	=	"" ;
		}
		for ( $ndx = 1 ; $ndx <= 10 ; $ndx++) {
			$this->infoTitle[ $ndx]	=	"" ;
			$this->infoData[ $ndx]	=	"" ;
		}
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::__construct(): end\n") ;
	}

	function	addMyText( $_text='') {
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::addMyText( '$_text'): begin\n") ;
		$this->frmContent->addLine( $_text, $this->defParaFmt) ;
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::addMyText(...): end\n") ;
	}

	function	skipMyLine() {
		$this->frmContent->skipLine( $this->defParaFmt) ;
	}

	/**
	 * Enter description here...
	 *
	 */
	function	addArticleNr( $_text) {
		$this->frmBarcode->addBC( $_text, $this->bcParaFmt) ;
		$this->frmBarcode->addLine( $_text, $this->bctParaFmt) ;
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
		$this->setupRcvrField( $this->frmRcvrAddr) ;
		$this->writeRcvrField( $this->frmRcvrAddr) ;
		$this->writeInfoField( $this->frmInfo) ;
		$this->writePostageField( $this->frmStamp) ;

		/**
		 * make a small line for the folding marks
		 */
		$this->myfpdf->Line( mmToPt( 3), mmToPt( 70),
								mmToPt( 102), mmToPt( 70)) ;
//		$this->myfpdf->Line( mmToPt( 48), mmToPt( 30),
//								mmToPt( 48), mmToPt( 102)) ;
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
	 * setupRcvrField
	 * The method adds the receiver field, thus it's only to be used for setting up the first page of the document.
	 *
	 * @return void
	 */
	function	writePostageField( $_frm) {
		$relVerOffs	=	0 ;
		$this->cdParaFmt->getCharFmt()->activate( $this->myfpdf) ;
		$_frm->addLine( $this->postage, $this->smallParaFmt) ;
	}
	function	setupRcvrField( $_frm) {
		$relVerOffs	=	0 ;

		/**
		 * start the footer
		 */
		$this->cdParaFmt->getCharFmt()->activate( $this->myfpdf) ;

		/**
		 * start with the very thin sender address in the recipient field
		 */
//		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + $relVerOffs, $this->doc->retAddrLine) ;	//config.ini:   [doc]retAddrLine
		$relVerOffs	+=	mmToPt( 1) ;

		/**
		 * draw the separating line between the header and the document content
		 */
//		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $relVerOffs,
//								$_frm->horOffs + $_frm->width, $_frm->verOffs + $relVerOffs) ;
		
	}

	/**
	 * writeRcvrField
	 * The method writes the receiver field to the frame passed as argument.
	 *
	 * @return void
	 */
	function	writeRcvrField( $_frm) {

		FDbg::dump( "BDocRegLetter::writeRcvrField(...)") ;
		$relVerOffs	=	mmToPt( 10) ;

		/**
		 * start the address
		 */
		$myParaFmt	=	$this->vsParaFmt ;
		$_frm->addLine( FTr::tr( "Receiver"), $myParaFmt) ;
		$this->defParaFmt->getCharFmt()->activate( $this->myfpdf) ;
		/**
		 * draw the separating line between the header and the document content
		 */
		$_frm->skipLine( $this->rcvrParaFmt) ;
		for ( $ndx = 1 ; $ndx <= 6 ; $ndx++) {
			FDbg::dumpL( 0x02000000, "BDocRegLetter::writeRcvrField(...), ndx=%d, data='%s'", $ndx) ;
			$_frm->addLine( $this->rcvrLines[ $ndx], $this->rcvrParaFmt) ;
		}

	}

	/**
	 * writeInfoField
	 * The method writes the informaion field to the frame passed as argument.
	 *
	 * @return void
	 */
	function	writeInfoField( $_frm) {
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::setRcvr( <_frm>): begin\n") ;
		/**
		 * 
		 * @var unknown_type
		 */
		$myParaFmt	=	$this->vsParaFmt ;
		$_frm->addLine( FTr::tr( "Sender"), $myParaFmt) ;
		$myParaFmt	=	$this->defParaFmt ;
		$_frm->addLine( $this->DHL_Simple->ReturnAddressLine, $myParaFmt) ;
		/**
		 * 
		 * @var unknown_type
		 */
		$relVerOffs	=	mmToPt( 15) ;
		/**
		 * draw the separating line between the header and the document content
		 */
		for ( $ndx = 1 ; $ndx <= 10 ; $ndx++) {
			$this->cdParaFmt->getCharFmt()->activate( $this->myfpdf) ;
			$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + $relVerOffs, $this->infoTitle[ $ndx] ) ;
			$myString	=	$this->infoData[ $ndx] ;
			$this->cdParaFmt->getCharFmt()->activate( $this->myfpdf) ;
			$this->myfpdf->Text( $_frm->horOffs + $_frm->width - $this->textWidth( $myString), $_frm->verOffs + $relVerOffs, $myString ) ;
			$relVerOffs	+=	mmToPt( 3.5) ;
		}
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::setRcvr( <_frm>): end\n") ;
	}

	/**
	 * setRcvr
	 * The method adds the _text for the receiver line specified by _ndx.
	 *
	 * @return void
	 */
	function	setRcvr( $_ndx, $_text) {
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::setRcvr( $_ndx, '$_text'): begin\n") ;
		if ( $_ndx >= 1 && $_ndx <= 6) {
			$this->rcvrLines[ $_ndx]	=	$_text ;
		}
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::setRcvr( $_ndx, '$_text'): end\n") ;
	}
	/**
	 * setInfo
	 * The method adds the _text for the info line specified by _ndx.
	 *
	 * @return void
	 */
	function	setPostage( $_postage) {
		$this->postage	=	$_postage ;
	}
	function	setInfo( $_ndx, $_title, $_data) {
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::setInfo( $_ndx, '$_title', '$_data'): begin\n") ;
		if ( $_ndx >= 1 && $_ndx <= 10) {
			$this->infoTitle[ $_ndx]	=	$_title ;
			$this->infoData[ $_ndx]	=	$_data ;
		}
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::setInfo( $_ndx, '$_title', '$_data'): end\n") ;
	}
	/**
	 * Enter description here...
	 *
	 */
	function	cascTokenStart( $_token) {
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::cascTokenStart( '$_token'): begin\n") ;
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::cascTokenStart( '$_token'): begin\n") ;
	}
	/**
	 * Enter description here...
	 *
	 */
	function	cascTokenEnd( $_token) {
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::cascTokenStart( '$_token'): begin\n") ;
		FDbg::dumpL( 0x00010000, "BDocAdrLbl100x150.doc::BDocArtLbl100x150::cascTokenStart( '$_token'): end\n") ;
	}
}
?>