<?php

/**
 * KundeBeleg.php - Basis Anwendungsklasse fuer Customernbestellung (KundeBeleg)
 *
 *	Definiert die Klassen:
 *		KundeBeleg
 *		KundeBelegPosition
 *
 * Einige Parameter haben eine extrem wichtige Bedeutung fuer das Totalsystem, d.h. von der
 * Bestellung ueber die Kommissionierung und den Lieferschein bis hin zur Beleg.
 *
 * Attribut:	PosType
 *
 * Dieses Attribut beschreibt wie sich eine Position in der Bestellung in den verschiedenen Phasen
 * verhaelt.
 * Bei der Erzeugung von Kommisison, Lieferung und Beleg werden grundsaetzlich alle Positionen
 * uebernommen deren Menge in dem entsprechenden Papier > 0 ist (Kommission: Menge noch zu liefern; Lieferschein: jetzt
 * geliefert; Beleg: berechnete Menge). Die EN
 * Eine "NORMALe" Position wird im Lager reserviert (falls der Article an sich reserviert werden muss), wird
 * kommissioniert, geliefert und ebenfalls berechnet.
 * Eine "LieFeRuNG" Position wird im Lager reserviert (s.o.). Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp gelistet. Auf der Beleg wird dieser Positionstyp NICHT gelistet.
 * Eine "ReCHNunG" Position wird im Lager NICHT reservert. Auf dem Kommissionierschein und dem Lieferschein
 * wird dieser Positionstyp nicht gelistet. Auf der Beleg wird dieser Typ gelistet.
 * Eine "KOMPonenten" Position wird im Lager reserviert, auf dem
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @package Application
 * @version 0.1
 * @filesource
 */
/**
 * KundeBelegPosition -
 *
 * @package Application
 * @subpackage KundeBeleg
 */
class	KundeBelegPosition	extends	AppObject	{
	const   IT_NORMAL       =                0 ;    // normal item
	const   IT_INVONLY      =                1 ;
	const   IT_DELIONLY	=                2 ;
	const	IT_KOMP		=		 3 ;			// Komponente
	const	IT_OPTION	=		 4 ;			// Komponente
	const	IT_HDLG		=		 9 ;			// Handlingpauschale
	const	IT_SHIP		=		10 ;		// Versandkosten (Versand und Versicherung)
	/**
	 *
	 * @var unknown_type
	 */
	public	$myArticle ;
	public	$myCond ;
	/**
	 *
	 * @var unknown_type
	 */
	const	NORMAL		=	0 ;			// normale Position
	const	LFRNG		=	1 ;			// wird geliefert aber nicht berechnet
	const	RCHNG		=	2 ;			// wird berechnet aber nicht geliefert
	const	KOMP		=	3 ;			// Komponente
	const	OPTION		=	4 ;			// Komponente
	const	_LASTNORM	=	8 ;			// Letzer "normaler" Posten Typ
	const	HDLGPSCH	=	9 ;			// Handlingpauschale
	const	VRSND		=	10 ;		// Versandkosten (Versand und Versicherung)
	private	static	$rPosType	=	array (
						KundeBelegPosition::NORMAL	=> "Normal",
						KundeBelegPosition::LFRNG	=> "Liefern",
						KundeBelegPosition::RCHNG	=> "Berechnen",
						KundeBelegPosition::KOMP		=> "Komponente",
						KundeBelegPosition::HDLGPSCH	=> "Handling",
						KundeBelegPosition::VRSND	=> "Versand"
					) ;
	/**
	 *
	 */
	function	__construct( $_cuOrdrNo="") {
		parent::__construct( "KundeBelegPosition", "Id") ;
		$this->BelegNr	=	$_cuOrdrNo ;
	}
	/**
	 *
	 * @return void
	 */
	function	getNextItemNo() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addWhere( "BelegNr='" . $this->BelegNr . "'") ;
		$myQuery->addOrder( ["PosNr DESC"]) ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		try {
			$row	=	FDb::queryRow( $myQuery) ;
			if ( $row) {
				$this->ItemNo	=	sprintf( "%d", intval( $row['ItemNo']) + 10) ;
			} else {
				$this->ItemNo	=	"10" ;
			}
		} catch ( Exception $e) {
			FDbg::abort() ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->_status ;
	}
	/**
	 *
	 */
	function	getRPosType() {	return self::$rPosType ;	}
}

?>
