<?php
require_once( "config.inc.php") ; 
require_once( "globalLib.php") ; 
?>
<html>
<head></head>
<body>
<div id="content">
	<div id="maindata">
		<form name="editorObject" id="editorObject" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( FTr::tr( "Id"), "_HId", 8, 8, "", "") ;
				rowDisplay( FTr::tr( "Assembly no."), "_HKey", 6, 6, "", "") ;
				rowEdit( FTr::tr( "Item no."), "_IPosNr", 6, 8, "", "") ;
				rowDisplay( FTr::tr( "Article no."), "_IArtikelNr", 16, 16, "", "") ;
				rowDisplayDblBR( FTr::tr( "Description"), "VOID", 64, 64, "", "VOID", 20, 20, "", "") ;
				rowEdit( FTr::tr( "Qty. required"), "_IMengeErf", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Qty. required total"), "_IMengeErfGes", 6, 8, "", "") ;
				rowDisplay( FTr::tr( "Qty. per package"), "_IMengeProVPE", 4, 4, "", "") ;
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
