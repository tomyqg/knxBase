<?php
/**
 * Article.php - Base class for Article
 *
 * This is the base class of Articles.
 *
 * Special considerations:
 *
 * Articles can be of the following fundamental types:
 *
 * Type			charateristics
 * Simple		only a single piece of something		:Comp = 0
 * 				e.g. Microscope
 * Composite-1	contains multiple items					:Comp = 1
 *				only sub-items are reservered in stock
 *				e.g. disection set
 * Composite-2	contains multiple items on purchasing	:Comp = 2
 * 				which need to be ordered from SAME
 * 				supplier on SAME order
 * 				e.g. rubber stopper with whole
 * Composite-3	contains multiple items					:Comp = 3
 * 				only main item is reserved in stock
 * 				e.g. PASCO experiment
 *
 * Valid combination of parameters nd article examples:
 * :Comp	Example
 *   0		microscope
 *  10		disection set
 *  11		trolley with different color trays
 *  20		rubber stopper with whole
 *  30		PASCO experiment
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2013-05-13	PA1		khw		added to rev. control
 * 2013-05-17
 * 2013-05-18	PA2		khw		added 'QuantityText' to getList and getSPList
 * 								method;
 *
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */

/**
 * erforder den WTF debugger und die datenbank-nahe Klasse BArticle
 */
//require_once( "Document.php") ;

/**
 * Article - User-Level Klasse
 *
 * This class acts as an interface towards the automatically generated BArticle which should
 * not be modified.
 *
 *
 * @package Application
 * @subpackage Article
 */

class	Artikel	extends	AppObject	{

	//private	static	$rArtSupStat		=> 	ArticleSupplyStatus
	//private	static	$rCompType			=>	ArticleCompositionType
	//private	static	$rArtType			=>	option only false/true
	const	ARTPRCMAN	=	  0 ;		// manual calculation
	const	ARTPRCAUTO	=	  1 ;		// automatic calculation OwnSalesPrice = MSRP - discount
	const	ARTPRCMSRP	=	  2 ;		// MSRP only, no sdiscounts
	//private	static	$rORDER_UNIT		=>	OrderUnit
	/**
	 * Erzeugung eines Objektes.
	 *
	 * Erzeugt ein Article-Objekt und versucht ggf. diesen Article aus der Db zu laden.
	 *
	 * @param string $_articleNo='' Articlenummer
	 * @return void
	 */
	function	__construct( $_articleNo='') {
		parent::__construct( "Artikel", "ArtikelNummer") ;
//		$this->dumpStruture() ;
		$this->traceUpdate	=	true ;
		if ( strlen( $_articleNo) > 0) {
			$this->setArticleNo( $_articleNo) ;
		} else {
		}
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	add( $_key="", $_id=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "Article.php::Article::add( '$_key', $_id, '$_val'): begin") ;
		try {
			/**
			 * get an ERP number first
			 */
			$myArticle	=	new Article( $_POST['_IArticleNo']) ;
			if ( $myArticle->_valid) {
				$e	=	new Exception( "Article.php::Article::add(...): Article no. already in use! Addition cancelled!") ;
				error_log( $e) ;
				throw $e ;
			}
			$myArtGr	=	new ArtGr( $_POST['_IPrimArtGr']) ;
			if ( $myArtGr->isValid()) {
				$erpNoStart	=	$myArtGr->ERPNoStart ;
				$erpNoEnd	=	$myArtGr->ERPNoEnd ;
				$myQuery	=	"SELECT IFNULL(( SELECT ERPNo + 1 FROM Article " .
								"ERPNo >= '$erpNoStart' AND ERPNo <= '$erpNoEnd' " .
								"ORDER BY ERPNo DESC LIMIT 1 ), " . ( $erpNoStart + 1) . " )  AS newKey" ;
				$myRow	=	FDb::queryRow( $myQuery) ;
				$myERPNo	=	sprintf( "%08s", $myRow['newKey']) ;
			} else {
					$e	=	new Exception( "Article.php::Article::add(...): can't determine ERP no. range! Addition cancelled!") ;
					error_log( $e) ;
					throw $e ;
			}
			$this->dumpPOST() ;
			$this->getFromPostL() ;
			$this->ERPNo	=	$myERPNo ;
			//			$this->ArticleNo	=	$_key ;
			$this->ArticleNo	=	$_POST['_IArticleNo'] ;		// !!! getFromPostL does NOT fetch the key column !!!
			$this->ArticleNoStock	=	$this->ArticleNo ;
			$this->ShopArticle	=	9 ;
			$this->EinzelSeite	=	1 ;
			$this->Marge	=	1.0 ;
			$this->MarginMinQ	=	1.0 ;
			$this->GenPflicht	=	1 ;
			$this->AutoPrice	=	1 ;
			$this->Markt	=	"all" ;
			$this->TaxClass	=	"A" ;
			$this->ErfDatum	=	$this->today() ;
			$this->storeInDb() ;

			$myArtTexte	=	new ArtTexte() ;
			$myArtTexte->ArticleNo	=	$this->ArticleNo ;
			$myArtTexte->Sprache	=	"de" ;
			$myArtTexte->ArticleDescription1	=	$this->ArticleDescription1 ;
			$myArtTexte->ArticleDescription2	=	$this->ArticleDescription2 ;
			$myArtTexte->QuantityText	=	$this->QuantityText ;
			$myArtTexte->storeInDb() ;
			$myArtTexte->Sprache	=	"en" ;
			$myArtTexte->storeInDb() ;
			$myArtTexte->Sprache	=	"es" ;
			$myArtTexte->storeInDb() ;
			$myArtTexte->Sprache	=	"fr" ;
			$myArtTexte->storeInDb() ;

			$myArticleStock	=	new ArticleStock() ;
			$myArticleStock->ArticleNo	=	$this->ArticleNo ;
			$myArticleStock->Def	=	1 ;
			$myArticleStock->storeInDb() ;

			$myArticleSalesPrice	=	new ArticleSalesPrice() ;
			$myArticleSalesPrice->ArticleNo	=	$this->ArticleNo ;
			$myArticleSalesPrice->ValidFrom	=	"2000-01-01" ;
			$myArticleSalesPrice->ValidUntil	=	"2000-01-01" ;
			$myArticleSalesPrice->Quantity	=	1 ;
			$myArticleSalesPrice->QuantityPerPU	=	1 ;
			$myArticleSalesPrice->storeInDb() ;

			$mySupplier	=	new Supplier() ;
			if ( $mySupplier->setSupplierPrefix( substr( $this->ArticleNo, 0, 3))) {
				$this->BildRef	=	substr( $this->ArticleNo, 0, 3) . "/" . $this->ArticleNo . ".jpg" ;
				$this->updateColInDb( "BildRef") ;
				$myArticlePurchasePriceRel	=	new ArticlePurchasePriceRel() ;
				$myArticlePurchasePriceRel->ArticleNo	=	$this->ArticleNo ;
				$myArticlePurchasePriceRel->SupplierNo	=	$mySupplier->SupplierNo ;
				$myArticlePurchasePriceRel->SupplierArticleNo	=	substr( $this->ArticleNo, 4) ;
				$myArticlePurchasePriceRel->KalkBasis	=	1 ;
				$myArticlePurchasePriceRel->MKF	=	1 ;
				$myArticlePurchasePriceRel->Marge	=	1 ;
				$myArticlePurchasePriceRel->OrdMode	=	1 ;
				$myArticlePurchasePriceRel->QuantityPerPU	=	1 ;
				$myArticlePurchasePriceRel->storeInDb() ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		$ret	=	$this->getXMLString() ;
		$ret	.=	$this->getTableArtTexteAsXML() ;
		$ret	.=	$this->getTableArticleStockAsXML() ;
		$ret	.=	$this->getTableArticleSalesPriceAsXML() ;
		FDbg::dumpL( 0x00000001, "Article.php::Article::add( '$_key', $_id, '$_val'): end") ;
		return $ret ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	upd( $_key, $_id, $_val) {
		FDbg::dumpL( 0x01000000, "Article::upd(...)") ;
		$this->getFromPostL() ;
		$this->PhonText	=	Phonetics::makePhoneticForDb( $this->ArticleNo . $this->getFullText( 0)) ; ;
		$this->updateInDb() ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		$inUse	=	0 ;
		$inUse	+=	FDb::getCount( "FROM CuRFQItem ArticleNo = '".$this->ArticleNo."' ") ;
		$inUse	+=	FDb::getCount( "FROM CuOffrItem ArticleNo = '".$this->ArticleNo."' ") ;
		$inUse	+=	FDb::getCount( "FROM CuOrdrItem ArticleNo = '".$this->ArticleNo."' ") ;
		$inUse	+=	FDb::getCount( "FROM CuCommItem ArticleNo = '".$this->ArticleNo."' ") ;
		$inUse	+=	FDb::getCount( "FROM CuDlvrItem ArticleNo = '".$this->ArticleNo."' ") ;
		$inUse	+=	FDb::getCount( "FROM CuInvcItem ArticleNo = '".$this->ArticleNo."' ") ;
		if ( $inUse == 0) {
			try {
				FDb::query( "DELETE FROM  AbKorrPosten ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArtBild ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArtEmpf ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArtGrComp CompArtNr = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ProdGrComp CompArtNr = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArticleComponent ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArticleComponent CompArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArtTexte ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArticleStock ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArticleUmsatz ArticleNo = '$this->ArticleNo' ") ;
	//			FDb::query( "DELETE FROM  EKDaten SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
	//			FDb::query( "DELETE FROM  EKPrice SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArticlePurchasePriceRel ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  InKonf ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  InKonfPosten ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  CuRFQItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  CuOffrItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  CuOrdrItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  KdGutsPosten ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  CuCommItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  KdLeihPosten ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  CuDlvrItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  CuInvcItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  SuOrdrItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  SuDlvrItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  CuCartItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  PCuOrdrItem ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  SerNo ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  PCuOrdrItem ArticleNo = '$this->ArticleNo' ") ;
	//			FDb::query( "DELETE FROM  VKDaten ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArticleSalesPrice ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArticleSalesPriceCache ArticleNo = '$this->ArticleNo' ") ;
	//			FDb::query( "DELETE FROM  ArticleSalesPricee ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  Article ArticleNo = '$this->ArticleNo' ") ;
				$this->ArticleNo	=	$this->ArticleNoNew ;
				$this->reload() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "Article.php::Article::del(...): article still in use [count:=$inUse]; move article first!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "Customer.php", "Customer", "getDepAsXML( '$_key', $_id, '$_val', <reply>)") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"Attribute"	:
			$myAttribute	=	new Attribute() ;
			if ( $_id == -1) {
				$myAttribute->DataTable	=	"Article" ;
				$myAttribute->RefNr	=	$this->ArticleNo ;
				$myAttribute->PosNr	=	10 ;
			} else {
				$myAttribute->setId( $_id) ;
			}
			return $myAttribute->getXMLString() ;
			break ;
		case	"ArtQPC"	:
			$myArtQPC	=	new ArtQPC() ;
			if ( $_id == -1) {
				$myArtQPC->ArticleNo	=	$this->ArticleNo ;
				$myArtQPC->QPC	=	1 ;
			} else {
				$myArtQPC->setId( $_id) ;
			}
			return $myArtQPC->getXMLString() ;
			break ;
		default	:
			$reply	=	parent::getDepAsXML( $_key, $_id, $_val, $reply) ;
			break ;
		}
		return $reply ;
	}
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
			if ( isset( $_POST['StartRow'])) {
				$this->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter	=	"( ArtikelNummer like '%" . $sCrit . "%' OR Bezeichnung like '%" . $sCrit . "%') " ;
			$myQuery	=	$this->getQueryObj( "Select") ;
			$myQuery->addWhere( $filter) ;
			$myQuery->addOrder( "ArtikelNummer") ;
			$reply->replyData	=	$this->tableFromQuery( $myQuery) ;
			break ;
		case	"Einkaufspreise"	:
			$myObj	=	new FDbObject( "Einkaufspreise") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter	=	"( Artikelnummer = '" . $this->ArtikelNummer . "') " ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( $filter) ;
			$myQuery->addOrder( "GueltigAb DESC") ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"Verkaufspreise"	:
			$myObj	=	new FDbObject( "Verkaufspreise") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$filter	=	"( Artikelnummer = '" . $this->ArtikelNummer . "') " ;
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( $filter) ;
			$myQuery->addOrder( "GueltigAb") ;
			$reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		return $reply ;
	}
}
?>
