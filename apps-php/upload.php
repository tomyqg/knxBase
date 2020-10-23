<?php

date_default_timezone_set( "Europe/Berlin") ;   // needs to be here, otherwise php complains

configFromFile( "app.ini") ;

$myDb   =       mysqli_connect( $dbSys->host, $dbSys->user, $dbSys->password, $dbSys->name) ;
mysqli_select_db( $myDb, $dbSys->name) ;

$sqlQuery       =       "SELECT * FROM log "
		.       "WHERE ( "
		.       "Transfer = 0) "
		.       "ORDER BY LogTime, GroupObjectId "
		.       "LIMIT 0, 10 ;" ;
echo $sqlQuery . "\n" ;

$sqlResult      =       mySqlQuery( $sqlQuery) ;
while ( mysqli_affected_rows( $myDb) >= 10) {
	$post	=	array() ;
	$post["user"]	=	$dbRemote->dbUser ;
	$post["password"]	=	$dbRemote->dbPassword ;

	$postData       =       "" ;
	$postData       .=       "user={$dbRemote->dbUser}" ;
	$postData       .=       "%26password={$dbRemote->dbPassword}" ;
	$index          =       0 ;
	$ids    =       array() ;
	while ( $row = mysqli_fetch_assoc( $sqlResult)) {
		echo "Index ... {$index}\n" ;
		$ids[]          =       $row[ "Id"] ;
		$post[ "LogTime[{$index}]"]	=	$row[ "LogTime"] ;
		$post[ "GroupObjectId[{$index}]"]	=	$row[ "GroupObjectId"] ;
		$post[ "DataType[{$index}]"]	=	$row[ "DataType"] ;
		$post[ "Value[{$index}]"]	=	$row[ "Value"] ;
		$postData       .=      "%26LogTime[{$index}]=" . $row[ "LogTime"] ;
		$postData       .=      "%26GroupObjectId[{$index}]=" . $row[ "GroupObjectId"] ;
		$postData       .=      "%26DataType[{$index}]=" . $row[ "DataType"] ;
		$postData       .=      "%26Value[{$index}]=" . $row[ "Value"] ;
		$index++ ;
	}
	$postCount	=	2 + $index * 4 ;

	$res    =       sendData( urlencode( $dbRemote->url), $dbRemote->urlUser, $dbRemote->urlPassword, $postData, $postCount, $post) ;

	if ( $res !== false) {
		echo "data transferred ... will update local data\n" ;
		foreach ( $ids as $id) {
			$sqlUpdQuery    =       "DELETE FROM log WHERE Id = " . $id . " ;" ;
			$sqlResult      =       mySqlQuery( $sqlUpdQuery) ;
		}
	}
	$sqlResult      =       mySqlQuery( $sqlQuery) ;
}
echo "no more data to transfer\n" ;

// sendSMS( "01714494070", "HEIZUNG", "Die aktuelle Temperatur von 12.23grdC unterschreitet den Schwellwert von 15grdC") ;

function	mySqlQuery( $_sql) {
	global	$dbSys ;
	global	$myDb ;
	$retryCount	=	0 ;
	$reconnectCount	=	0 ;
	do {
		error_log( "mySqlQuery, line ... : " . __LINE__) ;
		if ( $retryCount > 0) {
			error_log( "mySQL: retrying ...") ;
		}
		$sqlResult	=	mysqli_query( $myDb, $_sql) ;
		if ( ! $sqlResult) {
			error_log( "mySQL: error during query ...") ;
			do {
				error_log( "mySqlQuery, line ... : " . __LINE__) ;
				$myDb	=	mysqli_connect( $dbSys->host, $dbSys->user, $dbSys->password, $dbSys->name) ;
				if ( ! $myDb) {
					error_log( "mySQL: reconnecting ..., count := " . $reconnectCount . ", retry := " . $retryCount) ;
					$reconnectCount++ ;
					sleep( $reconnectCount) ;
				}
			} while ( $reconnectCount < 10 && ! $myDb) ;
			if ( $reconnectCount >= 10) {
				error_log( "mySQL: reconnect count exceeded; terminating ...") ;
			}
		}
	} while ( $retryCount < 10 && ! $sqlResult) ;
	if ( $retryCount >= 10) {
		error_log( "mySQL: retry count exceeded; terminating ...") ;
		die() ;
	}
	return ( $sqlResult) ;
}


/**
 *
 */
function	configFromFile( $_filename="") {
	if ( $_filename != "") {
		$appConf	=	parse_ini_file( $_filename, true) ;
		foreach ( $appConf as $section => $values) {
			if ( ! isset( ${$section})) {
error_log( "creating global ... {$section}") ;
				global	${$section} ;
				${$section}	=	new stdClass() ;
			}
			foreach ( $values as $name => $val) {
error_log( "creating global ... {$name}") ;
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
		'p'	=>	'hJeHxUZZEPwXt0YGfBwYkgadX3H81Hxi',
		'to'	=>	$_to,
		'type'	=>	'direct',
		'text'	=>	$_text,
		'from'	=>	$_from
	);
	$url	=	'https://gateway.sms77.io/api/sms?' . http_build_query($params);
	$ret	=	file_get_contents($url);
	if ( $ret == '100')
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
