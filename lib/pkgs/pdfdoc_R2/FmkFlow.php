<?php

/**
 * BDocFlow.php - Base Class for the creation of PDF documents based on
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
 * BFlow - Base Class for Flow Definition
 *
 * This class mainly maintains data for character formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	FmkFlow	{

	const	typeAuto	=	0 ;
	const	typeMan		=	1 ;
	const	typeCutoff	=	2 ;

	private	$name	=	"" ;
	private	$parent	=	null ;
	private	$currFrame ;		// frame which is currently used for the flow
	private	$saveZone ;			//
	private	$auto	=	false ;		// flow will automatically generate a new page on overflow
	public	$type	=	"fg" ;		// foregroudn text, alt.: bg= background
	public	$bgText	=	"" ;

	/**
	 * Enter description here...
	 *
	 * @param BDoc $_doc
	 * @param string $_name
	 */
	function	__construct( $_parent, $_name, $_flowType=FmkFlow::typeAuto, $_saveZone=20) {
		FDbg::begin( 1, "FmkFlow.php", "FmkFlow", "__construct( <_parent>, '$_name')") ;
		$this->parent	=	$_parent ;
		$this->name		=	$_name ;
		$this->saveZone	=	$_saveZone ;
		$this->myRemainder	=	null ;
		FDbg::end( 1, "FmkFlow.php", "FmkFlow", "__construct( <_parent>, '$_name')") ;
	}


	/**
	 * addText
	 * writes a block of text, encoded in ISO-8859-1, to the PDF.
	 * output is inside the frame which is part of the "Auto"-connect flow
	 * and with the fiven paragraph formatting.
	 *
	 * @param BParaFmt	$_par
	 * @param string	$_text
	 * @return void
	 */
	function	addBGText( $_xmlString) {
		FDbg::begin( 1, "FmkFlow.php", "FmkFlow", "addBGText( <_xmlString>)") ;
		FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFlow.php", "FmkFlow", "addBGText( <...>)", "I am '".$this->name."'") ;
		$this->bgText	=	$_xmlString ;
		FDbg::begin( 1, "FmkFlow.php", "FmkFlow", "addBGText( <_xmlString>)") ;
	}
	function	addText( $_xmlString) {
		FDbg::begin( 1, "FmkFlow.php", "FmkFlow", "addText( <_xmlString>)") ;
		FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFlow.php", "FmkFlow", "addText( <...>)", "I am '".$this->name."'") ;
		/**
		 * create XML reader, assign text, create HTML tree and output it
		 */
		$xml	=	new XMLReader() ;
		$xml->XML( $_xmlString) ;
		$attributes	=	array() ;
		/**
		 * as long as there is data
		 * on the current page
		 * find the frame which we need
		 */
		$moreData	=	true ;
		$remainder	=	null ;
		do {
			$myPage	=	$this->parent->currMasterPage ;
			FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFlow.php", "FmkFlow", "addText( <...>)", "current page = '".$myPage->getName()."', has ".count( $myPage->frames)." frames") ;
			foreach ( $myPage->frames as $frame) {
				FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFlow.php", "FmkFlow", "addText( <...>)", "checking out frame '".$frame->getName()."' '".$frame->getFlowName()."'") ;
				if ( $frame->getFlowName() == $this->name) {
					FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFlow.php", "FmkFlow", "addText( <...>)", "found a frame on the current page") ;
					$remainder	=	$frame->addText( $xml, $remainder) ;
				}
			}
			/**
			 *	IF there's data left AND the flow can automatically create a new page
			 *		create a new page
			 *	ELSEIF there's data left AND the flow will NOT automatically create a new page
			 *		attach the remainder to the flows's buffer
			 *	OTHERWISE
			 * 		discard the remainder
			 */
			if ( $remainder != null && $this->auto) {
				$this->parent->newPage() ;
			} else if ( $remainder != null) {
				if ( $this->myRemainder == null) {
					$this->myRemainder	=	$remainder ;
					$remainder	=	null ;
					FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFlow.php", "FmkFlow", "addText( <...>)", "flow '".$this->name."' has a long-term remainder '".$this->myRemainder->getBuffer()."' ") ;
				} else {
					$this->myRemainder->add( $remainder) ;
				}
			}
		} while ( $remainder != null) ;
//		$myBox	=	new FmkBox( $this, $this->parent->myFPDF) ;
//		$myBox->addText( $_xmlString) ;
		/**
		 *
		 */
		FDbg::end( 1, "FmkFlow.php", "FmkFlow", "addText( <_xmlString>)") ;
	}
	/**
	 *
	 * @param string $_xmlString	complete string <doc>...</doc> including tags
	 * @return remainder more data to be processed
	 */
	function	showBGText( $_frame) {
		$_rem	=	"" ;
		FDbg::begin( 1, "FmkFlow.php", "FmkFlow", "showBGText( <_xmlString>)") ;
		FDbg::trace( 3, FDbg::mdTrcInfo1, "FmkFlow.php", "FmkFlow", "showBGText( <...>)", "I am '".$this->name."'") ;
		/**
		 *
		 */
		$remainder	=	null ;
		/**
		 * create XML reader, assign text, create HTML tree and output it
		 */
		if ( $this->bgText != "") {
			$xml	=	new XMLReader() ;
			$xml->XML( $this->bgText) ;
			$_frame->addText( $xml, $_rem) ;
		}
		/**
		 *
		 */
		FDbg::end( 1, "FmkFlow.php", "FmkFlow", "showBGText( <_xmlString>)") ;
		return $remainder ;
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
		$textWidth	=	$this->myDoc->myfpdf->GetStringWidth( $_text) ;
		return $textWidth ;
	}
	function	set( $_attr, $_value) {
		switch ( $_attr) {
		case	"auto"	:
			if ( $_value == true)
				$this->$_attr	=	true ;
			break ;
		default	:
			FDbg::trace( 2, FDbg::mdTrcInfo1, "FmkFlow.php", "FmkFlow", "set( $_attr, $_value)") ;
			$this->$_attr	=	$_value ;
			break ;
		}
	}
	/**
	 * Enter description here...
	 * @return sring
	 */
	function	getName() {
		return $this->name ;
	}
	/**
	 * Enter description here...
	 * @return sring
	 */
	function	getAuto() {
		return $this->auto ;
	}
	/**
	 *
	 */
	function	addTable( $_par, $_table) {
		$frm	=	$this->myDoc->currMasterPage->getFrameByFlowName( "default") ;
		$this->myTablePar	=	$_par ;
		$this->myTable	=	$_table ;
		$this->inTable	=	FALSE ;
		$this->cellFrame	=	new BFrame( $this->myDoc, "CellFrame", "", 0.0, 0.0, 0.0, 0.0) ;
		//
		for ( $actRow=$this->myTable->getFirstRow( BRow::RTHeaderTS) ; $actRow !== FALSE ; $actRow=$this->myTable->getNextRow()) {
			$this->punchTableRow( $actRow) ;
		}
		$frm->currVerPos	+=	1.5 ;		// now add the height of everythign we have output'
//		$this->tableHead() ;
//		$this->inTable	=	TRUE ;
	}
	/**
	 *
	 * @param string $_prefix
	 */
	function	_debug( $_prefix="") {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix."+--------------------------------------------------") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." FmkFlow: own attributes") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => name=".$this->name) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => bgText=s".$this->bgText) ;
	}
}

?>
