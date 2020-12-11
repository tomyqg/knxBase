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
				rowDisplay( FTr::tr( "Id"), "_HId", 8, 8, "", "") ;
				rowDisplay( FTr::tr( "Quotation no."), "_HKey", 6, 6, "", "") ;
				rowEdit( FTr::tr( "Item no."), "_IPosNr", 6, 8, "", "") ;
				rowEdit( FTr::tr( "Quotation no."), "_IKdAngNr", 16, 16, "", "") ;
				rowEdit( FTr::tr( "Project no."), "_IProjNr", 16, 16, "", "") ;
//				rowHTMLEdit( FTr::tr( "Prefix"), "_RPrefixProj", 64, 5, "", "", "") ;
//				rowHTMLEdit( FTr::tr( "Postfix"), "_RPostfixProj", 64, 5, "", "", "") ;
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
