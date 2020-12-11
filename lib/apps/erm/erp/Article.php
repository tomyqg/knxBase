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
 * Type            charateristics
 * Simple        only a single piece of something        :Comp = 0
 *                e.g. Microscope
 * Composite-1    contains multiple items                    :Comp = 1
 *                only sub-items are reservered in stock
 *                e.g. disection set
 * Composite-2    contains multiple items on purchasing    :Comp = 2
 *                which need to be ordered from SAME
 *                supplier on SAME order
 *                e.g. rubber stopper with whole
 * Composite-3    contains multiple items                    :Comp = 3
 *                only main item is reserved in stock
 *                e.g. PASCO experiment
 *
 * Valid combination of parameters nd article examples:
 * :Comp    Example
 *   0        microscope
 *  10        disection set
 *  11        trolley with different color trays
 *  20        rubber stopper with whole
 *  30        PASCO experiment
 *
 * Revision history
 *
 * Date            Rev.    Who        what
 * ----------------------------------------------------------------------------
 * 2015-07-20    PA3        khw        adjusted to new environment with
 *                                FDb::getQueryObj(...);
 * 2013-05-18    PA2        khw        added 'QuantityText' to getList and getSPList
 *                                method;
 * 2013-05-13    PA1        khw        added to rev. control
 *
 *
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * erforder den WTF debugger und die datenbank-nahe Klasse BArticle
 */

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
class    Article extends AppObject {
	
	//private	static	$rArtSupStat		=> 	ArticleSupplyStatus
	//private	static	$rCompType			=>	ArticleCompositionType
	//private	static	$rArtType			=>	option only false/true
	const    ARTPRCMAN = 0;        // manual calculation
	const    ARTPRCAUTO = 1;        // automatic calculation OwnSalesPrice = MSRP - discount
	const    ARTPRCMSRP = 2;        // MSRP only, no sdiscounts
	//private	static	$rORDER_UNIT		=>	OrderUnit
	/**
	 * Erzeugung eines Objektes.
	 *
	 * Erzeugt ein Article-Objekt und versucht ggf. diesen Article aus der Db zu laden.
	 *
	 * @param string $_articleNo ='' Articlenummer
	 * @return void
	 */
	function __construct( $_articleNo = '' ) {
		FDbg::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "()" );
		parent::__construct( "Article", "ArticleNo" );
		$this->traceUpdate = true;
		if ( strlen( $_articleNo ) > 0 ) {
			$this->setArticleNo( $_articleNo );
		} else {
		}
		FDbg::end();
	}
	
	function setArticleNo( $_myArticleNo ) {
		$this->ArticleNo = $_myArticleNo;
		$this->fetchFromDb();
		if ( $this->isValid() == 1 ) {
		} else {
			$where = "ERPNo = '" . $this->ArticleNo . "'";
			//			if ( ! $this->fetchFromDbWhere( $where)) {
			//				$e	=	new Exception( "Article.php::Article::setArticleNo::invalid[ArticleNo=$_myArticleNo]") ;
			//				error_log( $e) ;
			//				throw ( $e) ;
			//			}
		}
		return $this->isValid();
	}
	
	/**
	 *
	 */
	static function getRArtSupStat() {
		return self::$rArtSupStat;
	}
	
	static function getRCompType() {
		return self::$rCompType;
	}
	
	static function getRArtType() {
		return self::$rArtType;
	}
	
	static function getRPrcMode() {
		return self::$rPrcMode;
	}
	
	static function getRLfMode() {
		return self::$rLfMode;
	}
	
	static function getRKdMode() {
		return self::$rKdMode;
	}
	
	static function getRAutoPrice() {
		return self::$rAutoPrice;
	}
	
	static function getRAutoPriceSrc() {
		return self::$rAutoPriceSrc;
	}
	
	/**
	 *
	 */
	static function rebuildPhonetic() {
		$actArticle = new Article();
		$actArtTexte = new ArtTexte();
		for ( $actArticle->_firstFromDb( "ArticleNo like '%' " ) ; $actArticle->isValid() ; $actArticle->_nextFromDb() ) {
			for ( $actArtTexte->_firstFromDb( "ArticleNo like '" . $actArticle->ArticleNo . "' " ) ; $actArtTexte->isValid() ; $actArtTexte->_nextFromDb() ) {
				$actArtTexte->PhonText = Phonetics::makePhoneticForDb( $actArticle->ArticleNo . $actArticle->getFullText( 0 ) );
				$actArtTexte->updateColInDb( "PhonText" );
			}
			$actArticle->PhonText = Phonetics::makePhoneticForDb( $actArticle->ArticleNo . $actArticle->getFullText( 0 ) );
			$actArticle->updateColInDb( "PhonText" );
		}
	}
	
	/**
	 * Vollst�ndigen Articletext, bestend aus den beiden Articlebezeichnungen
	 * sowie dem opt. Quantitytext, ggf. basierend auf [$_menge], abfragen.
	 *
	 * Die einzelnen Bestandteile des Articletextes werden, falls vorhanden
	 * �ber ", " getrennt an einander gehangen und als einzelner Textstring
	 * zur�ckgeliefert.
	 *
	 * @return string
	 */
	function getFullText( $_menge ) {
		
		$buf = sprintf( "%s", $this->ArticleDescription1 );
		if ( strcmp( $this->ArticleDescription1, $this->ArticleDescription2 ) != 0 && strlen( $this->ArticleDescription2 ) > 0 ) {
			$buf .= sprintf( ", %s", $this->ArticleDescription2 );
		}
		if ( strlen( $this->QuantityText ) > 0 ) {
			$buf .= sprintf( ", %s", $this->QuantityText );
		} else {
			if ( $_menge > 1 ) {
				$buf .= " " . $this->textFromQuantity( $_menge );
			}
		}
		
		return $buf;
		
	}
	
	/**
	 *
	 * @param $_mengePerPU
	 */
	function textFromQuantity( $_mengePerPU ) {
		switch ( $this->SPU ) {
		case    "lmm"    :
			$myBuf = sprintf( "(%d mm)", $_mengePerPU );                //	| | | | |
		break;
		case    "gmg"    :
			$myBuf = sprintf( "(Vorratsflasche mit %d mg)", $_mengePerPU );                //	| | | | |
		break;
		case    "ggr"    :
			$myBuf = sprintf( "(Vorratsflasche mit %d g)", $_mengePerPU );                //	| | | | |
		break;
		case    "gkg"    :
			$myBuf = sprintf( "(Vorratsflasche mit %d Kg)", $_mengePerPU );                //	| | | | |
		break;
		case    "vml"    :
			$myBuf = sprintf( "(Vorratsflasche mit %d ml)", $_mengePerPU );                //	| | | | |
		break;
		case    "vl"    :
			$myBuf = sprintf( "(Vorratsflasche mit %d L)", $_mengePerPU );                //	| | | | |
		break;
		case    "stck"    :
			if ( $_mengePerPU != 1 ) {
				$myBuf = sprintf( "(Packung zu %d St.)", $_mengePerPU );                //	| | | | |
			} else {
				$myBuf = "";
			}
		break;
		case    "stro"    :
			$myBuf = sprintf( "(%d Rolle(n))", $_mengePerPU );                //	| | | | |
		break;
		case    "sthe"    :
			$myBuf = sprintf( "(%d Heft(e))", $_mengePerPU );                //	| | | | |
		break;
		case    "stst"    :
			$myBuf = sprintf( "(%d Streifen)", $_mengePerPU );                //	| | | | |
		break;
		case    "stsa"    :
			$myBuf = sprintf( "(%d Satz)", $_mengePerPU );                //	| | | | |
		break;
		case    "stdo"    :
			$myBuf = sprintf( "(Dose zu %d)", $_mengePerPU );                //	| | | | |
		break;
		case    "stfl"    :
			$myBuf = sprintf( "(%d Flasche(n))", $_mengePerPU );                //	| | | | |
		break;
		case    "stvd"    :
			$myBuf = sprintf( "(%d Vorratsdose(n))", $_mengePerPU );                //	| | | | |
		break;
		case    "stvf"    :
			$myBuf = sprintf( "(%d Vorratsflasche(n))", $_mengePerPU );                //	| | | | |
		break;
		case    "stpa"    :
			$myBuf = sprintf( "(%d Paket(e))", $_mengePerPU );                //	| | | | |
		break;
		case    "stam"    :
			$myBuf = sprintf( "(%d Ampulle(n))", $_mengePerPU );                //	| | | | |
		break;
		default    :
			error_log( "Article::textFromQuantity(): Quantityeinheit [$this->ArticleNo=>$this->SPU] undefiniert" );
			//			$myBuf	=	sprintf( "(FEHLER)", $_mengePerPU) ;				//	| | | | |
			if ( $_mengePerPU != 1 ) {
				$myBuf = sprintf( "(Packung zu %d St.?)", $_mengePerPU );                //	| | | | |
			} else {
				$myBuf = "";
			}
		break;
		}
		return $myBuf;
	}
	
	function getLastAsXML( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		FDbg::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "( '$_key', $_id, '$_val', <REPLY>)" );
		if ( $reply == null ) {
			$reply = new Reply( __class__, $this->className );
		}
		if ( isset( SysSession::$data ) ) {
			$keyName = $this->className . "LastKey";
			if ( isset( SysSession::$data[ $keyName ] ) ) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__ ), __CLASS__, __METHOD__ . "( '$_key', $_id, '$_val', <REPLY>)", "ArtickelLastKey := '" . SysSession::$data[ $keyName ] . "'" );
				$this->setKey( SysSession::$data[ $keyName ] );
				$this->getXMLString( $_key, $_id, $_val, $reply );
			}
		}
		FDbg::end();
		return $reply;
	}
	
	/**
	 *
	 */
	function setKey( $_articleNo, $_db = "def" ) {
		FDbg::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "( '$_articleNo', '$_db')" );
		if ( ! FDbObject::setKey( $_articleNo ) ) {
			$where = "ERPNo = '" . $this->ArticleNo . "'";
			if ( ! $this->fetchFromDbWhere( $where ) ) {
				$e = new Exception( "Article.php::Article::setKey::invalid[ArticleNo=$_articleNo]" );
				error_log( $e );
				throw ( $e );
			}
		}
		if ( $this->isValid() ) {
			if ( isset( SysSession::$data ) ) {
				$keyName = $this->className . "LastKey";
				SysSession::$data[ $keyName ] = $this->ArticleNo;
			}
		}
		FDBg::end();
		return $this->isValid();
	}
	
	/**
	 *
	 * @param $_keyx
	 * @param $_id
	 * @param $_val
	 */
	function getXMLString( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		FDbg::begin( 1, "Article.php", "Article", "getAsXML( '$_key', $_id, '$_val')" );
		if ( $reply == null ) {
			$reply = new Reply( __class__, $this->className );
		}
		$reply->replyData = $this->getXMLF();
		FDbg::end();
		return $reply;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function add( $_key = "", $_id = - 1, $_val = "", $_reply = null ) {
		FDbg::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "( '$_key', $_id, '$_val')" );
		if ( $_val == "ArticleText" ) {
			$this->addDep( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "Attribute" ) {
			$this->addDep( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticleComponent" ) {
			$this->addDep( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticleStock" ) {
			$this->addDep( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticleSalesPrice" ) {
			$this->addDep( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticlePurchasePriceRel" ) {
			$this->addDep( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticlePurchasePrice" ) {
			$this->addDep( $_key, $_id, $_val, $_reply );
		} else {
			try {
				/**
				 * get an ERP number first
				 */
				$myArticle = new Article( $_POST[ '_IArticleNo' ] );
				if ( $myArticle->isValid() ) {
					$e = new Exception( "Article.php::Article::add(...): Article no. already in use! Addition cancelled!" );
					error_log( $e );
					throw $e;
				}
				$myArtGr = new ArtGr( $_POST[ '_IPrimArtGr' ] );
				if ( $myArtGr->isValid() ) {
					$erpNoStart = $myArtGr->ERPNoStart;
					$erpNoEnd = $myArtGr->ERPNoEnd;
					$myQuery = "SELECT IFNULL(( SELECT ERPNo + 1 FROM Article " . "ERPNo >= '$erpNoStart' AND ERPNo <= '$erpNoEnd' " . "ORDER BY ERPNo DESC LIMIT 1 ), " . ( $erpNoStart + 1 ) . " )  AS newKey";
					$myRow = FDb::queryRow( $myQuery );
					$myERPNo = sprintf( "%08s", $myRow[ 'newKey' ] );
				} else {
					$e = new Exception( "Article.php::Article::add(...): can't determine ERP no. range! Addition cancelled!" );
					error_log( $e );
					throw $e;
				}
				$this->dumpPOST();
				$this->getFromPostL();
				$this->ERPNo = $myERPNo;
				//			$this->ArticleNo	=	$_key ;
				$this->ArticleNo = $_POST[ '_IArticleNo' ];        // !!! getFromPostL does NOT fetch the key column !!!
				$this->ArticleNoStock = $this->ArticleNo;
				$this->ShopArticle = 9;
				$this->EinzelSeite = 1;
				$this->Margin = 1.0;
				$this->MarginMinQ = 1.0;
				$this->GenPflicht = 1;
				$this->AutoPrice = 1;
				$this->Markt = "all";
				$this->TaxClass = "A";
				$this->ErfDatum = $this->today();
				$this->storeInDb();
				
				$myArtTexte = new ArtTexte();
				$myArtTexte->ArticleNo = $this->ArticleNo;
				$myArtTexte->Sprache = "de";
				$myArtTexte->ArticleDescription1 = $this->ArticleDescription1;
				$myArtTexte->ArticleDescription2 = $this->ArticleDescription2;
				$myArtTexte->QuantityText = $this->QuantityText;
				$myArtTexte->storeInDb();
				$myArtTexte->Sprache = "en";
				$myArtTexte->storeInDb();
				$myArtTexte->Sprache = "es";
				$myArtTexte->storeInDb();
				$myArtTexte->Sprache = "fr";
				$myArtTexte->storeInDb();
				
				$myArticleStock = new ArticleStock();
				$myArticleStock->ArticleNo = $this->ArticleNo;
				$myArticleStock->Def = 1;
				$myArticleStock->storeInDb();
				
				$myArticleSalesPrice = new ArticleSalesPrice();
				$myArticleSalesPrice->ArticleNo = $this->ArticleNo;
				$myArticleSalesPrice->ValidFrom = "2000-01-01";
				$myArticleSalesPrice->ValidUntil = "2000-01-01";
				$myArticleSalesPrice->Quantity = 1;
				$myArticleSalesPrice->QuantityPerPU = 1;
				$myArticleSalesPrice->storeInDb();
				
				$mySupplier = new Supplier();
				if ( $mySupplier->setSupplierPrefix( substr( $this->ArticleNo, 0, 3 ) ) ) {
					$this->BildRef = substr( $this->ArticleNo, 0, 3 ) . "/" . $this->ArticleNo . ".jpg";
					$this->updateColInDb( "BildRef" );
					$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
					$myArticlePurchasePriceRel->ArticleNo = $this->ArticleNo;
					$myArticlePurchasePriceRel->SupplierNo = $mySupplier->SupplierNo;
					$myArticlePurchasePriceRel->SupplierArticleNo = substr( $this->ArticleNo, 4 );
					$myArticlePurchasePriceRel->CalculationBase = 1;
					$myArticlePurchasePriceRel->MKF = 1;
					$myArticlePurchasePriceRel->Margin = 1;
					$myArticlePurchasePriceRel->OrdMode = 1;
					$myArticlePurchasePriceRel->QuantityPerPU = 1;
					$myArticlePurchasePriceRel->storeInDb();
				}
			} catch ( Exception $e ) {
				throw $e;
			}
		}
		$this->getXMLString( $_key, $_id, $_val, $_reply );
		return $_reply;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function addDep( $_key = "", $_id = - 1, $_val = "", $_reply = null ) {
		if ( $_reply == null ) {
			$_reply = new Reply( __class__, $this->className );
		}
		$objName = $_val;
		switch ( $objName ) {
		case    "ArticleText"    :
			$myArticleText = new ArticleText();
			$myArticleText->getFromPostL();
			$myArticleText->ArticleNo = $this->ArticleNo;
			$myArticleText->storeInDb();
			break;
		case    "Attribute"    :
			$myAttribute = new Attribute();
			$myAttribute->first( "DataTable = 'Article' AND RefNo = '".$this->ArticleNo."'", "ItemNo DESC" ) ;
			$myItemNo	=	$myAttribute->ItemNo ;
			$myAttribute->DataTable	=	"Article" ;
			$myAttribute->getFromPostL();
			$myAttribute->RefNo = $this->ArticleNo;
			$myAttribute->ItemNo	=	sprintf( "%03d", intval( $myItemNo) + 10) ;
			$myAttribute->storeInDb();
			break;
		case    "ArticleComponent"    :
			$myArticleComponent = new ArticleComponent();
			$myArticleComponent->getFromPostL();
			$myArticleComponent->ArticleNo = $this->ArticleNo;
			$myArticleComponent->storeInDb();
			break;
		case    "ArticleStock"    :
			$myArticleStock = new ArticleStock();
			$myArticleStock->getFromPostL();
			$myArticleStock->ArticleNo = $this->ArticleNo;
			$myArticleStock->storeInDb();
			break;
		case    "ArticleSalesPrice"    :
			$myArticleSalesPrice = new ArticleSalesPrice();
			$myArticleSalesPrice->getFromPostL();
			$myArticleSalesPrice->ArticleNo = $this->ArticleNo;
			$myArticleSalesPrice->storeInDb();
			break;
		case    "ArticlePurchasePriceRel"    :
			$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
			$myArticlePurchasePriceRel->getFromPostL();
			$myArticlePurchasePriceRel->ArticleNo = $this->ArticleNo;
			$myArticlePurchasePriceRel->storeInDb();
			break;
		case    "ArticlePurchasePrice"    :
			$myArticlePurchasePrice = new ArticlePurchasePrice();
			$myArticlePurchasePrice->getFromPostL();
			$myArticlePurchasePrice->ArticleNo = $this->ArticleNo;
			$myArticlePurchasePrice->storeInDb();
			break;
		default    :
			$newDep = new $objName();
			$newDep->ArticleNo = $this->ArticleNo;
			$newDep->ArticleNo = $this->ArticleNo;
			$newDep->getFromPostL();
			$newDep->storeInDb();
			break;
		}
		FDbg::end();
		return $_reply;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function del( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		$inUse = 0;
		$inUse += FDb::getCount( "FROM CuRFQItem ArticleNo = '" . $this->ArticleNo . "' " );
		$inUse += FDb::getCount( "FROM CuOffrItem ArticleNo = '" . $this->ArticleNo . "' " );
		$inUse += FDb::getCount( "FROM CuOrdrItem ArticleNo = '" . $this->ArticleNo . "' " );
		$inUse += FDb::getCount( "FROM CuCommItem ArticleNo = '" . $this->ArticleNo . "' " );
		$inUse += FDb::getCount( "FROM CuDlvrItem ArticleNo = '" . $this->ArticleNo . "' " );
		$inUse += FDb::getCount( "FROM CuInvcItem ArticleNo = '" . $this->ArticleNo . "' " );
		if ( $inUse == 0 ) {
			try {
				FDb::query( "DELETE FROM  AbKorrPosten ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ArtBild ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ArtEmpf ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ArtGrComp CompArtNr = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ProdGrComp CompArtNr = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ArticleComponent ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ArticleComponent CompArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ArtTexte ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ArticleStock ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ArticleUmsatz ArticleNo = '$this->ArticleNo' " );
				//			FDb::query( "DELETE FROM  EKDaten SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
				//			FDb::query( "DELETE FROM  EKPrice SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArticlePurchasePriceRel ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  InKonf ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  InKonfPosten ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  CuRFQItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  CuOffrItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  CuOrdrItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  KdGutsPosten ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  CuCommItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  KdLeihPosten ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  CuDlvrItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  CuInvcItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  SuOrdrItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  SuDlvrItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  CuCartItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  PCuOrdrItem ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  SerNo ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  PCuOrdrItem ArticleNo = '$this->ArticleNo' " );
				//			FDb::query( "DELETE FROM  VKDaten ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  ArticleSalesPrice ArticleNo = '$this->ArticleNo' " );
				FDb::query( "DELETE FROM  ArticleSalesPriceCache ArticleNo = '$this->ArticleNo' " );
				//			FDb::query( "DELETE FROM  ArticleSalesPricee ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "DELETE FROM  Article ArticleNo = '$this->ArticleNo' " );
				$this->ArticleNo = $this->ArticleNoNew;
				$this->reload();
			} catch ( Exception $e ) {
				throw $e;
			}
		} else {
			$e = new Exception( "Article.php::Article::del(...): article still in use [count:=$inUse]; move article first!" );
			error_log( $e );
			throw $e;
		}
		return $this->getXMLComplete();
	}
	
	/**
	 *
	 */
	function setImage( $_key = "", $_id = - 1, $_val = "", $_reply = null ) {
		FDbg::begin( 1, "Article.php", "Article", "setImage( '$_key', $_id, '$_val')" );
		if ( $_reply == null ) {
			$_reply = new Reply( __class__, $this->className );
		}
		/**
		 *
		 */
		$elem = explode( ".", $this->ArticleNo );
		$path1 = $this->path->Images . "/";
		$path2 = "";
		$file = "";
		if ( count( $elem ) > 1 ) {
			for ( $i = 0 ; $i < 1 ; $i ++ ) {
				$path2 .= $elem[ $i ] . "/";
			}
		}
		for ( ; $i < count( $elem ) ; $i ++ ) {
			if ( $i > 1 ) {
				$file .= ".";
			}
			$file .= $elem[ $i ];
		}
		FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "storage path := '$path2'" );
		$fn = ( isset( $_SERVER[ 'HTTP_X_FILENAME' ] ) ? $_SERVER[ 'HTTP_X_FILENAME' ] : false );
		FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", print_r( $_FILES, true ) );
		if ( $fn ) {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "fn is set" );
			//			file_put_contents(
			//				'uploads/' . $fn,
			//				file_get_contents('php://input')
			//			);
			FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "$fn uploaded" );
		} else {
			FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "fn is * NOT * set" );
			$idx = 0;
			foreach ( $_FILES as $idx => $data ) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "ImageName['$idx']" );
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", print_r( $data, true ) );
				$filename = $path1 . $path2 . $file . "-" . sprintf( "%03d", $idx );
				$filenameWM = $path1 . $path2 . "wm_" . $file . "-" . sprintf( "%03d", $idx );
				$imageReference = $path2 . "wm_" . $file . "-" . sprintf( "%03d", $idx );
				switch ( $data[ "type" ] ) {
				case    "image/png"    :
					$filename .= ".png";
					$filenameWM .= ".png";
				break;
				case    "image/jpeg"    :
					$filename .= ".jpg";
					$filenameWM .= ".jpg";
				break;
				}
				$imageReference .= ".jpg";
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "setImage( ...)", "$filename" );
				if ( move_uploaded_file( $data[ "tmp_name" ], $filename ) ) {
					echo "File is valid, and was successfully uploaded.<br/>\n";
					/**
					 * create the watermarked (embossed comment) files
					 */
					$sysCmd = "cd " . $this->path->Images . "; export PATH=\$PATH:/opt/local/bin ; " . "/opt/local/bin/convert " . $filename . " " . "-font Arial -pointsize 40 " . "-draw \"gravity southwest fill black text 0,12 'Copyright: flaschen24.eu' fill white text 1,11 'Copyright: flaschen24.eu' \" " . $filenameWM . " ";
					error_log( "system command: '" . $sysCmd . "'" );
					system( $sysCmd, $res );
					error_log( $res );
					/**
					 * create thumb nails and various other formats
					 */
					$sysCmd = "cd " . $this->path->Images . "; export USER=_www ; make ";
					error_log( "system command: '" . $sysCmd );
					system( $sysCmd, $res );
					error_log( $res );
					/**
					 *
					 */
					$this->ImageReference = $imageReference;
					$this->updateColInDb( "ImageReference" );
				} else {
					echo "Possible file upload attack!<br/>\n";
				}
			}
		}
		FDbg::end();
		return $_reply;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function createFromArticlePurchasePriceRelId( $_key, $_id, $_val ) {
		try {
			$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
			$myArticlePurchasePriceRel->setId( $_id );
			if ( $myArticlePurchasePriceRel->isValid() ) {
				$mySupplier = new Supplier( $myArticlePurchasePriceRel->SupplierNo );
				$this->ArticleNo = $myArticlePurchasePriceRel->ArticleNo;
				$this->ArticleDescription1 = $myArticlePurchasePriceRel->SupplierArtText;
				$this->ArticleDescription2 = "";
				$this->ShopArticle = 9;
				$this->PrimArtGr = $mySupplier->SupplierPrefix;
				$this->assignERPNo();
				$this->EinzelSeite = 1;
				$this->Margin = 1.0;
				$this->MarginMinQ = 1.0;
				$this->GenPflicht = 1;
				$this->AutoPrice = 1;
				$this->Markt = "all";
				$this->ErfDatum = $this->today();
				$this->TaxClass = "A";
				$this->BildRef = $mySupplier->SupplierPrefix . "/" . str_replace( "/", "_", $newArticleNo ) . ".jpg";
				$this->storeInDb();
				
				$myArtTexte = new ArtTexte();
				$myArtTexte->ArticleNo = $this->ArticleNo;
				$myArtTexte->Sprache = "de";
				$myArtTexte->ArticleDescription1 = $this->ArticleDescription1;
				$myArtTexte->ArticleDescription2 = $this->ArticleDescription2;
				$myArtTexte->QuantityText = $this->QuantityText;
				$myArtTexte->storeInDb();
				
				$myArticleStock = new ArticleStock();
				$myArticleStock->ArticleNo = $this->ArticleNo;
				$myArticleStock->storeInDb();
				
				$myArticleSalesPrice = new ArticleSalesPrice();
				$myArticleSalesPrice->ArticleNo = $this->ArticleNo;
				$myArticleSalesPrice->ValidFrom = "2000-01-01";
				$myArticleSalesPrice->ValidUntil = "2000-01-01";
				$myArticleSalesPrice->Quantity = 1;
				$myArticleSalesPrice->QuantityPerPU = $myArticlePurchasePriceRel->QuantityPerPU;
				$myArticleSalesPrice->storeInDb();
				
			} else {
				throw new Exception( "Article::createFromArticlePurchasePriceRelId(): ArticlePurchasePriceRel with Id := [$_id] does not exist" );
			}
		} catch ( Exception $e ) {
			throw $e;
		}
		return $this->getXMLString();
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function assignERPNo( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::dumpL( 0x00000001, "Article.php::Article::assignERPNo( '$_key', $_id, '$_val'): begin" );
		try {
			$myArtGr = new ArtGr( $this->PrimArtGr );
			if ( $myArtGr->isValid() ) {
				$erpNoStart = $myArtGr->ERPNoStart;
				$erpNoEnd = $myArtGr->ERPNoEnd;
				if ( $this->ERPNo >= $erpNoStart && $this->ERPNo <= $erpNoEnd ) {
					$e = new Exception( "Article.php::Article::assignERPNo(...): ERP no. already in valid range! No new no. assigned!" );
					error_log( $e );
					throw $e;
				}
				$myQuery = "SELECT IFNULL(( SELECT ERPNo + 1 FROM Article " . "ERPNo >= '$erpNoStart' AND ERPNo <= '$erpNoEnd' " . "ORDER BY ERPNo DESC LIMIT 1 ), " . ( $erpNoStart + 1 ) . " )  AS newKey";
				$myRow = FDb::queryRow( $myQuery );
				$this->ERPNo = sprintf( "%08s", $myRow[ 'newKey' ] );
			} else {
				$e = new Exception( "Article.php::Article::assignERPNo(...): article group '" . $this->PrimArtGr . "' not valid!" );
				error_log( $e );
				throw $e;
			}
		} catch ( Exception $e ) {
			throw $e;
		}
		FDbg::dumpL( 0x00000001, "Article.php::Article::assignERPNo( '$_key', $_id, '$_val'): end" );
		return true;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function createFromArticlePurchasePriceId( $_key, $_id, $_val ) {
		try {
			$myArticlePurchasePrice = new ArticlePurchasePrice();
			$myArticlePurchasePrice->setId( $_id );
			if ( ! $myArticlePurchasePrice->isValid() ) {
				throw new Exception( "Article::createFromArticlePurchasePriceId(): ArticlePurchasePrice with Id := [$_id] does not exist" );
			}
			$mySupplier = new Supplier( $myArticlePurchasePrice->SupplierNo );
			if ( ! $mySupplier->isValid() ) {
				throw new Exception( "Article::createFromArticlePurchasePriceId(): Supplier with SupplierNo := [$mySupplier->SupplierNo] does not exist" );
			}
			$myArticlePurchasePriceRel = new ArticlePurchasePriceRel;
			if ( $mySupplier->UsePrefixInArtNo == 1 ) {
				if ( strlen( $mySupplier->SupplierPrefix ) != 3 ) {
					throw new Exception( "Article::createFromArticlePurchasePriceId(): Supplier with SupplierNo := [$mySupplier->SupplierNo] does not have valid SupplierPrefix" );
				}
				$newArticleNo = $mySupplier->SupplierPrefix . "." . Article::stripArtNr( $myArticlePurchasePrice->SupplierArticleNo );
			} else {
				$newArticleNo = Article::stripArticleNo( $myArticlePurchasePrice->SupplierArticleNo );
			}
			$myArticlePurchasePriceRel->ArticleNo = $newArticleNo;
			$myArticlePurchasePriceRel->SupplierNo = $myArticlePurchasePrice->SupplierNo;
			$myArticlePurchasePriceRel->SupplierArticleNo = $myArticlePurchasePrice->SupplierArticleNo;
			$myArticlePurchasePriceRel->SupplierArtText = $myArticlePurchasePrice->SupplierArtText;
			$myArticlePurchasePriceRel->CalculationBase = $myArticlePurchasePrice->Quantity;
			$myArticlePurchasePriceRel->MKF = 1;
			$myArticlePurchasePriceRel->Margin = 1.0;
			$myArticlePurchasePriceRel->QuantityPerPU = 1;
			$myArticlePurchasePriceRel->storeInDb();
			/**
			 * @var unknown_type
			 */
			$this->ArticleNo = $newArticleNo;
			$this->ArticleDescription1 = $myArticlePurchasePriceRel->SupplierArtText;
			$this->ArticleDescription2 = "";
			$this->ShopArticle = 9;
			$this->PrimArtGr = $mySupplier->SupplierPrefix;
			$this->assignERPNo();
			$this->EinzelSeite = 1;
			$this->Margin = 1.0;
			$this->MarginMinQ = 1.0;
			$this->GenPflicht = 1;
			$this->AutoPrice = 1;
			$this->Markt = "all";
			$this->ErfDatum = $this->today();
			$this->TaxClass = "A";
			$this->BildRef = $mySupplier->SupplierPrefix . "/" . str_replace( "/", "_", $newArticleNo ) . ".jpg";
			$this->storeInDb();
			/**
			 * now that we have a complete article we can calulate our ow selling price
			 */
			$myArticlePurchasePrice->_calcOwnVK( 1.0 );
			/**
			 *
			 * @var unknown_type
			 */
			$myArtTexte = new ArtTexte();
			$myArtTexte->ArticleNo = $newArticleNo;
			$myArtTexte->Sprache = "de";
			$myArtTexte->ArticleDescription1 = $this->ArticleDescription1;
			$myArtTexte->ArticleDescription2 = $this->ArticleDescription2;
			$myArtTexte->QuantityText = $this->QuantityText;
			$myArtTexte->storeInDb();
			/**
			 * create the other languages
			 * @var unknown_type
			 */
			$myArtTexte->Sprache = "en";
			$myArtTexte->storeInDb();
			$myArtTexte->Sprache = "fr";
			$myArtTexte->storeInDb();
			$myArtTexte->Sprache = "es";
			$myArtTexte->storeInDb();
			/**
			 *
			 * @var unknown_type
			 */
			$myArticleStock = new ArticleStock();
			$myArticleStock->ArticleNo = $newArticleNo;
			$myArticleStock->storeInDb();
			
			$myArticleSalesPrice = new ArticleSalesPrice();
			$myArticleSalesPrice->ArticleNo = $newArticleNo;
			$myArticleSalesPrice->ValidFrom = "2000-01-01";
			$myArticleSalesPrice->ValidUntil = "2000-01-01";
			$myArticleSalesPrice->Quantity = 1;
			$myArticleSalesPrice->QuantityPerPU = $myArticlePurchasePriceRel->QuantityPerPU;
			$myArticleSalesPrice->storeInDb();
		} catch ( Exception $e ) {
			throw $e;
		}
		return $this->getXMLString();
	}
	
	/**
	 *
	 * @param unknown_type $inStr
	 */
	static function stripArtNr( $inStr ) {
		$retStr = "";
		for ( $i = 0 ; $i < strlen( $inStr ) ; $i ++ ) {
			if ( ord( $inStr[ $i ] ) >= 0 && ord( $inStr[ $i ] ) < 127 ) {
				switch ( ord( $inStr[ $i ] ) ) {
				case    ord( " " )    :
				case    ord( "*" )    :
				case    ord( "%" )    :
				case    ord( "&" )    :
				case    ord( "#" )    :
				case    ord( "{" )    :
				case    ord( "}" )    :
				case    ord( "[" )    :
				case    ord( "}" )    :
				case    ord( "!" )    :
				case    ord( "@" )    :
				case    ord( "|" )    :
				case    ord( "$" )    :
				case    ord( "^" )    :
					//				case	ord( ".")	:
				case    ord( "," )    :
				case    ord( "<" )    :
				case    ord( ">" )    :
					//				case	ord( "/")	:
				case    ord( "?" )    :
				case    ord( ";" )    :
				case    ord( ":" )    :
				case    ord( "'" )    :
				case    ord( "\"" )    :
				case    ord( "\\" )    :
				case    ord( "`" )    :
				case    ord( "~" )    :
				case    ord( "+" )    :
				case    ord( "=" )    :
				break;
				default    :
					$retStr .= $inStr[ $i ];
				break;
				}
			}
		}
		return $retStr;
	}
	
	/**
	 * Vollst�ndigen Articletext, bestend aus den beiden Articlebezeichnungen
	 * sowie dem opt. Quantitytext, ggf. basierend auf [$_menge], abfragen.
	 *
	 * Die einzelnen Bestandteile des Articletextes werden, falls vorhanden
	 * �ber "\n" getrennt an einander gehangen und als einzelner Textstring
	 * zur�ckgeliefert.
	 *
	 * @return string
	 */
	function getFullTextLF( $_menge ) {
		
		$buf = $this->ArticleDescription1;
		if ( strlen( $this->ArticleDescription2 ) > 0 ) {
			$buf .= "\n" . $this->ArticleDescription2;
		}
		if ( strlen( $this->QuantityText ) > 0 ) {
			$buf .= "\n" . $this->QuantityText;
		} else {
			if ( $_menge > 1 ) {
				$buf .= "\n" . $this->textFromQuantity( $_menge );
			}
		}
		return $buf;
	}
	
	/**
	 * Reservieren der angegebenen [_menge] des Articles im Lager.
	 *
	 * Diese Funktion reserviert die angegebene Quantity [_menge] des
	 * Articles im Lager mit der Lagerbezeichnung [_stockId=""].
	 * Die Funktion ruft die SQL Stored Procedure Article_reserviere auf.
	 *
	 * @param int $_menge Quantity der Articles
	 * @param string $_stockId Id des Lagerortes
	 * @return int Ergebnis der SQL Stored Prcedure
	 */
	function reserve( $_menge, $_stockId = '' ) {
		$qtyBooked = 0;
		while ( $this->ArticleNoNew != "" ) {
			//			error_log( "Article.php::order2(): [".++$lc."] going to new article no. " . $this->ArticleNoNew) ;
			$this->setArticleNo( $this->ArticleNoNew );
		}
		switch ( $this->Comp ) {
		case     0    :            // simple item
			$actArticleStock = new ArticleStock();
			//
			// as we want to reserve the item which is stocked, we have to use this article number instead
			//
			$actArticleStock->ArticleNo = $this->ArticleNoStock;
			if ( $actArticleStock->getDefault() ) {
				$actArticleStock->reserve( $_menge );
				$qtyBooked = $_menge;
			} else {
				$e = new Exception( "Article.php::Article::reserve(...): no default stock for Article['$this->ArticleNo']!" );
				throw $e;
			}
		break;
		case    20    :            // rubber stopper with whole
		case    30    :            // PASCO experiment
		break;
		default    :
		break;
		}
		return $qtyBooked;
	}
	
	/**
	 * Reservieren der angegebenen [_menge] des Articles im Lager.
	 *
	 * Diese Funktion reserviert die angegebene Quantity [_menge] des
	 * Articles im Lager mit der Lagerbezeichnung [_stockId=""].
	 * Die Funktion ruft die SQL Stored Procedure Article_reserviere auf.
	 *
	 * @param int $_menge Quantity der Articles
	 * @param string $_stockId Id des Lagerortes
	 * @return int Ergebnis der SQL Stored Prcedure
	 */
	function unreserve( $_menge, $_stockId = '' ) {
		$qtyBooked = 0;
		while ( $this->ArticleNoNew != "" ) {
			//			error_log( "Article.php::order2(): [".++$lc."] going to new article no. " . $this->ArticleNoNew) ;
			$this->setArticleNo( $this->ArticleNoNew );
		}
		switch ( $this->Comp ) {
		case     0    :            // simple item
		case    20    :            // rubber stopper with whole
		case    30    :            // PASCO experiment
			$actArticleStock = new ArticleStock();
			$actArticleStock->ArticleNo = $this->ArticleNo;
			if ( $actArticleStock->getDefault() ) {
				$actArticleStock->unreserve( $_menge );
				$qtyBooked = $_menge;
			} else {
				error_log( "Article::unreserve(...): No default stock for Article['$this->ArticleNo'] " );
				throw new Exception( "Article::unreserve(...): No default stock for Article['$this->ArticleNo'] " );
			}
		break;
		case    10    :            // disection set
		break;
		default    :
		break;
		}
		return $qtyBooked;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function commission( $_menge, $_stockId = '' ) {
		$lc = 0;
		$qtyOrdered = 0;
		while ( $this->ArticleNoNew != "" ) {
			$this->setArticleNo( $this->ArticleNoNew );
		}
		$actArticleStock = new ArticleStock();
		$actArticleStock->getDefault( $this->ArticleNo );
		if ( $actArticleStock->isValid() ) {
			$actArticleStock->commission( $_menge );
			$qtyOrdered = $_menge;
		} else {
			error_log( "Article::commission(...): No default stock for Article['$this->ArticleNo'] " );
			throw new Exception( "Article::commission(...): No default stock for Article['$this->ArticleNo'] " );
		}
		return $qtyOrdered;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function uncommission( $_menge, $_stockId = '' ) {
		$lc = 0;
		$qtyOrdered = 0;
		while ( $this->ArticleNoNew != "" ) {
			$this->setArticleNo( $this->ArticleNoNew );
		}
		$actArticleStock = new ArticleStock();
		$actArticleStock->getDefault( $this->ArticleNo );
		if ( $actArticleStock->isValid() ) {
			$actArticleStock->uncommission( $_menge );
			$qtyOrdered = $_menge;
		} else {
			$e = new Exception( "Article.php::Article::deliver(...): no default stock for Article['$this->ArticleNo']!" );
			errorLog( $e );
			throw $e;
		}
		return $qtyOrdered;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function deliver( $_menge, $_stockId = '' ) {
		$lc = 0;
		$qtyDelivered = 0;
		if ( $this->BestArt > 0 ) {
			while ( $this->ArticleNoNew != "" ) {
				$this->setArticleNo( $this->ArticleNoNew );
			}
			switch ( $this->Comp ) {
			case     0    :            // simple item
				$actArticleStock = new ArticleStock();
				//
				// as we want to reserve the item which is stocked, we have to use this article number instead
				//
				$actArticleStock->ArticleNo = $this->ArticleNoStock;
				if ( $actArticleStock->getDefault() ) {
					$actArticleStock->deliver( $_menge );
					$qtyDelivered = $_menge;
				} else {
					$e = new Exception( "Article.php::Article::deliver(...): no default stock for Article['$this->ArticleNo']!" );
					error_log( $e );
					throw $e;
				}
			break;
			case    10    :
			case    20    :            // rubber stopper with whole
			case    30    :            // PASCO experiment
			break;
			default    :
				$e = new Exception( "Article.php::Article::deliver(...): invalid 'Comp' for Article['$this->ArticleNo']!" );
				error_log( $e );
				throw $e;
			break;
			}
		}
		return $qtyDelivered;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function undeliver( $_menge, $_stockId = '' ) {
		$lc = 0;
		$qtyDelivered = 0;
		while ( $this->ArticleNoNew != "" ) {
			$this->setArticleNo( $this->ArticleNoNew );
		}
		$actArticleStock = new ArticleStock();
		$actArticleStock->getDefault( $this->ArticleNo );
		if ( $actArticleStock->isValid() ) {
			$actArticleStock->undeliver( $_menge );
			$qtyDelivered = $_menge;
		} else {
			$e = new Exception( "Article.php::Article::deliver(...): no default stock for Article['$this->ArticleNo']!" );
			errorLog( $e );
			throw $e;
		}
		return $qtyDelivered;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function order( $_menge, $_stockId = '' ) {
		$lc = 0;
		$qtyOrdered = 0;
		while ( $this->ArticleNoNew != "" ) {
			//			error_log( "Article.php::order2(): [".++$lc."] going to new article no. " . $this->ArticleNoNew) ;
			$this->setArticleNo( $this->ArticleNoNew );
		}
		$actArticleStock = new ArticleStock();
		$actArticleStock->getDefault( $this->ArticleNo );
		if ( $actArticleStock->isValid() ) {
			$actArticleStock->order( $_menge );
			$qtyOrdered = $_menge;
		} else {
			error_log( "Article::order(...): No default stock for Article['$this->ArticleNo'] " );
			throw new Exception( "Article::order(...): No default stock for Article['$this->ArticleNo'] " );
		}
		//		error_log( "Article.php::order2(): ordered ".$qtyOrdered." pc(s). of article no. " . $this->ArticleNo) ;
		return $qtyOrdered;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function unorder( $_menge, $_stockId = '' ) {
		$lc = 0;
		$qtyOrdered = 0;
		while ( $this->ArticleNoNew != "" ) {
			//			error_log( "Article.php::unorder(): [".++$lc."] going to new article no. " . $this->ArticleNoNew) ;
			$this->setArticleNo( $this->ArticleNoNew );
		}
		$actArticleStock = new ArticleStock();
		$actArticleStock->getDefault( $this->ArticleNo );
		if ( $actArticleStock->isValid() ) {
			$actArticleStock->unorder( $_menge );
			$qtyOrdered = $_menge;
		} else {
			error_log( "Article::unorder(...): No default stock for Article['$this->ArticleNo'] " );
			throw new Exception( "Article::unorder(...): No default stock for Article['$this->ArticleNo'] " );
		}
		//		error_log( "Article.php::unorder(): ordered ".$qtyOrdered." pc(s). of article no. " . $this->ArticleNo) ;
		return $qtyOrdered;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function receive( $_menge, $_stockId = '', $_ordered = true, $_stock = true ) {
		$lc = 0;
		$qtyReceived = 0;
		while ( $this->ArticleNoNew != "" ) {
			$this->setArticleNo( $this->ArticleNoNew );
		}
		$actArticleStock = new ArticleStock();
		$actArticleStock->getDefault( $this->ArticleNo );
		if ( $actArticleStock->isValid() ) {
			$actArticleStock->receive( $_menge, $_ordered, $_stock );
			$qtyReceived = $_menge;
		} else {
			error_log( "Article::receive(...): No default stock for Article['$this->ArticleNo'] " );
			throw new Exception( "Article::receive(...): No default stock for Article['$this->ArticleNo'] " );
		}
		return $qtyReceived;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function unreceive( $_menge, $_stockId = '', $_ordered = true, $_stock = true ) {
		$lc = 0;
		$qtyOrdered = 0;
		while ( $this->ArticleNoNew != "" ) {
			$this->setArticleNo( $this->ArticleNoNew );
		}
		$actArticleStock = new ArticleStock();
		$actArticleStock->getDefault( $this->ArticleNo );
		if ( $actArticleStock->isValid() ) {
			$actArticleStock->unreceive( $_menge, $_ordered, $_stock );
			$qtyOrdered = $_menge;
		} else {
			error_log( "Article::unreceive(...): No default stock for Article['$this->ArticleNo'] " );
			throw new Exception( "Article::unreceive(...): No default stock for Article['$this->ArticleNo'] " );
		}
		return $qtyOrdered;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function correct( $_menge, $_stockId = '' ) {
		FDbg::dumpL( 0x00000100, "Article.php::Article::correct( $_menge, '$_stockId'): begin" );
		$lc = 0;
		$qtyCorrected = 0;
		$actArticleStock = new ArticleStock();
		$actArticleStock->getDefault( $this->ArticleNo );
		if ( $actArticleStock->isValid() ) {
			$actArticleStock->correct( $_menge );
			$qtyCorrected = $_menge;
		} else {
			$e = new Exception( "Article.php::Article::correct(...): No default stock for Article['$this->ArticleNo'] " );
			throw $e;
		}
		FDbg::dumpL( 0x00000100, "Article.php::Article::correct( $_menge, '$_stockId'): end" );
		return $qtyCorrected;
	}
	
	/**
	 * @param    int $_menge
	 * @param    string $_stockId
	 */
	function uncorrect( $_menge, $_stockId = '' ) {
		FDbg::dumpL( 0x00000100, "Article.php::Article::uncorrect( $_menge, '$_stockId'): begin" );
		$lc = 0;
		$qtyCorrected = 0;
		$actArticleStock = new ArticleStock();
		$actArticleStock->getDefault( $this->ArticleNo );
		if ( $actArticleStock->isValid() ) {
			$actArticleStock->uncorrect( $_menge );
			$qtyCorrected = $_menge;
		} else {
			$e = new Exception( "Article.php::Article::uncorrect(...): No default stock for Article['$this->ArticleNo'] " );
			throw $e;
		}
		FDbg::dumpL( 0x00000100, "Article.php::Article::uncorrect( $_menge, '$_stockId'): end" );
		return $qtyCorrected;
	}
	
	/**
	 * #
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function renumArticle( $_key = "", $_id = - 1, $_val = "" ) {
		$newArticleNo = $_POST[ '_IArticleNoNew' ];;
		if ( strlen( $newArticleNo ) >= 7 ) {
			$count = FDb::getCount( "FROM $this->className ArticleNo = '$newArticleNo' " );
			if ( $count != 0 ) {
				throw new Exception( "Article.php::Article::renumArticle( '$_key', $_id, '$_val'): new article no. already in use! [#:$count]" );
			}
			try {
				FDb::query( "UPDATE AbKorrPosten SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArtBild SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArtEmpf SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArtEmpf SET EmpfArticleNo = '$newArticleNo' EmpfArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArtGrComp SET CompArtNr = '$newArticleNo' CompArtNr = '$this->ArticleNo' " );
				FDb::query( "UPDATE ProdGrComp SET CompArtNr = '$newArticleNo' CompArtNr = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArticleComponent SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArticleComponent SET CompArticleNo = '$newArticleNo' CompArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArtTexte SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArticleStock SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArticleUmsatz SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				//				FDb::query( "UPDATE EKDaten SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
				//				FDb::query( "UPDATE EKPrice SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "UPDATE ArticlePurchasePriceRel SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE InKonf SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE InKonfPosten SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE InvItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuRFQItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuOffrItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuOrdrItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE KdGutsPosten SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuCommItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE KdLeihPosten SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuDlvrItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuInvcItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE SuOrdrItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE SuDlvrItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuCartItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE PCuOrdrItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE SerNo SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE PCuOrdrItem SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				//				FDb::query( "UPDATE TLabel SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
				//				FDb::query( "UPDATE TmpLabels SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
				//				FDb::query( "UPDATE VKDaten SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "UPDATE ArticleSalesPrice SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArticleSalesPriceCache SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				//				FDb::query( "UPDATE ArticleSalesPricee SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' ") ;
				FDb::query( "UPDATE Article SET ArticleNoNew = '$newArticleNo' ArticleNoNew = '$this->ArticleNo' " );
				FDb::query( "UPDATE Article SET ArticleNoReplacement = '$newArticleNo' ArticleNoReplacement = '$this->ArticleNo' " );
				FDb::query( "UPDATE Article SET ArticleNoStock = '$newArticleNo' ArticleNoStock = '$this->ArticleNo' " );
				FDb::query( "UPDATE Article SET ArticleNoOld = '$this->ArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE Article SET ArticleNo = '$newArticleNo' ArticleNo = '$this->ArticleNo' " );
				$this->ArticleNo = $newArticleNo;
				$this->reload();
			} catch ( Exception $e ) {
				error_log( "Article.php::Article::renumArticle( '$_key', $_id, '$_val'): exception '" . $e->getMessage() . "'!" );
			}
		} else {
			throw new Exception( "Article.php::Article::renumArticle( '$_key', $_id, '$_val'): new article no. is too short!" );
		}
		return $this->getXMLComplete();
	}
	
	/**
	 *
	 * @param string $_key article no.
	 * @param int $_id un-usewd
	 * @param sting $_val un-used
	 */
	function moveArticle( $_key = "", $_id = - 1, $_val = "" ) {
		try {
			$this->_reRefArticle( $_POST[ '_IArticleNoNew' ] );
		} catch ( Exception $e ) {
			throw $e;
		}
		$this->reload();
		return $this->getXMLComplete();
	}
	
	/**
	 * #
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function _reRefArticle( $_newArticleNo ) {
		error_log( "Article.php::Article::_reRefArticle( '$_newArticleNo'): begin" );
		if ( strlen( $_newArticleNo ) >= 7 ) {
			$count = FDb::getCount( "FROM $this->className ArticleNo = '$_newArticleNo' " );
			if ( $count != 1 ) {
				$e = new Exception( "Article.php::Article::_reRefArticle( '$_newArticleNo'): new article no. does not exist! [#:$count]" );
				error_log( $e );
				throw $e;
			}
			try {
				FDb::query( "UPDATE AbKorrPosten SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArticleComponent SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArticleComponent SET CompArticleNo = '$_newArticleNo' CompArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE ArtTexte SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE InKonf SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE InKonfPosten SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE InvItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuRFQItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuOffrItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuOrdrItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE KdGutsPosten SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuCommItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE KdLeihPosten SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuDlvrItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuInvcItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE SuOrdrItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE SuDlvrItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE CuCartItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE PCuOrdrItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE SerNo SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				FDb::query( "UPDATE PCuOrdrItem SET ArticleNo = '$_newArticleNo' ArticleNo = '$this->ArticleNo' " );
				$this->ArticleNoNew = $_newArticleNo;
				$this->updateColInDb( "ArticleNoNew" );
			} catch ( Exception $e ) {
				throw $e;
			}
		} else {
			$e = new Exception( "Article.php::Article::_reRefArticle( '$_newArticleNo'): new article no. is too short!" );
			error_log( $e );
			throw $e;
		}
		error_log( "Article.php::Article::_reRefArticle( '$_newArticleNo'): end" );
	}
	
	/**
	 *
	 * @param $_articleNo
	 * @param $_id
	 * @param $_val
	 */
	function detCompArticlePrice( $_key = "", $_id = - 1, $_val = "" ) {
		Article::_detCompArticlePrice( $_key );
		return $this->getXMLComplete();
	}
	
	/**
	 *
	 * @param string $_key article no.
	 * @param int $_id id of the purchasing price
	 * @param sting $_val un-used
	 */
	static function _detCompArticlePrice( $_articleNo = "" ) {
		FDbg::dumpL( 0x00000001, "Article.php::Article::detCompArticlePrice( '$_articleNo'): begin" );
		$actArticle = new Article( $_articleNo );
		if ( $actArticle->CompositionType > 0 ) {
			try {
				$actArticlePurchasePriceRel = new ArticlePurchasePriceRel();
				$actArticlePurchasePrice = new ArticlePurchasePrice();
				/**
				 * find the relation to the purchasing details
				 * find the relation to the purchasing details
				 */
				$actArticlePurchasePriceRel->fetchFromDbWhere( "ArticleNo = '$_articleNo' AND CalculationBase > 0" );
				$actArticlePurchasePrice->fetchFromDbWhere( "SupplierNo = '$actArticlePurchasePriceRel->SupplierNo' AND SupplierArticleNo = '$actArticlePurchasePriceRel->SupplierArticleNo' AND Quantity = $actArticlePurchasePriceRel->CalculationBase " );
				/**
				 *
				 * @var unknown_type
				 */
				$sumMSRP = 0.0;        // sum of MSRPs of the components
				$sumPP = 0.0;            // sum of the purchasing prices in the calculation base pricing
				$sumOwnArticleSalesPrice = 0.0;            // sum of the purchasing prices in the calculation base pricing
				$subArticle = new Article();
				$myArticleComponent = new ArticleComponent();
				$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
				$myArticlePurchasePrice = new ArticlePurchasePrice();
				$myArticleComponent->_firstFromDb( "ArticleNo='$_articleNo' " );
				while ( $myArticleComponent->isValid() ) {
					FDbg::dumpL( 0x00000002, "Article.php::Article::_detCompArticlePrice(...): :=> ArticleComponent.ArticleNo = $myArticleComponent->CompArticleNo, ArticleComponent.CompQuantity = $myArticleComponent->CompQuantity" );
					$subArticle->setArticleNo( $myArticleComponent->CompArticleNo );
					/**
					 * find the article which we needs to add
					 * means: as long as the article has a new article associated or as there is a
					 * replacement article, go to this
					 * article
					 */
					while ( strlen( $subArticle->ArticleNoNew ) > 0 || strlen( $subArticle->ArticleNoReplacement ) > 0 ) {
						if ( strlen( $subArticle->ArticleNoNew ) > 0 ) {
							$subArticle->setArticleNo( $subArticle->ArticleNoNew );
						} else {
							if ( strlen( $subArticle->ArticleNoReplacement ) > 0 ) {
								$subArticle->setArticleNo( $subArticle->ArticleNoReplacement );
							}
						}
					}
					if ( $subArticle->CompositionType > 0 ) {
						Article::_detCompArticlePrice( $subArticle->ArticleNo );
					}
					/**
					 * find the relation to the purchasing details
					 */
					$myArticlePurchasePriceRel->fetchFromDbWhere( "ArticleNo = '$myArticleComponent->CompArticleNo' AND CalculationBase > 0" );
					FDbg::dumpL( 0x00000002, "Article.php::Article::_detCompArticlePrice(...): :=> ArticlePurchasePriceRel.SupplierNo = $myArticlePurchasePriceRel->SupplierNo, ArticlePurchasePriceRel.SupplierArticleNo = $myArticlePurchasePriceRel->SupplierArticleNo" );
					/**
					 * find the relation to the purchasing details
					 */
					$myArticlePurchasePrice->fetchFromDbWhere( "SupplierNo = '$myArticlePurchasePriceRel->SupplierNo' " . "AND SupplierArticleNo = '$myArticlePurchasePriceRel->SupplierArticleNo' " . "AND Quantity = $myArticlePurchasePriceRel->CalculationBase " . "AND ValidFrom <= '" . $myArticlePurchasePriceRel->today() . "' " . "AND '" . $myArticlePurchasePriceRel->today() . "' < ValidUntil " );
					if ( ! $myArticlePurchasePrice->isValid() ) {
						$myArticlePurchasePrice->fetchFromDbWhere( "SupplierNo = '$myArticlePurchasePriceRel->SupplierNo' " . "AND SupplierArticleNo = '$myArticlePurchasePriceRel->SupplierArticleNo' " . "AND Quantity = $myArticlePurchasePriceRel->CalculationBase " . "AND ValidFrom <= '" . $myArticlePurchasePriceRel->today( - 1 * 365 * 24 * 60 * 60 ) . "' " . "AND '" . $myArticlePurchasePriceRel->today( - 1 * 365 * 24 * 60 * 60 ) . "' <= ValidUntil " );
					}
					FDbg::dumpL( 0x00000002, "Article.php::Article::_detCompArticlePrice(...): :=> ArticlePurchasePriceRel.SupplierNo = $myArticlePurchasePrice->SupplierNo, ArticlePurchasePrice.SupplierArticleNo = $myArticlePurchasePrice->SupplierArticleNo, Price = $myArticlePurchasePrice->Price " );
					$sumPP += $myArticleComponent->CompQuantity * $myArticlePurchasePrice->Price;
					$sumMSRP += $myArticleComponent->CompQuantity * $myArticlePurchasePrice->SupplierArticleSalesPrice;
					$sumOwnArticleSalesPrice += $myArticleComponent->CompQuantity * $myArticlePurchasePrice->OwnArticleSalesPrice;
					FDbg::dumpL( 0x00000002, "Article.php::Article::_detCompArticlePrice(...): :=> sumPP = $sumPP, sumMSRP = $sumMSRP, sumOwnArticleSalesPrice = $sumOwnArticleSalesPrice " );
					/**
					 * get next component
					 */
					$myArticleComponent->_nextFromDb();
				}
				/**
				 *
				 */
				$actArticlePurchasePrice->Price = $sumPP;
				$actArticlePurchasePrice->SupplierArticleSalesPrice = $sumMSRP;
				$actArticlePurchasePrice->OwnArticleSalesPrice = $sumOwnArticleSalesPrice * $actArticle->Margin * $actArticlePurchasePriceRel->Margin;
				$actArticlePurchasePrice->updateInDb();
			} catch ( Exception $e ) {
				throw $e;
			}
		} else {
			$e = new Exception( "Article.php::Article::detCompArticlePrice( '$_articleNo'): article is not a composite article" );
			throw $e;
		}
		FDbg::dumpL( 0x00000001, "Article.php::Article::detCompArticlePrice( '$_articleNo'): end" );
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return unknown
	 */
	function setStockLocationForArticle( $_key = "", $_id = - 1, $_val = "" ) {
		error_log( "Article.php::Article::setStockLocationForArticle( $_key, $_id,$_val" );
		$myArticleStock = new ArticleStock();
		$myArticleStock->getDefault( $this->ArticleNo );
		$myArticleStock->LagerOrt = $_val;
		$myArticleStock->updateColInDb( "LagerOrt" );
		$ret = $this->getTableArticleStockAsXML();
		return $ret;
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function getArtBildAsXML( $_key = "", $_id = - 1, $_val = "" ) {
		$ret = "";
		$myArtBild = new ArtBild();
		$myArtBild->setId( $_id );
		$ret .= $myArtBild->getXMLF();
		return $ret;
	}
	
	/**
	 *
	 */
	function getAsXML( $_key = "", $_id = - 1, $_val = "", $_reply = null ) {
		FDbg::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "( '$_key', $_id, '$_val')" );
		if ( $_reply == null ) {
			$_reply = new Reply( __class__, $this->className );
		} else {
			$_reply->instClass = __class__;
			$_reply->replyingClass = $this->className;
		}
		if ( $_val == "ArticleText" ) {
			$this->getDepAsXML( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "Attribute" ) {
			$this->getDepAsXML( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticleComponent" ) {
			$this->getDepAsXML( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticleStock" ) {
			$this->getDepAsXML( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticleSalesPrice" ) {
			$this->getDepAsXML( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticlePurchasePriceRel" ) {
			$this->getDepAsXML( $_key, $_id, $_val, $_reply );
		} else if ( $_val == "ArticlePurchasePrice" ) {
			$this->getDepAsXML( $_key, $_id, $_val, $_reply );
		} else {
			$_reply->replyData .= $this->getXMLF();
		}
		FDbg::end();
		return $_reply;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getDepAsXML()
	 */
	function getDepAsXML( $_key = "", $_id = - 1, $_val = "", $_reply = null ) {
		FDbg::begin( 1, "Article.php", "Article", "getDepAsXML( '$_key', $_id, '$_val', <reply>)" );
		if ( $_reply == null ) {
			$_reply = new Reply( __class__, $this->className );
		}
		$objName = $_val;
		switch ( $objName ) {
		case    "ArticleText"    :
			$myArticleText = new ArticleText();
			if ( $_id == - 1 ) {
				$myArticleText->Id = - 1;
			} else {
				$myArticleText->setId( $_id );
			}
			$_reply->replyData = $myArticleText->getXMLF();
			break;
		case    "Attribute"    :
			$myAttribute = new Attribute();
			if ( $_id == - 1 ) {
				$myAttribute->first( "DataTable = 'Article' AND RefNo = '".$this->ArticleNo."'", "ItemNo DESC" ) ;
				$myItemNo	=	$myAttribute->ItemNo ;
				$myAttribute->clear() ;
				$myAttribute->DataTable = "Article";
				$myAttribute->RefNo = $this->ArticleNo;
				$myAttribute->ItemNo = $myItemNo + 10;
			} else {
				$myAttribute->setId( $_id );
			}
			$_reply->replyData = $myAttribute->getXMLF();
			break;
		case    "ArticleComponent"    :
			$myArticleComponent = new ArticleComponent();
			if ( $_id == - 1 ) {
				$myArticleComponent->first( "ArticleNo = '".$this->ArticleNo."'", "ItemNo DESC" ) ;
				$myItemNo	=	$myArticleComponent->ItemNo ;
				$myArticleComponent->clear() ;
				$myArticleComponent->ArticleNo = $this->ArticleNo;
			} else {
				$myArticleComponent->setId( $_id );
			}
			$_reply->replyData = $myArticleComponent->getXMLF();
			break;
		case    "ArticleStock"    :
			$myArticleStock = new ArticleStock();
			if ( $_id == - 1 ) {
				$myArticleStock->first( "ArticleNo = '".$this->ArticleNo."'", "ItemNo DESC" ) ;
				$myItemNo	=	$myArticleStock->ItemNo ;
				$myArticleStock->clear() ;
				$myArticleStock->ArticleNo = $this->ArticleNo;
			} else {
				$myArticleStock->setId( $_id );
			}
			$_reply->replyData = $myArticleStock->getXMLF();
			break;
		case    "ArticleSalesPrice"    :
			$myArticleSalesPrice = new ArticleSalesPrice();
			if ( $_id == - 1 ) {
				$myArticleSalesPrice->first( "ArticleNo = '".$this->ArticleNo."'", "ItemNo DESC" ) ;
				$myItemNo	=	$myArticleSalesPrice->ItemNo ;
				$myArticleSalesPrice->clear() ;
				$myArticleSalesPrice->ArticleNo = $this->ArticleNo;
			} else {
				$myArticleSalesPrice->setId( $_id );
			}
			$_reply->replyData = $myArticleSalesPrice->getXMLF();
			break;
		case    "ArticlePurchasePriceRel"    :
			$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
			if ( $_id == - 1 ) {
				$myArticlePurchasePriceRel->clear() ;
				$myArticlePurchasePriceRel->ArticleNo = $this->ArticleNo;
			} else {
				$myArticlePurchasePriceRel->setId( $_id );
			}
			$_reply->replyData = $myArticlePurchasePriceRel->getXMLF();
			break;
		case    "ArticlePurchasePrice"    :
			$myArticlePurchasePrice = new ArticlePurchasePrice();
			if ( $_id == - 1 ) {
				$myArticlePurchasePrice->clear() ;
				$myArticlePurchasePrice->ArticleNo = $this->ArticleNo;
			} else {
				$myArticlePurchasePrice->setId( $_id );
			}
			$_reply->replyData = $myArticlePurchasePrice->getXMLF();
			break;
		case    "ArtQPC"    :
			$myArtQPC = new ArtQPC();
			if ( $_id == - 1 ) {
				$myArtQPC->ArticleNo = $this->ArticleNo;
				$myArtQPC->QPC = 1;
			} else {
				$myArtQPC->setId( $_id );
			}
			return $myArtQPC->getXMLString();
		break;
		default    :
			$reply = parent::getDepAsXML( $_key, $_id, $_val, $reply );
		break;
		}
		return $_reply;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return Ambigous <boolean, string>
	 */
	function addArtBild( $_key = "", $_id = - 1, $_val = "" ) {
		$myArtBild = new ArtBild();
		$myArtBild->ArticleNo = $this->ArticleNo;
		$myArtBild->getFromPostL();
		$myArtBild->storeInDb();
		return $this->getTableArtBildAsXML();
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return Ambigous <boolean, string>
	 */
	function getTableArtBildAsXML( $_key = "", $_id = - 1, $_val = "" ) {
		$ret = "";
		$myArtBild = new ArtBild();
		$ret .= $myArtBild->tableFromDb( "", "", "C.ArticleNo = '" . $this->ArticleNo . "' " );
		return $ret;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return Ambigous <boolean, string>
	 */
	function updArtBild( $_key = "", $_id = - 1, $_val = "" ) {
		$myArtBild = new ArtBild();
		$myArtBild->setId( $_id );
		$myArtBild->getFromPostL();
		$myArtBild->updateInDb();
		return $this->getTableArtBildAsXML();
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return Ambigous <boolean, string>
	 */
	function delArtBild( $_key = "", $_id = - 1, $_val = "" ) {
		$myArtBild = new ArtBild();
		$myArtBild->setId( $_id );
		$myArtBild->removeFromDb();
		return $this->getTableArtBildAsXML();
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 */
	function refPhonText( $_key = "", $_id = - 1, $_val = "" ) {
		$myArtTexte = new ArtTexte();
		$myArtTexte->setId( $_id );
		$myArtTexte->PhonText = Phonetics::makePhoneticForDb( $this->ArticleNo . $this->getFullText( 0 ) );
		$myArtTexte->updateInDb();
		$this->PhonText = $myArtTexte->PhonText;
		$this->updateInDb();
		return $this->getArtTexteAsXMLByLang( "", "", $myArtTexte->Sprache );
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 */
	function delArtTexte( $_key = "", $_id = - 1, $_val = "" ) {
		$myArtTexte = new ArtTexte();
		$myArtTexte->setId( $_id );
		$myArtTexte->removeFromDb();
		return $this->getTableArtTexteAsXML();
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return unknown
	 */
	function addArticlePurchasePriceRel( $_key = "", $_id = - 1, $_val = "" ) {
		$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
		$myArticlePurchasePriceRel->ArticleNo = $this->ArticleNo;
		$myArticlePurchasePriceRel->getFromPostL();
		$myArticlePurchasePriceRel->storeInDb();
		$ret = $this->getTableArticlePurchasePriceRelAsXML();
		return $ret;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return unknown
	 */
	function updArticlePurchasePriceRel( $_key = "", $_id = - 1, $_val = "" ) {
		$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
		$myArticlePurchasePriceRel->setId( $_id );
		$myArticlePurchasePriceRel->getFromPostL();
		$myArticlePurchasePriceRel->updateInDb();
		$ret = $this->getTableArticlePurchasePriceRelAsXML();
		return $ret;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 */
	function delArticlePurchasePriceRel( $_key = "", $_id = - 1, $_val = "" ) {
		$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
		$myArticlePurchasePriceRel->setId( $_id );
		$myArticlePurchasePriceRel->removeFromDb();
		return $this->getTableArticlePurchasePriceRelAsXML();
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return unknown
	 */
	function addArticlePurchasePrice( $_key = "", $_id = - 1, $_val = "" ) {
		$myArticlePurchasePrice = new ArticlePurchasePrice();
		$myArticlePurchasePrice->ArticleNo = $this->ArticleNo;
		$myArticlePurchasePrice->getFromPostL();
		$myArticlePurchasePrice->storeInDb();
		$ret = $this->getTableArticlePurchasePriceAsXML();
		return $ret;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @return unknown
	 */
	function updArticlePurchasePrice( $_key = "", $_id = - 1, $_val = "" ) {
		$myArticlePurchasePrice = new ArticlePurchasePrice();
		$myArticlePurchasePrice->setId( $_id );
		$myArticlePurchasePrice->getFromPostL();
		$myArticlePurchasePrice->updateInDb();
		$ret = $this->getTableArticlePurchasePriceAsXML();
		return $ret;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 */
	function delArticlePurchasePrice( $_key = "", $_id = - 1, $_val = "" ) {
		$myArticlePurchasePrice = new ArticlePurchasePrice();
		$myArticlePurchasePrice->setId( $_id );
		$myArticlePurchasePrice->removeFromDb();
		return $this->getTableArticlePurchasePriceAsXML();
	}
	
	/**
	 *
	 * @return unknown
	 */
	function addArticleSalesPrice() {
		$myArticleSalesPrice = new ArticleSalesPrice();
		$myArticleSalesPrice->ArticleNo = $this->ArticleNo;
		$myArticleSalesPrice->getFromPostL();
		$myArticleSalesPrice->storeInDb();
		$ret = $this->getTableArticleSalesPriceAsXML();
		return $ret;
	}
	
	function createDefaultSalesPrice( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::begin( 1, "Article.php", "Article", "createDefaultSalesPrice()" );
		$myArticleSalesPrice = new ArticleSalesPrice();
		$myMarkets = Market::getMarkets();
		foreach ( $myMarkets as $key => $market ) {
			$objClass = "Market_" . $key;
			$actMarket = new $objClass();
			if ( $actMarket->createDefaultSP() ) {
				$myArtQPC = new ArtQPC;
				$myArtQPC->setIterCond( "ArticleNo = '" . $this->ArticleNo . "' " );
				$myArtQPC->rewind();
				if ( $myArtQPC->getIterCount() > 0 ) {
					foreach ( $myArtQPC as $keyArtQPC => $valArtQPC ) {
						$cond = "ArticleNo = '" . $this->ArticleNo . "' AND MarketId = '" . $key . "' " . "AND ValidFrom = '2000-01-01' AND ValidUntil = '2000-01-01' " . "AND QuantityPerPU = " . $myArtQPC->QPC . " ";
						if ( $myArticleSalesPrice->existWhere( $cond ) == 0 ) {
							$myArticleSalesPrice->ArticleNo = $this->ArticleNo;
							$myArticleSalesPrice->ValidFrom = "2000-01-01";
							$myArticleSalesPrice->ValidUntil = "2000-01-01";
							$myArticleSalesPrice->MarketId = $key;
							$myArticleSalesPrice->Quantity = 1;
							$myArticleSalesPrice->QuantityPerPU = $myArtQPC->QPC;
							$myArticleSalesPrice->storeInDb();
						}
					}
				} else {
					$cond = "ArticleNo = '" . $this->ArticleNo . "' AND MarketId = '" . $key . "' " . "AND ValidFrom = '2000-01-01' AND ValidUntil = '2000-01-01' " . "AND QuantityPerPU = 1 ";
					if ( $myArticleSalesPrice->existWhere( $cond ) == 0 ) {
						$myArticleSalesPrice->ArticleNo = $this->ArticleNo;
						$myArticleSalesPrice->ValidFrom = "2000-01-01";
						$myArticleSalesPrice->ValidUntil = "2000-01-01";
						$myArticleSalesPrice->MarketId = $key;
						$myArticleSalesPrice->Quantity = 1;
						$myArticleSalesPrice->QuantityPerPU = 1;
						$myArticleSalesPrice->storeInDb();
					}
				}
			}
		}
		$ret = $this->getXMLComplete( $_key, $_id, $_val );
		FDbg::end( 1, "Article.php", "Article", "createDefaultSalesPrice()" );
		return $ret;
	}
	
	function updArticleSalesPrice( $_key = "", $_id = - 1, $_val = "" ) {
		$myArticleSalesPrice = new ArticleSalesPrice();
		$myArticleSalesPrice->setId( $_id );
		$myArticleSalesPrice->getFromPostL();
		$myArticleSalesPrice->updateInDb();
		$ret = $this->getTableArticleSalesPriceAsXML();
		return $ret;
	}
	
	function delArticleSalesPrice( $_key, $_id, $_val ) {
		$myArticleSalesPrice = new ArticleSalesPrice();
		$myArticleSalesPrice->setId( $_id );
		$myArticleSalesPrice->removeFromDb();
		return $this->getTableArticleSalesPriceAsXML();
	}
	
	/**
	 * applyAttrTmpl
	 * Copies all attributes from the attribute template with the id $_id to the article
	 * with the article no. $_key.
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @throws Exception
	 */
	function applyAttrTmpl( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::begin( 1, "Article.php", "Article", "applyAttrTmpl( '$_key', $_id, '$_val')" );
		try {
			$myAttrTmpl = new AttrTmpl( $_id );
			$myAttrTmplItem = new AttrTmplItem();
			$newAttribute = new Attribute();
			$newAttribute->DataTable = "Article";
			$myAttrTmplItem->setIterCond( "AttrTmplNo = '" . $myAttrTmpl->AttrTmplNo . "' " );
			foreach ( $myAttrTmplItem as $key => $value ) {
				$newAttribute->copyFrom( $myAttrTmplItem );
				$newAttribute->RefNr = $this->ArticleNo;
				$newAttribute->storeInDb();
			}
		} catch ( Exception $e ) {
			throw $e;
		}
		$ret = $this->getXMLComplete( $_key, $_id, $_val );
		FDbg::end( 1, "Article.php", "Article", "applyAttrTmpl( '$_key', $_id, '$_val')" );
		return $ret;
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function addDocument( $_key = "", $_id = - 1, $_val = "" ) {
		/*
	 *
	 */
		$myDocument = new Document();
		$myDocument->getFromPostL();
		$myDocument->RefType = Document::RT_ARTICLE;
		$myDocument->RefNr = $this->ArticleNo;
		$myDocument->Filename = $myDocument->RefType . "_" . $myDocument->RefNr . "_" . $this->ERPNo . "_" . $myDocument->DocType . "_" . $myDocument->DocRev . "_" . "." . $myDocument->Filetype;
		$myDocument->storeInDb();
		return $this->getTableDocumentsAsXML();
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return string
	 */
	function getTableDocumentsAsXML( $_key = "", $_id = - 1, $_val = "" ) {
		$ret = "";
		$ret .= "<DocList>\n";
		$ret .= "<URLPath>" . $this->url->Documents . "</URLPath>\n";
		$myDocument = new Document();
		$ret .= $myDocument->tableFromDb( "", "", "C.RefType='" . Document::RT_ARTICLE . "' AND C.RefNr = '" . $this->ArticleNo . "' ", "", "Doc" );
		$ret .= "</DocList>\n";
		return $ret;
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function updDocument( $_key = "", $_id = - 1, $_val = "" ) {
		$myDocument = new Document();
		$myDocument->setId( $_id );
		$myDocument->getFromPostL();
		$myDocument->updateInDb();
		return $this->getTableDocumentsAsXML();
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function delDocument( $_key = "", $_id = - 1, $_val = "" ) {
		$myDocument = new Document();
		if ( $myDocument->setId( $_id ) ) {
			$myDocument->removeFromDb();
		}
		return $this->getTableDocumentsAsXML();
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function _cacheSP( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::dumpL( 0x00000001, "Article.php::Article::_cacheSP( '$_key', $_id, '$_val'): begin" );
		$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
		$myArticlePurchasePrice = new ArticlePurchasePrice();
		try {
			/**
			 * see if there's a timely limited special price
			 */
			$myArticleSalesPrice = new ArticleSalesPrice();
			$myArticleSalesPrice->fetchFromDbWhere( "ArticleNo = '$this->ArticleNo' AND ValidFrom <= '" . $this->today() . "' AND '" . $this->today() . "' < ValidUntil " );
			if ( $myArticleSalesPrice->isValid() ) {
				/**
				 * brute force, remove everything in the sales price cache
				 */
				FDb::query( "DELETE FROM ArticleSalesPriceCache ArticleNo = '$this->ArticleNo' " );
				$newArticleSalesPriceCache = new ArticleSalesPriceCache();
				$newArticleSalesPriceCache->copyFrom( $myArticleSalesPrice );
				$newArticleSalesPriceCache->Rabatt = 0.0;
				$newArticleSalesPriceCache->storeInDb();
			} else {
				/**
				 * find the base for the sales price
				 */
				$myArticlePurchasePriceRel->fetchFromDbWhere( "ArticleNo = '$this->ArticleNo' AND CalculationBase > 0 " );
				$myArticlePurchasePrice->fetchFromDbWhere( "SupplierNo = '$myArticlePurchasePriceRel->SupplierNo' " . "AND SupplierArticleNo = '$myArticlePurchasePriceRel->SupplierArticleNo' " . "AND Quantity = $myArticlePurchasePriceRel->CalculationBase " . "AND ValidFrom < '" . $this->today() . "' " . "AND ValidUntil >= '" . $this->today() . "' " );
				FDb::query( "DELETE FROM ArticleSalesPriceCache ArticleNo = '$this->ArticleNo' " );
				if ( $myArticlePurchasePrice->isValid() ) {
					/**
					 *
					 * @var unknown_type
					 */
					$myArticleSalesPrice = new ArticleSalesPrice;
					$newArticleSalesPriceCache = new ArticleSalesPriceCache();
					for ( $res = $myArticleSalesPrice->_firstFromDb( "ArticleNo = '" . $this->ArticleNo . "' " ) ; $res ; $res = $myArticleSalesPrice->_nextFromDb() ) {
						FDbg::dumpL( 0x00000002, "Article.php::Article::_cacheSP(...): => caching article $myArticleSalesPrice->ArticleNo in qty. = $myArticleSalesPrice->QuantityPerPU" );
						$newArticleSalesPriceCache->copyFrom( $myArticleSalesPrice );
						$newArticleSalesPriceCache->Price = $myArticleSalesPrice->QuantityPerPU * $myArticlePurchasePrice->OwnArticleSalesPrice / $myArticlePurchasePrice->QuantityForPrice;
						$newArticleSalesPriceCache->Rabatt = $myArticlePurchasePrice->OwnRabatt;
						$newArticleSalesPriceCache->storeInDb();
					}
				}
			}
		} catch ( Exception $e ) {
			throw $e;
		}
		$ret = $this->getTableArticleSalesPriceCacheAsXML();
		FDbg::dumpL( 0x00000001, "Article.php::Article::_cacheSP( '$_key', $_id, '$_val'): end" );
		return $ret;
	}
	
	/**
	 *
	 */
	function calcCacheSP( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		FDbg::begin( 1, "Article.php", "Article", "calcCacheSP( '$_key', $_id, '$_val', <Reply>)" );
		if ( $reply == null ) {
			$reply = new Reply( __class__, $this->className );
		}
		try {
			$this->calcSP( $_key, $_id, $_val );
			$this->cacheSP( $_key, $_id, $_val );
		} catch ( Exception $e ) {
			FDbg::abort();
			throw $e;
		}
		$this->getTableDepAsXML( $_key, $_id, "ArticleSalesPriceCache", $reply );
		FDbg::end();
		return $reply;
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param string $_val additional criteria for ArticlePurchasePriceRel iteration
	 */
	function calcSP( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::begin( 1, "Article.php", "Article", "calcSP( '$_key', $_id, '$_val')" );
		$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
		$myArticlePurchasePrice = new ArticlePurchasePrice();
		$mySupplier = new Supplier();
		try {
			if ( $_id == - 1 ) {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "calcSP(...)", "calculating ALL sales prices for this article (id == -1)" );
				$myArticlePurchasePriceRel->setIterCond( "ArticleNo = '" . $this->ArticleNo . "' " . $_val );
				foreach ( $myArticlePurchasePriceRel as $key => $val ) {
					FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "calcSP(...)", "working on ArticleSalesPriceRel.Id = " . $val->Id );
					$myArticlePurchasePrice->setIterCond( "SupplierNo = '" . $myArticlePurchasePriceRel->SupplierNo . "' AND SupplierArticleNo = '" . $myArticlePurchasePriceRel->SupplierArticleNo . "' " );
					foreach ( $myArticlePurchasePrice as $keyEKP => $valEKP ) {
						$mySupplier->setSupplierNo( $myArticlePurchasePriceRel->SupplierNo );
						if ( $mySupplier->isValid() ) {
							if ( $mySupplier->AutoPrice == 1 ) {
								if ( $this->CompositionType > 0 ) {
									Article::_detCompArticlePrice( $this->ArticleNo );
								} else {
									$myArticlePurchasePrice->OwnArticleSalesPrice = $myArticlePurchasePrice->Price * $myArticlePurchasePriceRel->Margin * $this->Margin * $mySupplier->Margin;
									$myArticlePurchasePrice->updateInDb();
								}
							}
						}
					}
				}
				$ret = $this->getXMLComplete( $_key, $_id, "" );
			} else {
				FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "calcSP(...)", "calculating a SINGLE sales price (id != -1)" );
				$myArticlePurchasePrice->setId( $_id );
				$myArticlePurchasePriceRel->fetchFromDbWhere( "ArticleNo = '" . $this->ArticleNo . "' AND SupplierNo = '" . $myArticlePurchasePrice->SupplierNo . "' " );
				if ( ! $myArticlePurchasePrice->isValid() ) {
					$e = new Exception( "Article.php::Article::calcSP( '$_key', $_id, '$_val'): could not fetch ArticleSalesPrice by 'Id'" );
					error_log( $e );
					throw $e;
				}
				$mySupplier->setSupplierNo( $myArticlePurchasePrice->SupplierNo );
				if ( $mySupplier->isValid() ) {
					if ( $mySupplier->AutoPrice == 1 ) {
						if ( $this->CompositionType > 0 ) {
							Article::_detCompArticlePrice( $this->ArticleNo );
						} else {
							if ( $myArticlePurchasePrice->Price == 0 ) {
								FDbg::trace( 1, "Article.php", "Article", "calcSP(...)", "Price = 0" );
							}
							if ( $myArticlePurchasePriceRel->Margin == 0 ) {
								FDbg::trace( 1, "Article.php", "Article", "calcSP(...)", "ArticlePurchasePriceRel->Margin = 0" );
							}
							if ( $this->Margin == 0 ) {
								FDbg::trace( 1, "Article.php", "Article", "calcSP(...)", "Article->Margin = 0" );
							}
							if ( $mySupplier->Margin == 0 ) {
								FDbg::trace( 1, "Article.php", "Article", "calcSP(...)", "Supplier->Margin = 0" );
							}
							$myArticlePurchasePrice->OwnArticleSalesPrice = $myArticlePurchasePrice->Price * $myArticlePurchasePriceRel->Margin * $this->Margin * $mySupplier->Margin;
							$myArticlePurchasePrice->updateInDb();
						}
					}
				} else {
					FDbg::trace( 2, "Article.php", "Article", "calcSP(...)", "can't find suplier data" );
				}
				$ret = "<" . $this->className . ">\n" . $this->getDepAsXML( $_key, $_id, "ArticlePurchasePrice" ) . "</" . $this->className . ">\n";
			}
		} catch ( Exception $e ) {
			throw $e;
		}
		FDbg::end( 1, "Article.php", "Article", "calcOwnSalesPrice( '$_key', $_id, '$_val')" );
		return $ret;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function cacheSP( $_key = "", $_id = - 1, $_val = "" ) {
		require_once( "Market.php" );            // need to load this here to establish the module libraries
		FDbg::begin( 1, "Article.php", "Article", "cacheSP( '$_key', $_id, '$_val')" );
		$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
		$myArticlePurchasePrice = new ArticlePurchasePrice();
		$myArticleSalesPriceCache = new ArticleSalesPriceCache();
		try {
			FDbg::trace( 2, FDbg::mdTrcInfo1, "Article.php", "Article", "cacheSP( '$_key', $_id, '$_val')", "trying ... " );
			/**
			 * brute force, remove everything in the sales price cache
			 */
			$myQuery = $myArticleSalesPriceCache->getQueryObj( "Delete" );
			$myQuery->addWhere( "ArticleNo = '$this->ArticleNo' " );
			FDb::query( $myQuery );
			/**
			 * see if there's a timely limited special price
			 */
			$myArticleSalesPrice = new ArticleSalesPrice();
			$myArticleSalesPrice->setIterCond( "ArticleNo = '$this->ArticleNo' " );
			$myArticleSalesPrice->setIterOrder( [ "MarketId", "Quantity", "QuantityPerPU" ] );
			foreach ( $myArticleSalesPrice as $key => $val ) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, "Article.php", "Article", "cacheSP( '$_key', $_id, '$_val')", "iterating ArticleSalesPrice[id=" . $val->Id . "] " );
				/**
				 * IF the sales price is valid for today
				 *        take it
				 * ELSE if the sales price is a place holder
				 *
				 */
				$marketObj = "Market_" . $myArticleSalesPrice->MarketId;
				$myMarket = new $marketObj();
				if ( $myArticleSalesPrice->ValidFrom <= $this->today() && $this->today() <= $myArticleSalesPrice->ValidUntil ) {
					FDbg::trace( 2, FDbg::mdTrcInfo1, "Article.php", "Article", "cacheSP( '$_key', $_id, '$_val')", "ArticleSalesPrice valid today" );
					$newArticleSalesPriceCache = new ArticleSalesPriceCache();
					$newArticleSalesPriceCache->copyFrom( $myArticleSalesPrice );
					$newArticleSalesPriceCache->Price = $myArticleSalesPrice->Price;
					$newArticleSalesPriceCache->Rabatt = 0.0;
					$newArticleSalesPriceCache->storeInDb();
				} else {
					if ( $myArticleSalesPrice->ValidFrom == "2000-01-01" && $myArticleSalesPrice->ValidUntil == "2000-01-01" ) {
						FDbg::trace( 2, FDbg::mdTrcInfo1, "Article.php", "Article", "cacheSP( '$_key', $_id, '$_val')", "ArticleSalesPrice valid permanently" );
						/**
						 * find the base for the sales price
						 */
						$myArticlePurchasePriceRel->fetchFromDbWhere( "ArticleNo = '$this->ArticleNo' AND CalculationBase > 0 " );
						if ( ! $myArticlePurchasePriceRel->isValid() ) {
							if ( $myArticlePurchasePriceRel->_status == - 1 ) {
								throw new Exception( "Article.php::Article::cacheSP( '$_key', $_id, '$_val'): no valid calculation base found!" );
							} else {
								if ( $myArticlePurchasePriceRel->_status == - 2 ) {
									throw new Exception( "Article.php::Article::cacheSP( '$_key', $_id, '$_val'): more than 1 calculation base found!" );
								}
							}
						}
						$myArticlePurchasePrice->fetchFromDbWhere( "SupplierNo = '$myArticlePurchasePriceRel->SupplierNo' " . "AND SupplierArticleNo = '$myArticlePurchasePriceRel->SupplierArticleNo' " . "AND Quantity = $myArticlePurchasePriceRel->CalculationBase " . "AND ValidFrom <= '" . $this->today() . "' " . "AND '" . $this->today() . "' <= ValidUntil " );
						/**
						 * IF there's no valid price for this year, try to fetch the last one
						 * year. If not, the we definitely need to quit.
						 */
						if ( ! $myArticlePurchasePrice->isValid() ) {
							$myArticlePurchasePrice->fetchFromDbWhere( "SupplierNo = '$myArticlePurchasePriceRel->SupplierNo' " . "AND SupplierArticleNo = '$myArticlePurchasePriceRel->SupplierArticleNo' " . "AND Quantity = $myArticlePurchasePriceRel->CalculationBase " . "AND ValidFrom <= '" . $this->today( - 1 * 365 * 24 * 60 * 60 ) . "' " . "AND '" . $this->today( - 1 * 365 * 24 * 60 * 60 ) . "' <= ValidUntil " );
						}
						if ( ! $myArticlePurchasePrice->isValid() ) {
							$e = new Exception( "Article.php::Article::cacheSP( '$_key', $_id, '$_val'): no valid article purchase price found! this->ArticleNo = '" . $this->ArticleNo . "'" );
							FDbg::trace( 0, FDbg::mdTrcInfo1, "Article.php", "Article", "cacheSP( '$_key', $_id, '$_val')", "Exception: '" . $e->getMessage() . "'" );
							throw( $e );
						}
						/**
						 *
						 * @var unknown_type
						 */
						$newArticleSalesPriceCache = new ArticleSalesPriceCache();
						$newArticleSalesPriceCache->copyFrom( $myArticleSalesPrice );
						$myMarket->getPrice( $myArticlePurchasePriceRel, $myArticlePurchasePrice, $newArticleSalesPriceCache, 19.0, $this->MarginMinQ );
						$newArticleSalesPriceCache->storeInDb();
					}
				}
			}
		} catch ( Exception $e ) {
			FDbg::abort();
			throw $e;
		}
		$ret = $this->getXMLComplete( $_key, $_id, $_val );
		FDbg::end();
		return $ret;
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function consStock( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::dumpL( 0x00000001, "Article.php::Article::consStock( '$_key', $_id, '$_val'): begin" );
		$myArticleStock = new ArticleStock();
		if ( $myArticleStock->getDefault( $this->ArticleNo ) ) {
			$myArticleStock->cons();
		} else {
			throw new Exception( "Article.php::Article::consStock( '$_key', $_id, '$_val'): can't find DefaultStock" );
		}
		FDbg::dumpL( 0x00000001, "Article.php::Article::consStock( '$_key', $_id, '$_val'): end" );
		return $this->getXMLComplete();
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function consStockWC( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::dumpL( 0x00000001, "Article.php::Article::consStock( '$_key', $_id, '$_val'): begin" );
		$myArticleStock = new ArticleStock();
		if ( $myArticleStock->getDefault( $this->ArticleNo ) ) {
			$myArticleStock->consWC();
		} else {
			throw new Exception( "Article.php::Article::consStock( '$_key', $_id, '$_val'): can't find DefaultStock" );
		}
		FDbg::dumpL( 0x00000001, "Article.php::Article::consStock( '$_key', $_id, '$_val'): end" );
		return $this->getXMLComplete();
	}
	
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function calcPP( $_key = "", $_id = - 1, $_val = "" ) {
		$ret = "";
		$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
		$myArticlePurchasePrice = new ArticlePurchasePrice();
		$mySupplier = new Supplier();
		$mySupplierRabatt = new SupplierRabatt();
		try {
			if ( $_id == - 1 ) {
				$myArticlePurchasePriceRel->setIterCond( "ArticleNo = '" . $this->ArticleNo . "' " );
				foreach ( $myArticlePurchasePriceRel as $key => $val ) {
					$myArticlePurchasePrice->setIterCond( "SupplierNo = '" . $myArticlePurchasePriceRel->SupplierNo . "' AND SupplierArticleNo = '" . $myArticlePurchasePriceRel->SupplierArticleNo . "' " );
					foreach ( $myArticlePurchasePrice as $keyEKP => $valEKP ) {
						$mySupplierRabatt->setKeys( $myArticlePurchasePrice->SupplierNo, $myArticlePurchasePrice->HKRabKlasse, 1 );
						if ( $mySupplierRabatt->isValid() ) {
							$myArticlePurchasePrice->Price = $myArticlePurchasePrice->calcVP( $myArticlePurchasePrice->SupplierArticleSalesPrice, $mySupplierRabatt->Rabatt );
							$myArticlePurchasePrice->updateInDb();
						} else {
							throw new Exception( "Article::calcEK: HKRabKlasse can not be found by SupplierNo['" . $myArticlePurchasePrice->SupplierNo . "', '" . $myArticlePurchasePrice->HKRabKlasse . "']" );
						}
					}
				}
				$ret = $this->getXMLComplete( $_key, $_id, "" );
			} else {
				$myArticlePurchasePrice->setId( $_id );
				if ( $myArticlePurchasePrice->isValid() ) {
					$mySupplierRabatt->setKeys( $myArticlePurchasePrice->SupplierNo, $myArticlePurchasePrice->HKRabKlasse, 1 );
					if ( $mySupplierRabatt->isValid() ) {
						$myArticlePurchasePrice->Price = $myArticlePurchasePrice->calcEK( $myArticlePurchasePrice->SupplierArticleSalesPrice, $mySupplierRabatt->Rabatt );
						$myArticlePurchasePrice->updateInDb();
					} else {
						throw new Exception( "Article::calcPurchasePrice: HKRabKlasse can not be found by SupplierNo['" . $myArticlePurchasePrice->SupplierNo . "', '" . $myArticlePurchasePrice->HKRabKlasse . "']" );
					}
				} else {
					throw new Exception( "Article.php::Article::calcPurchasePrice: ArticlePurchasePrice can not be found by Id[$_id]" );
				}
				$ret = "<" . $this->className . ">\n" . $this->getDepAsXML( $_key, $_id, "ArticlePurchasePrice" ) . "</" . $this->className . ">\n";
			}
		} catch ( Exception $e ) {
			throw $e;
		}
		return ( $ret );
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function calcMSRP( $_key, $_id, $_val ) {
		$ret = "";
		$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
		$myArticlePurchasePrice = new ArticlePurchasePrice();
		$mySupplier = new Supplier();
		$mySupplierRabatt = new SupplierRabatt();
		try {
			if ( $_id == - 1 ) {
				$myArticlePurchasePriceRel->setIterCond( "ArticleNo = '" . $this->ArticleNo . "' " );
				foreach ( $myArticlePurchasePriceRel as $key => $val ) {
					$myArticlePurchasePrice->setIterCond( "SupplierNo = '" . $myArticlePurchasePriceRel->SupplierNo . "' AND SupplierArticleNo = '" . $myArticlePurchasePriceRel->SupplierArticleNo . "' " );
					foreach ( $myArticlePurchasePrice as $keyEKP => $valEKP ) {
						$mySupplierRabatt->setKeys( $myArticlePurchasePrice->SupplierNo, $myArticlePurchasePrice->HKRabKlasse, 1 );
						if ( $mySupplierRabatt->isValid() ) {
							$myArticlePurchasePrice->SupplierArticleSalesPrice = $myArticlePurchasePrice->calcVP( $myArticlePurchasePrice->Price, $mySupplierRabatt->Rabatt );
							$myArticlePurchasePrice->updateInDb();
						} else {
							throw new Exception( "Article::calcMSRP: HKRabKlasse can not be found by SupplierNo['" . $myArticlePurchasePrice->SupplierNo . "', '" . $myArticlePurchasePrice->HKRabKlasse . "']" );
						}
					}
				}
				$ret = $this->getXMLComplete( $_key, $_id, "" );
			} else {
				$myArticlePurchasePrice->setId( $_id );
				if ( $myArticlePurchasePrice->isValid() ) {
					$mySupplierRabatt->setKeys( $myArticlePurchasePrice->SupplierNo, $myArticlePurchasePrice->HKRabKlasse, 1 );
					if ( $mySupplierRabatt->isValid() ) {
						$myArticlePurchasePrice->SupplierArticleSalesPrice = $myArticlePurchasePrice->calcVP( $myArticlePurchasePrice->Price, $mySupplierRabatt->Rabatt );
						$myArticlePurchasePrice->updateInDb();
					} else {
						throw new Exception( "Article::calcMSRP: HKRabKlasse can not be found by SupplierNo['" . $myArticlePurchasePrice->SupplierNo . "', '" . $myArticlePurchasePrice->HKRabKlasse . "']" );
					}
				} else {
					throw new Exception( "Article.php::Article::calcMSRP: ArticlePurchasePrice can not be found by Id[$_id]" );
				}
				$ret = "<" . $this->className . ">\n" . $this->getDepAsXML( $_key, $_id, "ArticlePurchasePrice" ) . "</" . $this->className . ">\n";
			}
		} catch ( Exception $e ) {
			throw $e;
		}
		return ( $ret );
	}
	
	/**
	 *
	 */
	function copyArtTexte( $_key, $_id, $_lang ) {
		try {
			$myArtTexte = new ArtTexte( $this->ArticleNo, $_lang );
			if ( $myArtTexte->isValid() ) {
				$myArtTexte->ArticleDescription1 = $this->ArticleDescription1;
				$myArtTexte->ArticleDescription2 = $this->ArticleDescription2;
				$myArtTexte->updateInDb();
			}
		} catch ( Exception $e ) {
			error_log( "Article.php::Article::copyToArtTexte( '$_key', $_id, '$_val': exception $e->getMessage()" );
		}
		return $this->getXMLString();
	}
	
	/**
	 * report
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function report( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::dumpL( 0x00000100, "Article.php::Article::report( '$_key', $_id, '$_val'):" );
		$filtArticleNo = $_POST[ '_IFiltArticleNo' ];
		if ( $filtArticleNo == "" ) {
			$filtArticleNo = "%";
		}
		$filtDescription = $_POST[ '_IFiltDescription' ];
		if ( $filtDescription == "" ) {
			$filtDescription = "%";
		}
		$filtDesc1 = $_POST[ '_IFiltDesc1' ];
		$filtDesc2 = $_POST[ '_IFiltDesc2' ];
		$query = "SELECT A.ArticleNo, A.ArticleNoOld, A.ArticleDescription1, A.ArticleDescription2, A.QuantityText, VKPC.Price FROM Article AS A " . "LEFT JOIN ArticleSalesPriceCache AS VKPC ON VKPC.ArticleNo = A.ArticleNo " . "A.ArticleNo like '$filtArticleNo' " . "AND ( A.ArticleDescription1 like '$filtDescription' OR A.ArticleDescription2 like '$filtDescription') ";
		if ( $filtDesc1 != "" ) {
			$query .= "AND A.ArticleDescription1 like '$filtDesc1' ";
		}
		if ( $filtDesc2 != "" ) {
			$query .= "AND A.ArticleDescription2 like '$filtDesc2' ";
		}
		$query .= "AND SuppliererStatus = " . Article::ARTSPLNORM . " ";
		$query .= "ORDER BY A.ArticleNo ASC ";
		$res = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$res .= "<doc toc=\"true\" toclevel=\"3\" cover=\"false\" xmlns:fo=\"http://www.w3.org/1999/XSL/Format\" lang=\"en\">\n";
		$res .= "<Copyright>2007-2011 Copyright MODIS GmbH, D-51674 Wiehl - Bomig, Robert-Bosch-Str. 1</Copyright>\n";
		/**
		 *
		 */
		$res .= "<Image>" . $this->path->Logos . "logo_main.jpg" . "</Image>\n";
		$res .= "<Scope>Chemikalien, Indikatoren und Reagenzien</Scope>\n";
		$res .= "<Date>" . $this->today() . "</Date>\n";
		$res .= FDb::queryForXMLTable( $query, "Article" );
		$res .= "</doc>";
		$myFile = fopen( $this->path->Catalog . "articles.xml", "w" );
		fwrite( $myFile, $res );
		fclose( $myFile );
		$sysCmd = "fop -xml " . $this->path->Catalog . "articles.xml " . "-xsl " . $this->path->Styles . "pricelistReagents.xsl " . "-pdf " . $this->path->Catalog . "pricelist.pdf ";
		system( $sysCmd, $res );
		error_log( "sysCmd: '$sysCmd', result: $res" );
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function _stepDepSingle( $_key, $_id, $_val, $_step ) {
		return parent::_stepDepSingle( $_key, $_id, $_val, $_step );
	}
	
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return string
	 */
	function getDefAsXML( $_key = "", $_id = - 1, $_val = "" ) {
		$myBuf = "";
		if ( $_val == "ArticlePurchasePriceRel" ) {
			$myArticlePurchasePriceRel = new ArticlePurchasePriceRel();
			$myArticlePurchasePriceRel->setId( $_id );
			$myBuf .= "<ArticlePurchasePrice>";
			$myBuf .= "<SupplierNo>$myArticlePurchasePriceRel->SupplierNo</SupplierNo>";
			$myBuf .= "<SupplierArticleNo>$myArticlePurchasePriceRel->SupplierArticleNo</SupplierArticleNo>";
			$myBuf .= "</ArticlePurchasePrice>";
		}
		return $myBuf;
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function reAssignERPNo( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::dumpL( 0x00000001, "Article.php::Article::reAssignERPNo( '$_key', $_id, '$_val'): begin" );
		try {
			$this->assignERPNo();
			$this->updateColInDb( "ERPNo" );
		} catch ( Exception $e ) {
			throw $e;
		}
		FDbg::dumpL( 0x00000001, "Article.php::Article::reAssignERPNo( '$_key', $_id, '$_val'): end" );
		return $this->getXMLComplete();
	}
	
	/**
	 * updDep
	 */
	function updDep( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		FDbg::begin( 1, "Article.php", "Article", "updDep( '$_key', $_id, '$_val')" );
		$elem = explode( ".", $_val );
		$objName = $elem[ 0 ];
		if ( isset( $elem[ 1 ] ) ) {
			$attrName = $elem[ 1 ];
		} else {
			$attrName = "";
		}
		FDbg::trace( 2, "Article.php", "Article", "updDep( '$_key', $_id, '$_val')", "Class := '$objName', Attribute := '$attrName'" );
		switch ( $objName ) {
		default    :
			return parent::updDep( $_key, $_id, $_val );
		break;
		}
		FDbg::end();
		return $this->getTableDepAsXML( $_key, $_id, $objName );
	}
	
	/**
	 * delDep
	 * (non-PHPdoc)
	 * @see AppObject_R2::delDep()
	 */
	function delDep( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		FDbg::begin( 1, "Article.php", "Article", "delDep( '$_key', $_id, '$_val')" );
		if ( $reply == null ) {
			$reply = new Reply( __class__, $this->className );
		}
		switch ( $_val ) {
		default    :
			parent::delDep( $_key, $_id, $_val, $reply );
		break;
		}
		FDbg::end();
		return $reply;
	}
	
	/**
	 * getList
	 *
	 * return a list of <this> class objects or <this> class dependant obejcts
	 *
	 * @var     string $_key
	 * @var     int $_id
	 * @var     mixed $_val
	 * @return  Reply
	 */
	function getList( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		if ( $reply == null ) {
			$reply = new Reply( __class__, $this->className );
		}
		$sCrit = "";
		if ( isset( $_POST[ 'Search' ] ) ) {
			$sCrit = $_POST[ 'Search' ];
		}
		$objName = $_val;
		switch ( $objName ) {
		case    ""    :
		case    "Article"    :
			$myObj = new FDbObject( "Article", "ArticleNo", "def", "v_ArticleSurvey" );
			if ( isset( $_POST[ 'StartRow' ] ) ) {
				$myObj->setPage( intval( $_POST[ 'StartRow' ] ), intval( $_POST[ 'RowCount' ] ), $_POST[ 'step' ] );
			}
			$myQuery = $myObj->getQueryObj( "Select" );
			$filter1 = "ArticleNo LIKE '%" . $sCrit . "%' OR ArticleDescription1 LIKE '%" . $sCrit . "%' OR ArticleDescription2 LIKE '%" . $sCrit . "%' ";
			$filter2 = "";
			$myQuery->addWhere( [ $filter1, $filter2 ] );
			$reply->replyData = $myObj->tableFromQuery( $myQuery );
		break;
		case    "ArticleText"    :
			$myObj = new FDbObject( "ArticleText" );                // no specific object we need here
			if ( isset( $_POST[ 'StartRow' ] ) ) {
				$myObj->setPage( intval( $_POST[ 'StartRow' ] ), intval( $_POST[ 'RowCount' ] ), $_POST[ 'step' ] );
			}
			$filter = "ArticleNo = '" . $this->ArticleNo . "' ";
			$myQuery = $myObj->getQueryObj( "Select" );
			$myQuery->addWhere( [ $filter ] );
			$myQuery->addOrder( [ "Language" ] );
			$reply->replyData = $myObj->tableFromQuery( $myQuery );
		break;
		case    "Attribute"    :
			$myObj = new FDbObject( "Attribute" );                // no specific object we need here
			if ( isset( $_POST[ 'StartRow' ] ) ) {
				$myObj->setPage( intval( $_POST[ 'StartRow' ] ), intval( $_POST[ 'RowCount' ] ), $_POST[ 'step' ] );
			}
			$filter = "DataTable = 'Article' AND RefNo = '" . $this->ArticleNo . "' ";
			$myQuery = $myObj->getQueryObj( "Select" );
			$myQuery->addWhere( [ $filter ] );
			$myQuery->addOrder( [ "ItemNo" ] );
			$reply->replyData = $myObj->tableFromQuery( $myQuery );
		break;
		case    "ArticleComponent"    :
			$myObj = new FDbObject( "ArticleComponent" );                // no specific object we need here
			if ( isset( $_POST[ 'StartRow' ] ) ) {
				$myObj->setPage( intval( $_POST[ 'StartRow' ] ), intval( $_POST[ 'RowCount' ] ), $_POST[ 'step' ] );
			}
			$filter = "ArticleNo = '" . $this->ArticleNo . "' ";
			$myQuery = $myObj->getQueryObj( "Select" );
			$myQuery->addWhere( [ $filter ] );
			$reply->replyData = $myObj->tableFromQuery( $myQuery );
			break;
		case    "ArticleStock"    :
			$myObj = new FDbObject( "ArticleStock" );                // no specific object we need here
			if ( isset( $_POST[ 'StartRow' ] ) ) {
				$myObj->setPage( intval( $_POST[ 'StartRow' ] ), intval( $_POST[ 'RowCount' ] ), $_POST[ 'step' ] );
			}
			$filter = "ArticleNo = '" . $this->ArticleNo . "' ";
			$myQuery = $myObj->getQueryObj( "Select" );
			$myQuery->addWhere( [ $filter ] );
			$myQuery->addOrder( [ "WarehouseId", "StockId", "ShelfId", "Location" ] );
			$reply->replyData = $myObj->tableFromQuery( $myQuery );
		break;
		case    "ArticlePurchasePriceRel"    :
			$myObj = new FDbObject( "ArticlePurchasePriceRel", "ArticleNo", "def", "v_ArticlePurchasePriceRelSurvey" );
			if ( isset( $_POST[ 'StartRow' ] ) ) {
				$myObj->setPage( intval( $_POST[ 'StartRow' ] ), intval( $_POST[ 'RowCount' ] ), $_POST[ 'step' ] );
			}
			$filter = "ArticleNo = '" . $this->ArticleNo . "' ";
			$myQuery = $myObj->getQueryObj( "Select" );
			$myQuery->addWhere( [ $filter ] );
			$myQuery->addOrder( [ "SupplierNo", "QuantityPerPU" ] );
			$reply->replyData = $myObj->tableFromQuery( $myQuery );
			break;
		case    "ArticlePurchasePrice"    :
			$myObj = new FDbObject( "ArticlePurchasePrice", "ArticleNo", "def", "v_ArticlePurchasePriceSurvey" );
			if ( isset( $_POST[ 'StartRow' ] ) ) {
				$myObj->setPage( intval( $_POST[ 'StartRow' ] ), intval( $_POST[ 'RowCount' ] ), $_POST[ 'step' ] );
			}
			$filter = "ArticleNo = '" . $this->ArticleNo . "' ";
			$myQuery = $myObj->getQueryObj( "Select" );
			$myQuery->addWhere( [ $filter ] );
			$myQuery->addOrder( [ "SupplierNo", "ValidFrom DESC", "QuantityPerPU", "Quantity" ] );
			$reply->replyData = $myObj->tableFromQuery( $myQuery, "ArticlePurchasePrice" );
			break;
		case    "ArticleSalesPrice"    :
			$myObj = new FDbObject( "ArticleSalesPrice" );                // no specific object we need here
			if ( isset( $_POST[ 'StartRow' ] ) ) {
				$myObj->setPage( intval( $_POST[ 'StartRow' ] ), intval( $_POST[ 'RowCount' ] ), $_POST[ 'step' ] );
			}
			$filter = "ArticleNo = '" . $this->ArticleNo . "' ";
			$myQuery = $myObj->getQueryObj( "Select" );
			$myQuery->addWhere( [ $filter ] );
			$reply->replyData = $myObj->tableFromQuery( $myQuery );
			break ;
		case    "ArticleSalesPriceCache"    :
			$myObj = new FDbObject( "ArticleSalesPriceCache" );                // no specific object we need here
			if ( isset( $_POST[ 'StartRow' ] ) ) {
				$myObj->setPage( intval( $_POST[ 'StartRow' ] ), intval( $_POST[ 'RowCount' ] ), $_POST[ 'step' ] );
			}
			$filter = "ArticleNo = '" . $this->ArticleNo . "' ";
			$myQuery = $myObj->getQueryObj( "Select" );
			$myQuery->addWhere( [ $filter ] );
			$myQuery->addOrder( [ "MarketId", "QuantityPerPU", "Quantity" ] );
			$reply->replyData = $myObj->tableFromQuery( $myQuery );
			break;
		break;
		}
		//		error_log( $ret) ;
		return $reply;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @var     string $_key
	 * @var     int $_id
	 * @var     mixed $_val
	 * @return  Reply
	 */
	function getListAsXML( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		FDbg::begin( 1, "Article.php", "Article", "getListAsXML( '$_key', $_id, '$_val')" );
		if ( $reply == null ) {
			$reply = new Reply( __class__, $this->className );
		}
		$articleNoCrit = "";
		$erpNoCrit = "";
		$articleDescrCrit = "";
		$articleDescr1Crit = "";
		$articleDescr2Crit = "";
		if ( isset( $_POST[ '_SArticleNo' ] ) ) {
			$articleNoCrit = $_POST[ '_SArticleNo' ];
		}
		if ( isset( $_POST[ '_SERPNo' ] ) ) {
			$erpNoCrit = $_POST[ '_SERPNo' ];
		}
		if ( isset( $_POST[ '_SDescr' ] ) ) {
			$articleDescrCrit = $_POST[ '_SDescr' ];
		}
		if ( isset( $_POST[ '_SDescr1' ] ) ) {
			$articleDescr1Crit = $_POST[ '_SDescr1' ];
		}
		if ( isset( $_POST[ '_SDescr2' ] ) ) {
			$articleDescr2Crit = $_POST[ '_SDescr2' ];
		}
		$_POST[ '_step' ] = $_id;
		$filter = "( ";
		$filter .= "C.ArticleNo like '%" . $articleNoCrit . "%' ";
		$filter .= "AND C.ERPNo like '%" . $erpNoCrit . "%' ";
		if ( $articleDescrCrit != "" ) {
			$filter .= "  AND ( C.ArticleDescription1 like '%" . $articleDescrCrit . "%' OR C.ArticleDescription2 like '%" . $articleDescrCrit . "%' ) ";
		}
		if ( $articleDescr1Crit != "" ) {
			$filter .= "  AND ( C.ArticleDescription1 like '%" . $articleDescr1Crit . "%' ) ";
		}
		if ( $articleDescr2Crit != "" ) {
			$filter .= "  AND ( C.ArticleDescription2 like '%" . $articleDescr2Crit . "%' ) ";
		}
		$filter .= ") ";
		if ( isset( $_POST[ '_SSearch' ] ) ) {
			$filter .= "AND ( ";
			$filter .= "ArticleNo like '%" . $_POST[ '_SSearch' ] . "%' OR ArticleDescription1 like '%" . $_POST[ '_SSearch' ] . "%' ";
			$filter .= ") ";
		}
		$myObj = new FDbObject( "", "" );                // no specific object we need here
		$myObj->addCol( "Id", "int" );
		$myObj->addCol( "ArticleNo", "var" );
		$myObj->addCol( "ArticleDescription1", "var" );
		$myObj->addCol( "ArticleDescription2", "var" );
		$myObj->addCol( "QuantityText", "var" );
		$reply->replyData = $myObj->tableFromDb( " ", " ", $filter, "ORDER BY C.ArticleNo ASC ", "Article", "Article", "C.Id, C.ArticleNo, C.ArticleDescription1, C.ArticleDescription2, C.QuantityText " );
		FDbg::end();
		return $reply;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @var     string $_key
	 * @var     int $_id
	 * @var     mixed $_val
	 * @var     Reply $reply
	 * @return  Reply
	 */
	function getSPList( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		FDbg::begin( 1, "Article.php", "Article", "getListAsXML( '$_key', $_id, '$_val')" );
		if ( $reply == null ) {
			$reply = new Reply( __class__, $this->className );
		}
		$marketFilter = "shop";
		$articleNoFilter = "";
		$articleDescrFilter = "";
		$articleDescr1Filter = "";
		$articleDescr2Filter = "";
		if ( isset( $_POST[ '_SMarketId' ] ) ) {
			$marketFilter = $_POST[ '_SMarketId' ];
		}
		if ( isset( $_POST[ '_SArticleNo' ] ) ) {
			$articleNoFilter = $_POST[ '_SArticleNo' ];
		}
		if ( isset( $_POST[ '_SDescr' ] ) ) {
			$articleDescrFilter = $_POST[ '_SDescr' ];
		}
		if ( isset( $_POST[ '_SDescr1' ] ) ) {
			$articleDescr1Filter = $_POST[ '_SDescr1' ];
		}
		if ( isset( $_POST[ '_SDescr2' ] ) ) {
			$articleDescr2Filter = $_POST[ '_SDescr2' ];
		}
		$_POST[ '_step' ] = $_id;
		$filter = "(( A.ArticleNo like '" . $articleNoFilter . "%' ) ";
		$filter .= "  AND ( A.ArticleDescription1 like '%" . $articleDescrFilter . "%' OR A.ArticleDescription2 like '%" . $articleDescrFilter . "%' ) ";
		$filter .= "  AND ( C.MarketId = '" . $marketFilter . "' OR C.MarketId = '') ";
		if ( $articleDescr1Filter != "" ) {
			$filter .= "  AND ( ArticleDescription1 like '%" . $articleDescr1Filter . "%' ) ";
		}
		if ( $articleDescr2Filter != "" ) {
			$filter .= "  AND ( A.ArticleDescription2 like '%" . $articleDescr2Filter . "%' ) ";
		}
		$filter .= ") ";
		$ret = "";
		$myObj = new FDbObject( "", "" );                // no specific object we need here
		$myObj->addCol( "Id", "int" );
		$myObj->addCol( "ArticleNo", "var" );
		$myObj->addCol( "ArticleDescription1", "var" );
		$myObj->addCol( "ArticleDescription2", "var" );
		$myObj->addCol( "QuantityText", "var" );
		$myObj->addCol( "Price", "double" );
		$myObj->addCol( "QuantityPerPU", "var" );
		$reply->replyData = $myObj->tableFromDb( ", A.ArticleDescription1 AS ArticleDescription1, A.ArticleDescription2 AS ArticleDescription2, A.QuantityText AS QuantityText ", "LEFT JOIN Article AS A on A.ArticleNo = C.ArticleNo ", $filter, "ORDER BY A.ArticleNo ASC ", "Article",                // Db-Table to read from
			"ArticleSalesPriceCache",            // Camouflage-Table
			"C.Id, C.ArticleNo, C.QuantityPerPU, C.Price " );
		FDbg::end();
		return $reply;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @var     string $_key
	 * @var     int $_id
	 * @var     mixed $_val
	 * @var     Reply $reply
	 * @return  Reply
	 */
	function getPPList( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		$suppNoFilter = $_POST[ '_SSuppNo' ];
		$articleNoFilter = $_POST[ '_SArticleNo' ];
		$articleDescrFilter = $_POST[ '_SDescr' ];
		$_POST[ '_step' ] = $_id;
		
		$filter = "(";
		$filter .= "( C.SupplierNo = '" . $suppNoFilter . "')";
		$filter .= "  AND ( A.ArticleNo like '" . $articleNoFilter . "%' ) ";
		$filter .= "  AND ( A.ArticleDescription1 like '%" . $articleDescrFilter . "%' OR A.ArticleDescription2 like '%" . $articleDescrFilter . "%' ) ";
		if ( $_POST[ '_SDescr1' ] != "" ) {
			$filter .= "  AND ( A.ArticleDescription1 like '%" . $_POST[ '_SDescr1' ] . "%' ) ";
		}
		if ( $_POST[ '_SDescr2' ] != "" ) {
			$filter .= "  AND ( A.ArticleDescription2 like '%" . $_POST[ '_SDescr2' ] . "%' ) ";
		}
		$filter .= ") ";
		$ret = "";
		$myObj = new FDbObject( "", "" );                // no specific object we need here
		$myObj->addCol( "Id", "int" );
		$myObj->addCol( "ArticleNo", "var" );
		$myObj->addCol( "ArticleDescription1", "var" );
		$myObj->addCol( "ArticleDescription2", "var" );
		$myObj->addCol( "Quantity", "int" );
		$myObj->addCol( "Price", "float" );
		$myObj->addCol( "QuantityForPrice", "int" );
		$ret = $myObj->tableFromDb( ", A.ArticleDescription1 AS ArticleDescription1, A.ArticleDescription2 AS ArticleDescription2 ", "LEFT JOIN ArticlePurchasePriceRel AS EKPR on EKPR.SupplierNo = C.SupplierNo AND EKPR.SupplierArticleNo = C.SupplierArticleNo " . "LEFT JOIN Article AS A ON A.ArticleNo = EKPR.ArticleNo " . "LEFT JOIN ArticleStock AS AB ON AB.ArticleNo = A.ArticleNo ", $filter, "ORDER BY A.ArticleNo ASC ", "ArticlePurchasePrice", "ArticlePurchasePrice", "C.Id, C.Quantity, C.Price, C.QuantityForPrice, A.ArticleNo " );
		//		error_log( $ret) ;
		return $ret;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 */
	function rotImageCw( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::begin( 1, "Article.php", "Article", "rotImageCw( '$_key', $_id, '$_val')" );
		$this->_rotImage( "90" );
		FDbg::end( 1, "Article.php", "Article", "rotImage( '$_key', $_id, '$_val')" );
		return $this->getXMLComplete( $_key, $_id, $_val );
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 */
	function _rotImage( $_val = "0" ) {
		FDbg::begin( 1, "Article.php", "Article", "_rotImage( '$_val')" );
		$filename = $this->BildRef;
		$fullPathname = $this->path->Images;
		$fullFilename = $fullPathname . $filename;
		FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "_rotImage( '$_val')", "fullPathname  := '$fullPathname'" );
		FDbg::trace( 1, FDbg::mdTrcInfo1, "Article.php", "Article", "_rotImage( '$_val')", "fullFilename  := '$fullFilename'" );
		if ( chdir( $fullPathname ) ) {
			if ( $this->server->os == "MacOS" ) {
				$sysCmd = "cd " . $this->path->Images . " ; /opt/local/bin/convert " . $fullFilename . " -rotate " . $_val . " " . $fullFilename;
				system( $sysCmd );
				$sysCmd = "cd " . $this->path->Images . " ; export USER=_www ; make ";
				system( $sysCmd );
			} else {
				if ( $this->server->os == "linux" ) {
				
				}
			}
		} else {
			mkdir( $fullPathname );
		}
		FDbg::end( 1, "Article.php", "Article", "_rotImage( '$_val')" );
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 */
	function rotImageCCw( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::begin( 1, "Article.php", "Article", "rotImageCw( '$_key', $_id, '$_val')" );
		$this->_rotImage( "-90" );
		FDbg::end( 1, "Article.php", "Article", "rotImage( '$_key', $_id, '$_val')" );
		return $this->getXMLComplete( $_key, $_id, $_val );
	}
	
	/**
	 * refreshStockPrediction
	 */
	function refreshStockPrediction( $_key = "", $_id = - 1, $_val = "" ) {
		FDbg::begin( 1, "Article.php", "Article", "rotImageCw( '$_key', $_id, '$_val')" );
		FDb::query( "DELETE FROM ArticleStockPredict ArticleNo = '" . $_key . "' " );
		$myArticleAvgNeed = new ArticleAvgNeed();
		$myArticleAvgNeed->fetchFromDbWhere( "ArticleNo = '" . $_key . "' AND Cycle='3m' " );
		$myArticleStockPredict = new ArticleStockPredict();
		$myArticleStockPredict->ArticleNo = $_key;
		$myArticleStockPredict->QtyStock = 0;
		for ( $il0 = 1 ; $il0 <= 26 ; $il0 ++ ) {
			$myArticleStockPredict->Week = $il0;
			$myArticleStockPredict->QtyStock -= $myArticleAvgNeed->Qty / 4;
			$myArticleStockPredict->storeInDb();
		}
		$myArticleStockPredict->setIterCond( "ArticleNo = '" . $_key . "' " );
		$myArticleStockPredict->setIterOrder( "ORDER BY Week ASC " );
		foreach ( $myArticleStockPredict AS $ndx => $obj ) {
			if ( $myArticleStockPredict->QtyStock < 0 ) {
				$myArticleStockPredict->Critical = 1;
				$myArticleStockPredict->updateInDb();
			}
		}
		FDbg::end( 1, "Article.php", "Article", "rotImage( '$_key', $_id, '$_val')" );
		return $this->getTableDepAsXML( $_key, $_id, "ArticleStockPredict" );
	}
	
	/**
	 *
	 */
	function createPDF( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		FDbg::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "( '$_key', $_id, '$_val', <Reply>)" );
		if ( $reply == null ) {
			$reply = new Reply( __class__, $this->className );
		}
		$myArticleLabelDoc = new ArticleLabelDoc( $_key, intval( $_val ) );
		$myName = $myArticleLabelDoc->createPDF( $_key, $_id, $_val );
		$this->pdfName = $myArticleLabelDoc->pdfName;
		$this->fullPDFName = $myArticleLabelDoc->fullPDFName;
		$reply->replyReferences = "<Reference>" . $this->fullPDFName . "</Reference>\n";
		FDbg::end();
		return $reply;
	}
	
	/**
	 * returns the name of the PDF file which has been created
	 */
	function getPDF( $_key = "", $_id = - 1, $_val = "", $reply = null ) {
		FDbg::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "( '$_key', $_id, '$_val', <Reply>)" );
		if ( $reply == null ) {
			$reply = new Reply( __class__, $this->className );
		}
		$reply->replyMediaType = Reply::mediaAppPDF;
		$myArticleLabelDoc = new ArticleLabelDoc( $_key, ArticleLabelDoc::ArticleLabel75x25 );
		$reply->pdfName = $myArticleLabelDoc->pdfName;
		$reply->fullPDFName = $myArticleLabelDoc->fullPDFName;
		FDbg::end();
		return $reply;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param   string $_key
	 * @param   int $_id
	 * @param   mixed $_val
	 * @param   Reply $_reply
	 * @return  Reply
	 */
	function	acList( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		$a_json = array();
		$a_json_row = array();
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$_reply->replyMediaType	=	Reply::mediaTextJSON ;
		$sCrit	=	"" ;
		if ( isset( $_REQUEST['term']))
			$sCrit	=	$_REQUEST['term'] ;
		$myArticle	=	new Article() ;
		$myArticle->setIterCond( "ArticleNo like '%" . $sCrit . "%' OR ArticleDescription1 like '%" . $sCrit . "%'  OR ArticleDescription2 like '%" . $sCrit . "%'  ") ;
		$il0	=	0 ;
		foreach ( $myArticle as $article) {
			if ( $il0 < 50) {
				$a_json_row["id"]		=	$article->Id ;
				$a_json_row["value"]	=	$article->ArticleNo ;
				$a_json_row["label"]	=	$article->ArticleNo . ", " . $article->ArticleDescription1 . ", " . $article->ArticleDescription2 ;
				$a_json_row["Name1"]	=	$article->ArticleDescription1 ;
				array_push( $a_json, $a_json_row);
			}
			$il0++ ;
		}
		$_reply->data = json_encode($a_json);
		FDbg::end() ;
		return $_reply ;
	}
	
	/**
	 *
	 */
	protected function _postInstantiate() {
		FDbg::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "()" );
		FDbg::end();
	}
	
	/**
	 *
	 */
	protected function _postLoad() {
		FDbg::begin( 1, basename( __FILE__ ), __CLASS__, __METHOD__ . "()" );
		FDbg::end();
	}
}
