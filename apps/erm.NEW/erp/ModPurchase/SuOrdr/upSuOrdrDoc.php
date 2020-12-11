<?php

require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;

$suOrdrNo	=	$_POST['_DSuOrdrNo'] ;
$docType	=	$_POST['_IDocType'] ;
$filename	=	$_FILES['_IFilename']['name'] ;

echo "Upload: SuOrdr Dokument<br/>" ;
echo "SuOrdrNo: [" . $suOrdrNo . "]<br/>" ;
echo "Filename: [" . $filename . "]<br/>" ;
echo "Typ: [" . $docType . "]<br/>" ;

$fullPathname	=	$myConfig->path->Archive . "SuOrdr/" ;
echo "Full Pathname: [" . $fullPathname . "]<br/>" ;
$fullFilename	=	$fullPathname . $suOrdrNo . "_" . $docType . "_" . $filename ;
echo "Full Filename: [" . $fullFilename . "]<br/>" ;

if (move_uploaded_file($_FILES['_IFilename']['tmp_name'], $fullFilename)) {
	echo "File is valid, and was successfully uploaded.\n";
} else {
	echo "Possible file upload attack!\n";
}

?>
