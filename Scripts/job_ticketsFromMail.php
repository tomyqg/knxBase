#!/usr/bin/php
<?php
/**
 *
 */
$exitStatus	=	0 ;
error_log( "job_ticketsFrom.php: starting ...") ;
if ( !isset( $argv[1])) {
	echo( "job_ticketsFrom.php: terminating pre-maturely due to missing parameter <cycle> ...") ;
	die() ;
}
$schedule	=	$argv[1] ;
/**
 *
 */
if ( !isset( $argv[2])) {
	$script	=	"%" ;
} else {
	$script	=	$argv[2] ;
}
if ( isset( $argv[3])) {
	$_POST["ClientId"]	=	$argv[3] ;
	if ( isset( $argv[4])) {
		$_POST["ApplicationSystemId"]	=	$argv[4] ;
	} else {
		echo( "job_ticketsFrom.php: terminating execution pre-maturely due to missing parameter(s) ...") ;
		die() ;
	}
	if ( isset( $argv[5])) {
		$_POST["ApplicationId"]	=	$argv[5] ;
	} else {
		echo( "job_ticketsFrom.php: terminating execution pre-maturely due to missing parameter(s) ...") ;
		die() ;
	}
	if ( isset( $argv[6])) {
		$_POST["UserId"]	=	$argv[6] ;
	} else {
		echo( "job_ticketsFrom.php: terminating execution pre-maturely due to missing parameter(s) ...") ;
		die() ;
	}
	if ( isset( $argv[7])) {
		$_POST["Password"]	=	$argv[7] ;
	} else {
		echo( "job_ticketsFrom.php: terminating execution pre-maturely due to missing parameter(s) ...") ;
		die() ;
	}
}
/**
 *
 */
$_SERVER["DOCUMENT_ROOT"]	=	__DIR__ ;
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
/**
 * ---------------------------------------------------------------------------------------------------------------
 */
/**
 * ---------------------------------------------------------------------------------------------------------------
 */
exit( $exitStatus) ;
?>
