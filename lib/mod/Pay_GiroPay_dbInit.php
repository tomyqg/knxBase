<?php
/**
 * Default initialization data if carrier is not yet in the database
 * 
 *
INSERT INTO `Texte` ( `Name`, `RefNr`, `Sprache`, `Volltext`, `Volltext2`)
	VALUES ( 'PAY_GiroPay_InfoSys_1900', '', 'de_DE', 'Fehler bei Transaktionsstart. Die uebergebenen Daten sind unbrauchbar.', '' );
INSERT INTO `Texte` ( `Name`, `RefNr`, `Sprache`, `Volltext`, `Volltext2`)
	VALUES ( 'PAY_GiroPay_InfoSys_1910', '', 'de_DE', 'Fehler/Abbruch bei Transaktionsstart. BLZ ungueltig oder BLZ-Suche abgebrochen.', '' );
INSERT INTO `Texte` ( `Name`, `RefNr`, `Sprache`, `Volltext`, `Volltext2`)
	VALUES ( 'PAY_GiroPay_InfoSys_3100', '', 'de_DE', 'Benutzerseitiger Abbruch bei der Bezahlung.', '' );
INSERT INTO `Texte` ( `Name`, `RefNr`, `Sprache`, `Volltext`, `Volltext2`)
	VALUES ( 'PAY_GiroPay_InfoSys_4000', '', 'de_DE', 'Bezahlung erfolgreich.', '' );
INSERT INTO `Texte` ( `Name`, `RefNr`, `Sprache`, `Volltext`, `Volltext2`)
	VALUES ( 'PAY_GiroPay_InfoSys_4500', '', 'de_DE', 'Unbekanntes Transaktionsende. Zahlungseingang muss anhand der Kontoumsaetze ueberprueft werden.', '' );
INSERT INTO `Texte` ( `Name`, `RefNr`, `Sprache`, `Volltext`, `Volltext2`)
	VALUES ( 'PAY_GiroPay_InfoSys_4900', '', 'de_DE', 'Bezahlung nicht erfolgreich.', '' );
INSERT INTO `Texte` ( `Name`, `RefNr`, `Sprache`, `Volltext`, `Volltext2`)
	VALUES ( 'PAY_GiroPay_InfoUser_3100', '', 'de_DE', 'Sie haben die Bezahlung ueber Giropay abgebrochen.', '' );
INSERT INTO `Texte` ( `Name`, `RefNr`, `Sprache`, `Volltext`, `Volltext2`)
	VALUES ( 'PAY_GiroPay_InfoUser_4000', '', 'de_DE', 'Vielen Dank fuer die Bezahlung ueber Giropay.', '' );
INSERT INTO `Texte` ( `Name`, `RefNr`, `Sprache`, `Volltext`, `Volltext2`)
	VALUES ( 'PAY_GiroPay_InfoUser_4500', '', 'de_DE', 'Es ist ein unbekannter Fehler bei der Bezahlung ueber Giropay aufgetreten. Bitte ueberpruefen Sie Ihre Kontoumsaetze und wenden Sie sich bei Fragen an uns.', '' );

 */
FDbg::dumpL( 0x00000001, "Carr_DHL_Paket_dbinit.php: loading") ;
FDbg::dumpL( 0x00000001, "Carr_DHL_Paket.dbinit.php: loaded") ;
?>