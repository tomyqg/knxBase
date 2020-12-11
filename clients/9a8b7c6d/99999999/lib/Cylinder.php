<?php
/**
 * Cylinder.php - Class definition
 *  Domain:
 *  	- administrative
 * 	Cylinder references:
 * 		- n/a
 *  Cylinder is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package CylinderCalc
 */
/**
 * Cylinder - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package CylinderCalc
 * @subpackage Classes
 */
class	Cylinder	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "Cylinder", "CylinderId") ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @param Reply $reply
	 * @return \Reply
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Cylinder.php", "Cylinder", "add( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$this->getFromPostL() ;					// we need to do this HERE
		$this->LastUpdateTicks	=	time() ;
		$this->getSaveId() ;
		parent::upd( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @param Reply $reply
	 * @return \Reply
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Cylinder.php", "Cylinder", "upd( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$this->getFromPostL() ;					// we need to do this HERE
		$myTime	=	time() ;		// get the current time
		$this->LastUpdate	=	FDateTime::timeToMySqlDateTime( $myTime) ;
		$this->LastUpdateTicks	=	FDateTime::timeToTicks( $myTime) ;
		$this->getSaveId() ;
		parent::upd( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * getList(...)
	 * ============
	 *
	 * Enter description here ...
	 *
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Cylinder.php", "Cylinder", "getList( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		$sortOrder	=	"ORDER BY CylinderId ASC " ;
		switch ( $objName) {
			case	""	:
				$_POST['_step']	=	$_val ;
				/**
				 *
				 */
				$myObj	=	new FDbObject( "CylinderSurvey") ;				// no specific object we need here
				if ( isset( $_POST['StartRow'])) {
					$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
				}
				$myQuery	=	$myObj->getQueryObj( "Select") ;
				$myQuery->addOrder( ["CylinderId"]) ;
				$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Cylinder") ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	function	getSaveId() {
		$this->Checksum	=	md5(
								$this->ManufacturerId
							.	$this->typ
							.	$this->Current
							.	$this->ReservoirType
							.	$this->OrderNo
							.	$this->BrakeType
							.	$this->OpeningPressure
							.	$this->PressureMin
							.	$this->PressureMax
							.	$this->thaw
							.	$this->tha0
							.	$this->spw
							.	$this->sp0
							.	$this->StrokeMax
							) ;
	}
	/**
	 *
	 */
	function	getOwnerField() {	return $this->keyCol ;	}
}
?>
