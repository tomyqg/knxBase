#!/usr/bin/php5
<?php

require_once( "global.inc.php") ;
require_once( "mail.inc.php") ;
require_once( "parameter.inc.php") ;

$database   =   connectDatabase( $dbHost, $dbUser, $dbPasswd) ;
if ( ! mysql_select_db( $dbName)) {
	printf( "couldn't select database ... serious ... <br/>") ;
}

// Testmail mit einfachem Message Contents

$newMail	=	new mimeMail( "MODIS-GmbH <karl@modis-gmbh.eu>",
					"karl@modis-gmbh.eu",
					"MODIS GmbH <karl@modis-gmbh.eu>",
					"Testmail - bitte ignorieren",
					"") ;

$newMail->addData( "simple", "Einfache E-Mail, nur txt-Text") ;
$newMail->send() ;

// Testmail mit Text- und HTML-content

$newMail	=	new mimeMail( "MODIS-GmbH <karl@modis-gmbh.eu>",
					"karl@modis-gmbh.eu",
					"MODIS GmbH <karl@modis-gmbh.eu>",
					"Testmail - bitte ignorieren",
					"") ;

$myText	=	new mimeData( "multipart/alternative") ;
$myText->addData( "text/plain", "Einfacher Text, kombiniert txt-/HTML-Text\n") ;
$myText->addData( "text/html", "<html><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">HTML Text</body></html>\n") ;

$newMail->addData( "multipart/alternative", $myText->getData(), $myText->getHead()) ;
$newMail->send() ;

// Testmail mit einfachem Text und PDF Attachment

$newMail	=	new mimeMail( "MODIS-GmbH <karl@modis-gmbh.eu>",
					"karl@modis-gmbh.eu",
					"MODIS GmbH <karl@modis-gmbh.eu>",
					"Testmail - bitte ignorieren",
					"") ;

$myText	=	new mimeData( "multipart/alternative") ;
$myText->addData( "text/plain", "(1) Hello, world \n") ;
$myText->addData( "text/html", "<html><body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">(2)Hello, world</body></html>\n") ;

$myBody	=	new mimeData( "multipart/mixed") ;
$myBody->addData( "multipart/mixed", $myText->getAll()) ;
$myBody->addData( "application/pdf", $archivPath . "Mahnungen/000001.pdf", "Erinnerung.pdf", true) ;

$newMail->addData( "multipart/mixed", $myBody->getData(), $myBody->getHead()) ;
$newMail->send() ;

exit() ;

?>
