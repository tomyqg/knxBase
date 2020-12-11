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
		<form name="editorObject" id="editorObject" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( FTr::tr( "Id"), "_HId", 8, 8, "", "") ;
				rowDisplay( FTr::tr( "Journal template no."), "_HKey", 6, 6, "", "") ;
				rowEdit( FTr::tr( "Item no."), "_IItemNo", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Description"), "_IDescription", 32, 32, "", "") ;
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
</body>
</html>