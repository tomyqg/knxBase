<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * TilgPlan - Base Class
 *
 * @package Application
 * @subpackage TilgPlan
 */
class	TilgPlan	extends	AppObject_R2	{
	/**
	 *
	 */
	function	__construct( $_myTilgPlanNo="") {
		parent::__construct( "TilgPlan", "TilgPlanNo") ;
		$this->Rights	=	0x00000001 ;
		if ( strlen( $_myTilgPlanNo) > 0) {
			try {
				$this->setTilgPlanNo( $_myTilgPlanNo) ;
				$this->actTilgPlanContact	=	new TilgPlanContact() ;
				$this->Opening	=	"Hallo" ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
	}
	/**
	 *
	 */
	function	setTilgPlanNo( $_myTilgPlanNo) {
		$this->TilgPlanNo	=	$_myTilgPlanNo ;
		$this->reload() ;
		return $this->_valid ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TilgPlan.php", "TilgPlan", "add( '$_key', $_id, '$_val')") ;
		$myKey	=	$this->newKey( 6, "000000", "899999") ;
		if ( $this->_valid) {
			$this->getFromPostL() ;
			$this->TilgPlanNo	=	$myKey ;
			$this->Tax	=	1 ;
			$this->Remark	=	"" ;
			$this->updateInDb() ;
		} else {
			$e	=	new Exception( "TilgPlan.php::TilgPlan::add(): 'TilgPlan' invalid after creation!") ;
			error_log( $e) ;
			throw $e ;
		}
		FDbg::end( 1, "TilgPlan.php", "TilgPlan", "add( '$_key', $_id, '$_val')") ;
		return $this->getAsXML() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TilgPlan.php", "TilgPlan", "upd( '$_key', $_id, '$_val')") ;
		$this->_upd() ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	function	_upd( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TilgPlan.php", "TilgPlan", "_upd()") ;
		$this->getFromPostL() ;
		$this->updateInDb() ;
//		$this->_addRem( FTr::tr( "TilgPlan updated")) ;
		FDbg::end() ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "TilgPlan.php::TilgPlan::del(...)") ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		return $this->getAsXML() ;
	}
	/**
	 * 
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "TilgPlan.php", "TilgPlan", "addDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "TilgPlanContact") {
			$myTilgPlanContact	=	new TilgPlanContact() ;
			$myTilgPlanContact->TilgPlanNo	=	$this->TilgPlanNo ;
			$myTilgPlanContact->newTilgPlanContact() ;
			$myTilgPlanContact->getFromPostL() ;
			$myTilgPlanContact->updateInDb() ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, $_val, $reply) ;
		} else if ( $objName == "LiefTilgPlan") {
			$this->_addDepTilgPlan( "L") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "LiefTilgPlan", $reply) ;
		} else if ( $objName == "RechTilgPlan") {
			$this->_addDepTilgPlan( "R") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "RechTilgPlan", $reply) ;
		} else if ( $objName == "AddTilgPlan") {
			$this->_addDepTilgPlan( "A") ;
			$ret	=	$this->getTableDepAsXML( $_key, $_id, "AddTilgPlan", $reply) ;
		}
		FDbg::end( 1, "TilgPlan.php", "TilgPlan", "addDep( '$_key', $_id, '$_val')") ;
		return $ret ;
	}
	/**
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	_addDep( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		if ( $objName == "TilgPlanContact") {
			$myTilgPlanContact	=	new TilgPlanContact() ;
			$myTilgPlanContact->TilgPlanNo	=	$this->TilgPlanNo ;
			$myTilgPlanContact->newTilgPlanContact() ;
			$myTilgPlanContact->getFromPostL() ;
			$myTilgPlanContact->updateInDb() ;
			return $myTilgPlanContact->TilgPlanContactNo ;
		} else if ( $objName == "LiefTilgPlan") {
			return $this->_addDepTilgPlan( "L") ;
		} else if ( $objName == "RechTilgPlan") {
			return $this->_addDepTilgPlan( "R") ;
		} else if ( $objName == "AddTilgPlan") {
			return $this->_addDepTilgPlan( "A") ;
		}
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TilgPlan.php", "TilgPlan", "updDep( '$_key', $_id, '$_val')") ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "TilgPlan.php", "TilgPlan", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		case	"LiefTilgPlan"	:
			$myLiefTilgPlan	=	new TilgPlan() ;
			$myLiefTilgPlan->setId( $_id) ;
			$myLiefTilgPlan->_upd() ;
			return $this->getDepAsXML( $_key, $_id, $_val) ;
			break ;
		case	"RechTilgPlan"	:
			$myRechTilgPlan	=	new TilgPlan() ;
			$myRechTilgPlan->setId( $_id) ;
			$myRechTilgPlan->_upd() ;
			return $this->getDepAsXML( $_key, $_id, $_val) ;
			break ;
		case	"AddTilgPlan"	:
			$myAddTilgPlan	=	new TilgPlan() ;
			$myAddTilgPlan->setId( $_id) ;
			$myAddTilgPlan->_upd() ;
			return $this->getDepAsXML( $_key, $_id, $_val) ;
			break ;
		default	:
			return parent::updDep( $_key, $_id, $_val) ;
			break ;
		}
		FDbg::end() ;
		return $this->getTableDepAsXML( $_key, $_id, $objName) ;
	}
	function	delDep( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "TilgPlan.php", "TilgPlan", "delDep( '$_key', $_id, '$_val')") ;
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
	function	getAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "TilgPlan.php", "TilgPlan", "getAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		else {
			$reply->instClass	=	__class__ ;
			$reply->replyingClass	=	$this->className ;
		}
		$reply->replyData	.=	$this->getXMLF() ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
			case	""	:	
				$_kundeNrCrit	=	$sCrit ;
//				$_kundeNrCrit	=	$_POST['_SCustNo'] ;
// 				$_firmaCrit	=	$_POST['_SCompany'] ;
// 				$_nameCrit	=	$_POST['_SName'] ;
// 				$_phoneCrit	=	$_POST['_SPhone'] ;
// 				$_eMail	=	$_POST['_SeMail'] ;
 				$_POST['_step']	=	$_val ;
				$filter	=	"( C.TilgPlanName1 like '%" . $_kundeNrCrit . "%' OR C.TilgPlanName2  like '%" . $_kundeNrCrit . "%') " ;
//				$filter	=	"( C.TilgPlanNo like '%" . $_kundeNrCrit . "%' ) " ;
// 				$filter	.=	"  AND ( C.TilgPlanName1 like '%" . $_firmaCrit . "%' OR C.TilgPlanName2 LIKE '%" . $_firmaCrit . "%') " ;
// 				if ( $_POST['_SName'] != "")
// 					$filter	.=	"  AND ( KK.FirstName like '%" . $_POST['_SName'] . "%' OR KK.Name like '%" . $_POST['_SName'] . "%' ) " ;
// 				if ( $_POST['_SZIP'] != "")
// 					$filter	.=	"  AND ( C.ZIP like '%" . $_POST['_SZIP'] . "%' ) " ;
// 				$filter	.=	"AND ( C.eMail LIKE '%" . $_eMail . "%') " ;
				$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
				$myObj->addCol( "Id", "int") ;
				$myObj->addCol( "TilgPlanNo", "var") ;
				$myObj->addCol( "ZIP", "var") ;
				$myObj->addCol( "Name", "var") ;
				$myObj->addCol( "Contact", "var") ;
				$myObj->addCol( "eMail", "var") ;
				$reply->replyData	=	$myObj->tableFromDb( ", CONCAT ( C.TilgPlanName1, \", \", C.TilgPlanName2) AS Name, CONCAT( CC.LastName, \", \", CC.FirstName) AS Contact ",
										"LEFT JOIN TilgPlanContact AS CC ON CC.TilgPlanNo = C.TilgPlanNo ",
										$filter,
										"ORDER BY C.TilgPlanNo ASC ",
										"TilgPlan",
										"TilgPlan",
										"C.Id, C.TilgPlanNo, C.ZIP, C.eMail ") ;
				break ;
			case	"TilgPlanMonat"	:
				$reply->replyData	=	$this->getTilgPlan() ;
				break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
	function	getTilgPlan()  {
		$ret	=	"" ;
		$ret	.=	"<TableTilgPlanMonat>\n" ;
		$ret	.=	"<TableInfo>\n" ;
		$ret	.=	"<StartRow>0</StartRow>\n" ;
		$ret	.=	"<RowCount>10000</RowCount>\n" ;
		$ret	.=	"<PageCount>0</PageCount>\n" ;
		$ret	.=	"<TotalRows>10000</TotalRows>\n" ;
		$ret	.=	"</TableInfo>\n" ;
		/**
		 * 
		 */
		$il0	=	1 ;
		$restSchuld	=	$this->KreditBetrag ;
		$rate	=	$this->RateMonatlich ;
		$zinsSatz	=	$this->Zinssatz / 100.0 ;	// $C
		$zinsFaktor	=	$zinsSatz + 1.0 ;
		while ( $restSchuld > 0.0 && $il0 < 150) {
//			$zinsen	=	$restSchuld * ( ( pow ( $zinsFaktor, $this->LaufzeitMonate ) * $zinsSatz / 12 ) / ( pow ( $zinsFaktor, $this->LaufzeitMonate ) -  1 ) ) ;
			$zinsen	=	$restSchuld * $zinsSatz / 12.0 ;
			if ( $rate > $restSchuld + $zinsen) {
				$rate	=	$restSchuld + $zinsen ;
			}
			$tilgung	=	$rate - $zinsen ;
			$ret	.=	"<TilgPlanMonat>\n" ;
			$ret	.=	"<Id type=\"int\" title=\"Id\">".$il0."</Id>\n" ;
			$ret	.=	"<TilgPlanNo type=\"char\" title=\"TilgPlanNo\">".$this->TilgPlanNo."</TilgPlanNo>\n" ;
			$ret	.=	"<Monat type=\"int\" title=\"Monat\">".$il0."</Monat>\n" ;
			$ret	.=	"<Restschuld type=\"double\" title=\"Restschuld\">".number_format($restSchuld,2)."</Restschuld>\n" ;
			$ret	.=	"<Zinsen type=\"double\" title=\"Zinsen\">".number_format($zinsen,2)."</Zinsen>\n" ;
			$ret	.=	"<Rate type=\"double\" title=\"Rate\">".number_format($rate,2)."</Rate>\n" ;
			$ret	.=	"<Zinsen type=\"double\" title=\"Zinsen\">".number_format($zinsen,2)."</Zinsen>\n" ;
			$ret	.=	"<Tilgung type=\"double\" title=\"Tilgung\">".number_format($tilgung,2)."</Tilgung>\n" ;
			$restSchuld	-=	$tilgung ;
			$ret	.=	"<RestschuldPE type=\"double\" title=\"RestschuldPE\">".number_format($restSchuld,2)."</RestschuldPE>\n" ;
			$ret	.=	"</TilgPlanMonat>\n" ;
			$il0++ ;
		}
		$ret	.=	"</TableTilgPlanMonat>\n" ;
		return $ret ;
	}
}
?>
