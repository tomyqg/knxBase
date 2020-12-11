#!/usr/bin/php5
<?php

require_once( "global.inc.php") ;
include( "LfDoc.cls.php") ;
include( "KdDoc.cls.php") ;

echo "MAIN:opening db connection \n" ;

$database       =       connectDatabase( $dbHost, $dbUser, $dbPasswd) ;
if ( ! mysql_select_db( $dbName)) {
	echo "couldn't select database ... serious ... <br/>" ;
}

$myLfDoc	=	new LfDoc( $database) ;

echo "MAIN:creating SuOrdrDoc object \n" ;
$myLfDoc->setFromSuOrdr( $database, "000303") ;
$pdfName	=	$myLfDoc->getPDF( $database, 0, "A4", true) ;
system( "mv " . $pdfName . " /home/miskhwe/s_SuOrdr.pdf") ;

echo "MAIN:creating SuDlvrDoc object \n" ;
$myLfDoc->setFromSuDlvr( $database, "000201") ;
$pdfName	=	$myLfDoc->getPDF( $database, 0, "A4", true) ;
system( "mv " . $pdfName . " /home/miskhwe/s_SuDlvr.pdf") ;

$myKdDoc	=	new KdDoc( $database) ;

echo "MAIN:creating CuOrdrDoc object \n" ;
$myKdDoc->setFromCuOrdr( $database, "000201") ;
$pdfName	=	$myKdDoc->getPDF( $database, 0, "A4", true) ;
system( "mv " . $pdfName . " /home/miskhwe/s_CuOrdr.pdf") ;

echo "MAIN:creating CuCommDoc object \n" ;
$myKdDoc->setFromCuComm( $database, "000415") ;
$pdfName	=	$myKdDoc->getPDF( $database, 0, "A4", true) ;
system( "mv " . $pdfName . " /home/miskhwe/s_CuComm.pdf") ;

echo "MAIN:creating CuDlvrDoc object \n" ;
$myKdDoc->setFromCuDlvr( $database, "000350") ;
$pdfName	=	$myKdDoc->getPDF( $database, 0, "A4", true) ;
system( "mv " . $pdfName . " /home/miskhwe/s_CuDlvr.pdf") ;

echo "MAIN:creating CuInvcDoc object \n" ;
$myKdDoc->setFromCuInvc( $database, "000248") ;
$pdfName	=	$myKdDoc->getPDF( $database, 0, "A4", true) ;
system( "mv " . $pdfName . " /home/miskhwe/s_CuInvc.pdf") ;

?>
