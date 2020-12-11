<?php

/**
 * Calc.php Base class for Customer Order (Calc)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * Calc - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCalc which should
 * not be modified.
 *
 * @package Application
 * @subpackage Calcier
 */
class	Calc_Prop	extends	Calc	implements	iCalc	{
	/**
	 *
	 */
	function	__construct() {
		Calc::__construct() ;
		Calc::setName( "None") ;
	}
	/**
	 *
	 * @param string $_class class of the following object
	 * @param unknown $_obj	reference to the object
	 */
	function	recalc( $_obj) {
		FDbg::begin( 1, "Calc_Prop.php", "Calc_Prop", "recalc( <...>)") ;
			$myKeyCol	=	$_obj->keyCol ;		// get the name of the key column
		$myKey	=	$_obj->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$_obj->className . "Item" ;
		/**
		 *
		 */
		try {
			$myArtikel	=	new Artikel() ;
			$myEKPreisR	=	new EKPreisR() ;
			$myArtikelEKPreis	=	new ArtikelEKPreis() ;
			$actItem	=	new $itemObjClassName() ;
			$actItem->setIterCond( "$myKeyCol='$myKey' ") ;
			foreach ( $actItem as $key => $item) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, "Calc_Prop.php", "Calc_Prop", "recalc( <obj>)", "working on article no. " . $item->ArtikelNr) ;
				$myArtikel->setArtikelNr( $actItem->ArtikelNr) ;
				$myEKPreisR->getCalcBase( $myArtikel->ArtikelNr) ;
				$myArtikelEKPreis->getMostActual( $myEKPreisR->LiefNr, $myEKPreisR->LiefArtNr) ;
				if ( $myArtikelEKPreis->isValid()) {
					$myCurr	=	new Currency() ;
					$currCond	=	"WHERE VonWaehrung = '".$myArtikelEKPreis->Waehrung."' "
								.	"AND NachWaehrung = 'EUR' "
								.	"AND CGueltigVon <= '".$this->today()."' "
								.	"AND '".$this->today()."' < CGueltigBis " ;
					$myCurr->fetchFromDbWhere( $currCond) ;
					if ( $myCurr->isValid()) {
						$myArtikelEKPreis->dump() ;
						$mr	=	$myArtikelEKPreis->OwnRabatt ;
						$item->Preis	=	$item->RefPreis * ( 1.0 - $mr + $mr / sqrt( $item->Menge)) ;
						$item->GesamtPreis	=	$item->Menge * $item->Preis ;
						$item->updateColInDb( "Preis,GesamtPreis") ;
					} else {
						$_obj->StatusInfo	.=	"kein gueltiger Umrechnungskurs " . $myArtikelEKPreis->Waehrung . " nach EUR fuer Artikel Nr. " . $item->ArtikelNr . "\n" ;
					}
				} else {
					$_obj->StatusInfo	.=	"kein gueltiger Einkaufspreis fuer Artikel Nr. " . $myArtikel->ArtikelNr . "\n" ;
				}
			}
		} catch( Exception $e) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Calc_Prop.php", "Calc_Prop", "recalc( <obj>)", "exception := '".$e->getMessage()) ;
			throw $e ;
		}
		FDbg::end( 1, "Calc_Prop.php", "Calc_Prop", "recalc( <...>)") ;
	}
}
Calc::register( "Prop") ;
?>
