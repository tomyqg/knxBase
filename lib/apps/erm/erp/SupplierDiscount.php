<?php

/**
 * BDocument.php Base class for PDF-format printed matters
 *
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * SupplierDiscount - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BSupplierDiscount which should
 * not be modified.
 *
 * @package Application
 * @subpackage Lief
 */

class	SupplierDiscount	extends	AppObjectERM	{
	
	/**
	 *
	 */
	function	__construct() {
		parent::__construct( "SupplierDiscount", "Id") ;
	}
}
