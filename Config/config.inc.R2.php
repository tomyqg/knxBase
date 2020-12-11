<?php
/**
 * Copyright (c) 2015-2020 Karl-Heinz Welter
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
 * file:	config.inc.php
 * path:	$_SERVER["DOCUMENT_ROOT"]/
 *
 * configuration file for "mas"
 *
 *
 */
error_log( __FILE__ . " starting up ...") ;
$rp	=	"/mas" ;
date_default_timezone_set( "Europe/Berlin") ;	// needs to be here, otherwise php complains

/**
 * pull the include path together
 * this covers only the general part, w/o any client specific extensions, which will be
 * covered in the second step in case authentication proceeds.
 * see also: config_app.inc.php
 */
if ( ! isset( $includeBase)) {
	$includeBase	=	$_SERVER["DOCUMENT_ROOT"] . $rp ;	// "/.." ;
}
$pathC	=	$includeBase . "/Config" ;
$pathI	=	$includeBase . "/lib" . PATH_SEPARATOR
		.	$includeBase . "/lib/core" . PATH_SEPARATOR
		.	$includeBase . "/lib/sys" . PATH_SEPARATOR
		.	$includeBase . "/lib/gui" . PATH_SEPARATOR
		.	$includeBase . "/lib/apps" . PATH_SEPARATOR
		.	$includeBase . "/lib/pkgs/pdfdoc" . PATH_SEPARATOR
		.	$includeBase . "/lib/pkgs/fpdf" . PATH_SEPARATOR
				;
$currIncludePath	=	get_include_path() ;
$includePath	=	get_include_path() . PATH_SEPARATOR
				.	$pathC . PATH_SEPARATOR
				.	$pathI . PATH_SEPARATOR
				.	$includeBase ;

if ( ! set_include_path( $includePath)) {
	error_log( "config.inc.R2.php::*::main::failed to set include path! FATAL!");
	die() ;
}

/**
 * __eiss_autoload
 *
 * install the autloader for classes.
 *
 * @param $_className
 */
function __eiss_autoload( $_className) {
	global	$debugBoot ;
	$read	=	false ;
	$className	=	$_className ;
//	error_log( "__eiss_autoload( '$_className')") ;
	$incDirs	=	explode( PATH_SEPARATOR, get_include_path()) ;
	foreach( $incDirs as $dir) {
//		error_log( "checking '$dir/$_className.php'") ;
		if ( file_exists( $dir . "/" . $_className . ".php")) {
//			error_log( "$_className.php found in ... '$dir'") ;
			$read	=	true ;
		}
		if ( $read)
			break ;					// no other way to leave a foreach loop
	}
	if ( $read) {
		require_once $className . '.php' ;
	} else {
//		error_log( print_r( debug_backtrace(), true)) ;
		throw new Exception( "required class file >>>'$_className'<<< does not exist ...") ;
	}
}
spl_autoload_register('__eiss_autoload');

/**
 * load the configuration file
 * setup debugger with the data from the config file
 * setup the database connection
 * (happens in conf_db.inc.php).
 */
$mySysConfig	=	new Config( "config.ini") ;

/**
 *
 */
EISSCoreObject::__setSysConfig( $mySysConfig) ;	// attach the configuration to the CoreObject

/**
 * register the databases
 */
error_log( "as of here we have the sysconfig database accessible") ;
FDb::registerDb( $mySysConfig->dbSys->host, $mySysConfig->dbSys->user, $mySysConfig->dbSys->password, $mySysConfig->dbSys->name, "sys", $mySysConfig->dbSys->driver, $mySysConfig->dbSys->prefix) ;

/**
 * load the global configuration params
 * these are all parameters in the mas_sys database table SysConfigObj where
 * 	ApplicationSystemId	=	""
 * 	ApplicationId		=	""
 * 	ClientId			=	""
 * 	Class				=	""
 */
$mySysConfig->addFromSysDb() ;			// read all global configuration parameters
/**
 * initialize some global variables
 */
$lineEnd	=	"\n" ;

/**
 * load the language support
 */
FTr::init( "en") ;

/**
 *	establish the Session
 */
if ( isset( $_REQUEST[ 'sessionId'])) {
	$sessionId	= $_REQUEST[ 'sessionId' ];
} else {
	$sessionId	=	"" ;
}
$mySysSession	=	new SysSession( $sessionId) ;
error_log( "SysSession Object ... : ") ;
error_log( "            SysUser...: " . $mySysSession->SysUser->UserId) ;
//error_log( print_r( $mySysSession, true)) ;

if ( $mySysSession->Validity >= SysSession::VALIDLOGIN) {
	error_log( "SysSession is valid") ;
	if ( $mySysSession->ApplicationSystemId != "" && $mySysSession->ApplicationId != "") {
		error_log( "all preconditions met ..., SysUser = " . $mySysSession->SysUser->UserId) ;
		EISSCoreObject::__setSysUser( $mySysSession->SysUser) ;		// attach the user data to the CoreObject
		require_once( "config_app.inc.php") ;
		/**
		 * try to load additional, application specific settings. these are only read from the DOCUMENT_ROOT, i.e. these are
		 * web-server specific
		 */
		$lateAppConfig	=	$_SERVER["DOCUMENT_ROOT"] . "/../apps/" . $mySysSession->ApplicationSystemId . "/" . $mySysSession->ApplicationId . "/lateAppConfig.php" ;
		if ( file_exists( $lateAppConfig)) {
			include( $lateAppConfig) ;
		}
	} else {
		error_log( "SysSession is valid, but ApplicationSystemId and/or ApplicationId is/are not!") ;
	}
}
?>
