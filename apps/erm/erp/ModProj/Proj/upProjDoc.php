<?php

require_once( "config.inc.php") ;

$projNr	=	$_POST['_DProjNr'] ;
$docType	=	$_POST['_IDocType'] ;
$filename	=	$_FILES['_IFilename']['name'] ;

echo "Upload: Proj Dokument<br/>" ;
echo "ProjNr: [" . $projNr . "]<br/>" ;
echo "Filename: [" . $filename . "]<br/>" ;
echo "Typ: [" . $docType . "]<br/>" ;

$fullPathname	=	$myConfig->path->Archive . "Proj/" ;
echo "Full Pathname: [" . $fullPathname . "]<br/>" ;
$fullFilename	=	$fullPathname . $projNr . "_" . $docType . "_" . $filename ;
echo "Full Filename: [" . $fullFilename . "]<br/>" ;

if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
	echo "File is valid, and was successfully uploaded.\n";
} else {
	echo "Possible file upload attack!\n";
}

?>
