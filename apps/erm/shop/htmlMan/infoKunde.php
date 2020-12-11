<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "option.php") ;
require_once( "mzlibrary.inc.php") ;
require_once( "validateEMail.inc.php") ;
error_log( "infoCustomer.php") ;
//
// determine 'how' we are called, can be any of:
//	'kundeAllgmein.php'	customer general data
//	'kundeKontakte.php'	customer contacts
//	'kundeMerkzettel.php'	customer Merkzettel
//

$baseName	=	$_SERVER['REQUEST_URI'] ;
$baseNames	=	explode( "/", $baseName) ;
if ( isset( $baseNames[1])) {
	$baseName	=	$baseNames[1] ;
}

error_log( "Customer.php: baseName := '$baseName'") ;
echo "<!--	baseName: $baseName		-->\n" ;

//
//	exec_anmelden.php
//
//	This script is a bit special in that it needs to know what do after successful:
//	- identification of an existing customer, or
//	- successful registration of a new customer
//	Therefor input about the next page/step is required in for of the $_POST['_nextFunc'] parameter.
//
//	Login can be initiated through various means:
//	1. by clicking the 'Login' button on the Merkzettel page, in this case the next page - after login - will be the Merkzettel page again, or
//	2. during checkout when the 'Merkzettel speichern' button is clicked, in this case the next page shall be be the Merkzettel paga again.
//	3. during checkout when the 'Als Bestellung aufgeben' button is clicked, in this case the next page shall be the 2nd step of the ordering flow, or
//	4. by clicking the 'Anmelden' button on the left hand menu, in this case the next page - after login - shall be the 'CustomernInformation' page, or
//
//	Required input parameters:
//	$_POST['_nextFunc']	Determines the next page to be loaded after successful login. If this variable is NOT set it is assumed that the
//				'Anmelden' page was called from the left hand menu (see 4. above). The subsequent function will in this case
//				be the 'CustomernInformation'. In all other cases the parameter needs to be set accordingly, namely as follows:
//				case 1.:	'merkzettel.php'
//				case 2.:	'merkzettel.php'
//				case 3.:	'bestellen2.php'
//				case 4.:	'kunde.php'	implicit
//
//	Optionional input parameters:
//
//
//

$actionResult	=	0 ;

if ( isset( $_POST['_nextFunc'])) {
	$nextFunction	=	$_POST['_nextFunc'] ;
} else {
	$nextFunction	=	"/AllgemeineDaten.php" ;
}

//
// IF customer is valid
//	check if we have been called with any action to be performed
//

if ( isset( $_POST['_neuCustomer'])) {
	echo "<!--	_neuCustomer				-->\n" ;
	include( "regCustomer.php") ;
} else if ( isset( $_POST['_neuesPasswort'])) {
	echo "<!--	_neuesPasswort			-->\n" ;
	include( "npwCustomer.php") ;
}

if ( $myCustomer->_valid) {
	error_log( "infoCustomer.php: myCustomer->_valid == true") ;
	if ( isset( $_POST['_action'])) {
		if ( strcmp( $_POST['_action'],"updCustomer") == 0) {
			$myCustomer->FirmaName1	=	$_POST['_IFirmaName1'] ;
			$myCustomer->FirmaName2	=	$_POST['_IFirmaName2'] ;
			$myCustomer->Strasse	=	$_POST['_IStrasse'] ;
			$myCustomer->Hausnummer	=	$_POST['_IHausnummer'] ;
			$myCustomer->PLZ	=	$_POST['_IPLZ'] ;
			$myCustomer->Ort	=	$_POST['_IOrt'] ;
			$myCustomer->Land	=	$_POST['_ILand'] ;
			$myCustomer->Telefon	=	$_POST['_ITelefon'] ;
			$myCustomer->FAX	=	$_POST['_IFAX'] ;
			$myCustomer->Mobil	=	$_POST['_IMobil'] ;
			$myCustomer->eMail	=	$_POST['_IeMail'] ;
			$myCustomer->updateInDb() ;
			if ( $myCustomer->_valid) {
				$actionResult	=	110 ;			// Customerndaten erfolgreich upgedatet
			} else {
				$actionResult	=	111 ;			// Customerndaten NICHT erfolgreich upgedatet
			}

		//
		// insert: additional customer contact
		//

		} else if ( strcmp( $_POST['_action'], "insKontakt") == 0) {
			$insCustomerContact	=	new CustomerContact() ;
			$insCustomerContact->eMail	=	$_POST['_IIeMail'] ;
			$insCustomerContact->_valid	=	true ;		// kleiner "missbrauch"
			if ( ! validEmail( $insCustomerContact->eMail)) {
				$insCustomerContact->_valid	=	false ;
				$actionResult	=	202 ;			// Customerndaten NICHT erfolgreich upgedatet
			}
			if ( $insCustomerContact->_valid) {
				$insCustomerContact->CustomerNo	=	$myCustomer->CustomerNo ;
				$insCustomerContact->getCustomerContactNo() ;
				$insCustomerContact->Anrede	=	$_POST['_IIAnrede'] ;
				$insCustomerContact->Titel	=	$_POST['_IITitel'] ;
				$insCustomerContact->Vorname	=	$_POST['_IIVorname'] ;
				$insCustomerContact->Name	=	$_POST['_IIName'] ;
				$insCustomerContact->Telefon	=	$_POST['_IITelefon'] ;
				$insCustomerContact->FAX	=	$_POST['_IIFAX'] ;
				$insCustomerContact->Mobil	=	$_POST['_IIMobil'] ;
				$insCustomerContact->storeInDb() ;
				if ( $insCustomerContact->_valid) {
					if ( $_POST['_INewPassword1'] == $_POST['_INewPassword2']) {
						$insCustomerContact->updatePassword( $_POST['_INewPassword1']) ;
						$actionResult	=	200 ;			// Customerndaten erfolgreich upgedatet
					} else {
						$actionResult	=	203 ;			// Customerndaten NICHT erfolgreich upgedatet
					}
				} else {
					$actionResult	=	201 ;			// Customerndaten NICHT erfolgreich upgedatet
				}
			}

		//
		// chgPasswort: Passwort aendern
		//

		} else if ( strcmp( $_POST['_action'], "chgPasswort") == 0) {

			//
			// change: customer password
			//

			$dummyCustomer	=	new Customer() ;
			$dummyCustomer->CustomerNo	=	$myCustomer->CustomerNo ;
			$dummyCustomer->identifyCustomer( $_POST['_IOldPassword']) ;
			if ( $dummyCustomer->_valid) {
				if ( strlen( $_POST['_INewPassword1']) >= 5) {
					if ( $_POST['_INewPassword1'] == $_POST['_INewPassword2']) {
						$dummyCustomer->updatePassword( $_POST['_INewPassword1']) ;
						$actionResult	=	600 ;	// password updated successfully
					} else {
						$actionResult	=	602 ;	// passwords don't match
					}
				} else {
					$actionResult	=	603 ;		// new password too short
				}
			} else {
				$actionResult	=	601 ;			// Customerkontaktdaten NICHT erfolgreich upgedatet
			}

		//
		// upd/del: additional customer contact
		//

		} else if ( strcmp( $_POST['_action'], "fncKontakt") == 0) {

			//
			// update: customer contact
			//

			if ( isset( $_POST['_upd'])) {
				$updCustomerContact	=	new CustomerContact() ;
				$updCustomerContact->Id	=	intval( $_POST['_IIId']) ;
				$updCustomerContact->fetchFromDbById() ;
				if ( $updCustomerContact->_valid) {
					$updCustomerContact->eMail	=	$_POST['_IIeMail'] ;
					if ( ! validEmail( $updCustomerContact->eMail)) {
						$updCustomerContact->_valid	=	false ;
						$actionResult	=	212 ;			// Customerndaten NICHT erfolgreich upgedatet
					}
				} else {
					$actionResult	=	213 ;			// Customerkontaktdaten erfolgreich upgedatet
				}
				if ( $updCustomerContact->_valid) {
					$updCustomerContact->Id	=	intval( $_POST['_IIId']) ;
					$updCustomerContact->Funktion	=	$_POST['_IIFunktion'] ;
					$updCustomerContact->AdrZusatz	=	$_POST['_IIAdrZusatz'] ;
					$updCustomerContact->Anrede	=	$_POST['_IIAnrede'] ;
					$updCustomerContact->Titel	=	$_POST['_IITitel'] ;
					$updCustomerContact->Vorname	=	$_POST['_IIVorname'] ;
					$updCustomerContact->Name	=	$_POST['_IIName'] ;
					$updCustomerContact->Telefon	=	$_POST['_IITelefon'] ;
					$updCustomerContact->FAX	=	$_POST['_IIFAX'] ;
					$updCustomerContact->Mobil	=	$_POST['_IIMobil'] ;
					$updCustomerContact->updateInDb() ;
					if ( $updCustomerContact->_valid) {
						if ( isset( $_POST['_INewPassword1'])) {
							if ( strlen( $_POST['_INewPassword1']) > 0) {
								if ( $_POST['_INewPassword1'] == $_POST['_INewPassword2']) {
									$updCustomerContact->updatePassword( $_POST['_INewPassword1']) ;
									$actionResult	=	210 ;			// Customerndaten erfolgreich upgedatet
								} else {
									$actionResult	=	214 ;			// Customerndaten NICHT erfolgreich upgedatet
								}
							}
						} else {
							$actionResult	=	210 ;			// Customerndaten erfolgreich upgedatet
							$myCustomerContact->Funktion	=	$updCustomerContact->Funktion ;
							$myCustomerContact->AdrZusatz	=	$updCustomerContact->AdrZusatz ;
							$myCustomerContact->Anrede	=	$updCustomerContact->Anrede ;
							$myCustomerContact->Titel	=	$updCustomerContact->Titel ;
							$myCustomerContact->Vorname	=	$updCustomerContact->Vorname ;
							$myCustomerContact->Name	=	$updCustomerContact->Name ;
							$myCustomerContact->Telefon	=	$updCustomerContact->Telefon ;
							$myCustomerContact->FAX	=	$updCustomerContact->FAX ;
							$myCustomerContact->Mobil	=	$updCustomerContact->Mobil ;
						}
					} else {
						$actionResult	=	211 ;			// Customerkontaktdaten NICHT erfolgreich upgedatet
					}
				}

			//
			// delete: customer contact
			//

			} else if ( isset( $_POST['_del'])) {
				$delCustomerContact	=	new CustomerContact() ;
				$delCustomerContact->Id	=	intval( $_POST['_IIId']) ;
				$delCustomerContact->removeFromDb() ;
				if ( ! $delCustomerContact->_valid) {
					$actionResult	=	220 ;			// Customerkontaktdaten erfolgreich upgedatet
				} else {
					$actionResult	=	221 ;			// Customerkontaktdaten NICHT erfolgreich upgedatet
				}
			}

		//
		// update: vermerk
		//

		} else if ( strcmp( $_POST['_action'], "updateMZVermerk") == 0) {
			$myTempMerkzettel	=	new Merkzettel() ;
			$myTempMerkzettel->Id	=	$_POST['_IId'] ;
			$myTempMerkzettel->fetchFromDbById() ;
			if ( $myTempMerkzettel->_valid) {
				$myTempMerkzettel->Vermerk	=	$_POST['_IVermerk'] ;
				$myTempMerkzettel->updateInDb() ;
			} else {
				$actionResult	=	411 ;			// Customerkontaktdaten NICHT erfolgreich upgedatet
			}

		//
		// activate: merkzettel
		//

		} else if ( strcmp( $_POST['_action'], "activateMZ") == 0) {
			$myMerkzettel->MerkzettelUniqueId	=	$_POST['_IMerkzettelUniqueId'] ;
			$myMerkzettel->fetchFromDbByUniqueId() ;
			if ( $myMerkzettel->_valid) {
				$myMerkzettel->Status	=	1 ;			// Entwertung der Merkzettels ungueltig machen
				$myMerkzettel->updateInDb() ;
				$mySession->MerkzettelUniqueId	=	$myMerkzettel->MerkzettelUniqueId ;
				$mySession->MerkzettelNr	=	$myMerkzettel->MerkzettelNr ;
				$mySession->updateInDb() ;
				if ( $mySession->_valid) {
					$actionResult	=	410 ;			// Customerkontaktdaten erfolgreich upgedatet
				} else {
					$actionResult	=	412 ;			// Customerkontaktdaten erfolgreich upgedatet
				}
			} else {
				$actionResult	=	411 ;			// Customerkontaktdaten NICHT erfolgreich upgedatet
			}

		//
		// remove: merkzettel
		//

		} else if ( strcmp( $_POST['_action'], "removeMZ") == 0) {
			$myTempMerkzettel	=	new Merkzettel() ;
			$myTempMerkzettel->MerkzettelUniqueId	=	$_POST['_IMerkzettelUniqueId'] ;
			$myTempMerkzettel->fetchFromDbByUniqueId() ;
			if ( $myTempMerkzettel->_valid) {
				$myTempMerkzettel->CustomerNo	=	"ENTWERTET" ;
				$myTempMerkzettel->Status	=	9 ;
				$myTempMerkzettel->updateInDb() ;
				$actionResult	=	420 ;			// Customerkontaktdaten erfolgreich upgedatet
			} else {
				$actionResult	=	421 ;			// Customerkontaktdaten NICHT erfolgreich upgedatet
			}

		//
		// switch: memorize and create new merkzettel
		//

		} else if ( strcmp( $_POST['_action'], "switchMZ") == 0) {
			if ( $myMerkzettel->_valid) {
				$myMerkzettel->Status	=	7 ;
				$myMerkzettel->updateInDb() ;
				$mySession->MerkzettelUniqueId	=	"" ;
				$mySession->MerkzettelNr	=	"" ;
				$mySession->updateInDb() ;
				$actionResult	=	430 ;			// Customerkontaktdaten erfolgreich upgedatet
				$myMerkzettel->_valid	=	false ;
			} else {
				$actionResult	=	431 ;			// Customerkontaktdaten NICHT erfolgreich upgedatet
			}

		//
		// switch: memorize and create new merkzettel
		//

		} else if ( strcmp( $_POST['_action'], "abmelden") == 0) {
			$mySession->CustomerNo	=	"" ;
			$mySession->CustomerContactNo	=	"" ;
			$mySession->updateInDb() ;
			$myCustomer->clear() ;
			if ( $myMerkzettel->_valid) {
				$myMerkzettel->CustomerNo	=	"" ;
				$myMerkzettel->CustomerContactNo	=	"" ;
				$myMerkzettel->updateInDb() ;
			}
		} else {
			$actionResult	=	901 ;				// Customerndaten NICHT erfolgreich upgedatet
		}
	}
	if ( strcmp( $baseName, "AllgemeineDaten.php") == 0) {			// will be called on data update

		include( "infoCustomerAllgemein.php") ;

	} else if ( strcmp( $baseName, "AllgemeineDaten.html") == 0) {	// will be called on data display

		include( "infoCustomerAllgemein.php") ;

	} else if ( strcmp( $baseName, "AnmeldenCustomerninfo.html") == 0) {

		include( "infoCustomerAllgemein.php") ;

	} else if ( strcmp( $baseName, "kundeKontakte.html") == 0) {

		include( "infoCustomerContacte.php") ;

	} else if ( strcmp( $baseName, "GespeicherteMerkzettel.html") == 0) {

		include( "infoCustomerMerkzettel.php") ;

	} else if ( strcmp( $baseName, "Passwort.php") == 0) {			// will be called on password change

		include( "infoCustomerPasswort.php") ;

	} else if ( strcmp( $baseName, "Passwort.html") == 0) {			// will be called on password display

		include( "infoCustomerPasswort.php") ;

	} else if ( strcmp( $baseName, "Bestellungen.html") == 0) {

		include( "infoCustomerBestellungen.php") ;

	} else if ( strcmp( $baseName, "Anfragen.html") == 0) {

		include( "infoCustomerAnfrage.php") ;

	} else if ( strcmp( $baseName, "Angebote.html") == 0) {

		include( "infoCustomerAngebot.php") ;

	} else if ( strcmp( $baseName, "Abmelden.html") == 0) {

		$mySession->CustomerNo	=	"" ;
		$mySession->CustomerContactNo	=	"" ;
		$mySession->MerkzettelNr	=	"" ;
		$mySession->MerkzettelUniqueId	=	"" ;
		$mySession->updateInDb() ;
		$myCustomer->clear() ;

		if ( $myMerkzettel->_valid) {
			$myMerkzettel->CustomerNo	=	"" ;
			$myMerkzettel->CustomerContactNo	=	"" ;
			$myMerkzettel->updateInDb() ;
		}

		include( "infoCustomerAbmeldung.php") ;

	} else {

	}

	//
	//
	//

	if ( isset( $_POST['_action'])) {
		echo $_POST['_action'] . "<br/>\n" ;
		switch ( $actionResult) {

		// Meldungen im Hinblick auf Customer Daten
		// 100-109	Insert (existiert hier jedoch nicht)
		// 110-119	Update
		// 120-129	Delete (existiert hier jedoch nicht)

		case	0	:
			break ;
		case	110	:
			echo FTr::tr( "110: The Customer data has been updated successfully.") ;
			break ;
		case	111	:
			echo FTr::tr( "111: A problem occured while updateing the customer data.") ;
			break ;
		case	200	:
			echo FTr::tr( "200: The Customer Contact was added successfully.") ;
			break ;
		case	201	:
			echo FTr::tr( "201: A problem occured while adding the customer contact.") ;
			break ;
		case	202	:
			echo FTr::tr( "202: The E-Mail address of the new customer contact is invalid.") ;
			break ;
		case	203	:
			echo FTr::tr( "203: Passwords of customer don't match.") ;
			break ;
		case	210	:
			echo FTr::tr( "210: The Customer Contact data has been updated successfully.") ;
			break ;
		case	211	:
			echo FTr::tr( "211: A problem occured while updateing the customer contact data.") ;
			break ;
		case	212	:
			echo FTr::tr( "212: The E-Mail address of the new customer contact is invalid.") ;
			break ;
		case	213	:
			echo FTr::tr( "213: PANIC: Somebody seems to be fucking around with the form data.") ;
			break ;
		case	214	:
			echo FTr::tr( "203: Passwords of customer contact don't match.") ;
			break ;
		case	220	:
			echo FTr::tr( "220: The Customer Contact data was deleted successfully.") ;
			break ;
		case	221	:
			echo FTr::tr( "221: A problem occured while deleting the customer contact data.") ;
			break ;
		case	400	:
			echo FTr::tr( "400: The Customer data has been updated successfully.") ;
			break ;
		case	401	:
			echo FTr::tr( "401: A problem occured while updateing the customer data.") ;
			break ;
		case	410	:
			echo FTr::tr( "410: New wishlist has been activated.") ;
			break ;
		case	411	:
			echo FTr::tr( "411: A problem occured while loading the wishlist.") ;
			break ;
		case	412	:
			echo FTr::tr( "412: A problme occured while updating the session.") ;
			break ;
		case	420	:
			echo FTr::tr( "420: The wishlist has been finally deprecated.") ;
			break ;
		case	421	:
			echo FTr::tr( "421: A problem occured while loading the wishlist.") ;
			break ;
		case	430	:
			echo FTr::tr( "430: The wishlist was de-activated and a new one generated.") ;
			break ;
		case	431	:
			echo FTr::tr( "431: A problem occured while de-activaing the wishlist.") ;
			break ;
		case	501	:
			echo FTr::tr( "501: An invalid function was called.") ;
			break ;
		case	600	:
			echo FTr::tr( "600: The password was changed successfully.") ;
			break ;
		case	601	:
			echo FTr::tr( "601: The password is not correct.") ;
			break ;
		case	602	:
			echo FTr::tr( "602: The passwords don't match.") ;
			break ;
		case	603	:
			echo FTr::tr( "603: The new password is too short (min. 5 characters).") ;
			break ;
		default	:
			echo FTr::tr( "DEF: No function was called. Strange ....") . "<br/>" ;
			break ;
		}
	}
} else {
	echo "<!--	==> frag_kunde		-->\n" ;
	include( "frag_kunde.php") ;
}
?>
