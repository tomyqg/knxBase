<?php
/**
 * apps/erm/app.php
 * ================
 *
 */

error_log( print_r( $_POST, true)) ;

/**
 * setup the complete include path
 */
$rp	=	"/mas" ;
$includeBase	=	$_SERVER["DOCUMENT_ROOT"] . $rp ;

$pathC	=	$includeBase . "/Config" ;
$pathI	=	$includeBase . "/lib" . PATH_SEPARATOR
		.	$includeBase . "/lib/core" . PATH_SEPARATOR
		.	$includeBase . "/lib/sys" . PATH_SEPARATOR
		.	$includeBase . "/lib/gui" . PATH_SEPARATOR
		.	$includeBase . "/lib/apps" . PATH_SEPARATOR
		.	$includeBase . "/lib/apps/vms" . PATH_SEPARATOR
		.	$includeBase . "/lib/apps/vms/mms" . PATH_SEPARATOR
		.	$includeBase . "/lib/pkgs/pdfdoc" . PATH_SEPARATOR
		.	$includeBase . "/lib/pkgs/fpdf" . PATH_SEPARATOR
		;
$includePath	=	get_include_path() . PATH_SEPARATOR
		.	$pathC . PATH_SEPARATOR
		.	$pathI . PATH_SEPARATOR
		.	$includeBase
		;
if ( ! set_include_path( $includePath)) {
	error_log( "config.inc.php::*::main::failed to set include path! FATAL!");
}

/**
 * __eiss_autoload
 *
 * install the autloader for classes.
 *
 * @param $_className
 */
function __eiss_autoload( $_className) {
	$read	=	false ;
	$className	=	$_className ;
	$incDirs	=	explode( PATH_SEPARATOR, get_include_path()) ;
	foreach( $incDirs as $dir) {
//		error_log( "scanning " . $dir) ;
		if ( file_exists( $dir . "/" . $_className . ".php")) {
			$read	=	true ;
		}
		if ( $read)
			break ;					// no other way to leave a foreach loop
	}
	if ( $read) {
		require_once $className . '.php' ;
	} else {
		throw new Exception( "required class file >>>'$_className'<<< does not exist ...") ;
	}
}
spl_autoload_register('__eiss_autoload');

FDb::registerDb( "nas1", "erpdemo", "demoerp", "mas_sys", "sys", "mysql", "") ;
FDb::registerDb( "nas1", "erpdemo", "demoerp", "mas_vms_1a2b3c4d", "def", "mysql", "") ;

$showApp	=	false ;

function	validatePara() {
	$res	=	true ;
	$res	&=	isset( $_POST["json"]) ;
	return $res ;
}

//require_once( $_SERVER["DOCUMENT_ROOT"]."/mas/Config/config.inc.R2.php") ;

/**
 * read the additional "mas" configuration parameters from the database
 */
if ( validatePara()) {
	error_log( __FILE__ . ": parameters are complete") ;
	$login	=	json_decode(( $_POST["json"])) ;
	foreach ( $login as $attr => $value) {
		if ( is_object( $value)) {
			foreach ( $value as $attr2 => $value2) {
				if ( is_object( $value2)) {
					foreach ( $value2 as $attr3 => $value3) {
						error_log( $attr3 . " => " . $value3) ;
					}
					
				} else {
					error_log( $attr2 . " => " . $value2) ;
				}
			}
		} else {
			error_log( $attr . " => " . $value) ;
		}
	}
	$ApplicationSystemId	=	$login->login->app->ApplicationSystemId ;
	$ApplicationId			=	$login->login->app->ApplicationId ;
	$ClientId				=	$login->login->user->ClientId ;
	$UserId					=	$login->login->user->UserId ;
	$Password				=	$login->login->user->Password ;
	
	/**
	 *
	 */
	$mySysUser	=	new SysUser() ;
	if ( ! $mySysUser->identify( $Password, $UserId)) {
	} else {
		error_log( __FILE__ . ": user is identified as SysUser()") ;
		$myClientApplication = new ClientApplication() ;
		if ( $myClientApplication->setComplexKey( [ $ClientId, $UserId, $ApplicationSystemId, $ApplicationId])) {
			error_log( __FILE__ . ": user is registered for the desired context") ;
			$mySysSession	=	new SysSession() ;
			$mySysSession->ClientId	=	$ClientId ;
			$mySysSession->UserId	=	$UserId ;
			$mySysSession->ApplicationSystemId	=	$ApplicationSystemId ;
			$mySysSession->ApplicationId	=	$ApplicationId ;
			$mySysSession->Validity	=	SysSession::VALIDAPP ;
			$mySysSession->updateChecksum() ;
			$mySysSession->updateInDb() ;
			error_log( __FILE__ . ": SysSession->SessionId = '" . $mySysSession->SessionId . "'") ;
			$showApp	=	true ;
		}
	}
}

if ( $showApp) {
	$reply	=	new AppInfo() ;
	$reply->URL	=	"/mas/" . $mySysSession->ApplicationSystemId . "/" . $mySysSession->ApplicationId . "/app.php" ;
	$reply->SessionId	=	$mySysSession->SessionId ;
	error_log( print_r($reply, true)) ;
	echo json_encode( $reply) ;
} else {
	$reply	=	new AppInfo() ;
	$reply->URL	=	"/mas/login2.html" ;
	echo json_encode( $reply) ;
}
?>
