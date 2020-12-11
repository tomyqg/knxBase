<?php
/**
 * AppObjectSR - Application Object Supplier Relation
 *
 * Base class for all objects which deal with customer relations,
 * ie. KdAnf, KdAng, KdBestm KdKomm, KdLief, KdRech, KdMahn.
 *
 * This class adds stuff to deal with:
 * - Supplier Address
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
class	AppObjectSR	extends	AppObject	{

	public	$myLief ;
	public	$myLiefKontakt ;

	/**
	 * Konstructor f�r Klasse: KdAng (Angebot)
	 *
	 * Der Konstruktor kann mit mit oder ohne eine Angebotsnummer aufgerufen werden.
	 * Wenn der Konstruktor mit einer Angebotsnummer aufgerufen wird, wird versucht
	 *
	 * @param string $_myKdAngNr
	 * @return void
	 */
	function	__construct( $_className, $_keyColName) {
		parent::__construct( $_className, $_keyColName) ;
		$this->myLief	=	new Lief() ;
		$this->myLiefKontakt	=	new LiefKontakt() ;
	}
	function	reload() {
		$this->fetchFromDb() ;
		if ( $this->_valid) {
			try {
				$this->myLief->setLiefNr( $this->LiefNr) ;
				$this->myLiefKontakt->setLiefKontaktNr( $this->LiefNr, $this->LiefKontaktNr) ;
			} catch( Exception $e) {
				FDbg::dumpF( "AppObjectSR.php::AppObjectSR::__construct(...): exception='%s'", $e->getMessage()) ;
			}
		}
	}
	/**
	 *
	 * @param $_key
	 * @param $_id
	 * @param $_val
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
	 * UPdates a dependent object of AppObejctCR
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
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
		return $this->getTableDepAsXML( $_key, $_id, $_val) ;
	}
	/**
	 * Access the customer data.
	 *
	 * @return [Lief]
	 */
	function	getLief() {
		return $this->myLief ;
	}

	/**
	 * Access the customer contact data.
	 *
	 * @return [LiefKontakt]
	 */
	function	getLiefKontakt() {
		return $this->myLiefKontakt ;
	}
	/**
	 * Update all prices of a supplier order to reflect the prices from the purchase prices
	 * in the database
	 *
	 * @return string	complete LfBest as XML structure
	 */
	function	updPrices( $_key="", $_id=-1, $_val="") {
		$myEKPreisR	=	new EKPreisR() ;
		$myArtikelEKPreis	=	new ArtikelEKPreis ;
		/**
		 * prepare, determine all required classes
		 */
		$myKeyCol	=	$this->keyCol ;		// get the name of the key column
		$myKey	=	$this->$myKeyCol ;		// get the value of key column
		$itemObjClassName	=	$this->className . "Item" ;
		/**
		 *
		 */
		$actItem	=	new $itemObjClassName() ;
		$actItem->setIterCond( "$myKeyCol = '" . $this->$myKeyCol . "' ") ;
		foreach ( $actItem as $key => $item) {
			$myEKPreisR->fetchFromDbWhere( "WHERE LiefNr = $this->LiefNr AND ArtikelNr = '$actItem->ArtikelNr' AND KalkBasis > 0 ") ;
			if ( $myEKPreisR->_valid) {
				$actItem->LiefArtNr	=	$myEKPreisR->LiefArtNr ;
				$myArtikelEKPreis->setIterCond( "LiefNr = $this->LiefNr AND LiefArtNr = '$myEKPreisR->LiefArtNr' "
													. "AND GueltigVon <= '".$this->today() . "' AND GueltigBis > '" . $this->today(). "' "
													. "ORDER BY Menge ASC ") ;
				foreach ( $myArtikelEKPreis as $key2 => $item2) {
					if ( $actItem->Menge >= $myArtikelEKPreis->Menge) {
						$actItem->Preis	=	$myArtikelEKPreis->Preis ;
						$actItem->GesamtPreis	=	$actItem->Menge * $actItem->Preis ;
						$actItem->MengeFuerPreis	=	$myArtikelEKPreis->MengeFuerPreis ;
					}
				}
				$actItem->updateColInDb( "Preis") ;
				$actItem->updateColInDb( "GesamtPreis") ;
				$actItem->updateColInDb( "MengeFuerPreis") ;
			} else {
				$e	=	new Exception( "AppObjectSR.php::AppObectSR::updPrices(...): EKPreisR for '$actItem->ArtikelNr' not valid!") ;
				error_log( $e) ;
				throw $e ;
			}
		}
		return $this->getXMLComplete( $_key, $_id, $_val) ;
	}
	/**
	 * set the Custoner Nr and Customer Contact Number
	 *
	 * Sets the customer nr. and the customer contact nr. for this order and fetches the related data from the database.
	 * The order is then also updated in the database with the new customer nr. and customer contact nr.
	 *
	 * @return void
	 */
	function	setLiefFromLKId( $_key, $_kkId, $_val) {
		FDbg::dumpL( 0x01000000, "AppObjectSR.php::AppObjectSR::setLiefFromLKId( %d): ", $_kkId) ;
		$tmpLiefKontakt	=	new LiefKontakt() ;
		$tmpLiefKontakt->setId( $_kkId) ;
		if ( $tmpLiefKontakt->_valid) {
			try {
				$this->LiefNr	=	$tmpLiefKontakt->LiefNr ;
				$this->LiefKontaktNr	=	$tmpLiefKontakt->LiefKontaktNr ;
				$this->updateInDb() ;
				$this->reload() ;
			} catch( Exception $e) {
				FDbg::dumpF( "AppObjectSR.php::AppObjectSR::setLiefFromLKId(...): exception='%s'", $e->getMessage()) ;
			}
		} else {
			FDbg::dumpL( 0x01000000, "AppObjectSR.php::AppObjectSR::setLiefFromLKId(...): LiefKontakt not valid !") ;
		}
		FDbg::dumpL( 0x01000000, "AppObjectSR.php::AppObjectSR::setLiefFromLKId(...): done") ;
		return $this->getXMLComplete() ;
	}
	/**
	 * Setzt den Prefix sowie den Postfix der Kundenbestellung auf die Default Werte.
	 * Die Default Werte werden bestimmt durch die f�r den Kunden abgespeicherten Wert
	 * f�r Sprache (z.B. de_de, en_us).
	 * Diese Funktion verwendet die Stored Procedure mit dem Namen: LfBest_setTexte( @status, <LfBestNr>).
	 *
	 * @return void
	 */
	function	_setTexte( $_key="", $_id=-1, $_lfLiefNr="") {
		try {
			$myTexte	=	new SysTexte( $this->className."Prefix", $this->LiefNr, $this->myLief->Sprache) ;
			$this->Prefix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Prefix") ;
			$myTexte	=	new SysTexte( $this->className."Postfix", $this->LiefNr, $this->myLief->Sprache) ;
			$this->Postfix	=	$myTexte->Volltext ;
			$this->updateColInDb( "Postfix") ;
		} catch( Exception $e) {
			throw $e ;
		}
	}
	function	setTexte( $_key="", $_id=-1, $_val="") {
		$this->_setTexte( $_key, $_id, $_val) ;
		return $this->getXMLString() ;
	}
	function	_setAnschreiben( $_key="", $_id=-1, $_val="") {
		try {
			$myTexte	=	new SysTexte( $this->className."EMail", $this->LiefNr, $this->myLief->Sprache) ;
			$this->Anschreiben	=	$myTexte->Volltext ;
			$this->updateColInDb( "Anschreiben") ;
		} catch( Exception $e) {
			throw $e ;
		}
	}
	function	setAnschreiben( $_key="", $_id=-1, $_val="") {
		$this->_setAnschreiben( $_key, $_id, $_val) ;
		return $this->getXMLString() ;
	}
	function	newLief( $_key, $_val, $_id) {
		try {
			$newLief	=	new Lief() ;
			$newLief->add( '', '', 0) ;
			$this->LiefNr	=	$newLief->LiefNr ;
			$this->LiefKontaktNr	=	$newLief->_addLiefKontakt() ;
			$this->updateInDb() ;
			$this->reload() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}

	function	newLiefKontakt( $_key, $_val, $_id) {
		try {
			$newLief	=	new Lief() ;
			$newLief->LiefNr	=	$this->LiefNr ;
			$this->LiefKontaktNr	=	$newLief->_addLiefKontakt() ;
			$this->updateInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete( '', '', 0) ;
	}

	/**
	 * Add a new item to the article list
	 *
	 * @param	string	article no.
	 * @param	int		id of sales price
	 * @param	int		quantity of items to be added
	 * @return	void
	 */
	function	addPos( $_key, $_artikelEKPreisId, $_menge) {
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
			$myArtikelEKPreis	=	new ArtikelEKPreis() ;
			$myArtikelEKPreis->setId( $_artikelEKPreisId) ;
			if ( $myArtikelEKPreis->_valid) {
				$myEKPreisR	=	new EKPreisR() ;
				$myEKPreisR->_firstFromDb( "LiefNr='".$myArtikelEKPreis->LiefNr."' AND LiefArtNr='".$myArtikelEKPreis->LiefArtNr."' ") ;
				$myArtikel	=	new Artikel() ;
				$myArtikel->setKey( $myEKPreisR->ArtikelNr) ;
				if ( $myArtikel->_valid) {
					$actItem	=	new $itemObjClassName( $myKey) ;
					$cond	=	sprintf( "$myKeyCol='%s' AND ArtikelNr='%s' AND SubItemNo = '' ",
											$myKey, $myArtikel->ArtikelNr) ;
					$actItem->_firstFromDb( $cond) ;
					if ( $actItem->_valid) {
						$actItem->Menge	+=	$_menge ;
						$actItem->updateInDb() ;
					} else {
error_log( "here I am, creating a new item ... " . $itemObjClassName) ;
						$newItem	=	new $itemObjClassName( $myKey) ;
						$newItem->getNextItemNo() ;
						$newItem->ArtikelNr	=	$myEKPreisR->ArtikelNr ;
						$newItem->LiefArtNr	=	$myArtikelEKPreis->LiefArtNr ;
						$newItem->OrdMode	=	$myEKPreisR->OrdMode ;
						$newItem->Menge	=	$_menge ;
						$newItem->Preis	=	$myArtikelEKPreis->Preis ;
						$newItem->MengeProVPE	=	$myArtikelEKPreis->MengeProVPE ;
						$newItem->MengeFuerPreis	=	$myArtikelEKPreis->MengeFuerPreis ;
						$newItem->GesamtPreis	=	$newItem->Menge * $newItem->Preis / $newItem->MengeFuerPreis ;
						$newItem->storeInDb() ;
					}
				} else {
					$e	=	new Exception( "LfBest::LfBest::invalid Artikel[".$myEKPreisR->ArtikelNr."]") ;
					error_log( $e) ;
					throw $e ;
				}
			} else {
				$e	=	new Exception( "LfBest::LfBest::invalid ArtikelEKPreisId[$_artikelEKPreisId]") ;
				error_log( $e) ;
				throw $e ;
			}
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 * methods: object retrieval
	 */
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
		$ret	=	$myObj->tableFromDb( ",L.FirmaName1, L.FirmaName2, L.PLZ, LK.Vorname, LK.Name ",
								"LEFT JOIN Lief AS L ON L.LiefNr = C.LiefNr "
									. "LEFT JOIN LiefKontakt AS LK ON LK.LiefNr = C.LiefNr AND LK.LiefKontaktNr = C.LiefKontaktNr ",
								$filter,
								"ORDER BY C.".$this->keyCol." DESC ",
								$this->className,
								$this->className,
								"C.Id, C.".$this->keyCol.", C.Datum ") ;
		return $ret ;
	}
	/**
	 *
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 * @return unknown
	 */
	function	getTableDepAsXML( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		$myKeyCol	=	$this->keyCol ;
		$myKey	=	$this->$myKeyCol ;
		$_POST['_step']	=	$_id ;
		$tmpObj	=	new $objName() ;
		$tmpObj->addCol( "ERPNo", "var") ;
		$tmpObj->addCol( "ArtikelBez1", "var") ;
		$tmpObj->addCol( "ArtikelBez2", "var") ;
		$tmpObj->addCol( "MengenText", "var") ;
		$ordBy	=	"ORDER BY C.ItemNo, C.SubItemNo " ;
//		if ( isset( $_POST[ '_SOrdMode']))
//			$ordBy	=	"ORDER BY " . self::$rOrdMode[ $_POST[ '_SOrdMode']] ;
		$ret	=	$tmpObj->tableFromDb( ", A.ERPNo, A.ArtikelBez1, A.ArtikelBez2, A.MengenText",
								"LEFT JOIN Artikel AS A ON A.ArtikelNr = C.ArtikelNr",
								"C." . $myKeyCol . " = '" . $myKey . "' ",
								$ordBy) ;
		return $ret ;
	}
	/**
	 *
	 * @return string
	 */
	function	getLiefAsXML() {
		$ret	=	"" ;
		$ret	.=	'<LiefAdr>' ;
		$ret	.=	$this->myLief->getXMLF() ;
		$ret	.=	$this->myLiefKontakt->getXMLF() ;
		$ret	.=	'</LiefAdr>' ;
		return $ret ;
	}
}
