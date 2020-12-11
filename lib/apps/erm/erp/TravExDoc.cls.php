<?php

require_once( "global.inc.php") ;
require_once( "option.inc.php") ;
require_once( "iListDoc.inc.php") ;
require_once( "iPrintable.inc.php") ;
require_once( "iArchivable.itf.php") ;
require_once( "TravEx.php" );
require_once( "User.php") ;

/**
 *
 */
class	TravExDoc	extends TravEx	implements iListDoc, iPrintable, iArchivable {

	/**
	 *
	 */
	function	__construct( $_travExNr) {
		TravEx::__construct( $_travExNr) ;
		$this->setTexte() ;

	}

	/**
	 *
	 */
	function	archive() {

		global	$archivPath ;

		// create the travex-original (Rechnungsoriginal)

		$pdfTargetName	=	$archivPath . "Reisekosten/" . $this->TravExNr . ".pdf" ;
		$pdfName	=	$this->getPdf( 0, "A4", true) ;
		$systemCmd	=	"mv " . $pdfName . " " . $pdfTargetName . " " ;
		system( $systemCmd) ;

		$pdfs[]	=	$pdfTargetName ;

	}

	/**
	 *
	 */
	function	printIt( $_prn) {

		global	$archivPath ;

		if ( strcmp( $_prn, "-") != 0) {
			$systemCmd	=	"lpr -P " . $_prn . " " . $archivPath . "Reisekosten/" . $this->TravExNr . ".pdf " ;
			system( $systemCmd) ;
		}
	}

	/**
	 *
	 */
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

	/**
	 *
	 */
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

		$f_name	=	tempnam( "/tmp", "liste-") ;
		$tex_name	=	$f_name . ".tex" ;
		$tex_Iname	=	$texPath . "listeBasisV3.tex" ;

		//

		$texIFile	=	file_get_contents( $tex_Iname) ;
		if ( $texIFile) {
			$myReplTable	=	$this->getReplTable() ;
			$myReplTableIn	=	array_keys( $myReplTable) ;
			$myReplTableOut	=	array_values( $myReplTable) ;
			$texOFile	=	str_replace( $myReplTableIn, $myReplTableOut, $texIFile) ;
			$texIFile	=	file_put_contents( $tex_name, $texOFile) ;
		} else {
			echo "Problem ..." ;
		}

		// remove all temporary files

//		system( "rm " . $tex_name) ;

		return $tex_name ;

	}

	/**
	 *
	 */
	function	getReplTable() {

		$replTableOut = array() ;
		
		$replTableIn["HEAD1"]		=	"{\head Reisekostenabrechnung Nr. " . $this->TravExNr . "}\\\\" ;
		$replTableIn["LOOP1"]		=	$this->getMainData() ;

		$replTableIn["LOOP2"]		=	$this->getPosData() ;
		$replTableIn["PREFIX"]		=	"$ $" ;
		$replTableIn["ADDITION"]	=	"Addition" ;

		return $replTableIn ;

	}

	/**
	 *
	 */
	function	getMainData() {

		$myUser	=	new User() ;
		$myUser->UserId	=	$this->UserId ;
		$myUser->fetchFromDb() ;
		if ( $myUser->_valid == 1) {
			$userName	=	$myUser->FirstName . " " . $myUser->LastName ;
		} else {
			$userName	=	$this->UserId ;
		}

		$buf	=	"" ;

		$buf	.=	"\\begin{tabular}{ll}\n" ;
		$buf	.=	"Reisekostenabrechnung Nr.: & " . $this->TravExNr ."\\\\\n" ;
		$buf	.=	"User Id.: & " . $userName ."\\\\\n" ;
		$buf	.=	"Datum: & " . convDate( $this->Date) . "\\\\\n" ;
		$buf	.=	"Reisebeginn: & " . convDate( $this->StartDate) . " " . $this->StartTime . "\\\\\n" ;
		$buf	.=	"Reiseende: & " . convDate( $this->EndDate) . " " . $this->EndTime . "\\\\\n" ;
		$buf	.=	"Kurzbeschreibung: & " . $this->Slogan . "\\\\\n" ;
		$buf	.=	"\\end{tabular}\n" ;
		$buf	.=	"$ $\\\\\n" ;
		$buf	.=	"\\vspace{10mm}\n" ;
		$buf	.=	xmlToTeX( $this->Description) ;
		$buf	.=	"\\vspace{10mm}\n" ;

		return $buf ;
	}

	/**
	 *
	 */
	function	getPosData() {

		$buf	=	"" ;

		$myTravExPos	=	new TravExPos() ;

		$query	=	"SELECT * " ;
		$query	.=	"FROM TravExPos AS TEP " ;
		$query	.=	"LEFT JOIN TravExExpType TEET on TEET.ExpType = TEP.ExpType " ;
		$query	.=	"WHERE TravExNr = \"" . $this->TravExNr . "\" " ;
		$query	.=	"ORDER by PosNr " ;

echo $query . "<br/>" ;

		$sqlResult =       FDb::query( $query) ;

		if ( $sqlResult) {
			$numrows        =       FDb::rowCount() ;
			if ( $numrows == 0) {
				printf( "Keine Positionen ... <br/> \n") ;
			} else {
				$sum	=	0.0 ;
				$buf	.=	"\begin{longtable}{rlllrrr}\n" ;
				$buf	.=	"\setlength{\\tabcolsep}{3mm}\n" ;
				$buf	.=	"{\\parbox{15mm}{\\bfseries Pos.} \\hspace{5mm}} & \\parbox{30mm}{\\bfseries Datum} & \\parbox{50mm}{Ausgabe} & \\parbox{15mm}{Land} & \\parbox{15mm}{Menge} & \\parbox{25mm}{Preis} & \\parbox{25mm}{Gesamt} \\\\[1.0ex]\n" ;
				$myPosNr	=	1 ;
				while ($row = mysql_fetch_assoc( $sqlResult)) {
					$myTravExPos->assignFromRow( $row) ;
					$buf	.=	sprintf( "\\parbox{15mm}{%3d} & ", $myPosNr++) ;
					$buf	.=	sprintf( "\\parbox{30mm}{%s} & ", convDate( $myTravExPos->Date)) ;
					$buf	.=	sprintf( "\\parbox{50mm}{%s} & ", $row[ 'Title']) ;
					$buf	.=	sprintf( "\\parbox{15mm}{%s} & ", $myTravExPos->CountryCode) ;
					$buf	.=	sprintf( "\\parbox{15mm}{%5d %s} & ", $myTravExPos->Qty, $row[ 'QtyText']) ;
					$buf	.=	sprintf( "\\parbox{25mm}{%10.2f / %s} &", $myTravExPos->CostSingle, $row[ 'QtyText']) ;
					$buf	.=	sprintf( "\\parbox{25mm}{%10.2f}", $myTravExPos->CostTotal) ;
					$buf	.=	"\\\\\n" ;

					$sum	+=	$myTravExPos->CostTotal ;
				}

				$buf	.=	sprintf( " & ") ;
				$buf	.=	sprintf( " & ") ;
				$buf	.=	sprintf( "\\parbox{50mm}{%s} & ", "Gesamtausgaben:") ;
				$buf	.=	sprintf( " & ") ;
				$buf	.=	sprintf( " & ") ;
				$buf	.=	sprintf( " &") ;
				$buf	.=	sprintf( "\\parbox{25mm}{%10.2f}", $sum) ;
				$buf	.=	"\\\\\n" ;
				$buf	.=	"$ $\\vspace{30mm}\n" ;

				$buf	.=	 "\end{longtable}\n" ;
			}
		} else {
			$buf	.=	"FEHLER BEI DATENBANKZUGRIFF" ;
			$buf	.=	$query ;
		}

		return $buf ;
	}

	/**
	 *
	 */
	private	function	setTexte() {
	}

}

?>
