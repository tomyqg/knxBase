<?php
/**
 * CustomerInvoice.php Definition der Basis Klasses f�r Customern Lieferungen
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CustomerInvoiceItem - Basis Klasse f�r Customern Lieferung
 *
 * not be modified.
 *
 * @package Application
 * @subpackage CustomerInvoice
 */
class	CustomerInvoiceItem	extends	AppObjectERM	{
	/**
	 *
	 */
	function	__construct( $_myCustomerInvoiceNo="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_myCustomerInvoiceNo')") ;
		parent::__construct( "CustomerInvoiceItem", "Id") ;
		$this->CustomerInvoiceNo	=	$_myCustomerInvoiceNo ;
		FDbg::end() ;
	}
	/**
	 *
	 */
	function	reload() {
		FDbg::dumpL( 0x00000001, "CustomerInvoice.php::CustomerInvoiceItem::reload(): begin") ;
		$this->fetchFromDbById() ;
		$this->myArticle->setArticleNo( $this->ArticleNo) ;
		FDbg::end() ;
	}
	/**
	 * Zugriff auf Article
	 *
	 * @return [Article]
	 */
	function	getArticle() {
		return $this->myArticle ;
	}
	/**
	 *
	 * @return void
	 */
	function	getNextItemNo( $_posType=0) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myQuery->addWhere( "CustomerOrderNo='" . $this->CustomerOrderNo . "'") ;
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
