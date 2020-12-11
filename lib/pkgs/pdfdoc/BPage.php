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
 * BPage - Base Class for Page Definition
 *
 * This class mainly maintains data for page formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	BPage	{

	private	$name ;				// name of the master page
	private	$myDoc ;			// parent document
	private	$sheetWidth ;		// width of the sheet in [mm]
	private	$sheetHeight ;		// height if the sheet in [mm]
	public	$frames =	array() ;

	/**
	 * Enter description here...
	 *
	 * @param BDoc $_doc
	 * @param string $_name
	 */
	function	__construct( $_doc, $_name) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <BDoc>, '$_name')") ;
		$this->myDoc	=	$_doc ;
		$this->name		=	$_name ;
		$this->sheetWidth	=	mmToPt( 210) ;
		$this->sheetHeight	=	mmToPt( 297) ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_frm
	 */
	function	addFrame( $_frm) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( <BFrame>)") ;
		$this->myFrames[ $_frm->getName()]	=	$_frm ;
		$this->myDoc->addFrame( $_frm) ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_frm
	 */
	function	resetFrames() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		foreach ( $this->myFrames as $ndx => $frm) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "frameName='" . $frm->getName() . "'") ;
			$frm->currHorPos	=	0.0 ;
			$frm->currVerPos	=	0.0 ;
		}
		reset( $this->myFrames) ;
		FDbg::end() ;
	}

	/**
	 * Enter description here...
	 *
	 * @return sring
	 */
	function	getName() {
		return $this->name ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_flowName
	 * @return unknown
	 */
	function	getFrameByFlowName( $_flowName) {
		reset( $this->myFrames) ;
		foreach ( $this->myFrames as $ndx => $frm) {
			if ( strcmp( $frm->getFlowName(), $_flowName) == 0) {
				reset( $this->myFrames) ;
				return $frm ;
			}
		}
		reset( $this->myFrames) ;
		return false ;
	}

	/**
	 * Enter description here...
	 *
	 */
	function	_debug() {
		if ( FDbg::enabled()) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Page::_debug") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Page own attributes") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " => name=" . $this->name) ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " => width=" . $this->sheetWidth . " pt") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " => height=" . $this->sheetHeight . " pt") ;
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Nr. of Frames: " . count( $this->myFrames)) ;
		}
	}


}

?>
