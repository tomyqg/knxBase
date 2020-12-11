<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
?>
<div id="DivDocument" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="DivDocumentCPKey" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="DocumentKeyData" id="DocumentKeyData" onsubmit="return false ; ">  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Document Id.") ; ?>:&nbsp;</th>
							<td class="space"><input type="image" src="/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IId" id="MainInputField" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'Document', '/Common/hdlObject.php', 'getXMLComplete', MainInputField.value, '', '', null, showDocumentAll) ;}else{return true ;} return false ; " />
							</td>
							<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" name="sel_DocumentNr" border="0" onclick="selDocument( 'Base', 'Document', 'getXMLComplete', MainInputField, showDocumentAll) ; return false ; ">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="DivDocumentCPMain" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="DivDocumentTCMain" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="DivDocumentTCMainCPOverview" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Overview") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formDocumentOV" id="formDocumentOV" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowOption( FTr::tr( "Reference type"), "_IRefType", Document::getRRefType(), "", "") ;
								rowEdit( FTr::tr( "Reference no."), "_IRefNr", 24, 32, "", "") ;
								rowOption( FTr::tr( "Document type"), "_IDocType", Document::getRDocType(), "", "") ;
								?></table>
<!-- 
 							<button data-dojo-type="dijit/form/Button" onClick="requestDataMiner( 'Base', 'DataMinerMisc', '/Common/hdlObject.php', 'getT50', 'Document', 'divDocumentList', 'Id', 'screenDocument', 'retToDocument', document.forms['DocumentKeyData']._IId.value, cbDocumentDMRes, 'formDocumentOV') ; return false ; ">
 -->
 							<button data-dojo-type="dijit/form/Button" onClick="requestUni( 'Base', 'Document', '/Common/hdlObject.php', 'getTableDocumentsAsXML', 'halfstatic', '', '', null, showDocumentDocList) ; return false ;">
 								<?php echo FTr::tr( "Refresh") ; ?>
							</button>
<center>
<table>
	<tr>
		<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="requestDataMinerNew( 'Base', 'DataMinerMisc', '/Common/hdlObject.php', 'getF50', 'Document', 'divDocumentList', 'Id', 'screenDocument', 'retToDocument', document.forms['DocumentKeyData']._IId.value, cbDocumentDMRes, 'formDocumentOV', null) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="requestDataMinerNew( 'Base', 'DataMinerMisc', '/Common/hdlObject.php', 'getP50', 'Document', 'divDocumentList', 'Id', 'screenDocument', 'retToDocument', document.forms['DocumentKeyData']._IId.value, cbDocumentDMRes, 'formDocumentOV', null) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" onClick="requestDataMinerNew( 'Base', 'DataMinerMisc', '/Common/hdlObject.php', 'getT50', 'Document', 'divDocumentList', 'Id', 'screenDocument', 'retToDocument', document.forms['DocumentKeyData']._IId.value, cbDocumentDMRes, 'formDocumentOV', null) ; return false ; "/></td>
		<td><input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="requestDataMinerNew( 'Base', 'DataMinerMisc', '/Common/hdlObject.php', 'getN50', 'Document', 'divDocumentList', 'Id', 'screenDocument', 'retToDocument', document.forms['DocumentKeyData']._IId.value, cbDocumentDMRes, 'formDocumentOV', null) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="requestDataMinerNew( 'Base', 'DataMinerMisc', '/Common/hdlObject.php', 'getL50', 'Document', 'divDocumentList', 'Id', 'screenDocument', 'retToDocument', document.forms['DocumentKeyData']._IId.value, cbDocumentDMRes, 'formDocumentOV', null) ; return false ; " /></td>
	</tr>
</table>
</center>
						</form> 
						<div id="divDocumentList"></div>
					</div>
				</div>
			</div>
			<div id="DivDocumentTCMainCPMain" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Main") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formDocumentMain" id="formDocumentMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowOption( FTr::tr( "Reference type"), "_IRefType", Document::getRRefType(), "", "") ;
								rowEdit( FTr::tr( "Reference no."), "_IRefNr", 24, 32, "", "") ;
								rowOption( FTr::tr( "Document type"), "_IDocType", Document::getRDocType(), "", "") ;
								rowEdit( FTr::tr( "Product code"), "_IProdCode", 24, 32, "", "") ;
								rowEdit( FTr::tr( "Part no."), "_IPartNr", 4, 4, "", "") ;
								rowEdit( FTr::tr( "Revision"), "_IDocRev", 8, 8, "", "") ;
								rowEdit( FTr::tr( "Version"), "_IVersion", 6, 6, "", "") ;
								rowEdit( FTr::tr( "Filename"), "_IFilename", 40, 40, "", "") ;
								rowEdit( FTr::tr( "Filetype"), "_IFiletype", 4, 16, "", "") ;
								rowEdit( FTr::tr( "File URL"), "_IFileURL", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Title"), "_IDocTitle", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Author"), "_IDocAuthor", 24, 32, "", "") ;
								rowDate( FTr::tr( "Date"), "_DDocDateDocument", 10, 10, "", "") ;
								rowDate( FTr::tr( "Valid from"), "_IValidFromDocument", 10, 10, "", "") ;
								rowDate( FTr::tr( "Valid until"), "_IValidToDocument", 10, 10, "", "") ;
								rowOption( FTr::tr( "Status"), "_IStatus", Document::getRStatus(), "", "") ;
								?></table> 
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'Document', '/Common/hdlObject.php', 'add', '', '', '', 'formDocumentMain', showDocument) ; return false ; ">
								<?php echo FTr::tr( "Create") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'Base', 'Document', '/Common/hdlObject.php', 'upd', document.forms['DocumentKeyData']._IId.value, '', '', 'formDocumentMain', showDocument) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
							<button data-dojo-type="dijit/form/Button" onclick="clrFields() ;" /> 
								<?php echo FTr::tr( "Clear fields") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="DivDocumentTCMainCPUpload" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Upload") ; ?>">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formDocumentUpload" id="formDocumentUpload" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowFile( FTr::tr( "File"), "_IFilename", 24, 32, "", "") ;
								?></table> 
							<input type="button" value="aktualisieren" tabindex="14" border="0" onclick="requestUpload( 'Base', 'Document', '/Common/hdlObject.php', 'upload', document.forms['DocumentKeyData']._IId.value, '', '', 'formDocumentUpload', showDocument) ; return false ; ">
						</form> 
					</div>
				</div>
			</div>
			<div id="DivDocumentTCMainCPHistory" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Revision History") ; ?>"
					onShow="requestUni( 'Base', 'Document', '/Common/hdlObject.php', 'getF50', document.forms['DocumentKeyData']._IId.value, '', '', null, showDocumentDocList) ; return false ; ">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formDocumentHistory" id="formDocumentHistory" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								?></table>
							<input type="button" value="Refresh ..." onClick="requestUni( 'Base', 'Document', '/Common/hdlObject.php', 'getF50', document.forms['DocumentKeyData']._IId.value, '', '', null, showDocumentDocList) ; return false ; " />
<center>
<table>
	<tr>
		<td><input type="text" name="_SStartRow" size="4" maxlength="4" value="0" tabindex="1" border="0" /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_12.png" name="firstTen" value="0" tabindex="1" border="0" onclick="requestDataMinerNew( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getF50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Green/24/left.png" name="prevTen" value="0" tabindex="1" border="0" onclick="requestDataMinerNew( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getP50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/yellow/24/refresh.png" name="refresh" value="0" tabindex="1" border="0" onClick="requestDataMinerNew( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getT50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; "/></td>
		<td><input type="image" type="submit" src="/licon/Green/24/right.png" name="nextTen" value="0" tabindex="1" border="0" onclick="requestDataMinerNew( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getN50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; " /></td>
		<td><input type="image" type="submit" src="/licon/Blue/24/object_13.png" name="lastTen" value="0" tabindex="1" border="0" onclick="requestDataMinerNew( 'Base', 'DataMinerArtikelWOP', '/Common/hdlObject.php', 'getL50', 'ArticleWOP', 'divArtikelWOP', 'ArtikelNr', 'screenArtikel', 'retToDataMiner', '', cbArticleWOP, 'formArtikelWOPOV', null) ; return false ; " /></td>
	</tr>
</table>
</center>
						</form>
						<div id="divDocumentHist"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
