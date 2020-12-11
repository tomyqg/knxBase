<?php
require_once( "config.inc.php") ;
$myProj	=	new Proj( $_GET['ProjNr']) ;
$myHTML	=	$myProj->getAnschAsHTML() ;
echo $myHTML ;
?>