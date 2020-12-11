<?php

/**
 * CustomerTempOrder.php - Basis Anwendungsklasse fuer Customernbestellung (CustomerTempOrder)
 *
 *	Definiert die Klassen:
 *		CustomerTempOrder
 *		CustomerTempOrderItem
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
 * CustomerTempOrderItem -
 *
 * @package Application
 * @subpackage CustomerTempOrder
 */
class	CustomerTempOrderItem	extends	AppObjectERM	{
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
						CustomerTempOrderItem::NORMAL	=> "Normal",
						CustomerTempOrderItem::LFRNG		=> "Liefern",
						CustomerTempOrderItem::RCHNG		=> "Berechnen",
						CustomerTempOrderItem::KOMP		=> "Komponente",
						CustomerTempOrderItem::HDLGPSCH	=> "Handling",
						CustomerTempOrderItem::VRSND		=> "Versand"
					) ;
	/**
	 *
	 */
	function	__construct( $_cuOrdrNo="") {
		parent::__construct( "CustomerTempOrderItem", "Id") ;
		$this->CustomerOrderNo	=	$_cuOrdrNo ;
	}
	/**
	 *
	 * @return void
	 */
	function	_buche( $_sign) {
		$actArticle	=	new Article( $this->ArticleNr) ;
		if ( $_sign == -1) {
			$menge	=	$this->QuantityReserved * $_sign ;
		} else {
			$menge	=	(( $this->Menge  - $this->QuantityDelivered) * $this->QuantityPerPU) - $this->QuantityReserved ;
		}
		try {
			$qtyBooked	=	$actArticle->reserve( $menge) ;
			$this->QuantityReserved	+=	$qtyBooked ;
			$this->updateColInDb( "QuantityReserved") ;
		} catch( Exception $e) {

		}
	}
	/**
	 *
	 * @return void
	 */
	function	buche() {
		$this->_buche( 1) ;
	}
	/**
	 *
	 */
	function	unbuche() {
		$this->_buche( -1) ;
	}
	/**
	 *
	 * @return void
	 */
	function	getNextItemNo() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addWhere( "CustomerOrderNo='" . $this->CustomerOrderNo . "'") ;
		$myQuery->addOrder( ["ItemNo DESC"]) ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		try {
			$row	=	FDb::queryRow( $myQuery) ;
			error_log( $this->ItemNo . "..............................> " . $row['ItemNo']) ;
			if ( $row) {
			error_log( $this->ItemNo . "..............................> " . $row['ItemNo']) ;
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
