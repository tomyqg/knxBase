<?php

date_default_timezone_set( "Europe/Berlin") ;	// needs to be here, otherwise php complains

$authorized	=	false ;

if ( isset( $_SERVER[ "PHP_AUTH_USER"])) {
	if ( $_SERVER[ "PHP_AUTH_USER"] == "test" && $_SERVER['PHP_AUTH_PW'] == "unorthodox") {
		$authorized	=	true ;
	}
}

if ( ! $authorized) {
	header('WWW-Authenticate: Basic realm="KNX Data Upload"');
	header('HTTP/1.0 401 Unauthorized');
	echo 'Kein User/Password ... kein Upload :(';
	exit;
} else {
//	echo "<p>Hallo {$_SERVER['PHP_AUTH_USER']}.</p>";
//	echo "<p>Sie gaben {$_SERVER['PHP_AUTH_PW']} als Passwort ein.</p>";
}

myLog( print_r( $_REQUEST, true)) ;

$dbSys	=	new stdClass() ;
$dbSys->alias	=	"sys" ;
$dbSys->host		=	"localhost" ;
$dbSys->user		=	$_POST[ "user"] ;
$dbSys->password	=	$_POST[ "password"] ;
$dbSys->name		=	$_POST[ "user"] ;
$dbSys->driver		=	"mysql" ;
$dbSys->prefix		=	"" ;

myLog( $_POST["user"]) ;

$myDb	=	mysqli_connect( $dbSys->host, $dbSys->user, $dbSys->password, $dbSys->name) ;

myLog( "Hello, world ...") ;

$index	=	0 ;

foreach ( $_POST[ "LogTime"] as $index => $time) {
	$sql	=	"INSERT INTO log ( LogTime, GroupObjectId, DataType, Value) "
		.	"VALUES ( '" . $_POST[ "LogTime"][ $index] . "', "
		.		 "'" . $_POST[ "GroupObjectId"][ $index] . "', "
		.		 "'" . $_POST[ "DataType"][ $index] . "', "
		.		 "'" . $_POST[ "Value"][ $index] . "'"
		.	")" ;
	myLog( $sql, true) ;
	mySqlQuery( $sql) ;
}

/**
 *
 */
function	mySqlQuery( $_sql) {
	global	$dbSys ;
	global	$myDb ;
	$retryCount	=	0 ;
	$reconnectCount	=	0 ;
	do {
		myLog( "mySqlQuery, line ... : " . __LINE__) ;
		if ( $retryCount > 0) {
			myLog( "mySQL: retrying ...") ;
		}
		$sqlResult	=	mysqli_query( $myDb, $_sql) ;
		if ( ! $sqlResult) {
			myLog( "mySQL: error during query ...") ;
			do {
				myLog( "mySqlQuery, line ... : " . __LINE__) ;
				$myDb	=	mysqli_connect( $dbSys->host, $dbSys->user, $dbSys->password, $dbSys->name) ;
				if ( ! $myDb) {
					myLog( "mySQL: reconnecting ..., count := " . $reconnectCount . ", retry := " . $retryCount) ;
					$reconnectCount++ ;
					sleep( $reconnectCount) ;
				}
			} while ( $reconnectCount < 10 && ! $myDb) ;
			if ( $reconnectCount >= 10) {
				myLog( "mySQL: reconnect count exceeded; terminating ...") ;
			}
		}
	} while ( $retryCount < 10 && ! $sqlResult) ;
	if ( $retryCount >= 10) {
		myLog( "mySQL: retry count exceeded; terminating ...") ;
		die() ;
	}
	return ( $sqlResult) ;
}

/**
 *
 */
function        myLog( $_msg) {
	$file   =       fopen( "php.log", "a+") ;
	fwrite( $file, $_msg . "\n") ;
	fclose( $file) ;
}
