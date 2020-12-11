<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="KatGrC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="KatGrC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="KatGrKeyData" id="KatGrKeyData" onsubmit="return false ; " >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Catalog group no.") ; ?>:&nbsp;</th>
							<td class="space"><input type="image" src="/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IKatGrNr" id="_IKatGrNr" value="" onkeypress="return enterKey( event) ;"/>
							</td>
							<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<input type="button" name="selKatGrNr" value="<?php echo FTr::tr( "Select ...") ; ?>" border="0" onclick="selKatGr( 'ModMisc', 'KatGr', 'getXMLComplete', document.forms['KatGrKeyData']._IKatGrNr, showKatGrAll) ; return false ; "/> 
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Catalog group name") ; ?>:&nbsp;</th>
							<td></td>
							<td colspan="3">
								<input type="text" name="_DKatGrName" id="VOID" size="35" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="KatGrC3s1c2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="KatGrC3s1c2tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="KatGrC3s1c2tc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formKatGrMain" id="formKatGrMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Catalog group no."), "_IKatGrNr", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Catalog group name"), "_IKatGrName", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Market"), "_IMarkt", 24, 32, "all", "") ;
								rowEdit( FTr::tr( "Page type"), "_ISeitenTyp", 4, 4, "", "") ;
								rowEdit( FTr::tr( "Single page"), "_IEigeneSeite", 4, 4, "", "") ;
								rowEdit( FTr::tr( "Level"), "_ILevel", 3, 3, "", "") ;
								rowEdit( FTr::tr( "Menuentry"), "_IMenuEntry", 3, 3, "", "") ;
								rowEdit( FTr::tr( "SSL target"), "_ISSLZiel", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Target URL"), "_IZielURL", 32, 32, "", "") ;
								rowEdit( FTr::tr( "Kennung"), "_IKennung", 4, 4, "", "") ;
								rowEdit( FTr::tr( "Visible"), "_IVisible", 3, 3, "", "") ;
								rowEdit( FTr::tr( "Picture reference"), "_IPGBildRef", 32, 32, "", "") ;
//								rowOption( "Status:", "_IStatus", KatGr::getRStatus(), "0", "") ;
								?></table> 
							<input type="button" value="<?php echo FTr::tr( "Create") ; ?>" tabindex="14" border="0" onclick="requestUni( 'ModMisc', 'KatGr', '/Common/hdlObject.php', 'add', document.forms['formKatGrMain']._IKatGrNr.value, '', '', 'formKatGrMain', showKatGr) ; return false ; ">
							<input type="button" value="<?php echo FTr::tr( "Update") ; ?>" tabindex="14" border="0" onclick="requestUni( 'ModMisc', 'KatGr', '/Common/hdlObject.php', 'upd', document.forms['KatGrKeyData']._IKatGrNr.value, '', '', 'formKatGrMain', showKatGr) ; return false ; ">
							<input type="button" value="<?php echo FTr::tr( "Delete") ; ?>" tabindex="15" border="0" onclick="clrFields() ;" /> 
						</form> 
					</div>
				</div>
			</div>
			<div id="KatGrC3s1c2tc1cp2" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Text") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formKatGrTexte" id="formKatGrTexte" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowDisplay( FTr::tr( "Catalog group name"), "_IKatGrName", 24, 32, "", "") ;
								rowHTMLEdit( FTr::tr( "Long text"), "_RVolltextKatGr", 24, 32, "", "") ;
								rowTextEdit( FTr::tr( "Content"), "_IContent", 32, 8, "", "") ;
								rowTextEdit( FTr::tr( "Description"), "_IDescription", 32, 8, "", "") ;
								?></table> 
							<input type="button" value="<?php echo FTr::tr( "Create") ; ?>" tabindex="14" border="0" onclick="requestUni( 'ModMisc', 'KatGr', '/Common/hdlObject.php', 'add', '', '', '', 'formKatGrMain', showKatGr) ; return false ; ">
							<input type="button" value="<?php echo FTr::tr( "Update") ; ?>" tabindex="14" border="0" onclick="requestUni( 'ModMisc', 'KatGr', '/Common/hdlObject.php', 'upd', document.forms['KatGrKeyData']._IKatGrNr.value, '', '', 'formKatGrTexte', showKatGr) ; return false ; ">
							<input type="button" value="<?php echo FTr::tr( "Delete") ; ?>" tabindex="15" border="0" onclick="clrFields() ;" /> 
						</form> 
					</div>
				</div>
			</div>
			<div id="KatGrC3s1c2tc1cp3" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Components") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formKatGrComp" id="formKatGrComp" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowDisplay( FTr::tr( "Catalog group name"), "_IKatGrName", 24, 32, "", "") ;
								?></table> 
						</form> 
					</div>
				</div>
				<button data-dojo-type="dijit/form/Button" onclick="editorAdd( 'ModMisc', 'KatGr', '/ModMisc/KatGr/editorKatGrComp.php', 'getKatGrCompAsXML', document.forms['KatGrKeyData']._IKatGrNr.value, '', '', null, showTableKatGrComp, 'KatGrComp') ; ">
					<?php echo FTr::tr( "New ...") ; ?>
				</button>
				<div id="TableKatGrCompRoot">
					<table id="TableKatGrComp" eissClass="KatGrComp">
						<thead>
							<tr eissType="header">
								<th eissAttribute="Id">Id</th>
								<th eissAttribute="KatGrNr">Item</th>
								<th eissAttribute="PosNr">Item</th>
								<th eissAttribute="CompKatGrNr" eissLinkTo="screenKatGr" colspan="2">Kat.gr.no.</th>
								<th eissAttribute="KatGrName" >Katalog group name</th>
								<th eissAttribute="CompArtGrNr" eissLinkTo="screenArtGr" colspan="2">Art.gr.no.</th>
								<th eissAttribute="ArtGrName" >Article group name</th>
								<th eissAttribute="CompArtNr" eissLinkTo="screenArtikel" colspan="2">Art.no.</th>
								<th eissAttribute="ArtikelBez1">Article description</th>
								<th colspan="4" eissFunctions="edit,move,delete">Functions</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<button data-dojo-type="dijit/form/Button" onclick="editorAdd( 'ModMisc', 'KatGr', '/ModMisc/KatGr/editorKatGrComp.php', 'getKatGrCompAsXML', document.forms['KatGrKeyData']._IKatGrNr.value, '', '', null, showTableKatGrComp, 'KatGrComp') ; ">
					<?php echo FTr::tr( "New ...") ; ?>
				</button>
			</div>
			<div id="KatGrBild" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Pictures") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formKatGrBild" id="formKatGrBild" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Picture reference"), "_IPGBildRef", 32, 32, "", "") ;
								?></table> 
							<input type="button" value="<?php echo FTr::tr( "Create") ; ?>" tabindex="14" border="0" onclick="requestUni( 'ModMisc', 'KatGr', '/Common/hdlObject.php', 'add', document.forms['formKatGrBild']._IKatGrNr.value, '', '', 'formKatGrBild', showKatGr) ; return false ; ">
							<input type="button" value="<?php echo FTr::tr( "Update") ; ?>" tabindex="14" border="0" onclick="requestUni( 'ModMisc', 'KatGr', '/Common/hdlObject.php', 'upd', document.forms['KatGrKeyData']._IKatGrNr.value, '', '', 'formKatGrBild', showKatGr) ; return false ; ">
							<input type="button" value="<?php echo FTr::tr( "Delete") ; ?>" tabindex="15" border="0" onclick="clrFields() ;" /> 
						</form> 
						<div id="KatGrBildRefDiv"><img src="/Bilder/thumbs/leer.jpg" /></div>
					</div>
				</div>
			</div>
			<div id="KatGrC3s1c2tc1SubGroups" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Subgroups") ; ?>">
				<div id="tableSubKatGr">
				</div>
			</div>
			<div id="KatGrC3s1c2tc1cp7" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Functions") ; ?>">
				<div id="KatGrC3s1c2tc1cp7tc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
					<div id="ArtikelTc1cp7tc1cp00" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Change Catalog Group No.") ; ?>">
						<form name="formKatGrRenum" onsubmit="return false ;">
							<table>
							<?php
								rowEdit( FTr::tr( "New catalog group no."), "_IKatGrNrNeu", 32, 32, "", "") ;
							?>
							</table>
							<button data-dojo-type="dijit/form/Button"
								onclick="requestUni( 'Base', 'KatGr', '/Common/hdlObject.php', 'renumKatGr', document.forms['KatGrKeyData']._IKatGrNr.value, '', '', 'formKatGrRenum', showKatGrAll) ; return false ; "
								title="ERP-HELP-BUTTON-RenumArticle">
								<?php echo FTr::tr( "Change Catalog Group no.") ?>
							</button>
						</form>
					</div>
					<div id="ArtikelTc1cp7tc1cp01" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Create Catalog") ; ?>">
						<form name="formKatCreate" onsubmit="return false ;">
							<button data-dojo-type="dijit/form/Button"
								onclick="createCatalog( document.forms['KatGrKeyData']._IKatGrNr.value) ; return false ;" title="<?php echo FTr::tr( "ERP-BUTTON-HELP-CREATECATALOG") ; ?>" />
								<?php echo FTr::tr( "Create Catalog") ; ?>
							</button><br/>	
						</form>
						<form name="formKatCreateOl" onsubmit="return false ;">
							<button data-dojo-type="dijit/form/Button"
								onclick="createCatalogOutline( document.forms['KatGrKeyData']._IKatGrNr.value) ; return false ;" title="<?php echo FTr::tr( "ERP-BUTTON-HELP-CREATECATALOGOUTLINE") ; ?>" />
								<?php echo FTr::tr( "Create Catalog Outline") ; ?>
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
