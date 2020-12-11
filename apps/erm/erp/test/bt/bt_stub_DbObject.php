#!/usr/bin/php
<?php

$pathC	=	"../../phpconfig" ;
$pathI	=	"../../phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;

require_once( "config.inc.php") ;
require_once( "base/DbObject.php") ;

FDbg::setLevel( 0xffffffff) ;
FDbg::enable() ;

/**
 * iterate through a DbObject
 */
$newObj	=	new FDbObject( "Adr", "AdrNr") ;
foreach ( $newObj as $key => $adr) {
	echo( "Key: '$key' => '$adr->FirmaName1' \n") ;
}

/**
 * iterate through ArtKomp's
 */
$newObj	=	new ArtKomp() ;
foreach ( $newObj as $key => $artKomp) {
	echo( "Key: '$key' => '$artKomp->ArtikelNr', '$artKomp->CompArtikelNr' \n") ;
}

/**
 * iterate through ArtKomp's and join in some additional fields from another table
 */
$newObj	=	new ArtKomp() ;
$newObj->addCol( "ArtikelBez1", "VARCHAR") ;
$newObj->setIterJoin( "LEFT JOIN Artikel AS A ON A.ArtikelNr = C.CompArtikelNr", "A.ArtikelBez1") ;
foreach ( $newObj as $key => $artKomp) {
	echo( "Key: '$key' => '$artKomp->ArtikelNr', '$artKomp->CompArtikelNr', '$artKomp->ArtikelBez1' \n") ;
}

?>