<?php

/**
 * CustomerDelivery.php Definition der Basis Klasses f�r Kunden Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * BatchItem - Basis Klasse f�r Kunden Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerDelivery
 */
class	BatchItem	extends AppObject	{
	public	$myArticle ;
	/**
	 *
	 */
	function	__construct( $_myBatchNo='') {
		parent::__construct( "BatchItem", "Id") ;
		$this->BatchNo	=	$_myBatchNo ;
	}
	/**
	 *
	 */
	function    reload() {
		$this->fetchFromDbById() ;
	}
}
?>
