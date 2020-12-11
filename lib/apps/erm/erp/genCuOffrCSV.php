<?php

$lastPosNr	=	"" ;
$subPosCtr	=	0 ;

function	genCuOffrCSV( $_database, $_cuOffr) {

	//

	$prefix	=	"" ;
	$addition	=	"" ;
	$goOn	=	true ;

	//

	$myKunde	=	$_cuOffr->getKunde() ;
	$myKundeKontakt	=	$_cuOffr->getKundeKontakt() ;
	if ( ! $myKunde || ! $myKundeKontakt) {
		myDebug(  0, "%s<br />\n", sprintf( "PANIC..: die Kundendaten sind nicht lesbar")) ;
		die() ;
	}

	$parts	=	explode( "-", $_cuOffr->Datum) ;
	$myCuOffrDatum	=	$parts[2] . "." . $parts[1] . "." . $parts[0] ;
	$parts	=	explode( "-", $_cuOffr->RefDatum) ;
	$myCuOffrRefDatum	=	$parts[2] . "." . $parts[1] . "." . $parts[0] ;
	$query	=	"select AP.PosNr, AP.ArtikelNr, AP.Menge, AP.Preis, AP.RefPreis, AP.MengeProVPE, A.MengenEinheit, A.ArtikelBez1, A.ArtikelBez2, A.MengenText, A.MwstSatz, AT.Volltext, AT.RFQText " ;
	$query	.=	"from CuOffrItem as AP " ;
	$query	.=	"left join Artikel as A on A.ArtikelNr = AP.ArtikelNr " ;
	$query	.=	"left join ArtTexte AT on AT.ArtikelNr =  AP.ArtikelNr " ;
	$query	.=	"where AP.CuOffrNo = \"" . $_cuOffr->CuOffrNo . "\" " ;
	$query	.=	"and AP.ArtikelNr = A.ArtikelNr " ;
	$query	.=	"order by AP.PosNr " ;

	//

	$lang	=       "de" ;

	//

	$csv_name	=	tempnam( "/tmp", "angebot-") ;
	$csv_name	.=	".csv" ;

	$csvOFile	=	fopen( $csv_name, "w+") ;
	if ( $csvOFile) {
		fwrite( $csvOFile, sprintf( "\"Angebot\";\"\";\"\";\"\";\"\";\"\";\"\";\"\"\n", $myKunde->KundeNr)) ;
		fwrite( $csvOFile, sprintf( "\"\";\"\";\"\";\"\";\"\";\"\";\"\";\"\"\n")) ;
		fwrite( $csvOFile, sprintf( "\"Kunden Nr.\";\"%s\"\n", $myKunde->KundeNr)) ;
		fwrite( $csvOFile, sprintf( "\"Name\";\"%s\"\n", $myKunde->FirmaName1)) ;
		fwrite( $csvOFile, sprintf( "\"\";\"%s\"\n", $myKunde->FirmaName2)) ;
		fwrite( $csvOFile, sprintf( "\"z.Hd.\";\"%s %s %s %s\"\n",
							$myKundeKontakt->Anrede,
							$myKundeKontakt->Titel,
							$myKundeKontakt->Vorname,
							$myKundeKontakt->Name)) ;
		fwrite( $csvOFile, sprintf( "\"Strasse\";\"%s %s\"\n", $myKunde->Strasse, $myKunde->Hausnummer)) ;
		fwrite( $csvOFile, sprintf( "\"Ort\";\"%s %s\"\n", $myKunde->PLZ, $myKunde->Ort)) ;
		fwrite( $csvOFile, sprintf( "\n")) ;
		fwrite( $csvOFile, sprintf( "\"Land\";\"%s\"\n", $myKunde->Land)) ;
		fwrite( $csvOFile, sprintf( "\n")) ;
		fwrite( $csvOFile, sprintf( "\"Telefon\";\"%s\"\n", $myKunde->Telefon)) ;
		fwrite( $csvOFile, sprintf( "\"Fax\";\"%s\"\n", $myKunde->FAX)) ;
		fwrite( $csvOFile, sprintf( "\n")) ;
		fwrite( $csvOFile, sprintf( "\"Angebot Nr.\";\"%s\"\n", $_cuOffr->CuOffrNo)) ;
		fwrite( $csvOFile, sprintf( "\"Datum\";\"%s\"\n", $_cuOffr->Datum)) ;
		fwrite( $csvOFile, sprintf( "\n")) ;
		fwrite( $csvOFile, sprintf( "\"Ihre Anfrage\";\"%s\"\n", $_cuOffr->KdRefNr)) ;
		fwrite( $csvOFile, sprintf( "\"vom\";\"%s\"\n", $_cuOffr->KdRefDatum)) ;
		fwrite( $csvOFile, sprintf( "\n")) ;
		fwrite( $csvOFile, sprintf( "\n")) ;
		fwrite( $csvOFile, sprintf( "\"Position\";\"Artikel Nr.\";\"Artikel Bezeichnung\";\"Menge\";\" %%\";\"Einzelpreis\";\"Rabatt\";\"Preis / Pos.\";\"Mwst. Satz\"\n")) ;
		$sqlResult =       mysql_query( $query, $_database) ;
		if ( !$sqlResult) {
			$mainResult	=	-1 ;
			fwrite( $csvOFile, "FEHLER BEI DATENBANKZUGRIFF") ;
			fwrite( $csvOFile, $query) ;
		} else {
			$numrows        =       mysql_affected_rows( $_database) ;
			if ( $numrows == 0) {
				printf( "Keine CuCartsposten ... <br/> \n") ;
				die() ;
			}
			$ctrRow	=	1 ;
			$myGesamtPreis['0']	=	0.0 ;
			$myGesamtPreis['A']	=	0.0 ;
			$myGesamtPreis['B']	=	0.0 ;
			$myGesamtPreis['C']	=	0.0 ;
			while ($row = mysql_fetch_assoc( $sqlResult)) {
				$myGesamtPreis[ $row['MwstSatz']]	+=	writeLoopLineWPAsCSV( $csvOFile, $row, "PosNr") ;
			}
//ALT			$myGesamtPreis	-=	writeRabattCSV( $csvOFile, $myGesamtPreis, $_cuOffr->Rabatt) ;
//			$myGesamtPreis	+=	writeVersandKostenCSV( $csvOFile, $myGesamtPreis) ;
//						writeNettoCSV( $csvOFile, $myGesamtPreis) ;
//			$myGesamtPreis	+=	writeMwstCSV( $csvOFile, $myGesamtPreis, 19) ;
//						writeBruttoCSV( $csvOFile, $myGesamtPreis) ;
			$myGesamtPreis['A']	-=	writeRabattCSV( $csvOFile, $myGesamtPreis['A'], $_cuOffr->Rabatt) ;
			$myGesamtPreis['0']	=	$myGesamtPreis['A'] + $myGesamtPreis['B'] + $myGesamtPreis['C'] ;
			$myGesamtPreis['A']	+=	writeVersandKostenCSV( $csvOFile, $myGesamtPreis['0'], $_cuOffr->VersPausch) ;
			$myGesamtPreis['0']	=	$myGesamtPreis['A'] + $myGesamtPreis['B'] + $myGesamtPreis['C'] ;
			if ( strlen( $myKunde->UStId) == 0) {
				$myGesamtPreis['0']	+=	writeMwstCSV( $csvOFile, $myGesamtPreis['A'], 'A') ;
				$myGesamtPreis['0']	+=	writeMwstCSV( $csvOFile, $myGesamtPreis['B'], 'B') ;
				$myGesamtPreis['0']	+=	writeMwstCSV( $csvOFile, $myGesamtPreis['C'], 'C') ;
								writeBruttoCSV( $csvOFile, $myGesamtPreis['0']) ;
			} else {
			}
//ALT			$addition	=	xmlToTex( $_cuOffr->Rem1) ;
		}
		fclose( $csvOFile) ;
	} else {
		printf( "Kann csv files nicht oeffnen \n") ;
		die() ;
	}

	// remove all temporary files

//	system( "rm " . $pdf_name) ;

	return $csv_name ;
}

function	writeLoopLineWPAsCSV( $_oFile, $_row, $_posColName) {

	global	$lastPosNr ;
	global	$subPosCtr ;

	//

	$myPosPreis	=	0.0 ;

	$myPosPreis	=	$_row['Menge'] * $_row['Preis'] ;

	//

	$addPos	=	"" ;
	if ( strcmp( $_row[$_posColName], $lastPosNr) == 0) {
		$subPosCtr++ ;
		$subPosText	=	sprintf(".%d", $subPosCtr) ;
	} else {
		$subPosCtr	=	0 ;
		$subPosText	=	"" ;
	}

	if ( floatval( $_row['RefPreis']) != 0.00) {
		$myPosRabProz	=	(0.05 - 0.05 / sqrt( $_row['Menge'])) * 100.0 ;
		$myPosRabProz	=	((floatval( $_row['RefPreis']) - floatval( $_row['Preis'])) * 100.0) / floatval( $_row['RefPreis']) ;
		$myPosRabCurr	=	floatval( $_row['RefPreis']) - floatval( $_row['Preis']) ;
	} else {
		$myPosRabProz	=	0.0 ;
		$myPosRabProz	=	0.0 ;
		$myPosRabCurr	=	0.0 ;
	}

	if ( floatval( $_row['RefPreis']) != 0.00) {
		$preisText	=	number_format( $_row['RefPreis'], 2, ",", "") ;
		$sumPreisText	=	number_format( $myPosPreis, 2, ",", "") ;
	} else {
		$preisText	=	"n.A." ;
		$sumPreisText	=	"n.A." ;
	}

	if ( strlen( $_row['RFQText']) == 0) {

		if ( strcmp( $_row['ArtikelBez1'], $_row['ArtikelBez2']) != 0 && strcmp( $_row['ArtikelBez2'], "") != 0) {
			$mtext	=	sprintf( "%s, %s", $_row['ArtikelBez1'], $_row['ArtikelBez2']) ;
		} else {
			$mtext	=	sprintf( "%s", $_row['ArtikelBez1']) ;
		}
	
		if ( strcmp( $_row['MengenText'], "") != 0) {
			$mtext	.=	sprintf( ", %s", mTS( $_row['MengenText'])) ;
		} else if ( intval( $_row['MengeProVPE']) > 1) {
			$mtext	.=	textFromMenge( $_row['MengenEinheit'], $_row['MengeProVPE']) ;
		} else {
			$mtext	.=	"" ;
		}

		fwrite( $_oFile, sprintf( "\"%s\";\"%s\";\"%s\";%d;\"-%s%%\";\"%s\";\"-%s\";\"%s\";\"%s\"\n",
				intval( $_row[$_posColName]),
				$_row['ArtikelNr'],
				csvConvert( $mtext),
				intval( $_row['Menge']),
				number_format( $myPosRabProz, 2, ",", ""),
				$preisText,
				number_format( $myPosRabCurr, 2, ",", ""),
				$sumPreisText,
				$_row['MwstSatz']
				)
			) ;
	
		if ( strlen( $_row['Volltext']) > 0) {
			if ( mb_check_encoding( $_row['Volltext'],"UTF-8")) {
				fwrite( $_oFile, sprintf( "\"\";\"\";\"%s\";\"\";\"\";\"\";\"\";\"\"\n",
						csvConvert( mb_convert_encoding( xmlToPlain( $_row['Volltext']), "iso-8859-1", "UTF-8"))
						)
					) ;
			} else {
				echo "PANIC...: Probleme mit Volltext von ArtikelNr....: " . $_row['ArtikelNr'] . "<br />" ;
			}
		}

	} else {

		fwrite( $_oFile, sprintf( "\"%s\";\"%s\";\"%s\";%d;\"-%s%%\";\"%s\";\"-%s\";\"%s\";\"%s\"\n",
				intval( $_row[$_posColName]),
				$_row['ArtikelNr'],
				csvConvert( mb_convert_encoding( xmlToPlain( $_row['RFQText']), "iso-8859-1", "UTF-8")),
				intval( $_row['Menge']),
				number_format( $myPosRabProz, 2, ",", ""),
				$preisText,
				number_format( $myPosRabCurr, 2, ",", ""),
				$sumPreisText,
				$_row['MwstSatz']
				)
			) ;
	
	}

	$lastPosNr	=	$_row[$_posColName] ;

	return $myPosPreis ;
}

function	writeRabattCSV( $_oFile, $_netto, $_rabattSatz) {

	//

	$myRabatt	=	0.0 ;
	$myRabattSatz	=	0.0 ;

	//

	if ( $_rabattSatz > 1) {
		$myRabattSatz	=	$_rabattSatz ;
	} else {
		$myRabattSatz	=	$_rabattSatz * 100.0 ;
	}
	$myRabatt	=	$_netto * $myRabattSatz / 100.0 ;

	//

	if ( $myRabattSatz > 0.1) {
		$myRabatt	=	$myRabattSatz * $_netto / 100 ;
		fwrite( $_oFile, sprintf( ";;\"Rabatt\";\"%4.2f%%\";;;;\"-%s\"\n",
				number_format( $myRabattSatz, 1, ",", ""),
				number_format( $myRabatt, 2, ",", ""))
			) ;
	}

	//

	return $myRabatt ;
}

function	writeVersandKostenCSV( $_oFile, $_gesamtPreis) {

	//

	$myVersandKosten	=	0.0 ;

	//

	if ( $_gesamtPreis >= 200) {
		$myVersandKosten	=	0.00 ;
		fwrite( $_oFile, sprintf( ";;\"Versandkosten\";;;;;\"%s\"\n", number_format( $myVersandKosten, 2, ",", ""))) ;
	} else if ( $_gesamtPreis >= 100) {
		$myVersandKosten	=	6.00 ;
		fwrite( $_oFile, sprintf( ";;\"Versandkosten\";;;;;\"%s\"\n", number_format( $myVersandKosten, 2, ",", ""))) ;
	} else {
		$myVersandKosten	=	12.00 ;
		fwrite( $_oFile, sprintf( ";;\"Versandkosten\";;;;;\"%s\"\n", number_format( $myVersandKosten, 2, ",", ""))) ;
	}

	return $myVersandKosten ;
}

function	writeNettoCSV( $_oFile, $_netto) {

	//

	fwrite( $_oFile, sprintf( ";;\"Nettobetrag, EUR\";;;;;\"%s\"\n",
			number_format( $_netto, 2, ",", ""))
		) ;
}

function	writeMwstCSV( $_oFile, $_netto, $_mwstSatz) {

	//

	$myMwst	=	0.0 ;
	$myMwstSatz	=	0.0 ;

	if ( $_netto > 0.0) {

		switch ( $_mwstSatz) {
		case	'A'	:
			$myMwstSatz	=	19 ;
			break ;
		case	'B'	:
			$myMwstSatz	=	7 ;
			break ;
		case	'C'	:
			$myMwstSatz	=	0 ;
			break ;
		default	:
			$myMwstSatz	=	$_mwstSatz * 100.0 ;
			break ;
		}
		$myMwst	=	round( $_netto * $myMwstSatz / 100.0, 2) ;
		fwrite( $_oFile, sprintf( ";;\"Mehrwertsteuer (%s), %s%% auf EUR %s\";;;;;\"%s\"\n",
				$_mwstSatz,
				number_format( $myMwstSatz, 0, ",", ""),
				number_format( $_netto, 2, ",", ""),
				number_format( $myMwst, 2, ",", ""))
			) ;

	}

	return $myMwst ;
}

function	writeBruttoCSV( $_oFile, $_brutto) {

	//

	fwrite( $_oFile, sprintf( ";;\"Bruttobetrag, EUR\";;;;;\"%s\"\n",
			number_format( $_brutto, 2, ",", ""))
		) ;

}

function	csvConvert( $_str) {
	$res	=	str_replace( "\"", "\"\"", $_str) ;
	return( $res) ;
}

?>
