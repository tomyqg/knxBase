<?php

/**
 * CustomerOrder.php - Basis Anwendungsklasse fuer Customernbestellung (CustomerOrder)
 *
 *	Definiert die Klassen:
 *		CustomerOrder
 *		KundeAdresse
 *
 * Einige Parameter haben eine extrem wichtige Bedeutung fuer das Totalsystem, d.h. von der
 * Bestellung ueber die Kommissionierung und den Lieferschein bis hin zur Rechnung.
 *
 * Attribut:	PosType
 *
 * Dieses Attribut beschreibt wie sich eine Position in der Bestellung in den verschiedenen Phasen
 * verhaelt.
 * Bei der Erzeugung von Kommisison, Lieferung und Rechnung werden grundsaetzlich alle Positionen
 * uebernommen deren Menge in dem entsprechenden Papier > 0 ist (Kommission: Menge noch zu liefern; Lieferschein: jetzt
 * geliefert; Rechnung: berechnete Menge). Die EN
 * Eine "NORMALe" Position wird im Lager reserviert (falls der Article an sich reserviert werden muss), wird
 * kommissioniert, geliefert und ebenfalls berechnet.
 * Eine "LieFeRuNG" Position wird im Lager reserviert (s.o.). Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp gelistet. Auf der Rechnung wird dieser Positionstyp NICHT gelistet.
 * Eine "ReCHNunG" Position wird im Lager NICHT reservert. Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp nicht gelistet. Auf der Rechnung wird dieser Typ gelistet.
 * Eine "KOMPonenten" Position wird im Lager reserviert, auf dem
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @package Application
 * @version 0.1
 * @filesource
 */

/**
 * KundeAdresse -
 *
 * @package Application
 * @subpackage CustomerOrder
 */
class	KundeAdresse	extends	AppObject	{

	/**
	 *
	 */
	function	__construct() {
		parent::__construct( "KundeAdresse", "Id") ;
	}

	/**
	 *
	 * @return void
	 */
	function	getNextItemNo() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addWhere( "KundeNr='" . $this->KundeNr . "'") ;
		$myQuery->addOrder( ["KundeAdresseNr DESC"]) ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		try {
			$row	=	FDb::queryRow( $myQuery) ;
			if ( $row) {
				$this->PosNr	=	sprintf( "%d", intval( $row['PosNr']) + 10) ;
			} else {
				$this->PosNr	=	"10" ;
			}
		} catch ( Exception $e) {
			FDbg::abort() ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->_status ;
	}
}

?>
