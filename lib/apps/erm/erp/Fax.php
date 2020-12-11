<?php

function	analyzeFaxNr( $_faxNr) {
	$myConfig	=	EISSCoreObject::__getConfig() ;
	$plusFound	=	false ;
	$bracesFound	=	false ;
	$inCountryPrefix	=	false ;
	$inBraces	=	false ;
	$nrInBraces	=	false ;
	$ownCountry	=	true ;
	$countryPrefix	=	"" ;
	$buffer	=	"" ;
	$finalFaxNr	=	"" ;

	for ( $i=0 ; $i<strlen( $_faxNr) ; $i++) {
		switch ( $_faxNr[ $i]) {
		case	"+"	:
			FDbg::dumpL( 0x01000000, "faxtools::analyzeFaxNr: + erkannt") ;
			if ( ! $plusFound) {
				$plusFound	=	true ;
				$inCountryPrefix	=	true ;
			} else {
				FDbg::dumpL( 0x01000000, "faxtools::analyzeFaxNr: FEHLER: doppeltes + erkannt") ;
				return( "") ;
			}
			break ;
		case	"("	:
			FDbg::dumpL( 0x01000000, "faxtools::analyzeFaxNr: ( erkannt") ;
			$inBraces	=	true ;
			break ;
		case	")"	:
			FDbg::dumpL( 0x01000000, "faxtools::analyzeFaxNr: ) erkannt") ;
			$inBraces	=	false ;
			if ( $ownCountry) {
				$finalFaxNr	.=	$nrInBraces ;
			}
			break ;
		case	"0"	:
		case	"1"	:
		case	"2"	:
		case	"3"	:
		case	"4"	:
		case	"5"	:
		case	"6"	:
		case	"7"	:
		case	"8"	:
		case	"9"	:
			if ( $inCountryPrefix) {
				if ( $_faxNr[ $i] != "0")
					$countryPrefix	.=	$_faxNr[ $i] ;
				if ( strcmp( $countryPrefix, "49") == 0) {
					FDbg::dumpL( 0x01000000, "faxtools::analyzeFaxNr: Country-Prefix fuer Deutschland erkannt") ;
					$inCountryPrefix	=	false ;
				} else if ( strcmp( $countryPrefix, "31") == 0) {
					FDbg::dumpL( 0x01000000, "faxtools::analyzeFaxNr: Country-Prefix fuer Niederlande erkannt") ;
					$inCountryPrefix	=	false ;
				} else if ( strcmp( $countryPrefix, "43") == 0) {
					FDbg::dumpL( 0x01000000, "faxtools::analyzeFaxNr: Country-Prefix fuer Oesterreich erkannt") ;
					$inCountryPrefix	=	false ;
				} else if ( strcmp( $countryPrefix, "44") == 0) {
					FDbg::dumpL( 0x01000000, "faxtools::analyzeFaxNr: Country-Prefix fuer England erkannt") ;
					$inCountryPrefix	=	false ;
				}
				if ( $inCountryPrefix == false) {
					if ( strcmp( $countryPrefix, $myConfig->fax->countryPrefix) == 0) {
						$ownCountry	=	true ;
					} else {
						$ownCountry	=	false ;
						$finalFaxNr	.=	$myConfig->fax->countryDialPrefix ;
						$finalFaxNr	.=	$countryPrefix ;
					}
				}
			} else if ( $inBraces) {
				$nrInBraces	.=	$_faxNr[ $i] ;
			} else {
				$finalFaxNr	.=	$_faxNr[ $i] ;
			}
			break ;
		case	"-"	:
		case	" "	:
		case	"/"	:
			break ;
		default	:
			FDbg::dumpL( 0x01000000, "faxtools::analyzeFaxNr: Fehler: Ungueltiges Zeichen '%s' in Faxnummer erkannt !") ;
			return( "") ;
			break ;
		}

	}

	FDbg::dumpL( 1, "ERGEBNIS..: Analysierte Fax Nr. lautet [%s] !", $finalFaxNr) ;
	
	return( $finalFaxNr) ;

}

//C
//C Funktion:	sendeFax( $dest, $pdffiles, $pdfnames, $subject)
//C
//C Sendet ein FAX, bestehend aus ein oder mehreren PDF Dateien $pdffiles mit den Namen $pdfnames an die Telefax Nummer $dest. $subject
//C wird als Subject: der E-Mail verwendet falls ein Fax Server benutzt wird, z.B. Xaranet oder aehnlich.
//C

function	sendFax( $dest, $pdffiles, $pdfnames, $subject, $owner, $tries=3) {
	$result	=	false ;
	$myConfig	=	EISSCoreObject::__getConfig() ;
	switch ( $myConfig->fax->service) {
	case	"none"	:
		break ;
	case	"local"	:
		$systemCmd	=	"sendfax -s a4 -D -n " ;
		if ( strlen( $owner) > 0) {
			$systemCmd	.=	"-o " . $owner . " " ;
		}
		$systemCmd	.=	"-t " . $tries . " " ;
		$systemCmd	.=	"-d \"" . $dest . "\" " ;
		if ( is_array( $pdffiles)) {
			foreach ( $pdffiles as $file) {
				$systemCmd	.=	$file . " " ;
			}
		} else {
			$systemCmd	.=	$pdffiles . " " ;
		}
		error_log( "SystemCmd: [" . $systemCmd . "]") ;
		$faxResult	=	system( $systemCmd) ;
		error_log( "SystemRes: [" . $faxResult . "]") ;
		break ;
	case	"xara-simple"	:
		break ;
	case	"xara-xml"	:

		$xmlCall	=	"<request>\n" ;
		$xmlCall	.=	"<auth>\n" ;
		$xmlCall	.=	"<account>" . $myConfig->fax_xara->accountNo . "</account>\n" ;
		$xmlCall	.=	"<password>" . $myConfig->fax_xara->accountPasswd . "</password>\n" ;
		$xmlCall	.=	"</auth>\n" ;
		$xmlCall	.=	"<fax>\n" ;
		$xmlCall	.=	"<fax-id>" . $subject . "</fax-id>\n" ;
		$xmlCall	.=	"<to>" . $dest . "</to>\n" ;
		$xmlCall	.=	"<from>" . $myConfig->fax->faxNo . "</from>\n" ;
		$xmlCall	.=	"<station-id>" . $myConfig->fax->faxId . "</station-id>\n" ;
		$xmlCall	.=	"<retry>3</retry>\n" ;
		$xmlCall	.=	"<header>" . $ownFaxId . "</header>\n" ;
		$xmlCall	.=	"</fax>\n" ;
		$xmlCall	.=	"</request>\n" ;

		$newMail	=	new mimeMail( "Karl-Heinz Welter <khw@wimtecc.de>",
							$myConfig->fax->serverEMail,
							"Karl-Heinz Welter <karl@modis-gmbh.eu>",
							"",		// " . $faxNr,
							"Bcc: mail@modis-gmbh.eu\n") ;

		$myBody	=	new mimeData( "multipart/mixed") ;
		$myBody->addData( "text/plain", $xmlCall) ;
		for ( $il0 = 0 ; $il0 < ( count( $pdffiles) - 1 ) ; $il0++) {
			echo "Adding: [" . $pdffiles[$il0] . "] as " . $pdfnames[$il0] . "<br/>" ;
			$myBody->addData( "application/pdf", $pdffiles[$il0], $pfdnames[$il0], false) ;
		}
		echo "Adding: [" . $pdffiles[$il0] . "] as " . $pdfnames[$il0] . "<br/>" ;
		$myBody->addData( "application/pdf", $pdffiles[$il0], $pdfnames[$il0], true) ;

		$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
		$mailSendResult	=	999 ;
		$mailSendResult	=	$newMail->send() ;

		$result	=	true ;

		break ;
	case	"placetel"	:

		$newMail	=	new mimeMail( "MODIS Fax <welter@modis-gmbh.eu>",
							$myConfig->fax->serverEMail,
							"MODIS Fax <welter@modis-gmbh.eu>",
							$dest,		// " . $faxNr,
							"Bcc: ".$myConfig->eMail->Archive."\n") ;

		$myBody	=	new mimeData( "multipart/mixed") ;
		$myBody->addData( "text/plain", $myConfig->fax->serverPassword."\n") ;
		if ( is_array( $pdffiles)) {
			for ( $il0 = 0 ; $il0 < ( count( $pdffiles) - 1 ) ; $il0++) {
				error_log( "Adding: [" . $pdffiles[$il0] . "] as " . $pdfnames[$il0]) ;
				$myBody->addData( "application/pdf", $pdffiles[$il0], $pfdnames[$il0], false) ;
			}
			error_log( "Fax.php::sendFax(): after for-loop") ;
			error_log( "Adding: [" . $pdffiles[$il0] . "] as " . $pdfnames[$il0]) ;
			$myBody->addData( "application/pdf", $pdffiles[$il0], $pdfnames[$il0], true) ;
		} else {
			$myBody->addData( "application/pdf", $pdffiles, $pdfnames, true) ;
		}

		$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
		$mailSendResult	=	$newMail->send() ;

		$result	=	true ;

		break ;
	}

}

?>
