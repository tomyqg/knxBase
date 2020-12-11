<?php

require_once( "global.inc.php") ;
require_once( "option.inc.php") ;
require_once( "iPrintable.inc.php") ;
require_once( "iListDoc.inc.php") ;
require_once( "Artikel.inc.php") ;

class	PreislisteDoc	implements iListDoc, iPrintable {

	var	$LiefNrFilter ;
	var	$ArtikelNrFilter ;
	var	$ArtikelText ;

	function	__construct( $_db) {

		$this->LiefNrFilter	=	"%" ;
		$this->ArtikelNrFilter	=	"%" ;
		$this->ArtikelText	=	"Alle" ;

		$this->setTexte() ;

	}

	function	setLiefNrFilter( $_filter) {
		$this->LiefNrFilter	=	$_filter ;
	}

	function	setArtikelNrFilter( $_filter) {
		$this->ArtikelNrFilter	=	$_filter ;
	}

	function	setArtikelText( $_text) {
		$this->ArtikelText	=	$_text ;
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
		$tex_Iname	=	$texPath . "listeBasisReport.tex" ;

		//

		$texIFile	=	file_get_contents( $tex_Iname) ;
		if ( $texIFile) {
			$myReplTable	=	$this->getReplTable( $_db) ;
			$myReplTableIn	=	array_keys( $myReplTable) ;
			$myReplTableOut	=	array_values( $myReplTable) ;
			$myReplTable	=	$this->getReplTable( $_db) ;
			$texOFile	=	str_replace( $myReplTableIn, $myReplTableOut, $texIFile) ;
			$texIFile	=	file_put_contents( $tex_name, $texOFile) ;
		} else {
			echo "Problem ..." ;
		}

		// remove all temporary files

//		system( "rm " . $tex_name) ;

		return $tex_name ;

	}

	function	getReplTable( $_db) {

		$replTableOut = array() ;
		
		$replTableIn["HEAD1"]		=	"{\head Preisliste, Stand: " . date("d.m.Y") . "}\\\\" ;
		$replTableIn["LOOP1"]		=	$this->getMainData( $_db) ;

		$replTableIn["LOOP2"]		=	$this->getPosData( $_db) ;
		$replTableIn["PREFIX"]		=	"$ $" ;
		$replTableIn["ADDITION"]	=	"Addition" ;

		return $replTableIn ;

	}

	function	getMainData( $_db) {

		$buf	=	"" ;

		$buf	.=	"Irrtum vorbehalten.\\\\\n" ;
		$buf	.=	"\\\\\n" ;
		$buf	.=	"Alle Preise in Euro zzgl. gesetzlicher Mwst.\\\\\n" ;
		$buf	.=	"\\\\\n" ;
		$buf	.=	"\\begin{tabular}{ll}\n" ;
		$buf	.=	"Artikel: & " . $this->ArtikelText . "\\\\\n" ;
		$buf	.=	"\\end{tabular}\n" ;
		$buf	.=	"$ $\\\\\n" ;
		$buf	.=	"\\vspace{10mm}\n" ;

		return $buf ;
	}

	function	getPosData( $_db) {

		$myArtikel	=	new Artikel() ;

		$buf	=	"" ;

		$query	=	"SELECT A.ArtikelNr, A.ArtikelBez1, A.ArtikelBez2, A.MengenText, VKP.Preis, VKP.MengeProVPE " ;
		$query	.=	"FROM Artikel AS A " ;
		$query	.=	"LEFT JOIN VKPreis VKP on VKP.ArtikelNr = A.ArtikelNr " ;
		$query	.=	"WHERE A.ArtikelNr like '" . $this->ArtikelNrFilter . "' " ;
		$query	.=	"ORDER by A.ArtikelNr " ;

		$sqlResult =       mysql_query( $query, $_db) ;

		if ( $sqlResult) {
			$numrows        =       mysql_affected_rows( $_db) ;
			if ( $numrows == 0) {
				printf( "Keine Positionen ... <br/> \n") ;
			} else {

				$sum	=	0.0 ;
				$buf	.=	"\begin{longtable}{|l|p{100mm}|r|}\n" ;
				$buf	.=	"\\hline\n" ;
				$buf	.=	"\setlength{\\tabcolsep}{10mm}\n" ;
//				$buf	.=	"{\\parbox{20mm}{\\bfseries Artikel Nr..} \\hspace{2mm}} & {\\parbox{100mm}{\\bfseries Beschreibung} \\hspace{5mm}} & \\parbox{50mm}{Preis} \\\\hline\n" ;
				$buf	.=	"\\bfseries Artikel Nr.. \\hspace{5mm} & \\bfseries Beschreibung \\hspace{10mm} & \\hspace{20mm} Preis \\\\ \hline\n" ;
//				$buf	.=	"$ $\\vspace{5mm}\n" ;
				while ($row = mysql_fetch_assoc( $sqlResult)) {

					$myArtikel->ArtikelNr	=	$row[ 'ArtikelNr'] ;
					$myArtikel->fetchFromDb( $_db) ;

					$buf	.=	sprintf( "%s & ", $myArtikel->ArtikelNr) .
							sprintf( "%s & ", $myArtikel->getFullText( intval( $row[ 'MengeProVPE']))) .
							sprintf( "%s ", $row[ 'Preis'] ) .
							"\\\\ \\hline\n" ;
//					$buf	.=	"$ $\\vspace{5mm}\n" ;
				}

//				$buf	.=	"\\\\\n" ;
//				$buf	.=	"$ $\\vspace{30mm}\n" ;

				$buf	.=	 "\end{longtable}\n" ;
			}
		} else {
			$buf	.=	"FEHLER BEI DATENBANKZUGRIFF" ;
			$buf	.=	$query ;
		}

		return $buf ;
	}

	private	function	setTexte() {
	}

}

?>
