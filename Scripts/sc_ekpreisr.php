#!/usr/bin/php5
<?php

//
// Diese Script verifiziert die logische Konsistenz der Datanbank im Hinblick auf Artikel, aus der Perspective der EKPreisR Relation
//
// FUER alle EKPreisR (ggf. mit Lieferanten Filter) in der Tabelle
//   pruefe ob ein entsprechender Artikel definiert istwenn nicht, Statusmeldung
//   Nein:
//     Status Meldung ausgeben
//   Ja:
//     gib alle verfuegbaren Einkaufspreise aus, markiere den Referenzpreis fuer die Verkaufspreis Kalkulation,
//       
//
 
require_once( "global.inc.php") ; 
require_once( "Lieferant.inc.php") ; 
require_once( "LiefRabatt.inc.php") ; 
require_once( "Artikel.inc.php") ; 
require_once( "ArtikelEKPreis.inc.php") ; 
require_once( "EKPreisR.inc.php") ; 
require_once( "VKPreis.inc.php") ; 

$artNr	=	"%" ;
$liefNr	=	"%" ;
$propose	=	true ;
$mode	=	0 ;

$i	=	1 ;
while ( isset( $argv[$i])) {
	if ( strcmp( $argv[ $i], "-d") == 0) {
		$i++ ;
		if ( isset( $argv[ $i])) {
			$debugLevel	=	intval( $argv[ $i]) ;
		} else {
			printf( "PANIC.: -d muss gefolgt sein durch den Debug Level <br />\n") ;
		}
	} else if ( strcmp( $argv[ $i], "-m") == 0) {
		$i++ ;
		if ( isset( $argv[ $i])) {
			$mode	=	intval( $argv[ $i]) ;
		} else {
			printf( "PANIC.: -m muss gefolgt sein durch Modus <br />\n") ;
		}
	} else if ( strcmp( $argv[ $i], "-A") == 0) {
		$i++ ;
		$artNr	=	$argv[ $i] ;
	} else if ( strcmp( $argv[ $i], "-L") == 0) {
		$i++ ;
		$liefNr	=	$argv[ $i] ;
	} else if ( strcmp( $argv[ $i], "-p") == 0) {
		$propose	=	false ;
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

$refEK	=	0.0 ;
$lowestVK	=	0.0 ;

loopEKPreisR( $database) ;

//

if ( $propose) {
	fclose( $sqlFile) ;
}

function	loopEKPreisR( $_db) {

	//

	global	$debugLevel ;
	global	$mode ;
	global	$artNr ;
	global	$liefNr ;
	global	$refEK ;
	global	$lowestVK ;

	//

	$myArtikel	=	new Artikel() ; 
	$myLiefRabatt	=	new LiefRabatt() ; 

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from EKPreisR " ; 
	$coreQuery	.=	"where LiefNr like '" .$liefNr . "' " ; 
	$coreQuery	.=	"and ArtikelNr like '" .$artNr . "' " ; 
	$coreQuery	.=	"order by LiefNr, LiefArtNr ASC " ; 
	$sqlResult      =       mysql_query( $coreQuery, $_db) ; 
	$coreNumRows        =       mysql_affected_rows( $_db) ; 

	if ( !$sqlResult) { 

		myDebug( 0, "could not perform basic Db query for Artikel, will simply die ... \n") ;

	} else if ( $coreNumRows < 1) {

		myDebug( 0, "Keine Artikel erfasst ... \n") ;

	} else {

		// Fuer jeden Artikel

		$myLieferant	=	new Lieferant() ;
		$myEKPreisR	=	new EKPreisR() ;

		while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

			$myEKPreisR->assignFromRow( $coreRow) ;

			$myLieferant->LieferantNr	=	$myEKPreisR->LiefNr ;
			$myLieferant->fetchFromDb( $_db) ;

			$myArtikel->ArtikelNr	=	$myEKPreisR->ArtikelNr ;
			$myArtikel->fetchFromDb( $_db) ;

			if ( $myArtikel->_valid == 0 && $mode == 1) {

				myDebug(  1, "  Artikel existiert nicht........................: %s <br /> \n", $myArtikel->ArtikelNr) ;

			} else if ( $myArtikel->_valid == 1 && $mode != 1) {

				myDebug(  1, "  Betrachte Artikel Nr...........................: %s <br /> \n", $myArtikel->ArtikelNr) ;
				myDebug(  9, "    Bezeichung 1.................................: %s <br /> \n", $myArtikel->ArtikelBez1) ;
				if ( strcmp( $myArtikel->ArtikelBez1, $myArtikel->ArtikelBez2) != 0) {
					myDebug(  9, "    Bezeichung 2.................................: %s <br /> \n", $myArtikel->ArtikelBez2) ;
				}
				myDebug(  9, "    Lieferant Nr / Name .........................: %s, %s <br /> \n", $myLieferant->LieferantNr, $myLieferant->FirmaName1) ;
				myDebug(  9, "      Lieferant Artikel Nr.......................: %s <br /> \n", $myEKPreisR->LiefArtNr) ;

				$eksFound	=	loopArtikelEKPreis( $_db, $myEKPreisR) ;

				$vksFound	=	loopVKPreis( $_db, $myArtikel) ;

				myDebug(  9, "    Einkaufspreise...............................: %5d <br /> \n", $eksFound) ;

				if ( $debugLevel > 0)
					printf( "\n") ;

			}
	
		}
	
		myDebug(  0, "Anzahl Artikel in der Datenbank..................: " . $coreNumRows . "<br/> \n") ;
	}

}

function	loopArtikelEKPreis( $_db, $_eKPreisR) {

	//

	global	$refEK ;

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from ArtikelEKPreis a " ; 
	$coreQuery	.=	"left join LiefRabatt b on a.LiefNr = b.LieferantNr and a.HKRabKlasse = b.HKRabKlasse and b.Menge = 1 " ;
	$coreQuery	.=	"where a.LiefNr = '" . $_eKPreisR->LiefNr . "' " ; 
	$coreQuery	.=	"and a.LiefArtNr = '" . $_eKPreisR->LiefArtNr . "' " ; 
	$sqlResult      =       mysql_query( $coreQuery, $_db) ; 
	$coreNumRows        =       mysql_affected_rows( $_db) ; 

	if ( !$sqlResult) { 

		myDebug( 0, "could not perform basic Db query for Artikel, will simply die ... \n") ;
		myDebug( 0, "Query.....:  [%s] <br /> \n", $coreQuery) ;
		die() ;

	} else if ( $coreNumRows < 1) {

		myDebug( 0, "Keine Einkaufspreise fuer Artikel %s, Lieferanr Nr. %s, Lieferant Artikel Nr. %s \n",
					$_eKPreisR->ArtikelNr,
					$_eKPreisR->LiefNr,
					$_eKPreisR->LiefArtNr) ;

	} else {

		// Fuer jeden Eintrag in "ProdGr" muss eine statische Seite gebaut werden

		$myArtikelEKPreis	=	new ArtikelEKPreis() ;

		while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

			$myArtikelEKPreis->assignFromRow( $coreRow) ;

			myDebug(  9, "      In der Menge...............................: %5d <br /> \n", $myArtikelEKPreis->Menge) ;
			myDebug(  9, "      Rabattstaffel..............................: %s <br /> \n", $myArtikelEKPreis->HKRabKlasse) ;
			myDebug(  9, "      Rabatt.....................................: %7.2f <br /> \n", $coreRow['Rabatt']) ;
			if ( $myArtikelEKPreis->Menge <= 0) {
				myDebug(  9, "        Mengenangabe == 0........................: <br /> \n") ;
			}
			myDebug(  9, "        zu.......................................: %9.2f / %d ", $myArtikelEKPreis->Preis, $myArtikelEKPreis->MengeFuerPreis) ;
			if ( $myArtikelEKPreis->Menge == $_eKPreisR->KalkBasis) {
				myDebugS(  9, " <---------- Kalkulationsgrundlage...............<br/>\n") ;
				$refEK	=	$myArtikelEKPreis->Preis / $myArtikelEKPreis->MengeFuerPreis ;
			} else {
				myDebugS(  9, " <br/>\n") ;
			}
			myDebug(  9, "          Lieferant VK ist.......................: %9.2f / %d <br /> \n", $myArtikelEKPreis->LiefVKPreis, $myArtikelEKPreis->MengeFuerPreis) ;


		}
	
	}

	return $coreNumRows ;
}

function	loopVKPreis( $_db, $_artikel) {

	//

	global	$refEK ;
	global	$lowestVK ;

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from VKPreis " ; 
	$coreQuery	.=	"where ArtikelNr = '" . $_artikel->ArtikelNr . "' " ; 
	$sqlResult      =       mysql_query( $coreQuery, $_db) ; 
	$coreNumRows        =       mysql_affected_rows( $_db) ; 

	if ( !$sqlResult) { 

		myDebug( 0, "could not perform basic Db query for Artikel, will simply die ... \n") ;

	} else if ( $coreNumRows < 1) {

		myDebug( 0, "Keine Verkaufspreise fuer Artikel Nr. %s \n", $_artikel->ArtikelNr) ;

	} else {

		// Fuer jeden Eintrag in "ProdGr" muss eine statische Seite gebaut werden

		myDebug(  9, "    Wird verkauft wie folgt......................: <br /> \n") ;

		$myVKPreis	=	new VKPreis() ;

		while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

			$myVKPreis->assignFromRow( $coreRow) ;

			myDebug(  9, "      In der Menge...............................: %5d <br /> \n", $myVKPreis->Menge) ;
			myDebug(  9, "        zu.......................................: %9.2f / %d <br /> \n", $myVKPreis->Preis, $myVKPreis->MengeProVPE) ;

			if ( $myVKPreis->Preis <= $refEK) {
				myDebug(  0, ">>>>>   Dumping ALarm fuer Artikel Nr............: %s <br /> \n", $_artikel->ArtikelNr) ;
			}

		}
	
	}

	return $coreNumRows ;
}

function	proposeEKPreisR( $_artikel) {

	//

	global	$sqlFile ;

	//

	fwrite( $sqlFile,
			printf( "insert into EKPreisR ( ArtikelNr, LiefNr, LiefArtNr, KalkBasis) value( '%s', '100058', '%s', 1);\n",
				$_artikel->ArtikelNr,
				substr( $_artikel->ArtikelNr, 4)
			)
		) ;

}

?>
