<?php

error_log( print_r( $_REQUEST, true)) ;

$rp	=	"/mas" ;
$includeBase	=	$_SERVER["DOCUMENT_ROOT"] . $rp ;

$pathC	=	$includeBase . "/Config" ;
$pathI	=	$includeBase . "/lib" . PATH_SEPARATOR
		.	$includeBase . "/lib/core" . PATH_SEPARATOR
		.	$includeBase . "/lib/sys" . PATH_SEPARATOR
		.	$includeBase . "/lib/gui" . PATH_SEPARATOR
		.	$includeBase . "/lib/apps" . PATH_SEPARATOR
		.	$includeBase . "/lib/apps/erm" . PATH_SEPARATOR
		.	$includeBase . "/lib/pkgs/pdfdoc" . PATH_SEPARATOR
		.	$includeBase . "/lib/pkgs/fpdf" . PATH_SEPARATOR
;
$currIncludePath	=	get_include_path() ;
$includePath	=	get_include_path() . PATH_SEPARATOR
				.	$pathC . PATH_SEPARATOR
				.	$pathI . PATH_SEPARATOR
				.	$includeBase ;
error_log( " Include path stage 2 ..... : ") ;
error_log( "includePath := '$includePath'\n") ;
if ( ! set_include_path( $includePath)) {
	error_log( "config.inc.php::*::main::failed to set include path! FATAL!");
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
		error_log( print_r( debug_backtrace(), true)) ;
		throw new Exception( "required class file >>>'$_className'<<< does not exist ...") ;
	}
}
spl_autoload_register('__eiss_autoload');

FDb::registerDb( "nas1", "erpdemo", "demoerp", "mas_erm_1a2b3c4d", "def", "mysql", "") ;

$myCustomer	=	new Customer() ;
$myCustomer->setKey( $_REQUEST[ "CustomerNo"]) ;
//error_log( __FILE__ . "::" . __LINE__ . ": Customer data ... :") ;
//error_log( print_r( $myCustomer, true)) ;

$jsonData	=	$myCustomer->getListAsJSON( "", -1, "CustomerContact", null) ;

//error_log( $jsonData) ;

echo $jsonData ;
?>