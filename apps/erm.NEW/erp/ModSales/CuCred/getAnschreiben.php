<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$myKdGuts	=	new KdGuts( $_GET['KdGutsNr']) ;
$myHTML	=	$myKdGuts->getAnschAsHTML() ;
echo "<div style=\"width: 500px; height: 300px; overflow: auto;\">"
		. $myHTML
		. "</div>" ;
?>
