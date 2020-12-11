<?php
/**
 * CatalogGroupItem - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCatalogGroupItem which should
 * not be modified.
 *
 * @package WTA
 * @subpackage Article
 */
class	CatalogGroupItem	extends	FDbObject	{

	/*
	 * The constructor can be passed an ArticleNr (CatalogGroupItemNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_id=-1) {
		parent::__construct( "CatalogGroupItem", "Id") ;
		if ( $_id >= 0) {
			$this->setId( $_id) ;
		} else {
		}
	}

	/**
	 *
	 * @return void
	 */
	function	firstFromDb( $_cond='') {
		FDbg::get()->dumpL( 0x01000000, "CatalogGroupItem::firstFromDb()") ;
		if ( strlen( $_cond) > 0) {
			$this->myCond	=	$_cond ;
		} else {
			$this->myCond	=	sprintf( "CatalogGroupNr = '%s' ORDER BY PosNr ", $this->CatalogGroupNr) ;
		}
		BCatalogGroupItem::firstFromDb( $this->myCond) ;
	}

	/**
	 *
	 * @return void
	 */
	function	nextFromDb() {
		FDbg::get()->dumpL( 0x01000000, "CatalogGroupItem::nextFromDb()") ;
		BCatalogGroupItem::nextFromDb( $this->myCond) ;
	}

}

?>
