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
 * AttrTmplItem - 
 *
 * @package Application
 * @subpackage AttrTmpl
 */
class	AttrTmplItem	extends	FDbObject	{
	function	__construct( $_myAttrTmplNo='') {
		parent::__construct( "AttrTmplItem", "Id") ;
		$this->AttrTmplNo	=	$_myAttrTmplNo ;
	}
	/**
	 *
	 */
	function	getNextItemNo() {
		$query	=	sprintf( "SELECT ItemNo FROM AttrTmplItem WHERE AttrTmplNo='%s' ORDER BY ItemNo DESC LIMIT 0, 1 ", $this->AttrTmplNo) ;
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
