<?php

/**
 * apps/erm/app.php
 * ================
 *
 */

error_log( print_r( $_POST, true ) );

/**
 * setup the complete include path
 */
$rp = "/mas";
$includeBase = $_SERVER[ "DOCUMENT_ROOT" ] . $rp;

$pathC = $includeBase . "/Config";
$pathI = $includeBase . "/lib" . PATH_SEPARATOR
	. $includeBase . "/lib/core" . PATH_SEPARATOR
	. $includeBase . "/lib/sys" . PATH_SEPARATOR
	. $includeBase . "/lib/gui" . PATH_SEPARATOR
	. $includeBase . "/lib/apps" . PATH_SEPARATOR
	. $includeBase . "/lib/apps/vms" . PATH_SEPARATOR
	. $includeBase . "/lib/apps/vms/mms" . PATH_SEPARATOR
	. $includeBase . "/lib/pkgs/pdfdoc" . PATH_SEPARATOR
	. $includeBase . "/lib/pkgs/fpdf" . PATH_SEPARATOR;
$includePath = get_include_path() . PATH_SEPARATOR
	. $pathC . PATH_SEPARATOR
	. $pathI . PATH_SEPARATOR
	. $includeBase;
if ( ! set_include_path( $includePath ) ) {
	error_log( "config.inc.php::*::main::failed to set include path! FATAL!" );
}

/**
 * __eiss_autoload
 *
 * install the autloader for classes.
 *
 * @param $_className
 */
function __eiss_autoload( $_className ) {
	$read = false;
	$className = $_className;
	$incDirs = explode( PATH_SEPARATOR, get_include_path() );
	foreach ( $incDirs as $dir ) {
//		error_log( "scanning " . $dir) ;
		if ( file_exists( $dir . "/" . $_className . ".php" ) ) {
			$read = true;
		}
		if ( $read ) {
			break;
		}                    // no other way to leave a foreach loop
	}
	if ( $read ) {
		require_once $className . '.php';
	} else {
		throw new Exception( "required class file >>>'$_className'<<< does not exist ..." );
	}
}

spl_autoload_register( '__eiss_autoload' );

FDb::registerDb( "nas1", "erpdemo", "demoerp", "mas_sys", "sys", "mysql", "" );
FDb::registerDb( "nas1", "erpdemo", "demoerp", "mas_vms_1a2b3c4d", "def", "mysql", "" );

$showApp = false;

function validatePara() {
	$res = true;
	$res &= isset( $_GET[ "sessionId" ] );
	return $res;
}

/**
 * read the additional "mas" configuration parameters from the database
 */
if ( validatePara() ) {
	error_log( __FILE__ . ": parameters are complete");
	$SessionId = $_GET["sessionId"];
	error_log( __FILE__ . ": SessionId = " . $SessionId);
	
	/**
	 *
	 */
	$mySysSession = new SysSession( $SessionId) ;
	if ( ! $mySysSession->isValid()) {
	} else {
		error_log( __FILE__ . ": session is valid" );
		$showApp	=	true ;
	}
}

if ( $showApp) {
	require_once( "app.html") ;
} else {
	header('Location: /mas/loginNew.html');
	die() ;
}


