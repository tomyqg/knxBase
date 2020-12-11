<?php

/**
 * JournalTmpl.php - Basis Anwendungsklasse fuer Kundenbestellung (JournalTmpl)
 *
 *	Definiert die Klassen:
 *		JournalTmpl
 *		JournalTmplItem
 *
 * Einige Parameter haben eine extrem wichtige Bedeutung fuer das Gesamtsystem, d.h. von der
 * Bestellung ueber die Kommissionierung und den Lieferschein bis hin zur Rechnung.
 *
 * Journalibut:	PosType
 *
 * Dieses Journalibut beschreibt wie sich eine Position in der Bestellung in den verschiedenen Phasen
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
 * JournalTmpl - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BJournalTmpl which should
 * not be modified.
 *
 * @package Application
 * @subpackage Journalibute
 */
class	JournalTmpl	extends	AppObject	{
	/**
	 *
	 */
	function	__construct( $_myJournalTmplNo='') {
		parent::__construct( "JournalTmpl", "JournalTmplNo") ;
		if ( strlen( $_myJournalTmplNo) > 0) {
			$this->setJournalTmplNo( $_myJournalTmplNo) ;
		} else {
			error_log( "JournalTmpl.php::JournalTmpl::__construct(...): JournalTmplNo not specified !") ;
		}
	}
	function	setJournalTmplNo( $_myJournalTmplNo) {
		$this->JournalTmplNo	=	$_myJournalTmplNo ;
		if ( strlen( $_myJournalTmplNo) > 0) {
			$this->reload() ;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see DbObject::add()
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		$this->getFromPostL() ;
		$this->JournalTmplNo	=	$_POST[ "_IJournalTmplNo"] ;
		$this->storeInDb() ;
		return $this->getXMLString() ;
	}
	/**
	 * 
	 */
	function	addDep( $_key, $_id, $_val) {
		$this->_addDep( $_key, $_id, $_val) ;
		return $this->getXMLComplete() ;
	}
	/**
	 * nethods: retrieval
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, $_val="JournalTmplItem") ;
		return $ret ;
	}
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}
	/**
	 * methods: object retrieval
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$tmpObj	=	new $objName() ;
		$tmpObj->addCol( "DeAcc", "var") ;
		$tmpObj->addCol( "CrAcc", "var") ;
		$tmpObj->addCol( "CAD", "var") ;
		$tmpObj->addCol( "CAC", "var") ;
		$ordBy	=	"ORDER BY C.ItemNo " ;
		$ret	=	$tmpObj->tableFromDb( ", C.AmountDebit AS CAD, C.AmountCredit AS CAC, De.Description1 AS DeAcc, Cr.Description1 AS CrAcc ",
								"LEFT JOIN Account AS De ON De.AccountNo = C.AccountDebit AND De.SubAccountNo = '' "
								. "LEFT JOIN Account AS Cr ON Cr.AccountNo = C.AccountCredit AND Cr.SubAccountNo = '' ",
								"C." . $myKeyCol . " = '" . $myKey . "' ",
								"ORDER BY C.ItemNo ",
								"") ;
		return $ret ;
	}
	/**
	 * methods: internal
	 */
	function	_addDep( $_key, $_id, $_val) {
		try {
			$newJournalTmplPos	=	new JournalTmplItem() ;
			$newJournalTmplPos->JournalTmplNo	=	$this->JournalTmplNo ;
			$newJournalTmplPos->_getNextItemNo() ;
			$newJournalTmplPos->getFromPostL( ",ItemNo,") ;
			$newJournalTmplPos->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
	}
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="") {
		$_journalTmplNoCrit	=	$_POST['_SJournalTmplNo'] ;
		$_descriptionCrit	=	$_POST['_SDescription'] ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( C.JournalTmplNo like '%" . $_journalTmplNoCrit . "%' ) " ;
		$filter	.=	"  AND ( C.Description like '%" . $_descriptionCrit . "%') " ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( "JournalTmplNo", "var") ;
		$myObj->addCol( "Description", "var") ;
		$ret	=	$myObj->tableFromDb( " ",
								" ",
								$filter,
								"ORDER BY C.JournalTmplNo ASC ",
								"JournalTmpl",
								"JournalTmpl",
								"C.Id, C.JournalTmplNo, C.Description ") ;
//		error_log( $ret) ;
		return $ret ;
	}
}
/**
 * JournalTmplItem - 
 *
 * @package Application
 * @subpackage JournalTmpl
 */
class	JournalTmplItem	extends	FDbObject	{
	function	__construct( $_myJournalTmplNo='') {
		parent::__construct( "JournalTmplItem", "Id") ;
		$this->JournalTmplNo	=	$_myJournalTmplNo ;
	}
	/**
	 *
	 */
	function	_getNextItemNo() {
		$query	=	sprintf( "SELECT ItemNo FROM JournalTmplItem WHERE JournalTmplNo='%s' ORDER BY ItemNo DESC LIMIT 0, 1 ", $this->JournalTmplNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->ItemNo	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
}
?>
