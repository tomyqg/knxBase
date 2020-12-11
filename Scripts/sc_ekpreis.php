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
require_once( "Artikel.inc.php") ; 
require_once( "VKPreis.inc.php") ; 

$database	=	connectDatabase( $dbHost, $dbUser, $dbPasswd) ; 

if ( ! mysql_select_db( $dbName)) { 
	printf( "could not select database, will simply die ... \n") ;
	$mainResult	=	-1004 ;	
} else {
	echo "Database connection ok ... \n" ;
}

//

$mySQLFile	=	fopen( "/tmp/fix-sc_ekpreis.sql", "w+") ;
if ( ! $mySQLFile) {
	printf( "could not open sql-fix file, will simply die ... \n") ;
	die() ;
}

//

$myArtikel	=	new Artikel() ; 

// generate the basic artikel data

$coreQuery	=	"select * " ; 
$coreQuery	.=	"from Artikel " ; 
$coreQuery	.=	"order by ArtikelNr ASC " ; 
$sqlResult      =       mysql_query( $coreQuery, $database) ; 
$coreNumRows        =       mysql_affected_rows( $database) ; 

if ( !$sqlResult) { 

	printf( "could not perform basic Db query for Artikel, will simply die ... \n") ;

} else if ( $coreNumRows < 1) {

	printf( "002: Keine Produkt Gruppen definiert ... \n") ;

} else {

	// Fuer jeden Eintrag in "ProdGr" muss eine statische Seite gebaut werden

	$artikelCount	=	0 ;
	while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

		$myArtikel->assignFromRow( $coreRow) ;

		myDebug(  9, "  Betrachte Artikel Nr...........................: %s <br /> \n", $myArtikel->ArtikelNr) ;
		myDebug(  9, "    Artikel Bezeichnung..........................: %s <br /> \n", $myArtikel->ArtikelBez1) ;

		checkEKPreise( $database, $myArtikel->ArtikelNr) ;

		checkVKPreise( $database, $myArtikel->ArtikelNr) ;

	}
	
	myDebug(  9, "Anzahl Artikel in der Datenbank..................: " . $coreNumRows . "<br/> \n") ;
}

fclose( $mySQLFile) ;

function	checkEKPreise( $_database, $_artikelNr) {

	//	1.	see if Artikel has at least one (1) EKPreis attached

	$anzahlEKPreis	=	getCount( $_database, "from EKPreis where ArtikelNr = '".$_artikelNr."' ") ;
	myDebug(  9, "    Anzahl vorhandener EK Preise.................: " . $anzahlEKPreis . " \n") ;

	if ( $anzahlEKPreis == 0) {

		myDebug(  0, "Artikel Nummer [%s] hat keine Einkaufspreise \n", $_artikelNr) ;

	} else {

		$anzahlEKPreis	=	getCount( $_database, "from EKPreis where ArtikelNr = '".$_artikelNr."' and GueltigBis = '2099-12-31' ") ;
		myDebug(  9, "    Anzahl vorhandener EK Preise.................: " . $anzahlEKPreis . " \n") ;
	
		if ( $anzahlEKPreis == 0) {

			myDebug(  0, "Artikel Nummer [%s] hat keinen derzeit gueltigen Einkaufspreise \n", $_artikelNr) ;

		} else {

//			myDebug(  0, "Artikel Nummer [%s] hat mehrere derzeit gueltige Einkaufspreise \n", $_artikelNr) ;

		}

	}

}

function	checkVKPreise( $_database, $_artikelNr) {

	//	1.	see if Artikel has at least one (1) VKPreis attached

	$anzahlVKPreis	=	getCount( $_database, "from VKPreis where ArtikelNr = '".$_artikelNr."' ") ;
	myDebug(  9, "    Anzahl vorhandener VK Preise.................: " . $anzahlVKPreis . " \n") ;

	if ( $anzahlVKPreis == 0) {

		myDebug(  0, "Artikel Nummer [%s] hat keine Verkaufspreise \n", $_artikelNr) ;

	} else {

		$anzahlVKPreis	=	getCount( $_database, "from VKPreis where ArtikelNr = '".$_artikelNr."' and GueltigBis = '2099-12-31' ") ;
		myDebug(  9, "    Anzahl vorhandener VK Preise.................: " . $anzahlVKPreis . " \n") ;
	
		if ( $anzahlVKPreis == 0) {

			myDebug(  0, "Artikel Nummer [%s] hat keinen derzeit gueltigen Verkaufspreise \n", $_artikelNr) ;

		} else {

//			myDebug(  0, "Artikel Nummer [%s] hat mehrere derzeit gueltige Verkaufspreise \n", $_artikelNr) ;

		}

	}

}

?>
