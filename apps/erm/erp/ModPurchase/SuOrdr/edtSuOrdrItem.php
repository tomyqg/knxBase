<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ; 
if ( isset( $_GET["edtName"]))
	$edtName	=	$_GET["edtName"] ;
?>
<html>
<head>
</head>
<body>
<div id="content">
	<div id="maindata">
		<form name="editorObject" id="editorObject" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( FTr::tr( "Id"), "_HId", 16, 16, "", "") ;
				rowDisplay( FTr::tr( "Order no."), "_HKey", 16, 16, "", "") ;
				rowEdit( FTr::tr( "Item no."), "_IItemNo", 16, 16, "", "") ;
				rowOption( FTr::tr( "Order mode"), "_IOrdMode", Opt::getROrdMode(), "", "") ;
				rowEdit( FTr::tr( "Article no."), "_IArtikelNr", 16, 16, "", "") ;
				rowDisplay( FTr::tr( "Supp. article no."), "_ILiefArtNr", 16, 16, "", "") ;
				rowEdit( FTr::tr( "Qty."), "_IMenge", 16, 16, "", "") ;
				rowEdit( FTr::tr( "Qty. per pack"), "_IMengeProVPE", 16, 16, "", "") ;
				rowEdit( FTr::tr( "Price"), "_FPreis", 16, 16, "", "") ;
				rowEdit( FTr::tr( "Qty. for pricing"), "_IMengeFuerPreis", 16, 16, "", "") ;
				rowDisplay( FTr::tr( "Total price"), "_FGesamtPreis", 16, 16, "", "") ;
				rowDate( FTr::tr( "Planned delivery"), "_IPlndDlvrDate", 10, 10, "", "") ;
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
