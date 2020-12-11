<?php
$pathC	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpconfig" ;
$pathI	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);
	error_reporting(E_ALL ^ E_NOTICE);;

	require_once 'EbatNs/EbatNs_ServiceProxy.php';
	require_once 'EbatNs/EbatNs_Logger.php';
	require_once 'EbatNs/GetItemRequestType.php';
	require_once 'EbatNs/ItemType.php';
	
	$session = new EbatNs_Session('ebay.config.php');
	
	$cs = new EbatNs_ServiceProxy($session);
	$cs->setHandler('ItemType', 'handleItem');
	
	$logger = new EbatNs_Logger(true);
	// $logger->_debugXmlBeautify = true;
	// $logger->_debugSecureLogging = false;
	
	$cs->attachLogger($logger);
	
	$req = new GetItemRequestType();
	$req->setItemID(320835015683);
	$req->setDetailLevel($Facet_DetailLevelCodeType->ReturnAll);
	
	$res = $cs->GetItem($req);
	echo "<pre>";
	if ($res->getAck() != $Facet_AckCodeType->Success)
	{
		echo "we got a failure<br>";
		foreach ($res->getErrors() as $error)
		{
			echo "#" . $error->getErrorCode() . " " . htmlentities($error->getShortMessage()) . "/" . htmlentities($error->getLongMessage()) . "<br>"; 
		}	
	}
	else
	{
		//#type $item ItemType
		$item = $res->getItem();
		echo "ShippingTerms : " . $item->getShippingTerms() . "<br>";		
		
		print_r($item);
	}
	
	function handleItem($type, & $data)
	{
		echo "Hello Item<pre><br>";
		// print_r($data);
		return false;
	}
?>
