<?php

$pathC	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpconfig" ;
$pathI	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI . PATH_SEPARATOR . "/usr/lib/php/pear");

require_once( "config.inc.php") ;
require_once( "HTTP/Request2.php") ;

$req	=	new HTTP_Request2( "https://api.ebay.com/ws/api.dll", HTTP_Request2::METHOD_POST) ;
$req->setHeader( array(
					'X-EBAY-API-COMPATIBILITY-LEVEL' => '759',
					'X-EBAY-API-DEV-NAME' => 'c8e517c3-8e46-45b3-9284-992e95be4d7a',
					'X-EBAY-API-APP-NAME' => 'wimtecc60-6e7a-45a0-bd96-c5fd6d140e7',
					'X-EBAY-API-CERT-NAME' => 'd44bcd04-1b69-45f3-b6ad-f730f5dbd6b4',
					'X-EBAY-API-CALL-NAME' => 'GetSellerTransactions',
					'X-EBAY-API-SITEID' => '0',
					'Content-Type' => 'text/xml')) ;
$reqBody	=	"<?xml version='1.0' encoding='utf-8'?>" .
    			"<GetSellerTransactionsRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">" .
    			"  <RequesterCredentials>" .
    			"    <eBayAuthToken>" .
				"AgAAAA**AQAAAA**aAAAAA**Vaw3Tw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wBmYShAJSFoQmdj6x9nY+seQ**El4BAA**AAMAAA**mOc1siEx3Ym9xIt2omsS+Sl/UeauyHVF8cyEdH8YVSY7db+25ZQGZ7ltpqyhxIj/opb22Wm3kHzBhQkdzdGmM4g/OdKSM9Aprkf74GqcWeBJzFPcqMd7c27u29HkvxHFCXfVIR5amgfsjGIhqW5QX1zkv+mAOmHjkMHL20UqbJyGLAJnttXwAPtg+aCCUdvj1ox6FzXqylcgR3CLyMPyQVFGlGTA39wUShYGnMIzCbaHuFB/xf7Lru30xosjIjy7MytNUDTK0aKVlrkofAzBqp6z5yEaLOvA5UXh4e23NimIH1kwT6nGKCjYYyNB5LPKFg0dsfGXwCyUlsvWmcjvK44ikStF9hUAOocwCeICJ6NmJJrJl2tKzjCapBbmJhMRHDTycN/XRqlDHlG456UXVUU8KM8/QfqqAq8ueCpr7A7d1jfkn9UTJW8K+T0Y9NunWkO+rnVdPKKiXHSEiFVgCYqBXlqZmTGpY7bn7N786c4hXTSl3NnbRGpr/PEioEK0//iDJ430lyfVFC4Jil/7LwHlIJ8yj/Wcf4FTtVnSkVzPa5xd7Y4OU/3bORiH9lMz23Jxj6qsqxOpxESBphP/erFnjKC0ZJYH4DEeWlE9etAnG0LL9n5z+8ZesSiCOHUdU8DE1EExNW5iunFu+J10F0b6Gc4Kw3A6AVOiWC8/iQddfR3wkwcEjKRKFUzkpXMlGtJ23cpdnh0Cn17yUN4GISm3jX6dUNEGou05Vovoi0T3C11DD/zRNR1M9+feHGwK" .
				"</eBayAuthToken>" .
    			"  </RequesterCredentials>" .
    			"  <ModTimeFrom>2012-02-12T00:00:00.000Z</ModTimeFrom>" .
    			"  <ModTimeTo>2012-02-13T00:00:00.000Z</ModTimeTo>" .
    			"  <OrderRole>Seller</OrderRole>" .
    			"  <OrderStatus>Active</OrderStatus>" .
    			"  <ErrorLanguage>en_CA</ErrorLanguage>" .
       		"</GetSellerTransactionsRequest>";
$req->setBody( $reqBody) ;

$reply	=	$req->send() ;

echo $reply->getBody() ;

?>
