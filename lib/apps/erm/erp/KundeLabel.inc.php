<?php

require_once( "conf_pathes.inc.php") ;

require_once( "Kunde.inc.php") ;

class KundeLabel extends Kunde {

	var	$myKunde ;

	function	KundeLabel( $_db, $_kundeNr) {

		$this->KundeNr	=	$_kundeNr ;
		$this->fetchFromDb( $_db) ;

		if ( $this->_valid != 1) {
			echo "Kunde nicht gueltig ... <br/>\n" ;
die() ;
		}
	}

	function	genLabel( $_db, $_cnt, $_size, $_kundeKontaktId) {

		require_once( "textools.inc.php" );

		global	$texPath ;

		//

		$myKundeKontakt	=	new KundeKontakt() ;
		$myKundeKontakt->Id	=	$_kundeKontaktId ;
		$myKundeKontakt->fetchFromDbById( $_db) ;

		//
		// prepare file names for the different temporary files
		//
		//
		// sequence of file translations:
		//

		$size	=	$_size ;

		$f_name	=	tempnam( "/tmp", "labels-") ;
		$tex_name	=	$f_name . ".tex" ;
		$dvi_name	=	$f_name . ".dvi" ;
		$ps_name	=	$f_name . ".ps" ;
		$aux_name	=	$f_name . ".aux" ;
		$log_name	=	$f_name . ".log" ;
		$pdf_name	=	$f_name . ".pdf" ;
		switch ( $size) {
		case	11	:		// Grosser Aufkleber, i.e. 100 x 50
			$tex_Iname	=	$texPath . "labelAdr.tex" ;
			break ;
		case	12	:		// Kleiner Aufkleber, i.e. 75 x 25
			$tex_Iname	=	$texPath . "labelAdrSmall.tex" ;
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
						$this->genLabelSub( $_db, $texOFile, $_cnt, $size, $myKundeKontakt) ;
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
		//
		// now run the pdf-latex compiler
		//
		switch ( $size) {
		case	11	:		// Grosser Aufkleber, i.e. 100 x 50
			$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvips -D 203 -T 100mm,50mm " . $dvi_name . " ; ps2pdf " . $ps_name . " >/dev/null" ;
			break ;
		case	12	:		// Grosser Aufkleber, i.e. 100 x 50
			$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvips -D 203 -T 75mm,25mm " . $dvi_name . " ; ps2pdf " . $ps_name . " >/dev/null" ;
			break ;
		case	21	:		// Grosser Aufkleber, i.e. 100 x 50
			$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvips -D 203 -T 100mm,60mm " . $dvi_name . " ; ps2pdf " . $ps_name . " >/dev/null" ;
			break ;
		}
		system( $command) ;
		//
		// remove all temporary files
		//
//		system( "rm " . $tex_name) ;
		system( "rm " . $dvi_name) ;
		system( "rm " . $ps_name) ;
		system( "rm " . $log_name) ;
		system( "rm " . $aux_name) ;
	//	system( "rm " . $pdf_name) ;
		system( "rm " . $f_name) ;

		return $pdf_name ;
	}

	function	genLabelSub( $_db, $_file, $_cnt, $_size, $_kundeKontakt) {

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
				if ( strlen( $_kundeKontakt->Anrede) > 0) {
					switch ( $_kundeKontakt->Anrede) {
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
					if ( strlen( $_kundeKontakt->Titel) > 0) {
						$attnLine	.=	" " . $_kundeKontakt->Titel ;
					}
					if ( strlen( $_kundeKontakt->Vorname) > 0) {
						$attnLine	.=	" " . $_kundeKontakt->Vorname ;
					}
					if ( strlen( $_kundeKontakt->Name) > 0) {
						$attnLine	.=	" " . $_kundeKontakt->Name ;
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
		case	21	:		// Grosser Aufkleber, i.e. 100 x 60[62.5]
			break ;
		}
	}

}
