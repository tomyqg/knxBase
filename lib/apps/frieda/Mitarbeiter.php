<?php
/**
 * Mitarbeiter.php - Class definition
 *  Domain:
 *  	- administrative
 * 	Mitarbeiter references:
 * 		- n/a
 *  Mitarbeiter is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package MitarbeiterCalc
 */
/**
 * Mitarbeiter - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package MitarbeiterCalc
 * @subpackage Classes
 */
class	Mitarbeiter	extends	AppObjectCore	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "Mitarbeiter", "Id") ;
//		parent::setIdKey( "br_key") ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getXMLComplete()
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "Mitarbeiter") ;
		return $ret ;
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
		FDbg::begin( 1, "Mitarbeiter.php", "Mitarbeiter", "addDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, "Mitarbeiter.php", "Mitarbeiter", "updDep( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Mitarbeiter.php", "Mitarbeiter", "updDep( '$_key', $_id, '$_val')",
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
		FDbg::begin( 1, "Mitarbeiter.php", "Mitarbeiter", "updDep( '$_key', $_id, '$_val')") ;
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
		FDbg::begin( 1, "Mitarbeiter.php", "Mitarbeiter", "getList( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			$_POST['_step']	=	$_val ;
			/**
			 *
			 */
			$myObj	=	new FDbObject( $this->className) ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	new FSqlSelect( $this->className) ;
			$myQuery->addOrder( ["Id"]) ;
			$myQuery->addWhere( ["Vorname like '%".$sCrit."%'"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"Rolle"	:
			$myObj	=	new FDbObject( "Rolle") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	new FSqlSelect( $this->className) ;
			$myQuery->mssqlSelect	=	"SELECT * FROM nm_RolleMitarbeiter AS MR LEFT JOIN Rolle AS R ON R.Id = MR.RolleId WHERE MR.MitarbeiterId = " . $this->Id . " " ;
			$myQuery->mssqlCount		=	"SELECT COUNT(*) AS Count FROM nm_RolleMitarbeiter AS MR LEFT JOIN Rolle AS R ON R.Id = MR.RolleId WHERE MR.MitarbeiterId = " . $this->Id . " " ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery, "Rolle") ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * Get a new key for the object and stores the object as an empty object in the database.
	 * The object is then reloaded.
	 * @param int $_digits	number of digits for the key
	 * @param string $_nsStart	beginning of the number range within which to fetch the new key
	 * @param string $_nsEnd	end of the number range within which to fetch the new key
	 * @return void
	 */
	function	newKey( $_digits=6, $_nsStart="000000", $_nsEnd="999999", $_store=true) {
		FDbg::begin( 1, "Mitarbeiter.php", "Mitarbeiter", "newKey( $_digits, '$_nsStart', '$_nsEnd')") ;
		$lastObj	=	new Mitarbeiter() ;
		$lastObj->first( "MitarbeiterId DESC") ;
		$this->MitarbeiterId	=	$lastObj->MitarbeiterId ;
		$idParts	=	explode( ".", $lastObj->MitarbeiterId) ;
		if ( count( $idParts) == 2) {
			$newNo	=	sprintf( "%d", intVal( $idParts[1]) + 1) ;
			$this->MitarbeiterId	=	$idParts[0] . "." . str_pad( $newNo, strlen( $idParts[1]), "0", STR_PAD_LEFT) ;
		}
		FDbg::trace( 2, "Mitarbeiter.php", "Mitarbeiter", "newKey( $_digits, '$_nsStart', '$_nsEnd')", "will store") ;
		if ( $_store) {
			$this->storeInDb() ;
			$this->reload() ;		// reload; dependent objects might have changed, e.g. customer/supplier/-contacs
		} else {
			$this->_valid	=	true ;
		}
		FDbg::end() ;
		return $this->MitarbeiterId ;
	}
	/**
	 *
	 */
	function	getOwnerField() {	return $this->keyCol ;	}
}
?>
