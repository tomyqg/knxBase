<?php
/**
 * BDocument.php Base class for PDF-format printed matters
 * 
 * This file demonstrates the rich information that can be included in
 * in-code documentation through DocBlocks and tags.
 * @author Karl-Heinz Welter <karl@modis-gmbh.eu>
 * @version 0.1
 * @package Application
 */
/**
 * CatalogGroup - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BCatalogGroup which should
 * not be modified.
 *
 * @package Application
 * @subpackage CatalogGroup
 */

class	CatalogGroup	extends	AppObject	{

	private	$tmpCatalogGroupItem ;

	/*
	 * The constructor can be passed an ArticleNr (CatalogGroupNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_katGrNr='') {
		parent::__construct( "CatalogGroup", "CatalogGroupNr") ;
		if ( strlen( $_katGrNr) > 0) {
			$this->setCatalogGroupNr( $_katGrNr) ;
		} else {
		}
	}

	/**
	 *
	 */
	function	setCatalogGroupNr( $_katGrNr='') {
		if ( strlen( $_katGrNr) > 0) {
			$this->CatalogGroupNr	=	$_katGrNr ;
			$this->reload() ;
		} else {
		}
	}

	/**
	 *
	 * @return CuOrdrPos
	 */
	function	getFirstComp() {
		$this->tmpCatalogGroupItem	=	new CatalogGroupItem() ;
		$this->tmpCatalogGroupItem->CatalogGroupNr	=	$this->CatalogGroupItem ;
		$this->tmpCatalogGroupItem->firstFromDb() ;
		return $this->tmpCatalogGroupItem ;
	}

	/**
	 *
	 * @return CuOrdrPos
	 */
	function	getNextComp() {
		$this->tmpCatalogGroupItem->nextFromDb() ;
		return $this->tmpCatalogGroupItem ;
	}

	function	newCatalogGroup( $_key="", $_id=-1, $_val) {
		$newTexte	=	new Texte() ;
		FDbg::dumpL( 0x01000000, "CatalogGroup::add(...)") ;
		try {
			$this->newKey( 8, "00000000", "99999999") ;
			$myKey	=	$this->CatalogGroupNr ;
			$this->getFromPostL() ;
			$this->CatalogGroupNr	=	$myKey ;
			$this->updateInDb() ;
			$newTexte->Name	=	"CatalogGroupName" ;
			$newTexte->RefNr	=	$this->CatalogGroupNr ;
			$newTexte->Volltext	=	$this->CatalogGroupName ;
			$newTexte->Sprache	=	"de" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"en" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"es" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"fr" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"nl" ;
			$newTexte->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}
	/**
	 * add(...)
	 * 
	 * this method automatically creates 'untranslated' entries in the table Texte.
	 * w/o these basic entries the site generation would fail
	 * 
	 * @param unknown_type $_key
	 * @param unknown_type $_id
	 * @param unknown_type $_val
	 */
	function	add( $_key, $_id, $_val) {
		$newTexte	=	new Texte() ;
		FDbg::dumpL( 0x01000000, "CatalogGroup::add(...)") ;
		try {
			$this->getFromPostL() ;
			$this->ArtGrNr	=	$_key ;
			$this->storeInDb() ;
			$newTexte->Name	=	"CatalogGroupName" ;
			$newTexte->RefNr	=	$this->CatalogGroupNr ;
			$newTexte->Volltext	=	$this->CatalogGroupName ;
			$newTexte->Sprache	=	"de" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"en" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"es" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"fr" ;
			$newTexte->storeInDb() ;
			$newTexte->Sprache	=	"nl" ;
			$newTexte->storeInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getXMLString() ;
	}

	function	addCatalogGroupItem( $_key, $_id, $_val) {
		$myCatalogGroupItem	=	new CatalogGroupItem() ;
		$myCatalogGroupItem->getFromPostL() ;
		$myCatalogGroupItem->CatalogGroupNr	=	$this->CatalogGroupNr ;
		$myCatalogGroupItem->storeInDb() ;
		return $this->getTableCatalogGroupItemAsXML() ;
	}

	function	updCatalogGroupItem( $_key, $_id, $_val) {
		try {
			$myCatalogGroupItem	=	new CatalogGroupItem() ;
			$myCatalogGroupItem->setId( $_id) ;
			$myCatalogGroupItem->getFromPostL() ;
			$myCatalogGroupItem->updateInDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getTableCatalogGroupItemAsXML() ;
	}

//	function	delCatalogGroupItem( $_key, $_id, $_val) {
	function	delPos( $_key, $_id, $_val) {
		try {
			$myCatalogGroupItem	=	new CatalogGroupItem() ;
			$myCatalogGroupItem->setId( $_id) ;
			$myCatalogGroupItem->removeFromDb() ;
		} catch ( Exception $e) {
			throw $e ;
		}
		return $this->getTableCatalogGroupItemAsXML() ;
	}

	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTableDepsAsXML( $_key, $_id, "CatalogGroupItem") ;
		$ret	.=	$this->getTableSubCatalogGroupAsXML() ;
		return $ret ;
	}

	function	getXMLString() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}

	function	getCatalogGroupItemAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		/**
		 *
		 */
		$myCatalogGroupItem	=	new CatalogGroupItem() ;
		$myCatalogGroupItem->setId( $_id) ;
		$ret	.=	$myCatalogGroupItem->getXMLF() ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getTableDepsAsXML( $_key="", $_id=-1, $_val="") {
		$objName	=	$_val ;
		$tmpObj	=	new $objName() ;
		$tmpObj->addCol( "ERPNo", "var") ;
		$tmpObj->addCol( "CatalogGroupName", "var") ;
		$tmpObj->addCol( "ArtGrName", "var") ;
		$tmpObj->addCol( "ArtikelBez1", "var") ;
		$tmpObj->addCol( "ArtikelBez2", "var") ;
		$ret	=	$tmpObj->tableFromDb( ", KG.CatalogGroupName, AG.ArtGrName, A.ArtikelBez1, A.ArtikelBez2 ",
								"LEFT JOIN CatalogGroup AS KG ON KG.CatalogGroupNr = C.CompCatalogGroupNr "
								. "LEFT JOIN ArtGr AS AG ON AG.ArtGrNr = C.CompArtGrNr "
								. "LEFT JOIN Artikel AS A ON A.ArtikelNr = C.CompArtNr " ,
								"C.CatalogGroupNr = '$this->CatalogGroupNr' ",
								"ORDER BY C.PosNr ") ;
		return $ret ;
	}
	/**
	 * 
	 */
	function	getTableSubCatalogGroupAsXML() {
		$ret	=	"" ;
		$mySubCatalogGroup	=	new CatalogGroup() ;
//		$ret	.=	$mySubCatalogGroup->tableFromDb( "", "", "C.CatalogGroupNr like '" . $this->CatalogGroupNr . ":%' ") ;
		$ret	.=	$mySubCatalogGroup->tableFromDb( "", "", "C.CatalogGroupNr like '" . $this->CatalogGroupNr . ":%' ", "ORDER BY CatalogGroupNr ", "SubCatalogGroup") ;
		return $ret ;
	}
	/**
	 * #
	 * @param string $_key
	 * @param int $_id
	 * @param mixed $_val
	 */
	function	renumCatalogGroup( $_key="", $_id=-1, $_val="") {
		$newCatalogGroupNr	=	$_POST['_ICatalogGroupNrNeu'] ; ;
		if ( strlen ( $newCatalogGroupNr) == 8) {
			$count	=	FDb::getCount( "FROM $this->className WHERE CatalogGroupNr = '$newCatalogGroupNr' ") ;
			if ( $count != 0) {
				throw new Exception( "CatalogGroup.php::CatalogGroup::renumCatalogGroup( '$_key', $_id, '$_val'): new catalog group no. already in use! [#:$count]") ;
			}
			try {
				FDb::query( "UPDATE CatalogGroupItem SET CompCatalogGroupNr = '$newCatalogGroupNr' WHERE CompCatalogGroupNr = '$this->CatalogGroupNr' ") ;
				FDb::query( "UPDATE CatalogGroupItem SET CatalogGroupNr = '$newCatalogGroupNr' WHERE CatalogGroupNr = '$this->CatalogGroupNr' ") ;
				FDb::query( "UPDATE CatalogGroup SET CatalogGroupNr = '$newCatalogGroupNr' WHERE CatalogGroupNr = '$this->CatalogGroupNr' ") ;
				$this->CatalogGroupNr	=	$newCatalogGroupNr ;
				$this->reload() ;
			} catch ( Exception $e) {
				error_log( "CatalogGroup.php::CatalogGroup::renumCatalogGroup( '$_key', $_id, '$_val'): exception '" . $e->getMessage() . "'!") ;
			}
		} else {
			throw new Exception( "CatalogGroup.php::CatalogGroup::renumCatalogGroup( '$_key', $_id, '$_val'): new catalog group no. [" . $newCatalogGroupNr."] is too short!") ;
		}
		return $this->getXMLComplete() ;
	}
	/**
	 * 
	 * @param unknown_type $_digits
	 */
	function	newKey( $_digits=8, $_nsStart="00000000", $_nsEnd="99999999") {
		$myQuery	=	"SELECT IFNULL(( SELECT CatalogGroupNr + 1 FROM CatalogGroup " .
						"WHERE $_nsStart <= CatalogGroupNr AND CatalogGroupNr <= $_nsEnd " .
						"ORDER BY CatalogGroupNr DESC LIMIT 1 ), 1)  AS newKey" ;
		$myRow	=	FDb::queryRow( $myQuery) ;
		$keyCol	=	$this->keyCol ;
		$this->$keyCol	=	sprintf( "%0".$_digits."s", $myRow['newKey']) ;
		$this->storeInDb() ;
		$this->reload() ;
	}
}
?>
