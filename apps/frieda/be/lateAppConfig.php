<?php
FDbg::begin( 1, basename( __FILE__), __CLASS__, "lateAppConfig.php") ;
if ( isset( $myAppConfig->co)) {
	FDb::registerDb( $myAppConfig->co->dbHost, $myAppConfig->co->dbUser, $myAppConfig->co->dbPassword, $myAppConfig->co->dbName, $myAppConfig->co->dbAlias, $myAppConfig->co->dbDriver, $myAppConfig->co->dbPrefix) ;
}
FDbg::end() ;
?>