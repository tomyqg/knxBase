<?php
/**
 * AppObjectCR - Application Object Customer Relation
 *
 * Base class for all objects which deal with customer relations,
 * ie. KdAnf, KdAng, KdBestm KdKomm, KdLief, KdRech, KdMahn.
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
 * @subpackage AppObjects
 */
class	AppObjectCR	extends	AppObject	{
	/**
	 *
	 * @var unknown_type
	 */
	public	$Kunde ;
	public	$KundeKontakt ;
	public	$RechKunde ;
	public	$RechKundeKontakt ;
	public	$LiefKunde ;
	public	$LiefKundeKontakt ;
	/**
	 *
	 */
	const	ORD_BY_INOASINOA	=	"INoASINoA" ;		// order confirmation
	const	ORD_BY_INODSINOA	=	"INoSINoD" ;		// order confirmation
	const	ORD_BY_ARTNO	=	"ArtNo" ;		// order confirmation
	private	static	$rOrdMode	=	array (
						AppObjectCR::ORD_BY_INOASINOA	=> "ItemNo, SubItemNo ASC",
						AppObjectCR::ORD_BY_INODSINOA	=> "ItemNo DESC, SubItemNo ASC",
						AppObjectCR::ORD_BY_ARTNO	=> "ArtikelNr"
					) ;
	function	getROrdMode() {		return self::$rOrdMode ;			}
	/**
	 *
	 * @param string $_className
	 * @param string $_keyColName
	 */
	function	__construct( $_className, $_keyColName) {
		parent::__construct( $_className, $_keyColName) ;
		$this->Kunde	=	new Kunde() ;
		$this->KundeKontakt	=	new KundeKontakt() ;
		$this->RechKunde	=	NULL ;
		$this->RechKundeKontakt	=	NULL ;
		$this->LiefKunde	=	NULL ;
		$this->LiefKundeKontakt	=	NULL ;
	}
	/**
	 * (non-PHPdoc)
	 * @see DbObject::reload()
	 */
	function	reload( $_db="def") {
		$this->fetchFromDb() ;
		$this->_reloadCust() ;
	}
	/**
	 * methods: add/upd/copy/del
	 */
	/**
	 * (non-PHPdoc)
	 * @see AppObject::del()
	 */
	function	del( $_key="", $_id=-1, $_val="") {
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			$myKeyCol	=	$this->keyCol ;
			$myKey	=	$this->$myKeyCol ;
			$objName	=	$this->className ;
			$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." = '".$myKey."' ") ;
			$objName	=	$this->className . "Item" ;
			$myResult	=	FDb::query( "DELETE FROM ".$objName." WHERE ".$myKeyCol." = '".$myKey."' ") ;
		} else {
			$e	=	new Exception( "AppObject.php::AppObject::del: The object is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	/**
	 * methods: addDep/updDep/copyDep/delDep
	 */
	/**
	 * (non-PHPdoc)
	 * @see AppObject::addDep()
	 */
	function	addDep( $_key="", $_id=-1, $_val="") {
	}
	/**
	 * Updates a dependent object of AppObejctCR
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 * @throws Exception
	 * @return string
	 */
	function	updDep( $_key="", $_id=-1, $_val="") {
		$elem	=	explode( ".", $_val) ;
		$class	=	$elem[0] ;
//		$attr	=	$elem[1] ;
		if ( ! isset( $this->LockState))
			$this->LockState	=	0 ;
		if ( $this->LockState == 0) {
			try {
				$tmpObj	=	new $class() ;
				$myKeyCol	=	$this->keyCol ;
				$myKey	=	$this->$myKeyCol ;
				if ( $tmpObj->setId( $_id)) {
					$tmpObj->getFromPostL() ;
					if ( isset( $tmpObj) && isset( $tmpObj->Preis) && isset( $tmpObj->Menge))
						$tmpObj->GesamtPreis	=	$tmpObj->Menge * $tmpObj->Preis ;
					$tmpObj->updateInDb() ;
				} else {
					$e	=	new Exception( 'AppObject::updPos[Id='.$_id.'] is INVALID !') ;
					error_log( $e) ;
					throw $e ;
				}
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( 'AppObject::updPos: Das Objekt ist schreibgeschuetzt !') ;
			error_log( $e) ;
			throw $e ;
		}
		$ret	=	"<" . $this->className . ">\n" .
					$this->getDepAsXML( $_key, $_id, $class) .
					"</" . $this->className . ">\n" ;
		return $ret ;
	}
	/**
	 *
	 * @param string $_key
	 * @param unknown $_id
	 * @param string $_val
	 * @throws Exception
	 */
	function	_delDep( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		if ( $this->LockState == 0) {
			try {
				$this->unbuche() ;
				$tmpObj	=	new $objName() ;
				if ( $tmpObj->setId( $_id)) {
					$tmpObj->removeFromDb() ;
				} else {
					$e	=	new Exception( "AppObject.php::AppObject::delDep[Id='$_id'] dependent is INVALID !") ;
					error_log( $e) ;
					throw $e ;
				}
				$this->buche() ;
			} catch ( Exception $e) {
				throw $e ;
			}
		} else {
			$e	=	new Exception( "AppObject.php::AppObject::delDep(...): the object is locked!") ;
			error_log( $e) ;
			throw $e ;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see AppObject::delDep()
	 */
	function	delDep( $_key, $_id, $_val) {
		$this->_delDep( $_key, $_id, $_val) ;
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
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
		$subArtikel	=	new Artikel( "") ;
		try {
			$actItem	=	new $itemObjClassName() ;
			$actItem->Id	=	$_id ;
			$actItem->fetchFromDbById() ;
			if ( $actItem->_valid) {
				$myArtikel	=	new Artikel( $actItem->ArtikelNr) ;
				if ( $myArtikel->_valid == 1) {
					if ( $myArtikel->Comp == Artikel::COMP10) {
						$myArtKomp	=	new ArtKomp() ;
						$cond	=	sprintf( "ArtikelNr='%s' ", $myArtikel->ArtikelNr) ;
						$subItemNo	=	0 ;
						$myArtKomp->_firstFromDb( $cond) ;
						while ( $myArtKomp->_valid) {
							$subItemNo++ ;
							$subArtikel->setArtikelNr( $myArtKomp->CompArtikelNr) ;
							/**
							 * find the article which we needs to add
							 * means: as long as the article has a new article associated or as there is a
							 * replacement article, go to this
							 * article
							 */
							while ( strlen( $subArtikel->ArtikelNrNeu) > 0 || strlen( $subArtikel->ArtikelNrErsatz) > 0) {
								if ( strlen( $subArtikel->ArtikelNrNeu) > 0) {
									$subArtikel->setArtikelNr( $subArtikel->ArtikelNrNeu) ;
								} else if ( strlen( $subArtikel->ArtikelNrErsatz) > 0) {
									$subArtikel->setArtikelNr( $subArtikel->ArtikelNrErsatz) ;
								}
							}
							/**
							 * @var unknown_type
							 */
							$newItem	=	new $itemObjClassName( $myKey) ;
							$newItem->ItemNo	=	$actItem->ItemNo ;
							$newItem->SubItemNo	=	sprintf( "%s.%02d", $_posNrPrefix, $subItemNo) ;
							$newItem->PosType	=	0 ;
							$newItem->ArtikelNr	=	$subArtikel->ArtikelNr ;
							$newItem->Menge	=	$actItem->Menge * $myArtKomp->CompMenge ;
							$newItem->Preis	=	0.0 ;
							$newItem->RefPreis	=	0.0 ;
							$newItem->MengeProVPE	=	$myArtKomp->CompMengeProVPE ;
							$newItem->GesamtPreis	=	0.0 ;
							$newItem->storeInDb() ;
							if ( $subArtikel->Comp == Artikel::COMP10) {
								$this->addSubPos( $newItem->ItemNo, $subArtikel->ArtikelNr, $actItem->Menge * $myArtKomp->CompMenge, $newItem->SubItemNo) ;
							}
							$myArtKomp->_nextFromDb() ;
						}
					} else {
						$e	=	new Exception( "AppObjectCR.php::AppObject::expandPos: Artikel hat keine Komponenten!") ;
						error_log( $e) ;
						throw $e ;
					}
				} else {
					$e	=	new Exception( "AppObjectCR.php::AppObjectCR::expandPos(...): Artikel mit der ArtikelNr=$actKdLiefPos->ArtikelNr existiert nicht") ;
					error_log( $e) ;
					throw $e ;
				}
			} else {
				$e	=	new Exception( "AppObjectCR.php::AppObjectCR::expandPos(...): Item mit der Id=$_id existiert nicht") ;
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
	function	setKundeFromKKId( $_key="", $_kkId=-1, $_val="") {
		$tmpKundeKontakt	=	new KundeKontakt() ;
		$tmpKundeKontakt->setId( $_kkId) ;
		if ( $tmpKundeKontakt->_valid) {
			try {
				$this->KundeNr	=	$tmpKundeKontakt->KundeNr ;
				$this->KundeKontaktNr	=	$tmpKundeKontakt->KundeKontaktNr ;
				$this->updateInDb() ;
				$this->reload() ;
				$this->ModusLief	=	$this->Kunde->ModusLief ;
				$this->ModusRech	=	$this->Kunde->ModusRech ;
				$this->ModusSkonto	=	$this->Kunde->ModusSkonto ;
				$this->Rabatt	=	$this->Kunde->Rabatt ;
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
			error_log( "AppObjectCR.php::AppObjectCR::setKundeFromKKId(...): KundeKontakt not valid !") ;
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
	function	setRechKundeFromKKId( $_key="", $_kkId=-1, $_val="") {
		$tmpKundeKontakt	=	new KundeKontakt() ;
		$tmpKundeKontakt->setId( $_kkId) ;
		if ( $tmpKundeKontakt->_valid) {
			try {
				$this->RechKundeNr	=	$tmpKundeKontakt->KundeNr ;
				$this->RechKundeKontaktNr	=	$tmpKundeKontakt->KundeKontaktNr ;
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
	function	clearRechKunde() {
		try {
			$this->RechKundeNr	=	"" ;
			$this->RechKundeKontaktNr	=	"" ;
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
	function	setLiefKundeFromKKId( $_key="", $_kkId=-1, $_val="") {
		$tmpKundeKontakt	=	new KundeKontakt() ;
		$tmpKundeKontakt->setId( $_kkId) ;
		if ( $tmpKundeKontakt->_valid) {
			try {
				$this->LiefKundeNr	=	$tmpKundeKontakt->KundeNr ;
				$this->LiefKundeKontaktNr	=	$tmpKundeKontakt->KundeKontaktNr ;
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
	function	clearLiefKunde() {
		try {
			$this->LiefKundeNr	=	"" ;
			$this->LiefKundeKontaktNr	=	"" ;
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
		$actVKPreisCache	=	new VKPreisCache() ;
		$actItem	=	new $itemObjClassName( $_key) ;
		$actItem->setIterCond( "$myKeyCol = '$myKey' ") ;
		foreach ( $actItem as $key => $val) {
			if ( $actItem->SubItemNo == "") {
				if ( $actVKPreisCache->fetchFromDbWhere( "WHERE ArtikelNr = '$actItem->ArtikelNr' AND MengeProVPE = $actItem->MengeProVPE ")) {
					$actItem->Preis	=	$actVKPreisCache->Preis ;
					$actItem->RefPreis	=	$actVKPreisCache->Preis ;
					$actItem->GesamtPreis	=	$actItem->Menge * $actItem->Preis ;
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
	function	addPos( $_artikelNr, $_vkpid, $_menge) {
		error_log( "$_artikelNr, $_vkpid, $_menge") ;
		$this->_addPos( $_artikelNr, $_vkpid, $_menge) ;
		return $this->getXMLComplete() ;
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
		$myArtikel	=	new Artikel( $_artikelNr) ;
		$subArtikel	=	new Artikel( "") ;
		if ( $myArtikel->_valid == 1) {
			if ( $myArtikel->Comp == 0) {
			} else {
				$myArtKomp	=	new ArtKomp() ;
				$cond	=	sprintf( "ArtikelNr='%s' ", $_artikelNr) ;
				$subItemNo	=	0 ;
				$myArtKomp->_firstFromDb( $cond) ;
				while ( $myArtKomp->_valid) {
					$subItemNo++ ;
					$subArtikel->setArtikelNr( $myArtKomp->CompArtikelNr) ;
					/**
					 * find the article which we needs to add
					 * means: as long as the article has a new article associated or as there is a
					 * replacement article, go to this
					 * article
					 */
					while ( strlen( $subArtikel->ArtikelNrNeu) > 0 || strlen( $subArtikel->ArtikelNrErsatz) > 0) {
						if ( strlen( $subArtikel->ArtikelNrNeu) > 0) {
							$subArtikel->setArtikelNr( $subArtikel->ArtikelNrNeu) ;
						} else if ( strlen( $subArtikel->ArtikelNrErsatz) > 0) {
							$subArtikel->setArtikelNr( $subArtikel->ArtikelNrErsatz) ;
						}
					}
					$newItem	=	new $itemObjClassName() ;
					$newItem->$myKeyCol	=	$myKey ;
					$newItem->ItemNo	=	$_posNr ;
					$newItem->SubItemNo	=	sprintf( "%s.%02d", $_posNrPrefix, $subItemNo) ;
					$newItem->PosType	=	0 ;
					$newItem->ArtikelNr	=	$subArtikel->ArtikelNr ;
					$newItem->Menge	=	$_menge * $myArtKomp->CompMenge ;
					$newItem->Preis	=	0.0 ;
					$newItem->RefPreis	=	0.0 ;
					$newItem->MengeProVPE	=	$myArtKomp->CompMengeProVPE ;
					$newItem->GesamtPreis	=	0.0 ;
					$newItem->storeInDb() ;
					if ( $subArtikel->Comp > 0) {
						$this->addSubPos( $_posNr, $subArtikel->ArtikelNr, $_menge * $myArtKomp->CompMenge, $newItem->SubItemNo) ;
					}
					$myArtKomp->_nextFromDb() ;
				}
			}
		}
	}
	function	newKunde( $_key, $_val, $_id) {
		try {
			$newKunde	=	new Kunde() ;
			$newKunde->add() ;
			$this->KundeNr	=	$newKunde->KundeNr ;
			$this->KundeKontaktNr	=	$newKunde->_addDep( "", -1, "KundeKontakt") ;
			$this->LiefKundeNr	=	"" ;
			$this->LiefKundeKontaktNr	=	"" ;
			$this->RechKundeNr	=	"" ;
			$this->RechKundeKontaktNr	=	"" ;
			$this->updateInDb() ;
			$this->_reloadCust() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newKundeKontakt( $_key, $_val, $_id) {
		try {
			$newKunde	=	new Kunde( $this->KundeNr) ;
			if ( $newKunde->isValid()) {
				$this->KundeKontaktNr	=	$newKunde->_addDep( "", -1, "KundeKontakt") ;
				$this->updateColInDb( "KundeNr") ;
				$this->updateColInDb( "KundeKontaktNr") ;
				$this->_reloadCust() ;
			} else {
				$e	=	"AppObjectCR.php::AppObjectCR::newLiefKundeKontakt(...): invalid customer!" ;
				throw new Exception( $e) ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newLiefKunde( $_key, $_val, $_id) {
		try {
			$newKunde	=	new Kunde() ;
			$newKunde->KundeNr	=	$this->KundeNr ;
			$newKundeNr	=	$newKunde->_addDep( "", -1, "LiefKunde") ;
			$newKunde->KundeNr	=	$newKundeNr ;
			$newKunde->reload() ;
			$this->LiefKundeNr	=	$newKundeNr ;
			$this->LiefKundeKontaktNr	=	$newKunde->_addDep( "", -1, "KundeKontakt") ;
			$this->updateInDb() ;
			$this->_reloadCust() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newLiefKundeKontakt( $_key, $_val, $_id) {
		try {
			$newKunde	=	new Kunde( $this->LiefKundeNr) ;
			if ( $newKunde->isValid()) {
				$this->LiefKundeKontaktNr	=	$newKunde->_addDep( "", -1, "KundeKontakt") ;
				$this->updateInDb() ;
				$this->_reloadCust() ;
			} else {
				$e	=	"AppObjectCR.php::AppObjectCR::newLiefKundeKontakt(...): invalid delivery customer!" ;
				throw new Exception( $e) ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newRechKunde( $_key, $_val, $_id) {
		try {
			$newKunde	=	new Kunde() ;
			$newKunde->KundeNr	=	$this->KundeNr ;
			$newKundeNr	=	$newKunde->_addDepKunde( "R") ;
			$newKunde->KundeNr	=	$newKundeNr ;
			$newKunde->reload() ;
			$this->RechKundeNr	=	$newKundeNr ;
			$this->RechKundeKontaktNr	=	$newKunde->_addDep( "", -1, "KundeKontakt") ;
			$this->updateInDb() ;
			$this->_reloadCust() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newRechKundeKontakt( $_key, $_val, $_id) {
		try {
			$newKunde	=	new Kunde( $this->RechKundeNr) ;
			if ( $newKunde->isValid()) {
				$this->RechKundeKontaktNr	=	$newKunde->_addDep( "", -1, "KundeKontakt") ;
				$this->updateInDb() ;
				$this->_reloadCust() ;
			} else {
				$e	=	"AppObjectCR.php::AppObjectCR::newLiefKundeKontakt(...): invalid invoicing customer!" ;
				throw new Exception( $e) ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newAddKunde( $_key, $_val, $_id) {
		try {
			$kdNr	=	explode( "-", $this->KundeNr) ;		// remove possible -xxxx appendix
			$newKunde	=	new Kunde() ;
			$newKunde->KundeNr	=	$kdNr[0] ;
			$newKundeNr	=	$newKunde->_addDepKunde( "A") ;
			$newKunde->KundeNr	=	$newKundeNr ;
			$newKunde->reload() ;
			$this->KundeNr	=	$newKundeNr ;
			$this->KundeKontaktNr	=	$newKunde->_addDep( "", -1, "KundeKontakt") ;
			$this->updateInDb() ;
			$this->_reloadCust() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}
	function	newAddKundeKontakt( $_key, $_val, $_id) {
		try {
			$newKunde	=	new Kunde( $this->LiefKundeNr) ;
			if ( $newKunde->isValid()) {
				$this->AddKundeKontaktNr	=	$newKunde->_addDep( "", -1, "KundeKontakt") ;
				$this->updateInDb() ;
				$this->_reloadCust() ;
			} else {
				$e	=	"AppObjectCR.php::AppObjectCR::newLiefKundeKontakt(...): invalid delivery customer!" ;
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
	function	getList( $_key="", $_id=-1, $_val="") {
		$_suchKrit	=	$_POST['_S'.$this->keyCol] ;
		$_sStatus	=	intval( $_POST['_SStatus']) ;
		$_POST['_step']	=	$_id ;
		$filter	=	"( " ;
		$filter	.=	"C.".$this->keyCol." like '%" . $_suchKrit . "%' " ;
		if ( $_POST['_SCompany'] != "")
			$filter	.=	"  AND ( FirmaName1 like '%" . $_POST['_SCompany'] . "%' OR FirmaName2 LIKE '%" . $_POST['_SCompany'] . "%') " ;
		if ( $_POST['_SZIP'] != "")
			$filter	.=	"  AND ( PLZ like '%" . $_POST['_SZIP'] . "%' ) " ;
		if ( $_POST['_SContact'] != "")
			$filter	.=	"  AND ( Name like '%" . $_POST['_SContact'] . "%' OR Vorname LIKE '%" . $_POST['_SContact'] . "%') " ;
		if ( $_sStatus != -1) {
			$filter	.=	"AND ( C.Status = " . $_sStatus . ") " ;
		}
		$filter	.=	") " ;
		$ret	=	"" ;
		$myObj	=	new FDbObject( "", "") ;				// no specific object we need here
		$myObj->addCol( "Id", "int") ;
		$myObj->addCol( $this->keyCol, "var") ;
		$myObj->addCol( "Datum", "var") ;
		$myObj->addCol( "Status", "var") ;
		$myObj->addCol( "FirmaName1", "var") ;
		$myObj->addCol( "PLZ", "var") ;
		$myObj->addCol( "Vorname", "var") ;
		$myObj->addCol( "Name", "var") ;
		$ret	=	$myObj->tableFromDb( ",K.FirmaName1,K.FirmaName2,K.PLZ,KK.Vorname,KK.Name ",
								"JOIN Kunde AS K ON K.KundeNr = C.KundeNr "
									. "LEFT JOIN KundeKontakt AS KK ON KK.KundeNr = C.KundeNr AND KK.KundeKontaktNr = C.KundeKontaktNr ",
								$filter,
								"ORDER BY C.".$this->keyCol." DESC ",
								$this->className,
								$this->className,
								"C.Id, C.".$this->keyCol.", C.Datum") ;
		return $ret ;
	}
	/**
	 * methods: object retrieval
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$tmpObj	=	new $objName() ;
		$tmpObj->addCol( "ERPNo", "var") ;
		$tmpObj->addCol( "ArtikelBez1", "var") ;
		$tmpObj->addCol( "ArtikelBez2", "var") ;
		$tmpObj->addCol( "MengenText", "var") ;
		$ordBy	=	"ORDER BY C.ItemNo, C.SubItemNo " ;
		if ( isset( $_POST[ '_SOrdMode']))
			$ordBy	=	"ORDER BY " . self::$rOrdMode[ $_POST[ '_SOrdMode']] ;
		$ret	=	$tmpObj->tableFromDb( ", A.ERPNo, A.ArtikelBez1, A.ArtikelBez2, A.MengenText",
								"LEFT JOIN Artikel AS A ON A.ArtikelNr = C.ArtikelNr",
								"C." . $myKeyCol . " = '" . $myKey . "' ",
								$ordBy) ;
		return $ret ;
	}
	/**
	 * methods: object retrieval
	 */
	function	getDepAsXML( $_key="", $_id=-1, $_val="") {
		$_POST['_step']	=	$_id ;
		$objName	=	$_val ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$tmpObj	=	new $objName() ;
		$tmpObj->addCol( "ERPNo", "var") ;
		$tmpObj->addCol( "ArtikelBez1", "var") ;
		$tmpObj->addCol( "ArtikelBez2", "var") ;
		$tmpObj->addCol( "ProzSatz", "double") ;
		$tmpObj->addCol( "MengenText", "var") ;
		$ordBy	=	"ORDER BY C.ItemNo, C.SubItemNo " ;
		if ( isset( $_POST[ '_SOrdMode']))
			$ordBy	=	"ORDER BY " . self::$rOrdMode[ $_POST[ '_SOrdMode']] ;
		$ret	=	$tmpObj->tableFromDb( ", A.ERPNo, A.ArtikelBez1, A.ArtikelBez2, A.MengenText, M.ProzSatz ",
								"LEFT JOIN Artikel AS A ON A.ArtikelNr = C.ArtikelNr LEFT JOIN Mwst AS M ON M.MwstSatz = A.MwstSatz ",
								"C." . $myKeyCol . " = '" . $myKey . "' AND C.Id = " . $_id . " ",
								$ordBy) ;
		return $ret ;
	}
	function	getKundeAsXML() {
		$ret	=	"" ;
		$ret	.=	'<KundeAdr>' ;
		$ret	.=	$this->Kunde->getXMLF() ;
		$ret	.=	$this->KundeKontakt->getXMLF() ;
		$ret	.=	"<CustAttnLine>".$this->KundeKontakt->getAddrLine()."</CustAttnLine>\n" ;
		$ret	.=	"<CustOpngLine>".$this->KundeKontakt->getAnrede()."</CustOpngLine>\n" ;
		if ( $this->LiefKunde) {
			$ret	.=	$this->LiefKunde->getXMLF( "LiefKunde") ;
		}
		if ( $this->LiefKundeKontakt) {
			$ret	.=	$this->LiefKundeKontakt->getXMLF( "LiefKundeKontakt") ;
			$ret	.=	"<CustDlvrAttnLine>".$this->KundeKontakt->getAddrLine()."</CustDlvrAttnLine>\n" ;
			$ret	.=	"<CustDlvrOpngLine>".$this->KundeKontakt->getAnrede()."</CustDlvrOpngLine>\n" ;
		}
		if ( $this->RechKunde) {
			$ret	.=	$this->RechKunde->getXMLF( "RechKunde") ;
		}
		if ( $this->RechKundeKontakt) {
			$ret	.=	$this->RechKundeKontakt->getXMLF( "RechKundeKontakt") ;
			$ret	.=	"<CustInvcAttnLine>".$this->KundeKontakt->getAddrLine()."</CustInvcAttnLine>\n" ;
			$ret	.=	"<CustInvcOpngLine>".$this->KundeKontakt->getAnrede()."</CustInvcOpngLine>\n" ;
		}
		$ret	.=	'</KundeAdr>' ;
		return $ret ;
	}
	/**
	 *
	 */
	function	getKunde() 				{	return $this->Kunde ;				}
	function	getKundeKontakt()		{	return $this->KundeKontakt ;		}
	function	getLiefKunde()			{	return $this->LiefKunde ;			}
	function	getLiefKundeKontakt()	{	return $this->LiefKundeKontakt ;	}
	function	getRechKunde()			{	return $this->RechKunde ;			}
	function	getRechKundeKontakt()	{	return $this->RechKundeKontakt ;	}
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
		$totalProMwstSatz	=	array() ;
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
		$actArtikel	=	new Artikel() ;
		$actMwst	=	new Mwst() ;
		try {
			$totalNet	=	0.0 ;
			$actItem->setIterCond( "$myKeyCol = '$myKey' ") ;
			$actItem->setIterOrder( "ORDER BY ItemNo, SubItemNo ") ;
			foreach ( $actItem as $obj) {
				if ( $actItem->SubItemNo == "") {
					$actItem->GesamtPreis	=	$actItem->Menge * $actItem->Preis ;
					$actItem->updateColInDb( "GesamtPreis") ;
					$actArtikel->setArtikelNr( $actItem->ArtikelNr) ;
					$actMwst->setKey( $actArtikel->MwstSatz) ;
					$totalNet	+=	$actItem->GesamtPreis ;
					if ( ! isset( $totalProMwstSatz[$actArtikel->MwstSatz])) {
						$totalProMwstSatz[$actArtikel->MwstSatz]	=	$actItem->GesamtPreis * $actMwst->ProzSatz / 100.0 ;
					} else {
						$totalProMwstSatz[$actArtikel->MwstSatz]	+=	$actItem->GesamtPreis * $actMwst->ProzSatz / 100.0 ;
					}
				}
			}
			$totalTax	=	0.0 ;
			foreach ( $totalProMwstSatz as $idx => $lineTotal) {
				$totalTax	+=	$lineTotal ;
			}
			$this->GesamtPreis	=	$totalNet ;
			$this->updateColInDb( "GesamtPreis") ;
			$this->GesamtMwst	=	$totalTax ;
			$this->updateColInDb( "GesamtMwst") ;
			if ( isset( $this->ItemCount)) {
				$query	=	"UPDATE CuCart AS CuC "
						.	"SET ItemCount = "
							.	"( SELECT COUNT(*) FROM CuCartItem AS CuCI "
							.		"WHERE CuCI.CuCartNo = CuC.CuCartNo AND NOT CuCI.ArtikelNr LIKE \"HDLG%\") "
							.	"WHERE CuCartNo = '" . $this->CuCartNo . "' " ;
				$sqlRows	=	FDb::query( $query) ;	// consolidate the delivery data
			}
			$this->reload() ;
			if ( isset( $this->ItemCount)) {
				FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "_restate", "this->ItemCount := " . $this->ItemCount) ;
			}
		} catch ( Exception $e) {
			error_log( "Exception: $e->getMessage()") ;
			throw $e ;
		}
	}
	/**
	 * methods: internal
	 */
	function	_setTexte() {
		error_log( $this->className) ;
		if ( isset( $this->Prefix)) {
			try {
				$myTexte	=	new Texte( $this->className."Prefix", $this->KundeNr, $this->Kunde->Sprache) ;
				$this->Prefix	=	$myTexte->Volltext ;
				$this->updateColInDb( "Prefix") ;
			} catch ( Exception $e) {

			}
		} else {
			error_log( "Object '$this->className' does not have an attribute Prefix!") ;
		}
		if ( isset( $this->Postfix)) {
			try {
				$myTexte	=	new Texte( $this->className."Postfix", $this->KundeNr, $this->Kunde->Sprache) ;
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
				$myTexte	=	new Texte( $this->className."EMail", $this->KundeNr, $this->Kunde->Sprache) ;
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
		$actVKPreisCache	=	new VKPreisCache() ;
		try {
			for ( $actItem->firstFromDb( $myKeyCol, "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
					$actItem->_valid ;
					$actItem->nextFromDb()) {
				if ( $actItem->SubItemNo == "") {
					if ( $actVKPreisCache->fetchFromDbWhere( "WHERE ArtikelNr = '$actItem->ArtikelNr' AND MengeProVPE = $actItem->MengeProVPE ")) {
						$mr	=	$actVKPreisCache->Rabatt ;
						$actItem->Preis	=	$actVKPreisCache->Preis * ( 1.0 - $mr + $mr / sqrt( $actItem->Menge)) ;
						$actItem->GesamtPreis	=	$actItem->Menge * $actItem->Preis ;
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
		$actVKPreisCache	=	new VKPreisCache() ;
		$myFormula	=	new PricingFormula() ;
		try {
			$sumPR	=	0.0 ;
			$sumVK	=	0.0 ;
			for ( $actItem->firstFromDb( $myKeyCol, "", null, "", "ORDER BY ItemNo, SubItemNo ") ;
					$actItem->_valid ;
					$actItem->nextFromDb()) {
				if ( $actItem->SubItemNo == "") {
					if ( $actVKPreisCache->fetchFromDbWhere( "WHERE ArtikelNr = '$actItem->ArtikelNr' AND MengeProVPE = $actItem->MengeProVPE ")) {
						$mr	=	$actVKPreisCache->Rabatt ;
						$actItem->Preis	=	$actItem->RefPreis ;
						$actItem->KalkPreis	=	$actItem->RefPreis - ( $myFormula->calcPR( $mr, $actItem->RefPreis, $actItem->Menge ) / $actItem->Menge ) ;
						$sumPR	+=	( $actItem->RefPreis - $actItem->KalkPreis) * $actItem->Menge ;
						$sumVK	+=	( $actItem->RefPreis ) * $actItem->Menge ;
						$actItem->GesamtPreis	=	$actItem->Menge * $actItem->Preis ;
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
		$actEKPreisR	=	new EKPreisR() ;
		$actArtikelEKPreis	=	new ArtikelEKPreis() ;
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
					$actEKPreisR->getCalcBase( $actItem->ArtikelNr) ;
					$actArtikelEKPreis->getCalcBase( $actEKPreisR->LiefNr, $actEKPreisR->LiefArtNr, $actEKPreisR->KalkBasis) ;
					$actItem->Preis	=	$actItem->RefPreis ;
					$actItem->KalkPreis	=	$actArtikelEKPreis->Preis / $actArtikelEKPreis->MengeProVPE * $actItem->MengeProVPE * ( 1.0 + $this->Markup / 100.0) ;
					$actItem->GesamtPreis	=	$actItem->Menge * $actItem->Preis ;
					$myTotal	+=	$actItem->Menge * $actItem->KalkPreis ;
					$myTotalList	+=	$actItem->Menge * $actItem->Preis ;
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
	function	_reloadCust() {
		if ( $this->_valid) {
			FDbg::dumpL( 0x01000000, "AppObjectCR.php::AppObjectCR::reload(): AppObjectCR is valid !") ;
			$this->LiefKunde	=	null ;
			$this->LiefKundeKontakt	=	null ;
			$this->RechKunde	=	null ;
			$this->RechKundeKontakt	=	null ;
			/**
			 *
			 */
			try {
				$this->Kunde->setKundeNr( $this->KundeNr) ;
				$this->KundeKontakt->setKundeKontaktNr( $this->KundeNr, $this->KundeKontaktNr) ;
			} catch( Exception $e) {
				FDbg::dumpF( "AppObjectCR.php::AppObjectCR::__construct(...): exception='%s'", $e->getMessage()) ;
			}
			/**
			 * check to see if there's a dedicated invoicing address attached
			 */
			if ( isset( $this->RechKundeNr)) {
				if ( strlen( $this->RechKundeNr) > 0) {
					try {
						if ( $this->RechKunde === null) {
							$this->RechKunde	=	new Kunde() ;
							$this->RechKundeKontakt	=	new KundeKontakt() ;
						}
						$this->RechKunde->setKundeNr( $this->RechKundeNr) ;
						$this->RechKundeKontakt->setKundeKontaktNr( $this->RechKunde->KundeNr, $this->RechKundeKontaktNr) ;
					} catch( Exception $e) {
						FDbg::dumpL( 0x01000000, "AppObjectCR.php::AppObjectCR::__construct(...): exception='%s'", $e->getMessage()) ;
					}
				}
			} else {
				$this->RechKunde	=	null ;
			}
			/**
			 * check to see if there's a dedicated invoicing address attached
			 */
			if ( isset( $this->LiefKundeNr)) {
				if ( strlen( $this->LiefKundeNr) > 0) {
					try {
						if ( $this->LiefKunde === null) {
							$this->LiefKunde	=	new Kunde() ;
							$this->LiefKundeKontakt	=	new KundeKontakt() ;
						}
						$this->LiefKunde->setKundeNr(	$this->LiefKundeNr) ;
						$this->LiefKundeKontakt->setKundeKontaktNr( $this->LiefKunde->KundeNr, $this->LiefKundeKontaktNr) ;
					} catch( Exception $e) {
						FDbg::dumpL( 0x01000000, "AppObjectCR.php::AppObjectCR::__construct(...): exception='%s'", $e->getMessage()) ;
					}
				}
			}
		}
	}
	function	_addPos( $_artikelNr, $_vkpid, $_menge) {
		FDbg::begin( 1, "AppObjectCR.php", "AppObject", "_addPos( '$_artikelNr', $_vkpid, $_menge)") ;
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$this->className . "Item" ;
		/**
		 * let's go
		 */
		try {
			$myVKPreis	=	new VKPreisCache() ;
			$myVKPreis->setId( $_vkpid) ;
			if ( $myVKPreis->_valid) {
				$myArtikel	=	new Artikel() ;
				$myArtikel->setKey( $myVKPreis->ArtikelNr) ;
				if ( $myArtikel->_valid) {
					/**
					 * find the article which we needs to add
					 * means: as long as the article has a new article associated or as there is a
					 * replacement article, go to this
					 * article
					 */
					while ( strlen( $myArtikel->ArtikelNrNeu) > 0 || strlen( $myArtikel->ArtikelNrErsatz) > 0) {
						if ( strlen( $myArtikel->ArtikelNrNeu) > 0) {
							$myArtikel->setArtikelNr( $myArtikel->ArtikelNrNeu) ;
						} else if ( strlen( $myArtikel->ArtikelNrErsatz) > 0) {
							$myArtikel->setArtikelNr( $myArtikel->ArtikelNrErsatz) ;
						}
					}
					/**
					 * if the article does not have components
					 *
					 */
					if ( $myArtikel->Comp == 0) {
						FDbg::trace( 1, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "_addPos()",
										"article w/o components") ;
						$actItem	=	new $itemObjClassName( $myKey) ;
						$cond	=	sprintf( "$myKeyCol='%s' AND ArtikelNr='%s' AND MengeProVPE=%d AND SubItemNo = '' ",
												$myKey, $myArtikel->ArtikelNr, $myVKPreis->MengeProVPE) ;
						$actItem->_firstFromDb( $cond) ;
						if ( $actItem->_valid) {
							FDbg::trace( 1, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "_addPos()",
											"keycol := '" . $this->$myKeyCol . "' ") ;
							$actItem->Menge	+=	$_menge ;
							$actItem->updateInDb() ;
							$newItem	=	$actItem ;
						} else {
							FDbg::trace( 1, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "_addPos()",
											"keycol := '" . $this->$myKeyCol . "' ") ;
							$newItem	=	new $itemObjClassName() ;
							$newItem->$myKeyCol	=	$this->$myKeyCol ;
							$newItem->getNextItemNo() ;
							$newItem->ArtikelNr	=	$myVKPreis->ArtikelNr ;
							$newItem->Menge	=	$_menge ;
							$newItem->Preis	=	$myVKPreis->Preis ;
							$newItem->RefPreis	=	$myVKPreis->Preis ;
							$newItem->MengeProVPE	=	$myVKPreis->MengeProVPE ;
							$newItem->GesamtPreis	=	$newItem->Menge * $newItem->Preis ;
							$newItem->storeInDb() ;
						}
					} else {
						FDbg::trace( 1, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "_addPos()",
										"article w/ components") ;
						$newItem	=	new $itemObjClassName( $myKey) ;
						$newItem->getNextItemNo() ;
						$newItem->ArtikelNr	=	$myVKPreis->ArtikelNr ;
						$newItem->Menge	=	$_menge ;
						$newItem->Preis	=	$myVKPreis->Preis ;
						$newItem->RefPreis	=	$myVKPreis->Preis ;
						$newItem->MengeProVPE	=	$myVKPreis->MengeProVPE ;
						$newItem->GesamtPreis	=	$newItem->Menge * $newItem->Preis ;
						$newItem->storeInDb() ;
						$this->addSubPos( $newItem->ItemNo, $myArtikel->ArtikelNr, $newItem->Menge, "") ;
					}
				} else {
					FDbg::trace( 1, FDbg::trcInfo1, "AppObjectCR.php", "AppObject","_addPos()", "Article not valid!") ;
				}
			} else {
				FDbg::trace( 1, FDbg::trcInfo1, "AppObjectCR.php", "AppObject","_addPos()", "VKPreis not valid!") ;
			}
		} catch ( Exception $e) {
			FDbg::trace( 1, FDbg::trcInfo1, "AppObjectCR.php", "AppObject","_addPos()",
							"exception := '" . $e->getMessage() . "' ") ; ;
			throw $e ;
		}
		FDbg::end( 1, "AppObjectCR.php", "AppObject", "_addPos( '$_artikelNr', $_vkpid, $_menge)") ;
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
			$mailTo	=	$this->KundeKontakt->eMail ;
		}
		$mailCC	=	$_cc ;
		$mailBCC	=	"Bcc: " . $this->siteeMail->Archive ;
		if ( $_bcc != "") {
			$mailBCC	.=	"," . $_bcc ;
		}
		$mailBCC	.=	"\n" ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "mail( ...)", $mailFrom) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "mail( ...)", $mailTo) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "mail( ...)", $mailCC) ;
		FDbg::trace( 2, FDbg::mdTrcInfo1, "AppObjectCR.php", "AppObjectCR", "mail( ...)", $mailBCC) ;
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
}
?>
