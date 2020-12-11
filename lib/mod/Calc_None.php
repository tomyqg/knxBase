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
class	Calc_None	extends	Calc	implements	iCalc	{
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
		FDbg::begin( 1, "Calc_None.php", "Calc_None", "recalc( <...>)") ;
		$myKeyCol	=	$_obj->keyCol ;		// get the name of the key column
		$myKey	=	$_obj->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$_obj->className . "Item" ;
		/**
		 *
		 */
		try {
			$actItem	=	new $itemObjClassName() ;
			$actItem->setIterCond( "$myKeyCol='$myKey' ") ;
			foreach ( $actItem as $key => $item) {
				$item->Preis	=	$item->RefPreis ;
				$item->updateColInDb( "Preis") ;
			}
		} catch( Exception $e) {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Calc_None.php", "Calc_None", "recalc( <obj>)", "exception := '".$e->getMessage()) ;
			throw $e ;
		}

		FDbg::end( 1, "Calc_None.php", "Calc_None", "recalc( <...>)") ;
	}
}
Calc::register( "None") ;
?>
