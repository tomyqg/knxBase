<?php

$path="/srv/www/vhosts/mein-mikroskop.de/httpdocs/phpconfig" . PATH_SEPARATOR . "/srv/www/vhosts/mein-mikroskop.de/httpdocs/phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $path) ;

require_once( "pkgs/platform/FDbg.php") ;
require_once( "pkgs/platform/FDb.php") ;

FDbg::setLevel( 0xffffffff) ;
FDbg::setHTMLMode( 0xffffffff) ;
FDbg::enable() ;

echo "Diagnose: <br/>" ;

FDb::query( "SHOW TABLES ;") ;
$numRows	=	FDb::rowCount() ;
echo "Database contains: $numRows Tabellen <br/>" ;

?>
