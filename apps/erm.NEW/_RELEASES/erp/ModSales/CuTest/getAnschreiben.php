<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$myKdLeih	=	new KdLeih( $_GET['KdLeihNr']) ;
$myHTML	=	$myKdLeih->getAnschAsHTML() ;
echo "<div style=\"width: 500px; height: 300px; overflow: auto;\">"
		. $myHTML
		. "</div>" ;
?>
