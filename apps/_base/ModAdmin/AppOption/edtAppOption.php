<?php
/**
 * State of translation:	done
 */
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
?>
<html>
<head></head>
<body>
<div id="content">
	<div id="maindata">
		<form name="editorObject" id="editorObject" enctype="multipart/form-data" onsubmit="return false ;">
			<table><?php
				rowDisplay( FTr::tr( "Id"), "_HId", 10, 10, "", "") ;
				rowDisplay( FTr::tr( "Text MD5"), "_HKey", 10, 10, "", "") ;
				rowTextEdit( FTr::tr( "Translation"), "_IVolltext2", 64, 12, "") ;
				?></table>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionUpdateObject" onclick="updObject() ;" />
				<?php echo FTr::tr( "Update") ; ?>
			</button>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionDeleteObject" onclick="cancelObject() ;" />
				<?php echo FTr::tr( "Cancel") ; ?>
			</button>
		</form>
	</div>
</div>
</body>
</html>
