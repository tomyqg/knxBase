<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ; 
if ( isset( $_GET["edtName"]))
	$edtName	=	$_GET["edtName"] ;
?>
<html>
<head></head>
<body>
<div id="content">
	<div id="maindata">
		<form name="editorObject" id="editorObject" enctype="multipart/form-data" enctype="multipart/form-data" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( FTr::tr( "Id"), "_HId", 10, 10, "", "") ;
				rowDisplay( FTr::tr( "Reference type"), "_HRefType", 20, 20, "Article", "") ;
				rowDisplay( FTr::tr( "Article no."), "_HKey", 20, 20, "", "") ;
				rowOption( FTr::tr( "Document type"), "_IDocType", Document::getRDocType(), Document::DT_UG) ;
				rowEdit( FTr::tr( "Product Code"), "_IProdCode", 12, 12, "") ;
				rowEdit( FTr::tr( "Part no."), "_IPartNr", 4, 4, "") ;
				rowEdit( FTr::tr( "Revision"), "_IDocRev", 8, 8, "") ;
				rowEdit( FTr::tr( "Version"), "_IVersion", 6, 6, "") ;
				rowEdit( FTr::tr( "Filename"), "_IFilename", 32, 64, "") ;
				rowOption( FTr::tr( "Filetype"), "_IFiletype", Document::getRFiletype(), Document::FT_PDF) ;
				rowEdit( FTr::tr( "File URL"), "_IFileURL", 32, 64, "") ;
				rowEdit( FTr::tr( "Title"), "_IDocTitle", 32, 64, "") ;
				rowEdit( FTr::tr( "Author"), "_IAuthor", 12, 12, "") ;
				rowDate( FTr::tr( "Date"), "_IDocDate", 10, 10, "", "") ;
				rowDate( FTr::tr( "Valid from"), "_IValidFromDocE", 10, 10, "", "") ;
				rowDate( FTr::tr( "Valid to"), "_IValidToDocE", 10, 10, "", "") ;
				rowOption( FTr::tr( "Status"), "_IStatus", Document::getRStatus(), "") ;
				?></table>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionCreateObject" onclick="objEditorNS.add( '<?php echo $edtName ; ?>') ;" />
				<?php echo FTr::tr( "Create") ; ?>
			</button>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionUpdateObject" onclick="objEditorNS.upd( '<?php echo $edtName ; ?>') ;" />
				<?php echo FTr::tr( "Update") ; ?>
			</button>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionDeleteObject" onclick="objEditorNS.cancel( '<?php echo $edtName ; ?>') ;" />
				<?php echo FTr::tr( "Cancel") ; ?>
			</button>
		</form> 
	</div>
</div>






















