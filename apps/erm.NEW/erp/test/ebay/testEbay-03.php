<?php

$pathC	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpconfig" ;
$pathI	=	"/srv/www/vhosts/wimtecc.de/subdomains/erp/httpdocs/002/phpinc" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $pathC . PATH_SEPARATOR . $pathI . PATH_SEPARATOR . "/usr/lib/php/pear");

require_once( "config.inc.php") ;
require_once( "HTTP/Request2.php") ;

$req	=	new HTTP_Request2( "https://api.sandbox.ebay.com/ws/api.dll", HTTP_Request2::METHOD_POST) ;
$req->setHeader( array(
					'X-EBAY-API-COMPATIBILITY-LEVEL' => '759',
					'X-EBAY-API-DEV-NAME' => 'c8e517c3-8e46-45b3-9284-992e95be4d7a',
					'X-EBAY-API-APP-NAME' => 'wimteccf2-7b66-4cde-913d-72e7a70ece9',
					'X-EBAY-API-CERT-NAME' => '11004c52-3a4c-450a-924b-4f9289257216',
					'X-EBAY-API-CALL-NAME' => 'GetCategories',
					'X-EBAY-API-SITEID' => '0',
					'Content-Type' => 'text/xml')) ;
$reqBody	=	"<?xml version='1.0' encoding='utf-8'?>" .
    			"<GetCategoriesRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">" .
    			"  <RequesterCredentials>" .
    			"    <eBayAuthToken>AgAAAA**AQAAAA**aAAAAA**CocxTw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GhCJSEpAydj6x9nY+seQ**trcBAA**AAMAAA**scKhpaDokGFvipE+P4XDp5Ux6wRs+/OvasuKnMi58zFwWF7uQ6E2LmR0QPiWtKM3ptXabryac7aTFmSAChpOeMLXFfOUg88UK5FATBtr9exzNYTKlHCtyvDdFmRRfmN1UOGtpoysIBpBIie7ohxOuvNnYfJL9dL1hJEAxx0seE8znfe38iFlzrfWkRYsUUW0Sx1tRwSgqPJZwqeH24jP1nrtUyrVDst0KKQPc9Leut7zQ8GB3Tm4tSIHStKE7dJkIWZYBv7TYr+sTSLETmw2ceu+Qz3uSNYKhhdiLUV+2M1NQATN1exBMIQVhwBBnleeRUfbom95nAMHrP8FRqncPOnl2HXEAjElZKzYO3lKACnqP0f7ZMj3RHVh9IZErGKDWjX9Gx62eaxw7OgBc1ffr4k1TSs3uONxcMuMTPDN7gOYw7KxPhjbwS9OEShMeM1E6LniH+4hPKKapJnr82jJ9L9cpPcmaL1AJRiwcC2Q2A7samcZUTPaD9fF8nJ8YXnnK8fGMqWsW6jBBtm0LGY0SSnSB6WehO60hGzaGtcIuHQDg+TjvzTd3eGArfQND59l/yuV9ygPa/lO84YYJeQhU0rc6X2skg7k93hdkW5PZpq+nJ6q8MwXJLeGKe6pgPidlCjtVBz53A/1cqwxtPfAP/hYNbjoxFb4OO5BigY99H5/GFt/Jt9efjybtV0c4hmGtoBIq8Tm+LFajcZ0GPkr7mBivc9bAsv5ZeY3H4a+unF3O0bFK9VfMQ4jqIJ3PEsx</eBayAuthToken>" .
    			"  </RequesterCredentials>" .
    			"  <DetailLevel>ReturnAll</DetailLevel>" .
    			"  <ErrorLanguage>en_CA</ErrorLanguage>" .
       		"</GetCategoriesRequest>";
$req->setBody( $reqBody) ;

$reply	=	$req->send() ;

echo $reply->getBody() ;

?>
