<?php

/**
 * BDocRegLetter.php Base class for Normal (A4, Letter-size) Letter in PDF-format
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package WTS
 * @filesource
 */

/**
 * what's required
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDoc.php") ;

/**
 * BDocRegLetter - Basis Klasses für einen regulären Brief
 *
 * Diese Klasse basiert auf BDoc und stellt Methoden für das erstellen eines
 * normal formatierten Briefes zur Verfügung.
 *
 * @package WTS
 * @subpackage BDoc
 */

class	BDocRegLetter extends BDoc	{

	private	$defCharFmt ;		// default character-format
	public	$defParaFmt ;		// default paragraph-format
	public	$refCharFmt ;		// charater format for Reference line
	public	$refParaFmt ;		// charater format for Reference line
	public	$defTableCellFmt ;	// default paragraph-format for table-cell

	private	$cdCharFmt ;		// character-format for company header on frst page top right
	private	$cdParaFmt ;		// paragraph-format for company header on frst page top right

	private	$rcvrLines	=	array() ;
	private	$infoTitle	=	array() ;
	private	$infoData	=	array() ;
	private	$runHeadL ;
	private	$runHeadR ;
	private	$refLine ;
	private	$firstPage ;
	private	$rightPage ;
	
	/**
	 * BDoc - __construct
	 *
	 * Konstruktor für BDoc
	 * Initializes the document to A4 size, Regular Letter and Síngle-Sided with First page.
	 * Creates some standard character and paragraph formats.
	 *
	 * @return object
	 */
	function	__construct() {
		FDbg::get()->dump( "BDocRegLetter::__constructor") ;

		BDoc::__construct() ;
		$this->pageNr	=	0 ;

		BDoc::setSize( BDoc::DocSizeA4) ;
		BDoc::setType( BDoc::DocTypeRegLetter) ;
		BDoc::setSided( BDoc::DocSidedSingleWF) ;
		BDoc::addPage( $this->firstPage=new BPage( $this, "First")) ;
		BDoc::addPage( $this->rightPage=new BPage( $this, "Right")) ;
		
		/**
		 * add the required flows
		 */
		BDoc::addFlow( new BFlow( $this, "Auto")) ;

		/**
		 * setup the basic frames for all first page
		 */
		$this->firstPage->addFrame( $this->frmHeaderFirst=new BFrame( $this, "HeaderFirst", "", 20, 10, $this->sheetWidth - 20 - 10, 22)) ;
		$this->firstPage->addFrame( $this->frmContentFirst=new BFrame( $this, "ContentFirst", "Auto", 20, 110, $this->sheetWidth - 20 - 10, 160)) ;
		$this->firstPage->addFrame( $this->frmFooterFirst=new BFrame( $this, "FooterFirst", "", 20, 275, $this->sheetWidth - 20 - 10, 20)) ;

		/**
		 * setup the special frames for the first page
		 */
		$this->firstPage->addFrame( $this->frmRcvrAddr=new BFrame( $this, "RcvrAddr", "", 22, 50, 85, 45)) ;
		$this->firstPage->addFrame( $this->frmInfo=new BFrame( $this, "Info", "", 140, 50, 60, 60)) ;
		$this->firstPage->addFrame( $this->frmRef=new BFrame( $this, "Ref", "", 20, 95, 140, 10)) ;

		/**
		 * setup the frames for the "right" pages
		 */
		$this->rightPage->addFrame( $this->frmHeader=new BFrame( $this, "Header", "", 20, 10, $this->sheetWidth - 20 - 10, 12)) ;
		$this->rightPage->addFrame( $this->frmContent=new BFrame( $this, "Content", "Auto", 20, 40, $this->sheetWidth - 20 - 10, 230)) ;
		$this->rightPage->addFrame( $this->frmFooter=new BFrame( $this, "Footer", "", 20, 275, $this->sheetWidth - 20 - 10, 20)) ;

		/**
		 * setup some default character format
		 */
		$this->defCharFmt	=	new BCharFmt() ;
		$this->defParaFmt	=	new BParaFmt() ;
		$this->defParaFmt->setCharFmt( $this->defCharFmt) ;

		$this->rcvrCharFmt	=	new BCharFmt() ;
		$this->rcvrCharFmt->setCharSize( 10) ;
		$this->rcvrParaFmt	=	new BParaFmt() ;
		$this->rcvrParaFmt->setCharFmt( $this->rcvrCharFmt) ;

		$this->infoCharFmt	=	new BCharFmt() ;
		$this->infoCharFmt->setRGB( 204, 204, 204) ;
		$this->infoParaFmt	=	new BParaFmt() ;
		$this->infoParaFmt->setCharFmt( $this->infoCharFmt) ;

		$this->cdCharFmt	=	new BCharFmt() ;
		$this->cdCharFmt->setRGB( 153, 153, 153) ;
		$this->cdCharFmt->setCharSize( 9) ;				// 9pt font
		$this->cdParaFmt	=	new BParaFmt() ;
		$this->cdParaFmt->setCharFmt( $this->cdCharFmt) ;
		$this->cdParaFmt->setLineSpacing( 1.0) ;
		$this->cdParaFmt->setAlignment( BParaFmt::alignRight) ;

		$this->cdSCharFmt	=	new BCharFmt() ;
		$this->cdSCharFmt->setRGB( 153, 153, 153) ;
		$this->cdSCharFmt->setCharSize( 7) ;				// 9pt font
		$this->cdSParaFmt	=	new BParaFmt() ;
		$this->cdSParaFmt->setCharFmt( $this->cdSCharFmt) ;
		$this->cdSParaFmt->setLineSpacing( 1.2) ;
		$this->cdSParaFmt->setAlignment( BParaFmt::alignRight) ;

		$this->refCharFmt	=	new BCharFmt() ;
		$this->refCharFmt->setCharSize( 18) ;			// 18pt font
		$this->refCharFmt->setRGB( 230, 230, 230) ;
		$this->refParaFmt	=	new BParaFmt() ;
		$this->refParaFmt->setLineSpacing( 1.0) ;
		$this->refParaFmt->setCharFmt( $this->refCharFmt) ;
				
		/**
		 * setup some default paragraph format
		 */
		$this->defTableCellFmt	=	new BParaFmt() ;
		$this->defTableCellFmt->lineSpacing	=	1.5 ;
		$this->defTableCellFmt->setCharFmt( $this->defCharFmt) ;

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
		$this->refLine	=	"" ;
		
		$this->_debug() ;
	}

	function	setFormat() {
	}

	/**
	 * addMyText
	 * adds a block of plain text to the main frame
	 *
	 * @param string $_text plain text to be written
	 * @return void
	 */
	function	addMyText( $_text="") {
		FDbg::get()->dumpL( 0x01000000, "BDocRegLetter::addMyText(_text='%s'", $_text) ;
		BDoc::addText( $this->defParaFmt, $_text) ;
	}
	
	/**
	 * addMyText
	 * adds a block of plain text to the main frame
	 *
	 * @param string $_text plain text to be written
	 * @return void
	 */
	function	skipMyLine() {
		FDbg::get()->dumpL( 0x01000000, "BDocRegLetter::skipMyLine()") ;
		$this->skipLine( $this->defParaFmt) ;
	}
	
	/**
	 * addMyXML
	 * adds a block of XML formatted text to the main frame
	 *
	 * @param string $_xml XML formatted text to be written
	 * @return void
	 */
	function	addMyXML( $_xml) {
		FDbg::get()->dumpL( 0x01000000, "BDocRegLetter::addXML(...):<br/>\n") ;
		BDoc::addXML( $this->defParaFmt, $_xml) ;
	}

	/**
	 * setupFirstPage
	 * This method overides the setupFirstPage() method from the base class.
	 * The method performs the setup of the first page for the document.
	 *
	 * @return void
	 */
	function	setupFirstPage() {
		$this->setupHeaderFirst( $this->frmHeaderFirst) ;
		$this->setupFooter( $this->frmFooterFirst) ;
		$this->setupRcvrField( $this->frmRcvrAddr) ;
		$this->writeRcvrField( $this->frmRcvrAddr) ;
		$this->writeInfoField( $this->frmInfo) ;
		$this->writeRefField( $this->frmRef) ;

		/**
		 * make a small line for the folding marks
		 */
		$this->myfpdf->Line( mmToPt( 0), mmToPt( 105),
								mmToPt( 5), mmToPt( 105)) ;
		$this->myfpdf->Line( mmToPt( 0), mmToPt( 204),
								mmToPt( 5), mmToPt( 204)) ;
	}

	/**
	 * setupMidPage
	 * This method overides the setupMidPage() method from the base class.
	 * The method performs the setup of a middle page for the document.
	 *
	 * @return void
	 */
	function	setupMidPage() {
		$this->setupHeaderMid( $this->frmHeader) ;
		$this->setupFooter( $this->frmFooter) ;
		
		/**
		 * make a small line for the folding marks
		 */
		$this->myfpdf->Line( mmToPt( 0), mmToPt( 87),
								mmToPt( 5), mmToPt( 87)) ;
		$this->myfpdf->Line( mmToPt( 0), mmToPt( 192),
								mmToPt( 5), mmToPt( 192)) ;
	}

	/**
	 * setupLastPage
	 * This method overides the setupLastPage() method from the base class.
	 * The method performs the setup of the last page for the document.
	 *
	 * @return void
	 */
	function	setupLastPage() {
	}

	/**
	 * setupHeaderFirst
	 * The method adds the header for the first page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderFirst( $_frm) {
		FDbg::dumpL( 0x00010000, "BDocRegLetter::setupHeaderFirst(...), fontName='%s'", $this->cdParaFmt->getCharFmt()->getFontName()) ;
		$this->cdParaFmt->getCharFmt()->activate( $this->myfpdf) ;
		$this->myfpdf->Image( $this->path->Logos . "logo_brief.jpg", $_frm->horOffs, $_frm->verOffs, mmToPt(40), mmToPt(24), "jpg") ;

		/**
		 * right block of letter footer
		 */
		$_frm->addLine( "mein-mikroskop.de - Karl-Heinz Welter", $this->cdParaFmt) ;
		$_frm->addLine( "Muehlenstr. 66c", $this->cdParaFmt) ;
		$_frm->addLine( "52134 Herzogenrath", $this->cdParaFmt) ;
		$_frm->addLine( "Tel. +49 (0)2407 56 456 94", $this->cdParaFmt) ;
		$_frm->addLine( "Fax. +49 (0)2407 56 456 97", $this->cdParaFmt) ;
		$_frm->addLine( "www.mein-mikroskop.de", $this->cdSParaFmt) ;
		$_frm->addLine( "mail@mein-mikroskop.de", $this->cdSParaFmt) ;

		// draw the separating line between the header and the document content
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
								$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;

	}

	/**
	 * setupHeaderMid
	 * The method adds the header for a middle page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupHeaderMid( $_frm) {

		/**
		 * start the header
		 */
		FDbg::dumpL( 0x00010000, "BDocRegLetter::setupHeaderMid(...), fontName='%s'", $this->cdParaFmt->getCharFmt()->getFontName()) ;
		$this->cdParaFmt->getCharFmt()->activate( $this->myfpdf) ;

		/**
		 * 
		 */
		$_frm->addLine( "Die abgeleitete Klasse sollte Ihre eigene Methode setupHeaderMid(...) definieren !", $this->cdParaFmt);

		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $_frm->height + mmToPt( 1.0),
										$_frm->horOffs + $_frm->width, $_frm->verOffs + $_frm->height + mmToPt( 1.0)) ;

	}

	/**
	 * setupFooter
	 * The method adds the footer for any page to the frame passed as argument.
	 *
	 * @return void
	 */
	function	setupFooter( $_frm) {

		/**
		 * start the footer
		 */
		$this->cdParaFmt->getCharFmt()->activate( $this->myfpdf) ;

		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs - mmToPt( 1.0),
								$_frm->horOffs + $_frm->width, $_frm->verOffs - mmToPt( 1.0)) ;

		/**
		 * left block of letter footer
		 */
		FDbg::get()->dumpL( 2, "footerVerStart=%f", $_frm->verOffs) ;
		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + 12, "meinmikroskop.de" );
		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + 22, "Karl-Heinz Welter" );
		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + 32, "" );
		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + 42, "USt-IdNr.: DE254594481" );

		/**
		 * center block of letter footer
		 */
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(60), $_frm->verOffs + 12, "" );
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(60), $_frm->verOffs + 22, "" );
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(60), $_frm->verOffs + 32, "" );
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(60), $_frm->verOffs + 42, "" );

		/**
		 * right block of letter footer
		 */
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(120), $_frm->verOffs + 12, "Commerzbank Aachen" );
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(120), $_frm->verOffs + 22, "BLZ 390 400 13" );
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(120), $_frm->verOffs + 32, "Kto.-Nr.: 5008446" );
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(120), $_frm->verOffs + 42, "" );
	}

	/**
	 * setupRcvrField
	 * The method adds the receiver field, thus it's only to be used for setting up the first page of the document.
	 *
	 * @return void
	 */
	function	setupRcvrField( $_frm) {

		FDbg::get()->dump( "BDocRegLetter::setupRcvrField(...)") ;
		$relVerOffs	=	0 ;

		/**
		 * start the footer
		 */
		$this->cdParaFmt->getCharFmt()->activate( $this->myfpdf) ;

		/**
		 * start with the very thin sender address in the recipient field
		 */
		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + $relVerOffs, "mein-mikroskop.de, Muehlenstr. 66c, D-52134 Herzogenrath" ) ;
		$relVerOffs	+=	mmToPt( 1) ;

		/**
		 * draw the separating line between the header and the document content
		 */
		$this->myfpdf->Line( $_frm->horOffs, $_frm->verOffs + $relVerOffs,
								$_frm->horOffs + $_frm->width, $_frm->verOffs + $relVerOffs) ;
		
	}

	/**
	 * writeRcvrField
	 * The method writes the receiver field to the frame passed as argument.
	 *
	 * @return void
	 */
	function	writeRcvrField( $_frm) {

		FDbg::get()->dump( "BDocRegLetter::writeRcvrField(...)") ;
		$relVerOffs	=	mmToPt( 10) ;

		/**
		 * start the address
		 */
		$this->defParaFmt->getCharFmt()->activate( $this->myfpdf) ;

		/**
		 * draw the separating line between the header and the document content
		 */
		$_frm->skipLine( $this->rcvrParaFmt) ;
		for ( $ndx = 1 ; $ndx <= 6 ; $ndx++) {
			FDbg::get()->dumpL( 0x02000000, "BDocRegLetter::writeRcvrField(...), ndx=%d, data='%s'", $ndx) ;
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

		$relVerOffs	=	0 ;

		/**
		 * 
		 */
		$this->infoParaFmt->getCharFmt()->activate( $this->myfpdf) ;
		
		/**
		 * draw the separating line between the header and the document content
		 */
		for ( $ndx = 1 ; $ndx <= 10 ; $ndx++) {
			$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + $relVerOffs, $this->infoTitle[ $ndx] ) ;
			$myString	=	$this->infoData[ $ndx] ;
			$this->myfpdf->Text( $_frm->horOffs + $_frm->width - $this->textWidth( $myString), $_frm->verOffs + $relVerOffs, $myString ) ;
			$relVerOffs	+=	mmToPt( 4.5) ;
		}

	}

	/**
	 * writeRefField
	 * The method writes the reference (Betrifft) field to the frame passed as argument.
	 *
	 * @return void
	 */
	function	writeRefField( $_frm) {

		$relVerOffs	=	0 ;

		/**
		 * start the address
		 */
		$this->refCharFmt->activate( $this->myfpdf) ;

		/**
		 * draw the separating line between the header and the document content
		 */
		$this->frmRef->addLine( $this->refLine, $this->refParaFmt) ;

	}

	function	printPrefix() {
	}

	function	printPostfix() {
	}

	/**
	 * setRcvr
	 * The method adds the _text for the receiver line specified by _ndx.
	 *
	 * @return void
	 */
	function	setRcvr( $_ndx, $_text) {
		FDbg::get()->dump( "BDocRegLetter::setRcvr( %d, '%s')", $_ndx, $_text) ;
		if ( $_ndx >= 1 && $_ndx <= 6) {
			$this->rcvrLines[ $_ndx]	=	$_text ;
		}
	}

	/**
	 * setInfo
	 * The method adds the _text for the info line specified by _ndx.
	 *
	 * @return void
	 */
	function	setInfo( $_ndx, $_title, $_data) {
		if ( $_ndx >= 1 && $_ndx <= 10) {
			$this->infoTitle[ $_ndx]	=	$_title ;
			$this->infoData[ $_ndx]	=	$_data ;
		}
	}

	/**
	 * setRef
	 * The method adds the _text for the reference line specified by _ndx.
	 *
	 * @return void
	 */
	function	setRef( $_ref) {
		$this->refLine	=	$_ref ;
	}

	/**
	 * addTable
	 * The method adds the _table to the main frame.
	 *
	 * @return void
	 */
	function	addTable( $_table) {
		BDoc::addTable( $this->defParaFmt, $_table) ;
	}

	/**
	 * 
	 * 
	 *
	 * @param unknown_type $_token
	 */
	function	cascTokenStart( $_token) {
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_token
	 */
	function	cascTokenEnd( $_token) {
	}

	/**
	 * Debug
	 *
	 * @return void
	 */
	function	_debug() {
		//		printf( "dbg: BDocRegLetter::_debug():<br/>\n") ;
		BDoc::_debug() ;
	}

}

?>
