<?php
/**
 * Mitarbeiter.php - Base class for Mitarbeiter
 *
 * This is the base class of Mitarbeiters.
 *
 * Special considerations:
 *
 * Mitarbeiters can be of the following fundamental types:
 *
 * Type			charateristics
 * Simple		only a single piece of something		:Comp = 0
 * 				e.g. Microscope
 * Composite-1	contains multiple items					:Comp = 1
 *				only sub-items are reservered in stock
 *				e.g. disection set
 * Composite-2	contains multiple items on purchasing	:Comp = 2
 * 				which need to be ordered from SAME
 * 				supplier on SAME order
 * 				e.g. rubber stopper with whole
 * Composite-3	contains multiple items					:Comp = 3
 * 				only main item is reserved in stock
 * 				e.g. PASCO experiment
 *
 * Valid combination of parameters nd article examples:
 * :Comp	Example
 *   0		microscope
 *  10		disection set
 *  11		trolley with different color trays
 *  20		rubber stopper with whole
 *  30		PASCO experiment
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-05-13	PA1		khw		added to rev. control
 * 2013-05-17
 * 2013-05-18	PA2		khw		added 'QuantityText' to getList and getSPList
 * 								method;
 *
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */

/**
 * erforder den WTF debugger und die datenbank-nahe Klasse BMitarbeiter
 */
//require_once( "Document.php") ;

/**
 * Mitarbeiter - User-Level Klasse
 *
 * This class acts as an interface towards the automatically generated BMitarbeiter which should
 * not be modified.
 *
 *
 * @package Application
 * @subpackage Mitarbeiter
 */

class	AktivitaetenMitarbeiter	extends	AppObject_R2
{
	/**
	 * Erzeugung eines Objektes.
	 *
	 * Erzeugt ein Mitarbeiter-Objekt und versucht ggf. diesen Mitarbeiter aus der Db zu laden.
	 *
	 * @param string $_mitarbeiterNummer='' Mitarbeiternummer
	 * @return void
	 */
	function	__construct( $_id='') {
		parent::__construct( "AktivitaetenMitarbeiter", "LfdNr") ;
		$this->setIdKey( "UniqueID") ;
		if ( strlen( $_id) > 0) {
			$this->setId( $_id) ;
		} else {
		}
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "AktivitaetenMitarbeiter.php", "AktivitaetenMitarbeiter", "add( '$_key', $_id, '$_val')") ;
		try {
		} catch ( FException $e) {
		}
		FDbg::end() ;
		return $ret ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	upd( $_key, $_id, $_val) {
		FDbg::begin( 1, "AktivitaetenMitarbeiter.php", "MiAktivitaetenMitarbeiterarbeiter", "add( '$_key', $_id, '$_val')") ;
		try {
		} catch ( FException $e) {
		}
		FDbg::end() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, "AktivitaetenMitarbeiter.php", "AktivitaetenMitarbeiter", "add( '$_key', $_id, '$_val')") ;
		try {
		} catch ( FException $e) {
		}
		FDbg::end() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Customer.php", "Customer", "getDepAsXML( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"Attribute"	:
			$myAttribute	=	new Attribute() ;
			if ( $_id == -1) {
				$myAttribute->DataTable	=	"Mitarbeiter" ;
				$myAttribute->RefNr	=	$this->MitarbeiterNummer ;
				$myAttribute->PosNr	=	10 ;
			} else {
				$myAttribute->setId( $_id) ;
			}
			return $myAttribute->getXMLString() ;
			break ;
		case	"ArtQPC"	:
			$myArtQPC	=	new ArtQPC() ;
			if ( $_id == -1) {
				$myArtQPC->MitarbeiterNummer	=	$this->MitarbeiterNummer ;
				$myArtQPC->QPC	=	1 ;
			} else {
				$myArtQPC->setId( $_id) ;
			}
			return $myArtQPC->getXMLString() ;
			break ;
		default	:
			$reply	=	parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
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
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter	=	"( Mitarbeiternummer like '%" . $sCrit . "%' OR Name like '%" . $sCrit . "%' OR Vorname like '%" . $sCrit . "%') " ;
			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addWhere( [ "Mandant = '1'", $filter]) ;
			$myQuery->addOrder( "Mandant", "Mitarbeiternummer") ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"Aktivitaeten"	:
			$myObj	=	new FDbObject( "Aktivitaeten") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter1	=	"LfdNr = '" . $this->LfdNr . "' " ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( [ "LfdNr"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
//		error_log( $ret) ;
		return $reply ;
	}
}
?>
