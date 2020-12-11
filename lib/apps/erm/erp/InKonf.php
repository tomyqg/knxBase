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
 * requires the WTF Debugger and the Base Class
 */
require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;
require_once( "base/AppObjectCR.php") ;
require_once( "base/AppDepObject.php") ;
require_once( "option.php") ;
require_once( "XmlTools.php" );
/**
 * InKonf - User-Level Class for usage by the application
 *
 * This class acts as an interface towards the automatically generated BInKonf which should
 * not be modified.
 *
 * @package Application
 * @subpackage InKonf
 */
class	InKonf	extends	AppObjectCR	{

	const	NEU			=	  0 ;
	public	static	$rStatus	=	array (
						InKonf::NEU => "Neu",
						1 => "---undefiniert---",
						2 => "Im Lager gebucht",
						3 => "---undefiniert---",
						4 => "---undefiniert---",
						5 => "Angefangen",
						6 => "---undefiniert---",
						7 => "---undefiniert---",
						8 => "---undefiniert---",
						9 => "Abgeschlossen"
					) ;
	/**
	 * The constructor can be passed an ArticleNr (InKonfNr), in which case it will automatically
	 * (try to) load the respective article via the base class from the Database
	 *
	 * @param string $_artikelNr
	 * @return void
	 */
	function	__construct( $_myInKonfNr='') {
		AppObject::__construct( "InKonf", "InKonfNr") ;
		if ( strlen( $_myInKonfNr) >= 0) {
			$this->setInKonfNr( $_myInKonfNr) ;
		} else {
		}
	}
	/**
	 *
	 */
	function	setInKonfNr( $_myInKonfNr) {
		$this->InKonfNr	=	$_myInKonfNr ;
		if ( strlen( $_myInKonfNr) > 0) {
			$this->reload() ;
		} else {
		}
	}
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function	getRStatus() {
		return  self::$rStatus ;
	}
	/**
	 * 
	 */
	function	getXMLComplete( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLString() ;
		$ret	.=	$this->getTablePostenAsXML() ;
		$ret	.=	"<Document>" ;
		$filename	=	$this->path->Archive . "InKonf/" . $this->InKonfNr . ".pdf" ;
		if ( file_exists( $filename)) { 
			$ret	.=	 $this->url->Archive . "InKonf/" . $this->InKonfNr . ".pdf" ;
		}
		$ret	.=	"</Document>" ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getXMLString($_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$ret	.=	$this->getXMLF() ;
		$ret	.=	$this->getXMLDocInfo() ;
		return $ret ;
	}
	/**
	 * 
	 */
	function	getXMLDocInfo() {
		$ret	=	"<Document>" ;
		$filename	=	$this->path->Archive . "InKonf/" . $this->InKonfNr . ".pdf" ;
		if ( file_exists( $filename)) { 
			$ret	.=	 $this->url->Archive . "/InKonf/" . $this->InKonfNr . ".pdf" ;
		}
		$ret	.=	"</Document>" ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getInKonfPostenAsXML( $_key="", $_id=-1, $_val="") {
		$ret	=	"" ;
		$myInKonfPosten	=	new InKonfPosten() ;
		$myInKonfPosten->setId( $_id) ;
		$ret	.=	$myInKonfPosten->getXMLF() ;
		return $ret ;
	}
	/**
	 * 
	 * @param $_key
	 * @param $_id
	 * @param $_val
	 */
	function	getDocListAsXML( $_key="", $_id=-1, $_val="") {
		$fullPath	=	$this->path->Archive . "InKonf/" ;
		$myDir	=	opendir( $fullPath) ;
		$myFiles	=	array() ;
		while (($file = readdir( $myDir)) !== false) {
			if ( strncmp( $file, $this->InKonfNr, 6) == 0) {
				$myFiles[]	=	$file ;
			}
		}
		closedir( $myDir);
		reset( $myFiles) ;
		asort( $myFiles) ;
		$ret	=	"<DocList>\n" ;
		$ret	.=	"<URLPath>$this->url->Archive</URLPath>\n" ;
		foreach ( $myFiles as $file) {
			$ret	.=	"<Doc>\n" ;
			if ( filetype( $fullPath . $file) == "file") {
				$ret	.=	"<Filename>$file</Filename>\n" ;
				$ret	.=	"<Filetype>" . myFiletype( $file) . "</Filetype>\n" ;
				$ret	.=	"<Filesize>" . filesize( $fullPath . $file) . "</Filesize>\n" ;
				$ret	.=	"<FileURL>" . $this->url->Archive . "InKonf/" . $file . "</FileURL>\n" ;
			}
			$ret	.=	"</Doc>\n" ;
		}
		$ret	.=	"</DocList>" ;
		return $ret ;
	}
}
/**
 *
 */
class	InKonfPosten	extends	AppDepObject	{
	/**
	 * 
	 * @param $_myInKonfNr
	 */
	function	__construct( $_myInKonfNr='') {
		AppDepObject::__construct( "InKonfPosten", "Id") ;
		$this->InKonfNr	=	$_myInKonfNr ;
	}
	/**
	 *
	 * @return void
	 */
	function	buche() {
		FDbg::get()->dumpL( 99, "InKonfPosten::buche()") ;
		$myArtikelBestand	=	new ArtikelBestand( $this->ArtikelNr) ;
		$myArtikelBestand->getDefault() ;
		if ( $myArtikelBestand->_valid == 1) {
			$myArtikelBestand->reserve( ( $this->Menge * $this->MengeProVPE ) - $this->MengeReserviert ) ;
			$this->MengeReserviert	+=	( ( $this->Menge * $this->MengeProVPE ) - $this->MengeReserviert ) ;
			$this->updateInDb() ;
		} else {
		}
	}
	/**
	 *
	 */
	function	unbuche() {
		$myArtikelBestand	=	new ArtikelBestand( $this->ArtikelNr) ;
		$myArtikelBestand->getDefault() ;
		if ( $myArtikelBestand->_valid == 1) {
			$myArtikelBestand->unreserve( $this->MengeReserviert ) ;
			$this->MengeReserviert	=	0 ;
			$this->updateInDb() ;
		}
	}
	/**
	 *
	 * @return void
	 */
	function	getNextPosNr() {
		FDbg::get()->dumpL( 0x01000000, "InKonfPosten::getNextPosNr()") ;
		$query	=	sprintf( "SELECT PosNr FROM InKonfPosten WHERE InKonfNr='%s' ORDER BY PosNr DESC LIMIT 0, 1 ", $this->InKonfNr) ;
		$sqlResult	=	mysql_query( $query, FDb::get()) ;
		if ( !$sqlResult) { 
			$this->_status  =       -1 ; 
		} else { 
			$row    =       mysql_fetch_array( $sqlResult) ; 
			$this->PosNr	=	$row[0] + 1 ;
		}
		FDbg::get()->dumpL( 0x01000000, "InKonfPosten::getNextPosNr() done") ;
		return $this->_status ;
	}		
}
?>
