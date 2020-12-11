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
				rowDisplay( FTr::tr( "Id"), "_HId", 8, 8, "", "") ;
				rowDisplay( FTr::tr( "Customer no."), "_HKey", 12, 12, "", "") ;
				rowDisplay( FTr::tr( "Customer contact no."), "_HCustomerContactNo", 3, 3, "", "") ;
				rowOption( FTr::tr( "Position"), "_IFunktion", CustomerContact::getRFunktion(), "") ;
				rowEdit( FTr::tr( "Adresszusatz"), "_IAdrZusatz", 24, 32, "") ;
				rowOption( FTr::tr( "Anrede"), "_IAnrede", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Salutation'"), "Herr") ;
				rowOption( FTr::tr( "Title"), "_ITitel", Opt::getArray( "Options", "Key", "Value", "OptionName = 'Title'"), "") ;
				rowEdit( FTr::tr( "First (given) name"), "_IVorname", 24, 32, "", "") ;
				rowEdit( FTr::tr( "Name"), "_IName", 24, 32, "", "") ;
				rowEdit( FTr::tr( "Phone"), "_ITelefon", 24, 32, "", "") ;
				rowEdit( FTr::tr( "Fax"), "_IFAX", 24, 32, "", "") ;
				rowEdit( FTr::tr( "Cellphone"), "_IMobil", 24, 32, "", "") ;
				rowEdit( FTr::tr( "E-Mail"), "_IeMail", 24, 32, "", "") ;
				rowEdit( FTr::tr( "Username"), "_IBenutzerName", 16, 16, "", "") ;
				rowEdit( FTr::tr( "Password"), "_IPasswort", 24, 32, "", "") ;
				rowTextEdit( FTr::tr( "Remark(s)"), "_IBem1", 32, 4, "", "") ;
				rowTextEdit( FTr::tr( "Mailings"), "_IMailing", 32, 4, "", "") ;
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
