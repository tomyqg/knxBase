<?php

date_default_timezone_set( "Europe/Berlin") ;   // needs to be here, otherwise php complains

configFromFile( "app.ini") ;

$ifconfigData	=	exec( "ifconfig eth0 |grep 192") ;

printf( "Sending ... ") ;
sendSMS( "+491714494070", "H-P HEIZUNG", $ifconfigData) ;

/**
 *
 */
function	configFromFile( $_filename="") {
	if ( $_filename != "") {
		$appConf	=	parse_ini_file( $_filename, true) ;
		foreach ( $appConf as $section => $values) {
			if ( ! isset( ${$section})) {
				global	${$section} ;
				${$section}	=	new stdClass() ;
			}
			foreach ( $values as $name => $val) {
				${$section}->{$name}	=	$val ;
			}
		}
	}
}

/**
 *
 */
function	sendSMS( $_to, $_from, $_text) {
	$params = array(
		'u'	=>	'user898373',
		'p'	=>	'XoEtZiTHKjQsIAai9bBCGlwE9P4suEYQs5HYSHqQXq1xsF1hb5XjOBi0hYzJxPBt',
		'to'	=>	$_to,
		'text'	=>	$_text,
		'type'	=>	'direct',
		'from'	=>	$_from
	);
	$url	=	'https://gateway.sms77.io/api/sms?' . http_build_query($params);
	$ret	=	file_get_contents($url);
	$parts = explode("\n", $ret);
	if ( $ret!== false && $parts[0] == '100')
		echo "SMS verschickt";
	else
		echo "SMS nicht verschickt (Fehler: " . $ret . ")";
}

/**
 *
 */
function	sendDataN( $_url, $_urlUser, $_urlPassword, $_data, $_count) {
	$params = array(
	);
	$ret	=	file_get_contents( $_url);
	if ( $ret == '100')
		echo "SMS verschickt";
	else
		echo "SMS nicht verschickt (Fehler: " . $ret . ")";
	return $res ;
}

/**
 *
 */
function	sendData( $_url, $_urlUser, $_urlPassword, $_data, $_count, $_post) {
echo $_url . "\n" ;
echo $_data . "\n" ;
	$process	=	curl_init();
	curl_setopt( $process, CURLOPT_URL, urldecode( $_url)) ;
	curl_setopt( $process, CURLOPT_HEADER, true);
	curl_setopt( $process, CURLOPT_USERPWD, $_urlUser . ":" . $_urlPassword);
	curl_setopt( $process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ;
	curl_setopt( $process, CURLOPT_TIMEOUT, 30);
	curl_setopt( $process, CURLOPT_POST, $_count);
//	curl_setopt( $process, CURLOPT_POSTFIELDS, $_data);
	curl_setopt( $process, CURLOPT_POSTFIELDS, http_build_query( $_post));
//	curl_setopt( $process, CURLOPT_RETURNTRANSFER, true);
$info = curl_getinfo($process);
	print_r( $info) ;
	$res	=	curl_exec( $process);
	echo curl_error( $process) ;
	curl_close( $process);
	return $res ;
}

?>
