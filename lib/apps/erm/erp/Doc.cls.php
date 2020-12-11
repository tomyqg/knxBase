<?php

require_once( "global.inc.php") ;
require_once( "iPrintable.inc.php") ;
require_once( "BriefDoc.cls.php" );

class	DocList	implements iPrintable {

	public	$doc ;
	public	$taxes = array() ;

	function	__construct() {
	}

	function	setFromBrief( $_briefNr) {
		$this->doc	=	new BriefDoc( $_briefNr) ;
	}

	function	setRcvrFromKunde( $_kundeNr, $_kundeKontaktNr) {
		$this->doc->setRcvrFromKunde( $_kundeNr, $_kundeKontaktNr) ;
	}

	function	archive() {

		global	$archivPath ;

		// create the letter (Brief)

		$pdfTargetName	=	$archivPath . "Briefe/" . $this->CuDlvrNo . ".pdf" ;
		$pdfName	=	$this->getPDF( 0, "A4", true) ;
		$systemCmd	=	"mv " . $pdfName . " " . $pdfTargetName . " " ;
printf( "%s <br/>", $systemCmd) ;
		system( $systemCmd) ;

	}

	function	printIt( $_prn) {

		global	$archivPath ;

		if ( strcmp( $_prn, "-") != 0) {
			$systemCmd	=	"lpr -P " . $_prn . " " . $archivPath . "Briefe/" . $this->CuDlvrNo . ".pdf " ;
			system( $systemCmd) ;
		}
	}

	function	getPDF( $_cnt, $_size, $_finalMode) {

		global	$texPath ;

		//
		// prepare file names for the different temporary files
		//
		//
		// sequence of file translations:
		//

		$tex_name	=	$this->getTeX( $_cnt, $_size, $_finalMode) ;
		$dvi_name	=	str_replace( ".tex", ".dvi", $tex_name) ;
		$aux_name	=	str_replace( ".tex", ".aux", $tex_name) ;
		$log_name	=	str_replace( ".tex", ".log", $tex_name) ;
		$pdf_name	=	str_replace( ".tex", ".pdf", $tex_name) ;

		// now run the pdf-latex compiler

		$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvipdf " . $dvi_name . " >/dev/null" ;
		system( $command) ;

		// remove all temporary files

//		system( "rm " . $tex_name) ;
		system( "rm " . $dvi_name) ;
		system( "rm " . $log_name) ;
		system( "rm " . $aux_name) ;
	//	system( "rm " . $pdf_name) ;

		return $pdf_name ;

	}

	function	getTeX( $_cnt, $_size, $_finalMode) {

		require_once( "textools.inc.php" );

		global	$texPath ;

		//

		$signature	=	"" ;

		$prefix	=	"" ;
		$postfix	=	"" ;

		//
		// prepare file names for the different temporary files
		//
		//
		// sequence of file translations:
		//

		$f_name	=	tempnam( "/tmp", "letter-") ;
		$tex_name	=	$f_name . ".tex" ;
		$tex_Iname	=	$texPath . "briefBasisV3.tex" ;

		//

		$texIFile	=	file_get_contents( $tex_Iname) ;
		if ( $texIFile) {
			$myReplTable	=	$this->doc->getReplTable() ;
			$myReplTable["SIGNATURE"]	=	"Karl-Heinz Welter" ;
			$myReplTableIn	=	array_keys( $myReplTable) ;
			$myReplTableOut	=	array_values( $myReplTable) ;
			$myReplTable	=	$this->doc->getReplTable() ;
			$texOFile	=	str_replace( $myReplTableIn, $myReplTableOut, $texIFile) ;
			$texIFile	=	file_put_contents( $tex_name, $texOFile) ;
		} else {
			echo "Problem ..." ;
		}

		// remove all temporary files

//		system( "rm " . $tex_name) ;

		return $tex_name ;

	}

}

?>
