<?php

require_once( "textools.inc.php" );

$lastPosNr	=	"" ;
$subPosCtr	=	0 ;

function	genCuOffrDocV3( $_database, $_cuOffr, $signature, $_finalMode=false) {

	global	$texPath ;
	global	$modusSkonto ;

	//

	$myKunde	=	new Kunde() ;
	$myKundeKontakt	=	new KundeKontakt() ;

	$lang	=       "" ;
	$country	=       "" ;
	$anrede	=       "" ;
	$prefix	=	"" ;
	$postfix	=	"" ;

	$goOn	=	true ;

	//

	if ( ! getCuOffrKunde( $_database, $_cuOffr, $myKunde, $myKundeKontakt)) {
		myDebug(  0, "%s<br />\n", sprintf( "PANIC..: die Kundendaten sind nicht lesbar")) ;
		die() ;
	}

	//

	if ( $goOn) {

		//
		// prepare file names for the different temporary files
		//
		//
		// sequence of file translations:
		//

		$f_name	=	tempnam( "/tmp", "letter-") ;
		$tex_name	=	$f_name . ".tex" ;
		$dvi_name	=	$f_name . ".dvi" ;
		$aux_name	=	$f_name . ".aux" ;
		$log_name	=	$f_name . ".log" ;
		$pdf_name	=	$f_name . ".pdf" ;
		$tex_Iname	=	$texPath . "briefBasisV3.tex" ;

		// Den erforderlichen Adressblock holen, sowie die erforderliche Sprache, das Zielland und die persoenliche Anrede

		$rcvrBlock	=	getRcvrBlockKd( $myKunde, $myKundeKontakt, $lang, $country, $anrede) ;

		// Den zusaetzlichen Prefix generieren

		if ( strlen( $_cuOffr->Prefix) > 12) {
			$prefix	=	xmlToTex( $_cuOffr->Prefix) ;
		}

		// Den zusaetzlichen Postfix generieren

		$postfix	=	xmlToTex( "<div>Zahlungsbedingungen: " . $modusSkonto[ $_cuOffr->ModusSkonto] . "</div>") ;
		if ( strlen( $_cuOffr->Postfix) > 12) {
			$postfix	.=	xmlToTex( $_cuOffr->Postfix) ;
		}
	}

	//

	$texOFile	=	fopen( $tex_name, "w+") ;
	$texIFile	=	fopen( $tex_Iname, "r") ;
	$lastCuDlvrNo	=	"" ;
	if ( $texOFile && $texIFile) {
		while ( !feof( $texIFile)) {
			$lineBuffer	=	fgets( $texIFile, 1024) ;
			$buffer	=	substr( $lineBuffer, 0, strlen( $lineBuffer)-2) ;
			if ( strcmp( $buffer, "DRAFTCOPYNAME") == 0) {
				fwrite( $texOFile, "ENTWURF\n") ;
			} else if ( strcmp( $buffer, "DRAFTMODE") == 0) {
				if ( $_finalMode) {
					fwrite( $texOFile, "final\n") ;
				} else {
					fwrite( $texOFile, "draft\n") ;
				}
			} else if ( strcmp( $buffer, "NAME") == 0) {
				fwrite( $texOFile, $signature) ;
			} else if ( strcmp( $buffer, "SIGNATURE") == 0) {
				fwrite( $texOFile, $signature) ;
			} else if ( strcmp( $buffer, "DATE") == 0) {
				fwrite( $texOFile, "Angebot Nr.: ".$_cuOffr->CuOffrNo." / ".convDate( $_cuOffr->Datum)."\n") ;
			} else if ( strcmp( $buffer, "DOCTYPE") == 0) {
				fwrite( $texOFile, "Angebot\n") ;
			} else if ( strcmp( $buffer, "SUBJECT") == 0) {
				fwrite( $texOFile, "Angebot\n") ;
			} else if ( strcmp( $buffer, "TOPTABLE") == 0) {
				fwrite( $texOFile, sprintf( "\begin{tabular}{lr}\n")) ;
				fwrite( $texOFile, sprintf( "Angebot: & %s\\\\\n", $_cuOffr->CuOffrNo)) ;
				fwrite( $texOFile, sprintf( "Datum: & %s\\\\\n", convDate( $_cuOffr->Datum))) ;
				fwrite( $texOFile, sprintf( "Kunden-Nr.: & %s/%03d\\\\\n", $_cuOffr->KundeNr, $_cuOffr->KundeKontaktNr)) ;
				fwrite( $texOFile, sprintf( "\\\\[2mm]\n")) ;
				fwrite( $texOFile, sprintf( "Ihre Anfrage: & %s\\\\\n", $_cuOffr->KdRefNr)) ;
				fwrite( $texOFile, sprintf( "vom: & %s\\\\\n", convDate( $_cuOffr->KdRefDatum))) ;
				fwrite( $texOFile, sprintf( "\end{tabular}\n")) ;
			} else if ( strncmp( $buffer, "ADRESSE", 7) == 0) {
				fwrite( $texOFile, $rcvrBlock) ;
			} else if ( strcmp( $buffer, "OPENING") == 0) {
				fwrite( $texOFile, $anrede) ;
			} else if ( strcmp( $buffer, "PREFIX") == 0) {
				fwrite( $texOFile, $prefix) ;
			} else if ( strcmp( $buffer, "EINLEITUNG") == 0) {
			} else if ( strcmp( $buffer, "LOOPLISTE") == 0) {
				writeCuOffrArtikel( $_database, $_cuOffr, $texOFile) ;
			} else if ( strcmp( $buffer, "ADDITION") == 0) {
				fwrite( $texOFile, $postfix) ;
			} else if ( strcmp( $buffer, "ADRCODE") == 0) {
			} else if ( strcmp( $buffer, "AUSLEITUNG") == 0) {
				fwrite( $texOFile, "Mit freundlichen Gr\\\"{u}{\\ss}en\n") ;
			} else {
				fputs( $texOFile, $lineBuffer) ;
			}
		}
		fclose( $texIFile) ;
		fclose( $texOFile) ;
	} else {
		printf( "Kann tex files nicht oeffnen \n") ;
		die() ;
	}

	// now run the pdf-latex compiler

	$command	=	"cd /tmp; latex " . $tex_name . " >/dev/null ; dvipdf " . $dvi_name . " >/dev/null" ;
	system( $command) ;

	// remove all temporary files

//	system( "rm " . $tex_name) ;
	system( "rm " . $dvi_name) ;
	system( "rm " . $log_name) ;
	system( "rm " . $aux_name) ;
//	system( "rm " . $pdf_name) ;
	system( "rm " . $f_name) ;

	return $pdf_name ;
}

//
// writeCuOffrArtikel
//
// Gibt die Liste aller Artikel in diesem Auftrag aus
//

function	writeCuOffrArtikel( $_db, $_cuOffr, $_oFile) {

	$result	=	false ;

	$myArtikel	=	new Artikel() ;
	$myCuOffrItem	=	new CuOffrItem() ;

	$query	=	"select * " ;
	$query	.=	"from CuOffrItem " ;
	$query	.=	"where CuOffrNo = \"" . $_cuOffr->CuOffrNo . "\" " ;
	$query	.=	"order by PosNr " ;

	$sqlResult =       mysql_query( $query, $_db) ;

	if ( !$sqlResult) {
		fwrite( $_oFile, "FEHLER BEI DATENBANKZUGRIFF") ;
		fwrite( $_oFile, $query) ;
	} else {
		$numrows        =       mysql_affected_rows( $_db) ;
		if ( $numrows == 0) {
			printf( "Keine Auftragspositionen ... <br/> \n") ;
		} else {

			fwrite( $_oFile, "\begin{longtable}{rllrrrc}\n") ;
			fwrite( $_oFile, "Pos. & \\parbox{20mm}{Artikel Nr.} & \\parbox{65mm}{Bezeichnung} & Menge & Einzelpreis & Preis & Mwst.\\\\[-0.0ex]\n") ;

			$myGesamtPreis['0']	=	0.0 ;
			$myGesamtPreis['A']	=	0.0 ;
			$myGesamtPreis['B']	=	0.0 ;
			$myGesamtPreis['C']	=	0.0 ;

			while ($row = mysql_fetch_assoc( $sqlResult)) {

				$myCuOffrItem->assignFromRow( $row) ;

				$myArtikel->ArtikelNr	=	$myCuOffrItem->ArtikelNr ;
				$myArtikel->fetchFromDb( $_db) ;

				$myGesamtPreis[$myArtikel->MwstSatz]	+=	writeLoopLineWP( $_oFile, $myCuOffrItem, $myArtikel) ;
				fwrite( $_oFile, " \\\\[-1ex] \n") ;

			}

			fwrite( $_oFile, " \\\\[-2.0ex]\n") ;
			$myGesamtPreis['A']	-=	writeRabattKd( $_oFile, $myGesamtPreis['A'], $_cuOffr->Rabatt) ;
			$myGesamtPreis['0']	=	$myGesamtPreis['A'] + $myGesamtPreis['B'] + $myGesamtPreis['C'] ;
			$myGesamtPreis['A']	+=	writeVersandKostenKd( $_oFile, $myGesamtPreis['0'], $_cuOffr->VersPausch) ;
			$myGesamtPreis['0']	=	$myGesamtPreis['A'] + $myGesamtPreis['B'] + $myGesamtPreis['C'] ;
			fwrite( $_oFile, " \\hline \n") ;
			fwrite( $_oFile, " \\\\[-1.5ex] \n") ;
							writeNettoKd( $_oFile, $myGesamtPreis['0']) ;
			$myGesamtPreis['0']	+=	writeMwstKd( $_oFile, $myGesamtPreis['A'], 'A') ;
			$myGesamtPreis['0']	+=	writeMwstKd( $_oFile, $myGesamtPreis['B'], 'B') ;
			$myGesamtPreis['0']	+=	writeMwstKd( $_oFile, $myGesamtPreis['C'], 'C') ;
							writeBruttoKd( $_oFile, $myGesamtPreis['0']) ;
			fwrite( $_oFile, "\end{longtable}\n") ;

			$result	=	true ;
		}
	}

	return $result ;

}

function	writeLoopLineWP( $_oFile, $_cuOffrPosten, $_artikel) {

	global	$lastPosNr ;
	global	$subPosCtr ;

	//

	$myPosPreis	=	0.0 ;

	$myPosPreis	=	$_cuOffrPosten->Menge * $_cuOffrPosten->Preis ;

	//

	$addPos	=	"" ;

	if ( floatval( $_cuOffrPosten->RefPreis) != 0.00 && $_cuOffrPosten->Menge != 0) {
		$myPosRabProz	=	(0.05 - 0.05 / sqrt( $_cuOffrPosten->Menge)) * 100.0 ;
		$myPosRabProz	=	((floatval( $_cuOffrPosten->RefPreis) - floatval( $_cuOffrPosten->Preis)) * 100.0) / floatval( $_cuOffrPosten->RefPreis) ;
		$myPosRabCurr	=	floatval( $_cuOffrPosten->RefPreis) - floatval( $_cuOffrPosten->Preis) ;
	} else {
		$myPosRabProz	=	0.0 ;
		$myPosRabProz	=	0.0 ;
		$myPosRabCurr	=	0.0 ;
	}

	if ( floatval( $_cuOffrPosten->RefPreis) != 0.00) {
		$preisText	=	number_format( $_cuOffrPosten->RefPreis, 2, ",", "") ;
		$sumPreisText	=	number_format( $myPosPreis, 2, ",", "") ;
	} else {
		$preisText	=	"n.a." ;
		$sumPreisText	=	"n.a." ;
	}

	if ( strcmp( $_artikel->ArtikelBez1, $_artikel->ArtikelBez2) != 0 && strcmp( $_artikel->ArtikelBez2, "") != 0) {
		$text	=	sprintf( "%s, %s", mTS( $_artikel->ArtikelBez1), mTS( $_artikel->ArtikelBez2)) ;
	} else {
		$text	=	sprintf( "%s", mTS( $_artikel->ArtikelBez1), mTS( $_artikel->ArtikelBez2)) ;
	}
	if ( strcmp( $_artikel->MengenText, "") != 0) {
		$mtext	=	sprintf( ", %s", mTS( $_artikel->MengenText)) ;
	} else if ( $_cuOffrPosten->MengeProVPE > 1) {
		$mtext	=	textFromMenge( $_artikel->MengenEinheit, $_cuOffrPosten->MengeProVPE) ;
	} else {
		$mtext	=	"" ;
	}

	fwrite( $_oFile, sprintf( "%2d & %s & \parbox{70mm}{%s%s} & \parbox{14mm}{\begin{flushright}%d\end{flushright}} & \parbox{14mm}{\begin{flushright}%s\end{flushright}} & %s & %s",
			intval( $_cuOffrPosten->PosNr),
			mTS( $_cuOffrPosten->ArtikelNr),
			$text,
			$mtext,
			intval( $_cuOffrPosten->Menge),
			$preisText,
			$sumPreisText,
			$_artikel->MwstSatz
			)
		) ;
	fwrite( $_oFile, " \\\\[0.1ex] \n") ;

	return $myPosPreis ;
}

?>
