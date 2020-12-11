<?php

require_once( "FDbg.php") ;
require_once( "FDb.php") ;

require_once( "BDocRegLetter.php") ;

FDbg::setLevel( 0xffffffff) ;			// alles tracen
FDbg::enable() ;

/**
 *
 */
$newRegLetter	=	new BDocRegLetter() ;
$newRegLetter->setSize( BDoc::DocSizeA4) ;
$newRegLetter->setType( BDoc::DocTypeRegLetter) ;

$newRegLetter->setRcvr( 1, "1st address line") ;
$newRegLetter->setRcvr( 2, "2nd address line") ;
$newRegLetter->setRcvr( 3, "3rd address line") ;
$newRegLetter->setRcvr( 4, "4th address line") ;
$newRegLetter->setRcvr( 5, "5th address line") ;
$newRegLetter->setRcvr( 6, "") ;

$newRegLetter->setInfo( 1, "Rechnung Nr.:", "") ;
$newRegLetter->setInfo( 2, "Datum:", "") ;
$newRegLetter->setInfo( 3, "Kunde Nr.:", "") ;
$newRegLetter->setInfo( 4, "Auftragsbest. Nr.:", "") ;
$newRegLetter->setInfo( 5, "", "") ;
$newRegLetter->setInfo( 6, "Kundenseitig:", "") ;
$newRegLetter->setInfo( 7, "Ref. Nr.:", "") ;
$newRegLetter->setInfo( 8, "Ref. Datum:", "") ;

$newRegLetter->setRef( "Rechnung") ;

$newRegLetter->begin() ;

$newRegLetter->addMyText( "Hello, world.") ;
$newRegLetter->addMyText( "Hello, world 1.") ;
$newRegLetter->addMyText( "Hello, world 12.") ;
$newRegLetter->addMyText( "Hello, world 123.") ;
$newRegLetter->addMyText( "Hello, world 1234.") ;
$newRegLetter->addMyText( "Hello, world 12345.") ;
$newRegLetter->end( "/tmp/3731.pdf") ;

/**
 *
 */

/**
 *
 */
