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
		<form name="editorObject" enctype="multipart/form-data" onsubmit="return false ;"> 
			<table><?php
					rowDisplay( FTr::tr( "Id"), "_HId", 8, 8, "", "") ;
					rowDisplay( FTr::tr( "Customer no."), "_HKey", 11, 11, "", "") ;
					rowEdit( FTr::tr( "Company name 1"), "_IFirmaName1", 32, 64, "", "") ;
					rowEdit( FTr::tr( "Company name 2"), "_IFirmaName2", 32, 64, "", "") ;
					rowEdit( FTr::tr( "Company name 3"), "_IFirmaName3", 32, 64, "", "") ;
					rowEditDbl( FTr::tr( "Street / No."), "_IStrasse", 20, 32, "",
									"_IHausnummer", 6, 10, "", "") ;
					rowEditDbl( FTr::tr( "ZIP / City"), "_IPLZ", 6, 8, "",
									"_IOrt", 20, 32, "", "") ;
					rowOption( FTr::tr( "Country"), "_ILand", Opt::getRLaender(), "de", "") ;
					rowEdit( FTr::tr( "Phone"), "_ITelefon", 30, 32, "", "") ;
					rowEdit( FTr::tr( "Fax"), "_IFAX", 30, 32, "", "") ;
					rowEdit( FTr::tr( "E-Mail"), "_IeMail", 30, 32, "", "") ;
				?>
			</table>
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
