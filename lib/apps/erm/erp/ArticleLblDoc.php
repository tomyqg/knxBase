<?php
/**
 * 
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/pdfdoc/BDocArtLbl55x25.php") ;
require_once( "pkgs/pdfdoc/BDocArtLbl75x25.php") ;
require_once( "pkgs/pdfdoc/BDocArtLbl100x50.php") ;
require_once( "pkgs/pdfdoc/BDocArtLbl100x55.php") ;
require_once( "pkgs/pdfdoc/BDocArtLbl150x100.php") ;
require_once( "pkgs/pdfdoc/BDocArtLbl100x60.php") ;
/**
 * 
 * @author miskhwe
 *
 */
class	ArticleLblDoc	extends	EISSCoreObject	{
	/**
	 * 
	 * @var unknown_type
	 */
	public	$_valid ;
	private	$myArticle ;
	/**
	 * 
	 * @param $_artikelNr
	 */
	function	__construct( $_artikelNr="") {
		error_log( "ArticleLblDoc::__construct(\"$_artikelNr\")") ;
		if ( $_artikelNr != "") {
			$this->myArticle	=	new Artikel( $_artikelNr) ;
		}
	}
	/**
	 * 
	 * @param $_artikelNr
	 */
	function	setKey( $_artikelNr) {
		error_log( "ArticleLblDoc::__construct(\"$_artikelNr\")") ;
		$this->myArticle	=	new Artikel( $_artikelNr) ;
		$this->_valid	=	$this->myArticle->_valid ;
	}
	/**
	 * 
	 */
	function	getPDF() {
		$myLbl	=	new BDocArtLbl55x25() ;
		$myLbl->begin() ;
		$myLbl->addMyText( $this->myArticle->getFullText(1)) ;
		$myLbl->addArticleNr( $this->myArticle->ArtikelNr) ;
		$myLbl->end( $this->path->Archive . "test.pdf") ;

	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	createPDF55x25( $_key="", $_id=0, $_val="") {
		$myLbl	=	new BDocArtLbl55x25() ;
		$myLbl->begin() ;
		$myLbl->addMyText( iconv('UTF-8', 'windows-1252', $this->myArticle->getFullText(1))) ;
		$myLbl->addArticleNr( ( strlen( $this->myArticle->ERPNo > 0) ? $this->myArticle->ERPNo : $this->myArticle->ArtikelNr), $this->myArticle->ArtikelNr) ;
		$myLbl->end( $this->path->Archive . "test.pdf") ;
		$cmd	=	"lpr -P " . $this->printer->artlabel55x25 . " " . $this->path->Archive . "test.pdf" ;
		system( $cmd) ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	createPDF75x25( $_key="", $_id=0, $_val="") {
		$myLbl	=	new BDocArtLbl75x25() ;
		$myLbl->begin() ;
		$myLbl->addMyText( iconv('UTF-8', 'windows-1252', $this->myArticle->getFullText(1))) ;
		$myLbl->addArticleNr( ( strlen( $this->myArticle->ERPNo > 0) ? $this->myArticle->ERPNo : $this->myArticle->ArtikelNr), $this->myArticle->ArtikelNr) ;
		$myLbl->end( $this->path->Archive . "test.pdf") ;
		system( "lpr -P " . $this->printer->artlabel75x25 . " " . $this->path->Archive . "test.pdf") ;
		
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	createPDF100x50( $_key="", $_id=0, $_val="") {
		$myLbl	=	new BDocArtLbl100x50() ;
		$myLbl->begin() ;
		$myLbl->addMyText( iconv('UTF-8', 'windows-1252', $this->myArticle->getFullText(1))) ;
		$myLbl->addArticleNr( ( strlen( $this->myArticle->ERPNo > 0) ? $this->myArticle->ERPNo : $this->myArticle->ArtikelNr), $this->myArticle->ArtikelNr) ;
		$myLbl->end( $this->path->Archive . "test.pdf") ;
		$cmd	=	"lpr -o landscape -P " . $this->printer->artlabel100x50 . " " . $this->path->Archive . "test.pdf" ;
		system( $cmd) ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	createPDF100x55( $_key="", $_id=0, $_val="") {
		$myLbl	=	new BDocArtLbl100x55() ;
		$myLbl->begin() ;
		$myLbl->addMyText( iconv('UTF-8', 'windows-1252', $this->myArticle->getFullText(1))) ;
		$myLbl->addArticleNr( ( strlen( $this->myArticle->ERPNo > 0) ? $this->myArticle->ERPNo : $this->myArticle->ArtikelNr), $this->myArticle->ArtikelNr) ;
		$myLbl->end( $this->path->Archive . "test.pdf") ;
		$cmd	=	"lpr -P " . $this->printer->artlabel100x55 . " " . $this->path->Archive . "test.pdf" ;
		system( $cmd) ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	createPDF150x100( $_key="", $_id=0, $_val="") {
		$myLbl	=	new BDocArtLbl150x100() ;
		$myLbl->begin() ;
		$myLbl->addMyText( iconv('UTF-8', 'windows-1252', $this->myArticle->getFullText(1))) ;
		$myLbl->addArticleNr( ( strlen( $this->myArticle->ERPNo > 0) ? $this->myArticle->ERPNo : $this->myArticle->ArtikelNr), $this->myArticle->ArtikelNr) ;
		$myLbl->end( $this->path->Archive . "test.pdf") ;
		$cmd	=	"lpr -o landscape -P " . $this->printer->artlabel150x100 . " " . $this->path->Archive . "test.pdf" ;
		system( $cmd) ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	createPDF100x60( $_key="", $_id=0, $_val="") {
		$myLbl	=	new BDocArtLbl100x60() ;
		$myLbl->begin() ;
		$myLbl->addMyText( iconv('UTF-8', 'windows-1252', $this->myArticle->getFullText(1))) ;
		$myLbl->addArticleNr( ( strlen( $this->myArticle->ERPNo > 0) ? $this->myArticle->ERPNo : $this->myArticle->ArtikelNr), $this->myArticle->ArtikelNr) ;
		$myLbl->end( $this->path->Archive . "test.pdf") ;
		$cmd	=	"lpr -P " . $this->printer->artlabel100x60 . " " . $this->path->Archive . "test.pdf" ;
		system( $cmd) ;
	}
	/**
	 * 
	 * @param unknown_type $_token
	 */
	function	cascTokenStart( $_token) {
		error_log( "Here's my token: " . $_token . " to start") ;
	}
	/**
	 * 
	 * @param $_token
	 */
	function	cascTokenEnd( $_token) {
		error_log( "Here's my token: " . $_token . " to end") ;
	}

}

?>
