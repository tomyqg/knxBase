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
		<form name="editorObject" id="editorObject" onsubmit="return false ;"> 
			<table><?php
				rowDisplay( FTr::tr( "Id"), "_HId", 16, 16, "", "") ;
				rowDisplay( FTr::tr( "Product group"), "_HKey", 16, 16, "", "") ;
				rowEdit( FTr::tr( "Position"), "_IItemNo", 8, 8, "", "") ;
				rowEdit( FTr::tr( "Comp. product group"), "_ICompWebPageNo", 24, 32, "", "") ;
				rowEdit( FTr::tr( "Comp. article group"), "_ICompArtGrNo", 24, 32, "", "") ;
				rowEdit( FTr::tr( "Comp. article"), "_ICompArtNo", 24, 32, "", "") ;
				rowEdit( FTr::tr( "Generate"), "_IGenerieren", 24, 32, "", "") ;
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
			<br/>
			<input type="checkbox" name="editorKeep"><?php echo FTr::tr( "Keep dialog open") ; ?></input>
		</form> 
	</div>
</div>
