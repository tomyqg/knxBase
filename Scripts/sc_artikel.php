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
require_once( "formeln.inc.php") ; 
require_once( "Lieferant.inc.php") ; 
require_once( "Artikel.inc.php") ; 
require_once( "ArtikelEKPreis.inc.php") ; 
require_once( "EKPreisR.inc.php") ; 
require_once( "VKPreis.inc.php") ; 
require_once( "LiefRabatt.inc.php") ; 

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

	$myArtikel	=	new Artikel() ; 

	//

	$invArtikel	=	0 ;

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from Artikel " ; 
	$coreQuery	.=	"where ArtikelNr like '" .$artNr . "' " ; 
	$coreQuery	.=	"and LieferStatus < 8 " ;
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

		$myArtikel	=	new Artikel() ;

		while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

			$myArtikel->assignFromRow( $coreRow) ;

			myDebug(  1, "  Betrachte Artikel Nr...........................: %s <br /> \n", $myArtikel->ArtikelNr) ;
			myDebug(  9, "    Bezeichung 1.................................: %s <br /> \n", $myArtikel->ArtikelBez1) ;

			switch ( $_func) {
			case	"1"	:
				$nrEKs	=	getCount( $_db, " from EKPreisR where ArtikelNr='".$myArtikel->ArtikelNr."' ") ;
				myDebug(  9, "   --> Anzahl EKs................................: %d <br /> \n", $nrEKs) ;
				if ( $nrEKs == 0) {
					myDebug(  0, "   --> Keine EK Preis Relationen definiert.......: %s <br /> \n", $myArtikel->ArtikelNr) ;
					$invArtikel++ ;
				}
				break ;
			case	"2"	:
				$nrVKs	=	getCount( $_db, " from VKPreis where ArtikelNr='".$myArtikel->ArtikelNr."' ") ;
				myDebug(  9, "   --> Anzahl EKs................................: %d <br /> \n", $nrVKs) ;
				if ( $nrVKs == 0) {
					myDebug(  0, "   --> Keine VK Preise definiert.................: %s <br /> \n", $myArtikel->ArtikelNr) ;
					$invArtikel++ ;
				}
				break ;
			case	"3"	:
				$nrABs	=	getCount( $_db, " from ArtikelBestand where ArtikelNr='".$myArtikel->ArtikelNr."' ") ;
				myDebug(  9, "   --> Anzahl EKs................................: %d <br /> \n", $nrABs) ;
				if ( $nrABs == 0) {
					myDebug(  0, "   --> Keine Lagerbestand definiert..............: %s <br /> \n", $myArtikel->ArtikelNr) ;
					$invArtikel++ ;
					proposeABFix( $_db, $myArtikel, $nrABs) ;
				} else if ( $nrABs > 1) {
					$nrABRs	=	getCount( $_db, " from ArtikelBestand where ArtikelNr='".$myArtikel->ArtikelNr."' and RevCode='".$myArtikel->RevCode."' ") ;
					if ( $nrABRs > 1) {
						myDebug(  0, "   --> Mehrere Lagerbestaende definiert..........: %s <br /> \n", $myArtikel->ArtikelNr) ;
						$invArtikel++ ;
						proposeABFix( $_db, $myArtikel, $nrABs) ;
					}
				}
				break ;
			}
		}
	
		myDebug(  0, "Anzahl Artikel in der Datenbank..................: " . $coreNumRows . "<br/> \n") ;
		myDebug(  0, "  davon fehlerhaft...............................: " . $invArtikel . "<br/> \n") ;
	}

}

function	loopArtikel010( $_db, $_func) {

	//

	global	$debugLevel ;
	global	$propose ;
	global	$artNr ;
	global	$liefNr ;
	global	$lowestEK ;
	global	$lowestVK ;

	//

	$myArtikel	=	new Artikel() ; 

	//

	$invArtikel	=	0 ;

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from Artikel " ; 
	$coreQuery	.=	"where ArtikelNr like '" .$artNr . "' " ; 
	$coreQuery	.=	"and LieferStatus < 8 " ;			// nur Artikel die noch verkauft werden koennen (ab Lager
										// oder mit Neubestellung beim Lieferanten)
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

		$myArtikel	=	new Artikel() ;

		while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

			$myArtikel->assignFromRow( $coreRow) ;

			myDebug(  1, "  Betrachte Artikel Nr...........................: %s <br /> \n", $myArtikel->ArtikelNr) ;
			myDebug(  9, "    Bezeichung 1.................................: %s <br /> \n", $myArtikel->ArtikelBez1) ;

			$nrEKs	=	getCount( $_db, " from EKPreisR where ArtikelNr='".$myArtikel->ArtikelNr."' and RevCode='".$myArtikel->RevCode."' ") ;
			myDebug(  9, "   --> Anzahl EKs................................: %d <br /> \n", $nrEKs) ;

			if ( $nrEKs > 0) {
				$refEKs	=	getCount( $_db, " from EKPreisR where ArtikelNr='".$myArtikel->ArtikelNr."' and RevCode='".$myArtikel->RevCode."' and KalkBasis <> 0 ") ;
				myDebug(  9, "   --> Anzahl Referenz EKs.......................: %d <br /> \n", $refEKs) ;
				switch ( $_func) {
				case	"11"	:
					if ( $refEKs == 0) {
						myDebug(  0, "   --> Kein Referenzpreis definiert..............: %s <br /> \n", $myArtikel->ArtikelNr) ;
						$invArtikel++ ;
					}
					break ;
				case	"12"	:
					if ( $refEKs > 1) {
						myDebug(  0, "   --> Zu viele (%3d) Referenzpreise definiert...: %s <br /> \n", $refEKs, $myArtikel->ArtikelNr) ;
						$invArtikel++ ;
					}
					break ;
				}
			}

		}
	
		myDebug(  0, "Anzahl Artikel in der Datenbank..................: " . $coreNumRows . "<br/> \n") ;
		myDebug(  0, "  davon fehlerhaft...............................: " . $invArtikel . "<br/> \n") ;
	}

}

function	loopArtikel( $_db) {

	//

	global	$debugLevel ;
	global	$propose ;
	global	$artNr ;
	global	$liefNr ;
	global	$lowestEK ;
	global	$lowestVK ;

	//

	$myArtikel	=	new Artikel() ; 

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from Artikel " ; 
	$coreQuery	.=	"where ArtikelNr like '" .$artNr . "' " ; 
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

		$myArtikel	=	new Artikel() ;

		while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

			$lowestVK	=	0.0 ;
			$lowestEK	=	0.0 ;

			$myArtikel->assignFromRow( $coreRow) ;

			myDebug(  1, "  Betrachte Artikel Nr...........................: %s <br /> \n", $myArtikel->ArtikelNr) ;
			myDebug(  9, "    Bezeichung 1.................................: %s <br /> \n", $myArtikel->ArtikelBez1) ;
			if ( strcmp( $myArtikel->ArtikelBez1, $myArtikel->ArtikelBez2) != 0) {
				myDebug(  9, "    Bezeichung 2.................................: %s <br /> \n", $myArtikel->ArtikelBez2) ;
			}

			$relsFound	=	loopEKPreisR( $_db, $myArtikel, $liefNr, $eks) ;

			$vksFound	=	loopVKPreis( $_db, $myArtikel) ;

			if ( $eks > 0) {
				if ( $lowestVK < $lowestEK) {
					myDebug(  0, ">>> DUMPING ALARM ...............................: %s <br /> \n", $myArtikel->ArtikelNr) ;
					if ( $propose) {
						proposeVKPreisUpdate( $_db, $myArtikel) ;
					}
				} else if ( $lowestVK > ( $lowestEK * 2)) {
					myDebug(  0, ">>> WUCHER ALARM ................................: %s <br /> \n", $myArtikel->ArtikelNr) ;
					if ( $propose) {
						proposeVKPreisUpdate( $_db, $myArtikel) ;
					}
				}
			} else {
				myDebug(  0, ">>> ARTIKEL NICHT MEHR IN EK LISTE...............: %s <br /> \n", $myArtikel->ArtikelNr) ;
				if ( $propose) {
					proposeArtikelUpdate( $myArtikel) ;
				}
			}

			myDebug(  9, "    Relationen...................................: %5d <br /> \n", $relsFound) ;
			if ( $debugLevel > 0)
				printf( "\n") ;

		}
	
		myDebug(  0, "Anzahl Artikel in der Datenbank..................: " . $coreNumRows . "<br/> \n") ;
	}

}
function	loopEKPreisR( $_db, $_artikel, $_liefNr, & $_eks) {

	//

	global	$propose ;

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from EKPreisR " ; 
	$coreQuery	.=	"where ArtikelNr = '" . $_artikel->ArtikelNr . "' " ; 
	$sqlResult      =       mysql_query( $coreQuery, $_db) ; 
	$coreNumRows        =       mysql_affected_rows( $_db) ; 

	if ( !$sqlResult) { 

		myDebug( 0, "could not perform basic Db query for Artikel, will simply die ... \n") ;

	} else if ( $coreNumRows < 1) {

		myDebug( 0, "Keine Einkaufspreis Relationen fuer Artikel %s <br />\n",
					$_artikel->ArtikelNr) ;

		if ( $propose)
			proposeEKPreisR( $_artikel, $_liefNr) ;

	} else {

		// Fuer jeden Eintrag in "ProdGr" muss eine statische Seite gebaut werden

		$myLieferant	=	new Lieferant() ;
		$myEKPreisR	=	new EKPreisR() ;

		while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

			$myEKPreisR->assignFromRow( $coreRow) ;

			$myLieferant->LieferantNr	=	$myEKPreisR->LiefNr ;
			$myLieferant->fetchFromDb( $_db) ;

			if ( $myLieferant->_valid == 1) {

				myDebug(  9, "    Wird geliefert von...........................: %s <br /> \n", $myLieferant->FirmaName1) ;
				myDebug(  9, "      unter der Artikel Nr.......................: %s <br /> \n", $myEKPreisR->LiefArtNr) ;

				loopArtikelEKPreis( $_db, $myEKPreisR, $_eks) ;

			} else {

				myDebug(  0, ">   Kein Lieferant fuer Artikel Nr...............: %s <br /> \n", $_artikel->ArtikelNr) ;

			}

		}
	
	}

	return $coreNumRows ;
}

function	loopArtikelEKPreis( $_db, $_eKPreisR, & $_eks) {

	//

	global	$lowestEK ;

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from ArtikelEKPreis " ; 
	$coreQuery	.=	"where LiefNr = '" . $_eKPreisR->LiefNr . "' " ; 
	$coreQuery	.=	"and LiefArtNr = '" . $_eKPreisR->LiefArtNr . "' " ; 
	$sqlResult      =       mysql_query( $coreQuery, $_db) ; 
	$coreNumRows        =       mysql_affected_rows( $_db) ; 

	$_eks	=	$coreNumRows ;

	if ( !$sqlResult) { 

		myDebug( 0, "could not perform basic Db query for Artikel, will simply die ... \n") ;

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
			if ( $myArtikelEKPreis->Menge <= 0) {
				myDebug(  9, "        Mengenangabe == 0........................: <br /> \n") ;
			}
			myDebug(  9, "        zu.......................................: %9.2f / %d <br /> \n", $myArtikelEKPreis->Preis, $myArtikelEKPreis->MengeFuerPreis) ;

			if ( $myArtikelEKPreis->Preis < $lowestEK || $lowestEK == 0) {
				$lowestEK	=	$myArtikelEKPreis->Preis / $myArtikelEKPreis->MengeFuerPreis ;
			}

		}
	
	}

	return $coreNumRows ;
}

function	loopVKPreis( $_db, $_artikel) {

	//

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

			if ( $myVKPreis->Preis < $lowestVK || $lowestVK == 0) {
				$lowestVK	=	$myVKPreis->Preis ;
			}

		}
	
	}

	return $coreNumRows ;
}

function	proposeEKPreisR( $_artikel, $_liefNr) {

	//

	global	$sqlFile ;

	//

	fwrite( $sqlFile,
			sprintf( "insert into EKPreisR ( ArtikelNr, LiefNr, LiefArtNr, KalkBasis) value( '%s', '%s', '%s', 1);\n",
				$_artikel->ArtikelNr,
				$_liefNr,
				substr( $_artikel->ArtikelNr, 4)
			)
		) ;

}

function	proposeArtikelUpdate( $_artikel) {

	//

	global	$sqlFile ;

	//

	if ( $_artikel->LieferStatus <> 9 && $_artikel->LieferStatus <> 9) {
		fwrite( $sqlFile,
				sprintf( "update Artikel set LieferStatus = 9 where ArtikelNr = '%s';\n",
					$_artikel->ArtikelNr
				)
			) ;
	}

}

function	proposeVKPreisUpdate( $_db, $_artikel) {

	//

	global	$sqlFile ;

	//

	$myArtikel	=	new Artikel ;
	$myEKPreisR	=	new EKPReisR ;
	$myArtikelEKPreis	=	new ArtikelEKPreis ;
	$myVKPreis	=	new VKPreis ;
	$myLiefRabatt	=	new LiefRabatt ;

	$myArtikel->ArtikelNr	=	$_artikel->ArtikelNr ;
	$myArtikel->fetchFromDb( $_db) ;

	$myEKPreisR->ArtikelNr	=	$_artikel->ArtikelNr ;
	$myEKPreisR->fetchFromDb( $_db) ;

	if ( $myEKPreisR->_valid == 1) {

		$where	=	"where LiefNr='" . $myEKPreisR->LiefNr . "' and LiefArtNr='" . $myEKPreisR->LiefArtNr . "' " ;
		$myArtikelEKPreis->fetchFromDbWhere( $_db, $where) ;

		$myVKPreis->ArtikelNr	=	$_artikel->ArtikelNr ;
		$myVKPreis->fetchFromDb( $_db) ;

		$myVKPreis->ArtikelNr	=	$_artikel->ArtikelNr ;
		$myVKPreis->fetchFromDb( $_db) ;

		if ( $myVKPreis->_valid == 1) {

			$hr	=	calcHR( $myArtikelEKPreis->Preis, $myArtikelEKPreis->LiefVKPreis) ;
			$mm	=	calcMM( $hr) ;
			$sp	=	calcSP( $myArtikelEKPreis->LiefVKPreis, $myArtikelEKPreis->Preis, $mm) ;
			$vk	=	calcVK( $myArtikelEKPreis->Preis, $mm, $sp) ;
			$vk	*=	$myVKPreis->MengeProVPE / $myArtikelEKPreis->MengeFuerPreis ;

			if ( $vk < 10.0) {
				$vk	=	round( $vk, 2) ;
			} else if ( $vk < 100.0) {
				$vk	=	round( $vk, 1) ;
			} else {
				$vk	=	round( $vk, 0) ;
			}

			fwrite( $sqlFile,
					sprintf( "update VKPreis set Preis = '%.2f' where Id = %d and ArtikelNr='%s';\n",
						$vk,
						$myVKPreis->Id,$myArtikel->ArtikelNr
					)
				) ;
		}
	}

}

function	loopEKPreis100( $_db, $_func) {

	//

	global	$debugLevel ;
	global	$sqlFile ;
	global	$propose ;
	global	$artNr ;
	global	$liefNr ;
	global	$artNrPrefix ;
	global	$lowestEK ;
	global	$lowestVK ;
	global	$keyDate ;

	//

	$myArtikel	=	new Artikel() ; 
	$myArtikelEKPreis	=	new ArtikelEKPreis() ; 
	$myEKPreisR	=	new EKPreisR() ; 

	//

	$invArtikel	=	0 ;

	// generate the basic artikel data

	$coreQuery	=	"select * " ; 
	$coreQuery	.=	"from ArtikelEKPreis " ; 
	$coreQuery	.=	"where LiefNr like '" .$liefNr . "' " ; 
	$coreQuery	.=	"order by LiefArtNr ASC " ; 
	$sqlResult      =       mysql_query( $coreQuery, $_db) ; 
	$coreNumRows        =       mysql_affected_rows( $_db) ; 

	if ( !$sqlResult) { 

		myDebug( 0, "could not perform basic Db query for Artikel, will simply die ... \n") ;

	} else if ( $coreNumRows < 1) {

		myDebug( 0, "Keine Artikel erfasst ... \n") ;

	} else {

		// Fuer jeden Artikel

		$artikelEKs	=	0 ;

		while ($coreRow = mysql_fetch_assoc( $sqlResult)) {

			$myArtikelEKPreis->assignFromRow( $coreRow) ;

			myDebug(  1, "  Betrachte Artikel Nr...........................: %s <br /> \n", $myArtikelEKPreis->LiefArtNr) ;
			myDebug(  9, "    Bezeichung 1.................................: %s <br /> \n", $myArtikelEKPreis->LiefArtText) ;

			switch ( $_func) {
			case	"101"	:

				$myEKPreisR->LiefNr	=	$myArtikelEKPreis->LiefNr ;
				$myEKPreisR->LiefArtNr	=	$myArtikelEKPreis->LiefArtNr ;

				$whereClause	=	"where LiefNr = '" . $myArtikelEKPreis->LiefNr . "' " ;
				$whereClause	.=	"and LiefArtNr = '" . $myArtikelEKPreis->LiefArtNr . "' " ;
				$whereClause	.=	"limit 1 " ;

				$myEKPreisR->fetchFromDbWhere( $_db, $whereClause) ;
				if ( $myEKPreisR->_valid == 1) {
					$myArtikel->ArtikelNr	=	$myEKPreisR->ArtikelNr ;
					$myArtikel->fetchFromDb( $_db) ;
					if ( $myArtikel->_valid == 1) {
					} else {
					}
				} else {
					myDebug(  9, "   --> Keine Einkaufspreis Relation (EKPreisR)...: %s / %s <br /> \n",
							$myEKPreisR->LiefNr, $myEKPreisR->LiefArtNr) ;
					if ( $propose) {
						proposeNeuArtikel( $_db, $myArtikelEKPreis) ;
					}
				}
				break ;
			}
		}
	
		myDebug(  0, "Anzahl Artikel in der Datenbank..................: " . $coreNumRows . "<br/> \n") ;
		myDebug(  0, "  davon fehlerhaft...............................: " . $invArtikel . "<br/> \n") ;
	}

}

function	proposeNeuArtikel( $_db, $_artikelEKPreis) {

	//

	global	$debugLevel ;
	global	$sqlFile ;
	global	$artNr ;
	global	$lastLiefArtNr ;
	global	$liefNr ;
	global	$artNrPrefix ;
	global	$keyDate ;

	//

	if ( strcmp( $lastLiefArtNr, $_artikelEKPreis->LiefArtNr) <> 0) {
		if ( strlen( $_artikelEKPreis->LiefArtText) == 0) {
			$_artikelEKPreis->LiefArtText	=	$artNrPrefix . "." . $_artikelEKPreis->LiefArtNr ;
		}

		fwrite( $sqlFile, sprintf( "SQL: insert into Artikel (ArtikelNr, RevCode, ArtikelBez1, ErfDatum, ShopArtikel, MwstSatz) ")) ;
		fwrite( $sqlFile, sprintf( "values ('%s.%s', '%s', '%s', '%s', 3, 'A') ; \n",
					$artNrPrefix, $_artikelEKPreis->LiefArtNr,
					"",
					$_artikelEKPreis->LiefArtText,
					$keyDate
			)) ;
		fwrite( $sqlFile, sprintf( "SQL: insert into EKPreisR (ArtikelNr, RevCode, LiefNr, LiefArtNr, KalkBasis, LiefArtText) ")) ;
		fwrite( $sqlFile, sprintf( "values ('%s.%s', '%s', '%s', '%s', %d, '%s') ; \n",
					$artNrPrefix, $_artikelEKPreis->LiefArtNr,
					"",
					$liefNr,
					$_artikelEKPreis->LiefArtNr,
					$_artikelEKPreis->Menge,
					$_artikelEKPreis->LiefArtText
			)) ;
		$hr	=	calcHR( $_artikelEKPreis->Preis, $_artikelEKPreis->LiefVKPreis) ;
		$mm	=	calcMM( $hr) ;
		$sp	=	calcSP( $_artikelEKPreis->LiefVKPreis, $_artikelEKPreis->Preis, $mm) ;
		$vk	=	calcVK( $_artikelEKPreis->Preis, $mm, $sp) ;
		$vk	*=	1 / $_artikelEKPreis->MengeFuerPreis ;
		if ( $vk < 10.0) {
		} else if ( $vk < 100.0) {
			$vk	=	round( $vk, 1) ;
		} else {
			$vk	=	round( $vk, 0) ;
		}
		fwrite( $sqlFile, sprintf( "SQL: insert into VKPreis (ArtikelNr, RevCode, Menge, MengeProVPE, Preis, Waehrung) ")) ;
		fwrite( $sqlFile, sprintf( "values ('%s.%s', '%s', %d, %d, %7.2f, '%s') ; \n",
					$artNrPrefix, $_artikelEKPreis->LiefArtNr,
					"",
					$_artikelEKPreis->Menge,
					1,
					$vk,
					"EUR"
			)) ;
	} else {
echo "Hello .......................... \n" ;
	}

	$lastLiefArtNr	=	$_artikelEKPreis->LiefArtNr ;

}
function	proposeABFix( $_db, $_artikel, $_abs) {

	//

	global	$debugLevel ;
	global	$sqlFile ;
	global	$artNr ;
	global	$lastLiefArtNr ;
	global	$liefNr ;
	global	$artNrPrefix ;
	global	$keyDate ;

	//

	if ( $_abs == 0) {

		fwrite( $sqlFile, sprintf( "SQL: insert into ArtikelBestand (ArtikelNr, RevCode) ")) ;
		fwrite( $sqlFile, sprintf( "values ('%s', '%s') ; \n",
					$_artikel->ArtikelNr,
					""
			)) ;
	} else if ( $_abs > 1) {

		fwrite( $sqlFile, sprintf( "SQL: delete from ArtikelBestand where ArtikelNr = '%s' and RevCode='%s' limit %d ; \n",
					$_artikel->ArtikelNr,
					$_artikel->RevCode,
					$_abs - 1
			)) ;
	} else {
	}

}

?>
