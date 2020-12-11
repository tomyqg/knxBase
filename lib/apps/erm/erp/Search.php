<?php

/**
 *
 *
 *
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/DbObject.php") ;
require_once( "base/AppObject.php") ;

/**
 * Artikel - User-Level Klasse
 *
 * This class acts as an interface towards the automatically generated BArtikel which should
 * not be modified.
 *
 *
 * @package Application
 * @subpackage Basic
 */
class	Search	{

	var	$_valid	=	false ;

	/**
	 * Erzeugung eines Objektes.
	 *
	 * Erzeugt ein Artikel-Objekt und versucht ggf. diesen Artikel aus der Db zu laden.
	 *
	 * @param string $_artikelNr='' Artikelnummer
	 * @return void
	 */
	function	__construct( $_artikelNr='') {
	}

	/**
	 *
	 */
	function	getTableAsXML( $_key, $_id, $_val) {
		$myArtikel	=	new Artikel() ;
		$myKunde	=	new Kunde() ;
		$myLief	=	new Lief() ;
		$ret	=	"" ;
		$ret	.=	"<Results>\n" ;

		/**
		 *
		 */
		for ( $myArtikel->_firstFromDb( "ArtikelNr like \"%" . $_val . "%\" OR ArtikelBez1 like \"%" . $_val . "%\" ") ;
				$myArtikel->_valid ;
				$myArtikel->_nextFromDb()) {
			$ret	.=	"<Result>\n" ;
			$ret	.=	"<Type>Article</Type>\n" ;
			$ret	.=	"<Data1>" . $myArtikel->ArtikelNr . "</Data1>\n" ;
			$ret	.=	"<Data2>" . $myArtikel->ArtikelBez1 . "</Data2>\n" ;
			$ret	.=	"<Data3>" . $myArtikel->ArtikelBez2 . "</Data3>\n" ;
			$ret	.=	"</Result>\n" ;
		}

		/**
		 *
		 */
		for ( $myKunde->_firstFromDb( "KundeNr like \"%" . $_val . "%\" OR FirmaName1 like \"%" . $_val . "%\" OR FirmaName2 like \"%" . $_val . "%\" ") ;
				$myKunde->_valid ;
				$myKunde->_nextFromDb()) {
			$ret	.=	"<Result>\n" ;
			$ret	.=	"<Type>Cust</Type>\n" ;
			$ret	.=	"<Data1>" . $myKunde->KundeNr . "</Data1>\n" ;
			$ret	.=	"<Data2>" . $myKunde->FirmaName1 . "</Data2>\n" ;
			$ret	.=	"<Data3>" . $myKunde->FirmaName2 . "</Data3>\n" ;
			$ret	.=	"</Result>\n" ;
		}

		/**
		 *
		 */
		for ( $myLief->_firstFromDb( "LiefNr like \"%" . $_val . "%\" ") ;
				$myLief->_valid ;
				$myLief->_nextFromDb()) {
			$ret	.=	"<Result>\n" ;
			$ret	.=	"<Type>Supp</Type>\n" ;
			$ret	.=	"<Data1>" . $myLief->LiefNr . "</Data1>\n" ;
			$ret	.=	"<Data2>" . $myLief->FirmaName1 . "</Data2>\n" ;
			$ret	.=	"<Data3>" . $myLief->FirmaName2 . "</Data3>\n" ;
			$ret	.=	"</Result>\n" ;
		}

		$ret	.=	"</Results>\n" ;
		return $ret ;
	}

	function	getXMLComplete() {
		$ret	=	"" ;
		$ret	.=	$this->getAsXML() ;
		return $ret ;
	}

	function	getAsXML() {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		return $ret ;
	}

	function	getArtikelAsXML( $_key, $_id, $_val) {
		$ret	=	"" ;
		return $ret ;
	}
}

?>
