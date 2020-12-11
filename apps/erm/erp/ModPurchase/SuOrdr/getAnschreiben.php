<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$mySuOrdr	=	new SuOrdr( $_GET['SuOrdrNo']) ;
$myHTML	=	$mySuOrdr->getEMailAsHTML() ;
echo $myHTML ;
?>
