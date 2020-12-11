<?php
/**
 * 
 */
$_SERVER["DOCUMENT_ROOT"]	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/r2/httpdocs/" ;
require_once( "../../../Config/config.inc.php") ;
/**
 *
 */
echo "UStId: " . $myConfig->UStId . "\n" ;
echo "Company Name: " . $myConfig->CompanyName . "\n" ;
echo "Company Name 2: " . $myConfig->CompanyName2 . "\n" ;
?>