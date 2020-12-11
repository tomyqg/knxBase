<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ; 
?>
<html>
<head></head>
<body>
<div id="content">
	<div id="maindata">
		<form name="editorObject" id="editorObject" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( "Id:", "_HId", 8, 8, "", "") ;
				rowDisplay( "Angebto Nr.:", "_HKey", 6, 6, "", "") ;
				rowEdit( "Position Nr.:", "_IPosNr", 6, 8, "", "") ;
				rowDisplay( "Artikel Nr.:", "_IArtikelNr", 16, 16, "", "") ;
				rowDisplayDblBR( "Bezeichnung:", "VOID", 64, 64, "", "VOID", 20, 20, "", "") ;
				rowEdit( "Bestellte Menge:", "_IMenge", 6, 8, "", "") ;
				rowDisplay( "Gelieferte Menge:", "_IGelieferteMenge", 6, 8, "", "") ;
				rowDisplay( "Berechnete Menge:", "_IBerechneteMenge", 6, 8, "", "") ;
				rowEdit( "Preis:", "_FPreis", 12, 12, "", "") ;
				rowEdit( "Referenzpreis:", "_FRefPreis", 12, 12, "", "") ;
				rowDisplay( "Menge pro VPE:", "_IMengeProVPE", 4, 4, "", "") ;
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