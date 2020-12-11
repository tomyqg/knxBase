<?php
/**
 * Batch.php - Class definition
 *  Domain:
 *  	- administrative
 * 	Batch references:
 * 		- n/a
 *  Batch is referenced by:
 *  	- TrailerType
 *  	- Calculation (twice!)
 *
 * Axle Units are part of the administrative domain and can be created/modified/deleted only by administrative
 * staff in the Adm domain.
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package BatchCalc
 */
/**
 * Batch - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 * @package BatchCalc
 * @subpackage Classes
 */
class	Batch	extends	AppObjectBC	{
	/*
	 * The constructor can be passed an ArticleNr (ArtikelNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct() {
		parent::__construct( "Batch", "Id") ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
	/**
	 *	add a new batch for the active product.
	 *	today()s year and today()s day of the year are determined. only if there's no open batch for today() a new batch
	 *	will be created and new labels be prodced.
	 */
	function	add( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <_reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		$myProduct	=	new Product() ;
		if ( $myProduct->fetchFromDbWhere( [ "Active = 1"])) {
			$this->getFromPost() ;
			$this->BatchNo	=	$_POST["BatchNo"] ;
			$this->defaultDates() ;		// set all dates to TODAY
			$this->TotalCount	=	0 ;
			$this->copyFrom( $myProduct) ;
			$this->Year	=	FDateTime::getYear() ;
			$this->DayOfYear	=	FDateTime::getDayOfTheYear() ;
			$this->storeInDb() ;
			/**
			 *
			 */
			$myFile	=	fopen( "/srv/www/vhosts/wimtecc.de/mas_r1/clients/99999998/data/test.txt", "w+") ;
			$myBatchItem	=	new BatchItem() ;
			for ( $il0=0 ; $il0<intval( $this->Qty) ; $il0++) {
				$myBatchItem->Prefix	=	$this->Prefix ;
				$myBatchItem->AILevel	=	$this->AILevel ;
				$myBatchItem->BatchNo	=	$this->BatchNo ;
				$myBatchItem->Year	=	FDateTime::getYear() ;
				$myBatchItem->DayOfYear	=	FDateTime::getDayOfTheYear() ;
				$this->QtyTotal++ ;
				$myBatchItem->ItemNo	=	$this->QtyTotal ;
				$myBatchItem->Supplier	=	$this->Supplier ;
				fwrite( $myFile, $this->Prefix . $this->PartNo . $this->AILevel . $this->BatchNo . $this->Year . sprintf( "%3d%04d", intval( $this->Year), intval( $myBatchItem->ItemNo)) . $this->Supplier . "\n") ;
				$myBatchItem->storeInDb() ;
			}
			$this->updateColInDb( "QtyTotal") ;
			fclose( $myFile) ;
		} else {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <_reply>)", "There is no active product!") ;
		}
		FDbg::end() ;
		return $this->getXMLString( $_key, $_id, $_val, $reply) ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
	function	createLabels( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <_reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		/**
		 *
		 */
		/**
		 *
		 */
		$myFile	=	fopen( "/srv/www/vhosts/wimtecc.de/mas_r1/clients/99999998/data/test.txt", "w+") ;
		$myBatchItem	=	new BatchItem() ;
		$myBatchItem->Qty	=	intval( $_POST[ "Qty"]) ;
		for ( $il0=0 ; $il0<$myBatchItem->Qty ; $il0++) {
			$myBatchItem->Prefix	=	$this->Prefix ;
			$myBatchItem->AILevel	=	$this->AILevel ;
			$myBatchItem->BatchNo	=	$this->BatchNo ;
			$myBatchItem->Year	=	$this->Year ;
			$myBatchItem->DayOfYear	=	$this->DayOfYear ;
			$this->QtyTotal++ ;
			$myBatchItem->ItemNo	=	$this->QtyTotal ;
			$myBatchItem->Supplier	=	$this->Supplier ;
			fwrite( $myFile, $this->Prefix . $this->PartNo . $this->AILevel . $this->BatchNo . $this->Year . sprintf( "%3d%04d", intval( $this->Year), intval( $myBatchItem->ItemNo)) . $this->Supplier . "\n") ;
			$myBatchItem->create() ;
		}
		$this->updateColInDb( "QtyTotal") ;
		fclose( $myFile) ;
		FDbg::end() ;
		return $this->getXMLString( $_key, $_id, $_val, $reply) ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch ( $_val) {
			default	:
				if ( $_id >= 0) {
					parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
				} else {
					$newItem	=	new $_val ;
					$newItem->bremse_id	=	$this->br_id ;
					$newItem->getAsXML( $_key="", $_id=-1, $_val="", $reply) ;
				}
				break ;
		}
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::addDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * buche
	 */
	function	updDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$elem	=	explode( ".", $_val) ;
		$objName	=	$elem[0] ;
		if ( isset( $elem[1]))
			$attrName	=	$elem[1] ;
		else
			$attrName	=	"" ;
		FDbg::trace( 2, "Batch.php", "Batch", "updDep( '$_key', $_id, '$_val')",
						"Class := '$objName', Attribute := '$attrName'") ;
		switch ( $objName) {
		default	:
			parent::updDep( $_key, $_id, $_val, $reply) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::delDep()
	 */
	function	delDep( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		switch( $_val) {
			default	:
				parent::delDep( $_key, $_id, $_val, $reply) ;
				break ;
		}
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		$sortOrder	=	"ORDER BY C.ProtocolNo ASC " ;
		switch ( $objName) {
		case	""	:
			$filter	=	"" ;
			$_searchCrit	=	"" ;
			$_idCrit	=	"" ;
			$_descriptionCrit	=	"" ;
			if ( isset( $_POST['_SSearch']))
				$_searchCrit	=	$_POST['_SSearch'] ;
			$filter	.=	"(" ;
			$filter	.=	"( Id like '%" . $_searchCrit . "%' ) " ;
			$filter	.=	")" ;
			/**
			 *
			 */
			$myObj	=	new FDbObject( "Batch", "Id", "def", "v_BatchSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [$filter]) ;
			$myQuery->addOrder( ["Year DESC", "DayOfYear DESC"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"BatchItem"	:
			$myObj	=	new FDbObject( "BatchItem", "BatchNo", "def", "v_BatchItemSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter	=	"BatchNo = '" . $this->BatchNo . "' " ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter]) ;
			$myQuery->addOrder( ["ItemNo"]) ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $reply ;
	}
}
?>
