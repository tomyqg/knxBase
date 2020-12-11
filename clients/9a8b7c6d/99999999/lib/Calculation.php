<?php
/**
 * Calculation.php
 * ===============
 *
 *  Domain:
 *  	- administrative
 * 	Calculation references:
 * 		- n/a
 *  Calculation is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package bpwBrakeCalculator
 */
/**
 * Calculation - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package CalculationCalc
 * @subpackage Classes
 */
class	Calculation	extends	BCObject	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( "Calculation", "CalculationId") ;
		$this->addCol( "ImageRefABB", "var") ;
		$this->ImageRefABB	=	"" ;
		FDbg::end() ;
	}
	function	getNextAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		parent::getNextAsXML( $_key, $_id, $_val, $reply) ;
		$this->ImageRefABB	=	$this->CalculationId . "_abb.png" ;
		return parent::getXMLString( $_key, $_id, $_val, $reply) ;
	}
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::add()
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$this->clear() ;
		$this->Number	=	"NEW" ;
		$this->newKey() ;
		$this->getFromPostL() ;
		$this->storeInDb() ;
		$this->reload() ;											// reload
		/**
		 *
		 */
		$myTrailerType	=	new TrailerType() ;
		$myTrailerType->setKey( $this->TrailerTypeId) ;
		if ( $myTrailerType->isValid()) {
			$myTrailerData	=	new TrailerData() ;
			$myTrailerData->CalculationId	=	$this->CalculationId ;
			for ( $il0=1 ; $il0 <= intval( $myTrailerType->AxlesFront) ; $il0++) {
				$myTrailerData->newKey() ;
				$myTrailerData->AxleNo	=	$il0 ;
				$myTrailerData->storeInDb() ;
			}
			for ( $il0=1 ; $il0 <= intval( $myTrailerType->AxlesRear) ; $il0++) {
				$myTrailerData->newKey() ;
				$myTrailerData->AxleNo	=	$myTrailerType->AxlesFront + $il0 ;
				$myTrailerData->storeInDb() ;
			}
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__, "trailer type '".$myTrailerType->TrailerTypeId." is not acceptable") ;
		}
		/**
		 *
		 */
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch ( $_val) {
			default	:
				if ( $_id >= 0) {
					parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
				} else {
					$newItem	=	new $_val ;
					$newItem->bremse_id	=	$this->br_id ;
//					$newItem->Domain	=	$this->__getUser()->Domain ;
					$newItem->getAsXML( $_key="", $_id=-1, $_val="", $reply) ;
				}
				break ;
		}
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Calculation.php", "Calculation", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::addDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Calculation.php", "Calculation", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Calculation.php", "Calculation", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::delDep()
	 */
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Calculation.php", "Calculation", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Calculation.php", "Calculation", "getList( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$filter1	=	"( CalculationId like '%" . $sCrit . "%' OR TrailerManufacturer like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myObj	=	new FDbObject( "CalculationSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( [ "CalculationId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Calculation") ;
			break ;
		case	"TrailerData"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["CalculationId = '".$this->CalculationId."'"]) ;
			$myQuery->addOrder( ["AxleNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"Configuration"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["CalculationId = '".$this->CalculationId."'"]) ;
			$myQuery->addOrder( ["Axle"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;

/**
		FDbg::begin( 1, "Calculation.php", "Calculation", "getList( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		$sortOrder	=	"ORDER BY C.Number DESC " ;
		switch ( $objName) {
			case	""	:
				$_POST['_step']	=	$_val ;
				$filter	=	"" ;
				$_searchCrit	=	"" ;
				$calculationIdCrit	=	"" ;
				$descriptionCrit	=	"" ;
				$manufacturerCrit	=	"" ;
				$brakeCrit	=	"" ;
				$trailerTypeCrit	=	"" ;
				$cylinderSizeCrit	=	"" ;
				$calulationBaseCrit	=	"" ;
				$tyreSizeCrit	=	"" ;
				$dateCrit	=	"" ;
				$weightCrit	=	"" ;
				if ( isset( $_POST['_SSearch']))
					$_searchCrit	=	$_POST['_SSearch'] ;
				if ( isset( $_POST['filterCalculationId']))
					$calculationIdCrit	=	" AND C.CalculationId like '%" . $_POST['filterCalculationId'] . "%' " ;
				if ( isset( $_POST['filterManufacturer']))
					$manufacturerCrit	=	" AND C.TrailerManufacturer like '%" . $_POST['filterManufacturer'] . "%' " ;
				if ( isset( $_POST['filterBrake']))
					$brakeCrit	=	" AND B1.Description like '%" . $_POST['filterBrake'] . "%' " ;
				if ( isset( $_POST['filterWeight']))
					if ( intval( $_POST['filterWeight']) > 1000 )
						$weightCrit	=	" AND TD.Value1 <= " . strval( $_POST['filterWeight']) . " AND " . strval( $_POST['filterWeight']) . " <= TD.Value2 " ;
				if ( isset( $_POST['SortOrder'])) {
					if ( $_POST['SortOrder'] != "") {
						$sortOrder	=	"ORDER BY " . $_POST['SortOrder'] . " " ;
					}
				}
				$filter	.=	"(" ;
				$filter	.=	"( C.CalculationId like '%" . $_searchCrit . "%' OR C.Number like '%" . $_searchCrit . "%' ) " ;
				$filter	.=	")" ;
				$filter	.=	$calculationIdCrit ;
				$filter	.=	$weightCrit ;
				$filter	.=	$manufacturerCrit ;
				$filter	.=	$brakeCrit ;
				$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
				$myObj->addCol( "Id", "var") ;
				$myObj->addCol( "CalculationId", "var") ;
				$myObj->addCol( "Number", "var") ;
				$myObj->addCol( "TrailerManufacturer", "var") ;
				$myObj->addCol( "Value1", "var") ;
				$myObj->addCol( "Value2", "var") ;
				$myObj->addCol( "B1Description", "var") ;
				$myObj->addCol( "B2Description", "var") ;
				$myObj->addCol( "VTOptions", "var") ;
				$reply->replyData	=	$myObj->tableFromDb( ",TD.Value1, TD.Value2, B1.Description AS B1Description, B2.Description as B2Description, TT.Options as TTOptions ",
										"LEFT JOIN TrailerData AS TD ON TD.CalculationId = C.CalculationId AND AxleNo = 0 "
											. "LEFT JOIN Brake AS B1 ON B1.BrakeId = C.Brake1Id "
											. "LEFT JOIN Brake AS B2 ON B2.BrakeId = C.Brake2Id "
											. "LEFT JOIN TrailerType AS TT ON TT.TrailerTypeId = C.TrailerTypeId ",
										$filter,
										$sortOrder,
										"Calculation",
										"Calculation",
										"C.Id, C.CalculationId, C.Number, C.TrailerManufacturer, TD.Value1, TD.Value2 ") ;
				break ;
			case	"Configuration"	:
				$tmpObj	=	new $objName() ;
				$tmpObj->addCol( "Id", "int") ;
				$reply->replyData	=	$tmpObj->tableFromDb( " ", "", "CalculationId = '$this->CalculationId' ") ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
**/
	}
	/**
	 *
	 */
	function	getImageABB( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Calculation.php", "Calculation", "getList( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$reply->replyMediaType	=	Reply::mediaImgPng ;
			// Foto laden
$im = imagecreatefromjpeg( $this->path->image . "thumbs/" . "000/000.0025.jpg");

// wir erstellen ein Wasserzeichen mit GD
$stamp = imagecreatetruecolor(100, 70);
imagefilledrectangle($stamp, 0, 0, 99, 69, 0x0000FF);
imagefilledrectangle($stamp, 9, 9, 90, 60, 0xFFFFFF);
imagestring($stamp, 5, 20, 20, 'libGD', 0x0000FF);
imagestring($stamp, 3, 20, 40, '(c) 2007-9', 0x0000FF);

// R�nder setzen, Dimensionen ermitteln
$marge_right = 10;
$marge_bottom = 10;
$sx = imagesx($stamp);
$sy = imagesy($stamp);

// Wasserzeichen mit einer Transparenz von 50% �ber das Foto legen
imagecopymerge($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp), 50);
$reply->gdImage	=	$im ;
FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getOwnerField() {	return $this->keyCol ;	}
}
?>
