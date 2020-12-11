<?php

/**
 * CustomerReminder.php Definition der Basis Klasses für Kunden Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerReminderItem - Basis Klasse für Kunden Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerReminder
 */
class	CustomerReminderItem	extends AppDepObject	{

	public	$myArtikel ;
	public	$myCond ;

	/**
	 *
	 */
	function	__construct( $_myCustomerReminderNr='') {
		FDbg::dumpL( 0x01000000, "CustomerReminderItem::__constructor") ;
		parent::__construct( "CustomerReminderItem", "Id") ;
		$this->CustomerReminderNr	=	$_myCustomerReminderNr ;
		$this->myArtikel	=	new Artikel() ;
	}

	/**
	 *
	 */
	function    reload() {
		FDbg::dumpL( 0x01000000, "CustomerReminderItem::reload()") ;
		$this->fetchFromDbById() ;
		$this->myArtikel->setArtikelNr( $this->ArtikelNr) ;
		FDbg::dumpL( 0x01000000, "CustomerReminderItem::reload(), done") ;
	}

	function	getNextPosNr() {
		$query	=	sprintf( "SELECT PosNr FROM CustomerReminderItem WHERE CustomerReminderNr='%s' ORDER BY PosNr DESC LIMIT 0, 1 ", $this->CustomerReminderNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->PosNr	=	$row[0] + 1 ;
		}
		return $this->_status ;
	}
		
}


?>
