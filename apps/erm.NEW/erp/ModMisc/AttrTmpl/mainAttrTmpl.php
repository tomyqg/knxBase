<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="AttrTmplRoot" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="AttrTmplKey" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form enctype="multipart/form-data" name="AttrTmplKeyData" id="AttrTmplKeyData" >  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Template no.") ; ?>:&nbsp;</th>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/left.png"
									onclick="hookPrevObject() ; return false ;" />
							</td>
							<td>
								<input type="text" name="_IAttrTmplNo" id="_IAttrTmplNo" 
									onkeypress="enterKey( event) ; return false ;"/>
							</td>
							<td>
								<input type="image" src="/Rsrc/licon/yellow/18/right.png"
									onclick="hookNextObject() ; return false ;" />
							</td>
							<td>
								<button data-dojo-type="dijit/form/Button" name="btnSelAttrTmpl" 
									onclick="screenCurrent.selAttrTmpl.show( '', -1, '') ;">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="AttrTmplData" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="AttrTmplDataTC" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="AttrTmplDataTCMain" data-dojo-type="dijit/layout/ContentPane" title="Main">
				<div id="content">
					<div id="maindata">
						<form name="formAttrTmplMain" id="formAttrTmplMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Keywords"), "_IKeywords", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Data table"), "_IDataTable", 32, 64, "", "") ;
							?></table>
							<button data-dojo-type="dijit/form/Button" onclick="requestUni( 'ModMisc', 'AttrTmpl', '/Common/hdlObject.php', 'upd', document.forms['AttrTmplKeyData']._IAttrTmplNo.value, '', '', 'formAttrTmplMain', showAttrTmpl) ; return false ; ">
								<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form>
					</div>
				</div>
			</div>
			<div id="AttrTmplDataTCItems" data-dojo-type="dijit/layout/ContentPane" title="Attributes">
				<div id="content">
					<div id="maindata">
						<button data-dojo-type="dijit/form/Button" onclick="editorAdd( 'ModMisc', 'AttrTmpl', '/ModMisc/AttrTmpl/editorAttrTmplPosten.php', 'getAttrTmplPostenAsXML', document.forms['AttrTmplKeyData']._IAttrTmplNo.value, '', '', null, showTableAttrTmplPosten, 'AttrTmplPosten') ; return false ;">
							<?php echo FTr::tr( "New ...") ; ?>
						</button>
					</div>
					<div id="depdata">
						<div id="AttrTmplItemRoot">
							<?php tableBlock( "itemViews['dtvAttrTmplItems']", "formAttrTmplItemsTop") ; ?>
							<table id="TableAttrTmplItems" eissClass="AttrTmplItem" width="100%">
								<thead>
									<tr eissType="header">
										<th eissAttribute="Id"><?php echo FTr::tr( "Id") ; ?></th>
										<th eissAttribute="ItemNo"><?php echo FTr::tr( "Item no.") ; ?></th>
										<th eissAttribute="Attr"><?php echo FTr::tr( "Attribute") ; ?></th>
										<th eissAttribute="AttrValue" ><?php echo FTr::tr( "Default value") ; ?></th>
										<th colspan="4" eissMod="ModMisc" eissFunctions="edit,move,delete"><?php echo FTr::tr( "Functions") ; ?></th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							<?php tableBlockBot( "itemViews['dtvAttrTmplItems']", "formAttrTmplItemsBot") ; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
