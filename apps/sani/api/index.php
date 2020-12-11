<?php

echo $_SERVER[ 'HTTP_ACCEPT'] . "\n" ;

if ( strpos( $_SERVER[ 'HTTP_ACCEPT'], "text/html") !== false) {
	echo "<h1>HTML</h1>" ;
} else if ( strpos( $_SERVER[ 'HTTP_ACCEPT'], "application/json") !== false) {
	echo "Here should be json data \n" ;
} else {
	echo "can't provide required data format...\n" ;
}


?>
