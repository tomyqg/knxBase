<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
require_once( "MimeMail.php") ;
require_once( "XmlTools.php") ;
/**
 * DocumentRevision - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	DocumentRevision	extends	AppDepObjectCore	{

	/**
	 *
	 */
	function	__construct( $_myDocumentNo="", $_myDocumentRevisionNo="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myDocumentNo', '$_myDocumentRevisionNo')") ;
		parent::__construct( "DocumentRevision", array( "DocumentNo", "DocumentRevisionNo")) ;
		if ( strlen( $_myDocumentNo) > 0) {
			try {
				$this->setKey( array( $_myDocumentNo, $_myDocumentRevisionNo)) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
		FDbg::end() ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	add( $_key, $_id, $_val) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id)") ;
		return $this->_status ;
		FDbg::end() ;
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "Document.php", "DocumentRevision", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::end( 1, "Document.php", "DocumentRevision", "_upd()") ;
	}
		/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key, $_id, $_val) {
	}
	/**
	 *
	 */
	function	setDocumentRevisionNo( $_myDocumentNo, $_myDocumentRevisionNo) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myDocumentNo', '$_myDocumentRevisionNo')") ;
		$this->setKey( array( $_myDocumentNo, $_myDocumentRevisionNo)) ;
		if ( $this->_valid) {
			$this->Opening	=	$this->getSalutationLine() ;
		} else {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Document.php", "Document", "setDocumentRevisionNo( '$_myDocumentNo', '$_myDocumentRevisionNo')", "Document not valid!") ;
		}
		FDbg::end() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @return char
	 */
	function	newDocumentRevision() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$query	=	sprintf( "SELECT DocumentRevisionNo FROM DocumentRevision WHERE DocumentNo='%s' ORDER BY DocumentRevisionNo DESC LIMIT 0, 1 ", $this->DocumentNo) ;
		$sqlResult	=	FDb::query( $query) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$row    =       mysql_fetch_array( $sqlResult) ;
			$this->DocumentRevisionNo	=	sprintf( "%03d", intval( $row[0]) + 1) ;
			$this->storeInDb() ;
		}
		FDbg::end() ;
		return $this->_status ;
	}
	/**
	 * Use Case Methods
	 */
	function	getDocumentRevisionNo() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$this->_status	=	0 ;
		$query	=	"select DocumentRevisionNo from DocumentRevision " ;
		$query	.=	"where DocumentNo = '" . $this->DocumentNo . "' " ;
		$query	.=	"order by DocumentRevisionNo DESC " ;
		$query	.=	"Limit 0, 1 " ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
			$this->DocumentRevisionNo	=	sprintf( "%03d", 1) ;
			$this->_valid   =       true ;
		} else {
			$numrows        =       mysql_affected_rows( FDb::get()) ;
			if ( $numrows == 1) {
				$row    =       mysql_fetch_assoc( $sqlResult) ;
				$this->DocumentRevisionNo	=	sprintf( "%03d", intval( $row['DocumentRevisionNo']) + 1) ;
				$this->_valid   =       true ;
			} else {
				$this->DocumentRevisionNo	=	sprintf( "%03d", 1) ;
				$this->_valid   =       true ;
			}
		}
		FDbg::end() ;
		return $this->_status ;
	}
}

?>
