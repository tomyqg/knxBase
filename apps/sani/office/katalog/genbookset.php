<?php

//
// Diese Script generiert den gesamten "statischen" web shop
//
 
$pathC	=	"../phpconfig" ;
$pathI	=	"../phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

require_once( "config.inc.php") ; 

printf( "Arguments: $argv[0]\n") ;

createCatalogueSet( $argv) ;

function	createCatalogueSet( $_argv) {
	//
	$myProdGr	=	new ProdGr() ; 
	for ( $valid = $myProdGr->_firstFromDb( "SeitenTyp = '1' OR ProdGrNr = '' ") ; 
		$valid ;
		$valid = $myProdGr->_nextFromDb()) {
		printf( "%s: Erzeuge Katalog fuer (%s)%s als: %s.pdf \n",
				$_argv[0],
				$myProdGr->ProdGrNr,
				$myProdGr->ProdGrName,
				makeFileName( $myProdGr->ProdGrName)) ;
		$cmd	=	"./genbook -n \"" . makeFileName( $myProdGr->ProdGrName) . "\" -C " . $myProdGr->ProdGrNr . " " ;
		printf( "%s: command [%s] \n", $argv[0], $cmd) ;
		system( $cmd) ;
		system( "mv \"" . makeFileName( $myProdGr->ProdGrName) . "\".pdf pdf ") ;
		system( "rm \"" . makeFileName( $myProdGr->ProdGrName) . "\".* ") ;
	}
}

?>
