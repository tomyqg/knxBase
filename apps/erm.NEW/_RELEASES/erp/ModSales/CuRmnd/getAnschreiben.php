<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$myKdMahn	=	new KdMahn( $_GET['KdMahnNr']) ;
$myHTML	=	$myKdMahn->getAnschAsHTML() ;
echo "<div style=\"width: 500px; height: 300px; overflow: auto;\">"
		. $myHTML
		. "</div>" ;
?>
