<?php

$pathC	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpconfig" ;
$pathI	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI);

	set_time_limit(0);
	require_once 'EbatNs/EbatNs_ServiceProxy.php';
	require_once 'EbatNs/GetCategoriesRequestType.php';
	require_once 'EbatNs/GetCategoriesResponseType.php';
	require_once 'EbatNs/CategoryType.php';
	
	$session = new EbatNs_Session('ebay.config.php');
	
	$cs = new EbatNs_ServiceProxy($session);
	$cs->_debugLogDestination = '';
	$handler = new handlerMethods($cs);

	$req = new GetCategoriesRequestType();
	$req->CategorySiteID = 77;
	$req->LevelLimit = 3;
	$req->DetailLevel = 'ReturnAll';
	
	//#type $res GetCategoriesResponseType
	$res = $cs->GetCategories($req);
	
	class handlerMethods
	{
		// import-need to pass in by ref !
		function handlerMethods(& $cs)
		{
			$cs->setHandler('CategoryType', array(& $this, 'handleCategory'));
		}
		
		function handleCategory($type, & $Category)
		{
			//#type $Category CategoryType
			echo "(" . $Category->CategoryID . "/" . $Category->CategoryParentID[0] . ") " . $Category->CategoryName . "<br>";
			return true;
		}
	}
?>
