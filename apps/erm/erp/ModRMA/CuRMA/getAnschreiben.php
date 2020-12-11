<?php
require_once( "config.inc.php") ;
$myCuRMA	=	new CuRMA( $_GET['CuRMANr']) ;
$myHTML	=	$myCuRMA->getAnschAsHTML() ;
echo "<div style=\"width: 500px; height: 300px; overflow: auto;\">"
		. $myHTML
		. "</div>" ;
?>
