<?php
/**
 *
 */

/**
 *
 */
class	WAStatus	{

	public	$_valid	=	false ;
	/**
	 *
	 */
	function	__construct() {
		error_log( "WAStatus::__construct(): ") ;
	}

	/**
	 *
	 */
	function	setKey( $_key) {
		error_log( "WAStatus::setKey( $_key): ") ;
	}

	/**
	 *
	 */
	function	getTableAsXML( $_key, $_val, $_id) {
		error_log( "WAStatus::setKey( $_key, $_val, $_id): ") ;
		$res	=	"" ;
		$cond	=	"Status < " . CuOrdr::CLOSED . " " ;
		$acpCuOrdr	=	new CuOrdr() ;
		$acpCuOrdrPos	=	new CuOrdrItem() ;
		$actArtikelBestand	=	new ArtikelBestand() ;
		$res	.=	"<WAStatusListe>\n" ;
		$cnt	=	0 ;
		for ( $acpCuOrdr->_firstFromDb( $cond) ;
				$acpCuOrdr->_valid ;
				$acpCuOrdr->_nextFromDb()) {
			$cnt++ ;
			$res	.=	"<CuOrdr>\n" ;
			$res	.=	$this->tag( "CuOrdrNo", $acpCuOrdr->CuOrdrNo) ;
			$res	.=	$this->tag( "Status", $acpCuOrdr->Status) ;
			$res	.=	$this->tag( "StatLief", $acpCuOrdr->StatLief) ;
			$res	.=	$this->tag( "StatRech", $acpCuOrdr->StatRech) ;
			
			$res	.=	$this->tag( "StatComm", $acpCuOrdr->checkComm()) ;
			/**
			 *
			 */
//			$res	.=	"<CuOrdrItemListe>\n" ;
//			$acpCuOrdrPos->CuOrdrNo	=	$acpCuOrdr->CuOrdrNo ;
//			for ( $acpCuOrdrPos->firstFromDb( "CuOrdrNo", "Artikel", array(
//																		"ArtikelBez1" => "var",
//																		"ArtikelBez2" => "var",
//																		"MengenText" => "var",
//																		"MwstSatz" => "var",
//																		"Comp" => "var"
//																), "ArtikelNr") ;
//				$acpCuOrdrPos->_valid == 1 ;
//				$acpCuOrdrPos->nextFromDb()) {
//
//				/**
//				 *
//				 */
//				$actArtikelBestand->getDefault( $acpCuOrdrPos->ArtikelNr) ;
//
//				/**
//				 *
//				 */
//				$res	.=	"<PosNr>" . $acpCuOrdrPos->PosNr . "</PosNr>\n" ;
//				$res	.=	"<SubPosNr>" . $acpCuOrdrPos->SubPosNr . "</SubPosNr>\n" ;
//				$res	.=	"<ArtikelNr>" . $acpCuOrdrPos->ArtikelNr . "</ArtikelNr>\n" ;
//				$res	.=	$this->tag( "ArtikelBez1", $acpCuOrdrPos->ArtikelBez1) ;
//				$res	.=	"<ArtikelBestand>\n" ;
//				$res	.=	"<LagerOrt>" . $actArtikelBestand->LagerOrt . "</LagerOrt>\n" ;
//				$res	.=	"<Lagerbestand>" . $actArtikelBestand->Lagerbestand . "</Lagerbestand>\n" ;
//				$res	.=	"</ArtikelBestand>\n" ;
//			}
//			$res	.=	"</CuOrdrItemListe>\n" ;
			$res	.=	"</CuOrdr>\n" ;
		}
		$res	.=	$this->tag( "Count", $cnt) ;
		$res	.=	"</WAStatusListe>\n" ;
		return $res ;
	}

	/**
	 *
	 */
	function	__clone() {
	}
	
	/**
	 * 
	 */
	function	tag( $_tagName, $_tagValue) {
		return "<$_tagName><![CDATA[$_tagValue]]></$_tagName>\n" ;
	}
}	
?>
