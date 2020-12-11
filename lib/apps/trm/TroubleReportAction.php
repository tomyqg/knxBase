<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * TroubleReportAction - Base Class
 *
 * @package Application
 * @subpackage Basic
 */
class	TroubleReportAction	extends	AppDepObject_R2	{
	/**
	 *
	 */
	function	__construct( $_myTroubleReportNo="", $_myTroubleReportActionNo="") {
		parent::__construct( "TroubleReportAction", array( "TroubleReportNo", "ItemNo")) ;
		if ( strlen( $_myTroubleReportNo) > 0) {
			try {
				$this->setKey( array( $_myTroubleReportNo, $_myTroubleReportActionNo)) ;
			} catch ( Exception $e) {
				throw $e ;
			}
		}
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	add( $_key, $_id, $_val) {
		FDbg::dumpL( 0x00000001, "TroubleReportAction::new(...)") ;
		return $this->_status ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TroubleReportAction.php", "TroubleReportAction", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
		FDbg::end( 1, "TroubleReportAction.php", "TroubleReportAction", "_upd()") ;
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
	function	setTroubleReportActionNo( $_myTroubleReportNo, $_myTroubleReportActionNo) {
		FDbg::begin( 1, "TroubleReportAction.php", "TroubleReportAction", "setTroubleReportActionNo( '$_myTroubleReportNo', '$_myTroubleReportActionNo')") ;
		$this->setKey( array( $_myTroubleReportNo, $_myTroubleReportActionNo)) ;
		FDbg::end() ;
		return $this->_valid ;
	}
	/**
	 *
	 * @return char
	 */
	function	newTroubleReportAction() {
		FDbg::begin( 1, "TroubleReportAction.php", "TroubleReportAction", "newTroubleReportAction()") ;
		$query	=	sprintf( "SELECT ActionNo FROM TroubleReportAction WHERE TroubleReportNo='%s' ORDER BY ActionNo DESC LIMIT 0, 1 ", $this->TroubleReportNo) ;
		$sqlResult	=	FDb::query( $query) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       FDb::getRow( $sqlResult) ; 
			$this->ActionNo	=	sprintf( "%03d", intval( $row['ActionNo']) + 1) ;
			$this->storeInDb() ;
		}
		FDbg::end() ;
		return $this->_status ;
	}	
	/**
	 *
	 */
	function	_getNextItemNo() {
		FDbg::begin( 1, "TroubleReportAction.php", "TroubleReportAction", "getTroubleReportActionNo()") ;
		$query	=	"SELECT ActionNo FROM TroubleReportAction WHERE TroubleReportNo='".$this->TroubleReportNo."' ORDER BY ActionNo DESC LIMIT 0, 1 " ;
		try {
			$row	=	FDb::queryRow( $query) ;
			if ( $row) {
				$this->ActionNo	=	sprintf( "%03d", intval( $row['ActionNo']) + 1) ;
			} else {
				$this->ActionNo	=	"001" ;
			}
		} catch ( Exception $e) { 
			throw $e ;
		}
		FDbg::end() ;
	}
	/**
	 * Use Case Methods
	 */
	function	getTroubleReportActionNo() {
		FDbg::begin( 1, "TroubleReportAction.php", "TroubleReportAction", "getTroubleReportActionNo()") ;
		$this->_status	=	0 ;
		$query	=	"select ActionNo from TroubleReportAction " ;
		$query	.=	"where TroubleReportNo = '" . $this->TroubleReportNo . "' " ;
		$query	.=	"order by TroubleReportNo DESC " ;
		$query	.=	"Limit 0, 1 " ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
			$this->TroubleReportNo	=	sprintf( "%d", 1) ;
			$this->_valid   =       true ; 
		} else { 
			$numrows        =       mysql_affected_rows( FDb::get()) ; 
			if ( $numrows == 1) { 
				$row    =       mysql_fetch_assoc( $sqlResult) ; 
				$this->ActionNo	=	sprintf( "%03d", intval( $row['ActionNo']) + 1) ;
				$this->_valid   =       true ; 
			} else {
				$this->ActionNo	=	sprintf( "%03d", 1) ;
				$this->_valid   =       true ; 
			}
		}
		FDbg::end() ;
		return $this->_status ;
	}
	/**
	 *	Funktion:	identifyTroubleReportAction
	 */
}

?>
