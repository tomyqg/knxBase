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
 * JournalLineItem 
 *
 * @package Application
 * @subpackage CuOrdr
 */
class	FaJournalLineItem	extends	AppObject	{
	/**
	 * 
	 * @var unknown_type
	 */
	/**
	 *
	 */
	function	__construct() {
		parent::__construct( "FaJournalLineItem", "Id") ;
	}
	/**
	 * 
	 */
	function	_getNextLineNo() {
		$query	=	sprintf( "SELECT LineNo FROM FaJournalLineItem WHERE JournalNo='%s' ORDER BY LineNo DESC LIMIT 0, 1 ",
								$this->JournalNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
			$this->LineNo	=	10 ;
		} else {
			$row    =       mysql_fetch_array( $sqlResult) ;
			$this->LineNo	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
	/**
	 * 
	 */
	function	_getNextItemNo() {
		$query	=	sprintf( "SELECT ItemNo FROM FaJournalLineItem WHERE JournalNo='%s' AND LineNo = '%d' ORDER BY ItemNo DESC LIMIT 0, 1 ",
								$this->JournalNo,
								$this->LineNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
			$this->ItemNo	=	10 ;
		} else {
			$row    =       mysql_fetch_array( $sqlResult) ;
			$this->ItemNo	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
	/**
	 *
	 */
	function	getNextLineItemNo() {
		$query	=	sprintf( "SELECT ItemNo FROM FaJournalLineItem WHERE JournalNo='%s' AND LineNo = '%d' ORDER BY ItemNo DESC LIMIT 0, 1 ",
								$this->JournalNo,
								$this->LineNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->ItemNo	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
	/**
	 * 
	 */
	function	_clear() {
		$this->AccountDebit	=	"" ;
		$this->AccountCredit	=	"" ;
		$this->AmountDebit	=	0.0 ;
		$this->AmountCredit	=	0.0 ;
	}
}
?>
