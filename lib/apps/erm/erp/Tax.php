<?php
/**
 * Tax.php - Base class for taxes
 *
 * Diese Datei beinhaltet die Definition der Klasse: Tax
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Tax - User-Level Klasse
 *
 * �This class acts as an interface towards the automatically generated BTax which should
 * not be modified.
 *
 * @package Application
 * @subpackage Tax
 */
class	Tax	extends	AppObjectERM	{
	/*
	 * The constructor can be passed an ArticleNr (TaxNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_taxClass="") {
		parent::__construct( "Tax", "TaxClass") ;
		if ( $_taxClass != "") {
			try {
				$this->TaxClass	=	$_taxClass ;
				$this->reload() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$myObj	=	new FDbObject( "Tax", "TaxClass", "def", "v_TaxSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"TaxClass LIKE '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
	function	getOption( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$filter	=	"( 1 = 1 ) " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Key", "var") ;
		$myObj->addCol( "Value", "var") ;
		$ret	=	$myObj->tableFromDb( ", C.TaxClass AS `Key`, C.Percentage AS Value ",
								" ",
								$filter,
								"ORDER BY C.TaxClass ASC ",
								$this->className,
								$this->className) ;
		return $ret ;
	}
	/**
	 *
	 * @param unknown_type $_taxClass
	 */
	function	setKey( $_taxClass) {
		$this->TaxClass	=	$_taxClass ;
		$this->reload() ;
		return $this->_valid ;
	}
}

?>
