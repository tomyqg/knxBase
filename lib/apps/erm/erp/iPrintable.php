<?php

interface	iPrintable {
	/**
	 * create a PDF file and return the complete XML object
	 * on return the $this->pdfName attribute must be set to the filename only
	 * @param unknown $_key
	 * @param unknown $_val
	 * @param unknown $_pdfName
	 */
	public	function	createPDF( $_key, $_val, $_pdfName) ;
	/**
	 * return the complete /path/to/file/name.pdf
	 * on return the $this->pdfName attribute must be set to the filename only
	 * @param unknown $_key
	 * @param unknown $_val
	 * @param unknown $_pdfName
	 */
	public	function	getPDF( $_cnt, $_size, $_pdfName) ;
	/**
	 * print the PDF file and return the complete /path/to/file/name.pdf
	 * on return the $this->pdfName attribute must be set to the filename only
	 * @param unknown $_key
	 * @param unknown $_val
	 * @param unknown $_pdfName
	 */
	public	function	printPDF( $_cnt, $_size, $_pdfName) ;
}

?>
