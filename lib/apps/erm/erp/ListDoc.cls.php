<?php

require_once( "global.inc.php") ;
require_once( "iPrintable.inc.php") ;
require_once( "TravExDoc.cls.php" );
require_once( "PreislisteDoc.cls.php" );

class	ListDoc	implements iPrintable {

	public	$listDoc ;

	function	__construct( $_db) {
	}

	function	setFromTravEx( $_db, $_travExNr) {
		$this->listDoc	=	new TravExDoc( $_db, $_travExNr) ;
	}

	function	setPreisliste( $_db, $_liefF, $_artikelF) {
		$this->listDoc	=	new PreislisteDoc( $_db, $_liefF, $_artikelF) ;
	}

	function	getPDF( $_db, $_cnt, $_size, $_finalMode) {

		global	$texPath ;

		//
		// prepare file names for the different temporary files
		//
		//
		// sequence of file translations:
		//

		$tex_name	=	$this->getTeX( $_db, $_cnt, $_size, $_finalMode) ;
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

	function	getTeX( $_db, $_cnt, $_size, $_finalMode) {

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

		$f_name	=	tempnam( "/tmp", "liste-") ;
		$tex_name	=	$f_name . ".tex" ;
		$tex_Iname	=	$texPath . "listeBasisV3.tex" ;

		//

		$texIFile	=	file_get_contents( $tex_Iname) ;
		if ( $texIFile) {
			$myReplTable	=	$this->listDoc->getReplTable( $_db) ;
			$myReplTableIn	=	array_keys( $myReplTable) ;
			$myReplTableOut	=	array_values( $myReplTable) ;
			$myReplTable	=	$this->listDoc->getReplTable( $_db) ;
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
