<?php
/**
 * 
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
/**

 */
$articleNo	=	$_POST['_DArtikelNr'] ;
$dirPrefix	=	substr( $articleNo, 0, 3) ;
$myArticle	=	new Artikel( $articleNo) ;
$filename	=	$myArticle->BildRef ;
/**
 * 
 */
echo "Rotate: Article image clock-wise<br/>" ;
echo "ArtikelNr: [" . $articleNo . "]<br/>" ;
echo "Dir-Prefix: [" . $dirPrefix . "]<br/>" ;
echo "Filename: [" . $filename . "]<br/>" ;

$fullPathname	=	$myConfig->path->Images ;
echo "Full Pathname: [" . $fullPathname . "]<br/>" ;
$fullFilename	=	$fullPathname . $filename ;
echo "Full Filename: [" . $fullFilename . "]<br/>" ;

if ( chdir( $fullPathname)) {
	echo "Path exists<br/>" ;
} else {
	mkdir( $fullPathname) ;
}

echo "File is valid, and was successfully uploaded.<br/>\n";

$sysCmd	=	"cd " . $myConfig->path->Images . " ; export USER=_www ; /opt/local/bin/convert " . $fullFilename . " -rotate 90 " . $fullFilename ;
echo "system command: '" . $sysCmd . "'<br/>" ;
echo system( $sysCmd, $res) . "<br/>" ;
echo "$res" . "<br/>" ;

$sysCmd	=	"cd " . $myConfig->path->Images . " ; export USER=_www ; make " ;
echo "system command: '" . $sysCmd . "'<br/>" ;
echo system( $sysCmd, $res) . "<br/>" ;
echo "$res" . "<br/>" ;

?>