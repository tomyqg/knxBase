<?php
require_once( $_SERVER["DOCUMENT_ROOT"]."/../Config/config.inc.php") ;
require_once( "globalLib.php") ;
require_once( "common.php") ;
?>
<div id="CarrC3s1" data-dojo-type="dijit/form/SplitContainer" orientation="vertical" style="">
	<div id="CarrC3s1c1" data-dojo-type="dijit/layout/ContentPane" sizeshare="10" style="">
		<div id="content">
			<div id="keydata">
				<form name="CarrKeyData" id="CarrKeyData" onsubmit="return false ;">  
					<table>
						<tr>
							<th><?php echo FTr::tr( "Carrier") ; ?>:</th>
							<td class="space"><input type="image" src="/licon/yellow/18/left.png"
										onclick="hookPrevObject() ; return false ;" /></td>
							<td>
								<input type="text" name="_ICarrier" id="_ICarrier" value="" onkeypress="if(event.keyCode==13){requestUni( 'Base', 'Carr', '/Common/hdlObject.php', 'getXMLComplete', document.forms['CarrKeyData']._ICarrier.value, '', '', null, showCarrAll) ;}else{return true;} return false ; "/>
							</td>
							<td class="space"><input type="image" src="/licon/yellow/18/right.png"
										onclick="hookNextObject() ; return false ;" /></td>
							<td>
								<button data-dojo-type="dijit/form/Button" name="sel_Carrier" border="0" onclick="selCarr( 'Base', 'Carr', '', 'getXMLComplete', showCarrAll) ; return false ; ">
									<?php echo FTr::tr( "Select ...") ; ?>
								</button> 
							</td>
						</tr>
						<tr>
							<th><?php echo FTr::tr( "Full name") ; ?></th>
							<td colspan="4">
								<input type="text" name="_DFullName" id="VOID" size="35" value="" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<div id="CarrC3s1c2" data-dojo-type="dijit/layout/ContentPane" sizeshare="90" style="">
		<div id="CarrTc1" data-dojo-type="dijit/layout/TabContainer" style=" ">
			<div id="CarrTc1cp1" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "General") ; ?> (*)">
				<div id="content">
					<div id="maindata">
						<form method="post" name="formCarrMain" id="formCarrMain" enctype="multipart/form-data" onsubmit="return false ;" >
							<table><?php
								rowEdit( FTr::tr( "Carrier"), "_ICarrier", 6, 6, "", "") ;
								rowEdit( FTr::tr( "Carrier name"), "_ICarrName", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Full name"), "_IFullName", 32, 64, "", "") ;
								rowEdit( FTr::tr( "Handling timeout"), "_ICarrHdlgTO", 3, 8, "", "") ;
								?></table>
							<button type="button" data-dojo-type="dijit/form/Button" tabindex="14" border="0" onclick="requestUni( 'Base', 'Carr', '/Common/hdlObject.php', 'upd', document.forms['CarrKeyData']._ICarrier.value, '', '', 'formCarrMain', showCarr) ; return false ; ">
							<?php echo FTr::tr( "Update") ; ?>
							</button>
						</form> 
					</div>
				</div>
			</div>
			<div id="CarrTc1cp4" data-dojo-type="dijit/layout/ContentPane" title="<?php echo FTr::tr( "Options") ; ?>">
				<div id="content">
					<div id="maindata">
						<button type="button" data-dojo-type="dijit/form/Button" onclick="editorAdd( 'Base', 'Carr', '/Base/Carr/editorCarrOpt.php', 'getCarrOptAsXML', document.forms['CarrKeyData']._ICarrier.value, '', '', null, showTableCarrOpt, 'CarrOpt') ; ">
							<?php echo FTr::tr( "New option") ; ?>
						</button>
						<?php tableBlock( "refCarrOpt", "formCarrOptTop") ; ?>
					</div>
					<div id="depdata">
						<div id="TableCarrOptRoot">
							<table id="TableCarrOpt" eissClass="CarrOpt">
								<thead>
									<tr eissType="header">
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th colspan="2"><?php echo FTr::tr( "Validity") ; ?></th>
										<th colspan="2"><?php echo FTr::tr( "Weight") ; ?></th>
										<th colspan="2"><?php echo FTr::tr( "Length") ; ?></th>
										<th colspan="2"><?php echo FTr::tr( "Width") ; ?></th>
										<th colspan="2"><?php echo FTr::tr( "Height") ; ?></th>
									</tr>
									<tr eissType="header">
										<th eissAttribute="Id">Id</th>
										<th eissAttribute="CarrOptPos"><?php echo FTr::tr( "Item no.") ; ?></th>
										<th eissAttribute="VersOpt"><?php echo FTr::tr( "Option") ; ?></th>
										<th eissAttribute="Name"><?php echo FTr::tr( "Name") ; ?></th>
										<th eissAttribute="CountryTo"><?php echo FTr::tr( "Destination") ; ?></th>
										<th eissAttribute="ValidFrom"><?php echo FTr::tr( "From") ; ?></th>
										<th eissAttribute="ValidTo"><?php echo FTr::tr( "Until") ; ?></th>
										<th eissAttribute="WeightMin"><?php echo FTr::tr( "Min.") ; ?></th>
										<th eissAttribute="WeightMax"><?php echo FTr::tr( "Max.") ; ?></th>
										<th eissAttribute="LengthMin"><?php echo FTr::tr( "Min.") ; ?></th>
										<th eissAttribute="LengthMax"><?php echo FTr::tr( "Max.") ; ?></th>
										<th eissAttribute="WidthMin"><?php echo FTr::tr( "Min.") ; ?></th>
										<th eissAttribute="WidthMax"><?php echo FTr::tr( "Max.") ; ?></th>
										<th eissAttribute="HeightMin"><?php echo FTr::tr( "Min.") ; ?></th>
										<th eissAttribute="HeightMax"><?php echo FTr::tr( "Max.") ; ?></th>
										<th eissAttribute="Preis"><?php echo FTr::tr( "Price") ; ?></th>
										<th colspan="4" eissFunctions="edit,move,delete">Functions</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
					<div id="maindata">
						<?php tableBlock( "refCarrOpt", "formCarrOptBot") ; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
