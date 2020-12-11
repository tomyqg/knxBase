<?php
/**
 * CustomerOffer.php Anwendungsklasses f�r Angebote
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerOfferItem - Anwendungsklasse f�r: Angebot
 *
 * This class acts as an interface towards the automatically generated BCustomerOffer which should
 * not be modified.
 *
 * @package Base
 * @subpackage CustomerOffer
 */
class	CustomerOfferItem	extends	AppDepObject	{

	public	$myArtikel ;
	public	$myCond ;

	function	__construct( $_cuOffrNo='') {
		parent::__construct( "CustomerOfferItem", "Id") ;
		$this->CustomerOfferNo	=	$_cuOffrNo ;
	}
	/**
	 *
	 * @return void
	 */
	function	getNextItemNo() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addWhere( "CustomerInvoiceNo='" . $this->CustomerInvoiceNo . "'") ;
		$myQuery->addOrder( ["ItemNo DESC"]) ;
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
}

?>
