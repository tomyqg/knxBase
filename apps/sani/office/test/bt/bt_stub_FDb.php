<?php

require_once( "FDbg.php") ;
require_once( "FDb.php") ;

require_once( "global.inc.php") ;
require_once( "option.inc.php") ;

FDbg::setLevel( 0xffffffff) ;			// alles tracen
FDbg::enable() ;

/**
 *
 */

$myResult	=	FDb::query( "SELECT @numSpaceStart ;") ;
if ( $myResult) {
	$row	=	mysql_fetch_assoc( $myResult) ;
	FDbg::dumpF( "@numSpaceStart = '%s'", $row[ '@numSpaceStart']) ;
}

$myResult	=	FDb::query( "SELECT @numSpaceEnd ;") ;
if ( $myResult) {
	$row	=	mysql_fetch_assoc( $myResult) ;
	FDbg::dumpF( "@numSpaceEnd = '%s'", $row[ '@numSpaceEnd']) ;
}

$myRow	=	FDb::callProc( "KdAnf_new( @status, @newKdAnfNr)", "@newKdAnfNr") ;
if ( $myResult) {
	FDbg::dumpF( "@newKdAnfNr = '%s'", $myRow[ '@newKdAnfNr']) ;
}
