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
				rowDisplay( FTr::tr("Stock id."), "_HKey", 6, 6, "", "") ;
				rowDisplay( FTr::tr( "Warehouse id."), "_HWarehouseId", 16, 32, "", "") ;
				rowEdit( FTr::tr( "Stock id."), "_IStockId", 32, 32, "", "") ;
				rowEdit( FTr::tr( "Shelf id."), "_IShelfId", 32, 32, "", "") ;
//				rowEdit( FTr::tr( "Location"), "_ILocation", 32, 32, "", "") ;
				rowTextEdit( "Description", "_IDescription", 64, 4, "",
											"HELP_STOCK_LOCATION_DESCRIPTION") ;
				rowTextEdit( "Location", "_ILocation", 64, 4, "",
											"HELP_STOCK_LOCATION_LOCATION") ;
				?></table>
			<button type="submit" name="actionCreateObject" data-dojo-type="dijit/form/Button" onclick="newObject() ;">
				<?php echo FTr::tr( "Create") ; ?>
			</button>
			<button type="submit" name="actionUpdateObject" data-dojo-type="dijit/form/Button" onclick="updObject() ;">
				<?php echo FTr::tr( "Update") ; ?>
			</button>
			<button type="submit" name="actionDeleteObject" data-dojo-type="dijit/form/Button" onclick="cancelObject() ;" />
				<?php echo FTr::tr( "Cancel") ; ?>
			</button>
			<br/>
			<input type="checkbox" name="editorKeep"><?php echo FTr::tr( "Keep dialog open") ; ?></input>
		</form> 
	</div>
</div>
</body>
</html>
