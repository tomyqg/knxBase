<?php

/**
 * AttrTmpl.php - Basis Anwendungsklasse fuer Kundenbestellung (AttrTmpl)
 *
 *	Definiert die Klassen:
 *		AttrTmpl
 *		AttrTmplItem
 *
 * Einige Parameter haben eine extrem wichtige Bedeutung fuer das Gesamtsystem, d.h. von der
 * Bestellung ueber die Kommissionierung und den Lieferschein bis hin zur Rechnung.
 *
 * Attribut:	PosType
 *
 * Dieses Attribut beschreibt wie sich eine Position in der Bestellung in den verschiedenen Phasen
 * verhaelt.
 * Bei der Erzeugung von Kommisison, Lieferung und Rechnung werden grundsaetzlich alle Positionen
 * uebernommen deren Menge in dem entsprechenden Papier > 0 ist (Kommission: Menge noch zu liefern; Lieferschein: jetzt
 * geliefert; Rechnung: berechnete Menge). Die EN
 * Eine "NORMALe" Position wird im Lager reserviert (falls der Artikel an sich reserviert werden muss), wird
 * kommissioniert, geliefert und ebenfalls berechnet.
 * Eine "LieFeRuNG" Position wird im Lager reserviert (s.o.). Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp gelistet. Auf der Rechnung wird dieser Positionstyp NICHT gelistet.
 * Eine "ReCHNunG" Position wird im Lager NICHT reservert. Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp nicht gelistet. Auf der Rechnung wird dieser Typ gelistet.
 * Eine "KOMPonenten" Position wird im Lager reserviert, auf dem
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * AttrTmpl - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BAttrTmpl which should
 * not be modified.
 *
 * @package Application
 * @subpackage Attribute
 */
class	AttrTmpl	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myAttrTmplNo='') {
		parent::__construct( "AttrTmpl", "AttrTmplNo") ;
		if ( strlen( $_myAttrTmplNo) > 0) {
			$this->setAttrTmplNo( $_myAttrTmplNo) ;
		} else {
			error_log( "AttrTmpl.php::AttrTmpl::__construct(...): AttrTmplNo not specified !") ;
		}
	}
	function	setAttrTmplNo( $_myAttrTmplNo) {
		$this->AttrTmplNo	=	$_myAttrTmplNo ;
		if ( strlen( $_myAttrTmplNo) > 0) {
			$this->reload() ;
		}
	}
	/**
	 *
	 */
	function	addDep( $_key, $_id, $_val) {
		$this->_addDep( $_key, $_id, $_val) ;
		return $this->getXMLComplete() ;
	}
	function	getList( $_key="", $_id=-1, $_val="") {
		$attrTmplNoCrit	=	$_POST['_SAttrTmplNo'] ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( " ;
		$filter	.=	"C.AttrTmplNo like '%" . $attrTmplNoCrit . "%' " ;
		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "AttrTmplNo", "var") ;
		$myObj->addCol( "Keywords", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.AttrTmplNo ASC ",
								"AttrTmpl",
								"AttrTmpl",
								"C.Id, C.AttrTmplNo, C.Keywords ") ;
//		error_log( $ret) ;
		return $ret ;
	}
	/**
	 * nethods: retrieval
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, $_val="AttrTmplItem") ;
		return $ret ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 * methods: internal
	 */
	function	_addDep( $_key, $_id, $_val) {
		try {
			$newAttrTmplItem	=	new AttrTmplItem() ;
			$newAttrTmplItem->AttrTmplNo	=	$this->AttrTmplNo ;
			$newAttrTmplItem->getNextItemNo() ;
			$newAttrTmplItem->getFromPostL( ",ItemNo,") ;
			$newAttrTmplItem->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
	}
}
?>
