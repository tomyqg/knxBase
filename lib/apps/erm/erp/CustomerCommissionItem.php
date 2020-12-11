<?php
/**
 * CustomerCommission.php - Declaration of the base class for a Customer Commission (CustomerCommission)
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerCommission - Benutzer-Level Klasse f�r Customernkommission
 *
 * Implementiert Application Layer FUnktionen rund um Kommissinonierungsposten
 *
 * @package Base
 * @subpackage CustomerCommission
 */
class	CustomerCommissionItem	extends	AppObjectERM	{
	/**
	 *
	 * @var unknown_type
	 */
	public	$myCond ;
	/**
	 *
	 */
	function	__construct( $_myCustomerCommissionNo='') {
		parent::__construct( "CustomerCommissionItem", "Id") ;
		$this->CustomerCommissionNo	=	$_myCustomerCommissionNo ;
	}
	/**
	 *
	 */
	function    reload() {
		$this->fetchFromDbById() ;
	}
	/**
	 *
	 * @param $_sign
	 */
	function	_buche( $_sign) {
		$actArticle	=	new Article( $this->ArticleNo) ;
		if ( $_sign == -1) {
			$menge	=	$this->QuantityGebucht * $_sign ;
		} else {
			$menge	=	$this->Quantity - $this->QuantityGebucht ;
		}
		try {
			$qtyBooked	=	$actArticle->commission( $menge * $this->QuantityPerPU) ;
			$this->QuantityGebucht	+=	$qtyBooked ;
			$this->updateColInDb( "QuantityGebucht") ;
		} catch( Exception $e) {

		}
	}
	function	buche( $_key="", $_id=-1, $_val="") {
		$this->_buche( 1) ;
	}
	function	unbuche( $_key="", $_id=-1, $_val="") {
		$this->_buche( -1) ;
	}
	/**
	 * Zugriff auf Article
	 *
	 * @return [Article]
	 */
	function	getArticle() {
		return $this->myArticle ;
	}

	function	getNextItemNo() {
		$query	=	sprintf( "SELECT ItemNo FROM CustomerCommissionItem WHERE CustomerCommissionNo='%s' ORDER BY ItemNo DESC LIMIT 0, 1 ", $this->CustomerCommissionNo) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) {
			$this->_status  =       -1 ;
		} else {
			$row    =       mysql_fetch_array( $sqlResult) ;
			$this->ItemNo	=	$row[0] + 1 ;
		}
		return $this->_status ;
	}

}

?>
