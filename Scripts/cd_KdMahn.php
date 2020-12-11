#!/usr/bin/php5
<?php

require_once( "global.inc.php") ;
require_once( "parameter.inc.php") ;
require_once( "genKdMahnDoc.php") ;
require_once( "Kunde.inc.php") ;
require_once( "KundeKontakt.inc.php") ;
require_once( "KdBest.inc.php") ;
require_once( "KdRech.inc.php") ;
require_once( "KdMahn.inc.php") ;

//

$database   =   connectDatabase( $dbHost, $dbUser, $dbPasswd) ;
if ( ! mysql_select_db( $dbName)) {
	printf( "couldn't select database ... serious ... <br/>") ;
}

//

myTrace(  0, "cd_KdMahn: starting up ... <br /> \n") ;

//

$secPerDay	=	24 * 60 * 60 ;
$zahlTageInUnix	=	30 * $secPerDay ;
$sktoTageInUnix	=	10 * $secPerDay ;

$myKunde	=	new Kunde() ;
$myKundeKontakt	=	new KundeKontakt() ;
$myKdRech	=	new KdRech() ;

// wir schauen uns alle offenen Rechnungen an ...

$query  =       "select * from KdRech " ;
$query  .=      "where Status <> 9 " ;
$query  .=      "and BezahltBetrag = 0 " ;
$query  .=      "order by KdRechNr " ;

$sqlResult =       mysql_query( $query, $database) ;

if ( !$sqlResult) {
	echo "PANIC:Query failed <br/>\n" ;
	echo "Query: [" . $query . "]<br/>\n" ;
	die() ;
} else {

	// get the names of the result field including the `AS` denominator

	$anzahlUeberf	=	0 ;

	$unbez	=	0.0 ;
	$ueberf	=	0.0 ;

	$numrows        =       mysql_affected_rows( $database) ;
	while ($row = mysql_fetch_assoc( $sqlResult)) {

		$myKdRech->assignFromRow( $row) ;
		$myKdRech->recalcGesamtPreis( $database) ;
		$myKdRech->fetchFromDb( $database) ;

		$myKunde->KundeNr	=	$myKdRech->KundeNr ;
		$myKunde->fetchFromDb( $database) ;

		$myKundeKontakt->KundeNr	=	$myKdRech->KundeNr ;
		$myKundeKontakt->KundeKontaktNr	=	$myKdRech->KundeKontaktNr ;
		$myKundeKontakt->fetchFromDbByAll( $database) ;

		switch ( $myKdRech->Status) {
		case	0	:			// Rechnung ist neu ( heisst: noch nicht verschickt )
			break ;

		case	1	:			// Rechnung ist bezahlt, alles im gruenen Bereich
		case	5	:			// Rechnung ist bezahlt, alles im gruenen Bereich
			$rechDate	=	strptime( $myKdRech->Datum, "%Y-%m-%d") ;
			$rechDateUnix	=	mktime( 12, 0, 0, intval( $rechDate['tm_mon'])+1, intval( $rechDate['tm_mday']), intval( $rechDate['tm_year'])) ;
			$tageOffen	=	mktime() - $rechDateUnix ;

			if ( $myKdRech->BezahltBetrag > 0) {		// schon bezahlt, Status noch nicht upgedated

			} else if ( $tageOffen > $zahlTageInUnix) {

				$anzahlUeberf++ ;

				$unbez	+=	floatval( $myKdRech->GesamtPreis) ;
				$ueberf	+=	floatval( $myKdRech->GesamtPreis) ;

				createKdMahn( $database, $myKdRech, $tageOffen) ;

			} else if ( $tageOffen > $sktoTageInUnix) {

				$unbez	+=	floatval( $myKdRech->GesamtPreis) ;

			} else {					// noch nicht bezahlt

				$unbez	+=	floatval( $myKdRech->GesamtPreis) ;

			}
			break ;

		case	9	:			// Rechnung ist bezahlt, alles im gruenen Bereich
			break ;

		case	99	:			// Rechnung ist storniert, sollten wir eigentlich nicht haben
			break ;

		}

	}

	printf( "Gesamtbetrag der unbezahlten Rechnungen: netto      %9.2f <br />\n", $unbez) ;
	printf( "Gesamtbetrag der unbezahlten Rechnungen: brutto     %9.2f <br />\n", $unbez * 1.19) ;
	printf( "Gesamtbetrag der unbezahlten Rechnungen: brutto/Sk  %9.2f <br />\n", $unbez * 1.19 * 0.98) ;

	printf( "Anzahl der &uuml;berf&auml;lligen Rechnungen:                  %9d <br />\n", $anzahlUeberf) ;
	printf( "Gesamtbetrag der &uuml;berf&auml;lligen Rechnungen: netto      %9.2f <br />\n", $ueberf) ;
	printf( "Gesamtbetrag der &uuml;berf&auml;lligen Rechnungen: brutto     %9.2f <br />\n", $ueberf * 1.19) ;

	$mainResult	=	0 ;


}

myTrace(  0, "cd_KdMahn: finishing ... <br /> \n") ;

//
// zuerst muessen wir pruefen, ob fuer diese Rechnung bereits eine Mahnung verschickt wurde. Wenn nicht, dann
// fangen wir in Mahnstufe 1 an.
//
//
//
//
//
//

function	createKdMahn( $_database, $_kdRech, $_tageOffen) {
	$myKunde	=	new Kunde() ;
	$myKdBest	=	new KdBest() ;
	$newKdMahn	=	new KdMahn() ;

	printf( "Rechnung Nr....: [%s] ist ueberfaellig <br /> \n", $_kdRech->KdRechNr) ;

	$myKdBest->KdBestNr	=	$_kdRech->KdBestNr ;
	$myKdBest->fetchFromDb( $_database) ;

	$myKunde->KundeNr	=	$myKdBest->KundeNr ;
	$myKunde->fetchFromDb( $_database) ;

	$mahnungen	=	getCount( $_database, " from KdMahn where KdRechNr = '" . $_kdRech->KdRechNr . "' ") ;

	if ( $mahnungen == 0) {

		printf( "..bisher noch keine Mahnung geschrieben <br /> \n") ;
		printf( "..das holen wir jetzt nach... <br /> \n") ;

		$newKdMahn->getKdMahnNr( $_database) ;

		$newKdMahn->Datum	=	$myKdBest->today() ;		// get todays date (independent from object!)
		$newKdMahn->KdRechNr	=	$_kdRech->KdRechNr ;

		$newKdMahn->Prefix	=	getKdMahnPrefixS0( $_kdRech) ;

		$newKdMahn->Stufe	=	0 ;		// 0= Erinnerung

		$myText	=	date( "Ymd/Hi") . ": Automatisch erstellt am " . $keyDate . "\n" ;
		$newKdMahn->Rem1	=	$myText ;

		$newKdMahn->storeInDb( $_database) ;

		$pdfName	=	genKdMahnDoc( $_database, $newKdMahn->KdMahnNr, "K.-H. Welter", true) ;

		$pdfFile	=	$myKdMahn->path->Archive . "KdMahn/" . $newKdMahn->KdMahnNr . ".pdf" ;

		$systemCmd	=	"pdftk " . $pdfName . " " ;
		$systemCmd	.=	$myKdMahn->path->Archive . "KdRech/" . $newKdMahn->KdRechNr . "-Kopie.pdf " ;
		$systemCmd	.=	" cat output " . $pdfFile ;
		system( $systemCmd) ;

		$mailText	=	"Es wurde automatisch eine Mahnung  der Stufe '0' (Erinnerung) wie folgt erstellt: \n\n" ;
		$mailText	.=	" \n" ;
		$mailText	.=	"Mahnung Nr....:  " . $newKdMahn->KdMahnNr . " \n" ;
		$mailText	.=	" \n" ;
		$mailText	.=	"Kunde Nr......:  " . $myKunde->KundeNr . " \n" ;
		$mailText	.=	"  Name........:  " . $myKunde->FirmaName1 . " \n" ;
		$mailText	.=	"  Bestellung..:  " . $myKdBest->KdBestNr . " \n" ;
		$mailText	.=	"  Rechnung....:  " . $_kdRech->KdRechNr . " \n" ;
		$mailText	.=	"\n" ;
		$mailText	.=	"Bitte pruefen und verschicken. \n" ;
		$mailText	.=	"\n" ;
		$mailText	.=	"Falls kein Anhang existiert (oder diese leer ist) gab es ein Problem bei dem Einbeziehen der " ;
		$mailText	.=	"Rechnung. In diesem Fall bitte Mahnung und Rechung separat ausdrucken und verschicken. \n" ;
		$mailText	.=	"\n" ;

		infoMail( "karl@modis-gmbh.eu", "Mahnung", $mailText, "", $pdfFile) ;

	} else {

		printf( "..es wurde bereits eine Mahnung geschrieben <br /> \n") ;

	}

	
}

function	getKdMahnPrefixS0( $_kdRech) {

	$text	=	"" ;
	$text	.=	sprintf( "<div>\n") ;
	$text	.=	sprintf( "<p>sicherlich haben Sie ".chr(252)."bersehen, dass unsere ") ;
	$text	.=	sprintf( "Rechnung Nr. %s \n", $_kdRech->KdRechNr) ;
	$rechDate	=	strptime( $_kdRech->Datum, "%Y-%m-%d") ;
	$rechDateUnix	=	mktime( 12, 0, 0,
					intval( $rechDate['tm_mon'])+1,
					intval( $rechDate['tm_mday']),
					intval( $rechDate['tm_year'])) ;
	$rechDateStr	=	date( "d.m.Y", $rechDateUnix) ;
	$text	.=	sprintf( "vom %s ", $rechDateStr) ;
	$rechDate	=	strptime( $_kdRech->Datum, "%Y-%m-%d") ;
	$rechDateUnix	=	mktime( 12, 0, 0,
					intval( $rechDate['tm_mon'])+1,
					intval( $rechDate['tm_mday']),
					intval( $rechDate['tm_year'])) ;
	$zahlDateUnix	=	$rechDateUnix + ( 30 * 24 * 60 * 60 ) ;
	$zahlDateStr	=	date( "d.m.Y", $zahlDateUnix) ;
	$text	.=	sprintf( "am %s ", $zahlDateStr) ;
	$text	.=	sprintf( "f".chr(228)."llig gewesen w".chr(228)."re.</p>\n") ;
	$text	.=	sprintf( "<p>$ $</p>\n") ;
	$text	.=	sprintf( "<p>Wir m".chr(246)."chten Sie daher bitten, \n") ;
	$text	.=	sprintf( "den f".chr(228)."lligen Betrag umgehend auf unser unten ") ;
	$text	.=	sprintf( "genanntes Konto bei \n") ;
	$text	.=	sprintf( "der Kreis\\\\-sparkasse K".chr(246)."ln zu ".chr(252)."berweisen.</p>\n") ;
	$text	.=	sprintf( "<p>Die zugrunde liegende Rechnung ist als ") ;
	$text	.=	sprintf( "Kopie beigef".chr(252)."gt.\n") ;
	$text	.=	sprintf( "Sollten Sie den f".chr(228)."lligen Betrag in der ") ;
	$text	.=	sprintf( "Zwischenzeit ".chr(252)."berwiesen haben, \n") ;
	$text	.=	sprintf( "so betrachten Sie dieses Schreiben als ") ;
	$text	.=	sprintf( "gegenstandslos.</p>\n") ;
	$text	.=	sprintf( "</div>\n") ;

	return $text ;

}

function	infoMail( $_rcvr, $_subject, $_text, $_htmlText, $_pdfFile) {

	$newMail	=	new mimeMail( "MODIS-GmbH <karl@modis-gmbh.eu>",
						$_rcvr,
						"MODIS GmbH <karl@modis-gmbh.eu>",
						$_subject,
						"") ;

	$myText	=	new mimeData( "multipart/alternative") ;
	
	if ( strlen( $_text) > 0) {
		$myText->addData( "text/plain", $_text) ;
	} else {
		$myText->addData( "text/plain", "Kein plain text, siehe HTML fuer weitere Daten ... \n") ;
	}
	if ( strlen( $_htmlText) > 0) {
		$myText->addData( "text/html", $_htmlText) ;
	} else {
		$myText->addData( "text/html", "<html><body>".$_text."</body></html>") ;
	}

	$myBody	=	new mimeData( "multipart/mixed") ;
	$myBody->addData( "multipart/mixed", $myText->getAll(), "", false) ;
	$myBody->addData( "application/pdf", $_pdfFile, "Mahnung.pdf", true) ;

	$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
	$newMail->send() ;

}

?>
