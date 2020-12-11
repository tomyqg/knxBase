<?php
/**
 * Copyright (c) 2015, 2016 wimtecc, Karl-Heinz Welter
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 * config_app.inc.php
 *
 * Configuration step 2 for "mas".
 * This config file shall only be executed in case all login requirements have been fulfilled, i.e. the user
 * is authorized:
 * 		- for this client id AND
 * 		- for this application system id AND
 * 		- for an application id
 *
 * Here we will increase the lookup scope for the include files, ie. we will add - with priority -
 * a path to
 * 		- ~/../clients/<client id>/lib/<application system id>/<application id>,
 * 		- ~/../clients/<client id>/lib/<application system id>,
 * 		- ~/../clients/<client id>/lib,
 * 		- ~/../lib/apps/<application system id>/<application id>,
 * 		- ~/../lib/apps/<application system id> and
 * 		- ~/../lib/apps
 *
 * Loading sequence of configuration options:
 * ------------------------------------------
 *
 * load config.ini into sysConfig
 * 	-	this creates	sysConfig.dbSys.alias/.host/.user/.password/.name
 * 						sysConfig.debug.enabled/.level/.mask/...
 * 	-	dbSys will be registered
 * add configuration parameters from Db[sys]:SysConfigObj to sysConfig
 *
 * 	... proceed in config_app.inc.php
 *
 * add configuration parameters from Db[sys]:SysConfigObj[Class=UI]:[ApplicationSystemId]:[ApplicationId]
 * 			to sysConfig
 * if sysConfig.ui is defined
 * 		register Db for UI components
 * add configuration parameters from Db[sys]:AppConfigObj[Class=def]:[ApplicationSystemId]:[ApplicationId]
 * 			to appConfig
 * if appConfig.def is defined
 * 		register Db for def database access (application data)
 * 		add configuration parameters from Db[def]:AppConfigObj[Class=def]
 * 		override configuration parameters from Db[def]:AppConfigObj[Class=def]:[ApplicationSystemId]:[ApplicationId]
 *
 *
 * load $_SERVER['DOCUMENT_ROOT']/appConfig.ini into appConfig
 *
 * Preconditions:
 * --------------
 *
 * $_SESSION['
 * $myUser<User>		object defined and containing valid data
 *
 */
error_log( "entering config_app.inc.php") ;
/**
 * re-establish the include path, now including the
 * 	-	client-specific and
 * 	-	application-system-specific
 * pathes.
 */
$pathI	=	".:"
		.	$includeBase . "/clients/".$mySysSession->ClientId ."/lib/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId. PATH_SEPARATOR
		.	$includeBase . "/clients/".$mySysSession->ClientId ."/lib/".$mySysSession->ApplicationSystemId . PATH_SEPARATOR
		.	$includeBase . "/clients/".$mySysSession->ClientId ."/lib" . PATH_SEPARATOR
		.	$includeBase . "/lib/apps/".$mySysSession->ApplicationSystemId."/".$mySysSession->ApplicationId. PATH_SEPARATOR
		.	$includeBase . "/lib/apps/".$mySysSession->ApplicationSystemId . PATH_SEPARATOR
		.	$includeBase . "/lib/apps"
		;
/**
 * prepend this path so it gets priority over the already defined include pathes
 */
$includePath	=	$pathI . PATH_SEPARATOR . get_include_path() ;
FDbg::trace( 0, FDbg::mdTrcInfo1, "config_app.inc.php", "*", "main", "includePath := '$includePath'") ;
if ( ! set_include_path( $includePath)) {
	FDbg::trace( 1, FDbg::mdTrcInfo1, "config_app.inc.php", "*", "main", "failed to set include path! FATAL!");
	die() ;
}
/**
 * get application specific settings for the UI
 * if - and only IF - these are defined for this applicationsystem/application
 */
FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", __LINE__) ;
FDbg::trace( 1, FDbg::mdTrcInfo1, "config_app.inc.php", "*", "main", "working with mySysConfig") ;
/**
 * if there is an applicationSystem database defined, register it on the database layer
 */
$mySysConfig->addFromSysDb( "def", $mySysSession->ApplicationSystemId, $mySysSession->ApplicationId, $mySysSession->ClientId) ;
if ( isset( $mySysConfig->appSys)) {
//	error_log( print_r( $mySysConfig->appSys, true)) ;
	FDb::registerDb( $mySysConfig->appSys->dbHost, $mySysConfig->appSys->dbUser, $mySysConfig->appSys->dbPassword, $mySysConfig->appSys->dbName, $mySysConfig->appSys->dbAlias, $mySysConfig->appSys->dbDriver, $mySysConfig->appSys->dbPrefix) ;
}
$mySysConfig->addFromSysDb( "UI", $mySysSession->ApplicationSystemId, $mySysSession->ApplicationId) ;
if ( isset( $mySysConfig->ui)) {
	UI_Object::$dbAlias	=	$mySysConfig->ui->dbAlias ;
//	error_log( print_r( $mySysConfig->ui, true)) ;
	FDb::registerDb( $mySysConfig->ui->dbHost, $mySysConfig->ui->dbUser, $mySysConfig->ui->dbPassword, $mySysConfig->ui->dbName, $mySysConfig->ui->dbAlias, $mySysConfig->ui->dbDriver, $mySysConfig->ui->dbPrefix) ;
}
/**
 * get the "default" application database
 */
FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", __LINE__) ;
FDbg::trace( 1, FDbg::mdTrcInfo1, "config_app.inc.php", "*", "main", "working with myAppConfig") ;
$myAppConfig	=	EISSCoreObject::__getAppConfig() ;
$myAppConfig->addFromSysDb( "def", $mySysSession->ApplicationSystemId, $mySysSession->ApplicationId, $mySysSession->ClientId) ;
FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", __LINE__) ;
FDbg::trace( 1, FDbg::mdTrcInfo1, "config_app.inc.php", "*", "main", "myAppConfig->def->dbHost := '".$myAppConfig->def->dbHost."'") ;
if ( isset( $myAppConfig->def)) {
//	error_log( print_r( $myAppConfig->def, true)) ;
	FDb::registerDb( $myAppConfig->def->dbHost, $myAppConfig->def->dbUser, $myAppConfig->def->dbPassword, $myAppConfig->def->dbName, $myAppConfig->def->dbAlias, $myAppConfig->def->dbDriver, $myAppConfig->def->dbPrefix) ;
	/**
	 * only load the "def"-class parameters from the database if there is a database at all
	 */
//	FDbg::trace( 1, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__, "line..:".__LINE__) ;
	FDbg::trace( 1, FDbg::mdTrcInfo1, "config_app.inc.php", "*", "main", "working with myAppConfig") ;
	$myAppConfig->addFromAppDb( "def") ;
	$myAppConfig->addFromAppDb( "def", $mySysSession->ApplicationSystemId, $mySysSession->ApplicationId, $mySysSession->ClientId) ;
	/**
	 * now enable the Application Level translation stuff
	 */
	if ( isset( $myAppConfig->trans)) {
		AppTrans::__setDbAlias( $myAppConfig->trans->dbAlias) ;
	}
	/**
	 * enable translation through application level tran
	 */
	FTr::enableAppTrans() ;
	/**
	 * see if additional User data can be found in the application database
	 */
	try {
		/**
		 * the initial call to SysSession (in: config.inc.php) has only instantiated the SysUser and memorized the
		 * AppUserId of the AppUser. this is done as FDbObject will apply autorization rules as soon as
		 * an AppUser is set in SysSession. this we do now and make sure that we load the authorization data (buildAuthTree).
		 * with the authorization tree loaded we can NOW safely reference the AppUser object from the SysSession object.
		 */
		$myAppUser	=	new AppUser() ;
		$myAppUser->setKey( $mySysSession->AppUserId) ;
		if ( $myAppUser->isValid()) {
			EISSCoreObject::__setAppUser( $myAppUser) ;		// attach the user data to the CoreObject
			$myAppUser->buildAuthTree() ;				// get the authentication data for this user
			$myAppUser->showAuthTree() ;
			FTr::init( $myAppUser->Lang) ;
		} else {
			FDbg::trace( 0, FDbg::mdTrcInfo1, basename( __FILE__), __CLASS__, __METHOD__."()", "no AppUser identified!") ;
		}
	} catch ( Exception $e) {
	}
}
$myAppConfig->addFromAppDb( "") ;
/**
 *
 */
//error_log( print_r( $mySysConfig, true)) ;
error_log( "leaving config_app.inc.php") ;
?>
