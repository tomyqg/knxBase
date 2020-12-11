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
				rowDisplay( FTr::tr( "Id"), "_HId", 6, 6, "") ;
				rowDisplay( FTr::tr( "Article no."), "_HKey", 6, 6, "") ;
				rowDisplay( FTr::tr( "Rev. code."), "_IRevCode", 6, 6, "") ;
				rowEdit( FTr::tr( "Var. no."), "_IArtikelVarNr", 6, 6, "") ;
				rowOption( FTr::tr( "Market id."), "_IMarketId", Market::getMarkets(), "", "") ;
				rowOption( FTr::tr( "Price type"), "_IPreisTyp", Opt::getArray( "Options", "Key", "Value", "OptionName = 'PriceType'"), "0", "") ;
				rowEdit( FTr::tr( "Quantity"), "_IMenge", 6, 6, "1") ;
				rowEdit( FTr::tr( "Qty. per pack"), "_IMengeProVPE", 6, 6, "1") ;
				rowDate( FTr::tr( "Valid from"), "_IGueltigVonVKPreis", 10, 10, "") ;
				rowDate( FTr::tr( "Valid until"), "_IGueltigBisVKPreis", 10, 10, "") ;
				rowEdit( FTr::tr( "Price"), "_FPreis", 10, 10, sprintf( "%.2f", "")) ;
				rowOption( FTr::tr( "Currency"), "_IWaehrung", Currency::getRCurrency(), "EUR") ;
//				rowDisplay( "Referenz Lieferant Nr.:", "_IRefLiefNr", 10, 10, "") ;
				rowEdit( FTr::tr( "Discount"), "_IRabatt", 10, 10, sprintf( "%5.3f", "")) ;
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
