<?php

/**
 * CustomerDelivery.php Definition der Basis Klasses f�r Kunden Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerDeliveryItem - Basis Klasse f�r Kunden Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerDelivery
 */
class	CustomerDeliveryItem	extends AppObjectERM	{
	public	$myArticle ;
	/**
	 *
	 */
	function	__construct( $_myCustomerDeliveryNo='') {
		parent::__construct( "CustomerDeliveryItem", "Id") ;
		$this->CustomerDeliveryNo	=	$_myCustomerDeliveryNo ;
	}
	/**
	 *
	 */
	function    reload() {
		$this->fetchFromDbById() ;
	}
	/**
	 *
	 */
	function	getNextItemNo() {
		$query	=	sprintf( "SELECT ItemNo FROM CustomerDeliveryItem WHERE CustomerDeliveryNo='%s' ORDER BY ItemNo DESC LIMIT 0, 1 ", $this->CustomerDeliveryNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$row    =       mysql_fetch_array( $sqlResult) ;
			$this->ItemNo	=	$row[0] + 10 ;
		}
		return $this->_status ;
	}
	/**
	 *
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		error_log( "CustomerDelivery.php::CustomerDeliveryItem::_buche( $_sign): begin") ;
		if ( $_sign == -1) {
			$menge	=	$this->QuantityGebucht * $_sign ;
		} else {
			$menge	=	($this->QuantityDelivered * $this->QuantityPerPU) - $this->QuantityGebucht ;
		}
		try {
			$myArticle	=	new Article() ;
			$myArticle->setArticleNo( $this->ArticleNo) ;
			$qtyBooked	=	$myArticle->deliver( $menge) ;		// qty PerPack already considered above!!!
			$this->QuantityGebucht	+=	$qtyBooked ;
			$this->updateColInDb( "QuantityGebucht") ;
		} catch( Exception $e) {
			throw $e ;
		}
		error_log( "CustomerDelivery.php::CustomerDeliveryItem::_buche(...): end") ;
	}
	function	buche() {		$this->_buche( 1) ;			}
	function	unbuche() {		$this->_buche( -1) ;		}
}
?>
