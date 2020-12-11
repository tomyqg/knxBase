<?php

$pathC	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpconfig" ;
$pathI	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI . PATH_SEPARATOR . "/usr/lib/php/pear");

require_once( "config.inc.php") ;

$myMerchant	=	new Merchant() ;
if ( $myMerchant->setKey( "ebay")) {
	echo $myMerchant->now() . "\n" ;
	echo $myMerchant->before( $myMerchant->now()) . "\n" ;
} else {
	error_log( "can't find merchant") ;
}

?>
