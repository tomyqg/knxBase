<?php

error_log( "in lateAppConfig.inc.php") ;

if ( isset( $myAppConfig->cloud)) {
//	error_log( print_r( $myAppConfig->cloud, true)) ;
	FDb::registerDb( $myAppConfig->cloud->dbHost, $myAppConfig->cloud->dbUser, $myAppConfig->cloud->dbPassword, $myAppConfig->cloud->dbName, $myAppConfig->cloud->dbAlias, $myAppConfig->cloud->dbDriver, $myAppConfig->cloud->dbPrefix) ;
}

error_log( "Firma ..... : " . EISSCoreObject::__getAppConfig()->fallback->FirmaERPNr) ;
error_log( "Filiale ... : " . EISSCoreObject::__getAppConfig()->fallback->FilialeERPNr) ;

?>
