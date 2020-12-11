<?php

$pathC	=	"../phpconfig" ;
$pathI	=	"../phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

require_once( "config.inc.php") ; 

$myObj	=	new FDbObject( "KdBestPosten", array( "KdBestNr", "PosNr")) ;

$myObj->dump() ;

$myObj->setKey( array( "KdBestNr"=>"000001","PosNr"=>120)) ;

$myObj->dump() ;

$myObj->fetchFromDb() ;

$myObj->dump() ;

?>