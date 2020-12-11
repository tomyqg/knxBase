<?php
/**
 * 
 */
require_once( "textools.inc.php" );
/**
 * 
 * @var unknown_type
 */
$mEh["lmm"]	=	"mm" ;
$mEh["gmg"]	=	"mg" ;
$mEh["ggr"]	=	"gr" ;
$mEh["gkg"]	=	"kg" ;
$mEh["vml"]	=	"ml" ;
$mEh["vl"]	=	"L" ;
$mEh["stro"]	=	"Rolle" ;
$mEh["sthe"]	=	"Heft" ;
$mEh["stst"]	=	"Streifen" ;
$mEh["stsa"]	=	"Satz" ;
$mEh["stdo"]	=	"Dose" ;
$mEh["stfl"]	=	"Flasche" ;
$mEh["stvd"]	=	"Vorratsdose" ;
$mEh["stvf"]	=	"Vorratsflasche" ;
$mEh["stpa"]	=	"Paket" ;
$mEh["stam"]	=	"Ampulle" ;
$mEh["stck"]	=	"St." ;
/**
 * 
 * @author miskhwe
 *
 */
class	CatalogXML extends	EISSCoreObject	{
	private	$level	=	0 ;
	/**
	 * 
	 * @var unknown_type
	 */
	private	$myFilename	=	"modis-katalog" ;
	private	$chapter	=	"all" ;
	private	$artNr	=	"%" ;
	private	$liefNr	=	"%" ;
	private	$myOutFile	=	null ;
	/**
	 * 
	 * @param unknown_type $_katGr
	 */
	function	__construct() {
		FDbg::dumpL( 0x00000001, "CatalogXML.php::CatalogXML::__constuct(): ") ;
	}
	/**
	 * 
	 */
	function	setKey() {
		$this->_valid	=	true ;
	}
	/**
	 * 
	 * @param unknown_type $_filter
	 */
	function	createCatalog( $_filter, $_mode=-1, $_val="") {
		FDbg::dumpL( 0x00000001, "CatalogXML.php::CatalogXML::createCatalogXML( '$_filter', $_mode, '$_val'): ") ;
		$this->level	=	0 ;
		$myTexte	=	new Texte() ;
		$myKatGr	=	new KatGr() ; 
		$myArtikel	=	new Artikel() ; 
		/**
		 * @var string
		 */
		$f_name		=	"cat".$_filter ;
		$xml_name	=	$f_name . ".xml" ;
		$xml_name	=	$this->path->Catalog . $xml_name ;
		FDbg::dumpL( 0x00000001, "genkatR1A: xml_name := '$xml_name'") ;
		/**
		 * 
		 */
		$this->myOutFile	=	fopen( $xml_name, "w+") ;
		if ( $this->myOutFile) {
			fwrite( $this->myOutFile, "<doc toc=\"true\" toclevel=\"3\" cover=\"false\" xmlns:fo=\"http://www.w3.org/1999/XSL/Format\" lang=\"en\">") ;
			fwrite( $this->myOutFile, "<Copyright>2007-2011 Copyright MODIS GmbH, D-51674 Wiehl - Bomig, Robert-Bosch-Str. 1</Copyright>\n") ;
			/**
			 * 
			 */
			fwrite( $this->myOutFile, "<Image>".$this->path->Logos."logo_main.jpg"."</Image>\n") ;
			fwrite( $this->myOutFile, "<Scope>Laborshop</Scope>\n") ;
			fwrite( $this->myOutFile, "<Date>".$this->today()."</Date>\n") ;
			/**
			 * 
			 */
			$myTexte->fetchFromDbWhere( "where Name='KatAllgInfo' and Sprache='de_de' ") ;
			if ( $myTexte->_valid) {
				FDbg::dumpL( 0x00000001, "KatAllgInfo, de_de, Volltext='$myTexte->Volltext'") ;
				fwrite( $this->myOutFile, $myTexte->Volltext) ;
			}
			/**
			 * 
			 */
			fwrite( $this->myOutFile, "<Chapters>\n") ;
			if ( $myKatGr->setKey( $_filter)) {
				$this->writeKatGr( $myKatGr) ;
			}
			fwrite( $this->myOutFile, "</Chapters>\n") ;
			fwrite( $this->myOutFile, "</doc>\n") ;
		} else {
			printf( "Problems to create output file ... \n") ;
		}
		/**
		 * create the PDF file
		 */
		switch ( $_mode) {
		case	0	:		// normal
			$sysCmd	=	"fop -xml ".$xml_name . " "
						. "-xsl ".$this->path->Styles."catalog.xsl "
						. "-pdf ".$this->path->Catalog."catalog.pdf " ;
						system( $sysCmd, $res) ;
			break ;
		case	1	:		// outline w/o articles and prices
			$sysCmd	=	"fop -xml ".$xml_name . " "
						. "-xsl ".$this->path->Styles."catalogOutlineWOP.xsl "
						. "-pdf ".$this->path->Catalog."cat".$_filter."OlWOP.pdf " ;
						system( $sysCmd, $res) ;
			break ;
		case	2	:		// outline with articles and prices
			break ;
		}
		error_log( "sysCmd: '$sysCmd', result: $res") ;
		return true ;
	}
	/**
	 * 
	 * @param unknown_type $this->myOutFile
	 * @param unknown_type $_katGr
	 */
	function	writeKatGr( $_katGr) {
		/**
		 * 
		 */
		$this->level++ ;
		$myKatGrComp	=	new KatGrComp() ;
		$myKatGr	=	new KatGr() ;
		$myArtGr	=	new ArtGr() ;
		$myArtikel	=	new Artikel() ;
		/**
		 * 
		 */
		switch ( $this->level) {
		case	1	:
			$tag	=	"KatGr" ;
			break ;
		case	2	:
			$tag	=	"SubKatGr" ;
			break ;
		case	3	:
		case	4	:
			$tag	=	"SubSubKatGr" ;
			break ;
		}
		fwrite( $this->myOutFile, "<!-- Start Catalog Group '".$_katGr->KatGrNr."' -->\n") ;
		fwrite( $this->myOutFile, "<".$tag.">\n") ;
		fwrite( $this->myOutFile, "<KatGrNr>".$_katGr->KatGrNr."</KatGrNr>\n") ;
		fwrite( $this->myOutFile, "<Title>".$_katGr->KatGrName."</Title>\n") ;
		/**
		 * 
		 */
		$query	=	"select * " ; 
		$query	.=	"from KatGrComp " ; 
		$query	.=	"where KatGrNr = " . "'" . $_katGr->KatGrNr . "' " ;
		$query	.=	"order by " ;
		$query	.=	" PosNr ASC " ; 
		$query	.=	" , KatGrNr ASC " ; 
		$sqlResult	=       mysql_query( $query) ; 
		if ( !$sqlResult) { 
			echo $query . " \n" ;
			printf( "001: Probleme mit query ... \n") ;
			die() ;
		}
		$numrows	=       mysql_affected_rows() ; 
		if ( $numrows < 1) {
			fwrite( $this->myOutFile, "In dieser Produkt Gruppe sind keine Produkte vorhanden.\n") ;
		}
		//
		// Fuer jeden Eintrag in "KatGr" muss eine statische Seite gebaut werden
		//
		if ( strlen( $_katGr->Volltext) > 0) {
			FDbg::dumpL( 0x00000001, "KatGr->Volltext='$_katGr->Volltext'") ;
			fwrite( $this->myOutFile, "<Description>".$_katGr->Volltext."</Description>") ;
		}
		while ($row = mysql_fetch_assoc( $sqlResult)) {
			$myKatGrComp->assignFromRow( $row) ;
			//
			// wenn: neue ArtikelGruppe
			//	wenn: alte ArtikelGruppe noch beendet werden muss
			//		dann HTML fuer ArtikelGruppen Ende schreiben
			//	dann HTML fuer ArtikelGruppen Anfang schreiben
			//	Artikeldaten zuweisen
			// Artikel ausgeben
			// verschiedene Marker setzen
			//
			if ( $myKatGrComp->CompKatGrNr != "") {			// eigenstaendiger Artikel
				$myKatGr->KatGrNr	=	$myKatGrComp->CompKatGrNr ;
				$myKatGr->fetchFromDb() ;
				$this->writeKatGr( $myKatGr) ;
			} else if ( $myKatGrComp->CompArtGrNr != "") {	// eigenstaendiger Artikel
				$myArtGr->ArtGrNr	=	$myKatGrComp->CompArtGrNr ;
				$myArtGr->fetchFromDb() ;
				$this->writeArtGr( $myArtGr) ;
			} else if ( $myKatGrComp->CompArtNr != "") {	// eigenstaendiger Artikel
				$myArtikel->ArtikelNr	=	$myKatGrComp->CompArtNr ;
				$myArtikel->fetchFromDb() ;
				$this->writeArt( $myArtikel) ;
			}
		}
		fwrite( $this->myOutFile, "<!-- End Catalog Group '".$_katGr->KatGrNr."' -->\n") ;
		fwrite( $this->myOutFile, "</".$tag.">\n") ;
		$this->level-- ;
	}
	/**
	 * 
	 * @param unknown_type $this->myOutFile
	 * @param unknown_type $_artGr
	 */
	function	writeArtGr( $_artGr) {
		/**
		 * 
		 * @var unknown_type
		 */
		$this->level++ ;
		$myArtGrComp	=	new ArtGrComp() ;
		$myArtikel	=	new Artikel() ;
		$subArtGrComp	=	new ArtGrComp() ;
		/**
		 * 
		 */
		fwrite( $this->myOutFile, "<!-- Start Article Group '".$_artGr->ArtGrNr."' -->\n") ;
		$this->artGruppeStart( $_artGr) ;
		/**
		 * 
		 */
		$where	=	"ArtGrNr = " . "'" . $_artGr->ArtGrNr . "' " ;
		$where	.=	"ORDER BY PosNr ASC " ;
		fwrite( $this->myOutFile, "<Prices>\n") ;
		for ( $valid = $myArtGrComp->_firstFromDb( $where) ; 
					$valid ;
					$valid = $myArtGrComp->_nextFromDb()) {
			if ( $myArtGrComp->CompArtNr != "") {
				if ( strpos( $myArtGrComp->CompArtNr, "%") != 0 || strpos( $myArtGrComp->CompArtNr, "_") != 0) {
					$whereArtikel	=	"ArtikelNr like " . "'" . $myArtGrComp->CompArtNr . "' " ;
					$whereArtikel	.=	"ORDER BY ArtikelNr ASC " ;
					for ( $valid = $myArtikel->_firstFromDb( $whereArtikel) ; 
								$valid ;
								$valid = $myArtikel->_nextFromDb()) {
						$this->writeArtikel( $myArtikel) ;
					}
				} else {
					$myArtikel->ArtikelNr	=	$myArtGrComp->CompArtNr ;
					$myArtikel->fetchFromDb() ;
					$this->writeArtikel( $myArtikel) ;
				}
			} else if ( $myArtGrComp->CompArtGrNr != "") {
	//			FDbg::dumpL( 1, "Starting ArtGrNr .... %s \n", sprintf( "%s", $myArtGrComp->CompArtGrNr)) ;
	//			$subArtGr	=	new ArtGr ;
	//			$subArtGr->ArtGrNr	=	$myArtGrComp->CompArtGrNr ;
	//			$subArtGr->fetchFromDb() ;
	//			$this->writeArtGr( $subArtGr) ;
			}
		}
		fwrite( $this->myOutFile, "</Prices>\n") ;
		/**
		 * 
		 */
		$this->artGruppeEnd() ;
		fwrite( $this->myOutFile, "<!-- End Article Group '".$_artGr->ArtGrNr."' -->\n") ;
		$this->level-- ;
	}
	/**
	 * 
	 * @param unknown_type $this->myOutFile
	 * @param unknown_type $_artikel
	 */
	function	writeArtikel( $_artikel) {
		/**
		 * 
		 */
		global	$mEh ;
		/**
		 * 
		 * @var unknown_type
		 */
		$myVKPreisCache	=	new VKPreisCache() ;
		/**
		 * 
		 */
		fwrite( $this->myOutFile, "<!-- Start Article '".$_artikel->ArtikelNr."' -->\n") ;
		/**
		 * 
		 * @var unknown_type
		 */
		$where	=	"ArtikelNr = " . "'" . $_artikel->ArtikelNr . "' " ;
//		$where	.=	"AND GueltigVon<= '" . $this->today() . "' " ;
//		$where	.=	"AND GueltigBis > '" . $this->today() . "' " ;
		$where	.=	"ORDER BY ArtikelNr ASC " ;
		$lastMengeProVPE	=	0 ;
		for ( $valid = $myVKPreisCache->_firstFromDb( $where) ; 
					$valid ;
					$valid = $myVKPreisCache->_nextFromDb()) {
			$this->writeVKPreisCache( $_artikel, $myVKPreisCache) ;
		}
		fwrite( $this->myOutFile, "<!-- End Article '".$_artikel->ArtikelNr."' -->\n") ;
	}
	/**
	 * 
	 * @param unknown_type $this->myOutFile
	 * @param unknown_type $_artikel
	 */
	function	writeArt( $_artikel) {
		/**
		 * 
		 */
		fwrite( $this->myOutFile, "<!--Start Article outside Article Group '".$_artikel->ArtikelNr."' -->\n") ;
		$this->einzelArtikelStart( $_artikel) ;
		/**
		 * 
		 */
		fwrite( $this->myOutFile, "<Prices>\n") ;
		$this->writeArtikel( $_artikel) ;
		fwrite( $this->myOutFile, "</Prices>\n") ;
		/**
		 * 
		 */
		$this->einzelArtikelEnd() ;
		fwrite( $this->myOutFile, "<!--End Article outside Article Group '".$_artikel->ArtikelNr."' -->\n") ;
	}
	/**
	 * 
	 * @param unknown_type $this->myOutFile
	 * @param unknown_type $_artGr
	 */
	function	artGruppeStart( $_artGr) {
		fwrite( $this->myOutFile, "<ArtGr>\n") ;
		fwrite( $this->myOutFile, "<ArtGrNr>".$_artGr->ArtGrNr."</ArtGrNr>\n") ;
		fwrite( $this->myOutFile, "<Title>"./*$_artGr->ArtGrNr.":".*/$_artGr->ArtGrName . "</Title>\n") ;
		fwrite( $this->myOutFile, "<index-artgr>".$_artGr->ArtGrName . "</index-artgr>\n") ;
		//		if ( $_artGr->ShowImage > 0) {
			if ( file_exists( $this->path->Pictures . $_artGr->AGBildRef) && $_artGr->AGBildRef != "" && strpos( $_artGr->AGBildRef, "eer") === false) {
				fwrite( $this->myOutFile, "<Image>".$this->path->Pictures."print/" . $_artGr->AGBildRef . "</Image>\n") ;
			}
//		}
		if ( strlen( $_artGr->Volltext) > 0) {
			fwrite( $this->myOutFile, "<Description>".$_artGr->Volltext."</Description>") ;
		}
	}
	/**
	 * 
	 * @param unknown_type $this->myOutFile
	 */	
	function	artGruppeEnd() {
		fwrite( $this->myOutFile, "</ArtGr>\n") ;
	}
	/**
	 * 
	 * @param unknown_type $this->myOutFile
	 * @param unknown_type $_artikel
	 */
	function	einzelArtikelStart( $_artikel) {
		fwrite( $this->myOutFile, sprintf( "<Artikel>\n")) ;
		fwrite( $this->myOutFile, sprintf( "<Title>".$_artikel->ArtikelBez1."</Title>\n")) ;
		if ( file_exists( $this->path->Pictures . $_artikel->BildRef) && $_artikel->BildRef != "" && strpos( $_artikel->BildRef, "eer") === false) {
			$fileparts	=	explode( ".", $_artikel->BildRef) ;
			fwrite( $this->myOutFile, "<Image>".$this->path->Pictures."print/" . $_artikel->BildRef . "</Image>\n") ;
		}
		if ( strlen( $_artikel->Volltext) > 0) {
			fwrite( $this->myOutFile, sprintf( "<Volltext>\n")) ;
			fwrite( $this->myOutFile, $_artikel->Volltext) ;
			fwrite( $this->myOutFile, sprintf( "</Volltext>\n")) ;
		}
	}
	/**
	 * 
	 * @param unknown_type $this->myOutFile
	 */
	function	einzelArtikelEnd() {
		fwrite( $this->myOutFile, "</Artikel>\n") ;
	}
	/**
	 * 
	 * @param unknown_type $this->myOutFile
	 * @param unknown_type $_artikel
	 */
	function	writeArtikelKomp( $_artikel) {
		global $mEh ;
		/*
		 * 
		 */
		$query3	=	"select * " ;
		$query3	.=	"from ArtKomp " ;
		$query3	.=	"where ArtikelNr = " . "'" . $_artikel->ArtikelNr . "' " ;
		$query3	.=	"order by PosNr ASC " ;
		$sqlResult3      =       mysql_query( $query3) ;
		if ( !$sqlResult3) {
			echo $query3 . " \n" ;
			printf( "005: Probleme mit query ... \n") ;
			die() ;
		}
		$numrows3	=	mysql_affected_rows() ;
		if ( $numrows3 > 0) {
			fwrite( $this->myOutFile, "<!-- Start Components -->\n") ;
			$myArtikel	=	new Artikel() ;
			$myArtKomp	=	new ArtKomp() ;
			$myVKPreisCache	=	new VKPreisCache() ;
			while ($row3 = mysql_fetch_assoc( $sqlResult3)) {
				$myArtKomp->assignFromRow( $row3) ;
				$myArtikel->ArtikelNr	=	$myArtKomp->CompArtikelNr ;
				$myArtikel->fetchFromDb() ;
				$myVKPreisCache->ArtikelNr	=	$myArtKomp->CompArtikelNr ;
				$myVKPreisCache->fetchFromDb() ;
				$this->writeVKPreisCache( $myArtikel, $myVKPreisCache, "+") ;
			}
		}
	}
	/**
	 * 
	 * @param Artikel $this->myOutFile
	 * @param VKPreisCache $_vkpreis
	 */
	function	writeVKPreisCache( $_artikel, $_vkpreis) {
		/**
		 * 
		 */
		global $mEh ;
		/**
		 * 
		 */
		if ( strlen( $_artikel->ArtikelBez2) != 0) {
			$artikelText	=	$_artikel->ArtikelBez2 ;
		} else {
			$artikelText	=	$_artikel->ArtikelBez1 ;
		}
		if ( strlen( $_artikel->MengenText) > 5) {
			$artikelText	.=	$_artikel->MengenText ;
		}
		/**
		 * 
		 */
		fwrite( $this->myOutFile, "<Price>\n") ;
		if ( $_artikel->ERPNo != "") {
			fwrite( $this->myOutFile, "<ArtikelNr><index-artnr>".$_artikel->ERPNo."</index-artnr></ArtikelNr>\n") ;
		} else {
			fwrite( $this->myOutFile, "<ArtikelNr><index-artnr>".$_artikel->ArtikelNr."</index-artnr></ArtikelNr>\n") ;
		}
		fwrite( $this->myOutFile, "<Text>".$artikelText."</Text>\n") ;
		$myPriceText	=	$_vkpreis->Preis ;
		if ( $_vkpreis->MengeProVPE > 1) {
			$myPriceText	.=	"/" . $_vkpreis->MengeProVPE ;
		}
/*		fwrite( $this->myOutFile, "<Menge>".$myMenge."</Menge>\n") ;		*/
		fwrite( $this->myOutFile, "<Preis>".$myPriceText."</Preis>\n") ;
		fwrite( $this->myOutFile, "</Price>\n") ;
	}
}
?>
