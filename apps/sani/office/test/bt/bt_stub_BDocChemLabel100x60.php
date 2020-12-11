<?php

require_once( "FDbg.php") ;
require_once( "FDb.php") ;

require_once( "conf_pathes.inc.php") ;

require_once( "base/BDocChemLbl100x60.php") ;

FDbg::setLevel( 0xffffffff) ;			// alles tracen
FDbg::enable() ;

/**
 *
 */
$newLabel	=	new BDocChemLbl100x60() ;
$newLabel->setSize( BDoc::DocSizeLbl100x60) ;
$newLabel->setType( BDoc::DocTypeLbl) ;

$newLabel->begin() ;


$newLabel->addSymbol( BDocChemLbl100x60::SymbolC) ;
$newLabel->addSymbol( BDocChemLbl100x60::SymbolXI) ;
$newLabel->addSymbol( BDocChemLbl100x60::SymbolF) ;
$newLabel->addSymbol( BDocChemLbl100x60::SymbolF) ;
$newLabel->addSymbol( BDocChemLbl100x60::SymbolF) ;
$newLabel->addMyHead1( "Acetaldehyd") ;
$newLabel->addMyText( " ") ;
$newLabel->addMyText( "  kjsdfhksjdfh skdjfh skdhskdjhskdjf skdjhksdjhksdh ksdh skdjh skdhskdjh skdjhskdjhskdjh skdjfskdjhskdjhskdjfhskdfsk jhdkjfdh jhdjf hdj fhjdf jdhfdh") ;
$newLabel->addArticleNr( "123456") ;
//$newRegLetter->addMyText( "Hello, world 1.") ;
//$newRegLetter->addMyText( "Hello, world 12.") ;
//$newRegLetter->addMyText( "Hello, world 123.") ;
//$newRegLetter->addMyText( "Hello, world 1234.") ;
//$newRegLetter->addMyText( "Hello, world 12345.") ;
$newLabel->end( "/tmp/3731.pdf") ;

/**
 *
 */

/**
 *
 */
