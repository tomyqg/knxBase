<?php
/**
 * @author	Karl-Heinz Welter <khw@wimtecc.de>
 * @version 0.1
 * @package Application
 * @filesource
 */
/**
 * Supplier - Base Class
 *
 * @package Application
 * @subpackage Supplier
 */
class	Supplier	extends	AppObject	{

	var		$mySupplierContact ;
	const	DOCOC		=	"OC" ;			// order document
	const	DOCPL		=	"PL" ;			// order document
	const	DOCPLU		=	"PLU" ;			//
	const	DOCCAT		=	"CAT" ;			// order confirmation
	const	DOCCATWOP	=	"CATWOP" ;		// order confirmation
	const	DOCCATWP	=	"CATWP" ;		// order confirmation
	const	DOCPRI		=	"PRI" ;			// order confirmation
	const	DOCARTNOMIG	=	"ARTNOMIG" ;	// article no. migration information
	const	DOCMI		=	"MI" ;			// order confirmation
	static	$rDocType	=	array (
						Supplier::DOCOC			=>	"Order confirmation",
						Supplier::DOCPL			=>	"Price List",
						Supplier::DOCPLU		=>	"Price List Update",
						Supplier::DOCCAT		=>	"Catalogue",
						Supplier::DOCCATWOP		=>	"Catalogue w/o prices",
						Supplier::DOCCATWP		=>	"Catalogue w/ prices",
						Supplier::DOCPRI		=>	"Product Information",
						Supplier::DOCARTNOMIG	=>	"Article no. migration info",
						Supplier::DOCMI			=>	"Sonstiges"
					) ;

	const	DISCNO	=	0 ;
	const	DISCVOL	=	1 ;
	const	DISCQTY	=	2 ;
	static	$rDiscModel	=	array(
						Supplier::DISCNO		=>	"No retailer discount",
						Supplier::DISCVOL		=>	"Volume based discount",
						Supplier::DISCQTY		=>	"Qunatity based discount"
					) ;
	/**
	 *
	 */
	function	__construct( $_mySupplierNo="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_mySupplierNo')") ;
		parent::__construct( "Supplier", "SupplierNo") ;
		if ( strlen( $_mySupplierNo) > 0) {
			try {
				$this->setSupplierNo( $_mySupplierNo) ;
				$this->mySupplierContact	=	new SupplierContact() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
		}
		FDbg::end() ;
	}
	
	/**
	 * @param $_mySupplierNo
	 * @throws Exception
	 */
	function	setSupplierNo( $_mySupplierNo) {
		$this->SupplierNo	=	$_mySupplierNo ;
		$this->fetchFromDb() ;
		if ( $this->_valid) {
		} else {
			throw new Exception( "Supplier::setSupplierNo::invalid[$_mySupplierNo]") ;
		}
	}
	
	/**
	 * @param $_mySupplierPrefix
	 * @return bool
	 * @throws Exception
	 */
	function	setSupplierPrefix( $_mySupplierPrefix) {
		$this->SupplierPrefix	=	$_mySupplierPrefix ;
		$this->fetchFromDbWhere( "WHERE SupplierPrefix = '" . $this->SupplierPrefix . "' ") ;
		if ( $this->_valid) {
		} else {
			$e	=	new Exception( "Supplier.php::Supplier::setSupplierNo( '$_liefPrefix'): supplier invalid!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->_valid ;
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	add( $_key="", $_id=-1, $_val="", $_reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$mySupplier	=	new Supplier() ;
		if ( $_val == "SupplierContact"){
			$this->addDep( $_key, $_id, $_val, $_reply) ;
		} else if ( $mySupplier->first( "LENGTH(SupplierNo) = 8", "SupplierNo DESC")) {
			$this->getFromPostL() ;
			$this->SupplierNo	=	sprintf( "%08d", intval( $mySupplier->SupplierNo) + 1) ;
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)", "newKey := " . $this->SupplierNo) ;
			$this->storeInDb() ;
		} else {
			$this->getFromPostL() ;
			$this->SupplierNo	=	sprintf( "%08d", intval( $mySupplier->SupplierNo) + 1) ;
			FDbg::trace( 2, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)", "newKey := " . $this->SupplierNo) ;
			if ( ! $this->storeInDb()) {
				throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')",
					"object[".$this->cacheName."], Supplier invalid after creation!'") ;
			}
		}
		$this->getXMLString( $_key, $_id, $_val, $_reply) ;
		FDbg::end() ;
		return $this->getAsXML() ;
	}
	
	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @return null|Reply|string
	 */
	function	del( $_key="", $_id=-1, $_val="", $_reply = NULL) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val'): begin") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$objName	=	$this->className ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		$objName	=	$this->className . "Kontakt" ;
		$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." like '".$myKey."%' ") ;
		FDbg::end() ;
		return $this->getXMLString() ;
	}
	
	/**
	 * @return string
	 */
	function	getAddrStreet() {
		return $this->Strasse . " " . $this->Hausnummer ;
	}
	
	/**
	 * @return string
	 */
	function	getAddrCity() {
		return $this->PLZ . " " . $this->Ort ;
	}
	
	/**
	 * @return string
	 */
	function	getAddrCountry() {
		switch ( $this->Land) {
		case	"de"	:
			return "" ;
			break ;
		default	:
			$laender	=	Opt::getRLaender() ;
			return $laender[ $this->Land] ;
			break ;
		}
	}
	
	/**
	 * @param string $_nsStart
	 * @param string $_nsEnd
	 * @return int
	 */
	function	newSupplier( $_nsStart="100000", $_nsEnd="199999") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( $_nsStart, $_nsEnd):") ;
		$myQuery	=	"SELECT IFNULL(( SELECT $this->keyCol + 1 FROM $this->className " .
						"WHERE  SupplierNo >= $_nsStart AND SupplierNo <= $_nsEnd " .
						"ORDER BY $this->keyCol DESC LIMIT 1 ), $_nsStart+1)  AS newKey" ;
		$myRow	=	FDb::queryRow( $myQuery) ;
		$keyCol	=	$this->keyCol ;
		$this->$keyCol	=	sprintf( "%06s", $myRow['newKey']) ;
		$this->Tax	=	1 ;
		$this->SupplierPrefix	=	"NEW" ;
		$this->storeInDb() ;
		$this->reload() ;
		FDbg::end() ;
		return $this->_status ;
	}
	
	/**
	 * @param $_date
	 */
	function	deprecatePrices( $_date) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '%s)", $_date) ;
		$query	=	sprintf( "ArticlePurchasePrice_deprecate( @status, '%s', '%s') ", $this->SupplierNo, $_date) ;
		try {
			$result	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::dumpF( "Supplier::deprecatePrices(...): exception='%s'", $e->getMessage()) ;
		}
		FDbg::end() ;
	}
	
	/**
	 * Methode:	incPricesProz( _baseDate, _startDate, _inc)
	 *
	 * Produiziert fuer jeden f�r diesen Suppliereranten existierenden Einkaufspreis mit dem GueltigBis-Datum = _baseDate
	 * einen neuen Einkaufspreis mit dem GueltigVon-Datum = _startDate. Die Preise werden mit den Faktor _inc
	 * multipliziert.
	 */
	function	incPricesProz( $_baseDate, $_startDate, $_inc) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '%s','%s', ...)", $_baseDate, $_startDate) ;
		$query	=	sprintf( "ArticlePurchasePrice_newFromOld( @status, '%s', '%s', '%s', %f) ", $this->SupplierNo, $_baseDate, $_startDate, $_inc) ;
		try {
			$result	=	FDb::callProc( $query) ;
		} catch( Exception $e) {
			FDbg::dumpF( "Supplier::incPricesProz(...): exception='%s'", $e->getMessage()) ;
		}
			FDbg::end() ;
	}
	
	/**
	 * @param $_startDate
	 * @param $_endDate
	 * @throws Exception
	 */
	function	createVKPreise( $_startDate, $_endDate) {
		$query	=	sprintf( "createVKPreisProSupplier( @status, '%s', '%s', '%s') ", $this->SupplierNo, $_startDate, $_endDate) ;
		try {
			$result	=	FDb::callProc( $query) ;
			$this->syncVKPreise( $_startDate, $_endDate) ;
		} catch( Exception $e) {
			throw $e ;
		}
	}
	
	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_bval
	 * @return string
	 * @throws Exception
	 */
	function	checkArticles( $_key="", $_id=-1, $_bval="") {
		try {
			$myArticle	=	new Article() ;
			$myArticlePurchasePriceRel	=	new ArticlePurchasePriceRel() ;
			$myArticlePurchasePrice	=	new ArticlePurchasePrice() ;
			$lastSupplierArtNr	=	"" ;
			/**
			 * loop through all prices for this supplier
			 */
			for ( $myArticlePurchasePrice->_firstFromDb( "SupplierNo = '" . $this->SupplierNo . "' ") ;
					$myArticlePurchasePrice->_valid == 1 ;
					$myArticlePurchasePrice->_nextFromDb()) {
				if ( $myArticlePurchasePrice->SupplierArtNr != $lastSupplierArtNr) {
					try {
						$myArticlePurchasePriceRel->fetchFromDbWhere( "WHERE SupplierNo = '$myArticlePurchasePrice->SupplierNo' AND SupplierArtNr = '$myArticlePurchasePrice->SupplierArtNr' AND KalkBasis = $myArticlePurchasePrice->Quantity ") ;
						if ( $myArticlePurchasePriceRel->_valid) {
							$myArticle->setKey( $myArticlePurchasePriceRel->ArticleNr) ;
							if ( $myArticle->_valid) {

							} else {
								FDbg::dump( " :=> ArticlePurchasePrice.Id := $myArticlePurchasePrice->Id") ;
								FDbg::dump( " :=> Supplier = $myArticlePurchasePrice->SupplierNo, SuppArticleNo = $myArticlePurchasePrice->SupplierArtNr, Article no. = $myArticlePurchasePriceRel->ArticleNr has no Article") ;
							}
						} else {
							if ( $myArticlePurchasePriceRel->_status == -1) {
								FDbg::dump( " :=> ArticlePurchasePrice.Id := $myArticlePurchasePrice->Id") ;
								FDbg::dump( " :=> Supplier = $myArticlePurchasePrice->SupplierNo, SuppArticleNo = $myArticlePurchasePrice->SupplierArtNr has no ArticlePurchasePriceRel") ;
							} else {
								FDbg::dump( " :=> ArticlePurchasePrice.Id := $myArticlePurchasePrice->Id") ;
								FDbg::dump( " :=> Supplier = $myArticlePurchasePrice->SupplierNo, SuppArticleNo = $myArticlePurchasePrice->SupplierArtNr has multiple ArticlePurchasePriceRel") ;
							}
							}
					} catch ( Exception $e) {
						throw $e ;
					}
				}
				$lastSupplierArtNr	=	$myArticlePurchasePrice->SupplierArtNr ;
			}
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	calcPP( $_key="", $_id="", $_val="") {
		if ( $this->AutoPrice == 1) {
			try {
				$myArticle	=	new Article() ;
				$myArticlePurchasePriceRel	=	new ArticlePurchasePriceRel() ;
				$myArticlePurchasePriceRel->setIterCond( "SupplierNo = '" . $this->SupplierNo . "' ") ;
				foreach ( $myArticlePurchasePriceRel as $key => $val) {
					if ( $myArticlePurchasePriceRel->ArticleNr != $myArticle->ArticleNr) {
						$myArticle->setArticleNr( $myArticlePurchasePriceRel->ArticleNr) ;
					}
					try {
						$myArticle->calcPP() ;
					} catch (Exception $e) {
						throw $e ;
					}
				}
			} catch( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "Supplier.php::Supplier::recalcVKPrices(...): supplier does not support automatic calculation!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	calcSP( $_key="", $_id="", $_val="") {
		if ( $this->AutoPrice == 1) {
			try {
				$myArticle	=	new Article() ;
				$myArticlePurchasePriceRel	=	new ArticlePurchasePriceRel() ;
				$myArticlePurchasePriceRel->setIterCond( "SupplierNo = '" . $this->SupplierNo . "' ") ;
				foreach ( $myArticlePurchasePriceRel as $key => $val) {
					if ( $myArticlePurchasePriceRel->ArticleNr != $myArticle->ArticleNr) {
						$myArticle->setArticleNr( $myArticlePurchasePriceRel->ArticleNr) ;
					}
					try {
						$myArticle->calcSP( "", -1, "AND SupplierNo = '" . $this->SupplierNo . "' ") ;
					} catch (Exception $e) {
						throw $e ;
					}
				}
			} catch( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "Supplier.php::Supplier::recalcVKPrices(...): supplier does not support automatic calculation!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	cacheSP( $_key="", $_id="", $_val="") {
		try {
			$myArticle	=	new Article() ;
			$myArticlePurchasePriceRel	=	new ArticlePurchasePriceRel() ;
			$myArticlePurchasePriceRel->setIterCond( "SupplierNo = '" . $this->SupplierNo . "' ") ;
			foreach ( $myArticlePurchasePriceRel as $key => $val) {
				if ( $myArticlePurchasePriceRel->ArticleNr != $myArticle->ArticleNr) {
					$myArticle->setArticleNr( $myArticlePurchasePriceRel->ArticleNr) ;
				}
				try {
					$myArticle->cacheSP( "", -1, "AND SupplierNo = '" . $this->SupplierNo . "' ") ;
				} catch (Exception $e) {
//					throw $e ;
				}
			}
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	calcCacheSP( $_key="", $_id="", $_val="") {
		try {
			$myArticle	=	new Article() ;
			$myArticlePurchasePriceRel	=	new ArticlePurchasePriceRel() ;
			$myArticlePurchasePriceRel->setIterCond( "SupplierNo = '" . $this->SupplierNo . "' ") ;
			foreach ( $myArticlePurchasePriceRel as $key => $val) {
				if ( $myArticlePurchasePriceRel->ArticleNr != $myArticle->ArticleNr) {
					$myArticle->setArticleNr( $myArticlePurchasePriceRel->ArticleNr) ;
				}
				try {
					$myArticle->calcCacheSP( "", -1, "AND SupplierNo = '" . $this->SupplierNo . "' ") ;
				} catch (Exception $e) {
//					throw $e ;
				}
			}
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	
	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return string
	 * @throws Exception
	 */
	function	addRem( $_key="", $_id=-1, $_val="", $_reply=null) {
		try {
			$this->_addRem( $_POST[ '_IRem']) ;
		} catch( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	
	/**
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 * @param null $_reply
	 * @return null|Reply
	 */
	function	getAsXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		else {
			$_reply->instClass	=	__class__ ;
			$_reply->replyingClass	=	$this->className ;
		}
		if ( $_val == "SupplierContact"){
			$this->getDepAsXML( $_key, $_id, $_val, $_reply);
		} else {
			$_reply->replyData	.=	$this->getXMLF() ;
		}
		FDbg::end() ;
		return $_reply ;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AppObject_R2::getDepAsXML()
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	"SupplierContact"	:
			$mySupplierContact	=	new SupplierContact() ;
			if ( $_id == -1) {
				$mySupplierContact->Id	=	-1 ;
			} else {
				$mySupplierContact->setId( $_id) ;
			}
			$_reply->replyData	=	$mySupplierContact->getXMLF() ;
			break ;
		default	:
			$_reply	=	parent::getDepAsXML( $_key, $_id, $_val, $_reply) ;
			break ;
		}
		return $_reply ;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getTableDepAsXML()
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "SupplierContact") ;
		$ret	.=	$this->getTableDepAsXML( $_key, $_id, "SupplierDiscount") ;
		return $ret ;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AppObject::getTableDepAsXML()
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		$tmpObj	=	new $objName() ;
		$tmpObj->setId( $_id) ;
		$_POST['_step']	=	$_id ;
		return $tmpObj->tableFromDb( "", "", "SupplierNo = '$this->SupplierNo' ") ;
	}
	
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	getSupplierContactMailAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		$mySupplierContact	=	new SupplierContact() ;
		if ( $mySupplierContact->setId( $_id)) {
			$ret	.=	"<MailData>" ;
			$ret	.=	"<eMail type='varchar(32)'>" . $mySupplierContact->eMail . "</eMail>" ;
			$ret	.=	"</MailData>" ;
		}
		return $ret ;
	}
	
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	addDep( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val')") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$objName	=	$_val ;
		if ( $objName == "SupplierContact") {
			$mySupplierContact	=	new SupplierContact() ;
			$mySupplierContact->SupplierNo	=	$this->SupplierNo ;
			$mySupplierContact->first( "SupplierNo = '".$this->SupplierNo."'", "SupplierContactNo DESC" ) ;
			$myContactNo	=	$mySupplierContact->SupplierContactNo ;
			$mySupplierContact->getFromPostL() ;
			$mySupplierContact->SupplierContactNo	=	sprintf( "%03d", intval( $myContactNo) + 1) ;
			$mySupplierContact->storeInDb() ;
		} else if ( $objName == "SupplierDiscount") {
			$mySupplierDiscount	=	new SupplierDiscount() ;
			$mySupplierDiscount->SupplierNo	=	$this->SupplierNo ;
			$mySupplierDiscount->getFromPostL() ;
			$mySupplierDiscount->updateInDb() ;
			$this->getList( $_key, $_id, $objName, $_reply) ;
		}
		return $_reply ;
	}
	
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	delArticlePurchasePrice( $_key, $_id, $_val) {
		$ArticlePurchasePrice	=	new ArticlePurchasePrice() ;
		$ArticlePurchasePrice->setId( $_id) ;
		$ArticlePurchasePrice->removeFromDb() ;
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getDocListAsXML( $_key, $_id, $_val) {
		$fullPath	=	$this->path->Archive . "Supplier/" ;	//. $this->SupplierNo . "/" ;
		$myDir	=	opendir( $fullPath) ;
		if ( $myDir) {
			$myFiles	=	array() ;
			while (($file = readdir( $myDir)) !== false) {
				if ( strncmp( $file, $this->SupplierNo, 6) == 0) {
					$myFiles[]	=	$file ;
				}
			}
		}
		closedir( $myDir);
		reset( $myFiles) ;
		asort( $myFiles) ;
		$ret	=	"<DocList>\n" ;
		$ret	.=	"<URLPath>".$this->url->Archive."/Supplier/</URLPath>\n" ;
		foreach ( $myFiles as $file) {
			$ret	.=	"<Doc>\n" ;
			if ( filetype( $fullPath . $file) == "file") {
				$ret	.=	"<RefType>FILESYSTEM</RefType>" ;
				$ret	.=	"<RefNr>FILESYSTEM</RefNr>" ;
				$ret	.=	"<Filename>$file</Filename>\n" ;
				$ret	.=	"<Filetype>" . myFiletype( $file) . "</Filetype>\n" ;
				$ret	.=	"<Filesize>" . filesize( $fullPath . $file) . "</Filesize>\n" ;
				$ret	.=	"<FileURL>" . $this->url->Archive . "Supplier/" . $this->SupplierNo . "/" . $file . "</FileURL>\n" ;
			}
			$ret	.=	"</Doc>\n" ;
		}
		$ret	.=	"</DocList>" ;
		return $ret ;
	}
	
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getArticlePurchasePriceRelListAsXML( $_key, $_id, $_val) {
		/**
		 *
		 */
		$myArticlePurchasePriceRel	=	new ArticlePurchasePriceRel() ;
		$myArticlePurchasePriceRel->startRow	=	$this->startRow ;
		$myArticlePurchasePriceRel->rowCount	=	$this->rowCount ;
		$myArticlePurchasePriceRel->addCol( "SupplierVKPreis", "dou") ;
		$myArticlePurchasePriceRel->addCol( "Preis", "dou") ;
		$myArticlePurchasePriceRel->addCol( "OwnVKPreis", "dou") ;
		$myArticlePurchasePriceRel->addCol( "RealMargin", "float") ;
		$myArticlePurchasePriceRel->addCol( "ArticleBez1", "var") ;
		$ret	=	"<ArticlePurchasePriceRelList>\n" ;
		$crit	=	$_POST['_ISupplierArticlePurchasePriceRelCrit'] ;
		$_POST['_step']	=	$_val ;
		switch ( $_id) {
		case	0	:
			$myArticlePurchasePrice	=	new ArticlePurchasePrice() ;
			$myArticlePurchasePrice->addCol( "ArticleNr", "var") ;
			$ret	.=	$myArticlePurchasePrice->tableFromDb( ", A.ArticleNr ",
							"LEFT JOIN Article AS A ON A.ArticleNr = CONCAT( '$this->SupplierPrefix', '.', C.SupplierArtNr) ",
							"C.SupplierNo = '" . $this->SupplierNo . "' AND NOT EXISTS ( SELECT D.SupplierArtNr FROM ArticlePurchasePriceRel AS D WHERE D.SupplierNo = C.SupplierNo AND D.SupplierArtNr = C.SupplierArtNr ) ",
							"ORDER BY C.SupplierArtNr ",
							"SupplierArticlePurchasePriceRel") ;
			break ;
		case	1	:
			$ret	.=	$myArticlePurchasePriceRel->tableFromDb( ", DO.SupplierVKPreis, DO.Preis, DO.OwnVKPreis ",
							"LEFT JOIN ArticlePurchasePrice AS DO ON DO.SupplierNo = C.SupplierNo AND DO.SupplierArtNr = C.SupplierArtNr AND DO.Quantity = C.KalkBasis ",
							"C.SupplierNo = '" . $this->SupplierNo . "' AND NOT EXISTS ( SELECT D.ArticleNr FROM Article AS D WHERE D.ArticleNr = C.ArticleNr) AND ( C.SupplierArtText like '%".$crit."%' OR C.ArticleNr like '%".$crit."%') ",
							"ORDER BY C.SupplierArtNr ",
							"SupplierArticlePurchasePriceRel") ;
			break ;
		case	2	:
			$ret	.=	$myArticlePurchasePriceRel->tableFromDb( ", A.ArticleBez1, DO.SupplierVKPreis AS SupplierVKPreis, DO.Preis AS Preis, DO.OwnVKPreis AS OwnVKPreis, DO.OwnVKPreis / DO.Preis AS RealMargin ",
							"LEFT JOIN ArticlePurchasePrice AS DO ON DO.SupplierNo = C.SupplierNo AND DO.SupplierArtNr = C.SupplierArtNr AND DO.Quantity = C.KalkBasis " .
							"LEFT JOIN Article AS A ON A.ArticleNr = C.ArticleNr ",
							"C.SupplierNo = '" . $this->SupplierNo . "' AND EXISTS ( SELECT D.ArticleNr FROM Article AS D WHERE D.ArticleNr = C.ArticleNr) ",
							"ORDER BY C.SupplierArtNr ",
							"SupplierArticlePurchasePriceRel") ;
			break ;
		}
		$ret	.=	"</ArticlePurchasePriceRelList>\n" ;
		return $ret ;
	}
	
	/**
	 * assigns EPR numbers to all articles specific to this supplier and for which no ERP number has been
	 * assigned so far
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	assignERPNos( $_key="", $_id=-1, $_val="") {
		try {
			if ( $this->SupplierPrefix == "") {
				$e	=	"Supplier.php::Supplier::assignERPNos(...): supplier does not have prefix assigned" ;
				error_log( $e) ;
				throw new Exception( $e) ;
			}
			if ( $this->ERPNoStart == "") {
				$e	=	"Supplier.php::Supplier::assignERPNos(...): supplier does not have vallid ERP no. range" ;
				error_log( $e) ;
				throw new Exception( $e) ;
			}
			$myArticle	=	new Article() ;
			for ( $myArticle->_firstFromDb( "ArticleNr like '" . $this->SupplierPrefix . ".%' AND ERPNo = '' ") ;
					$myArticle->_valid ;
					$myArticle->_firstFromDb( "ArticleNr like '" . $this->SupplierPrefix . ".%' AND ERPNo = '' ")) {
				FDbg::dumpL( "0x00000002", "Supplier.php::Supplier::assignERPNo(...): current article no. '$myArticle->ArticleNr'") ;
				try {
					$erpNoStart	=	$this->ERPNoStart ;
					$erpNoEnd	=	$this->ERPNoEnd ;
					$myQuery	=	"SELECT IFNULL(( SELECT ERPNo + 1 FROM Article " .
									"WHERE ArticleNr LIKE '".$this->SupplierPrefix.".%' AND ERPNo >= '$erpNoStart' AND ERPNo <= '$erpNoEnd' " .
									"ORDER BY ERPNo DESC LIMIT 1 ), " . ( $erpNoStart + 1) . " )  AS newKey" ;
					$myRow	=	FDb::queryRow( $myQuery) ;
					$myArticle->ERPNo	=	sprintf( "%08s", $myRow['newKey']) ;
					$myArticle->updateColInDb( "ERPNo") ;
				} catch (Exception $e) {
					throw $e ;
				}
			}
		} catch( Exception $e) {
			throw $e ;
		}
		return true ;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @var unknown_type
	 */
	function	getList( $_key="", $_id=-1, $_val="", $_reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_id, '$_val', <Reply>)") ;
		if ( $_reply == null)
			$_reply	=	new Reply( __class__, $this->className) ;
		$sCrit	=	"" ;
		if ( isset( $_POST['Search']))
			$sCrit	=	$_POST['Search'] ;
		$objName	=	$_val ;
		switch ( $objName) {
		case	""	:
		case	"Supplier"	:
			$myObj	=	new FDbObject( "Supplier", "SupplierNo", "def", "v_SupplierSurvey") ;
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$filter1	=	"SupplierName like '%" . $sCrit . "%' " ;
			$filter2	=	"" ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$_reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		case	"SupplierContact"	:
			$myObj	=	new FDbObject( "v_SupplierContactSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( SupplierNo = '" . $this->SupplierNo . "') " ;
			$filter2	=	"( FirstName like '%" . $sCrit . "%' OR LastName  like '%" . $sCrit . "%') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1, $filter2]) ;
			$myQuery->addOrder( ["SupplierNo", "SupplierContactNo"]) ;
			$_reply->replyData	=	$myObj->tableFromQuery( $myQuery, "SupplierContact") ;
			break ;
		case	"SupplierDiscount"	:
			$myObj	=	new FDbObject( "SupplierDiscount", "Id", "def", "v_SupplierDiscountSurvey") ;				// no specific object we need here
			if ( isset( $_POST['StartRow'])) {
				$myObj->setPage( intval( $_POST['StartRow']), intval( $_POST['RowCount']), $_POST['step']) ;
			}
			$_customerNoCrit	=	$sCrit ;
			$filter1	=	"( SupplierNo = '" . $this->SupplierNo . "') " ;
			/**
			 *
			 */
			$myQuery	=	$myObj->getQueryObj( "Select") ;
			$myQuery->addWhere( [ $filter1]) ;
			$myQuery->addOrder( ["SupplierNo"]) ;
			$_reply->replyData	=	$myObj->tableFromQuery( $myQuery) ;
			break ;
		}
		FDbg::end() ;
		return $_reply ;
	}
	
	/**
	 *
	 */
	protected	function	_postInstantiate() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
	
	/**
	 *
	 */
	protected	function	_postLoad() {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		FDbg::end() ;
	}
}