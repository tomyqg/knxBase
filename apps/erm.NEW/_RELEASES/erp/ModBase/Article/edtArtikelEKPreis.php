<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ; 
if ( isset( $_GET['Id'])) {
	$myLiefNr	=	$_GET['LiefNr'] ;
} else {
	$myLiefNr	=	"" ;
}
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
				rowDisplay( FTr::tr( "Id"), "_HId", 10, 10, "", "") ;
				rowDisplay( FTr::tr( "Article no."), "_HKey", 10, 10, "", "HELP-ArticleNo") ;
				rowEdit( FTr::tr( "Supplier no."), "_ILiefNr", 6, 6, "", "HELP-SuppNo") ;
				rowEdit( FTr::tr( "Supplier article no."), "_ILiefArtNr", 32, 32, "", "HELP-SuppArticleNo") ;
				rowEdit( FTr::tr( "Quantity"), "_IMenge", 6, 6, "1", "HELP-Qty") ;
				rowEdit( FTr::tr( "Qty. per pack"), "_IMengeProVPE", 6, 6, "1", "HELP-QtyPerPack") ;
				rowEditDbl( FTr::tr( "Price / Qty."), "_FPreis", 12, 12, "",
									"_IMengeFuerPreis", 6, 6, "1", "HELP-Price-QtyForPrice") ;
				rowEdit( FTr::tr( "Currency"), "_IWaehrung", 6, 6, "EUR", "HELP-Currency") ;
				rowEdit( FTr::tr( "MSRP"), "_FLiefVKPreis", 12, 12, "", "HELP-MSRP") ;
				rowEdit( FTr::tr( "Discount class"), "_IHKRabKlasse", 6, 6, "", "HELP-DiscClass") ;
				rowEdit( FTr::tr( "Qty. correction factor"), "_IMKF", 6, 6, "1",
							"Faktor mit dem die von diesem Lieferanten<br/>per Lieferschein gelieferte Menge<br/>
							multipliziert werde muss um im Lager korrekt gebucht zu werden.") ;
				rowEdit( FTr::tr( "Markup"), "_IMarge", 6, 6, "1") ;
				rowEdit( FTr::tr( "Own sales price"), "_FOwnVKPreis", 12, 12, "", "HELP-OwnVKPreis") ;
				rowEdit( FTr::tr( "Own discount factor"), "_IOwnRabatt", 12, 12, "", "HELP-OwnRabatt") ;
				rowDate( FTr::tr( "Valid from"), "_IGueltigVon", 10, 10, "HELP-ValidFrom") ;
				rowDate( FTr::tr( "Valid to"), "_IGueltigBis", 10, 10, "HELP-ValidUntil") ;
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