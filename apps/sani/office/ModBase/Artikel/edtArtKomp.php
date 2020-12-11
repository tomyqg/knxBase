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
				rowDisplay( "Id:", "_HId", 10, 10, "", "") ;
				rowDisplay( "Artikel Nr.:", "_HKey", 16, 16, "", "") ;
				rowEdit( "Pos. Nr.:", "_IPosNr", 6, 6, "") ;
				rowOption( "Typ:", "_IKompTyp", ArtKomp::getRKompTyp(), "") ;
				rowEdit( "Komp. Art.:", "_ICompArtikelNr", 16, 16, "", "", "", "selArtikel( 'Base', 'Artikel', 'getXMLComplete', document.forms['ArtikelKeyData']._IArtikelNr.value, copyArtkelNrToCompArtikelNr) ; return false ; " ) ;
				rowEdit( "Menge:", "_ICompMenge", 6, 6, "") ;
				rowEdit( "Menge pro VPE:", "_ICompMengeProVPE", 6, 6, "") ;
				rowEdit( "Punkte Gruppe:", "_IPointGroupe", 12, 12, "") ;
				rowEdit( "Punkte:", "_IPoints", 12, 12, "") ;
				rowEdit( "Max. Punkte:", "_IMaxPoints", 12, 12, "") ;
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
