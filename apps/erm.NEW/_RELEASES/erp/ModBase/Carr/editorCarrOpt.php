<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ; 
?>
<html>
<head></head>
<body>
<div id="content">
	<div id="maindata">
		<form action="CarrBearb.php" method="post" name="editorObject" enctype="multipart/form-data" target="Carr" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( FTr::tr( "Id"), "_HId", 8, 8, "", "") ;
				rowDisplay( FTr::tr( "Carrier no."), "_HKey", 12, 12, "", "") ;
				rowEdit( FTr::tr( "Item no."), "_ICarrOptPos", 3, 6, "") ;
				rowOption( FTr::tr( "Option"), "_IVersOpt", Carr::getRVersOpt(), "") ;
				rowEdit( FTr::tr( "Name"), "_IName", 24, 64, "", "") ;
				rowEdit( FTr::tr( "Code"), "_ICode", 16, 16, "", "") ;
				rowEdit( FTr::tr( "From"), "_IFromLimit", 24, 32, "", "") ;
				rowEdit( FTr::tr( "To"), "_IToLimit", 24, 32, "", "") ;
				rowOption( FTr::tr( "Country from"), "_ICountryFrom", Opt::getRCountry(), "") ;
				rowOption( FTr::tr( "CountryTo"), "_ICountryTo", Opt::getRCountry(), "") ;
				rowEdit( FTr::tr( "Price"), "_FPreis", 24, 32, "", "") ;
				rowEdit( FTr::tr( "Weight min."), "_FWeightMin", 8, 8, "", "") ;
				rowEdit( FTr::tr( "Weight max."), "_FWeightMax", 8, 8, "", "") ;
				rowEdit( FTr::tr( "Length min."), "_FLengthMin", 8, 32, "", "") ;
				rowEdit( FTr::tr( "Length max."), "_FLengthMax", 8, 8, "", "") ;
				rowEdit( FTr::tr( "Width min."), "_FWidthMin", 8, 8, "", "") ;
				rowEdit( FTr::tr( "Width max."), "_FWidthMax", 8, 8, "", "") ;
				rowEdit( FTr::tr( "Height min."), "_FHeightMin", 8, 8, "", "") ;
				rowEdit( FTr::tr( "Height max."), "_FHeightMax", 8, 8, "", "") ;
				rowDate( FTr::tr( "Valid from"), "_IValidFrom", 11, 11, "", "") ;
				rowDate( FTr::tr( "Valid until"), "_IValidTo", 11, 11, "", "") ;
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
