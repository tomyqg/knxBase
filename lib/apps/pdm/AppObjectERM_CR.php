<?php
/**
 * AppObjectERM_CR - Application Object for ERM Customer Relation
 *
 * Base class for all objects which deal with customer relations,
 * ie. CustomerRFQ, CustomerQuotation, CustomerOrderTemp CustomerOrder, CustoemrCOmmission, CustomerDelivery, CustomerInvoice.
 *
 * This class adds stuff to deal with:
 * - Customer Address
 * - Customer Delivery Address
 * - Customer Invoicing Address
 *
 * @author [wimteccgen] Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package wtcAppERPBaseObjects
 * @filesource
 */
/**
 * DbObject - Basis Klasse
 *
 * Automatisch generierter Code. �nderungen ausschliesslich
 * �ber die entsp. <Basisklasse>.xml Datei !!!
 *
 * @package wtcAppERPBaseObjects
 * @subpackage AppObject_R2s
 */
class	AppObjectERM_CR	extends	AppObjectERM	{
	/**
	 *
	 * @var unknown_type
	 */
	public	$Customer ;
	public	$CustomerContact ;
	public	$InvoiceCustomer ;
	public	$InvoiceCustomerContact ;
	public	$DeliveryCustomer ;
	public	$DeliveryCustomerContact ;
	/**
	 *
	 */
	const	ORD_BY_INOASINOA	=	"INoASINoA" ;		// order confirmation
	const	ORD_BY_INODSINOA	=	"INoSINoD" ;		// order confirmation
	const	ORD_BY_ARTNO	=	"ArtNo" ;		// order confirmation
	private	static	$rOrdMode	=	array (
						AppObjectERM_CR::ORD_BY_INOASINOA	=> "ItemNo, SubItemNo ASC",
						AppObjectERM_CR::ORD_BY_INODSINOA	=> "ItemNo DESC, SubItemNo ASC",
						AppObjectERM_CR::ORD_BY_ARTNO	=> "ArticleNo"
					) ;
	function	getROrdMode() {		return self::$rOrdMode ;			}
	/**
	 *
	 * @param string $_className
	 * @param string $_keyColName
	 */
	function	__construct( $_className, $_keyColName, $_db="def", $_tableName="") {
		parent::__construct( $_className, $_keyColName, $_db, $_tableName) ;
	}
	/**
	 * (non-PHPdoc)
	 * @see DbObject::reload()
	 */
	function	reload( $_db="def") {
		$this->fetchFromDb() ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
	/**
	 * methods: addDep/updDep/copyDep/delDep
	 */
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @param string $_posNrPrefix
	 * @throws Exception
	 */
	function	expandDep( $_key="", $_id=-1, $_val="", $_posNrPrefix="") {
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$_val ;
		/**
		 *
		 */
		$subArticle	=	new Article( "") ;
		try {
			$actItem	=	new $itemObjClassName() ;
			$actItem->Id	=	$_id ;
			$actItem->fetchFromDbById() ;
			if ( $actItem->_valid) {
				$myArticle	=	new Article( $actItem->ArticleNo) ;
				if ( $myArticle->_valid == 1) {
					if ( $myArticle->CompositionType == Article::COMP10) {
						$myArticleComponent	=	new ArticleComponent() ;
						$cond	=	sprintf( "ArticleNo='%s' ", $myArticle->ArticleNo) ;
						$subItemNo	=	0 ;
						$myArticleComponent->_firstFromDb( $cond) ;
						while ( $myArticleComponent->_valid) {
							$subItemNo++ ;
							$subArticle->setArticleNo( $myArticleComponent->CompArticleNo) ;
							/**
							 * find the article which we needs to add
							 * means: as long as the article has a new article associated or as there is a
							 * replacement article, go to this
							 * article
							 */
							while ( strlen( $subArticle->ArticleNoNew) > 0 || strlen( $subArticle->ArticleNoReplacement) > 0) {
								if ( strlen( $subArticle->ArticleNoNew) > 0) {
									$subArticle->setArticleNo( $subArticle->ArticleNoNew) ;
								} else if ( strlen( $subArticle->ArticleNoReplacement) > 0) {
									$subArticle->setArticleNo( $subArticle->ArticleNoReplacement) ;
								}
							}
							/**
							 * @var unknown_type
							 */
							$newItem	=	new $itemObjClassName( $myKey) ;
							$newItem->ItemNo	=	$actItem->ItemNo ;
							$newItem->SubItemNo	=	sprintf( "%s.%02d", $_posNrPrefix, $subItemNo) ;
							$newItem->PosType	=	0 ;
							$newItem->ArticleNo	=	$subArticle->ArticleNo ;
							$newItem->Quantity	=	$actItem->Quantity * $myArticleComponent->CompQuantity ;
							$newItem->Price	=	0.0 ;
							$newItem->ReferencePrice	=	0.0 ;
							$newItem->QuantityPerPU	=	$myArticleComponent->CompQuantityPerPU ;
							$newItem->TotalPrice	=	0.0 ;
							$newItem->storeInDb() ;
							if ( $subArticle->Comp == Article::COMP10) {
								$this->addSubPos( $newItem->ItemNo, $subArticle->ArticleNo, $actItem->Quantity * $myArticleComponent->CompQuantity, $newItem->SubItemNo) ;
							}
							$myArticleComponent->_nextFromDb() ;
						}
					} else {
						$e	=	new Exception( "AppObjectERM_CR.php::AppObject_R2::expandPos: Article hat keine Komponenten!") ;
						error_log( $e) ;
						throw $e ;
					}
				} else {
					$e	=	new Exception( "AppObjectERM_CR.php::AppObjectERM_CR::expandPos(...): Article mit der ArticleNo=$actKdLiefPos->ArticleNo existiert nicht") ;
					error_log( $e) ;
					throw $e ;
				}
			} else {
				$e	=	new Exception( "AppObjectERM_CR.php::AppObjectERM_CR::expandPos(...): Item mit der Id=$_id existiert nicht") ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw( $e) ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @throws Exception
	 */
	function	collapseDep( $_key="", $_id=-1, $_val="") {
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$_val ;
		/**
		 *
		 */
		try {
			$tmpItem	=	new $itemObjClassName() ;
			$tmpItem->Id	=	$_id ;
			$tmpItem->fetchFromDbById() ;
			if ( $tmpItem->_valid) {
				$query	=	sprintf( "DELETE FROM $itemObjClassName WHERE $myKeyCol = '$myKey' AND ItemNo = '$tmpItem->ItemNo' AND SubItemNo != ''") ;
				FDb::query( $query) ;
			} else {
				$e	=	new Exception( "KdLief::expandPos(...): Item mit der Id=$_id existiert nicht") ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw( $e) ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 * methods: business logic
	 */
	/**
	 *
	 * @param string $_key
	 * @param unknown $_kkId
	 * @param string $_val
	 * @throws Exception
	 */
	function	setCustomerFromKKId( $_key="", $_kkId=-1, $_val="") {
		$tmpCustomerContact	=	new CustomerContact() ;
		$tmpCustomerContact->setId( $_kkId) ;
		if ( $tmpCustomerContact->_valid) {
			try {
				$this->CustomerNo	=	$tmpCustomerContact->CustomerNo ;
				$this->CustomerContactNo	=	$tmpCustomerContact->CustomerContactNo ;
				$this->updateInDb() ;
				$this->reload() ;
				$this->ModusLief	=	$this->Customer->ModusLief ;
				$this->ModusRech	=	$this->Customer->ModusRech ;
				$this->ModusSkonto	=	$this->Customer->ModusSkonto ;
				$this->Rabatt	=	$this->Customer->Rabatt ;
				if ( $this->Rabatt > 0) {
					$this->DiscountMode	=	Opt::DMDEALER ;
				} else {
					$this->DiscountMode	=	Opt::DMV1 ;
				}
				$this->updateInDb() ;
				$this->reload() ;
			} catch( Exception $e) {
				throw $e ;
			}
		} else {
			error_log( "AppObjectERM_CR.php::AppObjectERM_CR::setCustomerFromKKId(...): CustomerContact not valid !") ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param unknown $_kkId
	 * @param string $_val
	 * @throws Exception
	 */
	function	setInvoiceCustomerFromKKId( $_key="", $_kkId=-1, $_val="") {
		$tmpCustomerContact	=	new CustomerContact() ;
		$tmpCustomerContact->setId( $_kkId) ;
		if ( $tmpCustomerContact->_valid) {
			try {
				$this->InvoiceCustomerNo	=	$tmpCustomerContact->CustomerNo ;
				$this->InvoiceCustomerContactNo	=	$tmpCustomerContact->CustomerLKontaktNr ;
				$this->updateInDb() ;
				$this->reload() ;
			} catch( Exception $e) {
				throw $e ;
			}
		} else {
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @throws Exception
	 */
	function	clearInvoiceCustomer() {
		try {
			$this->InvoiceCustomerNo	=	"" ;
			$this->InvoiceCustomerContactNo	=	"" ;
			$this->updateInDb() ;
			$this->reload() ;
		} catch( Exception $e) {
				throw $e ;
		}
	}
	/**
	 *
	 * @param string $_key
	 * @param unknown $_kkId
	 * @param string $_val
	 * @throws Exception
	 */
	function	setDeliveryCustomerFromKKId( $_key="", $_kkId=-1, $_val="") {
		$tmpCustomerContact	=	new CustomerContact() ;
		$tmpCustomerContact->setId( $_kkId) ;
		if ( $tmpCustomerContact->_valid) {
			try {
				$this->DeliveryCustomerNo	=	$tmpCustomerContact->CustomerNo ;
				$this->DeliveryCustomerContactNo	=	$tmpCustomerContact->CustomerContactNo ;
				$this->updateInDb() ;
				$this->reload() ;
			} catch( Exception $e) {
				throw $e ;
			}
		} else {
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @throws Exception
	 */
	function	clearDeliveryCustomer() {
		try {
			$this->DeliveryCustomerNo	=	"" ;
			$this->DeliveryCustomerContactNo	=	"" ;
			$this->updateInDb() ;
			$this->reload() ;
		} catch( Exception $e) {
			throw $e ;
		}
	}
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return string
	 */
	function	setTexte( $_key="", $_id=-1, $_val="") {
		$this->_setTexte( $_key, $_id, $_val) ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @return string
	 */
	function	setAnschreiben( $_key="", $_id=-1, $_val="") {
		$this->_setAnschreiben( $_key, $_id, $_val) ;
		return $this->getXMLString() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 */
	function	updPrices( $_key="", $_id=-1, $_val="") {
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$this->className . "Item" ;
		/**
		 *
		 */
		$actArticleSalesPriceCache	=	new ArticleSalesPriceCache() ;
		$actItem	=	new $itemObjClassName( $_key) ;
		$actItem->setIterCond( "$myKeyCol = '$myKey' ") ;
		foreach ( $actItem as $key => $val) {
			if ( $actItem->SubItemNo == "") {
				if ( $actArticleSalesPriceCache->fetchFromDbWhere( "WHERE ArticleNo = '$actItem->ArticleNo' AND QuantityPerPU = $actItem->QuantityPerPU ")) {
					$actItem->Price	=	$actArticleSalesPriceCache->Price ;
					$actItem->ReferencePrice	=	$actArticleSalesPriceCache->Price ;
					$actItem->TotalPrice	=	$actItem->Quantity * $actItem->Price ;
					$actItem->updateInDb() ;
				}
			}
		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 */
	function	restate( $_key="", $_id=-1, $_val="") {
		$this->_restate( $_key, $_id, $_val) ;
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param string $_val
	 */
	function	recalc( $_key="", $_id=-1, $_val="") {
		require_once( "Calc.php") ;
		$calcModell	=	"Calc_" . $this->DiscountMode ;
		$myCalc	=	new $calcModell() ;
		$myCalc->recalc( $this) ;
		$this->StatusInfo	.=	"Neukalkulation abgeschlossen" ;
// 		switch ( $this->DiscountMode) {
// 		case	Opt::DMV1	:
// 			$this->_recalcDM10( $_key, $_id, $_val) ;
// 			break ;
// 		case	Opt::DMV2	:
// 			$this->_recalcDM20( $_key, $_id, $_val) ;
// 			break ;
// 		case	Opt::DMV3	:
// 			$this->_recalcDM30( $_key, $_id, $_val) ;
// 			break ;
// 		}
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * @param unknown $_artikelNr
	 * @param unknown $_vkpid
	 * @param unknown $_menge
	 */
	function	addPos( $_key="", $_vkpid=-1, $_val="", $reply=null) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_key', $_vkpid, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
 		$_result	=	$this->_addPos( $_key, $_vkpid, 1) ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "sub-class := '$_result'") ;
 		$result	=	$this->getList( $_key, $_id, "CustomerOrderItem") ;
		FDbg::end() ;
		return $result ;
	}
	function	addSubPos( $_posNr, $_artikelNr, $_menge, $_posNrPrefix) {
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$this->className . "Item" ;
		/**
		 * let's go
		 */
		$myArticle	=	new Article( $_artikelNr) ;
		$subArticle	=	new Article( "") ;
		if ( $myArticle->_valid == 1) {
			if ( $myArticle->Comp == 0) {
			} else {
				$myArticleComponent	=	new ArticleComponent() ;
				$cond	=	sprintf( "ArticleNo='%s' ", $_artikelNr) ;
				$subItemNo	=	0 ;
				$myArticleComponent->_firstFromDb( $cond) ;
				while ( $myArticleComponent->_valid) {
					$subItemNo++ ;
					$subArticle->setArticleNo( $myArticleComponent->CompArticleNo) ;
					/**
					 * find the article which we needs to add
					 * means: as long as the article has a new article associated or as there is a
					 * replacement article, go to this
					 * article
					 */
					while ( strlen( $subArticle->ArticleNoNew) > 0 || strlen( $subArticle->ArticleNoReplacement) > 0) {
						if ( strlen( $subArticle->ArticleNoNew) > 0) {
							$subArticle->setArticleNo( $subArticle->ArticleNoNew) ;
						} else if ( strlen( $subArticle->ArticleNoReplacement) > 0) {
							$subArticle->setArticleNo( $subArticle->ArticleNoReplacement) ;
						}
					}
					$newItem	=	new $itemObjClassName() ;
					$newItem->$myKeyCol	=	$myKey ;
					$newItem->ItemNo	=	$_posNr ;
					$newItem->SubItemNo	=	sprintf( "%s.%02d", $_posNrPrefix, $subItemNo) ;
					$newItem->PosType	=	0 ;
					$newItem->ArticleNo	=	$subArticle->ArticleNo ;
					$newItem->Quantity	=	$_menge * $myArticleComponent->CompQuantity ;
					$newItem->Price	=	0.0 ;
					$newItem->ReferencePrice	=	0.0 ;
					$newItem->QuantityPerPU	=	$myArticleComponent->CompQuantityPerPU ;
					$newItem->TotalPrice	=	0.0 ;
					$newItem->storeInDb() ;
					if ( $subArticle->CompositionType > 0) {
						$this->addSubPos( $_posNr, $subArticle->ArticleNo, $_menge * $myArticleComponent->CompQuantity, $newItem->SubItemNo) ;
					}
					$myArticleComponent->_nextFromDb() ;
				}
			}
		}
		return $itemObjClassName ;
	}
	function	newCustomer( $_key, $_val, $_id) {
		try {
			$newCustomer	=	new Customer() ;
			$newCustomer->add() ;
			$this->CustomerNo	=	$newCustomer->CustomerNo ;
			$this->CustomerContactNo	=	$newCustomer->_addDep( "", -1, "CustomerContact") ;
			$this->DeliveryCustomerNo	=	"" ;
			$this->DeliveryCustomerContactNo	=	"" ;
			$this->InvoiceCustomerNo	=	"" ;
			$this->InvoiceCustomerContactNo	=	"" ;
			$this->updateInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newCustomerContact( $_key, $_val, $_id) {
		try {
			$newCustomer	=	new Customer( $this->CustomerNo) ;
			if ( $newCustomer->isValid()) {
				$this->CustomerContactNo	=	$newCustomer->_addDep( "", -1, "CustomerContact") ;
				$this->updateColInDb( "CustomerNo") ;
				$this->updateColInDb( "CustomerContactNo") ;
			} else {
				$e	=	"AppObjectERM_CR.php::AppObjectERM_CR::newDeliveryCustomerContact(...): invalid customer!" ;
				throw new Exception( $e) ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newDeliveryCustomer( $_key, $_val, $_id) {
		try {
			$newCustomer	=	new Customer() ;
			$newCustomer->CustomerNo	=	$this->CustomerNo ;
			$newCustomerNo	=	$newCustomer->_addDep( "", -1, "DeliveryCustomer") ;
			$newCustomer->CustomerNo	=	$newCustomerNo ;
			$newCustomer->reload() ;
			$this->DeliveryCustomerNo	=	$newCustomerNo ;
			$this->DeliveryCustomerContactNo	=	$newCustomer->_addDep( "", -1, "CustomerContact") ;
			$this->updateInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newDeliveryCustomerContact( $_key, $_val, $_id) {
		try {
			$newCustomer	=	new Customer( $this->DeliveryCustomerNo) ;
			if ( $newCustomer->isValid()) {
				$this->DeliveryCustomerContactNo	=	$newCustomer->_addDep( "", -1, "CustomerContact") ;
				$this->updateInDb() ;
			} else {
				$e	=	"AppObjectERM_CR.php::AppObjectERM_CR::newDeliveryCustomerContact(...): invalid delivery customer!" ;
				throw new Exception( $e) ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newInvoiceCustomer( $_key, $_val, $_id) {
		try {
			$newCustomer	=	new Customer() ;
			$newCustomer->CustomerNo	=	$this->CustomerNo ;
			$newCustomerNo	=	$newCustomer->_addDepCustomer( "R") ;
			$newCustomer->CustomerNo	=	$newCustomerNo ;
			$newCustomer->reload() ;
			$this->InvoiceCustomerNo	=	$newCustomerNo ;
			$this->InvoiceCustomerContactNo	=	$newCustomer->_addDep( "", -1, "CustomerContact") ;
			$this->updateInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newInvoiceCustomerContact( $_key, $_val, $_id) {
		try {
			$newCustomer	=	new Customer( $this->InvoiceCustomerNo) ;
			if ( $newCustomer->isValid()) {
				$this->InvoiceCustomerContactNo	=	$newCustomer->_addDep( "", -1, "CustomerContact") ;
				$this->updateInDb() ;
			} else {
				$e	=	"AppObjectERM_CR.php::AppObjectERM_CR::newDeliveryCustomerContact(...): invalid invoicing customer!" ;
				throw new Exception( $e) ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newAddCustomer( $_key, $_val, $_id) {
		try {
			$kdNr	=	explode( "-", $this->CustomerNo) ;		// remove possible -xxxx appendix
			$newCustomer	=	new Customer() ;
			$newCustomer->CustomerNo	=	$kdNr[0] ;
			$newCustomerNo	=	$newCustomer->_addDepCustomer( "A") ;
			$newCustomer->CustomerNo	=	$newCustomerNo ;
			$newCustomer->reload() ;
			$this->CustomerNo	=	$newCustomerNo ;
			$this->CustomerContactNo	=	$newCustomer->_addDep( "", -1, "CustomerContact") ;
			$this->updateInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newAddCustomerContact( $_key, $_val, $_id) {
		try {
			$newCustomer	=	new Customer( $this->DeliveryCustomerNo) ;
			if ( $newCustomer->isValid()) {
				$this->AddCustomerContactNo	=	$newCustomer->_addDep( "", -1, "CustomerContact") ;
				$this->updateInDb() ;
			} else {
				$e	=	"AppObjectERM_CR.php::AppObjectERM_CR::newDeliveryCustomerContact(...): invalid delivery customer!" ;
				throw new Exception( $e) ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	setDM10( $_key="", $_id=-1, $_val="") {
		/**
		 * prepare, determine all required classes
		 */
		$this->DiscountMode	=	Opt::DMV1 ;
		$this->updateColInDb( "DiscountMode") ;
		$this->Rabatt	=	0.0 ;
		$this->updateColInDb( "Rabatt") ;
		$this->recalc(  $_key, $_id, $_val) ;		// recalc with the new discount mode
		$this->restate(  $_key, $_id, $_val) ;		// determine line total and grand total
		return $this->getXMLComplete() ;
	}
	function	setDM20( $_key="", $_id=-1, $_val="") {
		/**
		 * prepare, determine all required classes
		 */
		$this->DiscountMode	=	Opt::DMV2 ;
		$this->updateColInDb( "DiscountMode") ;
		$this->recalc(  $_key, $_id, $_val) ;		// recalc with the new discount mode
		$this->restate(  $_key, $_id, $_val) ;		// determine line total and grand total
		return $this->getXMLComplete() ;
	}
	function	setDM30( $_key="", $_id=-1, $_val="") {
		/**
		 * prepare, determine all required classes
		 */
		$this->DiscountMode	=	Opt::DMV3 ;
		$this->updateColInDb( "DiscountMode") ;
		$this->recalc(  $_key, $_id, $_val) ;		// recalc with the new discount mode
		$this->restate(  $_key, $_id, $_val) ;		// determine line total and grand total
		return $this->getXMLComplete() ;
	}
	/**
	 *
	 * Enter description here ...
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	getList( $_key="", $_id=-1, $_val="", $reply=null) {
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		if ( isset( $_POST['_S'.$this->keyCol]))
			$_searchCrit	=	$_POST['_S'.$this->keyCol] ;
		else
			$_searchCrit	=	"" ;
		if ( isset( $_POST['_SStatus']))
			$_sStatus	=	intval( $_POST['_SStatus']) ;
		else
			$_sStatus	=	-1 ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( " ;
		$filter	.=	"C.".$this->keyCol." like '%" . $_searchCrit . "%' " ;
		if ( isset( $_POST['_SCompany']))
			if ( $_POST['_SCompany'] != "")
				$filter	.=	"  AND ( CustomerName1 like '%" . $_POST['_SCompany'] . "%' OR CustomerName2 LIKE '%" . $_POST['_SCompany'] . "%') " ;
		if ( isset( $_POST['_SZIP']))
			if ( $_POST['_SZIP'] != "")
				$filter	.=	"  AND ( ZIP like '%" . $_POST['_SZIP'] . "%' ) " ;
		if ( isset( $_POST['_SContact']))
			if ( $_POST['_SContact'] != "")
				$filter	.=	"  AND ( LastName like '%" . $_POST['_SContact'] . "%' OR LastName LIKE '%" . $_POST['_SContact'] . "%') " ;
		if ( $_sStatus != -1) {
			$filter	.=	"AND ( C.Status = " . $_sStatus . ") " ;
		}
		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( $this->keyCol, "var") ;
		$myObj->addCol( "Date", "var") ;
		$myObj->addCol( "Status", "var") ;
		$myObj->addCol( "CustomerName1", "var") ;
		$myObj->addCol( "ZIP", "var") ;
		$myObj->addCol( "Vorname", "var") ;
		$myObj->addCol( "Name", "var") ;
		$reply->replyData	=	$myObj->tableFromDb( ",K.CustomerName1,K.CustomerName2,K.ZIP,KK.FirstName,KK.LastName ",
								"JOIN Customer AS K ON K.CustomerNo = C.CustomerNo "
									. "LEFT JOIN CustomerContact AS KK ON KK.CustomerNo = C.CustomerNo AND KK.CustomerContactNo = C.CustomerContactNo ",
								$filter,
								"ORDER BY C.".$this->keyCol." DESC ",
								$this->className,
								$this->className,
								"C.Id, C.".$this->keyCol.", C.Date") ;
		return $reply ;
	}
	/**
	 * methods: object retrieval
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="", $reply=null) {
		FDbg::begin( 1, "AppObjectERM_CR.php", "AppObjectERM_CR", "getTableDepAsXML( '$_key', $_id, '$_val')") ;
		if ( $reply == null)
			$reply	=	new Reply( __class__, $this->className) ;
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$tmpObj	=	new $objName() ;
		$tmpObj->addCol( "ERPNo", "var") ;
		$tmpObj->addCol( "ArticleDescription1", "var") ;
		$tmpObj->addCol( "ArticleDescription2", "var") ;
		$tmpObj->addCol( "QuantityText", "var") ;
		$tmpObj->addCol( "ArticleDescription", "var") ;
		$ordBy	=	"ORDER BY C.ItemNo, C.SubItemNo " ;
		if ( isset( $_POST[ '_SOrdMode']))
			$ordBy	=	"ORDER BY " . self::$rOrdMode[ $_POST[ '_SOrdMode']] ;
		$reply->replyData	=	$tmpObj->tableFromDb( ", A.ERPNo, A.ArticleDescription1, A.ArticleDescription2, A.QuantityText, CONCAT( A.ArticleDescription1, \" \", A.ArticleDescription2) AS ArticleDescription ",
								"LEFT JOIN Article AS A ON A.ArticleNo = C.ArticleNo",
								"C." . $myKeyCol . " = '" . $myKey . "' ",
								$ordBy) ;
		FDbg::end() ;
		return $reply ;
	}
	/**
	 *
	 */
	function	getCustomer() {
		if ( $this->Customer == null) {
			$this->Customer	=	new Customer( $this->CustomerNo) ;
		}
		return $this->Customer ;
	}
	function	getCustomerContact() {
		if ( $this->CustomerContact == null) {
			$this->CustomerContact	=	new CustomerContact( $this->CustomerNo, $this->CustomerContactNo) ;
		}
		return $this->CustomerContact ;
	}
	function	getDeliveryCustomer()			{	return $this->DeliveryCustomer ;			}
	function	getDeliveryCustomerContact()	{	return $this->DeliveryCustomerContact ;	}
	function	getInvoiceCustomer()			{	return $this->InvoiceCustomer ;			}
	function	getInvoiceCustomerContact()	{	return $this->InvoiceCustomerContact ;	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	/**
	 * Add a new item to the article list
	 *
	 * @param	string	article no.
	 * @param	int		id of sales price
	 * @param	int		quantity of items to be added
	 * @return	void
	 */
	/**
	 *
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	/**
	 * Performs a recalculation of all derived values
	 *
	 * @return void
	 */
	function	_restate( $_key="", $_id=-1, $_val="") {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$totalPerTaxClass	=	array() ;
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		if ( $this->className == "CuCart") {
			$itemObjClassName	=	$this->className . "Item" ;
		} else {
			$itemObjClassName	=	$this->className . "Item" ;
		}
		$actItem	=	new $itemObjClassName( $myKey) ;
		$actArticle	=	new Article() ;
		$actTax	=	new Tax() ;
		try {
			$totalNet	=	0.0 ;
			$actItem->setIterCond( "$myKeyCol = '$myKey' ") ;
			$actItem->setIterOrder( "ItemNo, SubItemNo ") ;
			foreach ( $actItem as $obj) {
				if ( $actItem->SubItemNo == "") {
					$actItem->TotalPrice	=	$actItem->Quantity * $actItem->Price ;
					$actItem->updateColInDb( "TotalPrice") ;
					$actArticle->setArticleNo( $actItem->ArticleNo) ;
					$actTax->setKey( $actArticle->TaxClass) ;
					$totalNet	+=	$actItem->TotalPrice ;
					if ( ! isset( $totalPerTaxClass[$actArticle->TaxClass])) {
						$totalPerTaxClass[$actArticle->TaxClass]	=	$actItem->TotalPrice * $actTax->Percentage / 100.0 ;
					} else {
						$totalPerTaxClass[$actArticle->TaxClass]	+=	$actItem->TotalPrice * $actTax->Percentage / 100.0 ;
					}
				}
			}
			$totalTax	=	0.0 ;
			foreach ( $totalPerTaxClass as $idx => $lineTotal) {
				$totalTax	+=	$lineTotal ;
			}
			$this->TotalPrice	=	$totalNet ;
			$this->updateColInDb( "TotalPrice") ;
			$this->TotalTax	=	$totalTax ;
			$this->updateColInDb( "TotalTax") ;
			$this->ItemCount	=	$actItem->getCountWhere( $myKeyCol . " = '" . $this->$myKeyCol . "' ") ;
			$this->updateColInDb( "ItemCount") ;
			$this->reload() ;
			if ( isset( $this->ItemCount)) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectERM_CR.php", "AppObjectERM_CR", "_restate", "this->ItemCount := " . $this->ItemCount) ;
			}
		} catch ( Exception $e) {
			error_log( "Exception: $e->getMessage()") ;
			throw $e ;
		}
		FDbg::end() ;
	}
	/**
	 * methods: internal
	 */
	function	_setTexte() {
		error_log( $this->className) ;
		if ( isset( $this->Prefix)) {
			try {
				$myTexte	=	new Texte( $this->className."Prefix", $this->CustomerNo, $this->Customer->Language) ;
				$this->Prefix	=	$myTexte->Volltext ;
				$this->updateColInDb( "Prefix") ;
			} catch ( Exception $e) {

			}
		} else {
			error_log( "Object '$this->className' does not have an attribute Prefix!") ;
		}
		if ( isset( $this->Postfix)) {
			try {
				$myTexte	=	new Texte( $this->className."Postfix", $this->CustomerNo, $this->Customer->Language) ;
				$this->Postfix	=	$myTexte->Volltext ;
				$this->updateColInDb( "Postfix") ;
			} catch ( Exception $e) {

			}
		} else {
			error_log( "Object '$this->className' does not have an attribute Postfix!") ;
		}
	}
	function	_setAnschreiben( $_key="", $_id=-1, $_val="") {
		if ( isset( $this->Anschreiben)) {
			try {
				$myTexte	=	new Texte( $this->className."EMail", $this->CustomerNo, $this->Customer->Language) ;
				$this->Anschreiben	=	$myTexte->Volltext ;
				$this->updateColInDb( "Anschreiben") ;
			} catch ( Exception $e) {

			}
		} else {
			error_log( "Object '$this->className' does not have an attribute Anschreiben!") ;
		}
	}
	function	_recalcDM10( $_key="", $_id=-1, $_val="") {
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$this->className . "Item" ;
		/**
		 *
		 */
		$actItem	=	new $itemObjClassName( $_key) ;
		$actArticleSalesPriceCache	=	new ArticleSalesPriceCache() ;
		try {
			for ( $actItem->firstFromDb( $myKeyCol, "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
					$actItem->_valid ;
					$actItem->nextFromDb()) {
				if ( $actItem->SubItemNo == "") {
					if ( $actArticleSalesPriceCache->fetchFromDbWhere( "WHERE ArticleNo = '$actItem->ArticleNo' AND QuantityPerPU = $actItem->QuantityPerPU ")) {
						$mr	=	$actArticleSalesPriceCache->Rabatt ;
						$actItem->Price	=	$actArticleSalesPriceCache->Price * ( 1.0 - $mr + $mr / sqrt( $actItem->Quantity)) ;
						$actItem->TotalPrice	=	$actItem->Quantity * $actItem->Price ;
						$actItem->updateInDb() ;
					}
				}
			}
		} catch( Exception $e) {
			FDbg::dumpF( "KdAnf::renumber(...): exception='%s'", $e->getMessage()) ;
		}
		return $this->getXMLComplete() ;
	}
	function	_recalcDM20( $_key="", $_id=-1, $_val="") {
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$this->className . "Item" ;
		/**
		 *
		 */
		$actItem	=	new $itemObjClassName( $_key) ;
		$actArticleSalesPriceCache	=	new ArticleSalesPriceCache() ;
		$myFormula	=	new PricingFormula() ;
		try {
			$sumPR	=	0.0 ;
			$sumVK	=	0.0 ;
			for ( $actItem->firstFromDb( $myKeyCol, "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
					$actItem->_valid ;
					$actItem->nextFromDb()) {
				if ( $actItem->SubItemNo == "") {
					if ( $actArticleSalesPriceCache->fetchFromDbWhere( "WHERE ArticleNo = '$actItem->ArticleNo' AND QuantityPerPU = $actItem->QuantityPerPU ")) {
						$mr	=	$actArticleSalesPriceCache->Rabatt ;
						$actItem->Price	=	$actItem->ReferencePrice ;
						$actItem->KalkPrice	=	$actItem->ReferencePrice - ( $myFormula->calcPR( $mr, $actItem->ReferencePrice, $actItem->Quantity ) / $actItem->Quantity ) ;
						$sumPR	+=	( $actItem->ReferencePrice - $actItem->KalkPrice) * $actItem->Quantity ;
						$sumVK	+=	( $actItem->ReferencePrice ) * $actItem->Quantity ;
						$actItem->TotalPrice	=	$actItem->Quantity * $actItem->Price ;
						$actItem->updateInDb() ;
					}
				}
			}
			$myRR	=	$myFormula->calcRR( $sumPR) ;
			$myRRP	=	$myFormula->calcRRP( $sumVK, $myRR) ;
			$this->Rabatt	=	$myRRP ;
			$this->updateColInDb( "Rabatt") ;
		} catch( Exception $e) {
			FDbg::dumpF( "KdAnf::renumber(...): exception='%s'", $e->getMessage()) ;
		}
		return $this->getXMLComplete() ;
	}
	function	_recalcDM30( $_key="", $_id=-1, $_val="") {
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$this->className . "Item" ;
		/**
		 *
		 */
		$actItem	=	new $itemObjClassName( $_key) ;
		$actEKPriceR	=	new EKPriceR() ;
		$actArticleEKPrice	=	new ArticleEKPrice() ;
		try {
			$myTotal	=	0.0 ;
			$myTotalList	=	0.0 ;
			for ( $actItem->firstFromDb( $myKeyCol, "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
					$actItem->_valid ;
					$actItem->nextFromDb()) {
				/**
				 * only main items need to be calculated
				 */
				if ( $actItem->SubItemNo == "") {
					$actEKPriceR->getCalcBase( $actItem->ArticleNo) ;
					$actArticleEKPrice->getCalcBase( $actEKPriceR->LiefNr, $actEKPriceR->LiefArtNr, $actEKPriceR->KalkBasis) ;
					$actItem->Price	=	$actItem->ReferencePrice ;
					$actItem->KalkPrice	=	$actArticleEKPrice->Price / $actArticleEKPrice->QuantityPerPU * $actItem->QuantityPerPU * ( 1.0 + $this->Markup / 100.0) ;
					$actItem->TotalPrice	=	$actItem->Quantity * $actItem->Price ;
					$myTotal	+=	$actItem->Quantity * $actItem->KalkPrice ;
					$myTotalList	+=	$actItem->Quantity * $actItem->Price ;
					$actItem->updateInDb() ;
				}
			}
			$this->Rabatt	=	( $myTotalList - $myTotal) / $myTotalList * 100.0 ;
			$this->updateColInDb( "Rabatt") ;
		} catch( Exception $e) {
			FDbg::dumpF( "KdAnf::renumber(...): exception='%s'", $e->getMessage()) ;
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	function	_addPos( $_artikelNr, $_vkpid, $_menge) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."( '$_artikelNr', $_vkpid, $_menge)") ;
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$this->className . "Item" ;
		FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."( ...)", "itemClassName := '$itemObjClassName'") ;
		/**
		 * let's go
		 */
		try {
			$myArticleSalesPrice	=	new ArticleSalesPriceCache() ;
			$myArticleSalesPrice->setId( $_vkpid) ;
			if ( $myArticleSalesPrice->_valid) {
				$myArticle	=	new Article() ;
				$myArticle->setKey( $myArticleSalesPrice->ArticleNo) ;
				if ( $myArticle->_valid) {
					/**
					 * find the article which we needs to add
					 * means: as long as the article has a new article associated or as there is a
					 * replacement article, go to this
					 * article
					 */
					while ( strlen( $myArticle->ArticleNoNew) > 0 || strlen( $myArticle->ArticleNoReplacement) > 0) {
						if ( strlen( $myArticle->ArticleNoNew) > 0) {
							$myArticle->setArticleNo( $myArticle->ArticleNoNew) ;
						} else if ( strlen( $myArticle->ArticleNoReplacement) > 0) {
							$myArticle->setArticleNo( $myArticle->ArticleNoReplacement) ;
						}
					}
					/**
					 * if the article does not have components
					 *
					 */
					if ( $myArticle->CompositionType == 0) {
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()",
										"article w/o components") ;
						$actItem	=	new $itemObjClassName( $myKey) ;
						$actItem->fetchFromDbWhere( [ "$myKeyCol='".$myKey."'", "ArticleNo='".$myArticle->ArticleNo."'", "QuantityPerPU=".$myArticleSalesPrice->QuantityPerPU, "SubItemNo = ''"]) ;
						if ( $actItem->_valid) {
							FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()",
											"updating existing item, keycol := '" . $this->$myKeyCol . "' ") ;
							$actItem->Quantity	+=	$_menge ;
							$actItem->updateInDb() ;
							$newItem	=	$actItem ;
						} else {
							FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()",
											"adding new item, keycol := '" . $this->$myKeyCol . "' ") ;
							$newItem	=	new $itemObjClassName() ;
							$newItem->$myKeyCol	=	$this->$myKeyCol ;
							$newItem->getNextItemNo() ;
							$newItem->ArticleNo	=	$myArticleSalesPrice->ArticleNo ;
							$newItem->Quantity	=	$_menge ;
							$newItem->Price	=	$myArticleSalesPrice->Price ;
							$newItem->ReferencePrice	=	$myArticleSalesPrice->Price ;
							$newItem->QuantityPerPU	=	$myArticleSalesPrice->QuantityPerPU ;
							$newItem->TotalPrice	=	$newItem->Quantity * $newItem->Price ;
							$newItem->storeInDb() ;
						}
					} else {
						FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()",
										"article w/ components") ;
						$newItem	=	new $itemObjClassName( $myKey) ;
						$newItem->getNextItemNo() ;
						$newItem->ArticleNo	=	$myArticleSalesPrice->ArticleNo ;
						$newItem->Quantity	=	$_menge ;
						$newItem->Price	=	$myArticleSalesPrice->Price ;
						$newItem->ReferencePrice	=	$myArticleSalesPrice->Price ;
						$newItem->QuantityPerPU	=	$myArticleSalesPrice->QuantityPerPU ;
						$newItem->TotalPrice	=	$newItem->Quantity * $newItem->Price ;
						$newItem->storeInDb() ;
						$this->addSubPos( $newItem->ItemNo, $myArticle->ArticleNo, $newItem->Quantity, "") ;
					}
				} else {
					FDbg::trace( 1, FDbg::trcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "Article not valid!") ;
				}
			} else {
				FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "VKPrice not valid!") ;
			}
		} catch ( Exception $e) {
			throw new FException( basename( __FILE__), __CLASS__, __METHOD__."( '$_query', '$_className', '$_db')", $e) ;
		}
		FDbg::end() ;
		return $itemObjClassName ;
	}
	/**
	 * This method sends an eMail, with the text named $_mailText coming from the 'Texte' Db-Table
	 * to the recipients
	 * @param string $_mailText	mand.: Name of the mail body in the 'Texte' Db-table
	 * @param string $_file	opt.: pdf-file in the Archive/CuOrdr path to attach
	 * @param string $_from	opt.: sending mail address
	 * @param string $_to	opt.: receiving mail address
	 * @param string $_cc	opt.: cc mail address
	 * @param string $_bcc	opt.: bcc mail address
	 */
	function	mail( $_mailText, $_file="", $_fileName="", $_from="", $_to="", $_cc="", $_bcc="") {
		/**
		 * get the eMail subject and make it accessible by the interpreter
		 */
		$myText	=	new Texte() ;
		$myText->setKeys( $_mailText."Subject") ;
		$this->mailSubject	=	$myText->Volltext ;
		/**
		 * get the eMail body
		 */
		$myText->setKeys( $_mailText."Text") ;
		$this->mailBodyText	=	xmlToPlain( "<div>".iconv( 'UTF-8', 'iso-8859-1//TRANSLIT', $myText->Volltext)."</div>") ;
		$myText->setKeys( $_mailText."HTML") ;
		$this->mailBodyHTML	=	$myText->Volltext ;
		/**
		 * get the eMail disclaimer and make it accessible by the interpreter
		 */
		$myText->setKeys( "DisclaimerText") ;
		$this->DisclaimerText	=	xmlToPlain( "<div>".iconv( 'UTF-8', 'iso-8859-1//TRANSLIT', $myText->Volltext)."</div>") ;
		$myText->setKeys( "DisclaimerHTML") ;
		$this->DisclaimerHTML	=	$myText->Volltext ;
		/**
		 * do some interpretation on the body contents
		 */
		$subjectText	=	$this->interpret( $this->mailSubject) ;
		$bodyText	=	$this->interpret( $this->mailBodyText) ;
		$bodyHTML	=	$this->interpret( $this->mailBodyHTML) ;
		/**
		 *
		 */
		$mailFrom	=	$_from ;
		if ( $mailFrom == "") {
			$mailFrom	=	$this->siteeMail->Sales ;
		}
		$mailTo	=	$_to ;
		if ( $mailTo == "") {
			$mailTo	=	$this->CustomerContact->eMail ;
		}
		$mailCC	=	$_cc ;
		$mailBCC	=	"Bcc: " . $this->siteeMail->Archive ;
		if ( $_bcc != "") {
			$mailBCC	.=	"," . $_bcc ;
		}
		$mailBCC	.=	"\n" ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectERM_CR.php", "AppObjectERM_CR", "mail( ...)", $mailFrom) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectERM_CR.php", "AppObjectERM_CR", "mail( ...)", $mailTo) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectERM_CR.php", "AppObjectERM_CR", "mail( ...)", $mailCC) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectERM_CR.php", "AppObjectERM_CR", "mail( ...)", $mailBCC) ;
		$myMail	=	new mimeMail( $mailFrom,
							$mailTo,
							"",
							$subjectText,
							$mailBCC) ;
		$myText	=	new mimeData( "multipart/alternative") ;
		$myText->addData( "text/plain", $bodyText) ;
		$myText->addData( "text/html", mimeMail::getHTMLBody( $bodyHTML), "", true) ;
		/**
		 * prepare the eMail attachment
		 */
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$file	=	$_file ;
		$fileName	=	$_fileName ;
		$myBody	=	new mimeData( "multipart/mixed") ;
//		$myBody->addData( "text/html", mimeMail::getHTMLBody( $bodyHTML)) ;
		if ( $file != null) {
			$myBody->addData( "multipart/mixed", $myText->getAll()) ;
			$myBody->addData( "application/pdf", $file, $fileName, true) ;
		} else {
			$myBody->addData( "multipart/mixed", $myText->getAll(), "", true) ;
		}
		$myMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
		/**
		 * and send it
		 */
		$mailSendResult	=	$myMail->send() ;
	}
	/**
	 *
	 * @return void
	 */
	function	newKey( $_digits=6, $_nsStart="000000", $_nsEnd="999999", $_store=true) {
		FDbg::begin( 1, basename( __FILE__), __CLASS__, __METHOD__."()") ;
		$myQuery	=	$this->getQueryObj( "Select") ;
		$myKeyCol	=	$this->keyCol ;
		$yKey	=	$this->$myKeyCol ;
		$myQuery->addOrder( [ $myKeyCol . " DESC"]) ;
		$myQuery->addLimit( new FSqlLimit( 0, 1)) ;
		error_log( $myQuery) ;
		try {
			$row	=	FDb::queryRow( $myQuery) ;
			error_log( "1CustomerOrderNo ..............................> " . $myKeyCol) ;
			error_log( "2CustomerOrderNo ..............................> " . $row[$myKeyCol]) ;
			if ( $row) {
			error_log( "3CustomerOrderNo ..............................> " . $row[$myKeyCol]) ;
				$this->$myKeyCol	=	sprintf( "%06d", intval( $row[$myKeyCol]) + 1) ;
			} else {
				$this->$myKeyCol	=	sprintf( "%06d", 1) ;
			}
			$this->storeInDb() ;
		} catch ( Exception $e) {
			FDbg::abort() ;
			throw $e ;
		}
		FDbg::end() ;
		return $this->_status ;
	}
}
?>
