<?php

/**
 * BDocParaFmt.php - Base Class for the creation of PDF documents based on
 * FPDFLib
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcCoreSubSystem
 * @filesource
 */
/**
 * BParaFmt - Base Class for Paragraph Formatting
 * procedural stuff in the start of the file
 * @package wtcCoreSubSystem
 * @subpackage PDFDoc
 */
class	BParaFmt	{

	const	alignLeft	=	1 ;
	const	alignCenter	=	2 ;
	const	alignRight	=	3 ;
	const	alignDec	=	4 ;

	private	$name ;
	private	$charFmt ;
	public	$indent ;
	public	$indentFirst ;
	public	$lineSpacing ;
	public	$alignment ;

	/**
	 * Enter description here...
	 *
	 */
	function	__construct() {
		$this->lineSpacing	=	1.5 ;
		$this->indent	=	0 ;
		$this->indentFirst	=	0 ;
		$this->alignment	=	BParaFmt::alignLeft ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_charFmt
	 */
	function	setCharFmt( $_charFmt) {
		$this->charFmt	=	$_charFmt ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getCharFmt() {
		return $this->charFmt ;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $_lineSpacing
	 */
	function	setLineSpacing( $_lineSpacing) {
		$this->lineSpacing	=	$_lineSpacing ;
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getLineSpacing() {
		return $this->lineSpacing ;
	}

	/**
	 * returns the height of the frame
	 *
	 * @return float Height of frame
	 */
	function	setAlignment( $_alignment) {
		$this->alignment	=	$_alignment ;
	}

	/**
	 * returns the height of the frame
	 *
	 * @return float Height of frame
	 */
	function	getAlignment() {
		return $this->alignment ;
	}

}

?>
