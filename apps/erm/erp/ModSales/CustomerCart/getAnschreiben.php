<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$myCuOrdr	=	new CuOrdr( $_GET['CuOrdrNo']) ;
$myHTML	=	$myCuOrdr->getAnschAsHTML() ;
echo "<div style=\"width: 500px; height: 300px; overflow: auto;\">"
		. $myHTML
		. "</div>" ;
?>
