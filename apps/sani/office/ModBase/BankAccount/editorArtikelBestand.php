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
		<form name="editorObject" id="editorObject" enctype="multipart/form-data" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( "Id:", "_HId", 10, 10, "", "") ;
				rowDisplay( "Artikel Nr.:", "_HKey", 10, 10, "", "") ;
				rowEdit( "Lager Id.:", "_ILagerId", 10, 10, "", "") ;
				rowEdit( "Lagerort:", "_ILagerOrt", 10, 10, "", "") ;
				rowOption( "Default:", "_IDef", Opt::getRflagNeinJa(), "", "") ;
				rowEdit( "Lagerbestand:", "_ILagerbestand", 10, 10, "", "") ;
				rowEdit( "Reserviert:", "_IReserviert", 10, 10, "", "") ;
				rowEdit( "Kommissioniert:", "_IKommissioniert", 10, 10, "", "") ;
				rowEdit( "Bestellt:", "_IBestellt", 10, 10, "", "") ;
				rowEdit( "Mindestbestand:", "_IMindestbestand", 10, 10, "", "") ;	
				?></table>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionCreateObject" onclick="newObject() ;" />
				<?php echo FTr::tr( "Create") ; ?>
			</button>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionUpdateObject" onclick="updObject() ;" />
				<?php echo FTr::tr( "Update") ; ?>
			</button>
			<button type="submit" data-dojo-type="dijit/form/Button" name="actionDeleteObject" onclick="cancelObject() ;" />
				<?php echo FTr::tr( "Cancel") ; ?>
			</button>
		</form> 
	</div>
</div>
