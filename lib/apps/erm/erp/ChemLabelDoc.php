<?php

require_once( "pkgs/platform/FDbg.php") ;

require_once( "pkgs/pdfdoc/BDocChemLbl100x60.php") ;
require_once( "ChemInfo.php" );
require_once( "Texte.php" );

class	ChemLabelDoc	extends	ChemInfo {
	
	const	ChemLabel100x60 =	0 ;

	private	$myChemLabel ;

	function	__construct( $_casNr, $_lang="de_de", $_size=ChemLabelDoc::ChemLabel100x60) {
		FDbg::dumpL( 0x00010000, "ChemLabelDoc::__construct( '%s')", $_casNr) ;
		switch ( $_size) {
		case	ChemLabelDoc::ChemLabel100x60	:
			$this->myChemInfo	=	ChemInfo::__construct() ;
			$this->setCASNrSprache( $_casNr, $_lang) ;
//			$this->_debug() ;
			$this->myChemLabel	=	new BDocChemLbl100x60() ;
			break ;
		}
		FDbg::dumpL( 0x00010000, "ChemLabelDoc::__construct(), done") ;
	}

	function	getPDF() {

		$this->myChemLabel->begin() ;

		/**
		 * 
		 */
		$this->myChemLabel->addMyHead1( $this->Heading) ;
		$this->myChemLabel->addMyXML( $this->Volltext) ;
		$this->myChemLabel->skipMyLine() ;
		
		$this->myChemLabel->addArticleNr( $this->CASNr) ;
		
		$this->myChemLabel->addContent( "100 ml") ;
		
		$myTexte	=	new Texte() ;

		$rSets	=	explode( ",", $this->RSets) ;
		foreach ( $rSets as $index => $rSet) {
			try {
				$rangeR	=	explode( "-", $rSet) ;
				if ( count( $rangeR) > 1) {
					for ( $i=intval( substr( $rangeR[0], 1)) ; $i<= intval( $rangeR[1]) ; $i++) {
						$rSet	=	"R" . $i ;
						$myTexte->setKeys( "RSet", $rSet, "de_de") ;
						$this->myChemLabel->addMyText( $rSet . ": " . $myTexte->Volltext) ;
					}
				} else {
					$myTexte->setKeys( "RSet", $rSet, "de_de") ;
					$this->myChemLabel->addMyText( $rSet . ": " . $myTexte->Volltext) ;
				}
			} catch ( Exception $e) {
//				$this->myChemLabel->addMyText( "Fehler") ;		
			}
		}
		$sSets	=	explode( ",", $this->SSets) ;
		foreach ( $sSets as $index => $sSet) {
			try {
				$rangeS	=	explode( "-", $sSet) ;
				if ( count( $rangeS)> 1) {
					for ( $i=intval( substr( $rangeS[0], 1)) ; $i<= intval( $rangeS[1]) ; $i++) {
						$sSet	=	"S" . $i ;
						$myTexte->setKeys( "SSet", $sSet, "de_de") ;
						$this->myChemLabel->addMyText( $sSet . ": " . $myTexte->Volltext) ;
					}
				} else {
					$myTexte->setKeys( "SSet", $sSet, "de_de") ;
					$this->myChemLabel->addMyText( $sSet . ": " . $myTexte->Volltext) ;
				}
			} catch ( Exception $e) {
//				$this->myChemLabel->addMyText( "Fehler") ;		
			}
			$sSet	=	strtok( ",") ;
		}
		$gefSym	=	strtok( $this->GefSym, ",") ;
		while ( strlen( $gefSym) > 0) {
			try {
				switch ( $gefSym) {
				case	"C"	:
					$this->myChemLabel->addSymbol( BDocChemLbl100x60::SymbolC) ;
					break ;	
				case	"F"	:
					$this->myChemLabel->addSymbol( BDocChemLbl100x60::SymbolF) ;
					break ;	
				case	"Xi"	:
					$this->myChemLabel->addSymbol( BDocChemLbl100x60::SymbolXI) ;
					break ;	
				case	"Xn"	:
					$this->myChemLabel->addSymbol( BDocChemLbl100x60::SymbolXN) ;
					break ;	
				}
			} catch ( Exception $e) {
				$this->myChemLabel->addMyText( "Fehler") ;		
			}
			$gefSym	=	strtok( ",") ;
		}

		/**
		 *
		 */
		$this->myChemLabel->end( "/tmp/test.pdf") ;

	}

	/**
	 *
	 */
	function	cascTokenStart( $_token) {
echo "Here's my token: " . $_token . " to start \n" ;
	}

	/**
	 *
	 */
	function	cascTokenEnd( $_token) {
echo "Here's my token: " . $_token . " to end \n" ;
	}

}

?>
