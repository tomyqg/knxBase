#!/usr/bin/php5

<?php

//
// Diese Script generiert den gesamten "statischen" web shop
//
 
$pathC	=	"../phpconfig" ;
$pathI	=	"../phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

require_once( "config.inc.php") ; 

$myCat	=	new Catalog() ;
$myCat->createCatalog( "11:11:01") ;

?>
