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
		<form name="editorObject" id="editorObject" enctype="multipart/form-data" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( "Id:", "_HId", 10, 10, "", "") ;
				rowDisplay( "Artikel Nr.:", "_HKey", 16, 16, "", "") ;
				rowEdit( "QPC.", "_IQPC", 5, 5, "", "") ;
				rowEdit( "Weight", "_IWeight", 5, 5, "", "") ;
				rowEdit( "Height", "_IHeight", 5, 5, "", "") ;
				rowEdit( "Width", "_IWidth", 5, 5, "", "") ;
				rowEdit( "Depth", "_IDepth", 5, 5, "", "") ;
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
