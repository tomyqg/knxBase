<?php

date_default_timezone_set( "Europe/Berlin") ;	// needs to be here, otherwise php complains

$inFile	=	fopen( $argv[1], "r+") ;
$nrLine	=	fgets( $inFile) ;
$newNrLine	=	sprintf( "%05d", ((int)$nrLine)+1) ;
rewind( $inFile) ;
fputs( $inFile, $newNrLine) ;
fclose( $inFile) ;

$inFile	=	fopen( "../ERP/rd.txt", "w+") ;
$newNrLine	=	date("Y-m-d h:i:s A") ;
rewind( $inFile) ;
fputs( $inFile, $newNrLine . "\n") ;
fputs( $inFile, gethostname() . "\n") ;
fclose( $inFile) ;

?>