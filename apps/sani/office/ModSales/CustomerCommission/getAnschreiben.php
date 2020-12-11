<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
$myCuDlvr	=	new CuDlvr( $_GET['CuDlvrNo']) ;
$myHTML	=	$myCuDlvr->getAnschAsHTML() ;
echo $myHTML ;
?>
