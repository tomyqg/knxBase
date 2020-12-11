<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
error_log( "suchen.php::main: begin") ;
if ( isset( $_COOKIE['Suchbegriff']))
	echo $_COOKIE['Suchbegriff'] ;
else
	echo FTr::tr( "Searchterm...") ;
error_log( "suchen.php::main: end") ;
?>
