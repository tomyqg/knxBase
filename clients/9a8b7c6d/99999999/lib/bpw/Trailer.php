<?php
/**
 * Trailer.php
 * ===============
 *
 *  Domain:
 *  	- administrative
 * 	Trailer references:
 * 		- n/a
 *  Trailer is referenced by:
 *  	- TrailerConfig
 *  	- Trailer (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package bpwBrakeCalculator
 */
/**
 * Trailer - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package TrailerCalc
 * @subpackage Classes
 */
class	Trailer	extends	BCObject	{
	/*
	 * @param string $_artikelNr
	 */
	function	__construct() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		parent::__construct( "Trailer", "TrailerId") ;
		$this->addCol( "ImageRefABB", "var") ;
		$this->ImageRefABB	=	"" ;
		FDbg::end() ;
	}
	function	getNextAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		parent::getNextAsXML( $_key, $_id, $_val, $reply) ;
		$this->ImageRefABB	=	$this->TrailerId . "_abb.png" ;
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
		$myTrailerConfig	=	new TrailerConfig() ;
		$myTrailerConfig->setKey( $this->TrailerConfigId) ;
		$ac	=	1 ;
		if ( $myTrailerConfig->isValid()) {
			$myTrailerAxle	=	new TrailerAxle() ;
			$myTrailerAxle->TrailerId	=	$this->TrailerId ;
			$myTrailerAxle->AxleGroupNo	=	0 ;
			for ( $il0=1 ; $il0 <= intval( $myTrailerConfig->AxlesFront) ; $il0++) {
				$myTrailerAxle->newKey() ;
				$myTrailerAxle->AxleNo	=	$ac++ ;
				$myTrailerAxle->storeInDb() ;
			}
			$myTrailerAxle->AxleGroupNo	=	1 ;
			for ( $il0=1 ; $il0 <= intval( $myTrailerConfig->AxlesCenter) ; $il0++) {
				$myTrailerAxle->newKey() ;
				$myTrailerAxle->AxleNo	=	$ac++ ;
				$myTrailerAxle->storeInDb() ;
			}
			$myTrailerAxle->AxleGroupNo	=	2 ;
			for ( $il0=1 ; $il0 <= intval( $myTrailerConfig->AxlesRear) ; $il0++) {
				$myTrailerAxle->newKey() ;
				$myTrailerAxle->AxleNo	=	$ac++ ;
				$myTrailerAxle->storeInDb() ;
			}
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__, "trailer type '".$myTrailerConfig->TrailerConfigId." is not acceptable") ;
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
	 * @see FDbObject::add()
	 */
	function	upd( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$this->getFromPostL() ;
		parent::upd( $_key, $_id, $_val, $reply) ;
		/**
		 *
		 */
		$myTrailerConfig	=	new TrailerConfig() ;
		$myTrailerConfig->setKey( $this->TrailerConfigId) ;
		if ( $myTrailerConfig->isValid()) {
			switch ( $myTrailerConfig->TrailerTypeNo) {
			case 175	:					// Semi-Trailer (Sattelanhänger)
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "Semi-Trailer discovered") ;
				break;
			default	:						// invalid type
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "invalid trailer type id (id='".$this->TrailerConfigId."'") ;
				break;
			}
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "TrailerConfig not valid!") ;
		}
		/**
		 *
		 */
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
		FDbg::begin( 1, "Trailer.php", "Trailer", "addDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Trailer.php", "Trailer", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		case "TrailerAxle"	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
//			$myTrailerAxle	=	new TrailerAxle() ;
//			$myTrailerAxle->setIterCond( "TrailerId = '".$this->TrailerId."' ") ;
//			$this->WeightMinTotal	=	0 ;
//			$this->WeightMaxTotal	=	0 ;
//			foreach ( $myTrailerAxle as $axle) {
//				$this->WeightMinTotal	+=	$axle->WeightMin ;
//				$this->WeightMaxTotal	+=	$axle->WeightMax ;
//			}
//			$this->updateInDb() ;
			break ;
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
		FDbg::begin( 1, "Trailer.php", "Trailer", "updDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$filter1	=	"( TrailerId like '%" . $sCrit . "%' ) " ;
			/**
			 *
			 */
			$myObj	=	new FDbObject( "TrailerSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( [ "TrailerId"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Trailer") ;
			break ;
		case	"TrailerAxle"	:
			$myObj	=	new $objName() ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( ["TrailerId = '".$this->TrailerId."'"]) ;
			$myQuery->addOrder( [ "AxleGroupNo", "AxleNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;

	}
	/**
	 * (non-PHPdoc)
	 * @see FDbObject::add()
	 */
	function	calculate( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 * add some variable which we need throughout this calculation for communication with the frontend
		 */
		$this->addCol( "rearWeightEmpty", "float") ;
		$this->addCol( "rearWeightLoaded", "float") ;
		/**
		 *
		 */
		$this->TrailerConfig	=	new TrailerConfig() ;
		$this->TrailerConfig->setKey( $this->TrailerConfigId) ;
		if ( $this->TrailerConfig->isValid()) {
			switch ( $this->TrailerConfig->TrailerTypeNo) {
			case 175	:					// Semi-Trailer (Sattelanhänger)
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "Semi-Trailer discovered") ;
				$myFormula	=	new FormulaECESemiTrailer( $this) ;
				error_log( "rearWeightEmpty   := " . $this->rearWeightEmpty) ;
		        error_log( "rearWeightLoaded  := " . $this->rearWeightLoaded) ;
		        error_log( "TotalWeightEmpty  := " . $this->TotalWeightEmpty) ;
		        error_log( "TotalWeightLoaded := " . $this->TotalWeightLoaded) ;
				break;
			default	:						// invalid type
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "invalid trailer type id (id='".$this->TrailerConfigId."'") ;
				break;
			}
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "TrailerConfig not valid!") ;
		}
		$this->getXMLString( $_key, $_id, $_val, $reply) ;
		/**
		 *
		 */
		FDbg::end() ;
		return $reply ;
	}
}
?>
