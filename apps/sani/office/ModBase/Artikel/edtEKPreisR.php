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
				rowDisplay( FTr::tr( "Id"), "_HId", 10, 10, "", "") ;
				rowDisplay( FTr::tr( "Article no."), "_HKey", 10, 10, "", "") ;
				rowEdit( FTr::tr( "Rev. code."), "_IRevCode", 6, 6, "") ;
				rowEdit( FTr::tr( "Var. no."), "_IArtikelVarNr", 6, 6, "") ;
				rowEdit( FTr::tr( "Supplier no."), "_ILiefNr", 12, 12, "", "", "",
				    "return selLiefNr( 'window.opener.document.EKPreisR._ILiefNr','') ;") ;
				rowEdit( FTr::tr( "Supplier article no."), "_ILiefArtNr", 32, 32, "") ;
				rowEdit( FTr::tr( "Kalculcation base"), "_IKalkBasis", 6, 6, "1",
							"Staffelmenge die zur Berechnung<br/>des VK-Preises herangezogen wird.") ;
				rowEdit( FTr::tr( "Supplier description"), "_ILiefArtText", 48, 64, "") ;
				rowEdit( FTr::tr( "Qty. correction factor"), "_IMKF", 6, 6, "1",
							"Faktor mit dem die von diesem Lieferanten<br/>per Lieferschein gelieferte Menge<br/>
							multipliziert werde muss um im Lager korrekt gebucht zu werden.") ;
				rowEdit( FTr::tr( "Markup"), "_IMarge", 6, 6, "1") ;
				rowOption( FTr::tr( "Ordering mode"), "_IOrdMode", Opt::getROrdMode(), "") ;
				rowEdit( FTr::tr( "Qty. per pack"), "_IMengeProVPE", 6, 6, "1") ;
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
</body>
</html>
