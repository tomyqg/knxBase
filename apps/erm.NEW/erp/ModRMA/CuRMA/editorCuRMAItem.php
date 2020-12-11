<?php
require_once( "config.inc.php") ; 
require_once( "globalLib.php") ; 
require_once( "option.php") ; 
?>
<html>
<head></head>
<body>
<div id="content">
	<div id="maindata">
		<form name="editorObject" id="editorObject" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( "Id:", "_HId", 8, 8, "", "") ;
				rowDisplay( "Customer RMA Nr.:", "_HKey", 6, 6, "", "") ;
				rowEdit( "Item Nr.:", "_IPosNr", 6, 8, "", "") ;
				rowDisplay( "Article no.:", "_IArtikelNr", 16, 16, "", "") ;
				rowDisplayDblBR( "Description:", "VOID", 64, 64, "", "VOID", 64, 64, "", "") ;
				rowEdit( "Bestellte Menge:", "_IMenge", 6, 8, "", "") ;
				rowDisplay( "Gelieferte Menge:", "_IGelieferteMenge", 6, 8, "", "") ;
				rowDisplay( "Berechnete Menge:", "_IBerechneteMenge", 6, 8, "", "") ;
				rowEdit( "Menge dir. bestellt:", "_IMengeDirektBest", 16, 16, "", "") ;
				rowEdit( "Preis:", "_FPreis", 12, 12, "", "") ;
				rowEdit( "Referenzpreis:", "_FRefPreis", 12, 12, "", "") ;
				rowDisplay( "Menge pro VPE:", "_IMengeProVPE", 4, 4, "", "") ;
				rowEdit( FTr::tr( "FOC").":", "_IFOC", 4, 4, "", "") ;
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