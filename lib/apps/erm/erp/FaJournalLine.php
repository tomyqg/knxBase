<?php
/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 *
 * Revision history
 *  
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-06-25	PA1		khw		added;
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * requires the WTF Debugger and the Base Class
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;
/**
 * JournalLine 
 *
 * @package Application
 * @subpackage CuOrdr
 */
class	FaJournalLine	extends	AppObject	{
	/**
	 * 
	 * @var unknown_type
	 */
	/**
	 *
	 */
	function	__construct() {
		parent::__construct( "FaJournalLine", "Id") ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getTableDepAsXML()
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		require_once( "FaJournalLine.php" );
		$filter	=	"" ;
		$_searchCrit	=	"" ;
		if ( isset( $_POST['_SSearch']))
			$_searchCrit	=	$_POST['_SSearch'] ;
		$objName	=	$_val ;
		$_POST['_step']	=	$_id ;
		if ( $objName == "FaJournalLineItem") {
			$tmpObj	=	new FaJournalLineItem() ;
			$tmpObj->setId( $_id) ;
			$filter	.=	"C.JournalNo = '" . $this->JournalNo . "' AND C.LineNo = $this->LineNo " ;
			return $tmpObj->tableFromDb( "", "", $filter) ;
		}
	}
	/**
	 *
	 */
	function	getNextLineNo() {
		$query	=	sprintf( "SELECT LineNo FROM FaJournalLine ORDER BY LineNo DESC LIMIT 0, 1 ", $this->JournalNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->LineNo	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
}
?>
