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
				rowDisplay( FTr::tr( "Delivery no."), "_HKey", 6, 6, "", "") ;
				rowEdit( FTr::tr( "Item no."), "_IItemNo", 6, 8, "", "") ;
				rowDisplay( FTr::tr( "Article no."), "_IArtikelNr", 16, 16, "", "") ;
				rowDisplayDblBR( FTr::tr( "Bezeichnung"), "VOID", 64, 64, "", "VOID", 64, 64, "", "") ;
				rowEdit( FTr::tr( "Add. text"), "_IAddText", 48, 64, "", "") ;
				rowEdit( FTr::tr( "Qty."), "_IMenge", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Dlvrd. already"), "_IMengeBereitsGeliefert", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Dlvrd. now"), "_IMengeGeliefert", 6, 8, "", "") ;
				rowDisplay( FTr::tr( "Invcd."), "_IMengeBerechnet", 6, 8, "", "") ;
				rowDisplay( FTr::tr( "Qty. per pack"), "_IMengeProVPE", 4, 4, "", "") ;
				rowEdit( FTr::tr( "FOC").":", "_IFOC", 4, 4, "", "") ;
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
