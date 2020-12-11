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
 * BPage - Base Class for Page Definition
 *
 * This class mainly maintains data for page formatting.
 * The basic idea for this particular implementation is coming from Adobe
 * FrameMaker 4.
 *
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	FmkPage	{

	var	$parent	=	null ;
	private	$name	=	"" ;
	private	$sheetWidth ;		// width of the sheet in [mm]
	private	$sheetHeight ;		// height if the sheet in [mm]
	public	$frames =	array() ;

	/**
	 * Enter description here...
	 *
	 * @param BDoc $_doc
	 * @param string $_name
	 */
	function	__construct( $_parent, $_name) {
		FDbg::begin( 1, "FmkPage.php", "FmkPage", "__construct( <_parent>, '$_name')") ;
		$this->parent	=	$_parent ;			// := $myDoc !!!
		$this->name		=	$_name ;
		$this->sheetWidth	=	mmToPt( 210) ;
		$this->sheetHeight	=	mmToPt( 297) ;
		FDbg::end( 1, "FmkPage.php", "FmkPage", "__construct( <_parent>, '$_name')") ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_frm
	 */
	function	addFrame( $_frm) {
		FDbg::dumpL( 0x01000000, "BPage::addFrame(): name='%s'", $_frm->getName()) ;
		$this->frames[ $_frm->getName()]	=	$_frm ;
		$this->myDoc->addFrame( $_frm) ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_frm
	 */
	function	resetFrames() {
		FDbg::dumpL( 0x01000000, "BPage::resetFrames(): ") ;
		foreach ( $this->frames as $ndx => $frm) {
			FDbg::dumpL( 0x02000000, "BPage::resetFrames(): frameName=%s", $frm->getName()) ;
			$frm->currHorPos	=	0.0 ;
			$frm->currVerPos	=	0.0 ;
		}
		reset( $this->frames) ;
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
		reset( $this->frames) ;
		foreach ( $this->frames as $ndx => $frm) {
			if ( strcmp( $frm->getFlowName(), $_flowName) == 0) {
				reset( $this->frames) ;
				return $frm ;
			}
		}
		reset( $this->frames) ;
		return false ;
	}
	function	set( $_attr, $_value) {
		switch ( $_attr) {
			default	:
				FDbg::trace( 2, FDbg::mdTrcInfo1, "FmkPage.php", "FmkPage", "set( $_attr, $_value)") ;
				$this->$_attr	=	$_value ;
				break ;
		}
	}
	/**
	 * Enter description here...
	 *
	 */
	function	_debug( $_prefix="") {
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix."+--------------------------------------------------") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." Page own attributes") ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => name=%s", $this->name) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => width=%dpt", $this->sheetWidth) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." => height=%dpt", $this->sheetHeight) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", $_prefix." Nr. of Frames: %d", count( $this->frames)) ;
		foreach ( $this->frames as $index => $obj) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", " frame['$index']") ;
			$obj->_debug( " ".$_prefix) ;
		}
	}


}

?>
