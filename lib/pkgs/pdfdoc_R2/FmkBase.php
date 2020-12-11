<?php
/**
 * FrameMaker like document formatter with PDF output
 *
 * This module requires fpdf to be available. See: http://www.fpdf.de/
 *
 * FmkBase takes an XML file, describing a document format similar to a framemaker document
 * base on pages->frames and flows.
 * IT DOES NOT SUPPORT THE FULL SCOPE OF FRAMEMAKER CAPABILITIES TODAY!
 *
 * Used extensions and abbreviations:
 * fmk		framemaker
 * fmt		framemaker template
 * frm		framemaker document
 * pdf		as known
 *
 *  Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-09-17	PA1		khw		derived from BDoc.php with the target
 * 								to enable pictures in a table and table-cell
 * 								content to flow to the next page in case it does
 * 								not fit;
 * 								XML based version including all formats
 * 								defined per XML;
 *
 */
class	FmkBase	{
	/**
	 *
	 * @var unknown
	 */
	const	DocSidedSingle		=  1 ;		// single sided, only
	const	DocSidedDouble		=  2 ;		// double sided only odd and even pages
	const	DocSidedSingleWF	=  3 ;		// regular letter
	const	DocSidedDoubleWF	=  4 ;		// just a plain sheet of paper
	/**
	 *
	 */
	var	$parent	=	null ;
	var	$formats	=	null ;
	var	$flows	=	array() ;
	var	$charFmt	=	array() ;
	var	$paraFmt	=	array() ;
	var	$tableFmt	=	array() ;
	var	$pages		=	array() ;
	var	$currCharFmt	=	null ;
	var	$currParFmt	=	null ;
	var	$currFrame	=	null ;
	static	$level	=	0 ;
	var	$pageNo	=	0 ;
	public	$currMasterPage	=	null ;
	/**
	 *
	 * @param string $_fileName
	 * @param string $_endTag
	 * @param string $_parent
	 */
	function	__construct( $_fileName, $_endTag="fmt", $_parent=null) {
		FDbg::begin( 1, "FmkBase.php", "FmkBase", "__construct( <parent>)") ;
		$this->parent	=	$_parent ;
		$this->docSided	=	FmkBase::DocSidedSingleWF ;
		$this->load( $_fileName, $_endTag, $_parent) ;
		FDbg::end( 1, "FmkBase.php", "FmkBase", "__construct( <parent>)") ;
	}
	/**
	 *
	 * @param string $_fileName	name of the file to be read
	 * @param string $_endTag	name of the end-tag to look for
	 */
	function	load( $_fileName, $_endTag="") {
		FDbg::begin( 1, "FmkBase.php", "FmkBase", "load( '$_fileName', '$_endTag')") ;
		//		echo "HTML::create( <xml>, <_parent>'): begin\n" ;
		self::$level++ ;
		$currObj	=	null ;
		$file	=	fopen( $_fileName, "r") ;
		if ( $file) {
			$xmlText	=	fread( $file, 65535) ;
			fclose( $file) ;
		} else {
			echo "no valid xml-file\n" ;
			die() ;
		}
		/**
		 * create XML reader, assign text, create HTML tree and output it
		 */
		$xml	=	new XMLReader() ;
		$xml->XML( $xmlText) ;
		$attributes	=	array() ;
		$end	=	false ;
		while ( $xml->read() && ! $end) {
			switch ( $xml->nodeType) {
				case	1	:			// start element
					/**
					 * disect the tag name for namespace:tag
					 */
					$v	=	explode( ":", $xml->name) ;
					if ( isset( $v[1])) {
						$ns	=	$v[0] ;
						$tag	=	$v[1] ;
					} else {
						//					echo "defaulting to namespace html\n" ;
						$ns	=	"fmk" ;
						$tag	=	$v[0] ;
					}
					while ( $xml->moveToNextAttribute()) {
						$attributes[ $xml->name]	=	$xml->value ;
					}
					$xml->moveToElement() ;
					/**
					 * perform action depending on namespace
					 */
					switch ( $ns) {
						case	"fmk"	:
							break ;
					}
					FDbg::trace( 101, FDBg::mdTrcInfo1, "FmkBase.php", "FmkBase", "load( ...)", "tag := '".$xml->name."'") ;
					if ( isset( $attributes[ "name"]))
						$actName	=	$attributes[ "name"] ;
					else
						$actName	=	"NO NAME ATTRIBUTE" ;
					if ( isset( $attributes[ "ref"]))
						$refName	=	$attributes[ "ref"] ;
					else
						$refName	=	"NO REFERENCE ATTRIBUTE" ;
					switch ( $xml->name) {
						case	"doc"	:
							$this->genDoc( $xml->readOuterXml()) ;
							$xml->next() ;
							break ;
						case	"template"	:
							$this->load( $actName) ;
							break ;
						case	"charformat"	:
							$this->charFmt[ $actName]	=	new FmkCharFmt( $this, $actName) ;
							$currObj	=	$this->charFmt[ $actName] ;
							break ;
						case	"parformat"	:
							$this->paraFmt[ $actName]	=	new FmkParFmt( $this, $actName) ;
							$currParFmt	=	$this->paraFmt[ $actName] ;
							$currObj	=	$this->paraFmt[ $actName] ;
							break ;
						case	"tableformat"	:
							$this->tableFmt[ $actName]	=	new FmkTableFmt( $this, $actName) ;
							$currTableFmt	=	$this->tableFmt[ $actName] ;
							$currObj	=	$this->tableFmt[ $actName] ;
							break ;
						case	"tablecolumn"	:
							if ( $currTableFmt != null) {
								$currObj	=	new FmkTableColumn( $this, $actName) ;
								$currTableFmt->cols[]	=	$currObj ;
							} else {
							}
							break ;
						case	"tablerow"	:
							if ( $currTableFmt != null) {
								$currObj	=	new FmkTableRow( $this, $actName) ;
								$currObj->rowType	=	intval( $attributes[ "rowType"]) ;
								$currTableFmt->rows[]	=	$currObj ;
								$currTableRow	=	$currObj ;
							} else {
							}
							break ;
						case	"tablecell"	:
							if ( $currTableRow != null) {
								$currObj	=	new FmkTableCell( $this, $actName) ;
								$currTableCell	=	$currObj ;
								$currTableRow->cells[]	=	$currObj ;
							} else {
							}
							break ;
						case	"flow"	:
							$this->flows[ $actName]	=	new FmkFlow( $this, $actName) ;
							$currObj	=	$this->flows[ $actName] ;
							break ;
						case	"page"	:
							$this->pages[ $actName]	=	new FmkPage( $this, $actName) ;
							$currPage	=	$this->pages[ $actName] ;
							break ;
						case	"frame"	:
							$currPage->frames[ $actName]	=	new FmkFrame( $currPage, $actName) ;
							$currFrame	=	$currPage->frames[ $actName] ;
							$currObj	=	$currPage->frames[ $actName] ;
							break ;
						case	"tab"	:
							if ( $currParFmt != null) {
								$currParFmt->tabsPos[]	=	mmToPt( intval( $attributes[ "pos"])) ;
								$currParFmt->tabsType[]	=	$attributes[ "type"] ;
							}
							break ;
						default	:
							break ;
					}
					$openTag	=	$xml->name ;
					foreach ( $attributes as $ndx => $val) {
						unset( $attributes[ $ndx]) ;
					}
					break ;
				case	3	:			// text node
					$text	=	trim( $xml->value, "\n\t") ;
					FDbg::trace( 101, FDBg::mdTrcInfo1, "FmkBase.php", "FmkBase", "load( ...)", "text := '".$text."'") ;
					$attr	=	$text ;
					if ( $currObj != null) {
//						$currObj->_debug() ;
						$currObj->set( $openTag, $attr) ;
//						if ( $currObj->set( $openTag, $attr)) {
//						} else {
//							FDbg::trace( 0, FDBg::mdTrcInfo1, "FmkBase.php", "FmkBase", "load( ...)", "'$openTag' not a valid attribute'!") ;
//						}
					} else {
						FDbg::trace( 0, FDBg::mdTrcInfo1, "FmkBase.php", "FmkBase", "load( ...)", "no object to assign this value to!") ;
					}
					break ;
				case	XMLReader::CDATA	:
					$myCData	=	$xml->value ;
					break ;
				case	14	:			// whitespace node
					break ;
				case	15	:			// end element
					/**
					* disect the tag name for namespace:tag
					*/
					$v	=	explode( ":", $xml->name) ;
					if ( isset( $v[1])) {
						$ns	=	$v[0] ;
						$tag	=	$v[1] ;
					} else {
						$ns	=	"fmk" ;
						$tag	=	$v[0] ;
					}
					//				echo "comparing tag := '" . $tag . "' against _endTag := '" . $_endTag . "' \n" ;
					switch ( $xml->name) {
					case	"charformat"	:
						$currCharFmt	=	null ;
						$currObj	=	null ;
						break ;
					case	"parformat"	:
						$currParFmt	=	null ;
						$currObj	=	null ;
						break ;
					case	"tableformat"	:
						$currParFmt	=	null ;
						$currObj	=	null ;
						break ;
					case	"tablecolumn"	:
						$currParFmt	=	null ;
						$currObj	=	null ;
						break ;
					case	"tablerow"	:
						$currParFmt	=	null ;
						$currObj	=	null ;
						break ;
					case	"tablecell"	:
						$currParFmt	=	null ;
						$currObj	=	null ;
						break ;
					case	"flow"	:
						$currObj	=	null ;
						break ;
					case	"page"	:
//							$currPage->_debug() ;
						$currPage	=	null ;
						break ;
					case	"frame"	:
						$currObj	=	null ;
						break ;
					case	"flows"	:		// now we need to link our frames to the flows
						FDbg::trace( 2, "FmkBase.php", "FmkBase", "load( <...>)", "linking frames to flows") ;
						foreach ( $this->pages as $page) {
							FDbg::trace( 2, "FmkBase.php", "FmkBase", "load( <...>)", "working on page '".$page->getName()."'") ;
							foreach ( $page->frames as $frame) {
								FDbg::trace( 2, "FmkBase.php", "FmkBase", "load( <...>)", "working on frames '".$frame->getName()."'") ;
								$neededFlow	=	$frame->getFlowName() ;
								FDbg::trace( 2, "FmkBase.php", "FmkBase", "load( <...>)", "will link to flow '".$neededFlow."'") ;
								if ( isset( $this->flows[ $neededFlow])) {
									FDbg::trace( 2, "FmkBase.php", "FmkBase", "load( <...>)", "flow '".$neededFlow."' found") ;
									$frame->flow	=	$this->flows[ $neededFlow] ;
								} else {
									FDbg::trace( 2, "FmkBase.php", "FmkBase", "load( <...>)", "flow '".$neededFlow."' *NOT* found") ;
								}
							}
						}
						break ;
					default	:
						break ;
					}
//					if ( $tag == $_endTag)
//						$end	=	true ;
					break ;
			}
		}
		if ( ! $end) {
			//			echo "exiting due to end of xml\n" ;
			$end	=	true ;
		}
		self::$level-- ;
		FDbg::end( 1, "FmkBase.php", "FmkBase", "load( '$_fileName', '$_endTag')") ;
	}
	/**
	 *
	 * @param string $_xmlString	complete string <doc>...</doc> including tags
	 */
	function	genDoc( $_xmlString) {
		FDbg::begin( 1, "FmkBase.php", "FmkBase", "genDoc( <_xmlString>)") ;
		/**
		 * create XML reader, assign text, create HTML tree and output it
		 */
		$xml	=	new XMLReader() ;
		$xml->XML( $_xmlString) ;
		$attributes	=	array() ;
		while ( $xml->read()) {
			FDbg::trace( 2, "FmkBase.php", "FmkBase", "genDoc( <...>)", "node := '".$xml->name."'") ;
			switch ( $xml->nodeType) {
			case	1	:			// start element
				/**
				 * disect the tag name for namespace:tag
				 */
				$v	=	explode( ":", $xml->name) ;
				if ( isset( $v[1])) {
					$ns	=	$v[0] ;
					$tag	=	$v[1] ;
				} else {
					//					echo "defaulting to namespace html\n" ;
					$ns	=	"fmk" ;
					$tag	=	$v[0] ;
				}
				while ( $xml->moveToNextAttribute()) {
					$attributes[ $xml->name]	=	$xml->value ;
				}
				$xml->moveToElement() ;
				/**
				 * perform action depending on namespace
				 */
				switch ( $ns) {
					case	"fmk"	:
						break ;
				}
				/**
				 * perform action depending on tag
				 */
				switch ( $xml->name) {
				case	"doc"	:
					FDbg::trace( 0, FDbg::mdTrcInfo1, "FmkBase.php", "FmkBase", "genDoc( <...>)", "<doc> encountered") ;
					$myFormat[]	=	mmToPt( 210) ;
					$myFormat[]	=	mmToPt( 297) ;
					$this->myFPDF	=	new FPDF( "P", "pt", $myFormat) ;
//					$this->newPage() ;
					break ;
				case	"bgtext"	:
					/**
					 * determine the target frame
					 */
					if ( isset( $attributes[ "targetFlow"])) {
						$targetFlow	=	$attributes[ "targetFlow"] ;
						FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFrame.php", "FmkFrame", "genDoc( <...>)", "bgtext shall go into flow '".$targetFlow."'") ;
						if ( isset( $this->flows[ $targetFlow])) {
							$currFlow	=	$this->flows[ $targetFlow] ;
							$currFlow->addBGText( $xml->readOuterXml()) ;
							$xml->next() ;
						} else {
							FDbg::trace( 0, FDbg::mdTrcInfo1, "FmkBase.php", "FmkBase", "genDoc( <...>)", "targetflow '$targetFlow' does not exist!") ;
						}
					}
					break ;
				case	"text"	:
					/**
					 *
					 */
					if ( $this->currMasterPage == null) {
						$this->newPage() ;
					}
					/**
					 * determine the target frame
					 */
					if ( isset( $attributes[ "targetFlow"])) {
						$targetFlow	=	$attributes[ "targetFlow"] ;
						FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFrame.php", "FmkFrame", "genDoc( <...>)", "shall go into flow '".$targetFlow."'") ;
						if ( isset( $this->flows[ $targetFlow])) {
							$currFlow	=	$this->flows[ $targetFlow] ;
							$currFlow->addText( $xml->readOuterXml()) ;
							$xml->next() ;
						} else {
							FDbg::trace( 0, FDbg::mdTrcInfo1, "FmkBase.php", "FmkBase", "genDoc( <...>)", "targetflow '$targetFlow' does not exist!") ;
						}
					}
					break ;
				}
				foreach ( $attributes as $ndx => $val) {
					unset( $attributes[ $ndx]) ;
				}
				break ;
			case	15	:			// end element
				switch ( $xml->name) {
				case	"doc"	:
					FDbg::trace( 0, FDbg::mdTrcInfo1, "FmkBase.php", "FmkBase", "genDoc( <...>)", "</doc> encountered") ;
					$_pdfName	=	"test.pdf" ;
					if ( php_sapi_name() != 'cli' ) {
						if ( strlen( $_pdfName) > 0) {
							$this->myFPDF->Output( $_pdfName, "F") ;
						} else {
							$this->mem	=	$this->myFPDF->Output( $this->doc, "F" ) ;
							header( "Content-Disposition: inline; filename = TextOut.pdf", true ) ;
							header( "Content-Type: application/pdf",  true ) ;
							header( "Content-Length: $Size", true ) ;
//							echo( $this->mem );
						}
					} else {
						$this->myFPDF->Output( $_pdfName, "F") ;
						FDbg::trace( 2, FDbg::mdTrcInfo1, "FmkBase.php", "FmkBase", "genDoc( <...>)", "document written") ;
					}
					break ;
				}
				break ;
			}
		}
		FDbg::end( 1, "FmkBase.php", "FmkBase", "genDoc( <_xmlString>)") ;
	}
	/**
	 * create a new page for the current document
	 * this function creates a new page. if a page is still open, this page will be closed first
	 * and then a new page will be added. performs setup of the important page parameters like
	 * frame assignments (header, content, footer) for the respective page nr.
	 *
	 * @return void
	 */
	function	newPage() {
		FDbg::begin( 1, "FmkBase.php", "FmkBase", "newPage()") ;
		$this->pageNo++ ;					// goto next page
		$this->myFPDF->AddPage() ;			// add a new page to the pdf document
		/**
		 * get reference to current page layout
		 */
		if ( $this->pageNo == 1) {			// special
			switch ( $this->docSided) {
				case	FmkBase::DocSidedSingle	:
				case	FmkBase::DocSidedDouble	:
					$this->currMasterPage	=	$this->pages[ "right"] ;
					break ;
				case	FmkBase::DocSidedSingleWF	:
				case	FmkBase::DocSidedDoubleWF	:
					$this->currMasterPage	=	$this->pages[ "first"] ;
					break ;
			}
			/**
			 *
			 */
//			$this->currMasterPage->resetFrames() ;
//			$this->setupFirstPage() ;
		} else {
			switch ( $this->docSided) {
				case	FmkBase::DocSidedSingle	:
				case	FmkBase::DocSidedSingleWF	:
					$this->currMasterPage	=	$this->pages[ "right"] ;
					break ;
				case	FmkBase::DocSidedDouble	:
				case	FmkBase::DocSidedDoubleWF	:
					$this->currMasterPage	=	$this->pages[ "left"] ;
					break ;
			}
			/**
			 *
			 */
//			$this->currMasterPage->resetFrames() ;
//			$this->setupMidPage() ;
		}
		/**
		 * check for every frame if there's background stuff to be inserted
		 */
		FDbg::trace( 0, FDbg::mdTrcInfo1, "FmkBase.php", "FmkBase", "newPage()", "adding background stuff") ;
		foreach ( $this->currMasterPage->frames as $ndx => $frm) {
			FDbg::trace( 0, FDbg::mdTrcInfo1, "FmkBase.php", "FmkBase", "newPage()", "for '".$frm->getName()."'") ;
			$currFrame	=	$frm ;
			$currFrame->showBGText() ;
		}
		/**
		 * check for every frame if there's data left over in the respective flow
		 * which we need to process first
		 */
		FDbg::trace( 0, FDbg::mdTrcInfo1, "FmkBase.php", "FmkBase", "newPage()", "adding left-over stuff") ;
		foreach ( $this->currMasterPage->frames as $ndx => $frm) {
			FDbg::trace( 0, FDbg::mdTrcInfo1, "FmkBase.php", "FmkBase", "newPage()", "for '".$frm->getName()."'") ;
			$currFrame	=	$frm ;
		}
		/**
		 *
		 */
		$this->debugFrames	=	true ;
		if ( $this->debugFrames) {
			foreach ( $this->currMasterPage->frames as $ndx => $frm) {
				$frm->showFrame( $this->myFPDF) ;
			}
		}
		FDbg::end( 1, "FmkBase.php", "FmkBase", "newPage()") ;
	}
	/**
	 *
	 */
	function	_debug() {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "+--------------------------------------------------") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "own attributes") ;
//		FDbg::dump( " => name=%s", $this->name) ;
//		FDbg::dump( " => width=%dpt", $this->sheetWidth) ;
//		FDbg::dump( " => height=%dpt", $this->sheetHeight) ;
//		FDbg::dump( "Nr. of Frames: %d", count( $this->frames)) ;
		foreach ( $this->charFmt as $index => $obj) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " charFmt['$index']") ;
			$obj->_debug( " ") ;
		}
		foreach ( $this->paraFmt as $index => $obj) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "paraFmt['$index']") ;
			$obj->_debug( " ") ;
		}
		foreach ( $this->pages as $index => $obj) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " pages['$index']") ;
			$obj->_debug( " ") ;
		}
		foreach ( $this->flows as $index => $obj) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " flows['$index']") ;
			$obj->_debug( " ") ;
		}
		foreach ( $this->tableFmt as $index => $obj) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " ftableFmt['$index']") ;
			$obj->_debug( " ") ;
		}
	}
}
/**
 * Conversion of [mm] into [dots] based on 72 dpi and 25.4 mm / inch
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 * @param double|int $x input measure in [mm]
 * return double
 */
function mmToPt( $x ) {
	return 720 * $x / 254 ;
}
/**
 * Conversion of [dots] into [mm] based on 72 dpi and 25.4 mm / inch
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 * @param double|int $x input measure in [mm]
 * return double
 */
function ptToMm( $x ) {
	return 254 * $x / 720 ;
}
