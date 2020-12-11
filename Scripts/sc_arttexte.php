#!/usr/bin/php5
<?php

//
// Diese Script verifiziert die logische Konsistenz der Datanbank im Hinblick auf Artikel
//
// THe following checks are performed:
//
//	1.	see if `ArtikelNr` appears twice
//	2.	see if Artikel has at least one (1) VKPreis attached
//	3.	see if Artikel has at least one (1) EKPreis attached
//	4.	see if Artikel has at least one (1) ArtikelBestand attached
//	5.	see if Artikel is attached to either ProdGr or ArtGr
//	5.a	see if Artikel is attached to ProdGr
//	5.b	see if Artikel is attached to ArtGr
//	6.	see if there are ProdGr which have both, ArtGr and Artikel attached
//	7.	see if there are ArtGr which have both, ArtGr and Artikel attached
//	2.	see if `ArtikelNr` appears twice
//
 
require_once( "global.inc.php") ; 
require_once( "textools.inc.php") ; 
require_once( "Lieferant.inc.php") ; 
require_once( "ArtTexte.inc.php") ; 

$lastLiefArtNr	=	"" ;
$artNr	=	"%" ;
$liefNr	=	"%" ;
$propose	=	false ;
$func	=	"" ;

$i	=	1 ;
while ( isset( $argv[$i])) {
	if ( strcmp( $argv[ $i], "-d") == 0) {
		$i++ ;
		if ( isset( $argv[ $i])) {
			$debugLevel	=	intval( $argv[ $i]) ;
		} else {
			printf( "PANIC.: -d muss gefolgt sein durch den Debug Level <br />\n") ;
		}
	} else if ( strcmp( $argv[ $i], "-f") == 0) {
		$i++ ;
		if ( isset( $argv[ $i])) {
			$func	=	$argv[ $i] ;
		} else {
			printf( "PANIC.: -f muss gefolgt sein durch die Funktion <br />\n") ;
		}
	} else if ( strcmp( $argv[ $i], "-A") == 0) {
		$i++ ;
		$artNr	=	$argv[ $i] ;
	} else if ( strcmp( $argv[ $i], "-L") == 0) {
		$i++ ;
		$liefNr	=	$argv[ $i] ;
		$i++ ;
		$artNrPrefix	=	$argv[ $i] ;
	} else if ( strcmp( $argv[ $i], "-p") == 0) {
		$propose	=	true ;
	} else if ( strcmp( $argv[ $i], "-?") == 0) {
		$func	=	"" ;
	}
	$i++ ;
}

printf( "Debug Level........: %d <br />\n", $debugLevel) ;
printf( "Artikel Filter.....: %s <br />\n", $artNr) ;
printf( "Lieferant Filter...: %s <br />\n", $liefNr) ;

$database	=	connectDatabase( $dbHost, $dbUser, $dbPasswd) ; 

if ( ! mysql_select_db( $dbName)) { 
	printf( "could not select database, will simply die ... \n") ;
	$mainResult	=	-1004 ;	
} else {
	echo "Database connection ok ... \n" ;
}

//

if ( $propose) {
	$sqlFile	=	fopen( "/tmp/sc_artikel.sql", "w+") ;
}

//

$lowestEK	=	0.0 ;
$lowestVK	=	0.0 ;

switch ( $func) {
case	""	:
	myDebugS( $debugLevel, "Keine Funktion angegeben <br />\n") ;
	myDebugS( $debugLevel, "Moegliche Funktionen sind:<br />\n") ;
	myDebugS( $debugLevel, "  1= prueft Artikel ausschliesslich auf Vorhandensein einer Einkaufspreis Relation (EKPreisR) <br />\n") ;
	myDebugS( $debugLevel, "     Moegliche Parameter: <br />\n") ;
	myDebugS( $debugLevel, "     <br />\n") ;
	myDebugS( $debugLevel, " <br />\n") ;
	myDebugS( $debugLevel, "  2= prueft Artikel ausschliesslich auf Vorhandensein eines Verkaufspreises (VKPreis) <br />\n") ;
	myDebugS( $debugLevel, " <br />\n") ;
	myDebugS( $debugLevel, "  3= prueft Artikel ausschliesslich auf Vorhandensein eines Lagerbestand Eintrages (ArtikelBestand) <br />\n") ;
	myDebugS( $debugLevel, " <br />\n") ;
	myDebugS( $debugLevel, " 11= identifiziert Artikel ohne Referenzpreis hat <br />\n") ;
	myDebugS( $debugLevel, " <br />\n") ;
	myDebugS( $debugLevel, " 12= identifiziert Artikel mit mehr als einem Referenzpreis hat <br />\n") ;
	myDebugS( $debugLevel, " <br />\n") ;
	myDebugS( $debugLevel, "101= Sucht Einkaufspreise fuer die kein Artikel existiert <br />\n") ;
	myDebugS( $debugLevel, "     Erforderliche Parameter: <br />\n") ;
	myDebugS( $debugLevel, "     -L <lieferant-nr> <ArtikelNr-Prefix> <br />\n") ;
	myDebugS( $debugLevel, "     <br />\n") ;
	myDebugS( $debugLevel, "     Optionale Parameter: <br />\n") ;
	myDebugS( $debugLevel, "     -p SQL Statement zur Korrektur des Problems in /tmp/sc_artikel.sql schreiben <br />\n") ;
	myDebugS( $debugLevel, "     <br />\n") ;
	myDebugS( $debugLevel, " <br />\n") ;
	break ;
case	"1"	:
case	"2"	:
case	"3"	:
	loopArtikel001( $database, $func) ;
	break ;
case	"11"	:
case	"12"	:
	loopArtikel010( $database, $func) ;
	break ;
case	"101"	:
	if ( strcmp( $liefNr, "%") == 0) {
	} else {
		loopEKPreis100( $database, $func) ;
	}
	break ;
case	"99"	:
	loopArtikel2( $database) ;
	break ;
}

//

if ( $propose) {
	fclose( $sqlFile) ;
}

function	loopArtikel001( $_db, $_func) {

	//

	global	$debugLevel ;
	global	$propose ;
	global	$artNr ;
	global	$liefNr ;
	global	$lowestEK ;
	global	$lowestVK ;

	//

	$myArtTexte	=	new ArtTexte() ; 

	//

	$invArtikel	=	0 ;

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from ArtTexte " ; 
	$coreQuery	.=	"where Volltext <> '' " ; 
	$coreQuery	.=	"order by ArtikelNr ASC " ; 
	$sqlResult      =       mysql_query( $coreQuery, $_db) ; 
	$coreNumRows        =       mysql_affected_rows( $_db) ; 

	if ( !$sqlResult) { 

		myDebug( 0, "could not perform basic Db query for Artikel, will simply die ... \n") ;

	} else if ( $coreNumRows < 1) {

		myDebug( 0, "Keine Artikel erfasst ... \n") ;

	} else {

		// Fuer jeden Artikel

		$eks	=	0 ;

		while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

			$myArtTexte->assignFromRow( $coreRow) ;

			myDebug(  1, "  Betrachte Artikel Nr...........................: %s <br /> \n", $myArtTexte->ArtikelNr) ;

			xmlToPlain( $myArtTexte->Volltext) ;
			
		}
	
		myDebug(  0, "Anzahl Artikel in der Datenbank..................: " . $coreNumRows . "<br/> \n") ;
		myDebug(  0, "  davon fehlerhaft...............................: " . $invArtikel . "<br/> \n") ;
	}

}

?>
