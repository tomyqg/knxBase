<?php

$pathC	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpconfig" ;
$pathI	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

	require_once 'EbatNs/EbatNs_ServiceProxy.php';
	require_once 'EbatNs/GeteBayOfficialTimeRequestType.php';

	$session = new EbatNs_Session('ebay.config.php');
	$cs = new EbatNs_ServiceProxy($session);
	
	$req = new GeteBayOfficialTimeRequestType();
	
	$res = $cs->GeteBayOfficialTime($req);
	echo "<pre>";
	print_r($res);
?>
