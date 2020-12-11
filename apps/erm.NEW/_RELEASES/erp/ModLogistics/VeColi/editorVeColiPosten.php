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
				rowDisplay( FTr::tr( "Id"), "_HId", 8, 8, "", "") ;
				rowDisplay( FTr::tr( "Colli Nr."), "_HKey", 6, 6, "", "") ;
				rowEdit( FTr::tr( "Position Nr."), "_IPosNr", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Weight"), "_FGewicht", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Length"), "_FEinzelDimL", 12, 12, "", "") ;
				rowEdit( FTr::tr( "Width"), "_FEinzelDimB", 12, 12, "", "") ;
				rowEdit( FTr::tr( "Height"), "_FEinzelDimH", 12, 12, "", "") ;
				rowEdit( FTr::tr( "Value"), "_FWert", 12, 12, "", "") ;
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
</body>
</html>
