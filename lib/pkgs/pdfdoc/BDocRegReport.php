<?php

/**
 * BDocRegLetter.php Base class for Normal (A4, Letter-size) Letter in PDF-format
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @package wtcCoreSubSystem
 * @version 0.1
 * @filesource
 */
/**
 * BDocRegLetter - Basis Klasses für einen regulären Brief
 *
 * Diese Klasse basiert auf BDoc und stellt Methoden für das erstellen eines
 * normal formatierten Briefes zur Verfügung.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */

class	BDocRegReport extends BDoc	{

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
	public	$formMode	=	true ;
	
	/**
	 * BDoc - __construct
	 *
	 * Konstruktor für BDoc
	 * Initializes the document to A4 size, Regular Letter and Síngle-Sided with First page.
	 * Creates some standard character and paragraph formats.
	 *
	 * @return object
	 */
	function	__construct( $_copy=false) {
		FDbg::dumpL( 0x02000000, "BDocRegLetter::__constructor") ;

		BDoc::__construct( $_copy) ;
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
		$this->firstPage->addFrame( $this->frmContentFirst=new BFrame( $this, "ContentFirst", "Auto", 20, 40, $this->sheetWidth - 20 - 10, 230)) ;
		$this->firstPage->addFrame( $this->frmFooterFirst=new BFrame( $this, "FooterFirst", "", 20, 275, $this->sheetWidth - 20 - 10, 20)) ;
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
		FDbg::dumpL( 0x01000000, "BDocRegLetter::addMyText(_text='%s'", $_text) ;
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
		FDbg::dumpL( 0x01000000, "BDocRegLetter::skipMyLine()") ;
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
		FDbg::dumpL( 0x01000000, "BDocRegLetter::addXML(...):<br/>\n") ;
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

		/**
		 * make a small line for the folding marks
		 */
		if ( $this->formMode) {
			$this->myfpdf->Line( mmToPt( 0), mmToPt( 105),
									mmToPt( 5), mmToPt( 105)) ;
			$this->myfpdf->Line( mmToPt( 0), mmToPt( 210),
									mmToPt( 5), mmToPt( 210)) ;
		}
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
		if ( $this->formMode) {
			$this->myfpdf->Line( mmToPt( 0), mmToPt( 87),
									mmToPt( 5), mmToPt( 87)) ;
			$this->myfpdf->Line( mmToPt( 0), mmToPt( 192),
									mmToPt( 5), mmToPt( 192)) ;
		}
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
		FDbg::dumpL( 0x01000000, "BDocRegLetter::setupHeaderFirst(...), fontName='%s'", $this->cdParaFmt->getCharFmt()->getFontName()) ;
		$this->cdParaFmt->getCharFmt()->activate( $this->myfpdf) ;
		$logoFileName	=	$this->path->Logos . $this->backend->fullSiteName . "/logo_brief.jpg" ;
		if ( file_exists( $logoFileName)) {
			$this->myfpdf->Image( $logoFileName, $_frm->horOffs, $_frm->verOffs, mmToPt(40), mmToPt(24), "jpg") ;
		} else {
			$e	=	new Exception( "logo '$logoFileName' does not exist") ;
			FDbg::trace( 0, FDbg::mdTrcInfo1, "BDocRegLetter.php", "BDocRegLetter", "setupHeaderFirst( <_frm>)", $e) ;
			throw $e ;
		}
		/**
		 * right block of letter footer
		 */
		$_frm->addLine( "<MEINE DOMAIN> - <MEIN NAME>", $this->cdParaFmt) ;
		$_frm->addLine( "Meine Str. 4712", $this->cdParaFmt) ;
		$_frm->addLine( "00000 Irgendwo", $this->cdParaFmt) ;
		$_frm->addLine( "Tel. +49 (0)xxxx xxxx-x", $this->cdParaFmt) ;
		$_frm->addLine( "Fax. +49 (0)xxxx xxxx-xxx", $this->cdParaFmt) ;
		$_frm->addLine( "<MEINE SHOP URL>", $this->cdSParaFmt) ;
		$_frm->addLine( "<MEINE E-MAIL>", $this->cdSParaFmt) ;

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
		FDbg::dumpL( 0x01000000, "BDocRegLetter::setupHeaderMid(...), fontName='%s'", $this->cdParaFmt->getCharFmt()->getFontName()) ;
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
		FDbg::dumpL( 0x02000000, "footerVerStart=%f", $_frm->verOffs) ;
		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + 12, "<FIRMENNAME>" ) ;
		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + 22, "<MEHR FIRMENNAME>") ;
		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + 32, "<NOCH FIRMENNAME>") ;
		$this->myfpdf->Text( $_frm->horOffs, $_frm->verOffs + 42, "USt-IdNr.: DExxxxxxxxx") ;

		/**
		 * center block of letter footer
		 */
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(60), $_frm->verOffs + 12, "" ) ;
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(60), $_frm->verOffs + 22, "" ) ;
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(60), $_frm->verOffs + 32, "" ) ;
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(60), $_frm->verOffs + 42, "" ) ;

		/**
		 * right block of letter footer
		 */
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(120), $_frm->verOffs + 12, "Meine Bank" ) ;
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(120), $_frm->verOffs + 22, "BLZ xxx xxx xx" ) ;
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(120), $_frm->verOffs + 32, "Kto.-Nr.: xxx xxx xxx xxx" ) ;
		$this->myfpdf->Text( $_frm->horOffs + mmToPt(120), $_frm->verOffs + 42, "" ) ;
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
		FDbg::dumpL( 0x02000000, "BDocRegLetter.php::BDocRegLetter::setRcvr( $_ndx, '$_text'): begin") ;
		if ( $_ndx >= 1 && $_ndx <= 7) {
			$this->rcvrLines[ $_ndx]	=	$_text ;
		} else {
			FDbg::dumpL( 0x20000000, "BDocRegLetter.php::BDocRegLetter::setRcvr( $_ndx, '$_text'): index out of allowed range [1..7]!") ;	
		}
		FDbg::dumpL( 0x02000000, "BDocRegLetter.php::BDocRegLetter::setRcvr( $_ndx, '$_text'): end") ;
	}

	/**
	 * setInfo
	 * The method adds the _text for the info line specified by _ndx.
	 *
	 * @return void
	 */
	function	setInfo( $_ndx, $_title, $_data) {
		FDbg::dumpL( 0x02000000, "BDocRegLetter.php::BDocRegLetter::setInfo( $_ndx, '$_title', $_data'): begin") ;
		if ( $_ndx >= 1 && $_ndx <= 10) {
			$this->infoTitle[ $_ndx]	=	$_title ;
			$this->infoData[ $_ndx]	=	$_data ;
		} else {
			FDbg::dumpL( 0x20000000, "BDocRegLetter.php::BDocRegLetter::setInfo( $_ndx, '$_text'): index out of allowed range [1..10]!") ;	
		}
		FDbg::dumpL( 0x02000000, "BDocRegLetter.php::BDocRegLetter::setInfo( $_ndx, '$_title', $_data'): begin") ;
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
