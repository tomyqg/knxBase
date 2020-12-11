<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ; 
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
				rowOption( FTr::tr( "Position"), "_IFunktion", KontoUnterkonto::getRFunktion(), "") ;
				rowEdit( FTr::tr( "Adresszusatz"), "_IAdrZusatz", 24, 32, "") ;
				rowOption( FTr::tr( "Anrede"), "_IAnrede", Opt::getRAnreden(), "") ;
				rowOption( FTr::tr( "Title"), "_ITitel", Opt::getRTitel(), "") ;
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
			<input type="submit" name="actionCreateObject" value="<?php echo FTr::tr( "Create") ; ?>" tabindex="14" border="0" onclick="newObject() ;" />
			<input type="submit" name="actionUpdateObject" value="<?php echo FTr::tr( "Update") ; ?>" tabindex="14" border="0" onclick="updObject() ;" />
			<input type="submit" name="actionDeleteObject" value="<?php echo FTr::tr( "Cancel") ; ?>" tabindex="14" border="0" onclick="cancelObject() ;" />
		</form> 
	</div>
</div>
