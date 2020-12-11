<?php
interface	iMArket	{
	/**
	 * @return bool
	 */
	function	createDefaultSP() ;
	/**
	 * @param	ArtikelEKPreis	$_pp
	 * @param	VKPreisCache 	$_sp
	 * @param	float			$_tax
	 * @return	float
	 */
	function	getPrice( $_ekpr, $_pp, $_sp, $_tax, $_MarginMinQ) ;
}
?>