<?php

require_once( "Receiver.php" );
require_once( "iPrintable.inc.php") ;

class ReceiverLabel extends Receiver implements iPrintable {

	function	getPDF( $_db, $_cnt, $_size, $_finalMode) {

		// now run the pdf-latex compiler

		$tex_name	=	$this->getTeX( $_db, $_cnt, $_size, $_finalMode) ;
		$dvi_name	=	str_replace( "tex", "dvi", $tex_name) ;
		$ps_name	=	str_replace( "tex", "ps", $tex_name) ;
		$aux_name	=	str_replace( "tex", "aux", $tex_name) ;
		$log_name	=	str_replace( "tex", "log", $tex_name) ;
		$pdf_name	=	str_replace( "tex", "pdf", $tex_name) ;

		switch ( $_size) {
		case	11	:		// Grosser Aufkleber, i.e. 100 x 50
			$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvips -D 203 -T 100mm,50mm " . $dvi_name . " ; ps2pdf " . $ps_name . " >/dev/null" ;
			break ;
		case	12	:		// Grosser Aufkleber, i.e. 100 x 50
			$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvips -D 203 -T 75mm,25mm " . $dvi_name . " ; ps2pdf " . $ps_name . " >/dev/null" ;
			break ;
		case	13	:		// Grosser Aufkleber, i.e. 102x 152
			$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvips -D 203 -T 152mm,102mm " . $dvi_name . " ; ps2pdf " . $ps_name . " >/dev/null" ;
			break ;
		case	21	:		// Grosser Aufkleber, i.e. 100 x 50
			$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvips -D 203 -T 100mm,60mm " . $dvi_name . " ; ps2pdf " . $ps_name . " >/dev/null" ;
			break ;
		}
		system( $command) ;

		// remove all temporary files

//		system( "rm " . $tex_name) ;

		return $pdf_name ;
	}

	function	getTeX( $_db, $_cnt, $_size, $_finalMode) {

		require_once( "textools.inc.php" );

		global	$texPath ;

		//
		// prepare file names for the different temporary files
		//
		//
		// sequence of file translations:
		//

		$size	=	$_size ;

		$f_name	=	tempnam( "/tmp", "labels-") ;
		$tex_name	=	$f_name . ".tex" ;
		switch ( $size) {
		case	11	:		// Grosser Aufkleber, i.e. 100 x 50
			$tex_Iname	=	$texPath . "labelAdr.tex" ;
			break ;
		case	12	:		// Kleiner Aufkleber, i.e. 75 x 25
			$tex_Iname	=	$texPath . "labelAdrSmall.tex" ;
			break ;
		case	13	:		// Kleiner Aufkleber, i.e. 75 x 25
			$tex_Iname	=	$texPath . "label102x152.tex" ;
			break ;
		}

		//

		$texOFile	=	fopen( $tex_name, "w+") ;
		if ( $texOFile) {
			$texIFile	=	fopen( $tex_Iname, "r") ;
			if ( $texIFile) {
				while ( !feof( $texIFile)) {
					$lineBuffer	=	fgets( $texIFile, 1024) ;
					if ( strncmp( $lineBuffer, "#HEAD1", 6) == 0) {
						fputs( $texOFile, sprintf( "{\head Adressaufkleber Kunde}\\\\")) ;
					} else if ( strncmp( $lineBuffer, "#LOOP", 5) == 0) {
						$this->genLabelSub( $_db, $texOFile, $_cnt, $size) ;
					} else {
						fputs( $texOFile, $lineBuffer) ;
					}
				}
				fclose( $texIFile) ;
			} else {
				printf( "Kann texIFile [%d][%s] nicht oeffnen \n", $size, $texIFile) ;
				die() ;
			}
			fclose( $texOFile) ;
		} else {
			printf( "Kann texOFile [%d][%s] nicht oeffnen \n", $size, $texIFile) ;
			die() ;
		}

		return $tex_name ;

	}

	function	genLabelSub( $_db, $_file, $_cnt, $_size) {

		$senderAdrLine	=	"MODIS GmbH, Dieselstr. 8, D-50374 Erftstadt" ;

		switch ( $_size) {
		case	11	:		// Grosser Aufkleber, i.e. 100 x 50
			for ( $i=1 ; $i <= $_cnt ; $i++) {
				fputs( $_file, sprintf( "\\newpage\n")) ;
				fputs( $_file, sprintf( "\\senderFont\n")) ;
				fputs( $_file, sprintf( "\\setlength{\\baselineskip}{1.0ex}\n")) ;
				fputs( $_file, sprintf( "%s\\\\\n", $senderAdrLine)) ;
				fputs( $_file, sprintf( "\\rule{9cm}{0.3mm}\\\\\n")) ;
				fputs( $_file, sprintf( "\\mainFont\n")) ;
				fputs( $_file, sprintf( "\\setlength{\\baselineskip}{2.3ex}\n")) ;
				fputs( $_file, sprintf( "%s\\\\\n", mTS( $this->FirmaName1))) ;
				fputs( $_file, sprintf( "%s\\\\\n", mTS( $this->FirmaName2))) ;
				if ( strlen( $this->Anrede) > 0) {
					switch ( $this->Anrede) {
					case	"Herr"	:
						$attnLine	=	"z.Hd. Herrn" ;
						break ;
					case	"Frau"	:
						$attnLine	=	"z.Hd. Frau" ;
						break ;
					case	"Mr"	:
						$attnLine	=	"Attn. Mr" ;
						break ;
					}
					if ( strlen( $this->Titel) > 0) {
						$attnLine	.=	" " . $this->Titel ;
					}
					if ( strlen( $this->Vorname) > 0) {
						$attnLine	.=	" " . $this->Vorname ;
					}
					if ( strlen( $this->Name) > 0) {
						$attnLine	.=	" " . $this->Name ;
					}
				} else {
					$attnLine	=	"" ;
				}
				fputs( $_file, sprintf( "%s\\\\\n", mTS( $attnLine))) ;
				fputs( $_file, sprintf( "%s %s\\\\\n", mTS( $this->Strasse), mTS( $this->Hausnummer))) ;
				fputs( $_file, sprintf( "%s %s\\\\\n", mTS( $this->PLZ), mTS( $this->Ort))) ;
				fputs( $_file, sprintf( "%s\n", mTS( $this->Land))) ;
			}
			break ;
		case	12	:		// Grosser Aufkleber, i.e. 100 x 50
			for ( $i=1 ; $i <= $_cnt ; $i++) {
				fputs( $_file, sprintf( "\\newpage\n")) ;
				fputs( $_file, sprintf( "%s\\\\\n", mTS( $this->FirmaName1))) ;
				fputs( $_file, sprintf( "%s\\\\\n", mTS( $this->FirmaName2))) ;
				fputs( $_file, sprintf( "%s %s\\\\\n", mTS( $this->Strasse), mTS( $this->Hausnummer))) ;
				fputs( $_file, sprintf( "%s %s\\\\\n", mTS( $this->PLZ), mTS( $this->Ort))) ;
			}
			break ;
		case	13	:		// Grosser Aufkleber, i.e. 100 x 50
			for ( $i=1 ; $i <= $_cnt ; $i++) {
				fputs( $_file, sprintf( "\\newpage\n")) ;
				fputs( $_file, sprintf( "\\senderFont\n")) ;
				fputs( $_file, sprintf( "\\setlength{\\baselineskip}{1.0ex}\n")) ;
				fputs( $_file, sprintf( "%s\\\\\n", $senderAdrLine)) ;
				fputs( $_file, sprintf( "\\rule{9cm}{0.3mm}\\\\\n")) ;
				fputs( $_file, sprintf( "\\mainFont\n")) ;
				fputs( $_file, sprintf( "\\setlength{\\baselineskip}{2.3ex}\n")) ;
				fputs( $_file, sprintf( "%s\\\\\n", mTS( $this->FirmaName1))) ;
				fputs( $_file, sprintf( "%s\\\\\n", mTS( $this->FirmaName2))) ;
				if ( strlen( $this->Anrede) > 0) {
					switch ( $this->Anrede) {
					case	"Herr"	:
						$attnLine	=	"z.Hd. Herrn" ;
						break ;
					case	"Frau"	:
						$attnLine	=	"z.Hd. Frau" ;
						break ;
					case	"Mr"	:
						$attnLine	=	"Attn. Mr" ;
						break ;
					}
					if ( strlen( $this->Titel) > 0) {
						$attnLine	.=	" " . $this->Titel ;
					}
					if ( strlen( $this->Vorname) > 0) {
						$attnLine	.=	" " . $this->Vorname ;
					}
					if ( strlen( $this->Name) > 0) {
						$attnLine	.=	" " . $this->Name ;
					}
				} else {
					$attnLine	=	"" ;
				}
				fputs( $_file, sprintf( "%s\\\\\n", mTS( $attnLine))) ;
				fputs( $_file, sprintf( "%s %s\\\\\n", mTS( $this->Strasse), mTS( $this->Hausnummer))) ;
				fputs( $_file, sprintf( "%s %s\\\\\n", mTS( $this->PLZ), mTS( $this->Ort))) ;
				fputs( $_file, sprintf( "%s\n", mTS( $this->Land))) ;
			}
			break ;
		case	21	:		// Grosser Aufkleber, i.e. 100 x 60[62.5]
			break ;
		}
	}

}
?>
