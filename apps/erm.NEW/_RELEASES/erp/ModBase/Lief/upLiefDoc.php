<?php
/**
 * 
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
/**
 * prepare some variables
 * @var unknown_type
 */
$liefNr	=	$_POST['_DLiefNr'] ;
$docType	=	$_POST['_IDocTypeLief'] ;
$filename	=	$_FILES['_IFilename']['name'] ;

echo "Upload: Lief Dokument<br/>" ;
echo "LiefNr: [" . $liefNr . "]<br/>" ;
echo "Filename: [" . $filename . "]<br/>" ;
echo "Typ: [" . $docType . "]<br/>" ;

$fullPathname	=	$myConfig->path->Archive . "Lief/" ;
echo "Full Pathname: [" . $fullPathname . "]<br/>" ;
$fullFilename	=	$fullPathname . $liefNr . "_" . $docType . "_" . $filename ;
echo "Full Filename: [" . $fullFilename . "]<br/>" ;

if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
	echo "File is valid, and was successfully uploaded.\n";
} else {
	echo "Possible file upload attack!\n";
}

?>
