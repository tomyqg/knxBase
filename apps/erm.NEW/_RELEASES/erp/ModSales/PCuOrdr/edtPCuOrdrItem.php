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
				rowDisplay( FTr::tr( "Temp. customer order no."), "_HKey", 6, 6, "", "") ;
				rowEdit( FTr::tr( "Position no."), "_IItemNo", 6, 8, "", "") ;
				rowDisplay( FTr::tr( "Article no."), "_IArtikelNr", 16, 16, "", "") ;
				rowDisplayDblBR( FTr::tr( "Description"), "VOID", 64, 64, "", "VOID", 20, 20, "", "") ;
				rowEdit( FTr::tr( "Quantity"), "_IMenge", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Price"), "_FPreis", 12, 12, "", "") ;
				rowEdit( FTr::tr( "Reference price"), "_FRefPreis", 12, 12, "", "") ;
				rowDisplay( FTr::tr( "Qty. per pack."), "_IMengeProVPE", 4, 4, "", "") ;
				?></table>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionUpdateObject" onclick="objEditorNS.upd( '<?php echo $edtName ; ?>') ;">
				<?php echo FTr::tr( "Update") ; ?>
			</button>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionDeleteObject" onclick="objEditorNS.cancel( '<?php echo $edtName ; ?>') ;">
				<?php echo FTr::tr( "Cancel") ; ?>
			</button>
		</form> 
	</div>
</div>
</body>
</html>
